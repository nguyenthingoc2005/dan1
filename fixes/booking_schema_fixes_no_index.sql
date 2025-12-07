-- ==============================================================================
-- SCRIPT SỬA LỖI SCHEMA BOOKING (KHÔNG BAO GỒM INDEX)
-- ==============================================================================
-- 
-- Các lỗi được phát hiện từ phân tích schema booking
-- Xem chi tiết: flows/BOOKING_SCHEMA_ANALYSIS.md
-- 
-- Ngày tạo: 2024-12-06
-- ==============================================================================

USE `tour_managementss`;

-- ==============================================================================
-- PHẦN 1: CRITICAL FIXES (Phải sửa ngay)
-- ==============================================================================

-- 1. Thêm FOREIGN KEY constraint cho booking_services.booking_id
-- Vấn đề: Thiếu foreign key constraint, có thể dẫn đến dữ liệu không nhất quán
ALTER TABLE `booking_services` 
ADD CONSTRAINT `booking_services_ibfk_booking` 
FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

-- ==============================================================================
-- PHẦN 2: MEDIUM PRIORITY FIXES (Nên sửa)
-- ==============================================================================

-- 2. Thêm rejected_by và rejected_at vào bookings
-- Vấn đề: Theo flow analysis, khi từ chối booking cần lưu thông tin người từ chối
ALTER TABLE `bookings` 
ADD COLUMN `rejected_by` int DEFAULT NULL AFTER `rejection_reason`,
ADD COLUMN `rejected_at` timestamp NULL DEFAULT NULL AFTER `rejected_by`,
ADD CONSTRAINT `bookings_ibfk_rejected_by` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- ==============================================================================
-- KIỂM TRA SAU KHI SỬA
-- ==============================================================================

-- Kiểm tra foreign key constraints
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM 
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE 
    TABLE_SCHEMA = 'tour_managementss'
    AND TABLE_NAME IN ('booking_services', 'bookings')
    AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, CONSTRAINT_NAME;

-- ==============================================================================
-- HOÀN TẤT
-- ==============================================================================
-- 
-- Sau khi chạy script này, vui lòng:
-- 1. Kiểm tra lại các foreign key constraints
-- 2. Test các query thường dùng
-- 
-- ==============================================================================

