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
            $countries = $this->countryModel->getAll(['status' => 'active'], 1, 100);
            if (!isset($countries['data'])) {
                $countries['data'] = [];
            }

            // Add provinces count for each country
            foreach ($countries['data'] as &$country) {
                try {
                    $country['provinces_count'] = $this->countryModel->getProvinceCount($country['id']);
                } catch (Exception $e) {
                    error_log("Error getting province count for country {$country['id']}: " . $e->getMessage());
                    $country['provinces_count'] = 0;
                }
            }

            // Load provinces if country_id is set
            $provinces = [];
            $current_country = null;
            $current_country_id = $country_id;
            if ($country_id) {
                try {
                    $current_country = $this->countryModel->findById($country_id);
                    $provinces = $this->provinceModel->getForDropdown($country_id);
                    foreach ($provinces as &$province) {
                        try {
                            $province['providers_count'] = $this->getServiceProviderCountByProvince($province['id']);
                        } catch (Exception $e) {
                            error_log("Error getting providers count for province {$province['id']}: " . $e->getMessage());
                            $province['providers_count'] = 0;
                        }
                    }
                } catch (Exception $e) {
                    error_log("Error loading provinces for country {$country_id}: " . $e->getMessage());
                    $provinces = [];
                }
            }

            // Load service providers and destinations if province_id is set
            $service_providers = ['data' => []];
            $destinations = ['data' => []];
            $current_province = null;
            $current_province_id = $province_id;
            if ($province_id) {
                try {
                    $current_province = $this->provinceModel->findById($province_id);
                    if (!$current_province && $country_id) {
                        $current_country = $this->countryModel->findById($country_id);
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

                    // Load destinations
                    $destinations = $this->destinationModel->getAll(['province_id' => $province_id, 'status' => 'active'], 1, 100);
                    if (!isset($destinations['data'])) {
                        $destinations['data'] = [];
                    }
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
                                $province_id = $current_provider_obj['province_id'];
                                $current_province_id = $province_id;
                            }
                            if (!$country_id && !empty($current_provider_obj['country_id'])) {
                                $country_id = $current_provider_obj['country_id'];
                                $current_country_id = $country_id;
                            }

                            // Reload provinces và providers nếu có province_id và chưa load
                            if ($province_id && empty($service_providers['data'])) {
                                if (!$country_id && !empty($current_provider_obj['country_id'])) {
                                    $country_id = $current_provider_obj['country_id'];
                                    $current_country_id = $country_id;
                                }
                                if ($country_id) {
                                    try {
                                        if (empty($current_country)) {
                                            $current_country = $this->countryModel->findById($country_id);
                                        }
                                        if (empty($provinces)) {
                                            $provinces = $this->provinceModel->getForDropdown($country_id);
                                            foreach ($provinces as &$province) {
                                                try {
                                                    $province['providers_count'] = $this->getServiceProviderCountByProvince($province['id']);
                                                } catch (Exception $e) {
                                                    $province['providers_count'] = 0;
                                                }
                                            }
                                        }
                                    } catch (Exception $e) {
                                        error_log("Error reloading provinces: " . $e->getMessage());
                                    }
                                }
                                try {
                                    if (empty($current_province)) {
                                        $current_province = $this->provinceModel->findById($province_id);
                                    }
                                    if (empty($service_providers['data'])) {
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
                                    }
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
            if (empty($_POST['service_provider_id']) || empty($_POST['service_type_id']) || empty($_POST['name'])) {
                throw new Exception("Vui lòng điền đầy đủ thông tin.");
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
            $data = [
                'name' => sanitize($_POST['name']),
                'service_type_id' => !empty($_POST['service_type_id']) ? (int) $_POST['service_type_id'] : null,
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

            // Validate service tồn tại
            $service = $this->serviceModel->findById((int) $_POST['service_id']);
            if (!$service) {
                throw new Exception("Không tìm thấy dịch vụ.");
            }

            // Giá chỉ gắn với service, không cần destination_id hay province_id
            // destination_id và province_id là optional (nếu muốn giá riêng cho địa điểm/tỉnh cụ thể)
            $data = [
                'service_id' => (int) $_POST['service_id'],
                'destination_id' => !empty($_POST['destination_id']) ? (int) $_POST['destination_id'] : null,
                'province_id' => !empty($_POST['province_id']) ? (int) $_POST['province_id'] : null,
                'unit_price' => (float) $_POST['unit_price'],
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
            $data = [
                'unit_price' => (float) $_POST['unit_price'],
                'start_date' => !empty($_POST['start_date']) ? $_POST['start_date'] : (!empty($_POST['valid_from']) ? $_POST['valid_from'] : null),
                'end_date' => !empty($_POST['end_date']) ? $_POST['end_date'] : (!empty($_POST['valid_to']) ? $_POST['valid_to'] : null),
                'price_type' => $_POST['price_type'] ?? 'standard',
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
                set_success('Tạo địa điểm thành công!');
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
            $data = [
                'name' => sanitize($_POST['name']),
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'locations' => isset($_POST['locations']) ? sanitize($_POST['locations']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : null
            ];

            if ($this->destinationModel->update($id, $data)) {
                set_success('Cập nhật thành công!');
                $destination = $this->destinationModel->findById($id);
                redirect('?act=admin&module=location-services&country_id=' . $destination['country_id'] . '&province_id=' . $destination['province_id'] . '&tab=destinations');
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

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Thiếu ID.");
            }

            $id = (int) $_GET['id'];
            $destination = $this->destinationModel->findById($id);
            $country_id = $destination['country_id'] ?? (!empty($_GET['country_id']) ? (int) $_GET['country_id'] : null);
            $province_id = $destination['province_id'] ?? (!empty($_GET['province_id']) ? (int) $_GET['province_id'] : null);

            if ($this->destinationModel->delete($id)) {
                set_success('Xóa địa điểm thành công!');
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
            $redirect_url .= '&province_id=' . $province_id . '&tab=destinations';
        }
        redirect($redirect_url);
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

