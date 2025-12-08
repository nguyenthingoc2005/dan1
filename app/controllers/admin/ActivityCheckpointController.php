<?php
namespace Admin;

/**
 * ==============================================================================
 * ACTIVITY CHECKPOINT CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý checkpoints - CHỈ XEM
 * Admin chỉ có quyền xem, không được tạo/sửa/xóa
 * 
 * Routing: ?act=admin-checkpoints&action=index
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class ActivityCheckpointController
{
    private $db;
    private $scheduleModel;
    private $checkpointModel;
    private $checkinModel;
    private $summaryModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/TourSchedule.php';
        require_once MODELS_PATH . '/ActivityCheckpoint.php';
        require_once MODELS_PATH . '/ActivityCheckin.php';
        require_once MODELS_PATH . '/ActivityCheckinSummary.php';
        $this->scheduleModel = new \TourSchedule($pdo);
        $this->checkpointModel = new \ActivityCheckpoint($pdo);
        $this->checkinModel = new \ActivityCheckin($pdo);
        $this->summaryModel = new \ActivityCheckinSummary($pdo);
    }

    /**
     * Danh sách tất cả checkpoints
     */
    public function index()
    {
        require_admin();

        $filters = [];
        $schedule_id = $_GET['schedule_id'] ?? null;
        $tour_id = $_GET['tour_id'] ?? null;

        if ($schedule_id) {
            $filters['schedule_id'] = $schedule_id;
        }

        if ($tour_id) {
            $filters['tour_id'] = $tour_id;
        }

        // Get schedules
        $schedules = [];
        if ($schedule_id) {
            $schedule = $this->scheduleModel->getById($schedule_id);
            if ($schedule) {
                $schedules = [$schedule];
            }
        } else {
            $result = $this->scheduleModel->getAll($filters, 1, 100);
            $schedules = $result['data'];
        }

        // Get checkpoints for each schedule
        $allCheckpoints = [];
        foreach ($schedules as $schedule) {
            $checkpoints = $this->checkpointModel->getBySchedule($schedule['id']);
            foreach ($checkpoints as $cp) {
                $cp['schedule'] = $schedule;
                $cp['stats'] = $this->checkinModel->getStatsByCheckpoint($cp['id']);
                $allCheckpoints[] = $cp;
            }
        }

        $page_title = 'Quản lý Checkpoints';
        $content_file = VIEWS_PATH . '/admin/checkpoints/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xem chi tiết checkpoint
     */
    public function show()
    {
        require_admin();
        $checkpoint_id = $_GET['id'] ?? null;

        if (!$checkpoint_id) {
            set_error("Checkpoint ID không hợp lệ.");
            redirect('?act=admin-checkpoints');
            return;
        }

        $checkpoint = $this->checkpointModel->getById($checkpoint_id);

        if (!$checkpoint) {
            set_error("Không tìm thấy checkpoint.");
            redirect('?act=admin-checkpoints');
            return;
        }

        // Get Tour Details
        require_once MODELS_PATH . '/Tour.php';
        $tourModel = new \Tour($this->db);
        $tour = $tourModel->findById($checkpoint['tour_id']);

        $schedule = $this->scheduleModel->getById($checkpoint['tour_schedule_id']);

        // Get summary
        $summary = $this->summaryModel->getByCheckpoint($checkpoint_id, $checkpoint['scheduled_date']);
        
        // Get all check-ins
        $checkins = $this->checkinModel->getByCheckpoint($checkpoint_id);
        $stats = $this->checkinModel->getStatsByCheckpoint($checkpoint_id);

        $page_title = 'Chi tiết Checkpoint: ' . htmlspecialchars($checkpoint['checkpoint_name']);
        $content_file = VIEWS_PATH . '/admin/checkpoints/show.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xem checkpoints theo schedule
     */
    public function bySchedule()
    {
        require_admin();
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$schedule_id) {
            set_error("Schedule ID không hợp lệ.");
            redirect('?act=admin-checkpoints');
            return;
        }

        $schedule = $this->scheduleModel->getById($schedule_id);

        if (!$schedule) {
            set_error("Không tìm thấy lịch tour.");
            redirect('?act=admin-checkpoints');
            return;
        }

        // Get Tour Details
        require_once MODELS_PATH . '/Tour.php';
        $tourModel = new \Tour($this->db);
        $tour = $tourModel->findById($schedule['tour_id']);

        // Get checkpoints
        $checkpoints = $this->checkpointModel->getBySchedule($schedule_id);

        // Get summary for each checkpoint
        foreach ($checkpoints as &$cp) {
            $summary = $this->summaryModel->getByCheckpoint($cp['id'], $cp['scheduled_date']);
            $cp['summary'] = $summary;
            $cp['stats'] = $this->checkinModel->getStatsByCheckpoint($cp['id']);
        }

        $page_title = 'Checkpoints: ' . htmlspecialchars($tour['tour_code']);
        $content_file = VIEWS_PATH . '/admin/checkpoints/by-schedule.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xem summary check-in của schedule
     */
    public function summary()
    {
        require_admin();
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$schedule_id) {
            set_error("Schedule ID không hợp lệ.");
            redirect('?act=admin-checkpoints');
            return;
        }

        $schedule = $this->scheduleModel->getById($schedule_id);

        if (!$schedule) {
            set_error("Không tìm thấy lịch tour.");
            redirect('?act=admin-checkpoints');
            return;
        }

        // Get Tour Details
        require_once MODELS_PATH . '/Tour.php';
        $tourModel = new \Tour($this->db);
        $tour = $tourModel->findById($schedule['tour_id']);

        // Get all summaries
        $summaries = $this->summaryModel->getBySchedule($schedule_id);

        // Get checkpoints
        $checkpoints = $this->checkpointModel->getBySchedule($schedule_id);

        $page_title = 'Summary Check-in: ' . htmlspecialchars($tour['tour_code']);
        $content_file = VIEWS_PATH . '/admin/checkpoints/summary.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }
}

