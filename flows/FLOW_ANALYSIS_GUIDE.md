# 📊 PHÂN TÍCH LUỒNG: HƯỚNG DẪN VIÊN (GUIDE)

## 🎯 THÔNG TIN CHUNG

- **Module:** Guide Management 👨‍🏫
- **Mục đích:** Quản lý luồng công việc của hướng dẫn viên từ khi nhận tour đến khi hoàn thành
- **Ngày tạo:** 2024-12-XX
- **Ngày cập nhật:** 2024-12-XX (Đã kiểm tra và sửa theo database schema)
- **Status:** ✅ Đã phân tích và đối chiếu với database

---

## ⚠️ CÁC SAI SÓT ĐÃ PHÁT HIỆN VÀ SỬA

### **1. Tên bảng Check-in**

- **Sai:** `checkins`
- **Đúng:** `customer_checkins` (theo database schema)
- **Đã sửa:** ✅

### **2. Cấu trúc bảng Journal**

- **Sai:**
  - Bảng `tour_journals` với `tour_schedule_id`, `author_id`, `images` (JSON), `status` (draft/published)
- **Đúng:**
  - Bảng `journals` với `booking_id`, `guide_id`, `journal_date`, `day_number`, `weather`, `highlights`, `issues`
  - Bảng `journal_images` riêng để lưu hình ảnh (không phải JSON)
- **Đã sửa:** ✅

### **3. Luồng Journal**

- **Đã cập nhật:**
  - Journal link với `booking_id` thay vì `tour_schedule_id` trực tiếp
  - Có thêm các trường: `journal_date`, `day_number`, `weather`, `highlights`, `issues`
  - Hình ảnh lưu riêng trong bảng `journal_images`

---

## 📋 MÔ TẢ TỔNG QUAN

Module Guide bao gồm các chức năng chính:

1. **Dashboard:** Xem tổng quan tour được phân công
2. **My Tours:** Xem danh sách và chi tiết tour được phân công
3. **Check-in:** Check-in hành khách khi tour bắt đầu
4. **Journal:** Viết nhật ký tour (trong và sau tour)
5. **Expenses:** Quản lý chi phí (chưa implement)

---

## 🔄 CÁC LUỒNG CHÍNH

### **LUỒNG 1: GUIDE NHẬN PHÂN CÔNG TOUR**

**Mô tả luồng:**

**Bước 1: Admin phân công Guide**

- Admin vào module "Lịch khởi hành"
- Chọn tour schedule cần phân công
- Click "Phân công Guide"
- Điều kiện:
  - Số người đã đặt (`booked`) >= Số người tối thiểu (`min_participants`)
  - Guide phải có role = 'guide' và status = 'active'
  - Guide không được có lịch trùng (kiểm tra qua `TourAssignment::checkGuideAvailability()`)
- Chọn Guide từ dropdown
- Nhập ghi chú cho Guide (nếu có)
- Click "Phân công HDV"

**Bước 2: Hệ thống cập nhật phân công**

- Cập nhật `tour_schedules.guide_id` = guide_id
- Lưu `tour_schedules.guide_notes` = ghi chú
- Tạo records trong `tour_assignments`:
  - Link với tất cả bookings trong schedule (đã approved)
  - Lưu lương dự kiến (`salary_amount` = số ngày × 500,000 VNĐ)
  - Trạng thái lương: `pending`
  - Trạng thái assignment: `assigned`
- Lưu lịch sử vào `schedule_guide_history`

**Bước 3: Guide nhận thông báo (hiện tại: thông qua Dashboard)**

- Guide đăng nhập vào hệ thống
- Vào Dashboard (`?act=guide-dashboard`)
- Xem số tour sắp tới được phân công
- Xem danh sách tour gần nhất (5 tour)

**Lưu ý:** Hiện tại chưa có hệ thống thông báo tự động (email/SMS). Guide phải tự kiểm tra Dashboard.

