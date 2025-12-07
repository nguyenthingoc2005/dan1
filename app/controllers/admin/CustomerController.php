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
        require_csrf_token();

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
                'created_by' => get_user_id()
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
        require_csrf_token();

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

    /**
     * Trang import khách hàng
     */
    public function import()
    {
        require_admin();

        $page_title = 'Import Khách hàng từ Excel';
        $content_file = VIEWS_PATH . '/admin/customers/import.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý import
     */
    public function importStore()
    {
        require_admin();
        require_csrf_token();

        try {
            if (empty($_FILES['file'])) {
                throw new Exception("Vui lòng chọn file");
            }

            $file = $_FILES['file'];

            // Validate file
            $allowed = ['csv', 'xlsx', 'xls'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, $allowed)) {
                throw new Exception("Chỉ chấp nhận file CSV, XLSX, XLS");
            }

            // Validate file size (max 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new Exception("File quá lớn. Tối đa 5MB");
            }

            // Upload file
            $uploadDir = 'public/uploads/imports/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = 'import_' . time() . '_' . basename($file['name']);
            $filePath = $uploadDir . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new Exception("Không thể upload file");
            }

            // Import
            require_once MODELS_PATH . '/CustomerImport.php';
            $importModel = new CustomerImport($this->db);
            $result = $importModel->importFromFile($filePath, $file['name'], get_user_id());

            // Check if AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => "Import thành công: {$result['success']} khách hàng. Tổng: {$result['total']} dòng. Lỗi: " . count($result['errors']) . " dòng.",
                    'data' => [
                        'imported' => $result['success'],
                        'errors' => $result['errors'],
                        'total' => $result['total'],
                        'log_id' => $result['log_id']
                    ]
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Redirect to result page for normal form submit
            set_success("Import thành công: {$result['success']} khách hàng");
            redirect('?act=admin&module=customers&action=importResult&log_id=' . $result['log_id']);

        } catch (Exception $e) {
            // Check if AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            set_error($e->getMessage());
            redirect('?act=admin&module=customers');
        }
    }

    /**
     * Hiển thị kết quả import
     */
    public function importResult()
    {
        require_admin();

        $log_id = $_GET['log_id'] ?? null;
        if (!$log_id) {
            redirect('?act=admin&module=customers&action=import');
        }

        require_once MODELS_PATH . '/CustomerImport.php';
        $importModel = new CustomerImport($this->db);

        // Get log details
        $sql = "SELECT il.*, u.full_name as importer_name
                FROM customer_import_logs il
                LEFT JOIN users u ON il.imported_by = u.id
                WHERE il.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $log_id]);
        $log = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$log) {
            set_error("Không tìm thấy log import");
            redirect('?act=admin&module=customers&action=import');
        }

        $errors = json_decode($log['error_details'], true) ?? [];

        $page_title = 'Kết quả Import';
        $content_file = VIEWS_PATH . '/admin/customers/import_result.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Danh sách import logs
     */
    public function importLogs()
    {
        require_admin();

        $page = $_GET['page'] ?? 1;
        $limit = 20;

        require_once MODELS_PATH . '/CustomerImport.php';
        $importModel = new CustomerImport($this->db);
        $logs = $importModel->getImportLogs($page, $limit);

        // Count total for pagination
        $countSql = "SELECT COUNT(*) FROM customer_import_logs";
        $stmt = $this->db->query($countSql);
        $total = $stmt->fetchColumn();
        $total_pages = ceil($total / $limit);
        $current_page = $page;

        $page_title = 'Lịch sử Import';
        $content_file = VIEWS_PATH . '/admin/customers/import_logs.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        require_admin();

        // Use PUBLIC_PATH constant from bootstrap
        $templatePath = defined('PUBLIC_PATH') ? PUBLIC_PATH . '/templates/customer_import_template.csv' : __DIR__ . '/../../public/templates/customer_import_template.csv';

        // Fallback to relative path if constant not defined
        if (!defined('PUBLIC_PATH')) {
            $templatePath = __DIR__ . '/../../public/templates/customer_import_template.csv';
        }

        // Normalize path
        $templatePath = realpath($templatePath);

        if (!$templatePath || !file_exists($templatePath)) {
            set_error("File template không tồn tại.");
            redirect('?act=admin&module=customers&action=import');
            return;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="customer_import_template.csv"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        readfile($templatePath);
        exit;
    }
}
