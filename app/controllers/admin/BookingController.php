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
                // 0. Handle Customer (New vs Existing)
                $customer_id = $_POST['customer_id'] ?? null;
                if (($_POST['customer_mode'] ?? 'existing') === 'new') {
                    // Validate New Customer
                    if (empty($_POST['new_customer_name']) || empty($_POST['new_customer_phone'])) {
                        throw new Exception("Vui lòng nhập tên và số điện thoại khách hàng mới.");
                    }

                    $newCustomerData = [
                        'full_name' => $_POST['new_customer_name'],
                        'phone' => $_POST['new_customer_phone'],
                        'email' => $_POST['new_customer_email'] ?? null,
                        'address' => $_POST['new_customer_address'] ?? null,
                        'created_by' => $_SESSION['user_id'] ?? 1
                    ];

                    $customer_id = $this->customerModel->create($newCustomerData);
                }

                if (empty($customer_id)) {
                    throw new Exception("Vui lòng chọn hoặc tạo khách hàng.");
                }

                // 1. Validate start_date >= today
                $today = date('Y-m-d');
                if ($_POST['start_date'] < $today) {
                    throw new Exception("Ngày khởi hành phải từ hôm nay trở đi");
                }

                // 2. Validate adult_count >= 1
                $adult = (int) ($_POST['adult_count'] ?? 1);
                $child = (int) ($_POST['child_count'] ?? 0);
                $infant = (int) ($_POST['infant_count'] ?? 0);

                if ($adult < 1) {
                    throw new Exception("Phải có ít nhất 1 người lớn");
                }

                // 3. Validate passenger info
                // Logic: Tổng người = Khách hàng đại diện (1) + Số người nhập thêm
                $total_count = $adult + $child + $infant;
                $passenger_count = !empty($_POST['passenger_names']) ? count($_POST['passenger_names']) : 0;
                $expected_passenger_count = $total_count - 1; // Trừ 1 vì đã có khách hàng đại diện

                // Nếu chỉ có 1 người (chính là đại diện), không cần nhập thêm
                if ($total_count > 1 && $passenger_count != $expected_passenger_count) {
                    throw new Exception("Số hành khách cần nhập thêm: $expected_passenger_count người (Tổng $total_count - 1 đại diện). Bạn đã nhập: $passenger_count người");
                }

                // 4. Get Tour Info & Validate
                $tour = $this->tourModel->findById($_POST['tour_id']);
                if (!$tour) {
                    throw new Exception("Tour không tồn tại.");
                }

                // Validate tour status
                if ($tour['status'] !== 'active') {
                    throw new Exception("Tour không đang hoạt động. Không thể tạo booking.");
                }

                // Validate tour approval (nếu cần)
                if (isset($tour['approval_status']) && $tour['approval_status'] !== 'approved') {
                    throw new Exception("Tour chưa được duyệt. Không thể tạo booking.");
                }

                // Participant counts
                $totalParticipants = $adult + $child + $infant;

                // 4. Handle Schedule & Pricing based on Tour Type
                require_once 'app/models/TourSchedule.php';
                $scheduleModel = new TourSchedule($this->pdo);
                $tour_type = $tour['tour_type'] ?? 'public';
                $schedule = null;
                $schedule_id = null;

                if ($tour_type == 'public') {
                    // PUBLIC TOUR: Bắt buộc phải có schedule
                    $schedule = $scheduleModel->getByTourAndStartDate($_POST['tour_id'], $_POST['start_date']);
                    if (!$schedule) {
                        throw new Exception("Tour công khai cần có lịch khởi hành. Vui lòng tạo schedule trước khi đặt tour.");
                    }
                    if ($schedule['status'] !== 'open') {
                        throw new Exception("Lịch không khả dụng để đặt (Status: {$schedule['status']}).");
                    }

                    // Quota check (chặt chẽ cho public tour)
                    $available = $schedule['quota'] - $schedule['booked'];
                    if ($totalParticipants > $available) {
                        throw new Exception("Số lượng người tham gia vượt quá khả dụng (Còn $available chỗ).");
                    }

                    $schedule_id = $schedule['id'];
                    // Pricing: ưu tiên schedule, fallback tour
                    $priceAdult = $schedule['adult_price'] ?? $tour['adult_price'];
                    $priceChild = $schedule['child_price'] ?? $tour['child_price'];
                    $priceInfant = $schedule['infant_price'] ?? $tour['infant_price'];

                } else if ($tour_type == 'custom') {
                    // CUSTOM TOUR: Có thể tự động tạo schedule hoặc dùng schedule có sẵn
                    $schedule = $scheduleModel->getByTourAndStartDate($_POST['tour_id'], $_POST['start_date']);

                    if (!$schedule) {
                        // Tự động tạo schedule cho custom tour
                        $end_date = date('Y-m-d', strtotime($_POST['start_date'] . " + {$tour['duration_days']} days"));

                        $scheduleData = [
                            'tour_id' => $_POST['tour_id'],
                            'start_date' => $_POST['start_date'],
                            'end_date' => $end_date,
                            'quota' => $totalParticipants, // Quota = số người booking (linh hoạt)
                            'adult_price' => $tour['adult_price'],
                            'child_price' => $tour['child_price'],
                            'infant_price' => $tour['infant_price']
                        ];

                        if ($scheduleModel->create($scheduleData)) {
                            // Get the created schedule
                            $schedule = $scheduleModel->getByTourAndStartDate($_POST['tour_id'], $_POST['start_date']);
                            $schedule_id = $schedule['id'];
                        } else {
                            throw new Exception("Không thể tạo lịch khởi hành cho tour tùy chỉnh.");
                        }
                    } else {
                        $schedule_id = $schedule['id'];
                        // Quota linh hoạt hơn cho custom tour (có thể tăng)
                        // Nếu cần, có thể tự động tăng quota
                        if ($totalParticipants > ($schedule['quota'] - $schedule['booked'])) {
                            // Tự động tăng quota cho custom tour
                            $new_quota = $schedule['booked'] + $totalParticipants;
                            $stmt = $this->pdo->prepare("UPDATE tour_schedules SET quota = :quota WHERE id = :id");
                            $stmt->execute(['quota' => $new_quota, 'id' => $schedule_id]);
                            $schedule['quota'] = $new_quota;
                        }
                    }

                    // Pricing: có thể thay đổi theo booking (hiện tại dùng giá tour hoặc schedule)
                    $priceAdult = $schedule['adult_price'] ?? $tour['adult_price'];
                    $priceChild = $schedule['child_price'] ?? $tour['child_price'];
                    $priceInfant = $schedule['infant_price'] ?? $tour['infant_price'];
                } else {
                    throw new Exception("Loại tour không hợp lệ.");
                }

                $total_amount = $priceAdult * $adult + $priceChild * $child + $priceInfant * $infant;
                $discount = (int) ($_POST['discount_amount'] ?? 0);

                $total_amount = $priceAdult * $adult + $priceChild * $child + $priceInfant * $infant;
                $discount = (int) ($_POST['discount_amount'] ?? 0);

                // 5. Validate discount <= total_amount
                if ($discount > $total_amount) {
                    throw new Exception("Số tiền giảm giá ($discount) không được lớn hơn tổng tiền ($total_amount)");
                }

                $final_amount = max(0, $total_amount - $discount);
                $deposit = (int) ($_POST['deposit_amount'] ?? 0);

                // 6. Validate deposit <= final_amount
                if ($deposit > $final_amount) {
                    throw new Exception("Tiền cọc ($deposit) không được lớn hơn số tiền sau giảm ($final_amount)");
                }

                $remaining = max(0, $final_amount - $deposit);

                // 7. Check duplicate booking (same customer, tour, date)
                $duplicate = $this->bookingModel->checkDuplicate(
                    $customer_id,
                    $_POST['tour_id'],
                    $_POST['start_date']
                );
                if ($duplicate) {
                    throw new Exception("Khách hàng đã có booking cho tour này vào ngày này (Booking #{$duplicate['booking_code']})");
                }

                // Get end_date from schedule
                $end_date = $schedule['end_date'] ?? date('Y-m-d', strtotime($_POST['start_date'] . " + {$tour['duration_days']} days"));

                $data = [
                    'tour_id' => $_POST['tour_id'],
                    'customer_id' => $customer_id,
                    // Note: tour_schedule_id removed - database doesn't have this column
                    // Using tour_id + start_date to track which schedule this booking belongs to
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
                    'created_by' => $_SESSION['user_id'] ?? 1
                ];

                // Passengers handling
                $passengers = [];

                // 1. Add Main Customer as Primary Representative (Khách hàng đại diện)
                $passengers[] = [
                    'customer_id' => $customer_id,
                    'age_type' => 'adult', // Default
                    'is_primary' => 1
                ];

                // 2. Add other passengers from form (if any)
                if (!empty($_POST['passenger_names'])) {
                    foreach ($_POST['passenger_names'] as $index => $name) {
                        if (empty($name))
                            continue;

                        // Other passengers cannot be primary (already have one)
                        $p_phone = $_POST['passenger_phones'][$index] ?? ($_POST['new_customer_phone'] ?? '0000000000');

                        $passengerData = [
                            'full_name' => $name,
                            'phone' => $p_phone,
                            'date_of_birth' => $_POST['passenger_dobs'][$index] ?? null,
                            'gender' => $_POST['passenger_genders'][$index] ?? 'other',
                            'created_by' => $_SESSION['user_id'] ?? 1
                        ];

                        $p_id = $this->customerModel->create($passengerData);

                        $passengers[] = [
                            'customer_id' => $p_id,
                            'age_type' => $_POST['passenger_types'][$index] ?? 'adult',
                            'is_primary' => 0 // Không phải đại diện
                        ];
                    }
                }

                $this->bookingModel->create($data, $passengers);

                // Increment booked count on schedule
                $scheduleModel->incrementBooked($schedule_id, $totalParticipants);

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

        // Get History
        $history = $this->bookingModel->getHistory($id);

        // Get Payments
        require_once 'app/models/Payment.php';
        $paymentModel = new Payment($this->pdo);
        $payments = $paymentModel->getByBookingId($id);

        $page_title = 'Chi tiết Booking: ' . $booking['booking_code'];
        $content_file = 'app/views/admin/bookings/show.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    public function storePayment()
    {
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                require_once 'app/models/Payment.php';
                $paymentModel = new Payment($this->pdo);

                $bookingId = $_POST['booking_id'];
                $amount = (float) str_replace(',', '', $_POST['amount']); // Remove commas if any

                // Validate booking exists
                $booking = $this->bookingModel->getById($bookingId);
                if (!$booking) {
                    throw new Exception("Booking không tồn tại");
                }

                // Validate booking status
                if ($booking['approval_status'] == 'cancelled') {
                    throw new Exception("Không thể thanh toán cho booking đã hủy");
                }

                if ($amount <= 0) {
                    throw new Exception("Số tiền phải lớn hơn 0");
                }

                // Validate amount <= remaining_amount
                $remaining = (float) $booking['remaining_amount'];
                if ($amount > $remaining) {
                    throw new Exception("Số tiền thanh toán (" . number_format($amount) . ") vượt quá số tiền còn lại (" . number_format($remaining) . ")");
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
                    'created_by' => $_SESSION['user_id'] ?? 1
                ];

                $paymentModel->create($data);

                // Update Booking Status
                $this->bookingModel->updatePaymentStatus($bookingId);

                // Log History
                $this->bookingModel->logHistory($bookingId, 'payment', 'payment', $_SESSION['user_id'], "Thêm thanh toán: " . number_format($amount));

                set_success("Đã thêm thanh toán thành công!");
                redirect("?act=admin&module=bookings&action=show&id=$bookingId");

            } catch (Exception $e) {
                set_error($e->getMessage());
                redirect("?act=admin&module=bookings&action=show&id=" . $_POST['booking_id']);
            }
        }
    }

    public function changeStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $action = $_POST['action'];
            $userId = $_SESSION['user_id'] ?? 1;

            try {
                // Validate booking exists
                $booking = $this->bookingModel->getById($id);
                if (!$booking) {
                    throw new Exception("Booking không tồn tại");
                }

                if ($action == 'approve') {
                    // Check if booking is pending
                    if ($booking['approval_status'] !== 'pending') {
                        throw new Exception("Chỉ có thể duyệt booking đang chờ duyệt");
                    }
                    
                    // Check quota availability before approving
                    require_once 'app/models/TourSchedule.php';
                    $scheduleModel = new TourSchedule($this->pdo);
                    $schedule = $scheduleModel->getByTourAndDateRange(
                        $booking['tour_id'], 
                        $booking['start_date'], 
                        $booking['end_date']
                    );
                    
                    if ($schedule) {
                        $totalParticipants = $booking['adult_count'] + $booking['child_count'] + $booking['infant_count'];
                        $available = $schedule['quota'] - $schedule['booked'] + $totalParticipants; // +participants vì đã bị trừ khi tạo
                        
                        if ($totalParticipants > $available) {
                            throw new Exception("Không đủ chỗ trống để duyệt booking này (Còn $available chỗ)");
                        }
                    }
                    
                    $this->bookingModel->updateStatus($id, 'approved', 'approval', $userId);
                    $this->bookingModel->logHistory($id, 'pending', 'approved', $userId, "Duyệt thủ công");
                    set_success("Đã duyệt Booking!");
                    
                } elseif ($action == 'reject') {
                    $reason = $_POST['reason'] ?? '';
                    if (empty($reason)) {
                        throw new Exception("Vui lòng nhập lý do từ chối");
                    }
                    
                    // Use new reject method that returns quota
                    $this->bookingModel->reject($id, $reason, $userId);
                    set_success("Đã từ chối Booking và trả lại chỗ trống!");
                    
                } elseif ($action == 'cancel') {
                    $reason = $_POST['reason'] ?? '';
                    if (empty($reason)) {
                        throw new Exception("Vui lòng nhập lý do hủy");
                    }
                    
                    // Use cancel method that calculates fee and returns quota
                    $result = $this->bookingModel->cancel($id, $reason, $userId);
                    
                    $message = "Đã hủy Booking!";
                    if ($result['fee'] > 0) {
                        $message .= " Phí hủy: " . number_format($result['fee']) . " VNĐ (" . $result['policy'] . ")";
                    }
                    if ($result['refund'] > 0) {
                        $message .= " - Hoàn trả: " . number_format($result['refund']) . " VNĐ";
                    }
                    set_success($message);
                }

                redirect("?act=admin&module=bookings&action=show&id=$id");

            } catch (Exception $e) {
                set_error("Lỗi: " . $e->getMessage());
                redirect("?act=admin&module=bookings&action=show&id=$id");
            }
        }
    }
}
