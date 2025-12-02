<?php
/**
 * ==============================================================================
 * TOUR MANAGEMENT SYSTEM - MAIN APPLICATION ROUTER
 * ==============================================================================
 * 
 * Entry point cho toàn bộ ứng dụng.
 * Xử lý routing theo parameter ?act=
 * 
 * Theo Vibe Coding: Simple is Best
 * 
 * @version 1.0
 * @date 2024-12-01
 * ==============================================================================
 */

// ============================================================================
// INITIALIZATION
// ============================================================================

// Start session
session_start();

// Load bootstrap (database, helpers, config)
require_once __DIR__ . '/bootstrap.php';

// ============================================================================
// PARSE ROUTE PARAMETERS
// ============================================================================

// Lấy action từ query string: ?act=login, ?act=admin-dashboard, etc.
$act = $_GET['act'] ?? '';

// Lấy HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// ============================================================================
// PUBLIC ROUTES (Không cần login)
// ============================================================================

// Route: Login
if ($act === 'login' || $act === '') {
    if ($method === 'GET') {
        // Nếu đã login rồi thì redirect về dashboard
        if (is_logged_in()) {
            redirect_to_dashboard();
            exit;
        }
        require VIEWS_PATH . '/auth/login.php';
        exit;
    } elseif ($method === 'POST') {
        require_once CONTROLLERS_PATH . '/AuthController.php';
        $authController = new AuthController($pdo);
        $authController->handleLogin();
        exit;
    }
}

// Route: Logout
if ($act === 'logout') {
    require_once CONTROLLERS_PATH . '/AuthController.php';
    $authController = new AuthController($pdo);
    $authController->logout();
    exit;
}

// Route: Access denied
if ($act === 'access-denied') {
    require VIEWS_PATH . '/errors/access-denied.php';
    exit;
}

// ============================================================================
// AUTHENTICATED ROUTES (Cần login)
// ============================================================================

require_login(); // Từ đây trở xuống phải login

// ============================================================================
// ADMIN ROUTES - MODULE BASED (act=admin&module=X&action=Y)
// ============================================================================

if ($act === 'admin') {
    require_admin();

    $module = $_GET['module'] ?? '';
    $action = $_GET['action'] ?? 'index';

    switch ($module) {
        // ========== CATEGORIES MODULE ==========
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

        // ========== USERS MODULE ==========
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

        // ========== DEFAULT: DASHBOARD ==========
        default:
            require_once CONTROLLERS_PATH . '/DashboardController.php';
            $dashboardController = new DashboardController($pdo);
            $dashboardController->adminDashboard();
            break;
    }
    exit;
}

// ============================================================================
// ADMIN ROUTES - OLD STYLE (Backward compatibility)
// ============================================================================

if (strpos($act, 'admin-') === 0 || strpos($act, 'admin/') === 0) {
    require_admin(); // Check admin permission

    require_once CONTROLLERS_PATH . '/DashboardController.php';
    $dashboardController = new DashboardController($pdo);

    switch ($act) {
        case 'admin-dashboard':
            $dashboardController->adminDashboard();
            break;

        // ========== USER MANAGEMENT ==========
        case 'admin/users':
        case 'admin/users/index':
            require_once CONTROLLERS_PATH . '/admin/UserController.php';
            $userController = new UserController($pdo);
            $userController->index();
            break;

        case 'admin/users/create':
            require_once CONTROLLERS_PATH . '/admin/UserController.php';
            $userController = new UserController($pdo);
            $userController->create();
            break;

        case 'admin/users/store':
            require_once CONTROLLERS_PATH . '/admin/UserController.php';
            $userController = new UserController($pdo);
            $userController->store();
            break;

        case 'admin/users/edit':
            require_once CONTROLLERS_PATH . '/admin/UserController.php';
            $userController = new UserController($pdo);
            $userController->edit();
            break;

        case 'admin/users/update':
            require_once CONTROLLERS_PATH . '/admin/UserController.php';
            $userController = new UserController($pdo);
            $userController->update();
            break;

        case 'admin/users/delete':
            require_once CONTROLLERS_PATH . '/admin/UserController.php';
            $userController = new UserController($pdo);
            $userController->delete();
            break;

        case 'admin/users/toggle-status':
            require_once CONTROLLERS_PATH . '/admin/UserController.php';
            $userController = new UserController($pdo);
            $userController->toggleStatus();
            break;

        // ========== OTHER ADMIN ROUTES ==========
        case 'admin-tours':
            echo "Admin Tours - Coming soon";
            break;

        case 'admin-tours-pending':
            echo "Admin Tours Pending - Coming soon";
            break;

        case 'admin-bookings':
            echo "Admin Bookings - Coming soon";
            break;

        case 'admin-bookings-pending':
            echo "Admin Bookings Pending - Coming soon";
            break;

        case 'admin-users':
            echo "Admin Users - Coming soon";
            break;

        case 'admin-suppliers':
            echo "Admin Suppliers - Coming soon";
            break;

        case 'admin-services':
            echo "Admin Services - Coming soon";
            break;

        case 'admin-categories':
            echo "Admin Categories - Coming soon";
            break;

        case 'admin-destinations':
            echo "Admin Destinations - Coming soon";
            break;

        case 'admin-reports':
            echo "Admin Reports - Coming soon";
            break;

        default:
            http_response_code(404);
            require VIEWS_PATH . '/errors/404.php';
            break;
    }
    exit;
}


