# 📝 TÀI LIỆU TRIỂN KHAI: MODULE HƯỚNG DẪN VIÊN (GUIDE)

## 🎯 MỤC ĐÍCH

Tài liệu này mô tả chi tiết logic và giao diện của module Hướng dẫn viên, được viết lại theo đúng database schema và flow analysis.

---

## 📋 CẤU TRÚC MODULE

### **1. Routes (`routes/guide.php`)**

```php
// Các routes cần có:
- ?act=guide-dashboard          → Dashboard
- ?act=guide-tours             → Danh sách tour
- ?act=guide-tours&action=show&id={id} → Chi tiết tour
- ?act=guide-checkin           → Danh sách tour cần check-in
- ?act=guide-checkin&action=show&schedule_id={id} → Check-in chi tiết
- ?act=guide-checkin&action=store → Lưu check-in
- ?act=guide-checkin&action=printManifest&schedule_id={id} → In manifest
- ?act=guide-journals          → Danh sách journal
- ?act=guide-journals&action=create → Tạo journal mới
- ?act=guide-journals&action=store → Lưu journal
- ?act=guide-journals&action=show&id={id} → Chi tiết journal
- ?act=guide-journals&action=edit&id={id} → Sửa journal
- ?act=guide-journals&action=update → Cập nhật journal
- ?act=guide-journals&action=delete&id={id} → Xóa journal
```

---

## 🔧 CONTROLLERS

### **1. DashboardController**

**Chức năng:**
- Hiển thị thống kê tour sắp tới
- Danh sách tour gần nhất (5 tour)

**Logic:**
```php
- Lấy số tour sắp tới: WHERE guide_id = user_id AND start_date >= today
- Lấy 5 tour gần nhất
- Hiển thị: tour_code, tour_name, start_date, duration
```

---

### **2. TourController**

**Chức năng:**
- `index()`: Danh sách tour được phân công
- `show()`: Chi tiết tour và danh sách hành khách

**Logic `index()`:**
```php
- Filter: guide_id = user_id, start_date >= today
- Pagination: 10 tours/page
- Hiển thị: tour_code, tour_name, start_date, end_date, booked/quota, guide_notes
```

**Logic `show()`:**
```php
- Validate: schedule.guide_id = user_id
- Lấy tour details
- Lấy bookings: tour_id + start_date (exact match), approval_status = 'approved'
- Lấy passengers từ booking_customers
- Hiển thị: tour info, guide_notes, passenger list
```

---

### **3. CheckinController**

**Chức năng:**
- `index()`: Danh sách tour cần check-in
- `show()`: Trang check-in chi tiết
- `store()`: Lưu check-in
- `printManifest()`: In danh sách hành khách

**Logic `index()`:**
```php
- Filter: guide_id = user_id, start_date >= today
- Lấy check-in stats cho mỗi schedule
- Hiển thị: tour info, số hành khách, tiến độ check-in
```

**Logic `show()`:**
```php
- Validate: schedule.guide_id = user_id
- Lấy bookings: tour_id + start_date, approval_status = 'approved'
- Filter bookings: payment_status = 'paid' AND remaining_amount = 0
- Lấy passengers + check-in status
- Hiển thị form check-in với dropdown status cho mỗi passenger
```

**Logic `store()`:**
```php
- Validate: schedule.guide_id = user_id
- Validate từng booking: approval_status = 'approved', payment_status = 'paid', remaining_amount = 0
- Batch check-in: Lưu vào customer_checkins
- Update checkin_time = NOW(), checked_by = user_id
```

**Logic `printManifest()`:**
```php
- Tương tự show() nhưng format cho in
- Hiển thị đầy đủ thông tin hành khách + check-in status
```

---

### **4. JournalController** ⚠️ CẦN VIẾT LẠI

**Vấn đề hiện tại:**
- Đang dùng bảng `tour_journals` (không tồn tại trong database)
- Database có bảng `journals` với cấu trúc khác

**Cấu trúc mới:**

**Logic `index()`:**
```php
- Filter: guide_id = user_id
- Lấy journals từ bảng journals (không phải tour_journals)
- Join với bookings để lấy tour info
- Pagination: 10 journals/page
- Hiển thị: title, booking/tour info, journal_date, day_number, created_at
```

**Logic `create()`:**
```php
- Lấy schedules được phân công cho guide (đã bắt đầu)
- Lấy bookings từ schedules (để chọn booking)
- Form: booking_id, journal_date, day_number, title, content, weather, highlights, issues, images
```

