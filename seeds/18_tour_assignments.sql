-- ==============================================================================
-- SEED DATA: TOUR ASSIGNMENTS
-- ==============================================================================
-- 
-- Phụ thuộc: tour_schedules (05_tour_schedules.sql), users (02_users.sql)
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

SET @tour_dalat = (SELECT id FROM tours WHERE tour_code = 'TOUR-20241206-001' LIMIT 1);
SET @tour_nhatrang = (SELECT id FROM tours WHERE tour_code = 'TOUR-20241206-002' LIMIT 1);
SET @schedule_dalat_1 = (SELECT id FROM tour_schedules WHERE tour_id = @tour_dalat AND start_date = '2024-12-20' LIMIT 1);
SET @schedule_dalat_2 = (SELECT id FROM tour_schedules WHERE tour_id = @tour_dalat AND start_date = '2024-12-27' LIMIT 1);
SET @schedule_nhatrang_1 = (SELECT id FROM tour_schedules WHERE tour_id = @tour_nhatrang AND start_date = '2024-12-25' LIMIT 1);
SET @guide1 = (SELECT id FROM users WHERE email = 'guide1@tour.com' LIMIT 1);
SET @guide2 = (SELECT id FROM users WHERE email = 'guide2@tour.com' LIMIT 1);
SET @user_admin = (SELECT id FROM users WHERE email = 'admin@tour.com' LIMIT 1);

INSERT INTO `tour_assignments` (`tour_schedule_id`, `guide_id`, `assignment_date`, `status`, `notes`, `created_by`) VALUES
(@schedule_dalat_1, @guide1, '2024-12-20', 'assigned', 'HDV có kinh nghiệm', @user_admin),
(@schedule_dalat_2, @guide1, '2024-12-27', 'assigned', 'Tour mùa cao điểm', @user_admin),
(@schedule_nhatrang_1, @guide2, '2024-12-25', 'assigned', 'Tour biển', @user_admin);

