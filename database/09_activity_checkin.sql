-- ==============================================================================
-- MODULE 9: ACTIVITY CHECK-IN (MỚI)
-- ==============================================================================
-- Tính năng: Check-in chi tiết theo hoạt động tour
-- - Lên xe (boarding)
-- - Ăn (meal: breakfast, lunch, dinner, snack)
-- - Ngủ (accommodation: check_in, check_out)
-- - Di chuyển (transfer)
-- - Hoạt động (activity)
-- Check-in qua web, không cần GPS, không real-time

-- Activity Checkpoint Templates
CREATE TABLE IF NOT EXISTS `activity_checkpoint_templates` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tour_id` INT NOT NULL,
  `checkpoint_code` VARCHAR(50) NOT NULL COMMENT 'Mã checkpoint (VD: "BOARDING_1", "MEAL_LUNCH_DAY2")',
  `checkpoint_name` VARCHAR(200) NOT NULL COMMENT 'Tên checkpoint',
  `checkpoint_type` ENUM('boarding','meal','accommodation','transfer','activity') NOT NULL,
  `meal_type` ENUM('breakfast','lunch','dinner','snack') NULL COMMENT 'Nếu type=meal',
  `accommodation_type` ENUM('check_in','check_out') NULL COMMENT 'Nếu type=accommodation',
  `scheduled_time` TIME NULL COMMENT 'Thời gian dự kiến',
  `scheduled_date_offset` INT DEFAULT 0 COMMENT 'Ngày thứ mấy trong tour (0=ngày đầu)',
  `location_name` VARCHAR(200) COMMENT 'Địa điểm',
  `location_address` TEXT COMMENT 'Địa chỉ chi tiết',
  `is_required` TINYINT(1) DEFAULT 1 COMMENT 'Bắt buộc phải check-in không',
  `estimated_duration` INT COMMENT 'Thời gian dự kiến hoạt động (phút)',
  `display_order` INT DEFAULT 0,
  `status` ENUM('active','inactive') DEFAULT 'active',
  `notes` TEXT COMMENT 'Ghi chú chung về checkpoint',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  KEY `idx_checkpoint_templates_type` (`checkpoint_type`),
  CONSTRAINT `activity_checkpoint_templates_ibfk_tour` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity Checkpoints (Thực tế cho tour schedule)
CREATE TABLE IF NOT EXISTS `activity_checkpoints` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tour_schedule_id` INT NOT NULL,
  `template_id` INT NULL COMMENT 'Copy từ template nào',
  `checkpoint_code` VARCHAR(50) NOT NULL,
  `checkpoint_name` VARCHAR(200) NOT NULL,
  `checkpoint_type` ENUM('boarding','meal','accommodation','transfer','activity') NOT NULL,
  `meal_type` ENUM('breakfast','lunch','dinner','snack') NULL,
  `accommodation_type` ENUM('check_in','check_out') NULL,
  `scheduled_date` DATE NOT NULL COMMENT 'Ngày check-in',
  `scheduled_time` TIME NULL COMMENT 'Thời gian dự kiến',
  `location_name` VARCHAR(200),
  `location_address` TEXT,
  `is_required` TINYINT(1) DEFAULT 1,
  `estimated_duration` INT,
  `display_order` INT DEFAULT 0,
  `status` ENUM('active','inactive') DEFAULT 'active',
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_schedule_id` (`tour_schedule_id`),
  KEY `template_id` (`template_id`),
  KEY `idx_checkpoints_type` (`checkpoint_type`),
  KEY `idx_checkpoints_date` (`scheduled_date`),
  CONSTRAINT `activity_checkpoints_ibfk_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_checkpoints_ibfk_template` FOREIGN KEY (`template_id`) REFERENCES `activity_checkpoint_templates` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity Checkins (Chi tiết từng khách)
CREATE TABLE IF NOT EXISTS `activity_checkins` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tour_schedule_id` INT NOT NULL,
  `activity_checkpoint_id` INT NOT NULL,
  `booking_customer_id` INT NOT NULL,
  `customer_id` INT NOT NULL COMMENT 'Snapshot',
  `booking_id` INT NOT NULL COMMENT 'Snapshot',
  `checkpoint_date` DATE NOT NULL,
  `scheduled_time` TIME NULL COMMENT 'Thời gian dự kiến',
  `actual_time` TIME NULL COMMENT 'Thời gian thực tế',
  `checkin_datetime` DATETIME NULL COMMENT 'Timestamp thực tế',
  `status` ENUM('present','absent','late','early','excused') DEFAULT 'present',
  `minutes_late` INT DEFAULT 0 COMMENT 'Số phút muộn (nếu late)',
  `minutes_early` INT DEFAULT 0 COMMENT 'Số phút sớm (nếu early)',
  `checked_by` INT NULL COMMENT 'HDV nào check-in',
  `notes` TEXT COMMENT 'Ghi chú của HDV về khách này',
  `excused_reason` TEXT COMMENT 'Lý do được miễn (nếu excused)',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_schedule_id` (`tour_schedule_id`),
  KEY `activity_checkpoint_id` (`activity_checkpoint_id`),
  KEY `booking_customer_id` (`booking_customer_id`),
  KEY `customer_id` (`customer_id`),
  KEY `booking_id` (`booking_id`),
  KEY `checked_by` (`checked_by`),
  KEY `idx_checkins_status` (`status`),
  CONSTRAINT `activity_checkins_ibfk_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_checkins_ibfk_checkpoint` FOREIGN KEY (`activity_checkpoint_id`) REFERENCES `activity_checkpoints` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_checkins_ibfk_booking_customer` FOREIGN KEY (`booking_customer_id`) REFERENCES `booking_customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_checkins_ibfk_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `activity_checkins_ibfk_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_checkins_ibfk_checked_by` FOREIGN KEY (`checked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity Checkin Summary (Tổng hợp checkpoint)
