<?php
/**
 * ==============================================================================
 * MIGRATION SCRIPT: Add fixed_cost_total column to tours table
 * ==============================================================================
 * 
 * Cách chạy:
 * 1. Mở trình duyệt và truy cập: http://localhost/duan1/run_migration_fixed_cost.php
 * 2. Hoặc chạy từ command line: php run_migration_fixed_cost.php
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

    echo "🔄 Bắt đầu migration...\n\n";

    // 1. Kiểm tra xem cột fixed_cost_total đã tồn tại chưa
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'tours' 
          AND COLUMN_NAME = 'fixed_cost_total'
    ");
    $exists = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;

    if ($exists) {
        echo "✅ Cột fixed_cost_total đã tồn tại.\n";
    } else {
        echo "➕ Đang thêm cột fixed_cost_total...\n";
        $pdo->exec("
            ALTER TABLE `tours` 
            ADD COLUMN `fixed_cost_total` DECIMAL(15,2) DEFAULT 0.00 
            COMMENT 'Tổng chi phí cố định (nhập trực tiếp)'
        ");
        echo "✅ Đã thêm cột fixed_cost_total thành công!\n";
    }

    // 2. Kiểm tra xem có 4 cột cũ không
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'tours' 
          AND COLUMN_NAME IN ('fixed_cost_guide', 'fixed_cost_management', 'fixed_cost_marketing', 'fixed_cost_other')
    ");
    $oldColumnsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    if ($oldColumnsCount >= 4) {
        echo "\n📦 Phát hiện 4 cột cũ. Đang migrate dữ liệu...\n";

        // Migrate dữ liệu từ 4 cột cũ sang fixed_cost_total
        $stmt = $pdo->prepare("
            UPDATE `tours` 
            SET `fixed_cost_total` = COALESCE(`fixed_cost_guide`, 0) + 
                                     COALESCE(`fixed_cost_management`, 0) + 
                                     COALESCE(`fixed_cost_marketing`, 0) + 
                                     COALESCE(`fixed_cost_other`, 0)
            WHERE `fixed_cost_total` = 0 
              AND (
                COALESCE(`fixed_cost_guide`, 0) > 0 OR
                COALESCE(`fixed_cost_management`, 0) > 0 OR
                COALESCE(`fixed_cost_marketing`, 0) > 0 OR
                COALESCE(`fixed_cost_other`, 0) > 0
              )
        ");
        $stmt->execute();
        $affected = $stmt->rowCount();
        echo "✅ Đã migrate dữ liệu cho {$affected} tour(s).\n";

        echo "\n⚠️  LƯU Ý: Bạn có muốn xóa 4 cột cũ không?\n";
        echo "   Nếu muốn xóa, hãy uncomment phần code bên dưới trong file này.\n";

        // Uncomment dòng dưới để xóa 4 cột cũ (SAU KHI ĐÃ KIỂM TRA DỮ LIỆU)
        /*
        echo "\n🗑️  Đang xóa 4 cột cũ...\n";
        $pdo->exec("
            ALTER TABLE `tours` 
            DROP COLUMN IF EXISTS `fixed_cost_guide`,
            DROP COLUMN IF EXISTS `fixed_cost_management`,
            DROP COLUMN IF EXISTS `fixed_cost_marketing`,
            DROP COLUMN IF EXISTS `fixed_cost_other`
        ");
        echo "✅ Đã xóa 4 cột cũ thành công!\n";
        */
    } else {
        echo "\nℹ️  Không tìm thấy 4 cột cũ, bỏ qua bước migrate dữ liệu.\n";
    }

    echo "\n✅ Migration hoàn tất thành công!\n";
    echo "\nBây giờ bạn có thể sử dụng cột fixed_cost_total trong ứng dụng.\n";

} catch (PDOException $e) {
    echo "\n❌ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}

