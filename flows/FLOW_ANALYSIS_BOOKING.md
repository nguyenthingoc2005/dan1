# 📊 PHÂN TÍCH LUỒNG: BOOKING MODULE

## 🎯 THÔNG TIN CHUNG

- **Module:** Booking Management 📝
- **Mục đích:** Quản lý đặt tour của khách hàng (tạo booking, quản lý khách, dịch vụ, tính tiền, duyệt/hủy booking)
- **Ngày tạo:** 2024-12-06
- **Status:** ⏳ Đang phân tích

---

## 📋 MÔ TẢ TỔNG QUAN

Module Booking quản lý việc đặt tour của khách hàng:

- **Booking:** Một đơn đặt tour của khách hàng cho một tour schedule cụ thể
- **Khách hàng trong Booking:** Danh sách khách tham gia (adult/child/infant)
- **Dịch vụ Booking:** Các dịch vụ thêm vào booking (nếu có)
- **Thanh toán:** Theo dõi tiền đặt cọc, thanh toán, hoàn tiền

**Ví dụ:**

- Khách hàng A đặt tour "Đà Lạt 3 ngày 2 đêm" khởi hành 15/12/2024
- Số người: 2 người lớn, 1 trẻ em
- Tổng tiền: 4,500,000đ
- Đặt cọc: 1,350,000đ (30%)
- Còn lại: 3,150,000đ

**Lưu ý:** Các chức năng CRUD cơ bản (xem, thêm, sửa, xóa) sẽ được bỏ qua, tập trung vào **luồng thao tác phức tạp của người dùng**.

**Quy tắc quan trọng:**

- **Deadline đặt booking:** ✅ **Phải đặt trước 1 ngày** (không thể đặt vào ngày khởi hành)
- **Điều kiện đi tour:** Booking phải `approved` và **PHẢI thanh toán đủ** (`paid`) mới cho đi
- Xem chi tiết: [BOOKING_DEADLINE_AND_CHECKIN_RULES.md](./BOOKING_DEADLINE_AND_CHECKIN_RULES.md)

---

## 🔄 CÁC LUỒNG CHÍNH

### **LUỒNG 1: TẠO BOOKING MỚI (BOOK-002)**

**Mô tả luồng:**

**Bước 1: Chọn Tour Schedule**

- User (Staff/Admin) vào module "Quản lý Booking"
- URL: `?act=admin&module=bookings` hoặc `?act=staff-bookings`
- Click "Tạo booking mới"
- URL: `?act=admin&module=bookings&action=create` hoặc `?act=staff-bookings&action=create`

**Bước 2: Chọn Khách hàng**

- **Option 1: Chọn khách hàng có sẵn**
  - Dropdown: Danh sách khách hàng (filter: `status = 'active'`)
  - Search/filter: Tìm theo tên, phone, email
  - Hiển thị: Tên + Phone + Email
- **Option 2: Tạo khách hàng mới**
  - Click "Thêm khách hàng mới" → Mở modal
  - Form tạo customer (như module Customer)
  - Sau khi tạo → Auto-select customer mới

**Bước 3: Chọn Tour & Schedule**

- **Chọn Tour:**
  - Dropdown: Danh sách tour (filter: `status = 'active'` AND `approval_status = 'approved'`)
  - Hiển thị: Tên tour + Mã tour + Số ngày/đêm
- **Chọn Schedule (Lịch trình khởi hành):**
  - Dropdown: Danh sách schedules của tour đã chọn
  - Filter: `status = 'open'` hoặc `pending'` (chỉ hiển thị schedule còn nhận booking)
  - Hiển thị:
    - Ngày khởi hành
    - Ngày kết thúc
    - Số chỗ còn lại: `quota - booked`
    - Giá (nếu schedule có giá riêng)
    - **Deadline đặt booking:** (nếu có quy định)
      - VD: "Có thể đặt đến 2 ngày trước ngày khởi hành"
      - Hoặc: "Có thể đặt đến ngày khởi hành"
  - Validation:
    - Chỉ cho phép chọn schedule còn chỗ (`quota - booked > 0`)
    - **Deadline đặt booking:** ✅ Phải đặt trước 1 ngày
      - Validation: `start_date >= ngày hiện tại + 1 ngày`
      - Nếu vi phạm → Hiển thị error: "Không thể đặt booking. Phải đặt trước 1 ngày so với ngày khởi hành."
- **Hiển thị thông tin tour:**
  - Tên tour
  - Số ngày/đêm
  - Giá mặc định (từ tour hoặc schedule)
  - **Lưu ý deadline:** "⚠️ Vui lòng đặt trước 1 ngày so với ngày khởi hành"

