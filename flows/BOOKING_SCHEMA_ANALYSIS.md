# 📊 PHÂN TÍCH SCHEMA BOOKING - THIẾU SÓT VÀ SAI SÓT

**Ngày phân tích:** 2024-12-06  
**Dựa trên:** `setup_database_complete.sql` và `FLOW_ANALYSIS_BOOKING.md`

---

## 🔴 **CÁC VẤN ĐỀ NGHIÊM TRỌNG (CRITICAL)**

### **1. THIẾU FOREIGN KEY CONSTRAINT CHO `booking_services.booking_id`** ❌

**Vấn đề:**

- Bảng `booking_services` có trường `booking_id` nhưng **KHÔNG có foreign key constraint** đến bảng `bookings`
- Điều này có thể dẫn đến:
  - Dữ liệu không nhất quán (orphan records)
  - Không thể đảm bảo referential integrity
  - Khó khăn trong việc quản lý dữ liệu

**Vị trí:** `setup_database_complete.sql` dòng 649-674

**Hiện trạng:**

```sql
CREATE TABLE IF NOT EXISTS `booking_services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,  -- ❌ KHÔNG CÓ FOREIGN KEY
  ...
  KEY `service_id` (`service_id`),
  CONSTRAINT `booking_services_ibfk_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  -- ❌ THIẾU: CONSTRAINT cho booking_id
```

**Cần sửa:**

```sql
-- Thêm foreign key constraint
ALTER TABLE `booking_services`
ADD CONSTRAINT `booking_services_ibfk_booking`
FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
```

**Hoặc sửa trong schema:**

```sql
CREATE TABLE IF NOT EXISTS `booking_services` (
  ...
  KEY `booking_id` (`booking_id`),  -- Thêm index
  CONSTRAINT `booking_services_ibfk_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ...
