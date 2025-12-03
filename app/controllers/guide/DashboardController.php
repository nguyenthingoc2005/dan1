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

        require_once MODELS_PATH . '/TourSchedule.php';
        $scheduleModel = new \TourSchedule($this->db);

        // Stats
        $raw_count = $scheduleModel->count([
            'guide_id' => $user_id,
            'start_date' => date('Y-m-d')
        ]);
        $upcoming_tours_count = (int) $raw_count;

        // Get recent assigned tours
        $my_schedules = $scheduleModel->getAll([
            'guide_id' => $user_id,
            'start_date' => date('Y-m-d')
        ], 1, 5)['data'];

        $page_title = 'Dashboard Hướng Dẫn Viên';
        $content_file = VIEWS_PATH . '/guide/dashboard.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }
}
