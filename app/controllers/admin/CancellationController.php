<?php
require_once 'app/models/Booking.php';
require_once 'app/models/Tour.php';
require_once 'app/models/Customer.php';
require_once 'app/models/Payment.php';

class CancellationController
{
    private $bookingModel;
    private $tourModel;
    private $customerModel;
    private $paymentModel;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->bookingModel = new Booking($pdo);
        $this->tourModel = new Tour($pdo);
        $this->customerModel = new Customer($pdo);
        $this->paymentModel = new Payment($pdo);
    }

    /**
     * Danh sách booking đã hủy
     */
    public function index()
    {
        require_admin();

        $page = (int) ($_GET['page'] ?? 1);
        $limit = 20;

        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '', // cancelled | refunded
            'tour_id' => $_GET['tour_id'] ?? '',
            'cancellation_date_from' => $_GET['cancellation_date_from'] ?? '',
            'cancellation_date_to' => $_GET['cancellation_date_to'] ?? '',
            'start_date_from' => $_GET['start_date_from'] ?? '',
            'start_date_to' => $_GET['start_date_to'] ?? '',
            'has_refund' => $_GET['has_refund'] ?? '', // yes | no
            'days_before' => $_GET['days_before'] ?? '' // Filter by days before departure
        ];

        $bookings = $this->bookingModel->getCancelledBookings($filters, $page, $limit);
        $total_records = $this->bookingModel->countCancelledBookings($filters);
        $total_pages = ceil($total_records / $limit);

        // Get active tours for filter
        $toursResult = $this->tourModel->getAll(['status' => 'active']);
        $tours = $toursResult['data'] ?? [];

        // Quick stats
        $stats = $this->bookingModel->getCancellationQuickStats();

        $page_title = 'Quản lý Hủy Booking';
        $content_file = 'app/views/admin/cancellations/index.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    /**
     * Chi tiết booking đã hủy
     */
    public function show()
    {
        require_admin();

        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) {
            set_error('Thiếu thông tin booking');
            redirect('?act=admin&module=cancellations');
            return;
        }

        $booking = $this->bookingModel->getById($id);
        if (!$booking) {
            set_error('Booking không tồn tại');
            redirect('?act=admin&module=cancellations');
            return;
        }

        // Ensure passengers array exists
        if (!isset($booking['passengers'])) {
            $booking['passengers'] = [];
        }

        // Check if booking is cancelled
        if (!in_array($booking['payment_status'], ['cancelled', 'refunded'])) {
            set_error('Booking này chưa được hủy');
            redirect('?act=admin&module=bookings&action=show&id=' . $id);
            return;
        }

        // Get cancellation policy info
        $policy = null;
        if (!empty($booking['cancellation_policy_id'])) {
            $sql = "SELECT * FROM cancellation_policies WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $booking['cancellation_policy_id']]);
            $policy = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Get payments (include creator name)
        $payments = $this->paymentModel->getByBookingId($id);

        // Get refund payments
        $refunds = array_filter($payments, function ($p) {
            return $p['payment_type'] === 'refund';
        });

        // Check if refund has been processed
        $hasRefundProcessed = $this->bookingModel->hasRefundProcessed($id);

        // Calculate days before departure when cancelled
        $daysBeforeDeparture = null;
        if ($booking['cancellation_date'] && $booking['start_date']) {
            $cancelDate = new DateTime($booking['cancellation_date']);
            $startDate = new DateTime($booking['start_date']);
            $interval = $cancelDate->diff($startDate);
            $daysBeforeDeparture = (int) $interval->format('%a');
        }

        $page_title = 'Chi tiết Booking Hủy';
        $content_file = 'app/views/admin/cancellations/show.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    /**
     * Xử lý hoàn tiền
     */
    public function processRefund()
    {
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            set_error('Phương thức không hợp lệ');
            redirect('?act=admin&module=cancellations');
            return;
        }

        require_csrf_token();

        try {
            $booking_id = (int) ($_POST['booking_id'] ?? 0);
            $amount = (float) ($_POST['amount'] ?? 0);
            $payment_method = $_POST['payment_method'] ?? 'bank_transfer';
            $notes = $_POST['notes'] ?? 'Hoàn tiền hủy booking';
            $payment_date = $_POST['payment_date'] ?? date('Y-m-d');

            if (!$booking_id || $amount <= 0) {
                throw new Exception('Thiếu thông tin bắt buộc');
            }

            // Get booking
            $booking = $this->bookingModel->getById($booking_id);
            if (!$booking) {
                throw new Exception('Booking không tồn tại');
            }

            // Check if booking is refunded
            if ($booking['payment_status'] !== 'refunded') {
                throw new Exception('Booking này không có tiền hoàn lại');
            }

            // Check if already processed
            if ($this->bookingModel->hasRefundProcessed($booking_id)) {
                throw new Exception('Hoàn tiền đã được xử lý trước đó');
            }

            // Validate amount
            if ($amount > $booking['refund_amount']) {
                throw new Exception('Số tiền hoàn lại không được vượt quá ' . number_format($booking['refund_amount']) . ' VNĐ');
            }

            // Process refund
            $refundData = [
                'booking_id' => $booking_id,
                'amount' => $amount,
                'payment_method' => $payment_method,
                'payment_type' => 'refund',
                'payment_date' => $payment_date,
                'notes' => $notes,
                'created_by' => $_SESSION['user_id'] ?? null
            ];

            $this->paymentModel->refund($booking_id, $amount, $refundData);

            set_success('Đã xử lý hoàn tiền thành công!');
            redirect('?act=admin&module=cancellations&action=show&id=' . $booking_id);

        } catch (Exception $e) {
            set_error('Lỗi: ' . $e->getMessage());
            redirect('?act=admin&module=cancellations&action=show&id=' . ($_POST['booking_id'] ?? ''));
        }
    }

    /**
     * Thống kê hủy booking
     */
    public function statistics()
    {
        require_admin();

        $filters = [
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'tour_id' => $_GET['tour_id'] ?? ''
        ];

        $stats = $this->bookingModel->getCancellationStatistics($filters);

        // Get active tours for filter
        $toursResult = $this->tourModel->getAll(['status' => 'active']);
        $tours = $toursResult['data'] ?? [];

        $page_title = 'Thống kê Hủy Booking';
        $content_file = 'app/views/admin/cancellations/statistics.php';
        require_once 'app/views/layouts/admin_layout.php';
    }
}

