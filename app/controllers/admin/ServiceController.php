<?php
/**
 * ==============================================================================
 * SERVICE CONTROLLER (ADMIN) - ĐÃ SỬA LẠI
 * ==============================================================================
 * 
 * Quản lý dịch vụ
 * Routing: ?act=admin&module=services&action=index
 * 
 * Validation:
 * - Service Provider: REQUIRED
 * - Name: REQUIRED
 * - Service Type: OPTIONAL
 * - Unit: OPTIONAL
 * 
 * @version 2.0
 * @date 2024-12-06
 * ==============================================================================
 */

class ServiceController
{
    private $db;
    private $serviceModel;
    private $serviceTypeModel;
    private $serviceProviderModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/Service.php';
        require_once MODELS_PATH . '/ServiceType.php';
        require_once MODELS_PATH . '/ServiceProvider.php';

        $this->serviceModel = new Service($pdo);
        $this->serviceTypeModel = new ServiceType($pdo);
        $this->serviceProviderModel = new ServiceProvider($pdo);
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
        if (!empty($_GET['service_provider_id']))
            $filters['service_provider_id'] = (int) $_GET['service_provider_id'];
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
        $service_providers = $this->serviceProviderModel->getForDropdown();

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
        $service_providers = $this->serviceProviderModel->getForDropdown();

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
            if (empty($_POST['name']) || empty($_POST['service_provider_id'])) {
                throw new Exception("Vui lòng nhập tên dịch vụ và chọn nhà cung cấp dịch vụ.");
            }

            // Validate service_provider_id exists and active
            $serviceProvider = $this->serviceProviderModel->findById($_POST['service_provider_id']);
            if (!$serviceProvider) {
                throw new Exception("Nhà cung cấp dịch vụ không tồn tại trong hệ thống.");
            }
            if ($serviceProvider['status'] != 'active') {
                throw new Exception("Nhà cung cấp dịch vụ không khả dụng (đã bị vô hiệu hóa).");
            }

            // Validate service_type_id if provided
            if (!empty($_POST['service_type_id'])) {
                $serviceType = $this->serviceTypeModel->findById($_POST['service_type_id']);
                if (!$serviceType) {
                    throw new Exception("Loại dịch vụ không tồn tại trong hệ thống.");
                }
                if ($serviceType['status'] != 'active') {
                    throw new Exception("Loại dịch vụ không khả dụng (đã bị vô hiệu hóa).");
                }
            }

            // Check duplicate service (same name + provider + type)
            $existing = $this->serviceModel->findByNameAndServiceProvider(
                $_POST['name'],
                $_POST['service_provider_id'],
                !empty($_POST['service_type_id']) ? (int) $_POST['service_type_id'] : null
            );
            if ($existing) {
                throw new Exception("Dịch vụ này đã tồn tại cho nhà cung cấp này. Vui lòng chọn tên khác hoặc sửa dịch vụ hiện có.");
            }

            // Validate unit (if provided)
            if (!empty($_POST['unit'])) {
                if (!ValidationHelper::validateServiceUnit($_POST['unit'], false)) {
                    throw new Exception("Đơn vị tính không hợp lệ. Các đơn vị hợp lệ: phòng/đêm, suất, xe/ngày, vé, người, bữa, ngày, giờ, km.");
                }
            }

            // Prepare data
            $data = [
                'service_provider_id' => (int) $_POST['service_provider_id'],
                'service_type_id' => !empty($_POST['service_type_id']) ? (int) $_POST['service_type_id'] : null,
                'name' => sanitize($_POST['name']),
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'unit' => isset($_POST['unit']) ? sanitize($_POST['unit']) : null,
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
        $service_providers = $this->serviceProviderModel->getForDropdown();

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
            if (empty($_POST['name']) || empty($_POST['service_provider_id'])) {
                throw new Exception("Vui lòng nhập tên dịch vụ và chọn nhà cung cấp dịch vụ.");
            }

            // Validate service_provider_id exists and active
            $serviceProvider = $this->serviceProviderModel->findById($_POST['service_provider_id']);
            if (!$serviceProvider) {
                throw new Exception("Nhà cung cấp dịch vụ không tồn tại trong hệ thống.");
            }
            if ($serviceProvider['status'] != 'active') {
                throw new Exception("Nhà cung cấp dịch vụ không khả dụng (đã bị vô hiệu hóa).");
            }

            // Validate service_type_id if provided
            if (!empty($_POST['service_type_id'])) {
                $serviceType = $this->serviceTypeModel->findById($_POST['service_type_id']);
                if (!$serviceType) {
                    throw new Exception("Loại dịch vụ không tồn tại trong hệ thống.");
                }
                if ($serviceType['status'] != 'active') {
                    throw new Exception("Loại dịch vụ không khả dụng (đã bị vô hiệu hóa).");
                }
            }

            // Check duplicate (exclude current service)
            $existing = $this->serviceModel->findByNameAndServiceProvider(
                $_POST['name'],
                $_POST['service_provider_id'],
                !empty($_POST['service_type_id']) ? (int) $_POST['service_type_id'] : null,
                $service_id
            );
            if ($existing) {
                throw new Exception("Dịch vụ này đã tồn tại cho nhà cung cấp này. Vui lòng chọn tên khác.");
            }

            // Validate unit (if provided)
            if (!empty($_POST['unit'])) {
                if (!ValidationHelper::validateServiceUnit($_POST['unit'], false)) {
                    throw new Exception("Đơn vị tính không hợp lệ. Các đơn vị hợp lệ: phòng/đêm, suất, xe/ngày, vé, người, bữa, ngày, giờ, km.");
                }
            }

            // Prepare data
            $data = [
                'service_provider_id' => (int) $_POST['service_provider_id'],
                'service_type_id' => !empty($_POST['service_type_id']) ? (int) $_POST['service_type_id'] : null,
                'name' => sanitize($_POST['name']),
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'unit' => isset($_POST['unit']) ? sanitize($_POST['unit']) : null,
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
                    'service_type_id' => $service['service_type_id'] ?? null,
                    'service_provider_id' => $service['service_provider_id']
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
