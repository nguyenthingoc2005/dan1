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
            try {
                // Validation
                if (empty($_POST['full_name'])) {
                    throw new \Exception("Vui lòng nhập tên khách hàng");
                }
                if (empty($_POST['phone'])) {
                    throw new \Exception("Vui lòng nhập số điện thoại");
                }

                // Check duplicate phone
                if ($this->customerModel->findByPhone($_POST['phone'])) {
                    throw new \Exception("Số điện thoại đã tồn tại trong hệ thống");
                }

                $data = [
                    'full_name' => sanitize($_POST['full_name']),
                    'phone' => sanitize($_POST['phone']),
                    'email' => !empty($_POST['email']) ? sanitize($_POST['email']) : null,
                    'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                    'gender' => $_POST['gender'] ?? 'other',
                    'address' => !empty($_POST['address']) ? sanitize($_POST['address']) : null,
                    'notes' => !empty($_POST['notes']) ? sanitize($_POST['notes']) : null,
                    'created_by' => get_user_id()
                ];

                $this->customerModel->create($data);

                set_success("Thêm khách hàng thành công!");
                redirect('?act=staff-customers');

            } catch (\Exception $e) {
                set_error($e->getMessage());
                $_SESSION['old'] = $_POST;
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
            try {
                $id = (int) $_POST['id'];

                $customer = $this->customerModel->findById($id);
                if (!$customer) {
                    throw new \Exception("Khách hàng không tồn tại");
                }

                // Validation
                if (empty($_POST['full_name'])) {
                    throw new \Exception("Vui lòng nhập tên khách hàng");
                }
                if (empty($_POST['phone'])) {
                    throw new \Exception("Vui lòng nhập số điện thoại");
                }

                // Check duplicate phone (exclude current customer)
                $existingPhone = $this->customerModel->findByPhone($_POST['phone']);
                if ($existingPhone && $existingPhone['id'] != $id) {
                    throw new \Exception("Số điện thoại đã tồn tại ở khách hàng khác");
                }

                $data = [
                    'full_name' => sanitize($_POST['full_name']),
                    'phone' => sanitize($_POST['phone']),
                    'email' => !empty($_POST['email']) ? sanitize($_POST['email']) : null,
                    'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                    'gender' => $_POST['gender'] ?? 'other',
                    'address' => !empty($_POST['address']) ? sanitize($_POST['address']) : null,
                    'notes' => !empty($_POST['notes']) ? sanitize($_POST['notes']) : null
                ];

                $this->customerModel->update($id, $data);

                set_success("Cập nhật khách hàng thành công!");
                redirect('?act=staff-customers');

            } catch (\Exception $e) {
                set_error($e->getMessage());
                redirect('?act=staff-customers&action=edit&id=' . $_POST['id']);
            }
        }
    }
}
