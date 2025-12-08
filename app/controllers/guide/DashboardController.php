<?php
namespace Guide;

/**
 * GUIDE DASHBOARD CONTROLLER
 * Dashboard với metrics chi tiết và thông tin hữu ích
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
        $user = get_auth_user();

        require_once MODELS_PATH . '/TourSchedule.php';
        require_once MODELS_PATH . '/Booking.php';
        require_once MODELS_PATH . '/IncurredExpense.php';
        require_once MODELS_PATH . '/Journal.php';
        require_once MODELS_PATH . '/Checkin.php';
        
        $scheduleModel = new \TourSchedule($this->db);
        $bookingModel = new \Booking($this->db);
        $expenseModel = new \IncurredExpense($this->db);
        $journalModel = new \Journal($this->db);
        $checkinModel = new \Checkin($this->db);

        // ====================================================================
        // THỐNG KÊ CÁ NHÂN
        // ====================================================================
        
        // 1. Tour đang diễn ra (start_date <= today AND end_date >= today)
        $ongoing_tours_sql = "SELECT COUNT(*) 
                              FROM tour_schedules 
                              WHERE guide_id = :guide_id 
                                AND start_date <= :today_start 
                                AND end_date >= :today_end";
        $stmt = $this->db->prepare($ongoing_tours_sql);
        $stmt->execute(['guide_id' => $user_id, 'today_start' => $today, 'today_end' => $today]);
        $ongoing_tours_count = (int) $stmt->fetchColumn();

        // 2. Tour sắp tới (start_date > today)
        $upcoming_tours_count = (int) $scheduleModel->count([
            'guide_id' => $user_id,
            'start_date' => date('Y-m-d', strtotime('+1 day'))
        ]);

        // 3. Tour sắp tới trong 7 ngày tới
        $next_7_days = date('Y-m-d', strtotime('+7 days'));
        $upcoming_7days_sql = "SELECT COUNT(*) 
                               FROM tour_schedules 
                               WHERE guide_id = :guide_id 
                                 AND start_date > :today 
                                 AND start_date <= :next_7_days";
        $stmt = $this->db->prepare($upcoming_7days_sql);
        $stmt->execute(['guide_id' => $user_id, 'today' => $today, 'next_7_days' => $next_7_days]);
        $upcoming_7days_count = (int) $stmt->fetchColumn();

        // 4. Tour đã hoàn thành (end_date < today)
        $completed_tours_sql = "SELECT COUNT(*) 
                                FROM tour_schedules 
                                WHERE guide_id = :guide_id 
                                  AND end_date < :today";
        $stmt = $this->db->prepare($completed_tours_sql);
        $stmt->execute(['guide_id' => $user_id, 'today' => $today]);
        $completed_tours_count = (int) $stmt->fetchColumn();

        // 5. Tổng số ngày làm việc (tổng duration_days của các tour đã hoàn thành)
        $total_working_days_sql = "SELECT COALESCE(SUM(t.duration_days), 0) as total_days
                                    FROM tour_schedules ts
                                    JOIN tours t ON ts.tour_id = t.id
                                    WHERE ts.guide_id = :guide_id 
                                      AND ts.end_date < :today";
        $stmt = $this->db->prepare($total_working_days_sql);
        $stmt->execute(['guide_id' => $user_id, 'today' => $today]);
        $total_working_days = (int) $stmt->fetchColumn();

        // 6. Số khách hàng đã phục vụ (từ bookings trong các tour đã hoàn thành)
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

        // 7. Tổng hành khách trong các tour sắp tới
        $upcoming_passengers_sql = "SELECT COUNT(DISTINCT bc.customer_id) as total
                                    FROM bookings b
                                    JOIN tour_schedules ts ON (b.tour_schedule_id = ts.id OR (b.tour_id = ts.tour_id AND b.start_date = ts.start_date))
                                    JOIN booking_customers bc ON b.id = bc.booking_id
                                    WHERE ts.guide_id = :guide_id
                                      AND ts.start_date >= :today
                                      AND b.payment_status IN ('paid', 'partial')
                                      AND bc.customer_id IS NOT NULL";
        $stmt = $this->db->prepare($upcoming_passengers_sql);
        $stmt->execute(['guide_id' => $user_id, 'today' => $today]);
        $upcoming_passengers_count = (int) $stmt->fetchColumn();

        // 8. Tổng chi phí phát sinh đã ghi (tất cả, không chỉ đã duyệt)
        $total_expenses_sql = "SELECT COALESCE(SUM(ie.amount), 0) as total
                               FROM incurred_expenses ie
                               LEFT JOIN bookings b ON ie.booking_id = b.id
                               LEFT JOIN tour_schedules ts ON (ie.tour_schedule_id = ts.id OR 
                                                              (ie.tour_schedule_id IS NULL AND b.tour_schedule_id = ts.id) OR
                                                              (ie.tour_schedule_id IS NULL AND b.tour_schedule_id IS NULL 
                                                               AND b.tour_id = ts.tour_id AND b.start_date = ts.start_date))
                               WHERE ts.guide_id = :guide_id
                                 AND ie.reported_by = :user_id";
        $stmt = $this->db->prepare($total_expenses_sql);
        $stmt->execute(['guide_id' => $user_id, 'user_id' => $user_id]);
        $total_expenses = (float) $stmt->fetchColumn();

        // 9. Chi phí chờ duyệt
        $pending_expenses_sql = "SELECT COUNT(*) as total
                                 FROM incurred_expenses ie
                                 LEFT JOIN bookings b ON ie.booking_id = b.id
                                 LEFT JOIN tour_schedules ts ON (ie.tour_schedule_id = ts.id OR 
                                                                (ie.tour_schedule_id IS NULL AND b.tour_schedule_id = ts.id) OR
                                                                (ie.tour_schedule_id IS NULL AND b.tour_schedule_id IS NULL 
                                                                 AND b.tour_id = ts.tour_id AND b.start_date = ts.start_date))
                                 WHERE ts.guide_id = :guide_id
                                   AND ie.reported_by = :user_id
                                   AND ie.approval_status = 'pending'";
        $stmt = $this->db->prepare($pending_expenses_sql);
        $stmt->execute(['guide_id' => $user_id, 'user_id' => $user_id]);
        $pending_expenses_count = (int) $stmt->fetchColumn();

        // 10. Số nhật ký đã viết
        $journals_count = $journalModel->count(['guide_id' => $user_id]);

        // 11. Tổng số tour đã được phân công (tất cả)
        $total_tours_assigned = (int) $scheduleModel->count([
            'guide_id' => $user_id
        ]);

        // 12. Check-in statistics (tổng quan - chỉ tour sắp tới và đang diễn ra)
        $checkin_stats_sql = "SELECT 
                                COUNT(DISTINCT CASE WHEN cc.id IS NOT NULL THEN bc.customer_id END) as total_checked_in,
                                COUNT(DISTINCT bc.customer_id) as total_passengers
                              FROM bookings b
                              JOIN tour_schedules ts ON (b.tour_schedule_id = ts.id OR (b.tour_id = ts.tour_id AND b.start_date = ts.start_date))
                              JOIN booking_customers bc ON b.id = bc.booking_id
                              LEFT JOIN customer_checkins cc ON cc.booking_id = b.id AND cc.customer_id = bc.customer_id
                              WHERE ts.guide_id = :guide_id
                                AND ts.start_date >= :today
                                AND b.payment_status IN ('paid', 'partial')
                                AND bc.customer_id IS NOT NULL
                                AND (b.remaining_amount = 0 OR b.remaining_amount IS NULL)";
        $stmt = $this->db->prepare($checkin_stats_sql);
        $stmt->execute(['guide_id' => $user_id, 'today' => $today]);
        $checkin_stats = $stmt->fetch(\PDO::FETCH_ASSOC);
        $checkin_percentage = ($checkin_stats['total_passengers'] ?? 0) > 0 
            ? round((($checkin_stats['total_checked_in'] ?? 0) / ($checkin_stats['total_passengers'] ?? 1)) * 100, 1)
            : 0;

        // ====================================================================
        // TOUR ĐANG DIỄN RA
        // ====================================================================
        $ongoing_tours_sql = "SELECT ts.*, t.name as tour_name, t.tour_code, t.duration_days, t.duration_nights
                              FROM tour_schedules ts
                              JOIN tours t ON ts.tour_id = t.id
                              WHERE ts.guide_id = :guide_id 
                                AND ts.start_date <= :today_start 
                                AND ts.end_date >= :today_end
                              ORDER BY ts.start_date ASC
                              LIMIT 3";
        $stmt = $this->db->prepare($ongoing_tours_sql);
        $stmt->execute(['guide_id' => $user_id, 'today_start' => $today, 'today_end' => $today]);
        $ongoing_tours = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // ====================================================================
        // TOUR SẮP TỚI (5 tour gần nhất)
        // ====================================================================
        $my_schedules = $scheduleModel->getAll([
            'guide_id' => $user_id,
            'start_date' => date('Y-m-d', strtotime('+1 day'))
        ], 1, 5)['data'];

        // Thêm thông tin thêm cho mỗi tour sắp tới
        foreach ($my_schedules as &$schedule) {
            // Đếm số hành khách
            $passengers_sql = "SELECT COUNT(DISTINCT bc.customer_id) as count
                               FROM bookings b
                               JOIN booking_customers bc ON b.id = bc.booking_id
                               WHERE (b.tour_schedule_id = :schedule_id OR 
                                      (b.tour_id = :tour_id AND b.start_date = :start_date))
                                 AND b.payment_status IN ('paid', 'partial')
                                 AND bc.customer_id IS NOT NULL";
            $stmt = $this->db->prepare($passengers_sql);
            $stmt->execute([
                'schedule_id' => $schedule['id'],
                'tour_id' => $schedule['tour_id'],
                'start_date' => $schedule['start_date']
            ]);
            $schedule['passengers_count'] = (int) $stmt->fetchColumn();
        }

        // ====================================================================
        // TOUR ĐÃ HOÀN THÀNH (5 tour gần nhất)
        // ====================================================================
        $completed_tours = $scheduleModel->getAll([
            'guide_id' => $user_id,
            'end_date' => date('Y-m-d', strtotime('-1 day'))
        ], 1, 5)['data'];

        // ====================================================================
        // NHẬT KÝ GẦN ĐÂY (3 nhật ký mới nhất)
        // ====================================================================
        $recent_journals_all = $journalModel->getAll(['guide_id' => $user_id], 1, 3);
        $recent_journals = array_slice($recent_journals_all, 0, 3);

        // Assemble stats
        $stats = [
            'ongoing_tours' => $ongoing_tours_count,
            'upcoming_tours' => $upcoming_tours_count,
            'upcoming_7days' => $upcoming_7days_count,
            'completed_tours' => $completed_tours_count,
            'total_tours_assigned' => $total_tours_assigned,
            'total_working_days' => $total_working_days,
            'customers_served' => $customers_served_count,
            'upcoming_passengers' => $upcoming_passengers_count,
            'total_expenses' => $total_expenses,
            'pending_expenses' => $pending_expenses_count,
            'journals_count' => $journals_count,
            'checkin_stats' => [
                'total_checked_in' => $checkin_stats['total_checked_in'] ?? 0,
                'total_passengers' => $checkin_stats['total_passengers'] ?? 0,
                'percentage' => $checkin_percentage
            ]
        ];

        $page_title = 'Dashboard Hướng Dẫn Viên';
        $content_file = VIEWS_PATH . '/guide/dashboard.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }
}
