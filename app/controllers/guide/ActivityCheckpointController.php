<?php
namespace Guide;

/**
 * ==============================================================================
 * ACTIVITY CHECKPOINT CONTROLLER (GUIDE)
 * ==============================================================================
 * 
 * Quản lý checkpoints cho tour schedule
 * HDV tự tạo/sửa/xóa checkpoints của tour schedule được phân công
 * 
 * Routing: ?act=guide-checkpoints&action=index
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

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/TourSchedule.php';
        require_once MODELS_PATH . '/ActivityCheckpoint.php';
        $this->scheduleModel = new \TourSchedule($pdo);
        $this->checkpointModel = new \ActivityCheckpoint($pdo);
    }

    /**
     * Danh sách checkpoints của schedule
     */
    public function index()
    {
        require_guide();
        $user_id = get_user_id();
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$schedule_id) {
            set_error("Schedule ID không hợp lệ.");
            redirect('?act=guide-tours');
            return;
        }

        $schedule = $this->scheduleModel->getById($schedule_id);

        if (!$schedule) {
            set_error("Không tìm thấy lịch tour.");
            redirect('?act=guide-tours');
            return;
        }

        // Verify ownership
        if ($schedule['guide_id'] != $user_id) {
            set_error("Bạn không được phân công tour này.");
            redirect('?act=guide-tours');
            return;
        }

        // Get Tour Details
        require_once MODELS_PATH . '/Tour.php';
        $tourModel = new \Tour($this->db);
        $tour = $tourModel->findById($schedule['tour_id']);

        // Get checkpoints
        $checkpoints = $this->checkpointModel->getBySchedule($schedule_id);

        // Group by date
        $checkpointsByDate = [];
        foreach ($checkpoints as $cp) {
            $date = $cp['scheduled_date'];
            if (!isset($checkpointsByDate[$date])) {
                $checkpointsByDate[$date] = [];
            }
            $checkpointsByDate[$date][] = $cp;
        }

        $page_title = 'Quản lý Checkpoints: ' . htmlspecialchars($tour['tour_code']);
        $content_file = VIEWS_PATH . '/guide/checkpoints/index.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }

    /**
     * Form tạo checkpoint
     */
    public function create()
    {
        require_guide();
        $user_id = get_user_id();
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$schedule_id) {
            set_error("Schedule ID không hợp lệ.");
            redirect('?act=guide-tours');
            return;
        }

        $schedule = $this->scheduleModel->getById($schedule_id);

        if (!$schedule) {
            set_error("Không tìm thấy lịch tour.");
            redirect('?act=guide-tours');
            return;
        }

        // Verify ownership
        if ($schedule['guide_id'] != $user_id) {
            set_error("Bạn không được phân công tour này.");
            redirect('?act=guide-tours');
            return;
        }

        // Get Tour Details
        require_once MODELS_PATH . '/Tour.php';
        $tourModel = new \Tour($this->db);
        $tour = $tourModel->findById($schedule['tour_id']);

        $page_title = 'Tạo Checkpoint: ' . htmlspecialchars($tour['tour_code']);
        $content_file = VIEWS_PATH . '/guide/checkpoints/create.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }

    /**
     * Lưu checkpoint mới
     */
    public function store()
    {
        require_guide();
        require_csrf_token();
        $user_id = get_user_id();

        try {
            if (empty($_POST['schedule_id'])) {
                throw new \Exception("Schedule ID không hợp lệ.");
            }

            $schedule_id = (int) $_POST['schedule_id'];
            $schedule = $this->scheduleModel->getById($schedule_id);

            if (!$schedule || $schedule['guide_id'] != $user_id) {
                throw new \Exception("Bạn không được phân công tour này.");
            }

            // Validate required fields
            if (empty($_POST['checkpoint_name']) || empty($_POST['checkpoint_type']) || empty($_POST['scheduled_date']) || empty($_POST['scheduled_time'])) {
                throw new \Exception("Vui lòng điền đầy đủ thông tin bắt buộc.");
            }

            $data = [
                'tour_schedule_id' => $schedule_id,
                'checkpoint_code' => sanitize($_POST['checkpoint_code'] ?? ''),
                'checkpoint_name' => sanitize($_POST['checkpoint_name']),
                'checkpoint_type' => sanitize($_POST['checkpoint_type']),
                'scheduled_date' => sanitize($_POST['scheduled_date']),
                'scheduled_time' => sanitize($_POST['scheduled_time']),
                'is_required' => isset($_POST['is_required']) ? 1 : 0,
                'display_order' => !empty($_POST['display_order']) ? (int)$_POST['display_order'] : 0,
                'status' => sanitize($_POST['status'] ?? 'active')
            ];

            // Validate date is within schedule range
            if ($data['scheduled_date'] < $schedule['start_date'] || $data['scheduled_date'] > $schedule['end_date']) {
                throw new \Exception("Ngày checkpoint phải nằm trong khoảng thời gian tour (" . date('d/m/Y', strtotime($schedule['start_date'])) . " - " . date('d/m/Y', strtotime($schedule['end_date'])) . ").");
            }

            $checkpoint_id = $this->checkpointModel->create($data, $user_id);

            if ($checkpoint_id) {
                set_success("Đã tạo checkpoint thành công!");
                redirect('?act=guide-checkpoints&action=index&schedule_id=' . $schedule_id);
            } else {
                throw new \Exception("Không thể tạo checkpoint.");
            }

        } catch (\Exception $e) {
            set_error($e->getMessage());
            redirect('?act=guide-checkpoints&action=create&schedule_id=' . ($_POST['schedule_id'] ?? ''));
        }
    }

    /**
     * Form sửa checkpoint
     */
    public function edit()
    {
        require_guide();
        $user_id = get_user_id();
        $checkpoint_id = $_GET['id'] ?? null;

        if (!$checkpoint_id) {
            set_error("Checkpoint ID không hợp lệ.");
            redirect('?act=guide-tours');
            return;
        }

        $checkpoint = $this->checkpointModel->getById($checkpoint_id);

        if (!$checkpoint) {
            set_error("Không tìm thấy checkpoint.");
            redirect('?act=guide-tours');
            return;
        }

        // Verify ownership
        if (!$this->checkpointModel->verifyOwnership($checkpoint_id, $user_id)) {
            set_error("Bạn không có quyền sửa checkpoint này.");
            redirect('?act=guide-checkpoints&action=index&schedule_id=' . $checkpoint['tour_schedule_id']);
            return;
        }

        // Get Tour Details
        require_once MODELS_PATH . '/Tour.php';
        $tourModel = new \Tour($this->db);
        $tour = $tourModel->findById($checkpoint['tour_id']);

        $schedule = $this->scheduleModel->getById($checkpoint['tour_schedule_id']);

        $page_title = 'Sửa Checkpoint: ' . htmlspecialchars($tour['tour_code']);
        $content_file = VIEWS_PATH . '/guide/checkpoints/edit.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }

    /**
     * Cập nhật checkpoint
     */
    public function update()
    {
        require_guide();
        require_csrf_token();
        $user_id = get_user_id();

        try {
            if (empty($_POST['id'])) {
                throw new \Exception("Checkpoint ID không hợp lệ.");
            }

            $checkpoint_id = (int) $_POST['id'];
            $checkpoint = $this->checkpointModel->getById($checkpoint_id);

            if (!$checkpoint) {
                throw new \Exception("Không tìm thấy checkpoint.");
            }

            // Verify ownership
            if (!$this->checkpointModel->verifyOwnership($checkpoint_id, $user_id)) {
                throw new \Exception("Bạn không có quyền sửa checkpoint này.");
            }

            // Validate required fields
            if (empty($_POST['checkpoint_name']) || empty($_POST['checkpoint_type']) || empty($_POST['scheduled_date']) || empty($_POST['scheduled_time'])) {
                throw new \Exception("Vui lòng điền đầy đủ thông tin bắt buộc.");
            }

            $schedule = $this->scheduleModel->getById($checkpoint['tour_schedule_id']);

            $data = [
                'checkpoint_code' => sanitize($_POST['checkpoint_code'] ?? ''),
                'checkpoint_name' => sanitize($_POST['checkpoint_name']),
                'checkpoint_type' => sanitize($_POST['checkpoint_type']),
                'scheduled_date' => sanitize($_POST['scheduled_date']),
                'scheduled_time' => sanitize($_POST['scheduled_time']),
                'is_required' => isset($_POST['is_required']) ? 1 : 0,
                'display_order' => !empty($_POST['display_order']) ? (int)$_POST['display_order'] : 0,
                'status' => sanitize($_POST['status'] ?? 'active')
            ];

            // Validate date is within schedule range
            if ($data['scheduled_date'] < $schedule['start_date'] || $data['scheduled_date'] > $schedule['end_date']) {
                throw new \Exception("Ngày checkpoint phải nằm trong khoảng thời gian tour (" . date('d/m/Y', strtotime($schedule['start_date'])) . " - " . date('d/m/Y', strtotime($schedule['end_date'])) . ").");
            }

            $result = $this->checkpointModel->update($checkpoint_id, $user_id, $data);

            if ($result) {
                set_success("Đã cập nhật checkpoint thành công!");
                redirect('?act=guide-checkpoints&action=index&schedule_id=' . $checkpoint['tour_schedule_id']);
            } else {
                throw new \Exception("Không thể cập nhật checkpoint.");
            }

        } catch (\Exception $e) {
            set_error($e->getMessage());
            redirect('?act=guide-checkpoints&action=edit&id=' . ($_POST['id'] ?? ''));
        }
    }

    /**
     * Xóa checkpoint
     */
    public function delete()
    {
        require_guide();
        require_csrf_token();
        $user_id = get_user_id();

        try {
            if (empty($_POST['id'])) {
                throw new \Exception("Checkpoint ID không hợp lệ.");
            }

            $checkpoint_id = (int) $_POST['id'];
            $checkpoint = $this->checkpointModel->getById($checkpoint_id);

            if (!$checkpoint) {
                throw new \Exception("Không tìm thấy checkpoint.");
            }

            // Verify ownership
            if (!$this->checkpointModel->verifyOwnership($checkpoint_id, $user_id)) {
                throw new \Exception("Bạn không có quyền xóa checkpoint này.");
            }

            $result = $this->checkpointModel->delete($checkpoint_id, $user_id);

            if ($result) {
                set_success("Đã xóa checkpoint thành công!");
            } else {
                throw new \Exception("Không thể xóa checkpoint.");
            }

        } catch (\Exception $e) {
            set_error($e->getMessage());
        }

        redirect('?act=guide-checkpoints&action=index&schedule_id=' . ($checkpoint['tour_schedule_id'] ?? ''));
    }
}

