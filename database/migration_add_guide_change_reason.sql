-- ==============================================================================
-- MIGRATION: Thêm trường lý do đổi HDV
-- ==============================================================================
-- Thêm cột change_reason vào bảng tour_assignments để lưu lý do thay đổi HDV
-- ==============================================================================

-- Thêm cột change_reason vào tour_assignments
ALTER TABLE `tour_assignments` 
ADD COLUMN `change_reason` TEXT NULL COMMENT 'Lý do thay đổi HDV (khi thay đổi từ HDV cũ sang HDV mới)' 
AFTER `notes`;

-- Thêm cột previous_guide_id để lưu HDV cũ (nếu có)
ALTER TABLE `tour_assignments` 
ADD COLUMN `previous_guide_id` INT NULL COMMENT 'HDV trước đó (khi thay đổi)' 
AFTER `guide_id`;

-- Thêm foreign key cho previous_guide_id
ALTER TABLE `tour_assignments` 
ADD CONSTRAINT `tour_assignments_ibfk_previous_guide` 
FOREIGN KEY (`previous_guide_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Thêm index cho previous_guide_id
ALTER TABLE `tour_assignments` 
ADD KEY `idx_tour_assignments_previous_guide` (`previous_guide_id`);

-- ==============================================================================
-- GHI CHÚ
-- ==============================================================================
-- - change_reason: Lưu lý do thay đổi HDV (VD: "HDV cũ bị ốm", "Yêu cầu khách hàng", ...)
-- - previous_guide_id: Lưu ID của HDV trước đó để có thể tra cứu lịch sử
-- - Khi gán HDV lần đầu: previous_guide_id = NULL, change_reason = NULL
-- - Khi thay đổi HDV: previous_guide_id = guide_id cũ, change_reason = lý do nhập vào
-- ==============================================================================

