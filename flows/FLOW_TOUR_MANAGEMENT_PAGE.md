# FLOW: TRANG QUẢN LÝ TOUR SCHEDULE (TOUR MANAGEMENT PAGE)

## 📋 TỔNG QUAN

Trang quản lý tour schedule đã chốt (có đủ booking đã thanh toán):
- Hiển thị thông tin tổng hợp
- Danh sách booking đã chốt
- Danh sách khách hàng
- Gán xe và tài xế
- Gán HDV
- Tính toán tài chính dự kiến
- Đổi HDV/tài xế nếu cần

**Thời điểm:** Sau khi đã phân phòng, trước ngày khởi hành

---

## 🔄 WORKFLOW CHI TIẾT

### BƯỚC 1: TRUY CẬP TRANG QUẢN LÝ TOUR SCHEDULE

**Actor:** Admin/Operations Manager

**Điều kiện:**
- Tour schedule đã có đủ booking đã thanh toán (`payment_status = 'paid'`)
- Thường là trước ngày khởi hành 1-3 ngày

**Hành động:**
1. Chọn tour schedule từ danh sách
2. Mở trang quản lý tour schedule

---

### BƯỚC 2: XEM THÔNG TIN TỔNG HỢP

**Actor:** Admin/Operations Manager

**Hiển thị:**

#### 2.1. Thông tin tour
```sql
SELECT 
    ts.id,
    ts.start_date,
    ts.end_date,
    t.name AS tour_name,
    t.tour_code,
    t.duration_days,
    t.duration_nights,
    t.min_participants,
    t.max_participants,
    ts.status
FROM tour_schedules ts
JOIN tours t ON ts.tour_id = t.id
WHERE ts.id = ?;
```

#### 2.2. Số người và doanh thu
```sql
SELECT 
    COUNT(DISTINCT b.id) AS total_bookings,
    SUM(b.adult_count + b.child_count + b.infant_count) AS total_participants,
    SUM(b.final_amount) AS total_revenue,
    SUM(b.paid_amount) AS total_collected
FROM bookings b
WHERE b.tour_schedule_id = ?
  AND b.payment_status = 'paid';
```

#### 2.3. Kiểm tra min_participants
```sql
-- Nếu total_participants < min_participants → Cảnh báo
SELECT 
    CASE 
        WHEN SUM(b.adult_count + b.child_count) < t.min_participants 
        THEN 'INSUFFICIENT' 
        ELSE 'SUFFICIENT' 
    END AS participant_status
FROM bookings b
JOIN tours t ON b.tour_id = t.id
WHERE b.tour_schedule_id = ?
  AND b.payment_status = 'paid';
```

---

### BƯỚC 3: XEM DANH SÁCH BOOKING ĐÃ CHỐT

**Actor:** Admin/Operations Manager

**Query:**
```sql
SELECT 
    b.id,
    b.booking_code,
    c.full_name AS customer_name,
    c.phone AS customer_phone,
    b.adult_count,
    b.child_count,
    b.infant_count,
    b.final_amount,
    b.paid_amount,
    b.payment_status,
    b.created_at AS booking_date
FROM bookings b
JOIN customers c ON b.customer_id = c.id
WHERE b.tour_schedule_id = ? 
  AND b.payment_status = 'paid'
ORDER BY b.created_at;
```

---

### BƯỚC 4: XEM DANH SÁCH KHÁCH HÀNG

**Actor:** Admin/Operations Manager

**Query:**
```sql
SELECT 
    bc.id,
    c.full_name,
    c.gender,
    c.date_of_birth,
    bc.age_type,
    b.booking_code,
    ra.room_number,
    ra.id AS room_assignment_id
FROM booking_customers bc
JOIN customers c ON bc.customer_id = c.id
JOIN bookings b ON bc.booking_id = b.id
LEFT JOIN room_assignment_customers rac ON bc.id = rac.booking_customer_id
LEFT JOIN room_assignments ra ON rac.room_assignment_id = ra.id
WHERE b.tour_schedule_id = ?
  AND b.payment_status = 'paid'
ORDER BY b.booking_code, bc.age_type;
```

---

### BƯỚC 5: GÁN XE VÀ TÀI XẾ

**Actor:** Admin/Operations Manager

**Hành động:**