```

---

### **2. THIẾU INDEX CHO `booking_services.booking_id`** ⚠️

**Vấn đề:**

- Trường `booking_id` trong `booking_services` không có index
- Ảnh hưởng hiệu năng khi query theo `booking_id`

**Cần sửa:**

```sql
ALTER TABLE `booking_services`
ADD KEY `idx_booking_id` (`booking_id`);
```

---

## 🟡 **CÁC VẤN ĐỀ TRUNG BÌNH (MEDIUM)**

### **3. THIẾU TRƯỜNG `rejected_by` VÀ `rejected_at` TRONG BẢNG `bookings`** ⚠️

**Vấn đề:**

- Theo flow analysis, khi từ chối booking cần lưu `rejected_by` và `rejected_at`
- Schema hiện tại chỉ có `rejection_reason`, không có `rejected_by` và `rejected_at`
- Code trong `Booking.php` method `reject()` không set `rejected_by`

**Vị trí:** `setup_database_complete.sql` dòng 577-632

**Hiện trạng:**

```sql
CREATE TABLE IF NOT EXISTS `bookings` (
  ...
  `approved_by` int DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  -- ❌ THIẾU: rejected_by, rejected_at
```

**Cần thêm:**

```sql
ALTER TABLE `bookings`
ADD COLUMN `rejected_by` int DEFAULT NULL AFTER `rejection_reason`,
ADD COLUMN `rejected_at` timestamp NULL DEFAULT NULL AFTER `rejected_by`,
ADD KEY `idx_rejected_by` (`rejected_by`),
ADD CONSTRAINT `bookings_ibfk_rejected_by` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
```

---

### **4. THIẾU TRƯỜNG `quota` VÀ `booked_seats` TRONG BẢNG `bookings`** ⚠️

**Vấn đề:**

- Schema có trường `quota` và `booked_seats` trong bảng `bookings` (dòng 589-590)
- Nhưng theo flow, các trường này không cần thiết vì đã có trong `tour_schedules`
- Có thể gây nhầm lẫn hoặc dữ liệu không nhất quán

**Vị trí:** `setup_database_complete.sql` dòng 589-590

**Hiện trạng:**

```sql
CREATE TABLE IF NOT EXISTS `bookings` (
  ...
  `quota` int DEFAULT NULL,  -- ⚠️ Có thể không cần thiết
  `booked_seats` int DEFAULT NULL,  -- ⚠️ Có thể không cần thiết
```

**Khuyến nghị:**

- Nếu không sử dụng, nên xóa 2 trường này để tránh nhầm lẫn
- Hoặc document rõ ràng mục đích sử dụng

---

### **5. THIẾU VALIDATION CHO `booking_status_history.old_status` VÀ `new_status`** ⚠️

**Vấn đề:**

- Schema định nghĩa `old_status` và `new_status` là `VARCHAR(50)` (dòng 680-681)
- Nhưng trong code, các status là ENUM (`pending`, `approved`, `rejected`, `cancelled`, `unpaid`, `partial`, `paid`, `refunded`)
- Không có validation để đảm bảo giá trị hợp lệ

**Vị trí:** `setup_database_complete.sql` dòng 677-691

**Hiện trạng:**

```sql
CREATE TABLE IF NOT EXISTS `booking_status_history` (
  ...
  `old_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
```

**Khuyến nghị:**

- Giữ nguyên VARCHAR để linh hoạt (có thể lưu cả `approval_status` và `payment_status`)
- Hoặc tách thành 2 bảng riêng: `booking_approval_history` và `booking_payment_history`

---

## 🟢 **CÁC VẤN ĐỀ NHỎ (LOW)**

### **6. THIẾU INDEX CHO CÁC TRƯỜNG THƯỜNG QUERY** ⚠️

**Các index nên thêm:**

```sql
-- booking_services
ALTER TABLE `booking_services`
ADD KEY `idx_booking_service_date` (`booking_id`, `service_date`),
ADD KEY `idx_payment_status` (`payment_status`);

-- bookings
ALTER TABLE `bookings`
ADD KEY `idx_tour_schedule_id` (`tour_schedule_id`),
ADD KEY `idx_start_date_end_date` (`start_date`, `end_date`);

-- booking_status_history
ALTER TABLE `booking_status_history`
ADD KEY `idx_booking_status` (`booking_id`, `new_status`),
ADD KEY `idx_created_at` (`created_at`);
```

---

### **7. THIẾU DEFAULT VALUE CHO MỘT SỐ TRƯỜNG** ⚠️

**Vấn đề:**

- Một số trường có thể cần default value để tránh NULL

**Khuyến nghị:**

```sql
-- booking_services.quantity nên có DEFAULT 1
ALTER TABLE `booking_services`
MODIFY COLUMN `quantity` int NOT NULL DEFAULT 1;

-- booking_services.unit_price và total_price nên có DEFAULT 0.00
ALTER TABLE `booking_services`
MODIFY COLUMN `unit_price` decimal(15,2) DEFAULT 0.00,
MODIFY COLUMN `total_price` decimal(15,2) DEFAULT 0.00;
```

---

## ✅ **CÁC ĐIỂM ĐÚNG (GOOD PRACTICES)**

### **1. Foreign Key Constraints đầy đủ (trừ booking_services.booking_id)**

- ✅ `booking_customers.booking_id` → `bookings.id` (CASCADE)
- ✅ `payments.booking_id` → `bookings.id` (CASCADE)
- ✅ `invoices.booking_id` → `bookings.id` (CASCADE)
- ✅ `refunds.booking_id` → `bookings.id` (CASCADE)
- ✅ `journals.booking_id` → `bookings.id`
- ✅ `customer_checkins.booking_id` → `bookings.id`

### **2. Cấu trúc bảng hợp lý**

- ✅ Bảng `bookings` có đầy đủ các trường cần thiết
- ✅ Bảng `booking_customers` quản lý hành khách đúng cách
- ✅ Bảng `booking_services` quản lý dịch vụ riêng biệt

### **3. Indexes cơ bản đã có**

- ✅ Index cho `bookings.tour_id`
- ✅ Index cho `bookings.customer_id`
- ✅ Index cho `bookings.approval_status`
- ✅ Index cho `bookings.payment_status`

---

## 📋 **CHECKLIST SỬA LỖI**

### **🔴 Critical (Phải sửa ngay):**

- [ ] **1. Thêm FOREIGN KEY constraint cho `booking_services.booking_id`**

  ```sql
  ALTER TABLE `booking_services`
  ADD CONSTRAINT `booking_services_ibfk_booking`
  FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
  ```

- [ ] **2. Thêm INDEX cho `booking_services.booking_id`**
  ```sql
  ALTER TABLE `booking_services`
  ADD KEY `idx_booking_id` (`booking_id`);
  ```

### **🟡 Medium (Nên sửa):**

- [ ] **3. Thêm `rejected_by` và `rejected_at` vào bảng `bookings`**

  ```sql
  ALTER TABLE `bookings`
  ADD COLUMN `rejected_by` int DEFAULT NULL AFTER `rejection_reason`,
  ADD COLUMN `rejected_at` timestamp NULL DEFAULT NULL AFTER `rejected_by`,
  ADD KEY `idx_rejected_by` (`rejected_by`),
  ADD CONSTRAINT `bookings_ibfk_rejected_by` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
  ```

- [ ] **4. Xem xét xóa `quota` và `booked_seats` từ bảng `bookings`** (nếu không dùng)

### **🟢 Low (Có thể sửa sau):**

- [ ] **5. Thêm các index bổ sung** (xem mục 6 ở trên)
- [ ] **6. Thêm default values** (xem mục 7 ở trên)

---

## 🔧 **SCRIPT SỬA LỖI TỔNG HỢP**

```sql
-- ==============================================================================
-- SCRIPT SỬA LỖI SCHEMA BOOKING
-- ==============================================================================

-- 1. Thêm FOREIGN KEY constraint cho booking_services.booking_id
ALTER TABLE `booking_services`
ADD CONSTRAINT `booking_services_ibfk_booking`
FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

-- 2. Thêm INDEX cho booking_services.booking_id (nếu chưa có)
ALTER TABLE `booking_services`
ADD KEY `idx_booking_id` (`booking_id`);

-- 3. Thêm rejected_by và rejected_at vào bookings
ALTER TABLE `bookings`
ADD COLUMN `rejected_by` int DEFAULT NULL AFTER `rejection_reason`,
ADD COLUMN `rejected_at` timestamp NULL DEFAULT NULL AFTER `rejected_by`,
ADD KEY `idx_rejected_by` (`rejected_by`),
ADD CONSTRAINT `bookings_ibfk_rejected_by` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- 4. Thêm các index bổ sung cho booking_services
ALTER TABLE `booking_services`
ADD KEY `idx_booking_service_date` (`booking_id`, `service_date`),
ADD KEY `idx_payment_status` (`payment_status`);

-- 5. Thêm index cho bookings
ALTER TABLE `bookings`
ADD KEY `idx_tour_schedule_id` (`tour_schedule_id`),
ADD KEY `idx_start_date_end_date` (`start_date`, `end_date`);

-- 6. Thêm index cho booking_status_history
ALTER TABLE `booking_status_history`
ADD KEY `idx_booking_status` (`booking_id`, `new_status`),
ADD KEY `idx_created_at` (`created_at`);

-- 7. Thêm default values cho booking_services
ALTER TABLE `booking_services`
MODIFY COLUMN `quantity` int NOT NULL DEFAULT 1,
MODIFY COLUMN `unit_price` decimal(15,2) DEFAULT 0.00,
MODIFY COLUMN `total_price` decimal(15,2) DEFAULT 0.00;
```

---

## 📊 **TÓM TẮT**

| #   | Vấn đề                                            | Mức độ      | Trạng thái     | Ưu tiên    |
| --- | ------------------------------------------------- | ----------- | -------------- | ---------- |
| 1   | Thiếu FK constraint `booking_services.booking_id` | 🔴 Critical | ❌ Thiếu       | **CAO**    |
| 2   | Thiếu index `booking_services.booking_id`         | 🔴 Critical | ❌ Thiếu       | **CAO**    |
| 3   | Thiếu `rejected_by`, `rejected_at`                | 🟡 Medium   | ❌ Thiếu       | Trung bình |
| 4   | Trường `quota`, `booked_seats` không cần thiết    | 🟡 Medium   | ⚠️ Có thể xóa  | Trung bình |
| 5   | Validation cho `booking_status_history`           | 🟡 Medium   | ⚠️ OK          | Thấp       |
| 6   | Thiếu index bổ sung                               | 🟢 Low      | ⚠️ Có thể thêm | Thấp       |
| 7   | Thiếu default values                              | 🟢 Low      | ⚠️ Có thể thêm | Thấp       |

---

**Ngày tạo:** 2024-12-06  
**Người phân tích:** AI Assistant  
**Version:** 1.0
