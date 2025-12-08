<?php
/**
 * ==============================================================================
 * MIGRATION SCRIPT: Create Room Assignment and Vehicle/Driver tables
 * ==============================================================================
 * 
 * Cách chạy:
 * 1. Mở trình duyệt và truy cập: http://localhost/duan1/run_migration_room_vehicle_tables.php
 * 2. Hoặc chạy từ command line: php run_migration_room_vehicle_tables.php
 * 
 * ==============================================================================
 */

// Load bootstrap để có database connection
require_once __DIR__ . '/bootstrap.php';

// $pdo đã được tạo trong database.php
if (!isset($pdo)) {
    die("❌ Không thể kết nối database. Vui lòng kiểm tra cấu hình.\n");
}

try {
    echo "🔄 Bắt đầu tạo các bảng Room Assignment và Vehicle/Driver...\n\n";

    // Đọc file SQL
    $sqlFile = __DIR__ . '/database/migration_create_room_vehicle_tables.sql';
    if (!file_exists($sqlFile)) {
        die("❌ Không tìm thấy file SQL: {$sqlFile}\n");
    }

    $sql = file_get_contents($sqlFile);

    // Tách các câu lệnh SQL (bỏ qua comments và empty lines)
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function ($stmt) {
            return !empty($stmt)
                && !preg_match('/^--/', $stmt)
                && !preg_match('/^\/\*/', $stmt)
                && strtoupper(trim($stmt)) !== 'SELECT';
        }
    );

    $successCount = 0;
    $skipCount = 0;
    $errorCount = 0;

    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement) || preg_match('/^--/', $statement)) {
            continue;
        }

        // Bỏ qua SELECT statements (như SELECT 'Migration completed...')
        if (preg_match('/^SELECT/i', $statement)) {
            continue;
        }

        try {
            $pdo->exec($statement);
            $successCount++;

            // Extract table name from CREATE TABLE statement
            if (preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches)) {
                echo "✅ Đã tạo bảng: {$matches[1]}\n";
            } elseif (preg_match('/ALTER TABLE.*?`?(\w+)`?/i', $statement, $matches)) {
                echo "✅ Đã cập nhật bảng: {$matches[1]}\n";
            }
        } catch (PDOException $e) {
            // Nếu bảng đã tồn tại, bỏ qua
            if (
                strpos($e->getMessage(), 'already exists') !== false ||
                strpos($e->getMessage(), 'Duplicate') !== false
            ) {
                $skipCount++;
                if (preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches)) {
                    echo "⏭️  Bảng {$matches[1]} đã tồn tại, bỏ qua...\n";
                }
            } else {
                $errorCount++;
                echo "❌ Lỗi: " . $e->getMessage() . "\n";
                echo "   Statement: " . substr($statement, 0, 100) . "...\n";
            }
        }
    }

    echo "\n" . str_repeat("=", 60) . "\n";
    echo "📊 KẾT QUẢ:\n";
    echo "   ✅ Thành công: {$successCount}\n";
    echo "   ⏭️  Đã tồn tại: {$skipCount}\n";
    echo "   ❌ Lỗi: {$errorCount}\n";
    echo str_repeat("=", 60) . "\n";

    if ($errorCount == 0) {
        echo "\n✅ Migration hoàn tất thành công!\n";
        echo "\nBây giờ bạn có thể sử dụng các tính năng phân phòng và quản lý xe/tài xế.\n";
    } else {
        echo "\n⚠️  Có một số lỗi xảy ra. Vui lòng kiểm tra lại.\n";
    }

} catch (Exception $e) {
    echo "\n❌ Lỗi: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

