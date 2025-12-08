-- ==============================================================================
-- MODULE 8: ROOM ASSIGNMENT (MỚI)
-- ==============================================================================
-- Tính năng: Phân phòng cho khách hàng trong tour
-- - Tự động phân phòng: nam/nam, nữ/nữ
-- - Yêu cầu đặc biệt: đơn phòng, cùng phòng với ai, tránh cùng phòng
-- - Phụ phí đơn phòng: cố định

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

