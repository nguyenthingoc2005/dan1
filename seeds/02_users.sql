-- ==============================================================================
-- SEED DATA: USERS
-- ==============================================================================
-- 
-- Phụ thuộc: roles (01_roles.sql)
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

SET @role_admin = (SELECT id FROM roles WHERE name = 'admin' LIMIT 1);
SET @role_staff = (SELECT id FROM roles WHERE name = 'staff' LIMIT 1);
SET @role_guide = (SELECT id FROM roles WHERE name = 'guide' LIMIT 1);

-- Password: password123 (hashed với bcrypt hoặc md5 tùy hệ thống)
-- Ở đây dùng md5 cho đơn giản, trong production nên dùng bcrypt
INSERT INTO `users` (`role_id`, `email`, `password`, `full_name`, `phone`, `date_of_birth`, `gender`, `address`, `status`) VALUES
(@role_admin, 'admin@tour.com', MD5('password123'), 'Nguyễn Văn Admin', '0901234567', '1980-01-15', 'male', '123 Đường ABC, Quận 1, TP.HCM', 'active'),
(@role_staff, 'staff1@tour.com', MD5('password123'), 'Trần Thị Nhân viên', '0901234568', '1990-05-20', 'female', '456 Đường XYZ, Quận 2, TP.HCM', 'active'),
(@role_staff, 'staff2@tour.com', MD5('password123'), 'Lê Văn Nhân viên', '0901234569', '1992-08-10', 'male', '789 Đường DEF, Quận 3, TP.HCM', 'active'),
(@role_guide, 'guide1@tour.com', MD5('password123'), 'Phạm Thị Hướng dẫn', '0901234570', '1988-03-25', 'female', '321 Đường GHI, Quận 4, TP.HCM', 'active'),
(@role_guide, 'guide2@tour.com', MD5('password123'), 'Hoàng Văn Hướng dẫn', '0901234571', '1985-11-30', 'male', '654 Đường JKL, Quận 5, TP.HCM', 'active')
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

