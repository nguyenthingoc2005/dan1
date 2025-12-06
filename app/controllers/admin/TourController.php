<?php
/**
 * ==============================================================================
 * TOUR CONTROLLER (ADMIN) - HOÀN TOÀN MỚI THEO FLOW ANALYSIS
 * ==============================================================================
 * 
 * Quản lý Tour du lịch theo flow mới
 * Routing: ?act=admin&module=tours
 * 
 * Features:
 * - CRUD Tours (6-step Wizard Form)
 * - Manage Itinerary với Timeline chi tiết
 * - Manage Day Services (thay thế tour_services)
 * - Manage Policies
 * - Pricing Calculation mới (không markup)
 * 
 * @version 2.0
 * @date 2024-12-06
 * ==============================================================================
 */

class TourController
{
    private $db;
    private $tourModel;
    private $destinationModel;
    private $serviceModel;
    private $serviceProviderModel;
    private $policyModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/Tour.php';
        require_once MODELS_PATH . '/Destination.php';
        require_once MODELS_PATH . '/Service.php';
        require_once MODELS_PATH . '/ServiceProvider.php';
        require_once MODELS_PATH . '/Policy.php';
        require_once COMMON_PATH . '/PricingHelper.php';

        $this->tourModel = new Tour($pdo);
        $this->destinationModel = new Destination($pdo);
        $this->serviceModel = new Service($pdo);
        $this->serviceProviderModel = new ServiceProvider($pdo);
        $this->policyModel = new Policy($pdo);
    }

    /**
     * Danh sách Tours
     * ĐÃ XÓA: category filter
     */
    public function index()
    {
        require_admin();

        // Filters
        $filters = [];
        if (!empty($_GET['status']))
            $filters['status'] = sanitize($_GET['status']);
        if (!empty($_GET['approval_status']))
            $filters['approval_status'] = sanitize($_GET['approval_status']);
        if (!empty($_GET['tour_type']))
            $filters['tour_type'] = sanitize($_GET['tour_type']);
        if (!empty($_GET['search']))
            $filters['search'] = sanitize($_GET['search']);

        // Pagination
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $result = $this->tourModel->getAll($filters, $page, 10);

        $tours = $result['data'];
        $total = $result['total'];
        $total_pages = $result['pages'];
        $current_page = $result['current_page'];

        $page_title = 'Quản lý Tour';
        $content_file = VIEWS_PATH . '/admin/tours/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

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
     * Form tạo Tour (Public - từ đầu)
     */
    public function create()
    {
        require_admin();

        // Load dropdown data
        $destinations = $this->destinationModel->getForDropdown();
        $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];
        $service_providers = $this->serviceProviderModel->getForDropdown();
        $policies = $this->policyModel->getAll(['status' => 'active']);

        $page_title = 'Tạo Tour Mới';
        $content_file = VIEWS_PATH . '/admin/tours/create.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form tạo Tour từ Template (Clone & Customize - Custom)
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

        // Load dropdown data
        $destinations = $this->destinationModel->getForDropdown();
        $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];
        $service_providers = $this->serviceProviderModel->getForDropdown();
        $policies = $this->policyModel->getAll(['status' => 'active']);

        // Prepare old_input từ template để pre-fill form
        $old_input = [
            'name' => '[Custom] ' . $template['name'],
            'introduction' => $template['introduction'] ?? null,
            'description' => $template['description'] ?? null,
            'duration_days' => $template['duration_days'],
            'duration_nights' => $template['duration_nights'] ?? 0,
            'departure_location' => $template['departure_location'] ?? null,
            'min_participants' => $template['min_participants'] ?? 15,
            'max_participants' => $template['max_participants'] ?? 45,
            'adult_price' => $template['adult_price'],
            'child_price' => $template['child_price'] ?? 0,
            'infant_price' => $template['infant_price'] ?? 0,
            'deposit_percentage' => $template['deposit_percentage'] ?? 30,
            'booking_deadline_days' => $template['booking_deadline_days'] ?? 1,
            'fixed_cost_guide' => $template['fixed_cost_guide'] ?? 0,
            'fixed_cost_management' => $template['fixed_cost_management'] ?? 0,
            'fixed_cost_marketing' => $template['fixed_cost_marketing'] ?? 0,
            'fixed_cost_other' => $template['fixed_cost_other'] ?? 0,
            'tour_type' => 'custom',
            'status' => 'draft',
            'parent_tour_id' => $template_id,
            'itinerary' => $template['itinerary'] ?? [],
            'itinerary_timelines' => $template['itinerary_timelines'] ?? [],
            'itinerary_day_services' => $template['itinerary_day_services'] ?? [],
            'highlights' => $template['highlights'] ?? [],
            'includes' => $template['includes'] ?? [],
            'excludes' => $template['excludes'] ?? [],
            'policy_ids' => array_column($template['policies'] ?? [], 'id')
        ];

        $is_from_template = true;
        $template_info = [
            'id' => $template['id'],
            'code' => $template['tour_code'],
            'name' => $template['name']
        ];

        $page_title = 'Tạo Tour Custom từ Template';
        $content_file = VIEWS_PATH . '/admin/tours/create.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý lưu Tour mới - THEO FLOW MỚI
     */
    public function store()
    {
        require_admin();

        try {
            // 1. Validation
            $errors = $this->validateTourData($_POST);

            if (!empty($errors)) {
                $old_input = $_POST;
                $destinations = $this->destinationModel->getForDropdown();
                $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];
                $service_providers = $this->serviceProviderModel->getForDropdown();
                $policies = $this->policyModel->getAll(['status' => 'active']);
                $page_title = 'Thêm Tour mới';
                $content_file = VIEWS_PATH . '/admin/tours/create.php';
                require VIEWS_PATH . '/layouts/admin_layout.php';
                return;
            }

            // 2. Auto-generate Tour Code
            $tour_code = $this->generateTourCodeUnique();

            // 3. Calculate pricing từ PricingHelper
            $fixed_costs = [
                'guide' => (float) ($_POST['fixed_cost_guide'] ?? 0),
                'management' => (float) ($_POST['fixed_cost_management'] ?? 0),
                'marketing' => (float) ($_POST['fixed_cost_marketing'] ?? 0),
                'other' => (float) ($_POST['fixed_cost_other'] ?? 0)
            ];
            $min_participants = (int) ($_POST['min_participants'] ?? 15);

            // Tính estimated_cost (sẽ tính lại sau khi có day_services)
            $estimated_cost_per_person = null;

            // 4. Prepare Tour Data - ĐÃ XÓA category_id, price_based_on_pax
            $data = [
                'code' => $tour_code,
                'name' => sanitize($_POST['name']),
                'introduction' => sanitize($_POST['introduction'] ?? ''),
                'description' => $_POST['description'] ?? '',
                'duration_days' => (int) $_POST['duration_days'],
                'duration_nights' => (int) ($_POST['duration_nights'] ?? 0),
                'departure_location' => sanitize($_POST['departure_location'] ?? ''),
                'min_participants' => $min_participants,
                'max_participants' => (int) ($_POST['max_participants'] ?? 45),
                'adult_price' => (float) $_POST['adult_price'],
                'child_price' => (float) ($_POST['child_price'] ?? 0),
                'infant_price' => (float) ($_POST['infant_price'] ?? 0),
                'estimated_cost_per_person' => $estimated_cost_per_person,
                'deposit_percentage' => (float) ($_POST['deposit_percentage'] ?? 30),
                'booking_deadline_days' => (int) ($_POST['booking_deadline_days'] ?? 1),
                'fixed_cost_guide' => $fixed_costs['guide'],
                'fixed_cost_management' => $fixed_costs['management'],
                'fixed_cost_marketing' => $fixed_costs['marketing'],
                'fixed_cost_other' => $fixed_costs['other'],
                'tour_type' => $_POST['tour_type'] ?? 'public',
                'approval_status' => ($_POST['status'] ?? 'draft') == 'pending' ? 'pending' : null,
                'status' => $_POST['status'] ?? 'draft',
                'parent_tour_id' => !empty($_POST['parent_tour_id']) ? (int) $_POST['parent_tour_id'] : null
            ];

            // 5. Prepare Itinerary Data
            $data['itinerary'] = $this->prepareItineraryData($_POST);

            // 6. Prepare Itinerary Timelines Data (MỚI)
            $data['itinerary_timelines'] = $this->prepareItineraryTimelinesData($_POST, $data['itinerary']);

            // 7. Prepare Itinerary Day Services Data (MỚI)
            $data['itinerary_day_services'] = $this->prepareItineraryDayServicesData($_POST, $data['itinerary']);

            // 8. Prepare Highlights
            if (!empty($_POST['highlights'])) {
                $highlights = is_array($_POST['highlights'])
                    ? $_POST['highlights']
                    : explode("\n", $_POST['highlights']);
                $data['highlights'] = array_filter(array_map('trim', $highlights));
            }

            // 9. Prepare Included/Excluded
            if (!empty($_POST['included'])) {
                $included = is_array($_POST['included']) ? $_POST['included'] : [$_POST['included']];
                $data['included'] = array_filter(array_map('trim', $included));
            }
            if (!empty($_POST['excluded'])) {
                $excluded = is_array($_POST['excluded']) ? $_POST['excluded'] : [$_POST['excluded']];
                $data['excluded'] = array_filter(array_map('trim', $excluded));
            }

            // 10. Prepare Policy IDs (MỚI)
            if (!empty($_POST['policy_ids'])) {
                $data['policy_ids'] = array_map('intval', $_POST['policy_ids']);
            }

            // 11. Save Tour
            $tour_id = $this->tourModel->create($data);

            // 12. Calculate và update estimated_cost_per_person sau khi có day_services
            require_once COMMON_PATH . '/PricingHelper.php';
            $estimated_cost = calculateTotalCostPerPerson(
                $this->db,
                $tour_id,
                $fixed_costs,
                $min_participants
            );
            $this->tourModel->updateStatus($tour_id, ['estimated_cost_per_person' => $estimated_cost]);

            // 13. Handle Images
            if (!empty($_FILES['images']['name'][0])) {
                $this->handleImageUploads($tour_id, $_FILES['images']);
            }

            set_success("Tạo tour thành công!");
            redirect('?act=admin&module=tours');

        } catch (Exception $e) {
            error_log("TourController::store() Error: " . $e->getMessage());
            $errors = ['system' => $e->getMessage()];
            $old_input = $_POST;
            $destinations = $this->destinationModel->getForDropdown();
            $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];
            $service_providers = $this->serviceProviderModel->getForDropdown();
            $policies = $this->policyModel->getAll(['status' => 'active']);
            $page_title = 'Thêm Tour mới';
            $content_file = VIEWS_PATH . '/admin/tours/create.php';
            require VIEWS_PATH . '/layouts/admin_layout.php';
        }
    }

    /**
     * Validate Tour Data
     */
    private function validateTourData($post)
    {
        $errors = [];

        // Basic validation
        if (empty($post['name']) || strlen(trim($post['name'])) < 2) {
            $errors['name'] = 'Tên tour phải có ít nhất 2 ký tự';
        }

        if (empty($post['duration_days']) || (int) $post['duration_days'] < 1) {
            $errors['duration_days'] = 'Số ngày phải lớn hơn 0';
        }

        $duration_days = (int) ($post['duration_days'] ?? 0);
        $duration_nights = (int) ($post['duration_nights'] ?? 0);
        if ($duration_nights > $duration_days) {
            $errors['duration_nights'] = 'Số đêm không thể lớn hơn số ngày';
        }

        if (empty($post['adult_price']) || (float) $post['adult_price'] <= 0) {
            $errors['adult_price'] = 'Giá người lớn phải lớn hơn 0';
        }

        $adult_price = (float) ($post['adult_price'] ?? 0);
        $child_price = (float) ($post['child_price'] ?? 0);
        $infant_price = (float) ($post['infant_price'] ?? 0);

        if ($child_price > $adult_price) {
            $errors['child_price'] = 'Giá trẻ em không được lớn hơn giá người lớn';
        }
        if ($infant_price > $child_price) {
            $errors['infant_price'] = 'Giá em bé không được lớn hơn giá trẻ em';
        }

        // Validate Itinerary count
        if (!empty($post['itinerary_day_number'])) {
            $itinerary_count = count($post['itinerary_day_number']);
            if ($itinerary_count != $duration_days) {
                $errors['itinerary'] = "Lịch trình phải nhập đủ cho $duration_days ngày (Hiện tại: $itinerary_count ngày)";
            }
        }

        // Validate Images
        if (!empty($_FILES['images']['name'][0])) {
            $count = count($_FILES['images']['name']);
            if ($count > 10) {
                $errors['images'] = 'Tối đa 10 hình ảnh';
            }

            $total_size = 0;
            foreach ($_FILES['images']['size'] as $size) {
                $total_size += $size;
            }
            if ($total_size > 10 * 1024 * 1024) {
                $errors['images'] = 'Tổng dung lượng hình ảnh không quá 10MB';
            }
        }

        // Validate Status
        $status = $post['status'] ?? 'draft';
        if (!in_array($status, ['draft', 'pending', 'active'])) {
            $errors['status'] = 'Trạng thái không hợp lệ';
        }

        return $errors;
    }

    /**
     * Prepare Itinerary Data từ POST
     */
    private function prepareItineraryData($post)
    {
        $itinerary = [];

        if (!empty($post['itinerary_day_number'])) {
            foreach ($post['itinerary_day_number'] as $key => $day_number) {
                $itinerary[] = [
                    'day_number' => (int) $day_number,
                    'title' => sanitize($post['itinerary_title'][$key] ?? ''),
                    'description' => $post['itinerary_description'][$key] ?? '',
                    'destination_id' => !empty($post['itinerary_destination'][$key])
                        ? (int) $post['itinerary_destination'][$key]
                        : null
                ];
            }
        }

        return $itinerary;
    }

    /**
     * Prepare Itinerary Timelines Data từ POST (MỚI)
     */
    private function prepareItineraryTimelinesData($post, $itinerary)
    {
        $timelines = [];

        if (!empty($post['timeline_day_number'])) {
            foreach ($post['timeline_day_number'] as $key => $day_number) {
                $timelines[] = [
                    'day_number' => (int) $day_number,
                    'timeline_time' => $post['timeline_time'][$key] ?? '',
                    'activity_title' => sanitize($post['timeline_activity_title'][$key] ?? ''),
                    'activity_description' => $post['timeline_activity_description'][$key] ?? '',
                    'location' => sanitize($post['timeline_location'][$key] ?? ''),
                    'destination_id' => !empty($post['timeline_destination'][$key])
                        ? (int) $post['timeline_destination'][$key]
                        : null,
                    'service_provider_id' => !empty($post['timeline_service_provider'][$key])
                        ? (int) $post['timeline_service_provider'][$key]
                        : null,
                    'service_id' => !empty($post['timeline_service'][$key])
                        ? (int) $post['timeline_service'][$key]
                        : null,
                    'timeline_type' => $post['timeline_type'][$key] ?? 'activity',
                    'display_order' => (int) ($post['timeline_display_order'][$key] ?? 0),
                    'notes' => sanitize($post['timeline_notes'][$key] ?? '')
                ];
            }
        }

        return $timelines;
    }

    /**
     * Prepare Itinerary Day Services Data từ POST (MỚI)
     */
    private function prepareItineraryDayServicesData($post, $itinerary)
    {
        $services = [];

        if (!empty($post['day_service_day_number'])) {
            foreach ($post['day_service_day_number'] as $key => $day_number) {
                $services[] = [
                    'day_number' => (int) $day_number,
                    'service_id' => (int) $post['day_service_service_id'][$key],
                    'service_provider_id' => !empty($post['day_service_provider_id'][$key])
                        ? (int) $post['day_service_provider_id'][$key]
                        : null,
                    'service_name' => sanitize($post['day_service_name'][$key] ?? ''),
                    'unit_price' => (float) $post['day_service_unit_price'][$key],
                    'quantity' => (float) ($post['day_service_quantity'][$key] ?? 1),
                    'unit' => sanitize($post['day_service_unit'][$key] ?? ''),
                    'is_included_in_price' => isset($post['day_service_included'][$key]) ? 1 : 0,
                    'notes' => sanitize($post['day_service_notes'][$key] ?? '')
                ];
            }
        }

        return $services;
    }

    /**
     * Auto-generate unique tour code
     */
    private function generateTourCodeUnique()
    {
        $date = date('Ymd');
        $prefix = "TOUR-{$date}-";

        $stmt = $this->db->prepare("
            SELECT tour_code FROM tours 
            WHERE tour_code LIKE :pattern 
            ORDER BY tour_code DESC LIMIT 1
        ");
        $stmt->execute(['pattern' => $prefix . '%']);
        $latest = $stmt->fetch();

        if ($latest) {
            $num = (int) substr($latest['tour_code'], -3);
            $num++;
        } else {
            $num = 1;
        }

        return $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);
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

                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                    continue;

                $new_name = 'tour_' . $tour_id . '_' . uniqid() . '.' . $ext;
                $dest_path = $upload_dir . $new_name;

                if (move_uploaded_file($tmp_name, $dest_path)) {
                    $is_primary = (!$has_primary && $i === 0);
                    $this->tourModel->addImage($tour_id, $dest_path, $is_primary);
                    if ($is_primary)
                        $has_primary = true;
                }
            }
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
        $action = $_POST['action'] ?? '';

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
     * Xóa Tour
     */
    public function delete()
    {
        require_admin();
        $id = $_GET['id'] ?? 0;

        $tour = $this->tourModel->findById($id);
        if (!$tour) {
            set_error('Tour không tồn tại!');
            redirect('?act=admin&module=tours');
        }

        // Check for dependencies
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM bookings WHERE tour_id = :id");
        $stmt->execute(['id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            set_error('Không thể xóa tour này vì đã có Booking phát sinh.');
            redirect('?act=admin&module=tours');
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tour_schedules WHERE tour_id = :id");
        $stmt->execute(['id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            set_error('Không thể xóa tour này vì đang có Lịch khởi hành.');
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
    // AJAX ENDPOINTS
    // ==========================================================================

    /**
     * AJAX: Lấy thông tin service theo ID
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
            // Get default price từ service_prices nếu có
            $stmt = $this->db->prepare("
                SELECT unit_price FROM service_prices 
                WHERE service_id = :service_id AND status = 'active'
                ORDER BY created_at DESC LIMIT 1
            ");
            $stmt->execute(['service_id' => $service_id]);
            $price = $stmt->fetch();

            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $service['id'],
                    'name' => $service['name'],
                    'unit_price' => $price ? (float) $price['unit_price'] : 0,
                    'unit' => $service['unit'] ?? ''
                ]
            ]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    /**
     * AJAX: Lấy destinations
     */
    public function getDestinations()
    {
        require_admin();
        header('Content-Type: application/json');

        $destinations = $this->destinationModel->getForDropdown();

        echo json_encode([
            'success' => true,
            'data' => $destinations
        ]);
        exit;
    }

    /**
     * AJAX: Lấy service providers theo destination hoặc service type
     */
    public function getServiceProviders()
    {
        require_admin();
        header('Content-Type: application/json');

        $filters = [];
        if (!empty($_GET['destination_id'])) {
            // Get province_id từ destination
            $stmt = $this->db->prepare("SELECT province_id FROM destinations WHERE id = :id");
            $stmt->execute(['id' => (int) $_GET['destination_id']]);
            $dest = $stmt->fetch();
            if ($dest) {
                $filters['province_id'] = $dest['province_id'];
            }
        }
        if (!empty($_GET['service_type_id'])) {
            $filters['service_type_id'] = (int) $_GET['service_type_id'];
        }

        $providers = $this->serviceProviderModel->getForDropdown(
            $filters['province_id'] ?? null,
            $filters['country_id'] ?? null
        );

        echo json_encode([
            'success' => true,
            'data' => $providers
        ]);
        exit;
    }

    /**
     * AJAX: Tạo policy mới
     */
    public function createPolicy()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if (empty($_POST['name'])) {
                throw new Exception('Tên chính sách không được để trống');
            }
            if (empty($_POST['content'])) {
                throw new Exception('Nội dung chính sách không được để trống');
            }

            $data = [
                'name' => sanitize($_POST['name']),
                'description' => sanitize($_POST['description'] ?? ''),
                'policy_type' => sanitize($_POST['policy_type'] ?? 'other'),
                'content' => $_POST['content'], // Rich text, không sanitize
                'status' => 'active'
            ];

            $policy_id = $this->policyModel->create($data);

            if ($policy_id) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Tạo chính sách thành công',
                    'policy_id' => $policy_id
                ]);
            } else {
                throw new Exception('Không thể tạo chính sách');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * AJAX: Lấy policy để preview
     */
    public function getPolicy()
    {
        require_admin();
        header('Content-Type: application/json');

        $id = $_GET['id'] ?? 0;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID']);
            exit;
        }

        $policy = $this->policyModel->findById($id);
        if ($policy) {
            echo json_encode([
                'success' => true,
                'data' => $policy
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Không tìm thấy chính sách'
            ]);
        }
        exit;
    }
}
