<?php
/**
 * ==============================================================================
 * VEHICLE CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý xe công ty
 * Routing: ?act=admin&module=vehicles&action=index
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class VehicleController
{
    private $pdo;
    private $vehicleModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        require_once MODELS_PATH . '/Vehicle.php';
        $this->vehicleModel = new Vehicle($pdo);
    }

    /**
     * Danh sách xe
     */
    public function index()
    {
        require_admin();

        $page = $_GET['page'] ?? 1;
        $filters = [
            'status' => $_GET['status'] ?? '',
            'vehicle_type' => $_GET['vehicle_type'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        $result = $this->vehicleModel->getAll($filters, $page, 20);
        $vehicles = $result['data'];
        $total = $result['total'];
        $total_pages = $result['pages'];
        $current_page = $result['current_page'];

        $page_title = 'Quản lý Xe';
        $content_file = VIEWS_PATH . '/admin/vehicles/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form tạo xe mới
     */
    public function create()
    {
        require_admin();

        $page_title = 'Thêm xe mới';
        $content_file = VIEWS_PATH . '/admin/vehicles/create.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý tạo xe mới
     */
    public function store()
    {
        require_admin();
        require_csrf_token();

        try {
            if (empty($_POST['vehicle_type'])) {
                throw new Exception("Vui lòng chọn loại xe.");
            }

            if (empty($_POST['license_plate'])) {
                throw new Exception("Vui lòng nhập biển số xe.");
            }

            if (empty($_POST['capacity']) || $_POST['capacity'] <= 0) {
                throw new Exception("Vui lòng nhập số chỗ hợp lệ.");
            }

            // Kiểm tra biển số đã tồn tại chưa
            if ($this->vehicleModel->isLicensePlateExists($_POST['license_plate'])) {
                throw new Exception("Biển số xe đã tồn tại.");
            }

            // Kiểm tra mã xe (nếu có)
            if (!empty($_POST['vehicle_code']) && $this->vehicleModel->isCodeExists($_POST['vehicle_code'])) {
                throw new Exception("Mã xe đã tồn tại.");
            }

            $data = [
                'vehicle_code' => !empty($_POST['vehicle_code']) ? trim($_POST['vehicle_code']) : null,
                'vehicle_type' => $_POST['vehicle_type'],
                'license_plate' => trim($_POST['license_plate']),
                'capacity' => (int)$_POST['capacity'],
                'status' => $_POST['status'] ?? 'active',
                'notes' => !empty($_POST['notes']) ? trim($_POST['notes']) : null
            ];

            $id = $this->vehicleModel->create($data);
            set_success("Thêm xe mới thành công!");
            redirect('?act=admin&module=vehicles&action=show&id=' . $id);

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=vehicles&action=create');
        }
    }

    /**
     * Chi tiết xe
     */
    public function show()
    {
        require_admin();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            set_error("Không tìm thấy xe.");
            redirect('?act=admin&module=vehicles');
        }

        $vehicle = $this->vehicleModel->findById($id);
        if (!$vehicle) {
            set_error("Xe không tồn tại.");
            redirect('?act=admin&module=vehicles');
        }

        // Lấy lịch sử phân công
        require_once MODELS_PATH . '/VehicleAssignment.php';
        $assignmentModel = new VehicleAssignment($this->pdo);
        $assignments = $assignmentModel->getByScheduleId(null); // TODO: Lấy theo vehicle_id

        $page_title = 'Chi tiết Xe: ' . $vehicle['license_plate'];
        $content_file = VIEWS_PATH . '/admin/vehicles/show.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form chỉnh sửa xe
     */
    public function edit()
    {
        require_admin();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            set_error("Không tìm thấy xe.");
            redirect('?act=admin&module=vehicles');
        }

        $vehicle = $this->vehicleModel->findById($id);
        if (!$vehicle) {
            set_error("Xe không tồn tại.");
            redirect('?act=admin&module=vehicles');
        }

        $page_title = 'Chỉnh sửa Xe: ' . $vehicle['license_plate'];
        $content_file = VIEWS_PATH . '/admin/vehicles/edit.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý cập nhật xe
     */
    public function update()
    {
        require_admin();
        require_csrf_token();

        try {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new Exception("Không tìm thấy xe.");
            }

            $vehicle = $this->vehicleModel->findById($id);
            if (!$vehicle) {
                throw new Exception("Xe không tồn tại.");
            }

            if (empty($_POST['vehicle_type'])) {
                throw new Exception("Vui lòng chọn loại xe.");
            }

            if (empty($_POST['license_plate'])) {
                throw new Exception("Vui lòng nhập biển số xe.");
            }

            if (empty($_POST['capacity']) || $_POST['capacity'] <= 0) {
                throw new Exception("Vui lòng nhập số chỗ hợp lệ.");
            }

            // Kiểm tra biển số đã tồn tại chưa (trừ chính nó)
            if ($this->vehicleModel->isLicensePlateExists($_POST['license_plate'], $id)) {
                throw new Exception("Biển số xe đã tồn tại.");
            }

            // Kiểm tra mã xe (nếu có)
            if (!empty($_POST['vehicle_code']) && $this->vehicleModel->isCodeExists($_POST['vehicle_code'], $id)) {
                throw new Exception("Mã xe đã tồn tại.");
            }

            $data = [
                'vehicle_code' => !empty($_POST['vehicle_code']) ? trim($_POST['vehicle_code']) : null,
                'vehicle_type' => $_POST['vehicle_type'],
                'license_plate' => trim($_POST['license_plate']),
                'capacity' => (int)$_POST['capacity'],
                'status' => $_POST['status'],
                'notes' => !empty($_POST['notes']) ? trim($_POST['notes']) : null
            ];

            $this->vehicleModel->update($id, $data);
            set_success("Cập nhật xe thành công!");
            redirect('?act=admin&module=vehicles&action=show&id=' . $id);

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=vehicles&action=edit&id=' . ($_POST['id'] ?? ''));
        }
    }

    /**
     * Xóa xe (soft delete)
     */
    public function delete()
    {
        require_admin();
        require_csrf_token();

        try {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new Exception("Không tìm thấy xe.");
            }

            $vehicle = $this->vehicleModel->findById($id);
            if (!$vehicle) {
                throw new Exception("Xe không tồn tại.");
            }

            // TODO: Kiểm tra xe có đang được sử dụng không

            $this->vehicleModel->delete($id);
            set_success("Xóa xe thành công!");
            redirect('?act=admin&module=vehicles');

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=vehicles');
        }
    }
}

