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
            default:
                $bookingController->index();
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
            default:
                $scheduleController->index();
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