---

### **LUỒNG 2: GUIDE XEM THÔNG TIN TOUR ĐƯỢC PHÂN CÔNG**

**Mô tả luồng:**

**Bước 1: Xem danh sách tour**

- Guide vào "My Tours" (`?act=guide-tours`)
- Hiển thị danh sách tour được phân công:
  - Mã tour (`tour_code`)
  - Tên tour
  - Ngày khởi hành (`start_date`)
  - Ngày kết thúc (`end_date`)
  - Số khách đã đặt (`booked` / `quota`)
  - Ghi chú từ điều hành (nếu có)
- Lọc mặc định: Tour từ hôm nay trở đi (`start_date >= today`)

**Bước 2: Xem chi tiết tour**

- Click "Xem chi tiết" trên tour
- URL: `?act=guide-tours&action=show&id={schedule_id}`
- Hiển thị:
  - **Thông tin chuyến đi:**
    - Ngày khởi hành, ngày kết thúc
    - Điểm khởi hành
    - Số lượng khách
  - **Ghi chú từ Điều hành:**
    - Hiển thị `guide_notes` (nếu có)
    - Đây là hướng dẫn đặc biệt từ admin/staff
  - **Danh sách hành khách:**
    - Tất cả hành khách từ các booking đã approved
    - Thông tin: Họ tên, năm sinh, giới tính, SĐT, Booking code
    - Đánh dấu trưởng đoàn (`is_primary = 1`)
    - Có nút "In danh sách" để in manifest

**Bước 3: In danh sách hành khách (Manifest)**

- Từ trang chi tiết tour, click "In danh sách"
- Hoặc từ trang Check-in, click "📄 In danh sách"
- URL: `?act=guide-checkin&action=printManifest&schedule_id={schedule_id}`
- Hiển thị danh sách đầy đủ hành khách với:
  - Thông tin cá nhân
  - Booking code
  - Trạng thái check-in (nếu đã check-in)

---

### **LUỒNG 3: GUIDE CHECK-IN HÀNH KHÁCH**

**Mô tả luồng:**

**Bước 1: Xem danh sách tour cần check-in**

- Guide vào "Check-in" (`?act=guide-checkin`)
- Hiển thị:
  - **Thống kê tổng quan:**
    - Tổng số tour
    - Tổng hành khách
    - Đã check-in
    - Chưa check-in
  - **Danh sách tour:**
    - Tour code, tên tour
    - Ngày khởi hành
    - Số hành khách
    - Tiến độ check-in (progress bar)
    - Thống kê: ✅ Có mặt / ❌ Vắng mặt / ⏰ Đến muộn
    - Nút "Check-in" và "📄 In danh sách"

**Bước 2: Vào trang check-in chi tiết**

- Click "Check-in" trên tour
- URL: `?act=guide-checkin&action=show&schedule_id={schedule_id}`
- Hiển thị:
  - **Thống kê:**
    - Tổng số hành khách
    - Có mặt (`present`)
    - Vắng mặt (`absent`)
    - Đến muộn (`late`)
    - Chưa check-in (`not_checked`)
  - **Danh sách hành khách:**
    - Chỉ hiển thị hành khách từ booking đã:
      - `approval_status = 'approved'`
      - `payment_status = 'paid'`
      - `remaining_amount = 0` (đã thanh toán đủ)
    - Mỗi hành khách có:
      - Họ tên, SĐT, Booking code
      - Dropdown chọn trạng thái: "Có mặt" / "Vắng mặt" / "Đến muộn"
      - Ô ghi chú (nếu có)
      - Thời gian check-in (nếu đã check-in trước đó)

**Bước 3: Thực hiện check-in**

- Guide chọn trạng thái cho từng hành khách:
  - `present`: Có mặt ✅
  - `absent`: Vắng mặt ❌
  - `late`: Đến muộn ⏰
