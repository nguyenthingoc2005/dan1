-- ==============================================================================
-- SEED DATA: VEHICLES
-- ==============================================================================
-- 
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

INSERT INTO `vehicles` (`vehicle_code`, `vehicle_type`, `license_plate`, `capacity`, `status`, `notes`) VALUES
('VEH-20241206-001', 'bus_45', '51A-12345', 45, 'active', 'Xe 45 chỗ - Tình trạng tốt'),
('VEH-20241206-002', 'bus_29', '51B-67890', 29, 'active', 'Xe 29 chỗ - Mới'),
('VEH-20241206-003', 'bus_16', '51C-11111', 16, 'active', 'Xe 16 chỗ - Dùng cho tour nhỏ'),
('VEH-20241206-004', 'car_7', '51D-22222', 7, 'active', 'Xe 7 chỗ - VIP'),
('VEH-20241206-005', 'car_4', '51E-33333', 4, 'maintenance', 'Xe 4 chỗ - Đang bảo trì');

