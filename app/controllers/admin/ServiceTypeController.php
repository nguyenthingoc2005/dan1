<?php
/**
 * ==============================================================================
 * SERVICE TYPE CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý loại dịch vụ - CHỈ ADMIN
 * Routing: ?act=admin&module=service-types&action=index
 * 
 * @version 1.0
 * @date 2024-12-02
 * ==============================================================================
 */

class ServiceTypeController
{
    private $db;
    private $serviceTypeModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/ServiceType.php';
        $this->serviceTypeModel = new ServiceType($pdo);
    }

    /**
     * Danh sách service types
     */
    public function index()
    {
        require_admin();

        // Get filters
        $filters = [];
        if (!empty($_GET['status']))
            $filters['status'] = sanitize($_GET['status']);
        if (!empty($_GET['search']))
            $filters['search'] = sanitize($_GET['search']);

        // Pagination
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $result = $this->serviceTypeModel->getAll($filters, $page, 20);

        $service_types = $result['data'];
        $total = $result['total'];
        $total_pages = $result['pages'];
        $current_page = $result['current_page'];

        $page_title = 'Quản lý loại dịch vụ';
        $content_file = VIEWS_PATH . '/admin/service-types/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form tạo service type
     */
    public function create()
    {
        require_admin();

        $page_title = 'Thêm loại dịch vụ mới';
        $content_file = VIEWS_PATH . '/admin/service-types/create.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý tạo service type
     */
    public function store()
    {
        require_admin();

        try {
            // Validate name
            if (empty($_POST['name'])) {
                throw new Exception("Vui lòng nhập tên loại dịch vụ.");
            }

            $name = sanitize($_POST['name']);

            // Auto-generate code from name
            $code = strtoupper(str_replace(' ', '_', remove_accents($name)));

            // Check for duplicate code
            $counter = 1;
            $original_code = $code;
            while ($this->serviceTypeModel->findByCode($code)) {
                $code = $original_code . '_' . $counter;
                $counter++;
            }

            // Prepare data
            $data = [
                'name' => $name,
                'code' => $code,
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            if ($this->serviceTypeModel->create($data)) {
                set_success("Tạo loại dịch vụ thành công!");
                redirect('?act=admin&module=service-types');
            } else {
                throw new Exception("Không thể tạo loại dịch vụ.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=service-types&action=create');
        }
    }

    /**
     * Form sửa service type
     */
    public function edit()
    {
        require_admin();

        if (empty($_GET['id'])) {
            set_error("Không tìm thấy loại dịch vụ.");
            redirect('?act=admin&module=service-types');
            return;
        }

        $service_type = $this->serviceTypeModel->findById((int) $_GET['id']);
        if (!$service_type) {
            set_error("Không tìm thấy loại dịch vụ.");
            redirect('?act=admin&module=service-types');
            return;
        }

        $page_title = 'Sửa loại dịch vụ';
        $content_file = VIEWS_PATH . '/admin/service-types/edit.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý update service type
     */
    public function update()
    {
        require_admin();

        try {
            if (empty($_POST['id'])) {
                throw new Exception("Không tìm thấy loại dịch vụ.");
            }

            $service_type_id = (int) $_POST['id'];
            $service_type = $this->serviceTypeModel->findById($service_type_id);

            if (!$service_type) {
                throw new Exception("Không tìm thấy loại dịch vụ.");
            }

            // Validate name
            if (empty($_POST['name'])) {
                throw new Exception("Vui lòng nhập tên loại dịch vụ.");
            }

            // KHÔNG cho sửa code (vì đã có trong system)

            // Prepare data
            $data = [
                'name' => sanitize($_POST['name']),
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            if ($this->serviceTypeModel->update($service_type_id, $data)) {
                set_success("Cập nhật thành công!");
            } else {
                throw new Exception("Không thể cập nhật.");
            }

            redirect('?act=admin&module=service-types');

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=service-types&action=edit&id=' . ($service_type_id ?? 0));
        }
    }

    /**
     * Xóa service type (soft delete với FK check)
     */
    public function delete()
    {
        require_admin();

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Không tìm thấy loại dịch vụ.");
            }

            $service_type_id = (int) $_GET['id'];

            // Delete sẽ tự động check FK constraint trong Model
            if ($this->serviceTypeModel->delete($service_type_id)) {
                set_success("Đã vô hiệu hóa loại dịch vụ.");
            } else {
                throw new Exception("Không thể xóa loại dịch vụ.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
        }

        redirect('?act=admin&module=service-types');
    }
}
