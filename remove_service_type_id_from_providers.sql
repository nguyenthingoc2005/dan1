-- ==============================================================================
-- MIGRATION: Bỏ service_type_id từ bảng service_providers
-- ==============================================================================
-- 
-- Lý do:
-- - Một nhà cung cấp có thể cung cấp nhiều loại dịch vụ khác nhau
-- - Mỗi service đã có service_type_id riêng, không cần ở provider level
-- - Linh hoạt hơn, tránh mâu thuẫn dữ liệu
-- 
-- Date: 2024-12-XX
-- ==============================================================================

-- Bỏ foreign key constraint trước
ALTER TABLE `service_providers` 
DROP FOREIGN KEY IF EXISTS `service_providers_ibfk_3`;

-- Bỏ index
ALTER TABLE `service_providers` 
DROP INDEX IF EXISTS `service_type_id`;

-- Bỏ cột service_type_id
ALTER TABLE `service_providers` 
DROP COLUMN IF EXISTS `service_type_id`;

-- ==============================================================================
-- LƯU Ý: 
-- - Các code đang sử dụng service_providers.service_type_id cần được cập nhật
-- - Thay vào đó, filter qua bảng services: 
--   SELECT DISTINCT sp.* FROM service_providers sp 
--   INNER JOIN services s ON s.service_provider_id = sp.id 
--   WHERE s.service_type_id = ?
-- ==============================================================================

