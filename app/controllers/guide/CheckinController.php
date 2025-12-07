<?php
namespace Guide;

/**
 * ==============================================================================
 * CHECKIN CONTROLLER (GUIDE)
 * ==============================================================================
 * 
 * Quản lý check-in hành khách cho tour
 * Routing: ?act=guide-checkin&action=index
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class CheckinController
{
    private $db;
    private $scheduleModel;
    private $bookingModel;
    private $checkinModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/TourSchedule.php';
        require_once MODELS_PATH . '/Booking.php';
        require_once MODELS_PATH . '/Checkin.php';
        $this->scheduleModel = new \TourSchedule($pdo);
        $this->bookingModel = new \Booking($pdo);
        $this->checkinModel = new \Checkin($pdo);
    }

    /**
     * Danh sách tour cần check-in
     */
    public function index()
    {
        require_guide();
        $user_id = get_user_id();

        // Filter: Only my assigned tours (KHÔNG filter start_date mặc định - hiển thị TẤT CẢ)
        $filters = [
            'guide_id' => $user_id
        ];

        // Allow filtering by date range if provided
        $filter_type = $_GET['filter'] ?? 'upcoming'; // upcoming (default), all, history
        $today = date('Y-m-d');
        
        if ($filter_type === 'upcoming') {
            // Chỉ lấy tours từ hôm nay trở đi (mặc định)
            $filters['start_date'] = $today;
        } elseif ($filter_type === 'history') {
            // Chỉ lấy tours trong quá khứ (trước hôm nay)
            $filters['end_date'] = date('Y-m-d', strtotime('-1 day'));
        }
        // Nếu filter_type === 'all', không thêm filter start_date/end_date

        $page = $_GET['page'] ?? 1;
        $result = $this->scheduleModel->getAll($filters, $page, 10);
        $schedules = $result['data'];
        $total_pages = $result['pages'];
        $current_page = $result['current_page'];

        // Get check-in stats for each schedule
        foreach ($schedules as &$schedule) {
            $schedule['checkin_stats'] = $this->checkinModel->getStatsBySchedule($schedule['id']);
        }

        $page_title = 'Check-in Hành khách';
        $content_file = VIEWS_PATH . '/guide/checkin/index.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }

    /**
     * Trang check-in cho một tour schedule
     */
    public function show()
    {
        require_guide();
        $user_id = get_user_id();
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$schedule_id) {
            redirect('?act=guide-checkin');
            return;
        }

        $schedule = $this->scheduleModel->getById($schedule_id);

        if (!$schedule) {
            set_error("Không tìm thấy lịch tour.");
            redirect('?act=guide-checkin');
            return;
        }

        // Verify ownership
        if ($schedule['guide_id'] != $user_id) {
            set_error("Bạn không được phân công tour này.");
            redirect('?act=guide-checkin');
            return;
        }

        // Get Tour Details
        require_once MODELS_PATH . '/Tour.php';
        $tourModel = new \Tour($this->db);
        $tour = $tourModel->findById($schedule['tour_id']);

        // Get all bookings for this schedule using tour_schedule_id (direct link)
        // Fallback to tour_id + start_date for backward compatibility with old bookings
        $allBookings = $this->bookingModel->getAll([
            'tour_schedule_id' => $schedule_id,
            'status' => 'paid'
        ], 1, 1000);
        
        // If no bookings found by tour_schedule_id, try fallback method (for old bookings)
        if (empty($allBookings)) {
            $allBookings = $this->bookingModel->getAll([
                'tour_id' => $schedule['tour_id'],
                'start_date' => $schedule['start_date'],
                'exact_date' => true,
                'status' => 'paid'
            ], 1, 1000);
        }

        // Filter bookings: Chỉ cho phép check-in nếu đã thanh toán đủ
        // Điều kiện: payment_status = 'paid' AND remaining_amount = 0
        $bookings = [];
        foreach ($allBookings as $booking) {
            if (in_array($booking['payment_status'], ['partial', 'paid']) 
                && (float)$booking['remaining_amount'] == 0) {
                $bookings[] = $booking;
            }
        }
        
        // Kiểm tra ngày bắt đầu: Chỉ cho phép check-in khi đến ngày bắt đầu tour
        $today = date('Y-m-d');
        $can_checkin = ($schedule['start_date'] <= $today);

        // Get all passengers with their check-in status
        // Chỉ lấy passengers có customer_id hợp lệ (tồn tại trong bảng customers)
        $passengers = [];
        foreach ($bookings as $booking) {
            $p_list = $this->bookingModel->getPassengers($booking['id']);
            foreach ($p_list as $p) {
                // Chỉ thêm passenger nếu có customer_id hợp lệ (không NULL và tồn tại trong customers)
                if (!empty($p['customer_id']) && !empty($p['id'])) {
                    $checkin = $this->checkinModel->getCustomerCheckin($booking['id'], $p['id']);
                    $p['booking_id'] = $booking['id'];
                    $p['booking_code'] = $booking['booking_code'];
                    $p['checkin_status'] = $checkin ? $checkin['status'] : null;
                    $p['checkin_time'] = $checkin ? $checkin['checkin_time'] : null;
                    $p['checkin_notes'] = $checkin ? $checkin['notes'] : null;
                    $passengers[] = $p;
                }
            }
        }

        // Get check-in stats
        $stats = $this->checkinModel->getStatsBySchedule($schedule_id);

        $page_title = 'Check-in: ' . htmlspecialchars($tour['tour_code']);
        $content_file = VIEWS_PATH . '/guide/checkin/show.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }

    /**
     * Xử lý lưu check-in
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

            // Get check-ins from POST
            $checkins = [];
            if (!empty($_POST['checkins']) && is_array($_POST['checkins'])) {
                foreach ($_POST['checkins'] as $checkin_data) {
                    if (empty($checkin_data['booking_id']) || empty($checkin_data['customer_id'])) {
                        continue;
                    }

                    // Validate booking before allowing check-in
                    $booking = $this->bookingModel->getById((int) $checkin_data['booking_id']);
                    if (!$booking) {
                        throw new \Exception("Booking không tồn tại.");
                    }

                    // Kiểm tra ngày bắt đầu: Chỉ cho phép check-in khi đến ngày bắt đầu tour
                    $today = date('Y-m-d');
                    if ($schedule['start_date'] > $today) {
                        throw new \Exception("Chưa đến ngày bắt đầu tour. Chỉ có thể check-in từ ngày " . date('d/m/Y', strtotime($schedule['start_date'])) . " trở đi.");
                    }

                    // Điều kiện check-in: payment_status = 'paid' AND remaining_amount = 0
                    if ($booking['payment_status'] !== 'paid') {
                        throw new \Exception("Booking #{$booking['booking_code']} chưa thanh toán đủ. Vui lòng thanh toán trước khi check-in.");
                    }
                    if ((float)$booking['remaining_amount'] > 0) {
                        throw new \Exception("Booking #{$booking['booking_code']} còn nợ " . number_format($booking['remaining_amount']) . " VNĐ. Vui lòng thanh toán đủ trước khi check-in.");
                    }
                    
                    // Validate customer_id tồn tại trong bảng customers
                    $customer_id = (int) $checkin_data['customer_id'];
                    $stmt = $this->db->prepare("SELECT id FROM customers WHERE id = :id");
                    $stmt->execute(['id' => $customer_id]);
                    if (!$stmt->fetch()) {
                        throw new \Exception("Customer ID không hợp lệ hoặc không tồn tại trong hệ thống.");
                    }

                    $checkins[] = [
                        'booking_id' => (int) $checkin_data['booking_id'],
                        'customer_id' => (int) $checkin_data['customer_id'],
                        'status' => sanitize($checkin_data['status'] ?? 'present'),
                        'notes' => !empty($checkin_data['notes']) ? sanitize($checkin_data['notes']) : null
                    ];
                }
            }

            if (empty($checkins)) {
                throw new \Exception("Không có dữ liệu check-in.");
            }

            // Batch check-in
            $this->checkinModel->batchCheckin($checkins, $user_id);

            set_success("Đã lưu check-in thành công!");
            redirect('?act=guide-checkin&action=show&schedule_id=' . $schedule_id);

        } catch (\Exception $e) {
            set_error($e->getMessage());
            redirect('?act=guide-checkin&action=show&schedule_id=' . ($_POST['schedule_id'] ?? ''));
        }
    }

    /**
     * In danh sách hành khách (Manifest)
     */
    public function printManifest()
    {
        require_guide();
        $user_id = get_user_id();
        $schedule_id = $_GET['schedule_id'] ?? null;

        if (!$schedule_id) {
            redirect('?act=guide-checkin');
            return;
        }

        $schedule = $this->scheduleModel->getById($schedule_id);

        if (!$schedule) {
            set_error("Không tìm thấy lịch tour.");
            redirect('?act=guide-checkin');
            return;
        }

        // Verify ownership
        if ($schedule['guide_id'] != $user_id) {
            set_error("Bạn không được phân công tour này.");
            redirect('?act=guide-checkin');
            return;
        }

        // Get Tour Details với đầy đủ thông tin
        require_once MODELS_PATH . '/Tour.php';
        $tourModel = new \Tour($this->db);
        $tour = $tourModel->findById($schedule['tour_id']);
        
        // Bổ sung thông tin từ schedule vào tour để hiển thị đầy đủ
        $tour['schedule_start_date'] = $schedule['start_date'];
        $tour['schedule_end_date'] = $schedule['end_date'];
        $tour['schedule_quota'] = $schedule['quota'];
        $tour['schedule_booked'] = $schedule['booked'];
        $tour['schedule_status'] = $schedule['status'];

        // Get all bookings for this schedule using tour_schedule_id (direct link)
        // Fallback to tour_id + start_date for backward compatibility with old bookings
        $allBookings = $this->bookingModel->getAll([
            'tour_schedule_id' => $schedule_id,
            'status' => 'paid'
        ], 1, 1000);
        
        // If no bookings found by tour_schedule_id, try fallback method (for old bookings)
        if (empty($allBookings)) {
            $allBookings = $this->bookingModel->getAll([
                'tour_id' => $schedule['tour_id'],
                'start_date' => $schedule['start_date'],
                'exact_date' => true,
                'status' => 'paid'
            ], 1, 1000);
        }

        // Filter bookings: Chỉ cho phép check-in nếu đã thanh toán đủ
        // Điều kiện: payment_status = 'paid' AND remaining_amount = 0
        $bookings = [];
        foreach ($allBookings as $booking) {
            if (in_array($booking['payment_status'], ['partial', 'paid']) 
                && (float)$booking['remaining_amount'] == 0) {
                $bookings[] = $booking;
            }
        }
        
        // Kiểm tra ngày bắt đầu: Chỉ cho phép check-in khi đến ngày bắt đầu tour
        $today = date('Y-m-d');
        $can_checkin = ($schedule['start_date'] <= $today);

        // Get all passengers
        $passengers = [];
        foreach ($bookings as $booking) {
            $p_list = $this->bookingModel->getPassengers($booking['id']);
            foreach ($p_list as $p) {
                $checkin = $this->checkinModel->getCustomerCheckin($booking['id'], $p['id']);
                $p['booking_id'] = $booking['id'];
                $p['booking_code'] = $booking['booking_code'];
                $p['checkin_status'] = $checkin ? $checkin['status'] : null;
                $p['checkin_time'] = $checkin ? $checkin['checkin_time'] : null;
                $passengers[] = $p;
            }
        }

        // Get check-in stats
        $stats = $this->checkinModel->getStatsBySchedule($schedule_id);

        $page_title = 'Danh sách Hành khách - ' . htmlspecialchars($tour['tour_code']);
        $content_file = VIEWS_PATH . '/guide/checkin/manifest.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }
}

