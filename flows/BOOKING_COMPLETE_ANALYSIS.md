# 📋 PHÂN TÍCH HOÀN CHỈNH: BOOKING FLOW & DATA STRUCTURE

**Ngày tạo:** 2024-12-06  
**Dựa trên:** Database schema, Flow analysis, và Code hiện tại

---

## 🎯 **1. KHI NÀO TẠO BOOKING ĐƯỢC? (VALIDATION RULES)**

### **1.1. Điều kiện Tour & Schedule**

✅ **Tour phải:**
- `status = 'active'` (đang hoạt động)
- `approval_status = 'approved'` (đã được duyệt)

✅ **Schedule phải:**
- `status = 'open'` hoặc `'pending'` (còn nhận booking)
- Còn chỗ: `quota - booked >= số người đặt`
- Với **Public Tour**: Bắt buộc phải có schedule trước
- Với **Custom Tour**: Có thể tự động tạo schedule nếu chưa có

### **1.2. Deadline Đặt Booking** ⏰

✅ **Quy định:** Phải đặt trước **1 ngày** so với ngày khởi hành

**Validation:**
```php
$deadline_days = $tour['booking_deadline_days'] ?? 1; // Mặc định 1 ngày
$today = date('Y-m-d');
$minStartDate = date('Y-m-d', strtotime("+{$deadline_days} days"));

if ($start_date < $minStartDate) {
    throw new Exception("Không thể đặt booking. Phải đặt trước {$deadline_days} ngày so với ngày khởi hành.");
}
```

**Ví dụ:**
- Hôm nay: 10/12/2024
- Tour khởi hành: 11/12/2024 → ✅ Có thể đặt (còn 1 ngày)
- Tour khởi hành: 10/12/2024 → ❌ Không thể đặt (chỉ còn 0 ngày)

### **1.3. Điều kiện Số Người**

✅ **Bắt buộc:**
- `adult_count >= 1` (phải có ít nhất 1 người lớn)
- `child_count >= 0`
- `infant_count >= 0`
- `total_participants = adult_count + child_count + infant_count > 0`

✅ **Quota:**
- `total_participants <= (quota - booked)` (không vượt quá số chỗ còn lại)
- Với Custom Tour: Có thể tự động tăng quota (nhưng không vượt `max_participants`)

### **1.4. Điều kiện Khách hàng**

✅ **Bắt buộc:**
- Phải có `customer_id` (khách hàng đại diện)
- Không được trùng booking (cùng `customer_id`, `tour_id`, `start_date`)

### **1.5. Điều kiện Giá**

✅ **Bắt buộc:**
- `total_amount > 0` (tổng tiền phải lớn hơn 0)
- `discount_amount <= total_amount` (giảm giá không được lớn hơn tổng tiền)
- `deposit_amount <= final_amount` (tiền cọc không được lớn hơn số tiền sau giảm)

---

## 👥 **2. DỮ LIỆU KHÁCH HÀNG (CUSTOMER DATA)**

### **2.1. Bảng `customers` - Các Trường**

