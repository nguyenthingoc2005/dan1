<?php
namespace Staff;

/**
 * ==============================================================================
 * BOOKING CONTROLLER (STAFF)
 * ==============================================================================
 * 
 * Staff chỉ quản lý Bookings do mình tạo
 * 
 * Key Differences từ Admin:
 * - Filter: created_by = current_user_id
 * - Có thể tạo booking, ghi nhận thanh toán
 * - KHÔNG CÓ changeStatus (approve/reject)
 * 
 * @version 1.0
 * @date 2024-12-03
 * ==============================================================================
 */

class BookingController
{
    private $bookingModel;
    private $tourModel;
    private $customerModel;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        require_once MODELS_PATH . '/Booking.php';
        require_once MODELS_PATH . '/Tour.php';
        require_once MODELS_PATH . '/Customer.php';

        $this->bookingModel = new \Booking($pdo);
        $this->tourModel = new \Tour($pdo);
        $this->customerModel = new \Customer($pdo);
    }

    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'tour_id' => $_GET['tour_id'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'created_by' => get_user_id() // KEY: Chỉ bookings của mình
        ];

        $bookings = $this->bookingModel->getAll($filters, $page, $limit);
        $total_records = $this->bookingModel->count($filters);
        $total_pages = ceil($total_records / $limit);

        // Get active tours for filter (all tours, không chỉ của mình)
        $tours = $this->tourModel->getAll(['status' => 'active']);

        $page_title = 'Bookings Của Tôi';
        $content_file = VIEWS_PATH . '/staff/bookings/index.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    public function create()
    {
        // Get active approved tours
        $tours = $this->tourModel->getAll(['status' => 'active', 'approval_status' => 'approved'])['data'];

        // Get customers
        $customers = $this->customerModel->getAll([], 1, 1000)['data'];

        // Get open schedules
        require_once MODELS_PATH . '/TourSchedule.php';
        $scheduleModel = new \TourSchedule($this->pdo);
        $schedules = $scheduleModel->getAll(['status' => 'open'], 1, 1000)['data'];

        $page_title = 'Tạo Booking Mới';
        $content_file = VIEWS_PATH . '/staff/bookings/create.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Handle Customer (New vs Existing)
                $customer_id = $_POST['customer_id'] ?? null;
                if (($_POST['customer_mode'] ?? 'existing') === 'new') {
                    if (empty($_POST['new_customer_name']) || empty($_POST['new_customer_phone'])) {
                        throw new \Exception("Vui lòng nhập tên và số điện thoại khách hàng mới.");
                    }

                    $newCustomerData = [
                        'full_name' => $_POST['new_customer_name'],
                        'phone' => $_POST['new_customer_phone'],
                        'email' => $_POST['new_customer_email'] ?? null,
                        'address' => $_POST['new_customer_address'] ?? null,
                        'created_by' => get_user_id()
                    ];

                    $customer_id = $this->customerModel->create($newCustomerData);
                }

                if (empty($customer_id)) {
                    throw new \Exception("Vui lòng chọn hoặc tạo khách hàng.");
                }

                // Validate start_date >= today
                $today = date('Y-m-d');
                if ($_POST['start_date'] < $today) {
                    throw new \Exception("Ngày khởi hành phải từ hôm nay trở đi");
                }

                // Validate counts
                $adult = (int) ($_POST['adult_count'] ?? 1);
                $child = (int) ($_POST['child_count'] ?? 0);
                $infant = (int) ($_POST['infant_count'] ?? 0);

                if ($adult < 1) {
                    throw new \Exception("Phải có ít nhất 1 người lớn");
                }

                // Validate passenger info
                $total_count = $adult + $child + $infant;
                $passenger_count = !empty($_POST['passenger_names']) ? count($_POST['passenger_names']) : 0;
                $expected_passenger_count = $total_count - 1;

                if ($total_count > 1 && $passenger_count != $expected_passenger_count) {
                    throw new \Exception("Số hành khách cần nhập thêm: $expected_passenger_count người (Tổng $total_count - 1 đại diện). Bạn đã nhập: $passenger_count người");
                }

                // Get Tour Info & Validate
                $tour = $this->tourModel->findById($_POST['tour_id']);
                if (!$tour) {
                    throw new \Exception("Tour không tồn tại.");
                }

                if ($tour['status'] !== 'active') {
                    throw new \Exception("Tour không đang hoạt động. Không thể tạo booking.");
                }

                if (isset($tour['approval_status']) && $tour['approval_status'] !== 'approved') {
                    throw new \Exception("Tour chưa được duyệt. Không thể tạo booking.");
                }

                $totalParticipants = $adult + $child + $infant;

                // Handle Schedule & Pricing
                require_once MODELS_PATH . '/TourSchedule.php';
                $scheduleModel = new \TourSchedule($this->pdo);
                $tour_type = $tour['tour_type'] ?? 'public';
                $schedule_id = null;

                if ($tour_type == 'public') {
                    $schedule = $scheduleModel->getByTourAndStartDate($_POST['tour_id'], $_POST['start_date']);
                    if (!$schedule) {
                        throw new \Exception("Tour công khai cần có lịch khởi hành. Vui lòng tạo schedule trước.");
                    }
                    if ($schedule['status'] !== 'open') {
                        throw new \Exception("Lịch không khả dụng để đặt.");
                    }

                    $available = $schedule['quota'] - $schedule['booked'];
                    if ($totalParticipants > $available) {
                        throw new \Exception("Số lượng người vượt quá khả dụng (Còn $available chỗ).");
                    }

                    $schedule_id = $schedule['id'];
                    $priceAdult = $schedule['adult_price'] ?? $tour['adult_price'];
                    $priceChild = $schedule['child_price'] ?? $tour['child_price'];
                    $priceInfant = $schedule['infant_price'] ?? $tour['infant_price'];

                } else if ($tour_type == 'custom') {
                    $schedule = $scheduleModel->getByTourAndStartDate($_POST['tour_id'], $_POST['start_date']);

                    if (!$schedule) {
                        $end_date = date('Y-m-d', strtotime($_POST['start_date'] . " + {$tour['duration_days']} days"));

                        $scheduleData = [
                            'tour_id' => $_POST['tour_id'],
                            'start_date' => $_POST['start_date'],
                            'end_date' => $end_date,
                            'quota' => $totalParticipants,
                            'adult_price' => $tour['adult_price'],
                            'child_price' => $tour['child_price'],
                            'infant_price' => $tour['infant_price']
                        ];

                        if ($scheduleModel->create($scheduleData)) {
                            $schedule = $scheduleModel->getByTourAndStartDate($_POST['tour_id'], $_POST['start_date']);
                            $schedule_id = $schedule['id'];
                        } else {
                            throw new \Exception("Không thể tạo lịch khởi hành.");
                        }
                    } else {
                        $schedule_id = $schedule['id'];
                        if ($totalParticipants > ($schedule['quota'] - $schedule['booked'])) {
                            $new_quota = $schedule['booked'] + $totalParticipants;
                            $stmt = $this->pdo->prepare("UPDATE tour_schedules SET quota = :quota WHERE id = :id");
                            $stmt->execute(['quota' => $new_quota, 'id' => $schedule_id]);
                            $schedule['quota'] = $new_quota;
                        }
                    }

                    $priceAdult = $schedule['adult_price'] ?? $tour['adult_price'];
                    $priceChild = $schedule['child_price'] ?? $tour['child_price'];
                    $priceInfant = $schedule['infant_price'] ?? $tour['infant_price'];
                } else {
                    throw new \Exception("Loại tour không hợp lệ.");
                }

                $total_amount = $priceAdult * $adult + $priceChild * $child + $priceInfant * $infant;
                $discount = (int) ($_POST['discount_amount'] ?? 0);

                if ($discount > $total_amount) {
                    throw new \Exception("Số tiền giảm giá không được lớn hơn tổng tiền");
                }

                $final_amount = max(0, $total_amount - $discount);
                $deposit = (int) ($_POST['deposit_amount'] ?? 0);

                if ($deposit > $final_amount) {
                    throw new \Exception("Tiền cọc không được lớn hơn số tiền sau giảm");
                }

                $remaining = max(0, $final_amount - $deposit);

                // Check duplicate booking
                $duplicate = $this->bookingModel->checkDuplicate(
                    $customer_id,
                    $_POST['tour_id'],
                    $_POST['start_date']
                );
                if ($duplicate) {
                    throw new \Exception("Khách hàng đã có booking cho tour này vào ngày này");
                }

                $end_date = $schedule['end_date'] ?? date('Y-m-d', strtotime($_POST['start_date'] . " + {$tour['duration_days']} days"));

                $data = [
                    'tour_id' => $_POST['tour_id'],
                    'customer_id' => $customer_id,
                    'adult_count' => $adult,
                    'child_count' => $child,
                    'infant_count' => $infant,
                    'start_date' => $_POST['start_date'],
                    'end_date' => $end_date,
                    'total_amount' => $total_amount,
                    'discount_amount' => $discount,
                    'final_amount' => $final_amount,
                    'deposit_amount' => $deposit,
                    'remaining_amount' => $remaining,
                    'payment_status' => $_POST['payment_status'] ?? 'unpaid',
                    'notes' => $_POST['notes'] ?? '',
                    'created_by' => get_user_id() // KEY: Set created_by
                ];

                // Passengers handling
                $passengers = [];
                $passengers[] = [
                    'customer_id' => $customer_id,
                    'age_type' => 'adult',
                    'is_primary' => 1
                ];

                if (!empty($_POST['passenger_names'])) {
                    foreach ($_POST['passenger_names'] as $index => $name) {
                        if (empty($name))
                            continue;

                        $p_phone = $_POST['passenger_phones'][$index] ?? ($_POST['new_customer_phone'] ?? '0000000000');

                        $passengerData = [
                            'full_name' => $name,
                            'phone' => $p_phone,
                            'date_of_birth' => $_POST['passenger_dobs'][$index] ?? null,
                            'gender' => $_POST['passenger_genders'][$index] ?? 'other',
                            'created_by' => get_user_id()
                        ];

                        $p_id = $this->customerModel->create($passengerData);

                        $passengers[] = [
                            'customer_id' => $p_id,
                            'age_type' => $_POST['passenger_types'][$index] ?? 'adult',
                            'is_primary' => 0
                        ];
                    }
                }

                $this->bookingModel->create($data, $passengers);
                $scheduleModel->incrementBooked($schedule_id, $totalParticipants);

                set_success("Tạo Booking thành công!");
                redirect('?act=staff-bookings');
            } catch (\Exception $e) {
                set_error($e->getMessage());
                $_SESSION['old'] = $_POST;
                redirect('?act=staff-bookings&action=create');
            }
        }
    }

    public function show()
    {
        $id = $_GET['id'] ?? null;
        if (!$id)
            redirect('?act=staff-bookings');

        $booking = $this->bookingModel->getById($id);
        if (!$booking) {
            set_error("Không tìm thấy Booking.");
            redirect('?act=staff-bookings');
        }

        // CHECK OWNERSHIP
        if ($booking['created_by'] != get_user_id()) {
            set_error("Bạn không có quyền xem booking này.");
            redirect('?act=staff-bookings');
        }

        // Get History
        $history = $this->bookingModel->getHistory($id);

        // Get Payments
        require_once MODELS_PATH . '/Payment.php';
        $paymentModel = new \Payment($this->pdo);
        $payments = $paymentModel->getByBookingId($id);

        $page_title = 'Chi tiết Booking: ' . $booking['booking_code'];
        $content_file = VIEWS_PATH . '/staff/bookings/show.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    public function storePayment()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                require_once MODELS_PATH . '/Payment.php';
                $paymentModel = new \Payment($this->pdo);

                $bookingId = $_POST['booking_id'];
                $amount = (float) str_replace(',', '', $_POST['amount']);

                $booking = $this->bookingModel->getById($bookingId);
                if (!$booking) {
                    throw new \Exception("Booking không tồn tại");
                }

                // CHECK OWNERSHIP
                if ($booking['created_by'] != get_user_id()) {
                    throw new \Exception("Bạn không có quyền thao tác booking này");
                }

                if ($booking['approval_status'] == 'cancelled') {
                    throw new \Exception("Không thể thanh toán cho booking đã hủy");
                }

                if ($amount <= 0) {
                    throw new \Exception("Số tiền phải lớn hơn 0");
                }

                $remaining = (float) $booking['remaining_amount'];
                if ($amount > $remaining) {
                    throw new \Exception("Số tiền thanh toán vượt quá số tiền còn lại");
                }

                $data = [
                    'booking_id' => $bookingId,
                    'payment_method' => $_POST['payment_method'],
                    'amount' => $amount,
                    'payment_type' => $_POST['payment_type'],
                    'transaction_id' => $_POST['transaction_id'] ?? null,
                    'receipt_number' => $_POST['receipt_number'] ?? null,
                    'payment_date' => $_POST['payment_date'] ?? date('Y-m-d'),
                    'notes' => $_POST['notes'] ?? '',
                    'created_by' => get_user_id()
                ];

                $paymentModel->create($data);
                $this->bookingModel->updatePaymentStatus($bookingId);
                $this->bookingModel->logHistory($bookingId, 'payment', 'payment', get_user_id(), "Thêm thanh toán: " . number_format($amount));

                set_success("Đã thêm thanh toán thành công!");
                redirect("?act=staff-bookings&action=show&id=$bookingId");

            } catch (\Exception $e) {
                set_error($e->getMessage());
                redirect("?act=staff-bookings&action=show&id=" . $_POST['booking_id']);
            }
        }
    }
}
