-- ==============================================================================
-- INSERT 3 CATEGORIES CỐ ĐỊNH
-- ==============================================================================
-- Chạy sau khi setup database xong
-- ==============================================================================

USE tour_management;

-- Truncate nếu đã có data test
-- TRUNCATE TABLE categories;

-- Insert 3 categories
INSERT INTO categories (name, description, display_order, status) VALUES
('Trong nước', 'Tour du lịch khám phá các địa danh văn hóa, thiên nhiên trong nước Việt Nam', 1, 'active'),
('Ngoài nước', 'Tour du lịch quốc tế đến các điểm đến nổi tiếng trên thế giới', 2, 'active'),
('Custom Tour', 'Tour thiết kế riêng theo yêu cầu của khách hàng', 3, 'active');

-- Verify
SELECT * FROM categories ORDER BY display_order;
