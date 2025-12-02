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
                http_response_code(404);
                require VIEWS_PATH . '/errors/404.php';
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
