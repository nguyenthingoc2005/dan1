# 📊 PHÂN TÍCH CHI TIẾT MODULE 1 - KHÁCH HÀNG

**Ngày phân tích:** 2024-12-XX  
**Mục tiêu:** Phân tích kỹ logic tính toán `total_spent`, `total_bookings` và các trường hợp của khách hàng

---

## 🔍 PHÂN TÍCH DATABASE SCHEMA

### Bảng `customers`

```sql
CREATE TABLE customers (
  id INT PRIMARY KEY AUTO_INCREMENT,
  customer_code VARCHAR(50) UNIQUE,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100),
  phone VARCHAR(20) NOT NULL,
  date_of_birth DATE,
  gender ENUM('male','female','other'),
  id_card VARCHAR(50),
  passport VARCHAR(50),
  nationality VARCHAR(50) DEFAULT 'Vietnam',
  address TEXT,
  customer_type ENUM('individual','group','corporate') DEFAULT 'individual',
  source ENUM('phone','email','facebook','zalo','walk_in','other'),
  special_requirements TEXT,
  notes TEXT,
  total_bookings INT DEFAULT '0',        -- ⚠️ KHÔNG ĐƯỢC UPDATE
  total_spent DECIMAL(15,2) DEFAULT '0.00',  -- ⚠️ KHÔNG ĐƯỢC UPDATE
  status ENUM('active','inactive','blacklist') DEFAULT 'active',
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```

### Bảng `bookings`

```sql
CREATE TABLE bookings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  booking_code VARCHAR(50) UNIQUE,
  tour_id INT NOT NULL,
  customer_id INT NOT NULL,  -- ⚠️ ĐÂY LÀ NGƯỜI ĐẠI DIỆN (PRIMARY CUSTOMER)
  adult_count INT DEFAULT 0,
  child_count INT DEFAULT 0,
  infant_count INT DEFAULT 0,
  final_amount DECIMAL(15,2) NOT NULL,  -- Tổng tiền booking
  paid_amount DECIMAL(15,2) DEFAULT '0.00',  -- Số tiền đã thanh toán
  payment_status ENUM('unpaid','partial','paid','refunded') DEFAULT 'unpaid',
  approval_status ENUM('pending','approved','rejected','cancelled') DEFAULT 'pending',
  ...
)
```

### Bảng `booking_customers`

```sql
CREATE TABLE booking_customers (
  id INT PRIMARY KEY AUTO_INCREMENT,
  booking_id INT NOT NULL,
  customer_id INT NOT NULL,  -- Khách hàng tham gia tour (có thể là passenger)
  age_type ENUM('adult','child','infant') DEFAULT 'adult',
  is_primary TINYINT(1) DEFAULT '0'  -- 1 = người đại diện, 0 = passenger
)
```

---

## 🎯 PHÂN TÍCH LOGIC NGHIỆP VỤ

### 1. Mối quan hệ Customer - Booking

**Có 2 loại khách hàng trong booking:**

1. **Primary Customer (Người đại diện):**
   - Lưu trong `bookings.customer_id`
   - Là người đặt tour, người thanh toán
   - Có thể có `is_primary = 1` trong `booking_customers` (nếu tham gia tour)
   - Hoặc chỉ là người đại diện (không tham gia tour)

2. **Passengers (Khách hàng tham gia tour):**
   - Lưu trong `booking_customers` với `is_primary = 0`
   - Có thể là người thân, bạn bè của primary customer
   - Có thể là chính primary customer nếu họ cũng tham gia tour

### 2. Logic tính `total_spent`

**Câu hỏi:** `total_spent` nên tính như thế nào?

**Option 1: Tổng `final_amount` của bookings mà customer là người đại diện**
```sql
SELECT SUM(final_amount) 
FROM bookings 
WHERE customer_id = :customer_id 
  AND approval_status IN ('approved', 'completed')
```
- ✅ Đơn giản, rõ ràng
- ✅ Phản ánh tổng giá trị booking mà customer đã đặt
- ❌ Không phản ánh số tiền thực tế đã thanh toán

**Option 2: Tổng `paid_amount` của bookings mà customer là người đại diện**
```sql
SELECT SUM(paid_amount) 
FROM bookings 
WHERE customer_id = :customer_id 
  AND approval_status IN ('approved', 'completed')
```
- ✅ Phản ánh số tiền thực tế đã thanh toán
- ✅ Chính xác hơn cho báo cáo tài chính
- ❌ Không bao gồm các booking chưa thanh toán

