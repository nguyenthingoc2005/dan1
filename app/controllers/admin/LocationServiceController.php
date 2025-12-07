<?php
/**
 * ==============================================================================
 * LOCATION SERVICE CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý thống nhất: Countries → Provinces → Service Providers → Services → Prices
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class LocationServiceController
{
    private $db;
    private $countryModel;
    private $provinceModel;
    private $serviceProviderModel;
    private $serviceModel;
    private $servicePriceModel;
    private $destinationModel;
    private $serviceTypeModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/Country.php';
        require_once MODELS_PATH . '/Province.php';
        require_once MODELS_PATH . '/ServiceProvider.php';
        require_once MODELS_PATH . '/Service.php';
        require_once MODELS_PATH . '/ServicePrice.php';
        require_once MODELS_PATH . '/Destination.php';
        require_once MODELS_PATH . '/ServiceType.php';

        $this->countryModel = new Country($pdo);
        $this->provinceModel = new Province($pdo);
        $this->serviceProviderModel = new ServiceProvider($pdo);
        $this->serviceModel = new Service($pdo);
        $this->servicePriceModel = new ServicePrice($pdo);
        $this->destinationModel = new Destination($pdo);
        $this->serviceTypeModel = new ServiceType($pdo);
    }

    /**
     * Trang chính - Quản lý thống nhất với Tab View
     * URL Parameters:
     * - country_id: Hiển thị provinces của country
     * - province_id: Hiển thị service providers và destinations của province (tab view)
     * - tab: providers hoặc destinations (default: providers)
     * - service_provider_id: Hiển thị services của provider
     */
    public function index()
    {
        require_admin();

        try {
            // Get URL parameters
            $country_id = !empty($_GET['country_id']) ? (int) $_GET['country_id'] : null;
            $province_id = !empty($_GET['province_id']) ? (int) $_GET['province_id'] : null;
            $service_provider_id = !empty($_GET['service_provider_id']) ? (int) $_GET['service_provider_id'] : null;
            $tab = !empty($_GET['tab']) ? $_GET['tab'] : 'providers'; // providers or destinations

            // Get countries (always needed for sidebar)
            // Admin cần thấy TẤT CẢ countries (cả active và inactive) để quản lý
            // Tăng per_page lên 200 để đảm bảo lấy đủ
            $countries = $this->countryModel->getAll([], 1, 200);
            if (!isset($countries['data'])) {
                $countries['data'] = [];
            }

            // Loại bỏ trùng lặp dựa trên ID (đảm bảo không mất dữ liệu)
            $unique_countries = [];
            $seen_ids = [];
            foreach ($countries['data'] as $country) {
                if (empty($country) || !isset($country['id'])) {
                    continue; // Bỏ qua nếu không có ID
                }
                $id = (int) $country['id'];
                if (!in_array($id, $seen_ids)) {
                    $seen_ids[] = $id;
                    $unique_countries[] = $country;
                }
            }
            $countries['data'] = $unique_countries;

            // Debug log để kiểm tra
            error_log("LocationServiceController::index() - Total countries loaded: " . count($countries['data']));
            error_log("LocationServiceController::index() - Country IDs: " . implode(', ', $seen_ids));

            // Add provinces count for each country
            foreach ($countries['data'] as &$country) {
                try {
                    $country['provinces_count'] = $this->countryModel->getProvinceCount($country['id']);
                } catch (Exception $e) {
                    error_log("Error getting province count for country {$country['id']}: " . $e->getMessage());
                    $country['provinces_count'] = 0;
                }
            }

            // Load service providers and destinations if province_id is set
            // QUAN TRỌNG: Nếu có province_id nhưng không có country_id, cần lấy country_id từ province
            $service_providers = ['data' => []];
            $destinations = ['data' => []];
            $current_province = null;
            $current_province_id = $province_id;
            // Initialize current_country_id từ URL parameter (có thể null)
            $current_country_id = $country_id;

            if ($province_id && !$country_id) {
                // Nếu có province_id nhưng không có country_id trong URL, lấy từ province
                try {
                    $current_province = $this->provinceModel->findById($province_id);
                    if ($current_province && !empty($current_province['country_id'])) {
                        $country_id = (int) $current_province['country_id'];
                        $current_country_id = $country_id; // Update từ province
                    }
                } catch (Exception $e) {
                    error_log("Error loading province {$province_id} to get country_id: " . $e->getMessage());
                }
            }

            // Load provinces if country_id is set (sau khi đã xác định từ province nếu cần)
            $provinces = [];
            $current_country = null;
            // Đảm bảo current_country_id được set đúng từ country_id (nếu có)
            if ($country_id) {
                $current_country_id = $country_id; // Giữ nguyên type (int hoặc null)
            }
            if ($country_id) {
                try {
                    $current_country = $this->countryModel->findById($country_id);
                    // Load tất cả provinces (cả active và inactive) cho admin
                    $provinces_result = $this->provinceModel->getAll(['country_id' => $country_id], 1, 100);
                    $provinces_raw = $provinces_result['data'] ?? [];

                    // Loại bỏ trùng lặp dựa trên ID
                    $unique_provinces = [];
                    $seen_province_ids = [];
                    foreach ($provinces_raw as $province) {
                        $id = (int) $province['id'];
                        if (!in_array($id, $seen_province_ids)) {
                            $seen_province_ids[] = $id;
                            $unique_provinces[] = $province;
                        }
                    }
                    $provinces = $unique_provinces;

                    foreach ($provinces as &$province) {
                        try {
                            $province['providers_count'] = $this->getServiceProviderCountByProvince($province['id']);
                        } catch (Exception $e) {
                            error_log("Error getting providers count for province {$province['id']}: " . $e->getMessage());
                            $province['providers_count'] = 0;
                        }
                    }
                    unset($province); // Unset reference
                } catch (Exception $e) {
                    error_log("Error loading provinces for country {$country_id}: " . $e->getMessage());
                    $provinces = [];
                }
            }

            // Load service providers and destinations if province_id is set
            if ($province_id) {
                try {
                    // Nếu chưa load province ở trên, load lại
                    if (!$current_province) {
                        $current_province = $this->provinceModel->findById($province_id);
                    }

                    // Đảm bảo current_country được set nếu chưa có
                    if (!$current_country && $current_province && !empty($current_province['country_id'])) {
                        $province_country_id = (int) $current_province['country_id'];
                        $current_country = $this->countryModel->findById($province_country_id);
                        if ($current_country) {
                            $current_country_id = (int) $current_country['id'];
                            // Đảm bảo country_id cũng được set đúng
                            if (!$country_id) {
                                $country_id = $province_country_id;
                            }
                        }
                    }
                    // Đảm bảo current_country_id luôn được set đúng từ country_id nếu có
                    if ($country_id && !$current_country_id) {
                        $current_country_id = (int) $country_id;
                    }
                    // Đảm bảo current_country_id được set đúng từ province nếu chưa có
                    if (!$current_country_id && $current_province && !empty($current_province['country_id'])) {
                        $current_country_id = (int) $current_province['country_id'];
                    }

                    // Load service providers
                    $service_providers = $this->serviceProviderModel->getAll(['province_id' => $province_id], 1, 100);
                    if (!isset($service_providers['data'])) {
                        $service_providers['data'] = [];
                    }
                    foreach ($service_providers['data'] as &$provider) {
                        try {
                            $provider['services_count'] = $this->serviceProviderModel->getServiceCount($provider['id']);
                        } catch (Exception $e) {
                            error_log("Error getting services count for provider {$provider['id']}: " . $e->getMessage());
                            $provider['services_count'] = 0;
                        }
                    }

                    // Load destinations - Admin cần thấy TẤT CẢ (cả active và inactive)
                    $destinations = $this->destinationModel->getAll(['province_id' => $province_id], 1, 100);
                    if (!isset($destinations['data'])) {
                        $destinations['data'] = [];
                    }

                    // Loại bỏ trùng lặp dựa trên ID
                    $unique_destinations = [];
                    $seen_destination_ids = [];
                    foreach ($destinations['data'] as $destination) {
                        $id = (int) $destination['id'];
                        if (!in_array($id, $seen_destination_ids)) {
                            $seen_destination_ids[] = $id;
                            $unique_destinations[] = $destination;
                        }
                    }
                    $destinations['data'] = $unique_destinations;
                } catch (Exception $e) {
                    error_log("Error loading providers/destinations for province {$province_id}: " . $e->getMessage());
                    $service_providers = ['data' => []];
                    $destinations = ['data' => []];
                }
            }

            // Load services if service_provider_id is set
            $services = ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
            $current_provider = null;
            $current_service_provider_id = $service_provider_id;
            if ($service_provider_id) {
                try {
                    // Load cả active và inactive để admin có thể thấy tất cả
                    $services = $this->serviceModel->getAll(['service_provider_id' => $service_provider_id], 1, 100);
                    if (!isset($services) || !is_array($services)) {
                        $services = ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
                    }
                    if (!isset($services['data'])) {
                        $services['data'] = [];
                    }

                    // Load prices cho mỗi service
                    foreach ($services['data'] as &$service) {
                        try {
                            $service['prices'] = $this->servicePriceModel->getByService($service['id'], ['status' => 'active']);
                            if (!isset($service['prices']) || !is_array($service['prices'])) {
                                $service['prices'] = [];
                            }
                        } catch (Exception $e) {
                            error_log("Error loading prices for service {$service['id']}: " . $e->getMessage());
                            $service['prices'] = [];
                        }
                    }
                    unset($service); // Unset reference
                } catch (PDOException $e) {
                    error_log("LocationServiceController::index() - Error loading services: " . $e->getMessage());
                    error_log("LocationServiceController::index() - Service Provider ID: " . $service_provider_id);
                    error_log("LocationServiceController::index() - Stack trace: " . $e->getTraceAsString());
                    $services = ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
                } catch (Exception $e) {
                    error_log("LocationServiceController::index() - General error loading services: " . $e->getMessage());
                    error_log("LocationServiceController::index() - Service Provider ID: " . $service_provider_id);
                    $services = ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
                }

                // Load provider info để hiển thị breadcrumb
                // Ưu tiên tìm trong danh sách đã load (nếu có)
                $found_in_list = false;
                if (!empty($service_providers['data'])) {
                    foreach ($service_providers['data'] as $provider) {
                        if ((int) $provider['id'] == (int) $service_provider_id) {
                            $current_provider = $provider;
                            $found_in_list = true;
                            break;
                        }
                    }
                }

                // Nếu không tìm thấy trong danh sách, load trực tiếp từ database
                if (!$found_in_list || empty($current_provider)) {
                    try {
                        $current_provider_obj = $this->serviceProviderModel->findById($service_provider_id);
                        if ($current_provider_obj) {
                            $current_provider = $current_provider_obj;
                            // Lấy province_id và country_id từ provider để load đúng context
                            if (!$province_id && !empty($current_provider_obj['province_id'])) {
                                $province_id = (int) $current_provider_obj['province_id'];
                                $current_province_id = $province_id;
                            }
                            if (!$country_id && !empty($current_provider_obj['country_id'])) {
                                $country_id = (int) $current_provider_obj['country_id'];
                                $current_country_id = $country_id;
                            }

                            // QUAN TRỌNG: Luôn reload provinces và providers khi có service_provider_id
                            // để đảm bảo sidebar hiển thị đúng
                            if ($province_id) {
                                if (!$country_id && !empty($current_provider_obj['country_id'])) {
                                    $country_id = (int) $current_provider_obj['country_id'];
                                    $current_country_id = $country_id;
                                }
                                if ($country_id) {
                                    try {
                                        if (empty($current_country)) {
                                            $current_country = $this->countryModel->findById($country_id);
                                        }
                                        // Luôn reload provinces để đảm bảo có đầy đủ dữ liệu cho sidebar
                                        $provinces_result = $this->provinceModel->getAll(['country_id' => $country_id], 1, 100);
                                        $provinces_raw = $provinces_result['data'] ?? [];

                                        // Loại bỏ trùng lặp dựa trên ID
                                        $unique_provinces = [];
                                        $seen_province_ids = [];
                                        foreach ($provinces_raw as $province) {
                                            $id = (int) $province['id'];
                                            if (!in_array($id, $seen_province_ids)) {
                                                $seen_province_ids[] = $id;
                                                $unique_provinces[] = $province;
                                            }
                                        }
                                        $provinces = $unique_provinces;

                                        foreach ($provinces as &$province) {
                                            try {
                                                $province['providers_count'] = $this->getServiceProviderCountByProvince($province['id']);
                                            } catch (Exception $e) {
                                                $province['providers_count'] = 0;
                                            }
                                        }
                                        unset($province); // Unset reference
                                    } catch (Exception $e) {
                                        error_log("Error reloading provinces: " . $e->getMessage());
                                    }
                                }
                                try {
                                    if (empty($current_province)) {
                                        $current_province = $this->provinceModel->findById($province_id);
                                    }
                                    // Luôn reload service providers để đảm bảo có đầy đủ dữ liệu
                                    $service_providers = $this->serviceProviderModel->getAll(['province_id' => $province_id], 1, 100);
                                    if (!isset($service_providers['data'])) {
                                        $service_providers['data'] = [];
                                    }
                                    foreach ($service_providers['data'] as &$provider) {
                                        try {
                                            $provider['services_count'] = $this->serviceProviderModel->getServiceCount($provider['id']);
                                        } catch (Exception $e) {
                                            $provider['services_count'] = 0;
                                        }
                                    }
                                    unset($provider); // Unset reference
                                } catch (Exception $e) {
                                    error_log("Error reloading providers: " . $e->getMessage());
                                }
                            }
                        }
                    } catch (Exception $e) {
                        error_log("Error loading provider info: " . $e->getMessage());
                    }
                }
            }

            // Set current_tab
            $current_tab = $tab;

            $page_title = 'Quản lý Địa điểm & Dịch vụ';
            $content_file = VIEWS_PATH . '/admin/location-services/index.php';
            require VIEWS_PATH . '/layouts/admin_layout.php';
        } catch (Exception $e) {
            error_log("LocationServiceController::index() Error: " . $e->getMessage());
            error_log("LocationServiceController::index() Stack trace: " . $e->getTraceAsString());
            die("Error loading page: " . htmlspecialchars($e->getMessage()));
        }
    }

    /**
     * AJAX: Lấy danh sách provinces theo country_id
     */
    public function getProvinces()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            $country_id = !empty($_GET['country_id']) ? (int) $_GET['country_id'] : null;

            if (!$country_id) {
                throw new Exception("Thiếu country_id.");
            }

            $provinces = $this->provinceModel->getForDropdown($country_id);

            // Get service providers count for each province
            foreach ($provinces as &$province) {
                $province['providers_count'] = $this->getServiceProviderCountByProvince($province['id']);
            }

            echo json_encode([
                'success' => true,
                'data' => $provinces
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * AJAX: Lấy danh sách service providers theo province_id
     */
    public function getServiceProviders()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            $province_id = !empty($_GET['province_id']) ? (int) $_GET['province_id'] : null;

            if (!$province_id) {
                throw new Exception("Thiếu province_id.");
            }

            $providers = $this->serviceProviderModel->getAll([
                'province_id' => $province_id,
                'status' => 'active'
            ], 1, 100);

            // Get services count for each provider
            foreach ($providers['data'] as &$provider) {
                $provider['services_count'] = $this->serviceProviderModel->getServiceCount($provider['id']);
            }

            echo json_encode([
                'success' => true,
                'data' => $providers['data']
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * AJAX: Lấy danh sách services theo service_provider_id
     */
    public function getServices()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            $service_provider_id = !empty($_GET['service_provider_id']) ? (int) $_GET['service_provider_id'] : null;

            if (!$service_provider_id) {
                throw new Exception("Thiếu service_provider_id.");
            }

            $services = $this->serviceModel->getAll([
                'service_provider_id' => $service_provider_id,
                'status' => 'active'
            ], 1, 100);

            // Get prices for each service
            foreach ($services['data'] as &$service) {
                $service['prices'] = $this->servicePriceModel->getByService($service['id'], ['status' => 'active']);
            }

            echo json_encode([
                'success' => true,
                'data' => $services['data']
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * AJAX: Lấy danh sách prices theo service_id
     */
    public function getPrices()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            $service_id = !empty($_GET['service_id']) ? (int) $_GET['service_id'] : null;

            if (!$service_id) {
                throw new Exception("Thiếu service_id.");
            }

            $prices = $this->servicePriceModel->getByService($service_id, ['status' => 'active']);

            echo json_encode([
                'success' => true,
                'data' => $prices
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * AJAX: Lấy thông tin price theo ID
     */
    public function getPrice()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            $id = !empty($_GET['id']) ? (int) $_GET['id'] : null;

            if (!$id) {
                throw new Exception("Thiếu ID.");
            }

            $price = $this->servicePriceModel->findById($id);

            if (!$price) {
                throw new Exception("Không tìm thấy giá.");
            }

            echo json_encode([
                'success' => true,
                'data' => $price
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Form tạo service provider
     */
    public function createProvider()
    {
        require_admin();

        $country_id = !empty($_GET['country_id']) ? (int) $_GET['country_id'] : null;
        $province_id = !empty($_GET['province_id']) ? (int) $_GET['province_id'] : null;

        if (!$country_id || !$province_id) {
            set_error("Thiếu thông tin quốc gia hoặc tỉnh thành.");
            redirect('?act=admin&module=location-services');
            return;
        }

        $current_country = $this->countryModel->findById($country_id);
        $current_province = $this->provinceModel->findById($province_id);

        if (!$current_country || !$current_province) {
            set_error("Không tìm thấy quốc gia hoặc tỉnh thành.");
            redirect('?act=admin&module=location-services');
            return;
        }

        // Truyền thêm các biến ID để form component sử dụng
        $current_country_id = $country_id;
        $current_province_id = $province_id;

        $service_types = $this->serviceTypeModel->getForDropdown();

        $page_title = 'Thêm Nhà dịch vụ';
        $content_file = VIEWS_PATH . '/admin/location-services/create-provider.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý tạo service provider
     */
    public function storeProvider()
    {
        require_admin();

        try {
            require_once COMMON_PATH . '/ValidationHelper.php';

            // Validate required fields
            if (empty($_POST['name']) || empty($_POST['province_id']) || empty($_POST['country_id'])) {
                throw new Exception("Vui lòng điền đầy đủ thông tin.");
            }

            $name = trim($_POST['name']);
            $province_id = (int) $_POST['province_id'];
            $country_id = (int) $_POST['country_id'];

            // Validate name: min 3, max 200
            if (mb_strlen($name) < 3) {
                throw new Exception("Tên nhà dịch vụ phải có ít nhất 3 ký tự.");
            }
            if (mb_strlen($name) > 200) {
                throw new Exception("Tên nhà dịch vụ không được quá 200 ký tự.");
            }

            // Validate province exists and active
            $province = $this->provinceModel->findById($province_id);
            if (!$province) {
                throw new Exception("Tỉnh thành không tồn tại.");
            }
            if ($province['status'] != 'active') {
                throw new Exception("Tỉnh thành không khả dụng (đã bị vô hiệu hóa).");
            }

            // Validate country exists and active
            $country = $this->countryModel->findById($country_id);
            if (!$country) {
                throw new Exception("Quốc gia không tồn tại.");
            }
            if ($country['status'] != 'active') {
                throw new Exception("Quốc gia không khả dụng (đã bị vô hiệu hóa).");
            }

            // Validate province belongs to country
            if ($province['country_id'] != $country_id) {
                throw new Exception("Tỉnh thành không thuộc quốc gia đã chọn.");
            }

            // Check duplicate name in same province
            $existing = $this->serviceProviderModel->getAll([
                'province_id' => $province_id,
                'name' => $name
            ], 1, 1);
            if (!empty($existing['data']) && count($existing['data']) > 0) {
                throw new Exception("Tên nhà dịch vụ đã tồn tại trong tỉnh này. Vui lòng chọn tên khác.");
            }

            // Validate service_type_id if provided
            if (!empty($_POST['service_type_id'])) {
                $service_type_id = (int) $_POST['service_type_id'];
                $service_type = $this->serviceTypeModel->findById($service_type_id);
                if (!$service_type) {
                    throw new Exception("Loại dịch vụ không tồn tại.");
                }
                if ($service_type['status'] != 'active') {
                    throw new Exception("Loại dịch vụ không khả dụng (đã bị vô hiệu hóa).");
                }
            }

            // Validate email format if provided
            if (!empty($_POST['email'])) {
                if (!ValidationHelper::validateEmail($_POST['email'], false)) {
                    throw new Exception("Email không hợp lệ.");
                }
            }

            // Validate phone format if provided
            if (!empty($_POST['phone'])) {
                if (!ValidationHelper::validatePhone($_POST['phone'], false)) {
                    throw new Exception("Số điện thoại không hợp lệ (VD: 0901234567).");
                }
            }

            $data = [
                'name' => sanitize($name),
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'province_id' => $province_id,
                'country_id' => $country_id,
                'contact_person' => isset($_POST['contact_person']) ? sanitize($_POST['contact_person']) : null,
                'email' => isset($_POST['email']) ? sanitize($_POST['email']) : null,
                'phone' => isset($_POST['phone']) ? sanitize($_POST['phone']) : null,
                'website' => isset($_POST['website']) ? sanitize($_POST['website']) : null,
                'address' => isset($_POST['address']) ? sanitize($_POST['address']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            $id = $this->serviceProviderModel->create($data);

            if ($id) {
                set_success('Tạo nhà dịch vụ thành công!');
                redirect('?act=admin&module=location-services&country_id=' . $data['country_id'] . '&province_id=' . $data['province_id'] . '&tab=providers');
            } else {
                throw new Exception("Không thể tạo nhà dịch vụ.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=location-services&action=create-provider&country_id=' . ($_POST['country_id'] ?? '') . '&province_id=' . ($_POST['province_id'] ?? ''));
        }
    }

    /**
     * Form sửa service provider
     */
    public function editProvider()
    {
        require_admin();

        if (empty($_GET['id'])) {
            set_error("Thiếu ID.");
            redirect('?act=admin&module=location-services');
            return;
        }

        $provider = $this->serviceProviderModel->findById((int) $_GET['id']);
        if (!$provider) {
            set_error("Không tìm thấy nhà dịch vụ.");
            redirect('?act=admin&module=location-services');
            return;
        }

        $country_id = $provider['country_id'] ?? (!empty($_GET['country_id']) ? (int) $_GET['country_id'] : null);
        $province_id = $provider['province_id'] ?? (!empty($_GET['province_id']) ? (int) $_GET['province_id'] : null);

        $current_country = $country_id ? $this->countryModel->findById($country_id) : null;
        $current_province = $province_id ? $this->provinceModel->findById($province_id) : null;

        // Truyền thêm các biến ID để form component sử dụng
        $current_country_id = $country_id;
        $current_province_id = $province_id;

        $service_types = $this->serviceTypeModel->getForDropdown();

        $page_title = 'Sửa Nhà dịch vụ';
        $content_file = VIEWS_PATH . '/admin/location-services/edit-provider.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý cập nhật service provider
     */
    public function updateProvider()
    {
        require_admin();

        try {
            require_once COMMON_PATH . '/ValidationHelper.php';

            if (empty($_POST['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_POST['id'];

            // Check provider exists
            $existing_provider = $this->serviceProviderModel->findById($id);
            if (!$existing_provider) {
                throw new Exception("Nhà dịch vụ không tồn tại.");
            }

            // Validate required fields
            if (empty($_POST['name'])) {
                throw new Exception("Vui lòng nhập tên nhà dịch vụ.");
            }

            $name = trim($_POST['name']);

            // Validate name: min 3, max 200
            if (mb_strlen($name) < 3) {
                throw new Exception("Tên nhà dịch vụ phải có ít nhất 3 ký tự.");
            }
            if (mb_strlen($name) > 200) {
                throw new Exception("Tên nhà dịch vụ không được quá 200 ký tự.");
            }

            // Check duplicate name in same province (exclude current provider)
            $existing = $this->serviceProviderModel->getAll([
                'province_id' => $existing_provider['province_id'],
                'name' => $name
            ], 1, 10);
            if (!empty($existing['data'])) {
                foreach ($existing['data'] as $provider) {
                    if ($provider['id'] != $id && $provider['name'] == $name) {
                        throw new Exception("Tên nhà dịch vụ đã tồn tại trong tỉnh này. Vui lòng chọn tên khác.");
                    }
                }
            }

            // Validate email format if provided
            if (!empty($_POST['email'])) {
                if (!ValidationHelper::validateEmail($_POST['email'], false)) {
                    throw new Exception("Email không hợp lệ.");
                }
            }

            // Validate phone format if provided
            if (!empty($_POST['phone'])) {
                if (!ValidationHelper::validatePhone($_POST['phone'], false)) {
                    throw new Exception("Số điện thoại không hợp lệ (VD: 0901234567).");
                }
            }

            $data = [
                'name' => sanitize($name),
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'contact_person' => isset($_POST['contact_person']) ? sanitize($_POST['contact_person']) : null,
                'email' => isset($_POST['email']) ? sanitize($_POST['email']) : null,
                'phone' => isset($_POST['phone']) ? sanitize($_POST['phone']) : null,
                'website' => isset($_POST['website']) ? sanitize($_POST['website']) : null,
                'address' => isset($_POST['address']) ? sanitize($_POST['address']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : null
            ];

            if ($this->serviceProviderModel->update($id, $data)) {
                set_success('Cập nhật thành công!');
                $provider = $this->serviceProviderModel->findById($id);
                redirect('?act=admin&module=location-services&country_id=' . $provider['country_id'] . '&province_id=' . $provider['province_id'] . '&tab=providers');
            } else {
                throw new Exception("Không thể cập nhật.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=location-services&action=edit-provider&id=' . ($_POST['id'] ?? '') . '&country_id=' . ($_POST['country_id'] ?? '') . '&province_id=' . ($_POST['province_id'] ?? ''));
        }
    }

    /**
     * AJAX: Tạo service provider (legacy - for modals)
     */
    public function createServiceProvider()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if (empty($_POST['name']) || empty($_POST['province_id']) || empty($_POST['country_id'])) {
                throw new Exception("Vui lòng điền đầy đủ thông tin.");
            }

            $data = [
                'name' => sanitize($_POST['name']),
                'service_type_id' => !empty($_POST['service_type_id']) ? (int) $_POST['service_type_id'] : null,
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'province_id' => (int) $_POST['province_id'],
                'country_id' => (int) $_POST['country_id'],
                'contact_person' => isset($_POST['contact_person']) ? sanitize($_POST['contact_person']) : null,
                'email' => isset($_POST['email']) ? sanitize($_POST['email']) : null,
                'phone' => isset($_POST['phone']) ? sanitize($_POST['phone']) : null,
                'website' => isset($_POST['website']) ? sanitize($_POST['website']) : null,
                'address' => isset($_POST['address']) ? sanitize($_POST['address']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            $id = $this->serviceProviderModel->create($data);

            if ($id) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Tạo nhà dịch vụ thành công!',
                    'id' => $id
                ]);
            } else {
                throw new Exception("Không thể tạo nhà dịch vụ.");
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
     * AJAX: Cập nhật service provider
     */
    public function updateServiceProvider()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if (empty($_POST['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_POST['id'];
            $data = [
                'name' => sanitize($_POST['name']),
                'service_type_id' => !empty($_POST['service_type_id']) ? (int) $_POST['service_type_id'] : null,
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'contact_person' => isset($_POST['contact_person']) ? sanitize($_POST['contact_person']) : null,
                'email' => isset($_POST['email']) ? sanitize($_POST['email']) : null,
                'phone' => isset($_POST['phone']) ? sanitize($_POST['phone']) : null,
                'website' => isset($_POST['website']) ? sanitize($_POST['website']) : null,
                'address' => isset($_POST['address']) ? sanitize($_POST['address']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : null
            ];

            if ($this->serviceProviderModel->update($id, $data)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật thành công!'
                ]);
            } else {
                throw new Exception("Không thể cập nhật.");
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
     * Xóa service provider - Redirect về trang trước
     */
    public function deleteServiceProvider()
    {
        require_admin();

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_GET['id'];
            $country_id = !empty($_GET['country_id']) ? (int) $_GET['country_id'] : null;
            $province_id = !empty($_GET['province_id']) ? (int) $_GET['province_id'] : null;

            if ($this->serviceProviderModel->delete($id)) {
                set_success('Xóa nhà cung cấp thành công!');
            } else {
                throw new Exception("Không thể xóa.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
        }

        // Redirect về trang trước
        $redirect_url = '?act=admin&module=location-services';
        if ($country_id) {
            $redirect_url .= '&country_id=' . $country_id;
        }
        if ($province_id) {
            $redirect_url .= '&province_id=' . $province_id . '&tab=providers';
        }
        redirect($redirect_url);
    }

    /**
     * Form tạo service
     */
    public function createService()
    {
        require_admin();

        $service_provider_id = !empty($_GET['service_provider_id']) ? (int) $_GET['service_provider_id'] : null;

        if (!$service_provider_id) {
            set_error("Thiếu thông tin nhà dịch vụ.");
            redirect('?act=admin&module=location-services');
            return;
        }

        $current_provider = $this->serviceProviderModel->findById($service_provider_id);
        if (!$current_provider) {
            set_error("Không tìm thấy nhà dịch vụ.");
            redirect('?act=admin&module=location-services');
            return;
        }

        $current_province = $this->provinceModel->findById($current_provider['province_id']);
        $current_country = $this->countryModel->findById($current_provider['country_id']);

        $service_types = $this->serviceTypeModel->getForDropdown();

        $page_title = 'Thêm Dịch vụ';
        $content_file = VIEWS_PATH . '/admin/location-services/create-service.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý tạo service
     */
    public function storeService()
    {
        require_admin();

        try {
            // Validate required fields
            if (empty($_POST['service_provider_id']) || empty($_POST['service_type_id']) || empty($_POST['name'])) {
                throw new Exception("Vui lòng điền đầy đủ thông tin.");
            }

            $name = trim($_POST['name']);
            $service_provider_id = (int) $_POST['service_provider_id'];
            $service_type_id = (int) $_POST['service_type_id'];

            // Validate name: min 3, max 200
            if (mb_strlen($name) < 3) {
                throw new Exception("Tên dịch vụ phải có ít nhất 3 ký tự.");
            }
            if (mb_strlen($name) > 200) {
                throw new Exception("Tên dịch vụ không được quá 200 ký tự.");
            }

            // Validate service_provider exists and active
            $provider = $this->serviceProviderModel->findById($service_provider_id);
            if (!$provider) {
                throw new Exception("Nhà cung cấp dịch vụ không tồn tại.");
            }
            if ($provider['status'] != 'active') {
                throw new Exception("Nhà cung cấp dịch vụ không khả dụng (đã bị vô hiệu hóa).");
            }

            // Validate service_type exists and active
            $service_type = $this->serviceTypeModel->findById($service_type_id);
            if (!$service_type) {
                throw new Exception("Loại dịch vụ không tồn tại.");
            }
            if ($service_type['status'] != 'active') {
                throw new Exception("Loại dịch vụ không khả dụng (đã bị vô hiệu hóa).");
            }

            // Check duplicate name in same service_provider and service_type
            $existing = $this->serviceModel->getAll([
                'service_provider_id' => $service_provider_id,
                'service_type_id' => $service_type_id,
                'name' => $name
            ], 1, 1);
            if (!empty($existing['data']) && count($existing['data']) > 0) {
                throw new Exception("Dịch vụ này đã tồn tại cho nhà cung cấp này. Vui lòng chọn tên khác hoặc sửa dịch vụ hiện có.");
            }

            $data = [
                'service_provider_id' => $service_provider_id,
                'service_type_id' => $service_type_id,
                'name' => sanitize($name),
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'unit' => isset($_POST['unit']) ? sanitize($_POST['unit']) : null,
                'notes' => isset($_POST['notes']) ? sanitize($_POST['notes']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            $id = $this->serviceModel->create($data);

            if ($id) {
                set_success('Tạo dịch vụ thành công!');
                $provider = $this->serviceProviderModel->findById($data['service_provider_id']);
                redirect('?act=admin&module=location-services&country_id=' . $provider['country_id'] . '&province_id=' . $provider['province_id'] . '&service_provider_id=' . $data['service_provider_id']);
            } else {
                throw new Exception("Không thể tạo dịch vụ.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=location-services&action=create-service&service_provider_id=' . ($_POST['service_provider_id'] ?? ''));
        }
    }

    /**
     * Form sửa service
     */
    public function editService()
    {
        require_admin();

        if (empty($_GET['id'])) {
            set_error("Thiếu ID.");
            redirect('?act=admin&module=location-services');
            return;
        }

        $service = $this->serviceModel->findById((int) $_GET['id']);
        if (!$service) {
            set_error("Không tìm thấy dịch vụ.");
            redirect('?act=admin&module=location-services');
            return;
        }

        $current_provider = $this->serviceProviderModel->findById($service['service_provider_id']);
        $current_province = $current_provider ? $this->provinceModel->findById($current_provider['province_id']) : null;
        $current_country = $current_provider ? $this->countryModel->findById($current_provider['country_id']) : null;

        $service_types = $this->serviceTypeModel->getForDropdown();

        $page_title = 'Sửa Dịch vụ';
        $content_file = VIEWS_PATH . '/admin/location-services/edit-service.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý cập nhật service
     */
    public function updateService()
    {
        require_admin();

        try {
            if (empty($_POST['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_POST['id'];

            // Check service exists
            $existing_service = $this->serviceModel->findById($id);
            if (!$existing_service) {
                throw new Exception("Dịch vụ không tồn tại.");
            }

            // Validate required fields
            if (empty($_POST['name'])) {
                throw new Exception("Vui lòng nhập tên dịch vụ.");
            }

            $name = trim($_POST['name']);

            // Validate name: min 3, max 200
            if (mb_strlen($name) < 3) {
                throw new Exception("Tên dịch vụ phải có ít nhất 3 ký tự.");
            }
            if (mb_strlen($name) > 200) {
                throw new Exception("Tên dịch vụ không được quá 200 ký tự.");
            }

            // Validate service_type_id if provided
            $service_type_id = !empty($_POST['service_type_id']) ? (int) $_POST['service_type_id'] : $existing_service['service_type_id'];
            if ($service_type_id) {
                $service_type = $this->serviceTypeModel->findById($service_type_id);
                if (!$service_type) {
                    throw new Exception("Loại dịch vụ không tồn tại.");
                }
                if ($service_type['status'] != 'active') {
                    throw new Exception("Loại dịch vụ không khả dụng (đã bị vô hiệu hóa).");
                }
            }

            // Check duplicate name in same service_provider and service_type (exclude current service)
            $service_provider_id = $existing_service['service_provider_id'];
            $existing = $this->serviceModel->getAll([
                'service_provider_id' => $service_provider_id,
                'service_type_id' => $service_type_id,
                'name' => $name
            ], 1, 10);
            if (!empty($existing['data'])) {
                foreach ($existing['data'] as $service) {
                    if ($service['id'] != $id && $service['name'] == $name) {
                        throw new Exception("Dịch vụ này đã tồn tại cho nhà cung cấp này. Vui lòng chọn tên khác.");
                    }
                }
            }

            $data = [
                'name' => sanitize($name),
                'service_type_id' => $service_type_id,
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'unit' => isset($_POST['unit']) ? sanitize($_POST['unit']) : null,
                'notes' => isset($_POST['notes']) ? sanitize($_POST['notes']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : null
            ];

            if ($this->serviceModel->update($id, $data)) {
                set_success('Cập nhật thành công!');
                $service = $this->serviceModel->findById($id);
                $provider = $this->serviceProviderModel->findById($service['service_provider_id']);
                redirect('?act=admin&module=location-services&country_id=' . $provider['country_id'] . '&province_id=' . $provider['province_id'] . '&service_provider_id=' . $service['service_provider_id']);
            } else {
                throw new Exception("Không thể cập nhật.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=location-services&action=edit-service&id=' . ($_POST['id'] ?? ''));
        }
    }

    /**
     * AJAX: Tạo service (legacy - for modals)
     */
    public function createServiceAjax()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if (empty($_POST['service_provider_id']) || empty($_POST['service_type_id']) || empty($_POST['name'])) {
                throw new Exception("Vui lòng điền đầy đủ thông tin.");
            }

            // Verify service provider exists
            $provider = $this->serviceProviderModel->findById((int) $_POST['service_provider_id']);
            if (!$provider) {
                throw new Exception("Nhà dịch vụ không tồn tại.");
            }

            $data = [
                'service_provider_id' => (int) $_POST['service_provider_id'],
                'service_type_id' => (int) $_POST['service_type_id'],
                'name' => sanitize($_POST['name']),
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'unit' => isset($_POST['unit']) ? sanitize($_POST['unit']) : null,
                'notes' => isset($_POST['notes']) ? sanitize($_POST['notes']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            $id = $this->serviceModel->create($data);

            if ($id) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Tạo dịch vụ thành công!',
                    'id' => $id
                ]);
            } else {
                throw new Exception("Không thể tạo dịch vụ.");
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
     * AJAX: Cập nhật service (legacy - for modals)
     */
    public function updateServiceAjax()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if (empty($_POST['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_POST['id'];
            $data = [
                'name' => sanitize($_POST['name']),
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'unit' => isset($_POST['unit']) ? sanitize($_POST['unit']) : null,
                'notes' => isset($_POST['notes']) ? sanitize($_POST['notes']) : null
            ];

            if ($this->serviceModel->update($id, $data)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật thành công!'
                ]);
            } else {
                throw new Exception("Không thể cập nhật.");
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
     * Xóa service - Redirect về trang trước
     */
    public function deleteService()
    {
        require_admin();

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_GET['id'];
            $service = $this->serviceModel->findById($id);
            $service_provider_id = $service['service_provider_id'] ?? null;
            $provider = $service_provider_id ? $this->serviceProviderModel->findById($service_provider_id) : null;

            if ($this->serviceModel->delete($id)) {
                set_success('Xóa dịch vụ thành công!');
            } else {
                throw new Exception("Không thể xóa.");
            }

            // Redirect về trang service provider
            if ($provider) {
                $redirect_url = '?act=admin&module=location-services';
                if ($provider['country_id']) {
                    $redirect_url .= '&country_id=' . $provider['country_id'];
                }
                if ($provider['province_id']) {
                    $redirect_url .= '&province_id=' . $provider['province_id'];
                }
                $redirect_url .= '&service_provider_id=' . $service_provider_id;
                redirect($redirect_url);
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
        }

        redirect('?act=admin&module=location-services');
    }

    /**
     * AJAX: Tạo service price
     */
    public function createPrice()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if (empty($_POST['service_id']) || empty($_POST['unit_price'])) {
                throw new Exception("Vui lòng điền đầy đủ thông tin.");
            }

            $service_id = (int) $_POST['service_id'];
            $unit_price = (float) $_POST['unit_price'];

            // Validate service tồn tại
            $service = $this->serviceModel->findById($service_id);
            if (!$service) {
                throw new Exception("Không tìm thấy dịch vụ.");
            }

            // Validate unit_price > 0
            if ($unit_price <= 0) {
                throw new Exception("Giá phải lớn hơn 0.");
            }

            // Validate price_type
            $price_type = $_POST['price_type'] ?? 'standard';
            $allowed_price_types = ['standard', 'peak', 'low'];
            if (!in_array($price_type, $allowed_price_types)) {
                throw new Exception("Loại giá không hợp lệ.");
            }

            $data = [
                'service_id' => $service_id,
                'unit_price' => $unit_price,
                'start_date' => !empty($_POST['start_date']) ? $_POST['start_date'] : (!empty($_POST['valid_from']) ? $_POST['valid_from'] : null),
                'end_date' => !empty($_POST['end_date']) ? $_POST['end_date'] : (!empty($_POST['valid_to']) ? $_POST['valid_to'] : null),
                'price_type' => $_POST['price_type'] ?? 'standard',
                'notes' => isset($_POST['notes']) ? sanitize($_POST['notes']) : null,
                'status' => 'active'
            ];

            $id = $this->servicePriceModel->create($data);

            if ($id) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Tạo giá thành công!',
                    'id' => $id
                ]);
            } else {
                throw new Exception("Không thể tạo giá.");
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
     * AJAX: Cập nhật service price
     */
    public function updatePrice()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if (empty($_POST['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_POST['id'];

            // Check price exists
            $existing_price = $this->servicePriceModel->findById($id);
            if (!$existing_price) {
                throw new Exception("Giá không tồn tại.");
            }

            // Validate required fields
            if (empty($_POST['unit_price'])) {
                throw new Exception("Vui lòng nhập giá.");
            }

            $unit_price = (float) $_POST['unit_price'];

            // Validate unit_price > 0
            if ($unit_price <= 0) {
                throw new Exception("Giá phải lớn hơn 0.");
            }

            // Validate price_type
            $price_type = $_POST['price_type'] ?? 'standard';
            $allowed_price_types = ['standard', 'peak', 'low'];
            if (!in_array($price_type, $allowed_price_types)) {
                throw new Exception("Loại giá không hợp lệ.");
            }

            $data = [
                'unit_price' => $unit_price,
                'start_date' => !empty($_POST['start_date']) ? $_POST['start_date'] : (!empty($_POST['valid_from']) ? $_POST['valid_from'] : null),
                'end_date' => !empty($_POST['end_date']) ? $_POST['end_date'] : (!empty($_POST['valid_to']) ? $_POST['valid_to'] : null),
                'price_type' => $price_type,
                'notes' => isset($_POST['notes']) ? sanitize($_POST['notes']) : null
            ];

            if ($this->servicePriceModel->update($id, $data)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật thành công!'
                ]);
            } else {
                throw new Exception("Không thể cập nhật.");
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
     * Xóa service price - Redirect về trang trước
     */
    public function deletePrice()
    {
        require_admin();

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_GET['id'];
            $price = $this->servicePriceModel->findById($id);
            $service_id = $price['service_id'] ?? null;
            $service = $service_id ? $this->serviceModel->findById($service_id) : null;
            $service_provider_id = $service['service_provider_id'] ?? null;
            $provider = $service_provider_id ? $this->serviceProviderModel->findById($service_provider_id) : null;

            if ($this->servicePriceModel->delete($id)) {
                set_success('Xóa giá thành công!');
            } else {
                throw new Exception("Không thể xóa.");
            }

            // Redirect về trang service provider
            if ($provider) {
                $redirect_url = '?act=admin&module=location-services';
                if ($provider['country_id']) {
                    $redirect_url .= '&country_id=' . $provider['country_id'];
                }
                if ($provider['province_id']) {
                    $redirect_url .= '&province_id=' . $provider['province_id'];
                }
                $redirect_url .= '&service_provider_id=' . $service_provider_id;
                redirect($redirect_url);
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
        }

        redirect('?act=admin&module=location-services');
    }

    /**
     * AJAX: Lấy dropdown data
     */
    public function getDropdownData()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            $data = [
                'service_types' => $this->serviceTypeModel->getForDropdown(),
                'provinces' => $this->provinceModel->getForDropdown(),
                'destinations' => []
            ];

            // Get destinations if province_id provided
            if (!empty($_GET['province_id'])) {
                $data['destinations'] = $this->destinationModel->getByProvince((int) $_GET['province_id']);
            }

            echo json_encode([
                'success' => true,
                'data' => $data
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * AJAX: Lấy thông tin service provider theo ID
     */
    public function getServiceProvider()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            $id = !empty($_GET['id']) ? (int) $_GET['id'] : null;

            if (!$id) {
                throw new Exception("Thiếu ID.");
            }

            $provider = $this->serviceProviderModel->findById($id);

            if (!$provider) {
                throw new Exception("Nhà dịch vụ không tồn tại.");
            }

            echo json_encode([
                'success' => true,
                'data' => $provider
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * AJAX: Lấy thông tin destination theo ID
     */
    public function getDestination()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            $id = !empty($_GET['id']) ? (int) $_GET['id'] : null;

            if (!$id) {
                throw new Exception("Thiếu ID.");
            }

            $destination = $this->destinationModel->findById($id);

            if (!$destination) {
                throw new Exception("Địa điểm không tồn tại.");
            }

            echo json_encode([
                'success' => true,
                'data' => $destination
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * AJAX: Lấy thông tin service theo ID
     */
    public function getService()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            $id = !empty($_GET['id']) ? (int) $_GET['id'] : null;

            if (!$id) {
                throw new Exception("Thiếu ID.");
            }

            $service = $this->serviceModel->findById($id);

            if (!$service) {
                throw new Exception("Dịch vụ không tồn tại.");
            }

            echo json_encode([
                'success' => true,
                'data' => $service
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Form tạo destination
     */
    public function createDestination()
    {
        require_admin();

        $country_id = !empty($_GET['country_id']) ? (int) $_GET['country_id'] : null;
        $province_id = !empty($_GET['province_id']) ? (int) $_GET['province_id'] : null;

        if (!$country_id || !$province_id) {
            set_error("Thiếu thông tin quốc gia hoặc tỉnh thành.");
            redirect('?act=admin&module=location-services');
            return;
        }

        $current_country = $this->countryModel->findById($country_id);
        $current_province = $this->provinceModel->findById($province_id);

        if (!$current_country || !$current_province) {
            set_error("Không tìm thấy quốc gia hoặc tỉnh thành.");
            redirect('?act=admin&module=location-services');
            return;
        }

        // Truyền thêm các biến ID để form component sử dụng
        $current_country_id = $country_id;
        $current_province_id = $province_id;

        $page_title = 'Thêm Địa điểm du lịch';
        $content_file = VIEWS_PATH . '/admin/location-services/create-destination.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý tạo destination
     */
    public function storeDestination()
    {
        require_admin();

        try {
            if (empty($_POST['name']) || empty($_POST['province_id']) || empty($_POST['country_id'])) {
                throw new Exception("Vui lòng điền đầy đủ thông tin.");
            }

            $name = trim($_POST['name']);
            $province_id = (int) $_POST['province_id'];
            $country_id = (int) $_POST['country_id'];

            // Validate name: min 3, max 200
            if (mb_strlen($name) < 3) {
                throw new Exception("Tên địa điểm phải có ít nhất 3 ký tự.");
            }
            if (mb_strlen($name) > 200) {
                throw new Exception("Tên địa điểm không được quá 200 ký tự.");
            }

            // Validate province exists and active
            $province = $this->provinceModel->findById($province_id);
            if (!$province) {
                throw new Exception("Tỉnh thành không tồn tại.");
            }
            if ($province['status'] != 'active') {
                throw new Exception("Tỉnh thành không khả dụng (đã bị vô hiệu hóa).");
            }

            // Lấy country_id từ province (ưu tiên) để đảm bảo đúng
            $province_country_id = !empty($province['country_id']) ? (int) $province['country_id'] : null;
            if ($province_country_id) {
                $country_id = $province_country_id; // Sử dụng country_id từ province
            }

            // Validate country exists and active
            $country = $this->countryModel->findById($country_id);
            if (!$country) {
                throw new Exception("Quốc gia không tồn tại.");
            }
            if ($country['status'] != 'active') {
                throw new Exception("Quốc gia không khả dụng (đã bị vô hiệu hóa).");
            }

            // Validate province belongs to country (với type casting để tránh lỗi so sánh)
            if ((int) $province['country_id'] !== (int) $country_id) {
                throw new Exception("Tỉnh thành không thuộc quốc gia đã chọn.");
            }

            $data = [
                'name' => sanitize($name),
                'province_id' => $province_id,
                'country_id' => $country_id,
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'locations' => isset($_POST['locations']) ? sanitize($_POST['locations']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            $id = $this->destinationModel->create($data);

            if ($id) {
                // Handle image uploads if any
                if (!empty($_FILES['images']['name'][0])) {
                    $uploaded_count = $this->handleDestinationImageUploads($id, $_FILES['images']);
                    if ($uploaded_count > 0) {
                        set_success("Tạo địa điểm thành công! Đã upload {$uploaded_count} ảnh.");
                    } else {
                        set_success('Tạo địa điểm thành công!');
                    }
                } else {
                    set_success('Tạo địa điểm thành công!');
                }
                redirect('?act=admin&module=location-services&country_id=' . $data['country_id'] . '&province_id=' . $data['province_id'] . '&tab=destinations');
            } else {
                throw new Exception("Không thể tạo địa điểm.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=location-services&action=create-destination&country_id=' . ($_POST['country_id'] ?? '') . '&province_id=' . ($_POST['province_id'] ?? ''));
        }
    }

    /**
     * Form sửa destination
     */
    public function editDestination()
    {
        require_admin();

        if (empty($_GET['id'])) {
            set_error("Thiếu ID.");
            redirect('?act=admin&module=location-services');
            return;
        }

        $destination = $this->destinationModel->findById((int) $_GET['id']);
        if (!$destination) {
            set_error("Không tìm thấy địa điểm.");
            redirect('?act=admin&module=location-services');
            return;
        }

        $country_id = $destination['country_id'] ?? (!empty($_GET['country_id']) ? (int) $_GET['country_id'] : null);
        $province_id = $destination['province_id'] ?? (!empty($_GET['province_id']) ? (int) $_GET['province_id'] : null);

        $current_country = $country_id ? $this->countryModel->findById($country_id) : null;
        $current_province = $province_id ? $this->provinceModel->findById($province_id) : null;

        // Truyền thêm các biến ID để form component sử dụng
        $current_country_id = $country_id;
        $current_province_id = $province_id;

        // Load images for destination
        $destination_images = $this->destinationModel->getImages($destination['id']);

        $page_title = 'Sửa Địa điểm du lịch';
        $content_file = VIEWS_PATH . '/admin/location-services/edit-destination.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý cập nhật destination
     */
    public function updateDestination()
    {
        require_admin();

        try {
            if (empty($_POST['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_POST['id'];

            // Check destination exists
            $existing_destination = $this->destinationModel->findById($id);
            if (!$existing_destination) {
                throw new Exception("Địa điểm không tồn tại.");
            }

            // Validate required fields
            if (empty($_POST['name'])) {
                throw new Exception("Vui lòng nhập tên địa điểm.");
            }

            $name = trim($_POST['name']);

            // Validate name: min 3, max 200
            if (mb_strlen($name) < 3) {
                throw new Exception("Tên địa điểm phải có ít nhất 3 ký tự.");
            }
            if (mb_strlen($name) > 200) {
                throw new Exception("Tên địa điểm không được quá 200 ký tự.");
            }

            $data = [
                'name' => sanitize($name),
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'locations' => isset($_POST['locations']) ? sanitize($_POST['locations']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : null
            ];

            if ($this->destinationModel->update($id, $data)) {
                // Xóa ảnh cũ nếu có
                $deleted_count = 0;
                if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
                    foreach ($_POST['delete_images'] as $image_id) {
                        $image_id = (int) $image_id;
                        if ($image_id > 0) {
                            // Lấy thông tin ảnh trước khi xóa
                            $stmt = $this->db->prepare("SELECT image_url FROM destination_images WHERE id = :id AND destination_id = :destination_id");
                            $stmt->execute(['id' => $image_id, 'destination_id' => $id]);
                            $image = $stmt->fetch();

                            if ($image) {
                                // Xóa từ database
                                if ($this->destinationModel->deleteImage($image_id)) {
                                    // Xóa file vật lý
                                    if (file_exists($image['image_url'])) {
                                        @unlink($image['image_url']);
                                    }
                                    $deleted_count++;
                                }
                            }
                        }
                    }
                }

                // Upload ảnh mới nếu có
                $uploaded_count = 0;
                if (isset($_FILES['images']) && isset($_FILES['images']['name']) && is_array($_FILES['images']['name'])) {
                    // Kiểm tra xem có ít nhất một file được chọn không
                    $has_files = false;
                    foreach ($_FILES['images']['name'] as $filename) {
                        if (!empty($filename)) {
                            $has_files = true;
                            break;
                        }
                    }

                    if ($has_files) {
                        $uploaded_count = $this->handleDestinationImageUploads($id, $_FILES['images']);
                        if ($uploaded_count === 0) {
                            error_log("Warning: No images were uploaded despite files being selected. Check file validation.");
                        }
                    }
                }

                // Tạo thông báo thành công
                $messages = [];
                if ($deleted_count > 0) {
                    $messages[] = "Đã xóa {$deleted_count} ảnh";
                }
                if ($uploaded_count > 0) {
                    $messages[] = "Đã upload {$uploaded_count} ảnh mới";
                }

                if (!empty($messages)) {
                    set_success("Cập nhật thành công! " . implode(", ", $messages) . ".");
                } else {
                    set_success('Cập nhật thành công!');
                }

                // Redirect về trang list destinations
                $destination = $this->destinationModel->findById($id);
                $country_id = $destination['country_id'] ?? $_POST['country_id'] ?? null;
                $province_id = $destination['province_id'] ?? $_POST['province_id'] ?? null;
                redirect('?act=admin&module=location-services&country_id=' . $country_id . '&province_id=' . $province_id . '&tab=destinations');
            } else {
                throw new Exception("Không thể cập nhật.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=location-services&action=edit-destination&id=' . ($_POST['id'] ?? '') . '&country_id=' . ($_POST['country_id'] ?? '') . '&province_id=' . ($_POST['province_id'] ?? ''));
        }
    }

    /**
     * AJAX: Tạo destination (legacy - for modals)
     */
    public function createDestinationAjax()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if (empty($_POST['name']) || empty($_POST['province_id']) || empty($_POST['country_id'])) {
                throw new Exception("Vui lòng điền đầy đủ thông tin.");
            }

            $data = [
                'name' => sanitize($_POST['name']),
                'province_id' => (int) $_POST['province_id'],
                'country_id' => (int) $_POST['country_id'],
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'locations' => isset($_POST['locations']) ? sanitize($_POST['locations']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            $id = $this->destinationModel->create($data);

            if ($id) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Tạo địa điểm thành công!',
                    'id' => $id
                ]);
            } else {
                throw new Exception("Không thể tạo địa điểm.");
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
     * AJAX: Cập nhật destination (legacy - for modals)
     */
    public function updateDestinationAjax()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if (empty($_POST['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_POST['id'];
            $data = [
                'name' => sanitize($_POST['name']),
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'locations' => isset($_POST['locations']) ? sanitize($_POST['locations']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : null
            ];

            if ($this->destinationModel->update($id, $data)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật thành công!'
                ]);
            } else {
                throw new Exception("Không thể cập nhật.");
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
     * Xóa destination - Redirect về trang trước
     */
    public function deleteDestination()
    {
        require_admin();

        // Trả về JSON vì được gọi qua AJAX
        header('Content-Type: application/json');

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_GET['id'];

            // Lấy thông tin destination TRƯỚC KHI xóa để lấy country_id và province_id
            $destination = $this->destinationModel->findById($id);
            if (!$destination) {
                throw new Exception("Địa điểm không tồn tại.");
            }

            // Gọi phương thức delete (sẽ kiểm tra phụ thuộc và thực hiện soft delete)
            if ($this->destinationModel->delete($id)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã vô hiệu hóa địa điểm thành công!'
                ]);
            } else {
                throw new Exception("Không thể vô hiệu hóa địa điểm.");
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
     * Form tạo country mới
     */
    public function createCountry()
    {
        require_admin();

        $page_title = 'Thêm Quốc gia';
        $content_file = VIEWS_PATH . '/admin/location-services/create-country.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý tạo country mới
     */
    public function storeCountry()
    {
        require_admin();

        try {
            require_once COMMON_PATH . '/ValidationHelper.php';

            // Validate required fields
            if (empty($_POST['code']) || empty($_POST['name'])) {
                throw new Exception("Vui lòng điền đầy đủ thông tin bắt buộc.");
            }

            $code = strtoupper(trim($_POST['code']));
            $name = trim($_POST['name']);

            // Validate code: max 10 chars, unique
            if (mb_strlen($code) > 10) {
                throw new Exception("Mã quốc gia không được quá 10 ký tự.");
            }
            if (mb_strlen($code) < 2) {
                throw new Exception("Mã quốc gia phải có ít nhất 2 ký tự.");
            }

            // Check duplicate code
            $existing = $this->countryModel->findByCode($code);
            if ($existing) {
                throw new Exception("Mã quốc gia '{$code}' đã tồn tại. Vui lòng chọn mã khác.");
            }

            // Validate name: max 100 chars
            if (mb_strlen($name) > 100) {
                throw new Exception("Tên quốc gia không được quá 100 ký tự.");
            }
            if (mb_strlen($name) < 2) {
                throw new Exception("Tên quốc gia phải có ít nhất 2 ký tự.");
            }

            $data = [
                'code' => $code,
                'name' => sanitize($name),
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            $id = $this->countryModel->create($data);

            if ($id) {
                set_success('Tạo quốc gia thành công!');
                redirect('?act=admin&module=location-services&country_id=' . $id);
            } else {
                throw new Exception("Không thể tạo quốc gia.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=location-services&action=create-country');
        }
    }

    /**
     * Form sửa country
     */
    public function editCountry()
    {
        require_admin();

        if (empty($_GET['id'])) {
            set_error("Thiếu ID.");
            redirect('?act=admin&module=location-services');
            return;
        }

        $country = $this->countryModel->findById((int) $_GET['id']);
        if (!$country) {
            set_error("Không tìm thấy quốc gia.");
            redirect('?act=admin&module=location-services');
            return;
        }

        $page_title = 'Sửa Quốc gia';
        $content_file = VIEWS_PATH . '/admin/location-services/edit-country.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý cập nhật country
     */
    public function updateCountry()
    {
        require_admin();

        try {
            if (empty($_POST['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_POST['id'];

            // Check country exists
            $existing_country = $this->countryModel->findById($id);
            if (!$existing_country) {
                throw new Exception("Quốc gia không tồn tại.");
            }

            // Validate required fields
            if (empty($_POST['name'])) {
                throw new Exception("Vui lòng nhập tên quốc gia.");
            }

            $name = trim($_POST['name']);

            // Validate name: max 100 chars
            if (mb_strlen($name) > 100) {
                throw new Exception("Tên quốc gia không được quá 100 ký tự.");
            }
            if (mb_strlen($name) < 2) {
                throw new Exception("Tên quốc gia phải có ít nhất 2 ký tự.");
            }

            $data = [
                'name' => sanitize($name),
                'status' => isset($_POST['status']) ? $_POST['status'] : null
            ];

            if ($this->countryModel->update($id, $data)) {
                set_success('Cập nhật thành công!');
                redirect('?act=admin&module=location-services&country_id=' . $id);
            } else {
                throw new Exception("Không thể cập nhật.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=location-services&action=edit-country&id=' . ($_POST['id'] ?? ''));
        }
    }

    /**
     * Xóa country - Redirect về trang trước
     */
    public function deleteCountry()
    {
        require_admin();

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_GET['id'];

            if ($this->countryModel->delete($id)) {
                set_success('Xóa quốc gia thành công!');
            } else {
                throw new Exception("Không thể xóa.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
        }

        redirect('?act=admin&module=location-services');
    }

    /**
     * Toggle country status
     */
    public function toggleCountryStatus()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_GET['id'];

            if ($this->countryModel->toggleStatus($id)) {
                $country = $this->countryModel->findById($id);
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật trạng thái thành công!',
                    'status' => $country['status']
                ]);
            } else {
                throw new Exception("Không thể cập nhật trạng thái.");
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
     * Form tạo province mới
     */
    public function createProvince()
    {
        require_admin();

        $country_id = !empty($_GET['country_id']) ? (int) $_GET['country_id'] : null;

        if (!$country_id) {
            set_error("Thiếu thông tin quốc gia.");
            redirect('?act=admin&module=location-services');
            return;
        }

        $current_country = $this->countryModel->findById($country_id);
        if (!$current_country) {
            set_error("Không tìm thấy quốc gia.");
            redirect('?act=admin&module=location-services');
            return;
        }

        $page_title = 'Thêm Tỉnh thành';
        $content_file = VIEWS_PATH . '/admin/location-services/create-province.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý tạo province mới
     */
    public function storeProvince()
    {
        require_admin();

        try {
            if (empty($_POST['name']) || empty($_POST['country_id'])) {
                throw new Exception("Vui lòng điền đầy đủ thông tin bắt buộc.");
            }

            $name = trim($_POST['name']);
            $country_id = (int) $_POST['country_id'];

            // Validate country exists and active
            $country = $this->countryModel->findById($country_id);
            if (!$country) {
                throw new Exception("Quốc gia không tồn tại.");
            }
            if ($country['status'] != 'active') {
                throw new Exception("Quốc gia không khả dụng (đã bị vô hiệu hóa).");
            }

            // Validate name: max 100 chars
            if (mb_strlen($name) > 100) {
                throw new Exception("Tên tỉnh thành không được quá 100 ký tự.");
            }
            if (mb_strlen($name) < 2) {
                throw new Exception("Tên tỉnh thành phải có ít nhất 2 ký tự.");
            }

            // Validate code if provided
            $code = !empty($_POST['code']) ? trim($_POST['code']) : null;
            if ($code && mb_strlen($code) > 20) {
                throw new Exception("Mã tỉnh thành không được quá 20 ký tự.");
            }

            // Check duplicate code if provided
            if ($code) {
                $existing = $this->provinceModel->findByCode($code);
                if ($existing) {
                    throw new Exception("Mã tỉnh thành '{$code}' đã tồn tại. Vui lòng chọn mã khác.");
                }
            }

            $data = [
                'country_id' => $country_id,
                'code' => $code,
                'name' => sanitize($name),
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            $id = $this->provinceModel->create($data);

            if ($id) {
                set_success('Tạo tỉnh thành thành công!');
                redirect('?act=admin&module=location-services&country_id=' . $country_id . '&province_id=' . $id);
            } else {
                throw new Exception("Không thể tạo tỉnh thành.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=location-services&action=create-province&country_id=' . ($_POST['country_id'] ?? ''));
        }
    }

    /**
     * Form sửa province
     */
    public function editProvince()
    {
        require_admin();

        if (empty($_GET['id'])) {
            set_error("Thiếu ID.");
            redirect('?act=admin&module=location-services');
            return;
        }

        $province = $this->provinceModel->findById((int) $_GET['id']);
        if (!$province) {
            set_error("Không tìm thấy tỉnh thành.");
            redirect('?act=admin&module=location-services');
            return;
        }

        $country_id = $province['country_id'] ?? (!empty($_GET['country_id']) ? (int) $_GET['country_id'] : null);
        $current_country = $country_id ? $this->countryModel->findById($country_id) : null;

        $page_title = 'Sửa Tỉnh thành';
        $content_file = VIEWS_PATH . '/admin/location-services/edit-province.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý cập nhật province
     */
    public function updateProvince()
    {
        require_admin();

        try {
            if (empty($_POST['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_POST['id'];

            // Check province exists
            $existing_province = $this->provinceModel->findById($id);
            if (!$existing_province) {
                throw new Exception("Tỉnh thành không tồn tại.");
            }

            // Validate required fields
            if (empty($_POST['name'])) {
                throw new Exception("Vui lòng nhập tên tỉnh thành.");
            }

            $name = trim($_POST['name']);

            // Validate name: max 100 chars
            if (mb_strlen($name) > 100) {
                throw new Exception("Tên tỉnh thành không được quá 100 ký tự.");
            }
            if (mb_strlen($name) < 2) {
                throw new Exception("Tên tỉnh thành phải có ít nhất 2 ký tự.");
            }

            $data = [
                'name' => sanitize($name),
                'status' => isset($_POST['status']) ? $_POST['status'] : null
            ];

            if ($this->provinceModel->update($id, $data)) {
                set_success('Cập nhật thành công!');
                $province = $this->provinceModel->findById($id);
                redirect('?act=admin&module=location-services&country_id=' . $province['country_id'] . '&province_id=' . $id);
            } else {
                throw new Exception("Không thể cập nhật.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=location-services&action=edit-province&id=' . ($_POST['id'] ?? '') . '&country_id=' . ($_POST['country_id'] ?? ''));
        }
    }

    /**
     * Xóa province - Redirect về trang trước
     */
    public function deleteProvince()
    {
        require_admin();

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_GET['id'];
            $province = $this->provinceModel->findById($id);
            $country_id = $province['country_id'] ?? (!empty($_GET['country_id']) ? (int) $_GET['country_id'] : null);

            if ($this->provinceModel->delete($id)) {
                set_success('Xóa tỉnh thành thành công!');
            } else {
                throw new Exception("Không thể xóa.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
        }

        // Redirect về trang trước
        $redirect_url = '?act=admin&module=location-services';
        if ($country_id) {
            $redirect_url .= '&country_id=' . $country_id;
        }
        redirect($redirect_url);
    }

    /**
     * Toggle province status
     */
    public function toggleProvinceStatus()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_GET['id'];

            if ($this->provinceModel->toggleStatus($id)) {
                $province = $this->provinceModel->findById($id);
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật trạng thái thành công!',
                    'status' => $province['status']
                ]);
            } else {
                throw new Exception("Không thể cập nhật trạng thái.");
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
     * Upload ảnh cho destination
     */
    public function uploadDestinationImage()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if (empty($_POST['destination_id'])) {
                throw new Exception("Thiếu destination_id.");
            }

            $destination_id = (int) $_POST['destination_id'];

            // Check destination exists
            $destination = $this->destinationModel->findById($destination_id);
            if (!$destination) {
                throw new Exception("Địa điểm không tồn tại.");
            }

            if (empty($_FILES['images']) || $_FILES['images']['error'][0] === UPLOAD_ERR_NO_FILE) {
                throw new Exception("Vui lòng chọn ít nhất một ảnh.");
            }

            $uploaded_count = $this->handleDestinationImageUploads($destination_id, $_FILES['images']);

            if ($uploaded_count > 0) {
                // Reload images
                $images = $this->destinationModel->getImages($destination_id);
                echo json_encode([
                    'success' => true,
                    'message' => "Đã upload {$uploaded_count} ảnh thành công!",
                    'images' => $images
                ]);
            } else {
                throw new Exception("Không có ảnh nào được upload. Vui lòng kiểm tra định dạng và kích thước file.");
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
     * Xóa ảnh destination
     */
    public function deleteDestinationImage()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if (empty($_POST['image_id'])) {
                throw new Exception("Thiếu image_id.");
            }

            $image_id = (int) $_POST['image_id'];

            // Get image info before delete (to get file path)
            $stmt = $this->db->prepare("SELECT image_url, destination_id FROM destination_images WHERE id = :id");
            $stmt->execute(['id' => $image_id]);
            $image = $stmt->fetch();

            if (!$image) {
                throw new Exception("Ảnh không tồn tại.");
            }

            // Delete from database
            if ($this->destinationModel->deleteImage($image_id)) {
                // Delete physical file
                $file_path = $image['image_url'];
                if (file_exists($file_path)) {
                    @unlink($file_path);
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Đã xóa ảnh thành công!'
                ]);
            } else {
                throw new Exception("Không thể xóa ảnh.");
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
     * Set primary image
     */
    public function setPrimaryDestinationImage()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if (empty($_POST['image_id']) || empty($_POST['destination_id'])) {
                throw new Exception("Thiếu thông tin.");
            }

            $image_id = (int) $_POST['image_id'];
            $destination_id = (int) $_POST['destination_id'];

            if ($this->destinationModel->setPrimaryImage($image_id, $destination_id)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã đặt làm ảnh chính!'
                ]);
            } else {
                throw new Exception("Không thể đặt làm ảnh chính.");
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
     * Cập nhật caption cho ảnh
     */
    public function updateDestinationImageCaption()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if (empty($_POST['image_id'])) {
                throw new Exception("Thiếu image_id.");
            }

            $image_id = (int) $_POST['image_id'];
            $caption = !empty($_POST['caption']) ? sanitize($_POST['caption']) : null;

            // Validate caption length
            if ($caption && mb_strlen($caption) > 255) {
                throw new Exception("Caption không được quá 255 ký tự.");
            }

            if ($this->destinationModel->updateImageCaption($image_id, $caption)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật caption thành công!'
                ]);
            } else {
                throw new Exception("Không thể cập nhật caption.");
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
     * Sắp xếp lại thứ tự ảnh
     */
    public function reorderDestinationImages()
    {
        require_admin();
        header('Content-Type: application/json');

        try {
            if (empty($_POST['destination_id']) || empty($_POST['image_ids'])) {
                throw new Exception("Thiếu thông tin.");
            }

            $destination_id = (int) $_POST['destination_id'];
            $image_ids = $_POST['image_ids'];

            if (!is_array($image_ids) || empty($image_ids)) {
                throw new Exception("Danh sách ảnh không hợp lệ.");
            }

            // Validate all image_ids belong to destination
            $placeholders = implode(',', array_fill(0, count($image_ids), '?'));
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM destination_images 
                WHERE id IN ({$placeholders}) AND destination_id = ?
            ");
            $params = array_merge($image_ids, [$destination_id]);
            $stmt->execute($params);
            $result = $stmt->fetch();

            if ($result['count'] != count($image_ids)) {
                throw new Exception("Một số ảnh không thuộc về địa điểm này.");
            }

            if ($this->destinationModel->reorderImages($destination_id, $image_ids)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Sắp xếp lại thứ tự thành công!'
                ]);
            } else {
                throw new Exception("Không thể sắp xếp lại thứ tự.");
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
     * Helper: Xử lý upload ảnh cho destination
     */
    private function handleDestinationImageUploads($destination_id, $files)
    {
        require_once COMMON_PATH . '/ValidationHelper.php';

        $upload_dir = 'public/uploads/destinations/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $count = count($files['name']);
        $uploaded_count = 0;
        $skipped_count = 0;

        // Check if destination already has images to determine primary
        $existing_images = $this->destinationModel->getImages($destination_id);
        $has_primary = false;
        foreach ($existing_images as $img) {
            if ($img['is_primary']) {
                $has_primary = true;
                break;
            }
        }

        for ($i = 0; $i < $count; $i++) {
            // Skip empty file names
            if (empty($files['name'][$i])) {
                continue;
            }

            // Check upload error
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                error_log("File upload error for file {$i}: " . $files['error'][$i]);
                $skipped_count++;
                continue;
            }

            $tmp_name = $files['tmp_name'][$i];
            $name = basename($files['name'][$i]);
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            // Basic extension check
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                error_log("Invalid file extension for file {$i}: {$ext}");
                $skipped_count++;
                continue;
            }

            // Size check (5MB limit)
            if ($files['size'][$i] > 5 * 1024 * 1024) {
                error_log("File too large for file {$i}: " . round($files['size'][$i] / 1024 / 1024, 2) . "MB");
                $skipped_count++;
                continue;
            }

            // Validate image file (MIME type + dimensions)
            $validation = ValidationHelper::validateImageFile($tmp_name);
            if (!$validation['valid']) {
                error_log("Image upload validation failed for file {$i}: " . $validation['error']);
                $skipped_count++;
                continue;
            }

            // Generate unique name
            $new_name = 'dest_' . $destination_id . '_' . uniqid() . '.' . $ext;
            $destination_path = $upload_dir . $new_name;

            if (move_uploaded_file($tmp_name, $destination_path)) {
                $is_primary = (!$has_primary && $uploaded_count === 0);

                if ($this->destinationModel->addImage($destination_id, $destination_path, $is_primary)) {
                    $uploaded_count++;
                } else {
                    error_log("Failed to add image to database for file {$i}. Removing uploaded file.");
                    @unlink($destination_path);
                    $skipped_count++;
                }
            } else {
                error_log("Failed to move uploaded file {$i} to {$destination_path}");
                $skipped_count++;
            }
        }

        if ($skipped_count > 0) {
            error_log("Skipped {$skipped_count} files during upload for destination {$destination_id}");
        }

        return $uploaded_count;
    }

    /**
     * Helper: Lấy số lượng service providers theo province
     */
    private function getServiceProviderCountByProvince($province_id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM service_providers
                WHERE (province_id = :province_id OR EXISTS (
                    SELECT 1 FROM service_provider_locations 
                    WHERE service_provider_id = service_providers.id 
                    AND province_id = :province_id
                ))
                AND status = 'active'
            ");
            $stmt->execute(['province_id' => $province_id]);
            $result = $stmt->fetch();
            return (int) ($result['count'] ?? 0);
        } catch (PDOException $e) {
            return 0;
        }
    }
}

