<?php
/**
 * ==============================================================================
 * CHECKIN MODEL
 * ==============================================================================
 * 
 * Quản lý check-in hành khách cho tour
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class Checkin
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy danh sách check-in theo booking_id
     */
    public function getByBooking($booking_id)
    {
        $sql = "SELECT cc.*, c.full_name, c.phone, c.email
                FROM customer_checkins cc
                JOIN customers c ON cc.customer_id = c.id
                WHERE cc.booking_id = :booking_id
                ORDER BY cc.checkin_time DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['booking_id' => $booking_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy check-in theo tour_schedule_id (tất cả bookings trong schedule)
     */
    public function getBySchedule($schedule_id)
    {
        try {
            // Ưu tiên: booking.tour_schedule_id trực tiếp > tour_id + start_date
            $sql = "SELECT cc.*, c.full_name, c.phone, c.email, b.booking_code
                    FROM customer_checkins cc
                    JOIN customers c ON cc.customer_id = c.id
                    JOIN bookings b ON cc.booking_id = b.id
                    JOIN tour_schedules ts ON (
                        b.tour_schedule_id = ts.id 
                        OR (b.tour_schedule_id IS NULL AND b.tour_id = ts.tour_id AND b.start_date = ts.start_date)
                    )
                    WHERE ts.id = :schedule_id
                    ORDER BY cc.checkin_time DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['schedule_id' => $schedule_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Checkin::getBySchedule() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy check-in status của một customer trong một booking
     */
    public function getCustomerCheckin($booking_id, $customer_id)
    {
        $sql = "SELECT * FROM customer_checkins 
                WHERE booking_id = :booking_id AND customer_id = :customer_id
                ORDER BY checkin_time DESC LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'booking_id' => $booking_id,
            'customer_id' => $customer_id
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo hoặc cập nhật check-in
     * 
     * FIX: Xóa các records trùng lặp cũ trước khi INSERT/UPDATE
     * để đảm bảo chỉ có 1 check-in cho mỗi booking_id + customer_id
     */
    public function checkin($booking_id, $customer_id, $status, $notes = null, $checked_by = null)
    {
        // Validate customer_id tồn tại trong bảng customers
        $stmt = $this->pdo->prepare("SELECT id FROM customers WHERE id = :id");
        $stmt->execute(['id' => $customer_id]);
        if (!$stmt->fetch()) {
            throw new Exception("Customer ID không hợp lệ hoặc không tồn tại trong hệ thống.");
        }

        // Check if already exists
        $existing = $this->getCustomerCheckin($booking_id, $customer_id);

        if ($existing) {
            // Xóa các records trùng lặp cũ (nếu có) - chỉ giữ lại record mới nhất
            // Điều này đảm bảo không có nhiều records cho cùng booking_id + customer_id
            $sql_delete_duplicates = "DELETE FROM customer_checkins 
                                     WHERE booking_id = :booking_id 
                                     AND customer_id = :customer_id 
                                     AND id != :keep_id";
            $stmt = $this->pdo->prepare($sql_delete_duplicates);
            $stmt->execute([
                'booking_id' => $booking_id,
                'customer_id' => $customer_id,
                'keep_id' => $existing['id']
            ]);

            // Update existing
            $sql = "UPDATE customer_checkins 
                    SET status = :status, notes = :notes, checkin_time = NOW(), checked_by = :checked_by
                    WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'id' => $existing['id'],
                'status' => $status,
                'notes' => $notes,
                'checked_by' => $checked_by
            ]);
        } else {
            // Trước khi INSERT, xóa các records trùng lặp cũ (nếu có) để đảm bảo an toàn
            $sql_delete_duplicates = "DELETE FROM customer_checkins 
                                     WHERE booking_id = :booking_id 
                                     AND customer_id = :customer_id";
            $stmt = $this->pdo->prepare($sql_delete_duplicates);
            $stmt->execute([
                'booking_id' => $booking_id,
                'customer_id' => $customer_id
            ]);

            // Create new
            $sql = "INSERT INTO customer_checkins (booking_id, customer_id, status, notes, checked_by, checkin_time)
                    VALUES (:booking_id, :customer_id, :status, :notes, :checked_by, NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'booking_id' => $booking_id,
                'customer_id' => $customer_id,
                'status' => $status,
                'notes' => $notes,
                'checked_by' => $checked_by
            ]);
        }
    }

    /**
     * Batch check-in (nhiều khách cùng lúc)
     */
    public function batchCheckin($checkins, $checked_by = null)
    {
        try {
            $this->pdo->beginTransaction();

            foreach ($checkins as $checkin) {
                $this->checkin(
                    $checkin['booking_id'],
                    $checkin['customer_id'],
                    $checkin['status'],
                    $checkin['notes'] ?? null,
                    $checked_by
                );
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Lấy thống kê check-in cho một schedule
     */
    public function getStatsBySchedule($schedule_id)
    {
        try {
            // Get all passengers in this schedule
            // Ưu tiên: booking.tour_schedule_id trực tiếp > tour_id + start_date
            $sql = "SELECT bc.customer_id, bc.booking_id
                    FROM booking_customers bc
                    JOIN bookings b ON bc.booking_id = b.id
                    JOIN tour_schedules ts ON (
                        b.tour_schedule_id = ts.id 
                        OR (b.tour_schedule_id IS NULL AND b.tour_id = ts.tour_id AND b.start_date = ts.start_date)
                    )
                    WHERE ts.id = :schedule_id
                    AND b.payment_status IN ('partial', 'paid')";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['schedule_id' => $schedule_id]);
            $all_passengers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $total = count($all_passengers);

            // Get check-ins
            $checkins = $this->getBySchedule($schedule_id);
            $checked_in = count($checkins);
            $present = count(array_filter($checkins, fn($c) => $c['status'] == 'present'));
            $absent = count(array_filter($checkins, fn($c) => $c['status'] == 'absent'));
            $late = count(array_filter($checkins, fn($c) => $c['status'] == 'late'));

            return [
                'total' => $total,
                'checked_in' => $checked_in,
                'not_checked_in' => $total - $checked_in,
                'present' => $present,
                'absent' => $absent,
                'late' => $late
            ];
        } catch (\Exception $e) {
            error_log("Checkin::getStatsBySchedule() Error: " . $e->getMessage());
            return [
                'total' => 0,
                'checked_in' => 0,
                'not_checked_in' => 0,
                'present' => 0,
                'absent' => 0,
                'late' => 0
            ];
        }
    }
}