| Trường | Loại | Bắt buộc | Mô tả | Validation |
|--------|------|----------|-------|------------|
| `id` | INT | ✅ | Primary key | AUTO_INCREMENT |
| `customer_code` | VARCHAR(50) | ❌ | Mã khách hàng | UNIQUE, auto-generate |
| `full_name` | VARCHAR(100) | ✅ | Họ và tên | NOT NULL |
| `email` | VARCHAR(100) | ❌ | Email | |
| `phone` | VARCHAR(20) | ✅ | Số điện thoại | NOT NULL |
| `date_of_birth` | DATE | ❌ | Ngày sinh | Format: YYYY-MM-DD |
| `gender` | ENUM | ❌ | Giới tính | 'male', 'female', 'other' |
| `id_card` | VARCHAR(50) | ❌ | CMND/CCCD | |
| `passport` | VARCHAR(50) | ❌ | Hộ chiếu | |
| `nationality` | VARCHAR(50) | ❌ | Quốc tịch | Default: 'Vietnam' |
| `address` | TEXT | ❌ | Địa chỉ | |
| `customer_type` | ENUM | ❌ | Loại khách | 'individual', 'group', 'corporate' |
| `source` | ENUM | ❌ | Nguồn | 'phone', 'email', 'facebook', 'zalo', 'walk_in', 'other' |
| `special_requirements` | TEXT | ❌ | Yêu cầu đặc biệt | |
| `notes` | TEXT | ❌ | Ghi chú | |
| `total_bookings` | INT | ❌ | Tổng số booking | Default: 0 (auto-update) |
| `total_spent` | DECIMAL(15,2) | ❌ | Tổng tiền đã chi | Default: 0.00 (auto-update) |
| `status` | ENUM | ❌ | Trạng thái | 'active', 'inactive', 'blacklist' |
| `created_by` | INT | ❌ | Người tạo | FK → users |
| `created_at` | TIMESTAMP | ❌ | Ngày tạo | AUTO |
| `updated_at` | TIMESTAMP | ❌ | Ngày cập nhật | AUTO |

### **2.2. Bảng `booking_customers` - Khách hàng trong Booking**

| Trường | Loại | Bắt buộc | Mô tả | Validation |
|--------|------|----------|-------|------------|
| `id` | INT | ✅ | Primary key | AUTO_INCREMENT |
| `booking_id` | INT | ✅ | Foreign key → bookings | NOT NULL, FK |
| `customer_id` | INT | ✅ | Foreign key → customers | NOT NULL, FK |
| `age_type` | ENUM | ✅ | Phân loại | 'adult', 'child', 'infant' |
| `is_primary` | TINYINT(1) | ✅ | Khách chính | DEFAULT 0 (chỉ 1 = 1) |

**Business Rules:**
- Tổng số records = `adult_count + child_count + infant_count`
- Phải có đúng 1 record có `is_primary = 1` (khách hàng đại diện)
- `age_type` phải khớp với `adult_count`, `child_count`, `infant_count`

---

## 📊 **3. FILE EXCEL/CSV IMPORT - CÁC CỘT**

### **3.1. Template File: `public/templates/customer_import_template.csv`**

**Header (Row 1):**
```
Họ tên,SĐT,Email,Giới tính,Loại
```

**Data (Row 2+):**
```
Nguyễn Văn A,0901234567,nguyenvana@gmail.com,Nam,Người lớn
Trần Thị B,0912345678,tranthib@gmail.com,Nữ,Người lớn
Lê Văn C,0923456789,levanc@gmail.com,Nam,Trẻ em
```

### **3.2. Các Cột Hỗ trợ (Field Mapping)**

| Cột Excel/CSV | Field Database | Bắt buộc | Mô tả | Ví dụ |
|--------------|----------------|----------|-------|-------|
| **Họ tên** | `full_name` | ✅ | Tên đầy đủ | "Nguyễn Văn A" |
| **SĐT** | `phone` | ✅ | Số điện thoại | "0901234567" |
| **Email** | `email` | ❌ | Email | "nguyenvana@gmail.com" |
| **Ngày sinh** | `date_of_birth` | ❌ | Ngày sinh | "1990-01-15" hoặc "15/01/1990" |
| **Giới tính** | `gender` | ❌ | Giới tính | "Nam", "Nữ", "Male", "Female" |
| **Loại** | `age_type` | ❌ | Phân loại (cho booking) | "Người lớn", "Trẻ em", "Em bé" |
| **Địa chỉ** | `address` | ❌ | Địa chỉ | "123 Đường ABC, Quận 1" |
| **CMND/CCCD** | `id_card` | ❌ | Số CMND/CCCD | "123456789" |
| **Passport** | `passport` | ❌ | Số hộ chiếu | "A12345678" |

### **3.3. Các Tên Cột Được Hỗ trợ (Aliases)**

**Họ tên:**
- `họ tên`, `ho ten`, `ten`, `name`, `fullname`, `khách hàng`, `khach hang`

**SĐT:**
- `sđt`, `sdt`, `phone`, `điện thoại`, `dien thoai`, `tel`, `mobile`, `so dien thoai`, `số điện thoại`

