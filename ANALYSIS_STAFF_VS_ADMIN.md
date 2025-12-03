# 📊 PHÂN TÍCH SO SÁNH STAFF vs ADMIN

**Ngày phân tích:** 2024-12-XX  
**Mục tiêu:** So sánh logic giữa Staff và Admin controllers để tìm sự khác biệt và thiếu sót

---

## 📋 TỔNG QUAN

### Modules Staff có:
1. **Tours** - `Staff\TourController`
2. **Bookings** - `Staff\BookingController`
3. **Customers** - `Staff\CustomerController`
4. **Payments** - `Staff\PaymentController` (READ ONLY)

### Modules Admin có:
1. **Tours** - `TourController`
2. **Bookings** - `BookingController`
3. **Customers** - `CustomerController`
4. **Payments** - `PaymentController`
5. **Categories, Destinations, Services, Suppliers, Users, Reports, Journals, Schedules** - (Staff không có)

---

## 🔍 PHÂN TÍCH CHI TIẾT TỪNG MODULE

### 1. TOUR MODULE

#### ✅ ĐIỂM GIỐNG NHAU

**Staff TourController:**
- ✅ `index()` - Filter `created_by = get_user_id()` (chỉ tours của mình)
- ✅ `create()` - Form tạo tour
- ✅ `store()` - Validation đầy đủ (name, price, duration, itinerary, images)
- ✅ `edit()` - Check ownership + chỉ edit được tour `draft/rejected/pending`
- ✅ `update()` - Check ownership + giới hạn status
- ✅ `show()` - Check ownership
- ✅ `handleImageUploads()` - Upload images

**Admin TourController:**
- ✅ `index()` - Không filter `created_by` (xem tất cả)
- ✅ `create()` - Form tạo tour
- ✅ `store()` - Validation đầy đủ
- ✅ `edit()` - Không check ownership (admin xem tất cả)
- ✅ `update()` - Không giới hạn status
- ✅ `show()` - Không check ownership
- ✅ `changeStatus()` - **CHỈ ADMIN CÓ** (approve/reject tour)
- ✅ `delete()` - **CHỈ ADMIN CÓ**
- ✅ `selectTemplate()` - **CHỈ ADMIN CÓ** (tạo tour từ template)
- ✅ `createFromTemplate()` - **CHỈ ADMIN CÓ**
- ✅ `getTemplateData()` - **CHỈ ADMIN CÓ** (AJAX)
- ✅ `getDestinations()` - **CHỈ ADMIN CÓ** (AJAX)
- ✅ `getServiceInfo()` - **CHỈ ADMIN CÓ** (AJAX)

#### ❌ THIẾU SÓT TRONG STAFF

1. **Thiếu validation giống Admin:**
   - ❌ Staff không có validation cho `min_participants`, `max_participants`
   - ❌ Staff không có validation cho `price_based_on_pax`, `deposit_percentage`
   - ❌ Staff không có field `included`/`excluded` items
   - ❌ Staff không có field `parent_tour_id` (tạo từ template)

2. **Thiếu AJAX endpoints:**
   - ❌ `getDestinations()` - Filter destinations theo category
   - ❌ `getServiceInfo()` - Lấy service info (price, unit) khi chọn service

3. **Logic khác biệt:**
   - ✅ Staff chỉ tạo tour với status `draft` hoặc `pending` (ĐÚNG)
   - ✅ Staff chỉ edit được tour `draft/rejected/pending` (ĐÚNG)
   - ✅ Staff không có quyền approve/reject (ĐÚNG)

---

### 2. BOOKING MODULE

#### ✅ ĐIỂM GIỐNG NHAU

**Staff BookingController:**
- ✅ `index()` - Filter `created_by = get_user_id()` (chỉ bookings của mình)
- ✅ `create()` - Form tạo booking
- ✅ `store()` - Validation đầy đủ:
  - Customer (new/existing)
  - Tour validation
  - Schedule handling (public/custom)
  - Passenger handling
  - Price calculation
- ✅ `show()` - Check ownership
- ✅ `storePayment()` - Check ownership + validation

**Admin BookingController:**
- ✅ `index()` - Không filter `created_by` (xem tất cả)
- ✅ `create()` - Form tạo booking
- ✅ `store()` - Validation đầy đủ + **TRANSACTION**
- ✅ `show()` - Không check ownership
- ✅ `storePayment()` - Validation + **TRANSACTION**
- ✅ `changeStatus()` - **CHỈ ADMIN CÓ** (approve/reject/cancel booking)
- ✅ `previewPassengers()` - **CHỈ ADMIN CÓ** (AJAX - preview Excel import)
- ✅ `downloadTemplate()` - **CHỈ ADMIN CÓ** (download CSV template)

#### ❌ THIẾU SÓT TRONG STAFF

