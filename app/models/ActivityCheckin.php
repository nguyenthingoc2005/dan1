<?php
/**
 * ==============================================================================
 * ACTIVITY CHECKIN MODEL
 * ==============================================================================
 * 
 * Quản lý check-in chi tiết theo checkpoint
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class ActivityCheckin
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy check-in theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT ac.*, 
                c.full_name, c.phone, c.email,
                b.booking_code,
                acp.checkpoint_name, acp.checkpoint_type
                FROM activity_checkins ac
                JOIN customers c ON ac.customer_id = c.id
                JOIN bookings b ON ac.booking_id = b.id
                JOIN activity_checkpoints acp ON ac.activity_checkpoint_id = acp.id
                WHERE ac.id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy check-ins theo checkpoint
     */
    public function getByCheckpoint($checkpoint_id)
    {
        $sql = "SELECT ac.*, 
                c.full_name, c.phone, c.email,
                b.booking_code,
                bc.age_type
                FROM activity_checkins ac
                JOIN customers c ON ac.customer_id = c.id
                JOIN bookings b ON ac.booking_id = b.id
                JOIN booking_customers bc ON ac.booking_customer_id = bc.id
                WHERE ac.activity_checkpoint_id = :checkpoint_id
                ORDER BY ac.checkin_datetime DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['checkpoint_id' => $checkpoint_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy lịch sử check-in của khách trong schedule
     */
    public function getByCustomer($schedule_id, $customer_id)
    {
        $sql = "SELECT ac.*, 
                acp.checkpoint_name, acp.checkpoint_type, acp.scheduled_date, acp.scheduled_time
                FROM activity_checkins ac
                JOIN activity_checkpoints acp ON ac.activity_checkpoint_id = acp.id
                WHERE ac.tour_schedule_id = :schedule_id 
                AND ac.customer_id = :customer_id
                ORDER BY ac.checkpoint_date ASC, ac.checkin_datetime DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'schedule_id' => $schedule_id,
            'customer_id' => $customer_id
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả check-ins theo schedule
     */
    public function getBySchedule($schedule_id)
    {
        $sql = "SELECT ac.*, 
                c.full_name, c.phone,
                b.booking_code,
                acp.checkpoint_name, acp.checkpoint_type
                FROM activity_checkins ac
                JOIN customers c ON ac.customer_id = c.id
                JOIN bookings b ON ac.booking_id = b.id
                JOIN activity_checkpoints acp ON ac.activity_checkpoint_id = acp.id
                WHERE ac.tour_schedule_id = :schedule_id
                ORDER BY ac.checkpoint_date DESC, ac.checkin_datetime DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['schedule_id' => $schedule_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy check-in của customer trong checkpoint
     */
    public function getCustomerCheckin($checkpoint_id, $booking_customer_id)
    {
        $sql = "SELECT * FROM activity_checkins 
                WHERE activity_checkpoint_id = :checkpoint_id 
                AND booking_customer_id = :booking_customer_id
                ORDER BY checkin_datetime DESC LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'checkpoint_id' => $checkpoint_id,
            'booking_customer_id' => $booking_customer_id
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check-in (tạo hoặc cập nhật)
     */
    public function checkin($checkpoint_id, $booking_customer_id, $data, $checked_by = null)
    {
        // Get booking_customer info
        $sql_bc = "SELECT bc.customer_id, bc.booking_id 
                   FROM booking_customers bc
                   WHERE bc.id = :booking_customer_id";
        $stmt_bc = $this->pdo->prepare($sql_bc);
        $stmt_bc->execute(['booking_customer_id' => $booking_customer_id]);
        $bc_info = $stmt_bc->fetch(PDO::FETCH_ASSOC);
        
        if (!$bc_info) {
            throw new Exception("Booking customer không tồn tại.");
        }

        // Get checkpoint info
        $sql_cp = "SELECT tour_schedule_id, scheduled_date, scheduled_time 
                   FROM activity_checkpoints 
                   WHERE id = :checkpoint_id";
        $stmt_cp = $this->pdo->prepare($sql_cp);
        $stmt_cp->execute(['checkpoint_id' => $checkpoint_id]);
        $cp_info = $stmt_cp->fetch(PDO::FETCH_ASSOC);
        
        if (!$cp_info) {
            throw new Exception("Checkpoint không tồn tại.");
        }

        // Calculate minutes late/early
        $minutes_late = 0;
        $minutes_early = 0;
        $actual_time = $data['actual_time'] ?? date('H:i:s');
        
        // Auto-update status based on time if status is 'present' and scheduled_time exists
        $final_status = $data['status'];
        
        if (!empty($cp_info['scheduled_time']) && !empty($actual_time)) {
            $scheduled = strtotime($cp_info['scheduled_time']);
            $actual = strtotime($actual_time);
            $diff = ($actual - $scheduled) / 60; // minutes
            
            if ($diff > 0) {
                $minutes_late = (int)$diff;
                // If status is 'present' but late more than 5 minutes, auto-change to 'late'
                if ($final_status == 'present' && $minutes_late > 5) {
                    $final_status = 'late';
                }
            } else {
                $minutes_early = abs((int)$diff);
                // If status is 'present' but early more than 10 minutes, auto-change to 'early'
                if ($final_status == 'present' && $minutes_early > 10) {
                    $final_status = 'early';
                }
            }
        }

        // Check if exists
        $existing = $this->getCustomerCheckin($checkpoint_id, $booking_customer_id);

        if ($existing) {
            // Update existing
            $sql = "UPDATE activity_checkins SET
                        status = :status,
                        actual_time = :actual_time,
                        checkin_datetime = NOW(),
                        minutes_late = :minutes_late,
                        minutes_early = :minutes_early,
                        notes = :notes,
                        excused_reason = :excused_reason,
                        checked_by = :checked_by,
                        updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'id' => $existing['id'],
                'status' => $final_status,
                'actual_time' => $actual_time,
                'minutes_late' => $minutes_late,
                'minutes_early' => $minutes_early,
                'notes' => $data['notes'] ?? null,
                'excused_reason' => $data['excused_reason'] ?? null,
                'checked_by' => $checked_by
            ]);
        } else {
            // Create new
            $sql = "INSERT INTO activity_checkins (
                        tour_schedule_id, activity_checkpoint_id, booking_customer_id,
                        customer_id, booking_id, checkpoint_date, scheduled_time,
                        actual_time, checkin_datetime, status, minutes_late, minutes_early,
                        checked_by, notes, excused_reason
                    ) VALUES (
                        :tour_schedule_id, :activity_checkpoint_id, :booking_customer_id,
                        :customer_id, :booking_id, :checkpoint_date, :scheduled_time,
                        :actual_time, NOW(), :status, :minutes_late, :minutes_early,
                        :checked_by, :notes, :excused_reason
                    )";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'tour_schedule_id' => $cp_info['tour_schedule_id'],
                'activity_checkpoint_id' => $checkpoint_id,
                'booking_customer_id' => $booking_customer_id,
                'customer_id' => $bc_info['customer_id'],
                'booking_id' => $bc_info['booking_id'],
                'checkpoint_date' => $cp_info['scheduled_date'],
                'scheduled_time' => $cp_info['scheduled_time'],
                'actual_time' => $actual_time,
                'status' => $final_status,
                'minutes_late' => $minutes_late,
                'minutes_early' => $minutes_early,
                'checked_by' => $checked_by,
                'notes' => $data['notes'] ?? null,
                'excused_reason' => $data['excused_reason'] ?? null
            ]);
        }
    }

    /**
     * Batch check-in
     */
    public function batchCheckin($checkpoint_id, $checkins, $checked_by = null)
    {
        try {
            $this->pdo->beginTransaction();

            foreach ($checkins as $checkin) {
                if (empty($checkin['booking_customer_id'])) {
                    continue;
                }

                $this->checkin(
                    $checkpoint_id,
                    $checkin['booking_customer_id'],
                    [
                        'status' => $checkin['status'] ?? 'present',
                        'actual_time' => $checkin['actual_time'] ?? null,
                        'notes' => $checkin['notes'] ?? null,
                        'excused_reason' => $checkin['excused_reason'] ?? null
                    ],
                    $checked_by
                );
            }

            // Update summary
            require_once MODELS_PATH . '/ActivityCheckinSummary.php';
            $summaryModel = new ActivityCheckinSummary($this->pdo);
            $summaryModel->updateSummary($checkpoint_id);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Cập nhật check-in
     */
    public function update($id, $data)
    {
        $sql = "UPDATE activity_checkins SET
                    status = :status,
                    actual_time = :actual_time,
                    minutes_late = :minutes_late,
                    minutes_early = :minutes_early,
                    notes = :notes,
                    excused_reason = :excused_reason,
                    updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'status' => $data['status'],
            'actual_time' => $data['actual_time'] ?? null,
            'minutes_late' => $data['minutes_late'] ?? 0,
            'minutes_early' => $data['minutes_early'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'excused_reason' => $data['excused_reason'] ?? null
        ]);
    }

    /**
     * Thống kê theo checkpoint
     */
    public function getStatsByCheckpoint($checkpoint_id)
    {
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN status = 'early' THEN 1 ELSE 0 END) as early_count,
                SUM(CASE WHEN status = 'excused' THEN 1 ELSE 0 END) as excused_count,
                AVG(CASE WHEN minutes_late > 0 THEN minutes_late ELSE NULL END) as avg_late_minutes
                FROM activity_checkins
                WHERE activity_checkpoint_id = :checkpoint_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['checkpoint_id' => $checkpoint_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'total' => (int)($result['total'] ?? 0),
            'present' => (int)($result['present_count'] ?? 0),
            'absent' => (int)($result['absent_count'] ?? 0),
            'late' => (int)($result['late_count'] ?? 0),
            'early' => (int)($result['early_count'] ?? 0),
            'excused' => (int)($result['excused_count'] ?? 0),
            'avg_late_minutes' => round($result['avg_late_minutes'] ?? 0, 2)
        ];
    }
}

