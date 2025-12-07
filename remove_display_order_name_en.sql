-- ==============================================================================
-- MIGRATION: Bỏ display_order và name_en từ countries và provinces
-- ==============================================================================
-- 
-- Lý do:
-- 1. display_order: Không cần thiết, có thể sắp xếp theo name hoặc id
-- 2. name_en: Không cần thiết, chỉ cần name (tiếng Việt) là đủ
-- 
-- Date: 2024-12-XX
-- ==============================================================================

-- Bỏ display_order và name_en từ bảng countries
ALTER TABLE `countries` 
DROP COLUMN IF EXISTS `display_order`,
DROP COLUMN IF EXISTS `name_en`;

-- Bỏ display_order và name_en từ bảng provinces
ALTER TABLE `provinces` 
DROP COLUMN IF EXISTS `display_order`,
DROP COLUMN IF EXISTS `name_en`;

-- ==============================================================================
-- LƯU Ý: 
-- - Nếu có dữ liệu quan trọng trong name_en, cần backup trước
-- - Các code đang sử dụng display_order cần được cập nhật
-- ==============================================================================

