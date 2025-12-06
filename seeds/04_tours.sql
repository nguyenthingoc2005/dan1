-- ==============================================================================
-- SEED DATA: TOURS
-- ==============================================================================
-- 
-- Phụ thuộc: 
--   - users (02_users.sql)
--   - destinations (từ seed_location_services.sql)
--   - service_providers, services (từ seed_location_services.sql)
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

SET @user_admin = (SELECT id FROM users WHERE email = 'admin@tour.com' LIMIT 1);
SET @destination_ho_xuan_huong = (SELECT id FROM destinations WHERE name = 'Hồ Xuân Hương' LIMIT 1);
SET @destination_vinpearl = (SELECT id FROM destinations WHERE name = 'Vinpearl Land' LIMIT 1);
SET @destination_halong = (SELECT id FROM destinations WHERE name = 'Vịnh Hạ Long' LIMIT 1);

-- Service Providers và Services (cần có từ seed_location_services.sql)
SET @provider_dalat_palace = (SELECT id FROM service_providers WHERE service_code = 'SP-20241206-001' LIMIT 1);
SET @provider_gia_han = (SELECT id FROM service_providers WHERE service_code = 'SP-20241206-002' LIMIT 1);
SET @provider_transport_dl = (SELECT id FROM service_providers WHERE service_code = 'SP-20241206-003' LIMIT 1);
SET @provider_vinpearl = (SELECT id FROM service_providers WHERE service_code = 'SP-20241206-004' LIMIT 1);

SET @service_deluxe = (SELECT id FROM services WHERE name = 'Phòng Deluxe' AND service_provider_id = @provider_dalat_palace LIMIT 1);
SET @service_buffet = (SELECT id FROM services WHERE name = 'Buffet sáng' AND service_provider_id = @provider_dalat_palace LIMIT 1);
SET @service_set_menu = (SELECT id FROM services WHERE name = 'Set menu đặc sản Đà Lạt' AND service_provider_id = @provider_gia_han LIMIT 1);
SET @service_ocean_view = (SELECT id FROM services WHERE name = 'Phòng Ocean View' AND service_provider_id = @provider_vinpearl LIMIT 1);
SET @service_xe_16 = (SELECT id FROM services WHERE name = 'Xe 16 chỗ' AND service_provider_id = @provider_transport_dl LIMIT 1);

INSERT INTO `tours` (`tour_code`, `name`, `introduction`, `description`, `duration_days`, `duration_nights`, `departure_location`, `min_participants`, `max_participants`, `adult_price`, `child_price`, `infant_price`, `estimated_cost_per_person`, `deposit_percentage`, `fixed_cost_guide`, `fixed_cost_management`, `fixed_cost_marketing`, `fixed_cost_other`, `booking_deadline_days`, `tour_type`, `approval_status`, `approved_by`, `approved_at`, `status`, `created_by`) VALUES
('TOUR-20241206-001', 'Tour Đà Lạt 3N2Đ - Khám phá Thành phố Ngàn Hoa', 
'Khám phá Đà Lạt với tour 3 ngày 2 đêm, tham quan các điểm du lịch nổi tiếng như Hồ Xuân Hương, Chợ Đà Lạt, Thung lũng Tình Yêu.',
'<p>Tour Đà Lạt 3N2Đ là hành trình khám phá thành phố ngàn hoa với nhiều điểm đến hấp dẫn. Bạn sẽ được tham quan các địa điểm nổi tiếng, thưởng thức ẩm thực địa phương và trải nghiệm văn hóa đặc sắc của Đà Lạt.</p>',
3, 2, 'TP.HCM', 15, 30, 3500000.00, 2800000.00, 0.00, 2500000.00, 30.00, 500000.00, 300000.00, 200000.00, 100000.00, 1, 'public', 'approved', @user_admin, NOW(), 'active', @user_admin),

('TOUR-20241206-002', 'Tour Nha Trang 4N3Đ - Biển xanh cát trắng',
'Khám phá Nha Trang với tour 4 ngày 3 đêm, tắm biển, tham quan Vinpearl Land và các điểm du lịch biển.',
'<p>Tour Nha Trang 4N3Đ mang đến cho bạn trải nghiệm tuyệt vời với biển xanh, cát trắng. Tham quan Vinpearl Land, tắm biển, thưởng thức hải sản tươi sống.</p>',
4, 3, 'TP.HCM', 20, 45, 4500000.00, 3600000.00, 0.00, 3200000.00, 30.00, 600000.00, 400000.00, 250000.00, 150000.00, 2, 'public', 'approved', @user_admin, NOW(), 'active', @user_admin),

('TOUR-20241206-003', 'Tour Hạ Long 2N1Đ - Di sản Thế giới',
'Khám phá Vịnh Hạ Long - Di sản thiên nhiên thế giới với tour 2 ngày 1 đêm trên du thuyền.',
'<p>Tour Hạ Long 2N1Đ đưa bạn khám phá một trong 7 kỳ quan thiên nhiên thế giới. Ngắm cảnh đẹp trên du thuyền, tham quan hang động, tắm biển.</p>',
2, 1, 'Hà Nội', 10, 25, 2800000.00, 2200000.00, 0.00, 2000000.00, 30.00, 400000.00, 250000.00, 150000.00, 100000.00, 1, 'public', 'approved', @user_admin, NOW(), 'active', @user_admin);

