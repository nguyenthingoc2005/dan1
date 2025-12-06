-- ==============================================================================
-- SEED DATA: BOOKING CUSTOMERS
-- ==============================================================================
-- 
-- Phụ thuộc: bookings (10_bookings.sql), customers (03_customers.sql)
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

SET @booking1 = (SELECT id FROM bookings WHERE booking_code = 'BK-20241206-001' LIMIT 1);
SET @booking2 = (SELECT id FROM bookings WHERE booking_code = 'BK-20241206-002' LIMIT 1);
SET @booking3 = (SELECT id FROM bookings WHERE booking_code = 'BK-20241206-003' LIMIT 1);
SET @customer1 = (SELECT id FROM customers WHERE customer_code = 'CUS-20241206-001' LIMIT 1);
SET @customer2 = (SELECT id FROM customers WHERE customer_code = 'CUS-20241206-002' LIMIT 1);
SET @customer3 = (SELECT id FROM customers WHERE customer_code = 'CUS-20241206-003' LIMIT 1);

INSERT INTO `booking_customers` (`booking_id`, `customer_id`, `age_type`, `is_primary`) VALUES
(@booking1, @customer1, 'adult', 1),
(@booking1, @customer1, 'adult', 0), -- Giả sử booking cho 2 người lớn cùng customer
(@booking2, @customer2, 'adult', 1),
(@booking2, @customer2, 'child', 0), -- Giả sử booking cho 1 người lớn + 1 trẻ em
(@booking3, @customer3, 'adult', 1),
(@booking3, @customer3, 'adult', 0),
(@booking3, @customer3, 'adult', 0); -- Giả sử booking cho 3 người lớn