1. **CRITICAL: Thiếu TRANSACTION trong `store()`**
   - ❌ Staff `store()` không có `beginTransaction()`, `commit()`, `rollBack()`
   - ❌ Admin `store()` có transaction để đảm bảo atomicity
   - **Hậu quả:** Nếu booking tạo thành công nhưng schedule update fail → data inconsistency

2. **CRITICAL: Thiếu TRANSACTION trong `storePayment()`**
   - ❌ Staff `storePayment()` không có transaction
   - ❌ Admin `storePayment()` có transaction
   - **Hậu quả:** Nếu payment tạo thành công nhưng booking update fail → data inconsistency

3. **Thiếu validation giống Admin:**
   - ❌ Staff không có validation `end_date >= start_date`
   - ❌ Staff không có validation `total_amount > 0`
   - ❌ Staff không có validation passenger count matching (adult/child/infant)
   - ❌ Staff không check duplicate customer by phone trước khi tạo mới

4. **Thiếu CSRF protection:**
   - ❌ Staff `store()` không có `require_csrf_token()`
   - ❌ Staff `storePayment()` không có `require_csrf_token()`
   - ✅ Admin có CSRF protection

5. **Thiếu Excel/CSV import:**
   - ❌ Staff không có `previewPassengers()` (AJAX)
   - ❌ Staff không có `downloadTemplate()`
   - ✅ Admin có đầy đủ

6. **Logic khác biệt:**
   - ✅ Staff không có quyền approve/reject booking (ĐÚNG)
   - ✅ Staff chỉ xem bookings của mình (ĐÚNG)

---

### 3. CUSTOMER MODULE

#### ✅ ĐIỂM GIỐNG NHAU

**Staff CustomerController:**
- ✅ `index()` - Không filter ownership (xem tất cả customers)
- ✅ `create()` - Form tạo customer
- ✅ `store()` - Validation (name, phone) + check duplicate phone
- ✅ `show()` - Xem chi tiết + booking history
- ✅ `edit()` - Form sửa customer
- ✅ `update()` - Validation + check duplicate phone (exclude current)

**Admin CustomerController:**
- ✅ `index()` - Không filter ownership (xem tất cả)
- ✅ `create()` - Form tạo customer
- ✅ `store()` - Validation đầy đủ
- ✅ `show()` - Xem chi tiết + booking history
- ✅ `edit()` - Form sửa customer
- ✅ `update()` - Validation đầy đủ
- ✅ `delete()` - **CHỈ ADMIN CÓ** (soft delete)

#### ❌ THIẾU SÓT TRONG STAFF

1. **Thiếu validation giống Admin:**
   - ❌ Staff không có validation `status` filter trong `index()`
   - ❌ Staff không có validation email unique (nếu có email)

2. **Logic khác biệt:**
   - ✅ Staff không có quyền delete customer (ĐÚNG - chỉ admin)

---

### 4. PAYMENT MODULE

#### ✅ ĐIỂM GIỐNG NHAU

**Staff PaymentController:**
- ✅ `index()` - Filter `created_by_booking = get_user_id()` (chỉ payments của bookings mình tạo)
- ✅ `show()` - Check ownership via booking
- ✅ READ ONLY (không create/update/delete)

**Admin PaymentController:**
- ✅ `index()` - Không filter ownership (xem tất cả)
- ✅ `show()` - Không check ownership
- ✅ `create()`, `update()`, `delete()`, `refund()` - **CHỈ ADMIN CÓ**

#### ❌ THIẾU SÓT TRONG STAFF

1. **Logic khác biệt:**
   - ✅ Staff chỉ xem payments của bookings mình tạo (ĐÚNG)
   - ✅ Staff không có quyền create/update/delete payment từ PaymentController (ĐÚNG - chỉ từ BookingController)

---

## 🔴 VẤN ĐỀ CRITICAL

### 1. **Staff BookingController::store() - THIẾU TRANSACTION**

**Vấn đề:**
```php
// Staff - KHÔNG CÓ TRANSACTION
$this->bookingModel->create($data, $passengers);
$scheduleModel->incrementBooked($schedule_id, $totalParticipants);
```

**Admin có:**
```php
// Admin - CÓ TRANSACTION
$this->pdo->beginTransaction();
try {
    $this->bookingModel->create($data, $passengers, false); // false = không tự tạo transaction
    $scheduleModel->incrementBooked($schedule_id, $totalParticipants);
    $this->pdo->commit();
} catch (Exception $e) {
    $this->pdo->rollBack();
    throw $e;
}
```

**Hậu quả:** Nếu `incrementBooked()` fail sau khi booking đã tạo → data inconsistency

### 2. **Staff BookingController::storePayment() - THIẾU TRANSACTION**

**Vấn đề:**
```php
// Staff - KHÔNG CÓ TRANSACTION
$paymentModel->create($data);
$this->bookingModel->updatePaymentStatus($bookingId);
```