**Bước 4: Nhập Số người**

- Form fields:
  - **Số người lớn:** Number - REQUIRED (default: 0, min: 0)
  - **Số trẻ em:** Number - REQUIRED (default: 0, min: 0)
  - **Số em bé:** Number - REQUIRED (default: 0, min: 0)
  - **Tổng số người:** Auto = adult + child + infant (Display only)
- Validation:
  - Tổng số người phải > 0
  - Tổng số người phải <= số chỗ còn lại của schedule (`quota - booked`)
- **Tự động tính giá:**
  - Giá người lớn = `schedule.adult_price` hoặc `tour.adult_price` (ưu tiên schedule)
  - Giá trẻ em = `schedule.child_price` hoặc `tour.child_price`
  - Giá em bé = `schedule.infant_price` hoặc `tour.infant_price`
  - `total_amount` = (adult_price × adult_count) + (child_price × child_count) + (infant_price × infant_count)

**Bước 5: Thêm Khách hàng vào Booking** (Optional - có thể làm sau)

- **Option 1: Thêm từ danh sách khách hàng có sẵn**
  - Button "Thêm khách hàng"
  - Modal: Chọn từ danh sách customers
  - Với mỗi khách hàng:
    - **Phân loại:** Dropdown (adult/child/infant) - REQUIRED
    - **Đánh dấu khách chính:** Checkbox (chỉ 1 khách được chọn)
- **Option 2: Thêm thông tin khách mới** (chưa có trong hệ thống)
  - Button "Thêm khách mới"
  - Form nhập thông tin:
    - Tên, Phone, Email, CMND/CCCD, Ngày sinh
    - **Phân loại:** Dropdown (adult/child/infant) - REQUIRED
    - **Đánh dấu khách chính:** Checkbox
  - Sau khi lưu → Tự động tạo customer mới và thêm vào booking
- **Validation:**
  - Tổng số khách trong danh sách phải = `adult_count + child_count + infant_count`
  - Phải có ít nhất 1 khách chính (`is_primary = 1`)
  - Không được trùng khách hàng trong cùng booking (nếu chọn từ danh sách)

**Bước 6: Thêm Dịch vụ (Optional)**

- Danh sách dịch vụ có thể thêm:
  - Filter: Dịch vụ từ tour schedule (từ `itinerary_day_services` của tour)
  - Hoặc dịch vụ khác (từ `services`)
- Với mỗi dịch vụ:
  - **Tên dịch vụ:** Dropdown - REQUIRED
  - **Nhà dịch vụ:** Dropdown (optional) - Tự động filter theo service
  - **Số lượng:** Number (default: 1)
  - **Đơn giá:** Number - REQUIRED (có thể auto-fill từ service)
  - **Tổng tiền:** Auto = đơn giá × số lượng
  - **Ghi chú:** Textarea (optional)
- **Tổng tiền dịch vụ:** Auto = Σ(tổng tiền của tất cả dịch vụ)
- **Lưu ý:** Dịch vụ này sẽ được tính riêng, không bao gồm trong `total_amount` của booking (tùy logic nghiệp vụ)

**Bước 7: Tính toán Giá & Đặt cọc**

- **Tổng tiền tour:**
  ```
  total_amount = (adult_price × adult_count) + (child_price × child_count) + (infant_price × infant_count)
  ```
- **Giảm giá (Discount):**
  - **Mã giảm giá:** Text (optional) - Nếu có mã giảm giá
  - **Hoặc giảm giá thủ công:** Number (optional) - Staff có thể nhập số tiền giảm
  - `discount_amount` = Số tiền giảm (validate: <= total_amount)
- **Tổng tiền sau giảm giá:**
  ```
  final_amount = total_amount - discount_amount
  ```
- **Đặt cọc (Deposit):**
  - **Tiền cọc:** Number - Optional (default: `final_amount × deposit_percentage` từ tour)
  - Validation: `deposit_amount <= final_amount`
- **Còn lại:**
  ```
  remaining_amount = final_amount - deposit_amount
  ```
- **Trạng thái thanh toán (Auto):**
  - Nếu `deposit_amount = 0` → `payment_status = 'unpaid'`
  - Nếu `deposit_amount > 0` AND `deposit_amount < final_amount` → `payment_status = 'partial'`
  - Nếu `deposit_amount = final_amount` → `payment_status = 'paid'`