**Option 3: Tổng `final_amount` của bookings mà customer tham gia (cả primary và passenger)**
```sql
SELECT SUM(b.final_amount)
FROM bookings b
JOIN booking_customers bc ON b.id = bc.booking_id
WHERE bc.customer_id = :customer_id
  AND b.approval_status IN ('approved', 'completed')
```
- ❌ Phức tạp, có thể double-count nếu customer vừa là primary vừa là passenger
- ❌ Không phản ánh đúng vai trò của customer

**🎯 QUYẾT ĐỊNH: Dùng Option 1 hoặc Option 2**

**Khuyến nghị:** Dùng **Option 1** (`final_amount`) vì:
- Phản ánh tổng giá trị booking mà customer đã cam kết
- Đơn giản, dễ hiểu
- Phù hợp với mục đích hiển thị "Tổng chi tiêu" trong danh sách khách hàng

**Nếu cần chính xác hơn:** Có thể thêm trường `total_paid` riêng để track số tiền đã thanh toán.

### 3. Logic tính `total_bookings`

**Câu hỏi:** `total_bookings` nên tính như thế nào?

**Option 1: Số lượng bookings mà customer là người đại diện**
```sql
SELECT COUNT(*) 
FROM bookings 
WHERE customer_id = :customer_id 
  AND approval_status IN ('approved', 'completed')
```
- ✅ Đơn giản, rõ ràng
- ✅ Phản ánh số lần customer đã đặt tour (với vai trò người đại diện)

**Option 2: Số lượng bookings mà customer tham gia (cả primary và passenger)**
```sql
SELECT COUNT(DISTINCT b.id)
FROM bookings b
JOIN booking_customers bc ON b.id = bc.booking_id
WHERE bc.customer_id = :customer_id
  AND b.approval_status IN ('approved', 'completed')
```
- ❌ Phức tạp, có thể double-count
- ❌ Không phản ánh đúng vai trò của customer

**🎯 QUYẾT ĐỊNH: Dùng Option 1**

---

## ❌ VẤN ĐỀ HIỆN TẠI

### 1. `total_spent` và `total_bookings` KHÔNG ĐƯỢC UPDATE

**Hiện tại:**
- Các trường `total_spent` và `total_bookings` chỉ có giá trị mặc định (0)
- Không có logic nào update các trường này khi:
  - Booking được tạo
  - Booking được approve
  - Booking được cancel
  - Payment được thực hiện

**Hậu quả:**
- Hiển thị sai trong danh sách khách hàng
- Không thể phân loại khách hàng VIP dựa trên chi tiêu
- Không thể thống kê chính xác

### 2. Thiếu logic tính toán động

**Cần thêm:**
- Method `updateCustomerStats($customer_id)` trong `Customer` model
- Gọi method này khi:
  - Booking được approve
  - Booking được cancel
  - Booking được reject

### 3. Thiếu validation và business rules

**Cần kiểm tra:**
- Customer có thể là primary của nhiều bookings không? ✅ Có
- Customer có thể là passenger của booking khác không? ✅ Có
- Khi cancel booking, có cần trừ lại `total_spent` không? ✅ Có (nếu đã approve)

---

## ✅ GIẢI PHÁP ĐỀ XUẤT

### 1. Thêm method `updateCustomerStats()` vào `Customer` model

```php
/**
 * Update customer statistics (total_bookings, total_spent)
 * Should be called when booking status changes
 */
public function updateCustomerStats($customer_id)
{
    // Calculate total_bookings
    $bookingsSql = "SELECT COUNT(*) 
                    FROM bookings 
                    WHERE customer_id = :customer_id 
                      AND approval_status IN ('approved', 'completed')";
    $stmt = $this->pdo->prepare($bookingsSql);
    $stmt->execute(['customer_id' => $customer_id]);
    $total_bookings = $stmt->fetchColumn();

    // Calculate total_spent (sum of final_amount)
    $spentSql = "SELECT COALESCE(SUM(final_amount), 0) 
                 FROM bookings 
                 WHERE customer_id = :customer_id 
                   AND approval_status IN ('approved', 'completed')";
    $stmt = $this->pdo->prepare($spentSql);
    $stmt->execute(['customer_id' => $customer_id]);
    $total_spent = $stmt->fetchColumn();

    // Update customer
    $updateSql = "UPDATE customers 
                  SET total_bookings = :total_bookings, 
                      total_spent = :total_spent,
                      updated_at = NOW()
                  WHERE id = :customer_id";
    $stmt = $this->pdo->prepare($updateSql);
    $stmt->execute([
        'customer_id' => $customer_id,
        'total_bookings' => $total_bookings,
        'total_spent' => $total_spent
    ]);
}
```

