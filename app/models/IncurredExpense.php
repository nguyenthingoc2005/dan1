<?php
/**
 * ==============================================================================
 * INCURRED EXPENSE MODEL
 * ==============================================================================
 * 
 * Quản lý chi phí phát sinh trong tour
 * Bảng: incurred_expenses
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class IncurredExpense
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy danh sách chi phí phát sinh theo tour_schedule_id
     */
    public function getByScheduleId($schedule_id)
    {
        try {
            // Lấy tour_schedule info một lần
            $scheduleStmt = $this->pdo->prepare("SELECT tour_id, start_date FROM tour_schedules WHERE id = ?");
            $scheduleStmt->execute([$schedule_id]);
            $schedule = $scheduleStmt->fetch(PDO::FETCH_ASSOC);

            if (!$schedule) {
                return [];
            }

            // Query đơn giản: ưu tiên tour_schedule_id trực tiếp, sau đó check qua booking
            // Sử dụng GROUP BY để tránh duplicate hoàn toàn
            $sql = "SELECT ie.id,
                           ie.booking_id,
                           ie.tour_schedule_id,
                           ie.expense_date,
                           ie.category,
                           ie.description,
                           ie.amount,
                           ie.receipt_file,
                           ie.reported_by,
                           ie.approved_by,
                           ie.approval_status,
                           ie.notes,
                           ie.created_at,
                           COALESCE(
                               (SELECT booking_code FROM bookings WHERE id = ie.booking_id LIMIT 1),
                               NULL
                           ) as booking_code,
                           COALESCE(
                               (SELECT full_name FROM users WHERE id = ie.reported_by LIMIT 1),
                               NULL
                           ) as reported_by_name,
                           COALESCE(
                               (SELECT full_name FROM users WHERE id = ie.approved_by LIMIT 1),
                               NULL
                           ) as approved_by_name
                    FROM incurred_expenses ie
                    WHERE (
                        -- Trường hợp 1: Expense có tour_schedule_id trực tiếp
                        ie.tour_schedule_id = :schedule_id1
                        OR
                        -- Trường hợp 2: Expense không có tour_schedule_id, nhưng booking có
                        (ie.tour_schedule_id IS NULL 
                         AND ie.booking_id IS NOT NULL
                         AND EXISTS (
                             SELECT 1 FROM bookings b 
                             WHERE b.id = ie.booking_id 
                             AND b.tour_schedule_id = :schedule_id2
                         ))
                        OR
                        -- Trường hợp 3: Expense không có tour_schedule_id, booking không có tour_schedule_id, nhưng match tour_id + start_date
                        (ie.tour_schedule_id IS NULL 
                         AND ie.booking_id IS NOT NULL
                         AND EXISTS (
                             SELECT 1 FROM bookings b 
                             WHERE b.id = ie.booking_id 
                             AND b.tour_schedule_id IS NULL
                             AND b.tour_id = :tour_id
                             AND b.start_date = :start_date
                         ))
                    )
                    GROUP BY ie.id
                    ORDER BY ie.expense_date DESC, ie.created_at DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'schedule_id1' => $schedule_id,
                'schedule_id2' => $schedule_id,
                'tour_id' => $schedule['tour_id'],
                'start_date' => $schedule['start_date']
            ]);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Đảm bảo không có duplicate dựa trên id
            $unique_results = [];
            $seen_ids = [];
            foreach ($results as $row) {
                $expense_id = $row['id'] ?? null;
                if ($expense_id && !in_array($expense_id, $seen_ids)) {
                    $unique_results[] = $row;
                    $seen_ids[] = $expense_id;
                }
            }
            
            return $unique_results;
        } catch (\Exception $e) {
            error_log("IncurredExpense::getByScheduleId() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách chi phí phát sinh theo booking_id
     */
    public function getByBookingId($booking_id)
    {
        $sql = "SELECT ie.*, 
                       b.booking_code,
                       u1.full_name as reported_by_name,
                       u2.full_name as approved_by_name
                FROM incurred_expenses ie
                JOIN bookings b ON ie.booking_id = b.id
                LEFT JOIN users u1 ON ie.reported_by = u1.id
                LEFT JOIN users u2 ON ie.approved_by = u2.id
                WHERE ie.booking_id = :booking_id
                ORDER BY ie.expense_date DESC, ie.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['booking_id' => $booking_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy chi tiết một chi phí
     */
    public function getById($id)
    {
        $sql = "SELECT ie.*, 
                       b.booking_code,
                       u1.full_name as reported_by_name,
                       u2.full_name as approved_by_name
                FROM incurred_expenses ie
                LEFT JOIN bookings b ON ie.booking_id = b.id
                LEFT JOIN users u1 ON ie.reported_by = u1.id
                LEFT JOIN users u2 ON ie.approved_by = u2.id
                WHERE ie.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo chi phí phát sinh mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO incurred_expenses (
                    booking_id, tour_schedule_id, expense_date, category, description, amount, 
                    receipt_file, reported_by, notes
                ) VALUES (
                    :booking_id, :tour_schedule_id, :expense_date, :category, :description, :amount,
                    :receipt_file, :reported_by, :notes
                )";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'booking_id' => $data['booking_id'] ?? null,
            'tour_schedule_id' => $data['tour_schedule_id'] ?? null,
            'expense_date' => $data['expense_date'],
            'category' => $data['category'] ?? null,
            'description' => $data['description'],
            'amount' => $data['amount'],
            'receipt_file' => $data['receipt_file'] ?? null,
            'reported_by' => $data['reported_by'],
            'notes' => $data['notes'] ?? null
        ]);
    }

    /**
     * Cập nhật chi phí phát sinh
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE incurred_expenses SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Xóa chi phí phát sinh
     */
    public function delete($id)
    {
        $sql = "DELETE FROM incurred_expenses WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Tính tổng chi phí phát sinh theo schedule
     */
    public function getTotalByScheduleId($schedule_id)
    {
        // Lấy tour_schedule info một lần
        $scheduleStmt = $this->pdo->prepare("SELECT tour_id, start_date FROM tour_schedules WHERE id = ?");
        $scheduleStmt->execute([$schedule_id]);
        $schedule = $scheduleStmt->fetch(PDO::FETCH_ASSOC);

        if (!$schedule) {
            return 0.00;
        }

        // Query đơn giản: check tour_schedule_id trực tiếp hoặc từ booking
        $sql = "SELECT SUM(ie.amount) as total
                FROM incurred_expenses ie
                LEFT JOIN bookings b ON ie.booking_id = b.id
                WHERE (ie.tour_schedule_id = :schedule_id1
                   OR (ie.tour_schedule_id IS NULL 
                       AND b.tour_schedule_id = :schedule_id2)
                   OR (ie.tour_schedule_id IS NULL 
                       AND b.tour_schedule_id IS NULL
                       AND b.tour_id = :tour_id
                       AND b.start_date = :start_date))
                  AND ie.approval_status = 'approved'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'schedule_id1' => $schedule_id,
            'schedule_id2' => $schedule_id,
            'tour_id' => $schedule['tour_id'],
            'start_date' => $schedule['start_date']
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (float) $result['total'] : 0.00;
    }

    /**
     * Lấy thống kê số lượng chi phí theo trạng thái duyệt (tổng hợp tất cả)
     */
    public function getApprovalStatistics()
    {
        try {
            $sql = "SELECT 
                        approval_status,
                        COUNT(*) as count,
                        SUM(amount) as total_amount
                    FROM incurred_expenses
                    GROUP BY approval_status";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stats = [
                'approved' => ['count' => 0, 'total' => 0],
                'pending' => ['count' => 0, 'total' => 0],
                'rejected' => ['count' => 0, 'total' => 0],
                'total' => ['count' => 0, 'total' => 0]
            ];
            
            foreach ($results as $row) {
                $status = $row['approval_status'] ?? 'pending';
                $stats[$status] = [
                    'count' => (int) $row['count'],
                    'total' => (float) $row['total_amount']
                ];
                $stats['total']['count'] += (int) $row['count'];
                $stats['total']['total'] += (float) $row['total_amount'];
            }
            
            return $stats;
        } catch (\Exception $e) {
            error_log("IncurredExpense::getApprovalStatistics() Error: " . $e->getMessage());
            return [
                'approved' => ['count' => 0, 'total' => 0],
                'pending' => ['count' => 0, 'total' => 0],
                'rejected' => ['count' => 0, 'total' => 0],
                'total' => ['count' => 0, 'total' => 0]
            ];
        }
    }

    /**
     * Đếm số lượng chi phí theo trạng thái duyệt cho một schedule
     */
    public function getCountByScheduleIdAndStatus($schedule_id, $status = null)
    {
        try {
            // Lấy tour_schedule info
            $scheduleStmt = $this->pdo->prepare("SELECT tour_id, start_date FROM tour_schedules WHERE id = ?");
            $scheduleStmt->execute([$schedule_id]);
            $schedule = $scheduleStmt->fetch(PDO::FETCH_ASSOC);

            if (!$schedule) {
                return 0;
            }

            $whereStatus = '';
            if ($status !== null) {
                $whereStatus = " AND ie.approval_status = :status";
            }

            $sql = "SELECT COUNT(*) as count
                    FROM incurred_expenses ie
                    LEFT JOIN bookings b ON ie.booking_id = b.id
                    WHERE (ie.tour_schedule_id = :schedule_id1
                       OR (ie.tour_schedule_id IS NULL 
                           AND b.tour_schedule_id = :schedule_id2)
                       OR (ie.tour_schedule_id IS NULL 
                           AND b.tour_schedule_id IS NULL
                           AND b.tour_id = :tour_id
                           AND b.start_date = :start_date))
                    $whereStatus";

            $params = [
                'schedule_id1' => $schedule_id,
                'schedule_id2' => $schedule_id,
                'tour_id' => $schedule['tour_id'],
                'start_date' => $schedule['start_date']
            ];
            
            if ($status !== null) {
                $params['status'] = $status;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int) $result['count'] : 0;
        } catch (\Exception $e) {
            error_log("IncurredExpense::getCountByScheduleIdAndStatus() Error: " . $e->getMessage());
            return 0;
        }
    }
}

