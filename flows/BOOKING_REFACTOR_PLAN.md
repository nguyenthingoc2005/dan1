# 📋 KẾ HOẠCH LÀM LẠI CHỨC NĂNG BOOKING

**Ngày tạo:** 2024-12-06  
**Dựa trên:** `FLOW_ANALYSIS_BOOKING.md`  
**Trạng thái:** ⏳ Chưa bắt đầu

---

## 🔍 SO SÁNH HIỆN TRẠNG VS TÀI LIỆU YÊU CẦU

### ✅ **CÁC CHỨC NĂNG ĐÃ CÓ (Hoạt động tốt)**

1. **Tạo Booking cơ bản:**

   - ✅ Chọn tour và schedule
   - ✅ Chọn/tạo khách hàng
   - ✅ Nhập số người (adult/child/infant)
   - ✅ Tính giá tự động
   - ✅ Thêm passengers vào booking
   - ✅ Tính giảm giá và đặt cọc
   - ✅ Validation cơ bản (quota, duplicate)
   - ✅ Auto-generate booking code
   - ✅ Transaction handling

2. **Quản lý Booking:**

   - ✅ Duyệt booking (approve)
   - ✅ Từ chối booking (reject)
   - ✅ Hủy booking (cancel) với tính phí
   - ✅ Ghi nhận thanh toán
   - ✅ Xem lịch sử thay đổi
   - ✅ Phân quyền (Admin vs Staff)

3. **Quota Management:**

   - ✅ Tự động cập nhật quota khi tạo booking
   - ✅ Trả lại quota khi hủy/từ chối

4. **Import Passengers:**
   - ✅ Import từ CSV/Excel
   - ✅ Preview passengers

---

### ❌ **CÁC CHỨC NĂNG THIẾU/SAI**

#### **1. DEADLINE ĐẶT BOOKING (CRITICAL - BOOK-002-Bước 3)**

**Yêu cầu:** Phải đặt trước **1 ngày** so với ngày khởi hành

**Hiện trạng:**

- ❌ Chỉ validate `start_date >= today` (cho phép đặt cùng ngày)
- ❌ Không có validation `start_date >= today + 1 day`
- ❌ Không hiển thị cảnh báo về deadline

**Cần sửa:**

- Controller: Thêm validation `start_date >= today + 1 day`
- View: Hiển thị cảnh báo "⚠️ Vui lòng đặt trước 1 ngày so với ngày khởi hành"
- Frontend JS: Disable dates không hợp lệ

**File cần sửa:**

- `app/controllers/admin/BookingController.php` (dòng 109-112)
- `app/controllers/staff/BookingController.php` (dòng 142-145)
- `app/views/admin/bookings/create.php`
- `app/views/staff/bookings/create.php`

---

#### **2. ĐIỀU KIỆN CHECK-IN (CRITICAL - BOOK-002-Bước 9, LUỒNG 5)**

**Yêu cầu:** Phải thanh toán đủ (`payment_status = 'paid'`) mới cho đi tour

**Hiện trạng:**

- ⚠️ Chưa có module check-in (guide check-in)
- ❌ Không có validation khi check-in

**Cần làm:**

- Kiểm tra trong `CheckinController` (guide)
- Validate: `approval_status = 'approved'` AND `payment_status = 'paid'` AND `remaining_amount = 0`
- Hiển thị error nếu chưa đủ điều kiện

**File cần sửa:**

- `app/controllers/guide/CheckinController.php` (cần kiểm tra)
- `app/views/guide/checkin/*.php` (cần kiểm tra)

---

#### **3. THÊM DỊCH VỤ VÀO BOOKING (BOOK-013)**

**Yêu cầu:** Cho phép thêm dịch vụ vào booking (dịch vụ bổ sung)

**Hiện trạng:**

- ✅ Có model `BookingService`
- ❌ Chưa có UI để thêm dịch vụ khi tạo booking
- ❌ Chưa có UI để quản lý dịch vụ trong booking detail

**Cần làm:**

- Form thêm dịch vụ trong booking create/view
- Controller methods: `storeBookingService`, `deleteBookingService`
- Hiển thị danh sách dịch vụ trong booking detail
- Tính tổng tiền dịch vụ (riêng biệt với tour price)

**File cần tạo/sửa:**

- `app/views/admin/bookings/create.php` (thêm section dịch vụ)
- `app/views/admin/bookings/show.php` (hiển thị dịch vụ)
- `app/controllers/admin/BookingController.php` (thêm methods)

---

#### **4. TOUR SCHEDULE SELECTION (BOOK-002-Bước 3)**

**Yêu cầu:**

- Filter schedule theo tour đã chọn
- Hiển thị số chỗ còn lại
- Chỉ hiển thị schedule `status = 'open'` hoặc `pending'`

**Hiện trạng:**

