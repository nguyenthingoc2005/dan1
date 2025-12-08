-- ==============================================================================
-- MIGRATION: Tạo các bảng Room Assignment và Vehicle/Driver
-- ==============================================================================
-- Mục đích: Tạo các bảng cần thiết cho tính năng phân phòng và quản lý xe/tài xế
-- Ngày: 2024-12-XX
-- ==============================================================================

-- ==============================================================================
-- PHẦN 1: ROOM ASSIGNMENT TABLES
-- ==============================================================================

-- Room Assignments
CREATE TABLE IF NOT EXISTS `room_assignments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tour_schedule_id` INT NOT NULL,
  `itinerary_id` INT NOT NULL COMMENT 'Ngày nào (đêm nào)',
  `service_provider_id` INT NULL COMMENT 'Khách sạn',
  `room_number` VARCHAR(50) COMMENT 'Số phòng (VD: "201", "301A")',
  `room_type` ENUM('single','double','twin','triple','quad','family') NOT NULL,
  `max_capacity` INT NOT NULL COMMENT 'Số người tối đa',
  `actual_occupancy` INT NOT NULL COMMENT 'Số người thực tế',
  `check_in_date` DATE NOT NULL COMMENT 'Ngày check-in phòng này',
  `check_out_date` DATE NOT NULL COMMENT 'Ngày check-out',
  `status` ENUM('pending','assigned','confirmed','cancelled') DEFAULT 'pending',
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_schedule_id` (`tour_schedule_id`),
  KEY `itinerary_id` (`itinerary_id`),
  KEY `service_provider_id` (`service_provider_id`),
  KEY `idx_room_assignments_status` (`status`),
  CONSTRAINT `room_assignments_ibfk_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_assignments_ibfk_itinerary` FOREIGN KEY (`itinerary_id`) REFERENCES `itineraries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_assignments_ibfk_service_provider` FOREIGN KEY (`service_provider_id`) REFERENCES `service_providers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Room Assignment Customers
CREATE TABLE IF NOT EXISTS `room_assignment_customers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `room_assignment_id` INT NOT NULL,
  `booking_customer_id` INT NOT NULL,
  `customer_id` INT NOT NULL COMMENT 'Snapshot để dễ query',
  `booking_id` INT NOT NULL COMMENT 'Snapshot',
  `role` ENUM('primary','companion') DEFAULT 'companion' COMMENT 'Ai là người chính',
  `room_preference` VARCHAR(100) COMMENT 'Yêu cầu phòng (window, non_smoking, ground_floor)',
  `special_notes` TEXT COMMENT 'Dị ứng, yêu cầu đặc biệt',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `room_assignment_id` (`room_assignment_id`),
  KEY `booking_customer_id` (`booking_customer_id`),
  KEY `customer_id` (`customer_id`),
  KEY `booking_id` (`booking_id`),
  CONSTRAINT `room_assignment_customers_ibfk_room` FOREIGN KEY (`room_assignment_id`) REFERENCES `room_assignments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_assignment_customers_ibfk_booking_customer` FOREIGN KEY (`booking_customer_id`) REFERENCES `booking_customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_assignment_customers_ibfk_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `room_assignment_customers_ibfk_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Room Requests
CREATE TABLE IF NOT EXISTS `room_requests` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `booking_id` INT NOT NULL,
  `customer_id` INT NOT NULL COMMENT 'Khách yêu cầu',
  `request_type` ENUM('single_room','share_with','avoid_sharing_with') NOT NULL,
  `target_customer_id` INT NULL COMMENT 'Nếu share_with hoặc avoid_sharing_with',
  `target_customer_name` VARCHAR(100) COMMENT 'Snapshot - phòng khi customer bị xóa',
  `reason` TEXT COMMENT 'Lý do yêu cầu',
  `status` ENUM('pending','approved','rejected','fulfilled') DEFAULT 'pending',
  `single_room_supplement` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Phụ phí đơn phòng (cố định)',
  `handled_by` INT NULL COMMENT 'User xử lý',
  `handled_at` TIMESTAMP NULL,
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `customer_id` (`customer_id`),
  KEY `target_customer_id` (`target_customer_id`),
  KEY `handled_by` (`handled_by`),
  KEY `idx_room_requests_status` (`status`),
  CONSTRAINT `room_requests_ibfk_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_requests_ibfk_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `room_requests_ibfk_target_customer` FOREIGN KEY (`target_customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `room_requests_ibfk_handled_by` FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Room Assignment History
CREATE TABLE IF NOT EXISTS `room_assignment_history` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `room_assignment_id` INT NOT NULL,
  `action` ENUM('created','updated','customer_added','customer_removed','cancelled') NOT NULL,
  `old_values` JSON COMMENT 'Snapshot giá trị cũ',
  `new_values` JSON COMMENT 'Snapshot giá trị mới',
  `changed_by` INT NULL,
  `reason` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `room_assignment_id` (`room_assignment_id`),
  KEY `changed_by` (`changed_by`),
  CONSTRAINT `room_assignment_history_ibfk_room` FOREIGN KEY (`room_assignment_id`) REFERENCES `room_assignments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_assignment_history_ibfk_changed_by` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- PHẦN 2: VEHICLE & DRIVER TABLES
-- ==============================================================================

-- Vehicles (kiểm tra xem đã có chưa)
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

-- Drivers
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

-- Vehicle Assignments
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

-- Driver Schedules
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

-- Vehicle Maintenance
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

-- Vehicle Assignment History
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

-- ==============================================================================
-- KẾT THÚC MIGRATION
-- ==============================================================================

SELECT 'Migration completed successfully! All tables created.' AS status;

