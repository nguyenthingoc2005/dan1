-- ==============================================================================
-- MODULE 7: OPERATIONS
-- ==============================================================================

-- Tour Allowance Rules (MỚI - Tính phụ cấp tự động)
CREATE TABLE IF NOT EXISTS `tour_allowance_rules` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `rule_name` VARCHAR(200) NOT NULL COMMENT 'Tên quy tắc (VD: "Tour trong nước 3 ngày 15-20 khách")',
  `tour_type` ENUM('public','custom') NOT NULL,
  `duration_days_min` INT NULL COMMENT 'Số ngày tối thiểu',
  `duration_days_max` INT NULL COMMENT 'Số ngày tối đa',
  `participant_min` INT NULL COMMENT 'Số khách tối thiểu',
  `participant_max` INT NULL COMMENT 'Số khách tối đa',
  `guide_allowance` DECIMAL(15,2) NOT NULL COMMENT 'Phụ cấp HDV cho tour này',
  `driver_allowance` DECIMAL(15,2) NOT NULL COMMENT 'Phụ cấp tài xế cho tour này',
  `season_multiplier` DECIMAL(5,2) DEFAULT 1.00 COMMENT 'Hệ số mùa vụ (1.0 = bình thường, 1.3 = cao điểm)',
  `priority` INT DEFAULT 0 COMMENT 'Ưu tiên (số càng cao càng ưu tiên)',
  `status` ENUM('active','inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_type` (`tour_type`),
  KEY `priority` (`priority`),
  KEY `idx_allowance_rules_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tour Assignments (ĐÃ SỬA - tour_schedule_id NOT NULL, thêm previous_guide_id và change_reason)
CREATE TABLE IF NOT EXISTS `tour_assignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_schedule_id` int NOT NULL COMMENT 'Bắt buộc - tour schedule nào',
  `booking_id` int DEFAULT NULL,
  `guide_id` int NOT NULL,
  `previous_guide_id` int DEFAULT NULL COMMENT 'HDV trước đó (khi thay đổi)',
  `assignment_date` date NOT NULL,
  `salary_amount` decimal(15,2) DEFAULT NULL COMMENT 'Phụ cấp tour (tự động từ tour_allowance_rules)',
  `salary_status` enum('pending','paid') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `paid_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `change_reason` text COLLATE utf8mb4_unicode_ci COMMENT 'Lý do thay đổi HDV (khi thay đổi từ HDV cũ sang HDV mới)',
  `status` enum('assigned','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'assigned',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `idx_tour_assignments_schedule` (`tour_schedule_id`),
  KEY `idx_tour_assignments_booking_id` (`booking_id`),
  KEY `idx_tour_assignments_guide_id` (`guide_id`),
  KEY `idx_tour_assignments_previous_guide` (`previous_guide_id`),
  CONSTRAINT `tour_assignments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  CONSTRAINT `tour_assignments_ibfk_2` FOREIGN KEY (`guide_id`) REFERENCES `users` (`id`),
  CONSTRAINT `tour_assignments_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tour_assignments_ibfk_previous_guide` FOREIGN KEY (`previous_guide_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tour_assignments_ibfk_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Journals
CREATE TABLE IF NOT EXISTS `journals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_schedule_id` int NOT NULL COMMENT 'Foreign key → tour_schedules (journal theo tour)',
  `booking_id` int DEFAULT NULL COMMENT 'Giữ lại để backward compatible, có thể NULL',
  `guide_id` int NOT NULL,
  `journal_date` date NOT NULL,
  `day_number` int DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `weather` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `highlights` text COLLATE utf8mb4_unicode_ci,
  `issues` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `guide_id` (`guide_id`),
  KEY `idx_journals_tour_schedule_id` (`tour_schedule_id`),
  CONSTRAINT `journals_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  CONSTRAINT `journals_ibfk_2` FOREIGN KEY (`guide_id`) REFERENCES `users` (`id`),
  CONSTRAINT `journals_ibfk_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Journal Images
CREATE TABLE IF NOT EXISTS `journal_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `journal_id` int NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` text COLLATE utf8mb4_unicode_ci,
  `display_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `journal_id` (`journal_id`),
  CONSTRAINT `journal_images_ibfk_1` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Incurred Expenses (ĐÃ SỬA - Thêm tour_schedule_id, booking_id có thể NULL)
CREATE TABLE IF NOT EXISTS `incurred_expenses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NULL COMMENT 'Có thể NULL nếu chi phí theo tour',
  `tour_schedule_id` int NULL COMMENT 'Link với tour schedule',
  `expense_date` date NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `receipt_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reported_by` int DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `reported_by` (`reported_by`),
  KEY `approved_by` (`approved_by`),
  KEY `booking_id` (`booking_id`),
  KEY `tour_schedule_id` (`tour_schedule_id`),
  KEY `idx_incurred_expenses_approval_status` (`approval_status`),
  CONSTRAINT `incurred_expenses_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  CONSTRAINT `incurred_expenses_ibfk_2` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `incurred_expenses_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `incurred_expenses_ibfk_tour_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Schedule Guide History
CREATE TABLE IF NOT EXISTS `schedule_guide_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `schedule_id` int NOT NULL,
  `old_guide_id` int DEFAULT NULL,
  `new_guide_id` int DEFAULT NULL,
  `old_guide_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_guide_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `changed_by` int DEFAULT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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