**Logic `store()`:**
```php
- Validate: booking phải thuộc schedule được phân công cho guide
- Validate: booking.approval_status = 'approved'
- Lưu vào bảng journals:
  - booking_id, guide_id, journal_date, day_number
  - title, content, weather, highlights, issues
- Upload images → Lưu vào bảng journal_images (mỗi hình 1 record)
```

**Logic `show()`:**
```php
- Validate: journal.guide_id = user_id
- Lấy journal + booking + tour info
- Lấy images từ journal_images
- Hiển thị đầy đủ thông tin
```

**Logic `edit()`:**
```php
- Validate: journal.guide_id = user_id
- Lấy journal + images
- Form tương tự create() nhưng có existing images
```

**Logic `update()`:**
```php
- Validate: journal.guide_id = user_id
- Update journals table
- Xử lý images: Xóa images đã bỏ, thêm images mới
```

**Logic `delete()`:**
```php
- Validate: journal.guide_id = user_id
- Xóa images từ journal_images (CASCADE)
- Xóa journal từ journals
```

---

## 🗄️ MODELS

### **1. Journal Model** ⚠️ CẦN VIẾT LẠI

**Bảng sử dụng:** `journals` (không phải `tour_journals`)

**Các methods cần có:**

```php
- getAll($filters, $page, $limit)
  - Filters: guide_id, booking_id, journal_date
  - Join với bookings, tours để lấy tour info
  
- getById($id)
  - Join với bookings, tours, users
  
- create($data)
  - Validate booking thuộc guide
  - Insert vào journals
  - Return journal_id
  
- update($id, $data)
  - Update journals table
  
- delete($id)
  - Xóa journal (images tự động xóa do CASCADE)
  
- getImages($journal_id)
  - Lấy từ journal_images
  
- addImage($journal_id, $image_url, $caption, $display_order)
  - Insert vào journal_images
  
- deleteImage($image_id)
  - Xóa từ journal_images
```

---

## 🎨 VIEWS

### **1. Dashboard (`app/views/guide/dashboard.php`)**

**Hiển thị:**
- Stats card: Số tour sắp tới
- Bảng: 5 tour gần nhất
- Mỗi tour: tour_code, tour_name, start_date, duration, nút "Chi tiết"

---

### **2. Tours Index (`app/views/guide/tours/index.php`)**

**Hiển thị:**
- Bảng danh sách tour
- Columns: Mã tour, Tên tour, Khởi hành, Kết thúc, Khách (booked/quota), Hành động
- Hiển thị guide_notes nếu có
- Pagination

---

### **3. Tours Show (`app/views/guide/tours/show.php`)**

**Layout:**
- Left: Thông tin chuyến đi, Ghi chú từ điều hành
- Right: Danh sách hành khách (table)
- Nút "In danh sách"

---

### **4. Check-in Index (`app/views/guide/checkin/index.php`)**

**Hiển thị:**
- Stats cards: Tổng tour, Tổng hành khách, Đã check-in, Chưa check-in
- Bảng: Tour, Ngày khởi hành, Hành khách, Tiến độ check-in, Hành động
- Progress bar cho mỗi tour
- Thống kê: ✅ Có mặt / ❌ Vắng mặt / ⏰ Đến muộn

---

### **5. Check-in Show (`app/views/guide/checkin/show.php`)**

**Hiển thị:**
- Stats cards: Tổng, Có mặt, Vắng mặt, Đến muộn, Chưa check-in
- Form check-in:
  - Table với columns: #, Họ tên, SĐT, Booking, Trạng thái, Ghi chú
  - Dropdown status cho mỗi passenger
  - Nút "Đánh dấu tất cả Có mặt/Vắng mặt"
  - Nút "Lưu check-in"

---

### **6. Check-in Manifest (`app/views/guide/checkin/manifest.php`)**

**Hiển thị:**
- Format cho in (print-friendly)
- Header: Tên tour, mã tour, ngày khởi hành
- Table: #, Họ tên, Năm sinh, Giới tính, SĐT, Booking, Check-in, Ghi chú
- Footer: Chữ ký guide

---

### **7. Journals Index (`app/views/guide/journals/index.php`)** ⚠️ CẦN SỬA

**Hiển thị:**
- Filters: Tour schedule, Booking, Ngày viết (bỏ filter status)
- Danh sách journal:
  - Title, Tour/Booking info, journal_date, day_number
  - Preview content, images
  - Nút Xem/Sửa/Xóa

---

### **8. Journals Create (`app/views/guide/journals/create.php`)** ⚠️ CẦN SỬA

**Form fields:**
- Chọn Booking (từ schedules được phân công)
- Ngày viết nhật ký (date picker)
- Số ngày trong tour (number, optional)
- Tiêu đề (text, required)
- Nội dung (textarea/rich text, required)
- Thời tiết (text, optional)
- Điểm nổi bật (textarea, optional)
- Vấn đề phát sinh (textarea, optional)
- Hình ảnh (file upload, multiple, optional)

