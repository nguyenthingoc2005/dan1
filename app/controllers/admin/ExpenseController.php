<?php
/**
 * ==============================================================================
 * EXPENSE CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý chi phí phát sinh trong tour - Admin
 * Routing: ?act=admin&module=expenses&action=index
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
        $this->scheduleModel = new TourSchedule($pdo);
        $this->bookingModel = new Booking($pdo);
        $this->expenseModel = new IncurredExpense($pdo);
    }

    /**
     * Danh sách chi phí phát sinh (theo schedule)
     */
    public function index()
    {
        require_admin();

        $schedule_id = $_GET['schedule_id'] ?? null;
        $page = $_GET['page'] ?? 1;
        $limit = 20;

        if ($schedule_id) {
            // Show expenses for a specific schedule
            $schedule = $this->scheduleModel->getById($schedule_id);
            if (!$schedule) {
                set_error("Không tìm thấy lịch tour.");
                redirect('?act=admin&module=schedules');
                return;
            }

            // Get Tour Details
            require_once MODELS_PATH . '/Tour.php';
            $tourModel = new Tour($this->db);
            $tour = $tourModel->findById($schedule['tour_id']);

            // Get expenses for this schedule
            $expenses = $this->expenseModel->getByScheduleId($schedule_id);
            $expense_total = $this->expenseModel->getTotalByScheduleId($schedule_id);

            $page_title = 'Chi phí phát sinh: ' . htmlspecialchars($tour['tour_code']);
            $content_file = VIEWS_PATH . '/admin/expenses/show.php';
            require VIEWS_PATH . '/layouts/admin_layout.php';
        } else {
            // List all schedules with expenses
            $filters = [];
            $result = $this->scheduleModel->getAll($filters, $page, $limit);
            $schedules_raw = $result['data'];
            $total_pages = $result['pages'];
            $current_page = $result['current_page'];

            // Loại bỏ duplicate schedules dựa trên id - sử dụng array_values để reset keys
            $schedules = [];
            $seen_schedule_ids = [];
            foreach ($schedules_raw as $schedule) {
                $schedule_id = $schedule['id'] ?? null;
                if ($schedule_id && !in_array($schedule_id, $seen_schedule_ids)) {
                    $schedules[] = $schedule;
                    $seen_schedule_ids[] = $schedule_id;
                }
            }
            // Reset array keys để đảm bảo không có vấn đề với foreach
            $schedules = array_values($schedules);

            // Get expense totals for each schedule
            foreach ($schedules as &$schedule) {
                $schedule['expense_total'] = $this->expenseModel->getTotalByScheduleId($schedule['id']);
                $expenses = $this->expenseModel->getByScheduleId($schedule['id']);
                
                // Loại bỏ duplicate dựa trên id
                $unique_expenses = [];
                $seen_ids = [];
                foreach ($expenses as $expense) {
                    $expense_id = $expense['id'] ?? null;
                    if ($expense_id && !in_array($expense_id, $seen_ids)) {
                        $unique_expenses[] = $expense;
                        $seen_ids[] = $expense_id;
                    }
                }
                $expenses = $unique_expenses;
                
                $schedule['expense_count'] = count($expenses);
                // Get approval counts for each schedule
                $schedule['expense_approved_count'] = $this->expenseModel->getCountByScheduleIdAndStatus($schedule['id'], 'approved');
                $schedule['expense_pending_count'] = $this->expenseModel->getCountByScheduleIdAndStatus($schedule['id'], 'pending');
                $schedule['expense_rejected_count'] = $this->expenseModel->getCountByScheduleIdAndStatus($schedule['id'], 'rejected');
                // Get all expenses for display
                $schedule['expenses'] = $expenses;
                // Get pending expenses for quick actions
                $schedule['pending_expenses'] = array_filter($expenses, function($exp) {
                    return ($exp['approval_status'] ?? 'pending') === 'pending';
                });
            }

            // Get overall statistics
            $expense_stats = $this->expenseModel->getApprovalStatistics();

            $page_title = 'Quản lý Chi phí phát sinh';
            $content_file = VIEWS_PATH . '/admin/expenses/index.php';
            require VIEWS_PATH . '/layouts/admin_layout.php';
        }
    }

    /**
     * Trang chi tiết chi phí cho một tour schedule
     */
    public function show()
    {
        require_admin();
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$schedule_id) {
            redirect('?act=admin&module=expenses');
            return;
        }

        $schedule = $this->scheduleModel->getById($schedule_id);

        if (!$schedule) {
            set_error("Không tìm thấy lịch tour.");
            redirect('?act=admin&module=expenses');
            return;
        }

        // Get Tour Details
        require_once MODELS_PATH . '/Tour.php';
        $tourModel = new Tour($this->db);
        $tour = $tourModel->findById($schedule['tour_id']);

        // Get bookings for this schedule
        $bookings = $this->bookingModel->getAll([
            'tour_schedule_id' => $schedule_id
        ], 1, 1000);
        
        if (empty($bookings)) {
            $bookings = $this->bookingModel->getAll([
                'tour_id' => $schedule['tour_id'],
                'start_date' => $schedule['start_date'],
                'exact_date' => true
            ], 1, 1000);
        }

        // Get expenses for this schedule
        $expenses = $this->expenseModel->getByScheduleId($schedule_id);
        $expense_total = $this->expenseModel->getTotalByScheduleId($schedule_id);

        $page_title = 'Chi phí phát sinh: ' . htmlspecialchars($tour['tour_code']);
        $content_file = VIEWS_PATH . '/admin/expenses/show.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Trang tạo chi phí mới
     */
    public function create()
    {
        require_admin();
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$schedule_id) {
            redirect('?act=admin&module=expenses');
            return;
        }

        $schedule = $this->scheduleModel->getById($schedule_id);

        if (!$schedule) {
            set_error("Không tìm thấy lịch tour.");
            redirect('?act=admin&module=expenses');
            return;
        }

        // Get Tour Details
        require_once MODELS_PATH . '/Tour.php';
        $tourModel = new Tour($this->db);
        $tour = $tourModel->findById($schedule['tour_id']);

        // Get bookings for this schedule
        $bookings = $this->bookingModel->getAll([
            'tour_schedule_id' => $schedule_id
        ], 1, 1000);
        
        if (empty($bookings)) {
            $bookings = $this->bookingModel->getAll([
                'tour_id' => $schedule['tour_id'],
                'start_date' => $schedule['start_date'],
                'exact_date' => true
            ], 1, 1000);
        }

        $page_title = 'Thêm chi phí phát sinh';
        $content_file = VIEWS_PATH . '/admin/expenses/create.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý lưu chi phí mới
     */
    public function store()
    {
        require_admin();
        require_csrf_token();
        $user_id = get_user_id();

        try {
            if (empty($_POST['schedule_id'])) {
                throw new \Exception("Schedule ID không hợp lệ.");
            }

            $schedule_id = (int) $_POST['schedule_id'];
            $schedule = $this->scheduleModel->getById($schedule_id);

            if (!$schedule) {
                throw new \Exception("Không tìm thấy lịch tour.");
            }

            // Validate booking_id (optional for admin)
            $booking_id = !empty($_POST['booking_id']) ? (int) $_POST['booking_id'] : null;
            if ($booking_id) {
                $booking = $this->bookingModel->getById($booking_id);
                if (!$booking) {
                    throw new \Exception("Booking không tồn tại.");
                }
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

            // Admin can auto-approve if checkbox is checked
            if (!empty($_POST['auto_approve'])) {
                $data['approval_status'] = 'approved';
                $data['approved_by'] = $user_id;
            }

            $this->expenseModel->create($data);

            set_success("Đã thêm chi phí phát sinh thành công!");
            redirect('?act=admin&module=expenses&action=show&schedule_id=' . $schedule_id);

        } catch (\Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=expenses&action=create&schedule_id=' . ($_POST['schedule_id'] ?? ''));
        }
    }

    /**
     * Trang sửa chi phí
     */
    public function edit()
    {
        require_admin();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            redirect('?act=admin&module=expenses');
            return;
        }

        $expense = $this->expenseModel->getById($id);
        if (!$expense) {
            set_error("Không tìm thấy chi phí.");
            redirect('?act=admin&module=expenses');
            return;
        }

        // Get schedule
        $schedule_id = null;
        if (!empty($expense['tour_schedule_id'])) {
            $schedule_id = $expense['tour_schedule_id'];
        } elseif (!empty($expense['booking_id'])) {
            $booking = $this->bookingModel->getById($expense['booking_id']);
            if ($booking && !empty($booking['tour_schedule_id'])) {
                $schedule_id = $booking['tour_schedule_id'];
            }
        }

        if (!$schedule_id) {
            set_error("Không tìm thấy lịch tour liên quan.");
            redirect('?act=admin&module=expenses');
            return;
        }

        $schedule = $this->scheduleModel->getById($schedule_id);
        if (!$schedule) {
            set_error("Không tìm thấy lịch tour.");
            redirect('?act=admin&module=expenses');
            return;
        }

        // Get Tour Details
        require_once MODELS_PATH . '/Tour.php';
        $tourModel = new Tour($this->db);
        $tour = $tourModel->findById($schedule['tour_id']);

        // Get bookings for this schedule
        $bookings = $this->bookingModel->getAll([
            'tour_schedule_id' => $schedule_id
        ], 1, 1000);

        $page_title = 'Sửa chi phí phát sinh';
        $content_file = VIEWS_PATH . '/admin/expenses/edit.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý cập nhật chi phí
     */
    public function update()
    {
        require_admin();
        require_csrf_token();
        $user_id = get_user_id();

        try {
            if (empty($_POST['id'])) {
                throw new \Exception("ID không hợp lệ.");
            }

            $id = (int) $_POST['id'];
            $expense = $this->expenseModel->getById($id);
            if (!$expense) {
                throw new \Exception("Không tìm thấy chi phí.");
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
            $receipt_file = $expense['receipt_file'];
            if (!empty($_FILES['receipt_file']['name'])) {
                $upload_dir = PUBLIC_PATH . '/uploads/expenses/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $file_ext = pathinfo($_FILES['receipt_file']['name'], PATHINFO_EXTENSION);
                $file_name = 'expense_' . time() . '_' . uniqid() . '.' . $file_ext;
                $file_path = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['receipt_file']['tmp_name'], $file_path)) {
                    // Delete old file if exists
                    if ($receipt_file && file_exists(PUBLIC_PATH . '/' . $receipt_file)) {
                        unlink(PUBLIC_PATH . '/' . $receipt_file);
                    }
                    $receipt_file = 'uploads/expenses/' . $file_name;
                }
            }

            // Update expense
            $data = [
                'expense_date' => sanitize($_POST['expense_date']),
                'category' => !empty($_POST['category']) ? sanitize($_POST['category']) : null,
                'description' => sanitize($_POST['description']),
                'amount' => (float) $_POST['amount'],
                'receipt_file' => $receipt_file,
                'notes' => !empty($_POST['notes']) ? sanitize($_POST['notes']) : null
            ];

            if (!empty($_POST['booking_id'])) {
                $data['booking_id'] = (int) $_POST['booking_id'];
            }

            $this->expenseModel->update($id, $data);

            set_success("Đã cập nhật chi phí thành công!");
            $schedule_id = !empty($_POST['schedule_id']) ? $_POST['schedule_id'] : null;
            if ($schedule_id) {
                redirect('?act=admin&module=expenses&action=show&schedule_id=' . $schedule_id);
            } else {
                redirect('?act=admin&module=expenses');
            }

        } catch (\Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=expenses&action=edit&id=' . ($_POST['id'] ?? ''));
        }
    }

    /**
     * Xóa chi phí
     */
    public function delete()
    {
        require_admin();
        $id = $_GET['id'] ?? null;
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$id) {
            set_error("ID không hợp lệ.");
            redirect('?act=admin&module=expenses');
            return;
        }

        try {
            $expense = $this->expenseModel->getById($id);
            if (!$expense) {
                throw new \Exception("Chi phí không tồn tại.");
            }

            $this->expenseModel->delete($id);

            set_success("Đã xóa chi phí thành công!");
            if ($schedule_id) {
                redirect('?act=admin&module=expenses&action=show&schedule_id=' . $schedule_id);
            } else {
                redirect('?act=admin&module=expenses');
            }

        } catch (\Exception $e) {
            set_error($e->getMessage());
            if ($schedule_id) {
                redirect('?act=admin&module=expenses&action=show&schedule_id=' . $schedule_id);
            } else {
                redirect('?act=admin&module=expenses');
            }
        }
    }

    /**
     * Duyệt chi phí
     */
    public function approve()
    {
        require_admin();
        
        // Kiểm tra CSRF token từ GET parameter
        $token = $_GET['token'] ?? '';
        if (!verify_csrf_token($token)) {
            set_error("CSRF token không hợp lệ. Vui lòng thử lại.");
            redirect('?act=admin&module=expenses');
            return;
        }
        
        $user_id = get_user_id();
        $id = $_GET['id'] ?? null;
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$id) {
            set_error("ID không hợp lệ.");
            redirect('?act=admin&module=expenses');
            return;
        }

        try {
            $expense = $this->expenseModel->getById($id);
            if (!$expense) {
                throw new \Exception("Chi phí không tồn tại.");
            }

            $this->expenseModel->update($id, [
                'approval_status' => 'approved',
                'approved_by' => $user_id
            ]);

            set_success("Đã duyệt chi phí thành công!");
            redirect('?act=admin&module=expenses');

        } catch (\Exception $e) {
            set_error($e->getMessage());
            if ($schedule_id) {
                redirect('?act=admin&module=expenses&action=show&schedule_id=' . $schedule_id);
            } else {
                redirect('?act=admin&module=expenses');
            }
        }
    }

    /**
     * Từ chối chi phí
     */
    public function reject()
    {
        require_admin();
        
        // Kiểm tra CSRF token từ GET hoặc POST parameter
        $token = $_GET['token'] ?? $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            set_error("CSRF token không hợp lệ. Vui lòng thử lại.");
            redirect('?act=admin&module=expenses');
            return;
        }
        
        $user_id = get_user_id();
        $id = $_GET['id'] ?? null;
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$id) {
            set_error("ID không hợp lệ.");
            redirect('?act=admin&module=expenses');
            return;
        }

        try {
            $expense = $this->expenseModel->getById($id);
            if (!$expense) {
                throw new \Exception("Chi phí không tồn tại.");
            }

            $rejection_reason = !empty($_POST['rejection_reason']) ? sanitize($_POST['rejection_reason']) : null;

            $update_data = [
                'approval_status' => 'rejected',
                'approved_by' => $user_id
            ];

            if ($rejection_reason) {
                $update_data['notes'] = ($expense['notes'] ? $expense['notes'] . "\n\n" : '') . "Lý do từ chối: " . $rejection_reason;
            }

            $this->expenseModel->update($id, $update_data);

            set_success("Đã từ chối chi phí.");
            redirect('?act=admin&module=expenses');

        } catch (\Exception $e) {
            set_error($e->getMessage());
            if ($schedule_id) {
                redirect('?act=admin&module=expenses&action=show&schedule_id=' . $schedule_id);
            } else {
                redirect('?act=admin&module=expenses');
            }
        }
    }

    /**
     * Duyệt tất cả chi phí pending của một schedule
     */
    public function approveAll()
    {
        require_admin();
        
        // Kiểm tra CSRF token từ GET parameter
        $token = $_GET['token'] ?? '';
        if (!verify_csrf_token($token)) {
            set_error("CSRF token không hợp lệ. Vui lòng thử lại.");
            redirect('?act=admin&module=expenses');
            return;
        }
        
        $user_id = get_user_id();
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$schedule_id) {
            set_error("Schedule ID không hợp lệ.");
            redirect('?act=admin&module=expenses');
            return;
        }

        try {
            // Lấy tất cả expenses pending của schedule này
            $expenses = $this->expenseModel->getByScheduleId($schedule_id);
            $pending_expenses = array_filter($expenses, function($exp) {
                return ($exp['approval_status'] ?? 'pending') === 'pending';
            });

            if (empty($pending_expenses)) {
                set_error("Không có chi phí nào chờ duyệt.");
                redirect('?act=admin&module=expenses');
                return;
            }

            $count = 0;
            foreach ($pending_expenses as $expense) {
                $this->expenseModel->update($expense['id'], [
                    'approval_status' => 'approved',
                    'approved_by' => $user_id
                ]);
                $count++;
            }

            set_success("Đã duyệt thành công {$count} chi phí!");
            redirect('?act=admin&module=expenses');

        } catch (\Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=expenses&action=show&schedule_id=' . $schedule_id);
        }
    }

    /**
     * Từ chối tất cả chi phí pending của một schedule
     */
    public function rejectAll()
    {
        require_admin();
        
        // Kiểm tra CSRF token từ POST hoặc GET
        $token = $_POST['csrf_token'] ?? $_GET['token'] ?? '';
        if (!verify_csrf_token($token)) {
            set_error("CSRF token không hợp lệ. Vui lòng thử lại.");
            redirect('?act=admin&module=expenses');
            return;
        }
        
        $user_id = get_user_id();
        $schedule_id = $_POST['schedule_id'] ?? $_GET['schedule_id'] ?? null;

        if (!$schedule_id) {
            set_error("Schedule ID không hợp lệ.");
            redirect('?act=admin&module=expenses');
            return;
        }

        try {
            // Lấy tất cả expenses pending của schedule này
            $expenses = $this->expenseModel->getByScheduleId($schedule_id);
            $pending_expenses = array_filter($expenses, function($exp) {
                return ($exp['approval_status'] ?? 'pending') === 'pending';
            });

            if (empty($pending_expenses)) {
                set_error("Không có chi phí nào chờ duyệt.");
                redirect('?act=admin&module=expenses');
                return;
            }

            $rejection_reason = !empty($_POST['rejection_reason']) ? sanitize($_POST['rejection_reason']) : null;

            $count = 0;
            foreach ($pending_expenses as $expense) {
                $update_data = [
                    'approval_status' => 'rejected',
                    'approved_by' => $user_id
                ];

                if ($rejection_reason) {
                    $update_data['notes'] = ($expense['notes'] ? $expense['notes'] . "\n\n" : '') . "Lý do từ chối: " . $rejection_reason;
                }

                $this->expenseModel->update($expense['id'], $update_data);
                $count++;
            }

            set_success("Đã từ chối {$count} chi phí!");
            redirect('?act=admin&module=expenses');

        } catch (\Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=expenses&action=show&schedule_id=' . $schedule_id);
        }
    }
}

