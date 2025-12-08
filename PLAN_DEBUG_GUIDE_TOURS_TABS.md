# KẾ HOẠCH DEBUG: KIỂM TRA DỮ LIỆU TỪNG BƯỚC

## 🎯 MỤC TIÊU
Kiểm tra từng bước trong `TourController::show()` để xác định dữ liệu nào bị thiếu hoặc sai.

---

## 📋 CHECKLIST DEBUG

### BƯỚC 1: Kiểm tra Schedule cơ bản
**File:** `app/controllers/guide/TourController.php`  
**Vị trí:** Sau line 69

```php
$schedule = $this->scheduleModel->getById($id);

// ✅ DEBUG POINT 1
var_dump('=== DEBUG 1: Schedule ===');
var_dump([
    'schedule_id' => $id,
    'schedule_exists' => !empty($schedule),
    'schedule_data' => $schedule,
    'guide_id' => $schedule['guide_id'] ?? null,
    'user_id' => $user_id,
    'tour_id' => $schedule['tour_id'] ?? null,
    'start_date' => $schedule['start_date'] ?? null,
    'end_date' => $schedule['end_date'] ?? null
]);
die(); // Tạm dừng để xem
```

**Kiểm tra:**
- [ ] `$schedule` có tồn tại không?
- [ ] `guide_id` có khớp với `$user_id` không?
- [ ] `tour_id`, `start_date`, `end_date` có giá trị không?

---

### BƯỚC 2: Kiểm tra Tour Details
**File:** `app/controllers/guide/TourController.php`  
**Vị trí:** Sau line 85

```php
$tour = $tourModel->findById($schedule['tour_id']);

// ✅ DEBUG POINT 2
var_dump('=== DEBUG 2: Tour ===');
var_dump([
    'tour_id' => $schedule['tour_id'],
    'tour_exists' => !empty($tour),
    'tour_data' => $tour,
    'tour_code' => $tour['tour_code'] ?? null,
    'tour_name' => $tour['name'] ?? null
]);
die(); // Tạm dừng để xem
```

**Kiểm tra:**
- [ ] `$tour` có tồn tại không?
- [ ] `tour_code`, `name` có giá trị không?

---

### BƯỚC 3: Kiểm tra Bookings (BƯỚC QUAN TRỌNG)
**File:** `app/controllers/guide/TourController.php`  
**Vị trí:** Sau line 107 (sau khi lấy bookings)

```php
// Sau khi lấy bookings
// ✅ DEBUG POINT 3
var_dump('=== DEBUG 3: Bookings ===');
var_dump([
    'schedule_id' => $id,
    'bookings_count' => count($bookings),
    'bookings_data' => $bookings,
    'first_booking' => $bookings[0] ?? null,
    'booking_ids' => array_column($bookings, 'id'),
    'booking_payment_statuses' => array_column($bookings, 'payment_status'),
    'booking_tour_schedule_ids' => array_column($bookings, 'tour_schedule_id')
]);
die(); // Tạm dừng để xem
```

**Kiểm tra:**
- [ ] Có bao nhiêu bookings?
- [ ] `tour_schedule_id` của bookings có khớp với `$id` không?
- [ ] `payment_status` là gì? (cần 'partial' hoặc 'paid')
- [ ] Có booking nào không?

**Nếu không có bookings, kiểm tra SQL trực tiếp:**
```sql
-- Kiểm tra bookings có tour_schedule_id = 17
SELECT * FROM bookings WHERE tour_schedule_id = 17;

-- Kiểm tra bookings theo tour_id + start_date
SELECT b.*, ts.id as schedule_id 
FROM bookings b
JOIN tour_schedules ts ON (b.tour_id = ts.tour_id AND b.start_date = ts.start_date)
WHERE ts.id = 17;

-- Kiểm tra payment_status
SELECT payment_status, COUNT(*) as count 
FROM bookings 
WHERE tour_schedule_id = 17 
GROUP BY payment_status;
```

---

### BƯỚC 4: Kiểm tra Passengers
**File:** `app/controllers/guide/TourController.php`  
**Vị trí:** Sau line 120 (sau khi extract passengers)

```php
// Sau khi extract passengers
// ✅ DEBUG POINT 4
var_dump('=== DEBUG 4: Passengers ===');
var_dump([
    'bookings_count' => count($bookings),
    'passengers_count' => count($passengers),
    'passengers_data' => $passengers,
    'first_passenger' => $passengers[0] ?? null,
    'passenger_names' => array_column($passengers, 'full_name')
]);
die(); // Tạm dừng để xem
```