---

### **9. Journals Show (`app/views/guide/journals/show.php`)** ⚠️ CẦN SỬA

**Hiển thị:**
- Title, Tour/Booking info, journal_date, day_number
- Content
- Weather, Highlights, Issues (nếu có)
- Images (grid layout)
- Nút Sửa/Xóa (nếu guide là tác giả)

---

### **10. Journals Edit (`app/views/guide/journals/edit.php`)** ⚠️ CẦN SỬA

**Form:**
- Tương tự create() nhưng:
  - Booking (read-only)
  - Có existing images với nút xóa
  - Có thể thêm images mới

---

## 🔄 LOGIC CHI TIẾT

### **1. Lấy Bookings từ Schedule**

```php
// Trong TourController và CheckinController
$bookings = $bookingModel->getAll([
    'tour_id' => $schedule['tour_id'],
    'start_date' => $schedule['start_date'],
    'exact_date' => true, // Exact match
    'status' => 'approved' // approval_status = 'approved'
], 1, 1000);
```

---

### **2. Lấy Passengers từ Bookings**

```php
$passengers = [];
foreach ($bookings as $booking) {
    $p_list = $bookingModel->getPassengers($booking['id']);
    foreach ($p_list as $p) {
        $p['booking_id'] = $booking['id'];
        $p['booking_code'] = $booking['booking_code'];
        $passengers[] = $p;
    }
}
```

---

### **3. Lấy Bookings cho Journal**

```php
// Trong JournalController::create()
// Lấy schedules được phân công
$schedules = $scheduleModel->getAll([
    'guide_id' => $user_id,
    'start_date' => date('Y-m-d') // Đã bắt đầu
], 1, 100);

// Lấy bookings từ mỗi schedule
$bookings = [];
foreach ($schedules as $schedule) {
    $schedule_bookings = $bookingModel->getAll([
        'tour_id' => $schedule['tour_id'],
        'start_date' => $schedule['start_date'],
        'exact_date' => true,
        'status' => 'approved'
    ], 1, 100);
    
    foreach ($schedule_bookings as $b) {
        $b['schedule_id'] = $schedule['id'];
        $b['tour_name'] = $schedule['tour_name'];
        $b['tour_code'] = $schedule['tour_code'];
        $bookings[] = $b;
    }
}
```

---

### **4. Lưu Journal với Images**

```php
// 1. Insert journal
$journal_id = $journalModel->create([
    'booking_id' => $booking_id,
    'guide_id' => $user_id,
    'journal_date' => $journal_date,
    'day_number' => $day_number,
    'title' => $title,
    'content' => $content,
    'weather' => $weather,
    'highlights' => $highlights,
    'issues' => $issues
]);

// 2. Upload và lưu images
foreach ($uploaded_images as $index => $image_path) {
    $journalModel->addImage($journal_id, $image_path, null, $index);
}
```

---

## ✅ VALIDATION RULES

### **Check-in:**
1. Guide phải được phân công tour
2. Booking phải: `approval_status = 'approved'`, `payment_status = 'paid'`, `remaining_amount = 0`
3. Status phải là: `present`, `absent`, hoặc `late`

### **Journal:**
1. Booking phải thuộc schedule được phân công cho guide
2. Booking phải: `approval_status = 'approved'`
3. Title và content không được trống
4. Images: Max 10, mỗi file max 5MB

---

## 🎨 UI/UX GUIDELINES

### **Color Scheme:**
- Primary: Blue (#3b82f6)
- Success: Green (#10b981)
- Warning: Yellow (#f59e0b)
- Danger: Red (#ef4444)
- Info: Blue (#3b82f6)

### **Icons:**
- Dashboard: 📊
- Tours: ✈️
- Check-in: ✅
- Journal: 📔
- Expenses: 💸

### **Layout:**
- Sidebar navigation (guide_layout.php)
- Header với page title
- Main content area với padding
- Responsive design (mobile-friendly)

---

## 📝 NOTES

### **Journal Model Migration:**
- Model hiện tại đang tạo bảng `tour_journals` (không khớp database)
- Cần sửa để dùng bảng `journals` và `journal_images`
- Cần migration script nếu có dữ liệu cũ

### **Booking Selection:**
- Journal link với `booking_id`, không phải `tour_schedule_id` trực tiếp
- Cần lấy bookings từ schedule để guide chọn
- Một schedule có thể có nhiều bookings

---

**Status:** 📝 Tài liệu đã hoàn thành - Sẵn sàng cho implementation

