-- ==============================================================================
-- MIGRATION: Cập nhật database cho tính năng Guide (Simple Version)
-- ==============================================================================
-- Mục đích: 
-- 1. Thêm tour_schedule_id vào incurred_expenses
-- 2. Đảm bảo journals có tour_schedule_id (nếu chưa có)
-- 3. Cập nhật dữ liệu hiện có
-- Ngày: 2024-12-XX
-- 
-- HƯỚNG DẪN:
-- 1. Chạy từng phần một, kiểm tra lỗi
-- 2. Nếu cột đã tồn tại, bỏ qua phần ALTER TABLE tương ứng
-- 3. Chạy phần UPDATE dữ liệu sau khi đã thêm cột
-- ==============================================================================

-- ==============================================================================
-- PHẦN 1: INCURRED_EXPENSES - Thêm tour_schedule_id
-- ==============================================================================

-- Bước 1.1: Thêm cột tour_schedule_id (chạy nếu chưa có)
ALTER TABLE `incurred_expenses`
ADD COLUMN `tour_schedule_id` INT NULL COMMENT 'Link với tour schedule' AFTER `booking_id`;

-- Bước 1.2: Thêm index (chạy nếu chưa có)
ALTER TABLE `incurred_expenses`
ADD INDEX `idx_tour_schedule_id` (`tour_schedule_id`);

-- Bước 1.3: Thêm foreign key (chạy nếu chưa có)
-- Lưu ý: Nếu có lỗi về foreign key đã tồn tại, bỏ qua bước này
ALTER TABLE `incurred_expenses`
ADD CONSTRAINT `incurred_expenses_ibfk_tour_schedule` 
FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE;

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

-- Bước 3.1: Thêm cột tour_schedule_id (chạy nếu chưa có)
ALTER TABLE `journals`
ADD COLUMN `tour_schedule_id` INT NULL COMMENT 'Foreign key → tour_schedules (journal theo tour)' AFTER `id`;

-- Bước 3.2: Thêm index (chạy nếu chưa có)
ALTER TABLE `journals`
ADD INDEX `idx_journals_tour_schedule_id` (`tour_schedule_id`);

-- Bước 3.3: Thêm foreign key (chạy nếu chưa có)
-- Lưu ý: Nếu có lỗi về foreign key đã tồn tại, bỏ qua bước này
ALTER TABLE `journals`
ADD CONSTRAINT `journals_ibfk_schedule` 
FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE;

-- Bước 3.4: Cập nhật dữ liệu journals
-- Cập nhật: Nếu booking có tour_schedule_id, copy sang journals
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
-- PHẦN 4: KIỂM TRA KẾT QUẢ
-- ==============================================================================

-- Kiểm tra số lượng records đã được cập nhật
SELECT 
    'incurred_expenses' AS table_name,
    COUNT(*) AS total_records,
    COUNT(tour_schedule_id) AS records_with_schedule_id,
    COUNT(*) - COUNT(tour_schedule_id) AS records_without_schedule_id
FROM incurred_expenses
UNION ALL
SELECT 
    'journals' AS table_name,
    COUNT(*) AS total_records,
    COUNT(tour_schedule_id) AS records_with_schedule_id,
    COUNT(*) - COUNT(tour_schedule_id) AS records_without_schedule_id
FROM journals;

-- ==============================================================================
-- KẾT THÚC MIGRATION
-- ==============================================================================

