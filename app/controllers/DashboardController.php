<?php
/**
 * ==============================================================================
 * DASHBOARD CONTROLLER - Dashboard Logic
 * ==============================================================================
 * 
 * Xử lý dashboard cho 3 roles: Admin, Staff, Guide
 * 
 * @version 1.0
 * @date 2024-12-01
 * ==============================================================================
 */

class DashboardController
{
    private $db;

    /**
     * Constructor
     */
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Admin Dashboard
     * 
     * @return void
     */
    public function adminDashboard()
    {
        // Require admin permission
        require_admin();

        try {
            // ====================================================================
            // OVERVIEW STATISTICS
            // ====================================================================

            // 1. Tổng số bookings
            $sql_total_bookings = "SELECT COUNT(*) as total FROM bookings";
            $stmt = $this->db->prepare($sql_total_bookings);
            $stmt->execute();
            $total_bookings = (int) $stmt->fetchColumn();
            
            // Debug: Log query result
            if (defined('APP_ENV') && APP_ENV === 'development' && isset($_GET['debug'])) {
                error_log("DEBUG: Total bookings query result: " . $total_bookings);
            }

            // 2. Số bookings đã thanh toán
            $sql_approved_bookings = "SELECT COUNT(*) as total FROM bookings WHERE payment_status IN ('partial', 'paid')";
            $stmt = $this->db->prepare($sql_approved_bookings);
            $stmt->execute();
            $approved_bookings = (int) $stmt->fetchColumn();

            // 3. Số bookings chờ thanh toán
            $sql_pending_bookings = "SELECT COUNT(*) as total FROM bookings WHERE payment_status = 'unpaid'";
            $stmt = $this->db->prepare($sql_pending_bookings);
            $stmt->execute();
            $pending_bookings = (int) $stmt->fetchColumn();

            // 4. Tổng doanh thu
            $sql_total_revenue = "SELECT COALESCE(SUM(amount), 0) as total 
                                 FROM payments 
                                 WHERE status = 'completed' 
                                 AND payment_type != 'refund'";
            $stmt = $this->db->prepare($sql_total_revenue);
            $stmt->execute();
            $total_revenue = (float) $stmt->fetchColumn();

            // 5. Doanh thu tháng này
            $sql_month_revenue = "SELECT COALESCE(SUM(amount), 0) as total 
                                 FROM payments 
                                 WHERE status = 'completed' 
                                 AND payment_type != 'refund'
                                 AND MONTH(payment_date) = MONTH(CURRENT_DATE())
                                 AND YEAR(payment_date) = YEAR(CURRENT_DATE())";
            $stmt = $this->db->prepare($sql_month_revenue);
            $stmt->execute();
            $month_revenue = (float) $stmt->fetchColumn();

            // 6. Số tours hoạt động - Kiểm tra tất cả status trước
            $sql_check_tours = "SELECT status, COUNT(*) as count FROM tours GROUP BY status";
            $stmt = $this->db->prepare($sql_check_tours);
            $stmt->execute();
            $tours_status_check = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug log
            if (defined('APP_ENV') && APP_ENV === 'development') {
                error_log("DEBUG: Tours status distribution: " . json_encode($tours_status_check));
            }
            
            $sql_active_tours = "SELECT COUNT(*) as total FROM tours WHERE status = 'active'";
            $stmt = $this->db->prepare($sql_active_tours);
            $stmt->execute();
            $active_tours = (int) $stmt->fetchColumn();

            // 7. Số tours chờ duyệt
            $sql_pending_tours = "SELECT COUNT(*) as total FROM tours WHERE status = 'pending'";
            $stmt = $this->db->prepare($sql_pending_tours);
            $stmt->execute();
            $pending_tours_count = (int) $stmt->fetchColumn();

            // 8. Tổng số khách hàng
            $sql_total_customers = "SELECT COUNT(*) as total FROM customers";
            $stmt = $this->db->prepare($sql_total_customers);
            $stmt->execute();
            $total_customers = (int) $stmt->fetchColumn();

            // 9. Tổng số nhân viên (staff) - Users table dùng role_id (foreign key)
            $sql_total_staff = "SELECT COUNT(*) as total 
                               FROM users u 
                               INNER JOIN roles r ON u.role_id = r.id 
                               WHERE r.name = 'staff' AND u.status = 'active'";
            $stmt = $this->db->prepare($sql_total_staff);
            $stmt->execute();
            $total_staff = (int) $stmt->fetchColumn();

            // 10. Tổng số hướng dẫn viên (guide)
            $sql_total_guides = "SELECT COUNT(*) as total 
                                FROM users u 
                                INNER JOIN roles r ON u.role_id = r.id 
                                WHERE r.name = 'guide' AND u.status = 'active'";
            $stmt = $this->db->prepare($sql_total_guides);
            $stmt->execute();
            $total_guides = (int) $stmt->fetchColumn();

            // 11. Số lịch tour sắp tới (7 ngày) - Status có thể là 'open', 'confirmed', 'in_progress'
            $sql_upcoming_schedules = "SELECT COUNT(*) as total 
                                      FROM tour_schedules 
                                      WHERE start_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                                      AND status IN ('open', 'confirmed', 'in_progress')";
            $stmt = $this->db->prepare($sql_upcoming_schedules);
            $stmt->execute();
            $upcoming_schedules = (int) $stmt->fetchColumn();

            // 12. Số lịch tour đang diễn ra
            $sql_active_schedules = "SELECT COUNT(*) as total 
                                    FROM tour_schedules 
                                    WHERE start_date <= CURDATE() 
                                    AND end_date >= CURDATE()
                                    AND status IN ('open', 'confirmed', 'in_progress')";
            $stmt = $this->db->prepare($sql_active_schedules);
            $stmt->execute();
            $active_schedules = (int) $stmt->fetchColumn();

            // 13. Tổng số xe
            $sql_total_vehicles = "SELECT COUNT(*) as total FROM vehicles WHERE status = 'active'";
            $stmt = $this->db->prepare($sql_total_vehicles);
            $stmt->execute();
            $total_vehicles = (int) $stmt->fetchColumn();

            // 14. Tổng số tài xế
            $sql_total_drivers = "SELECT COUNT(*) as total FROM drivers WHERE status = 'active'";
            $stmt = $this->db->prepare($sql_total_drivers);
            $stmt->execute();
            $total_drivers = (int) $stmt->fetchColumn();

            // ====================================================================
            // REVENUE TREND (6 tháng gần nhất)
            // ====================================================================
            $sql_revenue_trend = "SELECT 
                                    DATE_FORMAT(payment_date, '%Y-%m') as month,
                                    COALESCE(SUM(amount), 0) as revenue
                                  FROM payments 
                                  WHERE status = 'completed' 
                                  AND payment_type != 'refund'
                                  AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                                  GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
                                  ORDER BY month ASC";
            $stmt = $this->db->prepare($sql_revenue_trend);
            $stmt->execute();
            $revenue_trend_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Fill missing months with 0
            $revenue_trend = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = date('Y-m', strtotime("-$i months"));
                $found = false;
                foreach ($revenue_trend_raw as $row) {
                    if ($row['month'] == $month) {
                        $revenue_trend[] = $row;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $revenue_trend[] = ['month' => $month, 'revenue' => 0];
                }
            }

            // ====================================================================
            // BOOKING TREND (6 tháng gần nhất)
            // ====================================================================
            $sql_booking_trend = "SELECT 
                                    DATE_FORMAT(created_at, '%Y-%m') as month,
                                    COUNT(*) as count
                                  FROM bookings 
                                  WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                                  GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                                  ORDER BY month ASC";
            $stmt = $this->db->prepare($sql_booking_trend);
            $stmt->execute();
            $booking_trend_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Fill missing months with 0
            $booking_trend = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = date('Y-m', strtotime("-$i months"));
                $found = false;
                foreach ($booking_trend_raw as $row) {
                    if ($row['month'] == $month) {
                        $booking_trend[] = $row;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $booking_trend[] = ['month' => $month, 'count' => 0];
                }
            }

            // ====================================================================
            // BOOKING STATUS DISTRIBUTION
            // ====================================================================
            $sql_booking_status = "SELECT 
                                    payment_status,
                                    COUNT(*) as count
                                  FROM bookings 
                                  GROUP BY payment_status";
            $stmt = $this->db->prepare($sql_booking_status);
            $stmt->execute();
            $booking_status = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // ====================================================================
            // TOP TOURS BY REVENUE (Top 5)
            // ====================================================================
            $sql_top_tours = "SELECT 
                                t.id,
                                t.name,
                                COALESCE(SUM(p.amount), 0) as revenue,
                                COUNT(DISTINCT b.id) as booking_count
                              FROM tours t
                              INNER JOIN bookings b ON t.id = b.tour_id
                              LEFT JOIN payments p ON b.id = p.booking_id AND p.status = 'completed' AND p.payment_type != 'refund'
                              GROUP BY t.id, t.name
                              HAVING revenue > 0
                              ORDER BY revenue DESC
                              LIMIT 5";
            $stmt = $this->db->prepare($sql_top_tours);
            $stmt->execute();
            $top_tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // ====================================================================
            // RECENT BOOKINGS (10 mới nhất)
            // ====================================================================
            $sql_recent_bookings = "SELECT 
                                        b.id,
                                        b.booking_code,
                                        b.start_date,
                                        b.final_amount,
                                        b.payment_status,
                                        t.name as tour_name,
                                        c.full_name as customer_name
                                    FROM bookings b
                                    LEFT JOIN tours t ON b.tour_id = t.id
                                    LEFT JOIN customers c ON b.customer_id = c.id
                                    ORDER BY b.created_at DESC
                                    LIMIT 10";
            $stmt = $this->db->prepare($sql_recent_bookings);
            $stmt->execute();
            $recent_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // ====================================================================
            // PENDING TOURS (tours chờ duyệt)
            // ====================================================================
            $sql_pending_tours_list = "SELECT 
                                            t.id,
                                            t.name,
                                            t.duration_days,
                                            t.adult_price,
                                            t.created_at,
                                            u.full_name as staff_name
                                        FROM tours t
                                        LEFT JOIN users u ON t.created_by = u.id
                                        WHERE t.status = 'pending'
                                        ORDER BY t.created_at DESC
                                        LIMIT 10";
            $stmt = $this->db->prepare($sql_pending_tours_list);
            $stmt->execute();
            $pending_tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // ====================================================================
            // UPCOMING SCHEDULES (5 sắp tới)
            // ====================================================================
            $sql_upcoming_schedules_list = "SELECT 
                                                ts.id,
                                                ts.start_date,
                                                ts.end_date,
                                                ts.status,
                                                t.name as tour_name,
                                                COUNT(DISTINCT b.id) as booking_count
                                            FROM tour_schedules ts
                                            LEFT JOIN tours t ON ts.tour_id = t.id
                                            LEFT JOIN bookings b ON ts.id = b.tour_schedule_id
                                            WHERE ts.start_date >= CURDATE()
                                            AND ts.status IN ('open', 'confirmed', 'in_progress')
                                            GROUP BY ts.id, ts.start_date, ts.end_date, ts.status, t.name
                                            ORDER BY ts.start_date ASC
                                            LIMIT 5";
            $stmt = $this->db->prepare($sql_upcoming_schedules_list);
            $stmt->execute();
            $upcoming_schedules_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // ====================================================================
            // TOP STAFF BY BOOKINGS
            // ====================================================================
            $sql_top_staff = "SELECT 
                                u.id,
                                u.full_name,
                                COUNT(b.id) as booking_count,
                                COALESCE(SUM(b.final_amount), 0) as total_value
                              FROM users u
                              INNER JOIN roles r ON u.role_id = r.id
                              INNER JOIN bookings b ON u.id = b.created_by
                              WHERE r.name = 'staff' AND u.status = 'active'
                              GROUP BY u.id, u.full_name
                              HAVING booking_count > 0
                              ORDER BY booking_count DESC
                              LIMIT 5";
            $stmt = $this->db->prepare($sql_top_staff);
            $stmt->execute();
            $top_staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // ====================================================================
            // ASSEMBLE STATS ARRAY
            // ====================================================================
            $stats = [
                'total_bookings' => $total_bookings,
                'approved_bookings' => $approved_bookings,
                'pending_bookings' => $pending_bookings,
                'total_revenue' => $total_revenue,
                'month_revenue' => $month_revenue,
                'active_tours' => $active_tours,
                'pending_tours' => $pending_tours_count,
                'total_customers' => $total_customers,
                'total_staff' => $total_staff,
                'total_guides' => $total_guides,
                'upcoming_schedules' => $upcoming_schedules,
                'active_schedules' => $active_schedules,
                'total_vehicles' => $total_vehicles,
                'total_drivers' => $total_drivers
            ];

        } catch (PDOException $e) {
            error_log("DashboardController::adminDashboard() Error: " . $e->getMessage());
            
            // Log chi tiết lỗi để debug
            if (defined('APP_ENV') && APP_ENV === 'development') {
                error_log("SQL Error Details: " . print_r($e->getErrorInfo(), true));
            }

            // Fallback to empty data on error
            $stats = [
                'total_bookings' => 0,
                'approved_bookings' => 0,
                'pending_bookings' => 0,
                'total_revenue' => 0,
                'month_revenue' => 0,
                'active_tours' => 0,
                'pending_tours' => 0,
                'total_customers' => 0,
                'total_staff' => 0,
                'total_guides' => 0,
                'upcoming_schedules' => 0,
                'active_schedules' => 0,
                'total_vehicles' => 0,
                'total_drivers' => 0
            ];
            $revenue_trend = [];
            $booking_trend = [];
            $booking_status = [];
            $top_tours = [];
            $recent_bookings = [];
            $pending_tours = [];
            $upcoming_schedules_list = [];
            $top_staff = [];
        } catch (Exception $e) {
            error_log("DashboardController::adminDashboard() General Error: " . $e->getMessage());
            
            // Fallback to empty data on error
            $stats = [
                'total_bookings' => 0,
                'approved_bookings' => 0,
                'pending_bookings' => 0,
                'total_revenue' => 0,
                'month_revenue' => 0,
                'active_tours' => 0,
                'pending_tours' => 0,
                'total_customers' => 0,
                'total_staff' => 0,
                'total_guides' => 0,
                'upcoming_schedules' => 0,
                'active_schedules' => 0,
                'total_vehicles' => 0,
                'total_drivers' => 0
            ];
            $revenue_trend = [];
            $booking_trend = [];
            $booking_status = [];
            $top_tours = [];
            $recent_bookings = [];
            $pending_tours = [];
            $upcoming_schedules_list = [];
            $top_staff = [];
        }

        $page_title = 'Dashboard Admin';
        $content_file = VIEWS_PATH . '/admin/dashboard.php';

        // Load layout
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Staff Dashboard
     * 
     * @return void
     */
    public function staffDashboard()
    {
        // Require staff permission
        require_staff();

        $user_id = get_user_id();

        // Load Models
        require_once MODELS_PATH . '/Tour.php';
        require_once MODELS_PATH . '/Booking.php';
        require_once MODELS_PATH . '/Customer.php';

        $tourModel = new Tour($this->db);
        $bookingModel = new Booking($this->db);
        $customerModel = new Customer($this->db);

        // Fetch Stats
        $my_tours_data = $tourModel->getAll(['created_by' => $user_id], 1, 5);
        $my_tours = $my_tours_data['data'];
        $my_tours_count = $my_tours_data['total'];

        $my_bookings_count = $bookingModel->count(['created_by' => $user_id]);
        $my_customers_count = $customerModel->count(['created_by' => $user_id]);

        $stats = [
            'my_tours' => $my_tours_count,
            'my_bookings' => $my_bookings_count,
            'my_customers' => $my_customers_count
        ];

        $page_title = 'Dashboard Staff';
        $content_file = VIEWS_PATH . '/staff/dashboard.php';

        // Load layout
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    /**
     * Guide Dashboard
     * 
     * @return void
     */
    public function guideDashboard()
    {
        // Require guide permission
        require_guide();

        // TODO: Load thống kê cho guide
        // - Tours được giao
        // - Tours đang thực hiện
        // - Tours đã hoàn thành

        // Placeholder data
        $stats = [
            'assigned_tours' => 0,
            'active_tours' => 0,
            'completed_tours' => 0
        ];
        $assigned_tours = [];
        $upcoming_tours = [];

        $page_title = 'Dashboard Guide';
        $content_file = VIEWS_PATH . '/guide/dashboard.php';

        // Load layout
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }
}

// ============================================================================
// END OF DASHBOARD CONTROLLER
// ============================================================================