**Email:**
- `email`, `e-mail`, `mail`

**Ngày sinh:**
- `ngày sinh`, `ngay sinh`, `dob`, `birthday`, `sinh ngày`

**Giới tính:**
- `giới tính`, `gioi tinh`, `gender`, `sex`, `nam/nữ`

**Loại (Age Type):**
- `loại`, `loai`, `type`, `age_type`, `tuổi`, `tuoi`, `người lớn`, `trẻ em`, `em bé`

**Địa chỉ:**
- `địa chỉ`, `dia chi`, `address`, `địa điểm`

**CMND/CCCD:**
- `cmnd`, `cccd`, `id card`, `chứng minh`

**Passport:**
- `passport`, `hộ chiếu`, `ho chieu`

### **3.4. Format File**

✅ **Hỗ trợ:**
- CSV (`.csv`) - Delimiter: `,` hoặc `;` (tự động detect)
- Excel (`.xlsx`, `.xls`) - Cần export sang CSV hoặc dùng PhpSpreadsheet

✅ **Encoding:**
- UTF-8 (có thể có BOM - sẽ tự động loại bỏ)

✅ **Validation:**
- Row 1: Header (bắt buộc)
- Row 2+: Data
- Bỏ qua row trống
- Tối thiểu phải có `full_name` hoặc `phone`

### **3.5. Xử lý Đặc biệt**

**Phone Number:**
- Tự động loại bỏ khoảng trắng, dấu gạch ngang
- Xử lý format Excel (số, scientific notation)
- Chuyển `84xxxxxxxxx` → `0xxxxxxxxx`
- Thêm số 0 đầu nếu thiếu (9 chữ số → 10 chữ số)

**Date:**
- Hỗ trợ nhiều format: `Y-m-d`, `d/m/Y`, `d-m-Y`, `Y/m/d`, `d.m.Y`
- Tự động parse bằng `strtotime()` nếu format không khớp

**Gender:**
- `Nam`, `Male`, `M`, `1` → `male`
- `Nữ`, `Nu`, `Female`, `F`, `2` → `female`
- Khác → `other`

---

## 💰 **4. TIỀN DỊCH VỤ NGOÀI (BOOKING SERVICES)**

### **4.1. Cấu trúc Dữ liệu**

**Bảng `booking_services`:**

| Trường | Loại | Bắt buộc | Mô tả |
|--------|------|----------|-------|
| `id` | INT | ✅ | Primary key |
| `booking_id` | INT | ✅ | Foreign key → bookings |
| `service_id` | INT | ✅ | Foreign key → services |
| `service_provider_id` | INT | ❌ | Foreign key → service_providers |
| `service_name` | VARCHAR(200) | ❌ | Tên dịch vụ (snapshot) |
| `quantity` | INT | ✅ | Số lượng | DEFAULT 1 |
| `unit` | VARCHAR(50) | ❌ | Đơn vị | "bữa", "đêm", "vé" |
| `unit_price` | DECIMAL(15,2) | ✅ | Đơn giá/đơn vị | > 0 |
| `total_price` | DECIMAL(15,2) | ✅ | Tổng tiền | = quantity × unit_price |
| `service_date` | DATE | ❌ | Ngày sử dụng dịch vụ |
| `from_date` | DATE | ❌ | Từ ngày (dịch vụ theo khoảng) |
| `to_date` | DATE | ❌ | Đến ngày (dịch vụ theo khoảng) |
| `payment_status` | ENUM | ✅ | Trạng thái thanh toán | 'pending', 'partial', 'paid' |
| `paid_amount` | DECIMAL(15,2) | ✅ | Số tiền đã trả | DEFAULT 0.00 |
| `notes` | TEXT | ❌ | Ghi chú |
| `created_by` | INT | ❌ | Người tạo |
| `created_at` | TIMESTAMP | ❌ | Ngày tạo |

### **4.2. Cách Tính Tiền**

**Công thức:**
```
total_price = quantity × unit_price
```