**Kiểm tra:**
- [ ] Có bao nhiêu passengers?
- [ ] Mỗi booking có bao nhiêu passengers?
- [ ] `full_name`, `phone` có giá trị không?

**Nếu không có passengers, kiểm tra SQL:**
```sql
-- Kiểm tra booking_customers
SELECT bc.*, b.booking_code, b.tour_schedule_id
FROM booking_customers bc
JOIN bookings b ON bc.booking_id = b.id
WHERE b.tour_schedule_id = 17;
```

---

### BƯỚC 5: Kiểm tra Booking Services
**File:** `app/controllers/guide/TourController.php`  
**Vị trí:** Sau line 140 (sau khi lấy booking services)

```php
$bookingServices = $bookingServiceModel->getByScheduleId($id);

// ✅ DEBUG POINT 5
var_dump('=== DEBUG 5: Booking Services ===');
var_dump([
    'schedule_id' => $id,
    'booking_services_count' => count($bookingServices),
    'booking_services_data' => $bookingServices,
    'first_service' => $bookingServices[0] ?? null,
    'service_types' => array_unique(array_column($bookingServices, 'service_type_name'))
]);
die(); // Tạm dừng để xem
```

**Kiểm tra:**
- [ ] Có bao nhiêu booking services?
- [ ] `service_name`, `service_type_name` có giá trị không?
- [ ] `supplier_name` có giá trị không?

**Nếu không có services, kiểm tra SQL:**
```sql
-- Kiểm tra booking_services
SELECT bs.*, b.booking_code, b.tour_schedule_id
FROM booking_services bs
JOIN bookings b ON bs.booking_id = b.id
WHERE b.tour_schedule_id = 17;
```

---

### BƯỚC 6: Kiểm tra Expenses
**File:** `app/controllers/guide/TourController.php`  
**Vị trí:** Sau line 181 (sau khi lấy expenses)

```php
$expenses = $expenseModel->getByScheduleId($id);
$expense_total = $expenseModel->getTotalByScheduleId($id);

// ✅ DEBUG POINT 6
var_dump('=== DEBUG 6: Expenses ===');
var_dump([
    'schedule_id' => $id,
    'expenses_count' => count($expenses),
    'expenses_data' => $expenses,
    'expense_total' => $expense_total,
    'first_expense' => $expenses[0] ?? null
]);
die(); // Tạm dừng để xem
```

**Kiểm tra:**
- [ ] Có bao nhiêu expenses?
- [ ] `amount`, `description` có giá trị không?
- [ ] `approval_status` là gì?

**Nếu không có expenses, kiểm tra SQL:**
```sql
-- Kiểm tra incurred_expenses
SELECT ie.*, b.booking_code, b.tour_schedule_id
FROM incurred_expenses ie
LEFT JOIN bookings b ON ie.booking_id = b.id
WHERE ie.tour_schedule_id = 17
   OR (ie.tour_schedule_id IS NULL AND b.tour_schedule_id = 17)
   OR (ie.tour_schedule_id IS NULL AND b.tour_schedule_id IS NULL 
       AND b.tour_id = (SELECT tour_id FROM tour_schedules WHERE id = 17)
       AND b.start_date = (SELECT start_date FROM tour_schedules WHERE id = 17));
```

---

### BƯỚC 7: Kiểm tra Journals
**File:** `app/controllers/guide/TourController.php`  
**Vị trí:** Sau line 190 (sau khi lấy journals)

```php
$journals = $journalModel->getAll(['tour_schedule_id' => $id], 1, 100);

// ✅ DEBUG POINT 7
var_dump('=== DEBUG 7: Journals ===');
var_dump([
    'schedule_id' => $id,
    'journals_count' => count($journals),
    'journals_data' => $journals,
    'first_journal' => $journals[0] ?? null,
    'journal_titles' => array_column($journals, 'title')
]);
die(); // Tạm dừng để xem
```

**Kiểm tra:**
- [ ] Có bao nhiêu journals?
- [ ] `title`, `content` có giá trị không?
- [ ] `journal_date` có giá trị không?

**Nếu không có journals, kiểm tra SQL:**
```sql
-- Kiểm tra journals
SELECT j.*, ts.id as schedule_id
FROM journals j
JOIN tour_schedules ts ON j.tour_schedule_id = ts.id
WHERE ts.id = 17;
```

---

