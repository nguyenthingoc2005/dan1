<?php
/**
 * ==============================================================================
 * TOUR ASSIGNMENT CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý phân công hướng dẫn viên
 * - Kiểm tra lịch trùng HDV
 * - Tự động tính lương
 * - Validate role HDV
 * 
 * Routing: ?act=admin&module=assignments&action=index
 * 
 * @version 1.1
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
        require_once MODELS_PATH . '/TourAssignment.php';
        $this->assignmentModel = new TourAssignment($pdo);
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

        // Filter: upcoming tours (from today)
        $filters = [
            'start_date' => date('Y-m-d')
        ];

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = 20;

        $result = $scheduleModel->getAll($filters, $page, $limit);
        $schedules = $result['data'];
        $total_pages = $result['pages'];

        // Get all active guides for dropdown
        $sql = "SELECT u.id, u.full_name, u.phone, u.email
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE r.name = 'guide' AND u.status = 'active'
                ORDER BY u.full_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $guides = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $page_title = 'Phân công Hướng dẫn viên';
        $content_file = VIEWS_PATH . '/admin/assignments/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * AJAX: Lấy danh sách HDV có sẵn cho schedule
     */
    public function getAvailableGuides()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            $scheduleId = $_GET['schedule_id'] ?? 0;
            
            if (!$scheduleId) {
                throw new Exception("Thiếu schedule_id");
            }
            
            // Get schedule dates
            require_once MODELS_PATH . '/TourSchedule.php';
            $scheduleModel = new TourSchedule($this->db);
            $schedule = $scheduleModel->getById($scheduleId);
            
            if (!$schedule) {
                throw new Exception("Lịch khởi hành không tồn tại");
            }
            
            // Get available guides
            $availableGuides = $this->assignmentModel->getAvailableGuides(
                $schedule['start_date'], 
                $schedule['end_date']
            );
            
            // Calculate suggested salary
            $durationDays = (int) $schedule['duration_days'] ?? 1;
            $suggestedSalary = $this->assignmentModel->calculateSalary($durationDays);
            
            echo json_encode([
                'success' => true,
                'guides' => $availableGuides,
                'schedule' => [
                    'start_date' => $schedule['start_date'],
                    'end_date' => $schedule['end_date'],
                    'duration_days' => $durationDays
                ],
                'suggested_salary' => $suggestedSalary
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Xử lý phân công (với validation đầy đủ)
     */
    public function assign()
    {
        require_admin();

        try {
            if (empty($_POST['schedule_id']) || empty($_POST['guide_id'])) {
                throw new Exception("Vui lòng chọn lịch tour và hướng dẫn viên.");
            }

            $scheduleId = (int) $_POST['schedule_id'];
            $guideId = (int) $_POST['guide_id'];
            $notes = $_POST['notes'] ?? null;
            $salaryAmount = !empty($_POST['salary_amount']) ? (float) $_POST['salary_amount'] : null;

            // Use the new assignToSchedule method with full validation
            $result = $this->assignmentModel->assignToSchedule($scheduleId, $guideId, [
                'salary_amount' => $salaryAmount,
                'notes' => $notes,
                'created_by' => $_SESSION['user_id'] ?? 1
            ]);

            if ($result['success']) {
                set_success($result['message']);
            } else {
                throw new Exception($result['message']);
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

        try {
            $scheduleId = $_GET['schedule_id'] ?? $_POST['schedule_id'] ?? 0;
            
            if (!$scheduleId) {
                throw new Exception("Thiếu schedule_id");
            }
            
            if ($this->assignmentModel->removeAssignment($scheduleId)) {
                set_success("Đã hủy phân công HDV thành công!");
            } else {
                throw new Exception("Không thể hủy phân công");
            }
            
        } catch (Exception $e) {
            set_error($e->getMessage());
        }
        
        redirect('?act=admin&module=assignments');
    }

    /**
     * AJAX: Check guide availability
     */
    public function checkAvailability()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            $guideId = $_GET['guide_id'] ?? 0;
            $startDate = $_GET['start_date'] ?? '';
            $endDate = $_GET['end_date'] ?? '';
            
            if (!$guideId || !$startDate || !$endDate) {
                throw new Exception("Thiếu thông tin");
            }
            
            $result = $this->assignmentModel->checkGuideAvailability($guideId, $startDate, $endDate);
            
            echo json_encode([
                'success' => true,
                'available' => $result['available'],
                'conflict' => $result['conflict']
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
}
