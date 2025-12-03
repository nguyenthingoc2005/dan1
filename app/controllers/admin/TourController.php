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
            // Mã tour sẽ tự động generate, không cần validate
            if (empty($_POST['adult_price']))
                $errors['adult_price'] = 'Giá người lớn không được để trống';
            if (empty($_POST['duration_days']) || $_POST['duration_days'] < 1)
                $errors['duration_days'] = 'Số ngày phải lớn hơn 0';

            // 2. Validate Status (chỉ cho phép draft hoặc pending khi tạo mới)
            $status = $_POST['status'] ?? 'draft';
            if (!in_array($status, ['draft', 'pending'])) {
                $errors['status'] = 'Tour mới chỉ có thể là trạng thái Nháp hoặc Chờ duyệt';
            }

            // 3. Auto-generate Tour Code
            $tour_code = generateTourCodeUnique($this->db);

            // 4. Validate Duration: nights <= days
            $duration_nights = (int) ($_POST['duration_nights'] ?? 0);
            $duration_days = (int) $_POST['duration_days'];
            if ($duration_nights > $duration_days) {
                $errors['duration_nights'] = 'Số đêm không thể lớn hơn số ngày';
            }

            // 5. Validate Price Logic: adult >= child >= infant
            $adult_price = (float) $_POST['adult_price'];
            $child_price = !empty($_POST['child_price']) ? (float) $_POST['child_price'] : 0;
            $infant_price = !empty($_POST['infant_price']) ? (float) $_POST['infant_price'] : 0;

            if ($child_price > $adult_price) {
                $errors['child_price'] = 'Giá trẻ em không được lớn hơn giá người lớn';
            }
            if ($infant_price > $child_price) {
                $errors['infant_price'] = 'Giá em bé không được lớn hơn giá trẻ em';
            }

            // 6. Validate Itinerary Count
            $itinerary_count = isset($_POST['itinerary_day']) ? count($_POST['itinerary_day']) : 0;
            if ($itinerary_count != $duration_days) {
                $errors['itinerary'] = "Lịch trình phải nhập đủ cho $duration_days ngày (Hiện tại: $itinerary_count ngày)";
            }

            // 7. Validate File Uploads (images)
            if (!empty($_FILES['images']['name'][0])) {
                $total_size = 0;
                $count = count($_FILES['images']['name']);

                if ($count > 10) {
                    $errors['images'] = 'Tối đa 10 hình ảnh';
                }

                foreach ($_FILES['images']['size'] as $size) {
                    $total_size += $size;
                }

                if ($total_size > 10 * 1024 * 1024) { // 10MB
                    $errors['images'] = 'Tổng dung lượng hình ảnh không quá 10MB';
                }
            }

            // 8. Validate Service IDs (nếu có chọn services)
            if (!empty($_POST['service_ids'])) {
                foreach ($_POST['service_ids'] as $key => $service_id) {
                    if (!empty($service_id)) {
                        $service = $this->serviceModel->findById($service_id);
                        if (!$service) {
                            $errors['services'] = "Dịch vụ ID $service_id không tồn tại";
                            break;
                        }
                    }
                }
            }

            // 9. If errors, return to form with old input
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

            // 2. Validate Tour Type
            $tour_type = $_POST['tour_type'] ?? 'public';
            if (!in_array($tour_type, ['public', 'custom'])) {
                $errors['tour_type'] = 'Loại tour không hợp lệ';
            }

            // 2. Prepare Data
            $data = [
                'code' => $tour_code,
                'category_id' => !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null,
                'name' => sanitize($_POST['name']),
                'description' => $_POST['description'] ?? '',
                'duration_days' => (int) $_POST['duration_days'],
                'duration_nights' => (int) $_POST['duration_nights'],
                'departure_location' => sanitize($_POST['departure_location']),
                'min_participants' => (int) ($_POST['min_participants'] ?? 10),
                'max_participants' => (int) ($_POST['max_participants'] ?? 45),
                'price_based_on_pax' => (int) ($_POST['price_based_on_pax'] ?? 30),
                'adult_price' => (float) $_POST['adult_price'],
                'child_price' => !empty($_POST['child_price']) ? (float) $_POST['child_price'] : 0,
                'infant_price' => !empty($_POST['infant_price']) ? (float) $_POST['infant_price'] : 0,
                'deposit_percentage' => (float) ($_POST['deposit_percentage'] ?? 30),
                'tour_type' => $tour_type,
                'status' => $_POST['status'] ?? 'draft',
                'parent_tour_id' => !empty($_POST['parent_tour_id']) ? (int) $_POST['parent_tour_id'] : null
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

            // 5. Prepare Included/Excluded
            if (!empty($_POST['included'])) {
                $data['included'] = array_filter(array_map('sanitize', $_POST['included']));
            }
            if (!empty($_POST['excluded'])) {
                $data['excluded'] = array_filter(array_map('sanitize', $_POST['excluded']));
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
                        'service_name' => $_POST['service_names'][$key] ?? '',
                        'calculation_type' => $_POST['service_calc_types'][$key] ?? 'per_person',
                        'fixed_quantity' => !empty($_POST['service_quantities'][$key]) ? (int) $_POST['service_quantities'][$key] : 1,
                        'unit_price' => !empty($_POST['service_prices'][$key]) ? (float) $_POST['service_prices'][$key] : 0,
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

            // 1. Validate Tour Type
            $tour_type = $_POST['tour_type'] ?? 'public';
            if (!in_array($tour_type, ['public', 'custom'])) {
                throw new Exception('Loại tour không hợp lệ');
            }

            // 2. Prepare Data
            $data = [
                'category_id' => !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null,
                'name' => sanitize($_POST['name']),
                'description' => $_POST['description'] ?? '',
                'duration_days' => (int) $_POST['duration_days'],
                'duration_nights' => (int) $_POST['duration_nights'],
                'departure_location' => sanitize($_POST['departure_location']),
                'min_participants' => (int) ($_POST['min_participants'] ?? 10),
                'max_participants' => (int) ($_POST['max_participants'] ?? 45),
                'price_based_on_pax' => (int) ($_POST['price_based_on_pax'] ?? 30),
                'adult_price' => (float) $_POST['adult_price'],
                'child_price' => (float) ($_POST['child_price'] ?? 0),
                'infant_price' => (float) ($_POST['infant_price'] ?? 0),
                'deposit_percentage' => (float) ($_POST['deposit_percentage'] ?? 30),
                'tour_type' => $tour_type,
                'status' => $_POST['status']
            ];

            // 3. Itinerary
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

            // 4. Highlights
            if (!empty($_POST['highlights'])) {
                $data['highlights'] = array_map('sanitize', explode("\n", $_POST['highlights']));
            }

            // 5. Included/Excluded
            if (isset($_POST['included'])) {
                $data['included'] = array_filter(array_map('sanitize', $_POST['included']));
            }
            if (isset($_POST['excluded'])) {
                $data['excluded'] = array_filter(array_map('sanitize', $_POST['excluded']));
            }

            // 6. Update DB
            $this->tourModel->update($id, $data);

            // 7. Handle New Images
            if (!empty($_FILES['images']['name'][0])) {
                $this->handleImageUploads($id, $_FILES['images']);
            }

            // 8. Handle Services (Delete All & Re-insert)
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
                        'fixed_quantity' => !empty($_POST['service_quantities'][$key]) ? (int) $_POST['service_quantities'][$key] : 1,
                        'unit_price' => !empty($_POST['service_prices'][$key]) ? (float) $_POST['service_prices'][$key] : 0,
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
    /**
     * Xóa Tour
     */
    public function delete()
    {
        require_admin();
        $id = $_GET['id'] ?? 0;

        // Check if tour exists
        $tour = $this->tourModel->findById($id);
        if (!$tour) {
            set_error('Tour không tồn tại!');
            redirect('?act=admin&module=tours');
        }

        // Check for dependencies (Bookings)
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM bookings WHERE tour_id = :id");
        $stmt->execute(['id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            set_error('Không thể xóa tour này vì đã có Booking phát sinh. Hãy chuyển trạng thái sang "Ngừng hoạt động".');
            redirect('?act=admin&module=tours');
        }

        // Check for Schedules
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tour_schedules WHERE tour_id = :id");
        $stmt->execute(['id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            set_error('Không thể xóa tour này vì đang có Lịch khởi hành. Hãy xóa lịch trước.');
            redirect('?act=admin&module=tours');
        }

        if ($this->tourModel->delete($id)) {
            set_success('Đã xóa tour thành công!');
        } else {
            set_error('Có lỗi xảy ra khi xóa tour.');
        }
        redirect('?act=admin&module=tours');
    }

    // ==========================================================================
    // CUSTOM TOUR FROM TEMPLATE
    // ==========================================================================

    /**
     * Hiển thị form chọn template hoặc tạo mới
     */
    public function selectTemplate()
    {
        require_admin();

        // Lấy danh sách templates (public + approved tours)
        $templates = $this->tourModel->getTemplates();

        $page_title = 'Tạo Tour - Chọn phương thức';
        $content_file = VIEWS_PATH . '/admin/tours/select_template.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form tạo Tour từ Template (Clone & Customize)
     */
    public function createFromTemplate()
    {
        require_admin();

        $template_id = $_GET['template_id'] ?? 0;

        if (!$template_id) {
            set_error('Vui lòng chọn một template.');
            redirect('?act=admin&module=tours&action=selectTemplate');
            return;
        }

        // Lấy thông tin đầy đủ của template
        $template = $this->tourModel->getForClone($template_id);
        if (!$template) {
            set_error('Không tìm thấy tour template.');
            redirect('?act=admin&module=tours&action=selectTemplate');
            return;
        }

        // Prepare old_input từ template để pre-fill form
        $old_input = [
            'name' => '[Custom] ' . $template['name'],
            'category_id' => $template['category_id'],
            'description' => $template['description'],
            'duration_days' => $template['duration_days'],
            'duration_nights' => $template['duration_nights'],
            'departure_location' => $template['departure_location'],
            'adult_price' => $template['adult_price'],
            'child_price' => $template['child_price'],
            'infant_price' => $template['infant_price'],
            'tour_type' => 'custom', // Force custom type
            'status' => 'draft',
            'parent_tour_id' => $template_id,
            'itinerary' => $template['itinerary'] ?? [],
            'highlights' => $template['highlights'] ?? [],
            'services' => $template['services'] ?? []
        ];

        $categories = $this->categoryModel->getForDropdown();
        $destinations = $this->destinationModel->getForDropdown();
        $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];

        $is_from_template = true;
        $template_info = [
            'id' => $template['id'],
            'code' => $template['tour_code'],
            'name' => $template['name']
        ];

        $page_title = 'Tạo Tour Custom từ Template';
        $content_file = VIEWS_PATH . '/admin/tours/create_from_template.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * API: Lấy thông tin tour để clone (AJAX)
     */
    public function getTemplateData()
    {
        require_admin();

        header('Content-Type: application/json');

        $template_id = $_GET['id'] ?? 0;
        if (!$template_id) {
            echo json_encode(['success' => false, 'message' => 'ID template không hợp lệ']);
            exit;
        }

        $template = $this->tourModel->getForClone($template_id);
        if (!$template) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy template']);
            exit;
        }

        // Format data for JavaScript
        $data = [
            'success' => true,
            'tour' => [
                'id' => $template['id'],
                'tour_code' => $template['tour_code'],
                'name' => $template['name'],
                'category_id' => $template['category_id'],
                'description' => $template['description'],
                'duration_days' => $template['duration_days'],
                'duration_nights' => $template['duration_nights'],
                'departure_location' => $template['departure_location'],
                'adult_price' => $template['adult_price'],
                'child_price' => $template['child_price'],
                'infant_price' => $template['infant_price']
            ],
            'itinerary' => $template['itinerary'] ?? [],
            'services' => $template['services'] ?? [],
            'highlights' => $template['highlights'] ?? []
        ];

        echo json_encode($data);
        exit;
    }

    /**
     * API: Lấy destinations theo category (AJAX)
     */
    public function getDestinations()
    {
        require_admin();
        header('Content-Type: application/json');

        $category_id = $_GET['category_id'] ?? null;
        $destinations = $this->destinationModel->getByCategory($category_id);

        echo json_encode([
            'success' => true,
            'data' => $destinations
        ]);
        exit;
    }

    /**
     * API: Lấy thông tin service theo ID (AJAX)
     */
    public function getServiceInfo()
    {
        require_admin();
        header('Content-Type: application/json');

        $service_id = $_GET['id'] ?? 0;
        if (!$service_id) {
            echo json_encode(['success' => false]);
            exit;
        }

        $service = $this->serviceModel->findById($service_id);
        if ($service) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $service['id'],
                    'name' => $service['name'],
                    'unit_price' => $service['unit_price'],
                    'unit' => $service['unit']
                ]
            ]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }
}
