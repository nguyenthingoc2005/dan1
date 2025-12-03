<?php
/**
 * ==============================================================================
 * GUIDE ROUTES - Guide Section Routing
 * ==============================================================================
 */

// Only guide or admin can access
require_guide();

// Parse act parameter to extract module
// Example: ?act=guide-tours -> module = tours
$act = $_GET['act'] ?? '';
$module = str_replace('guide-', '', $act);
$action = $_GET['action'] ?? 'index';

switch ($module) {
    // ==========================================================================
    // TOURS MODULE
    // ==========================================================================
    case 'tours':
        require_once CONTROLLERS_PATH . '/guide/TourController.php';
        $controller = new Guide\TourController($pdo);

        switch ($action) {
            case 'index':
                $controller->index();
                break;
            case 'show':
                $controller->show();
                break;
            default:
                $controller->index();
                break;
        }
        break;

    // ==========================================================================
    // DEFAULT: DASHBOARD
    // ==========================================================================
    case 'dashboard':
    default:
        require_once CONTROLLERS_PATH . '/guide/DashboardController.php';
        $dashboardController = new Guide\DashboardController($pdo);
        $dashboardController->index();
        break;
}
