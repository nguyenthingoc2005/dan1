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

            // Get schedule info
            require_once MODELS_PATH . '/TourSchedule.php';
            $scheduleModel = new TourSchedule($this->db);
            $schedule = $scheduleModel->getById($schedule_id);

            if (!$schedule) {
                throw new Exception("Không tìm thấy lịch khởi hành.");
            }

            // Find booking for this schedule (same tour + start_date)
            // If no booking exists yet, we need to decide: create one or throw error?
            // For now, let's find existing bookings
            require_once MODELS_PATH . '/Booking.php';
            $bookingModel = new Booking($this->db);

            $sql = "SELECT id FROM bookings 
                    WHERE tour_id = :tour_id 
                      AND start_date = :start_date 
                      AND approval_status IN ('pending', 'approved')
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'tour_id' => $schedule['tour_id'],
                'start_date' => $schedule['start_date']
            ]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$booking) {
                throw new Exception("Chưa có booking nào cho lịch này. Vui lòng tạo booking trước khi phân công HDV.");
            }

            // Assign guide to booking
            if (!isset($this->assignmentModel)) {
                require_once MODELS_PATH . '/TourAssignment.php';
                $this->assignmentModel = new TourAssignment($this->db);
            }

            // Pass booking_id, guide_id, and assignment_date
            if (
                $this->assignmentModel->assign(
                    $booking['id'],
                    $guide_id,
                    $schedule['start_date'], // assignment_date from schedule
                    ['created_by' => $_SESSION['user_id'] ?? null]
                )
            ) {
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