### BƯỚC 8: Kiểm tra Check-in Data
**File:** `app/controllers/guide/TourController.php`  
**Vị trí:** Sau line 220 (sau khi lấy check-in passengers)

```php
// Sau khi lấy check-in passengers
// ✅ DEBUG POINT 8
var_dump('=== DEBUG 8: Check-in Data ===');
var_dump([
    'schedule_id' => $id,
    'checkin_passengers_count' => count($checkin_passengers),
    'checkin_passengers_data' => $checkin_passengers,
    'checkin_stats' => $checkin_stats,
    'can_checkin' => $can_checkin,
    'first_checkin_passenger' => $checkin_passengers[0] ?? null
]);
die(); // Tạm dừng để xem
```

**Kiểm tra:**
- [ ] Có bao nhiêu check-in passengers?
- [ ] `checkin_status`, `checkin_time` có giá trị không?
- [ ] `checkin_stats` có đúng không?

**Nếu không có check-in data, kiểm tra SQL:**
```sql
-- Kiểm tra customer_checkins
SELECT cc.*, c.full_name, b.booking_code, b.tour_schedule_id
FROM customer_checkins cc
JOIN customers c ON cc.customer_id = c.id
JOIN bookings b ON cc.booking_id = b.id
WHERE b.tour_schedule_id = 17;
```

---

## 🔧 CÁCH SỬ DỤNG

### Phương pháp 1: Debug từng bước
1. Mở file `app/controllers/guide/TourController.php`
2. Thêm code debug vào từng điểm (DEBUG POINT 1, 2, 3...)
3. Truy cập URL: `?act=guide-tours&action=show&id=17`
4. Xem kết quả var_dump
5. Ghi chú lại dữ liệu nào bị thiếu
6. Xóa code debug và chuyển sang bước tiếp theo

### Phương pháp 2: Debug tất cả cùng lúc
1. Thêm tất cả các debug points
2. Comment `die()` ở các điểm trước
3. Chỉ để `die()` ở điểm cuối cùng muốn xem
4. Xem tất cả dữ liệu cùng lúc

### Phương pháp 3: Sử dụng error_log thay vì var_dump
```php
// Thay vì var_dump, dùng error_log
error_log('DEBUG 3: Bookings count = ' . count($bookings));
error_log('DEBUG 3: Bookings = ' . print_r($bookings, true));
```
Sau đó xem file `logs/app.log`

---

## 📊 BẢNG TỔNG HỢP KẾT QUẢ

Sau khi debug, điền vào bảng này:

| Bước | Tên Dữ Liệu | Có Dữ Liệu? | Số Lượng | Ghi Chú |
|------|-------------|-------------|----------|---------|
| 1 | Schedule | ☐ | - | |
| 2 | Tour | ☐ | - | |
| 3 | Bookings | ☐ | - | |
| 4 | Passengers | ☐ | - | |
| 5 | Booking Services | ☐ | - | |
| 6 | Expenses | ☐ | - | |
| 7 | Journals | ☐ | - | |
| 8 | Check-in Data | ☐ | - | |

---

## 🎯 KẾT QUẢ MONG ĐỢI

### Nếu tất cả đều có dữ liệu:
- ✅ Vấn đề nằm ở JavaScript hoặc CSS
- ✅ Kiểm tra lại tab switching logic

### Nếu thiếu dữ liệu ở bước nào:
- ❌ Vấn đề nằm ở query SQL hoặc database
- ❌ Kiểm tra lại SQL query ở bước đó
- ❌ Kiểm tra database có dữ liệu không

### Nếu bookings = 0:
- ❌ Đây là vấn đề gốc rễ
- ❌ Tất cả dữ liệu khác (passengers, services, check-in) sẽ = 0
- ❌ Cần kiểm tra:
  - Bookings có `tour_schedule_id = 17` không?
  - Bookings có `payment_status IN ('partial', 'paid')` không?
  - Bookings có match `tour_id + start_date` không?

---

## 💡 LƯU Ý

1. **Luôn xóa code debug sau khi xong** để tránh lộ thông tin
2. **Không commit code debug** lên git
3. **Dùng `error_log()` thay vì `var_dump()`** nếu muốn giữ code sạch hơn
4. **Kiểm tra database trực tiếp** nếu var_dump cho thấy dữ liệu rỗng

---

## 🚀 BẮT ĐẦU DEBUG

Bắt đầu từ **BƯỚC 1** và làm tuần tự. Khi tìm thấy bước nào thiếu dữ liệu, dừng lại và kiểm tra SQL query ở bước đó.

