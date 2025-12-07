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
        $sql = "SELECT ie.*, 
                       b.booking_code,
                       u1.full_name as reported_by_name,
                       u2.full_name as approved_by_name
                FROM incurred_expenses ie
                JOIN bookings b ON ie.booking_id = b.id
                LEFT JOIN tour_schedules ts ON (b.tour_schedule_id = ts.id OR (b.tour_id = ts.tour_id AND b.start_date = ts.start_date))
                LEFT JOIN users u1 ON ie.reported_by = u1.id
                LEFT JOIN users u2 ON ie.approved_by = u2.id
                WHERE ts.id = :schedule_id
                ORDER BY ie.expense_date DESC, ie.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['schedule_id' => $schedule_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                JOIN bookings b ON ie.booking_id = b.id
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
                    booking_id, expense_date, category, description, amount, 
                    receipt_file, reported_by, notes
                ) VALUES (
                    :booking_id, :expense_date, :category, :description, :amount,
                    :receipt_file, :reported_by, :notes
                )";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'booking_id' => $data['booking_id'],
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
        $sql = "SELECT SUM(ie.amount) as total
                FROM incurred_expenses ie
                JOIN bookings b ON ie.booking_id = b.id
                LEFT JOIN tour_schedules ts ON (b.tour_schedule_id = ts.id OR (b.tour_id = ts.tour_id AND b.start_date = ts.start_date))
                WHERE ts.id = :schedule_id
                  AND ie.approval_status = 'approved'";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['schedule_id' => $schedule_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (float) $result['total'] : 0.00;
    }
}

