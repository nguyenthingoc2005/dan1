<?php
namespace Guide;

/**
 * GUIDE DASHBOARD CONTROLLER
 */
class DashboardController
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function index()
    {
        require_guide();
        $user_id = get_user_id();
        $today = date('Y-m-d');

        require_once MODELS_PATH . '/TourSchedule.php';
        require_once MODELS_PATH . '/Booking.php';
        require_once MODELS_PATH . '/IncurredExpense.php';
        require_once MODELS_PATH . '/Journal.php';
        
        $scheduleModel = new \TourSchedule($this->db);
        $bookingModel = new \Booking($this->db);
        $expenseModel = new \IncurredExpense($this->db);
        $journalModel = new \Journal($this->db);

        // ====================================================================
        // THỐNG KÊ CÁ NHÂN
        // ====================================================================
        
        // 1. Tour sắp tới (start_date >= today)
        $upcoming_tours_count = (int) $scheduleModel->count([
            'guide_id' => $user_id,
            'start_date' => $today
        ]);

        // 2. Tour đã hoàn thành (end_date < today)
        $completed_tours_sql = "SELECT COUNT(*) 
                                FROM tour_schedules 
                                WHERE guide_id = :guide_id 
                                  AND end_date < :today";
        $stmt = $this->db->prepare($completed_tours_sql);
        $stmt->execute(['guide_id' => $user_id, 'today' => $today]);
        $completed_tours_count = (int) $stmt->fetchColumn();

        // 3. Tổng số ngày làm việc (tổng duration_days của các tour đã hoàn thành)
        $total_working_days_sql = "SELECT COALESCE(SUM(t.duration_days), 0) as total_days
                                    FROM tour_schedules ts
                                    JOIN tours t ON ts.tour_id = t.id
                                    WHERE ts.guide_id = :guide_id 
                                      AND ts.end_date < :today";
        $stmt = $this->db->prepare($total_working_days_sql);
        $stmt->execute(['guide_id' => $user_id, 'today' => $today]);
        $total_working_days = (int) $stmt->fetchColumn();

        // 4. Số khách hàng đã phục vụ (từ bookings trong các tour đã hoàn thành)
        $customers_served_sql = "SELECT COUNT(DISTINCT bc.customer_id) as total_customers
                                 FROM bookings b
                                 JOIN tour_schedules ts ON (b.tour_schedule_id = ts.id OR (b.tour_id = ts.tour_id AND b.start_date = ts.start_date))
                                 JOIN booking_customers bc ON b.id = bc.booking_id
                                 WHERE ts.guide_id = :guide_id
                                   AND ts.end_date < :today
                                   AND b.payment_status IN ('paid', 'partial')
                                   AND bc.customer_id IS NOT NULL";
        $stmt = $this->db->prepare($customers_served_sql);
        $stmt->execute(['guide_id' => $user_id, 'today' => $today]);
        $customers_served_count = (int) $stmt->fetchColumn();

        // 5. Tổng chi phí phát sinh đã ghi (tất cả, không chỉ đã duyệt)
        $total_expenses_sql = "SELECT COALESCE(SUM(ie.amount), 0) as total
                               FROM incurred_expenses ie
                               JOIN bookings b ON ie.booking_id = b.id
                               JOIN tour_schedules ts ON (b.tour_schedule_id = ts.id OR (b.tour_id = ts.tour_id AND b.start_date = ts.start_date))
                               WHERE ts.guide_id = :guide_id
                                 AND ie.reported_by = :user_id";
        $stmt = $this->db->prepare($total_expenses_sql);
        $stmt->execute(['guide_id' => $user_id, 'user_id' => $user_id]);
        $total_expenses = (float) $stmt->fetchColumn();

        // 6. Số nhật ký đã viết
        $journals_count = $journalModel->count(['guide_id' => $user_id]);

        // 7. Tổng số tour đã được phân công (tất cả)
        $total_tours_assigned = (int) $scheduleModel->count([
            'guide_id' => $user_id
        ]);

        // ====================================================================
        // LỊCH SỬ TOUR ĐÃ HOÀN THÀNH (5 tour gần nhất)
        // ====================================================================
        $completed_tours = $scheduleModel->getAll([
            'guide_id' => $user_id,
            'end_date' => date('Y-m-d', strtotime('-1 day'))
        ], 1, 5)['data'];

        // ====================================================================
        // TOUR SẮP TỚI (5 tour gần nhất)
        // ====================================================================
        $my_schedules = $scheduleModel->getAll([
            'guide_id' => $user_id,
            'start_date' => $today
        ], 1, 5)['data'];

        // Assemble stats
        $stats = [
            'upcoming_tours' => $upcoming_tours_count,
            'completed_tours' => $completed_tours_count,
            'total_tours_assigned' => $total_tours_assigned,
            'total_working_days' => $total_working_days,
            'customers_served' => $customers_served_count,
            'total_expenses' => $total_expenses,
            'journals_count' => $journals_count
        ];

        $page_title = 'Dashboard Hướng Dẫn Viên';
        $content_file = VIEWS_PATH . '/guide/dashboard.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }
}