- **Hiển thị breakdown:**
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
  └─────────────────────────────────────────┘
  ```

**Bước 8: Thông tin Bổ sung**

- **Nguồn booking (Source):** Dropdown:
  - `phone` (Điện thoại)
  - `email` (Email)
  - `facebook` (Facebook)
  - `zalo` (Zalo)
  - `walk_in` (Đến trực tiếp)
  - `other` (Khác)
- **Yêu cầu đặc biệt:** Textarea (optional)
  - VD: "Khách ăn chay", "Cần phòng riêng", "Khách có trẻ em nhỏ..."
- **Ghi chú:** Textarea (optional)
  - Ghi chú cho khách hàng
- **Ghi chú nội bộ:** Textarea (optional)
  - Ghi chú chỉ staff/admin mới thấy

**Bước 9: Validate & Save**

- Server-side validation:
  - `customer_id`: Required
  - `tour_id`: Required, tour phải `status = 'active'` AND `approval_status = 'approved'`
  - `start_date`: Required, phải có schedule `status = 'open'` hoặc `pending'`
  - **Deadline đặt booking:** ✅ Phải đặt trước 1 ngày
    - Validation: `start_date >= ngày hiện tại + 1 ngày`
    - Nếu vi phạm → Hiển thị error: "Không thể đặt booking. Phải đặt trước 1 ngày so với ngày khởi hành. (Hôm nay: {ngày hiện tại}, Ngày khởi hành tối thiểu: {ngày hiện tại + 1 ngày})"
  - `adult_count + child_count + infant_count`: > 0
  - `total_amount`: > 0
  - `discount_amount`: <= `total_amount`
  - `deposit_amount`: <= `final_amount`
  - Kiểm tra quota: Tổng số người phải <= số chỗ còn lại của schedule
  - Kiểm tra duplicate: Không được trùng booking (cùng customer, tour, start_date)
- Nếu validation pass:
  - Start transaction
  - Auto-generate `booking_code` (format: BK-YYYYMMDD-XXX)
  - Insert vào `bookings`:
    - `approval_status` = 'pending' (chờ duyệt)
    - `payment_status` = Auto (theo deposit_amount)
    - `created_by` = current user ID
  - Insert `booking_customers` (nếu đã thêm khách ở Bước 5)
  - Insert `booking_services` (nếu đã thêm dịch vụ ở Bước 6)
  - **Cập nhật quota của schedule:**
    - `tour_schedules.booked` = `booked + total_participants`
    - Kiểm tra nếu `booked >= quota` → Tự động set `status = 'closed'` (nếu cần)
  - Commit transaction
  - Hiển thị success: "Tạo booking thành công! Mã booking: BK-..."
  - Redirect về chi tiết booking hoặc danh sách bookings
- Nếu validation fail:
  - Hiển thị errors
  - Giữ lại dữ liệu đã nhập
  - Quay lại form với errors

---

### **LUỒNG 2: DUYỆT BOOKING (BOOK-004)**

**Mô tả luồng:**

**Bước 1: Xem Danh sách Booking Pending**

- URL: `?act=admin&module=bookings&status=pending`
- Hiển thị danh sách bookings có `approval_status = 'pending'`
- Mỗi booking hiển thị:
  - Mã booking
  - Khách hàng
  - Tour & Ngày khởi hành
  - Số người
  - Tổng tiền
  - Đặt cọc
  - Ngày tạo

**Bước 2: Xem Chi tiết Booking**

- Click vào booking → Xem chi tiết
- URL: `?act=admin&module=bookings&action=view&id=X`
- Hiển thị đầy đủ thông tin:
  - Thông tin booking (mã, tour, schedule, ngày...)
  - Thông tin khách hàng
  - Danh sách khách tham gia
  - Dịch vụ đã đặt (nếu có)
  - Tính toán giá (breakdown)
  - Thanh toán (đã trả, còn lại)
  - Trạng thái (pending/approved/rejected/cancelled)

**Bước 3: Xác nhận Duyệt Booking**

- Click "Duyệt booking"
- Xác nhận: "Bạn có chắc chắn muốn duyệt booking này?"
- Form xác nhận (optional):
  - **Ghi chú:** Textarea (optional)

**Bước 4: Lưu Duyệt**

- Update `bookings`:
  - `approval_status` = 'approved'
  - `approved_by` = current user ID
  - `approved_at` = NOW()
- Log history: Tạo record trong `booking_status_history`
- **Cập nhật quota schedule:**
  - Nếu booking đã được approved trước đó → Không cập nhật lại quota (đã cập nhật khi tạo)
  - Hoặc: Quota chỉ cập nhật khi booking được approved (tùy logic nghiệp vụ)
