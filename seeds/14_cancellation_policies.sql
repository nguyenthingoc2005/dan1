-- ==============================================================================
-- SEED DATA: CANCELLATION POLICIES
-- ==============================================================================
-- 
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

INSERT INTO `cancellation_policies` (`name`, `description`, `days_before`, `fee_percentage`, `status`) VALUES
('Hủy trước 30 ngày', 'Hủy tour trước 30 ngày khởi hành', 30, 0.00, 'active'),
('Hủy trước 15 ngày', 'Hủy tour trước 15 ngày khởi hành', 15, 20.00, 'active'),
('Hủy trước 7 ngày', 'Hủy tour trước 7 ngày khởi hành', 7, 50.00, 'active'),
('Hủy trước 3 ngày', 'Hủy tour trước 3 ngày khởi hành', 3, 80.00, 'active'),
('Hủy trong 3 ngày', 'Hủy tour trong vòng 3 ngày trước khởi hành', 0, 100.00, 'active');

