-- ==============================================================================
-- POLICIES MODULE - DATABASE SCHEMA
-- ==============================================================================
-- 
-- Module quản lý chính sách (Policies) cho tour
-- 
-- Tables:
-- - policies: Bảng chính sách
-- - tour_policies: Bảng liên kết tour và policies (many-to-many)
-- 
-- Date: 2024-12-06
-- ==============================================================================

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- ==============================================================================
-- POLICIES TABLE
-- ==============================================================================
-- 
-- Bảng chính sách: Lưu các chính sách có thể áp dụng cho tour
-- 
-- Fields:
-- - id: Primary key
-- - name: Tên chính sách (VD: "Chính sách hủy tour", "Chính sách hoàn tiền")
-- - description: Mô tả ngắn gọn
-- - policy_type: Loại chính sách (cancellation, refund, payment, other)
-- - content: Nội dung chi tiết (HTML/Text)
-- - status: Trạng thái (active, inactive)
-- - created_at, updated_at: Timestamps
-- 
-- ==============================================================================

CREATE TABLE IF NOT EXISTS `policies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên chính sách',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Mô tả ngắn gọn',
  `policy_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Loại chính sách: cancellation, refund, payment, other',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nội dung chi tiết (HTML/Text)',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active' COMMENT 'Trạng thái: active, inactive',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_policies_status` (`status`),
  KEY `idx_policies_type` (`policy_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng chính sách';

-- ==============================================================================
-- TOUR POLICIES TABLE (Many-to-Many Relationship)
-- ==============================================================================
-- 
-- Bảng liên kết: Một tour có thể có nhiều policies, một policy có thể áp dụng cho nhiều tour
-- 
-- Fields:
-- - id: Primary key
-- - tour_id: Foreign key → tours.id
-- - policy_id: Foreign key → policies.id
-- 
-- Constraints:
-- - ON DELETE CASCADE: Khi xóa tour hoặc policy, tự động xóa liên kết
-- 
-- ==============================================================================

CREATE TABLE IF NOT EXISTS `tour_policies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tour_id` int NOT NULL COMMENT 'Foreign key → tours.id',
  `policy_id` int NOT NULL COMMENT 'Foreign key → policies.id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tour_policy` (`tour_id`, `policy_id`) COMMENT 'Một tour không thể có cùng một policy 2 lần',
  KEY `idx_tour_policies_tour_id` (`tour_id`),
  KEY `idx_tour_policies_policy_id` (`policy_id`),
  CONSTRAINT `tour_policies_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tour_policies_ibfk_2` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng liên kết tour và policies (many-to-many)';

-- ==============================================================================
-- SAMPLE DATA (Optional)
-- ==============================================================================

-- INSERT INTO `policies` (`name`, `description`, `policy_type`, `content`, `status`) VALUES
-- ('Chính sách hủy tour', 'Quy định về việc hủy tour và phí hủy', 'cancellation', '<p>Nội dung chính sách hủy tour...</p>', 'active'),
-- ('Chính sách hoàn tiền', 'Quy định về việc hoàn tiền khi hủy tour', 'refund', '<p>Nội dung chính sách hoàn tiền...</p>', 'active'),
-- ('Chính sách thanh toán', 'Quy định về phương thức và thời hạn thanh toán', 'payment', '<p>Nội dung chính sách thanh toán...</p>', 'active');

-- ==============================================================================
-- INDEXES FOR PERFORMANCE
-- ==============================================================================

-- Indexes đã được tạo trong CREATE TABLE statements
-- - idx_policies_status: Để filter theo status
-- - idx_policies_type: Để filter theo policy_type
-- - idx_tour_policies_tour_id: Để query policies của một tour
-- - idx_tour_policies_policy_id: Để query tours sử dụng một policy

-- ==============================================================================
-- NOTES
-- ==============================================================================
-- 
-- 1. Một tour có thể có nhiều policies (VD: chính sách hủy tour + chính sách hoàn tiền)
-- 2. Một policy có thể áp dụng cho nhiều tour
-- 3. Khi xóa tour, các liên kết policies sẽ tự động bị xóa (CASCADE)
-- 4. Khi xóa policy, các liên kết với tour sẽ tự động bị xóa (CASCADE)
-- 5. Nên kiểm tra số tour đang sử dụng policy trước khi xóa policy
-- 
-- ==============================================================================

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

