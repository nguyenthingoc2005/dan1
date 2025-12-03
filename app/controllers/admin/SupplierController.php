<?php
/**
 * ==============================================================================
 * SUPPLIER CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý nhà cung cấp - CHỈ ADMIN
 * Routing: ?act=admin&module=suppliers&action=index
 * 
 * Validation:
 * - Company name: REQUIRED
 * - Email: OPTIONAL, valid format, UNIQUE
 * - Phone: OPTIONAL, Vietnamese format
 * - Tax code: OPTIONAL, 10 digits, UNIQUE
 * - Contract dates: end >= start
 * 
 * @version 1.0
 * @date 2024-12-02
 * ==============================================================================
 */

class SupplierController
{
    private $db;
    private $supplierModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/Supplier.php';
        $this->supplierModel = new Supplier($pdo);
    }

    /**
     * Danh sách suppliers
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
        $result = $this->supplierModel->getAll($filters, $page, 20);

        $suppliers = $result['data'];
        $total = $result['total'];
        $total_pages = $result['pages'];
        $current_page = $result['current_page'];

        // Get expiring contracts (within 30 days)
        $expiring_contracts = $this->supplierModel->getExpiringContracts(30);

        // Get service count for each supplier
        foreach ($suppliers as &$supplier) {
            $supplier['service_count'] = $this->supplierModel->getServiceCount($supplier['id']);
        }

        $page_title = 'Quản lý nhà cung cấp';
        $content_file = VIEWS_PATH . '/admin/suppliers/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form tạo supplier
     */
    public function create()
    {
        require_admin();

        // Auto-generate supplier code for display
        $supplier_code = $this->supplierModel->generateSupplierCode();

        $page_title = 'Thêm nhà cung cấp mới';
        $content_file = VIEWS_PATH . '/admin/suppliers/create.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý tạo supplier
     */
    public function store()
    {
        require_admin();

        try {
            // 1. Validate company name - REQUIRED
            if (empty($_POST['company_name'])) {
                throw new Exception("Vui lòng nhập tên công ty.");
            }

            // 2. Validate email - OPTIONAL, format + unique
            $email = null;
            if (!empty($_POST['email'])) {
                $email = trim($_POST['email']);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Email không hợp lệ.");
                }

                $existing = $this->supplierModel->findByEmail($email);
                if ($existing) {
                    throw new Exception("Email đã được sử dụng bởi nhà cung cấp khác.");
                }
            }

            // 3. Validate phone - OPTIONAL, simple check
            $phone = null;
            if (!empty($_POST['phone'])) {
                $phone = trim($_POST['phone']);
                if (strlen($phone) < 10) {
                    throw new Exception("Số điện thoại không hợp lệ (tối thiểu 10 số).");
                }
            }

            // 4. Validate tax code - OPTIONAL, 10 digits + unique
            $tax_code = null;
            if (!empty($_POST['tax_code'])) {
                $tax_code = trim($_POST['tax_code']);
                if (!preg_match('/^[0-9]{10}$/', $tax_code)) {
                    throw new Exception("Mã số thuế phải là 10 chữ số.");
                }

                $existing = $this->supplierModel->findByTaxCode($tax_code);
                if ($existing) {
                    throw new Exception("Mã số thuế đã tồn tại.");
                }
            }

            // 5. Validate contract dates - OPTIONAL, end >= start
            $contract_start = !empty($_POST['contract_start']) ? $_POST['contract_start'] : null;
            $contract_end = !empty($_POST['contract_end']) ? $_POST['contract_end'] : null;

            if ($contract_start && $contract_end) {
                if (strtotime($contract_end) < strtotime($contract_start)) {
                    throw new Exception("Ngày kết thúc hợp đồng phải sau ngày bắt đầu.");
                }
            }

            // Prepare data
            $data = [
                'company_name' => sanitize($_POST['company_name']),
                'contact_person' => isset($_POST['contact_person']) ? sanitize($_POST['contact_person']) : null,
                'email' => $email,
                'phone' => $phone,
                'address' => isset($_POST['address']) ? sanitize($_POST['address']) : null,
                'tax_code' => $tax_code,
                'bank_name' => isset($_POST['bank_name']) ? sanitize($_POST['bank_name']) : null,
                'bank_account' => isset($_POST['bank_account']) ? sanitize($_POST['bank_account']) : null,
                'bank_holder' => isset($_POST['bank_holder']) ? sanitize($_POST['bank_holder']) : null,
                'contract_start' => $contract_start,
                'contract_end' => $contract_end,
                'payment_terms' => isset($_POST['payment_terms']) ? sanitize($_POST['payment_terms']) : null,
                'notes' => isset($_POST['notes']) ? sanitize($_POST['notes']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            if ($this->supplierModel->create($data)) {
                set_success("Tạo nhà cung cấp thành công!");
                redirect('?act=admin&module=suppliers');
            } else {
                throw new Exception("Không thể tạo nhà cung cấp.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=suppliers&action=create');
        }
    }

    /**
     * Form sửa supplier
     */
    public function edit()
    {
        require_admin();

        if (empty($_GET['id'])) {
            set_error("Không tìm thấy nhà cung cấp.");
            redirect('?act=admin&module=suppliers');
            return;
        }

        $supplier = $this->supplierModel->findById((int) $_GET['id']);
        if (!$supplier) {
            set_error("Không tìm thấy nhà cung cấp.");
            redirect('?act=admin&module=suppliers');
            return;
        }

        $page_title = 'Sửa nhà cung cấp';
        $content_file = VIEWS_PATH . '/admin/suppliers/edit.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý update supplier
     */
    public function update()
    {
        require_admin();

        try {
            if (empty($_POST['id'])) {
                throw new Exception("Không tìm thấy nhà cung cấp.");
            }

            $supplier_id = (int) $_POST['id'];
            $supplier = $this->supplierModel->findById($supplier_id);

            if (!$supplier) {
                throw new Exception("Không tìm thấy nhà cung cấp.");
            }

            // 1. Validate company name - REQUIRED
            if (empty($_POST['company_name'])) {
                throw new Exception("Vui lòng nhập tên công ty.");
            }

            // 2. Validate email - OPTIONAL, format + unique (exclude current)
            $email = null;
            if (!empty($_POST['email'])) {
                $email = trim($_POST['email']);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Email không hợp lệ.");
                }

                $existing = $this->supplierModel->findByEmail($email);
                if ($existing && $existing['id'] != $supplier_id) {
                    throw new Exception("Email đã được sử dụng bởi nhà cung cấp khác.");
                }
            }

            // 3. Validate phone - OPTIONAL
            $phone = null;
            if (!empty($_POST['phone'])) {
                $phone = trim($_POST['phone']);
                if (strlen($phone) < 10) {
                    throw new Exception("Số điện thoại không hợp lệ (tối thiểu 10 số).");
                }
            }

            // 4. Validate tax code - OPTIONAL, 10 digits + unique (exclude current)
            $tax_code = null;
            if (!empty($_POST['tax_code'])) {
                $tax_code = trim($_POST['tax_code']);
                if (!preg_match('/^[0-9]{10}$/', $tax_code)) {
                    throw new Exception("Mã số thuế phải là 10 chữ số.");
                }

                $existing = $this->supplierModel->findByTaxCode($tax_code);
                if ($existing && $existing['id'] != $supplier_id) {
                    throw new Exception("Mã số thuế đã tồn tại.");
                }
            }

            // 5. Validate contract dates
            $contract_start = !empty($_POST['contract_start']) ? $_POST['contract_start'] : null;
            $contract_end = !empty($_POST['contract_end']) ? $_POST['contract_end'] : null;

            if ($contract_start && $contract_end) {
                if (strtotime($contract_end) < strtotime($contract_start)) {
                    throw new Exception("Ngày kết thúc hợp đồng phải sau ngày bắt đầu.");
                }
            }

            // Prepare data
            $data = [
                'company_name' => sanitize($_POST['company_name']),
                'contact_person' => isset($_POST['contact_person']) ? sanitize($_POST['contact_person']) : null,
                'email' => $email,
                'phone' => $phone,
                'address' => isset($_POST['address']) ? sanitize($_POST['address']) : null,
                'tax_code' => $tax_code,
                'bank_name' => isset($_POST['bank_name']) ? sanitize($_POST['bank_name']) : null,
                'bank_account' => isset($_POST['bank_account']) ? sanitize($_POST['bank_account']) : null,
                'bank_holder' => isset($_POST['bank_holder']) ? sanitize($_POST['bank_holder']) : null,
                'contract_start' => $contract_start,
                'contract_end' => $contract_end,
                'payment_terms' => isset($_POST['payment_terms']) ? sanitize($_POST['payment_terms']) : null,
                'notes' => isset($_POST['notes']) ? sanitize($_POST['notes']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            if ($this->supplierModel->update($supplier_id, $data)) {
                set_success("Cập nhật thành công!");
            } else {
                throw new Exception("Không thể cập nhật.");
            }

            redirect('?act=admin&module=suppliers');

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=suppliers&action=edit&id=' . ($supplier_id ?? 0));
        }
    }

    /**
     * Xóa supplier (soft delete với FK check)
     */
    public function delete()
    {
        require_admin();

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Không tìm thấy nhà cung cấp.");
            }

            $supplier_id = (int) $_GET['id'];

            // Delete sẽ tự động check FK constraint trong Model
            if ($this->supplierModel->delete($supplier_id)) {
                set_success("Đã vô hiệu hóa nhà cung cấp.");
            } else {
                throw new Exception("Không thể xóa nhà cung cấp.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
        }

        redirect('?act=admin&module=suppliers');
    }
}
