<?php
namespace Staff;

/**
 * ==============================================================================
 * TOUR CONTROLLER (STAFF)
 * ==============================================================================
 * 
 * Staff chỉ quản lý Tours do mình tạo
 * 
 * Routing: ?act=staff-tours&action=index
 * 
 * Key Differences từ Admin:
 * - Filter: created_by = current_user_id
 * - Không có changeStatus (approve/reject)
 * - Chỉ edit được tour status = draft hoặc rejected
 * 
 * @version 1.0
 * @date 2024-12-03
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
        require_once MODELS_PATH . '/TourService.php';
        require_once MODELS_PATH . '/Service.php';

        $this->tourModel = new \Tour($pdo);
        $this->categoryModel = new \Category($pdo);
        $this->destinationModel = new \Destination($pdo);
        $this->tourServiceModel = new \TourService($pdo);
        $this->serviceModel = new \Service($pdo);
    }

    /**
     * Danh sách Tours của mình
     */
    public function index()
    {
        // Filters
        $filters = [];
        $filters['created_by'] = get_user_id(); // KEY: Chỉ tours của mình

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

        $page_title = 'Tours Của Tôi';
        $content_file = VIEWS_PATH . '/staff/tours/index.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    /**
     * Form tạo Tour
     */
    public function create()
    {
        $categories = $this->categoryModel->getForDropdown();
        $destinations = $this->destinationModel->getForDropdown();
        $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];

        $page_title = 'Tạo Tour Mới';
        $content_file = VIEWS_PATH . '/staff/tours/create.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    /**
     * Xử lý lưu Tour mới
     */
    public function store()
    {
        try {
            // Validation (giống Admin)
            $errors = [];
            if (empty($_POST['name']))
                $errors['name'] = 'Tên tour không được để trống';
            if (empty($_POST['adult_price']))
                $errors['adult_price'] = 'Giá người lớn không được để trống';
            if (empty($_POST['duration_days']) || $_POST['duration_days'] < 1)
                $errors['duration_days'] = 'Số ngày phải lớn hơn 0';

            // STAFF: Chỉ cho phép status draft hoặc pending
            $status = $_POST['status'] ?? 'draft';
            if (!in_array($status, ['draft', 'pending'])) {
                $errors['status'] = 'Bạn chỉ có thể tạo tour ở trạng thái Nháp hoặc Chờ duyệt';
            }

            // Auto-generate Tour Code
            $tour_code = generateTourCodeUnique($this->db);

            // Validate Duration
            $duration_nights = (int) ($_POST['duration_nights'] ?? 0);
            $duration_days = (int) $_POST['duration_days'];
            if ($duration_nights > $duration_days) {
                $errors['duration_nights'] = 'Số đêm không thể lớn hơn số ngày';
            }

            // Validate Price Logic
            $adult_price = (float) $_POST['adult_price'];
            $child_price = !empty($_POST['child_price']) ? (float) $_POST['child_price'] : 0;
            $infant_price = !empty($_POST['infant_price']) ? (float) $_POST['infant_price'] : 0;

            if ($child_price > $adult_price) {
                $errors['child_price'] = 'Giá trẻ em không được lớn hơn giá người lớn';
            }
            if ($infant_price > $child_price) {
                $errors['infant_price'] = 'Giá em bé không được lớn hơn giá trẻ em';
            }

            // Validate Itinerary Count
            $itinerary_count = isset($_POST['itinerary_day']) ? count($_POST['itinerary_day']) : 0;
            if ($itinerary_count != $duration_days) {
                $errors['itinerary'] = "Lịch trình phải nhập đủ cho $duration_days ngày (Hiện tại: $itinerary_count ngày)";
            }

            // Validate Images
            if (!empty($_FILES['images']['name'][0])) {
                $total_size = 0;
                $count = count($_FILES['images']['name']);

                if ($count > 10) {
                    $errors['images'] = 'Tối đa 10 hình ảnh';
                }

                foreach ($_FILES['images']['size'] as $size) {
                    $total_size += $size;
                }

                if ($total_size > 10 * 1024 * 1024) {
                    $errors['images'] = 'Tổng dung lượng hình ảnh không quá 10MB';
                }
            }

            // If errors, return to form
            if (!empty($errors)) {
                $old_input = $_POST;
                $categories = $this->categoryModel->getForDropdown();
                $destinations = $this->destinationModel->getForDropdown();
                $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];
                $page_title = 'Tạo Tour Mới';
                $content_file = VIEWS_PATH . '/staff/tours/create.php';
                require VIEWS_PATH . '/layouts/staff_layout.php';
                return;
            }

            // Validate Tour Type
            $tour_type = $_POST['tour_type'] ?? 'public';
            if (!in_array($tour_type, ['public', 'custom'])) {
                $errors['tour_type'] = 'Loại tour không hợp lệ';
            }

            // Prepare Data
            $data = [
                'code' => $tour_code,
                'category_id' => !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null,
                'name' => sanitize($_POST['name']),
                'description' => $_POST['description'] ?? '',
                'duration_days' => (int) $_POST['duration_days'],
                'duration_nights' => (int) $_POST['duration_nights'],
                'departure_location' => sanitize($_POST['departure_location']),
                'adult_price' => (float) $_POST['adult_price'],
                'child_price' => !empty($_POST['child_price']) ? (float) $_POST['child_price'] : 0,
                'infant_price' => !empty($_POST['infant_price']) ? (float) $_POST['infant_price'] : 0,
                'tour_type' => $tour_type,
                'status' => $status,
                'created_by' => get_user_id() // KEY: Set created_by
            ];

            // Prepare Itinerary
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

            // Prepare Highlights
            if (!empty($_POST['highlights'])) {
                $data['highlights'] = array_map('sanitize', explode("\n", $_POST['highlights']));
            }

            // Save to DB
            $tour_id = $this->tourModel->create($data);

            // Handle Images
            if (!empty($_FILES['images']['name'][0])) {
                $this->handleImageUploads($tour_id, $_FILES['images']);
            }

            // Handle Services
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

            set_success("Tạo tour thành công! Tour đã được gửi để chờ duyệt.");
            redirect('?act=staff-tours');

        } catch (\Exception $e) {
            $errors['system'] = $e->getMessage();
            $old_input = $_POST;
            $categories = $this->categoryModel->getForDropdown();
            $destinations = $this->destinationModel->getForDropdown();
            $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];
            $page_title = 'Tạo Tour Mới';
            $content_file = VIEWS_PATH . '/staff/tours/create.php';
            require VIEWS_PATH . '/layouts/staff_layout.php';
        }
    }

    /**
     * Form sửa Tour
     */
    public function edit()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $tour = $this->tourModel->findById($id);

        if (!$tour) {
            set_error("Không tìm thấy tour.");
            redirect('?act=staff-tours');
            return;
        }

        // CHECK OWNERSHIP
        if ($tour['created_by'] != get_user_id()) {
            set_error("Bạn không có quyền sửa tour này.");
            redirect('?act=staff-tours');
            return;
        }

        // STAFF: Chỉ edit được tour status = draft hoặc rejected
        if (!in_array($tour['approval_status'], ['draft', 'rejected', 'pending'])) {
            set_error("Bạn chỉ có thể sửa tour ở trạng thái Nháp, Chờ duyệt hoặc Bị từ chối.");
            redirect('?act=staff-tours');
            return;
        }

        $categories = $this->categoryModel->getForDropdown();
        $destinations = $this->destinationModel->getForDropdown();
        $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];

        $page_title = 'Sửa Tour: ' . htmlspecialchars($tour['name']);
        $content_file = VIEWS_PATH . '/staff/tours/edit.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    /**
     * Xử lý cập nhật Tour
     */
    public function update()
    {
        try {
            $id = (int) $_POST['id'];
            $tour = $this->tourModel->findById($id);

            // CHECK OWNERSHIP
            if ($tour['created_by'] != get_user_id()) {
                throw new \Exception('Bạn không có quyền sửa tour này.');
            }

            // STAFF: Chỉ update được tour draft/rejected
            if (!in_array($tour['approval_status'], ['draft', 'rejected', 'pending'])) {
                throw new \Exception('Bạn chỉ có thể sửa tour ở trạng thái Nháp hoặc Bị từ chối.');
            }

            // Validate Tour Type
            $tour_type = $_POST['tour_type'] ?? 'public';
            if (!in_array($tour_type, ['public', 'custom'])) {
                throw new \Exception('Loại tour không hợp lệ');
            }

            // STAFF: Giới hạn status
            $status = $_POST['status'] ?? 'draft';
            if (!in_array($status, ['draft', 'pending'])) {
                throw new \Exception('Bạn chỉ có thể set tour là Nháp hoặc Chờ duyệt');
            }

            // Prepare Data
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
                'tour_type' => $tour_type,
                'status' => $status
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

            // Update DB
            $this->tourModel->update($id, $data);

            // Handle New Images
            if (!empty($_FILES['images']['name'][0])) {
                $this->handleImageUploads($id, $_FILES['images']);
            }

            // Handle Services
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
            redirect('?act=staff-tours');

        } catch (\Exception $e) {
            set_error($e->getMessage());
            redirect('?act=staff-tours&action=edit&id=' . $_POST['id']);
        }
    }

    /**
     * Xem chi tiết Tour
     */
    public function show()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $tour = $this->tourModel->findById($id);

        if (!$tour) {
            set_error("Không tìm thấy tour.");
            redirect('?act=staff-tours');
            return;
        }

        // CHECK OWNERSHIP
        if ($tour['created_by'] != get_user_id()) {
            set_error("Bạn không có quyền xem tour này.");
            redirect('?act=staff-tours');
            return;
        }

        $page_title = 'Chi tiết Tour: ' . htmlspecialchars($tour['name']);
        $content_file = VIEWS_PATH . '/staff/tours/show.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
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
