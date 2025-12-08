# FLOW: QUẢN LÝ XE VÀ TÀI XẾ (VEHICLE & DRIVER MANAGEMENT)

## 📋 TỔNG QUAN

Tính năng quản lý xe và tài xế:
- Chỉ quản lý xe công ty
- Phụ cấp tài xế tự động tính từ `tour_allowance_rules`
- Tránh trùng lịch

---

## 🔄 WORKFLOW CHI TIẾT

### BƯỚC 1: QUẢN LÝ XE VÀ TÀI XẾ

**Actor:** Admin/Operations

**Hành động:**

#### 1.1. Thêm xe mới
```sql
INSERT INTO vehicles (vehicle_code, vehicle_type, license_plate, capacity, status)
VALUES ('VH001', 'bus_45', '29A-12345', 45, 'active');
```

#### 1.2. Thêm tài xế mới
```sql
INSERT INTO drivers (
    driver_code, full_name, phone, license_number, license_type,
    license_expiry_date, status
)
VALUES ('DRV001', 'Nguyễn Văn A', '0901234567', '123456789', 'D', '2025-12-31', 'active');
```

#### 1.3. Lịch bảo dưỡng xe
```sql
INSERT INTO vehicle_maintenance (
    vehicle_id, maintenance_type, maintenance_date,
    description, cost, status
)
VALUES (?, 'routine', '2024-12-15', 'Bảo dưỡng định kỳ', 2000000, 'scheduled');
```

---

### BƯỚC 2: PHÂN CÔNG XE VÀ TÀI XẾ (TRANG QUẢN LÝ TOUR SCHEDULE)

**Actor:** Admin/Operations Manager

**Thời điểm:** Trong trang quản lý tour schedule (sau khi đã có đủ booking đã thanh toán)

**Hành động:**

#### 2.1. Tính số xe cần
```sql
-- Tính tổng số khách
SELECT SUM(adult_count + child_count + infant_count) AS total_participants
FROM bookings
WHERE tour_schedule_id = ? AND payment_status = 'paid';

-- Gợi ý số xe và loại xe
-- VD: 25 khách → cần 1 xe bus_45 hoặc 2 xe bus_16
```

#### 2.2. Kiểm tra xe có sẵn
```sql
-- Kiểm tra xe không trùng lịch
SELECT v.*
FROM vehicles v
WHERE v.status = 'active'
  AND v.id NOT IN (
      SELECT va.vehicle_id
      FROM vehicle_assignments va
      WHERE va.status IN ('assigned', 'confirmed', 'in_use')
        AND (
            (va.start_date <= ? AND va.end_date >= ?)
            OR (va.start_date <= ? AND va.end_date >= ?)
        )
  )
  AND v.capacity >= ? -- Đủ chỗ cho số khách
ORDER BY v.capacity;
```

#### 2.3. Kiểm tra tài xế có sẵn
```sql
-- Kiểm tra tài xế không trùng lịch và có bằng lái phù hợp
SELECT d.*
FROM drivers d
WHERE d.status = 'active'
  AND d.license_type IN ('D', 'E') -- Bằng lái phù hợp cho xe lớn
  AND d.id NOT IN (
      SELECT ds.driver_id
      FROM driver_schedules ds
      WHERE ds.status IN ('scheduled', 'confirmed', 'in_progress')
        AND ds.schedule_date BETWEEN ? AND ?
  )
ORDER BY d.full_name;
```

#### 2.4. Tự động tính phụ cấp tài xế
```sql
-- Tìm rule phù hợp
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

#### 2.5. Tạo phân công
```sql
-- Tạo vehicle_assignment
INSERT INTO vehicle_assignments (
    tour_schedule_id, vehicle_id, driver_id,
    assignment_date, start_date, end_date,
    driver_salary, -- Tự động từ tour_allowance_rules
    estimated_fuel_cost,
    status, assigned_by
)
VALUES (?, ?, ?, CURDATE(), ?, ?, ?, ?, 'assigned', ?);

