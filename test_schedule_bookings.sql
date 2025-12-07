-- ==============================================================================
-- SQL QUERIES ĐỂ TEST SCHEDULE ID = 2
-- ==============================================================================

-- 1. Kiểm tra thông tin schedule
SELECT 
    id,
    tour_id,
    start_date,
    end_date,
    quota,
    booked,
    status
FROM tour_schedules
WHERE id = 2;

-- 2. Kiểm tra tất cả bookings cho schedule này (theo tour_id + start_date)
SELECT 
    b.id,
    b.booking_code,
    b.tour_id,
    b.tour_schedule_id,
    b.start_date,
    b.adult_count,
    b.child_count,
    b.infant_count,
    (b.adult_count + b.child_count + b.infant_count) as total_participants,
    b.approval_status,
    b.payment_status,
    c.full_name as customer_name,
    c.phone as customer_phone
FROM bookings b
LEFT JOIN customers c ON b.customer_id = c.id
WHERE b.tour_id = (
    SELECT tour_id FROM tour_schedules WHERE id = 2
)
AND b.start_date = (
    SELECT start_date FROM tour_schedules WHERE id = 2
)
ORDER BY b.created_at DESC;

-- 3. Kiểm tra bookings theo tour_schedule_id (nếu có)
SELECT 
    b.id,
    b.booking_code,
    b.tour_id,
    b.tour_schedule_id,
    b.start_date,
    b.adult_count,
    b.child_count,
    b.infant_count,
    (b.adult_count + b.child_count + b.infant_count) as total_participants,
    b.approval_status,
    b.payment_status,
    c.full_name as customer_name,
    c.phone as customer_phone
FROM bookings b
LEFT JOIN customers c ON b.customer_id = c.id
WHERE b.tour_schedule_id = 2
ORDER BY b.created_at DESC;

-- 4. Tính tổng số người đã đặt (chỉ approved/pending/completed)
SELECT 
    COUNT(*) as booking_count,
    COALESCE(SUM(adult_count + child_count + infant_count), 0) as total_participants,
    SUM(CASE WHEN approval_status = 'approved' THEN 1 ELSE 0 END) as approved_count,
    SUM(CASE WHEN approval_status = 'pending' THEN 1 ELSE 0 END) as pending_count,
    SUM(CASE WHEN approval_status = 'completed' THEN 1 ELSE 0 END) as completed_count,
    SUM(CASE WHEN approval_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count,
    SUM(CASE WHEN approval_status = 'rejected' THEN 1 ELSE 0 END) as rejected_count
FROM bookings
WHERE tour_id = (
    SELECT tour_id FROM tour_schedules WHERE id = 2
)
AND start_date = (
    SELECT start_date FROM tour_schedules WHERE id = 2
)
AND approval_status IN ('approved', 'pending', 'completed');

-- 5. Tính tổng số người đã đặt (theo tour_schedule_id nếu có)
SELECT 
    COUNT(*) as booking_count,
    COALESCE(SUM(adult_count + child_count + infant_count), 0) as total_participants,
    SUM(CASE WHEN approval_status = 'approved' THEN 1 ELSE 0 END) as approved_count,
    SUM(CASE WHEN approval_status = 'pending' THEN 1 ELSE 0 END) as pending_count,
    SUM(CASE WHEN approval_status = 'completed' THEN 1 ELSE 0 END) as completed_count,
    SUM(CASE WHEN approval_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count,
    SUM(CASE WHEN approval_status = 'rejected' THEN 1 ELSE 0 END) as rejected_count
FROM bookings
WHERE tour_schedule_id = 2
AND approval_status IN ('approved', 'pending', 'completed');

-- 6. So sánh booked trong schedule vs thực tế
SELECT 
    ts.id as schedule_id,
    ts.tour_id,
    ts.start_date,
    ts.booked as booked_in_schedule,
    COALESCE((
        SELECT SUM(adult_count + child_count + infant_count)
        FROM bookings
        WHERE tour_id = ts.tour_id
          AND start_date = ts.start_date
          AND approval_status IN ('approved', 'pending', 'completed')
    ), 0) as actual_booked_from_bookings,
    COALESCE((
        SELECT COUNT(*)
        FROM bookings
        WHERE tour_id = ts.tour_id
          AND start_date = ts.start_date
          AND approval_status IN ('approved', 'pending', 'completed')
    ), 0) as booking_count
FROM tour_schedules ts
WHERE ts.id = 2;

-- 7. Danh sách chi tiết khách hàng đã đặt (để hiển thị)
SELECT 
    b.id as booking_id,
    b.booking_code,
    b.tour_id,
    b.tour_schedule_id,
    b.start_date,
    c.id as customer_id,
    c.full_name as customer_name,
    c.phone as customer_phone,
    c.email as customer_email,
    b.adult_count,
    b.child_count,
    b.infant_count,
    (b.adult_count + b.child_count + b.infant_count) as total_participants,
    b.total_amount,
    b.final_amount,
    b.payment_status,
    b.approval_status,
    b.created_at
FROM bookings b
LEFT JOIN customers c ON b.customer_id = c.id
WHERE b.tour_id = (
    SELECT tour_id FROM tour_schedules WHERE id = 2
)
AND b.start_date = (
    SELECT start_date FROM tour_schedules WHERE id = 2
)
ORDER BY b.created_at DESC;

-- 8. DEBUG: Kiểm tra tất cả bookings (không filter gì) để tìm vấn đề
SELECT 
    b.id,
    b.booking_code,
    b.tour_id,
    b.tour_schedule_id,
    b.start_date,
    b.approval_status,
    (b.adult_count + b.child_count + b.infant_count) as total_participants,
    ts.id as schedule_id_from_db,
    ts.tour_id as schedule_tour_id,
    ts.start_date as schedule_start_date
FROM bookings b
LEFT JOIN tour_schedules ts ON (
    ts.tour_id = b.tour_id 
    AND ts.start_date = b.start_date
)
WHERE b.tour_id = (
    SELECT tour_id FROM tour_schedules WHERE id = 2
)
ORDER BY b.created_at DESC;

-- 9. DEBUG: Kiểm tra xem có bookings nào với tour_id = 1 không (bất kể start_date)
SELECT 
    b.id,
    b.booking_code,
    b.tour_id,
    b.tour_schedule_id,
    b.start_date,
    b.approval_status,
    (b.adult_count + b.child_count + b.infant_count) as total_participants
FROM bookings b
WHERE b.tour_id = (
    SELECT tour_id FROM tour_schedules WHERE id = 2
)
ORDER BY b.start_date DESC, b.created_at DESC;

-- 10. DEBUG: Kiểm tra xem có bookings nào với start_date = '2024-12-27' không (bất kể tour_id)
SELECT 
    b.id,
    b.booking_code,
    b.tour_id,
    b.tour_schedule_id,
    b.start_date,
    b.approval_status,
    (b.adult_count + b.child_count + b.infant_count) as total_participants
FROM bookings b
WHERE b.start_date = (
    SELECT start_date FROM tour_schedules WHERE id = 2
)
ORDER BY b.tour_id, b.created_at DESC;

