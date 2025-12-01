<?php
/**
 * ==============================================================================
 * ADMIN ROUTES - Admin Section Routing
 * ==============================================================================
 */

require_once CONTROLLERS_PATH . '/DashboardController.php';

// Get request URI
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = dirname($_SERVER['SCRIPT_NAME']);
if ($base_path !== '/') {
    $request_uri = substr($request_uri, strlen($base_path));
}
$request_uri = strtok($request_uri, '?');

// Remove /admin prefix
$route = str_replace('/admin', '', $request_uri);
if (empty($route)) {
    $route = '/';
}

// Instantiate controllers
$dashboardController = new DashboardController($pdo);

// ============================================================================
// ADMIN ROUTES
// ============================================================================

// Dashboard
if ($route === '/' || $route === '/dashboard') {
    $dashboardController->adminDashboard();
    exit;
}

// Tours
if (strpos($route, '/tours') === 0) {
    // TODO: TourController
    echo "Admin Tours - Coming soon";
    exit;
}

// Bookings
if (strpos($route, '/bookings') === 0) {
    // TODO: BookingController
    echo "Admin Bookings - Coming soon";
    exit;
}

// Users
if (strpos($route, '/users') === 0) {
    // TODO: UserController
    echo "Admin Users - Coming soon";
    exit;
}

// Suppliers
if (strpos($route, '/suppliers') === 0) {
    // TODO: SupplierController
    echo "Admin Suppliers - Coming soon";
    exit;
}

// Services
if (strpos($route, '/services') === 0) {
    // TODO: ServiceController
    echo "Admin Services - Coming soon";
    exit;
}

// Categories
if (strpos($route, '/categories') === 0) {
    // TODO: CategoryController
    echo "Admin Categories - Coming soon";
    exit;
}

// Destinations
if (strpos($route, '/destinations') === 0) {
    // TODO: DestinationController
    echo "Admin Destinations - Coming soon";
    exit;
}

// Reports
if (strpos($route, '/reports') === 0) {
    // TODO: ReportController
    echo "Admin Reports - Coming soon";
    exit;
}

// 404
http_response_code(404);
echo "404 - Admin route not found";