- Có thể nhập ghi chú cho từng hành khách
- Có nút tiện ích:
  - "✅ Đánh dấu tất cả 'Có mặt'"
  - "❌ Đánh dấu tất cả 'Vắng mặt'"
- Click "💾 Lưu check-in"

**Bước 4: Lưu check-in**

- Validation:
  - Kiểm tra guide có được phân công tour này không
  - Kiểm tra booking đã thanh toán đủ chưa
  - Nếu chưa thanh toán đủ: Báo lỗi và không cho check-in
- Lưu vào bảng `customer_checkins`:
  - `booking_id`, `customer_id`
  - `status` (present/absent/late)
  - `checkin_time` = NOW()
  - `notes` (nếu có)
  - `checked_by` = guide_id
- Hiển thị success message
- Redirect về trang check-in

**Lưu ý quan trọng:**

- **Chỉ check-in được khi booking đã thanh toán đủ:**
  - Nếu `payment_status != 'paid'`: Báo lỗi "Booking chưa thanh toán đủ"
  - Nếu `remaining_amount > 0`: Báo lỗi "Booking còn nợ X VNĐ"
- Guide có thể check-in lại để cập nhật trạng thái (ví dụ: từ "vắng mặt" → "đến muộn")

---

### **LUỒNG 4: GUIDE VIẾT NHẬT KÝ TOUR (JOURNAL)**

**Mô tả luồng:**

**Bước 1: Xem danh sách nhật ký**

- Guide vào "Journal" (`?act=guide-journal`)
- Hiển thị danh sách nhật ký đã viết:
  - Tiêu đề
  - Tour/Booking liên quan
  - Ngày viết nhật ký (`journal_date`)
  - Số ngày trong tour (`day_number`)
  - Ngày tạo
- Có thể lọc theo:
  - Tour schedule
  - Booking
  - Ngày viết nhật ký

**Bước 2: Tạo nhật ký mới**

- Click "Viết nhật ký mới"
- URL: `?act=guide-journal&action=create`
- Form:
  - **Chọn tour/booking:** Dropdown (chỉ tour được phân công cho guide)
  - **Ngày viết nhật ký:** Date picker (default: hôm nay)
  - **Số ngày trong tour:** Number (optional, VD: Day 1, Day 2...)
  - **Tiêu đề:** Text (REQUIRED)
  - **Nội dung:** Rich text editor (REQUIRED)
  - **Thời tiết:** Text (optional)
  - **Điểm nổi bật:** Textarea (optional)
  - **Vấn đề phát sinh:** Textarea (optional)
  - **Hình ảnh:** Upload nhiều hình (optional)
- Click "Lưu nhật ký"

**Bước 3: Lưu nhật ký**

- Validation:
  - Tour phải được phân công cho guide
  - Tiêu đề và nội dung không được trống
- Upload hình ảnh vào `public/uploads/journals/`
- Lưu vào bảng `journals`:
  - `booking_id` (từ booking trong schedule)
  - `guide_id` = guide_id
  - `journal_date` = ngày viết nhật ký
  - `day_number` = số ngày trong tour (optional)
  - `title`, `content`
  - `weather` (optional)
  - `highlights` (optional)
  - `issues` (optional)
- Lưu hình ảnh vào bảng `journal_images`:
  - Mỗi hình là 1 record riêng
  - `journal_id`, `image_url`, `caption`, `display_order`
- Redirect về trang chi tiết nhật ký

**Bước 4: Xem/Sửa/Xóa nhật ký**

- **Xem chi tiết:**

  - URL: `?act=guide-journal&action=show&id={journal_id}`
  - Hiển thị đầy đủ nội dung, hình ảnh
  - Chỉ guide tác giả mới xem được

