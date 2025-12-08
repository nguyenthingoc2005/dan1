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

        // Get expenses for this schedule
        require_once MODELS_PATH . '/IncurredExpense.php';
        $expenseModel = new \IncurredExpense($this->db);
        $expenses = $expenseModel->getByScheduleId($id);
        $expense_total = $expenseModel->getTotalByScheduleId($id);

        // Get journals for this schedule
        require_once MODELS_PATH . '/Journal.php';
        $journalModel = new \Journal($this->db);
        $journals = $journalModel->getAll(['tour_schedule_id' => $id], 1, 100);
        // Get images for each journal
        foreach ($journals as &$journal) {
            $journal['images'] = $journalModel->getImages($journal['id']);
        }

        // Get check-in data
        require_once MODELS_PATH . '/Checkin.php';
        $checkinModel = new \Checkin($this->db);
        
        // Get passengers with check-in status (chỉ lấy từ bookings đã thanh toán đủ)
        $checkin_passengers = [];
        $checkin_bookings = [];
        foreach ($bookings as $booking) {
            if (in_array($booking['payment_status'], ['partial', 'paid']) 
                && (float)$booking['remaining_amount'] == 0) {
                $checkin_bookings[] = $booking;
            }
        }

        foreach ($checkin_bookings as $booking) {
            $p_list = $this->bookingModel->getPassengers($booking['id']);
            foreach ($p_list as $p) {
                // Chỉ thêm passenger nếu có customer_id hợp lệ
                if (!empty($p['customer_id']) && !empty($p['id'])) {
                    $checkin = $checkinModel->getCustomerCheckin($booking['id'], $p['id']);
                    $p['booking_id'] = $booking['id'];
                    $p['booking_code'] = $booking['booking_code'];
                    $p['checkin_status'] = $checkin ? $checkin['status'] : null;
                    $p['checkin_time'] = $checkin ? $checkin['checkin_time'] : null;
                    $p['checkin_notes'] = $checkin ? $checkin['notes'] : null;
                    $checkin_passengers[] = $p;
                }
            }
        }

        // Get check-in stats
        $checkin_stats = $checkinModel->getStatsBySchedule($id);
        $can_checkin = ($schedule['start_date'] <= date('Y-m-d'));

        // Get room assignments (read-only) - chỉ query nếu bảng tồn tại
        $room_assignments = [];
        try {
            $room_assignments_sql = "SELECT 
                ra.id,
                ra.room_number,
                ra.room_type,
                ra.actual_occupancy,
                ra.max_capacity,
                i.day_number,
                sp.name AS hotel_name,
                GROUP_CONCAT(c.full_name ORDER BY c.full_name SEPARATOR ', ') AS customers
            FROM room_assignments ra
            JOIN itineraries i ON ra.itinerary_id = i.id
            LEFT JOIN service_providers sp ON ra.service_provider_id = sp.id
            LEFT JOIN room_assignment_customers rac ON ra.id = rac.room_assignment_id
            LEFT JOIN customers c ON rac.customer_id = c.id
            WHERE ra.tour_schedule_id = :schedule_id
              AND ra.status IN ('assigned', 'confirmed')
            GROUP BY ra.id
            ORDER BY i.day_number, ra.room_number";
            $stmt = $this->db->prepare($room_assignments_sql);
            $stmt->execute(['schedule_id' => $id]);
            $room_assignments = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Bảng chưa tồn tại, để mảng rỗng
            $room_assignments = [];
        }

        // Get vehicle assignments (read-only) - chỉ query nếu bảng tồn tại
        $vehicle_assignments = [];
        try {
            $vehicle_assignments_sql = "SELECT 
                va.id,
                v.vehicle_code,
                v.vehicle_type,
                v.license_plate,
                v.capacity,
                d.full_name AS driver_name,
                d.phone AS driver_phone,
                d.license_type,
                va.driver_salary,
                va.estimated_fuel_cost,
                va.status
            FROM vehicle_assignments va
            JOIN vehicles v ON va.vehicle_id = v.id
            JOIN drivers d ON va.driver_id = d.id
            WHERE va.tour_schedule_id = :schedule_id
              AND va.status != 'cancelled'";
            $stmt = $this->db->prepare($vehicle_assignments_sql);
            $stmt->execute(['schedule_id' => $id]);
            $vehicle_assignments = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Bảng chưa tồn tại, để mảng rỗng
            $vehicle_assignments = [];
        }

        // Check if tour has started
        $today = date('Y-m-d');
        $can_add_expense = ($schedule['start_date'] <= $today);
        $can_add_journal = ($schedule['start_date'] <= $today);

        $page_title = 'Chi tiết Tour: ' . $tour['tour_code'];
        $content_file = VIEWS_PATH . '/guide/tours/show.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }
}
