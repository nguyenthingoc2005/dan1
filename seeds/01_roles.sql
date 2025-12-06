-- ==============================================================================
-- SEED DATA: ROLES
-- ==============================================================================
-- 
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

INSERT INTO `roles` (`name`, `display_name`, `description`) VALUES
('admin', 'Quản trị viên', 'Quản trị toàn bộ hệ thống'),
('staff', 'Nhân viên', 'Quản lý tours, bookings, customers'),
('guide', 'Hướng dẫn viên', 'Quản lý tour assignments và journals')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name);