- **Kiểm tra schedule:**
  - Nếu sau khi duyệt, `booked >= min_participants` → Có thể phân công guide (nếu chưa có)
- **Lưu ý về thanh toán:**
  - Booking có thể được duyệt dù chưa thanh toán đủ (`payment_status = 'unpaid'` hoặc 'partial')
  - ✅ **Tuy nhiên, khi tour khởi hành, PHẢI thanh toán đủ mới cho đi** (`payment_status = 'paid'` và `remaining_amount = 0`)
- Hiển thị success: "Đã duyệt booking thành công!"
- Gửi thông báo đến khách hàng (nếu có hệ thống notification)

---

### **LUỒNG 3: TỪ CHỐI BOOKING (BOOK-005)**

**Mô tả luồng:**

**Bước 1: Chọn Booking để Từ chối**

- Từ danh sách booking hoặc chi tiết booking
- Click "Từ chối booking"

**Bước 2: Form Từ chối**

- Modal hoặc form riêng
- Form fields:
  - **Lý do từ chối:** Textarea - REQUIRED
    - VD: "Không đủ số người", "Tour đã đầy", "Lịch trình không phù hợp"...

**Bước 3: Xác nhận Từ chối**

- Xác nhận: "Bạn có chắc chắn muốn từ chối booking này?"
- Cảnh báo: "Booking này sẽ bị từ chối và không thể hoàn tác."

**Bước 4: Lưu Từ chối & Hoàn tiền**

- Update `bookings`:
  - `approval_status` = 'rejected'
  - `rejected_by` = current user ID (nếu có field)
  - `rejection_reason` = lý do từ chối
  - `rejected_at` = NOW() (nếu có field)
- Log history: Tạo record trong `booking_status_history`
- **Hoàn tiền (nếu đã đặt cọc):**
  - Nếu `paid_amount > 0`:
    - `refund_amount` = `paid_amount` (hoàn lại 100% vì lỗi từ công ty)
    - `payment_status` = 'refunded' hoặc 'refund_pending'
    - Tạo record trong `refunds` để xử lý hoàn tiền
- **Cập nhật quota schedule:**
  - `tour_schedules.booked` = `booked - total_participants` (trả lại chỗ)
- Hiển thị success: "Đã từ chối booking. Đã tạo yêu cầu hoàn tiền (nếu có)."
- Gửi thông báo đến khách hàng (nếu có hệ thống notification)

---

### **LUỒNG 4: HỦY BOOKING (BOOK-006)**

**Mô tả luồng:**

**Bước 1: Chọn Booking để Hủy**

- Từ danh sách booking hoặc chi tiết booking
- Click "Hủy booking"
- **Lưu ý:** Chỉ cho phép hủy booking `approval_status = 'pending'` hoặc 'approved'
- Booking `status = 'completed'` hoặc đã `cancelled'` không thể hủy

**Bước 2: Form Hủy Booking**

- Modal hoặc form riêng
- Form fields:
  - **Lý do hủy:** Textarea - REQUIRED
    - VD: "Khách hàng yêu cầu hủy", "Không thể tham gia"...
  - **Chọn chính sách hủy:** Dropdown - Optional
    - Filter: Chính sách hủy từ tour (`tour_policies` có `policy_type = 'cancellation'`)
    - Hiển thị: Tên chính sách + Mô tả
    - Nếu không chọn → Dùng chính sách mặc định

**Bước 3: Tính Phí Hủy**

- **Nếu có chính sách hủy:**
  - Load chính sách hủy (từ `policies` hoặc `tour_policies`)
  - Tính phí hủy dựa trên:
    - Số ngày trước ngày khởi hành
    - % phí hủy theo chính sách
  - **Ví dụ:**
    - Hủy trước 7 ngày: Phí 0%
    - Hủy trước 3 ngày: Phí 30%
    - Hủy trước 1 ngày: Phí 50%
    - Hủy trong ngày: Phí 100%
- **Tính hoàn tiền:**
  ```
  cancellation_fee = final_amount × (phí %)
  refund_amount = paid_amount - cancellation_fee
  ```
- **Hiển thị breakdown:**
  ```
  ┌─────────────────────────────────────────┐
  │ PHÂN TÍCH HỦY BOOKING                   │
  │ ─────────────────────────────────────── │
  │ Tổng tiền: 3,750,000đ                  │
  │ Đã thanh toán: 1,125,000đ              │
  │ ─────────────────────────────────────── │
  │ Phí hủy (30%): 1,125,000đ              │
  │ ─────────────────────────────────────── │
  │ Hoàn tiền: 0đ (phí hủy = tiền đã trả)  │
  └─────────────────────────────────────────┘
  ```

