-- ==============================================================================
-- MIGRATION: Thêm tour_schedule_id vào bảng incurred_expenses
-- ==============================================================================
-- Mục đích: Hỗ trợ liên kết chi phí phát sinh trực tiếp với tour schedule
-- Ngày: 2024-12-XX
-- ==============================================================================

-- Bước 1: Thêm cột tour_schedule_id vào bảng incurred_expenses
ALTER TABLE `incurred_expenses`
ADD COLUMN `tour_schedule_id` INT NULL COMMENT 'Link với tour schedule' AFTER `booking_id`;

-- Bước 2: Thêm index cho cột mới
ALTER TABLE `incurred_expenses`
ADD INDEX `idx_tour_schedule_id` (`tour_schedule_id`);

-- Bước 3: Thêm foreign key constraint
ALTER TABLE `incurred_expenses`
ADD CONSTRAINT `incurred_expenses_ibfk_tour_schedule` 
FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE;

-- Bước 4: Cập nhật dữ liệu hiện có (fill tour_schedule_id từ booking_id)
-- Nếu booking có tour_schedule_id, copy sang incurred_expenses
UPDATE `incurred_expenses` ie
INNER JOIN `bookings` b ON ie.booking_id = b.id
SET ie.tour_schedule_id = b.tour_schedule_id
WHERE ie.tour_schedule_id IS NULL 
  AND b.tour_schedule_id IS NOT NULL;

-- Nếu booking không có tour_schedule_id, tìm từ tour_id + start_date
UPDATE `incurred_expenses` ie
INNER JOIN `bookings` b ON ie.booking_id = b.id
INNER JOIN `tour_schedules` ts ON (
    b.tour_id = ts.tour_id 
    AND b.start_date = ts.start_date
)
SET ie.tour_schedule_id = ts.id
WHERE ie.tour_schedule_id IS NULL 
  AND b.tour_schedule_id IS NULL;

-- ==============================================================================
-- KẾT THÚC MIGRATION
-- ==============================================================================

