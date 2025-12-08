# KẾ HOẠCH MODULE: QUẢN LÝ TOUR ĐÃ CHỐT (TOUR OPERATIONS MANAGEMENT)

## 📋 TỔNG QUAN

Module này tạo một trang quản lý tập trung cho các tour schedule đã có đủ booking đã thanh toán, cho phép:

- Xem danh sách tour đã chốt (có đủ số người đã booking và thanh toán)
- Gán hướng dẫn viên
- Phân công xe và tài xế
- Phân phòng
- Xem tổng hợp thông tin tour

**Thời điểm sử dụng:**

- Sau khi đã qua deadline booking (không còn đặt được nữa)
- Và đã có đủ booking đã thanh toán (>= min_participants)

**Logic deadline:**

- `deadline_date = tour_schedules.start_date - tours.booking_deadline_days`
- `today >= deadline_date` → Đã qua deadline, không còn đặt được
- Ví dụ: Tour khởi hành 10/01, `booking_deadline_days = 3` → Deadline: 07/01
  - Trước 07/01: Có thể đặt booking
  - Từ 07/01 trở đi: Không thể đặt booking nữa → Hiển thị trong module này

---

## 🎯 MỤC TIÊU

1. **Trang danh sách tour đã chốt:**

   - Hiển thị các tour schedule có đủ booking đã thanh toán (`payment_status = 'paid'`)
   - Hiển thị số người đã booking, số người đã thanh toán
   - Hiển thị trạng thái: đã gán HDV chưa, đã gán xe chưa, đã phân phòng chưa
   - Filter theo ngày, tour, trạng thái

2. **Trang chi tiết tour:**
   - Thông tin tổng hợp tour
   - Danh sách booking đã thanh toán
   - Danh sách khách hàng
   - Gán hướng dẫn viên
   - Phân công xe và tài xế
   - Phân phòng
   - Tính toán tài chính dự kiến

---

## 📊 PHÂN TÍCH DATABASE

### Các bảng đã có sẵn:

1. **`tour_schedules`** - Lịch tour

   - `id`, `tour_id`, `start_date`, `end_date`
   - `status`: `open`, `closed`, `pending`, `confirmed`, `in_progress`, `completed`, `cancelled`
   - `guide_id`: HDV được gán
   - `booked`: Số chỗ đã đặt

2. **`bookings`** - Booking

   - `tour_schedule_id`: Link với tour schedule
   - `payment_status`: `unpaid`, `partial`, `paid`, `rejected`, `cancelled`, `refunded`
   - `adult_count`, `child_count`, `infant_count`

3. **`tour_assignments`** - Gán HDV

   - `tour_schedule_id`: Bắt buộc
   - `guide_id`: HDV
   - `salary_amount`: Phụ cấp (tự động từ `tour_allowance_rules`)

4. **`vehicles`** - Xe

   - `id`, `vehicle_code`, `vehicle_type`, `license_plate`, `capacity`, `status`

5. **`drivers`** - Tài xế

   - `id`, `driver_code`, `full_name`, `phone`, `license_type`, `status`

6. **`vehicle_assignments`** - Phân công xe và tài xế

   - `tour_schedule_id`: Bắt buộc
   - `vehicle_id`, `driver_id`
   - `driver_salary`: Phụ cấp tài xế (tự động từ `tour_allowance_rules`)
   - `status`: `assigned`, `confirmed`, `in_use`, `completed`, `cancelled`

7. **`driver_schedules`** - Lịch tài xế (tránh trùng lịch)

   - `driver_id`, `tour_schedule_id`, `vehicle_assignment_id`
   - `schedule_date`, `status`

8. **`room_assignments`** - Phân phòng

   - `tour_schedule_id`: Bắt buộc
   - `itinerary_id`: Đêm nào
   - `status`: `pending`, `assigned`, `confirmed`, `cancelled`

9. **`tour_allowance_rules`** - Quy tắc phụ cấp
   - `guide_allowance`: Phụ cấp HDV
   - `driver_allowance`: Phụ cấp tài xế
   - Tự động tính dựa trên: `tour_type`, `duration_days`, `participant_count`

