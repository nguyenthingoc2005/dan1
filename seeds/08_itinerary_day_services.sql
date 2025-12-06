-- ==============================================================================
-- SEED DATA: ITINERARY DAY SERVICES
-- ==============================================================================
-- 
-- Phụ thuộc: 
--   - itineraries (06_itineraries.sql)
--   - services, service_providers (từ seed_location_services.sql)
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

SET @tour_dalat = (SELECT id FROM tours WHERE tour_code = 'TOUR-20241206-001' LIMIT 1);
SET @provider_dalat_palace = (SELECT id FROM service_providers WHERE service_code = 'SP-20241206-001' LIMIT 1);
SET @provider_gia_han = (SELECT id FROM service_providers WHERE service_code = 'SP-20241206-002' LIMIT 1);
SET @service_deluxe = (SELECT id FROM services WHERE name = 'Phòng Deluxe' AND service_provider_id = @provider_dalat_palace LIMIT 1);
SET @service_buffet = (SELECT id FROM services WHERE name = 'Buffet sáng' AND service_provider_id = @provider_dalat_palace LIMIT 1);
SET @service_set_menu = (SELECT id FROM services WHERE name = 'Set menu đặc sản Đà Lạt' AND service_provider_id = @provider_gia_han LIMIT 1);

SET @itinerary_dalat_day1 = (SELECT id FROM itineraries WHERE tour_id = @tour_dalat AND day_number = 1 LIMIT 1);
SET @itinerary_dalat_day2 = (SELECT id FROM itineraries WHERE tour_id = @tour_dalat AND day_number = 2 LIMIT 1);

-- Day 1 Services
INSERT INTO `itinerary_day_services` (`itinerary_id`, `service_id`, `service_provider_id`, `service_name`, `unit_price`, `quantity`, `unit`, `is_included_in_price`, `notes`) VALUES
(@itinerary_dalat_day1, @service_deluxe, @provider_dalat_palace, 'Phòng Deluxe', 1500000.00, 1.00, 'phòng/đêm', 1, 'Phòng Deluxe view hồ'),
(@itinerary_dalat_day1, @service_buffet, @provider_dalat_palace, 'Buffet sáng', 200000.00, 1.00, 'suất', 1, 'Buffet sáng cho mỗi người'),
(@itinerary_dalat_day1, @service_set_menu, @provider_gia_han, 'Set menu đặc sản Đà Lạt', 350000.00, 1.00, 'suất', 1, 'Set menu tối cho mỗi người');

-- Day 2 Services
INSERT INTO `itinerary_day_services` (`itinerary_id`, `service_id`, `service_provider_id`, `service_name`, `unit_price`, `quantity`, `unit`, `is_included_in_price`, `notes`) VALUES
(@itinerary_dalat_day2, @service_deluxe, @provider_dalat_palace, 'Phòng Deluxe', 1500000.00, 1.00, 'phòng/đêm', 1, 'Phòng Deluxe view hồ'),
(@itinerary_dalat_day2, @service_buffet, @provider_dalat_palace, 'Buffet sáng', 200000.00, 1.00, 'suất', 1, 'Buffet sáng cho mỗi người'),
(@itinerary_dalat_day2, @service_set_menu, @provider_gia_han, 'Set menu đặc sản Đà Lạt', 350000.00, 1.00, 'suất', 1, 'Set menu trưa cho mỗi người');

