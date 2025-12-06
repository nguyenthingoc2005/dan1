-- ==============================================================================
-- SEED DATA: CUSTOMERS
-- ==============================================================================
-- 
-- Phụ thuộc: users (02_users.sql)
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

SET @user_staff1 = (SELECT id FROM users WHERE email = 'staff1@tour.com' LIMIT 1);

INSERT INTO `customers` (`customer_code`, `full_name`, `email`, `phone`, `date_of_birth`, `gender`, `id_card`, `passport`, `nationality`, `address`, `customer_type`, `source`, `status`, `created_by`) VALUES
('CUS-20241206-001', 'Nguyễn Văn Khách', 'khach1@email.com', '0912345678', '1985-06-15', 'male', '001234567890', NULL, 'Vietnam', '123 Nguyễn Huệ, Q1, TP.HCM', 'individual', 'phone', 'active', @user_staff1),
('CUS-20241206-002', 'Trần Thị Khách', 'khach2@email.com', '0912345679', '1990-08-20', 'female', '001234567891', NULL, 'Vietnam', '456 Lê Lợi, Q1, TP.HCM', 'individual', 'email', 'active', @user_staff1),
('CUS-20241206-003', 'Lê Văn Nhóm', 'nhom1@email.com', '0912345680', '1980-03-10', 'male', NULL, 'P123456789', 'Vietnam', '789 Điện Biên Phủ, Q.Bình Thạnh, TP.HCM', 'group', 'facebook', 'active', @user_staff1),
('CUS-20241206-004', 'Phạm Thị Doanh nghiệp', 'corp1@email.com', '0912345681', '1975-12-25', 'female', '001234567892', NULL, 'Vietnam', '321 Võ Văn Tần, Q3, TP.HCM', 'corporate', 'walk_in', 'active', @user_staff1),
('CUS-20241206-005', 'Hoàng Văn Khách', 'khach3@email.com', '0912345682', '1995-04-05', 'male', '001234567893', NULL, 'Vietnam', '654 Nguyễn Đình Chiểu, Q3, TP.HCM', 'individual', 'zalo', 'active', @user_staff1);

