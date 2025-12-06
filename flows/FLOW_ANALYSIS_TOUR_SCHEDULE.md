# 📊 PHÂN TÍCH LUỒNG: TOUR SCHEDULE MODULE

## 🎯 THÔNG TIN CHUNG

- **Module:** Tour Schedule Management 📅
- **Mục đích:** Quản lý lịch trình khởi hành cho tour (tạo schedule, phân công guide, quản lý quota)
- **Ngày tạo:** 2024-12-06
- **Status:** ⏳ Đang phân tích

---

## 📋 MÔ TẢ TỔNG QUAN

Module Tour Schedule quản lý các **lịch trình khởi hành cụ thể** của một tour:

- **Tour (tour mẫu):** Định nghĩa tour (3 ngày 2 đêm, giá, lịch trình...)
- **Tour Schedule (lịch trình khởi hành):** Một chuyến đi cụ thể của tour đó (VD: Khởi hành ngày 15/12/2024, có 30 chỗ, guide là anh A)

**Ví dụ:**

- Tour: "Đà Lạt 3 ngày 2 đêm" (tour mẫu)
- Tour Schedule 1: Khởi hành 15/12/2024, 20 chỗ, giá 1,500,000đ
- Tour Schedule 2: Khởi hành 20/12/2024, 30 chỗ, giá 1,600,000đ
- Tour Schedule 3: Khởi hành 25/12/2024, 25 chỗ, giá 1,550,000đ

**Lưu ý:** Các chức năng CRUD cơ bản (xem, thêm, sửa, xóa) sẽ được bỏ qua, tập trung vào **luồng thao tác phức tạp của người dùng**.

---

## 🔄 CÁC LUỒNG CHÍNH

### **LUỒNG 1: TẠO LỊCH TRÌNH TOUR MỚI (TOUR-008)**

**Mô tả luồng:**

**Bước 1: Chọn Tour**

- User (Staff/Admin) vào module "Quản lý Lịch trình Tour"
- URL: `?act=admin&module=tour-schedules` hoặc `?act=staff-tours&action=schedules`
- Hiển thị danh sách tour (filter: `status = 'active'` AND `approval_status = 'approved'`)
- User chọn tour cần tạo lịch trình → Click "Thêm lịch trình mới"

**Bước 2: Form Tạo Tour Schedule**

- URL: `?act=admin&module=tour-schedules&action=create&tour_id=X` hoặc `?act=staff-tours&action=createSchedule&tour_id=X`
- Form hiển thị với thông tin tour đã chọn:
  - **Tên Tour:** [Display only] - Hiển thị tên tour
  - **Số ngày/đêm:** [Display only] - Hiển thị `duration_days` / `duration_nights`
  - **Giá mặc định:** [Display only] - Hiển thị giá từ tour (`adult_price`, `child_price`, `infant_price`)
  - **Số người tối thiểu:** [Display only] - `min_participants`
  - **Số người tối đa:** [Display only] - `max_participants`

**Bước 3: Nhập Thông tin Lịch trình**

- Form fields:
  - **Ngày khởi hành:** Date picker - REQUIRED
    - Validation: Phải >= ngày hiện tại
    - Auto-calculate `end_date` = `start_date` + `duration_days` - 1
  - **Ngày kết thúc:** Date picker - REQUIRED (Auto-filled, có thể chỉnh sửa)
    - Validation: `end_date` - `start_date` + 1 = `duration_days`
  - **Số chỗ (Quota):** Number - REQUIRED (default: `max_participants` từ tour)
    - Validation: >= `min_participants`, <= `max_participants`
  - **Số chỗ đã đặt:** Number - DEFAULT 0 (Display only khi edit)
  - **Giá người lớn:** Number - Optional (default: `adult_price` từ tour)
    - Nếu để trống → Dùng giá từ tour
  - **Giá trẻ em:** Number - Optional (default: `child_price` từ tour)
    - Nếu để trống → Dùng giá từ tour
  - **Giá em bé:** Number - Optional (default: `infant_price` từ tour)
    - Nếu để trống → Dùng giá từ tour
  - **Hướng dẫn viên (Guide):** Dropdown - Optional
    - Filter: `role = 'guide'` AND `status = 'active'`
    - Hiển thị: Tên guide + Phone
    - Có thể chọn sau (Step phân công guide)
  - **Ghi chú guide:** Textarea - Optional
  - **Trạng thái:** Dropdown:
    - `open` (Mở đặt) - DEFAULT
    - `closed` (Đóng đặt)
    - (Không cho chọn `completed` hoặc `cancelled` khi tạo mới)

