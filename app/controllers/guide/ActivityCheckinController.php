<?php
namespace Guide;

/**
 * ==============================================================================
 * ACTIVITY CHECKIN CONTROLLER (GUIDE)
 * ==============================================================================
 * 
 * Quản lý check-in theo checkpoint
 * HDV check-in hành khách cho từng checkpoint
 * 
 * Routing: ?act=guide-activity-checkin&action=index
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class ActivityCheckinController
{
    private $db;
    private $scheduleModel;
    private $bookingModel;
    private $checkpointModel;
    private $checkinModel;
    private $summaryModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/TourSchedule.php';
        require_once MODELS_PATH . '/Booking.php';
        require_once MODELS_PATH . '/ActivityCheckpoint.php';
        require_once MODELS_PATH . '/ActivityCheckin.php';
        require_once MODELS_PATH . '/ActivityCheckinSummary.php';
        $this->scheduleModel = new \TourSchedule($pdo);
        $this->bookingModel = new \Booking($pdo);
        $this->checkpointModel = new \ActivityCheckpoint($pdo);
        $this->checkinModel = new \ActivityCheckin($pdo);
        $this->summaryModel = new \ActivityCheckinSummary($pdo);
    }

    /**
     * Danh sách tour schedules cần check-in
     */
    public function index()
    {
        require_guide();
        $user_id = get_user_id();

        $filters = [
            'guide_id' => $user_id
        ];

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

        // Get checkpoint counts for each schedule
        foreach ($schedules as &$schedule) {
            $checkpoints = $this->checkpointModel->getBySchedule($schedule['id']);
            $schedule['checkpoint_count'] = count($checkpoints);
            $schedule['active_checkpoint_count'] = count(array_filter($checkpoints, fn($cp) => $cp['status'] == 'active'));
        }

        $page_title = 'Check-in theo Hoạt động';
        $content_file = VIEWS_PATH . '/guide/activity-checkin/index.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }

    /**
     * Danh sách checkpoints của schedule
     */
    public function checkpoints()
    {
        require_guide();
        $user_id = get_user_id();
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$schedule_id) {
            redirect('?act=guide-activity-checkin');
            return;
        }

        $schedule = $this->scheduleModel->getById($schedule_id);

        if (!$schedule) {
            set_error("Không tìm thấy lịch tour.");
            redirect('?act=guide-activity-checkin');
            return;
        }

        // Verify ownership
        if ($schedule['guide_id'] != $user_id) {
            set_error("Bạn không được phân công tour này.");
            redirect('?act=guide-activity-checkin');
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
        $content_file = VIEWS_PATH . '/guide/activity-checkin/checkpoints.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }

    /**
     * Trang check-in cho checkpoint
     */
    public function show()
    {
        require_guide();
        $user_id = get_user_id();
        $checkpoint_id = $_GET['checkpoint_id'] ?? null;

        if (!$checkpoint_id) {
            redirect('?act=guide-activity-checkin');
            return;
        }

        $checkpoint = $this->checkpointModel->getById($checkpoint_id);

        if (!$checkpoint) {
            set_error("Không tìm thấy checkpoint.");
            redirect('?act=guide-activity-checkin');
            return;
        }

        // Verify schedule ownership
        if ($checkpoint['guide_id'] != $user_id) {
            set_error("Bạn không được phân công tour này.");
            redirect('?act=guide-activity-checkin');
            return;
        }

        // Get Tour Details
        require_once MODELS_PATH . '/Tour.php';
        $tourModel = new \Tour($this->db);
        $tour = $tourModel->findById($checkpoint['tour_id']);

        $schedule = $this->scheduleModel->getById($checkpoint['tour_schedule_id']);

        // Get all bookings for this schedule
        $allBookings = $this->bookingModel->getAll([
            'tour_schedule_id' => $checkpoint['tour_schedule_id'],
            'status' => 'paid'
        ], 1, 1000);
        
        if (empty($allBookings)) {
            $allBookings = $this->bookingModel->getAll([
                'tour_id' => $checkpoint['tour_id'],
                'start_date' => $schedule['start_date'],
                'exact_date' => true,
                'status' => 'paid'
            ], 1, 1000);
        }

        // Filter bookings: Chỉ cho phép check-in nếu đã thanh toán đủ
        $bookings = [];
        foreach ($allBookings as $booking) {
            if (in_array($booking['payment_status'], ['partial', 'paid']) 
                && (float)$booking['remaining_amount'] == 0) {
                $bookings[] = $booking;
            }
        }

        // Get all passengers with their check-in status
        $passengers = [];
        foreach ($bookings as $booking) {
            $p_list = $this->bookingModel->getPassengers($booking['id']);
            foreach ($p_list as $p) {
                if (!empty($p['customer_id'])) {
                    $stmt = $this->db->prepare("SELECT id FROM customers WHERE id = :id");
                    $stmt->execute(['id' => $p['customer_id']]);
                    if ($stmt->fetch()) {
                        $checkin = $this->checkinModel->getCustomerCheckin($checkpoint_id, $p['id']);
                        $p['booking_id'] = $booking['id'];
                        $p['booking_code'] = $booking['booking_code'];
                        $p['checkin_status'] = $checkin ? $checkin['status'] : null;
                        $p['checkin_time'] = $checkin ? $checkin['checkin_datetime'] : null;
                        $p['checkin_notes'] = $checkin ? $checkin['notes'] : null;
                        $p['minutes_late'] = $checkin ? $checkin['minutes_late'] : null;
                        $passengers[] = $p;
                    }
                }
            }
        }

        // Get stats
        $stats = $this->checkinModel->getStatsByCheckpoint($checkpoint_id);
        $summary = $this->summaryModel->getByCheckpoint($checkpoint_id, $checkpoint['scheduled_date']);

        // Check if can check-in (date must be today or past)
        $today = date('Y-m-d');
        $can_checkin = ($checkpoint['scheduled_date'] <= $today);

        $page_title = 'Check-in: ' . htmlspecialchars($checkpoint['checkpoint_name']);
        $content_file = VIEWS_PATH . '/guide/activity-checkin/show.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }

    /**
     * Lưu check-in
     */
    public function store()
    {
        require_guide();
        require_csrf_token();
        $user_id = get_user_id();

        try {
            if (empty($_POST['checkpoint_id'])) {
                throw new \Exception("Checkpoint ID không hợp lệ.");
            }

            $checkpoint_id = (int) $_POST['checkpoint_id'];
            $checkpoint = $this->checkpointModel->getById($checkpoint_id);

            if (!$checkpoint) {
                throw new \Exception("Không tìm thấy checkpoint.");
            }

            // Verify schedule ownership
            if ($checkpoint['guide_id'] != $user_id) {
                throw new \Exception("Bạn không được phân công tour này.");
            }

            // Check date
            $today = date('Y-m-d');
            if ($checkpoint['scheduled_date'] > $today) {
                throw new \Exception("Chưa đến ngày checkpoint. Chỉ có thể check-in từ ngày " . date('d/m/Y', strtotime($checkpoint['scheduled_date'])) . " trở đi.");
            }

            // Get check-ins from POST
            $checkins = [];
            if (!empty($_POST['checkins']) && is_array($_POST['checkins'])) {
                foreach ($_POST['checkins'] as $checkin_data) {
                    if (empty($checkin_data['booking_customer_id'])) {
                        continue;
                    }

                    // Validate booking_customer_id
                    $stmt = $this->db->prepare("SELECT id FROM booking_customers WHERE id = :id");
                    $stmt->execute(['id' => $checkin_data['booking_customer_id']]);
                    if (!$stmt->fetch()) {
                        continue;
                    }

                    $checkins[] = [
                        'booking_customer_id' => (int) $checkin_data['booking_customer_id'],
                        'status' => sanitize($checkin_data['status'] ?? 'present'),
                        'actual_time' => !empty($checkin_data['actual_time']) ? sanitize($checkin_data['actual_time']) : null,
                        'notes' => !empty($checkin_data['notes']) ? sanitize($checkin_data['notes']) : null,
                        'excused_reason' => !empty($checkin_data['excused_reason']) ? sanitize($checkin_data['excused_reason']) : null
                    ];
                }
            }

            if (empty($checkins)) {
                throw new \Exception("Không có dữ liệu check-in.");
            }

            // Batch check-in
            $this->checkinModel->batchCheckin($checkpoint_id, $checkins, $user_id);

            set_success("Đã lưu check-in thành công!");
            redirect('?act=guide-activity-checkin&action=show&checkpoint_id=' . $checkpoint_id);

        } catch (\Exception $e) {
            set_error($e->getMessage());
            redirect('?act=guide-activity-checkin&action=show&checkpoint_id=' . ($_POST['checkpoint_id'] ?? ''));
        }
    }

    /**
     * Xem summary checkpoint
     */
    public function summary()
    {
        require_guide();
        $user_id = get_user_id();
        $checkpoint_id = $_GET['checkpoint_id'] ?? null;

        if (!$checkpoint_id) {
            redirect('?act=guide-activity-checkin');
            return;
        }

        $checkpoint = $this->checkpointModel->getById($checkpoint_id);

        if (!$checkpoint) {
            set_error("Không tìm thấy checkpoint.");
            redirect('?act=guide-activity-checkin');
            return;
        }

        // Verify schedule ownership
        if ($checkpoint['guide_id'] != $user_id) {
            set_error("Bạn không được phân công tour này.");
            redirect('?act=guide-activity-checkin');
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

        $page_title = 'Summary: ' . htmlspecialchars($checkpoint['checkpoint_name']);
        $content_file = VIEWS_PATH . '/guide/activity-checkin/summary.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }

    /**
     * Lịch sử check-in của khách
     */
    public function history()
    {
        require_guide();
        $user_id = get_user_id();
        $schedule_id = $_GET['schedule_id'] ?? null;
        $customer_id = $_GET['customer_id'] ?? null;

        if (!$schedule_id || !$customer_id) {
            redirect('?act=guide-activity-checkin');
            return;
        }

        $schedule = $this->scheduleModel->getById($schedule_id);

        if (!$schedule || $schedule['guide_id'] != $user_id) {
            set_error("Bạn không được phân công tour này.");
            redirect('?act=guide-activity-checkin');
            return;
        }

        // Get customer info
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id = :id");
        $stmt->execute(['id' => $customer_id]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$customer) {
            set_error("Không tìm thấy khách hàng.");
            redirect('?act=guide-activity-checkin');
            return;
        }

        // Get check-in history
        $history = $this->checkinModel->getByCustomer($schedule_id, $customer_id);

        // Get Tour Details
        require_once MODELS_PATH . '/Tour.php';
        $tourModel = new \Tour($this->db);
        $tour = $tourModel->findById($schedule['tour_id']);

        $page_title = 'Lịch sử Check-in: ' . htmlspecialchars($customer['full_name']);
        $content_file = VIEWS_PATH . '/guide/activity-checkin/history.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }
}

