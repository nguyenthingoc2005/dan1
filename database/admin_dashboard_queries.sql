-- ==============================================================================
-- ADMIN DASHBOARD QUERIES
-- ==============================================================================
-- 
-- Các câu truy vấn SQL để hiển thị thông tin trên Admin Dashboard
-- 
-- @date 2024-12-XX
-- ==============================================================================

-- ==============================================================================
-- 1. TỔNG SỐ BOOKINGS
-- ==============================================================================
SELECT COUNT(*) as total 
FROM bookings;

-- ==============================================================================
-- 2. SỐ BOOKINGS ĐÃ DUYỆT
-- ==============================================================================
SELECT COUNT(*) as total 
FROM bookings 
WHERE approval_status = 'approved';

-- ==============================================================================
-- 3. SỐ BOOKINGS CHỜ DUYỆT
-- ==============================================================================
SELECT COUNT(*) as total 
FROM bookings 
WHERE approval_status = 'pending';

-- ==============================================================================
-- 4. TỔNG DOANH THU
-- Tính từ payments đã hoàn thành, không tính refund
-- ==============================================================================
SELECT COALESCE(SUM(amount), 0) as total 
FROM payments 
WHERE status = 'completed' 
AND payment_type != 'refund';

-- ==============================================================================
-- 5. SỐ TOURS HOẠT ĐỘNG
-- Tours có status = 'active' và approval_status = 'approved'
-- ==============================================================================
SELECT COUNT(*) as total 
FROM tours 
WHERE status = 'active' 
AND approval_status = 'approved';

-- ==============================================================================
-- 6. SỐ TOURS CHỜ DUYỆT
-- ==============================================================================
SELECT COUNT(*) as total 
FROM tours 
WHERE approval_status = 'pending';

-- ==============================================================================
-- 7. BOOKING GẦN ĐÂY (10 mới nhất)
-- Hiển thị thông tin booking kèm tour và customer
-- ==============================================================================
SELECT 
    b.id,
    b.booking_code,
    b.start_date,
    b.final_amount,
    b.approval_status,
    t.name as tour_name,
    c.full_name as customer_name
FROM bookings b
LEFT JOIN tours t ON b.tour_id = t.id
LEFT JOIN customers c ON b.customer_id = c.id
ORDER BY b.created_at DESC
LIMIT 10;

-- ==============================================================================
-- 8. TOURS CHỜ DUYỆT (danh sách)
-- Hiển thị thông tin tour kèm người tạo
-- ==============================================================================
SELECT 
    t.id,
    t.name,
    t.duration_days,
    t.adult_price,
    t.created_at,
    u.full_name as staff_name
FROM tours t
LEFT JOIN users u ON t.created_by = u.id
WHERE t.approval_status = 'pending'
ORDER BY t.created_at DESC
LIMIT 10;

-- ==============================================================================
-- BONUS: DOANH THU THEO THÁNG (cho biểu đồ)
-- ==============================================================================
SELECT 
    DATE_FORMAT(payment_date, '%Y-%m') as month,
    SUM(amount) as revenue
FROM payments 
WHERE status = 'completed' 
AND payment_type != 'refund'
GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
ORDER BY month DESC
LIMIT 12;

-- ==============================================================================
-- BONUS: BOOKINGS THEO TRẠNG THÁI (cho biểu đồ)
-- ==============================================================================
SELECT 
    approval_status,
    COUNT(*) as count
FROM bookings
GROUP BY approval_status;

-- ==============================================================================
-- BONUS: TOP 5 TOURS CÓ NHIỀU BOOKING NHẤT
-- ==============================================================================
SELECT 
    t.id,
    t.name,
    t.tour_code,
    COUNT(b.id) as booking_count,
    SUM(b.final_amount) as total_revenue
FROM tours t
LEFT JOIN bookings b ON t.id = b.tour_id 
    AND b.approval_status IN ('approved', 'completed')
GROUP BY t.id, t.name, t.tour_code
ORDER BY booking_count DESC
LIMIT 5;

