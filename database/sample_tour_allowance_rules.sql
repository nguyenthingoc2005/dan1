-- ==============================================================================
-- SAMPLE DATA: TOUR ALLOWANCE RULES
-- ==============================================================================
-- Dữ liệu mẫu cho bảng tour_allowance_rules
-- Chạy file này sau khi đã tạo bảng tour_allowance_rules
-- ==============================================================================

-- Xóa dữ liệu cũ (nếu có)
DELETE FROM `tour_allowance_rules`;

-- Reset AUTO_INCREMENT
ALTER TABLE `tour_allowance_rules` AUTO_INCREMENT = 1;

-- ==============================================================================
-- TOUR PUBLIC - CÁC TRƯỜNG HỢP PHỔ BIẾN
-- ==============================================================================

-- Tour public 1 ngày
INSERT INTO `tour_allowance_rules` 
(`rule_name`, `tour_type`, `duration_days_min`, `duration_days_max`, `participant_min`, `participant_max`, `guide_allowance`, `driver_allowance`, `priority`, `status`) VALUES
('Tour public 1 ngày, 10-15 khách', 'public', 1, 1, 10, 15, 800000, 400000, 10, 'active'),
('Tour public 1 ngày, 16-25 khách', 'public', 1, 1, 16, 25, 1000000, 500000, 10, 'active'),
('Tour public 1 ngày, 26-35 khách', 'public', 1, 1, 26, 35, 1200000, 600000, 10, 'active'),
('Tour public 1 ngày, 36-45 khách', 'public', 1, 1, 36, 45, 1500000, 800000, 10, 'active');

-- Tour public 2-3 ngày
INSERT INTO `tour_allowance_rules` 
(`rule_name`, `tour_type`, `duration_days_min`, `duration_days_max`, `participant_min`, `participant_max`, `guide_allowance`, `driver_allowance`, `priority`, `status`) VALUES
('Tour public 2-3 ngày, 10-15 khách', 'public', 2, 3, 10, 15, 1500000, 800000, 10, 'active'),
('Tour public 2-3 ngày, 16-20 khách', 'public', 2, 3, 16, 20, 1800000, 900000, 10, 'active'),
('Tour public 2-3 ngày, 21-30 khách', 'public', 2, 3, 21, 30, 2000000, 1000000, 10, 'active'),
('Tour public 2-3 ngày, 31-45 khách', 'public', 2, 3, 31, 45, 2500000, 1200000, 10, 'active');

-- Tour public 4-5 ngày
INSERT INTO `tour_allowance_rules` 
(`rule_name`, `tour_type`, `duration_days_min`, `duration_days_max`, `participant_min`, `participant_max`, `guide_allowance`, `driver_allowance`, `priority`, `status`) VALUES
('Tour public 4-5 ngày, 10-15 khách', 'public', 4, 5, 10, 15, 2500000, 1200000, 10, 'active'),
('Tour public 4-5 ngày, 16-20 khách', 'public', 4, 5, 16, 20, 3000000, 1500000, 10, 'active'),
('Tour public 4-5 ngày, 21-30 khách', 'public', 4, 5, 21, 30, 3500000, 1800000, 10, 'active'),
('Tour public 4-5 ngày, 31-45 khách', 'public', 4, 5, 31, 45, 4000000, 2000000, 10, 'active');

-- Tour public 6-7 ngày
INSERT INTO `tour_allowance_rules` 
(`rule_name`, `tour_type`, `duration_days_min`, `duration_days_max`, `participant_min`, `participant_max`, `guide_allowance`, `driver_allowance`, `priority`, `status`) VALUES
('Tour public 6-7 ngày, 10-15 khách', 'public', 6, 7, 10, 15, 3500000, 1800000, 10, 'active'),
('Tour public 6-7 ngày, 16-20 khách', 'public', 6, 7, 16, 20, 4000000, 2000000, 10, 'active'),
('Tour public 6-7 ngày, 21-30 khách', 'public', 6, 7, 21, 30, 4500000, 2200000, 10, 'active'),
('Tour public 6-7 ngày, 31-45 khách', 'public', 6, 7, 31, 45, 5000000, 2500000, 10, 'active');

