<?php
namespace Staff;

/**
 * ==============================================================================
 * CUSTOMER CONTROLLER (STAFF)
 * ==============================================================================
 * 
 * Staff có thể:
 * - Xem tất cả customers (không filter ownership)
 * - Create, Edit, Update customers
 * - KHÔNG DELETE (chỉ admin mới delete được)
 * 
 * @version 1.0
 * @date 2024-12-03
 * ==============================================================================
 */

class CustomerController
{
    private $customerModel;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        require_once MODELS_PATH . '/Customer.php';
        $this->customerModel = new \Customer($pdo);
    }

    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $filters = [
            'search' => $_GET['search'] ?? ''
        ];

        $result = $this->customerModel->getAll($filters, $page, $limit);
        $customers = $result['data'];
        $total_records = $result['total'];
        $total_pages = $result['pages'];

        $page_title = 'Quản lý Khách Hàng';
        $content_file = VIEWS_PATH . '/staff/customers/index.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    public function create()
    {
        $page_title = 'Thêm Khách Hàng Mới';
        $content_file = VIEWS_PATH . '/staff/customers/create.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf_token();
            try {
                // Prepare data (giống admin)
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

                // Validate using Model (giống admin)
                $validation = $this->customerModel->validate($data);
                
                if (!$validation['valid']) {
                    // Get first error message to display
                    $firstError = reset($validation['errors']);
                    throw new \Exception($firstError);
                }

                // Create customer
                $customerId = $this->customerModel->create($data);
                
                if ($customerId) {
                    set_success("Thêm khách hàng thành công! Mã KH: " . $this->customerModel->getById($customerId)['customer_code']);
                    redirect('?act=staff-customers');
                } else {
                    throw new \Exception("Không thể thêm khách hàng.");
                }

            } catch (\Exception $e) {
                set_error($e->getMessage());
                $_SESSION['old'] = $_POST; // Keep old input
                redirect('?act=staff-customers&action=create');
            }
        }
    }

    public function show()
    {
        $id = $_GET['id'] ?? null;
        if (!$id)
            redirect('?act=staff-customers');

        $customer = $this->customerModel->findById($id);
        if (!$customer) {
            set_error("Không tìm thấy khách hàng.");
            redirect('?act=staff-customers');
        }

        // Get Bookings của khách hàng này
        require_once MODELS_PATH . '/Booking.php';
        $bookingModel = new \Booking($this->pdo);
        $bookings = $bookingModel->getByCustomerId($id);

        $page_title = 'Chi tiết Khách Hàng: ' . htmlspecialchars($customer['full_name']);
        $content_file = VIEWS_PATH . '/staff/customers/show.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id)
            redirect('?act=staff-customers');

        $customer = $this->customerModel->findById($id);
        if (!$customer) {
            set_error("Không tìm thấy khách hàng.");
            redirect('?act=staff-customers');
        }

        $page_title = 'Sửa Khách Hàng: ' . htmlspecialchars($customer['full_name']);
        $content_file = VIEWS_PATH . '/staff/customers/edit.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf_token();
            try {
                if (empty($_POST['id'])) {
                    throw new \Exception("Thiếu ID khách hàng.");
                }

                $id = (int) $_POST['id'];
                
                // Check customer exists
                $existing = $this->customerModel->getById($id);
                if (!$existing) {
                    throw new \Exception("Khách hàng không tồn tại.");
                }

                // Prepare data (giống admin)
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
                    throw new \Exception($firstError);
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
                    redirect('?act=staff-customers&action=show&id=' . $id);
                } else {
                    throw new \Exception("Không thể cập nhật.");
                }

            } catch (\Exception $e) {
                set_error($e->getMessage());
                redirect('?act=staff-customers&action=edit&id=' . ($_POST['id'] ?? 0));
            }
        }
    }

    /**
     * Trang import khách hàng
     */
    public function import()
    {
        $page_title = 'Import Khách hàng từ Excel';
        $content_file = VIEWS_PATH . '/staff/customers/import.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    /**
     * Xử lý import
     */
    public function importStore()
    {
        require_csrf_token();

        try {
            if (empty($_FILES['file'])) {
                throw new \Exception("Vui lòng chọn file");
            }

            $file = $_FILES['file'];

            // Validate file
            $allowed = ['csv', 'xlsx', 'xls'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, $allowed)) {
                throw new \Exception("Chỉ chấp nhận file CSV, XLSX, XLS");
            }

            // Upload file
            $uploadDir = 'public/uploads/imports/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = 'import_' . time() . '_' . basename($file['name']);
            $filePath = $uploadDir . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new \Exception("Không thể upload file");
            }

            // Import
            require_once MODELS_PATH . '/CustomerImport.php';
            $importModel = new \CustomerImport($this->pdo);
            $result = $importModel->importFromFile($filePath, $file['name'], get_user_id());

            // Redirect to result page
            redirect('?act=staff-customers&action=importResult&log_id=' . $result['log_id']);

        } catch (\Exception $e) {
            set_error($e->getMessage());
            redirect('?act=staff-customers&action=import');
        }
    }

    /**
     * Hiển thị kết quả import
     */
    public function importResult()
    {
        $log_id = $_GET['log_id'] ?? null;
        if (!$log_id) {
            redirect('?act=staff-customers&action=import');
        }

        require_once MODELS_PATH . '/CustomerImport.php';
        $importModel = new \CustomerImport($this->pdo);

        // Get log details
        $sql = "SELECT il.*, u.full_name as importer_name
                FROM customer_import_logs il
                LEFT JOIN users u ON il.imported_by = u.id
                WHERE il.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $log_id]);
        $log = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$log) {
            set_error("Không tìm thấy log import");
            redirect('?act=staff-customers&action=import');
        }

        $errors = json_decode($log['error_details'], true) ?? [];

        $page_title = 'Kết quả Import';
        $content_file = VIEWS_PATH . '/staff/customers/import_result.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    /**
     * Danh sách import logs
     */
    public function importLogs()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 20;

        require_once MODELS_PATH . '/CustomerImport.php';
        $importModel = new \CustomerImport($this->pdo);
        $logs = $importModel->getImportLogs($page, $limit);

        // Count total for pagination
        $countSql = "SELECT COUNT(*) FROM customer_import_logs";
        $stmt = $this->pdo->query($countSql);
        $total = $stmt->fetchColumn();
        $total_pages = ceil($total / $limit);
        $current_page = $page;

        $page_title = 'Lịch sử Import';
        $content_file = VIEWS_PATH . '/staff/customers/import_logs.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
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
            redirect('?act=staff-customers&action=import');
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
