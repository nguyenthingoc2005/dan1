-- --------------------------------------------------------
-- Máy chủ:                      127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Phiên bản:           12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for duan
CREATE DATABASE IF NOT EXISTS `duan` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `duan`;

-- Dumping structure for table duan.activity_checkins
CREATE TABLE IF NOT EXISTS `activity_checkins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_schedule_id` int NOT NULL,
  `activity_checkpoint_id` int NOT NULL,
  `booking_customer_id` int NOT NULL,
  `customer_id` int NOT NULL COMMENT 'Snapshot',
  `booking_id` int NOT NULL COMMENT 'Snapshot',
  `checkpoint_date` date NOT NULL,
  `scheduled_time` time DEFAULT NULL COMMENT 'Thời gian dự kiến',
  `actual_time` time DEFAULT NULL COMMENT 'Thời gian thực tế',
  `checkin_datetime` datetime DEFAULT NULL COMMENT 'Timestamp thực tế',
  `status` enum('present','absent','late','early','excused') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'present',
  `minutes_late` int DEFAULT '0' COMMENT 'Số phút muộn (nếu late)',
  `minutes_early` int DEFAULT '0' COMMENT 'Số phút sớm (nếu early)',
  `checked_by` int DEFAULT NULL COMMENT 'HDV nào check-in',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú của HDV về khách này',
  `excused_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Lý do được miễn (nếu excused)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_checkpoint_customer` (`activity_checkpoint_id`,`booking_customer_id`),
  KEY `tour_schedule_id` (`tour_schedule_id`),
  KEY `activity_checkpoint_id` (`activity_checkpoint_id`),
  KEY `booking_customer_id` (`booking_customer_id`),
  KEY `customer_id` (`customer_id`),
  KEY `booking_id` (`booking_id`),
  KEY `checked_by` (`checked_by`),
  KEY `idx_checkins_status` (`status`),
  KEY `idx_checkins_date` (`checkpoint_date`),
  CONSTRAINT `activity_checkins_ibfk_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_checkins_ibfk_booking_customer` FOREIGN KEY (`booking_customer_id`) REFERENCES `booking_customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_checkins_ibfk_checked_by` FOREIGN KEY (`checked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `activity_checkins_ibfk_checkpoint` FOREIGN KEY (`activity_checkpoint_id`) REFERENCES `activity_checkpoints` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_checkins_ibfk_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `activity_checkins_ibfk_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.activity_checkins: ~0 rows (approximately)
INSERT INTO `activity_checkins` (`id`, `tour_schedule_id`, `activity_checkpoint_id`, `booking_customer_id`, `customer_id`, `booking_id`, `checkpoint_date`, `scheduled_time`, `actual_time`, `checkin_datetime`, `status`, `minutes_late`, `minutes_early`, `checked_by`, `notes`, `excused_reason`, `created_at`, `updated_at`) VALUES
	(61, 1, 1, 226, 1, 1, '2025-10-03', '06:30:00', '06:25:00', '2025-10-03 06:25:00', 'early', 0, 0, 5, 'Đến sớm 5 phút', NULL, '2025-10-02 23:25:00', '2025-12-08 19:47:32'),
	(62, 1, 1, 227, 2, 2, '2025-10-03', '06:30:00', '06:28:00', '2025-10-03 06:28:00', 'early', 0, 0, 5, NULL, NULL, '2025-10-02 23:28:00', '2025-12-08 19:47:32'),
	(63, 1, 1, 258, 3, 33, '2025-10-03', '06:30:00', '06:35:00', '2025-10-03 06:35:00', 'late', 5, 0, 5, 'Muộn 5 phút do kẹt xe', NULL, '2025-10-02 23:35:00', '2025-12-08 19:47:32'),
	(64, 1, 1, 259, 7, 34, '2025-10-03', '06:30:00', '06:30:00', '2025-10-03 06:30:00', 'present', 0, 0, 5, NULL, NULL, '2025-10-02 23:30:00', '2025-12-08 19:47:32'),
	(65, 1, 2, 226, 1, 1, '2025-10-03', '12:00:00', '12:00:00', '2025-10-03 12:00:00', 'present', 0, 0, 5, NULL, NULL, '2025-10-03 05:00:00', '2025-12-08 19:47:32'),
	(66, 1, 2, 227, 2, 2, '2025-10-03', '12:00:00', '12:00:00', '2025-10-03 12:00:00', 'present', 0, 0, 5, NULL, NULL, '2025-10-03 05:00:00', '2025-12-08 19:47:32'),
	(67, 1, 2, 258, 3, 33, '2025-10-03', '12:00:00', '12:05:00', '2025-10-03 12:05:00', 'late', 0, 0, 5, NULL, NULL, '2025-10-03 05:05:00', '2025-12-08 19:47:32'),
	(68, 1, 3, 226, 1, 1, '2025-10-03', '13:30:00', '13:30:00', '2025-10-03 13:30:00', 'present', 0, 0, 5, NULL, NULL, '2025-10-03 06:30:00', '2025-12-08 19:47:32'),
	(69, 1, 3, 227, 2, 2, '2025-10-03', '13:30:00', '13:28:00', '2025-10-03 13:28:00', 'early', 0, 0, 5, 'Lên thuyền sớm', NULL, '2025-10-03 06:28:00', '2025-12-08 19:47:32'),
	(70, 1, 3, 258, 3, 33, '2025-10-03', '13:30:00', '13:35:00', '2025-10-03 13:35:00', 'late', 0, 0, 5, NULL, NULL, '2025-10-03 06:35:00', '2025-12-08 19:47:32'),
	(71, 1, 3, 259, 7, 34, '2025-10-03', '13:30:00', '13:30:00', '2025-10-03 13:30:00', 'present', 0, 0, 5, NULL, NULL, '2025-10-03 06:30:00', '2025-12-08 19:47:32'),
	(87, 2, 36, 228, 5, 3, '2025-10-05', '10:00:00', '10:05:00', '2025-10-05 10:05:00', 'late', 0, 0, 6, NULL, NULL, '2025-10-05 03:05:00', '2025-12-08 19:53:09'),
	(88, 2, 36, 260, 11, 35, '2025-10-05', '10:00:00', '09:55:00', '2025-10-05 09:55:00', 'early', 0, 0, 6, NULL, NULL, '2025-10-05 02:55:00', '2025-12-08 19:53:09');

-- Dumping structure for table duan.activity_checkin_summary
CREATE TABLE IF NOT EXISTS `activity_checkin_summary` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_schedule_id` int NOT NULL,
  `activity_checkpoint_id` int NOT NULL,
  `checkpoint_date` date NOT NULL,
  `scheduled_start_time` time DEFAULT NULL,
  `actual_start_time` time DEFAULT NULL,
  `scheduled_end_time` time DEFAULT NULL,
  `actual_end_time` time DEFAULT NULL,
  `total_customers` int DEFAULT '0' COMMENT 'Tổng số khách cần check-in',
  `present_count` int DEFAULT '0' COMMENT 'Số người có mặt',
  `absent_count` int DEFAULT '0' COMMENT 'Số người vắng mặt',
  `late_count` int DEFAULT '0' COMMENT 'Số người muộn',
  `early_count` int DEFAULT '0' COMMENT 'Số người sớm',
  `excused_count` int DEFAULT '0' COMMENT 'Số người được miễn',
  `average_late_minutes` decimal(10,2) DEFAULT '0.00' COMMENT 'Trung bình số phút muộn',
  `started_by` int DEFAULT NULL COMMENT 'HDV bắt đầu checkpoint',
  `completed_by` int DEFAULT NULL COMMENT 'HDV hoàn thành checkpoint',
  `status` enum('pending','in_progress','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú tổng hợp về checkpoint này',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_checkpoint_summary` (`activity_checkpoint_id`,`checkpoint_date`),
  KEY `tour_schedule_id` (`tour_schedule_id`),
  KEY `activity_checkpoint_id` (`activity_checkpoint_id`),
  KEY `checkpoint_date` (`checkpoint_date`),
  KEY `started_by` (`started_by`),
  KEY `completed_by` (`completed_by`),
  CONSTRAINT `activity_checkin_summary_ibfk_checkpoint` FOREIGN KEY (`activity_checkpoint_id`) REFERENCES `activity_checkpoints` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_checkin_summary_ibfk_completed_by` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `activity_checkin_summary_ibfk_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activity_checkin_summary_ibfk_started_by` FOREIGN KEY (`started_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.activity_checkin_summary: ~11 rows (approximately)
INSERT INTO `activity_checkin_summary` (`id`, `tour_schedule_id`, `activity_checkpoint_id`, `checkpoint_date`, `scheduled_start_time`, `actual_start_time`, `scheduled_end_time`, `actual_end_time`, `total_customers`, `present_count`, `absent_count`, `late_count`, `early_count`, `excused_count`, `average_late_minutes`, `started_by`, `completed_by`, `status`, `notes`, `created_at`, `updated_at`) VALUES
	(2, 1, 1, '2025-10-03', '06:30:00', '06:25:00', '07:00:00', '06:40:00', 28, 26, 0, 1, 2, 0, 5.00, 5, 5, 'completed', 'Tập trung khá đúng giờ, chỉ 1 gia đình muộn 5 phút', '2025-10-02 23:45:00', '2025-12-08 19:53:21'),
	(3, 1, 2, '2025-10-03', '12:00:00', '12:00:00', NULL, NULL, 28, 27, 0, 1, 0, 0, 5.00, 5, 5, 'completed', 'Bữa trưa buffet hải sản, khách hài lòng', '2025-10-03 06:00:00', '2025-12-08 19:53:21'),
	(4, 1, 3, '2025-10-03', '13:30:00', '13:28:00', '16:30:00', '16:35:00', 28, 27, 0, 1, 1, 0, 5.00, 5, 5, 'completed', 'Du ngoạn vịnh Hạ Long rất đẹp, khách thích chụp ảnh', '2025-10-03 09:40:00', '2025-12-08 19:53:21'),
	(5, 1, 4, '2025-10-03', '18:00:00', '18:05:00', NULL, NULL, 28, 28, 0, 0, 0, 0, 0.00, 5, 5, 'completed', 'Check-in khách sạn suôn sẻ, phòng đẹp', '2025-10-03 11:30:00', '2025-12-08 19:53:21'),
	(6, 1, 5, '2025-10-04', '07:00:00', NULL, NULL, NULL, 28, 28, 0, 0, 0, 0, 0.00, 5, NULL, 'completed', NULL, '2025-10-04 00:45:00', '2025-12-08 19:53:21'),
	(7, 1, 6, '2025-10-04', '08:00:00', NULL, NULL, NULL, 28, 28, 0, 0, 0, 0, 0.00, 5, NULL, 'completed', NULL, '2025-10-04 01:15:00', '2025-12-08 19:53:21'),
	(8, 1, 7, '2025-10-04', '10:30:00', NULL, NULL, NULL, 28, 27, 0, 0, 0, 0, 0.00, 5, NULL, 'completed', NULL, '2025-10-04 05:30:00', '2025-12-08 19:53:21'),
	(9, 1, 8, '2025-10-04', '13:00:00', NULL, NULL, NULL, 28, 28, 0, 0, 0, 0, 0.00, 5, NULL, 'completed', NULL, '2025-10-04 07:00:00', '2025-12-08 19:53:21'),
	(10, 1, 9, '2025-10-04', '18:30:00', NULL, NULL, NULL, 28, 28, 0, 0, 0, 0, 0.00, 5, NULL, 'completed', NULL, '2025-10-04 12:00:00', '2025-12-08 19:53:21'),
	(11, 1, 10, '2025-10-05', '08:00:00', NULL, NULL, NULL, 28, 28, 0, 0, 0, 0, 0.00, 5, NULL, 'completed', NULL, '2025-10-05 01:20:00', '2025-12-08 19:53:21'),
	(12, 1, 11, '2025-10-05', '11:00:00', NULL, NULL, NULL, 28, 28, 0, 0, 0, 0, 0.00, 5, NULL, 'completed', NULL, '2025-10-05 04:15:00', '2025-12-08 19:53:21'),
	(17, 2, 36, '2025-10-05', '10:00:00', '09:55:00', NULL, NULL, 22, 20, 0, 1, 1, 0, 0.00, 6, NULL, 'completed', 'Đón đoàn tại sân bay, 2 khách bay chuyến sau', '2025-10-05 04:00:00', '2025-12-08 20:00:35'),
	(18, 2, 37, '2025-10-05', '12:00:00', NULL, NULL, NULL, 22, 22, 0, 0, 0, 0, 0.00, 6, NULL, 'completed', NULL, '2025-10-05 05:30:00', '2025-12-08 20:00:35'),
	(19, 2, 38, '2025-10-05', '14:00:00', NULL, NULL, NULL, 22, 22, 0, 0, 0, 0, 0.00, 6, NULL, 'completed', NULL, '2025-10-05 07:15:00', '2025-12-08 20:00:35'),
	(20, 2, 39, '2025-10-05', '15:00:00', NULL, NULL, NULL, 22, 22, 0, 0, 0, 0, 0.00, 6, NULL, 'completed', NULL, '2025-10-05 12:00:00', '2025-12-08 20:00:35'),
	(21, 3, 44, '2025-10-10', '06:30:00', NULL, NULL, NULL, 30, 30, 0, 0, 0, 0, 0.00, 7, NULL, 'completed', NULL, '2025-10-10 00:00:00', '2025-12-08 20:00:35'),
	(22, 3, 45, '2025-10-10', '12:00:00', NULL, NULL, NULL, 30, 30, 0, 0, 0, 0, 0.00, 7, NULL, 'completed', NULL, '2025-10-10 06:00:00', '2025-12-08 20:00:35'),
	(23, 3, 46, '2025-10-10', '13:30:00', NULL, NULL, NULL, 30, 29, 0, 0, 0, 0, 0.00, 7, NULL, 'completed', NULL, '2025-10-10 09:30:00', '2025-12-08 20:00:35'),
	(24, 3, 47, '2025-10-10', '18:00:00', NULL, NULL, NULL, 30, 30, 0, 0, 0, 0, 0.00, 7, NULL, 'completed', NULL, '2025-10-10 11:30:00', '2025-12-08 20:00:35');

-- Dumping structure for table duan.activity_checkpoints
CREATE TABLE IF NOT EXISTS `activity_checkpoints` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_schedule_id` int NOT NULL,
  `checkpoint_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã checkpoint (VD: "BOARDING_1", "MEAL_LUNCH_DAY2")',
  `checkpoint_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên checkpoint',
  `checkpoint_type` enum('boarding','meal','accommodation','transfer','activity') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `meal_type` enum('breakfast','lunch','dinner','snack') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nếu type=meal',
  `accommodation_type` enum('check_in','check_out') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nếu type=accommodation',
  `scheduled_date` date NOT NULL COMMENT 'Ngày check-in',
  `scheduled_time` time DEFAULT NULL COMMENT 'Thời gian dự kiến',
  `location_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Địa điểm',
  `location_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Địa chỉ chi tiết',
  `is_required` tinyint(1) DEFAULT '1' COMMENT 'Bắt buộc phải check-in không',
  `estimated_duration` int DEFAULT NULL COMMENT 'Thời gian dự kiến hoạt động (phút)',
  `display_order` int DEFAULT '0',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú chung về checkpoint',
  `created_by` int DEFAULT NULL COMMENT 'HDV tạo checkpoint',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_schedule_id` (`tour_schedule_id`),
  KEY `created_by` (`created_by`),
  KEY `idx_checkpoints_type` (`checkpoint_type`),
  KEY `idx_checkpoints_date` (`scheduled_date`),
  KEY `idx_checkpoints_schedule_date` (`tour_schedule_id`,`scheduled_date`),
  CONSTRAINT `activity_checkpoints_ibfk_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `activity_checkpoints_ibfk_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.activity_checkpoints: ~0 rows (approximately)
INSERT INTO `activity_checkpoints` (`id`, `tour_schedule_id`, `checkpoint_code`, `checkpoint_name`, `checkpoint_type`, `meal_type`, `accommodation_type`, `scheduled_date`, `scheduled_time`, `location_name`, `location_address`, `is_required`, `estimated_duration`, `display_order`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
	(1, 1, 'BOARDING_DAY1', 'Tập trung khởi hành', 'boarding', NULL, NULL, '2025-10-03', '06:30:00', 'Điểm hẹn Hà Nội', '123 Lê Duẩn, Ba Đình, Hà Nội', 1, 30, 1, 'active', NULL, 5, '2025-09-28 03:00:00', '2025-12-08 19:39:42'),
	(2, 1, 'MEAL_LUNCH_DAY1', 'Bữa trưa ngày 1', 'meal', 'lunch', NULL, '2025-10-03', '12:00:00', 'Nhà hàng Hải sản Hạ Long', 'Bãi Cháy, Hạ Long', 1, 60, 2, 'active', NULL, 5, '2025-09-28 03:05:00', '2025-12-08 19:39:42'),
	(3, 1, 'ACTIVITY_CRUISE', 'Lên du thuyền tham quan', 'activity', NULL, NULL, '2025-10-03', '13:30:00', 'Bến tàu Hạ Long', 'Bãi Cháy, Hạ Long', 1, 180, 3, 'active', NULL, 5, '2025-09-28 03:10:00', '2025-12-08 19:39:42'),
	(4, 1, 'ACCOM_CHECKIN_DAY1', 'Check-in khách sạn', 'accommodation', NULL, NULL, '2025-10-03', '18:00:00', 'Vinpearl Hotel Hạ Long', 'Bãi Cháy, Hạ Long', 1, 30, 4, 'active', NULL, 5, '2025-09-28 03:15:00', '2025-12-08 19:39:42'),
	(5, 1, 'MEAL_BREAKFAST_DAY2', 'Bữa sáng ngày 2', 'meal', 'breakfast', NULL, '2025-10-04', '07:00:00', 'Vinpearl Hotel Hạ Long', 'Bãi Cháy, Hạ Long', 1, 45, 5, 'active', NULL, 5, '2025-09-28 03:20:00', '2025-12-08 19:39:42'),
	(6, 1, 'TRANSFER_NINHBINH', 'Khởi hành đi Ninh Bình', 'transfer', NULL, NULL, '2025-10-04', '08:00:00', 'Vinpearl Hotel Hạ Long', 'Bãi Cháy, Hạ Long', 1, 15, 6, 'active', NULL, 5, '2025-09-28 03:25:00', '2025-12-08 19:39:42'),
	(7, 1, 'ACTIVITY_TRANGANH', 'Chèo thuyền Tràng An', 'activity', NULL, NULL, '2025-10-04', '10:30:00', 'Khu du lịch Tràng An', 'Hoa Lư, Ninh Bình', 1, 120, 7, 'active', NULL, 5, '2025-09-28 03:30:00', '2025-12-08 19:39:42'),
	(8, 1, 'MEAL_LUNCH_DAY2', 'Bữa trưa ngày 2', 'meal', 'lunch', NULL, '2025-10-04', '13:00:00', 'Nhà hàng Ninh Bình', 'TT Ninh Bình', 1, 60, 8, 'active', NULL, 5, '2025-09-28 03:35:00', '2025-12-08 19:39:42'),
	(9, 1, 'ACCOM_CHECKIN_DAY2', 'Check-in khách sạn Ninh Bình', 'accommodation', NULL, NULL, '2025-10-04', '18:30:00', 'Khách sạn Ninh Bình', 'TP Ninh Bình', 1, 30, 9, 'active', NULL, 5, '2025-09-28 03:40:00', '2025-12-08 19:39:42'),
	(10, 1, 'ACCOM_CHECKOUT_DAY3', 'Check-out khách sạn', 'accommodation', NULL, NULL, '2025-10-05', '08:00:00', 'Khách sạn Ninh Bình', 'TP Ninh Bình', 1, 20, 10, 'active', NULL, 5, '2025-09-28 03:45:00', '2025-12-08 19:39:42'),
	(11, 1, 'TRANSFER_RETURN', 'Khởi hành về Hà Nội', 'transfer', NULL, NULL, '2025-10-05', '11:00:00', 'Ninh Bình', NULL, 1, 15, 11, 'active', NULL, 5, '2025-09-28 03:50:00', '2025-12-08 19:39:42'),
	(36, 2, 'BOARDING_AIRPORT', 'Đón tại sân bay Đà Nẵng', 'boarding', NULL, NULL, '2025-10-05', '10:00:00', 'Sân bay Đà Nẵng', NULL, 1, 60, 1, 'active', NULL, 6, '2025-10-01 02:00:00', '2025-12-08 19:39:54'),
	(37, 2, 'ACCOM_CHECKIN_DAY1', 'Check-in khách sạn', 'accommodation', NULL, NULL, '2025-10-05', '12:00:00', 'Mường Thanh Grand Đà Nẵng', NULL, 1, 30, 2, 'active', NULL, 6, '2025-10-01 02:05:00', '2025-12-08 19:39:54'),
	(38, 2, 'TRANSFER_BANA', 'Khởi hành đi Bà Nà Hills', 'transfer', NULL, NULL, '2025-10-05', '14:00:00', 'Mường Thanh Grand', NULL, 1, 15, 3, 'active', NULL, 6, '2025-10-01 02:10:00', '2025-12-08 19:39:54'),
	(39, 2, 'ACTIVITY_BANA', 'Tham quan Bà Nà Hills', 'activity', NULL, NULL, '2025-10-05', '15:00:00', 'Bà Nà Hills', NULL, 1, 240, 4, 'active', NULL, 6, '2025-10-01 02:15:00', '2025-12-08 19:39:54'),
	(40, 2, 'MEAL_BREAKFAST_DAY2', 'Bữa sáng ngày 2', 'meal', 'breakfast', NULL, '2025-10-06', '07:00:00', 'Mường Thanh Grand', NULL, 1, 45, 5, 'active', NULL, 6, '2025-10-01 02:20:00', '2025-12-08 19:39:54'),
	(41, 2, 'ACTIVITY_BEACH', 'Tắm biển Mỹ Khê', 'activity', NULL, NULL, '2025-10-06', '09:00:00', 'Bãi biển Mỹ Khê', NULL, 0, 180, 6, 'active', NULL, 6, '2025-10-01 02:25:00', '2025-12-08 19:39:54'),
	(42, 2, 'TRANSFER_HOIAN', 'Khởi hành đi Hội An', 'transfer', NULL, NULL, '2025-10-07', '08:00:00', 'Mường Thanh Grand', NULL, 1, 15, 7, 'active', NULL, 6, '2025-10-01 02:30:00', '2025-12-08 19:39:54'),
	(43, 2, 'ACTIVITY_HOIAN', 'Tham quan phố cổ Hội An', 'activity', NULL, NULL, '2025-10-07', '10:00:00', 'Phố cổ Hội An', NULL, 1, 180, 8, 'active', NULL, 6, '2025-10-01 02:35:00', '2025-12-08 19:39:54'),
	(44, 3, 'BOARDING_DAY1', 'Tập trung khởi hành', 'boarding', NULL, NULL, '2025-10-10', '06:30:00', 'Điểm hẹn Hà Nội', NULL, 1, 30, 1, 'active', NULL, 7, '2025-10-05 03:00:00', '2025-12-08 19:40:08'),
	(45, 3, 'MEAL_LUNCH_DAY1', 'Bữa trưa ngày 1', 'meal', NULL, NULL, '2025-10-10', '12:00:00', 'Hải sản Hạ Long', NULL, 1, 60, 2, 'active', NULL, 7, '2025-10-05 03:05:00', '2025-12-08 19:40:08'),
	(46, 3, 'ACTIVITY_CRUISE', 'Du ngoạn vịnh Hạ Long', 'activity', NULL, NULL, '2025-10-10', '13:30:00', 'Vịnh Hạ Long', NULL, 1, 180, 3, 'active', NULL, 7, '2025-10-05 03:10:00', '2025-12-08 19:40:08'),
	(47, 3, 'ACCOM_CHECKIN_DAY1', 'Check-in khách sạn', 'accommodation', NULL, NULL, '2025-10-10', '18:00:00', 'Vinpearl Hạ Long', NULL, 1, 30, 4, 'active', NULL, 7, '2025-10-05 03:15:00', '2025-12-08 19:40:08');

-- Dumping structure for table duan.bookings
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tour_id` int NOT NULL,
  `tour_schedule_id` int DEFAULT NULL COMMENT 'Foreign key → tour_schedules',
  `customer_id` int NOT NULL,
  `adult_count` int DEFAULT '0',
  `child_count` int DEFAULT '0',
  `infant_count` int DEFAULT '0',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `quota` int DEFAULT NULL,
  `booked_seats` int DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `discount_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_amount` decimal(15,2) DEFAULT '0.00',
  `final_amount` decimal(15,2) NOT NULL,
  `deposit_amount` decimal(15,2) DEFAULT '0.00',
  `paid_amount` decimal(15,2) DEFAULT '0.00',
  `remaining_amount` decimal(15,2) DEFAULT '0.00',
  `payment_status` enum('unpaid','partial','paid','rejected','cancelled','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'unpaid',
  `approved_by` int DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `rejected_by` int DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `cancellation_date` date DEFAULT NULL,
  `cancellation_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancellation_policy_id` int DEFAULT NULL,
  `cancellation_fee` decimal(15,2) DEFAULT '0.00',
  `refund_amount` decimal(15,2) DEFAULT '0.00',
  `source` enum('phone','email','facebook','zalo','walk_in','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `special_requests` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `internal_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_code` (`booking_code`),
  KEY `approved_by` (`approved_by`),
  KEY `cancellation_policy_id` (`cancellation_policy_id`),
  KEY `created_by` (`created_by`),
  KEY `tour_schedule_id` (`tour_schedule_id`),
  KEY `idx_bookings_payment_status` (`payment_status`),
  KEY `idx_bookings_tour_id` (`tour_id`),
  KEY `idx_bookings_customer_id` (`customer_id`),
  KEY `idx_bookings_start_date` (`start_date`),
  KEY `bookings_ibfk_rejected_by` (`rejected_by`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`),
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_ibfk_4` FOREIGN KEY (`cancellation_policy_id`) REFERENCES `cancellation_policies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_ibfk_rejected_by` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bookings_ibfk_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.bookings: ~0 rows (approximately)
INSERT INTO `bookings` (`id`, `booking_code`, `tour_id`, `tour_schedule_id`, `customer_id`, `adult_count`, `child_count`, `infant_count`, `start_date`, `end_date`, `quota`, `booked_seats`, `total_amount`, `discount_code`, `discount_amount`, `final_amount`, `deposit_amount`, `paid_amount`, `remaining_amount`, `payment_status`, `approved_by`, `approved_at`, `rejection_reason`, `rejected_by`, `rejected_at`, `cancellation_date`, `cancellation_reason`, `cancellation_policy_id`, `cancellation_fee`, `refund_amount`, `source`, `special_requests`, `notes`, `internal_notes`, `created_by`, `created_at`, `updated_at`) VALUES
	(1, 'BK-2025-0001', 1, 1, 1, 2, 1, 0, '2025-10-03', '2025-10-05', NULL, NULL, 12500000.00, NULL, 0.00, 12500000.00, 3750000.00, 12500000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'phone', NULL, 'Gia đình 3 người', NULL, 2, '2025-09-20 03:30:00', '2025-12-08 19:34:57'),
	(2, 'BK-2025-0002', 1, 1, 2, 2, 0, 0, '2025-10-03', '2025-10-05', NULL, NULL, 9000000.00, 'AUTUMN2025', 720000.00, 8280000.00, 2484000.00, 8280000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'facebook', NULL, NULL, NULL, 3, '2025-09-21 04:00:00', '2025-12-08 19:34:57'),
	(3, 'BK-2025-0003', 2, 2, 5, 2, 2, 0, '2025-10-05', '2025-10-08', NULL, NULL, 20600000.00, NULL, 0.00, 20600000.00, 6180000.00, 20600000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'walk_in', NULL, 'Honeymoon package', NULL, 2, '2025-09-22 07:20:00', '2025-12-08 19:34:57'),
	(4, 'BK-2025-0004', 1, 3, 8, 4, 2, 0, '2025-10-10', '2025-10-12', NULL, NULL, 25000000.00, 'GROUP10', 500000.00, 24500000.00, 7350000.00, 24500000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'facebook', NULL, 'Nhóm bạn 6 người', NULL, 3, '2025-09-23 02:15:00', '2025-12-08 19:34:57'),
	(5, 'BK-2025-0005', 3, 4, 12, 2, 1, 0, '2025-10-12', '2025-10-14', NULL, NULL, 8800000.00, NULL, 0.00, 8800000.00, 2640000.00, 8800000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'zalo', NULL, NULL, NULL, 2, '2025-09-25 09:00:00', '2025-12-08 19:34:57'),
	(6, 'BK-2025-0006', 3, 4, 15, 3, 0, 0, '2025-10-12', '2025-10-14', NULL, NULL, 9600000.00, NULL, 0.00, 9600000.00, 2880000.00, 9600000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'walk_in', NULL, 'Tour công ty', NULL, 3, '2025-09-26 03:30:00', '2025-12-08 19:34:57'),
	(7, 'BK-2025-0007', 2, 6, 18, 2, 1, 0, '2025-10-18', '2025-10-21', NULL, NULL, 16100000.00, NULL, 0.00, 16100000.00, 4830000.00, 16100000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'phone', NULL, NULL, NULL, 2, '2025-09-28 06:45:00', '2025-12-08 19:34:57'),
	(8, 'BK-2025-0008', 5, 7, 22, 2, 0, 0, '2025-10-20', '2025-10-24', NULL, NULL, 15000000.00, NULL, 0.00, 15000000.00, 4500000.00, 15000000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'facebook', NULL, 'Kỷ niệm 10 năm', NULL, 3, '2025-09-29 04:15:00', '2025-12-08 19:34:57'),
	(9, 'BK-2025-0009', 1, 8, 25, 3, 1, 0, '2025-10-24', '2025-10-26', NULL, NULL, 17000000.00, NULL, 0.00, 17000000.00, 5100000.00, 17000000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'zalo', NULL, NULL, NULL, 2, '2025-10-01 02:00:00', '2025-12-08 19:34:57'),
	(10, 'BK-2025-0010', 6, 9, 30, 2, 0, 0, '2025-10-26', '2025-10-30', NULL, NULL, 25000000.00, NULL, 0.00, 25000000.00, 7500000.00, 25000000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'phone', NULL, 'Bangkok tour', NULL, 3, '2025-10-02 08:30:00', '2025-12-08 19:34:57'),
	(11, 'BK-2025-0011', 6, 9, 33, 4, 2, 0, '2025-10-26', '2025-10-30', NULL, NULL, 69000000.00, 'LOYAL100', 1000000.00, 68000000.00, 20400000.00, 68000000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'email', NULL, 'Gia đình lớn', NULL, 2, '2025-10-03 03:00:00', '2025-12-08 19:34:57'),
	(12, 'BK-2025-0012', 4, 10, 36, 2, 1, 0, '2025-10-28', '2025-10-31', NULL, NULL, 13500000.00, NULL, 0.00, 13500000.00, 4050000.00, 13500000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'facebook', NULL, NULL, NULL, 3, '2025-10-05 07:20:00', '2025-12-08 19:34:57'),
	(13, 'BK-2025-0013', 1, 11, 40, 2, 0, 0, '2025-11-01', '2025-11-03', NULL, NULL, 9000000.00, NULL, 0.00, 9000000.00, 2700000.00, 9000000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'zalo', NULL, NULL, NULL, 2, '2025-10-08 04:00:00', '2025-12-08 19:34:57'),
	(14, 'BK-2025-0014', 2, 12, 42, 2, 2, 0, '2025-11-03', '2025-11-06', NULL, NULL, 20600000.00, NULL, 0.00, 20600000.00, 6180000.00, 20600000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'phone', NULL, NULL, NULL, 3, '2025-10-10 02:30:00', '2025-12-08 19:34:57'),
	(15, 'BK-2025-0015', 3, 13, 45, 6, 0, 0, '2025-11-07', '2025-11-09', NULL, NULL, 19200000.00, 'GROUP10', 500000.00, 18700000.00, 5610000.00, 18700000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'walk_in', NULL, 'Nhóm đồng nghiệp', NULL, 2, '2025-10-12 09:00:00', '2025-12-08 19:34:57'),
	(16, 'BK-2025-0016', 5, 14, 48, 2, 0, 0, '2025-11-09', '2025-11-13', NULL, NULL, 15000000.00, NULL, 0.00, 15000000.00, 4500000.00, 15000000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'email', NULL, NULL, NULL, 3, '2025-10-15 03:45:00', '2025-12-08 19:34:57'),
	(17, 'BK-2025-0017', 1, 15, 50, 3, 2, 0, '2025-11-14', '2025-11-16', NULL, NULL, 20500000.00, NULL, 0.00, 20500000.00, 6150000.00, 11000000.00, 9500000.00, 'partial', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'facebook', NULL, NULL, NULL, 2, '2025-10-20 06:30:00', '2025-12-08 19:34:57'),
	(18, 'BK-2025-0018', 2, 16, 52, 2, 1, 0, '2025-11-16', '2025-11-19', NULL, NULL, 16100000.00, NULL, 0.00, 16100000.00, 4830000.00, 8000000.00, 8100000.00, 'partial', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'zalo', NULL, NULL, NULL, 3, '2025-10-22 04:00:00', '2025-12-08 19:34:57'),
	(19, 'BK-2025-0019', 4, 17, 55, 4, 0, 0, '2025-11-19', '2025-11-22', NULL, NULL, 19600000.00, NULL, 0.00, 19600000.00, 5880000.00, 5880000.00, 13720000.00, 'partial', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'phone', NULL, 'Nhóm 4 người', NULL, 2, '2025-10-25 08:15:00', '2025-12-08 19:34:57'),
	(20, 'BK-2025-0020', 6, 18, 58, 2, 0, 0, '2025-11-21', '2025-11-25', NULL, NULL, 25000000.00, NULL, 0.00, 25000000.00, 7500000.00, 7500000.00, 17500000.00, 'partial', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'email', NULL, NULL, NULL, 3, '2025-10-28 02:45:00', '2025-12-08 19:34:57'),
	(21, 'BK-2025-0021', 1, 19, 1, 2, 0, 0, '2025-11-24', '2025-11-26', NULL, NULL, 9000000.00, NULL, 0.00, 9000000.00, 2700000.00, 2700000.00, 6300000.00, 'partial', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'phone', NULL, NULL, NULL, 2, '2025-11-01 03:00:00', '2025-12-08 19:34:57'),
	(22, 'BK-2025-0022', 3, 20, 63, 2, 1, 0, '2025-11-26', '2025-11-28', NULL, NULL, 8800000.00, NULL, 0.00, 8800000.00, 2640000.00, 2640000.00, 6160000.00, 'partial', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'facebook', NULL, NULL, NULL, 3, '2025-11-03 07:20:00', '2025-12-08 19:34:57'),
	(23, 'BK-2025-0023', 2, 21, 65, 3, 0, 0, '2025-11-28', '2025-12-01', NULL, NULL, 17400000.00, NULL, 0.00, 17400000.00, 5220000.00, 5220000.00, 12180000.00, 'partial', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'zalo', NULL, NULL, NULL, 2, '2025-11-05 04:30:00', '2025-12-08 19:34:57'),
	(24, 'BK-2025-0024', 5, 22, 68, 2, 0, 0, '2025-11-30', '2025-12-04', NULL, NULL, 15000000.00, NULL, 0.00, 15000000.00, 0.00, 0.00, 15000000.00, 'unpaid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'phone', NULL, 'Chưa đặt cọc', NULL, 3, '2025-11-08 09:45:00', '2025-12-08 19:34:57'),
	(25, 'BK-2025-0025', 1, 23, 70, 2, 1, 0, '2025-12-05', '2025-12-07', NULL, NULL, 12500000.00, NULL, 0.00, 12500000.00, 0.00, 0.00, 12500000.00, 'unpaid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'walk_in', NULL, NULL, NULL, 2, '2025-11-10 02:15:00', '2025-12-08 19:34:57'),
	(26, 'BK-2025-0026', 2, 24, 72, 2, 0, 0, '2025-12-07', '2025-12-10', NULL, NULL, 11600000.00, NULL, 0.00, 11600000.00, 0.00, 0.00, 11600000.00, 'unpaid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'email', NULL, NULL, NULL, 3, '2025-11-12 06:00:00', '2025-12-08 19:34:57'),
	(27, 'BK-2025-0027', 4, 25, 75, 5, 0, 0, '2025-12-10', '2025-12-13', NULL, NULL, 24500000.00, NULL, 0.00, 24500000.00, 7350000.00, 7350000.00, 17150000.00, 'partial', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'facebook', NULL, 'Nhóm bạn', NULL, 2, '2025-11-15 03:30:00', '2025-12-08 19:34:57'),
	(28, 'BK-2025-0028', 1, 26, 77, 4, 2, 0, '2025-12-12', '2025-12-14', NULL, NULL, 25000000.00, NULL, 0.00, 25000000.00, 7500000.00, 7500000.00, 17500000.00, 'partial', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'zalo', NULL, NULL, NULL, 3, '2025-11-18 08:00:00', '2025-12-08 19:34:57'),
	(29, 'BK-2025-0029', 6, 27, 80, 2, 0, 0, '2025-12-15', '2025-12-19', NULL, NULL, 25000000.00, NULL, 0.00, 25000000.00, 0.00, 0.00, 25000000.00, 'unpaid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'phone', NULL, NULL, NULL, 2, '2025-11-20 04:45:00', '2025-12-08 19:34:57'),
	(30, 'BK-2025-0030', 3, 28, 10, 3, 1, 0, '2025-12-19', '2025-12-21', NULL, NULL, 12000000.00, NULL, 0.00, 12000000.00, 0.00, 0.00, 12000000.00, 'unpaid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'email', NULL, NULL, NULL, 3, '2025-11-22 02:00:00', '2025-12-08 19:34:57'),
	(31, 'BK-2025-0031', 5, 29, 14, 4, 1, 0, '2025-12-22', '2025-12-26', NULL, NULL, 35600000.00, 'LOYAL100', 1000000.00, 34600000.00, 10380000.00, 10380000.00, 24220000.00, 'partial', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'walk_in', NULL, 'Booking sớm', NULL, 2, '2025-11-25 07:30:00', '2025-12-08 19:34:57'),
	(32, 'BK-2025-0032', 2, 30, 17, 2, 0, 0, '2025-12-27', '2025-12-30', NULL, NULL, 11600000.00, NULL, 0.00, 11600000.00, 0.00, 0.00, 11600000.00, 'unpaid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'facebook', NULL, NULL, NULL, 3, '2025-11-28 03:15:00', '2025-12-08 19:34:57'),
	(33, 'BK-2025-0033', 1, 1, 3, 2, 0, 0, '2025-10-03', '2025-10-05', NULL, NULL, 9000000.00, NULL, 0.00, 9000000.00, 2700000.00, 9000000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'email', NULL, NULL, NULL, 2, '2025-09-21 02:30:00', '2025-12-08 19:34:57'),
	(34, 'BK-2025-0034', 1, 1, 7, 3, 0, 0, '2025-10-03', '2025-10-05', NULL, NULL, 13500000.00, NULL, 0.00, 13500000.00, 4050000.00, 13500000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'zalo', NULL, NULL, NULL, 3, '2025-09-22 08:00:00', '2025-12-08 19:34:57'),
	(35, 'BK-2025-0035', 2, 2, 11, 2, 1, 0, '2025-10-05', '2025-10-08', NULL, NULL, 16100000.00, NULL, 0.00, 16100000.00, 4830000.00, 16100000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'phone', NULL, NULL, NULL, 2, '2025-09-23 04:20:00', '2025-12-08 19:34:57'),
	(36, 'BK-2025-0036', 1, 3, 16, 2, 0, 0, '2025-10-10', '2025-10-12', NULL, NULL, 9000000.00, NULL, 0.00, 9000000.00, 2700000.00, 9000000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'facebook', NULL, NULL, NULL, 3, '2025-09-24 07:45:00', '2025-12-08 19:34:57'),
	(37, 'BK-2025-0037', 1, 3, 20, 3, 1, 0, '2025-10-10', '2025-10-12', NULL, NULL, 17000000.00, NULL, 0.00, 17000000.00, 5100000.00, 17000000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'walk_in', NULL, NULL, NULL, 2, '2025-09-25 03:00:00', '2025-12-08 19:34:57'),
	(38, 'BK-2025-0038', 3, 4, 24, 2, 0, 0, '2025-10-12', '2025-10-14', NULL, NULL, 6400000.00, NULL, 0.00, 6400000.00, 1920000.00, 6400000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'email', NULL, NULL, NULL, 3, '2025-09-26 09:30:00', '2025-12-08 19:34:57'),
	(39, 'BK-2025-0039', 2, 6, 28, 2, 2, 0, '2025-10-18', '2025-10-21', NULL, NULL, 20600000.00, NULL, 0.00, 20600000.00, 6180000.00, 20600000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'zalo', NULL, NULL, NULL, 2, '2025-09-29 02:15:00', '2025-12-08 19:34:57'),
	(40, 'BK-2025-0040', 5, 7, 32, 3, 0, 0, '2025-10-20', '2025-10-24', NULL, NULL, 22500000.00, NULL, 0.00, 22500000.00, 6750000.00, 22500000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'phone', NULL, NULL, NULL, 3, '2025-10-01 06:00:00', '2025-12-08 19:34:57'),
	(41, 'BK-2025-0041', 1, 8, 35, 2, 1, 0, '2025-10-24', '2025-10-26', NULL, NULL, 12500000.00, NULL, 0.00, 12500000.00, 3750000.00, 12500000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'facebook', NULL, NULL, NULL, 2, '2025-10-02 04:45:00', '2025-12-08 19:34:57'),
	(42, 'BK-2025-0042', 6, 9, 38, 3, 0, 0, '2025-10-26', '2025-10-30', NULL, NULL, 37500000.00, NULL, 0.00, 37500000.00, 11250000.00, 37500000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'email', NULL, NULL, NULL, 3, '2025-10-03 08:15:00', '2025-12-08 19:34:57'),
	(43, 'BK-2025-0043', 4, 10, 41, 2, 0, 0, '2025-10-28', '2025-10-31', NULL, NULL, 9800000.00, NULL, 0.00, 9800000.00, 2940000.00, 9800000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'walk_in', NULL, NULL, NULL, 2, '2025-10-06 02:30:00', '2025-12-08 19:34:57'),
	(44, 'BK-2025-0044', 1, 11, 44, 2, 0, 0, '2025-11-01', '2025-11-03', NULL, NULL, 9000000.00, NULL, 0.00, 9000000.00, 2700000.00, 9000000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'zalo', NULL, NULL, NULL, 3, '2025-10-09 07:00:00', '2025-12-08 19:34:57'),
	(45, 'BK-2025-0045', 2, 12, 47, 2, 1, 0, '2025-11-03', '2025-11-06', NULL, NULL, 16100000.00, NULL, 0.00, 16100000.00, 4830000.00, 16100000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'phone', NULL, NULL, NULL, 2, '2025-10-11 03:20:00', '2025-12-08 19:34:57'),
	(46, 'BK-2025-0046', 3, 13, 49, 4, 0, 0, '2025-11-07', '2025-11-09', NULL, NULL, 12800000.00, NULL, 0.00, 12800000.00, 3840000.00, 12800000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'email', NULL, NULL, NULL, 3, '2025-10-13 09:45:00', '2025-12-08 19:34:57'),
	(47, 'BK-2025-0047', 5, 14, 51, 2, 0, 0, '2025-11-09', '2025-11-13', NULL, NULL, 15000000.00, NULL, 0.00, 15000000.00, 4500000.00, 15000000.00, 0.00, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'facebook', NULL, NULL, NULL, 2, '2025-10-16 04:30:00', '2025-12-08 19:34:57'),
	(48, 'BK-2025-0048', 1, 15, 53, 2, 1, 0, '2025-11-14', '2025-11-16', NULL, NULL, 12500000.00, NULL, 0.00, 12500000.00, 3750000.00, 6000000.00, 6500000.00, 'partial', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'walk_in', NULL, NULL, NULL, 3, '2025-10-21 02:00:00', '2025-12-08 19:34:57'),
	(49, 'BK-2025-0049', 2, 16, 56, 2, 0, 0, '2025-11-16', '2025-11-19', NULL, NULL, 11600000.00, NULL, 0.00, 11600000.00, 3480000.00, 3480000.00, 8120000.00, 'partial', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'zalo', NULL, NULL, NULL, 2, '2025-10-23 08:30:00', '2025-12-08 19:34:57'),
	(50, 'BK-2025-0050', 4, 17, 59, 3, 1, 0, '2025-11-19', '2025-11-22', NULL, NULL, 18400000.00, NULL, 0.00, 18400000.00, 5520000.00, 5520000.00, 12880000.00, 'partial', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 'phone', NULL, NULL, NULL, 3, '2025-10-26 03:45:00', '2025-12-08 19:34:57');

-- Dumping structure for table duan.booking_customers
CREATE TABLE IF NOT EXISTS `booking_customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `age_type` enum('adult','child','infant') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'adult',
  `is_primary` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `booking_customers_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_customers_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=276 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.booking_customers: ~0 rows (approximately)
INSERT INTO `booking_customers` (`id`, `booking_id`, `customer_id`, `age_type`, `is_primary`) VALUES
	(226, 1, 1, 'adult', 1),
	(227, 2, 2, 'adult', 1),
	(228, 3, 5, 'adult', 1),
	(229, 4, 8, 'adult', 1),
	(230, 5, 12, 'adult', 1),
	(231, 6, 15, 'adult', 1),
	(232, 7, 18, 'adult', 1),
	(233, 8, 22, 'adult', 1),
	(234, 9, 25, 'adult', 1),
	(235, 10, 30, 'adult', 1),
	(236, 11, 33, 'adult', 1),
	(237, 12, 36, 'adult', 1),
	(238, 13, 40, 'adult', 1),
	(239, 14, 42, 'adult', 1),
	(240, 15, 45, 'adult', 1),
	(241, 16, 48, 'adult', 1),
	(242, 17, 50, 'adult', 1),
	(243, 18, 52, 'adult', 1),
	(244, 19, 55, 'adult', 1),
	(245, 20, 58, 'adult', 1),
	(246, 21, 1, 'adult', 1),
	(247, 22, 63, 'adult', 1),
	(248, 23, 65, 'adult', 1),
	(249, 24, 68, 'adult', 1),
	(250, 25, 70, 'adult', 1),
	(251, 26, 72, 'adult', 1),
	(252, 27, 75, 'adult', 1),
	(253, 28, 77, 'adult', 1),
	(254, 29, 80, 'adult', 1),
	(255, 30, 10, 'adult', 1),
	(256, 31, 14, 'adult', 1),
	(257, 32, 17, 'adult', 1),
	(258, 33, 3, 'adult', 1),
	(259, 34, 7, 'adult', 1),
	(260, 35, 11, 'adult', 1),
	(261, 36, 16, 'adult', 1),
	(262, 37, 20, 'adult', 1),
	(263, 38, 24, 'adult', 1),
	(264, 39, 28, 'adult', 1),
	(265, 40, 32, 'adult', 1),
	(266, 41, 35, 'adult', 1),
	(267, 42, 38, 'adult', 1),
	(268, 43, 41, 'adult', 1),
	(269, 44, 44, 'adult', 1),
	(270, 45, 47, 'adult', 1),
	(271, 46, 49, 'adult', 1),
	(272, 47, 51, 'adult', 1),
	(273, 48, 53, 'adult', 1),
	(274, 49, 56, 'adult', 1),
	(275, 50, 59, 'adult', 1);

-- Dumping structure for table duan.booking_services
CREATE TABLE IF NOT EXISTS `booking_services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `service_id` int NOT NULL,
  `service_provider_id` int DEFAULT NULL,
  `service_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL,
  `unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(15,2) DEFAULT NULL,
  `total_price` decimal(15,2) DEFAULT NULL,
  `service_date` date DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `payment_status` enum('pending','partial','paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `paid_amount` decimal(15,2) DEFAULT '0.00',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `service_provider_id` (`service_provider_id`),
  KEY `created_by` (`created_by`),
  KEY `booking_services_ibfk_booking` (`booking_id`),
  CONSTRAINT `booking_services_ibfk_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_services_ibfk_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `booking_services_ibfk_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  CONSTRAINT `booking_services_ibfk_service_provider` FOREIGN KEY (`service_provider_id`) REFERENCES `service_providers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.booking_services: ~0 rows (approximately)
INSERT INTO `booking_services` (`id`, `booking_id`, `service_id`, `service_provider_id`, `service_name`, `quantity`, `unit`, `unit_price`, `total_price`, `service_date`, `from_date`, `to_date`, `payment_status`, `paid_amount`, `notes`, `created_by`, `created_at`) VALUES
	(13, 1, 1, 1, 'Phòng Standard - Vinpearl Hạ Long', 4, 'đêm', 1200000.00, 4800000.00, '2025-10-03', NULL, NULL, 'paid', 0.00, '2 phòng x 2 đêm', 2, '2025-09-25 03:00:00'),
	(14, 1, 14, 11, 'Hải sản Hạ Long - Buffet', 3, 'suất', 350000.00, 1050000.00, '2025-10-03', NULL, NULL, 'paid', 0.00, 'Bữa trưa ngày 1', 2, '2025-09-25 03:05:00'),
	(15, 1, 14, 11, 'Hải sản Hạ Long - Buffet', 3, 'suất', 350000.00, 1050000.00, '2025-10-04', NULL, NULL, 'paid', 0.00, 'Bữa trưa ngày 2', 2, '2025-09-25 03:06:00'),
	(16, 1, 17, 14, 'Xe 45 chỗ', 1, 'ngày', 3500000.00, 3500000.00, '2025-10-03', NULL, NULL, 'paid', 0.00, '3 ngày thuê xe', 2, '2025-09-25 03:10:00'),
	(17, 1, 29, NULL, 'Bảo hiểm du lịch', 3, 'người', 50000.00, 150000.00, '2025-10-03', NULL, NULL, 'paid', 0.00, '2 adults + 1 child', 2, '2025-09-25 03:15:00'),
	(18, 2, 1, 1, 'Phòng Standard - Vinpearl Hạ Long', 2, 'đêm', 1200000.00, 2400000.00, '2025-10-03', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-09-26 02:00:00'),
	(19, 2, 14, 11, 'Hải sản Hạ Long - Buffet', 2, 'suất', 350000.00, 700000.00, '2025-10-03', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-09-26 02:05:00'),
	(20, 2, 17, 14, 'Xe 45 chỗ (chia sẻ)', 1, 'ngày', 1750000.00, 1750000.00, '2025-10-03', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-09-26 02:10:00'),
	(21, 2, 29, NULL, 'Bảo hiểm du lịch', 2, 'người', 50000.00, 100000.00, '2025-10-03', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-09-26 02:15:00'),
	(22, 3, 3, 2, 'Phòng Standard - Mường Thanh DN', 3, 'đêm', 1500000.00, 4500000.00, '2025-10-05', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-09-28 03:00:00'),
	(23, 3, 23, 18, 'Vé Sun World Bà Nà Hills', 2, 'vé', 750000.00, 1500000.00, '2025-10-05', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-09-28 03:05:00'),
	(24, 3, 24, 18, 'Vé trẻ em Bà Nà Hills', 2, 'vé', 600000.00, 1200000.00, '2025-10-05', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-09-28 03:06:00'),
	(25, 3, 15, 12, 'Buffet Bà Nà Restaurant', 4, 'suất', 200000.00, 800000.00, '2025-10-05', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-09-28 03:10:00'),
	(26, 3, 18, 14, 'Xe 16 chỗ Limousine', 4, 'ngày', 2200000.00, 8800000.00, '2025-10-05', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-09-28 03:15:00'),
	(27, 3, 29, NULL, 'Bảo hiểm du lịch', 4, 'người', 50000.00, 200000.00, '2025-10-05', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-09-28 03:20:00'),
	(28, 4, 1, 1, 'Phòng Standard - Vinpearl Hạ Long', 6, 'đêm', 1200000.00, 7200000.00, '2025-10-10', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-09-29 02:00:00'),
	(29, 4, 2, 1, 'Phòng Deluxe - Vinpearl Hạ Long', 2, 'đêm', 1800000.00, 3600000.00, '2025-10-10', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-09-29 02:05:00'),
	(30, 4, 14, 11, 'Hải sản Hạ Long - Buffet', 6, 'suất', 350000.00, 2100000.00, '2025-10-10', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-09-29 02:10:00'),
	(31, 4, 17, 14, 'Xe 45 chỗ', 1, 'ngày', 3500000.00, 3500000.00, '2025-10-10', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-09-29 02:15:00'),
	(32, 4, 29, NULL, 'Bảo hiểm du lịch', 6, 'người', 50000.00, 300000.00, '2025-10-10', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-09-29 02:20:00'),
	(33, 5, 10, 8, 'Phòng khách sạn Cần Thơ', 2, 'đêm', 800000.00, 1600000.00, '2025-10-12', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-10-01 03:00:00'),
	(34, 5, 12, 9, 'Buffet trưa - Ngọn 138', 3, 'suất', 250000.00, 750000.00, '2025-10-12', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-10-01 03:05:00'),
	(35, 5, 18, 14, 'Xe 16 chỗ', 3, 'ngày', 2200000.00, 6600000.00, '2025-10-12', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-10-01 03:10:00'),
	(36, 5, 29, NULL, 'Bảo hiểm du lịch', 3, 'người', 50000.00, 150000.00, '2025-10-12', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-10-01 03:15:00'),
	(37, 6, 10, 8, 'Phòng khách sạn Cần Thơ', 4, 'đêm', 800000.00, 3200000.00, '2025-10-12', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-10-02 02:00:00'),
	(38, 6, 12, 9, 'Buffet trưa - Ngọn 138', 3, 'suất', 250000.00, 750000.00, '2025-10-12', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-10-02 02:05:00'),
	(39, 6, 17, 14, 'Xe 45 chỗ (chia sẻ)', 1, 'ngày', 1750000.00, 1750000.00, '2025-10-12', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-10-02 02:10:00'),
	(40, 6, 29, NULL, 'Bảo hiểm du lịch', 3, 'người', 50000.00, 150000.00, '2025-10-12', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-10-02 02:15:00'),
	(41, 7, 3, 2, 'Phòng Standard - Mường Thanh DN', 3, 'đêm', 1500000.00, 4500000.00, '2025-10-18', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-10-03 03:00:00'),
	(42, 7, 23, 18, 'Vé Sun World Bà Nà Hills', 2, 'vé', 750000.00, 1500000.00, '2025-10-18', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-10-03 03:05:00'),
	(43, 7, 24, 18, 'Vé trẻ em Bà Nà Hills', 1, 'vé', 600000.00, 600000.00, '2025-10-18', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-10-03 03:06:00'),
	(44, 7, 15, 12, 'Buffet Bà Nà Restaurant', 3, 'suất', 200000.00, 600000.00, '2025-10-18', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-10-03 03:10:00'),
	(45, 7, 18, 14, 'Xe 16 chỗ Limousine', 4, 'ngày', 2200000.00, 8800000.00, '2025-10-18', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-10-03 03:15:00'),
	(46, 7, 29, NULL, 'Bảo hiểm du lịch', 3, 'người', 50000.00, 150000.00, '2025-10-18', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-10-03 03:20:00'),
	(47, 8, 9, 7, 'Villa 2 phòng ngủ - JW Marriott PQ', 4, 'đêm', 4500000.00, 18000000.00, '2025-10-20', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-10-04 04:00:00'),
	(48, 8, 25, 19, 'Vé VinWonders Phú Quốc', 2, 'vé', 850000.00, 1700000.00, '2025-10-20', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-10-04 04:05:00'),
	(49, 8, 16, 13, 'Dinner hải sản - Winston PQ', 2, 'suất', 500000.00, 1000000.00, '2025-10-20', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-10-04 04:10:00'),
	(50, 8, 19, 15, 'Xe 7 chỗ VIP', 5, 'ngày', 1500000.00, 7500000.00, '2025-10-20', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-10-04 04:15:00'),
	(51, 8, 30, NULL, 'Bảo hiểm quốc tế', 2, 'người', 150000.00, 300000.00, '2025-10-20', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-10-04 04:20:00'),
	(52, 9, 1, 1, 'Phòng Standard - Vinpearl Hạ Long', 4, 'đêm', 1200000.00, 4800000.00, '2025-10-24', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-10-05 02:00:00'),
	(53, 9, 14, 11, 'Hải sản Hạ Long - Buffet', 4, 'suất', 350000.00, 1400000.00, '2025-10-24', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-10-05 02:05:00'),
	(54, 9, 17, 14, 'Xe 45 chỗ', 1, 'ngày', 3500000.00, 3500000.00, '2025-10-24', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-10-05 02:10:00'),
	(55, 9, 29, NULL, 'Bảo hiểm du lịch', 4, 'người', 50000.00, 200000.00, '2025-10-24', NULL, NULL, 'paid', 0.00, NULL, 2, '2025-10-05 02:15:00'),
	(56, 10, 5, 3, 'Phòng Deluxe - Sheraton Bangkok', 4, 'đêm', 3200000.00, 12800000.00, '2025-10-26', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-10-06 03:00:00'),
	(57, 10, 21, 17, 'Vé máy bay HN-BKK khứ hồi', 2, 'vé', 4500000.00, 9000000.00, '2025-10-26', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-10-06 03:05:00'),
	(58, 10, 12, 9, 'Buffet quốc tế', 2, 'suất', 250000.00, 500000.00, '2025-10-26', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-10-06 03:10:00'),
	(59, 10, 18, 14, 'Xe 16 chỗ Thailand', 5, 'ngày', 2200000.00, 11000000.00, '2025-10-26', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-10-06 03:15:00'),
	(60, 10, 30, NULL, 'Bảo hiểm quốc tế', 2, 'người', 150000.00, 300000.00, '2025-10-26', NULL, NULL, 'paid', 0.00, NULL, 3, '2025-10-06 03:20:00');

-- Dumping structure for table duan.booking_status_history
CREATE TABLE IF NOT EXISTS `booking_status_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `old_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `changed_by` int DEFAULT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `changed_by` (`changed_by`),
  CONSTRAINT `booking_status_history_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_status_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.booking_status_history: ~0 rows (approximately)
INSERT INTO `booking_status_history` (`id`, `booking_id`, `old_status`, `new_status`, `changed_by`, `reason`, `notes`, `created_at`) VALUES
	(42, 1, NULL, 'unpaid', 2, 'Booking mới tạo', 'Khách đặt qua điện thoại', '2025-09-20 03:30:00'),
	(43, 1, 'unpaid', 'partial', 2, 'Nhận cọc 30%', 'Chuyển khoản 3.750.000đ', '2025-09-21 07:00:00'),
	(44, 1, 'partial', 'paid', 2, 'Thanh toán đủ', 'Thanh toán toàn bộ số còn lại', '2025-09-30 03:00:00'),
	(45, 2, NULL, 'unpaid', 3, 'Booking mới', 'Khách đặt qua Facebook', '2025-09-21 04:00:00'),
	(46, 2, 'unpaid', 'partial', 3, 'Nhận cọc', 'Cọc tiền mặt tại văn phòng', '2025-09-22 08:00:00'),
	(47, 2, 'partial', 'paid', 3, 'Thanh toán đủ', 'Chuyển khoản số còn lại', '2025-10-01 04:00:00'),
	(48, 3, NULL, 'unpaid', 2, NULL, 'Booking walk-in', '2025-09-22 07:20:00'),
	(49, 3, 'unpaid', 'partial', 2, NULL, 'Cọc 30%', '2025-09-23 03:00:00'),
	(50, 3, 'partial', 'paid', 2, NULL, 'Full payment', '2025-10-02 07:00:00'),
	(51, 5, 'unpaid', 'cancelled', 2, 'Khách hủy do bận công việc', 'Hoàn cọc 90% theo chính sách', '2025-10-08 02:00:00'),
	(52, 17, NULL, 'unpaid', 2, NULL, 'Booking Facebook', '2025-10-20 06:30:00'),
	(53, 17, 'unpaid', 'partial', 2, NULL, 'Nhận cọc + trả góp lần 1', '2025-11-05 07:00:00'),
	(54, 18, NULL, 'unpaid', 3, NULL, 'Booking Zalo', '2025-10-22 04:00:00'),
	(55, 18, 'unpaid', 'partial', 3, NULL, 'Nhận cọc + trả thêm', '2025-11-08 04:00:00'),
	(56, 19, NULL, 'unpaid', 2, NULL, 'Booking phone - Nhóm 4', '2025-10-25 08:15:00'),
	(57, 19, 'unpaid', 'partial', 2, NULL, 'Cọc 30%', '2025-10-26 08:00:00'),
	(58, 24, NULL, 'unpaid', 3, 'Booking mới', 'Khách chưa chuyển khoản cọc', '2025-11-08 09:45:00'),
	(59, 25, NULL, 'unpaid', 2, NULL, 'Booking walk-in', '2025-11-10 02:15:00'),
	(60, 26, NULL, 'unpaid', 3, NULL, 'Booking email', '2025-11-12 06:00:00'),
	(61, 29, NULL, 'unpaid', 2, NULL, 'Booking phone', '2025-11-20 04:45:00'),
	(62, 30, NULL, 'unpaid', 3, NULL, 'Booking email', '2025-11-22 02:00:00'),
	(63, 32, NULL, 'unpaid', 3, NULL, 'Booking Facebook', '2025-11-28 03:15:00');

-- Dumping structure for table duan.cancellation_policies
CREATE TABLE IF NOT EXISTS `cancellation_policies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `days_before` int NOT NULL,
  `fee_percentage` decimal(5,2) NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.cancellation_policies: ~0 rows (approximately)
INSERT INTO `cancellation_policies` (`id`, `name`, `description`, `days_before`, `fee_percentage`, `status`, `created_at`) VALUES
	(1, 'Hủy rất sớm', 'Hủy tour trước 30 ngày khởi hành', 30, 10.00, 'active', '2025-01-05 01:00:00'),
	(2, 'Hủy sớm', 'Hủy tour từ 15-30 ngày trước khởi hành', 15, 30.00, 'active', '2025-01-05 01:00:00'),
	(3, 'Hủy trung bình', 'Hủy tour từ 7-14 ngày trước khởi hành', 7, 50.00, 'active', '2025-01-05 01:00:00'),
	(4, 'Hủy gần', 'Hủy tour từ 3-6 ngày trước khởi hành', 3, 75.00, 'active', '2025-01-05 01:00:00'),
	(5, 'Hủy rất gần', 'Hủy tour dưới 3 ngày trước khởi hành', 0, 100.00, 'active', '2025-01-05 01:00:00');

-- Dumping structure for table duan.countries
CREATE TABLE IF NOT EXISTS `countries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_countries_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.countries: ~0 rows (approximately)
INSERT INTO `countries` (`id`, `code`, `name`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'VN', 'Việt Nam', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:30'),
	(2, 'TH', 'Thái Lan', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:30'),
	(3, 'SG', 'Singapore', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:30'),
	(4, 'JP', 'Nhật Bản', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:30'),
	(5, 'KR', 'Hàn Quốc', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:30'),
	(6, 'CN', 'Trung Quốc', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:30'),
	(7, 'KH', 'Campuchia', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:30');

-- Dumping structure for table duan.customers
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_card` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Vietnam',
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `customer_type` enum('individual','group','corporate') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'individual',
  `source` enum('phone','email','facebook','zalo','walk_in','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `special_requirements` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `total_bookings` int DEFAULT '0',
  `total_spent` decimal(15,2) DEFAULT '0.00',
  `status` enum('active','inactive','blacklist') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_code` (`customer_code`),
  KEY `created_by` (`created_by`),
  KEY `idx_customers_phone` (`phone`),
  KEY `idx_customers_email` (`email`),
  KEY `idx_customers_status` (`status`),
  CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.customers: ~0 rows (approximately)
INSERT INTO `customers` (`id`, `customer_code`, `full_name`, `email`, `phone`, `date_of_birth`, `gender`, `id_card`, `passport`, `nationality`, `address`, `customer_type`, `source`, `special_requirements`, `notes`, `total_bookings`, `total_spent`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
	(1, 'KH-2025-0001', 'Nguyễn Văn Hùng', 'nguyenvanhung1985@gmail.com', '0901234567', '1985-03-15', 'male', '001085012345', NULL, 'Vietnam', '12 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh', 'individual', 'phone', NULL, NULL, 0, 0.00, 'active', 2, '2025-09-15 03:30:00', '2025-12-08 19:34:44'),
	(2, 'KH-2025-0002', 'Nguyễn Thị Mai', 'ntmai1990@yahoo.com', '0912345678', '1990-07-22', 'female', '001090023456', NULL, 'Vietnam', '45 Lý Thường Kiệt, Hoàn Kiếm, Hà Nội', 'individual', 'facebook', NULL, NULL, 0, 0.00, 'active', 2, '2025-09-18 07:20:00', '2025-12-08 19:34:44'),
	(3, ' KH-2025-0003', 'Nguyễn Hoàng Long', 'hoanglong.nguyen@gmail.com', '0923456789', '1992-11-08', 'male', '001092034567', NULL, 'Vietnam', '78 Trần Hưng Đạo, Hải Châu, Đà Nẵng', 'individual', 'zalo', NULL, NULL, 0, 0.00, 'active', 3, '2025-09-20 02:15:00', '2025-12-08 19:34:44'),
	(4, 'KH-2025-0004', 'Nguyễn Thị Linh', 'linhnt1995@gmail.com', '0934567890', '1995-04-18', 'female', '001095045678', NULL, 'Vietnam', '23 Hai Bà Trưng, Quận 3, TP. Hồ Chí Minh', 'individual', 'email', NULL, NULL, 0, 0.00, 'active', 2, '2025-09-22 09:45:00', '2025-12-08 19:34:44'),
	(5, 'KH-2025-0005', 'Nguyễn Quang Hải', 'quanghai88@gmail.com', '0945678901', '1988-06-25', 'male', '001088056789', NULL, 'Vietnam', '56 Lê Lợi, Nha Trang, Khánh Hòa', 'individual', 'walk_in', NULL, NULL, 0, 0.00, 'active', 3, '2025-09-25 04:00:00', '2025-12-08 19:34:44'),
	(6, 'KH-2025-0006', 'Nguyễn Thị Hương', 'huongthinguyen@yahoo.com', '0956789012', '1993-09-14', 'female', '001093067890', NULL, 'Vietnam', '89 Nguyễn Thái Học, Ba Đình, Hà Nội', 'individual', 'phone', NULL, NULL, 0, 0.00, 'active', 2, '2025-09-27 01:30:00', '2025-12-08 19:34:44'),
	(7, 'KH-2025-0007', 'Nguyễn Đức Minh', 'minhnd1991@gmail.com', '0967890123', '1991-12-03', 'male', '001091078901', NULL, 'Vietnam', '34 Lý Tự Trọng, Quận 1, TP. Hồ Chí Minh', 'corporate', 'email', NULL, NULL, 0, 0.00, 'active', 3, '2025-09-28 08:20:00', '2025-12-08 19:34:44'),
	(8, 'KH-2025-0008', 'Nguyễn Thị Quỳnh Anh', 'quynhanh.nguyen94@gmail.com', '0978901234', '1994-02-28', 'female', '001094089012', NULL, 'Vietnam', '67 Phan Châu Trinh, Hải Châu, Đà Nẵng', 'individual', 'facebook', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-01 03:45:00', '2025-12-08 19:34:44'),
	(9, 'KH-2025-0009', 'Nguyễn Tiến Dũng', 'dungnt1987@gmail.com', '0989012345', '1987-05-17', 'male', '001087090123', NULL, 'Vietnam', '12 Trường Chinh, Thanh Xuân, Hà Nội', 'individual', 'zalo', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-02 07:00:00', '2025-12-08 19:34:44'),
	(10, 'KH-2025-0010', 'Nguyễn Thị Hồng Nhung', 'hongnhung96@yahoo.com', '0990123456', '1996-08-09', 'female', '001096001234', NULL, 'Vietnam', '45 Nguyễn Văn Linh, Quận 7, TP. Hồ Chí Minh', 'individual', 'phone', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-03 02:30:00', '2025-12-08 19:34:44'),
	(11, 'KH-2025-0011', 'Nguyễn Văn Tuấn', 'tuannv1989@gmail.com', '0901234568', '1989-01-15', 'male', '001089012345', NULL, 'Vietnam', '23 Bà Triệu, Hoàn Kiếm, Hà Nội', 'individual', 'facebook', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-05 04:15:00', '2025-12-08 19:34:44'),
	(12, 'KH-2025-0012', 'Nguyễn Thị Thanh Thảo', 'thaott1992@gmail.com', '0912345679', '1992-10-20', 'female', '001092023456', NULL, 'Vietnam', '56 Điện Biên Phủ, Quận 3, TP. Hồ Chí Minh', 'individual', 'zalo', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-06 09:20:00', '2025-12-08 19:34:44'),
	(13, 'KH-2025-0013', 'Nguyễn Hữu Thắng', 'thanghuu1986@yahoo.com', '0923456790', '1986-12-12', 'male', '001086034567', NULL, 'Vietnam', '78 Lê Duẩn, Hải Châu, Đà Nẵng', 'group', 'phone', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-08 01:45:00', '2025-12-08 19:34:44'),
	(14, 'KH-2025-0014', 'Nguyễn Thị Minh Tâm', 'minhtam93@gmail.com', '0934567891', '1993-03-25', 'female', '001093045678', NULL, 'Vietnam', '12 Trần Phú, Nha Trang, Khánh Hòa', 'individual', 'email', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-09 06:30:00', '2025-12-08 19:34:44'),
	(15, 'KH-2025-0015', 'Nguyễn Bảo Nam', 'baonam1994@gmail.com', '0945678902', '1994-07-07', 'male', '001094056789', NULL, 'Vietnam', '34 Nguyễn Trãi, Quận 1, TP. Hồ Chí Minh', 'individual', 'walk_in', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-10 03:00:00', '2025-12-08 19:34:44'),
	(16, 'KH-2025-0016', 'Nguyễn Thị Ngọc Mai', 'ngocmai1991@yahoo.com', '0956789013', '1991-05-30', 'female', '001091067890', NULL, 'Vietnam', '89 Cầu Giấy, Cầu Giấy, Hà Nội', 'individual', 'facebook', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-11 08:15:00', '2025-12-08 19:34:44'),
	(17, 'KH-2025-0017', 'Nguyễn Công Phượng', 'phuongcong1995@gmail.com', '0967890124', '1995-01-21', 'male', '001095078901', NULL, 'Vietnam', '45 Lê Lợi, Hải Châu, Đà Nẵng', 'individual', 'zalo', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-12 02:45:00', '2025-12-08 19:34:44'),
	(18, 'KH-2025-0018', 'Nguyễn Thị Ánh', 'anhnt1990@gmail.com', '0978901235', '1990-09-09', 'female', '001090089012', NULL, 'Vietnam', '12 Phan Đình Phùng, Ba Đình, Hà Nội', 'individual', 'phone', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-14 07:20:00', '2025-12-08 19:34:44'),
	(19, 'KH-2025-0019', 'Nguyễn Xuân Trường', 'truongxuan1988@gmail.com', '0989012346', '1988-11-28', 'male', '001088090123', NULL, 'Vietnam', '67 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh', 'individual', 'email', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-15 04:30:00', '2025-12-08 19:34:44'),
	(20, 'KH-2025-0020', 'Nguyễn Thị Bích Ngọc', 'bichngoc92@yahoo.com', '0990123457', '1992-04-14', 'female', '001092001234', NULL, 'Vietnam', '23 Lý Thái Tổ, Hoàn Kiếm, Hà Nội', 'individual', 'facebook', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-16 09:00:00', '2025-12-08 19:34:44'),
	(21, 'KH-2025-0021', 'Nguyễn Văn Đạt', 'datnv1987@gmail.com', '0901234569', '1987-02-18', 'male', '001087012345', NULL, 'Vietnam', '45 Đinh Tiên Hoàng, Quận 1, TP. Hồ Chí Minh', 'individual', 'zalo', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-18 03:15:00', '2025-12-08 19:34:44'),
	(22, 'KH-2025-0022', 'Nguyễn Thị Thu Hà', 'thuha1993@gmail.com', '0912345680', '1993-06-22', 'female', '001093023456', NULL, 'Vietnam', '56 Trần Hưng Đạo, Hoàn Kiếm, Hà Nội', 'individual', 'phone', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-19 06:45:00', '2025-12-08 19:34:44'),
	(23, 'KH-2025-0023', 'Nguyễn Anh Tuấn', 'anhtuan1989@yahoo.com', '0923456791', '1989-08-05', 'male', '001089034567', NULL, 'Vietnam', '78 Lê Lai, Quận 1, TP. Hồ Chí Minh', 'corporate', 'email', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-20 02:00:00', '2025-12-08 19:34:44'),
	(24, 'KH-2025-0024', 'Nguyễn Thị Diệu', 'dieunt1994@gmail.com', '0934567892', '1994-12-10', 'female', '001094045678', NULL, 'Vietnam', '12 Nguyễn Văn Linh, Hải Châu, Đà Nẵng', 'individual', 'facebook', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-21 08:30:00', '2025-12-08 19:34:44'),
	(25, 'KH-2025-0025', 'Nguyễn Khắc Việt', 'vietkhac1986@gmail.com', '0945678903', '1986-03-27', 'male', '001086056789', NULL, 'Vietnam', '34 Pasteur, Quận 3, TP. Hồ Chí Minh', 'individual', 'walk_in', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-22 04:20:00', '2025-12-08 19:34:44'),
	(26, 'KH-2025-0026', 'Nguyễn Thị Phương', 'phuongnt1995@yahoo.com', '0956789014', '1995-11-11', 'female', '001095067890', NULL, 'Vietnam', '89 Cách Mạng Tháng 8, Q10, TP. Hồ Chí Minh', 'individual', 'zalo', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-23 07:50:00', '2025-12-08 19:34:44'),
	(27, 'KH-2025-0027', 'Nguyễn Đình Trọng', 'trongdinh1990@gmail.com', '0967890125', '1990-05-16', 'male', '001090078901', NULL, 'Vietnam', '45 Hùng Vương, Đà Nẵng', 'individual', 'phone', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-24 03:00:00', '2025-12-08 19:34:44'),
	(28, 'KH-2025-0028', 'Nguyễn Thị Lan', 'lannt1991@gmail.com', '0978901236', '1991-07-29', 'female', '001091089012', NULL, 'Vietnam', '12 Lê Thánh Tôn, Quận 1, TP. Hồ Chí Minh', 'individual', 'email', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-25 09:30:00', '2025-12-08 19:34:44'),
	(29, 'KH-2025-0029', 'Nguyễn Thanh Tùng', 'tungthanh1987@yahoo.com', '0989012347', '1987-10-03', 'male', '001087090123', NULL, 'Vietnam', '67 Điện Biên Phủ, Ba Đình, Hà Nội', 'individual', 'facebook', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-26 02:20:00', '2025-12-08 19:34:44'),
	(30, 'KH-2025-0030', 'Nguyễn Thị My', 'mynt1993@gmail.com', '0990123458', '1993-12-05', 'female', '001093001234', NULL, 'Vietnam', '23 Phan Chu Trinh, Hải Châu, Đà Nẵng', 'individual', 'zalo', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-27 06:00:00', '2025-12-08 19:34:44'),
	(31, 'KH-2025-0031', 'Nguyễn Trọng Hoàng', 'hoangtrong1992@gmail.com', '0901234570', '1992-02-14', 'male', '001092012345', NULL, 'Vietnam', '45 Lý Tự Trọng, Quận 1, TP. Hồ Chí Minh', 'individual', 'phone', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-28 03:40:00', '2025-12-08 19:34:44'),
	(32, 'KH-2025-0032', 'Nguyễn Thị Dung', 'dungannt1996@yahoo.com', '0912345681', '1996-09-19', 'female', '001096023456', NULL, 'Vietnam', '56 Ngô Quyền, Hoàn Kiếm, Hà Nội', 'individual', 'email', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-29 08:15:00', '2025-12-08 19:34:44'),
	(33, 'KH-2025-0033', 'Trần Văn Thành', 'thanhtv1985@gmail.com', '0923456792', '1985-01-10', 'male', '002085034567', NULL, 'Vietnam', '12 Lê Lai, Quận 1, TP. Hồ Chí Minh', 'individual', 'facebook', NULL, NULL, 0, 0.00, 'active', 2, '2025-09-16 04:00:00', '2025-12-08 19:34:44'),
	(34, 'KH-2025-0034', 'Trần Thị Hoa', 'hoatran1990@gmail.com', '0934567893', '1990-03-20', 'female', '002090045678', NULL, 'Vietnam', '34Trường Chinh, Ba Đình, Hà Nội', 'individual', 'zalo', NULL, NULL, 0, 0.00, 'active', 3, '2025-09-19 07:30:00', '2025-12-08 19:34:44'),
	(35, 'KH-2025-0035', 'Trần Quang Hải', 'haiquang1988@yahoo.com', '0945678904', '1988-07-15', 'male', '002088056789', NULL, 'Vietnam', '45 Nguyễn Tri Phương, Quận 10, TP. Hồ Chí Minh', 'individual', 'phone', NULL, NULL, 0, 0.00, 'active', 2, '2025-09-21 02:45:00', '2025-12-08 19:34:44'),
	(36, 'KH-2025-0036', 'Trần Thị Ngọc', 'ngoctran1992@gmail.com', '0956789015', '1992-11-28', 'female', '002092067890', NULL, 'Vietnam', '67 Hoàng Diệu, Hải Châu, Đà Nẵng', 'individual', 'email', NULL, NULL, 0, 0.00, 'active', 3, '2025-09-23 09:00:00', '2025-12-08 19:34:44'),
	(37, 'KH-2025-0037', 'Trần Anh Dũng', 'dungtran1991@gmail.com', '0967890126', '1991-05-05', 'male', '002091078901', NULL, 'Vietnam', '12 Pasteur, Quận 1, TP. Hồ Chí Minh', 'corporate', 'walk_in', NULL, NULL, 0, 0.00, 'active', 2, '2025-09-26 03:30:00', '2025-12-08 19:34:44'),
	(38, 'KH-2025-0038', 'Trần Thị Lan Hương', 'lanhuongtran@yahoo.com', '0978901237', '1994-08-12', 'female', '002094089012', NULL, 'Vietnam', '23 Lý Thường Kiệt, Hoàn Kiếm, Hà Nội', 'individual', 'facebook', NULL, NULL, 0, 0.00, 'active', 3, '2025-09-29 06:20:00', '2025-12-08 19:34:44'),
	(39, 'KH-2025-0039', 'Trần Minh Tuấn', 'tuanminh1989@gmail.com', '0989012348', '1989-12-22', 'male', '002089090123', NULL, 'Vietnam', '45 Trần Hưng Đạo, Quận 1, TP. Hồ Chí Minh', 'individual', 'zalo', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-04 04:50:00', '2025-12-08 19:34:44'),
	(40, 'KH-2025-0040', 'Trần Thị Thanh', 'thanhtran1993@gmail.com', '0990123459', '1993-04-08', 'female', '002093001234', NULL, 'Vietnam', '56 Nguyễn Thái Học, Ba Đình, Hà Nội', 'individual', 'phone', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-07 01:15:00', '2025-12-08 19:34:44'),
	(41, 'KH-2025-0041', 'Trần Công Phượng', 'phuongtran1990@yahoo.com', '0901234571', '1990-02-28', 'male', '002090012345', NULL, 'Vietnam', '78 Lê Lợi, Hải Châu, Đà Nẵng', 'individual', 'email', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-13 08:30:00', '2025-12-08 19:34:44'),
	(42, 'KH-2025-0042', 'Trần Thị Hồng', 'hongtran1995@gmail.com', '0912345682', '1995-09-17', 'female', '002095023456', NULL, 'Vietnam', '12 Hai Bà Trưng, Quận 3, TP. Hồ Chí Minh', 'individual', 'facebook', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-17 03:40:00', '2025-12-08 19:34:44'),
	(43, 'KH-2025-0043', 'Trần Đức Phúc', 'phucductran1987@gmail.com', '0923456793', '1987-06-25', 'male', '002087034567', NULL, 'Vietnam', '34 Lý Tự Trọng, Quận 1, TP. Hồ Chí Minh', 'individual', 'zalo', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-30 02:15:00', '2025-12-08 19:34:44'),
	(44, 'KH-2025-0044', 'Trần Thị Kim Chi', 'kimchitran@yahoo.com', '0934567894', '1992-10-30', 'female', '002092045678', NULL, 'Vietnam', '45 Trần Phú, Nha Trang, Khánh Hòa', 'individual', 'phone', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-31 07:20:00', '2025-12-08 19:34:44'),
	(45, 'KH-2025-0045', 'Trần Văn Nam', '0945678905', 'namvan1986@gmail.com', '1986-03-15', 'male', '002086056789', NULL, 'Vietnam', '67 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh', 'group', 'email', NULL, NULL, 0, 0.00, 'active', 2, '2025-11-01 04:30:00', '2025-12-08 19:34:44'),
	(46, 'KH-2025-0046', 'Trần Thị Vy', 'vytran1994@gmail.com', '0956789016', '1994-12-08', 'female', '002094067890', NULL, 'Vietnam', '12 Phan Đình Phùng, Ba Đình, Hà Nội', 'individual', 'walk_in', NULL, NULL, 0, 0.00, 'active', 3, '2025-11-02 09:45:00', '2025-12-08 19:34:44'),
	(47, 'KH-2025-0047', 'Trần Bảo Long', 'longbao1991@yahoo.com', '0967890127', '1991-07-20', 'male', '002091078901', NULL, 'Vietnam', '23 Võ Văn Tần, Quận 3, TP. Hồ Chí Minh', 'individual', 'facebook', NULL, NULL, 0, 0.00, 'active', 2, '2025-11-03 03:00:00', '2025-12-08 19:34:44'),
	(48, 'KH-2025-0048', 'Trần Thị Tuyết', 'tuyettran1993@gmail.com', '0978901238', '1993-01-25', 'female', '002093089012', NULL, 'Vietnam', '45 Bạch Đằng, Hải Châu, Đà Nẵng', 'individual', 'zalo', NULL, NULL, 0, 0.00, 'active', 3, '2025-11-04 06:15:00', '2025-12-08 19:34:44'),
	(49, 'KH-2025-0049', 'Lê Văn Tâm', 'tamle1985@gmail.com', '0989012349', '1985-04-12', 'male', '003085090123', NULL, 'Vietnam', '56 Lê Lợi, Quận 1, TP. Hồ Chí Minh', 'individual', 'phone', NULL, NULL, 0, 0.00, 'active', 2, '2025-09-17 02:30:00', '2025-12-08 19:34:44'),
	(50, 'KH-2025-0050', 'Lê Thị Mai', 'maile1990@yahoo.com', '0990123460', '1990-09-03', 'female', '003090001234', NULL, 'Vietnam', '78 Lý Thường Kiệt, Hoàn Kiếm, Hà Nội', 'individual', 'email', NULL, NULL, 0, 0.00, 'active', 3, '2025-09-24 08:00:00', '2025-12-08 19:34:44'),
	(51, 'KH-2025-0051', 'Lê Hoàng Anh', 'anhhoanle1988@gmail.com', '0901234572', '1988-11-15', 'male', '003088012345', NULL, 'Vietnam', '12 Nguyễn Trãi, Quận 1, TP. Hồ Chí Minh', 'corporate', 'facebook', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-01 04:20:00', '2025-12-08 19:34:44'),
	(52, 'KH-2025-0052', 'Lê Thị Hường', 'huongle1992@gmail.com', '0912345683', '1992-06-28', 'female', '003092023456', NULL, 'Vietnam', '34 Trần Hưng Đạo, Hoàn Kiếm, Hà Nội', 'individual', 'zalo', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-08 07:40:00', '2025-12-08 19:34:44'),
	(53, 'KH-2025-0053', 'Lê Minh Quang', 'quangminh1987@yahoo.com', '0923456794', '1987-08-22', 'male', '003087034567', NULL, 'Vietnam', '45 Pasteur, Quận 3, TP. Hồ Chí Minh', 'individual', 'phone', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-15 03:15:00', '2025-12-08 19:34:44'),
	(54, 'KH-2025-0054', 'Lê Thị Ngọc Anh', 'ngocanhle@gmail.com', '0934567895', '1994-02-14', 'female', '003094045678', NULL, 'Vietnam', '67 Lê Duẩn, Hải Châu, Đà Nẵng', 'individual', 'email', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-22 09:00:00', '2025-12-08 19:34:44'),
	(55, 'KH-2025-0055', 'Lê Văn Phúc', 'phucle1991@gmail.com', '0945678906', '1991-10-05', 'male', '003091056789', NULL, 'Vietnam', '12 Đinh Tiên Hoàng, Quận 1, TP. Hồ Chí Minh', 'individual', 'walk_in', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-29 02:30:00', '2025-12-08 19:34:44'),
	(56, 'KH-2025-0056', 'Lê Thị Thanh Hương', 'thanhhuongle@yahoo.com', '0956789017', '1993-12-18', 'female', '003093067890', NULL, 'Vietnam', '23 Võ Thị Sáu, Quận 3, TP. Hồ Chí Minh', 'individual', 'facebook', NULL, NULL, 0, 0.00, 'active', 3, '2025-11-05 06:45:00', '2025-12-08 19:34:44'),
	(57, 'KH-2025-0057', 'Lê Quốc Tuấn', 'quoctuan1986@gmail.com', '0967890128', '1986-05-30', 'male', '003086078901', NULL, 'Vietnam', '45 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh', 'individual', 'zalo', NULL, NULL, 0, 0.00, 'active', 2, '2025-11-06 03:20:00', '2025-12-08 19:34:44'),
	(58, 'KH-2025-0058', 'Lê Thị Bích Loan', 'bichloanle1995@gmail.com', '0978901239', '1995-07-07', 'female', '003095089012', NULL, 'Vietnam', '56 Lý Tự Trọng, Hoàn Kiếm, Hà Nội', 'individual', 'phone', NULL, NULL, 0, 0.00, 'active', 3, '2025-11-07 08:30:00', '2025-12-08 19:34:44'),
	(59, 'KH-2025-0059', 'Lê Đức Thắng', 'thangle1989@yahoo.com', '0989012350', '1989-03-11', 'male', '003089090123', NULL, 'Vietnam', '78 Hai Bà Trưng, Quận 1, TP. Hồ Chí Minh', 'individual', 'email', NULL, NULL, 0, 0.00, 'active', 2, '2025-11-08 04:00:00', '2025-12-08 19:34:44'),
	(60, 'KH-2025-0060', 'Lê Thị Phương Thảo', 'phuongthao1992@gmail.com', '0990123461', '1992-09-24', 'female', '003092001234', NULL, 'Vietnam', '12 Phan Chu Trinh, Hải Châu, Đà Nẵng', 'individual', 'facebook', NULL, NULL, 0, 0.00, 'active', 3, '2025-11-09 07:15:00', '2025-12-08 19:34:44'),
	(61, 'KH-2025-0061', 'Phạm Văn Hải', 'haipham1985@gmail.com', '0901234573', '1985-02-18', 'male', '004085012345', NULL, 'Vietnam', '34 Lê Lai, Quận 1, TP. Hồ Chí Minh', 'group', 'phone', NULL, NULL, 0, 0.00, 'active', 2, '2025-09-30 03:00:00', '2025-12-08 19:34:44'),
	(62, 'KH-2025-0062', 'Phạm Thị Loan', 'loanpham1990@yahoo.com', '0912345684', '1990-08-25', 'female', '004090023456', NULL, 'Vietnam', '45 Trường Chinh, Thanh Xuân, Hà Nội', 'individual', 'email', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-12 06:20:00', '2025-12-08 19:34:44'),
	(63, 'KH-2025-0063', 'Phạm Đức An', 'anpham1988@gmail.com', '0923456795', '1988-10-10', 'male', '004088034567', NULL, 'Vietnam', '67 Nguyễn Văn Linh, Hải Châu, Đà Nẵng', 'individual', 'facebook', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-19 02:45:00', '2025-12-08 19:34:44'),
	(64, 'KH-2025-0064', 'Phạm Thị Thu', 'thupham1992@gmail.com', '0934567896', '1992-05-15', 'female', '004092045678', NULL, 'Vietnam', '12 Đồng Khởi, Quận 1, TP. Hồ Chí Minh', 'individual', 'zalo', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-26 09:10:00', '2025-12-08 19:34:44'),
	(65, 'KH-2025-0065', 'Phạm Quang Huy', 'huypham1991@yahoo.com', '0945678907', '1991-12-20', 'male', '004091056789', NULL, 'Vietnam', '23 Lý Thường Kiệt, Hoàn Kiếm, Hà Nội', 'individual', 'phone', NULL, NULL, 0, 0.00, 'active', 2, '2025-11-10 03:30:00', '2025-12-08 19:34:44'),
	(66, 'KH-2025-0066', 'Phạm Thị Hồng Ngọc', 'hongngocpham@gmail.com', '0956789018', '1994-07-08', 'female', '004094067890', NULL, 'Vietnam', '45 Trần Phú, Nha Trang, Khánh Hòa', 'individual', 'walk_in', NULL, NULL, 0, 0.00, 'active', 3, '2025-11-11 08:00:00', '2025-12-08 19:34:44'),
	(67, 'KH-2025-0067', 'Phạm Văn Tuấn', 'tuanpham1987@gmail.com', '0967890129', '1987-04-19', 'male', '004087078901', NULL, 'Vietnam', '56 Nguyễn Trãi, Quận 5, TP. Hồ Chí Minh', 'corporate', 'email', NULL, NULL, 0, 0.00, 'active', 2, '2025-11-12 04:20:00', '2025-12-08 19:34:44'),
	(68, 'KH-2025-0068', 'Phạm Thị Duyên', 'duyenpham1993@yahoo.com', '0978901240', '1993-11-03', 'female', '004093089012', NULL, 'Vietnam', '78 Phan Châu Trinh, Hải Châu, Đà Nẵng', 'individual', 'facebook', NULL, NULL, 0, 0.00, 'active', 3, '2025-11-13 07:40:00', '2025-12-08 19:34:44'),
	(69, 'KH-2025-0069', 'Hoàng Văn Long', 'longhoang1985@gmail.com', '0989012351', '1985-06-12', 'male', '005085090123', NULL, 'Vietnam', '12 Hai Bà Trưng, Quận 1, TP. Hồ Chí Minh', 'individual', 'zalo', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-02 02:15:00', '2025-12-08 19:34:44'),
	(70, 'KH-2025-0070', 'Phan Thị Hương', 'huongphan1990@gmail.com', '0990123462', '1990-09-20', 'female', '006090001234', NULL, 'Vietnam', '34 Lý Tự Trọng, Ba Đình, Hà Nội', 'individual', 'phone', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-09 08:30:00', '2025-12-08 19:34:44'),
	(71, 'KH-2025-0071', 'Vũ Đức Thắng', 'thangvu1988@yahoo.com', '0901234574', '1988-12-30', 'male', '007088012345', NULL, 'Vietnam', '45 Lê Lợi, Hải Châu, Đà Nẵng', 'individual', 'email', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-16 04:00:00', '2025-12-08 19:34:44'),
	(72, 'KH-2025-0072', 'Đặng Thị Lan', 'landang1992@gmail.com', '0912345685', '1992-04-25', 'female', '008092023456', NULL, 'Vietnam', '67 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh', 'individual', 'facebook', NULL, NULL, 0, 0.00, 'active', 3, '2025-10-23 07:20:00', '2025-12-08 19:34:44'),
	(73, 'KH-2025-0073', 'Hoàng Minh Tuấn', 'tuanhoang1991@gmail.com', '0923456796', '1991-07-18', 'male', '005091034567', NULL, 'Vietnam', '12 Trần Hưng Đạo, Hoàn Kiếm, Hà Nội', 'individual', 'zalo', NULL, NULL, 0, 0.00, 'active', 2, '2025-10-30 03:40:00', '2025-12-08 19:34:44'),
	(74, 'KH-2025-0074', 'Phan Thị My', 'myphan1994@yahoo.com', '0934567897', '1994-11-07', 'female', '006094045678', NULL, 'Vietnam', '23 Lê Lai, Quận 1, TP. Hồ Chí Minh', 'individual', 'phone', NULL, NULL, 0, 0.00, 'active', 3, '2025-11-14 02:30:00', '2025-12-08 19:34:44'),
	(75, 'KH-2025-0075', 'Vũ Quang Anh', 'anhvu1987@gmail.com', '0945678908', '1987-03-14', 'male', '007087056789', NULL, 'Vietnam', '45 Đinh Tiên Hoàng, Hoàn Kiếm, Hà Nội', 'group', 'walk_in', NULL, NULL, 0, 0.00, 'active', 2, '2025-11-15 09:00:00', '2025-12-08 19:34:44'),
	(76, 'KH-2025-0076', 'Đặng Thị Ngọc', 'ngocdang1993@gmail.com', '0956789019', '1993-08-29', 'female', '008093067890', NULL, 'Vietnam', '56 Pasteur, Quận 1, TP. Hồ Chí Minh', 'individual', 'email', NULL, NULL, 0, 0.00, 'active', 3, '2025-11-16 04:45:00', '2025-12-08 19:34:44'),
	(77, 'KH-2025-0077', 'Hoàng Văn Phúc', 'phuchoang1989@yahoo.com', '0967890130', '1989-01-22', 'male', '005089078901', NULL, 'Vietnam', '78 Lê Duẩn, Hải Châu, Đà Nẵng', 'individual', 'facebook', NULL, NULL, 0, 0.00, 'active', 2, '2025-11-17 06:20:00', '2025-12-08 19:34:44'),
	(78, 'KH-2025-0078', 'Phan Thị Thanh', 'thanhphan1995@gmail.com', '0978901241', '1995-10-16', 'female', '006095089012', NULL, 'Vietnam', '12 Nguyễn Thái Học, Ba Đình, Hà Nội', 'individual', 'zalo', NULL, NULL, 0, 0.00, 'active', 3, '2025-11-18 08:50:00', '2025-12-08 19:34:44'),
	(79, 'KH-2025-0079', 'Vũ Tiến Dũng', 'dungvu1986@gmail.com', '0989012352', '1986-05-05', 'male', '007086090123', NULL, 'Vietnam', '34 Trần Phú, Nha Trang, Khánh Hòa', 'individual', 'phone', NULL, NULL, 0, 0.00, 'active', 2, '2025-11-19 03:10:00', '2025-12-08 19:34:44'),
	(80, 'KH-2025-0080', 'Đặng Thị Hoa', 'hoadang1992@yahoo.com', '0990123463', '1992-12-28', 'female', '008092001234', NULL, 'Vietnam', '45 Võ Văn Tần, Quận 3, TP. Hồ Chí Minh', 'individual', 'email', NULL, NULL, 0, 0.00, 'active', 3, '2025-11-20 07:30:00', '2025-12-08 19:34:44');

-- Dumping structure for table duan.customer_checkins
CREATE TABLE IF NOT EXISTS `customer_checkins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `checkin_time` timestamp NOT NULL,
  `status` enum('present','absent','late') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'present',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `checked_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `customer_id` (`customer_id`),
  KEY `checked_by` (`checked_by`),
  CONSTRAINT `customer_checkins_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  CONSTRAINT `customer_checkins_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `customer_checkins_ibfk_3` FOREIGN KEY (`checked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.customer_checkins: ~0 rows (approximately)

-- Dumping structure for table duan.customer_import_logs
CREATE TABLE IF NOT EXISTS `customer_import_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imported_by` int DEFAULT NULL,
  `total_rows` int DEFAULT '0',
  `success_count` int DEFAULT '0',
  `error_count` int DEFAULT '0',
  `error_details` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `imported_by` (`imported_by`),
  CONSTRAINT `customer_import_logs_ibfk_1` FOREIGN KEY (`imported_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.customer_import_logs: ~0 rows (approximately)

-- Dumping structure for table duan.destinations
CREATE TABLE IF NOT EXISTS `destinations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `province_id` int DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `locations` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  KEY `province_id` (`province_id`),
  KEY `country_id` (`country_id`),
  CONSTRAINT `destinations_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `destinations_ibfk_3` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `destinations_ibfk_country` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `destinations_ibfk_province` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.destinations: ~0 rows (approximately)
INSERT INTO `destinations` (`id`, `province_id`, `country_id`, `name`, `description`, `locations`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'Hồ Hoàn Kiếm', 'Biểu tượng của Hà Nội, nơi gắn liền với truyền thuyết Hồ Gươm', 'Quận Hoàn Kiếm, Hà Nội', 'active', 1, NULL, '2025-01-10 01:00:00', '2025-12-08 19:30:00'),
	(2, 1, 1, 'Văn Miếu - Quốc Tử Giám', 'Di tích lịch sử văn hóa quan trọng, trường đại học đầu tiên của Việt Nam', 'Quận Đống Đa, Hà Nội', 'active', 1, NULL, '2025-01-10 01:00:00', '2025-12-08 19:30:00'),
	(3, 7, 1, 'Vịnh Hạ Long', 'Di sản thiên nhiên thế giới UNESCO với hàng nghìn hòn đảo đá vôi', 'TP. Hạ Long, Quảng Ninh', 'active', 1, NULL, '2025-01-10 02:00:00', '2025-12-08 19:30:00'),
	(4, 7, 1, 'Hang Sửng Sốt', 'Hang động lớn nhất vịnh Hạ Long với nhũ đá tuyệt đẹp', 'Đảo Bồ Hòn, Hạ Long, Quảng Ninh', 'active', 1, NULL, '2025-01-10 02:00:00', '2025-12-08 19:30:00'),
	(5, 7, 1, 'Đảo Cát Bà', 'Đảo lớn nhất vịnh Hạ Long, nổi tiếng với biển đẹp và vườn quốc gia', 'Huyện Cát Hải, Hải Phòng/Quảng Ninh', 'active', 1, NULL, '2025-01-10 03:00:00', '2025-12-08 19:30:00'),
	(6, 11, 1, 'Sapa - Ruộng Bậc Thang', 'Thị trấn miền núi với ruộng bậc thang tuyệt đẹp và văn hóa dân tộc', 'Thị xã Sapa, Lào Cai', 'active', 1, NULL, '2025-01-10 04:00:00', '2025-12-08 19:30:00'),
	(7, 11, 1, 'Đỉnh Fansipan', 'Nóc nhà Đông Dương, cao 3.143m, có cáp treo hiện đại', 'Sapa, Lào Cai', 'active', 1, NULL, '2025-01-10 04:00:00', '2025-12-08 19:30:00'),
	(8, 10, 1, 'Tràng An', 'Quần thể danh thắng UNESCO, du lịch sinh thái sông nước', 'Huyện Hoa Lư, Ninh Bình', 'active', 1, NULL, '2025-01-10 05:00:00', '2025-12-08 19:30:00'),
	(9, 10, 1, 'Hang Múa', 'Điểm ngắm toàn cảnh Tràng An từ trên cao sau 500 bậc thang', 'Huyện Hoa Lư, Ninh Bình', 'active', 1, NULL, '2025-01-10 05:00:00', '2025-12-08 19:30:00'),
	(10, 13, 1, 'Thác Bản Giốc', 'Thác nước lớn nhất Việt Nam, nằm trên biên giới Việt - Trung', 'Huyện Trùng Khánh, Cao Bằng', 'active', 1, NULL, '2025-01-10 06:00:00', '2025-12-08 19:30:00'),
	(11, 4, 1, 'Bà Nà Hills', 'Khu du lịch nghỉ dưỡng trên núi với Cầu Vàng nổi tiếng', 'Hòa Vang, Đà Nẵng', 'active', 1, NULL, '2025-01-11 01:00:00', '2025-12-08 19:30:12'),
	(12, 4, 1, 'Cầu Vàng', 'Cây cầu vàng được nâng đỡ bởi đôi bàn tay khổng lồ', 'Bà Nà Hills, Đà Nẵng', 'active', 1, NULL, '2025-01-11 01:00:00', '2025-12-08 19:30:12'),
	(13, 4, 1, 'Bãi biển Mỹ Khê', 'Bãi biển đẹp nhất hành tinh do Forbes bình chọn', 'Quận Sơn Trà, Đà Nẵng', 'active', 1, NULL, '2025-01-11 02:00:00', '2025-12-08 19:30:12'),
	(14, 4, 1, 'Hội An - Phố Cổ', 'Phố cổ được UNESCO công nhận di sản văn hóa thế giới', 'TP. Hội An, Quảng Nam (gần Đà Nẵng)', 'active', 1, NULL, '2025-01-11 03:00:00', '2025-12-08 19:30:12'),
	(15, 4, 1, 'Rừng Dừa Bảy Mẫu', 'Rừng dừa nước nguyên sinh, trải nghiệm thúng chai', 'Cẩm Thanh, Hội An', 'active', 1, NULL, '2025-01-11 03:00:00', '2025-12-08 19:30:12'),
	(16, 6, 1, 'Cố đô Huế - Đại Nội', 'Quần thể di tích cố đô triều Nguyễn, UNESCO', 'TP. Huế', 'active', 1, NULL, '2025-01-11 04:00:00', '2025-12-08 19:30:12'),
	(17, 6, 1, 'Lăng Khải Định', 'Lăng mộ vua Khải Định với kiến trúc Đông Tây kết hợp', 'Hương Thủy, Huế', 'active', 1, NULL, '2025-01-11 04:00:00', '2025-12-08 19:30:12'),
	(18, 6, 1, 'Động Phong Nha', 'Vườn quốc gia Phong Nha-Kẻ Bàng, hang động lớn nhất thế giới', 'Bố Trạch, Quảng Bình (gần Huế)', 'active', 1, NULL, '2025-01-11 05:00:00', '2025-12-08 19:30:12'),
	(19, 2, 1, 'Dinh Độc Lập', 'Công trình kiến trúc lịch sử quan trọng của TP.HCM', 'Quận 1, TP. Hồ Chí Minh', 'active', 1, NULL, '2025-01-12 01:00:00', '2025-12-08 19:30:12'),
	(20, 2, 1, 'Chợ Bến Thành', 'Chợ truyền thống lâu đời nhất Sài Gòn', 'Quận 1, TP. Hồ Chí Minh', 'active', 1, NULL, '2025-01-12 01:00:00', '2025-12-08 19:30:12'),
	(21, 5, 1, 'Chợ nổi Cái Răng', 'Chợ nổi lớn nhất miền Tây trên sông Hậu', 'Cái Răng, Cần Thơ', 'active', 1, NULL, '2025-01-12 02:00:00', '2025-12-08 19:30:12'),
	(22, 14, 1, 'Phú Quốc - Bãi Sao', 'Đảo ngọc với bãi biển cát trắng nước trong xanh', 'Phú Quốc, Kiên Giang', 'active', 1, NULL, '2025-01-12 03:00:00', '2025-12-08 19:30:12'),
	(23, 8, 1, 'Nha Trang - Vinpearl', 'Thành phố biển với bãi tắm đẹp và khu vui chơi Vinpearl', 'TP. Nha Trang, Khánh Hòa', 'active', 1, NULL, '2025-01-12 04:00:00', '2025-12-08 19:30:12'),
	(24, 9, 1, 'Đà Lạt - Hồ Xuân Hương', 'Thành phố ngàn hoa với khí hậu mát mẻ', 'TP. Đà Lạt, Lâm Đồng', 'active', 1, NULL, '2025-01-12 05:00:00', '2025-12-08 19:30:12'),
	(25, 15, 1, 'Vũng Tàu - Tượng Chúa Kitô', 'Thành phố biển gần Sài Gòn với tượng Chúa cao 32m', 'TP. Vũng Tàu, Bà Rịa - Vũng Tàu', 'active', 1, NULL, '2025-01-12 06:00:00', '2025-12-08 19:30:12');

-- Dumping structure for table duan.destination_images
CREATE TABLE IF NOT EXISTS `destination_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `destination_id` int NOT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT '0',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `destination_id` (`destination_id`),
  CONSTRAINT `destination_images_ibfk_1` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.destination_images: ~0 rows (approximately)

-- Dumping structure for table duan.discount_codes
CREATE TABLE IF NOT EXISTS `discount_codes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_type` enum('percentage','fixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_value` decimal(15,2) NOT NULL,
  `min_purchase` decimal(15,2) DEFAULT '0.00',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `usage_limit` int DEFAULT '0',
  `used_count` int DEFAULT '0',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `discount_codes_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.discount_codes: ~0 rows (approximately)
INSERT INTO `discount_codes` (`id`, `code`, `name`, `discount_type`, `discount_value`, `min_purchase`, `start_date`, `end_date`, `usage_limit`, `used_count`, `status`, `created_by`, `created_at`) VALUES
	(1, 'SUMMER2025', 'Ưu đãi mùa hè 2025', 'percentage', 10.00, 5000000.00, '2025-06-01', '2025-08-31', 100, 0, 'active', 1, '2025-01-10 01:00:00'),
	(2, 'TETDAT25', 'Tết Ất Tỵ 2025', 'percentage', 15.00, 10000000.00, '2025-01-15', '2025-02-15', 50, 0, 'active', 1, '2025-01-10 01:30:00'),
	(3, 'GROUP10', 'Ưu đãi nhóm 10 người', 'fixed', 500000.00, 0.00, '2025-01-01', '2025-12-31', 200, 0, 'active', 1, '2025-01-10 02:00:00'),
	(4, 'LOYAL100', 'Khách hàng VIP', 'fixed', 1000000.00, 8000000.00, '2025-01-01', '2025-12-31', 0, 0, 'active', 1, '2025-01-10 02:30:00'),
	(5, 'AUTUMN2025', 'Ưu đãi mùa thu', 'percentage', 8.00, 4000000.00, '2025-09-01', '2025-11-30', 150, 12, 'active', 1, '2025-01-10 03:00:00');

-- Dumping structure for table duan.drivers
CREATE TABLE IF NOT EXISTS `drivers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `driver_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã tài xế (VD: "DRV001")',
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_card` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CMND/CCCD',
  `license_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Số bằng lái',
  `license_type` enum('A1','A2','B1','B2','C','D','E','F') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hạng bằng',
  `license_issue_date` date DEFAULT NULL,
  `license_expiry_date` date DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `emergency_contact_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Người liên hệ khẩn cấp',
  `emergency_contact_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','on_trip','off_duty','suspended','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `hire_date` date DEFAULT NULL COMMENT 'Ngày bắt đầu làm việc',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `driver_code` (`driver_code`),
  KEY `idx_drivers_status` (`status`),
  KEY `idx_drivers_license_type` (`license_type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.drivers: ~0 rows (approximately)
INSERT INTO `drivers` (`id`, `driver_code`, `full_name`, `phone`, `email`, `id_card`, `license_number`, `license_type`, `license_issue_date`, `license_expiry_date`, `date_of_birth`, `address`, `emergency_contact_name`, `emergency_contact_phone`, `status`, `hire_date`, `notes`, `created_at`, `updated_at`) VALUES
	(2, 'DRV001', 'Hoàng Long Lưu', '0965932120', 'long16@gmail.com', NULL, '09876543210', 'C', '2024-02-09', '2031-03-09', NULL, NULL, NULL, NULL, 'active', NULL, NULL, '2025-12-08 20:06:52', '2025-12-08 20:06:52'),
	(3, 'DRV002', 'Nguyễn Long Chung', '0987654321', 'Chung@gmail.com', NULL, '09876543765', 'B2', '2022-02-22', '2019-09-09', NULL, NULL, NULL, NULL, 'active', NULL, NULL, '2025-12-08 20:07:45', '2025-12-08 20:07:45'),
	(4, 'DRV003', 'Lê Quốc Long', '0987678949', 'LongTran@gmail.com', NULL, '09878987631', 'B1', '2000-08-09', NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, '2025-12-08 20:08:32', '2025-12-08 20:08:32');

-- Dumping structure for table duan.driver_schedules
CREATE TABLE IF NOT EXISTS `driver_schedules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `driver_id` int NOT NULL,
  `tour_schedule_id` int NOT NULL,
  `vehicle_assignment_id` int DEFAULT NULL,
  `schedule_date` date NOT NULL COMMENT 'Ngày làm việc',
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `work_hours` decimal(5,2) DEFAULT '0.00' COMMENT 'Số giờ làm việc',
  `overtime_hours` decimal(5,2) DEFAULT '0.00' COMMENT 'Số giờ làm thêm',
  `status` enum('scheduled','confirmed','in_progress','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'scheduled',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `driver_id` (`driver_id`),
  KEY `tour_schedule_id` (`tour_schedule_id`),
  KEY `vehicle_assignment_id` (`vehicle_assignment_id`),
  KEY `schedule_date` (`schedule_date`),
  KEY `idx_driver_schedules_status` (`status`),
  CONSTRAINT `driver_schedules_ibfk_assignment` FOREIGN KEY (`vehicle_assignment_id`) REFERENCES `vehicle_assignments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `driver_schedules_ibfk_driver` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `driver_schedules_ibfk_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.driver_schedules: ~0 rows (approximately)

-- Dumping structure for table duan.email_logs
CREATE TABLE IF NOT EXISTS `email_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email_to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `email_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_id` int DEFAULT NULL,
  `related_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','sent','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.email_logs: ~0 rows (approximately)

-- Dumping structure for table duan.email_templates
CREATE TABLE IF NOT EXISTS `email_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `variables` json DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.email_templates: ~0 rows (approximately)

-- Dumping structure for table duan.incurred_expenses
CREATE TABLE IF NOT EXISTS `incurred_expenses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int DEFAULT NULL COMMENT 'Có thể NULL nếu chi phí theo tour',
  `tour_schedule_id` int DEFAULT NULL COMMENT 'Link với tour schedule',
  `expense_date` date NOT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `receipt_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reported_by` int DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `reported_by` (`reported_by`),
  KEY `approved_by` (`approved_by`),
  KEY `idx_incurred_expenses_booking_id` (`booking_id`),
  KEY `idx_incurred_expenses_approval_status` (`approval_status`),
  KEY `idx_tour_schedule_id` (`tour_schedule_id`),
  CONSTRAINT `incurred_expenses_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  CONSTRAINT `incurred_expenses_ibfk_2` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `incurred_expenses_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `incurred_expenses_ibfk_tour_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.incurred_expenses: ~0 rows (approximately)
INSERT INTO `incurred_expenses` (`id`, `booking_id`, `tour_schedule_id`, `expense_date`, `category`, `description`, `amount`, `receipt_file`, `reported_by`, `approved_by`, `approval_status`, `notes`, `created_at`) VALUES
	(1, 3, 2, '2025-10-06', 'Ăn uống', 'Thêm 5 suất ăn chay cho khách yêu cầu đặc biệt', 1250000.00, NULL, 6, 2, 'approved', 'Booking #3: Khách ăn chay, nhà hàng chuẩn bị menu riêng', '2025-10-06 13:00:00'),
	(2, 8, 7, '2025-10-22', 'Y tế', 'Mua thuốc tiêu hóa cho khách booking #8', 150000.00, NULL, 10, 2, 'approved', 'Khách ăn hải sản bị dị ứng nhẹ', '2025-10-22 15:00:00'),
	(3, 10, 9, '2025-10-28', 'Khác', 'Phí visa khẩn cấp cho 1 khách booking #10', 2000000.00, NULL, 6, 3, 'pending', 'Khách quên passport, làm visa gấp. Chờ hoàn tiền', '2025-10-28 01:00:00'),
	(4, 12, 10, '2025-10-29', 'Ăn uống', 'Buffet sinh nhật cho khách booking #12', 3000000.00, NULL, 7, NULL, 'pending', 'Gia đình booking #12 tổ chức sinh nhật trên tour', '2025-10-29 12:00:00'),
	(5, NULL, 1, '2025-10-03', 'Phương tiện', 'Sửa lốp xe bus giữa đường Hà Nội - Hạ Long', 500000.00, NULL, 5, 2, 'approved', 'Xe tour bị đinh, thay lốp dự phòng. Chi phí chung cả đoàn', '2025-10-03 11:00:00'),
	(6, NULL, 3, '2025-10-11', 'Vé tham quan', 'Mua thêm vé Bảo tàng Quảng Ninh cho cả đoàn', 800000.00, NULL, 7, 3, 'approved', 'Cả đoàn 30 người muốn thêm thăm bảo tàng (800K/30 = ~27K/người)', '2025-10-11 09:00:00'),
	(7, NULL, 2, '2025-10-07', 'Phương tiện', 'Thêm tiền parking tại Bà Nà Hills', 200000.00, NULL, 6, 2, 'approved', 'Parking fee ngoài dự tính cho xe bus', '2025-10-07 12:00:00'),
	(8, NULL, 9, '2025-10-28', 'Khác', 'Tip cho lái xe và hướng dẫn viên địa phương Bangkok', 1500000.00, NULL, 6, 3, 'approved', 'Chi phí tip theo quy định tour quốc tế', '2025-10-28 13:00:00'),
	(9, NULL, 7, '2025-10-23', 'Phương tiện', 'Thuê thêm xe đưa đón sân bay Phú Quốc', 800000.00, NULL, 10, 2, 'approved', 'Xe chính bị hỏng, thuê thêm xe dự phòng', '2025-10-23 08:00:00'),
	(10, NULL, 1, '2025-10-04', 'Ăn uống', 'Nước suối và snack cho cả đoàn khi leo Hang Múa', 300000.00, NULL, 5, 2, 'approved', 'Mua 60 chai nước + snack cho 28 người', '2025-10-04 04:00:00');

-- Dumping structure for table duan.invoices
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `invoice_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `tax_amount` decimal(15,2) DEFAULT '0.00',
  `discount_amount` decimal(15,2) DEFAULT '0.00',
  `total_amount` decimal(15,2) NOT NULL,
  `status` enum('draft','issued','paid','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `booking_id` (`booking_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.invoices: ~0 rows (approximately)
INSERT INTO `invoices` (`id`, `booking_id`, `invoice_number`, `invoice_date`, `subtotal`, `tax_amount`, `discount_amount`, `total_amount`, `status`, `created_by`, `created_at`) VALUES
	(1, 1, 'INV-2025-0001', '2025-09-30', 12500000.00, 1250000.00, 0.00, 13750000.00, 'paid', 2, '2025-09-30 04:00:00'),
	(2, 2, 'INV-2025-0002', '2025-10-01', 8280000.00, 828000.00, 720000.00, 9108000.00, 'paid', 3, '2025-10-01 05:00:00'),
	(3, 3, 'INV-2025-0003', '2025-10-02', 20600000.00, 2060000.00, 0.00, 22660000.00, 'paid', 2, '2025-10-02 08:00:00');

-- Dumping structure for table duan.itineraries
CREATE TABLE IF NOT EXISTS `itineraries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL,
  `destination_id` int DEFAULT NULL,
  `day_number` int NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `meals` json DEFAULT NULL,
  `accommodation` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `departure_time` time DEFAULT NULL,
  `display_order` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  KEY `destination_id` (`destination_id`),
  CONSTRAINT `itineraries_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE,
  CONSTRAINT `itineraries_ibfk_2` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.itineraries: ~0 rows (approximately)
INSERT INTO `itineraries` (`id`, `tour_id`, `destination_id`, `day_number`, `title`, `description`, `meals`, `accommodation`, `arrival_time`, `departure_time`, `display_order`) VALUES
	(1, 1, 1, 1, 'Hà Nội - Hạ Long', 'Khởi hành từ Hà Nội lúc 7h00 sáng, di chuyển 170km đến Hạ Long. 12h00 đến bến tàu, lên du thuyền 5 sao bắt đầu hành trình khám phá Vịnh Hạ Long. Thăm hang Sửng Sốt, chèo kayak. Chiều check-in khách sạn, tối tự do khám phá Hạ Long về đêm.', '{"lunch": true, "dinner": true, "breakfast": false}', 'Vinpearl Hotel Hạ Long', NULL, NULL, 1),
	(2, 1, 7, 2, 'Hạ Long - Ninh Bình', 'Sau bữa sáng, khởi hành đi Ninh Bình (100km). Tham quan Tràng An - chèo thuyền qua hang động, leo 500 bậc thang Hang Múa ngắm toàn cảnh. Chiều tham quan chùa Bái Đính - chùa lớn nhất Việt Nam. Tối về khách sạn nghỉ ngơi.', '{"lunch": true, "dinner": true, "breakfast": true}', 'Khách sạn Ninh Bình', NULL, NULL, 2),
	(3, 1, 1, 3, 'Ninh Bình - Hà Nội', 'Ăn sáng, tham quan thêm một số điểm tại Ninh Bình. 11h khởi hành về Hà Nội, ăn trưa trên đường. 15h về đến Hà Nội, kết thúc chương trình.', '{"lunch": true, "dinner": false, "breakfast": true}', NULL, NULL, NULL, 3),
	(4, 2, 11, 1, 'Đà Nẵng - Bà Nà Hills', 'Đón tại sân bay Đà Nẵng, đưa về khách sạn nghỉ ngơi. Chiều tham quan Bà Nà Hills, chinh phục Cầu Vàng, vui chơi tại Fantasy Park. Tối về khách sạn.', '{"lunch": true, "dinner": true, "breakfast": false}', 'Mường Thanh Grand Đà Nẵng', NULL, NULL, 1),
	(5, 2, 13, 2, 'Đà Nẵng - City Tour', 'Tham quan Bãi biển Mỹ Khê, Ngũ Hành Sơn, Chùa Linh Ứng. Chiều tự do tắm biển. Tối dạo phố ven bãi biển Mỹ Khê, thưởng thức hải sản.', '{"lunch": true, "dinner": false, "breakfast": true}', 'Mường Thanh Grand Đà Nẵng', NULL, NULL, 2),
	(6, 2, 14, 3, 'Đà Nẵng - Hội An', 'Khởi hành đi Hội An (30km). Tham quan phố cổ Hội An, chùa Cầu, nhà cổ, chợ Hội An. Chiều trải nghiệm chèo thuyền thúng tại rừng dừa Bảy Mẫu. Tối thả đèn hoa đăng trên sông Hoài.', '{"lunch": true, "dinner": true, "breakfast": true}', 'Mường Thanh Grand Đà Nẵng', NULL, NULL, 3),
	(7, 2, 13, 4, 'Đà Nẵng - Bay về', 'Tự do tắm biển buổi sáng. Trưa trả phòng, xe đưa ra sân bay. Kết thúc chương trình.', '{"lunch": true, "dinner": false, "breakfast": true}', NULL, NULL, NULL, 4),
	(8, 3, 19, 1, 'Sài Gòn - Mỹ Tho', '7h khởi hành từ Sài Gòn đi Mỹ Tho. Đi thuyền trên sông Tiền, tham quan làng nghề kẹo dừa, thưởng thức trái cây miệt vườn, nghe đờn ca tài tử. Chiều về Cần Thơ nghỉ ngơi.', '{"lunch": true, "dinner": true, "breakfast": false}', 'Khách sạn Cần Thơ', NULL, NULL, 1),
	(9, 3, 21, 2, 'Cần Thơ - Chợ nổi', '5h sáng đi thuyền tham quan chợ nổi Cái Răng sôi động. Ăn sáng trên thuyền. Tham quan vườn trái cây, làm kẹo dừa. Chiều về Sài Gòn.', '{"lunch": true, "dinner": true, "breakfast": true}', 'Khách sạn Cần Thơ', NULL, NULL, 2),
	(10, 3, 19, 3, 'Cần Thơ - Sài Gòn', 'Tự do tham quan Cần Thơ buổi sáng. 10h khởi hành về Sài Gòn. 15h về đến Sài Gòn, kết thúc.', '{"lunch": true, "dinner": false, "breakfast": true}', NULL, NULL, NULL, 3);

-- Dumping structure for table duan.itinerary_day_services
CREATE TABLE IF NOT EXISTS `itinerary_day_services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `itinerary_id` int NOT NULL COMMENT 'Foreign key → itineraries',
  `service_id` int NOT NULL COMMENT 'Foreign key → services',
  `service_provider_id` int DEFAULT NULL COMMENT 'Foreign key → service_providers (khách sạn, nhà hàng cụ thể)',
  `service_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tên dịch vụ (snapshot)',
  `unit_price` decimal(15,2) NOT NULL COMMENT 'Đơn giá/người',
  `quantity` decimal(10,2) NOT NULL DEFAULT '1.00' COMMENT 'Số lượng (VD: 1 bữa, 1 đêm)',
  `unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Đơn vị (VD: "bữa", "đêm", "vé")',
  `is_included_in_price` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Bao gồm trong giá tour',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_itinerary_id` (`itinerary_id`),
  KEY `idx_service_id` (`service_id`),
  KEY `idx_service_provider_id` (`service_provider_id`),
  KEY `idx_is_included` (`is_included_in_price`),
  KEY `idx_itinerary_service_included` (`itinerary_id`,`is_included_in_price`,`unit_price`),
  CONSTRAINT `fk_itinerary_day_services_itinerary` FOREIGN KEY (`itinerary_id`) REFERENCES `itineraries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_itinerary_day_services_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_itinerary_day_services_service_provider` FOREIGN KEY (`service_provider_id`) REFERENCES `service_providers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Dịch vụ theo từng ngày của tour (để tính chi phí)';

-- Dumping data for table duan.itinerary_day_services: ~0 rows (approximately)

-- Dumping structure for table duan.journals
CREATE TABLE IF NOT EXISTS `journals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_schedule_id` int NOT NULL COMMENT 'Foreign key → tour_schedules (journal theo tour)',
  `booking_id` int DEFAULT NULL COMMENT 'Giữ lại để backward compatible, có thể NULL',
  `guide_id` int NOT NULL,
  `journal_date` date NOT NULL,
  `day_number` int DEFAULT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `weather` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `highlights` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `issues` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `guide_id` (`guide_id`),
  KEY `idx_journals_tour_schedule_id` (`tour_schedule_id`),
  CONSTRAINT `journals_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  CONSTRAINT `journals_ibfk_2` FOREIGN KEY (`guide_id`) REFERENCES `users` (`id`),
  CONSTRAINT `journals_ibfk_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.journals: ~0 rows (approximately)
INSERT INTO `journals` (`id`, `tour_schedule_id`, `booking_id`, `guide_id`, `journal_date`, `day_number`, `title`, `content`, `weather`, `highlights`, `issues`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 5, '2025-10-03', 1, 'Ngày 1: Hà Nội - Hạ Long', 'Đoàn khởi hành đúng giờ lúc 7h sáng từ Hà Nội. Thời tiết khá đẹp, nắng nhẹ. Đoàn 28 người rất nhiệt tình và vui vẻ. 12h đến bến tàu Hạ Long, lên du thuyền 5 sao. Khách rất hài lòng với chất lượng du thuyền. Afternoon chèo kayak và thăm hang Sửng Sốt, mọi người đều phấn khích với cảnh đẹp. Tối về khách sạn, nhóm check-in suôn sẻ.', 'Nắng đẹp, nhiệt độ 26-28°C', 'Khách hài lòng với du thuyền 5 sao. Cảnh Vịnh Hạ Long tuyệt đẹp. Chèo kayak vui vẻ.', 'Không có vấn đề nào.', '2025-10-03 13:00:00', '2025-12-08 19:35:10'),
	(2, 1, 1, 5, '2025-10-04', 2, 'Ngày 2: Hạ Long - Ninh Bình', 'Khởi hành đi Ninh Bình sau bữa sáng buffet. Đoàn tham quan Tràng An, chèo thuyền qua các hang động. Khách rất thích thú với cảnh đẹp "Vịnh Hạ Long trên cạn". Leo 500 bậc thang Hang Múa khá vất vả nhưng view từ trên cao rất đáng. Chiều tham quan chùa Bái Đính - chùa lớn nhất VN. Tối về khách sạn Ninh Bình nghỉ ngơi.', 'Nắng vừa, mát mẻ 24-26°C', 'View từ đỉnh Hang Múa tuyệt đẹp. Chùa Bái Đính uy nghi.', 'Có 2 khách lớn tuổi hơi mệt khi leo Hang Múa, đã hỗ trợ nghỉ giữa đường.', '2025-10-04 14:00:00', '2025-12-08 19:35:10'),
	(3, 2, 3, 6, '2025-10-05', 1, 'Ngày 1: Đà Nẵng - Bà Nà Hills', 'Đón đoàn 22 người tại sân bay Đà Nẵng. Đoàn về khách sạn Mường Thanh nghỉ ngơi. Chiều lên Bà Nà Hills, cáp treo rất đẹp. Khách rất thích Cầu Vàng, check-in rất nhiều ảnh. Vui chơi tại Fantasy Park đến tối. Ăn tối buffet tại Bà Nà. Về khách sạn lúc 21h.', 'Nắng đẹp, mát mẻ trên Bà Nà', 'Cầu Vàng đẹp như mơ. Fantasy Park vui nhộn.', 'Peak hour cáp treo hơi đông, phải chờ 15 phút.', '2025-10-05 15:00:00', '2025-12-08 19:35:10'),
	(4, 2, 3, 6, '2025-10-06', 2, 'Ngày 2: City Tour Đà Nẵng', 'Tham quan Ngũ Hành Sơn, leo núi ngắm cảnh. Đi Chùa Linh Ứng - tượng Phật Quán Thế Âm cao 67m rất uy nghi. Chiều tắm biển Mỹ Khê, nước trong xanh, sóng nhẹ. Khách rất thích biển Đà Nẵng. Tối tự do dạo phố ăn hải sản.', 'Nắng gắt 28-30°C, biển đẹp', 'Biển Mỹ Khê đẹp tuyệt vời. Chùa Linh Ứng linh thiêng.', 'Không có.', '2025-10-06 13:30:00', '2025-12-08 19:35:10'),
	(5, 9, 10, 6, '2025-10-26', 1, 'Ngày 1: Hà Nội - Bangkok', 'Bay sáng sớm từ Hà Nội đến Bangkok. Đoàn 35 người, khá đông. Đến Bangkok lúc 10h (giờ địa phương). Tham quan Grand Palace - Hoàng Cung rất đẹp và Chùa Phật Vàng lung linh. Khách Việt rất thích chụp ảnh. Chiều shopping tại MBK Center. Tối ăn buffet hải sản và xem show ca múa.', 'Nắng nóng Bangkok 32-34°C', 'Grand Palace và Phật Vàng rất ấn tượng. Shopping MBK giá tốt.', 'Thời tiết nóng, phải nhắc đoàn mang nước.', '2025-10-26 15:00:00', '2025-12-08 19:35:10'),
	(6, 9, 10, 6, '2025-10-27', 2, 'Ngày 2: Bangkok - Chợ nổi', 'Đi chợ nổi Damnoen Saduak sáng sớm. Chợ rất sôi động, khách thích mua trái cây trên thuyền. Về Bangkok tham quan Safari World, xem show hải cẩu và voi. Chiều về khách sạn nghỉ. Tối tự do khám phá Bangkok về đêm.', 'Nắng, 30-32°C', 'Chợ nổi độc đáo. Safari World show voi hay.', 'Không có.', '2025-10-27 14:00:00', '2025-12-08 19:35:10'),
	(7, 7, 8, 10, '2025-10-20', 1, 'Ngày 1: Phú Quốc - VinWonders', 'Đón đoàn 20 người tại sân bay Phú Quốc. Về resort JW Marriott check-in. Chiều đi VinWonders Phú Quốc - công viên chủ đề lớn nhất VN. Khách rất thích các trò chơi mạo hiểm. Tối xem show nhạc nước Vinpearl rất hoành tráng.', 'Nắng đẹp, gió biển mát 28-30°C', 'VinWonders rộng lớn, trò chơi đa dạng. Show nhạc nước tuyệt vời.', 'Không có.', '2025-10-20 15:30:00', '2025-12-08 19:35:10'),
	(8, 7, 8, 10, '2025-10-21', 2, 'Ngày 2: Safari & Tắm biển', 'Sáng đi Safari Phú Quốc, xem động vật hoang dã. Có cả hổ, sư tử, hươu cao cổ. Show thú biểu diễn rất vui. Chiều tắm biển Bãi Sao - bãi biển đẹp nhất PQ, nước trong xanh như ngọc. Tối BBQ hải sản bên bờ biển tại resort.', 'Nắng đẹp, biển lặng', 'Safari đa dạng động vật. Bãi Sao cát trắng nước trong tuyệt đẹp.', 'Không có.', '2025-10-21 14:00:00', '2025-12-08 19:35:10');

-- Dumping structure for table duan.journal_images
CREATE TABLE IF NOT EXISTS `journal_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `journal_id` int NOT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `display_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `journal_id` (`journal_id`),
  CONSTRAINT `journal_images_ibfk_1` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.journal_images: ~0 rows (approximately)

-- Dumping structure for table duan.password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.password_resets: ~0 rows (approximately)

-- Dumping structure for table duan.payments
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `payment_method` enum('cash','bank_transfer','credit_card','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'cash',
  `amount` decimal(15,2) NOT NULL,
  `payment_type` enum('deposit','installment','full','refund') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'deposit',
  `transaction_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` date NOT NULL,
  `status` enum('pending','completed','failed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'completed',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `idx_payments_booking_id` (`booking_id`),
  KEY `idx_payments_payment_date` (`payment_date`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.payments: ~0 rows (approximately)
INSERT INTO `payments` (`id`, `booking_id`, `payment_method`, `amount`, `payment_type`, `transaction_id`, `receipt_number`, `payment_date`, `status`, `notes`, `created_by`, `created_at`) VALUES
	(1, 1, 'bank_transfer', 3750000.00, 'deposit', NULL, NULL, '2025-09-21', 'completed', 'Chuyển khoản cọc 30%', 2, '2025-09-21 07:00:00'),
	(2, 2, 'cash', 2484000.00, 'deposit', NULL, NULL, '2025-09-22', 'completed', 'Cọc tiền mặt', 3, '2025-09-22 08:00:00'),
	(3, 3, 'bank_transfer', 6180000.00, 'deposit', NULL, NULL, '2025-09-23', 'completed', NULL, 2, '2025-09-23 03:00:00'),
	(4, 4, 'bank_transfer', 7350000.00, 'deposit', NULL, NULL, '2025-09-24', 'completed', NULL, 3, '2025-09-24 04:00:00'),
	(5, 5, 'cash', 2640000.00, 'deposit', NULL, NULL, '2025-09-26', 'completed', NULL, 2, '2025-09-26 02:00:00'),
	(6, 1, 'bank_transfer', 8750000.00, 'full', NULL, NULL, '2025-09-30', 'completed', 'Thanh toán đủ', 2, '2025-09-30 03:00:00'),
	(7, 2, 'bank_transfer', 5796000.00, 'full', NULL, NULL, '2025-10-01', 'completed', NULL, 3, '2025-10-01 04:00:00'),
	(8, 3, 'credit_card', 14420000.00, 'full', NULL, NULL, '2025-10-02', 'completed', 'Thanh toán thẻ', 2, '2025-10-02 07:00:00'),
	(9, 4, 'bank_transfer', 17150000.00, 'full', NULL, NULL, '2025-10-05', 'completed', NULL, 3, '2025-10-05 02:00:00'),
	(10, 5, 'cash', 6160000.00, 'full', NULL, NULL, '2025-10-08', 'completed', NULL, 2, '2025-10-08 08:00:00'),
	(11, 17, 'bank_transfer', 6150000.00, 'deposit', NULL, NULL, '2025-10-21', 'completed', 'Cọc 30%', 2, '2025-10-21 03:00:00'),
	(12, 17, 'bank_transfer', 4850000.00, 'installment', NULL, NULL, '2025-11-05', 'completed', 'Trả góp lần 1', 2, '2025-11-05 07:00:00'),
	(13, 18, 'cash', 4830000.00, 'deposit', NULL, NULL, '2025-10-23', 'completed', 'Cọc', 3, '2025-10-23 02:00:00'),
	(14, 18, 'bank_transfer', 3170000.00, 'installment', NULL, NULL, '2025-11-08', 'completed', 'Trả thêm', 3, '2025-11-08 04:00:00'),
	(15, 19, 'bank_transfer', 5880000.00, 'deposit', NULL, NULL, '2025-10-26', 'completed', NULL, 2, '2025-10-26 08:00:00');

-- Dumping structure for table duan.payment_logs
CREATE TABLE IF NOT EXISTS `payment_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `payment_id` int NOT NULL,
  `action` enum('created','updated','deleted','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `changed_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  KEY `changed_by` (`changed_by`),
  CONSTRAINT `payment_logs_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_logs_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.payment_logs: ~0 rows (approximately)
INSERT INTO `payment_logs` (`id`, `payment_id`, `action`, `old_values`, `new_values`, `changed_by`, `created_at`) VALUES
	(20, 1, 'created', NULL, '{"amount": 3750000, "status": "completed", "payment_type": "deposit", "payment_method": "bank_transfer"}', 2, '2025-09-21 07:00:00'),
	(21, 2, 'created', NULL, '{"amount": 2484000, "status": "completed", "payment_type": "deposit", "payment_method": "cash"}', 3, '2025-09-22 08:00:00'),
	(22, 6, 'created', NULL, '{"amount": 8750000, "status": "completed", "payment_type": "full", "payment_method": "bank_transfer"}', 2, '2025-09-30 03:00:00'),
	(23, 7, 'created', NULL, '{"amount": 5796000, "status": "completed", "payment_type": "full", "payment_method": "bank_transfer"}', 3, '2025-10-01 04:00:00'),
	(24, 8, 'created', NULL, '{"amount": 14420000, "status": "completed", "payment_type": "full", "payment_method": "credit_card"}', 2, '2025-10-02 07:00:00'),
	(25, 11, 'created', NULL, '{"amount": 6150000, "status": "completed", "payment_type": "deposit", "payment_method": "bank_transfer"}', 2, '2025-10-21 03:00:00'),
	(26, 12, 'created', NULL, '{"amount": 4850000, "status": "completed", "payment_type": "installment", "payment_method": "bank_transfer"}', 2, '2025-11-05 07:00:00'),
	(27, 13, 'created', NULL, '{"amount": 4830000, "status": "completed", "payment_type": "deposit", "payment_method": "cash"}', 3, '2025-10-23 02:00:00'),
	(28, 14, 'created', NULL, '{"amount": 3170000, "status": "completed", "payment_type": "installment", "payment_method": "bank_transfer"}', 3, '2025-11-08 04:00:00'),
	(29, 8, 'updated', '{"status": "pending"}', '{"notes": "Đã xác nhận thanh toán thẻ", "status": "completed"}', 2, '2025-10-02 09:00:00');

-- Dumping structure for table duan.policies
CREATE TABLE IF NOT EXISTS `policies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `policy_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.policies: ~0 rows (approximately)
INSERT INTO `policies` (`id`, `name`, `description`, `policy_type`, `content`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Chính sách thanh toán', 'Quy định về thanh toán khi đặt tour', 'payment', '- Đặt cọc 30% tổng giá trị tour khi booking\r\n- Thanh toán đủ 100% trước 7 ngày khởi hành\r\n- Thanh toán qua: Tiền mặt, Chuyển khoản, Thẻ tín dụng\r\n- Không chấp nhận thanh toán trả góp', 'active', '2025-01-05 02:00:00', '2025-12-08 19:33:56'),
	(2, 'Chính sách hoàn tiền', 'Quy định hoàn tiền khi hủy tour', 'refund', '- Hoàn tiền theo chính sách hủy tour (phí hủy 10-100% tùy thời điểm)\r\n- Thời gian hoàn tiền: 7-10 ngày làm việc\r\n- Hoàn tiền qua tài khoản ngân hàng hoặc tiền mặt\r\n- Không hoàn tiền cho các trường hợp bất khả kháng', 'active', '2025-01-05 02:00:00', '2025-12-08 19:33:56'),
	(3, 'Quy định hành lý', 'Quy định về hành lý mang theo', 'luggage', '- Hành lý xách tay: Tối đa 7kg\r\n- Hành lý ký gửi: Tối đa 23kg\r\n- Không mang theo vật phẩm nguy hiểm, cấm\r\n- Khuyến khích mang theo thuốc cá nhân, đồ dùng vệ sinh', 'active', '2025-01-05 02:00:00', '2025-12-08 19:33:56'),
	(4, 'Quy định trẻ em và giá vé', 'Quy định về độ tuổi và giá vé trẻ em', 'children', '- Trẻ em dưới 2 tuổi: Miễn phí (không ghế riêng)\r\n- Trẻ em từ 2-10 tuổi: 75% giá người lớn\r\n- Trẻ em từ 11 tuổi trở lên: 100% giá người lớn\r\n- Trẻ em phải đi cùng ít nhất 1 người lớn', 'active', '2025-01-05 02:00:00', '2025-12-08 19:33:56'),
	(5, 'Điều kiện tham gia tour', 'Các điều kiện để tham gia tour', 'general', '- Khách hàng phải có đủ sức khỏe để tham gia tour\r\n- Chuẩn bị đầy đủ giấy tờ tùy thân (CMND/CCCD/Passport)\r\n- Tuân thủ lịch trình và quy định của hướng dẫn viên\r\n- Không làm ảnh hưởng đến các khách khác trong đoàn', 'active', '2025-01-05 02:00:00', '2025-12-08 19:33:56');

-- Dumping structure for table duan.provinces
CREATE TABLE IF NOT EXISTS `provinces` (
  `id` int NOT NULL AUTO_INCREMENT,
  `country_id` int NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `country_id` (`country_id`),
  KEY `idx_provinces_status` (`status`),
  CONSTRAINT `provinces_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.provinces: ~0 rows (approximately)
INSERT INTO `provinces` (`id`, `country_id`, `code`, `name`, `status`, `created_at`, `updated_at`) VALUES
	(1, 1, 'HN', 'Hà Nội', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:40'),
	(2, 1, 'HCM', 'TP. Hồ Chí Minh', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:40'),
	(3, 1, 'HP', 'Hải Phòng', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:40'),
	(4, 1, 'DN', 'Đà Nẵng', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:40'),
	(5, 1, 'CT', 'Cần Thơ', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:40'),
	(6, 1, 'HUE', 'Huế', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:40'),
	(7, 1, 'QN', 'Quảng Ninh', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:50'),
	(8, 1, 'KH', 'Khánh Hòa', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:50'),
	(9, 1, 'LD', 'Lâm Đồng', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:50'),
	(10, 1, 'NB', 'Ninh Bình', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:50'),
	(11, 1, 'LC', 'Lào Cai', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:50'),
	(12, 1, 'AG', 'An Giang', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:50'),
	(13, 1, 'CB', 'Cao Bằng', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:50'),
	(14, 1, 'KG', 'Kiên Giang', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:50'),
	(15, 1, 'BVT', 'Bà Rịa - Vũng Tàu', 'active', '2024-12-31 17:00:00', '2025-12-08 19:29:50');

-- Dumping structure for table duan.refunds
CREATE TABLE IF NOT EXISTS `refunds` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `payment_id` int DEFAULT NULL,
  `refund_amount` decimal(15,2) NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','completed','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `processed_by` int DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `payment_id` (`payment_id`),
  KEY `processed_by` (`processed_by`),
  CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `refunds_ibfk_2` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `refunds_ibfk_3` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.refunds: ~0 rows (approximately)

-- Dumping structure for table duan.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.roles: ~3 rows (approximately)
INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'Quản trị viên', 'Quản lý toàn bộ hệ thống', '2025-01-15 01:00:00', '2025-12-08 19:28:03'),
	(2, 'staff', 'Nhân viên', 'Tạo tour, booking, quản lý khách hàng', '2025-01-15 01:00:00', '2025-12-08 19:28:03'),
	(3, 'guide', 'Hướng dẫn viên', 'Điều hành tour, viết nhật ký', '2025-01-15 01:00:00', '2025-12-08 19:28:03');

-- Dumping structure for table duan.room_assignments
CREATE TABLE IF NOT EXISTS `room_assignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_schedule_id` int NOT NULL,
  `itinerary_id` int NOT NULL COMMENT 'Ngày nào (đêm nào)',
  `service_provider_id` int DEFAULT NULL COMMENT 'Khách sạn',
  `room_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Số phòng (VD: "201", "301A")',
  `room_type` enum('single','double','twin','triple','quad','family') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_capacity` int NOT NULL COMMENT 'Số người tối đa',
  `actual_occupancy` int NOT NULL COMMENT 'Số người thực tế',
  `check_in_date` date NOT NULL COMMENT 'Ngày check-in phòng này',
  `check_out_date` date NOT NULL COMMENT 'Ngày check-out',
  `status` enum('pending','assigned','confirmed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_schedule_id` (`tour_schedule_id`),
  KEY `itinerary_id` (`itinerary_id`),
  KEY `service_provider_id` (`service_provider_id`),
  KEY `idx_room_assignments_status` (`status`),
  CONSTRAINT `room_assignments_ibfk_itinerary` FOREIGN KEY (`itinerary_id`) REFERENCES `itineraries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_assignments_ibfk_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_assignments_ibfk_service_provider` FOREIGN KEY (`service_provider_id`) REFERENCES `service_providers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.room_assignments: ~0 rows (approximately)

-- Dumping structure for table duan.room_assignment_customers
CREATE TABLE IF NOT EXISTS `room_assignment_customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room_assignment_id` int NOT NULL,
  `booking_customer_id` int NOT NULL,
  `customer_id` int NOT NULL COMMENT 'Snapshot để dễ query',
  `booking_id` int NOT NULL COMMENT 'Snapshot',
  `role` enum('primary','companion') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'companion' COMMENT 'Ai là người chính',
  `room_preference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Yêu cầu phòng (window, non_smoking, ground_floor)',
  `special_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Dị ứng, yêu cầu đặc biệt',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `room_assignment_id` (`room_assignment_id`),
  KEY `booking_customer_id` (`booking_customer_id`),
  KEY `customer_id` (`customer_id`),
  KEY `booking_id` (`booking_id`),
  CONSTRAINT `room_assignment_customers_ibfk_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_assignment_customers_ibfk_booking_customer` FOREIGN KEY (`booking_customer_id`) REFERENCES `booking_customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_assignment_customers_ibfk_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `room_assignment_customers_ibfk_room` FOREIGN KEY (`room_assignment_id`) REFERENCES `room_assignments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.room_assignment_customers: ~0 rows (approximately)

-- Dumping structure for table duan.room_assignment_history
CREATE TABLE IF NOT EXISTS `room_assignment_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room_assignment_id` int NOT NULL,
  `action` enum('created','updated','customer_added','customer_removed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_values` json DEFAULT NULL COMMENT 'Snapshot giá trị cũ',
  `new_values` json DEFAULT NULL COMMENT 'Snapshot giá trị mới',
  `changed_by` int DEFAULT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `room_assignment_id` (`room_assignment_id`),
  KEY `changed_by` (`changed_by`),
  CONSTRAINT `room_assignment_history_ibfk_changed_by` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `room_assignment_history_ibfk_room` FOREIGN KEY (`room_assignment_id`) REFERENCES `room_assignments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.room_assignment_history: ~0 rows (approximately)

-- Dumping structure for table duan.room_requests
CREATE TABLE IF NOT EXISTS `room_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `customer_id` int NOT NULL COMMENT 'Khách yêu cầu',
  `request_type` enum('single_room','share_with','avoid_sharing_with') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_customer_id` int DEFAULT NULL COMMENT 'Nếu share_with hoặc avoid_sharing_with',
  `target_customer_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Snapshot - phòng khi customer bị xóa',
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Lý do yêu cầu',
  `status` enum('pending','approved','rejected','fulfilled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `single_room_supplement` decimal(15,2) DEFAULT '0.00' COMMENT 'Phụ phí đơn phòng (cố định)',
  `handled_by` int DEFAULT NULL COMMENT 'User xử lý',
  `handled_at` timestamp NULL DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  KEY `customer_id` (`customer_id`),
  KEY `target_customer_id` (`target_customer_id`),
  KEY `handled_by` (`handled_by`),
  KEY `idx_room_requests_status` (`status`),
  CONSTRAINT `room_requests_ibfk_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_requests_ibfk_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `room_requests_ibfk_handled_by` FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `room_requests_ibfk_target_customer` FOREIGN KEY (`target_customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.room_requests: ~0 rows (approximately)

-- Dumping structure for table duan.schedule_guide_history
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.schedule_guide_history: ~0 rows (approximately)

-- Dumping structure for table duan.schedule_vehicle_history
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
  `change_type` enum('vehicle','driver','both') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'both' COMMENT 'Loại thay đổi: chỉ xe, chỉ tài xế, hoặc cả hai',
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Lịch sử thay đổi xe và tài xế cho tour schedule';

-- Dumping data for table duan.schedule_vehicle_history: ~0 rows (approximately)

-- Dumping structure for table duan.services
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_provider_id` int NOT NULL,
  `service_type_id` int DEFAULT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `service_provider_id` (`service_provider_id`),
  KEY `service_type_id` (`service_type_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `services_ibfk_1` FOREIGN KEY (`service_provider_id`) REFERENCES `service_providers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `services_ibfk_2` FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `services_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.services: ~28 rows (approximately)
INSERT INTO `services` (`id`, `service_provider_id`, `service_type_id`, `name`, `description`, `unit`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'Phòng Standard 2 người - Vinpearl Hạ Long', 'Phòng tiêu chuẩn view vịnh', 'đêm', 'active', 1, '2025-01-08 01:00:00', '2025-12-08 19:30:58'),
	(2, 1, 1, 'Phòng Deluxe 2 người - Vinpearl Hạ Long', 'Phòng cao cấp hướng biển', 'đêm', 'active', 1, '2025-01-08 01:10:00', '2025-12-08 19:30:58'),
	(3, 2, 1, 'Phòng Standard - Mường Thanh Đà Nẵng', 'Phòng tiêu chuẩn 2 người', 'đêm', 'active', 1, '2025-01-08 01:20:00', '2025-12-08 19:30:58'),
	(4, 2, 1, 'Phòng Suite - Mường Thanh Đà Nẵng', 'Phòng Suite view biển', 'đêm', 'active', 1, '2025-01-08 01:30:00', '2025-12-08 19:30:58'),
	(5, 3, 1, 'Phòng Deluxe - Sheraton Saigon', 'Phòng cao cấp trung tâm SG', 'đêm', 'active', 1, '2025-01-08 01:40:00', '2025-12-08 19:30:58'),
	(6, 4, 1, 'Phòng Standard - Sapa Charm', 'Phòng view thung lũng', 'đêm', 'active', 1, '2025-01-08 01:50:00', '2025-12-08 19:30:58'),
	(7, 5, 1, 'Phòng Colonial - Azerai Huế', 'Phòng phong cách thuộc địa', 'đêm', 'active', 1, '2025-01-08 02:00:00', '2025-12-08 19:30:58'),
	(8, 6, 1, 'Phòng Deluxe - InterContinental NT', 'Phòng hướng biển Nha Trang', 'đêm', 'active', 1, '2025-01-08 02:10:00', '2025-12-08 19:30:58'),
	(9, 7, 1, 'Villa 2 phòng ngủ - JW Marriott PQ', 'Villa riêng tư Phú Quốc', 'đêm', 'active', 1, '2025-01-08 02:20:00', '2025-12-08 19:30:58'),
	(10, 8, 1, 'Phòng Heritage - Dalat Palace', 'Phòng cổ điển Đà Lạt', 'đêm', 'active', 1, '2025-01-08 02:30:00', '2025-12-08 19:30:58'),
	(11, 9, 2, 'Buffet sáng - Ngọn 138', 'Buffet sáng món Việt', 'suất', 'active', 1, '2025-01-08 03:00:00', '2025-12-08 19:30:58'),
	(12, 9, 2, 'Buffet trưa - Ngọn 138', 'Buffet trưa đa dạng', 'suất', 'active', 1, '2025-01-08 03:10:00', '2025-12-08 19:30:58'),
	(13, 10, 2, 'Set menu Huế - Madame Hương', 'Cơm âm phủ Huế', 'suất', 'active', 1, '2025-01-08 03:20:00', '2025-12-08 19:30:58'),
	(14, 11, 2, 'Hải sản tươi sống - Hạ Long', 'Buffet hải sản', 'suất', 'active', 1, '2025-01-08 03:30:00', '2025-12-08 19:30:58'),
	(15, 12, 2, 'Buffet trưa - Bà Nà Restaurant', 'Buffet tại Bà Nà Hills', 'suất', 'active', 1, '2025-01-08 03:40:00', '2025-12-08 19:30:58'),
	(16, 13, 2, 'Dinner hải sản - Winston PQ', 'Bữa tối cao cấp', 'suất', 'active', 1, '2025-01-08 03:50:00', '2025-12-08 19:30:58'),
	(17, 14, 3, 'Xe 45 chỗ - Phương Trang', 'Xe bus 45 chỗ có WC', 'ngày', 'active', 1, '2025-01-08 04:00:00', '2025-12-08 19:30:58'),
	(18, 14, 3, 'Xe 16 chỗ - Phương Trang', 'Xe Limousine 16 chỗ', 'ngày', 'active', 1, '2025-01-08 04:10:00', '2025-12-08 19:30:58'),
	(19, 15, 3, 'Xe 7 chỗ - Mai Linh', 'Xe 7 chỗ gia đình', 'ngày', 'active', 1, '2025-01-08 04:20:00', '2025-12-08 19:30:58'),
	(20, 16, 3, 'VinBus điện - Đà Nẵng', 'Xe bus điện tham quan', 'lượt', 'active', 1, '2025-01-08 04:30:00', '2025-12-08 19:30:58'),
	(21, 17, 3, 'Vé máy bay khứ hồi HN-BKK', 'Vietnam Airlines Economy', 'vé', 'active', 1, '2025-01-08 04:40:00', '2025-12-08 19:30:58'),
	(22, 17, 3, 'Vé máy bay khứ hồi HN-SG', 'Chuyến nội địa', 'vé', 'active', 1, '2025-01-08 04:50:00', '2025-12-08 19:30:58'),
	(23, 18, 4, 'Vé Sun World Bà Nà Hills', 'Vé người lớn bao gồm cáp treo', 'vé', 'active', 1, '2025-01-08 05:00:00', '2025-12-08 19:30:58'),
	(24, 18, 4, 'Vé trẻ em Bà Nà Hills', 'Vé trẻ em 1m-1m4', 'vé', 'active', 1, '2025-01-08 05:10:00', '2025-12-08 19:30:58'),
	(25, 19, 4, 'Vé VinWonders Phú Quốc', 'Vé người lớn', 'vé', 'active', 1, '2025-01-08 05:20:00', '2025-12-08 19:30:58'),
	(26, 19, 4, 'Vé trẻ em VinWonders PQ', 'Vé trẻ em 1m-1m4', 'vé', 'active', 1, '2025-01-08 05:30:00', '2025-12-08 19:30:58'),
	(27, 20, 4, 'Vé tham quan Đại Nội Huế', 'Vé vào cửa Đại Nội', 'vé', 'active', 1, '2025-01-08 05:40:00', '2025-12-08 19:30:58'),
	(28, 20, 4, 'Vé Lăng Khải Định', 'Vé tham quan lăng', 'vé', 'active', 1, '2025-01-08 05:50:00', '2025-12-08 19:30:58'),
	(29, 20, 6, 'Bảo hiểm du lịch Bảo Việt', 'Bảo hiểm tai nạn và y tế', 'người', 'active', 1, '2025-01-08 06:00:00', '2025-12-08 19:33:27'),
	(30, 20, 6, 'Bảo hiểm quốc tế PVI', 'Bảo hiểm du lịch nước ngoài', 'người', 'active', 1, '2025-01-08 06:10:00', '2025-12-08 19:33:27');

-- Dumping structure for table duan.service_prices
CREATE TABLE IF NOT EXISTS `service_prices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `price_type` enum('standard','peak','low') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'standard',
  `unit_price` decimal(15,2) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `service_prices_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_prices_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.service_prices: ~0 rows (approximately)
INSERT INTO `service_prices` (`id`, `service_id`, `price_type`, `unit_price`, `start_date`, `end_date`, `notes`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
	(1, 1, 'standard', 1200000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 01:00:00', '2025-12-08 19:33:40'),
	(2, 2, 'standard', 1800000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 01:00:00', '2025-12-08 19:33:40'),
	(3, 3, 'standard', 1500000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 01:00:00', '2025-12-08 19:33:40'),
	(4, 4, 'standard', 2500000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 01:00:00', '2025-12-08 19:33:40'),
	(5, 5, 'standard', 3200000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 01:00:00', '2025-12-08 19:33:40'),
	(6, 6, 'standard', 900000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 01:00:00', '2025-12-08 19:33:40'),
	(7, 7, 'standard', 2800000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 01:00:00', '2025-12-08 19:33:40'),
	(8, 8, 'standard', 2200000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 01:00:00', '2025-12-08 19:33:40'),
	(9, 9, 'standard', 4500000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 01:00:00', '2025-12-08 19:33:40'),
	(10, 10, 'standard', 1600000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 01:00:00', '2025-12-08 19:33:40'),
	(11, 11, 'standard', 120000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 02:00:00', '2025-12-08 19:33:40'),
	(12, 12, 'standard', 250000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 02:00:00', '2025-12-08 19:33:40'),
	(13, 13, 'standard', 180000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 02:00:00', '2025-12-08 19:33:40'),
	(14, 14, 'standard', 350000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 02:00:00', '2025-12-08 19:33:40'),
	(15, 15, 'standard', 200000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 02:00:00', '2025-12-08 19:33:40'),
	(16, 16, 'standard', 500000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 02:00:00', '2025-12-08 19:33:40'),
	(17, 17, 'standard', 3500000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 03:00:00', '2025-12-08 19:33:40'),
	(18, 18, 'standard', 2200000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 03:00:00', '2025-12-08 19:33:40'),
	(19, 19, 'standard', 1500000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 03:00:00', '2025-12-08 19:33:40'),
	(20, 20, 'standard', 50000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 03:00:00', '2025-12-08 19:33:40'),
	(21, 21, 'standard', 4500000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 03:00:00', '2025-12-08 19:33:40'),
	(22, 22, 'standard', 1800000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 03:00:00', '2025-12-08 19:33:40'),
	(23, 23, 'standard', 750000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 04:00:00', '2025-12-08 19:33:40'),
	(24, 24, 'standard', 600000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 04:00:00', '2025-12-08 19:33:40'),
	(25, 25, 'standard', 850000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 04:00:00', '2025-12-08 19:33:40'),
	(26, 26, 'standard', 680000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 04:00:00', '2025-12-08 19:33:40'),
	(27, 27, 'standard', 200000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 04:00:00', '2025-12-08 19:33:40'),
	(28, 28, 'standard', 150000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 04:00:00', '2025-12-08 19:33:40'),
	(29, 29, 'standard', 50000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 05:00:00', '2025-12-08 19:33:40'),
	(30, 30, 'standard', 150000.00, '2025-01-01', '2025-12-31', NULL, 'active', 1, '2025-01-09 05:00:00', '2025-12-08 19:33:40');

-- Dumping structure for table duan.service_providers
CREATE TABLE IF NOT EXISTS `service_providers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `province_id` int NOT NULL,
  `country_id` int NOT NULL,
  `service_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_code` (`service_code`),
  KEY `province_id` (`province_id`),
  KEY `country_id` (`country_id`),
  KEY `created_by` (`created_by`),
  KEY `idx_service_providers_status` (`status`),
  CONSTRAINT `service_providers_ibfk_1` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_providers_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_providers_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.service_providers: ~0 rows (approximately)
INSERT INTO `service_providers` (`id`, `province_id`, `country_id`, `service_code`, `name`, `description`, `address`, `phone`, `email`, `website`, `contact_person`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
	(1, 7, 1, 'SP-HTL-001', 'Vinpearl Hotel Hạ Long', 'Khách sạn 4 sao view vịnh', 'Bãi Cháy, Hạ Long, Quảng Ninh', '02033842222', 'halong@vinpearl.com', NULL, 'Nguyễn Thu Hà', 'active', 1, '2025-01-06 02:00:00', '2025-12-08 19:30:43'),
	(2, 4, 1, 'SP-HTL-002', 'Mường Thanh Grand Đà Nẵng', 'Khách sạn 5 sao bên bờ biển', '270 Võ Nguyên Giáp, Đà Nẵng', '02363959595', 'danang@muongthanh.vn', NULL, 'Trần Văn Phúc', 'active', 1, '2025-01-06 02:30:00', '2025-12-08 19:30:43'),
	(3, 2, 1, 'SP-HTL-003', 'Sheraton Saigon Hotel', 'Khách sạn 5 sao trung tâm Sài Gòn', '88 Đồng Khởi, Q1, TP.HCM', '02838279000', 'reservation@sheratonsaigon.com', NULL, 'Lê Minh Tuấn', 'active', 1, '2025-01-06 03:00:00', '2025-12-08 19:30:43'),
	(4, 11, 1, 'SP-HTL-004', 'Sapa Charm Resort', 'Resort 4 sao view thung lũng', 'Hoàng Liên, Sapa, Lào Cai', '02143871888', 'info@sapacharm.com', NULL, 'Hoàng Văn Sơn', 'active', 1, '2025-01-06 03:30:00', '2025-12-08 19:30:43'),
	(5, 6, 1, 'SP-HTL-005', 'Azerai La Residence Huế', 'Khách sạn 5 sao view sông Hương', '5 Lê Lợi, Huế', '02343837475', 'hue@azerai.com', NULL, 'Phạm Thị Lan', 'active', 1, '2025-01-06 04:00:00', '2025-12-08 19:30:43'),
	(6, 8, 1, 'SP-HTL-006', 'InterContinental Nha Trang', 'Resort 5 sao bãi Trần Phú', 'Trần Phú, Nha Trang', '02583888777', 'nhatrang@ihg.com', NULL, 'Nguyễn Quang Hải', 'active', 1, '2025-01-06 04:30:00', '2025-12-08 19:30:43'),
	(7, 14, 1, 'SP-HTL-007', 'JW Marriott Phú Quốc', 'Resort 5 sao sang trọng', 'Bãi Ông Lang, Phú Quốc', '02973778888', 'phuquoc@marriott.com', NULL, 'Trương Minh Tâm', 'active', 1, '2025-01-06 05:00:00', '2025-12-08 19:30:43'),
	(8, 9, 1, 'SP-HTL-008', 'Dalat Palace Heritage Hotel', 'Khách sạn 5 sao cổ điển', '2 Trần Phú, Đà Lạt', '02633825444', 'reservation@dalatpalace.vn', NULL, 'Lê Thu Hương', 'active', 1, '2025-01-06 05:30:00', '2025-12-08 19:30:43'),
	(9, 2, 1, 'SP-RES-001', 'Nhà hàng Ngọn 138', 'Nhà hàng món Việt truyền thống', '138 Nam Kỳ Khởi Nghĩa, Q1, TP.HCM', '02838279131', 'ngon138@gmail.com', NULL, 'Phan Văn Tùng', 'active', 1, '2025-01-07 01:00:00', '2025-12-08 19:30:43'),
	(10, 6, 1, 'SP-RES-002', 'Madame Hương Restaurant', 'Nhà hàng món Huế đặc sản', '6 Lê Lợi, Huế', '02343524674', 'madamehuong@gmail.com', NULL, 'Huỳnh Thị Mai', 'active', 1, '2025-01-07 01:30:00', '2025-12-08 19:30:43'),
	(11, 7, 1, 'SP-RES-003', 'Hải sản Hạ Long', 'Nhà hàng hải sản tươi sống', 'Bãi Cháy, Hạ Long', '02033845678', 'haisanhalong@yahoo.com', NULL, 'Nguyễn Thị Vân', 'active', 1, '2025-01-07 02:00:00', '2025-12-08 19:30:43'),
	(12, 4, 1, 'SP-RES-004', 'Bà Nà Restaurant', 'Nhà hàng buffet tại Bà Nà Hills', 'Bà Nà Hills, Đà Nẵng', '02363913913', 'restaurant@banahills.com.vn', NULL, 'Trần Hữu Nghĩa', 'active', 1, '2025-01-07 02:30:00', '2025-12-08 19:30:43'),
	(13, 14, 1, 'SP-RES-005', 'Winston Restaurant Phú Quốc', 'Nhà hàng hải sản cao cấp', 'Bãi Trường, Phú Quốc', '02973846888', 'winston@phuquoc.com', NULL, 'Đặng Minh Hoàng', 'active', 1, '2025-01-07 03:00:00', '2025-12-08 19:30:43'),
	(14, 2, 1, 'SP-VEH-001', 'Phương Trang FUTA Bus', 'Công ty vận tải uy tín', '272 Đề Thám, Q1, TP.HCM', '02838386852', 'futabus@futa.vn', NULL, 'Lâm Văn Thuận', 'active', 1, '2025-01-07 04:00:00', '2025-12-08 19:30:43'),
	(15, 1, 1, 'SP-VEH-002', 'Mai Linh Express', 'Dịch vụ xe limousine cao cấp', '123 Lê Duẩn, Hà Nội', '02438333666', 'express@mailinh.vn', NULL, 'Vũ Đức Anh', 'active', 1, '2025-01-07 04:30:00', '2025-12-08 19:30:43'),
	(16, 4, 1, 'SP-VEH-003', 'VinBus Đà Nẵng', 'Xe bus điện hiện đại', 'Nguyễn Văn Linh, Đà Nẵng', '1900232389', 'vinbus@vingroup.net', NULL, 'Phan Thị Thu', 'active', 1, '2025-01-07 05:00:00', '2025-12-08 19:30:43'),
	(17, 1, 1, 'SP-VEH-004', 'Vietnam Airlines', 'Hãng hàng không quốc gia', 'Nội Bài, Hà Nội', '1900545454', 'booking@vietnamairlines.com', NULL, 'Nguyễn Hồng Phúc', 'active', 1, '2025-01-07 05:30:00', '2025-12-08 19:30:43'),
	(18, 4, 1, 'SP-TIC-001', 'Sun World Bà Nà Hills', 'Khu vui chơi giải trí', 'Bà Nà Hills, Đà Nẵng', '02363913913', 'banaguideline@sunworld.vn', NULL, 'Hoàng Minh Đức', 'active', 1, '2025-01-07 06:00:00', '2025-12-08 19:30:43'),
	(19, 14, 1, 'SP-TIC-002', 'VinWonders Phú Quốc', 'Công viên chủ đề lớn nhất VN', 'Bãi Dài, Phú Quốc', '19006677', 'phuquoc@vinwonders.com', NULL, 'Trần Thị Ngọc', 'active', 1, '2025-01-07 06:30:00', '2025-12-08 19:30:43'),
	(20, 6, 1, 'SP-TIC-003', 'Ban quản lý Di tích Huế', 'Quản lý các di tích Huế', 'Đại Nội Huế', '02343523237', 'ditichue@hue.gov.vn', NULL, 'Lê Văn Thành', 'active', 1, '2025-01-07 07:00:00', '2025-12-08 19:30:43');

-- Dumping structure for table duan.service_provider_payments
CREATE TABLE IF NOT EXISTS `service_provider_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `payment_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_provider_id` int NOT NULL,
  `booking_id` int DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` enum('cash','bank_transfer','check') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'bank_transfer',
  `payment_date` date NOT NULL,
  `invoice_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_code` (`payment_code`),
  KEY `created_by` (`created_by`),
  KEY `idx_service_provider_payments_service_provider_id` (`service_provider_id`),
  KEY `idx_service_provider_payments_status` (`status`),
  CONSTRAINT `service_provider_payments_ibfk_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `service_provider_payments_ibfk_service_provider` FOREIGN KEY (`service_provider_id`) REFERENCES `service_providers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.service_provider_payments: ~0 rows (approximately)

-- Dumping structure for table duan.service_provider_payment_details
CREATE TABLE IF NOT EXISTS `service_provider_payment_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `payment_id` int NOT NULL,
  `booking_service_id` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  KEY `booking_service_id` (`booking_service_id`),
  CONSTRAINT `service_provider_payment_details_ibfk_booking_service` FOREIGN KEY (`booking_service_id`) REFERENCES `booking_services` (`id`),
  CONSTRAINT `service_provider_payment_details_ibfk_payment` FOREIGN KEY (`payment_id`) REFERENCES `service_provider_payments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.service_provider_payment_details: ~0 rows (approximately)

-- Dumping structure for table duan.service_types
CREATE TABLE IF NOT EXISTS `service_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `service_types_ibfk_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.service_types: ~0 rows (approximately)
INSERT INTO `service_types` (`id`, `name`, `description`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
	(1, 'Khách sạn', 'Dịch vụ lưu trú, khách sạn, resort', 'active', 1, '2025-01-05 01:00:00', '2025-12-08 19:30:32'),
	(2, 'Nhà hàng', 'Dịch vụ ăn uống, buffet, set menu', 'active', 1, '2025-01-05 01:00:00', '2025-12-08 19:30:32'),
	(3, 'Phương tiện', 'Dịch vụ vận chuyển, xe bus, máy bay', 'active', 1, '2025-01-05 01:00:00', '2025-12-08 19:30:32'),
	(4, 'Vé tham quan', 'Vé các điểm tham quan, công viên', 'active', 1, '2025-01-05 01:00:00', '2025-12-08 19:30:32'),
	(5, 'Hướng dẫn viên', 'Dịch vụ hướng dẫn viên du lịch', 'active', 1, '2025-01-05 01:00:00', '2025-12-08 19:30:32'),
	(6, 'Bảo hiểm', 'Bảo hiểm du lịch', 'active', 1, '2025-01-05 01:00:00', '2025-12-08 19:30:32');

-- Dumping structure for table duan.tours
CREATE TABLE IF NOT EXISTS `tours` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `introduction` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `duration_days` int NOT NULL,
  `duration_nights` int NOT NULL,
  `departure_location` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_participants` int DEFAULT '15',
  `max_participants` int DEFAULT '45',
  `adult_price` decimal(15,2) NOT NULL,
  `child_price` decimal(15,2) NOT NULL,
  `infant_price` decimal(15,2) DEFAULT '0.00',
  `estimated_cost_per_person` decimal(15,2) DEFAULT NULL,
  `markup_percentage` decimal(5,2) DEFAULT '0.00' COMMENT 'DEPRECATED - Không dùng nữa, giữ lại để backward compatible',
  `deposit_percentage` decimal(5,2) DEFAULT '30.00',
  `fixed_cost_guide` decimal(15,2) DEFAULT '0.00' COMMENT 'Chi phí lương HDV (cố định, không theo người)',
  `fixed_cost_management` decimal(15,2) DEFAULT '0.00' COMMENT 'Chi phí quản lý (cố định)',
  `fixed_cost_marketing` decimal(15,2) DEFAULT '0.00' COMMENT 'Chi phí marketing (cố định)',
  `fixed_cost_other` decimal(15,2) DEFAULT '0.00' COMMENT 'Chi phí khác (cố định)',
  `booking_deadline_days` int DEFAULT '1' COMMENT 'Số ngày tối thiểu trước ngày khởi hành để đặt booking (default: 1 ngày)',
  `tour_type` enum('public','custom') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `approved_by` int DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','pending','active','rejected','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `parent_tour_id` int DEFAULT NULL,
  `fixed_cost_total` decimal(15,2) DEFAULT '0.00' COMMENT 'Tổng chi phí cố định (nhập trực tiếp)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tour_code` (`tour_code`),
  KEY `approved_by` (`approved_by`),
  KEY `idx_tours_status` (`status`),
  KEY `idx_tours_created_by` (`created_by`),
  KEY `parent_tour_id` (`parent_tour_id`),
  CONSTRAINT `tours_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tours_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tours_ibfk_4` FOREIGN KEY (`parent_tour_id`) REFERENCES `tours` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.tours: ~0 rows (approximately)
INSERT INTO `tours` (`id`, `tour_code`, `name`, `thumbnail`, `introduction`, `description`, `duration_days`, `duration_nights`, `departure_location`, `min_participants`, `max_participants`, `adult_price`, `child_price`, `infant_price`, `estimated_cost_per_person`, `markup_percentage`, `deposit_percentage`, `fixed_cost_guide`, `fixed_cost_management`, `fixed_cost_marketing`, `fixed_cost_other`, `booking_deadline_days`, `tour_type`, `approved_by`, `approved_at`, `rejection_reason`, `status`, `created_by`, `created_at`, `updated_at`, `parent_tour_id`, `fixed_cost_total`) VALUES
	(1, 'TOUR-HN-HL001', 'Hà Nội - Hạ Long - Ninh Bình 3N2Đ', '/images/tours/halong.jpg', 'Khám phá di sản thiên nhiên thế giới Vịnh Hạ Long và Tràng An Ninh Bình', 'Tour 3 ngày 2 đêm khám phá vẻ đẹp kỳ vĩ của Vịnh Hạ Long - Di sản thiên nhiên thế giới được UNESCO công nhận, kết hợp với Tràng An Ninh Bình - Vịnh Hạ Long trên cạn. Trải nghiệm du ngoạn trên du thuyền 5 sao, thăm hang động tuyệt đẹp, chèo thuyền kayak. Thưởng thức hải sản tươi sống và khám phá văn hóa miền Bắc Việt Nam.', 3, 2, 'Hà Nội', 20, 40, 4500000.00, 3500000.00, 500000.00, NULL, 0.00, 30.00, 0.00, 0.00, 0.00, 0.00, 1, 'public', NULL, NULL, NULL, 'active', 2, '2025-08-15 03:00:00', '2025-12-08 19:34:14', NULL, 0.00),
	(2, 'TOUR-DN-HO001', 'Đà Nẵng - Hội An - Bà Nà 4N3Đ', '/images/tours/danang.jpg', 'Khám phá thành phố đáng sống và phố cổ Hội An', 'Tour 4 ngày 3 đêm trải nghiệm Đà Nẵng - thành phố đáng sống nhất Việt Nam với bãi biển Mỹ Khê tuyệt đẹp, Bà Nà Hills với Cầu Vàng nổi tiếng thế giới, và phố cổ Hội An - Di sản văn hóa UNESCO. Thưởng thức ẩm thực đa dạng, shopping đồ lưu niệm độc đáo.', 4, 3, 'Đà Nẵng', 15, 35, 5800000.00, 4500000.00, 600000.00, NULL, 0.00, 30.00, 0.00, 0.00, 0.00, 0.00, 1, 'public', NULL, NULL, NULL, 'active', 2, '2025-08-20 04:00:00', '2025-12-08 19:34:14', NULL, 0.00),
	(3, 'TOUR-SG-MT001', 'Sài Gòn - Mỹ Tho - Cần Thơ 3N2Đ', '/images/tours/mekong.jpg', 'Khám phá đồng bằng sông Cửu Long', 'Tour 3 ngày 2 đêm khám phá miền Tây sông nước với chợ nổi Cái Răng sôi động, vườn trái cây nhiệt đới, làm kẹo dừa, nghe đờn ca tài tử. Trải nghiệm cuộc sống bình dị của người dân miền Tây Nam Bộ.', 3, 2, 'TP. Hồ Chí Minh', 18, 36, 3200000.00, 2400000.00, 400000.00, NULL, 0.00, 30.00, 0.00, 0.00, 0.00, 0.00, 1, 'public', NULL, NULL, NULL, 'active', 3, '2025-08-25 02:00:00', '2025-12-08 19:34:14', NULL, 0.00),
	(4, 'TOUR-NT-002', 'Nha Trang Biển Xanh 4N3Đ', '/images/tours/nhatrang.jpg', 'Thiên đường biển đảo Nha Trang', 'Tour 4 ngày 3 đêm nghỉ dưỡng tại thành phố biển Nha Trang nổi tiếng. Tham quan Vinpearl Land, lặn ngắm san hô tại Hòn Mun, tắm bùn khoáng, thưởng thức hải sản tươi ngon. Thư giãn trên bãi biển cát trắng nước trong xanh.', 4, 3, 'Nha Trang', 15, 40, 4900000.00, 3700000.00, 500000.00, NULL, 0.00, 30.00, 0.00, 0.00, 0.00, 0.00, 1, 'public', NULL, NULL, NULL, 'active', 2, '2025-09-01 03:00:00', '2025-12-08 19:34:14', NULL, 0.00),
	(5, 'TOUR-PQ-003', 'Phú Quốc  Nghỉ Dưỡng 5N4Đ', '/images/tours/phuquoc.jpg', 'Đảo ngọc Phú Quốc - Thiên đường nhiệt đới', 'Tour 5 ngày 4 đêm nghỉ dưỡng tại đảo ngọc Phú Quốc. Khám phá VinWonders, Safari, Sunset Sanato Beach Club. Lặn biển ngắm san hô, câu cá, tham quan làng chài, chợ đêm. Thưởng thức hải sản tươi sống và rượu sim đặc sản.', 5, 4, 'Phú Quốc', 12, 30, 7500000.00, 5600000.00, 800000.00, NULL, 0.00, 30.00, 0.00, 0.00, 0.00, 0.00, 1, 'public', NULL, NULL, NULL, 'active', 3, '2025-09-05 07:00:00', '2025-12-08 19:34:14', NULL, 0.00),
	(6, 'TOUR-TH-BKK001', 'Bangkok - Pattaya 5N4Đ', '/images/tours/bangkok.jpg', 'Thái Lan huyền bí và hiện đại', 'Tour 5 ngày 4 đêm khám phá Bangkok - thủ đô Thái Lan với chùa Vàng, cung điện hoàng gia, chợ nổi Damnoen Saduak. Tham quan Pattaya với bãi biển đẹp, show Alcazar nổi tiếng, Safari World. Mua sắm thoải mái tại các trung tâm thương mại hiện đại.', 5, 4, 'Hà Nội/TP.HCM', 20, 40, 12500000.00, 9500000.00, 2000000.00, NULL, 0.00, 30.00, 0.00, 0.00, 0.00, 0.00, 1, 'public', NULL, NULL, NULL, 'active', 2, '2025-09-10 04:00:00', '2025-12-08 19:34:14', NULL, 0.00);

-- Dumping structure for table duan.tour_allowance_rules
CREATE TABLE IF NOT EXISTS `tour_allowance_rules` (
  `id` int DEFAULT NULL,
  `rule_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tour_type` enum('public','custom') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration_days_min` int DEFAULT NULL,
  `duration_days_max` int DEFAULT NULL,
  `participant_min` int DEFAULT NULL,
  `participant_max` int DEFAULT NULL,
  `guide_allowance` decimal(15,2) DEFAULT NULL,
  `driver_allowance` decimal(15,2) DEFAULT NULL,
  `priority` int DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.tour_allowance_rules: ~0 rows (approximately)

-- Dumping structure for table duan.tour_assignments
CREATE TABLE IF NOT EXISTS `tour_assignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_schedule_id` int DEFAULT NULL,
  `booking_id` int DEFAULT NULL,
  `guide_id` int NOT NULL,
  `previous_guide_id` int DEFAULT NULL COMMENT 'HDV trước đó (khi thay đổi)',
  `assignment_date` date NOT NULL,
  `salary_amount` decimal(15,2) DEFAULT NULL,
  `salary_status` enum('pending','paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `paid_date` date DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `change_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Lý do thay đổi HDV (khi thay đổi từ HDV cũ sang HDV mới)',
  `status` enum('assigned','in_progress','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'assigned',
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
  CONSTRAINT `tour_assignments_ibfk_previous_guide` FOREIGN KEY (`previous_guide_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.tour_assignments: ~0 rows (approximately)
INSERT INTO `tour_assignments` (`id`, `tour_schedule_id`, `booking_id`, `guide_id`, `previous_guide_id`, `assignment_date`, `salary_amount`, `salary_status`, `paid_date`, `notes`, `change_reason`, `status`, `created_by`, `created_at`) VALUES
	(1, 1, 1, 5, NULL, '2025-09-25', 2000000.00, 'paid', '2025-10-10', NULL, NULL, 'completed', 2, '2025-09-25 03:00:00'),
	(2, 2, 3, 6, NULL, '2025-09-28', 2500000.00, 'paid', '2025-10-15', NULL, NULL, 'completed', 3, '2025-09-28 04:00:00'),
	(3, 3, 4, 7, NULL, '2025-10-02', 2000000.00, 'paid', '2025-10-18', NULL, NULL, 'completed', 2, '2025-10-02 02:00:00'),
	(4, 4, 5, 8, NULL, '2025-10-05', 1800000.00, 'paid', '2025-10-20', NULL, NULL, 'completed', 3, '2025-10-05 03:30:00'),
	(5, 6, 7, 9, NULL, '2025-10-10', 2500000.00, 'paid', '2025-10-28', NULL, NULL, 'completed', 2, '2025-10-10 04:00:00'),
	(6, 7, 8, 10, NULL, '2025-10-12', 3000000.00, 'paid', '2025-10-30', NULL, NULL, 'completed', 3, '2025-10-12 07:00:00'),
	(7, 8, 9, 5, NULL, '2025-10-16', 2000000.00, 'paid', '2025-11-02', NULL, NULL, 'completed', 2, '2025-10-16 02:30:00'),
	(8, 9, 10, 6, NULL, '2025-10-18', 3500000.00, 'paid', '2025-11-05', NULL, NULL, 'completed', 3, '2025-10-18 08:00:00'),
	(9, 10, 12, 7, NULL, '2025-10-20', 2200000.00, 'paid', '2025-11-08', NULL, NULL, 'completed', 2, '2025-10-20 03:00:00'),
	(10, 11, 13, 8, NULL, '2025-10-25', 2000000.00, 'paid', '2025-11-10', NULL, NULL, 'completed', 3, '2025-10-25 04:00:00'),
	(11, 12, 14, 9, NULL, '2025-10-28', 2500000.00, 'paid', '2025-11-12', NULL, NULL, 'completed', 2, '2025-10-28 02:00:00'),
	(12, 13, 15, 10, NULL, '2025-10-31', 1800000.00, 'paid', '2025-11-15', NULL, NULL, 'completed', 3, '2025-10-31 03:30:00'),
	(13, 14, 16, 5, NULL, '2025-11-02', 3000000.00, 'paid', '2025-11-18', NULL, NULL, 'completed', 2, '2025-11-02 07:00:00'),
	(14, 15, 17, 6, NULL, '2025-11-08', 2000000.00, 'pending', NULL, NULL, NULL, 'in_progress', 3, '2025-11-08 02:00:00'),
	(15, 16, 18, 7, NULL, '2025-11-10', 2500000.00, 'pending', NULL, NULL, NULL, 'in_progress', 2, '2025-11-10 03:00:00'),
	(16, 17, 19, 8, NULL, '2025-11-12', 2200000.00, 'pending', NULL, NULL, NULL, 'in_progress', 3, '2025-11-12 04:00:00');

-- Dumping structure for table duan.tour_faqs
CREATE TABLE IF NOT EXISTS `tour_faqs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL,
  `question` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_faqs_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.tour_faqs: ~0 rows (approximately)

-- Dumping structure for table duan.tour_highlights
CREATE TABLE IF NOT EXISTS `tour_highlights` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL,
  `highlight` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_highlights_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=253 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.tour_highlights: ~0 rows (approximately)
INSERT INTO `tour_highlights` (`id`, `tour_id`, `highlight`, `display_order`) VALUES
	(221, 1, 'Du ngoạn Vịnh Hạ Long trên du thuyền 5 sao', 1),
	(222, 1, 'Thăm hang Sửng Sốt - hang động đẹp nhất Hạ Long', 2),
	(223, 1, 'Chèo kayak khám phá hang động và làng chài', 3),
	(224, 1, 'Tham quan Tràng An - Vịnh Hạ Long trên cạn', 4),
	(225, 1, 'Leo 500 bậc thang Hang Múa ngắm toàn cảnh Tràng An', 5),
	(226, 1, 'Thưởng thức hải sản tươi sống Hạ Long', 6),
	(227, 2, 'Chinh phục Bà Nà Hills và Cầu Vàng nổi tiếng thế giới', 1),
	(228, 2, 'Dạo bước trên bãi biển Mỹ Khê - Top bãi biển đẹp nhất hành tinh', 2),
	(229, 2, 'Khám phá phố cổ Hội An về đêm với hàng nghìn đèn lồng', 3),
	(230, 2, 'Trải nghiệm chèo thuyền thúng ở rừng dừa Bảy Mẫu', 4),
	(231, 2, 'Thả đèn hoa đăng trên sông Hoài cầu may mắn', 5),
	(232, 2, 'Thưởng thức ẩm thực đặc sản miền Trung', 6),
	(233, 3, 'Tham quan chợ nổi Cái Răng sôi động lúc bình minh', 1),
	(234, 3, 'Thăm vườn trái cây miệt vườn - Nếm trái cây tươi', 2),
	(235, 3, 'Trải nghiệm làm kẹo dừa thủ công', 3),
	(236, 3, 'Nghe đờn ca tài tử - di sản văn hóa phi vật thể UNESCO', 4),
	(237, 3, 'Đi thuyền khám phá rạch nhỏ miệt vườn', 5),
	(238, 4, 'Vui chơi tại VinWonders Nha Trang', 1),
	(239, 4, 'Lặn biển ngắm san hô tại Hòn Mun', 2),
	(240, 4, 'Tắm bùn khoáng I-resort thư giãn', 3),
	(241, 4, 'Tham quan Tháp Bà Ponagar - di tích Chăm cổ', 4),
	(242, 4, 'Thưởng thức hải sản tươi sống bên bờ biển', 5),
	(243, 5, 'Khám phá VinWonders Phú Quốc - công viên lớn nhất VN', 1),
	(244, 5, 'Trải nghiệm Safari Phú Quốc với hơn 3000 động vật', 2),
	(245, 5, 'Tắm biển Bãi Sao - bãi biển đẹp nhất Phú Quốc', 3),
	(246, 5, 'Check-in Sunset Sanato Beach Club sang chảnh', 4),
	(247, 5, 'Tham quan làng chài Hàm Ninh ăn hải sản tươi', 5),
	(248, 6, 'Tham quan Đại Hoàng Cung và Chùa Phật Vàng lung linh', 1),
	(249, 6, 'Khám phá chợ nổi Damnoen Saduak độc đáo', 2),
	(250, 6, 'Xem show Alcazar Pattaya nổi tiếng', 3),
	(251, 6, 'Vui chơi tại Safari World Bangkok', 4),
	(252, 6, 'Mua sắm tại Platinum, MBK Center', 5);

-- Dumping structure for table duan.tour_images
CREATE TABLE IF NOT EXISTS `tour_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT '0',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_images_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.tour_images: ~0 rows (approximately)

-- Dumping structure for table duan.tour_included_excluded
CREATE TABLE IF NOT EXISTS `tour_included_excluded` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL,
  `type` enum('included','excluded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  CONSTRAINT `tour_included_excluded_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.tour_included_excluded: ~0 rows (approximately)
INSERT INTO `tour_included_excluded` (`id`, `tour_id`, `type`, `item`, `display_order`) VALUES
	(203, 1, 'included', 'Xe ôtô đời mới có máy lạnh đưa đón suốt tuyến', 1),
	(204, 1, 'included', 'Khách sạn tiêu chuẩn 3-4 sao (2 người/phòng)', 2),
	(205, 1, 'included', 'Du thuyền 5 sao tham quan Vịnh Hạ Long', 3),
	(206, 1, 'included', 'Các bữa ăn theo chương trình', 4),
	(207, 1, 'included', 'Vé tham quan các điểm trong chương trình', 5),
	(208, 1, 'included', 'Hướng dẫn viên tiếng Việt nhiệt tình', 6),
	(209, 1, 'included', 'Bảo hiểm du lịch mức 100.000.000đ/vụ', 7),
	(210, 1, 'excluded', 'Vé máy bay khứ hồi (nếu có)', 1),
	(211, 1, 'excluded', 'Chi phí cá nhân ngoài chương trình', 2),
	(212, 1, 'excluded', 'Thuế VAT 10%', 3),
	(213, 2, 'included', 'Vé máy bay khứ hồi (nếu xuất phát từ Hà Nội/SG)', 1),
	(214, 2, 'included', 'Khách sạn 4 sao gần biển (2 người/phòng)', 2),
	(215, 2, 'included', 'Vé Sun World Bà Nà Hills (bao gồm cáp treo)', 3),
	(216, 2, 'included', 'Các bữa ăn buffet và set menu', 4),
	(217, 2, 'included', 'Xe đưa đón sân bay và tham quan', 5),
	(218, 2, 'included', 'HDV tiếng Việt nhiệt tình', 6),
	(219, 2, 'included', 'Bảo hiểm du lịch', 7),
	(220, 2, 'excluded', 'May mặc Hội An (nếu có)', 1),
	(221, 2, 'excluded', 'Đồ uống và chi phí cá nhân', 2);

-- Dumping structure for table duan.tour_journals
CREATE TABLE IF NOT EXISTS `tour_journals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_schedule_id` int NOT NULL,
  `author_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `images` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','published') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_schedule_id` (`tour_schedule_id`),
  KEY `author_id` (`author_id`),
  CONSTRAINT `tour_journals_ibfk_1` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tour_journals_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.tour_journals: ~0 rows (approximately)

-- Dumping structure for table duan.tour_policies
CREATE TABLE IF NOT EXISTS `tour_policies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL,
  `policy_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tour_id` (`tour_id`),
  KEY `policy_id` (`policy_id`),
  CONSTRAINT `tour_policies_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tour_policies_ibfk_2` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.tour_policies: ~0 rows (approximately)
INSERT INTO `tour_policies` (`id`, `tour_id`, `policy_id`) VALUES
	(59, 1, 1),
	(60, 1, 2),
	(61, 1, 3),
	(62, 1, 4),
	(63, 1, 5),
	(64, 2, 1),
	(65, 2, 2),
	(66, 2, 3),
	(67, 2, 4),
	(68, 2, 5),
	(69, 3, 1),
	(70, 3, 2),
	(71, 3, 3),
	(72, 3, 4),
	(73, 3, 5),
	(74, 4, 1),
	(75, 4, 2),
	(76, 4, 3),
	(77, 4, 4),
	(78, 4, 5),
	(79, 5, 1),
	(80, 5, 2),
	(81, 5, 3),
	(82, 5, 4),
	(83, 5, 5),
	(84, 6, 1),
	(85, 6, 2),
	(86, 6, 3),
	(87, 6, 4),
	(88, 6, 5);

-- Dumping structure for table duan.tour_schedules
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
  `status` enum('open','closed','pending','in_progress','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `guide_id` int DEFAULT NULL,
  `guide_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tour_schedule_unique` (`tour_id`,`start_date`,`end_date`),
  KEY `fk_schedule_guide` (`guide_id`),
  KEY `idx_schedule_status` (`status`),
  CONSTRAINT `fk_schedule_guide` FOREIGN KEY (`guide_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tour_schedules_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.tour_schedules: ~0 rows (approximately)
INSERT INTO `tour_schedules` (`id`, `tour_id`, `start_date`, `end_date`, `quota`, `booked`, `adult_price`, `child_price`, `infant_price`, `status`, `guide_id`, `guide_notes`, `created_at`, `updated_at`) VALUES
	(1, 1, '2025-10-03', '2025-10-05', 35, 28, 4500000.00, 3500000.00, 500000.00, 'completed', 5, NULL, '2025-09-10 02:00:00', '2025-12-08 19:34:29'),
	(2, 2, '2025-10-05', '2025-10-08', 30, 22, 5800000.00, 4500000.00, 600000.00, 'completed', 6, NULL, '2025-09-12 03:00:00', '2025-12-08 19:34:29'),
	(3, 1, '2025-10-10', '2025-10-12', 35, 30, 4500000.00, 3500000.00, 500000.00, 'completed', 7, NULL, '2025-09-15 04:00:00', '2025-12-08 19:34:29'),
	(4, 3, '2025-10-12', '2025-10-14', 32, 25, 3200000.00, 2400000.00, 400000.00, 'completed', 8, NULL, '2025-09-18 02:30:00', '2025-12-08 19:34:29'),
	(5, 4, '2025-10-15', '2025-10-18', 36, 0, 4900000.00, 3700000.00, 500000.00, 'cancelled', NULL, NULL, '2025-09-20 07:00:00', '2025-12-08 19:34:29'),
	(6, 2, '2025-10-18', '2025-10-21', 30, 26, 5800000.00, 4500000.00, 600000.00, 'completed', 9, NULL, '2025-09-22 03:30:00', '2025-12-08 19:34:29'),
	(7, 5, '2025-10-20', '2025-10-24', 28, 20, 7500000.00, 5600000.00, 800000.00, 'completed', 10, NULL, '2025-09-25 04:00:00', '2025-12-08 19:34:29'),
	(8, 1, '2025-10-24', '2025-10-26', 35, 32, 4500000.00, 3500000.00, 500000.00, 'completed', 5, NULL, '2025-09-28 02:00:00', '2025-12-08 19:34:29'),
	(9, 6, '2025-10-26', '2025-10-30', 40, 35, 12500000.00, 9500000.00, 2000000.00, 'completed', 6, NULL, '2025-09-30 08:00:00', '2025-12-08 19:34:29'),
	(10, 4, '2025-10-28', '2025-10-31', 36, 18, 4900000.00, 3700000.00, 500000.00, 'completed', 7, NULL, '2025-10-01 03:00:00', '2025-12-08 19:34:29'),
	(11, 1, '2025-11-01', '2025-11-03', 35, 29, 4500000.00, 3500000.00, 500000.00, 'completed', 8, NULL, '2025-10-05 02:30:00', '2025-12-08 19:34:29'),
	(12, 2, '2025-11-03', '2025-11-06', 30, 24, 5800000.00, 4500000.00, 600000.00, 'completed', 9, NULL, '2025-10-08 04:00:00', '2025-12-08 19:34:29'),
	(13, 3, '2025-11-07', '2025-11-09', 32, 27, 3200000.00, 2400000.00, 400000.00, 'completed', 10, NULL, '2025-10-12 03:00:00', '2025-12-08 19:34:29'),
	(14, 5, '2025-11-09', '2025-11-13', 28, 22, 7500000.00, 5600000.00, 800000.00, 'completed', 5, NULL, '2025-10-15 07:00:00', '2025-12-08 19:34:29'),
	(15, 1, '2025-11-14', '2025-11-16', 35, 31, 4500000.00, 3500000.00, 500000.00, 'in_progress', 6, NULL, '2025-10-20 02:00:00', '2025-12-08 19:34:29'),
	(16, 2, '2025-11-16', '2025-11-19', 30, 25, 5800000.00, 4500000.00, 600000.00, 'in_progress', 7, NULL, '2025-10-22 03:30:00', '2025-12-08 19:34:29'),
	(17, 4, '2025-11-19', '2025-11-22', 36, 28, 4900000.00, 3700000.00, 500000.00, 'in_progress', 8, NULL, '2025-10-25 04:00:00', '2025-12-08 19:34:29'),
	(18, 6, '2025-11-21', '2025-11-25', 40, 32, 12500000.00, 9500000.00, 2000000.00, 'open', 9, NULL, '2025-10-28 08:00:00', '2025-12-08 19:34:29'),
	(19, 1, '2025-11-24', '2025-11-26', 35, 15, 4500000.00, 3500000.00, 500000.00, 'open', 10, NULL, '2025-11-01 02:00:00', '2025-12-08 19:34:29'),
	(20, 3, '2025-11-26', '2025-11-28', 32, 12, 3200000.00, 2400000.00, 400000.00, 'open', 5, NULL, '2025-11-03 03:00:00', '2025-12-08 19:34:29'),
	(21, 2, '2025-11-28', '2025-12-01', 30, 18, 5800000.00, 4500000.00, 600000.00, 'open', 6, NULL, '2025-11-05 04:30:00', '2025-12-08 19:34:29'),
	(22, 5, '2025-11-30', '2025-12-04', 28, 10, 7500000.00, 5600000.00, 800000.00, 'open', 7, NULL, '2025-11-08 07:00:00', '2025-12-08 19:34:29'),
	(23, 1, '2025-12-05', '2025-12-07', 35, 8, 4500000.00, 3500000.00, 500000.00, 'open', 8, NULL, '2025-11-10 02:00:00', '2025-12-08 19:34:29'),
	(24, 2, '2025-12-07', '2025-12-10', 30, 14, 5800000.00, 4500000.00, 600000.00, 'open', 9, NULL, '2025-11-12 03:00:00', '2025-12-08 19:34:29'),
	(25, 4, '2025-12-10', '2025-12-13', 36, 20, 4900000.00, 3700000.00, 500000.00, 'open', 10, NULL, '2025-11-15 04:00:00', '2025-12-08 19:34:29'),
	(26, 1, '2025-12-12', '2025-12-14', 35, 22, 4500000.00, 3500000.00, 500000.00, 'open', 5, NULL, '2025-11-18 02:30:00', '2025-12-08 19:34:29'),
	(27, 6, '2025-12-15', '2025-12-19', 40, 28, 12500000.00, 9500000.00, 2000000.00, 'open', 6, NULL, '2025-11-20 07:00:00', '2025-12-08 19:34:29'),
	(28, 3, '2025-12-19', '2025-12-21', 32, 16, 3200000.00, 2400000.00, 400000.00, 'open', 7, NULL, '2025-11-22 03:00:00', '2025-12-08 19:34:29'),
	(29, 5, '2025-12-22', '2025-12-26', 28, 25, 7500000.00, 5600000.00, 800000.00, 'open', 8, NULL, '2025-11-25 08:00:00', '2025-12-08 19:34:29'),
	(30, 2, '2025-12-27', '2025-12-30', 30, 12, 5800000.00, 4500000.00, 600000.00, 'open', 9, NULL, '2025-11-28 04:00:00', '2025-12-08 19:34:29');

-- Dumping structure for table duan.tour_services
CREATE TABLE IF NOT EXISTS `tour_services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL,
  `service_id` int NOT NULL,
  `service_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calculation_type` enum('per_person','per_group','per_day','fixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'per_person',
  `fixed_quantity` int DEFAULT '1',
  `group_size` int DEFAULT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_included_in_price` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `tour_services_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.tour_services: ~0 rows (approximately)

-- Dumping structure for table duan.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','suspended') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `created_by` (`created_by`),
  KEY `idx_users_email` (`email`),
  KEY `idx_users_role_id` (`role_id`),
  KEY `idx_users_status` (`status`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.users: ~10 rows (approximately)
INSERT INTO `users` (`id`, `role_id`, `email`, `password`, `full_name`, `phone`, `date_of_birth`, `gender`, `address`, `avatar`, `status`, `last_login`, `created_by`, `created_at`, `updated_at`) VALUES
	(1, 1, 'admin@gmail.com', '$2y$10$Xcd2.g4WL0gcWnWIoHv1juhJuUAo7IHG794cPl0tVhzZF0RrgfWKS', 'Nguyễn Văn Quản', '0901234567', '1985-03-15', 'male', '123 Lê Lợi, Quận 1, TP. Hồ Chí Minh', NULL, 'active', NULL, NULL, '2025-01-15 02:00:00', '2025-12-08 20:13:37'),
	(2, 2, 'tranthimai@tourcompany.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị Mai', '0912345678', '1992-07-22', 'female', '45 Trần Hưng Đạo, Hoàn Kiếm, Hà Nội', NULL, 'active', NULL, 1, '2025-02-01 03:00:00', '2025-12-08 19:28:23'),
	(3, 2, 'lehoangnam@tourcompany.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Hoàng Nam', '0923456789', '1990-11-08', 'male', '78 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh', NULL, 'active', NULL, 1, '2025-02-01 03:30:00', '2025-12-08 19:28:23'),
	(4, 2, 'phamthithao@tourcompany.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thị Thảo', '0934567890', '1995-04-18', 'female', '156 Hai Bà Trưng, Hải Châu, Đà Nẵng', NULL, 'active', NULL, 1, '2025-02-15 07:00:00', '2025-12-08 19:28:23'),
	(5, 3, 'nguyenvanhung@tourcompany.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn Hùng', '0945678901', '1988-06-25', 'male', '23 Bà Triệu, Hoàn Kiếm, Hà Nội', NULL, 'active', NULL, 1, '2025-01-20 01:00:00', '2025-12-08 19:28:23'),
	(6, 3, 'tranthilinh@tourcompany.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị Linh', '0956789012', '1993-09-14', 'female', '67 Lý Thường Kiệt, Quận 10, TP. Hồ Chí Minh', NULL, 'active', NULL, 1, '2025-01-20 01:30:00', '2025-12-08 19:28:23'),
	(7, 3, 'leducminh@tourcompany.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Đức Minh', '0967890123', '1991-12-03', 'male', '89 Trường Chinh, Thanh Xuân, Hà Nội', NULL, 'active', NULL, 1, '2025-01-25 02:00:00', '2025-12-08 19:28:23'),
	(8, 3, 'hoangthiquynhanh@tourcompany.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hoàng Thị Quỳnh Anh', '0978901234', '1994-02-28', 'female', '12 Phan Chu Trinh, Sơn Trà, Đà Nẵng', NULL, 'active', NULL, 1, '2025-02-05 03:00:00', '2025-12-08 19:28:23'),
	(9, 3, 'phanquanghuy@tourcompany.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phan Quang Huy', '0989012345', '1987-05-17', 'male', '34 Nguyễn Thị Minh Khai, Quận 3, TP. Hồ Chí Minh', NULL, 'active', NULL, 1, '2025-02-10 04:00:00', '2025-12-08 19:28:23'),
	(10, 3, 'nv@gmail.com', '$2y$10$Xcd2.g4WL0gcWnWIoHv1juhJuUAo7IHG794cPl0tVhzZF0RrgfWKS', 'Vũ Thị Hồng Nhung', '0990123456', '1996-08-09', 'female', '56 Lê Duẩn, Nha Trang, Khánh Hòa', NULL, 'active', '2025-12-08 20:13:59', 1, '2025-02-20 07:30:00', '2025-12-08 20:13:59');

-- Dumping structure for table duan.vehicles
CREATE TABLE IF NOT EXISTS `vehicles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vehicle_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_type` enum('bus_45','bus_29','bus_16','car_7','car_4') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `license_plate` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` int NOT NULL,
  `status` enum('active','maintenance','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_code` (`vehicle_code`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.vehicles: ~0 rows (approximately)
INSERT INTO `vehicles` (`id`, `vehicle_code`, `vehicle_type`, `license_plate`, `capacity`, `status`, `notes`, `created_at`, `updated_at`) VALUES
	(6, 'VEH-20241206-001', 'bus_45', '51A-12345', 45, 'active', NULL, '2025-12-08 20:09:45', '2025-12-08 20:09:45'),
	(7, 'VEH-20241206-002', 'bus_45', '29A-98765', 45, 'active', NULL, '2025-12-08 20:10:17', '2025-12-08 20:10:17'),
	(8, 'VEH-20241206-003', 'bus_29', '51A-12348', 29, 'active', NULL, '2025-12-08 20:10:40', '2025-12-08 20:10:40'),
	(9, 'VEH-20241206-004', 'bus_16', '21A-13333', 16, 'active', NULL, '2025-12-08 20:11:00', '2025-12-08 20:11:00'),
	(10, 'VEH-20241206-005', 'car_7', '29A-93333', 7, 'active', NULL, '2025-12-08 20:11:23', '2025-12-08 20:11:23'),
	(11, 'VEH-20241206-006', 'car_4', '241A-99999', 4, 'active', NULL, '2025-12-08 20:11:47', '2025-12-08 20:11:47');

-- Dumping structure for table duan.vehicle_assignments
CREATE TABLE IF NOT EXISTS `vehicle_assignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_schedule_id` int NOT NULL,
  `vehicle_id` int NOT NULL,
  `driver_id` int NOT NULL,
  `assignment_date` date NOT NULL COMMENT 'Ngày phân công',
  `start_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_date` date NOT NULL,
  `end_time` time DEFAULT NULL,
  `pickup_location` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Địa điểm đón xe',
  `return_location` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Địa điểm trả xe',
  `estimated_distance` decimal(10,2) DEFAULT '0.00' COMMENT 'Quãng đường dự kiến (km)',
  `actual_distance` decimal(10,2) DEFAULT '0.00' COMMENT 'Quãng đường thực tế (km)',
  `estimated_fuel_cost` decimal(15,2) DEFAULT '0.00' COMMENT 'Chi phí nhiên liệu dự kiến',
  `actual_fuel_cost` decimal(15,2) DEFAULT '0.00' COMMENT 'Chi phí nhiên liệu thực tế',
  `driver_salary` decimal(15,2) DEFAULT '0.00' COMMENT 'Phụ cấp tour cho tài xế (tự động từ tour_allowance_rules)',
  `total_cost` decimal(15,2) DEFAULT '0.00' COMMENT 'Tổng chi phí',
  `status` enum('assigned','confirmed','in_use','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'assigned',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `assigned_by` int DEFAULT NULL COMMENT 'Ai phân công',
  `confirmed_by` int DEFAULT NULL COMMENT 'Ai xác nhận',
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tour_schedule_id` (`tour_schedule_id`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `driver_id` (`driver_id`),
  KEY `assigned_by` (`assigned_by`),
  KEY `confirmed_by` (`confirmed_by`),
  KEY `idx_vehicle_assignments_status` (`status`),
  CONSTRAINT `vehicle_assignments_ibfk_assigned_by` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `vehicle_assignments_ibfk_confirmed_by` FOREIGN KEY (`confirmed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `vehicle_assignments_ibfk_driver` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`),
  CONSTRAINT `vehicle_assignments_ibfk_schedule` FOREIGN KEY (`tour_schedule_id`) REFERENCES `tour_schedules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vehicle_assignments_ibfk_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.vehicle_assignments: ~0 rows (approximately)

-- Dumping structure for table duan.vehicle_assignment_history
CREATE TABLE IF NOT EXISTS `vehicle_assignment_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vehicle_assignment_id` int NOT NULL,
  `action` enum('created','updated','vehicle_changed','driver_changed','status_changed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_values` json DEFAULT NULL COMMENT 'Snapshot giá trị cũ',
  `new_values` json DEFAULT NULL COMMENT 'Snapshot giá trị mới',
  `changed_by` int DEFAULT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vehicle_assignment_id` (`vehicle_assignment_id`),
  KEY `changed_by` (`changed_by`),
  CONSTRAINT `vehicle_assignment_history_ibfk_assignment` FOREIGN KEY (`vehicle_assignment_id`) REFERENCES `vehicle_assignments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `vehicle_assignment_history_ibfk_changed_by` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.vehicle_assignment_history: ~0 rows (approximately)

-- Dumping structure for table duan.vehicle_maintenance
CREATE TABLE IF NOT EXISTS `vehicle_maintenance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vehicle_id` int NOT NULL,
  `maintenance_type` enum('routine','repair','inspection','emergency') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `maintenance_date` date NOT NULL,
  `maintenance_provider` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nơi bảo dưỡng (công ty, địa chỉ)',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Mô tả công việc',
  `cost` decimal(15,2) DEFAULT '0.00',
  `mileage_before` int DEFAULT '0' COMMENT 'Số km trước bảo dưỡng',
  `mileage_after` int DEFAULT '0' COMMENT 'Số km sau bảo dưỡng',
  `next_maintenance_date` date DEFAULT NULL COMMENT 'Ngày bảo dưỡng tiếp theo',
  `next_maintenance_mileage` int DEFAULT NULL COMMENT 'Số km bảo dưỡng tiếp theo',
  `status` enum('scheduled','in_progress','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'scheduled',
  `performed_by` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Người thực hiện (có thể là user hoặc text)',
  `receipt_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'File hóa đơn',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `maintenance_date` (`maintenance_date`),
  KEY `idx_vehicle_maintenance_status` (`status`),
  KEY `vehicle_maintenance_ibfk_created_by` (`created_by`),
  CONSTRAINT `vehicle_maintenance_ibfk_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `vehicle_maintenance_ibfk_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table duan.vehicle_maintenance: ~0 rows (approximately)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
