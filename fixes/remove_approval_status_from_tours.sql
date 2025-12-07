-- ==============================================================================
-- MIGRATION: XÓA CỘT approval_status TỪ BẢNG tours
-- ==============================================================================
-- 
-- Mục đích: Đơn giản hóa trạng thái tour, chỉ dùng 1 cột 'status'
-- Giữ lại: approved_by, approved_at, rejection_reason (để audit trail)
-- 
-- Date: 2024-12-XX
-- ==============================================================================

-- Bước 1: Migrate dữ liệu từ approval_status sang status
-- Chuyển đổi:
--   - approval_status = 'pending' → status = 'pending'
--   - approval_status = 'approved' AND status = 'active' → status = 'active'
--   - approval_status = 'rejected' → status = 'rejected'
--   - approval_status = 'approved' AND status = 'draft' → status = 'active' (nếu có)
--   - approval_status = 'approved' AND status = 'inactive' → status = 'inactive' (giữ nguyên)

UPDATE tours 
SET status = CASE
    WHEN approval_status = 'pending' THEN 'pending'
    WHEN approval_status = 'approved' AND status = 'active' THEN 'active'
    WHEN approval_status = 'approved' AND status = 'draft' THEN 'active'
    WHEN approval_status = 'approved' AND status = 'inactive' THEN 'inactive'
    WHEN approval_status = 'rejected' THEN 'rejected'
    ELSE status
END
WHERE approval_status IS NOT NULL;

-- Bước 2: Cập nhật cột status để thêm các giá trị mới
ALTER TABLE tours 
MODIFY COLUMN status enum('draft','pending','active','rejected','inactive') 
COLLATE utf8mb4_unicode_ci DEFAULT 'draft';

-- Bước 3: Xóa cột approval_status
ALTER TABLE tours DROP COLUMN approval_status;

-- ==============================================================================
-- VERIFY: Kiểm tra sau khi migration
-- ==============================================================================
-- SELECT status, COUNT(*) as count 
-- FROM tours 
-- GROUP BY status;
-- 
-- Kỳ vọng:
-- - draft: Tours nháp, chưa gửi duyệt
-- - pending: Tours đang chờ duyệt
-- - active: Tours đã duyệt và đang hoạt động
-- - rejected: Tours bị từ chối
-- - inactive: Tours bị ẩn/tạm dừng
-- ==============================================================================

