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
            // STATISTICS QUERIES
            // ====================================================================

            // 1. Tổng số bookings
            $sql_total_bookings = "SELECT COUNT(*) as total FROM bookings";
            $stmt = $this->db->prepare($sql_total_bookings);
            $stmt->execute();
            $total_bookings = (int) $stmt->fetchColumn();

            // 2. Số bookings đã duyệt
            $sql_approved_bookings = "SELECT COUNT(*) as total FROM bookings WHERE approval_status = 'approved'";
            $stmt = $this->db->prepare($sql_approved_bookings);
            $stmt->execute();
            $approved_bookings = (int) $stmt->fetchColumn();

            // 3. Số bookings chờ duyệt
            $sql_pending_bookings = "SELECT COUNT(*) as total FROM bookings WHERE approval_status = 'pending'";
            $stmt = $this->db->prepare($sql_pending_bookings);
            $stmt->execute();
            $pending_bookings = (int) $stmt->fetchColumn();

            // 4. Tổng doanh thu (từ payments completed, không tính refund)
            $sql_total_revenue = "SELECT COALESCE(SUM(amount), 0) as total 
                                 FROM payments 
                                 WHERE status = 'completed' 
                                 AND payment_type != 'refund'";
            $stmt = $this->db->prepare($sql_total_revenue);
            $stmt->execute();
            $total_revenue = (float) $stmt->fetchColumn();

            // 5. Số tours hoạt động (active và approved)
            $sql_active_tours = "SELECT COUNT(*) as total 
                                FROM tours 
                                WHERE status = 'active' 
                                AND approval_status = 'approved'";
            $stmt = $this->db->prepare($sql_active_tours);
            $stmt->execute();
            $active_tours = (int) $stmt->fetchColumn();

            // 6. Số tours chờ duyệt
            $sql_pending_tours = "SELECT COUNT(*) as total 
                                 FROM tours 
                                 WHERE approval_status = 'pending'";
            $stmt = $this->db->prepare($sql_pending_tours);
            $stmt->execute();
            $pending_tours_count = (int) $stmt->fetchColumn();

            // ====================================================================
            // RECENT BOOKINGS (10 mới nhất)
            // ====================================================================
            $sql_recent_bookings = "SELECT 
                                        b.id,
                                        b.booking_code,
                                        b.start_date,
                                        b.final_amount,
                                        b.approval_status,
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
            // ASSEMBLE STATS ARRAY
            // ====================================================================
            $stats = [
                'total_bookings' => $total_bookings,
                'approved_bookings' => $approved_bookings,
                'pending_bookings' => $pending_bookings,
                'total_revenue' => $total_revenue,
                'active_tours' => $active_tours,
                'pending_tours' => $pending_tours_count
            ];

        } catch (PDOException $e) {
            error_log("DashboardController::adminDashboard() Error: " . $e->getMessage());

            // Fallback to empty data on error
            $stats = [
                'total_bookings' => 0,
                'approved_bookings' => 0,
                'pending_bookings' => 0,
                'total_revenue' => 0,
                'active_tours' => 0,
                'pending_tours' => 0
            ];
            $recent_bookings = [];
            $pending_tours = [];
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
