-- ==============================================================================
-- DATABASE MIGRATION - NEW FEATURES
-- ==============================================================================
-- 
-- Tổng hợp tất cả các thay đổi để thêm tính năng mới:
-- 1. Phân phòng (Room Assignment)
-- 2. Check-in chi tiết theo hoạt động (Activity Check-in)
-- 3. Quản lý xe và tài xế (Vehicle & Driver Management)
-- 4. Template chi phí cố định (Tour Cost Templates) - Phương án 3
-- 5. Tính phụ cấp tự động (Tour Allowance Rules)
-- 
-- Date: 2024-12-XX
-- ==============================================================================

USE `tour_managementss`;

-- ==============================================================================
-- PHẦN 1: SỬA ĐỔI BẢNG HIỆN CÓ
-- ==============================================================================

-- 1.1. Sửa bảng tours - Bỏ 4 cột fixed_cost, thay bằng 1 cột + template
ALTER TABLE `tours`
DROP COLUMN `fixed_cost_guide`,
DROP COLUMN `fixed_cost_management`,
DROP COLUMN `fixed_cost_marketing`,
DROP COLUMN `fixed_cost_other`,
ADD COLUMN `fixed_cost_total` DECIMAL(15,2) DEFAULT 0.00 
  COMMENT 'Tổng chi phí cố định (tự động từ template hoặc nhập thủ công)',
ADD COLUMN `tour_cost_template_id` INT NULL 
  COMMENT 'Template chi phí (nếu có)',
ADD COLUMN `use_template_cost` TINYINT(1) DEFAULT 1 
  COMMENT '1 = dùng từ template, 0 = nhập thủ công',
ADD KEY `tour_cost_template_id` (`tour_cost_template_id`);

-- 1.2. Sửa bảng tour_schedules - Thêm status 'confirmed'
ALTER TABLE `tour_schedules`
MODIFY COLUMN `status` ENUM('open','closed','pending','confirmed','in_progress','completed','cancelled') 
  NOT NULL DEFAULT 'open';

-- 1.3. Sửa bảng incurred_expenses - Thêm tour_schedule_id, booking_id có thể NULL
ALTER TABLE `incurred_expenses`
MODIFY COLUMN `booking_id` INT NULL COMMENT 'Có thể NULL nếu chi phí theo tour',
ADD COLUMN `tour_schedule_id` INT NULL AFTER `booking_id`,
ADD KEY `tour_schedule_id` (`tour_schedule_id`),
ADD CONSTRAINT `incurred_expenses_ibfk_tour_schedule` 
  FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE;

-- 1.4. Sửa bảng tour_assignments - Đảm bảo tour_schedule_id NOT NULL
ALTER TABLE `tour_assignments`
MODIFY COLUMN `tour_schedule_id` INT NOT NULL COMMENT 'Bắt buộc - tour schedule nào',
ADD CONSTRAINT `tour_assignments_ibfk_schedule` 
  FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE;

-- 1.5. Sửa bảng service_provider_payments - Thêm tour_schedule_id
ALTER TABLE `service_provider_payments`
ADD COLUMN `tour_schedule_id` INT NULL AFTER `booking_id`,
ADD KEY `tour_schedule_id` (`tour_schedule_id`),
ADD CONSTRAINT `service_provider_payments_ibfk_tour_schedule` 
  FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE;

-- ==============================================================================
-- PHẦN 2: TẠO BẢNG MỚI - TEMPLATE & PHỤ CẤP
-- ==============================================================================

-- 2.1. Tour Cost Templates (Phương án 3 - Template đơn giản)
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
  FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm FK constraint cho tours sau khi tạo bảng tour_cost_templates
ALTER TABLE `tours`
ADD CONSTRAINT `tours_ibfk_cost_template` 
  FOREIGN KEY (`tour_cost_template_id`) REFERENCES `tour_cost_templates` (`id`) ON DELETE SET NULL;

-- 2.2. Tour Allowance Rules (Tính phụ cấp tự động)
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

-- ==============================================================================
-- PHẦN 3: TẠO BẢNG MỚI - PHÂN PHÒNG (ROOM ASSIGNMENT)
-- ==============================================================================