---

## 🔍 ĐIỀU KIỆN "ĐỦ SỐ NGƯỜI ĐÃ BOOKING VÀ THANH TOÁN"

### Query kiểm tra:

```sql
-- Tính số người đã thanh toán
SELECT
    ts.id AS schedule_id,
    COUNT(DISTINCT b.id) AS total_paid_bookings,
    SUM(b.adult_count + b.child_count + b.infant_count) AS total_paid_participants,
    t.min_participants,
    CASE
        WHEN SUM(b.adult_count + b.child_count + b.infant_count) >= t.min_participants
        THEN 'SUFFICIENT'
        ELSE 'INSUFFICIENT'
    END AS participant_status
FROM tour_schedules ts
JOIN tours t ON ts.tour_id = t.id
LEFT JOIN bookings b ON ts.id = b.tour_schedule_id
    AND b.payment_status = 'paid'
WHERE ts.status IN ('open', 'closed', 'pending', 'confirmed')
GROUP BY ts.id
HAVING total_paid_participants >= t.min_participants;
```

### Điều kiện hiển thị trong danh sách:

- ✅ **Đã qua deadline booking:** `CURDATE() >= DATE_SUB(tour_schedules.start_date, INTERVAL tours.booking_deadline_days DAY)`
  - Nghĩa là: Không còn đặt được booking nữa (đã chốt danh sách)
- ✅ Có ít nhất 1 booking với `payment_status = 'paid'`
- ✅ Tổng số người đã thanh toán >= `tours.min_participants`
- ✅ `tour_schedules.status` IN (`open`, `closed`, `pending`, `confirmed`)

---

## 🏗️ KIẾN TRÚC MODULE

### 1. CONTROLLER: `TourOperationsController.php`

**Location:** `app/controllers/admin/TourOperationsController.php`

**Methods:**

- `index()` - Danh sách tour đã chốt
- `show($id)` - Chi tiết tour (gán HDV, xe, phân phòng)
- `assignGuide()` - Gán HDV
- `assignVehicle()` - Phân công xe và tài xế
- `assignRoom()` - Phân phòng (redirect đến room assignment)
- `updateStatus()` - Cập nhật trạng thái tour schedule

### 2. MODEL: `TourOperations.php` (hoặc mở rộng `TourSchedule.php`)

**Location:** `app/models/TourOperations.php` (mới) hoặc mở rộng `TourSchedule.php`

**Methods:**

- `getReadyForOperations($filters)` - Lấy danh sách tour đã chốt
- `getTourOperationsSummary($schedule_id)` - Tổng hợp thông tin tour
- `checkReadyForOperations($schedule_id)` - Kiểm tra điều kiện
- `getPaidBookings($schedule_id)` - Lấy booking đã thanh toán
- `getPaidParticipants($schedule_id)` - Lấy số người đã thanh toán

### 3. VIEWS:

**Location:** `app/views/admin/tour-operations/`

- `index.php` - Danh sách tour đã chốt
- `show.php` - Chi tiết tour (tabs: Thông tin, Booking, Khách hàng, HDV, Xe & Tài xế, Phân phòng, Tài chính)

---

## 📝 CHI TIẾT IMPLEMENTATION

### PHASE 1: QUẢN LÝ XE VÀ TÀI XẾ (Ưu tiên)

#### 1.1. Model: `Vehicle.php` và `Driver.php`

**File:** `app/models/Vehicle.php` (mới)

```php
class Vehicle {
    // getAll($filters)
    // findById($id)
    // getAvailable($start_date, $end_date, $capacity_min) - Xe có sẵn, không trùng lịch
    // updateStatus($id, $status)
}
```

**File:** `app/models/Driver.php` (mới)

```php
class Driver {
    // getAll($filters)
    // findById($id)
    // getAvailable($start_date, $end_date, $license_type) - Tài xế có sẵn, không trùng lịch
    // updateStatus($id, $status)
}
```

