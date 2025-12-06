-- ==============================================================================
-- SEED DATA: BOOKINGS
-- ==============================================================================
-- 
-- Phụ thuộc: 
--   - tours (04_tours.sql), tour_schedules (05_tour_schedules.sql)
--   - customers (03_customers.sql)
--   - users (02_users.sql)
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

SET @tour_dalat = (SELECT id FROM tours WHERE tour_code = 'TOUR-20241206-001' LIMIT 1);
SET @tour_nhatrang = (SELECT id FROM tours WHERE tour_code = 'TOUR-20241206-002' LIMIT 1);
SET @customer1 = (SELECT id FROM customers WHERE customer_code = 'CUS-20241206-001' LIMIT 1);
SET @customer2 = (SELECT id FROM customers WHERE customer_code = 'CUS-20241206-002' LIMIT 1);
SET @customer3 = (SELECT id FROM customers WHERE customer_code = 'CUS-20241206-003' LIMIT 1);
SET @schedule_dalat_1 = (SELECT id FROM tour_schedules WHERE tour_id = @tour_dalat AND start_date = '2024-12-20' LIMIT 1);
SET @schedule_nhatrang_1 = (SELECT id FROM tour_schedules WHERE tour_id = @tour_nhatrang AND start_date = '2024-12-25' LIMIT 1);
SET @user_admin = (SELECT id FROM users WHERE email = 'admin@tour.com' LIMIT 1);
SET @user_staff1 = (SELECT id FROM users WHERE email = 'staff1@tour.com' LIMIT 1);

INSERT INTO `bookings` (`booking_code`, `tour_id`, `tour_schedule_id`, `customer_id`, `adult_count`, `child_count`, `infant_count`, `start_date`, `end_date`, `quota`, `booked_seats`, `total_amount`, `discount_amount`, `final_amount`, `deposit_amount`, `paid_amount`, `remaining_amount`, `payment_status`, `approval_status`, `approved_by`, `approved_at`, `source`, `created_by`) VALUES
('BK-20241206-001', @tour_dalat, @schedule_dalat_1, @customer1, 2, 0, 0, '2024-12-20', '2024-12-22', 30, 2, 7000000.00, 0.00, 7000000.00, 2100000.00, 2100000.00, 4900000.00, 'partial', 'approved', @user_admin, NOW(), 'phone', @user_staff1),
('BK-20241206-002', @tour_nhatrang, @schedule_nhatrang_1, @customer2, 1, 1, 0, '2024-12-25', '2024-12-28', 45, 2, 8100000.00, 0.00, 8100000.00, 2430000.00, 2430000.00, 5670000.00, 'partial', 'approved', @user_admin, NOW(), 'email', @user_staff1),
('BK-20241206-003', @tour_dalat, @schedule_dalat_1, @customer3, 3, 0, 0, '2024-12-20', '2024-12-22', 30, 3, 10500000.00, 0.00, 10500000.00, 3150000.00, 0.00, 10500000.00, 'unpaid', 'pending', NULL, NULL, 'facebook', @user_staff1);

