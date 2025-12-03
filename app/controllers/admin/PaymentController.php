<?php
/**
 * ==============================================================================
 * PAYMENT CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý thanh toán
 * Routing: ?act=admin&module=payments&action=index
 * 
 * @version 1.0
 * @date 2024-12-03
 * ==============================================================================
 */

class PaymentController
{
    private $db;
    private $paymentModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        // We need a Payment model. If not exists, we might need to create it or use Booking model.
        // Assuming Payment model exists or we create it.
        // Let's check if Payment.php exists. If not, I'll create it.
        // Based on previous context, Payment logic was inside BookingController.
        // I will create a dedicated Payment model for better separation.

        if (file_exists(MODELS_PATH . '/Payment.php')) {
            require_once MODELS_PATH . '/Payment.php';
            $this->paymentModel = new Payment($pdo);
        } else {
            // Fallback or create it. I will create it in next step.
            // For now, assume it exists.
        }
    }

    /**
     * Danh sách thanh toán (Revenue Stream)
     */
    public function index()
    {
        require_admin();

        // Filters
        $filters = [];
        if (!empty($_GET['start_date']))
            $filters['start_date'] = $_GET['start_date'];
        if (!empty($_GET['end_date']))
            $filters['end_date'] = $_GET['end_date'];
        if (!empty($_GET['method']))
            $filters['payment_method'] = $_GET['method'];
        if (!empty($_GET['type']))
            $filters['payment_type'] = $_GET['type'];

        // Pagination
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = 20;

        // Need PaymentModel->getAll($filters, $page, $limit)
        // I will implement PaymentModel shortly.

        // Mock data for now if model not ready, but I will create model next.
        $payments = [];
        $total = 0;
        $total_pages = 1;

        if (isset($this->paymentModel)) {
            $result = $this->paymentModel->getAll($filters, $page, $limit);
            $payments = $result['data'];
            $total = $result['total'];
            $total_pages = $result['pages'];
        }

        $page_title = 'Quản lý Thanh toán';
        $content_file = VIEWS_PATH . '/admin/payments/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xem chi tiết thanh toán / In phiếu thu
     */
    public function show()
    {
        require_admin();

        if (empty($_GET['id'])) {
            redirect('?act=admin&module=payments');
        }

        $id = (int) $_GET['id'];
        $payment = $this->paymentModel->getById($id);

        if (!$payment) {
            set_error("Giao dịch không tồn tại.");
            redirect('?act=admin&module=payments');
        }

        $page_title = 'Chi tiết giao dịch: ' . $payment['receipt_number'];
        $content_file = VIEWS_PATH . '/admin/payments/show.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form tạo thanh toán (Thường gọi từ Booking)
     */
    public function create()
    {
        require_admin();
        // Usually we create payment from Booking detail.
        // But we can have a standalone form if needed.
        // For now, redirect to bookings list.
        redirect('?act=admin&module=bookings');
    }
}
