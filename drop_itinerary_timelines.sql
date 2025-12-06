-- ==============================================================================
-- DROP BẢNG itinerary_timelines VÀ CÁC INDEX LIÊN QUAN
-- ==============================================================================
-- 
-- Lưu ý: Chạy các câu lệnh này theo thứ tự để tránh lỗi foreign key constraint
-- ==============================================================================

-- 1. Drop index trước (nếu có)
DROP INDEX IF EXISTS `idx_itinerary_time_order` ON `itinerary_timelines`;

-- 2. Drop bảng itinerary_timelines (sẽ tự động drop các foreign key constraints)
DROP TABLE IF EXISTS `itinerary_timelines`;

-- ==============================================================================
-- KIỂM TRA SAU KHI DROP
-- ==============================================================================
-- Chạy câu lệnh sau để kiểm tra bảng đã bị xóa chưa:
-- SHOW TABLES LIKE 'itinerary_timelines';
-- 
-- Nếu không có kết quả trả về, nghĩa là bảng đã được xóa thành công.
-- ==============================================================================

