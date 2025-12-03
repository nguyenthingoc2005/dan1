<?php
/**
 * ==============================================================================
 * SETTING CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý cài đặt hệ thống
 * Routing: ?act=admin&module=settings&action=general
 * 
 * @version 1.0
 * @date 2024-12-03
 * ==============================================================================
 */

class SettingController
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Cài đặt chung
     */
    public function general()
    {
        require_admin();

        // Get settings from DB (Mock for now)
        $settings = [
            'site_name' => 'Tour Management System',
            'site_email' => 'admin@example.com',
            'site_phone' => '1900 1234',
            'currency' => 'VND',
            'timezone' => 'Asia/Ho_Chi_Minh'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Save settings logic
            set_success("Đã lưu cài đặt chung!");
            redirect('?act=admin&module=settings&action=general');
        }

        $page_title = 'Cài đặt chung';
        $content_file = VIEWS_PATH . '/admin/settings/general.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Cài đặt Email
     */
    public function email()
    {
        require_admin();

        // Get email settings
        $settings = [
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => '587',
            'smtp_user' => 'user@gmail.com',
            'smtp_pass' => '',
            'smtp_secure' => 'tls'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Save email settings logic
            set_success("Đã lưu cấu hình Email!");
            redirect('?act=admin&module=settings&action=email');
        }

        $page_title = 'Cấu hình Email';
        $content_file = VIEWS_PATH . '/admin/settings/email.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }
}
