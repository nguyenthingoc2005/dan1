# ✅ QUY TẮC VALIDATION BOOKING

**Ngày tạo:** 2024-12-06  
**Dựa trên:** Flow analysis và Database schema

---

## 🔴 **VALIDATION KHI TẠO BOOKING**

### **1. Customer Validation**

```php
✅ customer_id: REQUIRED
   - Phải tồn tại trong bảng customers
   - customer.status = 'active'

✅ Nếu tạo customer mới:
   - full_name: REQUIRED (không rỗng)
   - phone: REQUIRED (không rỗng)
   - email: OPTIONAL (nếu có, phải đúng format)
```

### **2. Tour Validation**

```php
✅ tour_id: REQUIRED
   - Phải tồn tại trong bảng tours
   - tour.status = 'active' (đang hoạt động)
   - tour.approval_status = 'approved' (đã được duyệt)
```

### **3. Schedule Validation**

```php
✅ Với Public Tour:
   - Phải có schedule (tour_schedules)
   - schedule.status = 'open' hoặc 'pending'
   - schedule.quota - schedule.booked >= total_participants

✅ Với Custom Tour:
   - Có thể tự động tạo schedule nếu chưa có
   - Tổng số người không vượt max_participants của tour
```

### **4. Deadline Validation** ⏰

```php
✅ start_date >= today + booking_deadline_days
   - booking_deadline_days từ tour.booking_deadline_days (default: 1)
   - Nếu vi phạm → Error: "Không thể đặt booking. Phải đặt trước X ngày so với ngày khởi hành."

Ví dụ:
- Hôm nay: 10/12/2024
- booking_deadline_days: 1
- start_date tối thiểu: 11/12/2024
- start_date = 10/12/2024 → ❌ ERROR
```

### **5. Participant Validation**

```php
✅ adult_count >= 1 (bắt buộc có ít nhất 1 người lớn)
✅ child_count >= 0
✅ infant_count >= 0
✅ total_participants = adult_count + child_count + infant_count > 0
✅ total_participants <= (quota - booked) (không vượt quota)
```

### **6. Price Validation**

```php
✅ total_amount > 0 (tổng tiền phải lớn hơn 0)
✅ discount_amount <= total_amount (giảm giá không được lớn hơn tổng tiền)
✅ final_amount = total_amount - discount_amount >= 0
✅ deposit_amount <= final_amount (tiền cọc không được lớn hơn số tiền sau giảm)
✅ remaining_amount = final_amount - deposit_amount >= 0
```

### **7. Duplicate Validation**

```php
✅ Không được trùng booking:
   - Cùng customer_id
   - Cùng tour_id
   - Cùng start_date
   - approval_status NOT IN ('cancelled', 'rejected')
   
   → Nếu trùng → Error: "Khách hàng đã có booking cho tour này vào ngày này"
```

### **8. Passenger Validation** (Khi thêm khách)

```php
✅ Tổng số khách trong booking_customers = adult_count + child_count + infant_count
✅ Phải có đúng 1 khách có is_primary = 1 (khách chính)
✅ age_type phải khớp:
   - Số adult trong booking_customers = adult_count
   - Số child trong booking_customers = child_count
   - Số infant trong booking_customers = infant_count
✅ Không được trùng customer trong cùng booking
```

---

## 🟡 **VALIDATION KHI THÊM DỊCH VỤ**

### **1. Booking Validation**

```php
✅ booking_id: REQUIRED
   - Phải tồn tại
   - approval_status != 'cancelled' (chưa hủy)
   - days_until_start >= 1 (deadline: còn >= 1 ngày đến ngày khởi hành)
```

### **2. Service Validation**

```php
✅ service_id: REQUIRED
   - Phải tồn tại trong bảng services
   - service.status = 'active'

✅ service_provider_id: OPTIONAL
   - Nếu có, phải tồn tại trong bảng service_providers
   - Phải liên kết với service_id (nếu có)
```

### **3. Price & Quantity Validation**

```php
✅ quantity > 0 (số lượng phải lớn hơn 0)
✅ unit_price >= 0 (đơn giá không được âm)
✅ total_price = quantity × unit_price (tự động tính)
```

### **4. Date Validation**

```php
✅ Nếu có from_date và to_date:
   - to_date >= from_date (ngày kết thúc phải sau ngày bắt đầu)
   - from_date >= booking.start_date (không được trước ngày khởi hành)
```

---

## 🟢 **VALIDATION KHI HỦY BOOKING**

### **1. Booking Status Validation**

```php
✅ booking tồn tại
✅ approval_status != 'cancelled' (chưa hủy)
✅ approval_status != 'rejected' (chưa từ chối)
✅ start_date >= today (chưa khởi hành)
```

### **2. Cancellation Policy Validation**

```php
✅ Tự động tìm policy phù hợp:
   - days_before <= số ngày trước ngày khởi hành
   - status = 'active'
   - ORDER BY days_before DESC LIMIT 1
   
✅ Nếu không có policy → fee_percentage = 0 (mặc định)
```

### **3. Refund Calculation**

```php
✅ cancellation_fee = final_amount × (fee_percentage / 100)
✅ refund_amount = max(0, paid_amount - cancellation_fee)
✅ Nếu refund_amount > 0 → Tạo record refunds (nếu có bảng)
```

---