**Bước 4: Validation & Save**

- Server-side validation:
  - `tour_id`: Required, tour phải `status = 'active'` AND `approval_status = 'approved'`
  - `start_date`: Required, >= ngày hiện tại
  - `end_date`: Required, `end_date` - `start_date` + 1 = `duration_days`
  - `quota`: Required, >= `min_participants`, <= `max_participants`
  - `booked`: DEFAULT 0
  - `adult_price`, `child_price`, `infant_price`: Optional, nếu null → dùng giá từ tour
  - Unique constraint: `(tour_id, start_date, end_date)` không được trùng
- Nếu validation pass:
  - Insert vào `tour_schedules`
  - `status` = 'open' (nếu chưa set)
  - `booked` = 0
  - Hiển thị success: "Tạo lịch trình tour thành công!"
  - Redirect về danh sách lịch trình của tour đó
- Nếu validation fail:
  - Hiển thị errors
  - Giữ lại dữ liệu đã nhập

**Bước 5: Hiển thị Danh sách Lịch trình**

- Sau khi tạo, hiển thị danh sách lịch trình của tour đó
- URL: `?act=admin&module=tour-schedules&tour_id=X`
- Hiển thị:
  - Ngày khởi hành
  - Ngày kết thúc
  - Số chỗ (Quota/Đã đặt/Còn lại)
  - Giá
  - Guide (nếu có)
  - Trạng thái
  - Actions: Sửa, Xóa, Đóng/Mở, Phân công guide

---

### **LUỒNG 2: PHÂN CÔNG GUIDE CHO LỊCH TRÌNH (TOUR-010)**

**Mô tả luồng:**

**Bước 1: Chọn Lịch trình**

- Từ danh sách lịch trình tour
- Click "Phân công guide" hoặc "Chỉnh sửa guide"

**Bước 2: Kiểm tra Điều kiện Phân công Guide**

- **Validation quan trọng:**
  - Kiểm tra: `booked >= min_participants` (số người đã đặt phải >= số người tối thiểu)
  - Nếu `booked < min_participants`:
    - **Hiển thị cảnh báo:** "Tour này chưa đủ số người tối thiểu (`booked`/`min_participants`). Chỉ phân công guide khi đã đủ số người."
    - **Disable button "Lưu"** hoặc hiển thị thông báo: "Không thể phân công guide. Cần đủ `min_participants` người."
    - **Hiển thị thông tin:**
      - Số người tối thiểu: `min_participants`
      - Số người đã đặt: `booked`
      - Còn thiếu: `min_participants - booked` người
    - **Không cho phép phân công guide** cho đến khi đủ số người

**Bước 3: Form Phân công Guide** (Chỉ hiển thị khi `booked >= min_participants`)

- Modal hoặc form riêng
- Form fields:
  - **Lịch trình:** [Display only] - Hiển thị thông tin schedule (ngày khởi hành, tour...)
  - **Thông tin số người:** [Display only]
    - Số người tối thiểu: `min_participants`
    - Số người đã đặt: `booked` ✅ (đã đủ điều kiện)
  - **Hướng dẫn viên:** Dropdown - REQUIRED
    - Filter: `role = 'guide'` AND `status = 'active'`
    - Hiển thị: Tên guide + Phone + Email
    - Có thể search/filter
  - **Ghi chú:** Textarea - Optional
    - VD: "Hướng dẫn đặc biệt cho đoàn VIP", "Guide sẽ đón khách tại sân bay..."

**Bước 4: Kiểm tra Trùng lịch (Optional)**