-- Tour public 8-10 ngày
INSERT INTO `tour_allowance_rules` 
(`rule_name`, `tour_type`, `duration_days_min`, `duration_days_max`, `participant_min`, `participant_max`, `guide_allowance`, `driver_allowance`, `priority`, `status`) VALUES
('Tour public 8-10 ngày, 10-15 khách', 'public', 8, 10, 10, 15, 5000000, 2500000, 10, 'active'),
('Tour public 8-10 ngày, 16-20 khách', 'public', 8, 10, 16, 20, 5500000, 2800000, 10, 'active'),
('Tour public 8-10 ngày, 21-30 khách', 'public', 8, 10, 21, 30, 6000000, 3000000, 10, 'active'),
('Tour public 8-10 ngày, 31-45 khách', 'public', 8, 10, 31, 45, 6500000, 3200000, 10, 'active');

-- Tour public trên 10 ngày
INSERT INTO `tour_allowance_rules` 
(`rule_name`, `tour_type`, `duration_days_min`, `duration_days_max`, `participant_min`, `participant_max`, `guide_allowance`, `driver_allowance`, `priority`, `status`) VALUES
('Tour public 11-14 ngày, 10-15 khách', 'public', 11, 14, 10, 15, 7000000, 3500000, 10, 'active'),
('Tour public 11-14 ngày, 16-20 khách', 'public', 11, 14, 16, 20, 7500000, 3800000, 10, 'active'),
('Tour public 11-14 ngày, 21-30 khách', 'public', 11, 14, 21, 30, 8000000, 4000000, 10, 'active'),
('Tour public 11-14 ngày, 31-45 khách', 'public', 11, 14, 31, 45, 8500000, 4200000, 10, 'active'),
('Tour public 15+ ngày, bất kỳ số khách', 'public', 15, NULL, NULL, NULL, 10000000, 5000000, 10, 'active');

-- ==============================================================================
-- TOUR CUSTOM - TOUR TÙY CHỈNH
-- ==============================================================================

-- Tour custom - không phụ thuộc số ngày và số khách (linh hoạt)
INSERT INTO `tour_allowance_rules` 
(`rule_name`, `tour_type`, `duration_days_min`, `duration_days_max`, `participant_min`, `participant_max`, `guide_allowance`, `driver_allowance`, `priority`, `status`) VALUES
('Tour custom 1-3 ngày', 'custom', 1, 3, NULL, NULL, 3000000, 1500000, 5, 'active'),
('Tour custom 4-7 ngày', 'custom', 4, 7, NULL, NULL, 5000000, 2500000, 5, 'active'),
('Tour custom 8-14 ngày', 'custom', 8, 14, NULL, NULL, 8000000, 4000000, 5, 'active'),
('Tour custom 15+ ngày', 'custom', 15, NULL, NULL, NULL, 12000000, 6000000, 5, 'active');

-- ==============================================================================
-- RULE MẶC ĐỊNH - FALLBACK (Priority thấp hơn)
-- ==============================================================================

-- Rule mặc định cho tour public (nếu không match rule nào)
INSERT INTO `tour_allowance_rules` 
(`rule_name`, `tour_type`, `duration_days_min`, `duration_days_max`, `participant_min`, `participant_max`, `guide_allowance`, `driver_allowance`, `priority`, `status`) VALUES
('Tour public mặc định', 'public', NULL, NULL, NULL, NULL, 0, 0, 1, 'active');

-- Rule mặc định cho tour custom (nếu không match rule nào)
INSERT INTO `tour_allowance_rules` 
(`rule_name`, `tour_type`, `duration_days_min`, `duration_days_max`, `participant_min`, `participant_max`, `guide_allowance`, `driver_allowance`, `priority`, `status`) VALUES
('Tour custom mặc định', 'custom', NULL, NULL, NULL, NULL, 0, 0, 1, 'active');

-- ==============================================================================
-- GHI CHÚ
-- ==============================================================================
-- Priority: Số càng cao càng ưu tiên
-- - Priority 10: Rule cụ thể (ưu tiên cao nhất)
-- - Priority 5: Rule cho tour custom
-- - Priority 1: Rule mặc định (fallback)
--
-- Logic tìm rule:
-- 1. Match tour_type
-- 2. Match duration_days (nếu có)
-- 3. Match participant_count (nếu có - chỉ cho tài xế)
-- 4. Chọn rule có priority cao nhất
-- 5. Nếu không có rule nào match, dùng giá trị mặc định trong code
--    (HDV: 500k/ngày, Tài xế: 0)
-- ==============================================================================

