<?php
/**
 * ==============================================================================
 * TOUR ASSIGNMENT MODEL
 * ==============================================================================
 */

class TourAssignment
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Phân công guide cho booking
     * 
     * @param int $booking_id ID của booking
     * @param int $guide_id ID của guide
     * @param string $assignment_date Ngày phân công (Y-m-d)
     * @param array $data Optional additional data (salary, notes, etc.)
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
