<?php
namespace Staff;

/**
 * ==============================================================================
 * TOUR CONTROLLER (STAFF) - HOÀN TOÀN MỚI THEO FLOW ANALYSIS
 * ==============================================================================
 * 
 * Staff chỉ quản lý Tours do mình tạo
 * 
 * Routing: ?act=staff-tours&action=index
 * 
 * Features:
 * - CRUD Tours (6-step Wizard Form) - GIỐNG ADMIN
 * - Manage Itinerary với TinyMCE editor
 * - Manage Day Services (dịch vụ theo ngày)
 * - Manage Policies
 * - Pricing Calculation (tự động tính giá)
 * 
 * Key Differences từ Admin:
 * - Filter: created_by = current_user_id
 * - Không có changeStatus (approve/reject) - chỉ admin mới có
 * - Không có delete - chỉ admin mới có
 * - Chỉ edit được tour status = draft, rejected, hoặc pending
 * - Chỉ tạo được tour với status = draft hoặc pending
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

        $this->tourModel = new \Tour($pdo);
        $this->destinationModel = new \Destination($pdo);
        $this->serviceModel = new \Service($pdo);
        $this->serviceProviderModel = new \ServiceProvider($pdo);
        $this->policyModel = new \Policy($pdo);
    }

    /**
     * Danh sách Tours của mình
     * ĐÃ XÓA: category filter (giống admin)
     */
    public function index()
    {
        // Filters
        $filters = [];
        $filters['created_by'] = get_user_id(); // KEY: Chỉ tours của mình

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

        $page_title = 'Tours Của Tôi';
        $content_file = VIEWS_PATH . '/staff/tours/index.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    /**
     * Hiển thị form chọn template hoặc tạo mới
     */
    public function selectTemplate()
    {
        // Lấy danh sách templates (public + approved tours)
        $templates = $this->tourModel->getTemplates();

        $page_title = 'Tạo Tour - Chọn phương thức';
        $content_file = VIEWS_PATH . '/staff/tours/select_template.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    /**
     * Form tạo Tour (Public - từ đầu)
     */
    public function create()
    {
        // Khởi tạo session nếu chưa có
        $this->initTourSession();

        // Load dữ liệu từ session (nếu có) để restore khi reload
        $session_data = $this->loadTourSession();

        // Merge session data vào old_input để pre-fill form
        $old_input = $session_data;

        // Load dropdown data
        $destinations = $this->destinationModel->getForDropdown();
        $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];
        $service_providers = $this->serviceProviderModel->getForDropdown();
        $policies = $this->policyModel->getAll(['status' => 'active']);

        $page_title = 'Tạo Tour Mới';
        $content_file = VIEWS_PATH . '/staff/tours/create.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    /**
     * Form tạo Tour từ Template (Clone & Customize - Custom)
     */
    public function createFromTemplate()
    {
        $template_id = $_GET['template_id'] ?? 0;

        if (!$template_id) {
            set_error('Vui lòng chọn một template.');
            redirect('?act=staff-tours&action=selectTemplate');
            return;
        }

        // Lấy thông tin đầy đủ của template
        $template = $this->tourModel->getForClone($template_id);
        if (!$template) {
            set_error('Không tìm thấy tour template.');
            redirect('?act=staff-tours&action=selectTemplate');
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
            'itinerary_day_services' => $template['itinerary_day_services'] ?? [],
            'highlights' => $template['highlights'] ?? [],
            'includes' => $template['includes'] ?? [],
            'excludes' => $template['excludes'] ?? [],
            'policy_ids' => array_column($template['policies'] ?? [], 'id')
        ];

        // Lưu template data vào session để validation có thể check
        $this->initTourSession();
        $_SESSION['tour_form_data'] = array_merge($_SESSION['tour_form_data'] ?? [], $old_input);

        $is_from_template = true;
        $template_info = [
            'id' => $template['id'],
            'code' => $template['tour_code'],
            'name' => $template['name']
        ];

        $page_title = 'Tạo Tour Custom từ Template';
        $content_file = VIEWS_PATH . '/staff/tours/create.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    /**
     * Xử lý lưu Tour mới - THEO FLOW MỚI (GIỐNG ADMIN)
     */
    public function store()
    {
        try {
            // 1. Lấy dữ liệu từ SESSION và merge với POST
            $session_data = $this->loadTourSession();

            // Merge session data vào POST (POST có priority cao hơn)
            $form_data = array_merge($session_data, $_POST);

            // Đảm bảo các field từ POST được ưu tiên
            foreach ($_POST as $key => $value) {
                $form_data[$key] = $value;
            }

            // 2. Validation (dùng form_data đã merge, và cần có session_data để validate day services)
            $errors = $this->validateTourData($form_data, $session_data);

            // STAFF: Thêm validation cho status - chỉ cho phép draft hoặc pending
            $status = $form_data['status'] ?? 'draft';
            if (!in_array($status, ['draft', 'pending'])) {
                $errors['status'] = 'Bạn chỉ có thể tạo tour ở trạng thái Nháp hoặc Chờ duyệt';
            }

            if (!empty($errors)) {
                // Có lỗi: Giữ session, hiển thị lỗi + dữ liệu cũ
                $old_input = $form_data;
                $destinations = $this->destinationModel->getForDropdown();
                $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];
                $service_providers = $this->serviceProviderModel->getForDropdown();
                $policies = $this->policyModel->getAll(['status' => 'active']);
                $page_title = 'Thêm Tour mới';
                $content_file = VIEWS_PATH . '/staff/tours/create.php';
                require VIEWS_PATH . '/layouts/staff_layout.php';
                return;
            }

            // 2. Auto-generate Tour Code
            $tour_code = $this->generateTourCodeUnique();

            // 3. Calculate pricing từ PricingHelper
            // Lấy fixed costs từ form_data (đã merge session + POST)
            $fixed_costs = [
                'guide' => (float) ($form_data['fixed_cost_guide'] ?? 0),
                'management' => (float) ($form_data['fixed_cost_management'] ?? 0),
                'marketing' => (float) ($form_data['fixed_cost_marketing'] ?? 0),
                'other' => (float) ($form_data['fixed_cost_other'] ?? 0)
            ];
            $min_participants = (int) ($form_data['min_participants'] ?? 15);

            // Tính estimated_cost (sẽ tính lại sau khi có day_services)
            $estimated_cost_per_person = null;

            // 4. Prepare Tour Data - Lấy từ form_data (đã merge session + POST)
            $data = [
                'code' => $tour_code,
                'name' => sanitize($form_data['name'] ?? ''),
                'introduction' => sanitize($form_data['introduction'] ?? ''),
                'description' => $form_data['description'] ?? '',
                'duration_days' => (int) ($form_data['duration_days'] ?? 0),
                'duration_nights' => (int) ($form_data['duration_nights'] ?? 0),
                'departure_location' => sanitize($form_data['departure_location'] ?? ''),
                'min_participants' => $min_participants,
                'max_participants' => (int) ($form_data['max_participants'] ?? 45),
                'adult_price' => (float) ($form_data['adult_price'] ?? 0),
                'child_price' => (float) ($form_data['child_price'] ?? 0),
                'infant_price' => (float) ($form_data['infant_price'] ?? 0),
                'estimated_cost_per_person' => $estimated_cost_per_person,
                'deposit_percentage' => (float) ($form_data['deposit_percentage'] ?? 30),
                'booking_deadline_days' => (int) ($form_data['booking_deadline_days'] ?? 1),
                'fixed_cost_guide' => $fixed_costs['guide'],
                'fixed_cost_management' => $fixed_costs['management'],
                'fixed_cost_marketing' => $fixed_costs['marketing'],
                'fixed_cost_other' => $fixed_costs['other'],
                'tour_type' => $form_data['tour_type'] ?? 'public',
                'approval_status' => ($form_data['status'] ?? 'draft') == 'pending' ? 'pending' : null,
                'status' => $status, // Đã validate ở trên
                'parent_tour_id' => !empty($form_data['parent_tour_id']) ? (int) $form_data['parent_tour_id'] : null,
                'created_by' => get_user_id() // KEY: Set created_by
            ];

            // 5. Prepare Itinerary Data - Lấy từ session hoặc POST
            if (!empty($session_data['itinerary'])) {
                // Ưu tiên lấy từ session (đã có TinyMCE content)
                $data['itinerary'] = $session_data['itinerary'];
            } else {
                // Fallback: lấy từ POST
                $data['itinerary'] = $this->prepareItineraryData($form_data);
            }

            // 6. Prepare Itinerary Day Services Data - Lấy từ session hoặc POST
            if (!empty($session_data['itinerary_day_services'])) {
                $data['itinerary_day_services'] = $this->normalizeDayServicesFormat($session_data['itinerary_day_services']);
            } else {
                $data['itinerary_day_services'] = $this->prepareItineraryDayServicesData($form_data, $data['itinerary']);
            }

            // 8. Prepare Highlights - Lấy từ session hoặc POST
            if (!empty($session_data['highlights'])) {
                $data['highlights'] = $session_data['highlights'];
            } elseif (!empty($form_data['highlights'])) {
                $highlights = is_array($form_data['highlights'])
                    ? $form_data['highlights']
                    : explode("\n", $form_data['highlights']);
                $data['highlights'] = array_filter(array_map('trim', $highlights));
            }

            // 9. Prepare Included/Excluded - Lấy từ session hoặc POST
            if (!empty($session_data['included'])) {
                $data['included'] = $session_data['included'];
            } elseif (!empty($form_data['included'])) {
                $included = is_array($form_data['included']) ? $form_data['included'] : [$form_data['included']];
                $data['included'] = array_filter(array_map('trim', $included));
            }

            if (!empty($session_data['excluded'])) {
                $data['excluded'] = $session_data['excluded'];
            } elseif (!empty($form_data['excluded'])) {
                $excluded = is_array($form_data['excluded']) ? $form_data['excluded'] : [$form_data['excluded']];
                $data['excluded'] = array_filter(array_map('trim', $excluded));
            }

            // 10. Prepare Policy IDs - Lấy từ session hoặc POST
            if (!empty($session_data['policy_ids'])) {
                $data['policy_ids'] = $session_data['policy_ids'];
            } elseif (!empty($form_data['policy_ids'])) {
                $data['policy_ids'] = array_map('intval', $form_data['policy_ids']);
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

            // 14. Xóa session sau khi tạo tour thành công
            $this->clearTourSessionInternal();

            set_success("Tạo tour thành công! Tour đã được gửi để chờ duyệt.");
            redirect('?act=staff-tours');

        } catch (\Exception $e) {
            error_log("Staff TourController::store() Error: " . $e->getMessage());
            $errors = ['system' => $e->getMessage()];
            $old_input = $_POST;
            $destinations = $this->destinationModel->getForDropdown();
            $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];
            $service_providers = $this->serviceProviderModel->getForDropdown();
            $policies = $this->policyModel->getAll(['status' => 'active']);
            $page_title = 'Thêm Tour mới';
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

        // STAFF: Chỉ edit được tour status = draft, rejected, hoặc pending
        if (!in_array($tour['approval_status'] ?? null, [null, 'draft', 'rejected', 'pending'])) {
            set_error("Bạn chỉ có thể sửa tour ở trạng thái Nháp, Chờ duyệt hoặc Bị từ chối.");
            redirect('?act=staff-tours');
            return;
        }

        // Load dropdown data
        $destinations = $this->destinationModel->getForDropdown();
        $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];
        $service_providers = $this->serviceProviderModel->getForDropdown();
        $policies = $this->policyModel->getAll(['status' => 'active']);

        // Prepare itinerary day services by day
        $day_services_by_day = [];
        if (!empty($tour['itinerary_day_services']) && is_array($tour['itinerary_day_services'])) {
            foreach ($tour['itinerary_day_services'] as $service) {
                $day = $service['day_number'] ?? 1;
                if (!isset($day_services_by_day[$day])) {
                    $day_services_by_day[$day] = [];
                }
                $day_services_by_day[$day][] = $service;
            }
        }
        $tour['day_services_by_day'] = $day_services_by_day;

        // Prepare policy IDs
        $tour['policy_ids'] = array_column($tour['policies'] ?? [], 'id');

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

            // STAFF: Chỉ update được tour draft/rejected/pending
            if (!in_array($tour['approval_status'] ?? null, [null, 'draft', 'rejected', 'pending'])) {
                throw new \Exception('Bạn chỉ có thể sửa tour ở trạng thái Nháp, Chờ duyệt hoặc Bị từ chối.');
            }

            // Validation
            $errors = $this->validateTourData($_POST, []);

            // STAFF: Thêm validation cho status - chỉ cho phép draft hoặc pending
            $status = $_POST['status'] ?? 'draft';
            if (!in_array($status, ['draft', 'pending'])) {
                $errors['status'] = 'Bạn chỉ có thể set tour là Nháp hoặc Chờ duyệt';
            }

            if (!empty($errors)) {
                // Có lỗi: Load lại form với errors
                $destinations = $this->destinationModel->getForDropdown();
                $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];
                $service_providers = $this->serviceProviderModel->getForDropdown();
                $policies = $this->policyModel->getAll(['status' => 'active']);

                // Merge tour data với POST để giữ lại dữ liệu đã nhập
                $tour = array_merge($tour, $_POST);

                $page_title = 'Sửa Tour: ' . htmlspecialchars($tour['name']);
                $content_file = VIEWS_PATH . '/staff/tours/edit.php';
                require VIEWS_PATH . '/layouts/staff_layout.php';
                return;
            }

            // Calculate pricing từ PricingHelper
            $fixed_costs = [
                'guide' => (float) ($_POST['fixed_cost_guide'] ?? 0),
                'management' => (float) ($_POST['fixed_cost_management'] ?? 0),
                'marketing' => (float) ($_POST['fixed_cost_marketing'] ?? 0),
                'other' => (float) ($_POST['fixed_cost_other'] ?? 0)
            ];
            $min_participants = (int) ($_POST['min_participants'] ?? 15);

            // Prepare Tour Data
            $data = [
                'name' => sanitize($_POST['name'] ?? ''),
                'introduction' => sanitize($_POST['introduction'] ?? ''),
                'description' => $_POST['description'] ?? '',
                'duration_days' => (int) ($_POST['duration_days'] ?? 0),
                'duration_nights' => (int) ($_POST['duration_nights'] ?? 0),
                'departure_location' => sanitize($_POST['departure_location'] ?? ''),
                'min_participants' => $min_participants,
                'max_participants' => (int) ($_POST['max_participants'] ?? 45),
                'adult_price' => (float) ($_POST['adult_price'] ?? 0),
                'child_price' => (float) ($_POST['child_price'] ?? 0),
                'infant_price' => (float) ($_POST['infant_price'] ?? 0),
                'estimated_cost_per_person' => null, // Sẽ tính lại sau
                'deposit_percentage' => (float) ($_POST['deposit_percentage'] ?? 30),
                'booking_deadline_days' => (int) ($_POST['booking_deadline_days'] ?? 1),
                'fixed_cost_guide' => $fixed_costs['guide'],
                'fixed_cost_management' => $fixed_costs['management'],
                'fixed_cost_marketing' => $fixed_costs['marketing'],
                'fixed_cost_other' => $fixed_costs['other'],
                'tour_type' => $_POST['tour_type'] ?? 'public',
                'status' => $status,
                'approval_status' => $status == 'pending' ? 'pending' : null
            ];

            // Prepare Itinerary Data
            $data['itinerary'] = $this->prepareItineraryData($_POST);

            // Prepare Itinerary Day Services Data
            $data['itinerary_day_services'] = $this->prepareItineraryDayServicesData($_POST, $data['itinerary']);

            // Prepare Highlights
            if (!empty($_POST['highlights'])) {
                $highlights = is_array($_POST['highlights'])
                    ? $_POST['highlights']
                    : explode("\n", $_POST['highlights']);
                $data['highlights'] = array_filter(array_map('trim', $highlights));
            } else {
                $data['highlights'] = [];
            }

            // Prepare Included/Excluded
            if (!empty($_POST['included'])) {
                $included = is_array($_POST['included']) ? $_POST['included'] : [$_POST['included']];
                $data['included'] = array_filter(array_map('trim', $included));
            } else {
                $data['included'] = [];
            }

            if (!empty($_POST['excluded'])) {
                $excluded = is_array($_POST['excluded']) ? $_POST['excluded'] : [$_POST['excluded']];
                $data['excluded'] = array_filter(array_map('trim', $excluded));
            } else {
                $data['excluded'] = [];
            }

            // Prepare Policy IDs
            if (!empty($_POST['policy_ids'])) {
                $data['policy_ids'] = array_map('intval', $_POST['policy_ids']);
            } else {
                $data['policy_ids'] = [];
            }

            // Update Tour
            $this->tourModel->update($id, $data);

            // Calculate và update estimated_cost_per_person sau khi có day_services
            require_once COMMON_PATH . '/PricingHelper.php';
            $estimated_cost = calculateTotalCostPerPerson(
                $this->db,
                $id,
                $fixed_costs,
                $min_participants
            );
            $this->tourModel->updateStatus($id, ['estimated_cost_per_person' => $estimated_cost]);

            // Handle Images
            if (!empty($_FILES['images']['name'][0])) {
                $this->handleImageUploads($id, $_FILES['images']);
            }

            set_success("Cập nhật tour thành công!");
            redirect('?act=staff-tours&action=show&id=' . $id);

        } catch (\Exception $e) {
            error_log("Staff TourController::update() Error: " . $e->getMessage());
            set_error("Có lỗi xảy ra khi cập nhật tour: " . $e->getMessage());
            redirect('?act=staff-tours&action=edit&id=' . ($_POST['id'] ?? 0));
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
     * Validate Tour Data
     * @param array $post - POST data
     * @param array $session_data - Session data (optional, để validate day services từ session)
     */
    private function validateTourData($post, $session_data = [])
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

        // Validate Itinerary Day Services
        // Có thể có từ POST hoặc từ session (khi tạo từ template)
        $day_services_to_validate = [];

        // Lấy từ POST nếu có (ưu tiên user input)
        // Dùng isset() để phân biệt: POST không có field vs POST có field nhưng rỗng
        if (isset($post['day_service_day_number']) && is_array($post['day_service_day_number']) && !empty($post['day_service_day_number'])) {
            foreach ($post['day_service_day_number'] as $key => $day_number) {
                $day_services_to_validate[] = [
                    'day_number' => (int) $day_number,
                    'service_id' => $post['day_service_service_id'][$key] ?? null,
                    'service_provider_id' => $post['day_service_provider_id'][$key] ?? null,
                    'unit_price' => $post['day_service_unit_price'][$key] ?? 0,
                    'quantity' => $post['day_service_quantity'][$key] ?? 0,
                ];
            }
        }
        // Nếu POST không có field (user chưa chỉnh sửa), lấy từ session (khi tạo từ template)
        // Chỉ check session nếu POST hoàn toàn không có field (không phải array rỗng)
        elseif (!isset($post['day_service_day_number']) && !empty($session_data['itinerary_day_services'])) {
            $session_services = $this->normalizeDayServicesFormat($session_data['itinerary_day_services']);
            if (!empty($session_services)) {
                foreach ($session_services as $service) {
                    $day_services_to_validate[] = [
                        'day_number' => (int) ($service['day_number'] ?? $service['day'] ?? 1),
                        'service_id' => $service['service_id'] ?? null,
                        'service_provider_id' => $service['service_provider_id'] ?? null,
                        'unit_price' => (float) ($service['unit_price'] ?? 0),
                        'quantity' => (float) ($service['quantity'] ?? 0),
                    ];
                }
            }
        }

        // Validate các dịch vụ
        if (!empty($day_services_to_validate)) {
            foreach ($day_services_to_validate as $service) {
                $day_number = $service['day_number'];
                $service_id = $service['service_id'];
                $unit_price = (float) $service['unit_price'];
                $quantity = (float) $service['quantity'];
                $service_provider_id = $service['service_provider_id'];

                // Validate service_id tồn tại
                if (empty($service_id)) {
                    $errors['day_services'] = "Dịch vụ ngày $day_number: Vui lòng chọn dịch vụ";
                    break;
                } else {
                    // Kiểm tra service_id có tồn tại trong database
                    $stmt = $this->db->prepare("SELECT id FROM services WHERE id = :id AND status = 'active'");
                    $stmt->execute(['id' => (int) $service_id]);
                    if (!$stmt->fetch()) {
                        $errors['day_services'] = "Dịch vụ ngày $day_number: Dịch vụ không tồn tại hoặc đã bị vô hiệu hóa";
                        break;
                    }
                }

                // Validate unit_price > 0
                if ($unit_price <= 0) {
                    $errors['day_services'] = "Dịch vụ ngày $day_number: Đơn giá phải lớn hơn 0";
                    break;
                }

                // Validate quantity > 0
                if ($quantity <= 0) {
                    $errors['day_services'] = "Dịch vụ ngày $day_number: Số lượng phải lớn hơn 0";
                    break;
                }

                // Validate service_provider_id (nếu có) thuộc về service
                if (!empty($service_provider_id)) {
                    $stmt = $this->db->prepare("
                        SELECT s.id 
                        FROM services s 
                        WHERE s.id = :service_id 
                        AND s.service_provider_id = :provider_id
                    ");
                    $stmt->execute([
                        'service_id' => (int) $service_id,
                        'provider_id' => (int) $service_provider_id
                    ]);
                    if (!$stmt->fetch()) {
                        $errors['day_services'] = "Dịch vụ ngày $day_number: Nhà dịch vụ không thuộc về dịch vụ đã chọn";
                        break;
                    }
                }
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

        // Validate Status (Staff: chỉ draft hoặc pending)
        $status = $post['status'] ?? 'draft';
        if (!in_array($status, ['draft', 'pending'])) {
            $errors['status'] = 'Trạng thái không hợp lệ (chỉ cho phép Nháp hoặc Chờ duyệt)';
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
     * Prepare Itinerary Day Services Data từ POST
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
     * Normalize day services format từ session data
     * Chuyển đổi từ associative array {1: [...], 2: [...]} sang indexed array
     * Hoặc giữ nguyên nếu đã là indexed array format từ database
     */
    private function normalizeDayServicesFormat($day_services)
    {
        if (!is_array($day_services)) {
            return [];
        }

        // Nếu là array rỗng
        if (empty($day_services)) {
            return [];
        }

        // Kiểm tra format: Nếu phần tử đầu tiên có key là số (0, 1, 2...) và có 'service_id'
        // => Đây là indexed array từ database: [{service_id: 1, ...}, {service_id: 2, ...}]
        $first_key = array_key_first($day_services);
        $first_item = $day_services[$first_key] ?? null;

        // Nếu đã là indexed array format từ database: [{day_number: 1, service_id: 1, ...}, ...]
        if (is_numeric($first_key) && is_array($first_item) && isset($first_item['service_id'])) {
            // Đã đúng format, chỉ cần đảm bảo có day_number
            $normalized = [];
            foreach ($day_services as $service) {
                if (is_array($service) && isset($service['service_id'])) {
                    // Đảm bảo có day_number
                    if (!isset($service['day_number']) && isset($service['day'])) {
                        $service['day_number'] = (int) $service['day'];
                    }
                    $normalized[] = $service;
                }
            }
            return $normalized;
        }

        // Nếu là associative array với key là day_number: {1: [...], 2: [...]}
        // Hoặc {1: {service_id: 1, ...}, 2: {service_id: 2, ...}}
        $flattened = [];
        foreach ($day_services as $day_num => $services) {
            if (is_array($services)) {
                // Nếu là array of services: {1: [{service_id: 1, ...}, {service_id: 2, ...}]}
                if (isset($services[0]) && is_array($services[0])) {
                    foreach ($services as $service) {
                        if (is_array($service) && isset($service['service_id'])) {
                            $service['day_number'] = is_numeric($day_num) ? (int) $day_num : ($service['day_number'] ?? 1);
                            $flattened[] = $service;
                        }
                    }
                }
                // Nếu là single service object: {1: {service_id: 1, ...}}
                elseif (isset($services['service_id'])) {
                    $services['day_number'] = is_numeric($day_num) ? (int) $day_num : ($services['day_number'] ?? 1);
                    $flattened[] = $services;
                }
            }
        }

        return !empty($flattened) ? $flattened : $day_services;
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
     * Private method to clear session (internal use)
     */
    private function clearTourSessionInternal()
    {
        if (isset($_SESSION['tour_form_data'])) {
            unset($_SESSION['tour_form_data']);
        }
    }

    /**
     * Initialize Tour Session
     */
    private function initTourSession()
    {
        if (!isset($_SESSION['tour_form_data'])) {
            $_SESSION['tour_form_data'] = [
                'itinerary' => [],
                'itinerary_day_services' => [],
                'highlights' => [],
                'included' => [],
                'excluded' => [],
                'policy_ids' => [],
                'images' => [],
                'created_at' => date('Y-m-d H:i:s'),
                'last_updated' => date('Y-m-d H:i:s')
            ];
        }
    }

    /**
     * Load Tour Session Data
     */
    private function loadTourSession()
    {
        if (isset($_SESSION['tour_form_data'])) {
            return $_SESSION['tour_form_data'];
        }
        return [];
    }

    // ==========================================================================
    // AJAX ENDPOINTS
    // ==========================================================================

    /**
     * AJAX: Lấy thông tin service theo ID
     * Trả về: service info, service_provider_id, và danh sách providers cho service đó
     */
    public function getServiceInfo()
    {
        require_staff_or_admin();
        header('Content-Type: application/json');

        $service_id = $_GET['id'] ?? 0;
        $date = $_GET['date'] ?? date('Y-m-d'); // Ngày để lấy giá theo mùa

        if (!$service_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu service_id']);
            exit;
        }

        $service = $this->serviceModel->findById($service_id);
        if (!$service) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy dịch vụ']);
            exit;
        }

        // Get price từ service_prices theo date và price_type (ưu tiên: peak > standard > low)
        require_once MODELS_PATH . '/ServicePrice.php';
        $servicePriceModel = new \ServicePrice($this->db);

        // Lấy giá cho ngày cụ thể
        $price = $servicePriceModel->getPriceForService($service_id, $date);

        // Nếu không có giá cho ngày đó, lấy giá mới nhất
        if (!$price) {
            $stmt = $this->db->prepare("
                SELECT unit_price, price_type 
                FROM service_prices 
                WHERE service_id = :service_id AND status = 'active'
                ORDER BY 
                    CASE price_type 
                        WHEN 'peak' THEN 1 
                        WHEN 'standard' THEN 2 
                        WHEN 'low' THEN 3 
                    END,
                    created_at DESC 
                LIMIT 1
            ");
            $stmt->execute(['service_id' => $service_id]);
            $price = $stmt->fetch();
        }

        // Lấy danh sách providers cho service này
        $service_provider_id = $service['service_provider_id'] ?? null;
        $providers = [];

        if ($service_provider_id) {
            // Lấy provider của service này
            $provider = $this->serviceProviderModel->findById($service_provider_id);
            if ($provider) {
                $providers[] = [
                    'id' => $provider['id'],
                    'name' => $provider['name'],
                    'service_code' => $provider['service_code'] ?? ''
                ];
            }
        } else {
            // Nếu service không có provider, lấy tất cả providers active
            $providers = $this->serviceProviderModel->getForDropdown();
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'id' => $service['id'],
                'name' => $service['name'],
                'unit_price' => $price ? (float) $price['unit_price'] : 0,
                'price_type' => $price['price_type'] ?? 'standard',
                'unit' => $service['unit'] ?? '',
                'service_provider_id' => $service_provider_id,
                'service_provider_name' => $service['service_provider_name'] ?? null,
                'providers' => $providers // Danh sách providers cho service này
            ]
        ]);
        exit;
    }

    /**
     * AJAX: Lấy destinations
     */
    public function getDestinations()
    {
        require_staff_or_admin();
        header('Content-Type: application/json');

        $destinations = $this->destinationModel->getForDropdown();

        echo json_encode([
            'success' => true,
            'data' => $destinations
        ]);
        exit;
    }

    /**
     * AJAX: Lấy service providers theo destination, service type, hoặc service_id
     * Nếu có service_id, chỉ trả về provider của service đó
     */
    public function getServiceProviders()
    {
        require_staff_or_admin();
        header('Content-Type: application/json');

        $filters = [];

        // Nếu có service_id, lấy provider của service đó
        if (!empty($_GET['service_id'])) {
            $service_id = (int) $_GET['service_id'];
            $service = $this->serviceModel->findById($service_id);

            if ($service && !empty($service['service_provider_id'])) {
                $provider = $this->serviceProviderModel->findById($service['service_provider_id']);
                if ($provider) {
                    echo json_encode([
                        'success' => true,
                        'data' => [
                            [
                                'id' => $provider['id'],
                                'name' => $provider['name'],
                                'service_code' => $provider['service_code'] ?? ''
                            ]
                        ]
                    ]);
                    exit;
                }
            }

            // Nếu service không có provider, trả về empty
            echo json_encode([
                'success' => true,
                'data' => []
            ]);
            exit;
        }

        // Filter theo destination
        if (!empty($_GET['destination_id'])) {
            // Get province_id từ destination
            $stmt = $this->db->prepare("SELECT province_id FROM destinations WHERE id = :id");
            $stmt->execute(['id' => (int) $_GET['destination_id']]);
            $dest = $stmt->fetch();
            if ($dest) {
                $filters['province_id'] = $dest['province_id'];
            }
        }

        // Filter theo service_type_id
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
        require_staff_or_admin();
        header('Content-Type: application/json');

        try {
            if (empty($_POST['name'])) {
                throw new \Exception('Tên chính sách không được để trống');
            }
            if (empty($_POST['content'])) {
                throw new \Exception('Nội dung chính sách không được để trống');
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
                throw new \Exception('Không thể tạo chính sách');
            }

        } catch (\Exception $e) {
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
        require_staff_or_admin();
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

    /**
     * Load Day Services Editor Component (URL-based)
     * URL: ?act=staff-tours&action=loadDayServicesEditor&day=1&tour_id=123
     */
    public function loadDayServicesEditor()
    {
        require_staff_or_admin();

        // Set header to prevent redirect
        header('Content-Type: text/html; charset=utf-8');

        $day_number = (int) ($_GET['day'] ?? 1);
        $tour_id = (int) ($_GET['tour_id'] ?? 0);
        $day_services = [];

        // If editing existing tour, load day services from database
        if ($tour_id > 0) {
            $tour = $this->tourModel->findById($tour_id);
            // CHECK OWNERSHIP
            if ($tour && $tour['created_by'] != get_user_id()) {
                echo "Bạn không có quyền truy cập tour này.";
                exit;
            }
            if ($tour && !empty($tour['itinerary_day_services'])) {
                // Filter services by day_number
                foreach ($tour['itinerary_day_services'] as $service) {
                    if ($service['day_number'] == $day_number) {
                        $day_services[] = $service;
                    }
                }
            }
        } else {
            // If creating new tour, get from session or POST data
            if (!empty($_SESSION['tour_form_data']['day_services'][$day_number])) {
                $day_services = $_SESSION['tour_form_data']['day_services'][$day_number];
            }
        }

        // Get services and service providers
        $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];
        $service_providers = $this->serviceProviderModel->getForDropdown();

        // Include component (only HTML, no layout)
        $day_services_data = $day_services;
        require VIEWS_PATH . '/components/day-services-editor.php';
        exit;
    }

    /**
     * Load Itinerary Manager Component (URL-based) - Dịch vụ theo ngày
     * URL: ?act=staff-tours&action=loadItineraryManager&day=1&step=2
     */
    public function loadItineraryManager()
    {
        require_staff_or_admin();

        // Set header to prevent redirect
        header('Content-Type: text/html; charset=utf-8');

        $day_number = (int) ($_GET['day'] ?? 1);
        $step = (int) ($_GET['step'] ?? 2);
        $tour_id = (int) ($_GET['tour_id'] ?? 0);

        $day_services = [];

        // Load from session if creating new tour
        if ($tour_id == 0 && !empty($_SESSION['tour_form_data'])) {
            $day_services = $_SESSION['tour_form_data']['day_services'][$day_number] ?? [];
        }

        // If editing existing tour, load from database
        if ($tour_id > 0) {
            $tour = $this->tourModel->findById($tour_id);
            // CHECK OWNERSHIP
            if ($tour && $tour['created_by'] != get_user_id()) {
                echo "Bạn không có quyền truy cập tour này.";
                exit;
            }
            if ($tour) {
                // Load day services
                if (!empty($tour['itinerary_day_services'])) {
                    foreach ($tour['itinerary_day_services'] as $service) {
                        if ($service['day_number'] == $day_number) {
                            $day_services[] = $service;
                        }
                    }
                }
            }
        }

        // Get data for dropdowns
        $destinations = $this->destinationModel->getForDropdown();
        $services = $this->serviceModel->getAll(['status' => 'active'], 1, 1000)['data'];
        $service_providers = $this->serviceProviderModel->getForDropdown();

        // Include component (only HTML, no layout)
        require VIEWS_PATH . '/components/itinerary-manager.php';
        exit;
    }

    /**
     * Save Form Data to Session (AJAX)
     * URL: ?act=staff-tours&action=saveFormSession
     * Lưu toàn bộ dữ liệu tour vào session
     */
    public function saveFormSession()
    {
        require_staff_or_admin();
        header('Content-Type: application/json');

        // Support both JSON and form data
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        // If not JSON, try form data
        if (!$data && !empty($_POST)) {
            $data = $_POST;
        }

        if (!$data) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit;
        }

        // Khởi tạo session nếu chưa có
        $this->initTourSession();

        // Lưu từng phần dữ liệu
        if (isset($data['form_data']) && is_array($data['form_data'])) {
            // Thông tin cơ bản tour
            foreach ($data['form_data'] as $key => $value) {
                $_SESSION['tour_form_data'][$key] = $value;
            }
        }

        if (isset($data['itinerary']) && is_array($data['itinerary'])) {
            // Lịch trình theo ngày (bao gồm description từ TinyMCE)
            $_SESSION['tour_form_data']['itinerary'] = $data['itinerary'];
        }

        if (isset($data['itinerary_day_services'])) {
            // Dịch vụ theo ngày - có thể là array hoặc object
            if (is_array($data['itinerary_day_services'])) {
                // Nếu là array: [ {day_number: 1, ...}, {day_number: 2, ...} ]
                $_SESSION['tour_form_data']['itinerary_day_services'] = $data['itinerary_day_services'];
            } elseif (is_object($data['itinerary_day_services'])) {
                // Nếu là object: { 1: [...], 2: [...] }
                $_SESSION['tour_form_data']['itinerary_day_services'] = (array) $data['itinerary_day_services'];
            }
        }

        if (isset($data['highlights'])) {
            $_SESSION['tour_form_data']['highlights'] = is_array($data['highlights'])
                ? $data['highlights']
                : (is_string($data['highlights']) ? explode("\n", $data['highlights']) : []);
        }

        if (isset($data['included']) && is_array($data['included'])) {
            $_SESSION['tour_form_data']['included'] = $data['included'];
        }

        if (isset($data['excluded']) && is_array($data['excluded'])) {
            $_SESSION['tour_form_data']['excluded'] = $data['excluded'];
        }

        if (isset($data['policy_ids']) && is_array($data['policy_ids'])) {
            $_SESSION['tour_form_data']['policy_ids'] = array_map('intval', $data['policy_ids']);
        }

        // Update last_updated
        $_SESSION['tour_form_data']['last_updated'] = date('Y-m-d H:i:s');

        echo json_encode([
            'success' => true,
            'message' => 'Data saved to session',
            'data_count' => [
                'itinerary' => count($_SESSION['tour_form_data']['itinerary'] ?? []),
                'day_services' => count($_SESSION['tour_form_data']['itinerary_day_services'] ?? [])
            ]
        ]);
        exit;
    }

    /**
     * Clear Tour Session (AJAX)
     * URL: ?act=staff-tours&action=clearTourSession
     */
    public function clearTourSession()
    {
        require_staff_or_admin();
        header('Content-Type: application/json');

        if (isset($_SESSION['tour_form_data'])) {
            unset($_SESSION['tour_form_data']);
        }

        echo json_encode(['success' => true, 'message' => 'Session cleared']);
        exit;
    }

    /**
     * Upload image for TinyMCE editor
     * URL: ?act=staff-tours&action=uploadImage
     */
    public function uploadImage()
    {
        require_staff_or_admin();
        header('Content-Type: application/json; charset=utf-8');

        // Check if file was uploaded
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode([
                'success' => false,
                'message' => 'Không có file được upload hoặc có lỗi xảy ra.'
            ]);
            exit;
        }

        $file = $_FILES['file'];

        // Validate file type
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_type, $allowed_types)) {
            echo json_encode([
                'success' => false,
                'message' => 'Định dạng file không hợp lệ. Chỉ chấp nhận: JPG, PNG, GIF, WEBP'
            ]);
            exit;
        }

        // Validate file size (max 5MB)
        $max_size = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $max_size) {
            echo json_encode([
                'success' => false,
                'message' => 'File quá lớn. Tối đa 5MB'
            ]);
            exit;
        }

        // Validate image file using ValidationHelper
        require_once COMMON_PATH . '/ValidationHelper.php';
        $validation = \ValidationHelper::validateImageFile($file['tmp_name'], $max_size, $allowed_types);
        if (!$validation['valid']) {
            echo json_encode([
                'success' => false,
                'message' => $validation['error']
            ]);
            exit;
        }

        // Create upload directory
        $upload_dir = 'public/uploads/itinerary/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Generate unique filename
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'itinerary_' . uniqid() . '_' . time() . '.' . $extension;
        $file_path = $upload_dir . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            echo json_encode([
                'success' => false,
                'message' => 'Không thể lưu file. Vui lòng thử lại.'
            ]);
            exit;
        }

        // Get BASE_URL for image URL
        $base_url = defined('BASE_URL') ? BASE_URL : '';
        $image_url = $base_url . '/' . $file_path;

        // Return success with image URL (TinyMCE expects 'location' field)
        echo json_encode([
            'success' => true,
            'location' => $image_url,
            'message' => 'Upload ảnh thành công!'
        ]);
        exit;
    }
}
