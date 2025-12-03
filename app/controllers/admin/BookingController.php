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
        $toursResult = $this->tourModel->getAll(['status' => 'active']);
        $tours = $toursResult['data'] ?? [];

        // Get customers
        $customersResult = $this->customerModel->getAll([], 1, 1000); // Get all customers
        $customers = $customersResult['data'] ?? [];

        // Get open schedules
        require_once 'app/models/TourSchedule.php';
        $scheduleModel = new TourSchedule($this->pdo);
        $schedulesResult = $scheduleModel->getAll(['status' => 'open'], 1, 1000); // Get all open schedules
        $schedules = $schedulesResult['data'] ?? [];

        // DEBUG: Log data
        error_log("=== BOOKING CREATE DEBUG ===");
        error_log("Tours count: " . count($tours));
        error_log("Customers count: " . count($customers));
        error_log("Schedules count: " . count($schedules));

        if (!empty($schedules)) {
            error_log("First schedule: " . print_r($schedules[0] ?? null, true));
        }

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
                        'notes' => !empty($_POST['notes']) ? sanitize($_POST['notes']) : null,
                    'created_by' => $_SESSION['user_id'] ?? 1
                ];

                // Passengers handling
                $passengers = [];

                // 1. Add Main Customer as Primary Representative (Khách hàng đại diện)
                    // Note: Primary customer luôn là adult vì người đặt tour thường là người lớn (phụ huynh)
                    // Nếu booking chỉ có trẻ em, primary vẫn là adult (người đại diện pháp lý)
                    $primary_age_type = 'adult'; // Mặc định là adult
                    
                    // Nếu chỉ có 1 người và là trẻ em/em bé, có thể primary là trẻ em
                    // Nhưng trong thực tế, người đặt tour luôn là người lớn
                    // Nên giữ nguyên logic: primary = adult
                    
                $passengers[] = [
                    'customer_id' => $customer_id,
                        'age_type' => $primary_age_type,
                    'is_primary' => 1
                ];

                // 2. Add other passengers from form (if any)
                if (!empty($_POST['passenger_names'])) {
                    foreach ($_POST['passenger_names'] as $index => $name) {
                        if (empty($name))
                            continue;

                        // Other passengers cannot be primary (already have one)
                        $p_phone = $_POST['passenger_phones'][$index] ?? ($_POST['new_customer_phone'] ?? '0000000000');

                            // Check duplicate customer by phone before creating
                            if (!empty($p_phone) && $p_phone !== '0000000000') {
                                $existingCustomer = $this->customerModel->isPhoneExists($p_phone);
                                if ($existingCustomer) {
                                    // Get existing customer ID
                                    $stmt = $this->pdo->prepare("SELECT id FROM customers WHERE phone = :phone LIMIT 1");
                                    $stmt->execute(['phone' => $p_phone]);
                                    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                                    if ($existing) {
                                        $p_id = $existing['id'];
                                    } else {
                                        // Create new customer if not found (should not happen, but safety check)
                                        $passengerData = [
                                            'full_name' => $name,
                                            'phone' => $p_phone,
                                            'email' => $_POST['passenger_emails'][$index] ?? null,
                                            'gender' => $_POST['passenger_genders'][$index] ?? 'other',
                                            'created_by' => $_SESSION['user_id'] ?? 1
                                        ];
                                        $p_id = $this->customerModel->create($passengerData);
                                    }
                                } else {
                                    // Create new customer
                                    $passengerData = [
                                        'full_name' => $name,
                                        'phone' => $p_phone,
                                        'email' => $_POST['passenger_emails'][$index] ?? null,
                                        'gender' => $_POST['passenger_genders'][$index] ?? 'other',
                                        'created_by' => $_SESSION['user_id'] ?? 1
                                    ];
                                    $p_id = $this->customerModel->create($passengerData);
                                }
                            } else {
                                // Create customer without phone check if phone is empty/invalid
                        $passengerData = [
                            'full_name' => $name,
                            'phone' => $p_phone,
                                    'email' => $_POST['passenger_emails'][$index] ?? null,
                            'gender' => $_POST['passenger_genders'][$index] ?? 'other',
                            'created_by' => $_SESSION['user_id'] ?? 1
                        ];
                        $p_id = $this->customerModel->create($passengerData);
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
                // Log error for debugging
                error_log("BookingController::store() ERROR: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                error_log("POST data: " . print_r($_POST, true));
                
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
                // Log error for debugging
                error_log("BookingController::storePayment() ERROR: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                
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
                // Log error for debugging
                error_log("BookingController::changeStatus() ERROR: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                error_log("Action: " . ($_POST['action'] ?? 'unknown') . ", Booking ID: " . ($id ?? 'unknown'));
                
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

        // DEBUG
        error_log("=== PREVIEW PASSENGERS DEBUG ===");
        error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
        error_log("Files: " . print_r($_FILES, true));

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
                error_log("❌ Missing file upload");
                throw new Exception("Thiếu file upload");
            }

            $file = $_FILES['file'];
            error_log("File details: " . print_r($file, true));

            if ($file['error'] !== UPLOAD_ERR_OK) {
                error_log("❌ Upload error: " . $file['error']);
                throw new Exception("Lỗi upload file: " . $file['error']);
            }

            $allowedExtensions = ['csv', 'xlsx', 'xls'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            error_log("File extension: " . $extension);

            if (!in_array($extension, $allowedExtensions)) {
                error_log("❌ Invalid extension: " . $extension);
                throw new Exception("File không hợp lệ. Chỉ chấp nhận CSV, XLS, XLSX");
            }

            // Read file directly from tmp
            require_once __DIR__ . '/../../models/CustomerImport.php';
            $importModel = new CustomerImport($this->pdo);

            error_log("Reading file: " . $file['tmp_name']);
            // For Excel files, we'll try to read as CSV (user should save as CSV)
            // Or we can add PhpSpreadsheet library later
            $rows = $importModel->readFile($file['tmp_name'], $extension);
            error_log("Rows read: " . count($rows));
            error_log("First row: " . print_r($rows[0] ?? null, true));

            // Format để trả về cho JavaScript
            $passengers = [];
            foreach ($rows as $index => $row) {
                // Skip nếu không có tên
                if (empty($row['full_name'])) {
                    error_log("Skipping row $index: no full_name");
                    continue;
                }

                // Format phone: loại bỏ khoảng trắng, dấu gạch ngang
                $phone = $row['phone'] ?? '';
                if (!empty($phone)) {
                    // Loại bỏ khoảng trắng, dấu gạch ngang, dấu ngoặc
                    $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
                    
                    // Nếu là số (Excel format), chuyển về string
                    if (is_numeric($phone)) {
                        $phone = (string)$phone;
                        // Nếu bắt đầu bằng 84, chuyển thành 0
                        if (strpos($phone, '84') === 0 && strlen($phone) == 11) {
                            $phone = '0' . substr($phone, 2);
                        }
                        // Nếu thiếu số 0 đầu và có 9 chữ số, thêm 0
                        elseif (strlen($phone) == 9 && substr($phone, 0, 1) != '0') {
                            $phone = '0' . $phone;
                        }
                    }
                }
                
                $passenger = [
                    'name' => $row['full_name'] ?? '',
                    'phone' => $phone,
                    'email' => $row['email'] ?? '',
                    'gender' => $this->normalizeGender($row['gender'] ?? ''),
                    'age_type' => $this->determineAgeType($row)
                ];

                error_log("Passenger $index: " . print_r($passenger, true));
                $passengers[] = $passenger;
            }

            error_log("Total passengers: " . count($passengers));

            $response = [
                'success' => true,
                'passengers' => $passengers,
                'count' => count($passengers)
            ];

            error_log("Response: " . json_encode($response, JSON_UNESCAPED_UNICODE));
            echo json_encode($response, JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            error_log("❌ Exception: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());

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

        error_log("Template path: " . $templatePath);
        error_log("File exists: " . (file_exists($templatePath) ? 'YES' : 'NO'));

        if (!$templatePath || !file_exists($templatePath)) {
            error_log("❌ Template file not found at: " . $templatePath);
            set_error("File template không tồn tại tại: " . $templatePath);
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
}