- ⚠️ Dropdown schedule không filter theo tour (static list)
- ✅ Có hiển thị số chỗ còn lại
- ⚠️ Filter status không rõ ràng

**Cần sửa:**

- Frontend JS: Filter schedules theo tour_id
- Backend: Đảm bảo chỉ load schedules `status IN ('open', 'pending')`
- Hiển thị rõ ràng deadline đặt booking

**File cần sửa:**

- `app/views/admin/bookings/create.php` (JS filter)
- `app/controllers/admin/BookingController.php` (query filter)

---

#### **5. TRƯỜNG DỮ LIỆU THIẾU (BOOKING DATA)**

**Yêu cầu từ Flow Analysis:**

**Database có nhưng chưa sử dụng:**

- ✅ `tour_schedule_id` - Có trong DB nhưng controller không set
- ✅ `source` - Có trong DB nhưng form không có field
- ✅ `special_requests` - Có trong DB nhưng form không có field
- ✅ `internal_notes` - Có trong DB nhưng form không có field
- ✅ `discount_code` - Có trong DB nhưng form không có field

**Cần làm:**

- Thêm các field vào form create booking
- Set `tour_schedule_id` khi tạo booking (hiện tại dùng `tour_id + start_date`)

**File cần sửa:**

- `app/views/admin/bookings/create.php`
- `app/controllers/admin/BookingController.php` (dòng 322, 337)

---

#### **6. PHÂN TÍCH GIÁ (BOOK-002-Bước 7)**

**Yêu cầu:** Hiển thị breakdown giá rõ ràng

**Hiện trạng:**

- ⚠️ Có hiển thị giá nhưng không đầy đủ
- ❌ Không có breakdown chi tiết như trong flow

**Cần làm:**

- Tạo section hiển thị breakdown:
  ```
  Giá tour:
    • X người lớn × Y = Z
    • X trẻ em × Y = Z
    • X em bé × Y = Z
    Tổng: XXXđ
  Giảm giá: XXXđ
  Tổng tiền: XXXđ
  Đặt cọc: XXXđ (XX%)
  Còn lại: XXXđ
  ```

**File cần sửa:**

- `app/views/admin/bookings/create.php` (thêm section breakdown)
- `app/views/admin/bookings/show.php` (hiển thị breakdown)

---

#### **7. THÊM KHÁCH HÀNG VÀO BOOKING (BOOK-008)**

**Yêu cầu:** Cho phép thêm khách hàng sau khi tạo booking

**Hiện trạng:**

- ✅ Có thể thêm passengers khi tạo booking
- ❌ Không thể thêm passengers sau khi booking đã tạo

**Cần làm:**

- Form thêm passengers trong booking detail
- Controller method: `addPassengerToBooking`
- Validation: Tổng số phải khớp với `adult_count + child_count + infant_count`
- Update `adult_count`, `child_count`, `infant_count` khi thêm/xóa

**File cần tạo/sửa:**

- `app/views/admin/bookings/show.php` (button thêm khách)
- `app/controllers/admin/BookingController.php` (method mới)

---

#### **8. DUYỆT BOOKING (BOOK-004)**

**Yêu cầu:**

- Cập nhật quota khi duyệt (nếu quota chỉ trừ khi approved)
- Gửi thông báo đến khách hàng (nếu có hệ thống notification)

**Hiện trạng:**

- ✅ Có logic duyệt booking
- ⚠️ Quota đã được trừ khi tạo (pending), không phải khi duyệt
- ❌ Không có hệ thống notification

**Cần làm:**

- Xem xét lại logic: Quota trừ khi nào? (Theo flow: khi tạo booking)
- Nếu cần, thêm notification system (tùy chọn)

**Lưu ý:** Logic hiện tại đúng (trừ quota khi tạo), chỉ cần document rõ ràng

---

#### **9. TỪ CHỐI BOOKING (BOOK-005)**

**Yêu cầu:**

- Hoàn tiền 100% nếu đã đặt cọc
- Tạo record trong `refunds`

**Hiện trạng:**

- ✅ Có logic từ chối và trả lại quota
- ❌ Không hoàn tiền tự động
- ❌ Không tạo record `refunds`

**Cần làm:**

- Kiểm tra `paid_amount > 0` khi từ chối
- Tạo record trong bảng `refunds` (nếu có)
- Hoặc update `payment_status = 'refund_pending'`

**File cần sửa:**

- `app/models/Booking.php` (method `reject`, dòng 554)

---

#### **10. HỦY BOOKING (BOOK-006)**

**Yêu cầu:**

- Hiển thị breakdown phí hủy trước khi xác nhận
- Tính phí hủy theo chính sách
- Tạo record trong `refunds` nếu có hoàn tiền

**Hiện trạng:**