### 2. Gọi `updateCustomerStats()` trong các điểm sau:

**a. Khi booking được approve:**
- File: `app/models/Booking.php` - method `updateStatus()` hoặc `approve()`
- Sau khi update `approval_status = 'approved'`

**b. Khi booking được cancel:**
- File: `app/models/Booking.php` - method `cancel()`
- Sau khi update `approval_status = 'cancelled'`

**c. Khi booking được reject:**
- File: `app/models/Booking.php` - method `reject()`
- Sau khi update `approval_status = 'rejected'`

### 3. Thêm method `recalculateAllCustomerStats()` để fix dữ liệu cũ

```php
/**
 * Recalculate stats for all customers (for fixing existing data)
 */
public function recalculateAllCustomerStats()
{
    $sql = "SELECT id FROM customers";
    $stmt = $this->pdo->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($customers as $customer_id) {
        $this->updateCustomerStats($customer_id);
    }
}
```

### 4. Thêm method `getCustomerStats()` để tính toán real-time (không lưu DB)

```php
/**
 * Get customer statistics (real-time calculation, không lưu DB)
 * Useful for displaying accurate stats without updating DB
 */
public function getCustomerStats($customer_id)
{
    // Total bookings
    $bookingsSql = "SELECT COUNT(*) 
                    FROM bookings 
                    WHERE customer_id = :customer_id 
                      AND approval_status IN ('approved', 'completed')";
    $stmt = $this->pdo->prepare($bookingsSql);
    $stmt->execute(['customer_id' => $customer_id]);
    $total_bookings = $stmt->fetchColumn();

    // Total spent
    $spentSql = "SELECT COALESCE(SUM(final_amount), 0) 
                 FROM bookings 
                 WHERE customer_id = :customer_id 
                   AND approval_status IN ('approved', 'completed')";
    $stmt = $this->pdo->prepare($spentSql);
    $stmt->execute(['customer_id' => $customer_id]);
    $total_spent = $stmt->fetchColumn();

    return [
        'total_bookings' => (int) $total_bookings,
        'total_spent' => (float) $total_spent
    ];
}
```

---

## 📋 CÁC TRƯỜNG HỢP CỦA KHÁCH HÀNG

### Case 1: Customer là người đại diện và cũng tham gia tour
- `bookings.customer_id` = customer.id
- `booking_customers.customer_id` = customer.id với `is_primary = 1`
- ✅ `total_spent` và `total_bookings` được tính từ `bookings.customer_id`

### Case 2: Customer là người đại diện nhưng KHÔNG tham gia tour
- `bookings.customer_id` = customer.id
- Không có record trong `booking_customers` cho customer này
- ✅ `total_spent` và `total_bookings` được tính từ `bookings.customer_id`

### Case 3: Customer là passenger (không phải người đại diện)
- `bookings.customer_id` != customer.id (là customer khác)
- `booking_customers.customer_id` = customer.id với `is_primary = 0`
- ❌ `total_spent` và `total_bookings` KHÔNG được tính (vì không phải người đại diện)

**🎯 QUYẾT ĐỊNH:** Chỉ tính `total_spent` và `total_bookings` cho customer là người đại diện (`bookings.customer_id`).

---

## 🔧 CẦN SỬA

### 1. Customer Model
- [ ] Thêm method `updateCustomerStats($customer_id)`
- [ ] Thêm method `getCustomerStats($customer_id)` (real-time)
- [ ] Thêm method `recalculateAllCustomerStats()` (fix dữ liệu cũ)

### 2. Booking Model
- [ ] Gọi `Customer::updateCustomerStats()` khi booking được approve
- [ ] Gọi `Customer::updateCustomerStats()` khi booking được cancel
- [ ] Gọi `Customer::updateCustomerStats()` khi booking được reject

### 3. Views
- [ ] Có thể dùng `getCustomerStats()` để hiển thị real-time (không cần update DB mỗi lần)
- [ ] Hoặc dùng giá trị từ DB (`total_spent`, `total_bookings`) và update định kỳ

---

## 📊 TÓM TẮT

1. **Vấn đề:** `total_spent` và `total_bookings` không được update tự động
2. **Nguyên nhân:** Thiếu logic update khi booking status thay đổi
3. **Giải pháp:** 
   - Thêm method `updateCustomerStats()` vào Customer model
   - Gọi method này khi booking status thay đổi
   - Tính toán dựa trên `bookings.customer_id` (người đại diện)
4. **Logic:** 
   - `total_spent` = SUM(`final_amount`) của bookings approved/completed
   - `total_bookings` = COUNT(*) của bookings approved/completed

---

**Kết thúc phân tích**