- **Sửa nhật ký:**

  - URL: `?act=guide-journal&action=edit&id={journal_id}`
  - Có thể:
    - Sửa tất cả thông tin: ngày, số ngày, tiêu đề, nội dung
    - Sửa thời tiết, điểm nổi bật, vấn đề
    - Thêm/xóa hình ảnh (qua bảng `journal_images`)
  - Không sửa được `booking_id` và `guide_id`

- **Xóa nhật ký:**
  - Xóa cả hình ảnh liên quan (từ bảng `journal_images`)
  - Xóa record trong database

**Lưu ý:**

- Guide có thể viết nhiều nhật ký cho cùng một booking/tour
- Mỗi journal có thể đánh dấu `day_number` (ngày thứ mấy trong tour)
- Nhật ký có thể viết trong quá trình tour hoặc sau khi tour kết thúc
- Hình ảnh được lưu riêng trong bảng `journal_images`, không phải JSON

---

### **LUỒNG 5: GUIDE HOÀN THÀNH TOUR**

**Mô tả luồng:**

**Bước 1: Tour kết thúc**

- Sau khi tour kết thúc (`end_date` đã qua)
- Guide có thể:
  - Viết nhật ký tổng kết tour
  - Xem lại danh sách hành khách đã check-in
  - Xem lại thông tin tour

**Bước 2: Cập nhật trạng thái (nếu cần)**

- Hiện tại: Hệ thống chưa có chức năng guide tự cập nhật trạng thái tour
- Admin/Staff sẽ cập nhật `tour_schedules.status = 'completed'` sau khi tour kết thúc

**Bước 3: Thanh toán lương**

- Lương được tính tự động khi phân công:
  - `salary_amount` = số ngày tour × 500,000 VNĐ
- Trạng thái lương: `pending` → `paid` (do admin/staff cập nhật)
- Guide có thể xem lương trong module khác (nếu có)

---

## 📊 CÁC TRƯỜNG DỮ LIỆU LIÊN QUAN

### **1. Bảng `tour_schedules`**

| Trường        | Loại | Mô tả                                      |
| ------------- | ---- | ------------------------------------------ |
| `guide_id`    | INT  | Foreign key → users (guide được phân công) |
| `guide_notes` | TEXT | Ghi chú đặc biệt cho guide                 |

**Mối quan hệ:**

- N Tour Schedules → 1 User (`guide_id`) - Optional

---

### **2. Bảng `tour_assignments`**

| Trường             | Loại          | Mô tả                                    |
| ------------------ | ------------- | ---------------------------------------- |
| `tour_schedule_id` | INT           | Foreign key → tour_schedules             |
| `booking_id`       | INT           | Foreign key → bookings                   |
| `guide_id`         | INT           | Foreign key → users (guide)              |
| `assignment_date`  | DATE          | Ngày phân công                           |
| `salary_amount`    | DECIMAL(15,2) | Lương dự kiến                            |
| `salary_status`    | ENUM          | pending/paid (trạng thái lương)          |
| `notes`            | TEXT          | Ghi chú                                  |
| `status`           | ENUM          | assigned/in_progress/completed/cancelled |
| `paid_date`        | DATE          | Ngày thanh toán lương (optional)         |
| `created_by`       | INT           | Foreign key → users (người tạo)          |
| `created_at`       | TIMESTAMP     | Ngày tạo                                 |

**Mối quan hệ:**

- N Tour Assignments → 1 Tour Schedule
- N Tour Assignments → 1 Booking
- N Tour Assignments → 1 User (Guide)

**Business Logic:**

- `salary_amount` = số ngày tour × 500,000 VNĐ (mặc định)
- `salary_status` = 'pending' khi mới phân công, 'paid' khi đã thanh toán
- `status` = 'assigned' khi được phân công, 'in_progress' khi tour đang diễn ra, 'completed' khi tour kết thúc, 'cancelled' khi bị hủy
- `paid_date` được cập nhật khi `salary_status` = 'paid'

---

### **3. Bảng `customer_checkins`**