**File:** `app/models/VehicleAssignment.php` (mới)

```php
class VehicleAssignment {
    // create($data) - Tạo phân công
    // getByScheduleId($schedule_id) - Lấy phân công theo tour schedule
    // update($id, $data) - Cập nhật (đổi xe/tài xế)
    // calculateDriverSalary($schedule_id) - Tự động tính phụ cấp từ tour_allowance_rules
}
```

#### 1.2. Controller: `VehicleController.php` và `DriverController.php`

**File:** `app/controllers/admin/VehicleController.php` (mới)

- CRUD xe
- Quản lý bảo dưỡng

**File:** `app/controllers/admin/DriverController.php` (mới)

- CRUD tài xế
- Xem lịch tài xế

#### 1.3. Views:

**Location:** `app/views/admin/vehicles/`

- `index.php` - Danh sách xe
- `create.php`, `edit.php` - Form thêm/sửa xe
- `show.php` - Chi tiết xe (lịch sử phân công, bảo dưỡng)

**Location:** `app/views/admin/drivers/`

- `index.php` - Danh sách tài xế
- `create.php`, `edit.php` - Form thêm/sửa tài xế
- `show.php` - Chi tiết tài xế (lịch làm việc)

#### 1.4. Routes:

**File:** `routes/admin.php`

```php
case 'vehicles':
    // CRUD vehicles
case 'drivers':
    // CRUD drivers
```

---

### PHASE 2: TRANG QUẢN LÝ TOUR ĐÃ CHỐT

#### 2.1. Model: `TourOperations.php`

**File:** `app/models/TourOperations.php` (mới)

```php
class TourOperations {
    /**
     * Lấy danh sách tour đã chốt (có đủ booking đã thanh toán)
     * Điều kiện: Đã qua deadline booking + có đủ booking đã thanh toán
     */
    public function getReadyForOperations($filters = [], $page = 1, $limit = 20) {
        // Query: tour_schedules đã qua deadline booking
        // deadline_date = start_date - booking_deadline_days
        // CURDATE() >= deadline_date
        // JOIN với tours, bookings
        // GROUP BY và HAVING để filter: có đủ booking đã thanh toán >= min_participants
    }

    /**
     * Tổng hợp thông tin tour cho trang operations
     */
    public function getTourOperationsSummary($schedule_id) {
        // Thông tin tour
        // Số booking đã thanh toán
        // Số người đã thanh toán
        // Đã gán HDV chưa
        // Đã gán xe chưa
        // Đã phân phòng chưa
    }

    /**
     * Kiểm tra tour đã sẵn sàng cho operations chưa
     */
    public function checkReadyForOperations($schedule_id) {
        // Kiểm tra: có booking đã thanh toán >= min_participants
    }
}
```

#### 2.2. Controller: `TourOperationsController.php`

**File:** `app/controllers/admin/TourOperationsController.php` (mới)

```php
class TourOperationsController {
    public function index() {
        // Danh sách tour đã chốt
        // Filter: ngày, tour, trạng thái
        // Hiển thị: tour code, tên, ngày khởi hành, số người đã thanh toán,
        //           đã gán HDV, đã gán xe, đã phân phòng
    }

    public function show($id) {
        // Chi tiết tour
        // Tabs: Thông tin, Booking, Khách hàng, HDV, Xe & Tài xế, Phân phòng, Tài chính
    }

    public function assignGuide() {
        // Gán HDV
        // Tự động tính phụ cấp từ tour_allowance_rules
    }

    public function assignVehicle() {
        // Phân công xe và tài xế
        // Kiểm tra xe/tài xế có sẵn
        // Tự động tính phụ cấp tài xế
    }
}
```

#### 2.3. View: `index.php`

**File:** `app/views/admin/tour-operations/index.php` (mới)

**Hiển thị:**

