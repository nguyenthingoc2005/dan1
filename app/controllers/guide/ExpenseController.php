<?php
namespace Guide;

/**
 * ==============================================================================
 * EXPENSE CONTROLLER (GUIDE)
 * ==============================================================================
 * 
 * Quản lý chi phí phát sinh trong tour
 * Routing: ?act=guide-expenses&action=index
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class ExpenseController
{
    private $db;
    private $scheduleModel;
    private $bookingModel;
    private $expenseModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/TourSchedule.php';
        require_once MODELS_PATH . '/Booking.php';
        require_once MODELS_PATH . '/IncurredExpense.php';
        $this->scheduleModel = new \TourSchedule($pdo);
        $this->bookingModel = new \Booking($pdo);
        $this->expenseModel = new \IncurredExpense($pdo);
    }

    /**
     * Danh sách tour có thể ghi chi phí
     */
    public function index()
    {
        require_guide();
        $user_id = get_user_id();

        // Filter: Only my assigned tours
        $filters = [
            'guide_id' => $user_id
        ];

        // Allow filtering by date range
        $filter_type = $_GET['filter'] ?? 'upcoming';
        $today = date('Y-m-d');
        
        if ($filter_type === 'upcoming') {
            $filters['start_date'] = $today;
        } elseif ($filter_type === 'history') {
            $filters['end_date'] = date('Y-m-d', strtotime('-1 day'));
        }

        $page = $_GET['page'] ?? 1;
        $result = $this->scheduleModel->getAll($filters, $page, 10);
        $schedules = $result['data'];
        $total_pages = $result['pages'];
        $current_page = $result['current_page'];

        // Get expense totals for each schedule
        foreach ($schedules as &$schedule) {
            $schedule['expense_total'] = $this->expenseModel->getTotalByScheduleId($schedule['id']);
        }

        $page_title = 'Chi phí phát sinh';
        $content_file = VIEWS_PATH . '/guide/expenses/index.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }

    /**
     * Trang ghi chi phí cho một tour schedule
     */
    public function show()
    {
        require_guide();
        $user_id = get_user_id();
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$schedule_id) {
            redirect('?act=guide-expenses');
            return;
        }

        $schedule = $this->scheduleModel->getById($schedule_id);

        if (!$schedule) {
            set_error("Không tìm thấy lịch tour.");
            redirect('?act=guide-expenses');
            return;
        }

        // Verify ownership
        if ($schedule['guide_id'] != $user_id) {
            set_error("Bạn không được phân công tour này.");
            redirect('?act=guide-expenses');
            return;
        }

        // Get Tour Details
        require_once MODELS_PATH . '/Tour.php';
        $tourModel = new \Tour($this->db);
        $tour = $tourModel->findById($schedule['tour_id']);

        // Get bookings for this schedule
        $bookings = $this->bookingModel->getAll([
            'tour_schedule_id' => $schedule_id,
            'status' => 'paid'
        ], 1, 1000);
        
        if (empty($bookings)) {
            $bookings = $this->bookingModel->getAll([
                'tour_id' => $schedule['tour_id'],
                'start_date' => $schedule['start_date'],
                'exact_date' => true,
                'status' => 'paid'
            ], 1, 1000);
        }

        // Get expenses for this schedule
        $expenses = $this->expenseModel->getByScheduleId($schedule_id);
        $expense_total = $this->expenseModel->getTotalByScheduleId($schedule_id);

        $page_title = 'Chi phí phát sinh: ' . htmlspecialchars($tour['tour_code']);
        $content_file = VIEWS_PATH . '/guide/expenses/show.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }

    /**
     * Trang tạo chi phí mới
     */
    public function create()
    {
        require_guide();
        $user_id = get_user_id();
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$schedule_id) {
            redirect('?act=guide-expenses');
            return;
        }

        $schedule = $this->scheduleModel->getById($schedule_id);

        if (!$schedule) {
            set_error("Không tìm thấy lịch tour.");
            redirect('?act=guide-expenses');
            return;
        }

        // Verify ownership
        if ($schedule['guide_id'] != $user_id) {
            set_error("Bạn không được phân công tour này.");
            redirect('?act=guide-expenses');
            return;
        }

        // Kiểm tra tour đã bắt đầu chưa
        $today = date('Y-m-d');
        if ($schedule['start_date'] > $today) {
            set_error("Tour chưa bắt đầu. Chỉ có thể ghi chi phí phát sinh từ ngày " . date('d/m/Y', strtotime($schedule['start_date'])) . " trở đi.");
            redirect('?act=guide-expenses&action=show&schedule_id=' . $schedule_id);
            return;
        }

        // Get Tour Details
        require_once MODELS_PATH . '/Tour.php';
        $tourModel = new \Tour($this->db);
        $tour = $tourModel->findById($schedule['tour_id']);

        // Get bookings for this schedule
        $bookings = $this->bookingModel->getAll([
            'tour_schedule_id' => $schedule_id,
            'status' => 'paid'
        ], 1, 1000);
        
        if (empty($bookings)) {
            $bookings = $this->bookingModel->getAll([
                'tour_id' => $schedule['tour_id'],
                'start_date' => $schedule['start_date'],
                'exact_date' => true,
                'status' => 'paid'
            ], 1, 1000);
        }

        $page_title = 'Ghi chi phí phát sinh';
        $content_file = VIEWS_PATH . '/guide/expenses/create.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }

    /**
     * Xử lý lưu chi phí mới
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

            // Kiểm tra tour đã bắt đầu chưa
            $today = date('Y-m-d');
            if ($schedule['start_date'] > $today) {
                throw new \Exception("Tour chưa bắt đầu. Chỉ có thể ghi chi phí phát sinh từ ngày " . date('d/m/Y', strtotime($schedule['start_date'])) . " trở đi.");
            }

            // booking_id là optional - có thể NULL nếu là chi phí chung của tour
            $booking_id = !empty($_POST['booking_id']) ? (int) $_POST['booking_id'] : null;

            // Nếu có booking_id, validate booking thuộc schedule (optional)
            if ($booking_id) {
                $booking = $this->bookingModel->getById($booking_id);
                if (!$booking) {
                    throw new \Exception("Booking không tồn tại.");
                }
                // Note: Không bắt buộc booking phải thuộc schedule, vì có thể là chi phí chung
            }

            // Validate required fields
            if (empty($_POST['expense_date'])) {
                throw new \Exception("Vui lòng nhập ngày phát sinh.");
            }

            if (empty($_POST['description'])) {
                throw new \Exception("Vui lòng nhập mô tả chi phí.");
            }

            if (empty($_POST['amount']) || (float)$_POST['amount'] <= 0) {
                throw new \Exception("Vui lòng nhập số tiền hợp lệ.");
            }

            // Handle file upload (receipt)
            $receipt_file = null;
            if (!empty($_FILES['receipt_file']['name'])) {
                $upload_dir = PUBLIC_PATH . '/uploads/expenses/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $file_ext = pathinfo($_FILES['receipt_file']['name'], PATHINFO_EXTENSION);
                $file_name = 'expense_' . time() . '_' . uniqid() . '.' . $file_ext;
                $file_path = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['receipt_file']['tmp_name'], $file_path)) {
                    $receipt_file = 'uploads/expenses/' . $file_name;
                }
            }

            // Create expense
            $data = [
                'booking_id' => $booking_id,
                'tour_schedule_id' => $schedule_id,
                'expense_date' => sanitize($_POST['expense_date']),
                'category' => !empty($_POST['category']) ? sanitize($_POST['category']) : null,
                'description' => sanitize($_POST['description']),
                'amount' => (float) $_POST['amount'],
                'receipt_file' => $receipt_file,
                'reported_by' => $user_id,
                'notes' => !empty($_POST['notes']) ? sanitize($_POST['notes']) : null
            ];

            $this->expenseModel->create($data);

            set_success("Đã ghi chi phí phát sinh thành công!");
            redirect('?act=guide-expenses&action=show&schedule_id=' . $schedule_id);

        } catch (\Exception $e) {
            set_error($e->getMessage());
            redirect('?act=guide-expenses&action=create&schedule_id=' . ($_POST['schedule_id'] ?? ''));
        }
    }

    /**
     * Xóa chi phí
     */
    public function delete()
    {
        require_guide();
        $user_id = get_user_id();
        $id = $_GET['id'] ?? null;
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$id) {
            set_error("ID không hợp lệ.");
            redirect('?act=guide-expenses');
            return;
        }

        try {
            $expense = $this->expenseModel->getById($id);
            if (!$expense) {
                throw new \Exception("Chi phí không tồn tại.");
            }

            // Verify ownership through schedule
            if ($schedule_id) {
                $schedule = $this->scheduleModel->getById($schedule_id);
                if (!$schedule || $schedule['guide_id'] != $user_id) {
                    throw new \Exception("Bạn không có quyền xóa chi phí này.");
                }
            }

            // Only allow delete if pending or if user is the reporter
            if ($expense['approval_status'] !== 'pending' && $expense['reported_by'] != $user_id) {
                throw new \Exception("Chỉ có thể xóa chi phí đang chờ duyệt hoặc do chính bạn tạo.");
            }

            $this->expenseModel->delete($id);

            set_success("Đã xóa chi phí thành công!");
            redirect('?act=guide-expenses&action=show&schedule_id=' . ($schedule_id ?? ''));

        } catch (\Exception $e) {
            set_error($e->getMessage());
            redirect('?act=guide-expenses&action=show&schedule_id=' . ($schedule_id ?? ''));
        }
    }
}

