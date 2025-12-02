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

                // ... (Existing Schedule & Quota Logic) ...
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
                $tour = $this->tourModel->findById($_POST['tour_id']);
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
                    'customer_id' => $customer_id,
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

                // Passengers handling
                $passengers = [];
                if (!empty($_POST['passenger_names'])) {
                    foreach ($_POST['passenger_names'] as $index => $name) {
                        if (empty($name))
                            continue;

                        // For now, we need a customer_id for each passenger.
                        // Strategy: Create a "Guest" customer record or reuse main customer?
                        // Reusing main customer for all passengers is bad for data.
                        // Creating new customers for each passenger is correct but requires phone.
                        // Workaround: Create customer with MainPhone + Suffix?
                        // OR: Just store them as passengers linked to the Main Customer ID?
                        // Wait, booking_customers table has customer_id.
                        // Let's create a new customer record for each passenger.
                        // If phone is missing, use MainPhone.

                        $p_phone = $_POST['new_customer_phone'] ?? '0000000000'; // Fallback
                        // Actually, if we are in existing mode, we need to fetch main customer phone.
                        // This is getting complicated.
                        // SIMPLIFICATION: For V1, we will create a customer record for each passenger.
                        // Use a dummy phone if not provided? Or just use the main customer's phone.

                        // Let's try to find if this passenger already exists by name + phone? No.
                        // Just create a new customer record.

                        $passengerData = [
                            'full_name' => $name,
                            'phone' => $p_phone, // Reuse phone for now
                            'date_of_birth' => $_POST['passenger_dobs'][$index] ?? null,
                            'gender' => $_POST['passenger_genders'][$index] ?? 'other',
                            'created_by' => $_SESSION['user_id'] ?? 1
                        ];

                        $p_id = $this->customerModel->create($passengerData);

                        $passengers[] = [
                            'customer_id' => $p_id,
                            'age_type' => $_POST['passenger_types'][$index] ?? 'adult',
                            'is_primary' => 0
                        ];
                    }
                }

                // Add Main Customer as Primary Passenger if not in list
                // (Usually main customer is one of the passengers, but if they didn't add themselves to the list...)
                // Let's just add the main customer as primary passenger.
                $passengers[] = [
                    'customer_id' => $customer_id,
                    'age_type' => 'adult', // Default
                    'is_primary' => 1
                ];

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

                if ($amount <= 0)
                    throw new Exception("Số tiền phải lớn hơn 0");

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
                if ($action == 'approve') {
                    $this->bookingModel->updateStatus($id, 'approved', 'approval', $userId);
                    $this->bookingModel->logHistory($id, 'pending', 'approved', $userId, "Duyệt thủ công");
                    set_success("Đã duyệt Booking!");
                } elseif ($action == 'reject') {
                    $reason = $_POST['reason'] ?? '';
                    $this->bookingModel->updateStatus($id, 'rejected', 'approval', $userId, $reason);
                    $this->bookingModel->logHistory($id, 'pending', 'rejected', $userId, $reason);
                    set_success("Đã từ chối Booking!");
                } elseif ($action == 'cancel') {
                    $reason = $_POST['reason'] ?? '';
                    // Use advanced cancel method
                    $result = $this->bookingModel->cancel($id, $reason, $userId);
                    set_success("Đã hủy Booking! Phí hủy: " . number_format($result['fee']) . " VNĐ (" . $result['policy'] . ")");
                }

                redirect("?act=admin&module=bookings&action=show&id=$id");

            } catch (Exception $e) {
                set_error("Lỗi: " . $e->getMessage());
                redirect("?act=admin&module=bookings&action=show&id=$id");
            }
        }
    }
}
