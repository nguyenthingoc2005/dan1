<?php
/**
 * ==============================================================================
 * SERVICE CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý dịch vụ
 * Routing: ?act=admin&module=services&action=index
 * 
 * Validation:
 * - Service Type & Supplier: REQUIRED
 * - Name: REQUIRED
 * - Unit Price: Numeric
 * 
 * @version 1.0
 * @date 2024-12-02
 * ==============================================================================
 */

class ServiceController
{
    private $db;
    private $serviceModel;
    private $serviceTypeModel;
    private $supplierModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/Service.php';
        require_once MODELS_PATH . '/ServiceType.php';
        require_once MODELS_PATH . '/Supplier.php';

        $this->serviceModel = new Service($pdo);
        $this->serviceTypeModel = new ServiceType($pdo);
        $this->supplierModel = new Supplier($pdo);
    }

    /**
     * Danh sách services
     */
    public function index()
    {
        require_admin();

        // Get filters
        $filters = [];
        if (!empty($_GET['service_type_id']))
            $filters['service_type_id'] = (int) $_GET['service_type_id'];
        if (!empty($_GET['supplier_id']))
            $filters['supplier_id'] = (int) $_GET['supplier_id'];
        if (!empty($_GET['status']))
            $filters['status'] = sanitize($_GET['status']);
        if (!empty($_GET['search']))
            $filters['search'] = sanitize($_GET['search']);

        // Pagination
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $result = $this->serviceModel->getAll($filters, $page, 20);

        $services = $result['data'];
        $total = $result['total'];
        $total_pages = $result['pages'];
        $current_page = $result['current_page'];

        // Get data for filter dropdowns
        $service_types = $this->serviceTypeModel->getForDropdown();
        $suppliers = $this->supplierModel->getForDropdown();

        $page_title = 'Quản lý dịch vụ';
        $content_file = VIEWS_PATH . '/admin/services/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form tạo service
     */
    public function create()
    {
        require_admin();

        // Get dropdown data
        $service_types = $this->serviceTypeModel->getForDropdown();
        $suppliers = $this->supplierModel->getForDropdown();

        $page_title = 'Thêm dịch vụ mới';
        $content_file = VIEWS_PATH . '/admin/services/create.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý tạo service
     */
    public function store()
    {
        require_admin();

        try {
            require_once COMMON_PATH . '/ValidationHelper.php';

            // Validate required fields
            if (empty($_POST['name']) || empty($_POST['service_type_id']) || empty($_POST['supplier_id'])) {
                throw new Exception("Vui lòng nhập tên dịch vụ, chọn loại dịch vụ và nhà cung cấp.");
            }

            // Validate service_type_id exists and active
            $serviceType = $this->serviceTypeModel->findById($_POST['service_type_id']);
            if (!$serviceType) {
                throw new Exception("Loại dịch vụ không tồn tại trong hệ thống.");
            }
            if ($serviceType['status'] != 'active') {
                throw new Exception("Loại dịch vụ không khả dụng (đã bị vô hiệu hóa).");
            }

            // Validate supplier_id exists and active
            $supplier = $this->supplierModel->findById($_POST['supplier_id']);
            if (!$supplier) {
                throw new Exception("Nhà cung cấp không tồn tại trong hệ thống.");
            }
            if ($supplier['status'] != 'active') {
                throw new Exception("Nhà cung cấp không khả dụng (đã bị vô hiệu hóa).");
            }

            // Check duplicate service (same name + supplier + type)
            $existing = $this->serviceModel->findByNameAndSupplier(
                $_POST['name'],
                $_POST['supplier_id'],
                $_POST['service_type_id']
            );
            if ($existing) {
                throw new Exception("Dịch vụ này đã tồn tại cho nhà cung cấp này. Vui lòng chọn tên khác hoặc sửa dịch vụ hiện có.");
            }

            // Validate price
            $estimated_price = !empty($_POST['estimated_price']) ? (float) $_POST['estimated_price'] : 0;
            if ($estimated_price < 0) {
                throw new Exception("Giá dự kiến phải >= 0.");
            }
            if ($estimated_price > 0 && !ValidationHelper::validatePrice($estimated_price, 1000, 1000000000)) {
                throw new Exception("Giá dự kiến phải từ 1,000 VNĐ đến 1,000,000,000 VNĐ.");
            }

            // Validate unit (if provided)
            if (!empty($_POST['unit'])) {
                if (!ValidationHelper::validateServiceUnit($_POST['unit'], false)) {
                    throw new Exception("Đơn vị tính không hợp lệ. Các đơn vị hợp lệ: phòng/đêm, suất, xe/ngày, vé, người, bữa, ngày, giờ, km.");
                }
            }

            // Prepare data
            $data = [
                'service_type_id' => (int) $_POST['service_type_id'],
                'supplier_id' => (int) $_POST['supplier_id'],
                'name' => sanitize($_POST['name']),
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'unit' => isset($_POST['unit']) ? sanitize($_POST['unit']) : null,
                'estimated_price' => $estimated_price,
                'notes' => isset($_POST['notes']) ? sanitize($_POST['notes']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            if ($this->serviceModel->create($data)) {
                set_success("Tạo dịch vụ thành công!");
                redirect('?act=admin&module=services');
            } else {
                throw new Exception("Không thể tạo dịch vụ.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=services&action=create');
        }
    }

    /**
     * Form sửa service
     */
    public function edit()
    {
        require_admin();

        if (empty($_GET['id'])) {
            set_error("Không tìm thấy dịch vụ.");
            redirect('?act=admin&module=services');
            return;
        }

        $service = $this->serviceModel->findById((int) $_GET['id']);
        if (!$service) {
            set_error("Không tìm thấy dịch vụ.");
            redirect('?act=admin&module=services');
            return;
        }

        // Get dropdown data
        $service_types = $this->serviceTypeModel->getForDropdown();
        $suppliers = $this->supplierModel->getForDropdown();

        $page_title = 'Sửa dịch vụ';
        $content_file = VIEWS_PATH . '/admin/services/edit.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý update service
     */
    public function update()
    {
        require_admin();

        try {
            require_once COMMON_PATH . '/ValidationHelper.php';

            if (empty($_POST['id'])) {
                throw new Exception("Không tìm thấy dịch vụ.");
            }

            $service_id = (int) $_POST['id'];
            $service = $this->serviceModel->findById($service_id);

            if (!$service) {
                throw new Exception("Không tìm thấy dịch vụ.");
            }

            // Validate required fields
            if (empty($_POST['name']) || empty($_POST['service_type_id']) || empty($_POST['supplier_id'])) {
                throw new Exception("Vui lòng nhập tên dịch vụ, chọn loại dịch vụ và nhà cung cấp.");
            }

            // Validate service_type_id exists and active
            $serviceType = $this->serviceTypeModel->findById($_POST['service_type_id']);
            if (!$serviceType) {
                throw new Exception("Loại dịch vụ không tồn tại trong hệ thống.");
            }
            if ($serviceType['status'] != 'active') {
                throw new Exception("Loại dịch vụ không khả dụng (đã bị vô hiệu hóa).");
            }

            // Validate supplier_id exists and active
            $supplier = $this->supplierModel->findById($_POST['supplier_id']);
            if (!$supplier) {
                throw new Exception("Nhà cung cấp không tồn tại trong hệ thống.");
            }
            if ($supplier['status'] != 'active') {
                throw new Exception("Nhà cung cấp không khả dụng (đã bị vô hiệu hóa).");
            }

            // Check duplicate (exclude current service)
            $existing = $this->serviceModel->findByNameAndSupplier(
                $_POST['name'],
                $_POST['supplier_id'],
                $_POST['service_type_id'],
                $service_id
            );
            if ($existing) {
                throw new Exception("Dịch vụ này đã tồn tại cho nhà cung cấp này. Vui lòng chọn tên khác.");
            }

            // Check if service is being used in bookings (warn if changing supplier/type)
            if ($service['supplier_id'] != $_POST['supplier_id'] || $service['service_type_id'] != $_POST['service_type_id']) {
                $check_booking = $this->db->prepare("
                    SELECT COUNT(*) as count FROM booking_services WHERE service_id = :id
                ");
                $check_booking->execute(['id' => $service_id]);
                $booking_count = $check_booking->fetch()['count'];
                
                if ($booking_count > 0) {
                    throw new Exception("Không thể thay đổi nhà cung cấp/loại dịch vụ vì dịch vụ này đang được sử dụng trong {$booking_count} booking.");
                }
            }

            // Validate price
            $estimated_price = !empty($_POST['estimated_price']) ? (float) $_POST['estimated_price'] : 0;
            if ($estimated_price < 0) {
                throw new Exception("Giá dự kiến phải >= 0.");
            }
            if ($estimated_price > 0 && !ValidationHelper::validatePrice($estimated_price, 1000, 1000000000)) {
                throw new Exception("Giá dự kiến phải từ 1,000 VNĐ đến 1,000,000,000 VNĐ.");
            }

            // Validate unit (if provided)
            if (!empty($_POST['unit'])) {
                if (!ValidationHelper::validateServiceUnit($_POST['unit'], false)) {
                    throw new Exception("Đơn vị tính không hợp lệ. Các đơn vị hợp lệ: phòng/đêm, suất, xe/ngày, vé, người, bữa, ngày, giờ, km.");
                }
            }

            // Prepare data
            $data = [
                'service_type_id' => (int) $_POST['service_type_id'],
                'supplier_id' => (int) $_POST['supplier_id'],
                'name' => sanitize($_POST['name']),
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'unit' => isset($_POST['unit']) ? sanitize($_POST['unit']) : null,
                'estimated_price' => $estimated_price,
                'notes' => isset($_POST['notes']) ? sanitize($_POST['notes']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            if ($this->serviceModel->update($service_id, $data)) {
                set_success("Cập nhật thành công!");
            } else {
                throw new Exception("Không thể cập nhật.");
            }

            redirect('?act=admin&module=services');

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=services&action=edit&id=' . ($service_id ?? 0));
        }
    }

    /**
     * Xóa service (soft delete)
     */
    public function delete()
    {
        require_admin();

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Không tìm thấy dịch vụ.");
            }

            $service_id = (int) $_GET['id'];

            if ($this->serviceModel->delete($service_id)) {
                set_success("Đã vô hiệu hóa dịch vụ.");
            } else {
                throw new Exception("Không thể xóa dịch vụ.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
        }

        redirect('?act=admin&module=services');
    }

    /**
     * AJAX: Get service info (for tour creation form)
     */
    public function getServiceInfo()
    {
        require_admin();

        header('Content-Type: application/json');

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Thiếu ID dịch vụ.");
            }

            $service_id = (int) $_GET['id'];
            $service = $this->serviceModel->findById($service_id);

            if (!$service) {
                throw new Exception("Dịch vụ không tồn tại.");
            }

            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $service['id'],
                    'name' => $service['name'],
                    'unit' => $service['unit'] ?? '',
                    'estimated_price' => $service['estimated_price'] ?? 0,
                    'service_type_id' => $service['service_type_id'],
                    'supplier_id' => $service['supplier_id']
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
}