- Validation: Kiểm tra guide có trùng lịch không
- Query: Kiểm tra guide có schedule khác trong khoảng thời gian `start_date` đến `end_date` không
- Nếu trùng: Cảnh báo nhưng vẫn cho phép lưu (có thể 1 guide đi nhiều tour cùng lúc nếu xa nhau)

**Bước 5: Lưu Phân công & Cập nhật Trạng thái**

- Update `tour_schedules`:
  - `guide_id` = guide đã chọn
  - `guide_notes` = ghi chú (nếu có)
  - `status` = 'pending' (nếu hiện tại là 'open' hoặc 'closed')
- (Optional) Tạo record trong `tour_assignments` nếu cần quản lý lương guide chi tiết hơn
- Hiển thị success: "Phân công guide thành công! Tour đã sẵn sàng khởi hành (trạng thái: Pending)."
- Có thể gửi thông báo đến guide (nếu có hệ thống notification)

**Bước 5: Xem Lịch trình đã Phân công**

- Trong danh sách lịch trình, hiển thị:
  - Guide đã phân công (nếu có)
  - Có thể click để xem chi tiết hoặc thay đổi guide

---

### **LUỒNG 3: ĐÓNG/MỞ LỊCH TRÌNH (TOUR-012)**

**Mô tả luồng:**

**Bước 1: Chọn Lịch trình**

- Từ danh sách lịch trình tour
- Click "Đóng đặt" hoặc "Mở đặt"

**Bước 2: Xác nhận Đóng/Mở**

- **Nếu đóng (`status = 'closed'`):**
  - Cảnh báo: "Lịch trình này sẽ không thể đặt thêm booking mới. Bạn có chắc chắn?"
  - Kiểm tra: Nếu đã có booking (`booked > 0`) → Cảnh báo nhưng vẫn cho phép đóng
- **Nếu mở (`status = 'open'`):**
  - Xác nhận: "Lịch trình này sẽ được mở lại để đặt booking. Bạn có chắc chắn?"

**Bước 3: Cập nhật Trạng thái**

- Update `tour_schedules.status`:
  - `open` ↔ `closed`
- Lưu ý: Không cho phép đổi sang `completed` hoặc `cancelled` từ đây (có luồng riêng)

**Bước 4: Hiển thị Kết quả**

- Hiển thị success: "Đã đóng/mở lịch trình thành công!"
- Trạng thái trong danh sách được cập nhật ngay

---

### **LUỒNG 4: HỦY LỊCH TRÌNH (TOUR-013)**

**Mô tả luồng:**

**Bước 1: Chọn Lịch trình**

- Từ danh sách lịch trình tour
- Click "Hủy lịch trình"
- Chỉ hiển thị nút này nếu `status` = 'open' hoặc 'closed' (chưa hoàn thành)

**Bước 2: Kiểm tra Điều kiện**

- **Kiểm tra bookings:**
  - Query: Kiểm tra có booking nào có `tour_schedule_id` = schedule này không
  - Nếu có booking:
    - Đếm số booking và số khách
    - Cảnh báo: "Lịch trình này đã có X booking với Y khách. Bạn có chắc chắn muốn hủy?"
    - Lưu ý: Cần xử lý các booking đã đặt (hủy booking hoặc chuyển sang schedule khác)

**Bước 3: Xác nhận Hủy**

- Form xác nhận:
  - Hiển thị thông tin lịch trình
  - Số booking và số khách (nếu có)
  - **Lý do hủy:** Textarea - Optional
  - Checkbox: "Tự động hủy tất cả bookings liên quan" (nếu có booking)

**Bước 4: Xử lý Bookings & Hoàn tiền**

- **Hiển thị thông tin bookings:**

  - Danh sách tất cả bookings có `tour_schedule_id` = schedule này
  - Mỗi booking hiển thị:
    - Mã booking
    - Khách hàng
    - Số người
    - Tổng tiền
    - Số tiền đã thanh toán
    - Số tiền còn lại

