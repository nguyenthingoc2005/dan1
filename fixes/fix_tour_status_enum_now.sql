-- ==============================================================================
-- SCRIPT SỬA ENUM CỦA CỘT status TRONG BẢNG tours
-- ==============================================================================
-- 
-- Enum hiện tại: enum('active','inactive','draft')
-- Enum mới: enum('draft','pending','active','rejected','inactive')
-- 
-- Date: 2024-12-XX
-- ==============================================================================

-- Bước 1: Kiểm tra dữ liệu hiện tại (xem có giá trị nào không hợp lệ không)
SELECT status, COUNT(*) as count 
FROM tours 
GROUP BY status;

-- Bước 2: Nếu có dữ liệu, đảm bảo tất cả giá trị đều hợp lệ với enum mới
-- (Hiện tại enum chỉ có 'active','inactive','draft' nên không cần migrate)

-- Bước 3: Cập nhật enum để thêm 'pending' và 'rejected'
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

