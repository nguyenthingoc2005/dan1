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
    // CHECK-IN MODULE
    // ==========================================================================
    case 'checkin':
        require_once CONTROLLERS_PATH . '/guide/CheckinController.php';
        $checkinController = new Guide\CheckinController($pdo);

        switch ($action) {
            case 'index':
                $checkinController->index();
                break;
            case 'show':
                $checkinController->show();
                break;
            case 'store':
                $checkinController->store();
                break;
            case 'printManifest':
                $checkinController->printManifest();
                break;
            default:
                $checkinController->index();
                break;
        }
        break;

    // ==========================================================================
    // JOURNAL MODULE
    // ==========================================================================
    case 'journals':
    case 'journal': // Support both for backward compatibility
        require_once CONTROLLERS_PATH . '/guide/JournalController.php';
        $journalController = new Guide\JournalController($pdo);

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
            case 'edit':
                $journalController->edit();
                break;
            case 'update':
                $journalController->update();
                break;
            case 'delete':
                $journalController->delete();
                break;
            default:
                $journalController->index();
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