**Ví dụ:**
- Dịch vụ: "Phòng khách sạn"
- Số lượng: 2 đêm
- Đơn giá: 500,000đ/đêm
- Tổng tiền: 2 × 500,000 = 1,000,000đ

### **4.3. Quan hệ với Booking**

⚠️ **Lưu ý quan trọng:**

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

### **4.4. Deadline Thêm/Sửa Dịch vụ**

✅ **Quy tắc:** Không được thêm/sửa dịch vụ nếu còn **< 1 ngày** đến ngày khởi hành

**Validation:**
```php
$daysUntilStart = (strtotime($start_date) - strtotime($today)) / (60 * 60 * 24);
if ($daysUntilStart < 1) {
    throw new Exception("Không thể thêm/sửa. Booking này khởi hành trong vòng 1 ngày hoặc đã khởi hành.");
}
```

---

## ❌ **5. HỦY BOOKING (CANCELLATION)**

### **5.1. Điều kiện Hủy**

✅ **Có thể hủy nếu:**
- `approval_status = 'pending'` (chờ duyệt)
- `approval_status = 'approved'` (đã duyệt)
- Booking chưa khởi hành (`start_date >= today`)

❌ **Không thể hủy nếu:**
- `approval_status = 'cancelled'` (đã hủy rồi)
- `approval_status = 'rejected'` (đã từ chối)
- Booking đã khởi hành (`start_date < today`)

### **5.2. Tính Phí Hủy**

**Công thức:**
```
days_before = start_date - today
fee_percentage = cancellation_policy.fee_percentage (theo số ngày)
cancellation_fee = final_amount × (fee_percentage / 100)
refund_amount = paid_amount - cancellation_fee
```

**Ví dụ:**
- Tổng tiền: 3,750,000đ
- Đã thanh toán: 1,125,000đ
- Hủy trước 3 ngày → Phí 30%
- Phí hủy: 3,750,000 × 30% = 1,125,000đ
- Hoàn tiền: 1,125,000 - 1,125,000 = 0đ

### **5.3. Chính sách Hủy (Cancellation Policies)**

**Bảng `cancellation_policies`:**

| Trường | Loại | Mô tả |
|--------|------|-------|
| `id` | INT | Primary key |
| `name` | VARCHAR(100) | Tên chính sách |
| `description` | TEXT | Mô tả |
| `days_before` | INT | Số ngày trước ngày khởi hành |
| `fee_percentage` | DECIMAL(5,2) | % phí hủy |
| `status` | ENUM | 'active', 'inactive' |

**Logic tìm chính sách:**
```sql
SELECT * FROM cancellation_policies 
WHERE days_before <= :days_before 
AND status = 'active'
ORDER BY days_before DESC 
LIMIT 1
```

**Ví dụ chính sách:**
- Hủy trước 7 ngày: Phí 0%
- Hủy trước 3 ngày: Phí 30%
- Hủy trước 1 ngày: Phí 50%
- Hủy trong ngày: Phí 100%

### **5.4. Quy trình Hủy**

**Bước 1: Validate**
- Kiểm tra booking tồn tại
- Kiểm tra trạng thái có thể hủy
- Kiểm tra chưa khởi hành

**Bước 2: Tính Phí**
- Tính số ngày trước ngày khởi hành
- Tìm chính sách hủy phù hợp
- Tính phí hủy và hoàn tiền

**Bước 3: Cập nhật Database**
```sql
UPDATE bookings SET 
    approval_status = 'cancelled',
    cancellation_date = NOW(),
    cancellation_reason = :reason,
    cancellation_policy_id = :policy_id,
    cancellation_fee = :fee,
    refund_amount = :refund,
    payment_status = :payment_status (nếu có refund)
WHERE id = :id
```

**Bước 4: Trả lại Quota**
```sql
UPDATE tour_schedules 
SET booked = booked - :total_participants 
WHERE id = :schedule_id
```

**Bước 5: Log History**
- Tạo record trong `booking_status_history`
- Ghi lại lý do hủy, phí hủy