- **Option 1: Tự động hủy bookings & Hoàn tiền 100%**

  - Cập nhật tất cả bookings có `tour_schedule_id` = schedule này:
    - `approval_status` = 'cancelled'
    - `cancellation_reason` = "Lịch trình tour bị hủy: [Lý do hủy]"
    - `cancellation_fee` = 0 (không tính phí hủy vì lỗi từ công ty)
    - `refund_amount` = `paid_amount` (hoàn lại 100% số tiền đã thanh toán)
    - `payment_status` = 'refunded' hoặc 'refund_pending'
  - **Tạo yêu cầu hoàn tiền (refunds):**
    - Tự động tạo records trong bảng `refunds` (nếu có)
    - `refund_amount` = `paid_amount` của từng booking
    - `refund_reason` = "Tour bị hủy bởi công ty"
    - `refund_status` = 'pending' (chờ duyệt và xử lý)
  - **Cập nhật quota:**
    - Trả lại quota: `booked` = `booked` - tổng số người của các bookings
  - Gửi thông báo đến khách hàng (nếu có hệ thống notification)

- **Option 2: Chuyển sang schedule khác**

  - Cho phép chọn schedule khác (cùng tour, `status = 'open'` hoặc 'pending')
  - Kiểm tra quota của schedule mới (phải đủ chỗ)
  - Cập nhật `tour_schedule_id` của bookings sang schedule mới
  - Cập nhật quota:
    - Schedule cũ: `booked` giảm
    - Schedule mới: `booked` tăng
  - **Lưu ý:** Không hoàn tiền nếu chuyển schedule thành công

- **Option 3: Hủy bookings & Hoàn tiền theo chính sách hủy**
  - Áp dụng chính sách hủy tour (`tour_policies` có `policy_type = 'cancellation'`)
  - Tính phí hủy theo % trong chính sách
  - `refund_amount` = `paid_amount` - `cancellation_fee`
  - Tạo yêu cầu hoàn tiền tương ứng

**Bước 5: Cập nhật Trạng thái Schedule**

- Update `tour_schedules`:
  - `status` = 'cancelled'
  - `booked` = 0 (nếu đã hủy tất cả bookings)
- Lưu ý: Không xóa schedule, chỉ đổi trạng thái

**Bước 6: Hiển thị Kết quả**

- Hiển thị success: "Đã hủy lịch trình thành công!"
- Hiển thị thông tin: "Đã hủy X booking" (nếu có)
- Schedule được đánh dấu `status = 'cancelled'` trong danh sách

---

## 📊 CÁC TRƯỜNG DỮ LIỆU LIÊN QUAN

### **1. Bảng `tour_schedules`**

| Trường       | Loại          | Bắt buộc | Mô tả                                               | Validation                                           |
| ------------ | ------------- | -------- | --------------------------------------------------- | ---------------------------------------------------- |
| id           | INT           | ✅       | Primary key                                         | AUTO_INCREMENT                                       |
| tour_id      | INT           | ✅       | Foreign key → tours                                 | NOT NULL, FK                                         |
| start_date   | DATE          | ✅       | Ngày khởi hành                                      | NOT NULL, >= ngày hiện tại                           |
| end_date     | DATE          | ✅       | Ngày kết thúc                                       | NOT NULL, end_date - start_date + 1 = duration_days  |
| quota        | INT           | ✅       | Số chỗ (tổng)                                       | DEFAULT 20, >= min_participants, <= max_participants |
| booked       | INT           | ✅       | Số chỗ đã đặt                                       | DEFAULT 0, <= quota                                  |
| adult_price  | DECIMAL(15,2) | ❌       | Giá người lớn                                       | NULL hoặc > 0 (nếu NULL → dùng giá từ tour)          |
| child_price  | DECIMAL(15,2) | ❌       | Giá trẻ em                                          | NULL hoặc > 0 (nếu NULL → dùng giá từ tour)          |
| infant_price | DECIMAL(15,2) | ❌       | Giá em bé                                           | NULL hoặc >= 0 (nếu NULL → dùng giá từ tour)         |
| status       | ENUM          | ✅       | open/closed/pending/in_progress/completed/cancelled | DEFAULT 'open'                                       |
| guide_id     | INT           | ❌       | Foreign key → users (role = guide)                  | FK, NULL                                             |
| guide_notes  | TEXT          | ❌       | Ghi chú cho guide                                   |                                                      |
| created_at   | TIMESTAMP     | ❌       | Ngày tạo                                            |                                                      |
| updated_at   | TIMESTAMP     | ❌       | Ngày cập nhật                                       |                                                      |

