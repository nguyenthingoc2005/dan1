<?php
/**
 * ==============================================================================
 * ACTIVITY CHECKPOINT MODEL
 * ==============================================================================
 * 
 * Quản lý checkpoints cho tour schedule
 * HDV tự tạo checkpoints cho tour schedule được phân công
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class ActivityCheckpoint
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy checkpoint theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT ac.*, 
                ts.tour_id, ts.guide_id, ts.start_date as schedule_start_date, ts.end_date as schedule_end_date,
                u.full_name as created_by_name
                FROM activity_checkpoints ac
                JOIN tour_schedules ts ON ac.tour_schedule_id = ts.id
                LEFT JOIN users u ON ac.created_by = u.id
                WHERE ac.id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả checkpoints theo schedule
     */
    public function getBySchedule($schedule_id, $filters = [])
    {
        $sql = "SELECT ac.*, 
                u.full_name as created_by_name,
                (SELECT COUNT(*) FROM activity_checkins WHERE activity_checkpoint_id = ac.id) as checkin_count
                FROM activity_checkpoints ac
                LEFT JOIN users u ON ac.created_by = u.id
                WHERE ac.tour_schedule_id = :schedule_id";
        
        $params = ['schedule_id' => $schedule_id];

        // Filter by date
        if (!empty($filters['date'])) {
            $sql .= " AND ac.scheduled_date = :date";
            $params['date'] = $filters['date'];
        }

        // Filter by type
        if (!empty($filters['type'])) {
            $sql .= " AND ac.checkpoint_type = :type";
            $params['type'] = $filters['type'];
        }

        // Filter by status
        if (isset($filters['status'])) {
            $sql .= " AND ac.status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY ac.scheduled_date ASC, ac.scheduled_time ASC, ac.display_order ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy checkpoints theo ngày
     */
    public function getByDate($schedule_id, $date)
    {
        return $this->getBySchedule($schedule_id, ['date' => $date]);
    }

    /**
     * Lấy checkpoints của HDV
     */
    public function getByGuide($guide_id, $schedule_id = null)
    {
        $sql = "SELECT ac.*, 
                ts.tour_id, ts.start_date as schedule_start_date,
                u.full_name as created_by_name
                FROM activity_checkpoints ac
                JOIN tour_schedules ts ON ac.tour_schedule_id = ts.id
                LEFT JOIN users u ON ac.created_by = u.id
                WHERE ac.created_by = :guide_id";
        
        $params = ['guide_id' => $guide_id];

        if ($schedule_id) {
            $sql .= " AND ac.tour_schedule_id = :schedule_id";
            $params['schedule_id'] = $schedule_id;
        }

        $sql .= " ORDER BY ac.scheduled_date ASC, ac.scheduled_time ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy checkpoints sắp tới
     */
    public function getUpcoming($schedule_id, $limit = 5)
    {
        $today = date('Y-m-d');
        $now = date('H:i:s');

        $sql = "SELECT ac.* 
                FROM activity_checkpoints ac
                WHERE ac.tour_schedule_id = :schedule_id
                AND ac.status = 'active'
                AND (
                    (ac.scheduled_date > :today)
                    OR (ac.scheduled_date = :today AND (ac.scheduled_time IS NULL OR ac.scheduled_time >= :now))
                )
                ORDER BY ac.scheduled_date ASC, ac.scheduled_time ASC
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'schedule_id' => $schedule_id,
            'today' => $today,
            'now' => $now,
            'limit' => $limit
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Kiểm tra quyền sở hữu checkpoint
     */
    public function verifyOwnership($checkpoint_id, $guide_id)
    {
        $sql = "SELECT id FROM activity_checkpoints 
                WHERE id = :id AND created_by = :guide_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $checkpoint_id,
            'guide_id' => $guide_id
        ]);
        
        return $stmt->fetch() !== false;
    }

    /**
     * Kiểm tra HDV có quyền với schedule không
     */
    public function verifyScheduleOwnership($schedule_id, $guide_id)
    {
        $sql = "SELECT id FROM tour_schedules 
                WHERE id = :schedule_id AND guide_id = :guide_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'schedule_id' => $schedule_id,
            'guide_id' => $guide_id
        ]);
        
        return $stmt->fetch() !== false;
    }

    /**
     * Tạo checkpoint mới
     */
    public function create($data, $guide_id)
    {
        // Verify schedule ownership
        if (!$this->verifyScheduleOwnership($data['tour_schedule_id'], $guide_id)) {
            throw new Exception("Bạn không được phân công tour schedule này.");
        }

        $sql = "INSERT INTO activity_checkpoints (
                    tour_schedule_id, checkpoint_code, checkpoint_name, checkpoint_type,
                    meal_type, accommodation_type, scheduled_date, scheduled_time,
                    location_name, location_address, is_required, estimated_duration,
                    display_order, status, notes, created_by
                ) VALUES (
                    :tour_schedule_id, :checkpoint_code, :checkpoint_name, :checkpoint_type,
                    :meal_type, :accommodation_type, :scheduled_date, :scheduled_time,
                    :location_name, :location_address, :is_required, :estimated_duration,
                    :display_order, :status, :notes, :created_by
                )";

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'tour_schedule_id' => $data['tour_schedule_id'],
            'checkpoint_code' => $data['checkpoint_code'] ?? $this->generateCheckpointCode($data['tour_schedule_id']),
            'checkpoint_name' => $data['checkpoint_name'],
            'checkpoint_type' => $data['checkpoint_type'],
            'meal_type' => $data['meal_type'] ?? null,
            'accommodation_type' => $data['accommodation_type'] ?? null,
            'scheduled_date' => $data['scheduled_date'],
            'scheduled_time' => $data['scheduled_time'] ?? null,
            'location_name' => $data['location_name'] ?? null,
            'location_address' => $data['location_address'] ?? null,
            'is_required' => $data['is_required'] ?? 1,
            'estimated_duration' => $data['estimated_duration'] ?? null,
            'display_order' => $data['display_order'] ?? 0,
            'status' => $data['status'] ?? 'active',
            'notes' => $data['notes'] ?? null,
            'created_by' => $guide_id
        ]);

        if ($result) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật checkpoint
     */
    public function update($id, $guide_id, $data)
    {
        // Verify ownership
        if (!$this->verifyOwnership($id, $guide_id)) {
            throw new Exception("Bạn không có quyền sửa checkpoint này.");
        }

        $sql = "UPDATE activity_checkpoints SET
                    checkpoint_code = :checkpoint_code,
                    checkpoint_name = :checkpoint_name,
                    checkpoint_type = :checkpoint_type,
                    meal_type = :meal_type,
                    accommodation_type = :accommodation_type,
                    scheduled_date = :scheduled_date,
                    scheduled_time = :scheduled_time,
                    location_name = :location_name,
                    location_address = :location_address,
                    is_required = :is_required,
                    estimated_duration = :estimated_duration,
                    display_order = :display_order,
                    status = :status,
                    notes = :notes,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'checkpoint_code' => $data['checkpoint_code'] ?? null,
            'checkpoint_name' => $data['checkpoint_name'],
            'checkpoint_type' => $data['checkpoint_type'],
            'meal_type' => $data['meal_type'] ?? null,
            'accommodation_type' => $data['accommodation_type'] ?? null,
            'scheduled_date' => $data['scheduled_date'],
            'scheduled_time' => $data['scheduled_time'] ?? null,
            'location_name' => $data['location_name'] ?? null,
            'location_address' => $data['location_address'] ?? null,
            'is_required' => $data['is_required'] ?? 1,
            'estimated_duration' => $data['estimated_duration'] ?? null,
            'display_order' => $data['display_order'] ?? 0,
            'status' => $data['status'] ?? 'active',
            'notes' => $data['notes'] ?? null
        ]);
    }

    /**
     * Xóa checkpoint
     */
    public function delete($id, $guide_id)
    {
        // Verify ownership
        if (!$this->verifyOwnership($id, $guide_id)) {
            throw new Exception("Bạn không có quyền xóa checkpoint này.");
        }

        // Check if has check-ins
        $sql_check = "SELECT COUNT(*) as count FROM activity_checkins WHERE activity_checkpoint_id = :id";
        $stmt_check = $this->pdo->prepare($sql_check);
        $stmt_check->execute(['id' => $id]);
        $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            throw new Exception("Không thể xóa checkpoint đã có check-in. Vui lòng xóa các check-in trước.");
        }

        $sql = "DELETE FROM activity_checkpoints WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Tạo mã checkpoint tự động
     */
    private function generateCheckpointCode($schedule_id)
    {
        $prefix = "CP_" . $schedule_id . "_";
        $sql = "SELECT COUNT(*) as count FROM activity_checkpoints WHERE tour_schedule_id = :schedule_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['schedule_id' => $schedule_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $number = ($result['count'] ?? 0) + 1;
        return $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Lấy thống kê checkpoint
     */
    public function getStats($checkpoint_id)
    {
        $sql = "SELECT 
                COUNT(*) as total_checkins,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_count,
                SUM(CASE WHEN status = 'early' THEN 1 ELSE 0 END) as early_count,
                SUM(CASE WHEN status = 'excused' THEN 1 ELSE 0 END) as excused_count
                FROM activity_checkins
                WHERE activity_checkpoint_id = :checkpoint_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['checkpoint_id' => $checkpoint_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

