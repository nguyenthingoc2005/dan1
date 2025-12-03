<?php
/**
 * ==============================================================================
 * REPORT CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý báo cáo thống kê
 * Routing: ?act=admin&module=reports&action=index
 * 
 * @version 1.0
 * @date 2024-12-03
 * ==============================================================================
 */

class ReportController
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Dashboard báo cáo chung
     */
    public function index()
    {
        require_admin();

        // Redirect to revenue report by default
        redirect('?act=admin&module=reports&action=revenue');
    }

    /**
     * Báo cáo doanh thu
     */
    public function revenue()
    {
        require_admin();

        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-t');

        // Logic get revenue stats
        // Total revenue
        // Revenue by tour
        // Revenue by month (for chart)

        // Mock data for now
        $total_revenue = 0;
        $revenue_by_tour = [];

        // SQL to get revenue
        $sql = "SELECT SUM(amount) as total FROM payments WHERE status = 'completed' AND payment_date BETWEEN :start AND :end";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['start' => $start_date, 'end' => $end_date]);
        $total_revenue = $stmt->fetchColumn() ?: 0;

        $page_title = 'Báo cáo Doanh thu';
        $content_file = VIEWS_PATH . '/admin/reports/revenue.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Báo cáo booking
     */
    public function bookings()
    {
        require_admin();

        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-t');

        // Logic get booking stats
        // Total bookings
        // Bookings by status

        $page_title = 'Báo cáo Đặt tour';
        $content_file = VIEWS_PATH . '/admin/reports/bookings.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }
}
