# 📊 PHÂN TÍCH SO SÁNH BOOKING: STAFF vs ADMIN

**Ngày phân tích:** 2024-12-XX  
**Mục tiêu:** So sánh chức năng tạo booking giữa Staff và Admin để cập nhật Staff

---

## 🔴 CRITICAL - THIẾU SÓT

### 1. **CSRF Protection**
- **Admin:** ✅ Có `require_csrf_token()` và `csrf_field()`
- **Staff:** ❌ KHÔNG CÓ
- **Cần:** Thêm CSRF protection vào `store()` và `storePayment()`

### 2. **Transaction Handling**
- **Admin:** ✅ Có `beginTransaction()`, `commit()`, `rollBack()`
- **Staff:** ❌ KHÔNG CÓ
- **Cần:** Wrap booking creation và schedule update trong transaction

### 3. **Validation đầy đủ**
- **Admin:** ✅ 
  - Validate `end_date >= start_date`
  - Validate `total_amount > 0`
  - Validate age_type matching (adult/child/infant counts)
  - Check duplicate customer by phone
- **Staff:** ❌ Thiếu:
  - `end_date >= start_date`
  - `total_amount > 0`
  - Age_type matching
  - Check duplicate customer by phone

---

## 🟡 HIGH PRIORITY - THIẾU FEATURES

### 4. **Excel/CSV Import**
- **Admin:** ✅ Có `previewPassengers()`, `downloadTemplate()`
- **Staff:** ❌ KHÔNG CÓ
- **Cần:** Thêm import functionality cho staff

### 5. **Passenger List Format**
- **Admin:** ✅ Phone, Email, Gender (KHÔNG có Date of Birth)
- **Staff:** ❌ Date of Birth (bản cũ)
- **Cần:** Cập nhật passenger list format

### 6. **Hiển thị Giá Tour**
- **Admin:** ✅ Hiển thị giá adult/child khi chọn tour
- **Staff:** ❌ KHÔNG CÓ
- **Cần:** Thêm display giá tour

### 7. **Auto-fill Service Prices**
- **Admin:** ✅ Có AJAX `getServiceInfo()` để auto-fill service price
- **Staff:** ❌ KHÔNG CÓ
- **Cần:** Thêm AJAX endpoint (hoặc dùng chung với admin)

---

## 🟢 MEDIUM PRIORITY

### 8. **Error Logging**
- **Admin:** ✅ Có `error_log()` cho debugging
- **Staff:** ⚠️ Ít error logging
- **Cần:** Thêm error logging

### 9. **Code Structure**
- **Admin:** ✅ Code được refactor, clean
- **Staff:** ⚠️ Code còn cũ, cần refactor

---

## 📋 CHECKLIST CẦN CẬP NHẬT

### Controller (`app/controllers/staff/BookingController.php`)

- [ ] Thêm `require_csrf_token()` vào `store()`
- [ ] Thêm `require_csrf_token()` vào `storePayment()`
- [ ] Thêm transaction handling vào `store()`
- [ ] Thêm transaction handling vào `storePayment()`
- [ ] Thêm validation `end_date >= start_date`
- [ ] Thêm validation `total_amount > 0`
- [ ] Thêm validation age_type matching
- [ ] Thêm check duplicate customer by phone
- [ ] Thêm method `previewPassengers()`
- [ ] Thêm method `downloadTemplate()`
- [ ] Thêm error logging

### View (`app/views/staff/bookings/create.php`)

- [ ] Thêm `csrf_field()` vào form
- [ ] Cập nhật passenger list: Remove Date of Birth, Add Phone, Email
- [ ] Thêm hiển thị giá tour (adult, child)
- [ ] Thêm button "Import Excel/CSV"
- [ ] Thêm link "Download Template"
- [ ] Thêm JavaScript cho import functionality
- [ ] Cập nhật JavaScript để match admin version

### Routes (`routes/staff.php`)

- [ ] Thêm route `previewPassengers`
- [ ] Thêm route `downloadTemplate`

---

## 🔄 WORKFLOW SO SÁNH

### Admin Booking Create:
1. Chọn Tour → Hiển thị giá
2. Chọn Schedule → Auto-fill end_date
3. Chọn/Create Customer → Check duplicate by phone
4. Nhập số lượng (adult/child/infant)
5. **Import Excel/CSV** (nếu có)
6. Nhập passengers: Name, Phone, Email, Gender
7. Validate age_type matching
8. Tính toán total_amount
9. Submit với CSRF token
10. Transaction: Create booking + Update schedule

### Staff Booking Create (HIỆN TẠI):
1. Chọn Tour
2. Chọn Schedule → Auto-fill end_date
3. Chọn/Create Customer
4. Nhập số lượng (adult/child/infant)
5. Nhập passengers: Name, Date of Birth, Gender (BẢN CŨ)
6. Tính toán total_amount
7. Submit (KHÔNG CÓ CSRF)
8. Create booking (KHÔNG CÓ TRANSACTION)

---

## ✅ KẾT LUẬN

Staff booking đang là **BẢN CŨ** và thiếu nhiều tính năng quan trọng:
- ❌ Không có CSRF protection (SECURITY RISK)
- ❌ Không có transaction handling (DATA INTEGRITY RISK)
- ❌ Thiếu validation quan trọng
- ❌ Passenger list format cũ (có date of birth)
- ❌ Không có Excel/CSV import

**Cần cập nhật ngay để đồng bộ với Admin!**