**Bước 6: Hoàn tiền (nếu có)**
- Nếu `refund_amount > 0`:
  - Tạo record trong `refunds` (nếu có bảng)
  - Hoặc update `payment_status = 'refunded'`

### **5.5. Cập nhật Customer Stats**

Sau khi hủy, tự động cập nhật:
- `customers.total_bookings` (giảm)
- `customers.total_spent` (giảm)

---

## 📊 **6. TỔNG HỢP: FLOW TẠO BOOKING**

### **6.1. Quy trình Tạo Booking**

```
1. Chọn Khách hàng (có sẵn hoặc tạo mới)
   ↓
2. Chọn Tour & Schedule
   - Validate: tour active & approved
   - Validate: schedule open/pending & còn chỗ
   - Validate: deadline >= 1 ngày
   ↓
3. Nhập Số người (adult/child/infant)
   - Validate: adult >= 1
   - Validate: total <= quota - booked
   - Auto tính: total_amount
   ↓
4. Thêm Khách hàng vào Booking (Optional)
   - Import từ Excel hoặc nhập thủ công
   - Validate: tổng số khách = adult + child + infant
   ↓
5. Thêm Dịch vụ (Optional)
   - Chọn dịch vụ & nhà cung cấp
   - Nhập số lượng & đơn giá
   - Auto tính: total_price = quantity × unit_price
   ↓
6. Tính Giá & Đặt cọc
   - total_amount = (adult_price × adult) + (child_price × child) + (infant_price × infant)
   - discount_amount (optional)
   - final_amount = total_amount - discount_amount
   - deposit_amount (optional, default = final_amount × deposit_percentage)
   - remaining_amount = final_amount - deposit_amount
   ↓
7. Thông tin Bổ sung
   - source (phone/email/facebook/zalo/walk_in/other)
   - special_requests
   - notes
   - internal_notes
   ↓
8. Validate & Save
   - Start transaction
   - Insert bookings
   - Insert booking_customers
   - Insert booking_services (nếu có)
   - Update tour_schedules.booked
   - Commit transaction
```

### **6.2. Breakdown Giá Booking**

```
┌─────────────────────────────────────────┐
│ PHÂN TÍCH GIÁ BOOKING                   │
│ ─────────────────────────────────────── │
│ Tour: Đà Lạt 3 ngày 2 đêm              │
│ Ngày khởi hành: 15/12/2024             │
│ ─────────────────────────────────────── │
│ Giá tour:                              │
│   • 2 người lớn × 1,500,000đ = 3,000,000đ │
│   • 1 trẻ em × 750,000đ = 750,000đ     │
│   Tổng: 3,750,000đ                     │
│ ─────────────────────────────────────── │
│ Giảm giá: 0đ                           │
│ ─────────────────────────────────────── │
│ Tổng tiền: 3,750,000đ                  │
│ Đặt cọc: 1,125,000đ (30%)              │
│ Còn lại: 2,625,000đ                    │
│ ─────────────────────────────────────── │
│ Dịch vụ ngoài (nếu có):                │
│   • Phòng khách sạn: 1,000,000đ       │
│   • Xe đưa đón: 500,000đ               │
│   Tổng dịch vụ: 1,500,000đ             │
│ ─────────────────────────────────────── │
│ TỔNG CỘNG: 5,250,000đ                  │
│ (3,750,000đ tour + 1,500,000đ dịch vụ) │
└─────────────────────────────────────────┘
```

---

## ✅ **7. CHECKLIST VALIDATION**

### **7.1. Khi Tạo Booking**

- [ ] `customer_id` có và hợp lệ
- [ ] `tour_id` có và `status = 'active'` AND `approval_status = 'approved'`
- [ ] `start_date >= today + booking_deadline_days` (deadline)
- [ ] Schedule có và `status = 'open'` hoặc `'pending'`
- [ ] `total_participants <= (quota - booked)`
- [ ] `adult_count >= 1`
- [ ] `total_amount > 0`
- [ ] `discount_amount <= total_amount`
- [ ] `deposit_amount <= final_amount`
- [ ] Không trùng booking (cùng customer, tour, start_date)

