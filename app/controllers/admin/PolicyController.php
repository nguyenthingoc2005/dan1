<?php
/**
 * ==============================================================================
 * POLICY CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý chính sách (Policies) - CRUD đầy đủ
 * Routing: ?act=admin&module=policies&action=index
 * 
 * @version 1.0
 * @date 2024-12-06
 * ==============================================================================
 */

class PolicyController
{
    private $db;
    private $policyModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/Policy.php';
        $this->policyModel = new Policy($pdo);
    }

    /**
     * Danh sách policies
     */
    public function index()
    {
        require_admin();

        $filters = [];
        if (!empty($_GET['status'])) {
            $filters['status'] = sanitize($_GET['status']);
        }
        if (!empty($_GET['policy_type'])) {
            $filters['policy_type'] = sanitize($_GET['policy_type']);
        }

        $policies = $this->policyModel->getAll($filters);

        // Đếm số tour đang sử dụng mỗi policy
        foreach ($policies as &$policy) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM tour_policies WHERE policy_id = :id");
            $stmt->execute(['id' => $policy['id']]);
            $result = $stmt->fetch();
            $policy['tour_count'] = $result['count'] ?? 0;
        }

        $page_title = 'Quản lý chính sách';
        $content_file = VIEWS_PATH . '/admin/policies/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form tạo policy
     */
    public function create()
    {
        require_admin();

        $page_title = 'Thêm chính sách mới';
        $content_file = VIEWS_PATH . '/admin/policies/create.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý tạo policy
     */
    public function store()
    {
        require_admin();

        try {
            if (empty($_POST['name'])) {
                throw new Exception("Vui lòng nhập tên chính sách.");
            }

            if (empty($_POST['content'])) {
                throw new Exception("Vui lòng nhập nội dung chính sách.");
            }

            $data = [
                'name' => sanitize($_POST['name']),
                'description' => !empty($_POST['description']) ? sanitize($_POST['description']) : null,
                'policy_type' => !empty($_POST['policy_type']) ? sanitize($_POST['policy_type']) : null,
                'content' => $_POST['content'], // Rich text, không sanitize
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            if ($this->policyModel->create($data)) {
                set_success("Tạo chính sách thành công!");
                redirect('?act=admin&module=policies');
            } else {
                throw new Exception("Không thể tạo chính sách.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=policies&action=create');
        }
    }

    /**
     * Form sửa policy
     */
    public function edit()
    {
        require_admin();

        if (empty($_GET['id'])) {
            set_error("Không tìm thấy chính sách.");
            redirect('?act=admin&module=policies');
            return;
        }

        $policy = $this->policyModel->findById((int) $_GET['id']);
        if (!$policy) {
            set_error("Không tìm thấy chính sách.");
            redirect('?act=admin&module=policies');
            return;
        }

        $page_title = 'Sửa chính sách';
        $content_file = VIEWS_PATH . '/admin/policies/edit.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý update policy
     */
    public function update()
    {
        require_admin();

        try {
            if (empty($_POST['id'])) {
                throw new Exception("Không tìm thấy chính sách.");
            }

            $policy_id = (int) $_POST['id'];
            $policy = $this->policyModel->findById($policy_id);

            if (!$policy) {
                throw new Exception("Không tìm thấy chính sách.");
            }

            if (empty($_POST['name'])) {
                throw new Exception("Vui lòng nhập tên chính sách.");
            }

            if (empty($_POST['content'])) {
                throw new Exception("Vui lòng nhập nội dung chính sách.");
            }

            $data = [
                'name' => sanitize($_POST['name']),
                'description' => !empty($_POST['description']) ? sanitize($_POST['description']) : null,
                'policy_type' => !empty($_POST['policy_type']) ? sanitize($_POST['policy_type']) : null,
                'content' => $_POST['content'], // Rich text, không sanitize
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            if ($this->policyModel->update($policy_id, $data)) {
                set_success("Cập nhật chính sách thành công!");
            } else {
                throw new Exception("Không thể cập nhật.");
            }

            redirect('?act=admin&module=policies');

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=policies&action=edit&id=' . ($policy_id ?? 0));
        }
    }

    /**
     * Xóa policy
     */
    public function delete()
    {
        require_admin();

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Không tìm thấy chính sách.");
            }

            $policy_id = (int) $_GET['id'];
            $policy = $this->policyModel->findById($policy_id);

            if (!$policy) {
                throw new Exception("Không tìm thấy chính sách.");
            }

            // Kiểm tra xem policy có đang được sử dụng bởi tour nào không
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM tour_policies WHERE policy_id = :id");
            $stmt->execute(['id' => $policy_id]);
            $result = $stmt->fetch();
            $tour_count = $result['count'] ?? 0;

            if ($tour_count > 0) {
                throw new Exception("Không thể xóa chính sách này vì đang được sử dụng bởi {$tour_count} tour. Vui lòng vô hiệu hóa thay vì xóa.");
            }

            if ($this->policyModel->delete($policy_id)) {
                set_success("Đã xóa chính sách thành công!");
            } else {
                throw new Exception("Không thể xóa chính sách.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
        }

        redirect('?act=admin&module=policies');
    }
}

