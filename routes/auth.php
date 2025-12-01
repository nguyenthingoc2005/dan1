<?php
/**
 * ==============================================================================
 * AUTH ROUTES - Login/Logout Routing
 * ==============================================================================
 */

require_once CONTROLLERS_PATH . '/AuthController.php';

// Instantiate auth controller
$authController = new AuthController($pdo);

// Get current request URI
$request_uri = $_SERVER['REQUEST_URI'];
var_dump($request_url);
die();
$base_path = dirname($_SERVER['SCRIPT_NAME']);
if ($base_path !== '/') {
    $request_uri = substr($request_uri, strlen($base_path));
}
$request_uri = strtok($request_uri, '?');
$method = $_SERVER['REQUEST_METHOD'];

// Route: /login
if ($request_uri === '/login') {
    if ($method === 'GET') {
        $authController->showLogin();
    } elseif ($method === 'POST') {
        $authController->handleLogin();
    }
    exit;
}

// Route: /logout
if ($request_uri === '/logout') {
    $authController->logout();
    exit;
}

// Route: /register (optional)
if ($request_uri === '/register') {
    if ($method === 'GET') {
        $authController->showRegister();
    } elseif ($method === 'POST') {
        $authController->handleRegister();
    }
    exit;
}
