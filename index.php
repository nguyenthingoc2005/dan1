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
 * @version 2.0 - Refactored routing
 * @date 2024-12-02
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

// Lấy action từ query string: ?act=login, ?act=admin, etc.
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
// ADMIN ROUTES - Module Based (act=admin&module=X&action=Y)
// ============================================================================

if ($act === 'admin') {
    require __DIR__ . '/routes/admin.php';
    exit;
}

// ============================================================================
// STAFF ROUTES (act=staff-X)
// ============================================================================

if (strpos($act, 'staff-') === 0) {
    require __DIR__ . '/routes/staff.php';
    exit;
}

// ============================================================================
// GUIDE ROUTES (act=guide-X)
// ============================================================================

if (strpos($act, 'guide-') === 0) {
    require __DIR__ . '/routes/guide.php';
    exit;
}

// ============================================================================
// PROFILE ROUTES (act=profile/X) - All roles
// ============================================================================

if (strpos($act, 'profile') === 0) {
    require __DIR__ . '/routes/profile.php';
    exit;
}

// ============================================================================
// 404 NOT FOUND
// ============================================================================

http_response_code(404);
require VIEWS_PATH . '/errors/404.php';
exit;
