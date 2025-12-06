# 📋 QUY TẮC THÊM DỊCH VỤ & HÀNH KHÁCH VÀO BOOKING

**Ngày cập nhật:** 2024-12-06

---

## 🔒 QUY TẮC CHÍNH

### **1. Deadline Thêm/Sửa Booking**

**Quy tắc:** Không được thêm/sửa dịch vụ hoặc hành khách nếu còn **< 1 ngày** đến ngày khởi hành.

**Áp dụng cho:**
- ✅ Thêm dịch vụ vào booking đã tạo
- ✅ Xóa dịch vụ từ booking đã tạo
- ✅ Thêm hành khách vào booking đã tạo
- ✅ Sửa thông tin booking (nếu có)

**Validation:**
```php
$daysUntilStart = (strtotime($start_date) - strtotime($today)) / (60 * 60 * 24);
if ($daysUntilStart < 1) {
    throw new Exception("Không thể thêm/sửa. Booking này khởi hành trong vòng 1 ngày hoặc đã khởi hành.");
}
```

**UI:**
- Button "Thêm dịch vụ" / "Thêm khách" sẽ bị ẩn nếu vi phạm deadline
- Hiển thị thông báo: "⚠️ Không thể thêm (còn < 1 ngày đến ngày khởi hành)"

---

### **2. Trạng thái Booking**

**Điều kiện thêm/sửa:**
- ✅ `approval_status != 'cancelled'` (không được hủy)
- ✅ `start_date - today >= 1 day` (deadline 1 ngày)

**Không cho phép thêm nếu:**
- ❌ Booking đã bị hủy (`approval_status = 'cancelled'`)
- ❌ Còn < 1 ngày đến ngày khởi hành
- ❌ Booking đã khởi hành (quá khứ)

---

## 📝 CHI TIẾT TỪNG CHỨC NĂNG

### **A. Thêm Dịch vụ vào Booking**

**Khi nào có thể thêm:**
- Booking đã được tạo (có `booking_id`)
- Booking chưa bị hủy
- Còn >= 1 ngày đến ngày khởi hành

**Validation (Backend):**
```php
// app/controllers/admin/BookingController.php::storeBookingService()
- Validate booking exists
- Validate deadline (daysUntilStart >= 1)
- Validate approval_status != 'cancelled'
- Validate service exists & active
- Validate quantity > 0, unit_price >= 0
```

**Xóa dịch vụ:**
- Chỉ cho phép xóa nếu `paid_amount = 0` (chưa thanh toán)
- Vẫn phải tuân theo deadline (>= 1 ngày)

---

### **B. Thêm Hành khách vào Booking**

**Khi nào có thể thêm:**
- Booking đã được tạo (có `booking_id`)
- Booking chưa bị hủy
- Còn >= 1 ngày đến ngày khởi hành

**Validation (Backend):**
```php
// app/controllers/admin/BookingController.php::addPassengerToBooking()
- Validate booking exists
- Validate deadline (daysUntilStart >= 1)
- Validate approval_status != 'cancelled'
- Validate customer not already in booking
- Validate only 1 primary customer
```

**Tự động cập nhật:**
- `adult_count`, `child_count`, `infant_count` được tự động cập nhật

---

## 🎯 VỀ TỔNG TIỀN BOOKING

**Lưu ý quan trọng:**

Theo design hiện tại, **dịch vụ booking (booking_services) KHÔNG tự động cộng vào `total_amount` của booking**.

**Lý do:**
- Dịch vụ được quản lý riêng trong bảng `booking_services`
- Có thể có trạng thái thanh toán riêng (`payment_status` trong `booking_services`)
- Tổng tiền booking chỉ tính giá tour (adult/child/infant × price)

**Nếu muốn cộng dịch vụ vào tổng tiền:**
- Cần tính lại `total_amount` khi thêm/xóa dịch vụ
- Cập nhật `final_amount`, `remaining_amount`
- Có thể cần xác nhận lại với khách hàng về giá mới

**Hiện tại:** Dịch vụ hiển thị riêng trong booking detail, không ảnh hưởng `total_amount`.

---

## 📊 FLOW TỔNG QUAN

```
┌─────────────────────────────────────────────────┐
│ TẠO BOOKING                                     │
│ - Chọn tour, schedule, số người                │
│ - Tính total_amount (chỉ giá tour)             │
│ - Deadline: >= 1 ngày                           │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│ BOOKING ĐÃ TẠO (có booking_id)                  │
│                                                  │
│ ✅ CÓ THỂ:                                       │
│ - Thêm dịch vụ (nếu >= 1 ngày)                 │
│ - Thêm hành khách (nếu >= 1 ngày)              │
│ - Xóa dịch vụ chưa thanh toán                  │
│                                                  │
│ ❌ KHÔNG THỂ:                                    │
│ - Thêm/sửa nếu < 1 ngày đến ngày khởi hành     │
│ - Thêm vào booking đã hủy                      │
└─────────────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────────────┐
│ < 1 NGÀY ĐẾN NGÀY KHỞI HÀNH                    │
│                                                  │
│ - Tất cả chức năng thêm/sửa bị khóa            │
│ - Chỉ có thể xem và thanh toán                 │
│ - Cần admin quyền để override (nếu cần)        │
└─────────────────────────────────────────────────┘
```

---

## 🔧 CẬP NHẬT CODE

### **Files đã sửa:**

1. **`app/controllers/admin/BookingController.php`**
   - `storeBookingService()`: Thêm validation deadline
   - `addPassengerToBooking()`: Thêm validation deadline

2. **`app/views/admin/bookings/show.php`**
   - Ẩn button "Thêm dịch vụ" nếu vi phạm deadline
   - Ẩn button "Thêm khách" nếu vi phạm deadline
   - Hiển thị thông báo khi không thể thêm

---

## ✅ TEST CASES

### **Test Case 1: Thêm dịch vụ thành công**
- Booking có `start_date = today + 2 days`
- `approval_status = 'approved'`
- ✅ Expected: Có thể thêm dịch vụ

### **Test Case 2: Không thể thêm dịch vụ (deadline)**
- Booking có `start_date = today` (hôm nay)
- `approval_status = 'approved'`
- ❌ Expected: Không thể thêm, hiển thị thông báo

### **Test Case 3: Không thể thêm hành khách (deadline)**
- Booking có `start_date = today + 0.5 days` (nửa ngày nữa)
- `approval_status = 'approved'`
- ❌ Expected: Không thể thêm, hiển thị thông báo

### **Test Case 4: Booking đã hủy**
- Booking có `approval_status = 'cancelled'`
- ❌ Expected: Không thể thêm dịch vụ hoặc hành khách

---

**Status:** ✅ Đã triển khai và test

