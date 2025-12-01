<?php
/**
 * ==============================================================================
 * TOUR MANAGEMENT SYSTEM - DATABASE CONNECTION
 * ==============================================================================
 * 
 * Tạo PDO connection đến MySQL database
 * - Sử dụng credentials từ .env
 * - Error handling graceful
 * - Return $pdo object để sử dụng trong toàn ứng dụng
 * 
 * Theo Vibe Coding: Simple is Best
 * 
 * @version 1.0
 * @date 2024-12-01
 * ==============================================================================
 */

try {
    // ========================================================================
    // GET DATABASE CONFIG FROM ENV
    // ========================================================================

    $db_host = defined('DB_HOST') ? DB_HOST : 'localhost';
    $db_port = defined('DB_PORT') ? DB_PORT : '3306';
    $db_name = defined('DB_NAME') ? DB_NAME : 'tour_management';
    $db_user = defined('DB_USER') ? DB_USER : 'root';
    $db_pass = defined('DB_PASS') ? DB_PASS : '';

    // ========================================================================
    // CREATE PDO CONNECTION
    // ========================================================================

    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Fetch associative arrays
        PDO::ATTR_EMULATE_PREPARES => false,                   // Use real prepared statements
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"      // Set charset
    ];

    $pdo = new PDO($dsn, $db_user, $db_pass, $options);

    // ========================================================================
    // SUCCESS MESSAGE (Development only)
    // ========================================================================

    if ((defined('APP_ENV') && APP_ENV === 'development') && isset($_GET['debug'])) {
        echo "✅ Database connected successfully!<br>";
        echo "📊 Database: {$db_name}<br>";
        echo "🖥️ Host: {$db_host}:{$db_port}<br>";
    }

} catch (PDOException $e) {
    // ========================================================================
    // ERROR HANDLING
    // ========================================================================

    // Log error
    $error_msg = "[" . date('Y-m-d H:i:s') . "] Database Connection Error: " . $e->getMessage() . "\n";

    // Create logs directory if not exists
    $log_dir = dirname(__FILE__) . '/logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    file_put_contents($log_dir . '/database-errors.log', $error_msg, FILE_APPEND);

    // User-friendly error message
    if ((defined('APP_ENV') && APP_ENV === 'development')) {
        // Development: Show detailed error
        die("
            <div style='font-family: monospace; padding: 20px; background: #fee; border-left: 4px solid #c00;'>
                <h2 style='color: #c00; margin: 0 0 10px 0;'>❌ Database Connection Failed</h2>
                <p><strong>Error:</strong> {$e->getMessage()}</p>
                <p><strong>Host:</strong> {$db_host}:{$db_port}</p>
                <p><strong>Database:</strong> {$db_name}</p>
                <p><strong>User:</strong> {$db_user}</p>
                <hr>
                <p style='font-size: 12px; color: #666;'>
                    💡 <strong>Suggestions:</strong><br>
                    1. Check if MySQL is running<br>
                    2. Verify database credentials in .env file<br>
                    3. Ensure database '{$db_name}' exists<br>
                    4. Check MySQL user permissions
                </p>
            </div>
        ");
    } else {
        // Production: Generic error
        die("
            <div style='font-family: sans-serif; padding: 20px; text-align: center;'>
                <h2>⚠️ Service Temporarily Unavailable</h2>
                <p>We're experiencing technical difficulties. Please try again later.</p>
                <p style='font-size: 12px; color: #999;'>Error ID: " . uniqid() . "</p>
            </div>
        ");
    }
}

// ============================================================================
// END OF DATABASE CONNECTION
// ============================================================================
