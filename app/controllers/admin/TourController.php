<?php
/**
 * ==============================================================================
 * TOUR CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý Tour du lịch
 * Routing: ?act=admin&module=tours
 * 
 * Features:
 * - CRUD Tours (Complex Form)
 * - Manage Itinerary
 * - Manage Images
 * 
 * @version 1.0
 * @date 2024-12-02
 * ==============================================================================
 */

class TourController
{
    private $db;
    private $tourModel;
    private $categoryModel;
    private $destinationModel;
    private $tourServiceModel;
    private $serviceModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/Tour.php';
        require_once MODELS_PATH . '/Category.php';
        require_once MODELS_PATH . '/Destination.php';

        $this->tourModel = new Tour($pdo);
        $this->categoryModel = new Category($pdo);
        $this->destinationModel = new Destination($pdo);

        // [NEW] Load Service Models
        require_once MODELS_PATH . '/TourService.php';
        require_once MODELS_PATH . '/Service.php';
        $this->tourServiceModel = new TourService($pdo);
        $this->serviceModel = new Service($pdo);
    }

    /**
     * Danh sách Tours
     */
    public function index()
    {
        require_admin();

        // Filters
        $filters = [];
        if (!empty($_GET['category_id']))
            $filters['category_id'] = (int) $_GET['category_id'];
        if (!empty($_GET['status']))
            $filters['status'] = sanitize($_GET['status']);
        if (!empty($_GET['search']))
            $filters['search'] = sanitize($_GET['search']);

        // Pagination
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $result = $this->tourModel->getAll($filters, $page, 10);

        $tours = $result['data'];
        $total = $result['total'];
        $total_pages = $result['pages'];
        $current_page = $result['current_page'];

        // Dropdown Data
        $categories = $this->categoryModel->getForDropdown();

        $page_title = 'Quản lý Tour';
        $content_file = VIEWS_PATH . '/admin/tours/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form tạo Tour
     */
    public function create()
    {
        require_admin();

        $categories = $this->categoryModel->getForDropdown();
        $destinations = $this->destinationModel->getForDropdown();

        // [NEW] Get Services for Dropdown
        $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];

        $page_title = 'Thêm Tour mới';
        $content_file = VIEWS_PATH . '/admin/tours/create.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý lưu Tour mới
     */
    public function store()
    {
        require_admin();

        try {
            // 1. Validate Basic Info
            $errors = [];
            if (empty($_POST['name']))
                $errors['name'] = 'Tên tour không được để trống';
            if (empty($_POST['code']))
                $errors['code'] = 'Mã tour không được để trống';
            if (empty($_POST['adult_price']))
                $errors['adult_price'] = 'Giá người lớn không được để trống';
            if (empty($_POST['duration_days']) || $_POST['duration_days'] < 1)
                $errors['duration_days'] = 'Số ngày phải lớn hơn 0';

            // Validate Itinerary Count
            $duration_days = (int) $_POST['duration_days'];
            $itinerary_count = isset($_POST['itinerary_day']) ? count($_POST['itinerary_day']) : 0;
            if ($itinerary_count != $duration_days) {
                $errors['itinerary'] = "Lịch trình phải nhập đủ cho $duration_days ngày (Hiện tại: $itinerary_count ngày)";
            }

            if (!empty($errors)) {
                // Pass old input and errors back to view
                $old_input = $_POST;
                $categories = $this->categoryModel->getForDropdown();
                $destinations = $this->destinationModel->getForDropdown();
                $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];
                $page_title = 'Thêm Tour mới';
                $content_file = VIEWS_PATH . '/admin/tours/create.php';
                require VIEWS_PATH . '/layouts/admin_layout.php';
                return;
            }

            // 2. Prepare Data
            $data = [
                'code' => sanitize($_POST['code']),
                'category_id' => !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null,
                'name' => sanitize($_POST['name']),
                'description' => $_POST['description'] ?? '',
                'duration_days' => (int) $_POST['duration_days'],
                'duration_nights' => (int) $_POST['duration_nights'],
                'departure_location' => sanitize($_POST['departure_location']),
                'adult_price' => (float) $_POST['adult_price'],
                'child_price' => !empty($_POST['child_price']) ? (float) $_POST['child_price'] : 0,
                'infant_price' => !empty($_POST['infant_price']) ? (float) $_POST['infant_price'] : 0,
                'status' => $_POST['status'] ?? 'draft'
            ];

            // 3. Prepare Itinerary Data
            $itinerary = [];
            if (!empty($_POST['itinerary_day'])) {
                foreach ($_POST['itinerary_day'] as $key => $day) {
                    $itinerary[] = [
                        'day' => $day,
                        'title' => sanitize($_POST['itinerary_title'][$key] ?? ''),
                        'description' => $_POST['itinerary_desc'][$key] ?? '',
                        'destination_id' => !empty($_POST['itinerary_dest'][$key]) ? (int) $_POST['itinerary_dest'][$key] : null
                    ];
                }
            }
            $data['itinerary'] = $itinerary;

            // 4. Prepare Highlights
            if (!empty($_POST['highlights'])) {
                $data['highlights'] = array_map('sanitize', explode("\n", $_POST['highlights']));
            }

            // 5. Save to DB
            $tour_id = $this->tourModel->create($data);

            // 6. Handle Images
            if (!empty($_FILES['images']['name'][0])) {
                $this->handleImageUploads($tour_id, $_FILES['images']);
            }

            // [NEW] 7. Handle Services
            if (!empty($_POST['service_ids'])) {
                foreach ($_POST['service_ids'] as $key => $service_id) {
                    if (empty($service_id))
                        continue;

                    $serviceData = [
                        'tour_id' => $tour_id,
                        'service_id' => $service_id,
                        'service_name' => $_POST['service_names'][$key] ?? '', // Fallback or fetch from DB if needed
                        'calculation_type' => $_POST['service_calc_types'][$key] ?? 'per_person',
                        'fixed_quantity' => $_POST['service_quantities'][$key] ?? 1,
                        'unit_price' => $_POST['service_prices'][$key] ?? 0,
                        'unit' => $_POST['service_units'][$key] ?? '',
                        'notes' => $_POST['service_notes'][$key] ?? '',
                        'is_included_in_price' => 1
                    ];
                    $this->tourServiceModel->create($serviceData);
                }
            }

            set_success("Tạo tour thành công!");
            redirect('?act=admin&module=tours');

        } catch (Exception $e) {
            // Handle Exception with old input
            $errors['system'] = $e->getMessage();
            $old_input = $_POST;
            $categories = $this->categoryModel->getForDropdown();
            $destinations = $this->destinationModel->getForDropdown();
            $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];
            $page_title = 'Thêm Tour mới';
            $content_file = VIEWS_PATH . '/admin/tours/create.php';
            require VIEWS_PATH . '/layouts/admin_layout.php';
        }
    }

    /**
     * Form sửa Tour
     */
    public function edit()
    {
        require_admin();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $tour = $this->tourModel->findById($id);

        if (!$tour) {
            set_error("Không tìm thấy tour.");
            redirect('?act=admin&module=tours');
            return;
        }

        $categories = $this->categoryModel->getForDropdown();
        $destinations = $this->destinationModel->getForDropdown();

        $page_title = 'Sửa Tour: ' . htmlspecialchars($tour['name']);
        $content_file = VIEWS_PATH . '/admin/tours/edit.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý cập nhật Tour
     */
    public function update()
    {
        require_admin();

        try {
            $id = (int) $_POST['id'];

            // 1. Prepare Data (Similar to store)
            $data = [
                'category_id' => !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null,
                'name' => sanitize($_POST['name']),
                'description' => $_POST['description'] ?? '',
                'duration_days' => (int) $_POST['duration_days'],
                'duration_nights' => (int) $_POST['duration_nights'],
                'departure_location' => sanitize($_POST['departure_location']),
                'adult_price' => (float) $_POST['adult_price'],
                'child_price' => (float) ($_POST['child_price'] ?? 0),
                'infant_price' => (float) ($_POST['infant_price'] ?? 0),
                'status' => $_POST['status']
            ];

            // Itinerary
            $itinerary = [];
            if (!empty($_POST['itinerary_day'])) {
                foreach ($_POST['itinerary_day'] as $key => $day) {
                    $itinerary[] = [
                        'day' => $day,
                        'title' => sanitize($_POST['itinerary_title'][$key] ?? ''),
                        'description' => $_POST['itinerary_desc'][$key] ?? '',
                        'destination_id' => !empty($_POST['itinerary_dest'][$key]) ? (int) $_POST['itinerary_dest'][$key] : null
                    ];
                }
            }
            $data['itinerary'] = $itinerary;

            // Highlights
            if (!empty($_POST['highlights'])) {
                $data['highlights'] = array_map('sanitize', explode("\n", $_POST['highlights']));
            }

            // 2. Update DB
            $this->tourModel->update($id, $data);

            // 3. Handle New Images
            if (!empty($_FILES['images']['name'][0])) {
                $this->handleImageUploads($id, $_FILES['images']);
            }

            // [NEW] 4. Handle Services (Delete All & Re-insert)
            $this->tourServiceModel->deleteAllByTourId($id);
            if (!empty($_POST['service_ids'])) {
                foreach ($_POST['service_ids'] as $key => $service_id) {
                    if (empty($service_id))
                        continue;

                    $serviceData = [
                        'tour_id' => $id,
                        'service_id' => $service_id,
                        'service_name' => $_POST['service_names'][$key] ?? '',
                        'calculation_type' => $_POST['service_calc_types'][$key] ?? 'per_person',
                        'fixed_quantity' => $_POST['service_quantities'][$key] ?? 1,
                        'unit_price' => $_POST['service_prices'][$key] ?? 0,
                        'unit' => $_POST['service_units'][$key] ?? '',
                        'notes' => $_POST['service_notes'][$key] ?? '',
                        'is_included_in_price' => 1
                    ];
                    $this->tourServiceModel->create($serviceData);
                }
            }

            set_success("Cập nhật tour thành công!");
            redirect('?act=admin&module=tours');

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=tours&action=edit&id=' . $_POST['id']);
        }
    }

    /**
     * Xem chi tiết Tour
     */
    public function show()
    {
        require_admin();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $tour = $this->tourModel->findById($id);

        if (!$tour) {
            set_error("Không tìm thấy tour.");
            redirect('?act=admin&module=tours');
            return;
        }

        $page_title = 'Chi tiết Tour: ' . htmlspecialchars($tour['name']);
        $content_file = VIEWS_PATH . '/admin/tours/show.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Thay đổi trạng thái Tour (Duyệt/Từ chối/Ẩn)
     */
    public function changeStatus()
    {
        require_admin();

        $id = $_POST['id'] ?? 0;
        $action = $_POST['action'] ?? ''; // approve, reject, hide

        if (!$id || !$action) {
            set_error('Dữ liệu không hợp lệ.');
            redirect('?act=admin&module=tours');
            return;
        }

        $tour = $this->tourModel->findById($id);
        if (!$tour) {
            set_error('Không tìm thấy tour.');
            redirect('?act=admin&module=tours');
            return;
        }

        $data = [];
        $message = '';

        // [NEW] Logic to determine data based on action
        switch ($action) {
            case 'approve':
                $data = [
                    'approval_status' => 'approved',
                    'approved_by' => get_user_id(),
                    'approved_at' => date('Y-m-d H:i:s'),
                    'status' => 'active'
                ];
                $message = 'Đã duyệt tour thành công.';
                break;
            case 'reject':
                $data = [
                    'approval_status' => 'rejected',
                    'rejection_reason' => $_POST['reason'] ?? '',
                    'status' => 'draft'
                ];
                $message = 'Đã từ chối tour.';
                break;
            case 'hide':
                $data = ['status' => 'inactive'];
                $message = 'Đã ẩn tour.';
                break;
            case 'show':
                $data = ['status' => 'active'];
                $message = 'Đã hiện tour.';
                break;
            default:
                set_error('Hành động không hợp lệ.');
                redirect('?act=admin&module=tours');
                return;
        }

        if ($this->tourModel->updateStatus($id, $data)) {
            set_success($message);
        } else {
            set_error('Có lỗi xảy ra khi cập nhật trạng thái.');
        }

        redirect('?act=admin&module=tours&action=show&id=' . $id);
    }

    /**
     * Helper: Upload Images
     */
    private function handleImageUploads($tour_id, $files)
    {
        $upload_dir = 'public/uploads/tours/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $count = count($files['name']);

        // Check existing primary
        $existing_images = $this->tourModel->getImages($tour_id);
        $has_primary = false;
        foreach ($existing_images as $img) {
            if ($img['is_primary']) {
                $has_primary = true;
                break;
            }
        }

        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $tmp_name = $files['tmp_name'][$i];
                $name = basename($files['name'][$i]);
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                    continue;

                $new_name = 'tour_' . $tour_id . '_' . uniqid() . '.' . $ext;
                $dest_path = $upload_dir . $new_name;

                if (move_uploaded_file($tmp_name, $dest_path)) {
                    $is_primary = (!$has_primary && $i === 0);
                    $this->tourModel->addImage($tour_id, $dest_path, $is_primary);
                }
            }
        }
    }
}
