-- ============================================================================
-- SCHEMA: TIMELINE CHI TIẾT VÀ DỊCH VỤ THEO NGÀY CHO TOUR
-- ============================================================================
-- Mục đích: 
-- - Timeline chi tiết cho từng ngày (giờ, địa điểm, hoạt động)
-- - Gán dịch vụ vào từng ngày để tính chi phí chính xác
-- - Biết được ngày nào ở khách sạn nào, ăn ở đâu
-- ============================================================================

-- ============================================================================
-- BẢNG 1: itinerary_timelines
-- ============================================================================
-- Timeline chi tiết cho từng ngày của tour
-- Mỗi timeline item = 1 hoạt động trong ngày (có giờ, địa điểm, mô tả)

CREATE TABLE IF NOT EXISTS `itinerary_timelines` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `itinerary_id` INT NOT NULL COMMENT 'Foreign key → itineraries',
  `timeline_time` TIME NOT NULL COMMENT 'Giờ (VD: 07:00, 08:30)',
  `activity_title` VARCHAR(200) NOT NULL COMMENT 'Tên hoạt động (VD: "Ăn sáng")',
  `activity_description` TEXT NULL COMMENT 'Mô tả chi tiết hoạt động',
  `location` VARCHAR(200) NULL COMMENT 'Địa điểm (VD: "Nhà hàng ABC")',
  `destination_id` INT NULL COMMENT 'Foreign key → destinations',
  `service_provider_id` INT NULL COMMENT 'Foreign key → service_providers (khách sạn, nhà hàng)',
  `service_id` INT NULL COMMENT 'Foreign key → services (optional)',
  `timeline_type` ENUM('meal', 'accommodation', 'activity', 'transport') NOT NULL DEFAULT 'activity' COMMENT 'Loại timeline',
  `display_order` INT NOT NULL DEFAULT 0 COMMENT 'Thứ tự hiển thị (sắp xếp theo giờ)',
  `notes` TEXT NULL COMMENT 'Ghi chú',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_itinerary_id` (`itinerary_id`),
  KEY `idx_destination_id` (`destination_id`),
  KEY `idx_service_provider_id` (`service_provider_id`),
  KEY `idx_service_id` (`service_id`),
  KEY `idx_timeline_time` (`timeline_time`),
  KEY `idx_timeline_type` (`timeline_type`),
  
  CONSTRAINT `fk_itinerary_timelines_itinerary` 
    FOREIGN KEY (`itinerary_id`) REFERENCES `itineraries` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_itinerary_timelines_destination` 
    FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_itinerary_timelines_service_provider` 
    FOREIGN KEY (`service_provider_id`) REFERENCES `service_providers` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_itinerary_timelines_service` 
    FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Timeline chi tiết cho từng ngày của tour';

-- ============================================================================
-- BẢNG 2: itinerary_day_services
-- ============================================================================
-- Liên kết dịch vụ với từng ngày của tour (để tính chi phí)
-- Mỗi record = 1 dịch vụ được sử dụng trong 1 ngày cụ thể

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
  
  CONSTRAINT `fk_itinerary_day_services_itinerary` 
    FOREIGN KEY (`itinerary_id`) REFERENCES `itineraries` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_itinerary_day_services_service` 
    FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_itinerary_day_services_service_provider` 
    FOREIGN KEY (`service_provider_id`) REFERENCES `service_providers` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Dịch vụ theo từng ngày của tour (để tính chi phí)';

-- ============================================================================
-- INDEXES BỔ SUNG (Để tối ưu query)
-- ============================================================================

-- Index để query timeline theo ngày và sắp xếp theo giờ
CREATE INDEX `idx_itinerary_time_order` ON `itinerary_timelines` (`itinerary_id`, `timeline_time`, `display_order`);

-- Index để query dịch vụ theo ngày và filter included
CREATE INDEX `idx_itinerary_service_included` ON `itinerary_day_services` (`itinerary_id`, `is_included_in_price`, `unit_price`);

-- ============================================================================
-- VIEW: Tổng hợp chi phí dịch vụ theo ngày
-- ============================================================================

CREATE OR REPLACE VIEW `v_itinerary_day_service_cost` AS
SELECT 
  ids.itinerary_id,
  i.tour_id,
  i.day_number,
  COUNT(ids.id) AS total_services,
  SUM(CASE WHEN ids.is_included_in_price = 1 THEN ids.unit_price * ids.quantity ELSE 0 END) AS total_cost_per_person,
  SUM(CASE WHEN ids.is_included_in_price = 1 THEN 1 ELSE 0 END) AS included_services_count
