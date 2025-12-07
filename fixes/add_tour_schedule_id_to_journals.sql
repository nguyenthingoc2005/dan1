-- ==============================================================================
-- MIGRATION: Thêm tour_schedule_id vào bảng journals
-- ==============================================================================
-- 
-- Journal nên link với tour_schedule_id (theo tour) thay vì chỉ booking_id
-- Giữ booking_id để backward compatible
-- 
-- Date: 2024-12-XX
-- ==============================================================================

-- 1. Thêm cột tour_schedule_id (cho phép NULL tạm thời)
ALTER TABLE `journals` 
ADD COLUMN `tour_schedule_id` int DEFAULT NULL COMMENT 'Foreign key → tour_schedules (journal theo tour)' AFTER `id`;

-- 2. Cập nhật dữ liệu hiện có: Lấy tour_schedule_id từ booking
UPDATE `journals` j
INNER JOIN `bookings` b ON j.booking_id = b.id
INNER JOIN `tour_schedules` ts ON (b.tour_id = ts.tour_id AND b.start_date = ts.start_date)
SET j.tour_schedule_id = ts.id
WHERE j.tour_schedule_id IS NULL;

-- 3. Thêm foreign key constraint
ALTER TABLE `journals`
ADD CONSTRAINT `journals_ibfk_schedule` 
FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE;

-- 4. Thêm index
ALTER TABLE `journals`
ADD KEY `idx_journals_tour_schedule_id` (`tour_schedule_id`);

-- 5. Thay đổi booking_id thành có thể NULL (backward compatible)
ALTER TABLE `journals`
MODIFY COLUMN `booking_id` int DEFAULT NULL COMMENT 'Giữ lại để backward compatible, có thể NULL';

-- 6. Cập nhật foreign key constraint cho booking_id (cho phép NULL)
ALTER TABLE `journals`
DROP FOREIGN KEY `journals_ibfk_1`;

ALTER TABLE `journals`
ADD CONSTRAINT `journals_ibfk_1` 
FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL;

-- 7. Thay đổi tour_schedule_id thành NOT NULL (sau khi đã có dữ liệu)
ALTER TABLE `journals`
MODIFY COLUMN `tour_schedule_id` int NOT NULL COMMENT 'Foreign key → tour_schedules (journal theo tour)';

