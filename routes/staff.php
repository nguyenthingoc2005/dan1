<?php
/**
 * ==============================================================================
 * STAFF ROUTES - Module Based Routing
 * ==============================================================================
 * 
 * Pattern: ?act=staff-{module}&action=Y
 * 
 * Modules: tours, bookings, customers, payments
 * 
 * @version 2.0
 * @date 2024-12-03
 * ==============================================================================
 */

// Only staff or admin can access
require_staff_or_admin();

// Parse act parameter to extract module
// Example: ?act=staff-tours -> module = tours
$act = $_GET['act'] ?? '';
$module = str_replace('staff-', '', $act);
$action = $_GET['action'] ?? 'index';

switch ($module) {
    // ==========================================================================
    // TOURS MODULE
    // ==========================================================================
    case 'tours':
        require_once CONTROLLERS_PATH . '/staff/TourController.php';
        $controller = new Staff\TourController($pdo);

        switch ($action) {
            case 'index':
                $controller->index();
                break;
            case 'selectTemplate':
                $controller->selectTemplate();
                break;
            case 'create':
                $controller->create();
                break;
            case 'createFromTemplate':
                $controller->createFromTemplate();
                break;
            case 'store':
                $controller->store();
                break;
            case 'show':
                $controller->show();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'update':
                $controller->update();
                break;
            case 'getDestinations':
                $controller->getDestinations();
                break;
            case 'getServiceInfo':
                $controller->getServiceInfo();
                break;
            case 'getServiceProviders':
                $controller->getServiceProviders();
                break;
            case 'createPolicy':
                $controller->createPolicy();
                break;
            case 'getPolicy':
                $controller->getPolicy();
                break;
            case 'loadDayServicesEditor':
                $controller->loadDayServicesEditor();
                break;
            case 'loadItineraryManager':
                $controller->loadItineraryManager();
                break;
            case 'saveFormSession':
                $controller->saveFormSession();
                break;
            case 'clearTourSession':
                $controller->clearTourSession();
                break;
            case 'uploadImage':
                $controller->uploadImage();
                break;
            default:
                $controller->index();
                break;
        }
        break;

    // ==========================================================================
    // BOOKINGS MODULE
    // ==========================================================================
    case 'bookings':
        require_once CONTROLLERS_PATH . '/staff/BookingController.php';
        $controller = new Staff\BookingController($pdo);

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
            case 'show':
                $controller->show();
                break;
            case 'storePayment':
                $controller->storePayment();
                break;
            case 'previewPassengers':
                $controller->previewPassengers();
                break;
            case 'downloadTemplate':
                $controller->downloadTemplate();
                break;
            case 'storeBookingService':
                $controller->storeBookingService();
                break;
            case 'deleteBookingService':
                $controller->deleteBookingService();
                break;
            case 'addPassengerToBooking':
                $controller->addPassengerToBooking();
                break;
            default:
                $controller->index();
                break;
        }
        break;

    // ==========================================================================
    // CUSTOMERS MODULE
    // ==========================================================================
    case 'customers':
        require_once CONTROLLERS_PATH . '/staff/CustomerController.php';
        $controller = new Staff\CustomerController($pdo);

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
            case 'show':
                $controller->show();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'update':
                $controller->update();
                break;
            case 'import':
                $controller->import();
                break;
            case 'importStore':
                $controller->importStore();
                break;
            case 'importResult':
                $controller->importResult();
                break;
            case 'importLogs':
                $controller->importLogs();
                break;
            case 'downloadTemplate':
                $controller->downloadTemplate();
                break;
            default:
                $controller->index();
                break;
        }
        break;

    // ==========================================================================
    // PAYMENTS MODULE (READ ONLY)
    // ==========================================================================
    case 'payments':
        require_once CONTROLLERS_PATH . '/staff/PaymentController.php';
        $controller = new Staff\PaymentController($pdo);

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
    // SCHEDULES MODULE (READ ONLY - ĐỂ TƯ VẤN KHÁCH HÀNG)
    // ==========================================================================
    case 'schedules':
        require_once CONTROLLERS_PATH . '/staff/ScheduleController.php';
        $controller = new Staff\ScheduleController($pdo);

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
        require_once CONTROLLERS_PATH . '/DashboardController.php';
        $dashboardController = new DashboardController($pdo);
        $dashboardController->staffDashboard();
        break;
}
