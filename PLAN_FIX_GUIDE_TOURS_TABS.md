# KẾ HOẠCH SỬA LỖI: KHÔNG HIỂN THỊ DỮ LIỆU Ở CÁC TAB TRONG GUIDE TOURS

## 🔍 PHÂN TÍCH VẤN ĐỀ

### URLs bị ảnh hưởng:

- `?act=guide-tours&action=show&id=17&tab=checkin`
- `?act=guide-tours&action=show&id=17&tab=expenses`
- `?act=guide-tours&action=show&id=17&tab=journals`
- `?act=guide-tours&action=show&id=17&tab=services`
- `?act=guide-tours&action=show&id=17&tab=passengers`

### Các tab không hiển thị dữ liệu:

1. **Check-in** - Không hiển thị danh sách check-in
2. **Expenses** - Không hiển thị chi phí phát sinh
3. **Journals** - Không hiển thị nhật ký tour
4. **Services** - Không hiển thị dịch vụ
5. **Passengers** - Không hiển thị hành khách

---

## 🐛 CÁC NGUYÊN NHÂN CÓ THỂ XẢY RA

### 1. **LỖI SQL QUERY - IncurredExpense Model**

**File:** `app/models/IncurredExpense.php`
**Vấn đề:**

- Method `getByScheduleId()` có thể có lỗi với SQL parameters
- Query phức tạp với nhiều OR conditions có thể không match đúng dữ liệu
- Có thể thiếu dữ liệu trong bảng `incurred_expenses` với `tour_schedule_id`

**Kiểm tra:**

```php
// Line 57-62: execute() với named parameters
$stmt->execute([
    'schedule_id1' => $schedule_id,
    'schedule_id2' => $schedule_id,
    'tour_id' => $schedule['tour_id'],
    'start_date' => $schedule['start_date']
]);
```

**Vấn đề tiềm ẩn:**

- Nếu `$schedule` là null hoặc không có `tour_id`, sẽ lỗi
- SQL query có thể không match nếu dữ liệu cũ không có `tour_schedule_id`

---

### 2. **LỖI SQL QUERY - Checkin Model**

**File:** `app/models/Checkin.php`
**Vấn đề:**

- Method `getBySchedule()` chỉ join qua `tour_id` và `start_date`, không check `tour_schedule_id` trực tiếp
- Method `getStatsBySchedule()` cũng có vấn đề tương tự

**Code hiện tại:**

```php
// Line 48: Chỉ join qua tour_id + start_date
JOIN tour_schedules ts ON (b.tour_id = ts.tour_id AND b.start_date = ts.start_date)
```

**Vấn đề:**

- Nếu booking có `tour_schedule_id` nhưng không match với `start_date`, sẽ không tìm thấy
- Cần thêm điều kiện check `b.tour_schedule_id = ts.id`

---

### 3. **LỖI JAVASCRIPT - Tab Switching**

**File:** `app/views/guide/tours/show.php`
**Vấn đề:**

- JavaScript có thể không đọc đúng `?tab=` parameter từ URL
- Các section có class `hidden` mặc định, nếu JS không chạy sẽ không hiển thị
- Có thể có lỗi JavaScript console ngăn code chạy

**Code hiện tại:**

```javascript
// Line 921-932: Đọc tab từ URL
const urlParams = new URLSearchParams(window.location.search);
const tab = urlParams.get("tab");
```

**Vấn đề tiềm ẩn:**

- Nếu `tab` không có trong `tabMap`, sẽ fallback về `tour-info`
- Các section có thể bị ẩn do CSS hoặc JavaScript error

---

### 4. **LỖI DỮ LIỆU RỖNG - Controller**

**File:** `app/controllers/guide/TourController.php`
**Vấn đề:**

- Controller có thể không load đúng dữ liệu do:
  - Bookings không tìm thấy (filter `status => 'paid'` quá strict)
  - Các model methods trả về mảng rỗng
  - Không có error handling khi query fail

**Code hiện tại:**

```php
// Line 94-107: Get bookings
$bookings = $this->bookingModel->getAll([
    'tour_schedule_id' => $id,
    'status' => 'paid'
], 1, 1000);
```

**Vấn đề:**

- Nếu không có booking với status `paid`, sẽ không có passengers, services, checkin data
- Cần check cả `partial` và `paid` status

---

### 5. **LỖI BOOKING SERVICE QUERY**

**File:** `app/models/BookingService.php`
**Vấn đề:**

- Method `getByScheduleId()` join phức tạp có thể miss dữ liệu
- Join condition: `(b.tour_schedule_id = ts.id OR (b.tour_id = ts.tour_id AND b.start_date = ts.start_date))`

**Vấn đề tiềm ẩn:**

- Nếu booking không có `tour_schedule_id` và `start_date` không match chính xác, sẽ không tìm thấy

---

### 6. **LỖI JOURNAL QUERY**

**File:** `app/models/Journal.php`
**Vấn đề:**

- Method `getAll()` filter bằng `tour_schedule_id` có vẻ đúng
- Nhưng có thể không có dữ liệu trong bảng `journals` với `tour_schedule_id` tương ứng

---

### 7. **LỖI PASSENGERS EXTRACTION**

**File:** `app/controllers/guide/TourController.php`
**Vấn đề:**

- Line 110-120: Extract passengers từ bookings
- Nếu `$bookings` rỗng, `$passengers` sẽ rỗng
- Method `getPassengers()` có thể fail nếu booking_id không hợp lệ

---

