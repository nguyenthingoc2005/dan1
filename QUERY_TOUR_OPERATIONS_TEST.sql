-- ==============================================================================
-- QUERY TEST: LẤY DANH SÁCH TOUR ĐÃ CHỐT
-- ==============================================================================
-- Điều kiện: Đủ số người tối thiểu (>= min_participants)
-- Thao tác chỉ được phép khi: tour đóng HOẶC đã qua deadline booking
-- ==============================================================================

-- QUERY CHÍNH: Lấy danh sách tour đã đủ số người
SELECT 
    ts.id,
    ts.start_date,
    ts.end_date,
    ts.status,
    t.tour_code,
    t.name AS tour_name,
    t.min_participants,
    t.booking_deadline_days,
    DATE_SUB(ts.start_date, INTERVAL t.booking_deadline_days DAY) AS booking_deadline_date,
    CURDATE() AS today,
    COUNT(DISTINCT b.id) AS total_paid_bookings,
    SUM(b.adult_count + b.child_count + b.infant_count) AS total_paid_participants,
    SUM(b.final_amount) AS total_revenue,
    ts.guide_id,
    u.full_name AS guide_name,
    (SELECT COUNT(*) FROM vehicle_assignments WHERE tour_schedule_id = ts.id) AS vehicle_count,
    (SELECT COUNT(*) FROM room_assignments WHERE tour_schedule_id = ts.id) AS room_assignment_count,
    -- Kiểm tra có thể thao tác không
    CASE 
        WHEN ts.status = 'closed' THEN 'CLOSED'
        WHEN CURDATE() >= DATE_SUB(ts.start_date, INTERVAL t.booking_deadline_days DAY)
        THEN 'PASSED_DEADLINE'
        ELSE 'NOT_READY'
    END AS can_operate_status,
    -- Trạng thái operations
    CASE 
        WHEN ts.guide_id IS NOT NULL 
            AND EXISTS (SELECT 1 FROM vehicle_assignments WHERE tour_schedule_id = ts.id)
            AND EXISTS (SELECT 1 FROM room_assignments WHERE tour_schedule_id = ts.id)
        THEN 'ready'
        WHEN ts.guide_id IS NULL 
            AND NOT EXISTS (SELECT 1 FROM vehicle_assignments WHERE tour_schedule_id = ts.id)
            AND NOT EXISTS (SELECT 1 FROM room_assignments WHERE tour_schedule_id = ts.id)
        THEN 'not_started'
        ELSE 'in_progress'
    END AS operations_status
FROM tour_schedules ts
JOIN tours t ON ts.tour_id = t.id
LEFT JOIN bookings b ON ts.id = b.tour_schedule_id 
    AND b.payment_status = 'paid'
LEFT JOIN users u ON ts.guide_id = u.id
WHERE 1=1  -- Có thể thêm filter ở đây
GROUP BY ts.id
HAVING SUM(b.adult_count + b.child_count + b.infant_count) >= t.min_participants
ORDER BY ts.start_date ASC;

-- ==============================================================================
-- QUERY KIỂM TRA ĐIỀU KIỆN THAO TÁC (cho 1 tour cụ thể)
-- ==============================================================================
-- Thay :schedule_id bằng ID tour schedule bạn muốn test
-- ==============================================================================

SELECT 
    ts.id,
    ts.status,
    t.booking_deadline_days,
    DATE_SUB(ts.start_date, INTERVAL t.booking_deadline_days DAY) AS booking_deadline_date,
    CURDATE() AS today,
    CASE 
        WHEN ts.status = 'closed' THEN 'CLOSED'
        WHEN CURDATE() >= DATE_SUB(ts.start_date, INTERVAL t.booking_deadline_days DAY)
        THEN 'PASSED_DEADLINE'
        ELSE 'NOT_READY'
    END AS operations_status,
    SUM(b.adult_count + b.child_count + b.infant_count) AS total_paid_participants,
    t.min_participants,
    CASE 
        WHEN SUM(b.adult_count + b.child_count + b.infant_count) >= t.min_participants
        THEN 'SUFFICIENT'
        ELSE 'INSUFFICIENT'
    END AS participant_status,
    -- Kết luận: Có thể thao tác không?
    CASE 
        WHEN (ts.status = 'closed' OR CURDATE() >= DATE_SUB(ts.start_date, INTERVAL t.booking_deadline_days DAY))
            AND SUM(b.adult_count + b.child_count + b.infant_count) >= t.min_participants
        THEN 'YES - Có thể thao tác'
        ELSE 'NO - Chưa thể thao tác'
    END AS can_operate
