-- ==============================================================================
-- SEED DATA: TOUR SERVICES
-- ==============================================================================
-- 
-- Phụ thuộc: tours (04_tours.sql), services (từ seed_location_services.sql)
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

SET @tour_dalat = (SELECT id FROM tours WHERE tour_code = 'TOUR-20241206-001' LIMIT 1);
SET @provider_dalat_palace = (SELECT id FROM service_providers WHERE service_code = 'SP-20241206-001' LIMIT 1);
SET @provider_transport_dl = (SELECT id FROM service_providers WHERE service_code = 'SP-20241206-003' LIMIT 1);
SET @service_deluxe = (SELECT id FROM services WHERE name = 'Phòng Deluxe' AND service_provider_id = @provider_dalat_palace LIMIT 1);
SET @service_xe_16 = (SELECT id FROM services WHERE name = 'Xe 16 chỗ' AND service_provider_id = @provider_transport_dl LIMIT 1);

INSERT INTO `tour_services` (`tour_id`, `service_id`, `service_name`, `calculation_type`, `fixed_quantity`, `unit_price`, `unit`, `is_included_in_price`, `notes`) VALUES
(@tour_dalat, @service_xe_16, 'Xe 16 chỗ', 'per_day', 1, 2000000.00, 'xe/ngày', 1, 'Xe 16 chỗ cho tour'),
(@tour_dalat, @service_deluxe, 'Phòng Deluxe', 'per_person', 1, 1500000.00, 'phòng/đêm', 1, 'Phòng khách sạn Deluxe');

