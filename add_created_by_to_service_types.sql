-- ==============================================================================
-- ADD created_by COLUMN TO service_types TABLE
-- ==============================================================================
-- 
-- Thêm cột created_by để lưu thông tin người tạo loại dịch vụ
-- Chạy script này nếu cột chưa tồn tại trong database
-- 
-- ==============================================================================

-- Kiểm tra và thêm cột created_by nếu chưa tồn tại
-- Lưu ý: Nếu cột đã tồn tại, sẽ báo lỗi. Bỏ qua lỗi nếu cột đã có.

-- Thêm cột created_by
ALTER TABLE `service_types` 
ADD COLUMN `created_by` int DEFAULT NULL AFTER `status`;

-- Thêm index
ALTER TABLE `service_types` 
ADD KEY `created_by` (`created_by`);

-- Thêm foreign key constraint
ALTER TABLE `service_types` 
ADD CONSTRAINT `service_types_ibfk_created_by` 
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

