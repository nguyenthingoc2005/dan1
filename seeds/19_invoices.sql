-- ==============================================================================
-- SEED DATA: INVOICES
-- ==============================================================================
-- 
-- Phụ thuộc: bookings (10_bookings.sql), users (02_users.sql)
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

SET @booking1 = (SELECT id FROM bookings WHERE booking_code = 'BK-20241206-001' LIMIT 1);
SET @booking2 = (SELECT id FROM bookings WHERE booking_code = 'BK-20241206-002' LIMIT 1);
SET @user_staff1 = (SELECT id FROM users WHERE email = 'staff1@tour.com' LIMIT 1);

INSERT INTO `invoices` (`invoice_number`, `booking_id`, `invoice_date`, `subtotal`, `tax_amount`, `discount_amount`, `total_amount`, `status`, `created_by`) VALUES
('INV-20241206-001', @booking1, '2024-12-06', 7000000.00, 0.00, 0.00, 7000000.00, 'issued', @user_staff1),
('INV-20241206-002', @booking2, '2024-12-06', 8100000.00, 0.00, 0.00, 8100000.00, 'issued', @user_staff1);

