-- ==============================================================================
-- SEED DATA: SERVICE PROVIDER PAYMENTS
-- ==============================================================================
-- 
-- Phụ thuộc: 
--   - service_providers (từ seed_location_services.sql)
--   - bookings (10_bookings.sql), booking_services (12_booking_services.sql)
--   - users (02_users.sql)
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

-- Lấy các biến cần thiết
SET @booking1 = (SELECT id FROM bookings WHERE booking_code = 'BK-20241206-001' LIMIT 1);
SET @provider_dalat_palace = (SELECT id FROM service_providers WHERE service_code = 'SP-20241206-001' LIMIT 1);
SET @service_deluxe = (SELECT id FROM services WHERE name = 'Phòng Deluxe' AND service_provider_id = @provider_dalat_palace LIMIT 1);
SET @booking_service1 = (SELECT id FROM booking_services WHERE booking_id = @booking1 AND service_id = @service_deluxe LIMIT 1);
SET @user_staff1 = (SELECT id FROM users WHERE email = 'staff1@tour.com' LIMIT 1);

-- Chỉ INSERT nếu tất cả các biến không NULL
-- Kiểm tra @provider_dalat_palace (bắt buộc cho service_provider_id)
INSERT INTO `service_provider_payments` (`payment_code`, `service_provider_id`, `booking_id`, `amount`, `payment_method`, `payment_date`, `invoice_number`, `status`, `created_by`) 
SELECT 
    'SPP-20241206-001' AS payment_code,
    @provider_dalat_palace AS service_provider_id,
    @booking1 AS booking_id,
    3000000.00 AS amount,
    'bank_transfer' AS payment_method,
    '2024-12-10' AS payment_date,
    'INV-SP-001' AS invoice_number,
    'pending' AS status,
    @user_staff1 AS created_by
WHERE @provider_dalat_palace IS NOT NULL 
  AND @booking1 IS NOT NULL 
  AND @user_staff1 IS NOT NULL;

-- Chỉ INSERT detail nếu payment đã được tạo và booking_service1 không NULL
INSERT INTO `service_provider_payment_details` (`payment_id`, `booking_service_id`, `amount`, `notes`)
SELECT 
    (SELECT id FROM service_provider_payments WHERE payment_code = 'SPP-20241206-001' LIMIT 1) AS payment_id,
    @booking_service1 AS booking_service_id,
    3000000.00 AS amount,
    'Thanh toán phòng khách sạn' AS notes
WHERE @booking_service1 IS NOT NULL
  AND EXISTS (SELECT 1 FROM service_provider_payments WHERE payment_code = 'SPP-20241206-001' LIMIT 1);

