<?php
/**
 * ==============================================================================
 * DISCOUNT CODE CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý mã giảm giá - CRUD đầy đủ
 * Routing: ?act=admin&module=discount-codes&action=index
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class DiscountCodeController
{
    private $db;
    private $discountCodeModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/DiscountCode.php';
        $this->discountCodeModel = new DiscountCode($pdo);
    }

    /**
     * Danh sách mã giảm giá
     */
    public function index()
    {
        require_admin();

        $filters = [];
        if (!empty($_GET['status'])) {
            $filters['status'] = sanitize($_GET['status']);
        }
        if (!empty($_GET['search'])) {
            $filters['search'] = sanitize($_GET['search']);
        }

        $discountCodes = $this->discountCodeModel->getAll($filters);

        // Tính số booking đã sử dụng mỗi mã
        foreach ($discountCodes as &$code) {
            $code['booking_count'] = $this->discountCodeModel->getUsageCount($code['code']);
        }

        $page_title = 'Quản lý mã giảm giá';
        $content_file = VIEWS_PATH . '/admin/discount-codes/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form tạo mã giảm giá
     */
    public function create()
    {
        require_admin();

        $page_title = 'Thêm mã giảm giá mới';
        $content_file = VIEWS_PATH . '/admin/discount-codes/create.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý tạo mã giảm giá
     */
    public function store()
    {
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf_token();

            try {
                if (empty($_POST['code'])) {
                    throw new Exception("Vui lòng nhập mã giảm giá.");
                }

                if (empty($_POST['discount_type'])) {
                    throw new Exception("Vui lòng chọn loại giảm giá.");
                }

                if (empty($_POST['discount_value']) || $_POST['discount_value'] <= 0) {
                    throw new Exception("Vui lòng nhập giá trị giảm giá hợp lệ.");
                }

                // Validate discount_value theo type
                if ($_POST['discount_type'] === 'percentage' && $_POST['discount_value'] > 100) {
                    throw new Exception("Giảm giá theo phần trăm không được vượt quá 100%.");
                }

                // Kiểm tra mã đã tồn tại chưa
                if ($this->discountCodeModel->codeExists($_POST['code'])) {
                    throw new Exception("Mã giảm giá đã tồn tại. Vui lòng chọn mã khác.");
                }

                // Validate dates
                if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
                    if ($_POST['start_date'] > $_POST['end_date']) {
                        throw new Exception("Ngày bắt đầu phải trước hoặc bằng ngày kết thúc.");
                    }
                }

                $data = [
                    'code' => $_POST['code'],
                    'name' => !empty($_POST['name']) ? sanitize($_POST['name']) : null,
                    'discount_type' => $_POST['discount_type'],
                    'discount_value' => (float) $_POST['discount_value'],
                    'min_purchase' => !empty($_POST['min_purchase']) ? (float) $_POST['min_purchase'] : 0,
                    'start_date' => !empty($_POST['start_date']) ? $_POST['start_date'] : null,
                    'end_date' => !empty($_POST['end_date']) ? $_POST['end_date'] : null,
                    'usage_limit' => !empty($_POST['usage_limit']) ? (int) $_POST['usage_limit'] : 0,
                    'status' => isset($_POST['status']) ? $_POST['status'] : 'active',
                    'created_by' => $_SESSION['user_id'] ?? null
                ];

                if ($this->discountCodeModel->create($data)) {
                    set_success("Tạo mã giảm giá thành công!");
                    redirect('?act=admin&module=discount-codes');
                } else {
                    throw new Exception("Không thể tạo mã giảm giá.");
                }

            } catch (Exception $e) {
                set_error($e->getMessage());
                $_SESSION['old'] = $_POST;
                redirect('?act=admin&module=discount-codes&action=create');
            }
        }
    }

    /**
     * Form sửa mã giảm giá
     */
    public function edit()
    {
        require_admin();

        if (empty($_GET['id'])) {
            set_error("Không tìm thấy mã giảm giá.");
            redirect('?act=admin&module=discount-codes');
            return;
        }

        $discountCode = $this->discountCodeModel->findById((int) $_GET['id']);
        if (!$discountCode) {
            set_error("Không tìm thấy mã giảm giá.");
            redirect('?act=admin&module=discount-codes');
            return;
        }

        $page_title = 'Sửa mã giảm giá';
        $content_file = VIEWS_PATH . '/admin/discount-codes/edit.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý update mã giảm giá
     */
    public function update()
    {
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf_token();

            try {
                if (empty($_POST['id'])) {
                    throw new Exception("Không tìm thấy mã giảm giá.");
                }

                $id = (int) $_POST['id'];
                $discountCode = $this->discountCodeModel->findById($id);

                if (!$discountCode) {
                    throw new Exception("Không tìm thấy mã giảm giá.");
                }

                if (empty($_POST['code'])) {
                    throw new Exception("Vui lòng nhập mã giảm giá.");
                }

                if (empty($_POST['discount_type'])) {
                    throw new Exception("Vui lòng chọn loại giảm giá.");
                }

                if (empty($_POST['discount_value']) || $_POST['discount_value'] <= 0) {
                    throw new Exception("Vui lòng nhập giá trị giảm giá hợp lệ.");
                }

                // Validate discount_value theo type
                if ($_POST['discount_type'] === 'percentage' && $_POST['discount_value'] > 100) {
                    throw new Exception("Giảm giá theo phần trăm không được vượt quá 100%.");
                }

                // Kiểm tra mã đã tồn tại chưa (trừ chính nó)
                if ($this->discountCodeModel->codeExists($_POST['code'], $id)) {
                    throw new Exception("Mã giảm giá đã tồn tại. Vui lòng chọn mã khác.");
                }

                // Validate dates
                if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
                    if ($_POST['start_date'] > $_POST['end_date']) {
                        throw new Exception("Ngày bắt đầu phải trước hoặc bằng ngày kết thúc.");
                    }
                }

                $data = [
                    'code' => $_POST['code'],
                    'name' => !empty($_POST['name']) ? sanitize($_POST['name']) : null,
                    'discount_type' => $_POST['discount_type'],
                    'discount_value' => (float) $_POST['discount_value'],
                    'min_purchase' => !empty($_POST['min_purchase']) ? (float) $_POST['min_purchase'] : 0,
                    'start_date' => !empty($_POST['start_date']) ? $_POST['start_date'] : null,
                    'end_date' => !empty($_POST['end_date']) ? $_POST['end_date'] : null,
                    'usage_limit' => !empty($_POST['usage_limit']) ? (int) $_POST['usage_limit'] : 0,
                    'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
                ];

                if ($this->discountCodeModel->update($id, $data)) {
                    set_success("Cập nhật mã giảm giá thành công!");
                } else {
                    throw new Exception("Không thể cập nhật.");
                }

                redirect('?act=admin&module=discount-codes');

            } catch (Exception $e) {
                set_error($e->getMessage());
                redirect('?act=admin&module=discount-codes&action=edit&id=' . ($id ?? 0));
            }
        }
    }

    /**
     * Xóa mã giảm giá
     */
    public function delete()
    {
        require_admin();

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Không tìm thấy mã giảm giá.");
            }

            $id = (int) $_GET['id'];
            $discountCode = $this->discountCodeModel->findById($id);

            if (!$discountCode) {
                throw new Exception("Không tìm thấy mã giảm giá.");
            }

            if ($this->discountCodeModel->delete($id)) {
                set_success("Đã xóa mã giảm giá thành công!");
            } else {
                throw new Exception("Không thể xóa mã giảm giá.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
        }

        redirect('?act=admin&module=discount-codes');
    }

    /**
     * Toggle status (active/inactive)
     */
    public function toggleStatus()
    {
        require_admin();

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Không tìm thấy mã giảm giá.");
            }

            $id = (int) $_GET['id'];
            $discountCode = $this->discountCodeModel->findById($id);

            if (!$discountCode) {
                throw new Exception("Không tìm thấy mã giảm giá.");
            }

            $newStatus = $discountCode['status'] === 'active' ? 'inactive' : 'active';
            $this->discountCodeModel->update($id, ['status' => $newStatus]);

            set_success("Đã " . ($newStatus === 'active' ? 'kích hoạt' : 'vô hiệu hóa') . " mã giảm giá thành công!");

        } catch (Exception $e) {
            set_error($e->getMessage());
        }

        redirect('?act=admin&module=discount-codes');
    }
}

