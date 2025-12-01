<?php
/**
 * ==============================================================================
 * GUIDE ROUTES - Guide Section Routing
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

// Remove /guide prefix
$route = str_replace('/guide', '', $request_uri);
if (empty($route)) {
    $route = '/';
}

// Instantiate controllers
$dashboardController = new DashboardController($pdo);

// ============================================================================
// GUIDE ROUTES
// ============================================================================

// Dashboard
if ($route === '/' || $route === '/dashboard') {
    $dashboardController->guideDashboard();
    exit;
}

// Tours
if (strpos($route, '/tours') === 0) {
    // TODO: GuideController
    echo "Guide Tours - Coming soon";
    exit;
}

// Check-in
if (strpos($route, '/checkin') === 0) {
    // TODO: CheckinController
    echo "Guide Check-in - Coming soon";
    exit;
}

// Journal
if (strpos($route, '/journal') === 0) {
    // TODO: JournalController
    echo "Guide Journal - Coming soon";
    exit;
}

// Expenses
if (strpos($route, '/expenses') === 0) {
    // TODO: ExpenseController
    echo "Guide Expenses - Coming soon";
    exit;
}

// 404
http_response_code(404);
echo "404 - Guide route not found";