// ============================================================================
// STAFF ROUTES
// ============================================================================

if (strpos($act, 'staff-') === 0) {
    require_staff(); // Check staff permission

    require_once CONTROLLERS_PATH . '/DashboardController.php';
    $dashboardController = new DashboardController($pdo);

    switch ($act) {
        case 'staff-dashboard':
            $dashboardController->staffDashboard();
            break;

        case 'staff-tours':
            echo "Staff Tours - Coming soon";
            break;

        case 'staff-tours-create':
            echo "Staff Create Tour - Coming soon";
            break;

        case 'staff-bookings':
            echo "Staff Bookings - Coming soon";
            break;

        case 'staff-bookings-create':
            echo "Staff Create Booking - Coming soon";
            break;

        case 'staff-customers':
            echo "Staff Customers - Coming soon";
            break;

        case 'staff-customers-create':
            echo "Staff Create Customer - Coming soon";
            break;

        case 'staff-customers-import':
            echo "Staff Import Customers - Coming soon";
            break;

        case 'staff-payments':
            echo "Staff Payments - Coming soon";
            break;

        default:
            http_response_code(404);
            require VIEWS_PATH . '/errors/404.php';
            break;
    }
    exit;
}

// ============================================================================
// PROFILE ROUTES (All roles)
// ============================================================================

if (strpos($act, 'profile') === 0) {
    require_once CONTROLLERS_PATH . '/ProfileController.php';
    $profileController = new ProfileController($pdo);

    switch ($act) {
        case 'profile':
        case 'profile/index':
            $profileController->index();
            break;

        case 'profile/edit':
            $profileController->edit();
            break;

        case 'profile/update':
            $profileController->update();
            break;

        case 'profile/change-password':
            $profileController->changePassword();
            break;

        case 'profile/update-password':
            $profileController->updatePassword();
            break;

        default:
            http_response_code(404);
            require VIEWS_PATH . '/errors/404.php';
            break;
    }
    exit;
}

// ============================================================================
// GUIDE ROUTES
// ============================================================================

if (strpos($act, 'guide-') === 0) {
    require_guide(); // Check guide permission

    require_once CONTROLLERS_PATH . '/DashboardController.php';
    $dashboardController = new DashboardController($pdo);

    switch ($act) {
        case 'guide-dashboard':
            $dashboardController->guideDashboard();
            break;

        case 'guide-tours':
            echo "Guide Tours - Coming soon";
            break;

        case 'guide-checkin':
            echo "Guide Check-in - Coming soon";
            break;

        case 'guide-journal':
            echo "Guide Journal - Coming soon";
            break;

        case 'guide-expenses':
            echo "Guide Expenses - Coming soon";
            break;

        default:
            http_response_code(404);
            require VIEWS_PATH . '/errors/404.php';
            break;
    }
    exit;
}

// ============================================================================
// 404 NOT FOUND
// ============================================================================

http_response_code(404);
require VIEWS_PATH . '/errors/404.php';
exit;