| Trường         | Loại      | Mô tả                                     |
| -------------- | --------- | ----------------------------------------- |
| `id`           | INT       | Primary key                               |
| `booking_id`   | INT       | Foreign key → bookings                    |
| `customer_id`  | INT       | Foreign key → customers                   |
| `status`       | ENUM      | present/absent/late (trạng thái check-in) |
| `checkin_time` | TIMESTAMP | Thời gian check-in                        |
| `notes`        | TEXT      | Ghi chú                                   |
| `checked_by`   | INT       | Foreign key → users (guide)               |

**Mối quan hệ:**

- N Customer Checkins → 1 Booking
- N Customer Checkins → 1 Customer
- N Customer Checkins → 1 User (Guide)

**Business Logic:**

- Mỗi customer chỉ có 1 check-in record cho 1 booking (có thể update)
- Có thể cập nhật lại check-in (thay đổi status, notes)
- `checkin_time` tự động cập nhật khi check-in

---

### **4. Bảng `journals`**

| Trường         | Loại      | Mô tả                         |
| -------------- | --------- | ----------------------------- |
| `id`           | INT       | Primary key                   |
| `booking_id`   | INT       | Foreign key → bookings        |
| `guide_id`     | INT       | Foreign key → users (guide)   |
| `journal_date` | DATE      | Ngày viết nhật ký             |
| `day_number`   | INT       | Số ngày trong tour (optional) |
| `title`        | VARCHAR   | Tiêu đề nhật ký               |
| `content`      | TEXT      | Nội dung (HTML)               |
| `weather`      | VARCHAR   | Thời tiết (optional)          |
| `highlights`   | TEXT      | Điểm nổi bật (optional)       |
| `issues`       | TEXT      | Vấn đề phát sinh (optional)   |
| `created_at`   | TIMESTAMP | Ngày tạo                      |
| `updated_at`   | TIMESTAMP | Ngày cập nhật                 |

**Mối quan hệ:**

- N Journals → 1 Booking
- N Journals → 1 User (Guide)
- N Journals → N Journal Images (qua bảng `journal_images`)

**Business Logic:**

- Journal được link với `booking_id` (không phải `tour_schedule_id`)
- Một booking có thể có nhiều journal (mỗi ngày một journal)
- `day_number` để đánh dấu ngày thứ mấy trong tour
- Hình ảnh lưu riêng trong bảng `journal_images` (không phải JSON)

### **4b. Bảng `journal_images`**

| Trường          | Loại      | Mô tả                     |
| --------------- | --------- | ------------------------- |
| `id`            | INT       | Primary key               |
| `journal_id`    | INT       | Foreign key → journals    |
| `image_url`     | VARCHAR   | Đường dẫn hình ảnh        |
| `caption`       | TEXT      | Mô tả hình ảnh (optional) |
| `display_order` | INT       | Thứ tự hiển thị           |
| `created_at`    | TIMESTAMP | Ngày tạo                  |

**Mối quan hệ:**

- N Journal Images → 1 Journal

**Business Logic:**

- Mỗi hình ảnh là 1 record riêng
- Có thể sắp xếp thứ tự hiển thị bằng `display_order`

---

### **5. Bảng `schedule_guide_history`**

| Trường           | Loại      | Mô tả                        |
| ---------------- | --------- | ---------------------------- |
| `schedule_id`    | INT       | Foreign key → tour_schedules |
| `old_guide_id`   | INT       | Guide cũ (nếu thay đổi)      |
| `new_guide_id`   | INT       | Guide mới                    |
| `old_guide_name` | VARCHAR   | Tên guide cũ (snapshot)      |
| `new_guide_name` | VARCHAR   | Tên guide mới (snapshot)     |
| `changed_by`     | INT       | User thay đổi (admin/staff)  |
| `reason`         | TEXT      | Lý do thay đổi               |
| `notes`          | TEXT      | Ghi chú                      |
| `created_at`     | TIMESTAMP | Thời gian thay đổi           |

