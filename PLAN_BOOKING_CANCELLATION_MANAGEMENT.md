# KẾ HOẠCH TRANG QUẢN LÝ HỦY BOOKING

## 📋 MỤC TIÊU

Tạo một module quản lý riêng để xử lý và theo dõi các booking đã hủy, bao gồm:
- Danh sách booking đã hủy
- Chi tiết thông tin hủy
- Quản lý hoàn tiền
- Thống kê và báo cáo

---

## 🔍 PHÂN TÍCH CÁC CASE HỦY

### 1. **Case 1: Hủy Booking Trước Ngày Khởi Hành**
- **Điều kiện**: Booking chưa khởi hành (`start_date > today`)
- **Xử lý**: 
  - Tự động tính phí hủy theo `cancellation_policies` dựa trên số ngày trước khởi hành
  - Tính số tiền hoàn lại = `paid_amount - cancellation_fee`
  - Trả lại quota cho schedule
  - Cập nhật `payment_status`:
    - `cancelled`: Nếu không có tiền hoàn lại (paid_amount = 0 hoặc fee >= paid_amount)
    - `refunded`: Nếu có tiền hoàn lại (refund_amount > 0)

### 2. **Case 2: Hủy Booking Đã Thanh Toán Một Phần**
- **Điều kiện**: `payment_status = 'partial'` và `paid_amount > 0`
- **Xử lý**:
  - Tính phí hủy theo policy
  - Hoàn lại = `paid_amount - cancellation_fee`
  - Nếu hoàn lại > 0 → `payment_status = 'refunded'`
  - Nếu hoàn lại = 0 → `payment_status = 'cancelled'`

### 3. **Case 3: Hủy Booking Đã Thanh Toán Đủ**
- **Điều kiện**: `payment_status = 'paid'`
- **Xử lý**:
  - Tính phí hủy theo policy
  - Hoàn lại = `paid_amount - cancellation_fee`
  - Luôn có hoàn lại → `payment_status = 'refunded'`

### 4. **Case 4: Hủy Booking Chưa Thanh Toán**
- **Điều kiện**: `payment_status = 'unpaid'`
- **Xử lý**:
  - Không có phí hủy (vì chưa thanh toán)
  - Không có hoàn lại
  - `payment_status = 'cancelled'`

### 5. **Case 5: Hủy Booking Đã Khởi Hành** (Hiện tại không cho phép)
- **Điều kiện**: `start_date < today`
- **Xử lý**: 
  - Hiện tại: Throw exception "Không thể hủy booking đã khởi hành"
  - **Có thể mở rộng**: Cho phép hủy với phí 100% (không hoàn tiền)

---

## 📁 CẤU TRÚC MODULE

### 1. **Controller**: `app/controllers/admin/CancellationController.php`
- `index()`: Danh sách booking đã hủy
- `show($id)`: Chi tiết booking hủy
- `processRefund()`: Xử lý hoàn tiền thủ công
- `export()`: Xuất danh sách hủy (Excel/CSV)
- `statistics()`: Thống kê hủy booking

### 2. **Views**: `app/views/admin/cancellations/`
- `index.php`: Danh sách booking đã hủy
- `show.php`: Chi tiết booking hủy
- `statistics.php`: Trang thống kê

### 3. **Routes**: Thêm vào `routes/admin.php`
```php
case 'cancellations':
    require_once CONTROLLERS_PATH . '/admin/CancellationController.php';
    $controller = new CancellationController($pdo);
    // ... actions
```

---

## 🎯 CHỨC NĂNG CHI TIẾT

### **Trang 1: Danh Sách Booking Đã Hủy** (`index.php`)

#### **Filters & Search:**
- Tìm kiếm: Booking code, tên khách hàng, số điện thoại
- Lọc theo:
  - Trạng thái: `cancelled` | `refunded`
  - Tour
  - Khoảng thời gian hủy (`cancellation_date`)
  - Khoảng thời gian khởi hành (`start_date`)
  - Có hoàn tiền / Không hoàn tiền

#### **Table Columns:**
1. Booking Code
2. Khách hàng (Tên + SĐT)
3. Tour (Tên tour)
4. Ngày khởi hành
5. Ngày hủy
6. Lý do hủy (truncate)
7. Phí hủy
8. Số tiền hoàn lại
9. Trạng thái hoàn tiền
10. Actions (Xem chi tiết)

