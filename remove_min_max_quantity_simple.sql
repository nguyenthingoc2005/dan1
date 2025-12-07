-- ==============================================================================
-- BỎ 2 CỘT min_quantity và max_quantity TỪ service_prices
-- ==============================================================================
-- 
-- File này dùng để chạy trực tiếp trên database hiện tại
-- 
-- Date: 2024-12-XX
-- ==============================================================================

USE `tour_managementss`;

-- Bỏ cột min_quantity
ALTER TABLE `service_prices` DROP COLUMN IF EXISTS `min_quantity`;

-- Bỏ cột max_quantity
ALTER TABLE `service_prices` DROP COLUMN IF EXISTS `max_quantity`;

-- Kiểm tra kết quả
SELECT 'Đã bỏ 2 cột min_quantity và max_quantity khỏi bảng service_prices' AS result;