-- 3.1. Room Assignments
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
  FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`itinerary_id`) REFERENCES `itineraries` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`service_provider_id`) REFERENCES `service_providers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.2. Room Assignment Customers
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
  FOREIGN KEY (`room_assignment_id`) REFERENCES `room_assignments` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`booking_customer_id`) REFERENCES `booking_customers` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.3. Room Requests
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
  FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  FOREIGN KEY (`target_customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.4. Room Assignment History
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
  FOREIGN KEY (`room_assignment_id`) REFERENCES `room_assignments` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- PHẦN 4: TẠO BẢNG MỚI - CHECK-IN CHI TIẾT (ACTIVITY CHECK-IN)
-- ==============================================================================

-- 4.1. Activity Checkpoint Templates
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
  FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.2. Activity Checkpoints (Thực tế cho tour schedule)
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
  FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`template_id`) REFERENCES `activity_checkpoint_templates` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.3. Activity Checkins (Chi tiết từng khách)
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
  FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`activity_checkpoint_id`) REFERENCES `activity_checkpoints` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`booking_customer_id`) REFERENCES `booking_customers` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`checked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.4. Activity Checkin Summary (Tổng hợp checkpoint)
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
  FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`activity_checkpoint_id`) REFERENCES `activity_checkpoints` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`started_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- PHẦN 5: TẠO BẢNG MỚI - QUẢN LÝ XE VÀ TÀI XẾ
-- ==============================================================================

-- 5.1. Drivers
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

-- 5.2. Vehicle Assignments
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
  FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`),
  FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`),
  FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`confirmed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5.3. Driver Schedules (Tránh trùng lịch)
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
  FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`vehicle_assignment_id`) REFERENCES `vehicle_assignments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5.4. Vehicle Maintenance
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
  FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5.5. Vehicle Assignment History
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
  FOREIGN KEY (`vehicle_assignment_id`) REFERENCES `vehicle_assignments` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- PHẦN 6: INSERT DỮ LIỆU MẪU (OPTIONAL)
-- ==============================================================================

-- 6.1. Insert mẫu Tour Cost Templates
INSERT INTO `tour_cost_templates` (`template_name`, `description`, `fixed_cost_total`, `is_default`, `status`) VALUES
('Tour trong nước 3 ngày', 'Template cho tour trong nước ngắn ngày', 2000000, 1, 'active'),
('Tour trong nước 5-7 ngày', 'Template cho tour trong nước dài ngày', 3000000, 0, 'active'),
('Tour quốc tế', 'Template cho tour quốc tế', 5000000, 0, 'active');

-- 6.2. Insert mẫu Tour Allowance Rules
INSERT INTO `tour_allowance_rules` 
(`rule_name`, `tour_type`, `duration_days_min`, `duration_days_max`, `participant_min`, `participant_max`, `guide_allowance`, `driver_allowance`, `priority`, `status`) VALUES
('Tour public 1-3 ngày, 15-20 khách', 'public', 1, 3, 15, 20, 1000000, 500000, 10, 'active'),
('Tour public 1-3 ngày, 21-30 khách', 'public', 1, 3, 21, 30, 1500000, 800000, 10, 'active'),
('Tour public 1-3 ngày, 31-45 khách', 'public', 1, 3, 31, 45, 2000000, 1000000, 10, 'active'),
('Tour public 4-7 ngày, 15-20 khách', 'public', 4, 7, 15, 20, 2000000, 1000000, 10, 'active'),
('Tour public 4-7 ngày, 21-30 khách', 'public', 4, 7, 21, 30, 2500000, 1200000, 10, 'active'),
('Tour custom', 'custom', NULL, NULL, NULL, NULL, 3000000, 1500000, 5, 'active');

-- ==============================================================================
-- TỔNG KẾT
-- ==============================================================================
-- 
-- Tổng số bảng mới: 15 bảng
-- - tour_cost_templates (1)
-- - tour_allowance_rules (1)
-- - room_assignments, room_assignment_customers, room_requests, room_assignment_history (4)
-- - activity_checkpoint_templates, activity_checkpoints, activity_checkins, activity_checkin_summary (4)
-- - drivers, vehicle_assignments, driver_schedules, vehicle_maintenance, vehicle_assignment_history (5)
-- 
-- Tổng số bảng sửa: 5 bảng
-- - tours
-- - tour_schedules
-- - incurred_expenses
-- - tour_assignments
-- - service_provider_payments
-- 
-- Tổng số bảng sau migration: 38 + 15 = 53 bảng
-- ==============================================================================