**Admin có:**
```php
// Admin - CÓ TRANSACTION
$this->pdo->beginTransaction();
try {
    $paymentModel->create($data);
    $this->bookingModel->updatePaymentStatus($bookingId);
    $this->pdo->commit();
} catch (Exception $e) {
    $this->pdo->rollBack();
    throw $e;
}
```

**Hậu quả:** Nếu `updatePaymentStatus()` fail sau khi payment đã tạo → data inconsistency

### 3. **Staff BookingController::store() - THIẾU VALIDATION**

**Thiếu:**
- Validation `end_date >= start_date`
- Validation `total_amount > 0`
- Validation passenger count matching (adult/child/infant)
- Check duplicate customer by phone trước khi tạo mới

### 4. **Staff BookingController - THIẾU CSRF PROTECTION**

**Thiếu:**
- `require_csrf_token()` trong `store()`
- `require_csrf_token()` trong `storePayment()`

---

## 🟡 VẤN ĐỀ HIGH PRIORITY

### 1. **Staff TourController - THIẾU VALIDATION**

**Thiếu:**
- Validation `min_participants`, `max_participants`
- Validation `price_based_on_pax`, `deposit_percentage`
- Field `included`/`excluded` items
- AJAX endpoints: `getDestinations()`, `getServiceInfo()`

### 2. **Staff BookingController - THIẾU EXCEL/CSV IMPORT**

**Thiếu:**
- `previewPassengers()` - AJAX preview Excel import
- `downloadTemplate()` - Download CSV template

### 3. **Staff CustomerController - THIẾU VALIDATION**

**Thiếu:**
- Validation email unique (nếu có email)
- Filter `status` trong `index()`

---

## 🟢 VẤN ĐỀ MEDIUM PRIORITY

### 1. **Staff TourController - THIẾU FEATURES**

**Thiếu:**
- Tạo tour từ template (chỉ admin có)
- AJAX endpoints cho dynamic form

### 2. **Staff BookingController - THIẾU FEATURES**

**Thiếu:**
- Excel/CSV import passengers (chỉ admin có)

---

## 📊 TÓM TẮT SỰ KHÁC BIỆT

| Feature | Staff | Admin | Ghi chú |
|---------|-------|-------|---------|
| **Tours** |
| Xem tất cả tours | ❌ (chỉ của mình) | ✅ | ĐÚNG - Staff chỉ xem tours mình tạo |
| Approve/Reject tour | ❌ | ✅ | ĐÚNG - Chỉ admin duyệt |
| Tạo từ template | ❌ | ✅ | Có thể thêm cho staff |
| Validation đầy đủ | ⚠️ (thiếu một số) | ✅ | Cần bổ sung |
| **Bookings** |
| Xem tất cả bookings | ❌ (chỉ của mình) | ✅ | ĐÚNG - Staff chỉ xem bookings mình tạo |
| Approve/Reject booking | ❌ | ✅ | ĐÚNG - Chỉ admin duyệt |
| Transaction trong store() | ❌ | ✅ | **CRITICAL - Cần fix** |
| Transaction trong storePayment() | ❌ | ✅ | **CRITICAL - Cần fix** |
| CSRF protection | ❌ | ✅ | **CRITICAL - Cần fix** |
| Excel/CSV import | ❌ | ✅ | Có thể thêm cho staff |
| Validation đầy đủ | ⚠️ (thiếu một số) | ✅ | Cần bổ sung |
| **Customers** |
| Xem tất cả customers | ✅ | ✅ | Giống nhau |
| Delete customer | ❌ | ✅ | ĐÚNG - Chỉ admin delete |
| Validation đầy đủ | ⚠️ (thiếu email unique) | ✅ | Cần bổ sung |
| **Payments** |
| Xem tất cả payments | ❌ (chỉ của bookings mình tạo) | ✅ | ĐÚNG - Staff chỉ xem payments của bookings mình tạo |
| Create/Update/Delete payment | ❌ (chỉ từ BookingController) | ✅ | ĐÚNG - Staff chỉ tạo payment từ booking show page |

---

## ✅ KẾT LUẬN

### Logic đúng (không cần sửa):
1. ✅ Staff chỉ xem tours/bookings của mình
2. ✅ Staff không có quyền approve/reject
3. ✅ Staff không có quyền delete customer
4. ✅ Staff chỉ xem payments của bookings mình tạo

### Cần fix ngay (CRITICAL):
1. 🔴 **Staff BookingController::store()** - Thêm TRANSACTION
2. 🔴 **Staff BookingController::storePayment()** - Thêm TRANSACTION
3. 🔴 **Staff BookingController** - Thêm CSRF protection
4. 🔴 **Staff BookingController::store()** - Thêm validation đầy đủ

### Cần bổ sung (HIGH):
1. 🟡 **Staff TourController** - Thêm validation đầy đủ
2. 🟡 **Staff BookingController** - Thêm Excel/CSV import
3. 🟡 **Staff CustomerController** - Thêm validation email unique

---

**Kết thúc phân tích**

