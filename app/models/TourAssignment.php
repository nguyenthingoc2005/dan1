<?php
/**
 * ==============================================================================
 * TOUR ASSIGNMENT MODEL
 * ==============================================================================
 * 
 * Quản lý phân công hướng dẫn viên với:
 * - Kiểm tra lịch trùng
 * - Tính lương tự động
 * - Validate guide role
 * 
 * @version 1.1
 * @date 2024-12-03
 * ==============================================================================
 */

class TourAssignment
{
    private $pdo;
    
    // Lương mặc định theo ngày (có thể cấu hình)
    const DEFAULT_DAILY_SALARY = 500000; // 500k/ngày

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // ========================================================================
    // VALIDATION METHODS
    // ========================================================================

    /**
     * Kiểm tra user có phải là Guide không
     */
    public function isGuide($userId)
    {
        $sql = "SELECT u.id FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE u.id = :id AND r.name = 'guide' AND u.status = 'active'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch() !== false;
    }

    /**
     * Kiểm tra Guide có rảnh trong khoảng thời gian không
     * @param int $guideId
     * @param string $startDate
     * @param string $endDate
     * @param int|null $excludeAssignmentId - Loại trừ khi update
     * @return array ['available' => bool, 'conflict' => null|array]
     */
    public function checkGuideAvailability($guideId, $startDate, $endDate, $excludeAssignmentId = null)
    {
        // Tìm các assignment của guide trong khoảng thời gian trùng
        // Trùng lịch: (start1 <= end2) AND (end1 >= start2)
        $sql = "SELECT ta.id, ta.booking_id, b.booking_code, b.start_date, b.end_date, t.name as tour_name
                FROM tour_assignments ta
                JOIN bookings b ON ta.booking_id = b.id
                JOIN tours t ON b.tour_id = t.id
                WHERE ta.guide_id = :guide_id
                  AND ta.status NOT IN ('cancelled', 'completed')
                  AND b.approval_status NOT IN ('cancelled', 'rejected')
                  AND b.start_date <= :end_date
                  AND b.end_date >= :start_date";
        
        $params = [
            'guide_id' => $guideId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        if ($excludeAssignmentId) {
            $sql .= " AND ta.id != :exclude_id";
            $params['exclude_id'] = $excludeAssignmentId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $conflict = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($conflict) {
            return [
                'available' => false,
                'conflict' => $conflict
            ];
        }
        
        // Cũng check trong tour_schedules nếu guide được assign trực tiếp
        $sql2 = "SELECT ts.id, ts.start_date, ts.end_date, t.name as tour_name
                 FROM tour_schedules ts
                 JOIN tours t ON ts.tour_id = t.id
                 WHERE ts.guide_id = :guide_id
                   AND ts.status NOT IN ('cancelled', 'completed')
                   AND ts.start_date <= :end_date
                   AND ts.end_date >= :start_date";
        
        $stmt2 = $this->pdo->prepare($sql2);
        $stmt2->execute([
            'guide_id' => $guideId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        $conflict2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        if ($conflict2) {
            return [
                'available' => false,
                'conflict' => $conflict2
            ];
        }
        
        return ['available' => true, 'conflict' => null];
    }

    /**
     * Tính lương HDV dựa trên số ngày tour
     * @param int $durationDays
     * @param float|null $customDailyRate
     * @return float
     */
    public function calculateSalary($durationDays, $customDailyRate = null)
    {
        $dailyRate = $customDailyRate ?? self::DEFAULT_DAILY_SALARY;
        return $durationDays * $dailyRate;
    }

    /**
     * Lấy danh sách Guide có sẵn trong khoảng thời gian
     */
    public function getAvailableGuides($startDate, $endDate)
    {
        // Lấy tất cả guides active
        $sql = "SELECT u.id, u.full_name, u.phone, u.email,
                       (SELECT COUNT(*) FROM tour_assignments ta 
                        JOIN bookings b ON ta.booking_id = b.id
                        WHERE ta.guide_id = u.id 
                        AND ta.status NOT IN ('cancelled', 'completed')
                        AND b.start_date >= CURDATE()) as current_tours
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE r.name = 'guide' AND u.status = 'active'
                ORDER BY current_tours ASC, u.full_name ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $allGuides = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Filter những guide có sẵn
        $availableGuides = [];
        foreach ($allGuides as $guide) {
            $check = $this->checkGuideAvailability($guide['id'], $startDate, $endDate);
            if ($check['available']) {
                $availableGuides[] = $guide;
            }
        }
        
        return $availableGuides;
    }

    // ========================================================================
    // CRUD METHODS
    // ========================================================================

    /**
     * Phân công guide cho schedule (với validation đầy đủ)
     * 
     * @param int $scheduleId
     * @param int $guideId
     * @param array $data Optional: salary_amount, notes, created_by
     * @return array ['success' => bool, 'message' => string, 'id' => int|null]
     */
    public function assignToSchedule($scheduleId, $guideId, $data = [])
    {
        try {
            $this->pdo->beginTransaction();
            
            // 1. Validate Guide is actually a guide
            if (!$this->isGuide($guideId)) {
                throw new Exception("Người dùng này không phải là Hướng dẫn viên hoặc đã bị vô hiệu hóa");
            }
            
            // 2. Get schedule info
            $sql = "SELECT ts.*, t.name as tour_name, t.duration_days
                    FROM tour_schedules ts
                    JOIN tours t ON ts.tour_id = t.id
                    WHERE ts.id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $scheduleId]);
            $schedule = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$schedule) {
                throw new Exception("Lịch khởi hành không tồn tại");
            }
            
            // 3. Check guide availability
            $availability = $this->checkGuideAvailability($guideId, $schedule['start_date'], $schedule['end_date']);
            if (!$availability['available']) {
                $conflict = $availability['conflict'];
                throw new Exception("HDV đã có lịch trùng: " . $conflict['tour_name'] . 
                    " ({$conflict['start_date']} - {$conflict['end_date']})");
            }
            
            // 4. Calculate salary if not provided
            $salary = $data['salary_amount'] ?? $this->calculateSalary($schedule['duration_days']);
            
            // 5. Update tour_schedules with guide_id
            $updateSql = "UPDATE tour_schedules SET guide_id = :guide_id, guide_notes = :notes WHERE id = :id";
            $this->pdo->prepare($updateSql)->execute([
                'guide_id' => $guideId,
                'notes' => $data['notes'] ?? null,
                'id' => $scheduleId
            ]);
            
            // 6. Also create tour_assignment record for any bookings in this schedule
            // Find all approved bookings for this schedule
            $bookingsSql = "SELECT id FROM bookings 
                           WHERE tour_id = :tour_id 
                           AND start_date = :start_date 
                           AND end_date = :end_date
                           AND approval_status IN ('pending', 'approved')";
            $bookingsStmt = $this->pdo->prepare($bookingsSql);
            $bookingsStmt->execute([
                'tour_id' => $schedule['tour_id'],
                'start_date' => $schedule['start_date'],
                'end_date' => $schedule['end_date']
            ]);
            $bookings = $bookingsStmt->fetchAll(PDO::FETCH_COLUMN);
            
            $assignmentId = null;
            foreach ($bookings as $bookingId) {
                // Check if assignment already exists
                $checkSql = "SELECT id FROM tour_assignments WHERE tour_schedule_id = :schedule_id AND booking_id = :booking_id";
                $checkStmt = $this->pdo->prepare($checkSql);
                $checkStmt->execute(['schedule_id' => $scheduleId, 'booking_id' => $bookingId]);
                
                if (!$checkStmt->fetch()) {
                    $insertSql = "INSERT INTO tour_assignments (
                        tour_schedule_id, booking_id, guide_id, assignment_date,
                        salary_amount, salary_status, notes, status, created_by
                    ) VALUES (
                        :schedule_id, :booking_id, :guide_id, :assignment_date,
                        :salary, :salary_status, :notes, :status, :created_by
                    )";
                    
                    $this->pdo->prepare($insertSql)->execute([
                        'schedule_id' => $scheduleId,
                        'booking_id' => $bookingId,
                        'guide_id' => $guideId,
                        'assignment_date' => date('Y-m-d'),
                        'salary' => $salary,
                        'salary_status' => 'pending',
                        'notes' => $data['notes'] ?? null,
                        'status' => 'assigned',
                        'created_by' => $data['created_by'] ?? ($_SESSION['user_id'] ?? null)
                    ]);
                    
                    if (!$assignmentId) {
                        $assignmentId = $this->pdo->lastInsertId();
                    }
                }
            }
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => "Phân công HDV thành công! Lương dự kiến: " . number_format($salary) . " VNĐ",
                'id' => $assignmentId,
                'salary' => $salary
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'id' => null
            ];
        }
    }

    /**
     * Phân công guide cho booking (legacy method - backward compatibility)
     */
    public function assign($booking_id, $guide_id, $assignment_date = null, $data = [])
    {
        // Default assignment_date to today if not provided
        if (!$assignment_date) {
            $assignment_date = date('Y-m-d');
        }

        $sql = "INSERT INTO tour_assignments (
            booking_id, guide_id, assignment_date, 
            salary_amount, salary_status, notes, status, created_by
        ) VALUES (
            :booking_id, :guide_id, :assignment_date,
            :salary_amount, :salary_status, :notes, :status, :created_by
        )";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'booking_id' => $booking_id,
            'guide_id' => $guide_id,
            'assignment_date' => $assignment_date,
            'salary_amount' => $data['salary_amount'] ?? null,
            'salary_status' => $data['salary_status'] ?? 'pending',
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'] ?? 'assigned',
            'created_by' => $data['created_by'] ?? ($_SESSION['user_id'] ?? null)
        ]);
    }

    /**
     * Hủy phân công
     */
    public function removeAssignment($scheduleId)
    {
        try {
            $this->pdo->beginTransaction();
            
            // Update tour_schedules
            $sql = "UPDATE tour_schedules SET guide_id = NULL, guide_notes = NULL WHERE id = :id";
            $this->pdo->prepare($sql)->execute(['id' => $scheduleId]);
            
            // Update tour_assignments status
            $sql2 = "UPDATE tour_assignments SET status = 'cancelled' WHERE tour_schedule_id = :schedule_id";
            $this->pdo->prepare($sql2)->execute(['schedule_id' => $scheduleId]);
            
            $this->pdo->commit();
            return true;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Lấy danh sách assignments theo booking
     */
    public function getByBooking($booking_id)
    {
        $sql = "SELECT ta.*, u.full_name, u.phone, u.email 
                FROM tour_assignments ta
                JOIN users u ON ta.guide_id = u.id
                WHERE ta.booking_id = :booking_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['booking_id' => $booking_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách assignments theo schedule (backward compatibility)
     * Tìm qua bookings có cùng tour và ngày khởi hành với schedule
     */
    public function getBySchedule($schedule_id)
    {
        $sql = "SELECT ta.*, u.full_name, u.phone, u.email 
                FROM tour_assignments ta
                JOIN bookings b ON ta.booking_id = b.id
                JOIN tour_schedules ts ON (b.tour_id = ts.tour_id AND b.start_date = ts.start_date)
                JOIN users u ON ta.guide_id = u.id
                WHERE ts.id = :schedule_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['schedule_id' => $schedule_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy assignment theo guide (Lịch làm việc của guide)
     */
    public function getByGuide($guide_id)
    {
        $sql = "SELECT ta.*, b.start_date, b.end_date, t.name as tour_name, b.booking_code
                FROM tour_assignments ta
                JOIN bookings b ON ta.booking_id = b.id
                JOIN tours t ON b.tour_id = t.id
                WHERE ta.guide_id = :guide_id
                ORDER BY b.start_date ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['guide_id' => $guide_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