**Bước 4: Xác nhận Hủy**

- Xác nhận: "Bạn có chắc chắn muốn hủy booking này?"
- Hiển thị:
  - Phí hủy
  - Số tiền sẽ hoàn lại (nếu có)
  - Cảnh báo: "Booking này sẽ bị hủy và không thể hoàn tác."

**Bước 5: Lưu Hủy & Hoàn tiền**

- Start transaction
- Update `bookings`:
  - `approval_status` = 'cancelled'
  - `cancellation_date` = NOW()
  - `cancellation_reason` = lý do hủy
  - `cancellation_policy_id` = ID chính sách hủy (nếu có)
  - `cancellation_fee` = phí hủy đã tính
  - `refund_amount` = số tiền hoàn lại
  - `payment_status`:
    - Nếu `refund_amount > 0` → 'refunded' hoặc 'refund_pending'
    - Nếu `refund_amount = 0` → Giữ nguyên hoặc 'refunded'
- Log history: Tạo record trong `booking_status_history`
- **Hoàn tiền (nếu có):**
  - Nếu `refund_amount > 0`:
    - Tạo record trong `refunds` để xử lý hoàn tiền
    - `refund_amount` = số tiền hoàn lại
    - `refund_reason` = "Hủy booking"
    - `refund_status` = 'pending' (chờ duyệt và xử lý)
- **Cập nhật quota schedule:**
  - `tour_schedules.booked` = `booked - total_participants` (trả lại chỗ)
- Commit transaction
- Hiển thị success: "Đã hủy booking. Phí hủy: XXXđ. Hoàn tiền: XXXđ (nếu có)."
- Gửi thông báo đến khách hàng (nếu có hệ thống notification)

---

### **LUỒNG 5: THÊM KHÁCH HÀNG VÀO BOOKING (BOOK-008)**

**Mô tả luồng:**

**Bước 1: Chọn Booking**

- Từ chi tiết booking
- Click "Thêm khách hàng"

**Bước 2: Form Thêm Khách**

- Modal hoặc form riêng
- **Option 1: Chọn từ danh sách**
  - Dropdown: Danh sách customers
  - Search/filter
- **Option 2: Thêm mới**
  - Form tạo customer mới
- Với mỗi khách:
  - **Phân loại:** Dropdown (adult/child/infant) - REQUIRED
  - **Đánh dấu khách chính:** Checkbox (chỉ 1 khách được chọn)

**Bước 3: Validate**

- Validation:
  - Tổng số khách sau khi thêm phải = `adult_count + child_count + infant_count`
  - Phải có ít nhất 1 khách chính (`is_primary = 1`)
  - Không được trùng khách trong cùng booking

**Bước 4: Lưu**

- Insert vào `booking_customers`
- Cập nhật `bookings.adult_count`, `child_count`, `infant_count` (nếu cần)
- Hiển thị success: "Đã thêm khách hàng vào booking!"

---

### **LUỒNG 6: THÊM DỊCH VỤ VÀO BOOKING (BOOK-013)**

**Mô tả luồng:**

**Bước 1: Chọn Booking**

- Từ chi tiết booking
- Tab "Dịch vụ" hoặc click "Thêm dịch vụ"

**Bước 2: Form Thêm Dịch vụ**

- Modal hoặc form riêng
- Form fields:
  - **Chọn dịch vụ:** Dropdown - REQUIRED
    - Filter: Services có `status = 'active'`
    - Có thể filter theo loại dịch vụ, nhà dịch vụ
  - **Nhà dịch vụ:** Dropdown (optional)
    - Filter theo service đã chọn
  - **Số lượng:** Number (default: 1, min: 1)
  - **Đơn giá:** Number - REQUIRED
    - Có thể auto-fill từ service
  - **Tổng tiền:** Auto = đơn giá × số lượng
  - **Ghi chú:** Textarea (optional)

**Bước 3: Lưu**

- Insert vào `booking_services`
- **Lưu ý:** Dịch vụ này tính riêng, không tự động cập nhật `total_amount` của booking (tùy logic nghiệp vụ)
- Hiển thị success: "Đã thêm dịch vụ vào booking!"

---

## 📊 CÁC TRƯỜNG DỮ LIỆU LIÊN QUAN

### **1. Bảng `bookings`**

