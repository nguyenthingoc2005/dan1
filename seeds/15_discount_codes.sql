-- ==============================================================================
-- SEED DATA: DISCOUNT CODES
-- ==============================================================================
-- 
-- Phụ thuộc: users (02_users.sql)
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

SET @user_admin = (SELECT id FROM users WHERE email = 'admin@tour.com' LIMIT 1);

INSERT INTO `discount_codes` (`code`, `name`, `discount_type`, `discount_value`, `min_purchase`, `start_date`, `end_date`, `usage_limit`, `used_count`, `status`, `created_by`) VALUES
('WELCOME10', 'Giảm 10% cho khách hàng mới', 'percentage', 10.00, 0.00, '2024-12-01', '2025-12-31', 100, 5, 'active', @user_admin),
('SUMMER2025', 'Giảm 500k cho tour mùa hè', 'fixed', 500000.00, 5000000.00, '2025-06-01', '2025-08-31', 50, 0, 'active', @user_admin),
('EARLYBIRD', 'Giảm 15% đặt sớm', 'percentage', 15.00, 10000000.00, '2024-12-01', '2025-03-31', 30, 2, 'active', @user_admin);

