-- ==============================================================================
-- MODULE 10: VEHICLE & DRIVER MANAGEMENT (MỚI)
-- ==============================================================================
-- Tính năng: Quản lý xe và tài xế
-- - Chỉ quản lý xe công ty
-- - Phụ cấp tài xế tự động tính từ tour_allowance_rules
-- - Tránh trùng lịch

-- Vehicles (Đã có trong schema cũ)
CREATE TABLE IF NOT EXISTS `vehicles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vehicle_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_type` enum('bus_45','bus_29','bus_16','car_7','car_4') COLLATE utf8mb4_unicode_ci NOT NULL,
  `license_plate` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` int NOT NULL,
  `status` enum('active','maintenance','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_code` (`vehicle_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Drivers (MỚI)
CREATE TABLE IF NOT EXISTS `drivers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `driver_code` VARCHAR(50) COMMENT 'Mã tài xế (VD: "DRV001")',
  `full_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `email` VARCHAR(100),
  `id_card` VARCHAR(50) COMMENT 'CMND/CCCD',
  `license_number` VARCHAR(50) NOT NULL COMMENT 'Số bằng lái',
  `license_type` ENUM('A1','A2','B1','B2','C','D','E','F') COMMENT 'Hạng bằng',
  `license_issue_date` DATE,
  `license_expiry_date` DATE,
  `date_of_birth` DATE,
  `address` TEXT,
  `emergency_contact_name` VARCHAR(100) COMMENT 'Người liên hệ khẩn cấp',
  `emergency_contact_phone` VARCHAR(20),
  `status` ENUM('active','on_trip','off_duty','suspended','inactive') DEFAULT 'active',
  `hire_date` DATE COMMENT 'Ngày bắt đầu làm việc',
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `driver_code` (`driver_code`),
  KEY `idx_drivers_status` (`status`),
  KEY `idx_drivers_license_type` (`license_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vehicle Assignments (MỚI)
CREATE TABLE IF NOT EXISTS `vehicle_assignments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tour_schedule_id` INT NOT NULL,
  `vehicle_id` INT NOT NULL,
  `driver_id` INT NOT NULL,
  `assignment_date` DATE NOT NULL COMMENT 'Ngày phân công',
  `start_date` DATE NOT NULL,
  `start_time` TIME NULL,
  `end_date` DATE NOT NULL,
  `end_time` TIME NULL,
  `pickup_location` VARCHAR(200) COMMENT 'Địa điểm đón xe',
  `return_location` VARCHAR(200) COMMENT 'Địa điểm trả xe',
  `estimated_distance` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Quãng đường dự kiến (km)',
  `actual_distance` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Quãng đường thực tế (km)',
  `estimated_fuel_cost` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Chi phí nhiên liệu dự kiến',
  `actual_fuel_cost` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Chi phí nhiên liệu thực tế',
  `driver_salary` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Phụ cấp tour cho tài xế (tự động từ tour_allowance_rules)',
  `total_cost` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Tổng chi phí',
  `status` ENUM('assigned','confirmed','in_use','completed','cancelled') DEFAULT 'assigned',
  `notes` TEXT,
  `assigned_by` INT NULL COMMENT 'Ai phân công',
  `confirmed_by` INT NULL COMMENT 'Ai xác nhận',
  `confirmed_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_schedule_id` (`tour_schedule_id`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `driver_id` (`driver_id`),
  KEY `assigned_by` (`assigned_by`),
  KEY `confirmed_by` (`confirmed_by`),
  KEY `idx_vehicle_assignments_status` (`status`),
  CONSTRAINT `vehicle_assignments_ibfk_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vehicle_assignments_ibfk_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`),
  CONSTRAINT `vehicle_assignments_ibfk_driver` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`),
  CONSTRAINT `vehicle_assignments_ibfk_assigned_by` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `vehicle_assignments_ibfk_confirmed_by` FOREIGN KEY (`confirmed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Driver Schedules (MỚI - Tránh trùng lịch)
CREATE TABLE IF NOT EXISTS `driver_schedules` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `driver_id` INT NOT NULL,
  `tour_schedule_id` INT NOT NULL,
  `vehicle_assignment_id` INT NULL,
  `schedule_date` DATE NOT NULL COMMENT 'Ngày làm việc',
  `start_time` TIME NULL,
  `end_time` TIME NULL,
  `work_hours` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Số giờ làm việc',
  `overtime_hours` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Số giờ làm thêm',
  `status` ENUM('scheduled','confirmed','in_progress','completed','cancelled') DEFAULT 'scheduled',
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `driver_id` (`driver_id`),
  KEY `tour_schedule_id` (`tour_schedule_id`),
  KEY `vehicle_assignment_id` (`vehicle_assignment_id`),
  KEY `schedule_date` (`schedule_date`),
  KEY `idx_driver_schedules_status` (`status`),
  CONSTRAINT `driver_schedules_ibfk_driver` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `driver_schedules_ibfk_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `driver_schedules_ibfk_assignment` FOREIGN KEY (`vehicle_assignment_id`) REFERENCES `vehicle_assignments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vehicle Maintenance (MỚI)
CREATE TABLE IF NOT EXISTS `vehicle_maintenance` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `vehicle_id` INT NOT NULL,
  `maintenance_type` ENUM('routine','repair','inspection','emergency') NOT NULL,
  `maintenance_date` DATE NOT NULL,
  `maintenance_provider` VARCHAR(200) COMMENT 'Nơi bảo dưỡng (công ty, địa chỉ)',
  `description` TEXT COMMENT 'Mô tả công việc',
  `cost` DECIMAL(15,2) DEFAULT 0.00,
  `mileage_before` INT DEFAULT 0 COMMENT 'Số km trước bảo dưỡng',
  `mileage_after` INT DEFAULT 0 COMMENT 'Số km sau bảo dưỡng',
  `next_maintenance_date` DATE NULL COMMENT 'Ngày bảo dưỡng tiếp theo',
  `next_maintenance_mileage` INT NULL COMMENT 'Số km bảo dưỡng tiếp theo',
  `status` ENUM('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
  `performed_by` VARCHAR(200) COMMENT 'Người thực hiện (có thể là user hoặc text)',
  `receipt_file` VARCHAR(255) COMMENT 'File hóa đơn',
  `notes` TEXT,
  `created_by` INT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `maintenance_date` (`maintenance_date`),
  KEY `idx_vehicle_maintenance_status` (`status`),
  CONSTRAINT `vehicle_maintenance_ibfk_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vehicle_maintenance_ibfk_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vehicle Assignment History (MỚI)
CREATE TABLE IF NOT EXISTS `vehicle_assignment_history` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `vehicle_assignment_id` INT NOT NULL,
  `action` ENUM('created','updated','vehicle_changed','driver_changed','status_changed','cancelled') NOT NULL,
  `old_values` JSON COMMENT 'Snapshot giá trị cũ',
  `new_values` JSON COMMENT 'Snapshot giá trị mới',
  `changed_by` INT NULL,
  `reason` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vehicle_assignment_id` (`vehicle_assignment_id`),
  KEY `changed_by` (`changed_by`),
  CONSTRAINT `vehicle_assignment_history_ibfk_assignment` FOREIGN KEY (`vehicle_assignment_id`) REFERENCES `vehicle_assignments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vehicle_assignment_history_ibfk_changed_by` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

