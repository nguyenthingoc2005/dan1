-- ==============================================================================
-- SEED DATA: TOUR SCHEDULES
-- ==============================================================================
-- 
-- Phụ thuộc: tours (04_tours.sql), users (02_users.sql)
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

SET @tour_dalat = (SELECT id FROM tours WHERE tour_code = 'TOUR-20241206-001' LIMIT 1);
SET @tour_nhatrang = (SELECT id FROM tours WHERE tour_code = 'TOUR-20241206-002' LIMIT 1);
SET @tour_halong = (SELECT id FROM tours WHERE tour_code = 'TOUR-20241206-003' LIMIT 1);
SET @guide1 = (SELECT id FROM users WHERE email = 'guide1@tour.com' LIMIT 1);
SET @guide2 = (SELECT id FROM users WHERE email = 'guide2@tour.com' LIMIT 1);

INSERT INTO `tour_schedules` (`tour_id`, `start_date`, `end_date`, `quota`, `booked`, `adult_price`, `child_price`, `infant_price`, `status`, `guide_id`, `guide_notes`) VALUES
-- Tour Đà Lạt
(@tour_dalat, '2024-12-20', '2024-12-22', 30, 0, 3500000.00, 2800000.00, 0.00, 'open', @guide1, 'HDV có kinh nghiệm về Đà Lạt'),
(@tour_dalat, '2024-12-27', '2024-12-29', 30, 5, 3800000.00, 3000000.00, 0.00, 'open', @guide1, 'Mùa cao điểm - Giá tăng'),
(@tour_dalat, '2025-01-03', '2025-01-05', 30, 0, 4000000.00, 3200000.00, 0.00, 'pending', NULL, 'Chờ xác nhận HDV'),
(@tour_dalat, '2025-01-10', '2025-01-12', 30, 0, 3500000.00, 2800000.00, 0.00, 'open', @guide1, NULL),

-- Tour Nha Trang
(@tour_nhatrang, '2024-12-25', '2024-12-28', 45, 0, 4500000.00, 3600000.00, 0.00, 'open', @guide2, 'Tour mùa đông - Giá tốt'),
(@tour_nhatrang, '2025-01-01', '2025-01-04', 45, 12, 5000000.00, 4000000.00, 0.00, 'open', @guide2, 'Tết Dương lịch - Giá cao điểm'),
(@tour_nhatrang, '2025-01-15', '2025-01-18', 45, 0, 4500000.00, 3600000.00, 0.00, 'open', NULL, NULL),

-- Tour Hạ Long
(@tour_halong, '2024-12-22', '2024-12-23', 25, 0, 2800000.00, 2200000.00, 0.00, 'open', @guide1, 'Tour cuối tuần'),
(@tour_halong, '2024-12-29', '2024-12-30', 25, 8, 3000000.00, 2400000.00, 0.00, 'in_progress', @guide1, 'Tour đang diễn ra'),
(@tour_halong, '2025-01-05', '2025-01-06', 25, 0, 2800000.00, 2200000.00, 0.00, 'open', @guide2, NULL);