-- Tạo driver_schedule (tránh trùng lịch)
INSERT INTO driver_schedules (
    driver_id, tour_schedule_id, vehicle_assignment_id,
    schedule_date, start_time, end_time,
    status
)
VALUES (?, ?, ?, ?, ?, ?, 'scheduled');
```

**Dữ liệu tạo:**
- `vehicle_assignments` (status: assigned)
- `driver_schedules` (status: scheduled)

---

### BƯỚC 3: XÁC NHẬN PHÂN CÔNG

**Actor:** Tài xế hoặc Admin

**Hành động:**
1. Tài xế xác nhận nhận việc
   ```sql
   UPDATE vehicle_assignments
   SET 
       status = 'confirmed',
       confirmed_by = ?,
       confirmed_at = NOW()
   WHERE id = ?;
   ```

2. Cập nhật driver_schedule
   ```sql
   UPDATE driver_schedules
   SET status = 'confirmed'
   WHERE vehicle_assignment_id = ?;
   ```

---

### BƯỚC 4: ĐỔI XE/TÀI XẾ (NẾU CẦN)

**Actor:** Admin/Operations

**Hành động:**
1. Xem phân công hiện tại
   ```sql
   SELECT 
       va.id,
       v.vehicle_code,
       v.vehicle_type,
       d.full_name AS driver_name,
       d.license_type
   FROM vehicle_assignments va
   JOIN vehicles v ON va.vehicle_id = v.id
   JOIN drivers d ON va.driver_id = d.id
   WHERE va.tour_schedule_id = ?;
   ```

2. Chọn "Đổi xe" hoặc "Đổi tài xế"
3. Chọn xe/tài xế mới từ danh sách có sẵn
4. Cập nhật
   ```sql
   -- Ghi log
   INSERT INTO vehicle_assignment_history (
       vehicle_assignment_id, action, old_values, new_values, changed_by, reason
   )
   VALUES (?, 'driver_changed', ?, ?, ?, ?);
   
   -- Cập nhật
   UPDATE vehicle_assignments
   SET driver_id = ?
   WHERE id = ?;
   ```

**Dữ liệu tạo:**
- `vehicle_assignment_history` (log thay đổi)

---

### BƯỚC 5: TRONG QUÁ TRÌNH TOUR

**Actor:** HDV, Operations

**Hành động:**
1. Cập nhật thông tin thực tế
   ```sql
   UPDATE vehicle_assignments
   SET 
       actual_distance = ?,
       actual_fuel_cost = ?,
       status = 'in_use'
   WHERE id = ?;
   ```

2. Ghi nhận vấn đề (nếu có)
   - Ghi vào `notes` hoặc `journals.issues`

---

### BƯỚC 6: KẾT THÚC TOUR

**Actor:** HDV, Operations

**Hành động:**
1. Cập nhật trạng thái
   ```sql
   UPDATE vehicle_assignments
   SET status = 'completed'
   WHERE tour_schedule_id = ?;
   ```

2. Cập nhật driver_schedule
   ```sql
   UPDATE driver_schedules
   SET status = 'completed'
   WHERE tour_schedule_id = ?;
   ```

3. Cập nhật số km xe (nếu cần)
   ```sql
   UPDATE vehicles
   SET ... -- Cập nhật số km nếu có field mileage
   WHERE id = ?;
   ```

---

## 📊 QUERY HỮU ÍCH

### Kiểm tra xe có sẵn trong khoảng thời gian
```sql
SELECT v.*
FROM vehicles v
WHERE v.status = 'active'
  AND v.id NOT IN (
      SELECT va.vehicle_id
      FROM vehicle_assignments va
      WHERE va.status IN ('assigned', 'confirmed', 'in_use')
        AND (
            (va.start_date <= ? AND va.end_date >= ?)
            OR (va.start_date <= ? AND va.end_date >= ?)
        )
  )
  AND v.capacity >= ?
ORDER BY v.capacity;
```

### Kiểm tra tài xế có sẵn
```sql
SELECT d.*
FROM drivers d
WHERE d.status = 'active'
  AND d.license_type IN (?) -- Bằng lái phù hợp
  AND d.id NOT IN (
      SELECT ds.driver_id
      FROM driver_schedules ds
      WHERE ds.status IN ('scheduled', 'confirmed', 'in_progress')
        AND ds.schedule_date BETWEEN ? AND ?
  );
```

### Xem phân công xe/tài xế của tour schedule
```sql
SELECT 
    va.id,
    v.vehicle_code,
    v.vehicle_type,
    v.capacity,
    d.full_name AS driver_name,
    d.phone AS driver_phone,
    d.license_type,
    va.driver_salary,
    va.estimated_fuel_cost,
    va.status
FROM vehicle_assignments va
JOIN vehicles v ON va.vehicle_id = v.id
JOIN drivers d ON va.driver_id = d.id
WHERE va.tour_schedule_id = ?;
```

---

## ⚠️ BUSINESS RULES

1. **Xe phải có sẵn: không trùng lịch với tour khác hoặc bảo dưỡng**
2. **Tài xế phải có sẵn: không trùng lịch**
3. **Bằng lái phù hợp: tài xế phải có bằng lái phù hợp loại xe**
   - Xe lớn (bus_45, bus_29): cần bằng D hoặc E
   - Xe nhỏ (car_7, car_4): cần bằng B1 hoặc B2
4. **Số chỗ: xe phải đủ chỗ cho số khách (có thể thêm buffer)**
5. **Thay đổi: ghi log vào vehicle_assignment_history**
6. **Chi phí: tự động tính từ phụ cấp tài xế + chi phí nhiên liệu**

---

## 🔄 TRƯỜNG HỢP ĐẶC BIỆT

### Xe bị hỏng trong tour
1. Ghi nhận vào `journals.issues`
2. Tìm xe thay thế (nếu có)
3. Cập nhật `vehicle_assignments`

### Tài xế bị ốm
1. Tìm tài xế thay thế
2. Cập nhật `vehicle_assignments.driver_id`
3. Ghi log vào `vehicle_assignment_history`

### Bảo dưỡng xe đột xuất
1. Tạo `vehicle_maintenance` (type: emergency)
2. Cập nhật `vehicles.status = 'maintenance'`
3. Xe không thể dùng cho tour trong thời gian bảo dưỡng

