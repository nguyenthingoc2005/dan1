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

        // TODO: Load thống kê cho admin
        // - Tổng số tours
        // - Tổng số bookings
        // - Tổng doanh thu
        // - Tours/Bookings chờ duyệt

        // Placeholder data to prevent warnings
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
