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

        // Get active tab from URL
        $active_tab = $_GET['tab'] ?? 'tour-info';
        $allowed_tabs = ['tour-info', 'checkin', 'expenses', 'journals', 'services', 'passengers', 'rooms', 'vehicles'];
        if (!in_array($active_tab, $allowed_tabs)) {
            $active_tab = 'tour-info';
        }

        // Get Tour Details (always needed)
        require_once MODELS_PATH . '/Tour.php';
        $tourModel = new \Tour($this->db);
        $tour = $tourModel->findById($schedule['tour_id']);

        // Initialize variables
        $bookings = [];
        $passengers = [];
        $dayServices = [];
        $servicesByDay = [];
        $bookingServices = [];
        $bookingServicesByDate = [];
        $bookingServicesByType = [];
        $expenses = [];
        $expense_total = 0;
        $journals = [];
        $checkin_passengers = [];
        $checkin_stats = [];
        $room_assignments = [];
        $vehicle_assignments = [];

        // Get bookings (needed for passengers, checkin, services)
        if (in_array($active_tab, ['passengers', 'checkin', 'services'])) {
            // Get bookings for this schedule using tour_schedule_id (direct link)
            // Include both 'partial' and 'paid' status, not just 'paid'
            // Fallback to tour_id + start_date for backward compatibility
            $bookingsBySchedule = $this->bookingModel->getAll([
                'tour_schedule_id' => $id
            ], 1, 1000);

            // Filter by payment status: include partial and paid
            foreach ($bookingsBySchedule as $booking) {
                if (in_array($booking['payment_status'], ['partial', 'paid'])) {
                    $bookings[] = $booking;
                }
            }

            // If no bookings found by tour_schedule_id, try fallback method
            if (empty($bookings)) {
                $bookingsByDate = $this->bookingModel->getAll([
                    'tour_id' => $schedule['tour_id'],
                    'start_date' => $schedule['start_date'],
                    'exact_date' => true
                ], 1, 1000);

                // Filter by payment status
                foreach ($bookingsByDate as $booking) {
                    if (in_array($booking['payment_status'], ['partial', 'paid'])) {
                        $bookings[] = $booking;
                    }
                }
            }
        }

        // Extract passengers (only for passengers tab)
        if ($active_tab === 'passengers' && !empty($bookings)) {
            foreach ($bookings as $b) {
                try {
                    $p_list = $this->bookingModel->getPassengers($b['id']);
                    // Get primary customer's phone as emergency contact
                    $primary_customer_phone = $b['customer_phone'] ?? null;
                    foreach ($p_list as $p) {
                        $p['booking_code'] = $b['booking_code'];
                        $p['emergency_contact'] = $primary_customer_phone; // SĐT khẩn cấp = SĐT của người đặt tour
                        $passengers[] = $p;
                    }
                } catch (\Exception $e) {
                    error_log("TourController::show() - Error loading passengers for booking {$b['id']}: " . $e->getMessage());
                    continue;
                }
            }
        }

        // Get itinerary day services (only for services tab)
        if ($active_tab === 'services') {
            require_once MODELS_PATH . '/ItineraryDayService.php';
            $dayServiceModel = new \ItineraryDayService($this->db);
            $dayServices = $dayServiceModel->getByTourId($schedule['tour_id']);

            // Group services by day
            foreach ($dayServices as $service) {
                $day = $service['day_number'] ?? 1;
                if (!isset($servicesByDay[$day])) {
                    $servicesByDay[$day] = [];
                }
                $servicesByDay[$day][] = $service;
            }
        }

        // Get booking services (only for services tab)
        if ($active_tab === 'services') {
            require_once MODELS_PATH . '/BookingService.php';
            $bookingServiceModel = new \BookingService($this->db);
            $bookingServices = $bookingServiceModel->getByScheduleId($id);

            // Debug: Log booking services count
            if (empty($bookingServices)) {
                error_log("TourController::show() - No booking services found for schedule_id: $id");
            }

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
        }

        // Get expenses (only for expenses tab)
        if ($active_tab === 'expenses') {
            require_once MODELS_PATH . '/IncurredExpense.php';
            $expenseModel = new \IncurredExpense($this->db);
            $expenses = $expenseModel->getByScheduleId($id);
            $expense_total = $expenseModel->getTotalByScheduleId($id);
        }

        // Debug: Log expenses count
        if (empty($expenses)) {
            error_log("TourController::show() - No expenses found for schedule_id: $id");
        }

        // Get journals (only for journals tab)
        if ($active_tab === 'journals') {
            require_once MODELS_PATH . '/Journal.php';
            $journalModel = new \Journal($this->db);
            $journals = $journalModel->getAll(['tour_schedule_id' => $id], 1, 100);
            // Get images for each journal
            foreach ($journals as &$journal) {
                $journal['images'] = $journalModel->getImages($journal['id']);
            }
        }

        // Debug: Log journals count
        if (empty($journals)) {
            error_log("TourController::show() - No journals found for schedule_id: $id");
        }

        // Get check-in data (only for checkin tab)
        $can_checkin = ($schedule['start_date'] <= date('Y-m-d'));
        if ($active_tab === 'checkin') {
            require_once MODELS_PATH . '/Checkin.php';
            $checkinModel = new \Checkin($this->db);

            // Get passengers with check-in status
            // Relaxed filter: include all bookings with partial or paid status
            foreach ($bookings as $booking) {
                try {
                    $p_list = $this->bookingModel->getPassengers($booking['id']);
                    foreach ($p_list as $p) {
                        // Chỉ thêm passenger nếu có customer_id hợp lệ
                        if (!empty($p['customer_id'])) {
                            // Validate customer_id tồn tại trong bảng customers
                            $stmt = $this->db->prepare("SELECT id FROM customers WHERE id = :id");
                            $stmt->execute(['id' => $p['customer_id']]);
                            if ($stmt->fetch()) {
                                $checkin = $checkinModel->getCustomerCheckin($booking['id'], $p['customer_id']);
                                $p['booking_id'] = $booking['id'];
                                $p['booking_code'] = $booking['booking_code'];
                                $p['checkin_status'] = $checkin ? $checkin['status'] : null;
                                $p['checkin_time'] = $checkin ? $checkin['checkin_time'] : null;
                                $p['checkin_notes'] = $checkin ? $checkin['notes'] : null;
                                $checkin_passengers[] = $p;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    error_log("TourController::show() - Error loading passengers for booking {$booking['id']}: " . $e->getMessage());
                    continue;
                }
            }

            // Get check-in stats
            $checkin_stats = $checkinModel->getStatsBySchedule($id);
        }

        // Get room assignments (only for rooms tab)
        if ($active_tab === 'rooms') {
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
                error_log("TourController::show() - Error loading room assignments for schedule_id $id: " . $e->getMessage());
                $room_assignments = [];
            }
        }

        // Get vehicle assignments (only for vehicles tab)
        if ($active_tab === 'vehicles') {
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
                error_log("TourController::show() - Error loading vehicle assignments for schedule_id $id: " . $e->getMessage());
                $vehicle_assignments = [];
            }
        }

        // Check if tour has started
        $today = date('Y-m-d');
        $can_add_expense = ($schedule['start_date'] <= $today);
        $can_add_journal = ($schedule['start_date'] <= $today);

        // Pass active_tab to view
        $page_title = 'Chi tiết Tour: ' . $tour['tour_code'];
        $content_file = VIEWS_PATH . '/guide/tours/show.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }
}