**Mối quan hệ:**

- N Schedule Guide History → 1 Tour Schedule
- N Schedule Guide History → 1 User (changed_by)

**Business Logic:**

- Lưu lịch sử mỗi lần thay đổi guide
- Lưu snapshot tên guide để phòng khi guide bị xóa

---

## ✅ VALIDATION RULES

### **1. Check-in**

1. **Guide phải được phân công tour:**

   - `tour_schedules.guide_id` = current user_id
   - Nếu không: Báo lỗi "Bạn không được phân công tour này"

2. **Booking phải thanh toán đủ:**

   - `approval_status` = 'approved'
   - `payment_status` = 'paid'
   - `remaining_amount` = 0
   - Nếu không đủ: Báo lỗi và không cho check-in

3. **Trạng thái check-in:**
   - Phải chọn: `present`, `absent`, hoặc `late`
   - Không được để trống

---

### **2. Journal**

1. **Tour phải được phân công:**

   - `tour_schedules.guide_id` = current user_id
   - Nếu không: Báo lỗi "Bạn không được phân công tour này"

2. **Booking phải hợp lệ:**

   - `booking_id` phải thuộc tour schedule được phân công cho guide
   - Booking phải có `approval_status = 'approved'`

3. **Tiêu đề và nội dung:**

   - `title`: Required, min 2 ký tự
   - `content`: Required, không được trống

4. **Hình ảnh:**

   - Tối đa 10 hình
   - Mỗi file max 5MB
   - Chỉ chấp nhận: jpg, jpeg, png, gif, webp
   - Lưu vào bảng `journal_images` (không phải JSON)

5. **Sửa/Xóa:**
   - Chỉ guide tác giả mới sửa/xóa được
   - Khi xóa journal, tự động xóa hình ảnh liên quan

---

## 🔒 BUSINESS RULES

### **1. Phân công Guide**

1. **Điều kiện phân công:**

   - Số người đã đặt >= Số người tối thiểu
   - Guide phải có role = 'guide' và status = 'active'
   - Guide không được có lịch trùng

2. **Tính lương:**

   - Mặc định: 500,000 VNĐ/ngày
   - Tổng lương = số ngày tour × 500,000
   - Có thể tùy chỉnh khi phân công

3. **Lưu assignment:**
   - Tự động tạo assignment cho tất cả bookings đã approved trong schedule
   - Trạng thái lương: `pending`
   - Trạng thái assignment: `assigned`
   - Khi tour bắt đầu: có thể cập nhật `status = 'in_progress'`
   - Khi tour kết thúc: có thể cập nhật `status = 'completed'`

---

### **2. Check-in**

1. **Thời điểm check-in:**

   - Guide có thể check-in trước ngày khởi hành (để chuẩn bị)
   - Thường check-in vào ngày khởi hành

2. **Cập nhật check-in:**

   - Có thể check-in lại để cập nhật trạng thái
   - `checkin_time` tự động cập nhật

3. **Validation thanh toán:**
   - **Bắt buộc:** Booking phải thanh toán đủ mới được check-in
   - Đây là rule quan trọng để đảm bảo tài chính

---

### **3. Journal**

1. **Viết nhật ký:**

   - Có thể viết nhiều nhật ký cho cùng một booking/tour
   - Mỗi journal có thể đánh dấu `day_number` (ngày thứ mấy)
   - Có thể viết trong quá trình tour hoặc sau khi tour kết thúc

2. **Cấu trúc dữ liệu:**

   - Journal link với `booking_id` (không phải `tour_schedule_id` trực tiếp)
   - Hình ảnh lưu riêng trong bảng `journal_images`
   - Có các trường bổ sung: `weather`, `highlights`, `issues`