- Bảng danh sách tour đã chốt
- Cột: Mã Tour, Tên Tour, Ngày khởi hành, Số người đã thanh toán, HDV, Xe, Phân phòng, Hành động
- Filter: Tour, Từ ngày, Đến ngày, Trạng thái
- Badge màu:
  - 🟢 Đã đủ: Có HDV + Xe + Phân phòng
  - 🟡 Thiếu: Thiếu 1 trong 3
  - 🔴 Chưa: Chưa có gì

#### 2.4. View: `show.php`

**File:** `app/views/admin/tour-operations/show.php` (mới)

**Tabs:**

1. **Thông tin tổng hợp:**

   - Thông tin tour (tên, mã, ngày, số ngày)
   - Số booking đã thanh toán
   - Số người đã thanh toán
   - Doanh thu
   - Trạng thái: Đã gán HDV, Đã gán xe, Đã phân phòng

2. **Booking:**

   - Danh sách booking đã thanh toán
   - Mã booking, khách hàng, số người, số tiền

3. **Khách hàng:**

   - Danh sách khách hàng (từ booking_customers)
   - Tên, giới tính, tuổi, booking code

4. **HDV:**

   - HDV hiện tại (nếu có)
   - Form chọn HDV mới
   - Danh sách HDV có sẵn (không trùng lịch)
   - Phụ cấp HDV (tự động tính)

5. **Xe & Tài xế:**

   - Phân công hiện tại (nếu có)
   - Form chọn xe và tài xế
   - Danh sách xe có sẵn (không trùng lịch, đủ chỗ)
   - Danh sách tài xế có sẵn (không trùng lịch, bằng lái phù hợp)
   - Phụ cấp tài xế (tự động tính)
   - Chi phí nhiên liệu dự kiến

6. **Phân phòng:**

   - Link đến trang phân phòng (hoặc embed)
   - Xem phân phòng hiện tại

7. **Tài chính:**
   - Doanh thu
   - Chi phí dịch vụ (ước tính)
   - Chi phí xe
   - Chi phí HDV
   - Chi phí cố định
   - Lợi nhuận dự kiến

---

### PHASE 3: TÍCH HỢP VÀ HOÀN THIỆN

#### 3.1. Tích hợp với Room Assignment

- Link từ trang tour operations đến trang phân phòng
- Hiển thị trạng thái phân phòng trong danh sách

#### 3.2. Tự động tính phụ cấp

- Tích hợp với `tour_allowance_rules`
- Tự động tính khi gán HDV/tài xế

#### 3.3. Kiểm tra trùng lịch

- Xe: Không trùng với tour khác hoặc bảo dưỡng
- Tài xế: Không trùng lịch
- HDV: Không trùng lịch (từ tour_assignments)

#### 3.4. Xác nhận tour

- Button "Xác nhận tour" khi đã đủ:
  - ✅ Đã gán HDV
  - ✅ Đã gán xe và tài xế
  - ✅ Đã phân phòng
  - ✅ Đủ min_participants
- Cập nhật `tour_schedules.status = 'confirmed'`

---

## 🔄 WORKFLOW

### Bước 1: Xem danh sách tour đã chốt

1. Admin vào trang "Quản lý Tour Đã Chốt"
2. Hệ thống hiển thị các tour schedule có đủ booking đã thanh toán
3. Filter theo ngày, tour, trạng thái

### Bước 2: Chọn tour để xử lý

1. Click vào tour trong danh sách
2. Mở trang chi tiết với các tabs

### Bước 3: Gán HDV

1. Vào tab "HDV"
2. Xem HDV hiện tại (nếu có)
3. Chọn HDV mới từ danh sách có sẵn
4. Hệ thống tự động tính phụ cấp
5. Lưu → Tạo `tour_assignments`

### Bước 4: Phân công xe và tài xế

1. Vào tab "Xe & Tài xế"
2. Tính số xe cần (dựa trên số người)
3. Chọn xe từ danh sách có sẵn
4. Chọn tài xế từ danh sách có sẵn
5. Hệ thống tự động tính phụ cấp tài xế
6. Nhập chi phí nhiên liệu dự kiến
7. Lưu → Tạo `vehicle_assignments` và `driver_schedules`

