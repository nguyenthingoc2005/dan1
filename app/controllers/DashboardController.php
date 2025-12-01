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

        // TODO: Load thống kê cho staff
        // - Tours của tôi
        // - Bookings của tôi
        // - Khách hàng của tôi

        // Placeholder data
        $stats = [
            'my_tours' => 0,
            'my_bookings' => 0,
            'my_customers' => 0
        ];
        $my_tours = [];
        $recent_bookings = [];

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