- ✅ Có tính phí hủy
- ✅ Có hiển thị thông tin phí hủy
- ❌ Không hiển thị breakdown trước khi hủy (chỉ hiển thị sau)
- ❌ Không tạo record `refunds`

**Cần làm:**

- Modal hủy: Hiển thị breakdown trước khi xác nhận
- Tạo record `refunds` khi có hoàn tiền

**File cần sửa:**

- `app/views/admin/bookings/show.php` (modal hủy)
- `app/models/Booking.php` (method `cancel`)

---

#### **11. BOOKING_CODE GENERATION**

**Yêu cầu:** Format `BK-YYYYMMDD-XXX`

**Hiện trạng:**

- ✅ Đúng format (dòng 630 trong Booking.php)

**Không cần sửa**

---

#### **12. VALIDATION CẢI THIỆN**

**Yêu cầu từ Flow:**

- Tour phải `status = 'active'` AND `approval_status = 'approved'`
- Schedule phải `status = 'open'` hoặc `pending'`
- Deadline đặt booking: `start_date >= today + 1 day`

**Hiện trạng:**

- ✅ Validate tour status và approval
- ⚠️ Validate schedule status có nhưng không đầy đủ
- ❌ Thiếu deadline validation

**Cần sửa:**

- Thêm deadline validation (như đã nêu ở mục 1)

---

## 📊 BẢNG TỔNG HỢP

| #   | Vấn đề                   | Mức độ      | Trạng thái         | File cần sửa      |
| --- | ------------------------ | ----------- | ------------------ | ----------------- |
| 1   | Deadline đặt booking     | 🔴 Critical | ❌ Thiếu           | Controller, View  |
| 2   | Điều kiện check-in       | 🔴 Critical | ❌ Thiếu           | CheckinController |
| 3   | Thêm dịch vụ vào booking | 🟡 Medium   | ❌ Thiếu           | Controller, View  |
| 4   | Tour schedule selection  | 🟡 Medium   | ⚠️ Chưa đầy đủ     | View (JS)         |
| 5   | Trường dữ liệu thiếu     | 🟡 Medium   | ❌ Thiếu           | View, Controller  |
| 6   | Phân tích giá            | 🟢 Low      | ⚠️ Chưa đầy đủ     | View              |
| 7   | Thêm khách sau khi tạo   | 🟡 Medium   | ❌ Thiếu           | Controller, View  |
| 8   | Duyệt booking            | 🟢 Low      | ✅ OK              | -                 |
| 9   | Từ chối booking          | 🟡 Medium   | ⚠️ Thiếu hoàn tiền | Model             |
| 10  | Hủy booking              | 🟡 Medium   | ⚠️ Thiếu breakdown | View, Model       |
| 11  | Booking code             | 🟢 Low      | ✅ OK              | -                 |
| 12  | Validation               | 🟡 Medium   | ⚠️ Thiếu deadline  | Controller        |

**Mức độ ưu tiên:**

- 🔴 **Critical:** Phải làm ngay (ảnh hưởng business logic)
- 🟡 **Medium:** Nên làm (cải thiện UX và đầy đủ tính năng)
- 🟢 **Low:** Có thể làm sau (nice to have)

---

## 🎯 KẾ HOẠCH TRIỂN KHAI

### **PHASE 1: CRITICAL FIXES (Ưu tiên cao nhất)**

**Mục tiêu:** Sửa các vấn đề critical ảnh hưởng đến business logic

#### **Task 1.1: Deadline Đặt Booking (1-2 giờ)**

1. **Backend Validation:**

   - Sửa `BookingController::store()` (Admin)
   - Sửa `Staff\BookingController::store()` (Staff)
   - Validate: `start_date >= today + 1 day`
   - Error message: "Không thể đặt booking. Phải đặt trước 1 ngày so với ngày khởi hành. (Hôm nay: {today}, Ngày khởi hành tối thiểu: {today + 1})"

2. **Frontend Validation:**
   - Disable dates trong quá khứ và hôm nay
   - Hiển thị cảnh báo: "⚠️ Vui lòng đặt trước 1 ngày so với ngày khởi hành"
   - JS validation khi submit form

**Files:**

- `app/controllers/admin/BookingController.php`
- `app/controllers/staff/BookingController.php`
- `app/views/admin/bookings/create.php`
- `app/views/staff/bookings/create.php`

---

#### **Task 1.2: Điều kiện Check-in (1 giờ)**

1. Kiểm tra `CheckinController` hiện tại
2. Thêm validation:
   - `approval_status = 'approved'`
   - `payment_status = 'paid'`
   - `remaining_amount = 0`
3. Hiển thị error nếu không đủ điều kiện

**Files:**

- `app/controllers/guide/CheckinController.php`
- `app/views/guide/checkin/*.php`

---

