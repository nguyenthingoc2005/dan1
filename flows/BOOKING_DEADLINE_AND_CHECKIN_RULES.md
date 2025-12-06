# 📋 QUY TẮC: DEADLINE ĐẶT BOOKING VÀ ĐIỀU KIỆN ĐI TOUR

## 🎯 TỔNG QUAN

File này mô tả các quy tắc về:

1. **Deadline đặt booking:** Khi nào có thể đặt booking trước ngày tour khởi hành
2. **Điều kiện đi tour:** Booking phải đáp ứng điều kiện gì để được đi tour

---

## ⏰ DEADLINE ĐẶT BOOKING

### **Quy định:**

**Option 1: Không có deadline (Mặc định)**

- Có thể đặt booking đến ngày khởi hành
- Validation: `start_date >= ngày hiện tại`

**Option 2: Phải đặt trước 1 ngày** ✅ **(Đã chọn)**

- Phải đặt booking trước 1 ngày so với ngày khởi hành
- Validation: `start_date >= ngày hiện tại + 1 ngày`
- Nếu vi phạm → Không cho tạo booking
- **Ví dụ:**
  - Ngày hiện tại: 10/12/2024
  - Tour khởi hành: 15/12/2024 → ✅ Có thể đặt (còn 5 ngày)
  - Tour khởi hành: 11/12/2024 → ✅ Có thể đặt (còn 1 ngày)
  - Tour khởi hành: 10/12/2024 → ❌ Không thể đặt (chỉ còn 0 ngày)

**Option 3: Phải đặt trước X ngày** (X > 1)

- VD: "Phải đặt trước 2 ngày", "Phải đặt trước 7 ngày"
- Validation: `start_date >= ngày hiện tại + X ngày`
- Nếu vi phạm → Không cho tạo booking

**Option 4: Cấu hình theo từng tour**

- Mỗi tour có thể có deadline khác nhau
- Lưu trong `tours.booking_deadline_days` (số ngày tối thiểu trước ngày khởi hành)

### **Ví dụ:**

```
Ngày hiện tại: 10/12/2024
Tour khởi hành: 15/12/2024

Quy định hiện tại: "Phải đặt trước 1 ngày" ✅
- Deadline: 14/12/2024 (1 ngày trước ngày khởi hành)
- Có thể đặt từ 10/12 đến 14/12
- Không thể đặt từ 15/12 trở đi (ngày khởi hành)

Ví dụ khác:
- Ngày hiện tại: 10/12/2024
- Tour khởi hành: 11/12/2024 → ✅ Có thể đặt (còn 1 ngày)
- Tour khởi hành: 10/12/2024 → ❌ Không thể đặt (chỉ còn 0 ngày)
```

### **Validation khi tạo booking:**

```php
$today = date('Y-m-d');
$deadline_days = 1; // ✅ Quy định: Phải đặt trước 1 ngày
// Hoặc: $deadline_days = $tour['booking_deadline_days'] ?? 1; // Mặc định 1 ngày
$min_start_date = date('Y-m-d', strtotime("+{$deadline_days} days"));

if ($start_date < $min_start_date) {
    throw new Exception("Không thể đặt booking. Phải đặt trước {$deadline_days} ngày so với ngày khởi hành. (Hôm nay: {$today}, Ngày khởi hành tối thiểu: {$min_start_date})");
}
```

---

## ✅ ĐIỀU KIỆN ĐI TOUR (CHECK-IN)

### **1. Trạng thái Booking (BẮT BUỘC)**

**Điều kiện bắt buộc:**

- `approval_status` = 'approved' ✅
- `start_date` = ngày hiện tại ✅ (đúng ngày khởi hành)

**Không cho đi nếu:**

- `approval_status` = 'pending' ❌ (chưa được duyệt)
- `approval_status` = 'rejected' ❌ (bị từ chối)
- `approval_status` = 'cancelled' ❌ (đã hủy)

### **2. Thanh toán** ✅ **PHẢI THANH TOÁN ĐỦ MỚI CHO ĐI** (Bắt buộc)

**Điều kiện bắt buộc:**

- `payment_status` = 'paid' ✅
- `remaining_amount` = 0 ✅
- `paid_amount` = `final_amount` ✅

**Không cho đi nếu:**

- `payment_status` = 'unpaid' ❌ (chưa thanh toán)
- `payment_status` = 'partial' ❌ (chưa thanh toán đủ)
- `remaining_amount` > 0 ❌ (còn nợ tiền)

**Validation khi check-in:**

- Kiểm tra `payment_status = 'paid'`
- Kiểm tra `remaining_amount = 0`
- Nếu không đủ điều kiện → Hiển thị error: "Booking này chưa thanh toán đủ. Vui lòng thanh toán trước khi check-in."
- Không cho phép check-in cho đến khi thanh toán đủ

---

### **3. Validation khi Check-in**

**Kiểm tra đầy đủ:**

