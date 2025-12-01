<?php
/**
 * ==============================================================================
 * STAFF ROUTES - Staff Section Routing
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

// Remove /staff prefix
$route = str_replace('/staff', '', $request_uri);
if (empty($route)) {
    $route = '/';
}

// Instantiate controllers
$dashboardController = new DashboardController($pdo);

// ============================================================================
// STAFF ROUTES
// ============================================================================

// Dashboard
if ($route === '/' || $route === '/dashboard') {
    $dashboardController->staffDashboard();
    exit;
}

// Tours
if (strpos($route, '/tours') === 0) {
    // TODO: TourController
    echo "Staff Tours - Coming soon";
    exit;
}

// Bookings
if (strpos($route, '/bookings') === 0) {
    // TODO: BookingController
    echo "Staff Bookings - Coming soon";
    exit;
}

// Customers
if (strpos($route, '/customers') === 0) {
    // TODO: CustomerController
    echo "Staff Customers - Coming soon";
    exit;
}

// Payments
if (strpos($route, '/payments') === 0) {
    // TODO: PaymentController
    echo "Staff Payments - Coming soon";
    exit;
}

// 404
http_response_code(404);
echo "404 - Staff route not found";