### **PHASE 2: MEDIUM PRIORITY (Cải thiện tính năng)**

#### **Task 2.1: Thêm Dịch vụ vào Booking (3-4 giờ)**

1. **Backend:**

   - Controller method: `storeBookingService()`
   - Controller method: `deleteBookingService()`
   - Validate dịch vụ

2. **Frontend:**
   - Form thêm dịch vụ (modal)
   - Danh sách dịch vụ trong booking detail
   - Tổng tiền dịch vụ

**Files:**

- `app/controllers/admin/BookingController.php`
- `app/views/admin/bookings/show.php`
- `app/views/admin/bookings/create.php` (optional: thêm khi tạo)

---

#### **Task 2.2: Bổ sung Trường Dữ liệu (2 giờ)**

1. Thêm fields vào form:

   - `source` (dropdown)
   - `special_requests` (textarea)
   - `internal_notes` (textarea)
   - `discount_code` (text)

2. Set `tour_schedule_id` khi tạo booking

**Files:**

- `app/views/admin/bookings/create.php`
- `app/controllers/admin/BookingController.php`

---

#### **Task 2.3: Thêm Khách hàng sau khi tạo Booking (2-3 giờ)**

1. Form modal thêm passenger
2. Controller method: `addPassengerToBooking()`
3. Update `adult_count`, `child_count`, `infant_count`
4. Validation tổng số khách

**Files:**

- `app/views/admin/bookings/show.php`
- `app/controllers/admin/BookingController.php`

---

#### **Task 2.4: Tour Schedule Selection (1 giờ)**

1. Frontend JS: Filter schedules theo tour_id
2. Backend: Đảm bảo chỉ load schedules `status IN ('open', 'pending')`
3. Hiển thị rõ ràng deadline

**Files:**

- `app/views/admin/bookings/create.php` (JS)
- `app/controllers/admin/BookingController.php`

---

#### **Task 2.5: Từ chối Booking - Hoàn tiền (1-2 giờ)**

1. Kiểm tra `paid_amount > 0` khi từ chối
2. Tạo record trong `refunds` (nếu có bảng) hoặc update `payment_status = 'refund_pending'`
3. Log history

**Files:**

- `app/models/Booking.php` (method `reject`)

---

#### **Task 2.6: Hủy Booking - Breakdown trước khi hủy (1-2 giờ)**

1. Modal hủy: Hiển thị breakdown phí hủy
2. Tạo record `refunds` khi có hoàn tiền

**Files:**

- `app/views/admin/bookings/show.php` (modal)
- `app/models/Booking.php` (method `cancel`)

---

### **PHASE 3: LOW PRIORITY (Nice to have)**

#### **Task 3.1: Phân tích Giá Breakdown (1-2 giờ)**

1. Section hiển thị breakdown chi tiết
2. Format đẹp, dễ đọc

**Files:**

- `app/views/admin/bookings/create.php`
- `app/views/admin/bookings/show.php`

---

## 📝 CHECKLIST TRIỂN KHAI

### **Phase 1: Critical Fixes**

- [ ] Task 1.1: Deadline đặt booking (Backend + Frontend)
- [ ] Task 1.2: Điều kiện check-in

### **Phase 2: Medium Priority**

- [ ] Task 2.1: Thêm dịch vụ vào booking
- [ ] Task 2.2: Bổ sung trường dữ liệu
- [ ] Task 2.3: Thêm khách hàng sau khi tạo
- [ ] Task 2.4: Tour schedule selection
- [ ] Task 2.5: Từ chối booking - Hoàn tiền
- [ ] Task 2.6: Hủy booking - Breakdown

### **Phase 3: Low Priority**

- [ ] Task 3.1: Phân tích giá breakdown

---

## ⚠️ LƯU Ý KHI TRIỂN KHAI

1. **Database:**

   - Đảm bảo các trường đã có trong DB (check `setup_database_complete.sql`)
   - `tour_schedule_id` đã có nhưng chưa được set

2. **Backward Compatibility:**

   - Không break các booking đã có
   - Xử lý migration nếu cần

3. **Testing:**

   - Test với booking mới
   - Test với booking cũ (nếu có)
   - Test các edge cases

4. **Validation:**

   - Server-side validation là bắt buộc
   - Client-side validation để cải thiện UX

5. **Error Messages:**
   - Thông báo lỗi rõ ràng, dễ hiểu
   - Hiển thị cảnh báo trước khi submit

---

## 📚 TÀI LIỆU THAM KHẢO

- Flow Analysis: `flows/FLOW_ANALYSIS_BOOKING.md`
- Database Schema: `setup_database_complete.sql`
- Booking Deadline Rules: `flows/BOOKING_DEADLINE_AND_CHECKIN_RULES.md`

---

**Ngày cập nhật:** 2024-12-06  
**Version:** 1.0
