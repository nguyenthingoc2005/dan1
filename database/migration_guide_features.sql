-- ==============================================================================
-- MIGRATION: Cập nhật database cho tính năng Guide
-- ==============================================================================
-- Mục đích: 
-- 1. Thêm tour_schedule_id vào incurred_expenses
-- 2. Đảm bảo journals có tour_schedule_id (nếu chưa có)
-- 3. Cập nhật dữ liệu hiện có
-- Ngày: 2024-12-XX
-- ==============================================================================

-- ==============================================================================
-- PHẦN 1: INCURRED_EXPENSES - Thêm tour_schedule_id
-- ==============================================================================

-- Kiểm tra và thêm cột tour_schedule_id nếu chưa có
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'incurred_expenses'
      AND COLUMN_NAME = 'tour_schedule_id'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `incurred_expenses`
     ADD COLUMN `tour_schedule_id` INT NULL COMMENT ''Link với tour schedule'' AFTER `booking_id`',
    'SELECT ''Column tour_schedule_id already exists in incurred_expenses'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Thêm index nếu chưa có
SET @idx_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'incurred_expenses'
      AND INDEX_NAME = 'idx_tour_schedule_id'
);

SET @sql = IF(@idx_exists = 0,
    'ALTER TABLE `incurred_expenses`
     ADD INDEX `idx_tour_schedule_id` (`tour_schedule_id`)',
    'SELECT ''Index idx_tour_schedule_id already exists in incurred_expenses'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Thêm foreign key nếu chưa có
SET @fk_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'incurred_expenses'
      AND CONSTRAINT_NAME = 'incurred_expenses_ibfk_tour_schedule'
);

SET @sql = IF(@fk_exists = 0,
    'ALTER TABLE `incurred_expenses`
     ADD CONSTRAINT `incurred_expenses_ibfk_tour_schedule` 
     FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE',
    'SELECT ''Foreign key incurred_expenses_ibfk_tour_schedule already exists'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ==============================================================================
-- PHẦN 2: CẬP NHẬT DỮ LIỆU - Fill tour_schedule_id từ booking_id
-- ==============================================================================

-- Cập nhật: Nếu booking có tour_schedule_id, copy sang incurred_expenses
UPDATE `incurred_expenses` ie
INNER JOIN `bookings` b ON ie.booking_id = b.id
SET ie.tour_schedule_id = b.tour_schedule_id
WHERE ie.tour_schedule_id IS NULL 
  AND b.tour_schedule_id IS NOT NULL
  AND ie.booking_id IS NOT NULL;

-- Cập nhật: Nếu booking không có tour_schedule_id, tìm từ tour_id + start_date
UPDATE `incurred_expenses` ie
INNER JOIN `bookings` b ON ie.booking_id = b.id
INNER JOIN `tour_schedules` ts ON (
    b.tour_id = ts.tour_id 
    AND b.start_date = ts.start_date
)
SET ie.tour_schedule_id = ts.id
WHERE ie.tour_schedule_id IS NULL 
  AND b.tour_schedule_id IS NULL
  AND ie.booking_id IS NOT NULL;

-- ==============================================================================
-- PHẦN 3: JOURNALS - Kiểm tra và thêm tour_schedule_id nếu chưa có
-- ==============================================================================

-- Kiểm tra và thêm cột tour_schedule_id nếu chưa có
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'journals'
      AND COLUMN_NAME = 'tour_schedule_id'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `journals`
     ADD COLUMN `tour_schedule_id` INT NULL COMMENT ''Foreign key → tour_schedules (journal theo tour)'' AFTER `id`,
     ADD INDEX `idx_journals_tour_schedule_id` (`tour_schedule_id`),
     ADD CONSTRAINT `journals_ibfk_schedule` 
     FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE',
    'SELECT ''Column tour_schedule_id already exists in journals'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Cập nhật dữ liệu journals: Fill tour_schedule_id từ booking_id
UPDATE `journals` j
INNER JOIN `bookings` b ON j.booking_id = b.id
SET j.tour_schedule_id = b.tour_schedule_id
WHERE j.tour_schedule_id IS NULL 
  AND b.tour_schedule_id IS NOT NULL
  AND j.booking_id IS NOT NULL;

-- Cập nhật: Nếu booking không có tour_schedule_id, tìm từ tour_id + start_date
UPDATE `journals` j
INNER JOIN `bookings` b ON j.booking_id = b.id
INNER JOIN `tour_schedules` ts ON (
    b.tour_id = ts.tour_id 
    AND b.start_date = ts.start_date
)
SET j.tour_schedule_id = ts.id
WHERE j.tour_schedule_id IS NULL 
  AND b.tour_schedule_id IS NULL
  AND j.booking_id IS NOT NULL;

-- ==============================================================================
-- PHẦN 4: ĐẢM BẢO tour_schedule_id NOT NULL cho journals (nếu cần)
-- ==============================================================================
-- Lưu ý: Chỉ chạy sau khi đã fill hết dữ liệu
-- Nếu muốn tour_schedule_id là NOT NULL, uncomment phần dưới:

-- ALTER TABLE `journals`
-- MODIFY COLUMN `tour_schedule_id` INT NOT NULL COMMENT 'Foreign key → tour_schedules (journal theo tour)';

-- ==============================================================================
-- KẾT THÚC MIGRATION
-- ==============================================================================

SELECT 'Migration completed successfully!' AS status;

