-- ==============================================================================
-- SEED DATA: SERVICE TYPES (Loại dịch vụ)
-- ==============================================================================
-- 
-- File này nên chạy TRƯỚC seed_location_services.sql
-- Date: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

INSERT INTO `service_types` (`name`, `description`, `status`, `display_order`) VALUES
-- Dịch vụ cơ bản
('Khách sạn', 'Dịch vụ lưu trú tại khách sạn, resort', 'active', 1),
('Nhà hàng', 'Dịch vụ ăn uống tại nhà hàng', 'active', 2),
('Vận chuyển', 'Dịch vụ vận chuyển, xe đưa đón', 'active', 3),
('Vé tham quan', 'Vé vào cửa các điểm tham quan', 'active', 4),
('Hướng dẫn viên', 'Dịch vụ hướng dẫn viên du lịch', 'active', 5),

-- Dịch vụ hỗ trợ
('Bảo hiểm', 'Dịch vụ bảo hiểm du lịch', 'active', 6),
('Mua sắm', 'Dịch vụ mua sắm, shopping', 'active', 7),
('Giải trí', 'Dịch vụ giải trí, show diễn', 'active', 8),
('Spa & Massage', 'Dịch vụ spa, massage, thư giãn', 'active', 9),
('Thể thao & Hoạt động', 'Dịch vụ thể thao, hoạt động ngoài trời, adventure', 'active', 10),

-- Dịch vụ tour
('Tour nội địa', 'Tour du lịch trong nước', 'active', 11),
('Tour quốc tế', 'Tour du lịch nước ngoài', 'active', 12),

-- Dịch vụ hành chính
('Dịch vụ Visa', 'Dịch vụ làm visa, thủ tục xuất nhập cảnh', 'active', 13),
('Đổi tiền', 'Dịch vụ đổi ngoại tệ', 'active', 14),
('SIM & Internet', 'Dịch vụ SIM card, internet, wifi', 'active', 15),

-- Dịch vụ chuyên nghiệp
('Chụp ảnh', 'Dịch vụ chụp ảnh, quay phim du lịch', 'active', 16),
('Tổ chức sự kiện', 'Dịch vụ tổ chức sự kiện, hội nghị, team building', 'active', 17),
('Cho thuê thiết bị', 'Cho thuê thiết bị du lịch, máy ảnh, đồ dùng', 'active', 18),

-- Dịch vụ hỗ trợ khác
('Dịch vụ y tế', 'Dịch vụ y tế, khám sức khỏe, cấp cứu', 'active', 19),
('Dịch vụ giặt ủi', 'Dịch vụ giặt ủi, làm sạch', 'active', 20),
('Dịch vụ lễ tân', 'Dịch vụ lễ tân, hỗ trợ khách hàng', 'active', 21),
('Dịch vụ đưa đón sân bay', 'Dịch vụ đưa đón sân bay', 'active', 22),
('Dịch vụ phòng họp', 'Cho thuê phòng họp, hội nghị', 'active', 23),
('Dịch vụ lưu trữ hành lý', 'Dịch vụ lưu trữ, gửi hành lý', 'active', 24),
('Dịch vụ đặt bàn', 'Dịch vụ đặt bàn nhà hàng, đặt chỗ', 'active', 25)
ON DUPLICATE KEY UPDATE name = VALUES(name);

