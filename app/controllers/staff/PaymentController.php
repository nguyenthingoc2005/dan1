<?php
namespace Staff;

/**
 * ==============================================================================
 * PAYMENT CONTROLLER (STAFF) - READ ONLY
 * ==============================================================================
 * 
 * Staff xem Payments của các bookings mình đã tạo
 * Chức năng READ ONLY, không create payment từ đây
 * (Create payment từ BookingController::storePayment)
 * 
 * @version 1.0
 * @date 2024-12-03
 * ==============================================================================
 */

class PaymentController
{
    private $paymentModel;
    private $bookingModel;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        require_once MODELS_PATH . '/Payment.php';
        require_once MODELS_PATH . '/Booking.php';

        $this->paymentModel = new \Payment($pdo);
        $this->bookingModel = new \Booking($pdo);
    }

    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $filters = [
            'search' => $_GET['search'] ?? '',
            'payment_method' => $_GET['payment_method'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? '',
            'created_by_booking' => get_user_id() // KEY: Chỉ payments của bookings mình tạo
        ];

        $result = $this->paymentModel->getAll($filters, $page, $limit);
        $payments = $result['data'];
        $total_records = $result['total'];
        $total_pages = $result['pages'];

        $page_title = 'Lịch Sử Thanh Toán';
        $content_file = VIEWS_PATH . '/staff/payments/index.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    public function show()
    {
        $id = $_GET['id'] ?? null;
        if (!$id)
            redirect('?act=staff-payments');

        $payment = $this->paymentModel->findById($id);
        if (!$payment) {
            set_error("Không tìm thấy payment.");
            redirect('?act=staff-payments');
        }

        // Get booking
        $booking = $this->bookingModel->getById($payment['booking_id']);

        // CHECK OWNERSHIP via booking
        if ($booking['created_by'] != get_user_id()) {
            set_error("Bạn không có quyền xem payment này.");
            redirect('?act=staff-payments');
        }

        $page_title = 'Chi tiết Thanh Toán #' . $payment['id'];
        $content_file = VIEWS_PATH . '/staff/payments/show.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }
}
