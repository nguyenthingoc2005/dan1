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
            'guide_id' => $user_id,
            'start_date' => date('Y-m-d') // Default: Upcoming
        ];

        // Allow filtering by status/date if needed
        if (isset($_GET['status']) && $_GET['status'] == 'history') {
            // Logic for history could be start_date < today
            // For now, let's keep it simple
        }

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

        $bookings = $this->bookingModel->getAll([
            'tour_id' => $schedule['tour_id'],
            'start_date' => $schedule['start_date'],
            'status' => 'approved' // Only approved bookings
        ], 1, 1000); // getAll returns array directly, not ['data']

        // Extract passengers
        $passengers = [];
        foreach ($bookings as $b) {
            // Get passengers for each booking
            // Ideally Booking model should have getPassengers($booking_id)
            // For now, let's assume we can get them or just list the bookers
            // Optimization: Add getPassengersBySchedule to BookingModel later
            // For MVP: List bookings is enough, or fetch passengers individually
            $p_list = $this->bookingModel->getPassengers($b['id']);
            foreach ($p_list as $p) {
                $p['booking_code'] = $b['booking_code'];
                $passengers[] = $p;
            }
        }

        $page_title = 'Chi tiết Tour: ' . $tour['tour_code'];
        $content_file = VIEWS_PATH . '/guide/tours/show.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }
}
