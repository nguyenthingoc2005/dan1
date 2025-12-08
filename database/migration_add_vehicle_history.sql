-- ==============================================================================
-- MIGRATION: Tạo bảng lịch sử thay đổi xe và tài xế
-- ==============================================================================
-- Tạo bảng schedule_vehicle_history để lưu lịch sử thay đổi xe/tài xế
-- Tương tự như schedule_guide_history
-- ==============================================================================

CREATE TABLE IF NOT EXISTS `schedule_vehicle_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `schedule_id` int NOT NULL COMMENT 'Tour schedule ID',
  `old_vehicle_id` int DEFAULT NULL COMMENT 'Xe cũ',
  `new_vehicle_id` int DEFAULT NULL COMMENT 'Xe mới',
  `old_vehicle_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã xe cũ (snapshot)',
  `new_vehicle_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã xe mới (snapshot)',
  `old_vehicle_plate` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Biển số xe cũ',
  `new_vehicle_plate` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Biển số xe mới',
  `old_driver_id` int DEFAULT NULL COMMENT 'Tài xế cũ',
  `new_driver_id` int DEFAULT NULL COMMENT 'Tài xế mới',
  `old_driver_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tên tài xế cũ (snapshot)',
  `new_driver_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tên tài xế mới (snapshot)',
  `change_type` ENUM('vehicle', 'driver', 'both') NOT NULL DEFAULT 'both' COMMENT 'Loại thay đổi: chỉ xe, chỉ tài xế, hoặc cả hai',
  `changed_by` int DEFAULT NULL COMMENT 'User thực hiện thay đổi',
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Lý do thay đổi',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú thêm',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `schedule_id` (`schedule_id`),
  KEY `changed_by` (`changed_by`),
  KEY `old_vehicle_id` (`old_vehicle_id`),
  KEY `new_vehicle_id` (`new_vehicle_id`),
  KEY `old_driver_id` (`old_driver_id`),
  KEY `new_driver_id` (`new_driver_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `schedule_vehicle_history_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedule_vehicle_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `schedule_vehicle_history_ibfk_3` FOREIGN KEY (`old_vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `schedule_vehicle_history_ibfk_4` FOREIGN KEY (`new_vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `schedule_vehicle_history_ibfk_5` FOREIGN KEY (`old_driver_id`) REFERENCES `drivers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `schedule_vehicle_history_ibfk_6` FOREIGN KEY (`new_driver_id`) REFERENCES `drivers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Lịch sử thay đổi xe và tài xế cho tour schedule';

-- ==============================================================================
-- GHI CHÚ
-- ==============================================================================
-- - change_type: 'vehicle' = chỉ đổi xe, 'driver' = chỉ đổi tài xế, 'both' = đổi cả hai
-- - Lưu snapshot (vehicle_code, license_plate, driver_name) để có thể hiển thị ngay cả khi record bị xóa
-- - Khi gán lần đầu: old_vehicle_id = NULL, old_driver_id = NULL
-- - Khi thay đổi: old_vehicle_id/driver_id = giá trị cũ, new_vehicle_id/driver_id = giá trị mới
-- ==============================================================================

