<?php
/**
 * ==============================================================================
 * ADMIN ROUTES - Module Based Routing
 * ==============================================================================
 * 
 * Pattern: ?act=admin&module=X&action=Y
 * 
 * Modules: categories, users, service-types, suppliers, services, destinations
 * 
 * @version 2.1
 * @date 2024-12-02
 * ==============================================================================
 */

// Only admin can access
require_admin();

// Get module and action from query string
$module = $_GET['module'] ?? '';
$action = $_GET['action'] ?? 'index';

switch ($module) {
    // ==========================================================================
    // CATEGORIES MODULE
    // ==========================================================================
    case 'categories':
        require_once CONTROLLERS_PATH . '/admin/CategoryController.php';
        $controller = new CategoryController($pdo);

        switch ($action) {
            case 'index':
                $controller->index();
                break;
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'update':
                $controller->update();
                break;
            case 'delete':
                $controller->delete();
                break;
            default:
                http_response_code(404);
                require VIEWS_PATH . '/errors/404.php';
        }
        break;

    // ==========================================================================
    // USERS MODULE
    // ==========================================================================
    case 'users':
        require_once CONTROLLERS_PATH . '/admin/UserController.php';
        $controller = new UserController($pdo);

        switch ($action) {
            case 'index':
                $controller->index();
                break;
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'update':
                $controller->update();
                break;
            case 'delete':
                $controller->delete();
                break;
            case 'toggle-status':
                $controller->toggleStatus();
                break;
            default:
                http_response_code(404);
                require VIEWS_PATH . '/errors/404.php';
        }
        break;

    // ==========================================================================
    // SERVICE TYPES MODULE
    // ==========================================================================
    case 'service-types':
        require_once CONTROLLERS_PATH . '/admin/ServiceTypeController.php';
        $controller = new ServiceTypeController($pdo);

        switch ($action) {
            case 'index':
                $controller->index();
                break;
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'update':
                $controller->update();
                break;
            case 'delete':
                $controller->delete();
                break;
            default:
                http_response_code(404);
                require VIEWS_PATH . '/errors/404.php';
        }
        break;

    // ==========================================================================
    // SUPPLIERS MODULE
    // ==========================================================================
    case 'suppliers':
        require_once CONTROLLERS_PATH . '/admin/SupplierController.php';
        $controller = new SupplierController($pdo);

        switch ($action) {
            case 'index':
                $controller->index();
                break;
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'update':
                $controller->update();
                break;
            case 'delete':
                $controller->delete();
                break;
            default:
                http_response_code(404);
                require VIEWS_PATH . '/errors/404.php';
        }
        break;

    // ==========================================================================
    // SERVICES MODULE
    // ==========================================================================
    case 'services':
        require_once CONTROLLERS_PATH . '/admin/ServiceController.php';
        $controller = new ServiceController($pdo);

        switch ($action) {
            case 'index':
                $controller->index();
                break;
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'update':
                $controller->update();
                break;
            case 'delete':
                $controller->delete();
                break;
            case 'getServiceInfo':
                $controller->getServiceInfo();
                break;
            default:
                http_response_code(404);
                require VIEWS_PATH . '/errors/404.php';
        }
        break;

    // ==========================================================================
    // DESTINATIONS MODULE (Coming soon)
    // ==========================================================================
    case 'destinations':
        require_once CONTROLLERS_PATH . '/admin/DestinationController.php';
        $controller = new DestinationController($pdo);

        switch ($action) {
            case 'index':
                $controller->index();
                break;
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'update':
                $controller->update();
                break;
            case 'delete':
                $controller->delete();
                break;
            default:
                require VIEWS_PATH . '/errors/404.php';
        }
        break;

    // ==========================================================================
    // MODULE: TOURS
    // ==========================================================================
    case 'tours':
        require_once CONTROLLERS_PATH . '/admin/TourController.php';
        $tourController = new TourController($pdo);

        switch ($action) {
            case 'index':
                $tourController->index();
                break;
            case 'create':
                $tourController->create();
                break;
            case 'store':
                $tourController->store();
                break;
            case 'show':
                $tourController->show();
                break;
            case 'edit':
                $tourController->edit();
                break;
            case 'update':
                $tourController->update();
                break;
            case 'changeStatus':
                $tourController->changeStatus();
                break;
            case 'delete':
                $tourController->delete();
                break;
            // NEW: Custom Tour from Template
            case 'selectTemplate':
                $tourController->selectTemplate();
                break;
            case 'createFromTemplate':
                $tourController->createFromTemplate();
                break;
            case 'getTemplateData':
                $tourController->getTemplateData();
                break;
            // AJAX APIs
            case 'getDestinations':
                $tourController->getDestinations();
                break;
            case 'getServiceInfo':
                $tourController->getServiceInfo();
                break;
            default:
                $tourController->index();
                break;
        }
        break;

    // ==========================================================================
    // MODULE: BOOKINGS
    // ==========================================================================
    case 'bookings':
        require_once CONTROLLERS_PATH . '/admin/BookingController.php';
        $bookingController = new BookingController($pdo);

        switch ($action) {
            case 'index':
                $bookingController->index();
                break;
            case 'create':
                $bookingController->create();
                break;
            case 'store':
                $bookingController->store();
                break;
            case 'show':
                $bookingController->show();
                break;
            case 'changeStatus':
                $bookingController->changeStatus();
                break;
            case 'storePayment':
                $bookingController->storePayment();
                break;
            case 'importPassengers':
                $bookingController->importPassengers();
                break;
            case 'previewPassengers':
                $bookingController->previewPassengers();
                break;
            case 'downloadTemplate':
                $bookingController->downloadTemplate();
                break;
            default:
                $bookingController->index();
                break;
        }
        break;

    // ==========================================================================
    // MODULE: CUSTOMERS (Quản lý Khách hàng)
    // ==========================================================================
    case 'customers':
        require_once CONTROLLERS_PATH . '/admin/CustomerController.php';
        $customerController = new CustomerController($pdo);

        switch ($action) {
            case 'index':
                $customerController->index();
                break;
            case 'create':
                $customerController->create();
                break;
            case 'store':
                $customerController->store();
                break;
            case 'show':
                $customerController->show();
                break;
            case 'edit':
                $customerController->edit();
                break;
            case 'update':
                $customerController->update();
                break;
            case 'delete':
                $customerController->delete();
                break;
            default:
                $customerController->index();
                break;
        }
        break;

    // ==========================================================================
    // MODULE: SCHEDULES (Tour Schedules / Lịch khởi hành)
    // ==========================================================================
    case 'schedules':
        require_once CONTROLLERS_PATH . '/admin/TourScheduleController.php';
        $scheduleController = new TourScheduleController($pdo);

        switch ($action) {
            case 'index':
                $scheduleController->index();
                break;
            case 'create':
                $scheduleController->create();
                break;
            case 'store':
                $scheduleController->store();
                break;
            case 'show':
                $scheduleController->show();
                break;
            case 'edit':
                $scheduleController->edit();
                break;
            case 'update':
                $scheduleController->update();
                break;
            case 'delete':
                $scheduleController->delete();
                break;
            case 'changeStatus':
                $scheduleController->changeStatus();
                break;
            default:
                $scheduleController->index();
                break;
        }
        break;

    // ==========================================================================
    // MODULE: PAYMENTS (Quản lý Thanh toán)
    // ==========================================================================
    case 'payments':
        require_once CONTROLLERS_PATH . '/admin/PaymentController.php';
        $paymentController = new PaymentController($pdo);

        switch ($action) {
            case 'index':
                $paymentController->index();
                break;
            case 'create':
                $paymentController->create();
                break;
            case 'show':
                $paymentController->show();
                break;
            default:
                $paymentController->index();
                break;
        }
        break;

    // ==========================================================================
    // MODULE: JOURNALS (Nhật ký Tour)
    // ==========================================================================
    case 'journals':
        require_once CONTROLLERS_PATH . '/admin/JournalController.php';
        $journalController = new JournalController($pdo);

        switch ($action) {
            case 'index':
                $journalController->index();
                break;
            case 'create':
                $journalController->create();
                break;
            case 'store':
                $journalController->store();
                break;
            case 'show':
                $journalController->show();
                break;
            default:
                $journalController->index();
                break;
        }
        break;

    // ==========================================================================
    // MODULE: BOOKING SERVICES (Dịch vụ đặt cho Booking)
    // ==========================================================================
    case 'booking-services':
        require_once CONTROLLERS_PATH . '/admin/BookingServiceController.php';
        $bookingServiceController = new BookingServiceController($pdo);

        switch ($action) {
            case 'index':
                $bookingServiceController->index();
                break;
            case 'store':
                $bookingServiceController->store();
                break;
            case 'update':
                $bookingServiceController->update();
                break;
            case 'delete':
                $bookingServiceController->delete();
                break;
            case 'getServiceInfo':
                $bookingServiceController->getServiceInfo();
                break;
            case 'copyFromTour':
                $bookingServiceController->copyFromTour();
                break;
            default:
                $bookingServiceController->index();
                break;
        }
        break;

    // ==========================================================================
    // MODULE: REPORTS (Báo cáo)
    // ==========================================================================
    case 'reports':
        require_once CONTROLLERS_PATH . '/admin/ReportController.php';
        $reportController = new ReportController($pdo);

        switch ($action) {
            case 'index':
                $reportController->index();
                break;
            case 'revenue':
                $reportController->revenue();
                break;
            case 'bookings':
                $reportController->bookings();
                break;
            default:
                $reportController->index();
                break;
        }
        break;

    // ==========================================================================
    // MODULE: SETTINGS (Cài đặt)
    // ==========================================================================
    case 'settings':
        require_once CONTROLLERS_PATH . '/admin/SettingController.php';
        $settingController = new SettingController($pdo);

        switch ($action) {
            case 'general':
                $settingController->general();
                break;
            case 'email':
                $settingController->email();
                break;
            default:
                $settingController->general();
                break;
        }
        break;

    // ==========================================================================
    // DEFAULT: DASHBOARD
    // ==========================================================================
    default:
        require_once CONTROLLERS_PATH . '/DashboardController.php';
        $dashboardController = new DashboardController($pdo);
        $dashboardController->adminDashboard();
        break;
}
