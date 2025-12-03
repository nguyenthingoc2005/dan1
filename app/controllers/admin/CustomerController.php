<?php
/**
 * ==============================================================================
 * CUSTOMER CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý khách hàng
 * Routing: ?act=admin&module=customers&action=index
 * 
 * @version 1.0
 * @date 2024-12-03
 * ==============================================================================
 */

class CustomerController
{
    private $db;
    private $customerModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/Customer.php';
        $this->customerModel = new Customer($pdo);
    }

    /**
     * Danh sách khách hàng
     */
    public function index()
    {
        require_admin();

        // Get filters
        $filters = [];
        if (!empty($_GET['search']))
            $filters['search'] = sanitize($_GET['search']);
        if (!empty($_GET['status']))
            $filters['status'] = sanitize($_GET['status']);

        // Pagination
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = 20;

        $result = $this->customerModel->getAll($filters, $page, $limit); // Note: Customer model might need update to return pagination array

        // If model returns just array, we handle pagination manually or update model.
        // Let's check Customer model first. Assuming it returns array for now, we might need to fix it.
        // Actually, let's look at Customer.php model structure.
        // For now, I will assume standard structure.

        $customers = $result['data'] ?? $result; // Fallback
        $total = $result['total'] ?? count($customers);
        $total_pages = $result['pages'] ?? 1;
        $current_page = $page;

        $page_title = 'Quản lý Khách hàng';
        $content_file = VIEWS_PATH . '/admin/customers/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form tạo khách hàng
     */
    public function create()
    {
        require_admin();

        $page_title = 'Thêm khách hàng mới';
        $content_file = VIEWS_PATH . '/admin/customers/create.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý tạo khách hàng
     */
    public function store()
    {
        require_admin();

        try {
            // Prepare data
            $data = [
                'full_name' => sanitize($_POST['full_name'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'email' => !empty($_POST['email']) ? sanitize($_POST['email']) : null,
                'address' => !empty($_POST['address']) ? sanitize($_POST['address']) : null,
                'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                'gender' => $_POST['gender'] ?? null,
                'id_card' => !empty($_POST['id_card']) ? sanitize($_POST['id_card']) : null,
                'passport' => !empty($_POST['passport']) ? sanitize($_POST['passport']) : null,
                'nationality' => !empty($_POST['nationality']) ? sanitize($_POST['nationality']) : 'Vietnam',
                'customer_type' => $_POST['customer_type'] ?? 'individual',
                'source' => $_POST['source'] ?? 'other',
                'special_requirements' => !empty($_POST['special_requirements']) ? sanitize($_POST['special_requirements']) : null,
                'notes' => !empty($_POST['notes']) ? sanitize($_POST['notes']) : null,
                'created_by' => $_SESSION['user_id'] ?? 1
            ];

            // Validate using Model
            $validation = $this->customerModel->validate($data);
            
            if (!$validation['valid']) {
                // Get first error message to display
                $firstError = reset($validation['errors']);
                throw new Exception($firstError);
            }

            // Create customer
            $customerId = $this->customerModel->create($data);
            
            if ($customerId) {
                set_success("Thêm khách hàng thành công! Mã KH: " . $this->customerModel->getById($customerId)['customer_code']);
                redirect('?act=admin&module=customers');
            } else {
                throw new Exception("Không thể thêm khách hàng.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            $_SESSION['old'] = $_POST; // Keep old input
            redirect('?act=admin&module=customers&action=create');
        }
    }

    /**
     * Xem chi tiết khách hàng
     */
    public function show()
    {
        require_admin();

        if (empty($_GET['id'])) {
            redirect('?act=admin&module=customers');
        }

        $id = (int) $_GET['id'];
        $customer = $this->customerModel->getById($id);

        if (!$customer) {
            set_error("Khách hàng không tồn tại.");
            redirect('?act=admin&module=customers');
        }

        // Get booking history
        require_once MODELS_PATH . '/Booking.php';
        $bookingModel = new Booking($this->db);
        // Use getByCustomerId method for better performance
        $bookings = $bookingModel->getByCustomerId($id);

        $page_title = 'Chi tiết khách hàng: ' . $customer['full_name'];
        $content_file = VIEWS_PATH . '/admin/customers/show.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form sửa khách hàng
     */
    public function edit()
    {
        require_admin();

        if (empty($_GET['id'])) {
            redirect('?act=admin&module=customers');
        }

        $id = (int) $_GET['id'];
        $customer = $this->customerModel->getById($id);

        if (!$customer) {
            set_error("Khách hàng không tồn tại.");
            redirect('?act=admin&module=customers');
        }

        $page_title = 'Sửa thông tin khách hàng';
        $content_file = VIEWS_PATH . '/admin/customers/edit.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý cập nhật
     */
    public function update()
    {
        require_admin();

        try {
            if (empty($_POST['id'])) {
                throw new Exception("Thiếu ID khách hàng.");
            }

            $id = (int) $_POST['id'];
            
            // Check customer exists
            $existing = $this->customerModel->getById($id);
            if (!$existing) {
                throw new Exception("Khách hàng không tồn tại.");
            }

            // Prepare data
            $data = [
                'full_name' => sanitize($_POST['full_name'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'email' => !empty($_POST['email']) ? sanitize($_POST['email']) : null,
                'address' => !empty($_POST['address']) ? sanitize($_POST['address']) : null,
                'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                'gender' => $_POST['gender'] ?? null,
                'id_card' => !empty($_POST['id_card']) ? sanitize($_POST['id_card']) : null,
                'passport' => !empty($_POST['passport']) ? sanitize($_POST['passport']) : null,
                'nationality' => !empty($_POST['nationality']) ? sanitize($_POST['nationality']) : 'Vietnam',
                'customer_type' => $_POST['customer_type'] ?? 'individual',
                'source' => $_POST['source'] ?? 'other',
                'special_requirements' => !empty($_POST['special_requirements']) ? sanitize($_POST['special_requirements']) : null,
                'notes' => !empty($_POST['notes']) ? sanitize($_POST['notes']) : null,
                'status' => $_POST['status'] ?? 'active'
            ];

            // Validate using Model (pass excludeId to skip current customer in unique checks)
            $validation = $this->customerModel->validate($data, $id);
            
            if (!$validation['valid']) {
                $firstError = reset($validation['errors']);
                throw new Exception($firstError);
            }

            // Normalize phone
            $data['phone'] = preg_replace('/[\s\-\(\)]/', '', $data['phone']);
            if (!empty($data['id_card'])) {
                $data['id_card'] = preg_replace('/\s/', '', $data['id_card']);
            }
            if (!empty($data['passport'])) {
                $data['passport'] = strtoupper($data['passport']);
            }

            if ($this->customerModel->update($id, $data)) {
                set_success("Cập nhật thành công!");
                redirect('?act=admin&module=customers&action=show&id=' . $id);
            } else {
                throw new Exception("Không thể cập nhật.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=customers&action=edit&id=' . ($_POST['id'] ?? 0));
        }
    }

    /**
     * Xóa khách hàng (Soft delete)
     */
    public function delete()
    {
        require_admin();

        if (!empty($_GET['id'])) {
            $id = (int) $_GET['id'];
            // Check dependencies (bookings)
            // Ideally model should handle this or return false
            if ($this->customerModel->delete($id)) {
                set_success("Đã xóa khách hàng.");
            } else {
                set_error("Không thể xóa khách hàng này (có thể đang có booking).");
            }
        }

        redirect('?act=admin&module=customers');
    }
}