| Trường                 | Loại          | Bắt buộc | Mô tả                                   | Validation                                  |
| ---------------------- | ------------- | -------- | --------------------------------------- | ------------------------------------------- |
| id                     | INT           | ✅       | Primary key                             | AUTO_INCREMENT                              |
| booking_code           | VARCHAR(50)   | ❌       | Mã booking                              | UNIQUE, auto-generate (BK-YYYYMMDD-XXX)     |
| tour_id                | INT           | ✅       | Foreign key → tours                     | NOT NULL, FK                                |
| tour_schedule_id       | INT           | ❌       | Foreign key → tour_schedules            | FK, NULL (optional, có thể dùng start_date) |
| customer_id            | INT           | ✅       | Foreign key → customers                 | NOT NULL, FK                                |
| adult_count            | INT           | ✅       | Số người lớn                            | DEFAULT 0, >= 0                             |
| child_count            | INT           | ✅       | Số trẻ em                               | DEFAULT 0, >= 0                             |
| infant_count           | INT           | ✅       | Số em bé                                | DEFAULT 0, >= 0                             |
| start_date             | DATE          | ✅       | Ngày khởi hành                          | NOT NULL                                    |
| end_date               | DATE          | ✅       | Ngày kết thúc                           | NOT NULL, >= start_date                     |
| total_amount           | DECIMAL(15,2) | ✅       | Tổng tiền tour                          | NOT NULL, > 0                               |
| discount_code          | VARCHAR(50)   | ❌       | Mã giảm giá                             |                                             |
| discount_amount        | DECIMAL(15,2) | ✅       | Số tiền giảm giá                        | DEFAULT 0.00, <= total_amount               |
| final_amount           | DECIMAL(15,2) | ✅       | Tổng tiền sau giảm giá                  | NOT NULL, = total_amount - discount_amount  |
| deposit_amount         | DECIMAL(15,2) | ✅       | Tiền đặt cọc                            | DEFAULT 0.00, <= final_amount               |
| paid_amount            | DECIMAL(15,2) | ✅       | Tổng số tiền đã thanh toán              | DEFAULT 0.00, <= final_amount               |
| remaining_amount       | DECIMAL(15,2) | ✅       | Số tiền còn lại                         | DEFAULT 0.00, = final_amount - paid_amount  |
| payment_status         | ENUM          | ✅       | unpaid/partial/paid/refunded            | DEFAULT 'unpaid'                            |
| approval_status        | ENUM          | ✅       | pending/approved/rejected/cancelled     | DEFAULT 'pending'                           |
| approved_by            | INT           | ❌       | Foreign key → users                     | FK, NULL                                    |
| approved_at            | TIMESTAMP     | ❌       | Thời gian duyệt                         |                                             |
| rejection_reason       | TEXT          | ❌       | Lý do từ chối                           |                                             |
| cancellation_date      | DATE          | ❌       | Ngày hủy                                |                                             |
| cancellation_reason    | TEXT          | ❌       | Lý do hủy                               |                                             |
| cancellation_policy_id | INT           | ❌       | Foreign key → policies                  | FK, NULL                                    |
| cancellation_fee       | DECIMAL(15,2) | ✅       | Phí hủy                                 | DEFAULT 0.00                                |
| refund_amount          | DECIMAL(15,2) | ✅       | Số tiền hoàn lại                        | DEFAULT 0.00                                |
| source                 | ENUM          | ❌       | phone/email/facebook/zalo/walk_in/other |                                             |
| special_requests       | TEXT          | ❌       | Yêu cầu đặc biệt                        |                                             |
| notes                  | TEXT          | ❌       | Ghi chú cho khách                       |                                             |
| internal_notes         | TEXT          | ❌       | Ghi chú nội bộ                          |                                             |
| created_by             | INT           | ❌       | Foreign key → users                     | FK, NULL                                    |
| created_at             | TIMESTAMP     | ❌       | Ngày tạo                                |                                             |
| updated_at             | TIMESTAMP     | ❌       | Ngày cập nhật                           |                                             |

**Mối quan hệ:**

- N Bookings → 1 Tour (`tour_id`) - REQUIRED
- N Bookings → 1 Tour Schedule (`tour_schedule_id`) - Optional
- N Bookings → 1 Customer (`customer_id`) - REQUIRED
- N Bookings → 1 User (`approved_by`) - Optional
- N Bookings → 1 User (`created_by`) - Optional
- N Bookings → 1 Policy (`cancellation_policy_id`) - Optional

**Business Logic:**

