<?php
/**
 * ==============================================================================
 * DRIVER CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý tài xế
 * Routing: ?act=admin&module=drivers&action=index
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class DriverController
{
    private $pdo;
    private $driverModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        require_once MODELS_PATH . '/Driver.php';
        $this->driverModel = new Driver($pdo);
    }

    /**
     * Danh sách tài xế
     */
    public function index()
    {
        require_admin();

        $page = $_GET['page'] ?? 1;
        $filters = [
            'status' => $_GET['status'] ?? '',
            'license_type' => $_GET['license_type'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        $result = $this->driverModel->getAll($filters, $page, 20);
        $drivers = $result['data'];
        $total = $result['total'];
        $total_pages = $result['pages'];
        $current_page = $result['current_page'];

        $page_title = 'Quản lý Tài xế';
        $content_file = VIEWS_PATH . '/admin/drivers/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form tạo tài xế mới
     */
    public function create()
    {
        require_admin();

        $page_title = 'Thêm tài xế mới';
        $content_file = VIEWS_PATH . '/admin/drivers/create.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý tạo tài xế mới
     */
    public function store()
    {
        require_admin();
        require_csrf_token();

        try {
            if (empty($_POST['full_name'])) {
                throw new Exception("Vui lòng nhập họ tên tài xế.");
            }

            if (empty($_POST['license_number'])) {
                throw new Exception("Vui lòng nhập số bằng lái.");
            }

            // Kiểm tra số bằng lái đã tồn tại chưa
            if ($this->driverModel->isLicenseNumberExists($_POST['license_number'])) {
                throw new Exception("Số bằng lái đã tồn tại.");
            }

            // Kiểm tra mã tài xế (nếu có)
            if (!empty($_POST['driver_code']) && $this->driverModel->isCodeExists($_POST['driver_code'])) {
                throw new Exception("Mã tài xế đã tồn tại.");
            }

            $data = [
                'driver_code' => !empty($_POST['driver_code']) ? trim($_POST['driver_code']) : null,
                'full_name' => trim($_POST['full_name']),
                'phone' => !empty($_POST['phone']) ? trim($_POST['phone']) : null,
                'email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
                'id_card' => !empty($_POST['id_card']) ? trim($_POST['id_card']) : null,
                'license_number' => trim($_POST['license_number']),
                'license_type' => $_POST['license_type'] ?? null,
                'license_issue_date' => !empty($_POST['license_issue_date']) ? $_POST['license_issue_date'] : null,
                'license_expiry_date' => !empty($_POST['license_expiry_date']) ? $_POST['license_expiry_date'] : null,
                'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                'address' => !empty($_POST['address']) ? trim($_POST['address']) : null,
                'emergency_contact_name' => !empty($_POST['emergency_contact_name']) ? trim($_POST['emergency_contact_name']) : null,
                'emergency_contact_phone' => !empty($_POST['emergency_contact_phone']) ? trim($_POST['emergency_contact_phone']) : null,
                'status' => $_POST['status'] ?? 'active',
                'hire_date' => !empty($_POST['hire_date']) ? $_POST['hire_date'] : null,
                'notes' => !empty($_POST['notes']) ? trim($_POST['notes']) : null
            ];

            $id = $this->driverModel->create($data);
            set_success("Thêm tài xế mới thành công!");
            redirect('?act=admin&module=drivers&action=show&id=' . $id);

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=drivers&action=create');
        }
    }

    /**
     * Chi tiết tài xế
     */
    public function show()
    {
        require_admin();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            set_error("Không tìm thấy tài xế.");
            redirect('?act=admin&module=drivers');
        }

        $driver = $this->driverModel->findById($id);
        if (!$driver) {
            set_error("Tài xế không tồn tại.");
            redirect('?act=admin&module=drivers');
        }

        // Lấy lịch làm việc
        $sql = "
            SELECT 
                ds.*,
                ts.start_date,
                ts.end_date,
                t.tour_code,
                t.name AS tour_name
            FROM driver_schedules ds
            JOIN tour_schedules ts ON ds.tour_schedule_id = ts.id
            JOIN tours t ON ts.tour_id = t.id
            WHERE ds.driver_id = :driver_id
            ORDER BY ds.schedule_date DESC
            LIMIT 50
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['driver_id' => $id]);
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $page_title = 'Chi tiết Tài xế: ' . $driver['full_name'];
        $content_file = VIEWS_PATH . '/admin/drivers/show.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form chỉnh sửa tài xế
     */
    public function edit()
    {
        require_admin();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            set_error("Không tìm thấy tài xế.");
            redirect('?act=admin&module=drivers');
        }

        $driver = $this->driverModel->findById($id);
        if (!$driver) {
            set_error("Tài xế không tồn tại.");
            redirect('?act=admin&module=drivers');
        }

        $page_title = 'Chỉnh sửa Tài xế: ' . $driver['full_name'];
        $content_file = VIEWS_PATH . '/admin/drivers/edit.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý cập nhật tài xế
     */
    public function update()
    {
        require_admin();
        require_csrf_token();

        try {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new Exception("Không tìm thấy tài xế.");
            }

            $driver = $this->driverModel->findById($id);
            if (!$driver) {
                throw new Exception("Tài xế không tồn tại.");
            }

            if (empty($_POST['full_name'])) {
                throw new Exception("Vui lòng nhập họ tên tài xế.");
            }

            if (empty($_POST['license_number'])) {
                throw new Exception("Vui lòng nhập số bằng lái.");
            }

            // Kiểm tra số bằng lái đã tồn tại chưa (trừ chính nó)
            if ($this->driverModel->isLicenseNumberExists($_POST['license_number'], $id)) {
                throw new Exception("Số bằng lái đã tồn tại.");
            }

            // Kiểm tra mã tài xế (nếu có)
            if (!empty($_POST['driver_code']) && $this->driverModel->isCodeExists($_POST['driver_code'], $id)) {
                throw new Exception("Mã tài xế đã tồn tại.");
            }

            $data = [
                'driver_code' => !empty($_POST['driver_code']) ? trim($_POST['driver_code']) : null,
                'full_name' => trim($_POST['full_name']),
                'phone' => !empty($_POST['phone']) ? trim($_POST['phone']) : null,
                'email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
                'id_card' => !empty($_POST['id_card']) ? trim($_POST['id_card']) : null,
                'license_number' => trim($_POST['license_number']),
                'license_type' => $_POST['license_type'] ?? null,
                'license_issue_date' => !empty($_POST['license_issue_date']) ? $_POST['license_issue_date'] : null,
                'license_expiry_date' => !empty($_POST['license_expiry_date']) ? $_POST['license_expiry_date'] : null,
                'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                'address' => !empty($_POST['address']) ? trim($_POST['address']) : null,
                'emergency_contact_name' => !empty($_POST['emergency_contact_name']) ? trim($_POST['emergency_contact_name']) : null,
                'emergency_contact_phone' => !empty($_POST['emergency_contact_phone']) ? trim($_POST['emergency_contact_phone']) : null,
                'status' => $_POST['status'],
                'hire_date' => !empty($_POST['hire_date']) ? $_POST['hire_date'] : null,
                'notes' => !empty($_POST['notes']) ? trim($_POST['notes']) : null
            ];

            $this->driverModel->update($id, $data);
            set_success("Cập nhật tài xế thành công!");
            redirect('?act=admin&module=drivers&action=show&id=' . $id);

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=drivers&action=edit&id=' . ($_POST['id'] ?? ''));
        }
    }

    /**
     * Xóa tài xế (soft delete)
     */
    public function delete()
    {
        require_admin();
        require_csrf_token();

        try {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new Exception("Không tìm thấy tài xế.");
            }

            $driver = $this->driverModel->findById($id);
            if (!$driver) {
                throw new Exception("Tài xế không tồn tại.");
            }

            // TODO: Kiểm tra tài xế có đang được sử dụng không

            $this->driverModel->delete($id);
            set_success("Xóa tài xế thành công!");
            redirect('?act=admin&module=drivers');

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=drivers');
        }
    }
}

