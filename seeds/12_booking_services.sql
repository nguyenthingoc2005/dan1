-- ==============================================================================
-- SEED DATA: BOOKING SERVICES
-- ==============================================================================
-- 
-- Phụ thuộc: 
--   - bookings (10_bookings.sql)
--   - services, service_providers (từ seed_location_services.sql)
--   - users (02_users.sql)
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

SET @booking1 = (SELECT id FROM bookings WHERE booking_code = 'BK-20241206-001' LIMIT 1);
SET @booking2 = (SELECT id FROM bookings WHERE booking_code = 'BK-20241206-002' LIMIT 1);
SET @provider_dalat_palace = (SELECT id FROM service_providers WHERE service_code = 'SP-20241206-001' LIMIT 1);
SET @provider_vinpearl = (SELECT id FROM service_providers WHERE service_code = 'SP-20241206-004' LIMIT 1);
SET @service_deluxe = (SELECT id FROM services WHERE name = 'Phòng Deluxe' AND service_provider_id = @provider_dalat_palace LIMIT 1);
SET @service_buffet = (SELECT id FROM services WHERE name = 'Buffet sáng' AND service_provider_id = @provider_dalat_palace LIMIT 1);
SET @service_ocean_view = (SELECT id FROM services WHERE name = 'Phòng Ocean View' AND service_provider_id = @provider_vinpearl LIMIT 1);
SET @user_staff1 = (SELECT id FROM users WHERE email = 'staff1@tour.com' LIMIT 1);

INSERT INTO `booking_services` (`booking_id`, `service_id`, `service_provider_id`, `service_name`, `quantity`, `unit`, `unit_price`, `total_price`, `service_date`, `from_date`, `to_date`, `payment_status`, `paid_amount`, `created_by`) VALUES
(@booking1, @service_deluxe, @provider_dalat_palace, 'Phòng Deluxe', 1, 'phòng/đêm', 1500000.00, 3000000.00, NULL, '2024-12-20', '2024-12-22', 'pending', 0.00, @user_staff1),
(@booking1, @service_buffet, @provider_dalat_palace, 'Buffet sáng', 2, 'suất', 200000.00, 400000.00, '2024-12-21', NULL, NULL, 'pending', 0.00, @user_staff1),
(@booking2, @service_ocean_view, @provider_vinpearl, 'Phòng Ocean View', 1, 'phòng/đêm', 2500000.00, 7500000.00, NULL, '2024-12-25', '2024-12-28', 'pending', 0.00, @user_staff1);

