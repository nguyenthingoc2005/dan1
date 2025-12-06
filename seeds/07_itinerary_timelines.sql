-- ==============================================================================
-- SEED DATA: ITINERARY TIMELINES
-- ==============================================================================
-- 
-- Phụ thuộc: itineraries (06_itineraries.sql), destinations (từ seed_location_services.sql)
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

SET @tour_dalat = (SELECT id FROM tours WHERE tour_code = 'TOUR-20241206-001' LIMIT 1);
SET @destination_ho_xuan_huong = (SELECT id FROM destinations WHERE name = 'Hồ Xuân Hương' LIMIT 1);

SET @itinerary_dalat_day1 = (SELECT id FROM itineraries WHERE tour_id = @tour_dalat AND day_number = 1 LIMIT 1);
SET @itinerary_dalat_day2 = (SELECT id FROM itineraries WHERE tour_id = @tour_dalat AND day_number = 2 LIMIT 1);

-- Day 1 Timeline
INSERT INTO `itinerary_timelines` (`itinerary_id`, `timeline_time`, `activity_title`, `activity_description`, `location`, `destination_id`, `display_order`) VALUES
(@itinerary_dalat_day1, '06:00:00', 'Tập trung - Khởi hành', 'Tập trung tại điểm hẹn, khởi hành đi Đà Lạt', 'TP.HCM', NULL, 1),
(@itinerary_dalat_day1, '12:00:00', 'Nghỉ giữa đường', 'Nghỉ ăn trưa tại nhà hàng dọc đường', 'Nhà hàng dọc đường', NULL, 2),
(@itinerary_dalat_day1, '15:00:00', 'Đến Đà Lạt - Nhận phòng', 'Đến Đà Lạt, nhận phòng khách sạn, nghỉ ngơi', 'Khách sạn Dalat Palace', NULL, 3),
(@itinerary_dalat_day1, '16:00:00', 'Tham quan Hồ Xuân Hương', 'Tham quan Hồ Xuân Hương, chụp ảnh', 'Hồ Xuân Hương', @destination_ho_xuan_huong, 4),
(@itinerary_dalat_day1, '18:00:00', 'Tham quan Chợ Đà Lạt', 'Tham quan Chợ Đà Lạt, mua sắm đặc sản', 'Chợ Đà Lạt', NULL, 5),
(@itinerary_dalat_day1, '19:30:00', 'Ăn tối', 'Ăn tối tại nhà hàng địa phương', 'Nhà hàng Gia Hân', NULL, 6),
(@itinerary_dalat_day1, '21:00:00', 'Nghỉ đêm', 'Về khách sạn nghỉ đêm', 'Khách sạn Dalat Palace', NULL, 7);

-- Day 2 Timeline
INSERT INTO `itinerary_timelines` (`itinerary_id`, `timeline_time`, `activity_title`, `activity_description`, `location`, `destination_id`, `display_order`) VALUES
(@itinerary_dalat_day2, '08:00:00', 'Ăn sáng', 'Ăn sáng buffet tại khách sạn', 'Khách sạn Dalat Palace', NULL, 1),
(@itinerary_dalat_day2, '09:00:00', 'Tham quan Thung lũng Tình Yêu', 'Tham quan Thung lũng Tình Yêu, chụp ảnh', 'Thung lũng Tình Yêu', NULL, 2),
(@itinerary_dalat_day2, '12:00:00', 'Ăn trưa', 'Ăn trưa tại nhà hàng Gia Hân', 'Nhà hàng Gia Hân', NULL, 3),
(@itinerary_dalat_day2, '14:00:00', 'Tham quan Dinh Bảo Đại', 'Tham quan Dinh Bảo Đại, tìm hiểu lịch sử', 'Dinh Bảo Đại', NULL, 4),
(@itinerary_dalat_day2, '17:00:00', 'Mua sắm đặc sản', 'Mua sắm đặc sản Đà Lạt', 'Chợ Đà Lạt', NULL, 5),
(@itinerary_dalat_day2, '19:00:00', 'Ăn tối', 'Ăn tối tại khách sạn', 'Khách sạn Dalat Palace', NULL, 6);

