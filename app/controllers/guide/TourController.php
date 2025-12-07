<?php
namespace Guide;

/**
 * GUIDE TOUR CONTROLLER
 * Quản lý lịch tour được phân công
 */
class TourController
{
    private $db;
    private $scheduleModel;
    private $bookingModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/TourSchedule.php';
        require_once MODELS_PATH . '/Booking.php';
        $this->scheduleModel = new \TourSchedule($pdo);
        $this->bookingModel = new \Booking($pdo);
    }

    public function index()
    {
        require_guide();
        $user_id = get_user_id();

        $page = $_GET['page'] ?? 1;
        $limit = 10;

        // Filter: Only my assigned tours
        $filters = [
            'guide_id' => $user_id
            // KHÔNG filter start_date mặc định - hiển thị TẤT CẢ tours được gán
        ];

        // Allow filtering by date range if provided
        $filter_type = $_GET['filter'] ?? 'all'; // all, upcoming, history
        $today = date('Y-m-d');
        
        if ($filter_type === 'upcoming') {
            // Chỉ lấy tours từ hôm nay trở đi
            $filters['start_date'] = $today;
        } elseif ($filter_type === 'history') {
            // Chỉ lấy tours trong quá khứ (trước hôm nay)
            $filters['end_date'] = date('Y-m-d', strtotime('-1 day'));
        }
        // Nếu filter_type === 'all' hoặc không có, không thêm filter start_date/end_date

        $result = $this->scheduleModel->getAll($filters, $page, $limit);
        $schedules = $result['data'];
        $total_pages = $result['pages'];
        $current_page = $result['current_page'];

        $page_title = 'Lịch Tour Của Tôi';
        $content_file = VIEWS_PATH . '/guide/tours/index.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }

    public function show()
    {
        require_guide();
        $user_id = get_user_id();
        $id = $_GET['id'] ?? null;

        if (!$id)
            redirect('?act=guide-tours');

        $schedule = $this->scheduleModel->getById($id);

        if (!$schedule) {
            set_error("Không tìm thấy lịch tour.");
            redirect('?act=guide-tours');
        }

        // Verify ownership
        if ($schedule['guide_id'] != $user_id) {
            set_error("Bạn không được phân công tour này.");
            redirect('?act=guide-tours');
        }

        // Get Tour Details
        require_once MODELS_PATH . '/Tour.php';
        $tourModel = new \Tour($this->db);
        $tour = $tourModel->findById($schedule['tour_id']);

        // Get Passenger List (from all bookings in this schedule)
        // We need a method in Booking model or here to get passengers by schedule
        // Since bookings are linked to tour_id + start_date (or schedule_id if we had it directly)
        // Let's use tour_id + start_date

        // Get bookings for this schedule using tour_schedule_id (direct link)
        // Fallback to tour_id + start_date for backward compatibility
        $bookings = $this->bookingModel->getAll([
            'tour_schedule_id' => $id,
            'status' => 'paid'
        ], 1, 1000);
        
        // If no bookings found by tour_schedule_id, try fallback method
        if (empty($bookings)) {
            $bookings = $this->bookingModel->getAll([
                'tour_id' => $schedule['tour_id'],
                'start_date' => $schedule['start_date'],
                'exact_date' => true,
                'status' => 'paid'
            ], 1, 1000);
        }

        // Extract passengers with emergency contact (primary customer's phone)
        $passengers = [];
        foreach ($bookings as $b) {
            $p_list = $this->bookingModel->getPassengers($b['id']);
            // Get primary customer's phone as emergency contact
            $primary_customer_phone = $b['customer_phone'] ?? null;
            foreach ($p_list as $p) {
                $p['booking_code'] = $b['booking_code'];
                $p['emergency_contact'] = $primary_customer_phone; // SĐT khẩn cấp = SĐT của người đặt tour
                $passengers[] = $p;
            }
        }

        // Get itinerary day services (dịch vụ theo ngày - template)
        require_once MODELS_PATH . '/ItineraryDayService.php';
        $dayServiceModel = new \ItineraryDayService($this->db);
        $dayServices = $dayServiceModel->getByTourId($schedule['tour_id']);
        
        // Group services by day
        $servicesByDay = [];
        foreach ($dayServices as $service) {
            $day = $service['day_number'] ?? 1;
            if (!isset($servicesByDay[$day])) {
                $servicesByDay[$day] = [];
            }
            $servicesByDay[$day][] = $service;
        }

        // Get booking services (dịch vụ đã đặt thực tế cho các booking trong schedule)
        require_once MODELS_PATH . '/BookingService.php';
        $bookingServiceModel = new \BookingService($this->db);
        $bookingServices = $bookingServiceModel->getByScheduleId($id);
        
        // Group booking services by service_date or by service_type
        $bookingServicesByDate = [];
        $bookingServicesByType = [];
        foreach ($bookingServices as $bs) {
            // Group by date
            $service_date = $bs['service_date'] ?? $schedule['start_date'];
            if (!isset($bookingServicesByDate[$service_date])) {
                $bookingServicesByDate[$service_date] = [];
            }
            $bookingServicesByDate[$service_date][] = $bs;
            
            // Group by type
            $service_type = $bs['service_type_name'] ?? 'Khác';
            if (!isset($bookingServicesByType[$service_type])) {
                $bookingServicesByType[$service_type] = [];
            }
            $bookingServicesByType[$service_type][] = $bs;
        }

        $page_title = 'Chi tiết Tour: ' . $tour['tour_code'];
        $content_file = VIEWS_PATH . '/guide/tours/show.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }
}