### Bước 5: Phân phòng

1. Vào tab "Phân phòng"
2. Link đến trang phân phòng (hoặc embed)
3. Thực hiện phân phòng tự động hoặc thủ công

### Bước 6: Xác nhận tour

1. Kiểm tra đã đủ: HDV, Xe, Phân phòng
2. Click "Xác nhận tour"
3. Cập nhật `tour_schedules.status = 'confirmed'`

---

## 📋 QUERIES QUAN TRỌNG

### 1. Lấy danh sách tour đã chốt:

```sql
SELECT
    ts.id,
    ts.start_date,
    ts.end_date,
    t.tour_code,
    t.name AS tour_name,
    t.min_participants,
    t.booking_deadline_days,
    DATE_SUB(ts.start_date, INTERVAL t.booking_deadline_days DAY) AS booking_deadline_date,
    COUNT(DISTINCT b.id) AS total_paid_bookings,
    SUM(b.adult_count + b.child_count + b.infant_count) AS total_paid_participants,
    SUM(b.final_amount) AS total_revenue,
    ts.status,
    ts.guide_id,
    u.full_name AS guide_name,
    (SELECT COUNT(*) FROM vehicle_assignments WHERE tour_schedule_id = ts.id) AS vehicle_count,
    (SELECT COUNT(*) FROM room_assignments WHERE tour_schedule_id = ts.id) AS room_assignment_count
FROM tour_schedules ts
JOIN tours t ON ts.tour_id = t.id
LEFT JOIN bookings b ON ts.id = b.tour_schedule_id
    AND b.payment_status = 'paid'
LEFT JOIN users u ON ts.guide_id = u.id
WHERE ts.status IN ('open', 'closed', 'pending', 'confirmed')
  -- Đã qua deadline booking (không còn đặt được nữa)
  AND CURDATE() >= DATE_SUB(ts.start_date, INTERVAL t.booking_deadline_days DAY)
GROUP BY ts.id
HAVING total_paid_participants >= t.min_participants
ORDER BY ts.start_date DESC;
```

### 2. Kiểm tra xe có sẵn:

```sql
SELECT v.*
FROM vehicles v
WHERE v.status = 'active'
  AND v.capacity >= :capacity_min
  AND v.id NOT IN (
      SELECT va.vehicle_id
      FROM vehicle_assignments va
      WHERE va.status IN ('assigned', 'confirmed', 'in_use')
        AND (
            (va.start_date <= :end_date AND va.end_date >= :start_date)
        )
  )
ORDER BY v.capacity;
```

### 3. Kiểm tra tài xế có sẵn:

```sql
SELECT d.*
FROM drivers d
WHERE d.status = 'active'
  AND d.license_type IN (:license_types) -- ['D', 'E'] cho xe lớn
  AND d.id NOT IN (
      SELECT ds.driver_id
      FROM driver_schedules ds
      WHERE ds.status IN ('scheduled', 'confirmed', 'in_progress')
        AND ds.schedule_date BETWEEN :start_date AND :end_date
  )
ORDER BY d.full_name;
```

### 4. Tự động tính phụ cấp tài xế:

```sql
SELECT driver_allowance
FROM tour_allowance_rules
WHERE tour_type = :tour_type
  AND (duration_days_min IS NULL OR :duration_days >= duration_days_min)
  AND (duration_days_max IS NULL OR :duration_days <= duration_days_max)
  AND (participant_min IS NULL OR :participant_count >= participant_min)
  AND (participant_max IS NULL OR :participant_count <= participant_max)
  AND status = 'active'
ORDER BY priority DESC
LIMIT 1;
```

---

## ⚠️ BUSINESS RULES

1. **Điều kiện hiển thị trong danh sách:**

   - **Đã qua deadline booking:** `CURDATE() >= DATE_SUB(tour_schedules.start_date, INTERVAL tours.booking_deadline_days DAY)`
     - Không còn đặt được booking nữa (đã chốt danh sách)
   - Phải có ít nhất 1 booking với `payment_status = 'paid'`
   - Tổng số người đã thanh toán >= `tours.min_participants`