### **7.2. Khi Thêm Dịch vụ**

- [ ] Booking tồn tại và chưa hủy
- [ ] `days_until_start >= 1` (deadline)
- [ ] `service_id` có và hợp lệ
- [ ] `quantity > 0`
- [ ] `unit_price >= 0`
- [ ] `total_price = quantity × unit_price`

### **7.3. Khi Hủy Booking**

- [ ] Booking tồn tại
- [ ] `approval_status != 'cancelled'` (chưa hủy)
- [ ] `start_date >= today` (chưa khởi hành)
- [ ] Tính phí hủy đúng
- [ ] Trả lại quota cho schedule
- [ ] Cập nhật customer stats

---

## 📝 **8. TEMPLATE EXCEL/CSV CHI TIẾT**

### **8.1. Template Đầy đủ**

**File:** `public/templates/customer_import_template.csv`

```csv
Họ tên,SĐT,Email,Ngày sinh,Giới tính,Loại,Địa chỉ,CMND/CCCD,Passport
Nguyễn Văn A,0901234567,nguyenvana@gmail.com,1990-01-15,Nam,Người lớn,123 Đường ABC,123456789,
Trần Thị B,0912345678,tranthib@gmail.com,1992-05-20,Nữ,Người lớn,456 Đường XYZ,987654321,
Lê Văn C,0923456789,,2010-03-10,Nam,Trẻ em,789 Đường DEF,,
```

### **8.2. Template Tối thiểu**

```csv
Họ tên,SĐT
Nguyễn Văn A,0901234567
Trần Thị B,0912345678
```

**Lưu ý:** Tối thiểu phải có `Họ tên` hoặc `SĐT`

---

## 🔧 **9. CODE EXAMPLES**

### **9.1. Validate Tạo Booking**

```php
// 1. Validate deadline
$deadline_days = (int) ($tour['booking_deadline_days'] ?? 1);
$today = date('Y-m-d');
$minStartDate = date('Y-m-d', strtotime("+{$deadline_days} days"));
if ($_POST['start_date'] < $minStartDate) {
    throw new Exception("Không thể đặt booking. Phải đặt trước {$deadline_days} ngày.");
}

// 2. Validate tour
if ($tour['status'] !== 'active' || $tour['approval_status'] !== 'approved') {
    throw new Exception("Tour không đang hoạt động hoặc chưa được duyệt.");
}

// 3. Validate quota
$available = $schedule['quota'] - $schedule['booked'];
if ($totalParticipants > $available) {
    throw new Exception("Số lượng người tham gia vượt quá khả dụng (Còn $available chỗ).");
}
```

### **9.2. Tính Tiền Dịch vụ**

```php
$total_price = $quantity * $unit_price;

$bookingServiceData = [
    'booking_id' => $booking_id,
    'service_id' => $service_id,
    'service_provider_id' => $service_provider_id,
    'quantity' => $quantity,
    'unit_price' => $unit_price,
    'total_price' => $total_price,
    'payment_status' => 'pending',
    'paid_amount' => 0
];
```

### **9.3. Hủy Booking**

```php
// 1. Tính số ngày trước ngày khởi hành
$startDate = new DateTime($booking['start_date']);
$today = new DateTime();
$daysBefore = (int) $today->diff($startDate)->format('%a');

// 2. Tìm chính sách hủy
$policy = $this->findCancellationPolicy($daysBefore);

// 3. Tính phí
$feePercentage = $policy ? (float) $policy['fee_percentage'] : 0;
$feeAmount = ($booking['final_amount'] * $feePercentage) / 100;
$refundAmount = max(0, $booking['paid_amount'] - $feeAmount);

// 4. Update booking
$this->updateBookingCancellation($booking_id, $reason, $policy_id, $feeAmount, $refundAmount);

// 5. Trả lại quota
$this->returnQuotaToSchedule($schedule_id, $totalParticipants);
```

---

**Ngày tạo:** 2024-12-06  
**Version:** 1.0  
**Status:** ✅ Hoàn chỉnh