- `booking_code` = auto-generate (BK-YYYYMMDD-XXX)
- `total_amount` = (adult_price × adult_count) + (child_price × child_count) + (infant_price × infant_count)
- `final_amount` = `total_amount - discount_amount`
- `remaining_amount` = `final_amount - paid_amount`
- `payment_status`:
  - `unpaid`: Chưa thanh toán (`paid_amount = 0`)
  - `partial`: Thanh toán một phần (`paid_amount > 0` AND `paid_amount < final_amount`)
  - `paid`: Đã thanh toán đủ (`paid_amount = final_amount`)
  - `refunded`: Đã hoàn tiền (khi hủy booking)
- `approval_status`:
  - `pending`: Chờ duyệt (mặc định khi tạo)
  - `approved`: Đã duyệt (Admin duyệt)
  - `rejected`: Từ chối (Admin từ chối)
  - `cancelled`: Hủy (Staff/Khách hủy)

**Tính toán tự động:**

- `total_participants` = `adult_count + child_count + infant_count`
- `payment_percentage` = (`paid_amount` / `final_amount`) × 100

---

### **2. Bảng `booking_customers`**

| Trường      | Loại       | Bắt buộc | Mô tả                   | Validation      |
| ----------- | ---------- | -------- | ----------------------- | --------------- |
| id          | INT        | ✅       | Primary key             | AUTO_INCREMENT  |
| booking_id  | INT        | ✅       | Foreign key → bookings  | NOT NULL, FK    |
| customer_id | INT        | ✅       | Foreign key → customers | NOT NULL, FK    |
| age_type    | ENUM       | ✅       | adult/child/infant      | DEFAULT 'adult' |
| is_primary  | TINYINT(1) | ✅       | Khách chính             | DEFAULT 0       |

**Mối quan hệ:**

- N Booking Customers → 1 Booking (`booking_id`) - REQUIRED
- N Booking Customers → 1 Customer (`customer_id`) - REQUIRED

**Business Logic:**

- Tổng số records trong `booking_customers` phải = `adult_count + child_count + infant_count` của booking
- Phải có đúng 1 record có `is_primary = 1` (khách chính)
- `age_type` phải khớp với `adult_count`, `child_count`, `infant_count`

---

### **3. Bảng `booking_services`**

| Trường              | Loại          | Bắt buộc | Mô tả                           | Validation                        |
| ------------------- | ------------- | -------- | ------------------------------- | --------------------------------- |
| id                  | INT           | ✅       | Primary key                     | AUTO_INCREMENT                    |
| booking_id          | INT           | ✅       | Foreign key → bookings          | NOT NULL, FK                      |
| service_id          | INT           | ✅       | Foreign key → services          | NOT NULL, FK                      |
| service_provider_id | INT           | ❌       | Foreign key → service_providers | FK, NULL                          |
| service_name        | VARCHAR(200)  | ❌       | Tên dịch vụ (snapshot)          |                                   |
| quantity            | INT           | ✅       | Số lượng                        | DEFAULT 1, > 0                    |
| unit_price          | DECIMAL(15,2) | ✅       | Đơn giá                         | NOT NULL, > 0                     |
| total_price         | DECIMAL(15,2) | ✅       | Tổng tiền                       | NOT NULL, = quantity × unit_price |
| notes               | TEXT          | ❌       | Ghi chú                         |                                   |
| created_by          | INT           | ❌       | Foreign key → users             | FK, NULL                          |
| created_at          | TIMESTAMP     | ❌       | Ngày tạo                        |                                   |

**Mối quan hệ:**

- N Booking Services → 1 Booking (`booking_id`) - REQUIRED
- N Booking Services → 1 Service (`service_id`) - REQUIRED
- N Booking Services → 1 Service Provider (`service_provider_id`) - Optional

**Business Logic:**

- `service_name` lưu snapshot tên dịch vụ
- `total_price` = `quantity × unit_price`
- Dịch vụ này tính riêng, không tự động cập nhật `total_amount` của booking (tùy logic nghiệp vụ)

---

### **4. Bảng `booking_status_history`**

| Trường        | Loại      | Bắt buộc | Mô tả                  | Validation     |
| ------------- | --------- | -------- | ---------------------- | -------------- |
| id            | INT       | ✅       | Primary key            | AUTO_INCREMENT |
| booking_id    | INT       | ✅       | Foreign key → bookings | NOT NULL, FK   |
| old_status    | ENUM      | ❌       | Trạng thái cũ          |                |
| new_status    | ENUM      | ✅       | Trạng thái mới         | NOT NULL       |
| changed_by    | INT       | ❌       | Foreign key → users    | FK, NULL       |
| change_reason | TEXT      | ❌       | Lý do thay đổi         |                |
| notes         | TEXT      | ❌       | Ghi chú                |                |
| created_at    | TIMESTAMP | ❌       | Thời gian thay đổi     |                |