2. **Gán xe:**

   - Xe phải có sẵn (không trùng lịch)
   - Xe phải đủ chỗ cho số khách (có thể thêm buffer)
   - Xe không được trong thời gian bảo dưỡng

3. **Gán tài xế:**

   - Tài xế phải có sẵn (không trùng lịch)
   - Bằng lái phù hợp với loại xe:
     - Xe lớn (bus_45, bus_29): Cần bằng D hoặc E
     - Xe nhỏ (car_7, car_4): Cần bằng B1 hoặc B2

4. **Gán HDV:**

   - HDV phải có sẵn (không trùng lịch)
   - HDV phải có role = 'guide'

5. **Xác nhận tour:**
   - Phải có HDV
   - Phải có xe và tài xế
   - Phải có phân phòng (hoặc không bắt buộc, tùy business)
   - Phải đủ min_participants

---

## 🎨 UI/UX DESIGN

### Trang danh sách:

- Card/Table layout
- Badge màu cho trạng thái
- Filter sidebar hoặc top bar
- Pagination

### Trang chi tiết:

- Tab navigation
- Summary card ở đầu trang
- Form inline hoặc modal cho gán HDV/xe
- Table cho danh sách booking/khách hàng

---

## 📅 TIMELINE ƯỚC TÍNH

### Phase 1: Quản lý Xe và Tài xế (3-4 ngày)

- Day 1-2: Models và Controllers (Vehicle, Driver, VehicleAssignment)
- Day 3: Views (CRUD xe và tài xế)
- Day 4: Testing và fix bugs

### Phase 2: Trang Quản lý Tour Đã Chốt (4-5 ngày)

- Day 1-2: Model TourOperations, Controller TourOperationsController
- Day 3: View index.php (danh sách)
- Day 4: View show.php (chi tiết với tabs)
- Day 5: Tích hợp gán HDV, xe, phân phòng

### Phase 3: Tích hợp và Hoàn thiện (2-3 ngày)

- Day 1: Tích hợp với Room Assignment
- Day 2: Tự động tính phụ cấp
- Day 3: Testing tổng hợp, fix bugs

**Tổng cộng: 9-12 ngày**

---

## 🔗 LIÊN KẾT VỚI MODULES KHÁC

1. **Tour Schedules:** Module này mở rộng từ tour schedules
2. **Room Assignment:** Link đến trang phân phòng
3. **Tour Allowance Rules:** Tự động tính phụ cấp
4. **Bookings:** Lấy booking đã thanh toán
5. **Users:** Lấy danh sách HDV

---

## 📝 NOTES

- Module này tập trung vào **quản lý operations** sau khi tour đã có đủ booking
- Ưu tiên làm **quản lý xe và tài xế trước** (Phase 1)
- Sau đó mới làm trang quản lý tour đã chốt (Phase 2)
- Tích hợp với các module hiện có (Room Assignment, Tour Assignments)

---

## ✅ CHECKLIST

### Phase 1: Quản lý Xe và Tài xế

- [ ] Model Vehicle
- [ ] Model Driver
- [ ] Model VehicleAssignment
- [ ] Controller VehicleController
- [ ] Controller DriverController
- [ ] Views CRUD xe
- [ ] Views CRUD tài xế
- [ ] Routes
- [ ] Testing

### Phase 2: Trang Quản lý Tour Đã Chốt

- [ ] Model TourOperations
- [ ] Controller TourOperationsController
- [ ] View index.php (danh sách)
- [ ] View show.php (chi tiết)
- [ ] Tích hợp gán HDV
- [ ] Tích hợp gán xe và tài xế
- [ ] Link phân phòng
- [ ] Testing

### Phase 3: Tích hợp và Hoàn thiện

- [ ] Tích hợp Room Assignment
- [ ] Tự động tính phụ cấp
- [ ] Kiểm tra trùng lịch
- [ ] Xác nhận tour
- [ ] Testing tổng hợp
