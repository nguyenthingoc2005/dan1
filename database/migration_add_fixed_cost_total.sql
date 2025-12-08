-- ==============================================================================
-- MIGRATION: Add fixed_cost_total column to tours table
-- ==============================================================================
-- 
-- Mục đích: Thêm cột fixed_cost_total để thay thế 4 cột cũ
-- (fixed_cost_guide, fixed_cost_management, fixed_cost_marketing, fixed_cost_other)
-- 
-- Ngày tạo: 2024-12-06
-- ==============================================================================

-- Kiểm tra và thêm cột fixed_cost_total nếu chưa có
SET @dbname = DATABASE();
SET @tablename = 'tours';
SET @columnname = 'fixed_cost_total';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  'SELECT "Column fixed_cost_total already exists" AS message;',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' DECIMAL(15,2) DEFAULT 0.00 COMMENT "Tổng chi phí cố định (nhập trực tiếp)";')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Kiểm tra và migrate dữ liệu từ 4 cột cũ (nếu có) sang fixed_cost_total
-- Chỉ migrate nếu các cột cũ tồn tại và fixed_cost_total = 0
SET @has_old_columns = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tours'
    AND COLUMN_NAME IN ('fixed_cost_guide', 'fixed_cost_management', 'fixed_cost_marketing', 'fixed_cost_other')
) >= 4;

SET @migrate_sql = IF(@has_old_columns = 1,
  'UPDATE `tours` 
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
     );',
  'SELECT "Old columns not found, skipping data migration" AS message;'
);

PREPARE migrate_stmt FROM @migrate_sql;
EXECUTE migrate_stmt;
DEALLOCATE PREPARE migrate_stmt;

-- Tùy chọn: Xóa 4 cột cũ nếu muốn (uncomment để chạy)
-- LƯU Ý: Chỉ xóa sau khi đã kiểm tra dữ liệu đã được migrate đúng
/*
ALTER TABLE `tours` 
  DROP COLUMN IF EXISTS `fixed_cost_guide`,
  DROP COLUMN IF EXISTS `fixed_cost_management`,
  DROP COLUMN IF EXISTS `fixed_cost_marketing`,
  DROP COLUMN IF EXISTS `fixed_cost_other`;
*/

SELECT "Migration completed successfully!" AS message;

