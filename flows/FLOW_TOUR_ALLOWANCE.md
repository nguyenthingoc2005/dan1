# FLOW: TÍNH PHỤ CẤP TỰ ĐỘNG (TOUR ALLOWANCE)

## 📋 TỔNG QUAN

Tính năng tự động tính phụ cấp HDV và tài xế:
- Dựa vào quy mô tour (tour_type, duration_days, số khách)
- Tự động điền vào `tour_assignments.salary_amount` và `vehicle_assignments.driver_salary`

---

## 🔄 WORKFLOW CHI TIẾT

### BƯỚC 1: THIẾT LẬP QUY TẮC

**Actor:** Admin/Manager

**Hành động:**
1. Tạo quy tắc phụ cấp
   ```sql
   INSERT INTO tour_allowance_rules (
       rule_name, tour_type, duration_days_min, duration_days_max,
       participant_min, participant_max,
       guide_allowance, driver_allowance, priority, status
   )
   VALUES (
       'Tour public 1-3 ngày, 15-20 khách',
       'public', 1, 3, 15, 20,
       1000000, 500000, 10, 'active'
   );
   ```

**Ví dụ quy tắc:**
- Tour public 1-3 ngày, 15-20 khách: HDV 1.000.000, Tài xế 500.000
- Tour public 1-3 ngày, 21-30 khách: HDV 1.500.000, Tài xế 800.000
- Tour public 4-7 ngày, 15-20 khách: HDV 2.000.000, Tài xế 1.000.000
- Tour custom: HDV 3.000.000, Tài xế 1.500.000

---

### BƯỚC 2: TỰ ĐỘNG TÍNH PHỤ CẤP (KHI GÁN HDV/TÀI XẾ)

**Actor:** System (Tự động)

**Thời điểm:** Khi gán HDV hoặc tài xế cho tour schedule

**Hành động:**

#### 2.1. Lấy thông tin tour
```sql
SELECT 
    t.tour_type,
    t.duration_days,
    SUM(b.adult_count + b.child_count) AS total_participants
FROM tours t
JOIN tour_schedules ts ON t.id = ts.tour_id
LEFT JOIN bookings b ON ts.id = b.tour_schedule_id AND b.payment_status = 'paid'
WHERE ts.id = ?
GROUP BY t.id;
```

#### 2.2. Tìm rule phù hợp
```sql
SELECT 
    guide_allowance,
    driver_allowance
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

#### 2.3. Tự động điền phụ cấp
```sql
-- Khi gán HDV
INSERT INTO tour_assignments (
    tour_schedule_id, guide_id, assignment_date,
    salary_amount, -- Tự động từ tour_allowance_rules
    status, created_by
)
VALUES (?, ?, CURDATE(), ?, 'assigned', ?);

-- Khi gán tài xế
INSERT INTO vehicle_assignments (
    tour_schedule_id, vehicle_id, driver_id,
    driver_salary, -- Tự động từ tour_allowance_rules
    ...
)
VALUES (?, ?, ?, ?, ...);
```

---

## 📊 QUERY HỮU ÍCH

### Xem tất cả quy tắc
```sql
SELECT 
    id, rule_name, tour_type, 
    duration_days_min, duration_days_max,
    participant_min, participant_max,
    guide_allowance, driver_allowance,
    priority, status
FROM tour_allowance_rules
WHERE status = 'active'
ORDER BY priority DESC, tour_type;
```

### Test rule cho một tour
```sql
-- Giả sử tour: public, 3 ngày, 25 khách
SELECT 
    rule_name,
    guide_allowance,
    driver_allowance
FROM tour_allowance_rules
WHERE tour_type = 'public'
  AND (duration_days_min IS NULL OR 3 >= duration_days_min)
  AND (duration_days_max IS NULL OR 3 <= duration_days_max)
  AND (participant_min IS NULL OR 25 >= participant_min)
  AND (participant_max IS NULL OR 25 <= participant_max)
  AND status = 'active'
ORDER BY priority DESC
LIMIT 1;
```

---

## ⚠️ BUSINESS RULES

1. **Priority: rule có priority cao hơn được ưu tiên khi có nhiều rule khớp**
2. **NULL values: nếu NULL thì không giới hạn (match tất cả)**
3. **Tự động: phụ cấp tự động tính khi gán HDV/tài xế**
4. **Có thể chỉnh sửa: sau khi tự động điền, có thể chỉnh sửa thủ công nếu cần**

---

## 🔄 TRƯỜNG HỢP ĐẶC BIỆT

### Không tìm thấy rule phù hợp
- Sử dụng rule có priority thấp nhất (fallback)
- Hoặc cảnh báo admin thiết lập rule mới

### Nhiều rule khớp
- Chọn rule có priority cao nhất
- Nếu priority bằng nhau → chọn rule đầu tiên

