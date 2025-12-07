-- ==============================================================================
-- REMOVE min_quantity và max_quantity từ service_prices
-- ==============================================================================
-- 
-- Mục đích: Bỏ 2 cột min_quantity và max_quantity khỏi bảng service_prices
-- vì không sử dụng trong hệ thống
-- 
-- Date: 2024-12-XX
-- ==============================================================================

USE `tour_managementss`;

-- Kiểm tra và xóa cột min_quantity nếu tồn tại
SET @dbname = DATABASE();
SET @tablename = 'service_prices';
SET @columnname = 'min_quantity';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  CONCAT('ALTER TABLE `', @tablename, '` DROP COLUMN `', @columnname, '`;'),
  'SELECT "Column min_quantity does not exist, skipping..." AS message;'
));
PREPARE alterIfExists FROM @preparedStatement;
EXECUTE alterIfExists;
DEALLOCATE PREPARE alterIfExists;

-- Kiểm tra và xóa cột max_quantity nếu tồn tại
SET @columnname = 'max_quantity';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  CONCAT('ALTER TABLE `', @tablename, '` DROP COLUMN `', @columnname, '`;'),
  'SELECT "Column max_quantity does not exist, skipping..." AS message;'
));
PREPARE alterIfExists FROM @preparedStatement;
EXECUTE alterIfExists;
DEALLOCATE PREPARE alterIfExists;

-- Hoặc cách đơn giản hơn (nếu chắc chắn cột tồn tại):
-- ALTER TABLE `service_prices` DROP COLUMN `min_quantity`;
-- ALTER TABLE `service_prices` DROP COLUMN `max_quantity`;

SELECT 'Migration completed: Removed min_quantity and max_quantity from service_prices' AS result;

