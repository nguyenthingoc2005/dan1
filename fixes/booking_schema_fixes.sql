-- ==============================================================================
-- SCRIPT SỬA LỖI SCHEMA BOOKING
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

-- 2. Thêm INDEX cho booking_services.booking_id (nếu chưa có)
-- Vấn đề: Thiếu index, ảnh hưởng hiệu năng query
ALTER TABLE `booking_services` 
ADD KEY `idx_booking_id` (`booking_id`);

-- ==============================================================================
-- PHẦN 2: MEDIUM PRIORITY FIXES (Nên sửa)
-- ==============================================================================

-- 3. Thêm rejected_by và rejected_at vào bookings
-- Vấn đề: Theo flow analysis, khi từ chối booking cần lưu thông tin người từ chối
ALTER TABLE `bookings` 
ADD COLUMN `rejected_by` int DEFAULT NULL AFTER `rejection_reason`,
ADD COLUMN `rejected_at` timestamp NULL DEFAULT NULL AFTER `rejected_by`,
ADD KEY `idx_rejected_by` (`rejected_by`),
ADD CONSTRAINT `bookings_ibfk_rejected_by` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- ==============================================================================
-- PHẦN 3: LOW PRIORITY FIXES (Có thể sửa sau)
-- ==============================================================================

-- 4. Thêm các index bổ sung cho booking_services (cải thiện hiệu năng)
ALTER TABLE `booking_services` 
ADD KEY `idx_booking_service_date` (`booking_id`, `service_date`),
ADD KEY `idx_payment_status` (`payment_status`);

-- 5. Thêm index cho bookings (cải thiện hiệu năng)
ALTER TABLE `bookings` 
ADD KEY `idx_tour_schedule_id` (`tour_schedule_id`),
ADD KEY `idx_start_date_end_date` (`start_date`, `end_date`);

-- 6. Thêm index cho booking_status_history (cải thiện hiệu năng)
ALTER TABLE `booking_status_history` 
ADD KEY `idx_booking_status` (`booking_id`, `new_status`),
ADD KEY `idx_created_at` (`created_at`);

-- 7. Thêm default values cho booking_services (tránh NULL)
ALTER TABLE `booking_services` 
MODIFY COLUMN `quantity` int NOT NULL DEFAULT 1,
MODIFY COLUMN `unit_price` decimal(15,2) DEFAULT 0.00,
MODIFY COLUMN `total_price` decimal(15,2) DEFAULT 0.00;

-- ==============================================================================
-- PHẦN 4: OPTIONAL - XÓA CÁC TRƯỜNG KHÔNG CẦN THIẾT
-- ==============================================================================
-- 
-- LƯU Ý: Chỉ chạy phần này nếu chắc chắn không sử dụng các trường này
-- 
-- Xóa quota và booked_seats từ bookings (nếu không dùng)
-- ALTER TABLE `bookings` 
-- DROP COLUMN `quota`,
-- DROP COLUMN `booked_seats`;
-- 
-- ==============================================================================

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
    AND TABLE_NAME = 'booking_services'
    AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Kiểm tra indexes
SHOW INDEX FROM `booking_services` WHERE Key_name LIKE 'idx_%';
SHOW INDEX FROM `bookings` WHERE Key_name LIKE 'idx_%';
SHOW INDEX FROM `booking_status_history` WHERE Key_name LIKE 'idx_%';

-- ==============================================================================
-- HOÀN TẤT
-- ==============================================================================
-- 
-- Sau khi chạy script này, vui lòng:
-- 1. Kiểm tra lại các foreign key constraints
-- 2. Test các query thường dùng
-- 3. Kiểm tra hiệu năng
-- 
-- ==============================================================================

