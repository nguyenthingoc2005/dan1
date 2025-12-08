<?php
require_once 'app/models/CancellationPolicy.php';

class CancellationPolicyController
{
    private $policyModel;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->policyModel = new CancellationPolicy($pdo);
    }

    /**
     * Danh sách policies
     */
    public function index()
    {
        require_admin();

        $status = $_GET['status'] ?? '';
        $filters = [];
        if ($status) {
            $filters['status'] = $status;
        }

        // Chỉ gọi getAll() 1 lần
        $policies = $this->policyModel->getAll($filters);
        
        // Debug: Log số lượng policies (có thể xóa sau)
        // error_log("CancellationPolicyController::index() - Found " . count($policies) . " policies");

        $page_title = 'Quản lý Chính sách Hủy';
        $content_file = 'app/views/admin/cancellation-policies/index.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    /**
     * Form tạo mới
     */
    public function create()
    {
        require_admin();

        $page_title = 'Tạo Chính sách Hủy mới';
        $content_file = 'app/views/admin/cancellation-policies/create.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    /**
     * Lưu policy mới
     */
    public function store()
    {
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('?act=admin&module=cancellation-policies');
            return;
        }

        require_csrf_token();

        try {
            $data = [
                'name' => sanitize($_POST['name'] ?? ''),
                'description' => sanitize($_POST['description'] ?? ''),
                'days_before' => (int) ($_POST['days_before'] ?? 0),
                'fee_percentage' => (float) ($_POST['fee_percentage'] ?? 0),
                'status' => $_POST['status'] ?? 'active'
            ];

            // Validation
            if (empty($data['name'])) {
                throw new Exception('Tên policy không được để trống');
            }
            if ($data['days_before'] < 0) {
                throw new Exception('Số ngày trước khởi hành phải >= 0');
            }
            if ($data['fee_percentage'] < 0 || $data['fee_percentage'] > 100) {
                throw new Exception('Phí hủy phải từ 0% đến 100%');
            }

            $this->policyModel->create($data);
            set_success('Đã tạo chính sách hủy thành công!');
            redirect('?act=admin&module=cancellation-policies');

        } catch (Exception $e) {
            set_error('Lỗi: ' . $e->getMessage());
            redirect('?act=admin&module=cancellation-policies&action=create');
        }
    }

    /**
     * Form chỉnh sửa
     */
    public function edit()
    {
        require_admin();

        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) {
            set_error('Thiếu thông tin policy');
            redirect('?act=admin&module=cancellation-policies');
            return;
        }

        $policy = $this->policyModel->getById($id);
        if (!$policy) {
            set_error('Policy không tồn tại');
            redirect('?act=admin&module=cancellation-policies');
            return;
        }

        $page_title = 'Chỉnh sửa Chính sách Hủy';
        $content_file = 'app/views/admin/cancellation-policies/edit.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    /**
     * Cập nhật policy
     */
    public function update()
    {
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('?act=admin&module=cancellation-policies');
            return;
        }

        require_csrf_token();

        try {
            $id = (int) ($_POST['id'] ?? 0);
            if (!$id) {
                throw new Exception('Thiếu thông tin policy');
            }

            $data = [
                'name' => sanitize($_POST['name'] ?? ''),
                'description' => sanitize($_POST['description'] ?? ''),
                'days_before' => (int) ($_POST['days_before'] ?? 0),
                'fee_percentage' => (float) ($_POST['fee_percentage'] ?? 0),
                'status' => $_POST['status'] ?? 'active'
            ];

            // Validation
            if (empty($data['name'])) {
                throw new Exception('Tên policy không được để trống');
            }
            if ($data['days_before'] < 0) {
                throw new Exception('Số ngày trước khởi hành phải >= 0');
            }
            if ($data['fee_percentage'] < 0 || $data['fee_percentage'] > 100) {
                throw new Exception('Phí hủy phải từ 0% đến 100%');
            }

            $this->policyModel->update($id, $data);
            set_success('Đã cập nhật chính sách hủy thành công!');
            redirect('?act=admin&module=cancellation-policies');

        } catch (Exception $e) {
            set_error('Lỗi: ' . $e->getMessage());
            redirect('?act=admin&module=cancellation-policies&action=edit&id=' . ($_POST['id'] ?? ''));
        }
    }

    /**
     * Xóa policy
     */
    public function delete()
    {
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('?act=admin&module=cancellation-policies');
            return;
        }

        require_csrf_token();

        try {
            $id = (int) ($_POST['id'] ?? 0);
            if (!$id) {
                throw new Exception('Thiếu thông tin policy');
            }

            $this->policyModel->delete($id);
            set_success('Đã xóa chính sách hủy thành công!');
            redirect('?act=admin&module=cancellation-policies');

        } catch (Exception $e) {
            set_error('Lỗi: ' . $e->getMessage());
            redirect('?act=admin&module=cancellation-policies');
        }
    }

    /**
     * Toggle status
     */
    public function toggleStatus()
    {
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('?act=admin&module=cancellation-policies');
            return;
        }

        require_csrf_token();

        try {
            $id = (int) ($_POST['id'] ?? 0);
            if (!$id) {
                throw new Exception('Thiếu thông tin policy');
            }

            $this->policyModel->toggleStatus($id);
            set_success('Đã thay đổi trạng thái thành công!');
            redirect('?act=admin&module=cancellation-policies');

        } catch (Exception $e) {
            set_error('Lỗi: ' . $e->getMessage());
            redirect('?act=admin&module=cancellation-policies');
        }
    }
}

