-- Migration: Thêm bảng lưu lịch sử thay đổi HDV cho tour schedules
-- Date: 2024-12-XX
-- Description: Lưu lại lịch sử mỗi lần thay đổi HDV để theo dõi và audit

CREATE TABLE IF NOT EXISTS `schedule_guide_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `schedule_id` int NOT NULL,
  `old_guide_id` int DEFAULT NULL,
  `new_guide_id` int DEFAULT NULL,
  `old_guide_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_guide_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `changed_by` int DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `schedule_id` (`schedule_id`),
  KEY `changed_by` (`changed_by`),
  KEY `old_guide_id` (`old_guide_id`),
  KEY `new_guide_id` (`new_guide_id`),
  CONSTRAINT `schedule_guide_history_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedule_guide_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `schedule_guide_history_ibfk_3` FOREIGN KEY (`old_guide_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `schedule_guide_history_ibfk_4` FOREIGN KEY (`new_guide_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

