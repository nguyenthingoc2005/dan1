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

            // Check if guide is available (optional logic)
            // ...

            // Assign guide (Update tour_schedules table or create new assignment record)
            // Assuming we update tour_schedules table with guide_id column
            // Need to check if guide_id column exists in tour_schedules

            // For now, let's assume we have a separate table `tour_assignments` or column `guide_id` in `tour_schedules`.
            // Let's check schema.
            // Schema doesn't have guide_id in tour_schedules.
            // We should create a new table `tour_assignments` (schedule_id, guide_id, status).

            if (!isset($this->assignmentModel)) {
                // Create model if not exists
                require_once MODELS_PATH . '/TourAssignment.php';
                $this->assignmentModel = new TourAssignment($this->db);
            }

            if ($this->assignmentModel->assign($schedule_id, $guide_id)) {
                set_success("Phân công thành công!");
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
