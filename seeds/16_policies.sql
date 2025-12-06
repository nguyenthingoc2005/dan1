-- ==============================================================================
-- SEED DATA: POLICIES
-- ==============================================================================
-- 
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

INSERT INTO `policies` (`name`, `description`, `policy_type`, `content`, `status`) VALUES
('Chính sách đặt tour', 'Quy định về việc đặt tour và thanh toán', 'booking', '<p>Khách hàng cần đặt cọc 30% khi đặt tour. Số tiền còn lại thanh toán trước 7 ngày khởi hành.</p>', 'active'),
('Chính sách hủy tour', 'Quy định về việc hủy tour và phí hủy', 'cancellation', '<p>Khách hàng có thể hủy tour theo chính sách hủy đã quy định. Phí hủy phụ thuộc vào thời gian hủy.</p>', 'active'),
('Chính sách đổi tour', 'Quy định về việc đổi tour', 'change', '<p>Khách hàng có thể đổi tour trước 15 ngày khởi hành với phí đổi 10% giá tour.</p>', 'active'),
('Chính sách bảo hiểm', 'Quy định về bảo hiểm du lịch', 'insurance', '<p>Tất cả tour đều bao gồm bảo hiểm du lịch. Khách hàng nên mua thêm bảo hiểm nếu cần.</p>', 'active');