CREATE TABLE IF NOT EXISTS `activity_checkin_summary` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tour_schedule_id` INT NOT NULL,
  `activity_checkpoint_id` INT NOT NULL,
  `checkpoint_date` DATE NOT NULL,
  `scheduled_start_time` TIME NULL,
  `actual_start_time` TIME NULL,
  `scheduled_end_time` TIME NULL,
  `actual_end_time` TIME NULL,
  `total_customers` INT DEFAULT 0 COMMENT 'Tổng số khách cần check-in',
  `present_count` INT DEFAULT 0 COMMENT 'Số người có mặt',
  `absent_count` INT DEFAULT 0 COMMENT 'Số người vắng mặt',
  `late_count` INT DEFAULT 0 COMMENT 'Số người muộn',
  `early_count` INT DEFAULT 0 COMMENT 'Số người sớm',
  `excused_count` INT DEFAULT 0 COMMENT 'Số người được miễn',
  `average_late_minutes` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Trung bình số phút muộn',
  `started_by` INT NULL COMMENT 'HDV bắt đầu checkpoint',
  `completed_by` INT NULL COMMENT 'HDV hoàn thành checkpoint',
  `status` ENUM('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `notes` TEXT COMMENT 'Ghi chú tổng hợp về checkpoint này',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_schedule_id` (`tour_schedule_id`),
  KEY `activity_checkpoint_id` (`activity_checkpoint_id`),
  KEY `checkpoint_date` (`checkpoint_date`),
  KEY `started_by` (`started_by`),
  KEY `completed_by` (`completed_by`),
  CONSTRAINT `activity_checkin_summary_ibfk_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_checkin_summary_ibfk_checkpoint` FOREIGN KEY (`activity_checkpoint_id`) REFERENCES `activity_checkpoints` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_checkin_summary_ibfk_started_by` FOREIGN KEY (`started_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `activity_checkin_summary_ibfk_completed_by` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