FROM 
  `itinerary_day_services` ids
  INNER JOIN `itineraries` i ON ids.itinerary_id = i.id
GROUP BY 
  ids.itinerary_id, i.tour_id, i.day_number;

-- ============================================================================
-- VIEW: Tổng hợp chi phí dịch vụ theo tour
-- ============================================================================

CREATE OR REPLACE VIEW `v_tour_service_cost_summary` AS
SELECT 
  i.tour_id,
  COUNT(DISTINCT i.id) AS total_days,
  COUNT(ids.id) AS total_services,
  SUM(CASE WHEN ids.is_included_in_price = 1 THEN ids.unit_price * ids.quantity ELSE 0 END) AS total_service_cost_per_person,
  SUM(CASE WHEN ids.is_included_in_price = 1 THEN 1 ELSE 0 END) AS included_services_count,
  SUM(CASE WHEN ids.is_included_in_price = 0 THEN ids.unit_price * ids.quantity ELSE 0 END) AS optional_service_cost_per_person
FROM 
  `itineraries` i
  LEFT JOIN `itinerary_day_services` ids ON i.id = ids.itinerary_id
GROUP BY 
  i.tour_id;

-- ============================================================================
-- TRIGGER: Auto-update service_name khi tạo/update
-- ============================================================================

DELIMITER $$

CREATE TRIGGER `trg_itinerary_day_services_set_name` 
BEFORE INSERT ON `itinerary_day_services`
FOR EACH ROW
BEGIN
  IF NEW.service_name IS NULL OR NEW.service_name = '' THEN
    SELECT `name` INTO NEW.service_name 
    FROM `services` 
    WHERE `id` = NEW.service_id 
    LIMIT 1;
  END IF;
END$$

CREATE TRIGGER `trg_itinerary_day_services_update_name` 
BEFORE UPDATE ON `itinerary_day_services`
FOR EACH ROW
BEGIN
  IF NEW.service_name IS NULL OR NEW.service_name = '' THEN
    SELECT `name` INTO NEW.service_name 
    FROM `services` 
    WHERE `id` = NEW.service_id 
    LIMIT 1;
  END IF;
END$$

DELIMITER ;

-- ============================================================================
-- STORED PROCEDURE: Tính chi phí dịch vụ/người cho tour
-- ============================================================================

DELIMITER $$

CREATE PROCEDURE `sp_calculate_tour_service_cost`(IN p_tour_id INT)
BEGIN
  SELECT 
    SUM(ids.unit_price * ids.quantity) AS total_service_cost_per_person
  FROM 
    `itinerary_day_services` ids
    INNER JOIN `itineraries` i ON ids.itinerary_id = i.id
  WHERE 
    i.tour_id = p_tour_id
    AND ids.is_included_in_price = 1;
END$$

DELIMITER ;

-- ============================================================================
-- GHI CHÚ VÀ HƯỚNG DẪN SỬ DỤNG
-- ============================================================================

/*
1. LUỒNG TẠO TIMELINE:
   - Tạo itinerary (ngày) → Tạo timeline items cho ngày đó → Gán dịch vụ vào ngày

2. LUỒNG TÍNH CHI PHÍ:
   - Query tất cả itinerary_day_services có is_included_in_price = 1
   - SUM(unit_price × quantity) → Chi phí dịch vụ/người

3. BACKWARD COMPATIBILITY:
   - Giữ nguyên bảng tour_services (để tương thích với tour cũ)
   - Tour mới nên dùng itinerary_day_services
   - Có thể migrate dữ liệu từ tour_services → itinerary_day_services (nhưng không biết ngày nào → Gán vào ngày 1)

4. VALIDATION:
   - Mỗi ngày phải có ít nhất 1 timeline item
   - Nếu timeline_type = 'meal' → Phải có service_provider_id (nhà hàng)
   - Nếu timeline_type = 'accommodation' → Phải có service_provider_id (khách sạn)

5. AUTO-SYNC:
   - Khi thêm timeline item với service_provider_id và service_id → Tự động tạo itinerary_day_services (nếu chưa có)
   - Khi xóa timeline item → Xóa itinerary_day_services tương ứng (nếu có)
*/