#### 5.1. Tính số xe cần
```sql
-- Tính tổng số khách
SELECT SUM(adult_count + child_count + infant_count) AS total_participants
FROM bookings
WHERE tour_schedule_id = ? AND payment_status = 'paid';

-- Gợi ý số xe
-- VD: 25 khách → cần 1 xe bus_45 hoặc 2 xe bus_16
```

#### 5.2. Chọn xe
- Hiển thị danh sách xe có sẵn (không trùng lịch)
- Chọn xe phù hợp

#### 5.3. Chọn tài xế
- Hiển thị danh sách tài xế có sẵn (không trùng lịch, bằng lái phù hợp)
- Chọn tài xế cho từng xe

#### 5.4. Tự động tính phụ cấp tài xế
```sql
-- Tự động tính từ tour_allowance_rules
SELECT driver_allowance
FROM tour_allowance_rules
WHERE tour_type = ?
  AND (duration_days_min IS NULL OR ? >= duration_days_min)
  AND (duration_days_max IS NULL OR ? <= duration_days_max)
  AND (participant_min IS NULL OR ? >= participant_min)
  AND (participant_max IS NULL OR ? <= participant_max)
  AND status = 'active'
ORDER BY priority DESC
LIMIT 1;
```

#### 5.5. Lưu phân công
```sql
INSERT INTO vehicle_assignments (
    tour_schedule_id, vehicle_id, driver_id,
    assignment_date, start_date, end_date,
    driver_salary, -- Tự động từ tour_allowance_rules
    estimated_fuel_cost,
    status, assigned_by
)
VALUES (?, ?, ?, CURDATE(), ?, ?, ?, ?, 'assigned', ?);

INSERT INTO driver_schedules (
    driver_id, tour_schedule_id, vehicle_assignment_id,
    schedule_date, status
)
VALUES (?, ?, ?, ?, 'scheduled');
```

---

### BƯỚC 6: GÁN HDV

**Actor:** Admin/Operations Manager

**Hành động:**

#### 6.1. Xem HDV hiện tại (nếu có)
```sql
SELECT 
    u.id,
    u.full_name,
    u.phone,
    ta.salary_amount AS guide_allowance
FROM tour_schedules ts
LEFT JOIN users u ON ts.guide_id = u.id
LEFT JOIN tour_assignments ta ON ts.id = ta.tour_schedule_id
WHERE ts.id = ?;
```

#### 6.2. Chọn HDV mới
- Hiển thị danh sách HDV có sẵn (không trùng lịch)
- Chọn HDV

#### 6.3. Tự động tính phụ cấp HDV
```sql
-- Tự động tính từ tour_allowance_rules
SELECT guide_allowance
FROM tour_allowance_rules
WHERE tour_type = ?
  AND (duration_days_min IS NULL OR ? >= duration_days_min)
  AND (duration_days_max IS NULL OR ? <= duration_days_max)
  AND (participant_min IS NULL OR ? >= participant_min)
  AND (participant_max IS NULL OR ? <= participant_max)
  AND status = 'active'
ORDER BY priority DESC
LIMIT 1;
```

#### 6.4. Lưu phân công
```sql
-- Cập nhật tour_schedules
UPDATE tour_schedules
SET guide_id = ?
WHERE id = ?;

-- Tạo tour_assignments
INSERT INTO tour_assignments (
    tour_schedule_id, guide_id, assignment_date,
    salary_amount, -- Tự động từ tour_allowance_rules
    status, created_by
)
VALUES (?, ?, CURDATE(), ?, 'assigned', ?);
```

---

### BƯỚC 7: ĐỔI HDV/TÀI XẾ

**Actor:** Admin/Operations Manager

**Hành động:**
1. Xem HDV/tài xế hiện tại
2. Chọn "Đổi HDV" hoặc "Đổi tài xế"
3. Chọn người thay thế từ danh sách có sẵn
4. Lưu → cập nhật và ghi log

**Đổi HDV:**
```sql
-- Ghi log
INSERT INTO schedule_guide_history (
    schedule_id, old_guide_id, new_guide_id, changed_by, reason
)
VALUES (?, ?, ?, ?, ?);

-- Cập nhật
UPDATE tour_schedules SET guide_id = ? WHERE id = ?;
UPDATE tour_assignments SET guide_id = ? WHERE tour_schedule_id = ?;
```