3. **Hình ảnh:**
   - Lưu vào `public/uploads/journals/`
   - Tên file: `journal_{uniqid}_{timestamp}.{ext}`
   - Mỗi hình là 1 record trong `journal_images`
   - Khi xóa journal, tự động xóa hình ảnh liên quan (CASCADE)

---

## ⚠️ TRƯỜNG HỢP ĐẶC BIỆT

### **1. Guide bị thay đổi sau khi đã check-in**

- Nếu admin thay đổi guide sau khi đã check-in:
  - Check-in records vẫn giữ nguyên (`checked_by` = guide cũ)
  - Guide mới có thể xem check-in nhưng không thể sửa
  - Lịch sử được lưu trong `schedule_guide_history`

---

### **2. Booking chưa thanh toán đủ nhưng tour đã bắt đầu**

- Guide không thể check-in hành khách từ booking chưa thanh toán đủ
- Phải liên hệ admin/staff để xử lý thanh toán trước
- Sau khi thanh toán đủ, mới check-in được

---

### **3. Tour bị hủy sau khi đã phân công**

- Nếu `tour_schedules.status` = 'cancelled':
  - Guide vẫn có thể xem thông tin tour
  - Không thể check-in
  - Có thể viết journal (ghi lại lý do hủy)

---

## 🔗 DEPENDENCIES

### **Phụ thuộc vào:**

- **Module Tour:** Tours, Tour Schedules
- **Module Booking:** Bookings, Customers
- **Module System:** Users (guide role)
- **Module Payment:** Payment status (để validate check-in)

### **Ảnh hưởng đến:**

- **Module Operations:** Check-in data, Journal data
- **Module Finance:** Salary calculation (tour_assignments)

---

## 📝 GHI CHÚ

### **1. Thông báo cho Guide**

- **Hiện tại:** Guide phải tự kiểm tra Dashboard để biết được phân công tour
- **Có thể cải thiện:**
  - Gửi email/SMS khi được phân công tour
  - Thông báo trong hệ thống (notification center)
  - Reminder trước ngày khởi hành

---

### **2. Check-in Offline**

- **Hiện tại:** Guide phải có internet để check-in
- **Có thể cải thiện:**
  - Cho phép check-in offline (sync sau)
  - Mobile app để check-in dễ dàng hơn

---

### **3. Journal Structure**

- **Lưu ý:** Journal trong database link với `booking_id`, không phải `tour_schedule_id` trực tiếp
- **Có thể cải thiện:**
  - Cho phép admin/staff xem journal
  - Export journal ra PDF
  - Chia sẻ journal với khách hàng
  - Tích hợp với tour schedule để dễ quản lý hơn

---

### **4. Expenses Module**

- **Hiện tại:** Có menu "Expenses" nhưng chưa implement
- **Có thể phát triển:**
  - Guide ghi lại chi phí phát sinh trong tour
  - Upload hóa đơn
  - Admin/staff duyệt và thanh toán

---

## 🎯 TÓM TẮT LUỒNG CHÍNH

### **Quy trình Guide từ nhận tour đến hoàn thành:**

1. **Nhận phân công:**

   - Admin phân công guide cho tour schedule
   - Guide xem thông báo qua Dashboard

2. **Chuẩn bị tour:**

   - Xem chi tiết tour được phân công
   - Xem danh sách hành khách
   - Đọc ghi chú từ điều hành
   - In danh sách hành khách (manifest)

3. **Check-in hành khách:**

   - Vào trang check-in
   - Chọn trạng thái cho từng hành khách (có mặt/vắng mặt/đến muộn)
   - Lưu check-in

4. **Trong quá trình tour:**

   - Có thể viết journal (nhật ký)
   - Upload hình ảnh
   - Ghi lại các sự kiện đặc biệt

5. **Sau khi tour kết thúc:**
   - Viết journal tổng kết
   - Hoàn thành các công việc còn lại

---

**Status:** ✅ Đã phân tích đầy đủ - Sẵn sàng cho development và testing