**Mối quan hệ:**

- N Booking Status History → 1 Booking (`booking_id`) - REQUIRED
- N Booking Status History → 1 User (`changed_by`) - Optional

**Business Logic:**

- Tự động log mỗi khi `approval_status` hoặc `payment_status` thay đổi
- Lưu lịch sử đầy đủ các thay đổi trạng thái booking

---

## 🔒 BUSINESS RULES

1. **Tạo Booking:**

   - Chỉ tour có `status = 'active'` AND `approval_status = 'approved'` mới được đặt booking
   - Chỉ schedule có `status = 'open'` hoặc `pending'` mới được đặt booking
   - Tổng số người phải <= số chỗ còn lại của schedule (`quota - booked`)
   - Không được trùng booking (cùng customer, tour, start_date)
   - `total_amount` phải > 0

2. **Tính Giá:**

   - Giá ưu tiên: Schedule price > Tour price
   - `total_amount` = (adult_price × adult_count) + (child_price × child_count) + (infant_price × infant_count)
   - `discount_amount` <= `total_amount`
   - `final_amount` = `total_amount - discount_amount`
   - `deposit_amount` <= `final_amount`

3. **Quota & Booking:**

   - Khi tạo booking → Tự động tăng `tour_schedules.booked`
   - Khi hủy booking → Tự động giảm `tour_schedules.booked`
   - Nếu `booked >= quota` → Không cho phép đặt booking mới

4. **Duyệt Booking:**

   - Chỉ Admin mới có thể duyệt booking
   - Booking `approval_status = 'pending'` mới được duyệt
   - Sau khi duyệt → Không thể sửa thông tin chính (tour, schedule, số người...)

5. **Hủy Booking:**

   - Tính phí hủy theo chính sách hủy của tour
   - Phí hủy dựa trên số ngày trước ngày khởi hành
   - `refund_amount` = `paid_amount - cancellation_fee`
   - Tự động tạo yêu cầu hoàn tiền (refunds) nếu có refund

6. **Thanh toán:**

   - `paid_amount` tự động cập nhật khi có payment
   - `remaining_amount` = `final_amount - paid_amount`
   - `payment_status` tự động update theo `paid_amount` và `final_amount`

---

## ⚠️ TRƯỜNG HỢP ĐẶC BIỆT

1. **Booking Trùng:**

   - Kiểm tra duplicate: Cùng customer, tour, start_date
   - Nếu trùng → Hiển thị error và đề xuất booking đã tồn tại

2. **Schedule Hết Chỗ:**

   - Khi tạo booking: Kiểm tra `quota - booked >= total_participants`
   - Nếu không đủ → Hiển thị error: "Schedule này chỉ còn X chỗ. Bạn đã chọn Y người."
   - Đề xuất: Giảm số người hoặc chọn schedule khác

3. **Hủy Booking Đã Thanh toán:**

   - Tính phí hủy theo chính sách
   - Nếu `cancellation_fee >= paid_amount` → Không hoàn tiền
   - Nếu `cancellation_fee < paid_amount` → Hoàn lại `paid_amount - cancellation_fee`

4. **Từ chối Booking:**

   - Hoàn tiền 100% (nếu đã đặt cọc)
   - Trả lại quota cho schedule
   - Gửi thông báo đến khách hàng

5. **Deadline Đặt Booking:** ✅ **Phải đặt trước 1 ngày**

   - **Quy định:** Phải đặt booking trước 1 ngày so với ngày khởi hành
   - **Validation:** Khi tạo booking, kiểm tra `start_date >= ngày hiện tại + 1 ngày`
   - Nếu vi phạm → Hiển thị error: "Không thể đặt booking. Phải đặt trước 1 ngày so với ngày khởi hành."

6. **Điều kiện Đi Tour (Check-in):** ✅ **Phải thanh toán đủ mới cho đi**

   - **Bắt buộc:**
     - `approval_status` = 'approved' (phải được duyệt)
     - `start_date = ngày hiện tại` (đúng ngày khởi hành)
     - `payment_status` = 'paid' (phải thanh toán đủ) ✅
     - `remaining_amount` = 0 (không còn nợ) ✅
   - **Khi check-in:**
     - Kiểm tra đủ điều kiện → Cho phép check-in
     - Nếu chưa đủ điều kiện → ❌ Hiển thị error: "Booking này chưa thanh toán đủ. Vui lòng thanh toán trước khi check-in."

---

**Ngày tạo:** 2024-12-06
