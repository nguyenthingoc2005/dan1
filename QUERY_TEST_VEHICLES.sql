-- ==============================================================================
-- QUERY TEST: Kiểm tra xe khả dụng
-- ==============================================================================
-- Chạy các query này để kiểm tra tại sao không có xe khả dụng
-- ==============================================================================

-- 1. Kiểm tra tổng số xe trong database
SELECT COUNT(*) as total_vehicles FROM vehicles;

-- 2. Kiểm tra số xe có status = 'active'
SELECT COUNT(*) as active_vehicles FROM vehicles WHERE status = 'active';

-- 3. Xem tất cả xe active
SELECT id, vehicle_code, license_plate, vehicle_type, capacity, status 
FROM vehicles 
WHERE status = 'active'
ORDER BY capacity ASC;

-- 4. Kiểm tra xe có bị trùng lịch không (thay start_date và end_date)
-- Ví dụ: Tour từ 2024-12-15 đến 2024-12-20
SELECT v.id, v.vehicle_code, v.license_plate, v.capacity,
       va.id as assignment_id, va.start_date, va.end_date, va.status as assignment_status
FROM vehicles v
LEFT JOIN vehicle_assignments va ON v.id = va.vehicle_id
    AND va.status IN ('assigned', 'confirmed', 'in_use')
    AND va.start_date <= '2024-12-20'  -- Thay bằng end_date của tour
    AND va.end_date >= '2024-12-15'    -- Thay bằng start_date của tour
WHERE v.status = 'active'
ORDER BY v.capacity ASC;

-- 5. Query lấy xe khả dụng (giống trong code)
-- Thay :start_date, :end_date, :capacity_min bằng giá trị thực tế
SELECT DISTINCT v.*
FROM vehicles v
LEFT JOIN vehicle_assignments va ON v.id = va.vehicle_id
    AND va.status IN ('assigned', 'confirmed', 'in_use')
    AND va.start_date <= '2024-12-20'  -- Thay bằng end_date
    AND va.end_date >= '2024-12-15'    -- Thay bằng start_date
WHERE v.status = 'active'
  AND v.capacity >= 0  -- Thay bằng capacity_min (có thể thử 0 để xem tất cả)
  AND va.id IS NULL
ORDER BY v.capacity ASC;

-- 6. Kiểm tra vehicle_assignments có dữ liệu không
SELECT COUNT(*) as total_assignments FROM vehicle_assignments;
SELECT * FROM vehicle_assignments ORDER BY created_at DESC LIMIT 10;

-- 7. Kiểm tra vehicle_maintenance (nếu bảng tồn tại)
-- SELECT COUNT(*) FROM vehicle_maintenance;

-- ==============================================================================
-- GHI CHÚ
-- ==============================================================================
-- - Nếu query 3 trả về 0: Không có xe nào có status = 'active'
-- - Nếu query 4 có assignment_id: Xe đang bị trùng lịch
-- - Nếu query 5 trả về 0 nhưng query 3 có kết quả: Có thể do trùng lịch hoặc capacity_min quá lớn
-- ==============================================================================