**Mối quan hệ:**

- N Tour Schedules → 1 Tour (`tour_id`) - REQUIRED
- N Tour Schedules → 1 User (`guide_id`) - Optional

**Business Logic:**

- Unique constraint: `(tour_id, start_date, end_date)` - Không được trùng lịch trình cho cùng tour
- `end_date` = `start_date` + `duration_days` - 1 (tự động tính)
- `quota` phải nằm trong khoảng `min_participants` đến `max_participants` của tour
- `booked` <= `quota` (validate khi tạo booking)
- `adult_price`, `child_price`, `infant_price`:
  - Nếu NULL → Dùng giá từ tour (`tours.adult_price`, `tours.child_price`, `tours.infant_price`)
  - Nếu có giá → Ưu tiên dùng giá của schedule (cho phép điều chỉnh giá theo từng chuyến)
- `status`:
  - `open`: Mở đặt booking (đang nhận đặt)
  - `closed`: Đóng đặt (không nhận booking mới, nhưng chưa đủ điều kiện khởi hành)
  - `pending`: Trước đi (đã đủ số người tối thiểu, đã phân công guide, chờ đến ngày khởi hành)
  - `in_progress`: Đang đi (tour đang diễn ra)
  - `completed`: Đã đi xong (tour đã hoàn thành)
  - `cancelled`: Tour bị hủy (không đủ số người hoặc lý do khác)

**Tính toán tự động:**

- `available_slots` = `quota` - `booked` (số chỗ còn lại)
- `booking_percentage` = (`booked` / `quota`) × 100 (phần trăm đã đặt)

---

### **2. Bảng `tours` (Liên quan)**

- `duration_days`, `duration_nights`: Dùng để tính `end_date`
- `min_participants`, `max_participants`: Validate `quota`
- `adult_price`, `child_price`, `infant_price`: Giá mặc định nếu schedule không set giá riêng

---

### **3. Bảng `bookings` (Liên quan)**

- `tour_schedule_id`: Link booking với schedule
- `status`: Trạng thái booking (pending, confirmed, cancelled...)
- `adult_count`, `child_count`, `infant_count`: Số khách, dùng để tính `booked`

---

## 🔒 BUSINESS RULES

1. **Tạo Schedule:**

   - Chỉ tour có `status = 'active'` AND `approval_status = 'approved'` mới được tạo schedule
   - `start_date` phải >= ngày hiện tại
   - `quota` phải nằm trong khoảng `min_participants` đến `max_participants`
   - Không được trùng lịch trình (cùng tour, cùng ngày khởi hành)

2. **Giá Schedule:**

   - Nếu schedule có giá riêng → Ưu tiên dùng giá của schedule
   - Nếu schedule không có giá (NULL) → Dùng giá từ tour
   - Cho phép điều chỉnh giá theo từng chuyến (VD: Giá cao hơn vào dịp lễ)

3. **Quota & Booking:**

   - `booked` tự động cập nhật khi tạo/hủy booking
   - Không cho phép đặt booking nếu `booked` >= `quota`
   - Không cho phép đặt booking nếu `status` = 'closed' hoặc 'cancelled'

4. **Phân công Guide:**

   - **Điều kiện bắt buộc:** Chỉ phân công guide khi `booked >= min_participants` (đủ số người tối thiểu)
   - Guide phải có `role = 'guide'` AND `status = 'active'`
   - Có thể phân công guide sau khi tạo schedule (khi đủ số người)
   - Có thể thay đổi guide (cập nhật `guide_id`)
   - Sau khi phân công guide → Tự động set `status = 'pending'` (nếu hiện tại là 'open' hoặc 'closed')

5. **Đóng/Mở Schedule:**

   - Chỉ schedule `status = 'open'` hoặc `closed'` mới có thể đóng/mở
   - Schedule `status = 'pending'`, `in_progress'`, `completed'` hoặc `cancelled'` không thể đóng/mở

