<?php
/**
 * ==============================================================================
 * BOOKING SERVICE CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý dịch vụ thực tế cho booking
 * - Thêm/sửa/xóa dịch vụ
 * - Theo dõi thanh toán cho nhà cung cấp
 * 
 * Routing: ?act=admin&module=booking-services&action=index&booking_id=X
 * 
 * @version 1.0
 * @date 2024-12-03
 * ==============================================================================
 */

class BookingServiceController
{
    private $db;
    private $bookingServiceModel;
    private $bookingModel;
    private $serviceModel;
    private $supplierModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        
        require_once MODELS_PATH . '/BookingService.php';
        require_once MODELS_PATH . '/Booking.php';
        require_once MODELS_PATH . '/Service.php';
        require_once MODELS_PATH . '/Supplier.php';
        
        $this->bookingServiceModel = new BookingService($pdo);
        $this->bookingModel = new Booking($pdo);
        $this->serviceModel = new Service($pdo);
        $this->supplierModel = new Supplier($pdo);
    }

    /**
     * Danh sách dịch vụ của booking
     */
    public function index()
    {
        require_admin();
        
        $bookingId = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;
        
        if (!$bookingId) {
            set_error("Thiếu Booking ID");
            redirect('?act=admin&module=bookings');
            return;
        }
        
        // Get booking info
        $booking = $this->bookingModel->getById($bookingId);
        if (!$booking) {
            set_error("Booking không tồn tại");
            redirect('?act=admin&module=bookings');
            return;
        }
        
        // Get services for this booking
        $services = $this->bookingServiceModel->getByBookingId($bookingId);
        
        // Get totals
        $totals = $this->bookingServiceModel->getTotalCostByBooking($bookingId);
        
        // Get available services and suppliers for adding new
        $availableServices = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];
        $suppliers = $this->supplierModel->getForDropdown();
        
        $page_title = 'Dịch vụ - ' . $booking['booking_code'];
        $content_file = VIEWS_PATH . '/admin/booking-services/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Thêm dịch vụ mới cho booking
     */
    public function store()
    {
        require_admin();
        
        try {
            $bookingId = (int) ($_POST['booking_id'] ?? 0);
            
            if (!$bookingId) {
                throw new Exception("Thiếu Booking ID");
            }
            
            // Check booking exists and is not cancelled
            $booking = $this->bookingModel->getById($bookingId);
            if (!$booking) {
                throw new Exception("Booking không tồn tại");
            }
            if (in_array($booking['payment_status'], ['cancelled', 'rejected', 'refunded'])) {
                throw new Exception("Không thể thêm dịch vụ cho booking đã hủy/từ chối");
            }
            
            // Prepare data
            $data = [
                'booking_id' => $bookingId,
                'service_id' => (int) $_POST['service_id'],
                'supplier_id' => (int) $_POST['supplier_id'],
                'service_name' => sanitize($_POST['service_name'] ?? ''),
                'quantity' => (int) ($_POST['quantity'] ?? 1),
                'unit' => sanitize($_POST['unit'] ?? ''),
                'unit_price' => (float) ($_POST['unit_price'] ?? 0),
                'total_price' => (float) ($_POST['total_price'] ?? 0),
                'service_date' => !empty($_POST['service_date']) ? $_POST['service_date'] : null,
                'from_date' => !empty($_POST['from_date']) ? $_POST['from_date'] : null,
                'to_date' => !empty($_POST['to_date']) ? $_POST['to_date'] : null,
                'notes' => sanitize($_POST['notes'] ?? ''),
                'created_by' => $_SESSION['user_id'] ?? 1
            ];
            
            // Auto-calculate total_price if not provided
            if ($data['total_price'] <= 0) {
                $data['total_price'] = $data['quantity'] * $data['unit_price'];
            }
            
            // Validate
            $validation = $this->bookingServiceModel->validate($data);
            if (!$validation['valid']) {
                $firstError = reset($validation['errors']);
                throw new Exception($firstError);
            }
            
            // Validate service exists
            $service = $this->serviceModel->findById($data['service_id']);
            if (!$service) {
                throw new Exception("Dịch vụ không tồn tại");
            }
            
            // Validate supplier exists
            $supplier = $this->supplierModel->findById($data['supplier_id']);
            if (!$supplier) {
                throw new Exception("Nhà cung cấp không tồn tại");
            }
            
            // Auto-fill service name if empty
            if (empty($data['service_name'])) {
                $data['service_name'] = $service['name'];
            }
            
            // Create
            $id = $this->bookingServiceModel->create($data);
            
            if ($id) {
                set_success("Thêm dịch vụ thành công!");
            } else {
                throw new Exception("Không thể thêm dịch vụ");
            }
            
        } catch (Exception $e) {
            set_error($e->getMessage());
        }
        
        redirect('?act=admin&module=booking-services&booking_id=' . ($bookingId ?? 0));
    }

    /**
     * Cập nhật dịch vụ
     */
    public function update()
    {
        require_admin();
        
        $bookingId = 0;
        
        try {
            $id = (int) ($_POST['id'] ?? 0);
            $bookingId = (int) ($_POST['booking_id'] ?? 0);
            
            if (!$id) {
                throw new Exception("Thiếu ID dịch vụ");
            }
            
            // Check service exists
            $existing = $this->bookingServiceModel->getById($id);
            if (!$existing) {
                throw new Exception("Dịch vụ không tồn tại");
            }
            
            $bookingId = $existing['booking_id'];
            
            // Prepare data
            $data = [
                'service_name' => sanitize($_POST['service_name'] ?? ''),
                'quantity' => (int) ($_POST['quantity'] ?? 1),
                'unit' => sanitize($_POST['unit'] ?? ''),
                'unit_price' => (float) ($_POST['unit_price'] ?? 0),
                'total_price' => (float) ($_POST['total_price'] ?? 0),
                'service_date' => !empty($_POST['service_date']) ? $_POST['service_date'] : null,
                'from_date' => !empty($_POST['from_date']) ? $_POST['from_date'] : null,
                'to_date' => !empty($_POST['to_date']) ? $_POST['to_date'] : null,
                'notes' => sanitize($_POST['notes'] ?? '')
            ];
            
            // Auto-calculate total_price if not provided
            if ($data['total_price'] <= 0) {
                $data['total_price'] = $data['quantity'] * $data['unit_price'];
            }
            
            // Update
            if ($this->bookingServiceModel->update($id, $data)) {
                set_success("Cập nhật dịch vụ thành công!");
            } else {
                throw new Exception("Không thể cập nhật dịch vụ");
            }
            
        } catch (Exception $e) {
            set_error($e->getMessage());
        }
        
        redirect('?act=admin&module=booking-services&booking_id=' . $bookingId);
    }

    /**
     * Xóa dịch vụ
     */
    public function delete()
    {
        require_admin();
        
        $bookingId = 0;
        
        try {
            $id = (int) ($_GET['id'] ?? 0);
            
            if (!$id) {
                throw new Exception("Thiếu ID dịch vụ");
            }
            
            // Get booking ID before delete
            $existing = $this->bookingServiceModel->getById($id);
            if (!$existing) {
                throw new Exception("Dịch vụ không tồn tại");
            }
            
            $bookingId = $existing['booking_id'];
            
            // Check if already paid
            if ($existing['paid_amount'] > 0) {
                throw new Exception("Không thể xóa dịch vụ đã thanh toán. Hãy hoàn tiền trước.");
            }
            
            if ($this->bookingServiceModel->delete($id)) {
                set_success("Đã xóa dịch vụ!");
            } else {
                throw new Exception("Không thể xóa dịch vụ");
            }
            
        } catch (Exception $e) {
            set_error($e->getMessage());
        }
        
        redirect('?act=admin&module=booking-services&booking_id=' . $bookingId);
    }

    /**
     * AJAX: Get service info for auto-fill
     */
    public function getServiceInfo()
    {
        require_admin();
        header('Content-Type: application/json');
        
        try {
            $serviceId = (int) ($_GET['service_id'] ?? 0);
            
            if (!$serviceId) {
                throw new Exception("Thiếu Service ID");
            }
            
            $service = $this->serviceModel->findById($serviceId);
            
            if (!$service) {
                throw new Exception("Dịch vụ không tồn tại");
            }
            
            echo json_encode([
                'success' => true,
                'service' => [
                    'id' => $service['id'],
                    'name' => $service['name'],
                    'supplier_id' => $service['supplier_id'],
                    'unit' => $service['unit'],
                    'estimated_price' => $service['estimated_price']
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
     * Copy dịch vụ từ tour template
     */
    public function copyFromTour()
    {
        require_admin();
        
        try {
            $bookingId = (int) ($_POST['booking_id'] ?? 0);
            
            if (!$bookingId) {
                throw new Exception("Thiếu Booking ID");
            }
            
            $booking = $this->bookingModel->getById($bookingId);
            if (!$booking) {
                throw new Exception("Booking không tồn tại");
            }
            
            // Get participant count
            $participantCount = $booking['adult_count'] + $booking['child_count'];
            
            // Copy services
            if ($this->bookingServiceModel->copyFromTourServices($bookingId, $booking['tour_id'], $participantCount)) {
                set_success("Đã copy dịch vụ từ tour template!");
            } else {
                throw new Exception("Không thể copy dịch vụ");
            }
            
        } catch (Exception $e) {
            set_error($e->getMessage());
        }
        
        redirect('?act=admin&module=booking-services&booking_id=' . ($bookingId ?? 0));
    }
}

