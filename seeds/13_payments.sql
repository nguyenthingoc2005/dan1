-- ==============================================================================
-- SEED DATA: PAYMENTS
-- ==============================================================================
-- 
-- Phụ thuộc: bookings (10_bookings.sql), users (02_users.sql)
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

SET @booking1 = (SELECT id FROM bookings WHERE booking_code = 'BK-20241206-001' LIMIT 1);
SET @booking2 = (SELECT id FROM bookings WHERE booking_code = 'BK-20241206-002' LIMIT 1);
SET @user_staff1 = (SELECT id FROM users WHERE email = 'staff1@tour.com' LIMIT 1);

INSERT INTO `payments` (`booking_id`, `amount`, `payment_method`, `payment_type`, `payment_date`, `transaction_id`, `notes`, `status`, `created_by`) VALUES
(@booking1, 2100000.00, 'bank_transfer', 'deposit', '2024-12-06', 'TXN123456789', 'Thanh toán đặt cọc booking BK-20241206-001', 'completed', @user_staff1),
(@booking2, 2430000.00, 'cash', 'deposit', '2024-12-06', NULL, 'Thanh toán đặt cọc booking BK-20241206-002', 'completed', @user_staff1);