### 8. **LỖI CHECK-IN DATA LOADING**

**File:** `app/controllers/guide/TourController.php`
**Vấn đề:**

- Line 180-204: Load check-in data
- Filter bookings chỉ lấy `payment_status IN ('partial', 'paid')` và `remaining_amount == 0`
- Có thể quá strict, miss một số bookings

---

## ✅ KẾ HOẠCH SỬA LỖI

### BƯỚC 1: Sửa IncurredExpense Model

**File:** `app/models/IncurredExpense.php`

**Vấn đề:** SQL query có thể không match dữ liệu cũ
**Giải pháp:**

1. Thêm error handling
2. Cải thiện query để match cả dữ liệu cũ và mới
3. Thêm logging để debug

---

### BƯỚC 2: Sửa Checkin Model

**File:** `app/models/Checkin.php`

**Vấn đề:** Không check `tour_schedule_id` trực tiếp
**Giải pháp:**

1. Update `getBySchedule()` để check `b.tour_schedule_id = ts.id` trước
2. Update `getStatsBySchedule()` tương tự
3. Fallback về `tour_id + start_date` nếu cần

---

### BƯỚC 3: Sửa Controller - Booking Filter

**File:** `app/controllers/guide/TourController.php`

**Vấn đề:** Filter quá strict
**Giải pháp:**

1. Thay `'status' => 'paid'` thành check `payment_status IN ('partial', 'paid')`
2. Bỏ điều kiện `remaining_amount == 0` hoặc làm nó flexible hơn
3. Thêm fallback để lấy bookings theo nhiều cách

---

### BƯỚC 4: Sửa Controller - Check-in Data

**File:** `app/controllers/guide/TourController.php`

**Vấn đề:** Filter check-in bookings quá strict
**Giải pháp:**

1. Relax điều kiện filter bookings
2. Đảm bảo lấy được tất cả passengers từ bookings hợp lệ

---

### BƯỚC 5: Cải thiện JavaScript

**File:** `app/views/guide/tours/show.php`

**Vấn đề:** JavaScript có thể fail silently
**Giải pháp:**

1. Thêm error handling
2. Thêm console.log để debug
3. Đảm bảo tab switching hoạt động đúng
4. Thêm fallback nếu tab không hợp lệ

---

### BƯỚC 6: Thêm Error Handling & Logging

**Files:** Tất cả models và controller

**Giải pháp:**

1. Thêm try-catch blocks
2. Log errors vào file log
3. Return empty array thay vì null khi có lỗi
4. Thêm validation cho input parameters

---

### BƯỚC 7: Kiểm tra Database Schema

**Vấn đề:** Có thể thiếu dữ liệu hoặc schema không đúng

**Kiểm tra:**

1. Verify `bookings.tour_schedule_id` có được set đúng không
2. Verify `incurred_expenses.tour_schedule_id` có dữ liệu không
3. Verify `journals.tour_schedule_id` có dữ liệu không
4. Verify `customer_checkins` có dữ liệu cho schedule này không

---

## 🔧 CHI TIẾT SỬA LỖI

### 1. IncurredExpense::getByScheduleId()

```php
// THÊM: Error handling và logging
// THÊM: Check null schedule
// CẢI THIỆN: Query để match cả dữ liệu cũ và mới
```

### 2. Checkin::getBySchedule()

```php
// THAY ĐỔI: Join condition để check tour_schedule_id trước
// THÊM: Fallback về tour_id + start_date
```

### 3. Checkin::getStatsBySchedule()

```php
// THAY ĐỔI: Join condition tương tự
// CẢI THIỆN: Query để lấy đúng tất cả passengers
```

### 4. TourController::show()

```php
// THAY ĐỔI: Booking filter - không quá strict
// THÊM: Error handling cho mỗi data loading
// THÊM: Logging để debug
```

### 5. JavaScript Tab Switching

```javascript
// THÊM: Error handling
// THÊM: Console logging
// CẢI THIỆN: Tab validation
// THÊM: Fallback nếu tab không hợp lệ
```

---

## 📋 CHECKLIST KIỂM TRA

- [ ] Kiểm tra database có dữ liệu cho schedule_id = 17
- [ ] Kiểm tra bookings có `tour_schedule_id = 17` hoặc match `tour_id + start_date`
- [ ] Kiểm tra `incurred_expenses` có dữ liệu
- [ ] Kiểm tra `journals` có dữ liệu
- [ ] Kiểm tra `customer_checkins` có dữ liệu
- [ ] Kiểm tra JavaScript console có lỗi không
- [ ] Kiểm tra network tab xem có request nào fail không
- [ ] Test từng tab một để xác định tab nào bị lỗi

---

## 🎯 ƯU TIÊN SỬA LỖI

1. **CAO:** Sửa Checkin model - ảnh hưởng trực tiếp đến tab checkin
2. **CAO:** Sửa Controller booking filter - ảnh hưởng đến passengers, services
3. **TRUNG BÌNH:** Sửa IncurredExpense model - ảnh hưởng đến tab expenses
4. **TRUNG BÌNH:** Cải thiện JavaScript - ảnh hưởng đến UX
5. **THẤP:** Thêm logging - để debug trong tương lai

---

## 📝 GHI CHÚ

- Tất cả các sửa lỗi cần được test kỹ với schedule_id = 17
- Cần đảm bảo backward compatibility với dữ liệu cũ
- Cần thêm error handling để tránh crash khi dữ liệu không có
- Cần log errors để dễ debug trong tương lai