**Đổi tài xế:**
```sql
-- Ghi log
INSERT INTO vehicle_assignment_history (
    vehicle_assignment_id, action, old_values, new_values, changed_by, reason
)
VALUES (?, 'driver_changed', ?, ?, ?, ?);

-- Cập nhật
UPDATE vehicle_assignments SET driver_id = ? WHERE id = ?;
UPDATE driver_schedules SET driver_id = ? WHERE vehicle_assignment_id = ?;
```

---

### BƯỚC 8: TÍNH TOÁN TÀI CHÍNH DỰ KIẾN

**Actor:** Admin/Operations Manager

**Query:**
```sql
SELECT 
    -- Doanh thu
    SUM(b.final_amount) AS total_revenue,
    
    -- Chi phí dịch vụ (ước tính)
    (SELECT SUM(ids.unit_price * ids.quantity * ?) -- số khách
     FROM itinerary_day_services ids
     JOIN itineraries i ON ids.itinerary_id = i.id
     WHERE i.tour_id = ?) AS estimated_service_cost,
    
    -- Chi phí xe
    (SELECT SUM(va.driver_salary + va.estimated_fuel_cost)
     FROM vehicle_assignments va
     WHERE va.tour_schedule_id = ?) AS vehicle_cost,
    
    -- Chi phí HDV
    (SELECT ta.salary_amount
     FROM tour_assignments ta
     WHERE ta.tour_schedule_id = ?
     LIMIT 1) AS guide_cost,
    
    -- Chi phí cố định
    t.fixed_cost_total AS fixed_cost,
    
    -- Lợi nhuận dự kiến
    SUM(b.final_amount) - 
    (SELECT SUM(ids.unit_price * ids.quantity * ?) ...) -
    (SELECT SUM(va.driver_salary + va.estimated_fuel_cost) ...) -
    (SELECT ta.salary_amount ...) -
    t.fixed_cost_total AS estimated_profit
    
FROM tour_schedules ts
JOIN tours t ON ts.tour_id = t.id
LEFT JOIN bookings b ON ts.id = b.tour_schedule_id AND b.payment_status = 'paid'
WHERE ts.id = ?
GROUP BY ts.id;
```

---

### BƯỚC 9: XÁC NHẬN TOUR SCHEDULE

**Actor:** Admin/Operations Manager

**Hành động:**
1. Kiểm tra:
   - ✅ Đã gán HDV
   - ✅ Đã gán xe và tài xế
   - ✅ Đã phân phòng
   - ✅ Đủ min_participants

2. Xác nhận
   ```sql
   UPDATE tour_schedules
   SET status = 'confirmed'
   WHERE id = ?;
   ```

3. Gửi thông báo
   - Email/SMS cho HDV
   - Email/SMS cho tài xế
   - Email/SMS cho khách hàng

---

## 📊 QUERY TỔNG HỢP

### Thông tin đầy đủ tour schedule
```sql
SELECT 
    ts.id,
    ts.start_date,
    ts.end_date,
    t.name AS tour_name,
    t.tour_code,
    t.duration_days,
    t.duration_nights,
    t.min_participants,
    t.max_participants,
    COUNT(DISTINCT b.id) AS total_bookings,
    SUM(b.adult_count + b.child_count + b.infant_count) AS total_participants,
    SUM(b.final_amount) AS total_revenue,
    ts.status,
    u.full_name AS guide_name,
    (SELECT COUNT(*) FROM vehicle_assignments WHERE tour_schedule_id = ts.id) AS vehicle_count
FROM tour_schedules ts
JOIN tours t ON ts.tour_id = t.id
LEFT JOIN bookings b ON ts.id = b.tour_schedule_id AND b.payment_status = 'paid'
LEFT JOIN users u ON ts.guide_id = u.id
WHERE ts.id = ?
GROUP BY ts.id;
```

---

## ⚠️ BUSINESS RULES

1. **Chỉ cho truy cập khi có booking đã thanh toán đủ**
2. **Phải đủ min_participants mới được xác nhận**
3. **Phải gán HDV và xe/tài xế trước khi xác nhận**
4. **Phụ cấp tự động tính từ tour_allowance_rules**

