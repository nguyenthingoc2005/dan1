# 📊 PHÂN TÍCH CÁC MODULE CÒN LẠI

**Ngày phân tích:** 2024-12-XX  
**Mục tiêu:** Phân tích 5 module còn lại: Khách hàng, Thanh toán, Nhật ký tour, Báo cáo, Nhân viên

---

## 📋 MỤC LỤC

1. [Module 1: Quản lý Khách hàng](#1-module-1-quản-lý-khách-hàng)
2. [Module 2: Quản lý Thanh toán](#2-module-2-quản-lý-thanh-toán)
3. [Module 3: Nhật ký Tour](#3-module-3-nhật-ký-tour)
4. [Module 4: Báo cáo & Thống kê](#4-module-4-báo-cáo--thống-kê)
5. [Module 5: Quản lý Nhân viên](#5-module-5-quản-lý-nhân-viên)

---

## 1. MODULE 1: QUẢN LÝ KHÁCH HÀNG

### ✅ Đã có

**Models:**
- ✅ `app/models/Customer.php` - CRUD đầy đủ, validation tốt
- ✅ `app/models/CustomerImport.php` - Import Excel/CSV (đã fix delimiter)

**Controllers:**
- ✅ `app/controllers/admin/CustomerController.php` - CRUD đầy đủ
- ✅ `app/controllers/staff/CustomerController.php` - CRUD cho staff

**Views:**
- ✅ `app/views/admin/customers/index.php`
- ✅ `app/views/admin/customers/create.php`
- ✅ `app/views/admin/customers/edit.php`
- ✅ `app/views/admin/customers/show.php`

**Database:**
- ✅ Bảng `customers` đầy đủ
- ✅ Bảng `customer_import_logs` (nếu cần)

### ❌ Thiếu sót / Cần cải thiện

#### 🔴 CRITICAL (Ưu tiên cao)

1. **Thiếu filter `status` trong `Customer::getAll()`**
   - Hiện tại chỉ filter `search` và `created_by`
   - Cần thêm filter `status` (active, inactive, blacklist)
   - **File:** `app/models/Customer.php` line 223-268

2. **Thiếu hiển thị booking history trong `show.php`**
   - Controller có fetch bookings nhưng view có thể chưa hiển thị đầy đủ
   - Cần hiển thị: danh sách bookings, tổng số tour đã đi, tổng tiền đã chi
   - **File:** `app/views/admin/customers/show.php`

3. **Thiếu validation khi update phone/email**
   - Cần check duplicate khi update (đã có `excludeId` nhưng cần verify)
   - **File:** `app/controllers/admin/CustomerController.php` line 184-247

#### 🟡 HIGH (Ưu tiên trung bình)

4. **Thiếu export Excel danh sách khách hàng**
   - Cần chức năng export toàn bộ hoặc filtered customers ra Excel
   - **File:** `app/controllers/admin/CustomerController.php` - Thêm method `export()`

5. **Thiếu thống kê khách hàng**
   - Tổng số khách hàng theo status
   - Khách hàng mới trong tháng
   - Top khách hàng chi tiêu nhiều nhất
   - **File:** `app/views/admin/customers/index.php` - Thêm stats cards

6. **Thiếu merge duplicate customers**
   - Khi có duplicate (cùng phone hoặc email), cần chức năng merge
   - **File:** `app/controllers/admin/CustomerController.php` - Thêm method `merge()`

7. **Thiếu blacklist management**
   - Chức năng đưa khách hàng vào blacklist với lý do
   - **File:** `app/controllers/admin/CustomerController.php` - Thêm method `blacklist()`

#### 🟢 MEDIUM (Ưu tiên thấp)

8. **Thiếu customer tags/labels**
   - Gắn tag cho khách hàng (VIP, Regular, Corporate, etc.)
   - **Database:** Cần thêm bảng `customer_tags` và `customer_tag_relations`

9. **Thiếu customer notes history**
   - Lịch sử ghi chú về khách hàng (tương tự booking status history)
   - **Database:** Cần thêm bảng `customer_notes`

10. **Thiếu customer communication log**
    - Lịch sử liên lạc (email, phone, meeting)
    - **Database:** Cần thêm bảng `customer_communications`

---

## 2. MODULE 2: QUẢN LÝ THANH TOÁN

### ✅ Đã có

**Models:**
- ✅ `app/models/Payment.php` - CRUD cơ bản, filter theo date/method/type

**Controllers:**
- ✅ `app/controllers/admin/PaymentController.php` - Có structure nhưng chưa đầy đủ
- ✅ `app/controllers/staff/PaymentController.php` - Có structure

**Views:**
- ✅ `app/views/admin/payments/index.php`
- ✅ `app/views/admin/payments/show.php`

**Database:**
- ✅ Bảng `payments` đầy đủ
- ✅ Bảng `payment_logs` (audit trail)

### ❌ Thiếu sót / Cần cải thiện

#### 🔴 CRITICAL (Ưu tiên cao)

1. **Thiếu validation khi tạo payment**
   - Chưa check `amount` không vượt quá `total_amount - paid_amount` của booking
   - Chưa check `payment_date` không trong tương lai
   - **File:** `app/models/Payment.php` line 145-190

2. **Thiếu auto-update `paid_amount` và `payment_status` của booking**
   - Khi tạo payment, cần update `bookings.paid_amount` và `bookings.payment_status`
   - Cần transaction để đảm bảo consistency
   - **File:** `app/models/Payment.php` - Cần trigger hoặc logic trong `create()`

3. **Thiếu payment logs khi tạo/sửa/xóa**
   - Cần ghi log vào `payment_logs` mỗi khi có thay đổi
   - **File:** `app/models/Payment.php` - Thêm method `logPayment()`

4. **Thiếu validation refund**
   - Khi refund, cần check `refund_amount <= paid_amount`
   - Không được refund quá số tiền đã thanh toán
   - **File:** `app/models/Payment.php` - Thêm method `refund()`

#### 🟡 HIGH (Ưu tiên trung bình)

5. **Thiếu receipt/invoice generation**
   - Tạo hóa đơn/phiếu thu tự động khi payment completed
   - **File:** `app/controllers/admin/PaymentController.php` - Thêm method `generateReceipt()`

6. **Thiếu payment reminders**
   - Nhắc nhở thanh toán cho bookings còn nợ
   - **File:** `app/controllers/admin/PaymentController.php` - Thêm method `sendReminders()`

7. **Thiếu payment schedule**
   - Lịch thanh toán định kỳ (installment plan)
   - **Database:** Cần thêm bảng `payment_schedules`

8. **Thiếu filter và search nâng cao**
   - Filter theo booking code, customer name
   - Search theo transaction_id, receipt_number
   - **File:** `app/models/Payment.php` line 20-81

9. **Thiếu payment statistics**
   - Tổng doanh thu theo ngày/tháng/năm
   - Doanh thu theo payment method
   - Doanh thu theo tour
   - **File:** `app/controllers/admin/PaymentController.php` - Thêm method `statistics()`

#### 🟢 MEDIUM (Ưu tiên thấp)

10. **Thiếu payment reconciliation**
    - Đối soát thanh toán (so sánh với bank statement)
    - **Database:** Cần thêm bảng `payment_reconciliations`

11. **Thiếu payment gateway integration**
    - Tích hợp thanh toán online (VNPay, MoMo, etc.)
    - **File:** Cần tạo `app/models/PaymentGateway.php`

12. **Thiếu export payment report**
    - Export danh sách thanh toán ra Excel/PDF
    - **File:** `app/controllers/admin/PaymentController.php` - Thêm method `export()`

---

## 3. MODULE 3: NHẬT KÝ TOUR

### ✅ Đã có

**Models:**
- ✅ `app/models/Journal.php` - CRUD cơ bản, có `createTableIfNotExists()`

**Controllers:**
- ✅ `app/controllers/admin/JournalController.php` - Có structure

**Views:**
- ✅ `app/views/admin/journals/index.php`
- ✅ `app/views/admin/journals/create.php`
- ✅ `app/views/admin/journals/show.php`

**Database:**
- ✅ Bảng `journals` (theo booking_id)
- ✅ Bảng `tour_journals` (theo tour_schedule_id)
- ✅ Bảng `journal_images`

### ❌ Thiếu sót / Cần cải thiện

#### 🔴 CRITICAL (Ưu tiên cao)

1. **Confusion giữa 2 bảng `journals` và `tour_journals`**
   - `journals`: theo `booking_id` và `guide_id`, có `journal_date`, `day_number`
   - `tour_journals`: theo `tour_schedule_id` và `author_id`, có `title`, `content`, `images`
   - **Cần quyết định:** Dùng bảng nào? Hoặc merge logic?
   - **File:** `app/models/Journal.php` - Cần refactor

2. **Thiếu guide role access**
   - Guide cần viết nhật ký tour, nhưng hiện tại chỉ có admin controller
   - **File:** Cần tạo `app/controllers/guide/JournalController.php`

3. **Thiếu validation khi tạo journal**
   - Chỉ guide được phân công mới được viết nhật ký
   - Chỉ viết được cho tour schedule đã bắt đầu hoặc đã hoàn thành
   - **File:** `app/models/Journal.php` - Thêm validation

4. **Thiếu image upload handling**
   - Controller có comment "Handle file upload (images)" nhưng chưa implement
   - **File:** `app/controllers/admin/JournalController.php` line 92-93

#### 🟡 HIGH (Ưu tiên trung bình)

5. **Thiếu daily journal entries**
   - Mỗi ngày của tour cần có 1 journal entry riêng
   - Hiển thị theo `day_number` hoặc `journal_date`
   - **File:** `app/models/Journal.php` - Cần support multiple entries per schedule

6. **Thiếu journal template**
   - Template mẫu cho guide viết nhật ký (weather, highlights, issues)
   - **File:** `app/views/guide/journals/create.php` - Cần tạo view cho guide

7. **Thiếu journal approval workflow**
   - Guide viết → Admin duyệt → Publish
   - **Database:** Bảng `tour_journals` đã có `status` (draft, published)
   - **File:** `app/controllers/admin/JournalController.php` - Thêm method `approve()`

8. **Thiếu journal statistics**
   - Số lượng journal theo tour
   - Số lượng journal theo guide
   - **File:** `app/controllers/admin/JournalController.php` - Thêm method `statistics()`

#### 🟢 MEDIUM (Ưu tiên thấp)

9. **Thiếu journal comments**
    - Admin/Staff có thể comment trên journal
    - **Database:** Cần thêm bảng `journal_comments`

10. **Thiếu journal export**
    - Export journal ra PDF để in hoặc gửi khách
    - **File:** `app/controllers/admin/JournalController.php` - Thêm method `export()`

11. **Thiếu journal search**
    - Search theo tour name, guide name, date range
    - **File:** `app/models/Journal.php` - Thêm filter trong `getAll()`

---

## 4. MODULE 4: BÁO CÁO & THỐNG KÊ

### ✅ Đã có

**Controllers:**
- ✅ `app/controllers/admin/ReportController.php` - Có structure cơ bản

**Views:**
- ✅ `app/views/admin/reports/revenue.php`
- ✅ `app/views/admin/reports/bookings.php`

**Database:**
- ✅ Các bảng cần thiết đã có (bookings, payments, tours, etc.)

### ❌ Thiếu sót / Cần cải thiện

#### 🔴 CRITICAL (Ưu tiên cao)

1. **Thiếu profit/loss calculation**
   - Theo workflow: Admin xem báo cáo lợi nhuận
   - Cần tính: Revenue - Costs = Profit
   - Costs bao gồm: Supplier payments, Guide salary, Incurred expenses
   - **File:** `app/controllers/admin/ReportController.php` - Thêm method `profit()`

2. **Thiếu revenue report đầy đủ**
   - Hiện tại chỉ có query đơn giản, chưa có breakdown
   - Cần: Revenue by tour, Revenue by month, Revenue by payment method
   - **File:** `app/views/admin/reports/revenue.php` - Cần implement đầy đủ

3. **Thiếu booking statistics**
   - Số lượng booking theo status
   - Booking conversion rate
   - Average booking value
   - **File:** `app/views/admin/reports/bookings.php` - Cần implement đầy đủ

#### 🟡 HIGH (Ưu tiên trung bình)

4. **Thiếu tour performance report**
   - Tour nào bán chạy nhất
   - Tour nào có lợi nhuận cao nhất
   - Tour nào có tỷ lệ hủy cao
   - **File:** `app/controllers/admin/ReportController.php` - Thêm method `tourPerformance()`

5. **Thiếu guide performance report**
   - Guide nào được đánh giá cao nhất
   - Guide nào có nhiều tour nhất
   - Guide nào có chi phí phát sinh nhiều nhất
   - **File:** `app/controllers/admin/ReportController.php` - Thêm method `guidePerformance()`

6. **Thiếu customer analytics**
   - Top customers by spending
   - Customer retention rate
   - New vs returning customers
   - **File:** `app/controllers/admin/ReportController.php` - Thêm method `customerAnalytics()`

7. **Thiếu date range filters**
   - Filter theo ngày, tháng, quý, năm
   - So sánh với kỳ trước
   - **File:** `app/views/admin/reports/revenue.php` - Cần thêm filters

8. **Thiếu export reports**
    - Export báo cáo ra Excel/PDF
    - **File:** `app/controllers/admin/ReportController.php` - Thêm method `export()`

9. **Thiếu charts/visualizations**
    - Biểu đồ doanh thu theo tháng
    - Biểu đồ booking status
    - **File:** Cần tích hợp chart library (Chart.js hoặc similar)

#### 🟢 MEDIUM (Ưu tiên thấp)

10. **Thiếu scheduled reports**
    - Tự động gửi báo cáo hàng tuần/tháng qua email
    - **Database:** Cần thêm bảng `report_schedules`

11. **Thiếu custom reports**
    - Admin có thể tạo báo cáo tùy chỉnh
    - **Database:** Cần thêm bảng `custom_reports`

12. **Thiếu dashboard widgets**
    - Widget hiển thị key metrics trên dashboard
    - **File:** `app/views/admin/dashboard.php` - Cần thêm widgets

---

## 5. MODULE 5: QUẢN LÝ NHÂN VIÊN

### ✅ Đã có

**Models:**
- ✅ `app/models/User.php` - CRUD đầy đủ, validation tốt

**Controllers:**
- ✅ `app/controllers/admin/UserController.php` - CRUD đầy đủ

**Views:**
- ✅ `app/views/admin/users/index.php`
- ✅ `app/views/admin/users/create.php`
- ✅ `app/views/admin/users/edit.php`

**Database:**
- ✅ Bảng `users` đầy đủ
- ✅ Bảng `roles` đầy đủ
- ✅ Bảng `password_resets`

### ❌ Thiếu sót / Cần cải thiện

#### 🔴 CRITICAL (Ưu tiên cao)

1. **Thiếu validation email unique khi update**
   - Hiện tại chỉ check khi create
   - Cần check khi update (trừ chính user đó)
   - **File:** `app/controllers/admin/UserController.php` line 170-230

2. **Thiếu user activity log**
   - Log các hành động của user (login, create booking, etc.)
   - **Database:** Cần thêm bảng `user_activity_logs`

3. **Thiếu password reset functionality**
   - Có bảng `password_resets` nhưng chưa có controller logic
   - **File:** Cần tạo `app/controllers/auth/PasswordResetController.php`

#### 🟡 HIGH (Ưu tiên trung bình)

4. **Thiếu user profile page**
   - User có thể xem và sửa profile của mình
   - **File:** Cần tạo `app/controllers/ProfileController.php`

5. **Thiếu user statistics**
   - Số lượng booking tạo bởi user
   - Số lượng tour tạo bởi user
   - Performance metrics
   - **File:** `app/views/admin/users/show.php` - Cần tạo view

6. **Thiếu role permissions management**
   - Hiện tại chỉ có roles, chưa có permissions chi tiết
   - **Database:** Cần thêm bảng `permissions` và `role_permissions`

7. **Thiếu user status management**
   - Toggle active/inactive
   - Có method `toggleStatus()` nhưng cần verify UI
   - **File:** `app/views/admin/users/index.php` - Cần verify

8. **Thiếu user search và filter nâng cao**
   - Filter theo role, status, date created
   - Search theo name, email, phone
   - **File:** `app/models/User.php` line 143-210 - Đã có nhưng cần verify

#### 🟢 MEDIUM (Ưu tiên thấp)

9. **Thiếu user groups/teams**
    - Nhóm nhân viên theo phòng ban
    - **Database:** Cần thêm bảng `teams` và `user_teams`

10. **Thiếu user notes/comments**
    - Ghi chú về nhân viên (performance, issues, etc.)
    - **Database:** Cần thêm bảng `user_notes`

11. **Thiếu user attendance**
    - Chấm công, nghỉ phép
    - **Database:** Cần thêm bảng `attendance`

12. **Thiếu user salary management**
    - Quản lý lương cho guide (đã có trong tour_assignments nhưng cần centralize)
    - **Database:** Cần thêm bảng `user_salaries`

---

## 📊 TỔNG KẾT THEO ĐỘ ƯU TIÊN

### 🔴 CRITICAL (Phải làm ngay)

**Module 1 - Khách hàng:**
1. Filter `status` trong `Customer::getAll()`
2. Hiển thị booking history trong `show.php`
3. Validation khi update phone/email

**Module 2 - Thanh toán:**
1. Validation khi tạo payment (amount, date)
2. Auto-update `paid_amount` và `payment_status` của booking
3. Payment logs khi tạo/sửa/xóa
4. Validation refund

**Module 3 - Nhật ký tour:**
1. Quyết định dùng bảng `journals` hay `tour_journals`
2. Guide role access (tạo controller cho guide)
3. Validation khi tạo journal
4. Image upload handling

**Module 4 - Báo cáo:**
1. Profit/loss calculation
2. Revenue report đầy đủ
3. Booking statistics đầy đủ

**Module 5 - Nhân viên:**
1. Validation email unique khi update
2. User activity log
3. Password reset functionality

### 🟡 HIGH (Làm sau khi xong Critical)

**Module 1:** Export Excel, Thống kê, Merge duplicate, Blacklist  
**Module 2:** Receipt generation, Payment reminders, Payment schedule, Statistics  
**Module 3:** Daily journal entries, Template, Approval workflow, Statistics  
**Module 4:** Tour performance, Guide performance, Customer analytics, Charts  
**Module 5:** User profile, User statistics, Role permissions, Status management

### 🟢 MEDIUM (Có thể làm sau)

Các tính năng nâng cao, không ảnh hưởng đến core functionality.

---

## 🎯 KẾ HOẠCH THỰC HIỆN

### Phase 1: Critical Issues (Tuần 1-2)

1. **Module 1 - Khách hàng:**
   - Fix filter `status`
   - Hiển thị booking history
   - Fix validation update

2. **Module 2 - Thanh toán:**
   - Fix validation
   - Auto-update booking payment status
   - Implement payment logs

3. **Module 3 - Nhật ký tour:**
   - Quyết định và refactor journal model
   - Tạo guide controller
   - Implement image upload

4. **Module 4 - Báo cáo:**
   - Implement profit calculation
   - Complete revenue report
   - Complete booking statistics

5. **Module 5 - Nhân viên:**
   - Fix validation
   - Implement activity log
   - Implement password reset

### Phase 2: High Priority (Tuần 3-4)

- Implement các tính năng HIGH priority cho từng module

### Phase 3: Medium Priority (Tuần 5+)

- Implement các tính năng MEDIUM priority (nếu cần)

---

## 📝 LƯU Ý

1. **Database changes:** Một số tính năng cần thêm bảng mới, cần migration script
2. **Testing:** Sau mỗi phase cần test kỹ để đảm bảo không break existing functionality
3. **Documentation:** Cập nhật documentation khi thêm tính năng mới
4. **Security:** Đảm bảo tất cả input được validate và sanitize
5. **Performance:** Một số report có thể cần cache hoặc optimize query

---

**Kết thúc báo cáo phân tích**

