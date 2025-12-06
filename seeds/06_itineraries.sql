-- ==============================================================================
-- SEED DATA: ITINERARIES
-- ==============================================================================
-- 
-- Phụ thuộc: tours (04_tours.sql), destinations (từ seed_location_services.sql)
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

SET @tour_dalat = (SELECT id FROM tours WHERE tour_code = 'TOUR-20241206-001' LIMIT 1);
SET @tour_nhatrang = (SELECT id FROM tours WHERE tour_code = 'TOUR-20241206-002' LIMIT 1);
SET @destination_ho_xuan_huong = (SELECT id FROM destinations WHERE name = 'Hồ Xuân Hương' LIMIT 1);
SET @destination_vinpearl = (SELECT id FROM destinations WHERE name = 'Vinpearl Land' LIMIT 1);

-- Tour Đà Lạt - 3 ngày 2 đêm
INSERT INTO `itineraries` (`tour_id`, `destination_id`, `day_number`, `title`, `description`, `meals`, `accommodation`, `arrival_time`, `departure_time`, `display_order`) VALUES
(@tour_dalat, @destination_ho_xuan_huong, 1, 'Ngày 1: Khởi hành - Tham quan Hồ Xuân Hương', 
'Khởi hành từ TP.HCM, đến Đà Lạt, tham quan Hồ Xuân Hương, Chợ Đà Lạt',
JSON_OBJECT('breakfast', 'Trên xe', 'lunch', 'Nhà hàng địa phương', 'dinner', 'Khách sạn'),
'Khách sạn Dalat Palace', '06:00:00', '22:00:00', 1),

(@tour_dalat, NULL, 2, 'Ngày 2: Thung lũng Tình Yêu - Dinh Bảo Đại',
'Tham quan Thung lũng Tình Yêu, Dinh Bảo Đại, mua sắm đặc sản',
JSON_OBJECT('breakfast', 'Khách sạn', 'lunch', 'Nhà hàng Gia Hân', 'dinner', 'Khách sạn'),
'Khách sạn Dalat Palace', '08:00:00', '20:00:00', 2),

(@tour_dalat, NULL, 3, 'Ngày 3: Tham quan - Về TP.HCM',
'Tham quan các điểm du lịch còn lại, mua sắm, về TP.HCM',
JSON_OBJECT('breakfast', 'Khách sạn', 'lunch', 'Nhà hàng địa phương', 'dinner', NULL),
NULL, '08:00:00', '16:00:00', 3);

-- Tour Nha Trang - 4 ngày 3 đêm
INSERT INTO `itineraries` (`tour_id`, `destination_id`, `day_number`, `title`, `description`, `meals`, `accommodation`, `arrival_time`, `departure_time`, `display_order`) VALUES
(@tour_nhatrang, @destination_vinpearl, 1, 'Ngày 1: Khởi hành - Đến Nha Trang',
'Khởi hành từ TP.HCM, đến Nha Trang, nhận phòng, nghỉ ngơi',
JSON_OBJECT('breakfast', 'Trên xe', 'lunch', 'Nhà hàng hải sản', 'dinner', 'Resort'),
'Resort Vinpearl Nha Trang', '06:00:00', '20:00:00', 1),

(@tour_nhatrang, @destination_vinpearl, 2, 'Ngày 2: Vinpearl Land',
'Tham quan Vinpearl Land, vui chơi công viên giải trí, tắm biển',
JSON_OBJECT('breakfast', 'Resort', 'lunch', 'Trong công viên', 'dinner', 'Resort'),
'Resort Vinpearl Nha Trang', '08:00:00', '22:00:00', 2),

(@tour_nhatrang, NULL, 3, 'Ngày 3: Tham quan - Tắm biển',
'Tham quan Tháp Bà Ponagar, tắm biển, thưởng thức hải sản',
JSON_OBJECT('breakfast', 'Resort', 'lunch', 'Nhà hàng hải sản', 'dinner', 'Resort'),
'Resort Vinpearl Nha Trang', '08:00:00', '20:00:00', 3),

(@tour_nhatrang, NULL, 4, 'Ngày 4: Mua sắm - Về TP.HCM',
'Mua sắm đặc sản, về TP.HCM',
JSON_OBJECT('breakfast', 'Resort', 'lunch', 'Nhà hàng địa phương', 'dinner', NULL),
NULL, '08:00:00', '18:00:00', 4);