#### **Features:**
- Pagination
- Export Excel/CSV
- Quick stats: Tổng số hủy, Tổng phí hủy, Tổng tiền hoàn lại

---

### **Trang 2: Chi Tiết Booking Hủy** (`show.php`)

#### **Sections:**

**A. Thông Tin Booking:**
- Booking code, Tour, Khách hàng
- Ngày khởi hành, Ngày kết thúc
- Số lượng người (Adult/Child/Infant)

**B. Thông Tin Hủy:**
- Ngày hủy (`cancellation_date`)
- Lý do hủy (`cancellation_reason`)
- Policy áp dụng (`cancellation_policy_id` → name, fee_percentage)
- Số ngày trước khởi hành (tính toán)
- Phí hủy (`cancellation_fee`)
- Số tiền hoàn lại (`refund_amount`)

**C. Thông Tin Thanh Toán:**
- Tổng tiền tour (`total_amount`)
- Giảm giá (`discount_amount`)
- Thành tiền (`final_amount`)
- Đã thanh toán (`paid_amount`)
- Phí hủy (`cancellation_fee`)
- Số tiền hoàn lại (`refund_amount`)
- Trạng thái: `cancelled` | `refunded`

**D. Lịch Sử Thanh Toán:**
- Danh sách payments (nếu có)
- Danh sách refunds (nếu có)

**E. Actions:**
- Nếu `refund_amount > 0` và chưa hoàn tiền → Button "Xử lý hoàn tiền"
- Export PDF thông tin hủy
- Quay lại danh sách

---

### **Trang 3: Xử Lý Hoàn Tiền** (Modal/Page)

#### **Khi nào cần:**
- Booking có `refund_amount > 0`
- Chưa có payment với `payment_type = 'refund'` cho booking này

#### **Form:**
- Số tiền hoàn lại (pre-filled từ `refund_amount`, có thể chỉnh sửa)
- Phương thức hoàn tiền: `bank_transfer` | `cash` | `other`
- Ghi chú
- Ngày hoàn tiền

#### **Xử lý:**
- Tạo payment với `payment_type = 'refund'`
- Cập nhật booking `paid_amount` (giảm đi)
- Ghi log

---

### **Trang 4: Thống Kê Hủy Booking** (`statistics.php`)

#### **Metrics:**
1. **Tổng quan:**
   - Tổng số booking đã hủy
   - Tổng phí hủy
   - Tổng tiền hoàn lại
   - Tỷ lệ hủy (so với tổng booking)

2. **Theo thời gian:**
   - Biểu đồ hủy theo tháng/năm
   - Top tháng có nhiều hủy nhất

3. **Theo tour:**
   - Top tour bị hủy nhiều nhất
   - Tỷ lệ hủy theo tour

4. **Theo lý do:**
   - Thống kê lý do hủy phổ biến

5. **Theo policy:**
   - Số booking hủy theo từng policy
   - Tổng phí hủy theo policy

---

## 🔧 MODEL METHODS CẦN BỔ SUNG

### **Booking Model:**
```php
// Lấy danh sách booking đã hủy
public function getCancelledBookings($filters = [], $page = 1, $limit = 20)

// Lấy thống kê hủy booking
public function getCancellationStatistics($filters = [])

// Kiểm tra đã hoàn tiền chưa
public function hasRefundProcessed($booking_id)
```

### **Payment Model:**
- Đã có method `refund()` - OK

---

## 📊 DATABASE QUERIES CẦN

### **1. Danh sách booking đã hủy:**
```sql
SELECT b.*, 
       t.name as tour_name, t.tour_code,
       c.full_name as customer_name, c.phone as customer_phone,
       cp.name as policy_name, cp.fee_percentage,
       u.full_name as cancelled_by_name
FROM bookings b
LEFT JOIN tours t ON b.tour_id = t.id
LEFT JOIN customers c ON b.customer_id = c.id
LEFT JOIN cancellation_policies cp ON b.cancellation_policy_id = cp.id
LEFT JOIN users u ON b.rejected_by = u.id
WHERE b.payment_status IN ('cancelled', 'refunded')
  AND b.cancellation_date IS NOT NULL
ORDER BY b.cancellation_date DESC
```

