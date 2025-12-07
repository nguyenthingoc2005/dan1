# 🔍 PHÂN TÍCH CODE BOOKING - VẤN ĐỀ VÀ CLEANUP

**Ngày phân tích:** 2024-12-06

---

## 🔴 **CÁC VẤN ĐỀ NGHIÊM TRỌNG**

### **1. THIẾU SET `rejected_by` VÀ `rejected_at` KHI TỪ CHỐI BOOKING** ❌

**Vấn đề:**
- Schema đã có `rejected_by` và `rejected_at` nhưng code không set khi reject
- Method `Booking::reject()` chỉ set `rejection_reason`, không set `rejected_by` và `rejected_at`

**Vị trí:** `app/models/Booking.php` dòng 590-633

**Hiện trạng:**
```php
public function reject($id, $reason, $userId)
{
    // ...
    $sql = "UPDATE bookings SET 
            approval_status = 'rejected',
            rejection_reason = :reason
            WHERE id = :id";
    // ❌ THIẾU: rejected_by, rejected_at
}
```

**Cần sửa:**
```php
$sql = "UPDATE bookings SET 
        approval_status = 'rejected',
        rejection_reason = :reason,
        rejected_by = :user_id,
        rejected_at = NOW()
        WHERE id = :id";
```

---

### **2. THIẾU VALIDATION `start_date >= today` KHI HỦY BOOKING** ⚠️

**Vấn đề:**
- Method `cancel()` không validate booking chưa khởi hành
- Có thể hủy booking đã khởi hành (quá khứ)

**Vị trí:** `app/models/Booking.php` dòng 497-585

**Cần thêm:**
```php
// Validate booking chưa khởi hành
$startDate = new DateTime($booking['start_date']);
$today = new DateTime();
if ($startDate < $today) {
    throw new Exception("Không thể hủy booking đã khởi hành");
}
```

---

## 🟡 **CÁC VẤN ĐỀ TRUNG BÌNH**

### **3. NHIỀU DEBUG CODE (error_log) CẦN XÓA** ⚠️

**Vấn đề:**
- Có 41 dòng `error_log` trong `BookingController.php`
- Nhiều debug code không cần thiết trong production

**Vị trí:** `app/controllers/admin/BookingController.php`

**Cần xóa:**
- Dòng 60-68: DEBUG log trong `create()`
- Dòng 760-853: DEBUG log trong `previewPassengers()`
- Dòng 1206-1210: DEBUG log trong `downloadTemplate()`

**Giữ lại:**
- Error logging trong catch blocks (dòng 448-450, 573-574, 662-664, etc.) - Cần thiết cho debugging

---

### **4. BIẾN KHÔNG DÙNG: `quota` VÀ `booked_seats` TRONG BẢNG `bookings`** ⚠️

**Vấn đề:**
- Schema có `bookings.quota` và `bookings.booked_seats` nhưng code không dùng
- Chỉ dùng `tour_schedules.quota` và `tour_schedules.booked`

**Khuyến nghị:**
- Xóa 2 trường này khỏi schema (nếu chắc chắn không dùng)
- Hoặc document rõ mục đích sử dụng

---

### **5. CODE DUPLICATE: VALIDATE DEADLINE** ⚠️

**Vấn đề:**
- Logic validate deadline được lặp lại ở nhiều nơi:
  - `store()` - dòng 108-118
  - `storeBookingService()` - dòng 954-966
  - `addPassengerToBooking()` - dòng 1096-1108

**Cần refactor:**
- Tạo method riêng: `validateBookingDeadline($booking_id, $deadline_days = null)`

---

### **6. THIẾU VALIDATION: `rejected_by` TRONG `updateStatus()`** ⚠️

**Vấn đề:**
- Method `updateStatus()` có thể set `rejected` nhưng không set `rejected_by`
- Chỉ set `rejection_reason`

**Vị trí:** `app/models/Booking.php` dòng 352-392

**Cần sửa:**
```php
if ($status == 'rejected' || $status == 'cancelled') {
    $sql = "UPDATE bookings SET 
            approval_status = :status, 
            rejection_reason = :reason,
            rejected_by = :user_id,
            rejected_at = NOW()
            WHERE id = :id";
    $params['reason'] = $reason;
    $params['user_id'] = $userId; // Thêm user_id
}
```

---

## 🟢 **CÁC VẤN ĐỀ NHỎ**

### **7. THIẾU VALIDATION: `service_provider_id` KHÔNG BẮT BUỘC** ⚠️

**Vấn đề:**
- `BookingService::validate()` yêu cầu `service_provider_id` nhưng trong thực tế có thể không cần
- Code cho phép `service_provider_id = null` nhưng validation lại bắt buộc

**Vị trí:** `app/models/BookingService.php` dòng 256-259

**Cần sửa:**
- Xóa validation `service_provider_id` required (đã là optional trong schema)

---

### **8. CODE COMMENT KHÔNG CẦN THIẾT** ⚠️

**Vấn đề:**
- Nhiều comment dài, không cần thiết
- Comment trùng với code

**Ví dụ:**
```php
// Note: Primary customer luôn là adult vì người đặt tour thường là người lớn (phụ huynh)
// Nếu booking chỉ có trẻ em, primary vẫn là adult (người đại diện pháp lý)
$primary_age_type = 'adult'; // Mặc định là adult
```

---

## 📋 **CHECKLIST CLEANUP**

### **🔴 Critical (Phải sửa ngay):**

- [ ] **1. Thêm `rejected_by` và `rejected_at` vào method `reject()`**
- [ ] **2. Thêm validation `start_date >= today` vào method `cancel()`**

### **🟡 Medium (Nên sửa):**

- [ ] **3. Xóa debug code (error_log không cần thiết)**
- [ ] **4. Refactor duplicate validation deadline**
- [ ] **5. Sửa `updateStatus()` để set `rejected_by` khi reject**

### **🟢 Low (Có thể sửa sau):**

- [ ] **6. Xóa validation `service_provider_id` required**
- [ ] **7. Clean up comments không cần thiết**

---

**Ngày tạo:** 2024-12-06  
**Version:** 1.0

