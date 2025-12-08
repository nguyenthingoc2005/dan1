-- ==============================================================================
-- DATABASE SCHEMA HOÀN CHỈNH - TOUR MANAGEMENT SYSTEM V2
-- ==============================================================================
-- 
-- Tổng hợp tất cả các module với tính năng mới:
-- 
-- NEW FEATURES:
-- - ✅ Phân phòng (Room Assignment)
-- - ✅ Check-in chi tiết theo hoạt động (Activity Check-in)
-- - ✅ Quản lý xe và tài xế (Vehicle & Driver Management)
-- - ✅ Template chi phí cố định (Tour Cost Templates) - Phương án 3
-- - ✅ Tính phụ cấp tự động (Tour Allowance Rules)
-- 
-- CHANGES:
-- - ✅ Tours: Bỏ 4 cột fixed_cost, thay bằng 1 cột fixed_cost_total + template
-- - ✅ Tour Schedules: Thêm status 'confirmed'
-- - ✅ Incurred Expenses: Thêm tour_schedule_id, booking_id có thể NULL
-- - ✅ Tour Assignments: tour_schedule_id NOT NULL
-- - ✅ Service Provider Payments: Thêm tour_schedule_id
-- 
-- Tổng số bảng: 53 bảng
-- 
-- Date: 2024-12-XX
-- ==============================================================================

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping database structure for tour_management
CREATE DATABASE IF NOT EXISTS `tour_managementss` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `tour_managementss`;

-- ==============================================================================
-- IMPORT TẤT CẢ CÁC MODULE
-- ==============================================================================

-- Module 1: System (Users & Roles)
SOURCE database/01_system.sql;

-- Module 2: Location Services
SOURCE database/02_location_services.sql;

-- Module 3: Tour
SOURCE database/03_tour.sql;

-- Module 4: Customer
SOURCE database/04_customer.sql;

-- Module 5: Booking
SOURCE database/05_booking.sql;

-- Module 6: Payment
SOURCE database/06_payment.sql;

-- Module 7: Operations
SOURCE database/07_operations.sql;

-- Module 8: Room Assignment (MỚI)
SOURCE database/08_room_assignment.sql;

-- Module 9: Activity Check-in (MỚI)
SOURCE database/09_activity_checkin.sql;

-- Module 10: Vehicle & Driver (MỚI)
SOURCE database/10_vehicle_driver.sql;

-- Module 11: System Other
SOURCE database/11_system_other.sql;

-- ==============================================================================
-- INSERT DỮ LIỆU MẪU
-- ==============================================================================

-- Insert mẫu Tour Cost Templates
INSERT INTO `tour_cost_templates` (`template_name`, `description`, `fixed_cost_total`, `is_default`, `status`) VALUES
('Tour trong nước 3 ngày', 'Template cho tour trong nước ngắn ngày', 2000000, 1, 'active'),
('Tour trong nước 5-7 ngày', 'Template cho tour trong nước dài ngày', 3000000, 0, 'active'),
('Tour quốc tế', 'Template cho tour quốc tế', 5000000, 0, 'active');

-- Insert mẫu Tour Allowance Rules
INSERT INTO `tour_allowance_rules` 
(`rule_name`, `tour_type`, `duration_days_min`, `duration_days_max`, `participant_min`, `participant_max`, `guide_allowance`, `driver_allowance`, `priority`, `status`) VALUES
('Tour public 1-3 ngày, 15-20 khách', 'public', 1, 3, 15, 20, 1000000, 500000, 10, 'active'),
('Tour public 1-3 ngày, 21-30 khách', 'public', 1, 3, 21, 30, 1500000, 800000, 10, 'active'),
('Tour public 1-3 ngày, 31-45 khách', 'public', 1, 3, 31, 45, 2000000, 1000000, 10, 'active'),
('Tour public 4-7 ngày, 15-20 khách', 'public', 4, 7, 15, 20, 2000000, 1000000, 10, 'active'),
('Tour public 4-7 ngày, 21-30 khách', 'public', 4, 7, 21, 30, 2500000, 1200000, 10, 'active'),
('Tour custom', 'custom', NULL, NULL, NULL, NULL, 3000000, 1500000, 5, 'active');

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

