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
        $tours = $this->tourModel->getAll(['status' => 'active', 'approval_status' => 'approved'])['data'] ?? [];

        // Get customers
        $customers = $this->customerModel->getAll([], 1, 1000)['data'] ?? [];

        // Get open schedules
        require_once MODELS_PATH . '/TourSchedule.php';
        $scheduleModel = new \TourSchedule($this->pdo);
        $schedules = $scheduleModel->getAll(['status' => 'open'], 1, 1000)['data'] ?? [];

        // Pre-fill from query params (from schedule view)
        $prefill = [
            'tour_id' => isset($_GET['tour_id']) ? (int) $_GET['tour_id'] : null,
            'start_date' => isset($_GET['start_date']) ? sanitize($_GET['start_date']) : null
        ];

        $page_title = 'Tạo Booking Mới';
        $content_file = VIEWS_PATH . '/staff/bookings/create.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            require_csrf_token();
            
            try {
                // Handle Customer (New vs Existing)
                $customer_id = $_POST['customer_id'] ?? null;
                if (($_POST['customer_mode'] ?? 'existing') === 'new') {
                    if (empty($_POST['new_customer_name']) || empty($_POST['new_customer_phone'])) {
                        throw new \Exception("Vui lòng nhập tên và số điện thoại khách hàng mới.");
                    }

                    // Check duplicate customer by phone
                    if (!empty($_POST['new_customer_phone'])) {
                        $existingCustomer = $this->customerModel->isPhoneExists($_POST['new_customer_phone']);
                        if ($existingCustomer) {
                            // Use existing customer
                            $stmt = $this->pdo->prepare("SELECT id FROM customers WHERE phone = :phone LIMIT 1");
                            $stmt->execute(['phone' => $_POST['new_customer_phone']]);
                            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                            if ($existing) {
                                $customer_id = $existing['id'];
                            } else {
                                // Create new if not found (should not happen)
                                $newCustomerData = [
                                    'full_name' => $_POST['new_customer_name'],
                                    'phone' => $_POST['new_customer_phone'],
                                    'email' => $_POST['new_customer_email'] ?? null,
                                    'address' => $_POST['new_customer_address'] ?? null,
                                    'created_by' => get_user_id()
                                ];
                                $customer_id = $this->customerModel->create($newCustomerData);
                            }
                        } else {
                            // Create new customer
                            $newCustomerData = [
                                'full_name' => $_POST['new_customer_name'],
                                'phone' => $_POST['new_customer_phone'],
                                'email' => $_POST['new_customer_email'] ?? null,
                                'address' => $_POST['new_customer_address'] ?? null,
                                'created_by' => get_user_id()
                            ];
                            $customer_id = $this->customerModel->create($newCustomerData);
                        }
                    }
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

                // Validate child_count and infant_count >= 0
                if ($child < 0 || $infant < 0) {
                    throw new \Exception("Số lượng trẻ em và em bé không được âm");
                }

                // Validate passenger info
                $total_count = $adult + $child + $infant;
                $passenger_count = !empty($_POST['passenger_names']) ? count($_POST['passenger_names']) : 0;
                $expected_passenger_count = $total_count - 1;

                if ($total_count > 1 && $passenger_count != $expected_passenger_count) {
                    throw new \Exception("Số hành khách cần nhập thêm: $expected_passenger_count người (Tổng $total_count - 1 đại diện). Bạn đã nhập: $passenger_count người");
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
                    $total_adult_expected = 1 + $passenger_adult_count;
                    $total_child_expected = $passenger_child_count;
                    $total_infant_expected = $passenger_infant_count;
                    
                    if ($total_adult_expected != $adult || $total_child_expected != $child || $total_infant_expected != $infant) {
                        throw new \Exception("Số lượng loại hành khách không khớp. Đã nhập: $adult người lớn, $child trẻ em, $infant em bé. Nhưng trong danh sách có: $total_adult_expected người lớn, $total_child_expected trẻ em, $total_infant_expected em bé.");
                    }
                } else {
                    // Nếu không có passengers, chỉ có primary customer (mặc định là adult)
                    if ($adult != 1 || $child != 0 || $infant != 0) {
                        throw new \Exception("Khi chỉ có 1 người (khách hàng đại diện), số lượng phải là: 1 người lớn, 0 trẻ em, 0 em bé.");
                    }
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

                        // Check max_participants before creating schedule
                        $maxParticipants = (int) ($tour['max_participants'] ?? 999);
                        if ($totalParticipants > $maxParticipants) {
                            throw new \Exception("Số lượng người tham gia ($totalParticipants) vượt quá giới hạn tối đa của tour ($maxParticipants người).");
                        }

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
                            // Check max_participants before increasing quota
                            $maxParticipants = (int) ($tour['max_participants'] ?? 999);
                            $new_quota = $schedule['booked'] + $totalParticipants;
                            
                            if ($new_quota > $maxParticipants) {
                                throw new \Exception("Số lượng người tham gia ($totalParticipants) vượt quá giới hạn tối đa của tour ($maxParticipants người).");
                            }
                            
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

                // Validate discount <= total_amount
                if ($discount > $total_amount) {
                    throw new \Exception("Số tiền giảm giá ($discount) không được lớn hơn tổng tiền ($total_amount)");
                }

                $final_amount = max(0, $total_amount - $discount);
                $deposit = (int) ($_POST['deposit_amount'] ?? 0);

                // Validate deposit <= final_amount
                if ($deposit > $final_amount) {
                    throw new \Exception("Tiền cọc ($deposit) không được lớn hơn số tiền sau giảm ($final_amount)");
                }

                $remaining = max(0, $final_amount - $deposit);

                // Check duplicate booking
                $duplicate = $this->bookingModel->checkDuplicate(
                    $customer_id,
                    $_POST['tour_id'],
                    $_POST['start_date']
                );
                if ($duplicate) {
                    throw new \Exception("Khách hàng đã có booking cho tour này vào ngày này (Booking #{$duplicate['booking_code']})");
                }

                // Get end_date from schedule
                $end_date = $schedule['end_date'] ?? date('Y-m-d', strtotime($_POST['start_date'] . " + {$tour['duration_days']} days"));

                // Validate end_date >= start_date
                if ($end_date < $_POST['start_date']) {
                    throw new \Exception("Ngày kết thúc phải sau hoặc bằng ngày khởi hành");
                }

                // Validate total_amount > 0
                if ($total_amount <= 0) {
                    throw new \Exception("Tổng tiền tour phải lớn hơn 0");
                }

                // Start transaction to ensure atomicity
                $this->pdo->beginTransaction();
                
                try {
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
                        'notes' => !empty($_POST['notes']) ? sanitize($_POST['notes']) : null,
                        'created_by' => get_user_id()
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
                                        // Create new customer if not found (should not happen)
                                        $passengerData = [
                                            'full_name' => $name,
                                            'phone' => $p_phone,
                                            'email' => $_POST['passenger_emails'][$index] ?? null,
                                            'gender' => $_POST['passenger_genders'][$index] ?? 'other',
                                            'created_by' => get_user_id()
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
                                        'created_by' => get_user_id()
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
                                    'created_by' => get_user_id()
                                ];
                                $p_id = $this->customerModel->create($passengerData);
                            }

                            $passengers[] = [
                                'customer_id' => $p_id,
                                'age_type' => $_POST['passenger_types'][$index] ?? 'adult',
                                'is_primary' => 0
                            ];
                        }
                    }

                    // Create booking (skip its own transaction since we're in a transaction already)
                    $booking_id = $this->bookingModel->create($data, $passengers, false);

                    // Increment booked count on schedule (must be in same transaction)
                    if (!$scheduleModel->incrementBooked($schedule_id, $totalParticipants)) {
                        throw new \Exception("Không thể cập nhật số lượng đã đặt cho lịch khởi hành");
                    }

                    // Commit transaction
                    $this->pdo->commit();

                    set_success("Tạo Booking thành công!");
                    redirect('?act=staff-bookings');
                } catch (\Exception $e) {
                    // Rollback transaction on any error
                    $this->pdo->rollBack();
                    throw $e; // Re-throw to be caught by outer catch
                }
            } catch (\Exception $e) {
                error_log("Staff BookingController::store() Error: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
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
            // Verify CSRF token
            require_csrf_token();
            
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

                // Validate payment_date (not in future)
                $payment_date = $_POST['payment_date'] ?? date('Y-m-d');
                $today = date('Y-m-d');
                if ($payment_date > $today) {
                    throw new \Exception("Ngày thanh toán không được là tương lai");
                }

                // Start transaction
                $this->pdo->beginTransaction();
                
                try {
                    $data = [
                        'booking_id' => $bookingId,
                        'payment_method' => $_POST['payment_method'],
                        'amount' => $amount,
                        'payment_type' => $_POST['payment_type'],
                        'transaction_id' => $_POST['transaction_id'] ?? null,
                        'receipt_number' => $_POST['receipt_number'] ?? null,
                        'payment_date' => $payment_date,
                        'notes' => !empty($_POST['notes']) ? sanitize($_POST['notes']) : null,
                        'created_by' => get_user_id()
                    ];

                    $payment_id = $paymentModel->create($data);
                    
                    // Update booking payment status
                    $this->bookingModel->updatePaymentStatus($bookingId);
                    
                    // Log payment action
                    $this->bookingModel->logHistory($bookingId, 'payment', $booking['approval_status'], get_user_id(), "Thanh toán: " . number_format($amount, 0, ',', '.') . " đ");

                    // Commit transaction
                    $this->pdo->commit();

                    set_success("Ghi nhận thanh toán thành công!");
                    redirect('?act=staff-bookings&action=show&id=' . $bookingId);
                } catch (\Exception $e) {
                    // Rollback transaction on any error
                    $this->pdo->rollBack();
                    throw $e; // Re-throw to be caught by outer catch
                }
            } catch (\Exception $e) {
                error_log("Staff BookingController::storePayment() Error: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                set_error($e->getMessage());
                redirect('?act=staff-bookings&action=show&id=' . $bookingId);
            }
        }
    }

    /**
     * Import và trả về danh sách passengers để fill vào form
     * (Không tạo customer, chỉ đọc file để fill form)
     */
    public function previewPassengers()
    {
        require_staff_or_admin();
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
                throw new \Exception("Thiếu file upload");
            }

            $file = $_FILES['file'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception("Lỗi upload file: " . $file['error']);
            }

            $allowedExtensions = ['csv', 'xlsx', 'xls'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $allowedExtensions)) {
                throw new \Exception("File không hợp lệ. Chỉ chấp nhận CSV, XLS, XLSX");
            }

            // Read file directly from tmp
            require_once MODELS_PATH . '/CustomerImport.php';
            $importModel = new \CustomerImport($this->pdo);

            $rows = $importModel->readFile($file['tmp_name'], $extension);

            // Format để trả về cho JavaScript
            $passengers = [];
            foreach ($rows as $index => $row) {
                // Skip nếu không có tên
                if (empty($row['full_name'])) {
                    continue;
                }

                // Format phone
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
                
                $passenger = [
                    'name' => $row['full_name'] ?? '',
                    'phone' => $phone,
                    'email' => $row['email'] ?? '',
                    'gender' => $this->normalizeGender($row['gender'] ?? ''),
                    'age_type' => $this->determineAgeType($row)
                ];

                $passengers[] = $passenger;
            }

            echo json_encode([
                'success' => true,
                'passengers' => $passengers,
                'count' => count($passengers)
            ], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    /**
     * Xác định age_type từ dữ liệu
     */
    private function determineAgeType($row)
    {
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
        return 'adult';
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
        require_staff_or_admin();

        $templatePath = defined('PUBLIC_PATH') ? PUBLIC_PATH . '/templates/customer_import_template.csv' : __DIR__ . '/../../public/templates/customer_import_template.csv';
        $templatePath = realpath($templatePath);

        if (!$templatePath || !file_exists($templatePath)) {
            set_error("File template không tồn tại.");
            redirect('?act=staff-bookings&action=create');
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
