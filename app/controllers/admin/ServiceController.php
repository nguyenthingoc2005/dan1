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
            // Validate required fields
            if (empty($_POST['name']) || empty($_POST['service_type_id']) || empty($_POST['supplier_id'])) {
                throw new Exception("Vui lòng nhập tên dịch vụ, chọn loại dịch vụ và nhà cung cấp.");
            }

            // Validate numeric fields
            $unit_price = !empty($_POST['unit_price']) ? (float) $_POST['unit_price'] : 0;
            if ($unit_price < 0)
                throw new Exception("Đơn giá không hợp lệ.");

            $capacity = !empty($_POST['capacity']) ? (int) $_POST['capacity'] : null;
            if ($capacity !== null && $capacity < 0)
                throw new Exception("Sức chứa không hợp lệ.");

            // Prepare data
            $data = [
                'service_type_id' => (int) $_POST['service_type_id'],
                'supplier_id' => (int) $_POST['supplier_id'],
                'name' => sanitize($_POST['name']),
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'unit_price' => $unit_price,
                'capacity' => $capacity,
                'availability' => isset($_POST['availability']) ? $_POST['availability'] : 'available',
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

            // Validate numeric fields
            $unit_price = !empty($_POST['unit_price']) ? (float) $_POST['unit_price'] : 0;
            if ($unit_price < 0)
                throw new Exception("Đơn giá không hợp lệ.");

            $capacity = !empty($_POST['capacity']) ? (int) $_POST['capacity'] : null;
            if ($capacity !== null && $capacity < 0)
                throw new Exception("Sức chứa không hợp lệ.");

            // Prepare data
            $data = [
                'service_type_id' => (int) $_POST['service_type_id'],
                'supplier_id' => (int) $_POST['supplier_id'],
                'name' => sanitize($_POST['name']),
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'unit_price' => $unit_price,
                'capacity' => $capacity,
                'availability' => isset($_POST['availability']) ? $_POST['availability'] : 'available',
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
}
