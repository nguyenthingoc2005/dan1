<?php
require_once 'app/models/Booking.php';
require_once 'app/models/Tour.php';
require_once 'app/models/Customer.php';

class BookingController
{
    private $bookingModel;
    private $tourModel;
    private $customerModel;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->bookingModel = new Booking($pdo);
        $this->tourModel = new Tour($pdo);
        $this->customerModel = new Customer($pdo);
    }

    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'tour_id' => $_GET['tour_id'] ?? '',
            'start_date' => $_GET['start_date'] ?? ''
        ];

        $bookings = $this->bookingModel->getAll($filters, $page, $limit);
        $total_records = $this->bookingModel->count($filters);
        $total_pages = ceil($total_records / $limit);

        // Get active tours for filter
        $tours = $this->tourModel->getAll(['status' => 'active']);

        $page_title = 'Quản lý Đặt Tour';
        $content_file = 'app/views/admin/bookings/index.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    public function create()
    {
        // Get active tours for selection
        $tours = $this->tourModel->getAll(['status' => 'active'])['data'];
        // Get customers
        $customers = $this->customerModel->getAll();

        // Get open schedules
        require_once 'app/models/TourSchedule.php';
        $scheduleModel = new TourSchedule($this->pdo);
        $schedules = $scheduleModel->getAll(['status' => 'open'], 1, 1000); // Get all open schedules

        $page_title = 'Tạo Booking Mới';
        $content_file = 'app/views/admin/bookings/create.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    public function store()
    {
        // Only admin can create booking
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Basic Validation
                if (empty($_POST['tour_id']) || empty($_POST['customer_id']) || empty($_POST['start_date'])) {
                    throw new Exception("Vui lòng nhập đầy đủ thông tin bắt buộc.");
                }

                // Load schedule for selected start date
                require_once 'app/models/TourSchedule.php';
                $scheduleModel = new TourSchedule($this->pdo);
                $schedule = $scheduleModel->getByTourAndStartDate($_POST['tour_id'], $_POST['start_date']);
                if (!$schedule) {
                    throw new Exception("Lịch khởi hành không tồn tại.");
                }
                if ($schedule['status'] !== 'open') {
                    throw new Exception("Lịch không khả dụng để đặt.");
                }

                // Participant counts
                $adult = (int) ($_POST['adult_count'] ?? 1);
                $child = (int) ($_POST['child_count'] ?? 0);
                $infant = (int) ($_POST['infant_count'] ?? 0);
                $totalParticipants = $adult + $child + $infant;

                // Quota check
                $available = $schedule['quota'] - $schedule['booked'];
                if ($totalParticipants > $available) {
                    throw new Exception("Số lượng người tham gia vượt quá khả dụng (Còn $available chỗ).");
                }

                // Pricing (fallback to tour prices)
                $tour = $this->tourModel->getById($_POST['tour_id']);
                $priceAdult = $schedule['adult_price'] ?? $tour['adult_price'];
                $priceChild = $schedule['child_price'] ?? $tour['child_price'];
                $priceInfant = $schedule['infant_price'] ?? $tour['infant_price'];

                $total_amount = $priceAdult * $adult + $priceChild * $child + $priceInfant * $infant;
                $discount = (int) ($_POST['discount_amount'] ?? 0);
                $final_amount = max(0, $total_amount - $discount);
                $deposit = (int) ($_POST['deposit_amount'] ?? 0);
                $remaining = max(0, $final_amount - $deposit);

                $data = [
                    'tour_id' => $_POST['tour_id'],
                    'customer_id' => $_POST['customer_id'],
                    'tour_schedule_id' => $schedule['id'],
                    'adult_count' => $adult,
                    'child_count' => $child,
                    'infant_count' => $infant,
                    'start_date' => $schedule['start_date'],
                    'end_date' => $schedule['end_date'],
                    'total_amount' => $total_amount,
                    'discount_amount' => $discount,
                    'final_amount' => $final_amount,
                    'deposit_amount' => $deposit,
                    'remaining_amount' => $remaining,
                    'payment_status' => $_POST['payment_status'] ?? 'unpaid',
                    'notes' => $_POST['notes'] ?? '',
                    'created_by' => $_SESSION['user_id'] ?? 1
                ];

                // Passengers handling would go here
                $passengers = [];

                $this->bookingModel->create($data, $passengers);

                // Increment booked count on schedule
                $scheduleModel->incrementBooked($schedule['id'], $totalParticipants);

                set_success("Tạo Booking thành công!");
                redirect('?act=admin&module=bookings');
            } catch (Exception $e) {
                set_error($e->getMessage());
                $_SESSION['old'] = $_POST;
                redirect('?act=admin&module=bookings&action=create');
            }
        }
    }

    public function show()
    {
        $id = $_GET['id'] ?? null;
        if (!$id)
            redirect('?act=admin&module=bookings');

        $booking = $this->bookingModel->getById($id);
        if (!$booking) {
            set_error("Không tìm thấy Booking.");
            redirect('?act=admin&module=bookings');
        }

        $page_title = 'Chi tiết Booking: ' . $booking['booking_code'];
        $content_file = 'app/views/admin/bookings/show.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    public function changeStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $action = $_POST['action'];
            $userId = $_SESSION['user_id'] ?? 1;

            try {
                if ($action == 'approve') {
                    $this->bookingModel->updateStatus($id, 'approved', 'approval', $userId);
                    set_success("Đã duyệt Booking!");
                } elseif ($action == 'reject') {
                    $reason = $_POST['reason'] ?? '';
                    $this->bookingModel->updateStatus($id, 'rejected', 'approval', $userId, $reason);
                    set_success("Đã từ chối Booking!");
                } elseif ($action == 'cancel') {
                    $reason = $_POST['reason'] ?? '';
                    $this->bookingModel->updateStatus($id, 'cancelled', 'approval', $userId, $reason);
                    set_success("Đã hủy Booking!");
                }

                redirect("?act=admin&module=bookings&action=show&id=$id");

            } catch (Exception $e) {
                set_error("Lỗi: " . $e->getMessage());
                redirect("?act=admin&module=bookings&action=show&id=$id");
            }
        }
    }
}