```php
// 1. Kiểm tra trạng thái booking
if ($booking['approval_status'] !== 'approved') {
    throw new Exception("Booking chưa được duyệt. Không thể check-in.");
}

// 2. Kiểm tra ngày khởi hành
if ($booking['start_date'] != date('Y-m-d')) {
    throw new Exception("Chưa đến ngày khởi hành. Không thể check-in.");
}

// 3. Kiểm tra thanh toán (BẮT BUỘC phải thanh toán đủ)
if ($booking['payment_status'] !== 'paid') {
    throw new Exception("Booking chưa thanh toán đủ. Vui lòng thanh toán trước khi check-in.");
}

if ($booking['remaining_amount'] > 0) {
    throw new Exception("Booking còn nợ " . number_format($booking['remaining_amount']) . "đ. Vui lòng thanh toán trước khi check-in.");
}

// Kiểm tra paid_amount = final_amount
if ($booking['paid_amount'] < $booking['final_amount']) {
    throw new Exception("Booking chưa thanh toán đủ. Còn thiếu " . number_format($booking['final_amount'] - $booking['paid_amount']) . "đ. Vui lòng thanh toán trước khi check-in.");
}

// Nếu tất cả điều kiện đều pass → Cho phép check-in
```

---

## 📊 BẢNG TÓM TẮT

### **Deadline Đặt Booking:** ✅ **ĐÃ CHỌN: Phải đặt trước 1 ngày**

| Quy định                     | Validation                             | Ví dụ                         |
| ---------------------------- | -------------------------------------- | ----------------------------- |
| ✅ **Phải đặt trước 1 ngày** | `start_date >= ngày hiện tại + 1 ngày` | Phải đặt trước 1 ngày         |
| Không deadline               | `start_date >= ngày hiện tại`          | Có thể đặt đến ngày khởi hành |
| Phải đặt trước 2 ngày        | `start_date >= ngày hiện tại + 2 ngày` | Phải đặt trước 2 ngày         |
| Phải đặt trước 7 ngày        | `start_date >= ngày hiện tại + 7 ngày` | Phải đặt trước 7 ngày         |

### **Điều kiện Đi Tour:** ✅ **PHẢI THANH TOÁN ĐỦ MỚI CHO ĐI**

| Trạng thái  | Thanh toán          | Kết quả                                  |
| ----------- | ------------------- | ---------------------------------------- |
| `approved`  | `paid` (đủ)         | ✅ Cho đi                                |
| `approved`  | `partial` (chưa đủ) | ❌ **Không cho đi** (phải thanh toán đủ) |
| `approved`  | `unpaid` (chưa trả) | ❌ **Không cho đi** (phải thanh toán đủ) |
| `pending`   | Bất kỳ              | ❌ Không cho đi (chưa duyệt)             |
| `rejected`  | Bất kỳ              | ❌ Không cho đi (bị từ chối)             |
| `cancelled` | Bất kỳ              | ❌ Không cho đi (đã hủy)                 |

---

## 🔧 CẤU HÌNH

### **1. Cấu hình Deadline:** ✅ **ĐÃ CHỌN: Phải đặt trước 1 ngày**

```php
// Cấu hình mặc định: Phải đặt trước 1 ngày
$booking_deadline_days = 1;

// Hoặc lưu trong settings/config
define('BOOKING_DEADLINE_DAYS', 1);

// Hoặc cấu hình theo tour (tours table)
ALTER TABLE tours ADD COLUMN booking_deadline_days INT DEFAULT 1; -- Mặc định 1 ngày
```

### **2. Cấu hình Chính sách Thanh toán:** ✅ **ĐÃ CHỌN: Phải thanh toán đủ**

```php
// Cấu hình mặc định: Bắt buộc thanh toán đủ
$require_full_payment_for_checkin = true; // Bắt buộc

// Hoặc lưu trong settings/config
define('REQUIRE_FULL_PAYMENT_FOR_CHECKIN', true);
```

**Lưu ý:** Với quy định này, không cần cấu hình thêm. Hệ thống sẽ:

- Yêu cầu đặt booking trước 1 ngày
- Yêu cầu thanh toán đủ trước khi check-in

---

## ⚠️ LƯU Ý

1. **Deadline đặt booking:** ✅ **Phải đặt trước 1 ngày**

   - Validation: `start_date >= ngày hiện tại + 1 ngày`
   - Nếu vi phạm → Không cho tạo booking
   - Hiển thị cảnh báo khi gần deadline (VD: Chỉ còn 1 ngày nữa)

2. **Thanh toán khi đi tour:** ✅ **Phải thanh toán đủ mới cho đi**

   - **Bắt buộc:** `payment_status = 'paid'` và `remaining_amount = 0`
   - Nếu chưa thanh toán đủ → Không cho check-in
   - Yêu cầu thanh toán trước khi đi tour
   - **Lưu ý:** Tránh rủi ro tài chính, đảm bảo khách đã thanh toán đủ

3. **Check-in:**
   - Chỉ cho phép check-in vào đúng ngày khởi hành (`start_date = ngày hiện tại`)
   - Kiểm tra đầy đủ: `approved` + `paid` (đủ) + `start_date = hôm nay`
   - Sau khi check-in → Đánh dấu khách đã đi (trong `customer_checkins`)

---

**Ngày tạo:** 2024-12-06
