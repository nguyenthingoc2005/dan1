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
        $toursResult = $this->tourModel->getAll(['status' => 'active']);
        $tours = $toursResult['data'] ?? [];

        $page_title = 'Quản lý Đặt Tour';
        $content_file = 'app/views/admin/bookings/index.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    public function create()
    {
        // Get active tours for selection
        $toursResult = $this->tourModel->getAll(['status' => 'active']);
        $tours = $toursResult['data'] ?? [];

        // Get customers
        $customersResult = $this->customerModel->getAll([], 1, 1000); // Get all customers
        $customers = $customersResult['data'] ?? [];

        // Get open schedules
        require_once 'app/models/TourSchedule.php';
        $scheduleModel = new TourSchedule($this->pdo);
        $schedulesResult = $scheduleModel->getAll(['status' => 'open'], 1, 1000);
        $schedules = $schedulesResult['data'] ?? [];

        $page_title = 'Tạo Booking Mới';
        $content_file = 'app/views/admin/bookings/create.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    public function store()
    {
        // Only admin can create booking
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            require_csrf_token();
            
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

                // 1. Validate start_date >= today + booking_deadline_days (DEADLINE: Phải đặt trước deadline)
                $tour = $this->tourModel->findById($_POST['tour_id']);
                if (!$tour) {
                    throw new Exception("Tour không tồn tại.");
                }
                $deadline_days = (int) ($tour['booking_deadline_days'] ?? 1);
                $today = date('Y-m-d');
                $minStartDate = date('Y-m-d', strtotime($today . " +{$deadline_days} day"));
                if ($_POST['start_date'] < $minStartDate) {
                    throw new Exception("Không thể đặt booking. Phải đặt trước {$deadline_days} ngày so với ngày khởi hành. (Hôm nay: {$today}, Ngày khởi hành tối thiểu: {$minStartDate})");
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

                // Validate age_type của passengers khớp với adult/child/infant count
                if (!empty($_POST['passenger_types'])) {
                    $passenger_adult_count = 0;
                    $passenger_child_count = 0;
                    $passenger_infant_count = 0;
                    
                    foreach ($_POST['passenger_types'] as $age_type) {
                        if ($age_type === 'adult') $passenger_adult_count++;
                        elseif ($age_type === 'child') $passenger_child_count++;
                        elseif ($age_type === 'infant') $passenger_infant_count++;
                    }
                    
                    // Primary customer được tính là adult (mặc định)
                    // Nên tổng adult = primary (1) + passenger adults
                    $total_adult_expected = 1 + $passenger_adult_count;
                    $total_child_expected = $passenger_child_count;
                    $total_infant_expected = $passenger_infant_count;
                    
                    if ($total_adult_expected != $adult || $total_child_expected != $child || $total_infant_expected != $infant) {
                        throw new Exception("Số lượng loại hành khách không khớp. Đã nhập: $adult người lớn, $child trẻ em, $infant em bé. Nhưng trong danh sách có: $total_adult_expected người lớn, $total_child_expected trẻ em, $total_infant_expected em bé.");
                    }
                } else {
                    // Nếu không có passengers, chỉ có primary customer (mặc định là adult)
                    if ($adult != 1 || $child != 0 || $infant != 0) {
                        throw new Exception("Khi chỉ có 1 người (khách hàng đại diện), số lượng phải là: 1 người lớn, 0 trẻ em, 0 em bé.");
                    }
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

                        // Check max_participants before creating schedule
                        $maxParticipants = (int) ($tour['max_participants'] ?? 999);
                        if ($totalParticipants > $maxParticipants) {
                            throw new Exception("Số lượng người tham gia ($totalParticipants) vượt quá giới hạn tối đa của tour ($maxParticipants người).");
                        }

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
                        // Nhưng phải check max_participants của tour
                        if ($totalParticipants > ($schedule['quota'] - $schedule['booked'])) {
                            // Check max_participants before increasing quota
                            $maxParticipants = (int) ($tour['max_participants'] ?? 999);
                            $new_quota = $schedule['booked'] + $totalParticipants;
                            
                            if ($new_quota > $maxParticipants) {
                                throw new Exception("Số lượng người tham gia ($totalParticipants) vượt quá giới hạn tối đa của tour ($maxParticipants người).");
                            }
                            
                            // Tự động tăng quota cho custom tour (nhưng không vượt max_participants)
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

                // Validate end_date >= start_date
                if ($end_date < $_POST['start_date']) {
                    throw new Exception("Ngày kết thúc phải sau hoặc bằng ngày khởi hành");
                }

                // Validate total_amount > 0
                if ($total_amount <= 0) {
                    throw new Exception("Tổng tiền tour phải lớn hơn 0");
                }

                // Validate child_count and infant_count >= 0
                if ($child < 0 || $infant < 0) {
                    throw new Exception("Số lượng trẻ em và em bé không được âm");
                }

                // Start transaction to ensure atomicity
                $this->pdo->beginTransaction();
                
                try {
                $data = [
                    'tour_id' => $_POST['tour_id'],
                    'customer_id' => $customer_id,
                    'tour_schedule_id' => $schedule_id, // Set schedule_id
                    'adult_count' => $adult,
                    'child_count' => $child,
                    'infant_count' => $infant,
                    'start_date' => $_POST['start_date'],
                    'end_date' => $end_date,
                    'total_amount' => $total_amount,
                    'discount_code' => !empty($_POST['discount_code']) ? sanitize($_POST['discount_code']) : null,
                    'discount_amount' => $discount,
                    'final_amount' => $final_amount,
                    'deposit_amount' => $deposit,
                    'remaining_amount' => $remaining,
                    'payment_status' => $_POST['payment_status'] ?? 'unpaid',
                    'source' => !empty($_POST['source']) ? sanitize($_POST['source']) : null,
                    'special_requests' => !empty($_POST['special_requests']) ? sanitize($_POST['special_requests']) : null,
                    'notes' => !empty($_POST['notes']) ? sanitize($_POST['notes']) : null,
                    'internal_notes' => !empty($_POST['internal_notes']) ? sanitize($_POST['internal_notes']) : null,
                    'created_by' => $_SESSION['user_id'] ?? 1
                ];

                // Passengers handling
                $passengers = [];

                // 1. Add Main Customer as Primary Representative
                $passengers[] = [
                    'customer_id' => $customer_id,
                    'age_type' => 'adult', // Primary customer is always adult
                    'is_primary' => 1
                ];

                // 2. Add other passengers from form (if any)
                if (!empty($_POST['passenger_names'])) {
                    foreach ($_POST['passenger_names'] as $index => $name) {
                        if (empty($name))
                            continue;

                        $p_phone = $_POST['passenger_phones'][$index] ?? ($_POST['new_customer_phone'] ?? '0000000000');

                        // Check duplicate customer by phone before creating
                        if (!empty($p_phone) && $p_phone !== '0000000000') {
                            $existingCustomer = $this->customerModel->isPhoneExists($p_phone);
                            if ($existingCustomer) {
                                $stmt = $this->pdo->prepare("SELECT id FROM customers WHERE phone = :phone LIMIT 1");
                                $stmt->execute(['phone' => $p_phone]);
                                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                                $p_id = $existing ? $existing['id'] : $this->createPassengerCustomer($name, $p_phone, $index);
                            } else {
                                $p_id = $this->createPassengerCustomer($name, $p_phone, $index);
                            }
                        } else {
                            $p_id = $this->createPassengerCustomer($name, $p_phone, $index);
                        }

                        $passengers[] = [
                            'customer_id' => $p_id,
                            'age_type' => $_POST['passenger_types'][$index] ?? 'adult',
                            'is_primary' => 0 // Không phải đại diện
                        ];
                    }
                }

                // Create booking (skip its own transaction since we're in a transaction already)
                $booking_id = $this->bookingModel->create($data, $passengers, false);

                // Increment booked count on schedule (must be in same transaction)
                if (!$scheduleModel->incrementBooked($schedule_id, $totalParticipants)) {
                    throw new Exception("Không thể cập nhật số lượng đã đặt cho lịch khởi hành");
                }

                // Commit transaction
                $this->pdo->commit();

                set_success("Tạo Booking thành công!");
                redirect('?act=admin&module=bookings');

            } catch (Exception $e) {
                // Rollback transaction on any error
                $this->pdo->rollBack();
                throw $e; // Re-throw to be caught by outer catch
            }
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

        // Get Booking Services
        require_once 'app/models/BookingService.php';
        $bookingServiceModel = new BookingService($this->pdo);
        $bookingServices = $bookingServiceModel->getByBookingId($id);
        $serviceTotals = $bookingServiceModel->getTotalCostByBooking($id);

        // Get available services for adding
        require_once 'app/models/Service.php';
        $serviceModel = new Service($this->pdo);
        $availableServices = $serviceModel->getAll(['status' => 'active'], 1, 1000);
        $availableServicesList = $availableServices['data'] ?? [];

        // Get service providers for dropdown
        require_once 'app/models/ServiceProvider.php';
        $serviceProviderModel = new ServiceProvider($this->pdo);
        $allProviders = $serviceProviderModel->getAll(['status' => 'active'], 1, 1000);
        $serviceProviders = $allProviders['data'] ?? [];

        // Get available customers for adding passengers
        $customersResult = $this->customerModel->getAll([], 1, 1000);
        $availableCustomers = $customersResult['data'] ?? [];

        $page_title = 'Chi tiết Booking: ' . $booking['booking_code'];
        $content_file = 'app/views/admin/bookings/show.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    public function storePayment()
    {
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            require_csrf_token();
            
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

                // Validate payment_date (không được trong tương lai)
                $payment_date = $_POST['payment_date'] ?? date('Y-m-d');
                $today = date('Y-m-d');
                if ($payment_date > $today) {
                    throw new Exception("Ngày thanh toán không được trong tương lai");
                }

                $data = [
                    'booking_id' => $bookingId,
                    'payment_method' => $_POST['payment_method'],
                    'amount' => $amount,
                    'payment_type' => $_POST['payment_type'],
                    'transaction_id' => $_POST['transaction_id'] ?? null,
                    'receipt_number' => $_POST['receipt_number'] ?? null,
                    'payment_date' => $payment_date,
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
                redirect("?act=admin&module=bookings&action=show&id=" . ($_POST['booking_id'] ?? ''));
            }
        }
    }

    public function changeStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            require_csrf_token();
            
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

                    // Note: Quota đã được check và trừ khi tạo booking (pending)
                    // Khi approve, cần check lại schedule status để đảm bảo vẫn còn open
                    require_once 'app/models/TourSchedule.php';
                    $scheduleModel = new TourSchedule($this->pdo);
                    $schedule = $scheduleModel->getByTourAndDateRange(
                        $booking['tour_id'],
                        $booking['start_date'],
                        $booking['end_date']
                    );

                    if ($schedule) {
                        // Check schedule status must be 'open'
                        if ($schedule['status'] !== 'open') {
                            throw new Exception("Không thể duyệt booking. Lịch khởi hành không còn khả dụng (Status: {$schedule['status']}). Vui lòng kiểm tra lại lịch khởi hành.");
                        }
                    } else {
                        // Schedule không tồn tại - có thể đã bị xóa
                        throw new Exception("Không thể duyệt booking. Lịch khởi hành không tồn tại. Vui lòng kiểm tra lại.");
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
                redirect("?act=admin&module=bookings&action=show&id=" . ($id ?? ''));
            }
        }
    }

    /**
     * Import danh sách khách hàng từ Excel/CSV
     * Trả về JSON để fill vào passenger list
     */
    public function importPassengers()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
                throw new Exception("Thiếu file upload");
            }

            $file = $_FILES['file'];

            // Validate file
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Lỗi upload file: " . $file['error']);
            }

            $allowedExtensions = ['csv', 'xlsx', 'xls'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $allowedExtensions)) {
                throw new Exception("File không hợp lệ. Chỉ chấp nhận CSV, XLS, XLSX");
            }

            // Validate file size (max 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new Exception("File quá lớn. Tối đa 5MB");
            }

            // Move uploaded file
            $uploadDir = 'public/uploads/imports/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = 'import_' . time() . '_' . basename($file['name']);
            $filePath = $uploadDir . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new Exception("Không thể lưu file");
            }

            // Import file
            require_once MODELS_PATH . '/CustomerImport.php';
            $importModel = new CustomerImport($this->pdo);

            $result = $importModel->importFromFile($filePath, $file['name'], get_user_id());

            // Format data để trả về cho passenger list
            $passengers = [];
            if ($result['success'] > 0) {
                // Lấy danh sách khách hàng vừa import (theo phone hoặc name)
                // Hoặc trả về data từ file để fill vào form
                // Tạm thời trả về thông báo thành công
            }

            echo json_encode([
                'success' => true,
                'message' => "Import thành công: {$result['success']} khách hàng",
                'data' => [
                    'imported' => $result['success'],
                    'errors' => $result['errors'],
                    'total' => $result['total']
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Import và trả về danh sách passengers để fill vào form
     * (Không tạo customer, chỉ đọc file để fill form)
     */
    public function previewPassengers()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
                throw new Exception("Thiếu file upload");
            }

            $file = $_FILES['file'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Lỗi upload file: " . $file['error']);
            }

            $allowedExtensions = ['csv', 'xlsx', 'xls'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $allowedExtensions)) {
                throw new Exception("File không hợp lệ. Chỉ chấp nhận CSV, XLS, XLSX");
            }

            // Read file directly from tmp
            require_once __DIR__ . '/../../models/CustomerImport.php';
            $importModel = new CustomerImport($this->pdo);
            $rows = $importModel->readFile($file['tmp_name'], $extension);

            // Format để trả về cho JavaScript
            $passengers = [];
            foreach ($rows as $row) {
                // Skip nếu không có tên
                if (empty($row['full_name'])) {
                    continue;
                }

                // Format phone: loại bỏ khoảng trắng, dấu gạch ngang
                $phone = $row['phone'] ?? '';
                if (!empty($phone)) {
                    $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
                    
                    if (is_numeric($phone)) {
                        $phone = (string)$phone;
                        if (strpos($phone, '84') === 0 && strlen($phone) == 11) {
                            $phone = '0' . substr($phone, 2);
                        } elseif (strlen($phone) == 9 && substr($phone, 0, 1) != '0') {
                            $phone = '0' . $phone;
                        }
                    }
                }
                
                $passengers[] = [
                    'name' => $row['full_name'] ?? '',
                    'phone' => $phone,
                    'email' => $row['email'] ?? '',
                    'gender' => $this->normalizeGender($row['gender'] ?? ''),
                    'age_type' => $this->determineAgeType($row)
                ];
            }

            echo json_encode([
                'success' => true,
                'passengers' => $passengers,
                'count' => count($passengers)
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    /**
     * Xác định age_type từ dữ liệu
     * Chỉ dựa vào cột "Loại" trong file CSV
     */
    private function determineAgeType($row)
    {
        // Nếu có field age_type trong file
        if (!empty($row['age_type'])) {
            $ageType = strtolower(trim($row['age_type']));
            if (stripos($ageType, 'trẻ') !== false || stripos($ageType, 'child') !== false || stripos($ageType, 'trẻ em') !== false) {
                return 'child';
            } elseif (stripos($ageType, 'bé') !== false || stripos($ageType, 'infant') !== false || stripos($ageType, 'em bé') !== false) {
                return 'infant';
            } elseif (stripos($ageType, 'lớn') !== false || stripos($ageType, 'adult') !== false || stripos($ageType, 'người lớn') !== false) {
                return 'adult';
            }
        }

        // Mặc định là người lớn
        return 'adult';
    }

    private function parseDate($dateStr)
    {
        if (empty($dateStr)) {
            return null;
        }

        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d', 'd.m.Y'];

        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $dateStr);
            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }

        $timestamp = strtotime($dateStr);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    private function normalizeGender($genderStr)
    {
        if (empty($genderStr)) {
            return 'other';
        }

        $genderStr = strtolower(trim($genderStr));

        if (in_array($genderStr, ['nam', 'male', 'm', '1'])) {
            return 'male';
        } elseif (in_array($genderStr, ['nữ', 'nu', 'female', 'f', '2'])) {
            return 'female';
        }

        return 'other';
    }

    /**
     * Thêm dịch vụ vào booking
     */
    public function storeBookingService()
    {
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf_token();
            
            try {
                require_once 'app/models/BookingService.php';
                require_once 'app/models/Service.php';
                $bookingServiceModel = new BookingService($this->pdo);
                $serviceModel = new Service($this->pdo);

                $booking_id = (int)($_POST['booking_id'] ?? 0);
                $service_id = (int)($_POST['service_id'] ?? 0);
                $service_provider_id = !empty($_POST['service_provider_id']) ? (int)$_POST['service_provider_id'] : null;

                if (!$booking_id || !$service_id) {
                    throw new Exception("Thiếu thông tin bắt buộc.");
                }

                // Validate booking exists
                $booking = $this->bookingModel->getById($booking_id);
                if (!$booking) {
                    throw new Exception("Booking không tồn tại.");
                }

                // Validate deadline: Không được thêm dịch vụ nếu đã qua deadline
                $this->validateBookingDeadline($booking);

                if ($booking['approval_status'] === 'cancelled') {
                    throw new Exception("Không thể thêm dịch vụ vào booking đã hủy.");
                }

                // Get service info
                $service = $serviceModel->findById($service_id);
                if (!$service || $service['status'] !== 'active') {
                    throw new Exception("Dịch vụ không tồn tại hoặc không khả dụng.");
                }

                // Get service provider if not provided (use from service)
                if (!$service_provider_id && !empty($service['service_provider_id'])) {
                    $service_provider_id = $service['service_provider_id'];
                }

                $quantity = (int)($_POST['quantity'] ?? 1);
                $unit_price = (float)($_POST['unit_price'] ?? 0);
                $total_price = $quantity * $unit_price;

                if ($quantity <= 0) {
                    throw new Exception("Số lượng phải lớn hơn 0.");
                }
                if ($unit_price < 0) {
                    throw new Exception("Đơn giá không được âm.");
                }

                $data = [
                    'booking_id' => $booking_id,
                    'service_id' => $service_id,
                    'service_provider_id' => $service_provider_id,
                    'service_name' => $service['name'], // Snapshot
                    'quantity' => $quantity,
                    'unit' => $service['unit'] ?? null,
                    'unit_price' => $unit_price,
                    'total_price' => $total_price,
                    'service_date' => $booking['start_date'], // Default to booking start date
                    'notes' => !empty($_POST['notes']) ? sanitize($_POST['notes']) : null,
                    'created_by' => $_SESSION['user_id'] ?? 1
                ];

                $bookingServiceModel->create($data);

                set_success("Đã thêm dịch vụ vào booking!");
                redirect("?act=admin&module=bookings&action=show&id=$booking_id");

            } catch (Exception $e) {
                set_error($e->getMessage());
                redirect("?act=admin&module=bookings&action=show&id=" . ($_POST['booking_id'] ?? ''));
            }
        }
    }

    /**
     * Xóa dịch vụ khỏi booking
     */
    public function deleteBookingService()
    {
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf_token();
            
            try {
                require_once 'app/models/BookingService.php';
                $bookingServiceModel = new BookingService($this->pdo);

                $id = (int)($_POST['id'] ?? 0);
                $booking_id = (int)($_POST['booking_id'] ?? 0);

                if (!$id || !$booking_id) {
                    throw new Exception("Thiếu thông tin.");
                }

                // Check if service exists and not paid
                $service = $bookingServiceModel->getById($id);
                if (!$service) {
                    throw new Exception("Dịch vụ không tồn tại.");
                }

                if ($service['booking_id'] != $booking_id) {
                    throw new Exception("Dịch vụ không thuộc booking này.");
                }

                if ($service['paid_amount'] > 0) {
                    throw new Exception("Không thể xóa dịch vụ đã thanh toán.");
                }

                if ($bookingServiceModel->delete($id)) {
                    set_success("Đã xóa dịch vụ khỏi booking!");
                } else {
                    throw new Exception("Không thể xóa dịch vụ.");
                }

                redirect("?act=admin&module=bookings&action=show&id=$booking_id");

            } catch (Exception $e) {
                set_error($e->getMessage());
                redirect("?act=admin&module=bookings&action=show&id=" . ($_POST['booking_id'] ?? ''));
            }
        }
    }

    /**
     * Thêm khách hàng vào booking (sau khi đã tạo)
     */
    public function addPassengerToBooking()
    {
        require_admin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf_token();
            
            try {
                $booking_id = (int)($_POST['booking_id'] ?? 0);
                $customer_id = (int)($_POST['customer_id'] ?? 0);
                $age_type = $_POST['age_type'] ?? 'adult';
                $is_primary = isset($_POST['is_primary']) ? 1 : 0;

                if (!$booking_id || !$customer_id) {
                    throw new Exception("Thiếu thông tin bắt buộc.");
                }

                // Validate booking
                $booking = $this->bookingModel->getById($booking_id);
                if (!$booking) {
                    throw new Exception("Booking không tồn tại.");
                }

                // Validate deadline: Không được thêm hành khách nếu đã qua deadline
                $this->validateBookingDeadline($booking, "Không thể thêm hành khách");

                if ($booking['approval_status'] === 'cancelled') {
                    throw new Exception("Không thể thêm khách vào booking đã hủy.");
                }

                // Validate age_type
                if (!in_array($age_type, ['adult', 'child', 'infant'])) {
                    throw new Exception("Loại khách hàng không hợp lệ.");
                }

                // Check if customer already in booking
                $existingPassengers = $booking['passengers'] ?? [];
                foreach ($existingPassengers as $p) {
                    if ($p['customer_id'] == $customer_id) {
                        throw new Exception("Khách hàng này đã có trong booking.");
                    }
                }

                // Check if primary already exists
                if ($is_primary) {
                    foreach ($existingPassengers as $p) {
                        if ($p['is_primary']) {
                            throw new Exception("Booking đã có khách chính. Không thể thêm khách chính khác.");
                        }
                    }
                }

                // Add passenger
                $sql = "INSERT INTO booking_customers (booking_id, customer_id, age_type, is_primary) 
                        VALUES (:booking_id, :customer_id, :age_type, :is_primary)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    'booking_id' => $booking_id,
                    'customer_id' => $customer_id,
                    'age_type' => $age_type,
                    'is_primary' => $is_primary
                ]);

                // Update booking counts
                $currentAdult = $booking['adult_count'];
                $currentChild = $booking['child_count'];
                $currentInfant = $booking['infant_count'];

                if ($age_type === 'adult') {
                    $currentAdult++;
                } elseif ($age_type === 'child') {
                    $currentChild++;
                } else {
                    $currentInfant++;
                }

                $updateSql = "UPDATE bookings SET 
                              adult_count = :adult,
                              child_count = :child,
                              infant_count = :infant
                              WHERE id = :id";
                $updateStmt = $this->pdo->prepare($updateSql);
                $updateStmt->execute([
                    'adult' => $currentAdult,
                    'child' => $currentChild,
                    'infant' => $currentInfant,
                    'id' => $booking_id
                ]);

                // Log history
                $this->bookingModel->logHistory($booking_id, $booking['approval_status'], $booking['approval_status'], 
                    $_SESSION['user_id'] ?? null, "Thêm khách hàng vào booking");

                set_success("Đã thêm khách hàng vào booking!");
                redirect("?act=admin&module=bookings&action=show&id=$booking_id");

            } catch (Exception $e) {
                set_error($e->getMessage());
                redirect("?act=admin&module=bookings&action=show&id=" . ($_POST['booking_id'] ?? ''));
            }
        }
    }

    /**
     * Download template CSV file
     */
    public function downloadTemplate()
    {
        require_admin();

        // Use PUBLIC_PATH constant from bootstrap
        $templatePath = PUBLIC_PATH . '/templates/customer_import_template.csv';

        // Fallback to relative path if constant not defined
        if (!defined('PUBLIC_PATH')) {
            $templatePath = __DIR__ . '/../../public/templates/customer_import_template.csv';
        }

        // Normalize path
        $templatePath = realpath($templatePath);

        if (!$templatePath || !file_exists($templatePath)) {
            set_error("File template không tồn tại");
            redirect('?act=admin&module=bookings&action=create');
            return;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="customer_import_template.csv"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        // Add BOM for UTF-8 Excel compatibility
        echo "\xEF\xBB\xBF";

        readfile($templatePath);
        exit;
    }

    /**
     * Validate booking deadline (helper method)
     */
    private function validateBookingDeadline($booking, $actionMessage = "Không thể thực hiện thao tác")
    {
        $today = date('Y-m-d');
        $start_date = $booking['start_date'] ?? null;
        
        if (!$start_date) {
            throw new Exception("Booking không có ngày khởi hành");
        }

        $tour = $this->tourModel->findById($booking['tour_id']);
        $deadline_days = (int) ($tour['booking_deadline_days'] ?? 1);
        
        $daysUntilStart = (strtotime($start_date) - strtotime($today)) / (60 * 60 * 24);
        if ($daysUntilStart < $deadline_days) {
            throw new Exception("{$actionMessage}. Booking này khởi hành trong vòng {$deadline_days} ngày hoặc đã khởi hành. Vui lòng liên hệ admin để xử lý.");
        }
    }

    /**
     * Create passenger customer (helper method)
     */
    private function createPassengerCustomer($name, $phone, $index)
    {
        $passengerData = [
            'full_name' => $name,
            'phone' => $phone,
            'email' => $_POST['passenger_emails'][$index] ?? null,
            'gender' => $_POST['passenger_genders'][$index] ?? 'other',
            'created_by' => $_SESSION['user_id'] ?? 1
        ];
        return $this->customerModel->create($passengerData);
    }
}
