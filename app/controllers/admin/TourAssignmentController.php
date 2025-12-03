<?php
/**
 * ==============================================================================
 * TOUR ASSIGNMENT CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý phân công hướng dẫn viên
 * Routing: ?act=admin&module=assignments&action=index
 * 
 * @version 1.0
 * @date 2024-12-03
 * ==============================================================================
 */

class TourAssignmentController
{
    private $db;
    private $assignmentModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        // Check if TourAssignment model exists, if not create it
        if (file_exists(MODELS_PATH . '/TourAssignment.php')) {
            require_once MODELS_PATH . '/TourAssignment.php';
            $this->assignmentModel = new TourAssignment($pdo);
        }
    }

    /**
     * Danh sách phân công (Lịch tour sắp tới)
     */
    public function index()
    {
        require_admin();

        // Get upcoming schedules
        require_once MODELS_PATH . '/TourSchedule.php';
        $scheduleModel = new TourSchedule($this->db);

        // Filter: upcoming tours
        $filters = [
            'start_date' => date('Y-m-d'),
            'status' => 'open'
        ];

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = 20;

        $result = $scheduleModel->getAll($filters, $page, $limit);
        $schedules = $result['data'];

        // Get guides list for assignment modal
        require_once MODELS_PATH . '/User.php';
        $userModel = new User($this->db);
        $guides = $userModel->getAll(['role' => 'guide', 'status' => 'active']);

        $page_title = 'Phân công Hướng dẫn viên';
        $content_file = VIEWS_PATH . '/admin/assignments/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý phân công
     */
    public function assign()
    {
        require_admin();

        try {
            if (empty($_POST['schedule_id']) || empty($_POST['guide_id'])) {
                throw new Exception("Vui lòng chọn lịch tour và hướng dẫn viên.");
            }

            $schedule_id = (int) $_POST['schedule_id'];
            $guide_id = (int) $_POST['guide_id'];
            $notes = $_POST['notes'] ?? null;

            // Get schedule info
            require_once MODELS_PATH . '/TourSchedule.php';
            $scheduleModel = new TourSchedule($this->db);
            $schedule = $scheduleModel->getById($schedule_id);

            if (!$schedule) {
                throw new Exception("Không tìm thấy lịch khởi hành.");
            }

            // Assign guide directly to schedule
            if ($scheduleModel->assignGuide($schedule_id, $guide_id, $notes)) {
                set_success("Phân công hướng dẫn viên thành công!");
            } else {
                throw new Exception("Không thể phân công.");
            }

            redirect('?act=admin&module=assignments');

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=assignments');
        }
    }

    /**
     * Hủy phân công
     */
    public function remove()
    {
        require_admin();
        // Logic remove assignment
        redirect('?act=admin&module=assignments');
    }
}
