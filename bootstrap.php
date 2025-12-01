<?php
/**
 * ==============================================================================
 * TOUR MANAGEMENT SYSTEM - APPLICATION BOOTSTRAP
 * ==============================================================================
 * 
 * Khởi tạo ứng dụng:
 * - Load .env file và parse thành constants
 * - Define paths (APP_PATH, VIEWS_PATH, etc.)
 * - Set timezone, error reporting
 * - Load database connection
 * - Load helper files
 * 
 * Theo Vibe Coding: Simple is Best
 * 
 * @version 1.0
 * @date 2024-12-01
 * ==============================================================================
 */

// ============================================================================
// PREVENT MULTIPLE INCLUDES
// ============================================================================

if (defined('APP_INITIALIZED')) {
    return;
}
define('APP_INITIALIZED', true);

// ============================================================================
// LOAD ENVIRONMENT FILE (.env)
// ============================================================================

$env_file = __DIR__ . '/.env';
if (!file_exists($env_file)) {
    die('❌ ERROR: .env file not found! Please create .env file in project root.');
}

// Parse .env file và define constants
$env_lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($env_lines as $line) {
    // Skip comments và empty lines
    if (empty($line) || strpos(trim($line), '#') === 0) {
        continue;
    }

    // Parse KEY=VALUE
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        // Define constant nếu chưa có
        if (!defined($key)) {
            define($key, $value);
        }
    }
}

// ============================================================================
// DEFINE APPLICATION PATHS
// ============================================================================

define('APP_PATH', __DIR__);
define('VIEWS_PATH', APP_PATH . '/app/views');
define('CONTROLLERS_PATH', APP_PATH . '/app/controllers');
define('MODELS_PATH', APP_PATH . '/app/models');
define('COMMON_PATH', APP_PATH . '/common');
define('ROUTES_PATH', APP_PATH . '/routes');
define('PUBLIC_PATH', APP_PATH . '/public');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');

// ============================================================================
// APPLICATION CONFIGURATION
// ============================================================================

// Set timezone
date_default_timezone_set(TIMEZONE ?? 'Asia/Ho_Chi_Minh');

// Error reporting based on environment
if ((APP_ENV ?? 'development') === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);

    // Create logs directory if not exists
    $log_dir = APP_PATH . '/logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    ini_set('error_log', $log_dir . '/error.log');
}

// ============================================================================
// LOAD DATABASE CONNECTION
// ============================================================================

require_once APP_PATH . '/database.php';

// ============================================================================
// LOAD HELPER FILES
// ============================================================================

require_once COMMON_PATH . '/functions.php';
require_once COMMON_PATH . '/AuthHelper.php';

// ============================================================================
// END OF BOOTSTRAP
// ============================================================================

// Debug info (chỉ hiển thị trong development)
if ((APP_ENV ?? 'development') === 'development' && isset($_GET['debug'])) {
    echo "✅ Bootstrap loaded successfully!<br>";
    echo "📁 APP_PATH: " . APP_PATH . "<br>";
    echo "🗄️ Database: " . (isset($pdo) ? 'Connected' : 'Not connected') . "<br>";
    echo "🌍 Timezone: " . date_default_timezone_get() . "<br>";
    echo "🔧 Environment: " . (APP_ENV ?? 'not set') . "<br>";
}
