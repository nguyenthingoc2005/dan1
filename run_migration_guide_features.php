<?php
/**
 * ==============================================================================
 * MIGRATION SCRIPT: Add tour_schedule_id to incurred_expenses and journals
 * ==============================================================================
 * 
 * Cách chạy:
 * 1. Mở trình duyệt và truy cập: http://localhost/duan1/run_migration_guide_features.php
 * 2. Hoặc chạy từ command line: php run_migration_guide_features.php
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
    echo "🔄 Bắt đầu migration cho tính năng Guide...\n\n";

    // ==========================================================================
    // PHẦN 1: INCURRED_EXPENSES - Thêm tour_schedule_id
    // ==========================================================================
    echo "📋 PHẦN 1: Cập nhật bảng incurred_expenses\n";
    echo str_repeat("-", 60) . "\n";

    // 1.1. Kiểm tra xem cột tour_schedule_id đã tồn tại chưa
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'incurred_expenses' 
          AND COLUMN_NAME = 'tour_schedule_id'
    ");
    $exists = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;

    if ($exists) {
        echo "✅ Cột tour_schedule_id đã tồn tại trong incurred_expenses.\n";
    } else {
        echo "➕ Đang thêm cột tour_schedule_id vào incurred_expenses...\n";
        $pdo->exec("
            ALTER TABLE `incurred_expenses`
            ADD COLUMN `tour_schedule_id` INT NULL COMMENT 'Link với tour schedule' AFTER `booking_id`
        ");
        echo "✅ Đã thêm cột tour_schedule_id thành công!\n";
    }

    // 1.2. Thêm index
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM INFORMATION_SCHEMA.STATISTICS 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'incurred_expenses'
          AND INDEX_NAME = 'idx_tour_schedule_id'
    ");
    $idxExists = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;

    if ($idxExists) {
        echo "✅ Index idx_tour_schedule_id đã tồn tại.\n";
    } else {
        echo "➕ Đang thêm index idx_tour_schedule_id...\n";
        $pdo->exec("
            ALTER TABLE `incurred_expenses`
            ADD INDEX `idx_tour_schedule_id` (`tour_schedule_id`)
        ");
        echo "✅ Đã thêm index thành công!\n";
    }

    // 1.3. Thêm foreign key
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'incurred_expenses'
          AND CONSTRAINT_NAME = 'incurred_expenses_ibfk_tour_schedule'
    ");
    $fkExists = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;

    if ($fkExists) {
        echo "✅ Foreign key incurred_expenses_ibfk_tour_schedule đã tồn tại.\n";
    } else {
        echo "➕ Đang thêm foreign key...\n";
        try {
            $pdo->exec("
                ALTER TABLE `incurred_expenses`
                ADD CONSTRAINT `incurred_expenses_ibfk_tour_schedule` 
                FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE
            ");
            echo "✅ Đã thêm foreign key thành công!\n";
        } catch (PDOException $e) {
            echo "⚠️  Lỗi khi thêm foreign key: " . $e->getMessage() . "\n";
            echo "   (Có thể foreign key đã tồn tại với tên khác, bỏ qua...)\n";
        }
    }

    // 1.4. Cập nhật dữ liệu: Fill tour_schedule_id từ booking_id
    echo "\n📊 Đang cập nhật dữ liệu incurred_expenses...\n";
    
    // Cập nhật từ booking.tour_schedule_id
    $stmt = $pdo->prepare("
        UPDATE `incurred_expenses` ie
        INNER JOIN `bookings` b ON ie.booking_id = b.id
        SET ie.tour_schedule_id = b.tour_schedule_id
        WHERE ie.tour_schedule_id IS NULL 
          AND b.tour_schedule_id IS NOT NULL
          AND ie.booking_id IS NOT NULL
    ");
    $stmt->execute();
    $updated1 = $stmt->rowCount();
    echo "   ✅ Đã cập nhật {$updated1} record(s) từ booking.tour_schedule_id\n";

    // Cập nhật từ tour_id + start_date
    $stmt = $pdo->prepare("
        UPDATE `incurred_expenses` ie
        INNER JOIN `bookings` b ON ie.booking_id = b.id
        INNER JOIN `tour_schedules` ts ON (
            b.tour_id = ts.tour_id 
            AND b.start_date = ts.start_date
        )
        SET ie.tour_schedule_id = ts.id
        WHERE ie.tour_schedule_id IS NULL 
          AND b.tour_schedule_id IS NULL
          AND ie.booking_id IS NOT NULL
    ");
    $stmt->execute();
    $updated2 = $stmt->rowCount();
    echo "   ✅ Đã cập nhật {$updated2} record(s) từ tour_id + start_date\n";

    // ==========================================================================
    // PHẦN 2: JOURNALS - Thêm tour_schedule_id
    // ==========================================================================
    echo "\n📋 PHẦN 2: Cập nhật bảng journals\n";
    echo str_repeat("-", 60) . "\n";

    // 2.1. Kiểm tra xem cột tour_schedule_id đã tồn tại chưa
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'journals' 
          AND COLUMN_NAME = 'tour_schedule_id'
    ");
    $exists = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;

    if ($exists) {
        echo "✅ Cột tour_schedule_id đã tồn tại trong journals.\n";
    } else {
        echo "➕ Đang thêm cột tour_schedule_id vào journals...\n";
        $pdo->exec("
            ALTER TABLE `journals`
            ADD COLUMN `tour_schedule_id` INT NULL COMMENT 'Foreign key → tour_schedules (journal theo tour)' AFTER `id`
        ");
        echo "✅ Đã thêm cột tour_schedule_id thành công!\n";
    }

    // 2.2. Thêm index
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM INFORMATION_SCHEMA.STATISTICS 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'journals'
          AND INDEX_NAME = 'idx_journals_tour_schedule_id'
    ");
    $idxExists = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;

    if ($idxExists) {
        echo "✅ Index idx_journals_tour_schedule_id đã tồn tại.\n";
    } else {
        echo "➕ Đang thêm index idx_journals_tour_schedule_id...\n";
        $pdo->exec("
            ALTER TABLE `journals`
            ADD INDEX `idx_journals_tour_schedule_id` (`tour_schedule_id`)
        ");
        echo "✅ Đã thêm index thành công!\n";
    }

    // 2.3. Thêm foreign key
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'journals'
          AND CONSTRAINT_NAME = 'journals_ibfk_schedule'
    ");
    $fkExists = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;

    if ($fkExists) {
        echo "✅ Foreign key journals_ibfk_schedule đã tồn tại.\n";
    } else {
        echo "➕ Đang thêm foreign key...\n";
        try {
            $pdo->exec("
                ALTER TABLE `journals`
                ADD CONSTRAINT `journals_ibfk_schedule` 
                FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE
            ");
            echo "✅ Đã thêm foreign key thành công!\n";
        } catch (PDOException $e) {
            echo "⚠️  Lỗi khi thêm foreign key: " . $e->getMessage() . "\n";
            echo "   (Có thể foreign key đã tồn tại với tên khác, bỏ qua...)\n";
        }
    }

    // 2.4. Cập nhật dữ liệu: Fill tour_schedule_id từ booking_id
    echo "\n📊 Đang cập nhật dữ liệu journals...\n";
    
    // Cập nhật từ booking.tour_schedule_id
    $stmt = $pdo->prepare("
        UPDATE `journals` j
        INNER JOIN `bookings` b ON j.booking_id = b.id
        SET j.tour_schedule_id = b.tour_schedule_id
        WHERE j.tour_schedule_id IS NULL 
          AND b.tour_schedule_id IS NOT NULL
          AND j.booking_id IS NOT NULL
    ");
    $stmt->execute();
    $updated3 = $stmt->rowCount();
    echo "   ✅ Đã cập nhật {$updated3} record(s) từ booking.tour_schedule_id\n";

    // Cập nhật từ tour_id + start_date
    $stmt = $pdo->prepare("
        UPDATE `journals` j
        INNER JOIN `bookings` b ON j.booking_id = b.id
        INNER JOIN `tour_schedules` ts ON (
            b.tour_id = ts.tour_id 
            AND b.start_date = ts.start_date
        )
        SET j.tour_schedule_id = ts.id
        WHERE j.tour_schedule_id IS NULL 
          AND b.tour_schedule_id IS NULL
          AND j.booking_id IS NOT NULL
    ");
    $stmt->execute();
    $updated4 = $stmt->rowCount();
    echo "   ✅ Đã cập nhật {$updated4} record(s) từ tour_id + start_date\n";

    // ==========================================================================
    // PHẦN 3: KIỂM TRA KẾT QUẢ
    // ==========================================================================
    echo "\n📊 PHẦN 3: Kiểm tra kết quả\n";
    echo str_repeat("-", 60) . "\n";

    // Kiểm tra incurred_expenses
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) AS total,
            COUNT(tour_schedule_id) AS with_schedule_id,
            COUNT(*) - COUNT(tour_schedule_id) AS without_schedule_id
        FROM incurred_expenses
    ");
    $expensesStats = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "📋 incurred_expenses:\n";
    echo "   - Tổng số: {$expensesStats['total']}\n";
    echo "   - Có tour_schedule_id: {$expensesStats['with_schedule_id']}\n";
    echo "   - Chưa có tour_schedule_id: {$expensesStats['without_schedule_id']}\n";

    // Kiểm tra journals
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) AS total,
            COUNT(tour_schedule_id) AS with_schedule_id,
            COUNT(*) - COUNT(tour_schedule_id) AS without_schedule_id
        FROM journals
    ");
    $journalsStats = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\n📋 journals:\n";
    echo "   - Tổng số: {$journalsStats['total']}\n";
    echo "   - Có tour_schedule_id: {$journalsStats['with_schedule_id']}\n";
    echo "   - Chưa có tour_schedule_id: {$journalsStats['without_schedule_id']}\n";

    echo "\n✅ Migration hoàn tất thành công!\n";
    echo "\nBây giờ bạn có thể sử dụng tour_schedule_id trong incurred_expenses và journals.\n";

} catch (PDOException $e) {
    echo "\n❌ Lỗi: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