## 📋 **VALIDATION SUMMARY TABLE**

| Validation | Khi nào | Mức độ | Error Message |
|------------|---------|--------|---------------|
| customer_id required | Tạo booking | 🔴 Critical | "Vui lòng chọn hoặc tạo khách hàng" |
| tour active & approved | Tạo booking | 🔴 Critical | "Tour không đang hoạt động hoặc chưa được duyệt" |
| deadline >= 1 ngày | Tạo booking | 🔴 Critical | "Không thể đặt booking. Phải đặt trước X ngày" |
| quota available | Tạo booking | 🔴 Critical | "Số lượng người tham gia vượt quá khả dụng" |
| adult_count >= 1 | Tạo booking | 🔴 Critical | "Phải có ít nhất 1 người lớn" |
| total_amount > 0 | Tạo booking | 🔴 Critical | "Tổng tiền tour phải lớn hơn 0" |
| duplicate booking | Tạo booking | 🟡 Medium | "Khách hàng đã có booking cho tour này vào ngày này" |
| days_until_start >= 1 | Thêm dịch vụ | 🟡 Medium | "Không thể thêm/sửa. Booking này khởi hành trong vòng 1 ngày" |
| quantity > 0 | Thêm dịch vụ | 🟡 Medium | "Số lượng phải >= 1" |
| booking not cancelled | Hủy booking | 🔴 Critical | "Booking đã được hủy trước đó" |
| start_date >= today | Hủy booking | 🔴 Critical | "Không thể hủy booking đã khởi hành" |

---

## 🔧 **CODE VALIDATION EXAMPLES**

### **Example 1: Validate Tạo Booking**

```php
// Deadline validation
$deadline_days = (int) ($tour['booking_deadline_days'] ?? 1);
$today = date('Y-m-d');
$minStartDate = date('Y-m-d', strtotime("+{$deadline_days} days"));
if ($_POST['start_date'] < $minStartDate) {
    throw new Exception("Không thể đặt booking. Phải đặt trước {$deadline_days} ngày so với ngày khởi hành. (Hôm nay: {$today}, Ngày khởi hành tối thiểu: {$minStartDate})");
}

// Tour validation
if ($tour['status'] !== 'active') {
    throw new Exception("Tour không đang hoạt động. Không thể tạo booking.");
}
if ($tour['approval_status'] !== 'approved') {
    throw new Exception("Tour chưa được duyệt. Không thể tạo booking.");
}

// Quota validation
$available = $schedule['quota'] - $schedule['booked'];
if ($totalParticipants > $available) {
    throw new Exception("Số lượng người tham gia vượt quá khả dụng (Còn $available chỗ).");
}

// Participant validation
if ($adult < 1) {
    throw new Exception("Phải có ít nhất 1 người lớn");
}

// Price validation
if ($total_amount <= 0) {
    throw new Exception("Tổng tiền tour phải lớn hơn 0");
}
if ($discount > $total_amount) {
    throw new Exception("Số tiền giảm giá ($discount) không được lớn hơn tổng tiền ($total_amount)");
}
if ($deposit > $final_amount) {
    throw new Exception("Tiền cọc ($deposit) không được lớn hơn số tiền sau giảm ($final_amount)");
}

// Duplicate validation
$duplicate = $this->bookingModel->checkDuplicate($customer_id, $tour_id, $start_date);
if ($duplicate) {
    throw new Exception("Khách hàng đã có booking cho tour này vào ngày này (Booking #{$duplicate['booking_code']})");
}
```

### **Example 2: Validate Thêm Dịch vụ**

```php
// Booking validation
$booking = $this->bookingModel->getById($booking_id);
if (!$booking) {
    throw new Exception("Booking không tồn tại.");
}
if ($booking['approval_status'] === 'cancelled') {
    throw new Exception("Không thể thêm dịch vụ vào booking đã hủy.");
}

// Deadline validation
$startDate = new DateTime($booking['start_date']);
$today = new DateTime();
$daysUntilStart = (int) $today->diff($startDate)->format('%a');
if ($daysUntilStart < 1) {
    throw new Exception("Không thể thêm/sửa. Booking này khởi hành trong vòng 1 ngày hoặc đã khởi hành.");
}

// Service validation
$service = $this->serviceModel->getById($service_id);
if (!$service || $service['status'] !== 'active') {
    throw new Exception("Dịch vụ không tồn tại hoặc không hoạt động.");
}

// Quantity & Price validation
if ($quantity < 1) {
    throw new Exception("Số lượng phải >= 1");
}
if ($unit_price < 0) {
    throw new Exception("Đơn giá phải >= 0");
}
```

### **Example 3: Validate Hủy Booking**

```php
// Booking validation
$booking = $this->bookingModel->getById($booking_id);
if (!$booking) {
    throw new Exception("Booking không tồn tại.");
}
if ($booking['approval_status'] === 'cancelled') {
    throw new Exception("Booking đã được hủy trước đó");
}

// Start date validation
$startDate = new DateTime($booking['start_date']);
$today = new DateTime();
if ($startDate < $today) {
    throw new Exception("Không thể hủy booking đã khởi hành");
}

// Reason validation
if (empty($reason)) {
    throw new Exception("Vui lòng nhập lý do hủy");
}
```

---

**Ngày tạo:** 2024-12-06  
**Version:** 1.0

