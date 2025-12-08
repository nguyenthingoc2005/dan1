-- ==============================================================================
-- MODULE 3: TOUR
-- ==============================================================================

-- Tour Cost Templates (MỚI - Phương án 3)
CREATE TABLE IF NOT EXISTS `tour_cost_templates` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `template_name` VARCHAR(200) NOT NULL COMMENT 'Tên template (VD: "Tour trong nước 3 ngày")',
  `description` TEXT COMMENT 'Mô tả template',
  `fixed_cost_total` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Tổng chi phí cố định mặc định',
  `is_default` TINYINT(1) DEFAULT 0 COMMENT '1 = template mặc định',
  `status` ENUM('active','inactive') DEFAULT 'active',
  `created_by` INT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `idx_templates_status` (`status`),
  CONSTRAINT `tour_cost_templates_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tours (ĐÃ SỬA - Bỏ 4 cột fixed_cost, thay bằng 1 cột + template)
CREATE TABLE IF NOT EXISTS `tours` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `introduction` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `duration_days` int NOT NULL,
  `duration_nights` int NOT NULL,
  `departure_location` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_participants` int DEFAULT '15',
  `max_participants` int DEFAULT '45',
  `adult_price` decimal(15,2) NOT NULL,
  `child_price` decimal(15,2) NOT NULL,
  `infant_price` decimal(15,2) DEFAULT '0.00',
  `estimated_cost_per_person` decimal(15,2) DEFAULT NULL,
  `markup_percentage` decimal(5,2) DEFAULT '0.00' COMMENT 'DEPRECATED - Không dùng nữa, giữ lại để backward compatible',
  `deposit_percentage` decimal(5,2) DEFAULT '30.00',
  `fixed_cost_total` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Tổng chi phí cố định (tự động từ template hoặc nhập thủ công)',
  `tour_cost_template_id` INT NULL COMMENT 'Template chi phí (nếu có)',
  `use_template_cost` TINYINT(1) DEFAULT 1 COMMENT '1 = dùng từ template, 0 = nhập thủ công',
  `booking_deadline_days` int DEFAULT '1' COMMENT 'Số ngày tối thiểu trước ngày khởi hành để đặt booking (default: 1 ngày)',
  `tour_type` enum('public','custom') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `approved_by` int DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','pending','active','rejected','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `parent_tour_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tour_code` (`tour_code`),
  KEY `approved_by` (`approved_by`),
  KEY `idx_tours_status` (`status`),
  KEY `idx_tours_created_by` (`created_by`),
  KEY `parent_tour_id` (`parent_tour_id`),
  KEY `tour_cost_template_id` (`tour_cost_template_id`),
  CONSTRAINT `tours_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tours_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tours_ibfk_4` FOREIGN KEY (`parent_tour_id`) REFERENCES `tours` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tours_ibfk_cost_template` FOREIGN KEY (`tour_cost_template_id`) REFERENCES `tour_cost_templates` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tour Schedules (ĐÃ SỬA - Thêm status 'confirmed')
CREATE TABLE IF NOT EXISTS `tour_schedules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `quota` int DEFAULT '20',
  `booked` int DEFAULT '0',
  `adult_price` decimal(15,2) DEFAULT NULL,
  `child_price` decimal(15,2) DEFAULT NULL,
  `infant_price` decimal(15,2) DEFAULT NULL,
  `status` enum('open','closed','pending','confirmed','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `guide_id` int DEFAULT NULL,
  `guide_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tour_schedule_unique` (`tour_id`,`start_date`,`end_date`),
  KEY `fk_schedule_guide` (`guide_id`),
  KEY `idx_schedule_status` (`status`),
  CONSTRAINT `fk_schedule_guide` FOREIGN KEY (`guide_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tour_schedules_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Itineraries
CREATE TABLE IF NOT EXISTS `itineraries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL,
  `destination_id` int DEFAULT NULL,
  `day_number` int NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `meals` json DEFAULT NULL,
  `accommodation` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `departure_time` time DEFAULT NULL,
  `display_order` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  KEY `destination_id` (`destination_id`),
  CONSTRAINT `itineraries_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE,
  CONSTRAINT `itineraries_ibfk_2` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Itinerary Day Services
CREATE TABLE IF NOT EXISTS `itinerary_day_services` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `itinerary_id` INT NOT NULL COMMENT 'Foreign key → itineraries',
  `service_id` INT NOT NULL COMMENT 'Foreign key → services',
  `service_provider_id` INT NULL COMMENT 'Foreign key → service_providers (khách sạn, nhà hàng cụ thể)',
  `service_name` VARCHAR(200) NULL COMMENT 'Tên dịch vụ (snapshot)',
  `unit_price` DECIMAL(15,2) NOT NULL COMMENT 'Đơn giá/người',
  `quantity` DECIMAL(10,2) NOT NULL DEFAULT 1.00 COMMENT 'Số lượng (VD: 1 bữa, 1 đêm)',
  `unit` VARCHAR(50) NULL COMMENT 'Đơn vị (VD: "bữa", "đêm", "vé")',
  `is_included_in_price` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Bao gồm trong giá tour',
  `notes` TEXT NULL COMMENT 'Ghi chú',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_itinerary_id` (`itinerary_id`),
  KEY `idx_service_id` (`service_id`),
  KEY `idx_service_provider_id` (`service_provider_id`),
  KEY `idx_is_included` (`is_included_in_price`),
  CONSTRAINT `fk_itinerary_day_services_itinerary` FOREIGN KEY (`itinerary_id`) REFERENCES `itineraries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_itinerary_day_services_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_itinerary_day_services_service_provider` FOREIGN KEY (`service_provider_id`) REFERENCES `service_providers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Dịch vụ theo từng ngày của tour (để tính chi phí)';

-- Indexes bổ sung cho itinerary_day_services
CREATE INDEX IF NOT EXISTS `idx_itinerary_service_included` ON `itinerary_day_services` (`itinerary_id`, `is_included_in_price`, `unit_price`);

-- Tour Services (Backward compatible - giữ lại)
CREATE TABLE IF NOT EXISTS `tour_services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL,
  `service_id` int NOT NULL,
  `service_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calculation_type` enum('per_person','per_group','per_day','fixed') COLLATE utf8mb4_unicode_ci DEFAULT 'per_person',
  `fixed_quantity` int DEFAULT '1',
  `group_size` int DEFAULT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_included_in_price` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `tour_services_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tour Images
CREATE TABLE IF NOT EXISTS `tour_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT '0',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_images_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tour Highlights
CREATE TABLE IF NOT EXISTS `tour_highlights` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL,
  `highlight` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_highlights_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tour Included/Excluded
CREATE TABLE IF NOT EXISTS `tour_included_excluded` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL,
  `type` enum('included','excluded') COLLATE utf8mb4_unicode_ci NOT NULL,
  `item` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_included_excluded_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tour FAQs
CREATE TABLE IF NOT EXISTS `tour_faqs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_faqs_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Policies
CREATE TABLE IF NOT EXISTS `policies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `policy_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tour Policies
CREATE TABLE IF NOT EXISTS `tour_policies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL,
  `policy_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  KEY `policy_id` (`policy_id`),
  CONSTRAINT `tour_policies_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tour_policies_ibfk_2` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