### **2. Thống kê hủy:**
```sql
-- Tổng số hủy
SELECT COUNT(*) FROM bookings 
WHERE payment_status IN ('cancelled', 'refunded')

-- Tổng phí hủy
SELECT SUM(cancellation_fee) FROM bookings 
WHERE payment_status IN ('cancelled', 'refunded')

-- Tổng tiền hoàn lại
SELECT SUM(refund_amount) FROM bookings 
WHERE payment_status = 'refunded'
```

---

## 🎨 UI/UX DESIGN

### **Color Scheme:**
- `cancelled`: Red/Danger (không hoàn tiền)
- `refunded`: Orange/Warning (có hoàn tiền, chưa xử lý)
- `refunded` + đã hoàn tiền: Green/Success

### **Icons:**
- Hủy: `alert-triangle` hoặc `x-circle`
- Hoàn tiền: `dollar-sign` hoặc `arrow-left`
- Thống kê: `bar-chart` hoặc `trending-down`

---

## 📝 MENU NAVIGATION

Thêm vào `common/MenuHelper.php`:
```php
[
    'title' => 'Hủy Booking',
    'icon' => 'x-circle',
    'url' => '?act=admin&module=cancellations',
    'submenu' => [
        [
            'title' => 'Danh sách hủy',
            'url' => '?act=admin&module=cancellations'
        ],
        [
            'title' => 'Thống kê',
            'url' => '?act=admin&module=cancellations&action=statistics'
        ]
    ]
]
```

---

## ✅ CHECKLIST IMPLEMENTATION

### **Phase 1: Core Features**
- [ ] Tạo `CancellationController`
- [ ] Tạo route `cancellations`
- [ ] Tạo view `index.php` (danh sách)
- [ ] Tạo view `show.php` (chi tiết)
- [ ] Thêm method `getCancelledBookings()` vào Booking model
- [ ] Test filters & search

### **Phase 2: Refund Processing**
- [ ] Tạo form xử lý hoàn tiền
- [ ] Tích hợp với Payment model
- [ ] Validate refund amount
- [ ] Test refund flow

### **Phase 3: Statistics**
- [ ] Tạo view `statistics.php`
- [ ] Thêm method `getCancellationStatistics()`
- [ ] Tạo charts (có thể dùng Chart.js)
- [ ] Export statistics

### **Phase 4: Enhancements**
- [ ] Export Excel/CSV
- [ ] Export PDF chi tiết hủy
- [ ] Email notification khi hủy
- [ ] Dashboard widget (số hủy hôm nay/tuần/tháng)

---

## 🔄 WORKFLOW XỬ LÝ HỦY

```
1. Admin hủy booking từ trang booking detail
   ↓
2. System tự động:
   - Tính phí hủy theo policy
   - Tính số tiền hoàn lại
   - Cập nhật payment_status
   - Trả lại quota
   ↓
3. Booking xuất hiện trong "Danh sách hủy"
   ↓
4. Admin xem chi tiết → Nếu có refund_amount > 0
   ↓
5. Admin xử lý hoàn tiền (tạo refund payment)
   ↓
6. System cập nhật paid_amount và ghi log
```

---

## 🚀 PRIORITY

**High Priority:**
1. Danh sách booking đã hủy (index)
2. Chi tiết booking hủy (show)
3. Xử lý hoàn tiền

**Medium Priority:**
4. Thống kê hủy booking
5. Export Excel/CSV

**Low Priority:**
6. Export PDF
7. Email notification
8. Dashboard widget

---

## 📌 NOTES

1. **Backward Compatibility:**
   - Các booking hủy cũ vẫn hiển thị được
   - Nếu không có `cancellation_date` → không hiển thị trong danh sách

2. **Security:**
   - Chỉ admin mới truy cập được
   - Validate refund amount không vượt quá `refund_amount`

3. **Performance:**
   - Index trên `payment_status` và `cancellation_date`
   - Cache statistics nếu cần

---

**Date Created:** 2025-01-08  
**Version:** 1.0