6. **Chuyển Trạng thái Tự động:**

   - `open` → `pending`: Khi `booked >= min_participants` AND đã phân công guide (tự động hoặc thủ công)
   - `pending` → `in_progress`: Khi `start_date = ngày hiện tại` (tự động khi tour khởi hành)
   - `in_progress` → `completed`: Khi `end_date < ngày hiện tại` (tự động khi tour kết thúc)
   - `open`/`closed`/`pending` → `cancelled`: Khi hủy tour (thủ công hoặc tự động)

7. **Hủy Tour vì Không đủ Số người:**

   - **Điều kiện:** Khi đến ngày khởi hành (`start_date`) và `booked < min_participants`
   - **Tự động hủy:** Có thể cấu hình để tự động hủy hoặc cảnh báo staff
   - **Xử lý bookings:** Tự động hủy tất cả bookings và hoàn tiền 100% (không tính phí hủy)

8. **Hủy Schedule:**

   - Chỉ schedule chưa hoàn thành (`status != 'completed'`) mới có thể hủy
   - Nếu có booking → Phải xử lý bookings trước (hủy hoặc chuyển schedule)
   - Không xóa schedule, chỉ đổi `status = 'cancelled'`

9. **Auto-complete Schedule:**

   - Khi `end_date < ngày hiện tại` AND `status = 'in_progress'` → Tự động set `status = 'completed'` (chạy cron job)

---

## ⚠️ TRƯỜNG HỢP ĐẶC BIỆT

1. **Schedule Trùng Ngày:**

   - Unique constraint: `(tour_id, start_date, end_date)` không cho phép trùng
   - Nếu user nhập trùng → Hiển thị error: "Đã có lịch trình cho tour này trong khoảng thời gian này"

2. **Guide Trùng Lịch:**

   - Validation: Kiểm tra guide có schedule khác trong khoảng thời gian không
   - Nếu trùng: Cảnh báo nhưng vẫn cho phép lưu (có thể 1 guide đi nhiều tour nếu xa nhau)
   - Có thể hiển thị danh sách schedule trùng để user quyết định

3. **Hủy Schedule có Booking:**

   - Có 3 options:
     - Option 1: Tự động hủy tất cả bookings & Hoàn tiền 100% (không tính phí hủy)
     - Option 2: Chuyển bookings sang schedule khác (nếu có schedule khác còn chỗ)
     - Option 3: Hủy bookings & Hoàn tiền theo chính sách hủy (tính phí hủy)
   - User phải chọn option trước khi hủy
   - Tự động tạo yêu cầu hoàn tiền (refunds) cho từng booking

4. **Hủy Tour vì Không đủ Số người:**

   - **Khi nào:** Đến ngày khởi hành (`start_date`) nhưng `booked < min_participants`
   - **Xử lý tự động:**
     - Set `status = 'cancelled'`
     - Hủy tất cả bookings
     - Hoàn tiền 100% (không tính phí hủy vì lỗi từ công ty)
     - Gửi thông báo đến khách hàng
   - **Cảnh báo trước:** Có thể cảnh báo staff trước ngày khởi hành (VD: 2-3 ngày trước) nếu chưa đủ số người

5. **Quota Thay đổi:**

   - Nếu giảm `quota` và `booked` > `quota` mới → Không cho phép (phải hủy booking trước)
   - Nếu tăng `quota` → Cho phép (mở thêm chỗ)

6. **Hoàn tiền khi Hủy Tour:**

   - **Hoàn tiền 100%:** Khi tour bị hủy vì lỗi từ công ty (không đủ số người, lý do khách quan)
     - `refund_amount` = `paid_amount`
     - `cancellation_fee` = 0
   - **Hoàn tiền theo chính sách:** Khi hủy vì lý do khác (nếu chọn Option 3)
     - Tính phí hủy theo `tour_policies`
     - `refund_amount` = `paid_amount` - `cancellation_fee`
   - Tự động tạo records trong bảng `refunds` để xử lý hoàn tiền

---

**Ngày tạo:** 2024-12-06
