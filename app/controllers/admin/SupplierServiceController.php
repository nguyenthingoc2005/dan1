<?php
/**
 * ==============================================================================
 * SUPPLIER-SERVICE RELATIONSHIP CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Trang quản lý quan hệ Nhà cung cấp ↔ Dịch vụ
 * Routing: ?act=admin&module=supplier-services
 * 
 * @version 1.0
 * @date 2024-12-03
 * ==============================================================================
 */

class SupplierServiceController
{
    private $pdo;
    private $supplierModel;
    private $serviceModel;
    private $serviceTypeModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        require_once MODELS_PATH . '/Supplier.php';
        require_once MODELS_PATH . '/Service.php';
        require_once MODELS_PATH . '/ServiceType.php';

        $this->supplierModel = new Supplier($pdo);
        $this->serviceModel = new Service($pdo);
        $this->serviceTypeModel = new ServiceType($pdo);
    }

    /**
     * List all supplier-service relationships
     */
    public function index()
    {
        require_admin();

        // Get all suppliers with pagination
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $result = $this->supplierModel->getAll(['status' => 'active'], $page, 50);
        $suppliers = $result['data'];

        // For each supplier, get their services
        foreach ($suppliers as &$supplier) {
            $services_result = $this->serviceModel->getAll([
                'supplier_id' => $supplier['id']
            ], 1, 100);
            $supplier['services'] = $services_result['data'];
        }

        $page_title = 'Quản lý Dịch vụ theo Nhà cung cấp';
        $content_file = VIEWS_PATH . '/admin/supplier-services/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form to add service to a supplier
     */
    public function addService()
    {
        require_admin();

        $supplier_id = $_GET['supplier_id'] ?? 0;
        $supplier = $this->supplierModel->findById($supplier_id);

        if (!$supplier) {
            set_error("Không tìm thấy nhà cung cấp");
            redirect('?act=admin&module=supplier-services');
            return;
        }

        // Get service types
        $service_types = $this->serviceTypeModel->getAll([], 1, 100)['data'];

        $page_title = "Thêm dịch vụ cho {$supplier['company_name']}";
        $content_file = VIEWS_PATH . '/admin/supplier-services/add.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Handle adding service to supplier
     */
    public function storeService()
    {
        require_admin();

        try {
            $supplier_id = (int) $_POST['supplier_id'];

            // Validate
            if (empty($_POST['service_type_id'])) {
                throw new Exception("Vui lòng chọn loại dịch vụ");
            }
            if (empty($_POST['name'])) {
                throw new Exception("Vui lòng nhập tên dịch vụ");
            }

            $data = [
                'supplier_id' => $supplier_id,
                'service_type_id' => (int) $_POST['service_type_id'],
                'name' => sanitize($_POST['name']),
                'unit' => sanitize($_POST['unit'] ?? ''),
                'estimated_price' => (float) ($_POST['estimated_price'] ?? 0),
                'notes' => sanitize($_POST['notes'] ?? ''),
                'status' => 'active'
            ];

            if ($this->serviceModel->create($data)) {
                set_success("Thêm dịch vụ thành công!");
            } else {
                throw new Exception("Không thể thêm dịch vụ");
            }
        } catch (Exception $e) {
            set_error($e->getMessage());
        }

        redirect('?act=admin&module=supplier-services');
    }

    /**
     * Soft delete a service
     */
    public function deleteService()
    {
        require_admin();

        try {
            $service_id = $_GET['service_id'] ?? 0;

            if (!$service_id) {
                throw new Exception("Không tìm thấy dịch vụ");
            }

            // Soft delete by setting status to inactive
            if ($this->serviceModel->update($service_id, ['status' => 'inactive'])) {
                set_success("Đã vô hiệu hóa dịch vụ");
            } else {
                throw new Exception("Không thể xóa dịch vụ");
            }
        } catch (Exception $e) {
            set_error($e->getMessage());
        }

        redirect('?act=admin&module=supplier-services');
    }
}
