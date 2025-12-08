<?php
/**
 * ==============================================================================
 * ACTIVITY CHECKIN SUMMARY MODEL
 * ==============================================================================
 * 
 * Quản lý tổng hợp check-in theo checkpoint
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class ActivityCheckinSummary
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy summary theo checkpoint
     */
    public function getByCheckpoint($checkpoint_id, $date = null)
    {
        $sql = "SELECT acs.*, 
                acp.checkpoint_name, acp.checkpoint_type,
                u1.full_name as started_by_name,
                u2.full_name as completed_by_name
                FROM activity_checkin_summary acs
                JOIN activity_checkpoints acp ON acs.activity_checkpoint_id = acp.id
                LEFT JOIN users u1 ON acs.started_by = u1.id
                LEFT JOIN users u2 ON acs.completed_by = u2.id
                WHERE acs.activity_checkpoint_id = :checkpoint_id";
        
        $params = ['checkpoint_id' => $checkpoint_id];

        if ($date) {
            $sql .= " AND acs.checkpoint_date = :date";
            $params['date'] = $date;
        }

        $sql .= " ORDER BY acs.checkpoint_date DESC LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả summary theo schedule
     */
    public function getBySchedule($schedule_id)
    {
        $sql = "SELECT acs.*, 
                acp.checkpoint_name, acp.checkpoint_type, acp.scheduled_date, acp.scheduled_time
                FROM activity_checkin_summary acs
                JOIN activity_checkpoints acp ON acs.activity_checkpoint_id = acp.id
                WHERE acs.tour_schedule_id = :schedule_id
                ORDER BY acs.checkpoint_date DESC, acp.scheduled_time ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['schedule_id' => $schedule_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tự động cập nhật summary từ check-ins
     */
    public function updateSummary($checkpoint_id)
    {
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

        // Get all check-ins for this checkpoint
        require_once MODELS_PATH . '/ActivityCheckin.php';
        $checkinModel = new ActivityCheckin($this->pdo);
        $checkins = $checkinModel->getByCheckpoint($checkpoint_id);
        
        $total = count($checkins);
        $present = 0;
        $absent = 0;
        $late = 0;
        $early = 0;
        $excused = 0;
        $total_late_minutes = 0;
        $late_count = 0;

        foreach ($checkins as $checkin) {
            switch ($checkin['status']) {
                case 'present':
                    $present++;
                    break;
                case 'absent':
                    $absent++;
                    break;
                case 'late':
                    $late++;
                    if ($checkin['minutes_late'] > 0) {
                        $total_late_minutes += $checkin['minutes_late'];
                        $late_count++;
                    }
                    break;
                case 'early':
                    $early++;
                    break;
                case 'excused':
                    $excused++;
                    break;
            }
        }

        $avg_late_minutes = $late_count > 0 ? $total_late_minutes / $late_count : 0;

        // Get earliest and latest check-in times
        $actual_start_time = null;
        $actual_end_time = null;
        if (!empty($checkins)) {
            $times = array_filter(array_column($checkins, 'actual_time'));
            if (!empty($times)) {
                sort($times);
                $actual_start_time = $times[0];
                $actual_end_time = end($times);
            }
        }

        // Check if summary exists
        $existing = $this->getByCheckpoint($checkpoint_id, $cp_info['scheduled_date']);

        if ($existing) {
            // Update existing
            $sql = "UPDATE activity_checkin_summary SET
                        total_customers = :total_customers,
                        present_count = :present_count,
                        absent_count = :absent_count,
                        late_count = :late_count,
                        early_count = :early_count,
                        excused_count = :excused_count,
                        average_late_minutes = :average_late_minutes,
                        actual_start_time = :actual_start_time,
                        actual_end_time = :actual_end_time,
                        updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'id' => $existing['id'],
                'total_customers' => $total,
                'present_count' => $present,
                'absent_count' => $absent,
                'late_count' => $late,
                'early_count' => $early,
                'excused_count' => $excused,
                'average_late_minutes' => round($avg_late_minutes, 2),
                'actual_start_time' => $actual_start_time,
                'actual_end_time' => $actual_end_time
            ]);
        } else {
            // Create new
            $sql = "INSERT INTO activity_checkin_summary (
                        tour_schedule_id, activity_checkpoint_id, checkpoint_date,
                        scheduled_start_time, actual_start_time, actual_end_time,
                        total_customers, present_count, absent_count, late_count,
                        early_count, excused_count, average_late_minutes
                    ) VALUES (
                        :tour_schedule_id, :activity_checkpoint_id, :checkpoint_date,
                        :scheduled_start_time, :actual_start_time, :actual_end_time,
                        :total_customers, :present_count, :absent_count, :late_count,
                        :early_count, :excused_count, :average_late_minutes
                    )";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'tour_schedule_id' => $cp_info['tour_schedule_id'],
                'activity_checkpoint_id' => $checkpoint_id,
                'checkpoint_date' => $cp_info['scheduled_date'],
                'scheduled_start_time' => $cp_info['scheduled_time'],
                'actual_start_time' => $actual_start_time,
                'actual_end_time' => $actual_end_time,
                'total_customers' => $total,
                'present_count' => $present,
                'absent_count' => $absent,
                'late_count' => $late,
                'early_count' => $early,
                'excused_count' => $excused,
                'average_late_minutes' => round($avg_late_minutes, 2)
            ]);
        }
    }

    /**
     * Bắt đầu checkpoint
     */
    public function startCheckpoint($checkpoint_id, $started_by)
    {
        $summary = $this->getByCheckpoint($checkpoint_id);
        
        if (!$summary) {
            // Create summary first
            $this->updateSummary($checkpoint_id);
            $summary = $this->getByCheckpoint($checkpoint_id);
        }

        $sql = "UPDATE activity_checkin_summary SET
                    status = 'in_progress',
                    started_by = :started_by,
                    actual_start_time = :actual_start_time,
                    updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $summary['id'],
            'started_by' => $started_by,
            'actual_start_time' => date('H:i:s')
        ]);
    }

    /**
     * Hoàn thành checkpoint
     */
    public function completeCheckpoint($checkpoint_id, $completed_by)
    {
        // Update summary first
        $this->updateSummary($checkpoint_id);
        
        $summary = $this->getByCheckpoint($checkpoint_id);
        
        if (!$summary) {
            throw new Exception("Summary không tồn tại.");
        }

        $sql = "UPDATE activity_checkin_summary SET
                    status = 'completed',
                    completed_by = :completed_by,
                    actual_end_time = :actual_end_time,
                    updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $summary['id'],
            'completed_by' => $completed_by,
            'actual_end_time' => date('H:i:s')
        ]);
    }
}

