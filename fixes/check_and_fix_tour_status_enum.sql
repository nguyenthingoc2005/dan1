-- ==============================================================================
-- SCRIPT KIỂM TRA VÀ SỬA ENUM CỦA CỘT status TRONG BẢNG tours
-- ==============================================================================
-- 
-- Mục đích: Đảm bảo enum của status có đầy đủ các giá trị: draft, pending, active, rejected, inactive
-- 
-- Date: 2024-12-XX
-- ==============================================================================

-- Bước 1: Kiểm tra enum hiện tại
SELECT COLUMN_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'tours' 
  AND COLUMN_NAME = 'status';

-- Bước 2: Nếu enum chưa đúng, cập nhật lại
-- Lưu ý: MySQL không cho phép MODIFY enum trực tiếp nếu có dữ liệu không hợp lệ
-- Nên cần kiểm tra và migrate dữ liệu trước

-- Kiểm tra dữ liệu hiện tại
SELECT status, COUNT(*) as count 
FROM tours 
GROUP BY status;

-- Nếu có giá trị không hợp lệ, cần migrate trước
-- Ví dụ: nếu có status = 'approved' (từ approval_status cũ), cần chuyển thành 'active'
UPDATE tours 
SET status = CASE
    WHEN status = 'approved' THEN 'active'
    WHEN status NOT IN ('draft', 'pending', 'active', 'rejected', 'inactive') THEN 'draft'
    ELSE status
END
WHERE status NOT IN ('draft', 'pending', 'active', 'rejected', 'inactive');

-- Bước 3: Cập nhật enum
ALTER TABLE tours 
MODIFY COLUMN status enum('draft','pending','active','rejected','inactive') 
COLLATE utf8mb4_unicode_ci DEFAULT 'draft';

-- Bước 4: Verify sau khi update
SELECT COLUMN_TYPE, COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'tours' 
  AND COLUMN_NAME = 'status';

-- Kỳ vọng:
-- COLUMN_TYPE: enum('draft','pending','active','rejected','inactive')
-- COLUMN_DEFAULT: 'draft'