FROM tour_schedules ts
JOIN tours t ON ts.tour_id = t.id
LEFT JOIN bookings b ON ts.id = b.tour_schedule_id 
    AND b.payment_status = 'paid'
WHERE ts.id = :schedule_id  -- Thay bằng ID cụ thể, VD: 1
GROUP BY ts.id;

-- ==============================================================================
-- QUERY TEST VỚI FILTER (ví dụ)
-- ==============================================================================

-- Lọc theo tour
SELECT 
    ts.id,
    ts.start_date,
    t.tour_code,
    t.name AS tour_name,
    COUNT(DISTINCT b.id) AS total_paid_bookings,
    SUM(b.adult_count + b.child_count + b.infant_count) AS total_paid_participants,
    t.min_participants,
    ts.status,
    CASE 
        WHEN ts.status = 'closed' THEN 'CLOSED'
        WHEN CURDATE() >= DATE_SUB(ts.start_date, INTERVAL t.booking_deadline_days DAY)
        THEN 'PASSED_DEADLINE'
        ELSE 'NOT_READY'
    END AS can_operate_status
FROM tour_schedules ts
JOIN tours t ON ts.tour_id = t.id
LEFT JOIN bookings b ON ts.id = b.tour_schedule_id 
    AND b.payment_status = 'paid'
WHERE ts.tour_id = 1  -- Thay bằng tour_id cụ thể
GROUP BY ts.id
HAVING SUM(b.adult_count + b.child_count + b.infant_count) >= t.min_participants
ORDER BY ts.start_date ASC;

-- Lọc theo ngày
SELECT 
    ts.id,
    ts.start_date,
    t.tour_code,
    t.name AS tour_name,
    COUNT(DISTINCT b.id) AS total_paid_bookings,
    SUM(b.adult_count + b.child_count + b.infant_count) AS total_paid_participants,
    t.min_participants
FROM tour_schedules ts
JOIN tours t ON ts.tour_id = t.id
LEFT JOIN bookings b ON ts.id = b.tour_schedule_id 
    AND b.payment_status = 'paid'
WHERE ts.start_date >= '2024-01-01'  -- Từ ngày
  AND ts.start_date <= '2024-12-31'  -- Đến ngày
GROUP BY ts.id
HAVING SUM(b.adult_count + b.child_count + b.infant_count) >= t.min_participants
ORDER BY ts.start_date ASC;

-- ==============================================================================
-- QUERY KIỂM TRA CHI TIẾT 1 TOUR
-- ==============================================================================

SELECT 
    ts.id AS schedule_id,
    ts.start_date,
    ts.end_date,
    ts.status AS schedule_status,
    t.tour_code,
    t.name AS tour_name,
    t.duration_days,
    t.duration_nights,
    t.min_participants,
    t.max_participants,
    t.booking_deadline_days,
    DATE_SUB(ts.start_date, INTERVAL t.booking_deadline_days DAY) AS booking_deadline_date,
    CURDATE() AS today,
    -- Thông tin booking
    COUNT(DISTINCT b.id) AS total_paid_bookings,
    SUM(b.adult_count + b.child_count + b.infant_count) AS total_paid_participants,
    SUM(b.final_amount) AS total_revenue,
    SUM(b.paid_amount) AS total_collected,
    -- HDV
    ts.guide_id,
    u.full_name AS guide_name,
    u.phone AS guide_phone,
    -- Xe
    (SELECT COUNT(*) FROM vehicle_assignments WHERE tour_schedule_id = ts.id) AS vehicle_count,
    -- Phân phòng
    (SELECT COUNT(*) FROM room_assignments WHERE tour_schedule_id = ts.id) AS room_assignment_count,
    -- Kiểm tra điều kiện
    CASE 
        WHEN SUM(b.adult_count + b.child_count + b.infant_count) >= t.min_participants 
        THEN 'SUFFICIENT' 
        ELSE 'INSUFFICIENT' 
    END AS participant_status,
    CASE 
        WHEN ts.status = 'closed' THEN 'CLOSED'
        WHEN CURDATE() >= DATE_SUB(ts.start_date, INTERVAL t.booking_deadline_days DAY)
        THEN 'PASSED_DEADLINE'
        ELSE 'NOT_READY'
    END AS can_operate_status
FROM tour_schedules ts
JOIN tours t ON ts.tour_id = t.id
LEFT JOIN bookings b ON ts.id = b.tour_schedule_id 
    AND b.payment_status = 'paid'
LEFT JOIN users u ON ts.guide_id = u.id
WHERE ts.id = 1  -- Thay bằng schedule_id cụ thể
GROUP BY ts.id;

