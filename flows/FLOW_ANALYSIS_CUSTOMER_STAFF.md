# 📊 PHÂN TÍCH LUỒNG: CUSTOMER & STAFF MODULE

## 🎯 THÔNG TIN CHUNG

- **Module:** Customer (Khách hàng) & Staff/User (Nhân viên) 👥
- **Mục đích:** Quản lý thông tin khách hàng, nhân viên và các chức năng liên quan
- **Ngày tạo:** 2024-12-06
- **Status:** ⏳ Đang phân tích

---

## 📋 MÔ TẢ TỔNG QUAN

Module này bao gồm 2 phần chính:

### **1. MODULE CUSTOMER (Khách hàng)**

- Quản lý thông tin khách hàng
- Import khách hàng từ Excel/CSV
- Check-in khách hàng khi tour khởi hành
- Xem lịch sử booking của khách hàng

### **2. MODULE STAFF/USER (Nhân viên)**

- Quản lý thông tin người dùng (staff, admin, guide)
- Quản lý vai trò (roles) và phân quyền
- Quản lý tài khoản người dùng

**Lưu ý:** Các chức năng CRUD cơ bản (xem, thêm, sửa, xóa) sẽ được bỏ qua, tập trung vào **luồng thao tác phức tạp của người dùng**.

---

## 🔄 CÁC LUỒNG CHÍNH

### **LUỒNG 1: TẠO KHÁCH HÀNG MỚI (CUS-002)**

**Mô tả luồng:**

**Bước 1: Vào Form Tạo Khách Hàng**

- User (Staff/Admin) vào module "Quản lý Khách hàng"
- Click nút "Thêm khách hàng mới"
- URL: `?act=staff-customers&action=create` hoặc `?act=admin&module=customers&action=create`

**Bước 2: Điền Thông Tin**

- Form hiển thị các trường:
  - **Họ và tên** - REQUIRED
  - **Số điện thoại** - REQUIRED
  - **Email** - Optional
  - **Ngày sinh** - Optional (date picker)
  - **Giới tính** - Optional (dropdown: Nam/Nữ/Khác)
  - **Địa chỉ** - Optional
  - **CMND/CCCD** - Optional
  - **Hộ chiếu** - Optional
  - **Quốc tịch** - Optional (default: Vietnam)
  - **Loại khách hàng** - Optional (dropdown: Cá nhân/Nhóm/Doanh nghiệp)
  - **Nguồn** - Optional (dropdown: Phone/Email/Facebook/Zalo/Walk-in/Khác)
  - **Yêu cầu đặc biệt** - Optional
  - **Ghi chú** - Optional

**Bước 3: Validate & Submit**

- Click "Lưu":
  - Client-side validation (nếu có)
  - Server-side validation:
    - `full_name`: Required, min 2 ký tự, max 100 ký tự
    - `phone`: Required, format (0/+84) + 9-10 số, không trùng
    - `email`: Optional, format hợp lệ, không trùng
    - `id_card`: Optional, 9 hoặc 12 chữ số, không trùng
    - `passport`: Optional, format [A-Z][0-9]{7,8}
    - `date_of_birth`: Optional, phải là ngày trong quá khứ, không quá 120 tuổi
  - Normalize data:
    - Phone: Remove spaces, dashes, parentheses
    - ID Card: Remove spaces
    - Passport: Uppercase

**Bước 4: Tạo Customer**

- Nếu validation pass:
  - Auto-generate `customer_code` (CUS-YYYYMMDD-XXX) nếu chưa có
  - Set `created_by` = current user ID
  - Set `total_bookings` = 0, `total_spent` = 0.00
  - Set `status` = 'active'
  - Insert vào database
  - Hiển thị success message: "Thêm khách hàng thành công!"
  - Redirect về danh sách khách hàng
- Nếu validation fail:
  - Hiển thị error messages
  - Giữ lại dữ liệu đã nhập (old input)
  - Redirect về form với errors

---

### **LUỒNG 2: IMPORT KHÁCH HÀNG TỪ EXCEL (CUS-006)**

**Mô tả luồng:**

**Bước 1: Vào Trang Import**

- User (Staff/Admin) vào module "Quản lý Khách hàng"
- Click nút "Import từ Excel" hoặc "Nhập khẩu"
- URL: `?act=admin&module=customers&action=import`

**Bước 2: Download Template (Optional)**

- Click "Tải mẫu file Excel" để download template CSV/Excel
- Template có các cột: Họ tên, SĐT, Email, Ngày sinh, Giới tính, Loại, Địa chỉ, CMND, Hộ chiếu

**Bước 3: Upload File**

- Chọn file CSV/Excel từ máy tính
- File được upload lên server (temporary location)
- Validate file:
  - File type: .csv, .xlsx, .xls
  - File size: Max (tùy config)
  - File có dữ liệu

**Bước 4: Parse File**

- Đọc file:
  - Row 1: Header (tự động detect delimiter: comma hoặc semicolon)
  - Row 2+: Data rows
- Map columns tự động dựa trên header:
  - Tên cột có thể viết nhiều cách: "Họ tên", "Họ và tên", "Ten", "Full Name"
  - Normalize Vietnamese text để so sánh (loại bỏ dấu)
  - Map các field: full_name, phone, email, date_of_birth, gender, address, id_card, passport

**Bước 5: Validate & Import**

- Với mỗi row:
  - Validate từng field (giống validation khi tạo thủ công)
  - Kiểm tra duplicate phone:
    - Nếu phone đã tồn tại → Skip row (không tạo duplicate)
    - Ghi log: "Row X: Bỏ qua do trùng số điện thoại"
  - Nếu có lỗi validation:
    - Ghi log lỗi chi tiết: `{row_number: X, errors: ['Field Y không hợp lệ']}`
    - Không tạo customer
  - Nếu pass:
    - Normalize data (phone, id_card, passport)
    - Tạo customer với `created_by` = current user ID
    - Tăng `success_count`

**Bước 6: Tạo Import Log**

- Sau khi import xong:
  - Tạo record trong `customer_import_logs`:
    - `file_name` = tên file gốc
    - `file_path` = đường dẫn file đã upload
    - `imported_by` = current user ID
    - `total_rows` = tổng số dòng (trừ header)
    - `success_count` = số dòng import thành công
    - `error_count` = số dòng có lỗi
    - `error_details` = JSON array chứa chi tiết lỗi từng row
  - Commit transaction

**Bước 7: Hiển thị Kết quả**

- Redirect về trang kết quả hoặc danh sách
- Hiển thị summary:
  - "Đã import thành công: X khách hàng"
  - "Có lỗi: Y dòng"
  - Link "Xem chi tiết lỗi" → mở modal hoặc trang chi tiết log

---

### **LUỒNG 3: CHECK-IN KHÁCH HÀNG (CUS-008)**

**Mô tả luồng:**

**Bước 1: Vào Trang Check-in**

- User (Guide/Staff) vào module "Check-in" hoặc "Quản lý Tour"
- Chọn booking cần check-in (từ tour schedule)
- URL: `?act=guide-checkin&booking_id=X` hoặc tương tự

**Bước 2: Xem Danh Sách Khách Hàng trong Booking**

- Hiển thị danh sách customers của booking đó (từ `booking_customers`)
- Mỗi customer hiển thị:
  - Tên, SĐT
  - Loại: Adult/Child/Infant
  - Trạng thái check-in: Chưa check-in / Đã check-in / Vắng mặt / Đến muộn

**Bước 3: Thực hiện Check-in**

- Click "Check-in" hoặc checkbox cho từng customer:
  - Nếu chưa check-in:
    - Mở form/modal:
      - **Trạng thái**: Dropdown (Present/Vắng mặt/Đến muộn) - Default: Present
      - **Thời gian check-in**: Auto-fill = NOW (có thể sửa)
      - **Ghi chú**: Optional
    - Click "Xác nhận":
      - Validate: `booking_id` và `customer_id` phải tồn tại
      - Kiểm tra: Customer này chưa check-in cho booking này (tránh duplicate)
      - Tạo record trong `customer_checkins`:
        - `booking_id` = booking đã chọn
        - `customer_id` = customer đã chọn
        - `checkin_time` = thời gian check-in (default: NOW())
        - `status` = 'present' / 'absent' / 'late'
        - `notes` = ghi chú (nếu có)
        - `checked_by` = current user ID
      - Hiển thị success message
  - Nếu đã check-in:
    - Hiển thị thông tin check-in (thời gian, trạng thái)
    - Có thể cho phép "Sửa" hoặc "Hủy check-in"

**Bước 4: Cập nhật Trạng thái**

- Sau khi check-in:
  - Có thể cập nhật status (Present → Absent hoặc Late)
  - Có thể thêm/sửa ghi chú
  - Có thể xóa check-in (nếu chưa tour khởi hành)

---

### **LUỒNG 4: XEM LỊCH SỬ BOOKING CỦA KHÁCH (CUS-004)**

**Mô tả luồng:**

**Bước 1: Vào Chi tiết Khách hàng**

- User (Staff/Admin) vào module "Quản lý Khách hàng"
- Click vào customer trong danh sách hoặc tìm kiếm
- URL: `?act=staff-customers&action=show&id=X`

**Bước 2: Xem Thông tin Khách hàng**

- Hiển thị tab/section "Thông tin cơ bản":
  - Tất cả thông tin customer (từ bảng `customers`)
  - Tổng số booking (`total_bookings`)
  - Tổng số tiền đã chi (`total_spent`)
  - Trạng thái: Active/Inactive/Blacklist

**Bước 3: Xem Lịch sử Booking**

- Hiển thị tab/section "Lịch sử Booking":
  - Query từ `bookings` table WHERE `customer_id` = customer.id
  - Hiển thị danh sách bookings:
    - Mã booking (`booking_code`)
    - Tour name (join với `tours`)
    - Ngày khởi hành/kết thúc (`start_date`, `end_date`)
    - Số lượng khách (adult/child/infant)
    - Tổng tiền (`total_amount`, `final_amount`)
    - Trạng thái thanh toán (`payment_status`)
    - Trạng thái booking (`approval_status`)
    - Ngày tạo booking
  - Sắp xếp: Mới nhất trước (ORDER BY created_at DESC)
  - Pagination nếu có nhiều bookings

**Bước 4: Xem Chi tiết Booking (Optional)**

- Click vào một booking → chuyển sang trang chi tiết booking
- Hoặc mở modal để xem nhanh:
  - Thông tin booking đầy đủ
  - Danh sách payments
  - Services đã đặt

---

### **LUỒNG 5: TẠO NGƯỜI DÙNG MỚI (SYS-002)**

**Mô tả luồng:**

**Bước 1: Vào Form Tạo Người dùng**

- User (Admin) vào module "Quản lý Người dùng"
- Click nút "Thêm người dùng mới"
- URL: `?act=admin&module=users&action=create`

**Bước 2: Điền Thông Tin**

- Form hiển thị các trường:
  - **Email** - REQUIRED (dùng để đăng nhập)
  - **Mật khẩu** - REQUIRED (min 8 ký tự)
  - **Xác nhận mật khẩu** - REQUIRED (phải khớp)
  - **Họ và tên** - REQUIRED
  - **Vai trò (Role)** - REQUIRED (dropdown: Admin/Staff/Guide)
  - **Số điện thoại** - Optional
  - **Ngày sinh** - Optional
  - **Giới tính** - Optional
  - **Địa chỉ** - Optional
  - **Ảnh đại diện** - Optional (upload file)

**Bước 3: Validate & Submit**

- Click "Lưu":
  - Client-side validation:
    - Email format
    - Password strength (min 8 ký tự, có số và chữ)
    - Password confirmation match
  - Server-side validation:
    - `email`: Required, format hợp lệ, UNIQUE
    - `password`: Required, min 8 ký tự
    - `full_name`: Required, min 2 ký tự
    - `role_id`: Required, phải tồn tại trong bảng `roles`
    - Upload avatar: Validate file type, size

**Bước 4: Tạo User**

- Nếu validation pass:
  - Hash password (bcrypt/argon2)
  - Upload avatar (nếu có) → lưu vào `public/uploads/avatars/`
  - Set `created_by` = current user ID
  - Set `status` = 'active'
  - Set `last_login` = NULL
  - Insert vào database
  - Hiển thị success message: "Tạo người dùng thành công!"
  - Redirect về danh sách users
- Nếu validation fail:
  - Hiển thị error messages
  - Giữ lại dữ liệu đã nhập
  - Redirect về form với errors

**Lưu ý:** Sau khi tạo, user có thể đăng nhập ngay bằng email và password đã set.

---

### **LUỒNG 6: PHÂN QUYỀN NGƯỜI DÙNG (SYS-004)**

**Mô tả luồng:**

**Lưu ý:** Hệ thống hiện tại sử dụng role-based access control đơn giản (RBAC). Mỗi user có 1 role (admin, staff, guide) và quyền được xác định bởi role đó. Nếu cần phân quyền chi tiết hơn (permission-based), sẽ cần thêm bảng `permissions` và `role_permissions`.

**Bước 1: Vào Trang Quản lý Người dùng**

- User (Admin) vào module "Quản lý Người dùng"
- Click vào user cần phân quyền hoặc vào trang "Phân quyền"

**Bước 2: Chọn Vai trò**

- Hiển thị form:
  - **User hiện tại**: [Tên user] ([Email])
  - **Vai trò hiện tại**: [Role name]
  - **Chọn vai trò mới**: Dropdown các roles có sẵn
    - Admin (Quản trị viên - Full access)
    - Staff (Nhân viên - Quản lý bookings, customers, tours)
    - Guide (Hướng dẫn viên - Quản lý journals, check-in)

**Bước 3: Cập nhật Role**

- Click "Lưu":
  - Validate: `role_id` phải tồn tại
  - Update `users.role_id` = role mới
  - Có thể log thay đổi (audit log) nếu cần
  - Hiển thị success message: "Cập nhật vai trò thành công!"

**Business Logic:**

- Admin có thể thay đổi role của bất kỳ user nào
- Không được thay đổi role của chính mình (hoặc cần confirm)
- Khi thay đổi role, user phải đăng nhập lại để áp dụng quyền mới

---

**Note:** Nếu hệ thống cần phân quyền chi tiết hơn (permission-based), sẽ cần:

- Bảng `permissions`: Danh sách các quyền (ví dụ: 'customers.create', 'bookings.approve')
- Bảng `role_permissions`: Mapping giữa roles và permissions
- Bảng `user_permissions`: Mapping giữa users và permissions (nếu cần override)

---

## 📊 CÁC TRƯỜNG DỮ LIỆU LIÊN QUAN

### **1. Bảng `customers`**

| Trường               | Loại          | Bắt buộc | Mô tả                                   | Validation           |
| -------------------- | ------------- | -------- | --------------------------------------- | -------------------- |
| id                   | INT           | ✅       | Primary key                             | AUTO_INCREMENT       |
| customer_code        | VARCHAR(50)   | ❌       | Mã khách hàng                           | UNIQUE               |
| full_name            | VARCHAR(100)  | ✅       | Họ và tên                               | NOT NULL             |
| email                | VARCHAR(100)  | ❌       | Email                                   |                      |
| phone                | VARCHAR(20)   | ✅       | Số điện thoại                           | NOT NULL             |
| date_of_birth        | DATE          | ❌       | Ngày sinh                               |                      |
| gender               | ENUM          | ❌       | male/female/other                       |                      |
| id_card              | VARCHAR(50)   | ❌       | CMND/CCCD                               |                      |
| passport             | VARCHAR(50)   | ❌       | Hộ chiếu                                |                      |
| nationality          | VARCHAR(50)   | ❌       | Quốc tịch                               | DEFAULT 'Vietnam'    |
| address              | TEXT          | ❌       | Địa chỉ                                 |                      |
| customer_type        | ENUM          | ✅       | individual/group/corporate              | DEFAULT 'individual' |
| source               | ENUM          | ❌       | phone/email/facebook/zalo/walk_in/other |                      |
| special_requirements | TEXT          | ❌       | Yêu cầu đặc biệt                        |                      |
| notes                | TEXT          | ❌       | Ghi chú                                 |                      |
| total_bookings       | INT           | ✅       | Tổng số booking                         | DEFAULT 0            |
| total_spent          | DECIMAL(15,2) | ✅       | Tổng số tiền đã chi                     | DEFAULT '0.00'       |
| status               | ENUM          | ✅       | active/inactive/blacklist               | DEFAULT 'active'     |
| created_by           | INT           | ❌       | Foreign key → users                     | FK, NULL             |
| created_at           | TIMESTAMP     | ❌       | Ngày tạo                                |                      |
| updated_at           | TIMESTAMP     | ❌       | Ngày cập nhật                           |                      |

**Mối quan hệ:**

- N Customers → 1 User (`created_by`) - Optional
- 1 Customer → N Bookings (`bookings.customer_id`)
- 1 Customer → N Customer Checkins (`customer_checkins.customer_id`)

**Business Logic:**

- `customer_code` = auto-generate (CUS-YYYYMMDD-XXX)
- Tối thiểu cần `full_name` HOẶC `phone` (một trong hai)
- `total_bookings` và `total_spent` được cập nhật tự động khi có booking mới/thanh toán
- Không được xóa customer có booking đã thanh toán, chỉ có thể set status = 'inactive' hoặc 'blacklist'

---

### **2. Bảng `customer_checkins`**

| Trường       | Loại      | Bắt buộc | Mô tả                   | Validation        |
| ------------ | --------- | -------- | ----------------------- | ----------------- |
| id           | INT       | ✅       | Primary key             | AUTO_INCREMENT    |
| booking_id   | INT       | ✅       | Foreign key → bookings  | NOT NULL, FK      |
| customer_id  | INT       | ✅       | Foreign key → customers | NOT NULL, FK      |
| checkin_time | TIMESTAMP | ✅       | Thời gian check-in      | NOT NULL          |
| status       | ENUM      | ✅       | present/absent/late     | DEFAULT 'present' |
| notes        | TEXT      | ❌       | Ghi chú                 |                   |
| checked_by   | INT       | ❌       | Foreign key → users     | FK, NULL          |

**Mối quan hệ:**

- N Customer Checkins → 1 Booking (`booking_id`) - REQUIRED
- N Customer Checkins → 1 Customer (`customer_id`) - REQUIRED
- N Customer Checkins → 1 User (`checked_by`) - Optional (người thực hiện check-in)

**Business Logic:**

- Check-in được thực hiện khi tour khởi hành
- Một customer có thể check-in nhiều lần cho các booking khác nhau
- `checkin_time` = thời gian thực tế check-in (có thể khác với `booking.start_date`)

---

### **3. Bảng `customer_import_logs`**

| Trường        | Loại         | Bắt buộc | Mô tả                     | Validation     |
| ------------- | ------------ | -------- | ------------------------- | -------------- |
| id            | INT          | ✅       | Primary key               | AUTO_INCREMENT |
| file_name     | VARCHAR(255) | ✅       | Tên file                  | NOT NULL       |
| file_path     | VARCHAR(255) | ❌       | Đường dẫn file            |                |
| imported_by   | INT          | ❌       | Foreign key → users       | FK, NULL       |
| total_rows    | INT          | ✅       | Tổng số dòng              | DEFAULT 0      |
| success_count | INT          | ✅       | Số dòng import thành công | DEFAULT 0      |
| error_count   | INT          | ✅       | Số dòng lỗi               | DEFAULT 0      |
| error_details | JSON         | ❌       | Chi tiết lỗi (array)      |                |
| created_at    | TIMESTAMP    | ❌       | Ngày tạo                  |                |

**Mối quan hệ:**

- N Import Logs → 1 User (`imported_by`) - Optional

**Business Logic:**

- `error_details` lưu dưới dạng JSON, chứa thông tin: `{row_number, errors: []}`
- Mỗi lần import tạo 1 log record

---

### **4. Bảng `users`**

| Trường        | Loại         | Bắt buộc | Mô tả                     | Validation       |
| ------------- | ------------ | -------- | ------------------------- | ---------------- |
| id            | INT          | ✅       | Primary key               | AUTO_INCREMENT   |
| role_id       | INT          | ✅       | Foreign key → roles       | NOT NULL, FK     |
| email         | VARCHAR(100) | ✅       | Email (dùng để đăng nhập) | UNIQUE, NOT NULL |
| password      | VARCHAR(255) | ✅       | Mật khẩu (hashed)         | NOT NULL         |
| full_name     | VARCHAR(100) | ✅       | Họ và tên                 | NOT NULL         |
| phone         | VARCHAR(20)  | ❌       | Số điện thoại             |                  |
| date_of_birth | DATE         | ❌       | Ngày sinh                 |                  |
| gender        | ENUM         | ❌       | male/female/other         |                  |
| address       | TEXT         | ❌       | Địa chỉ                   |                  |
| avatar        | VARCHAR(255) | ❌       | Đường dẫn ảnh đại diện    |                  |
| status        | ENUM         | ✅       | active/inactive/suspended | DEFAULT 'active' |
| last_login    | TIMESTAMP    | ❌       | Lần đăng nhập cuối        |                  |
| created_by    | INT          | ❌       | Foreign key → users       | FK, NULL         |
| created_at    | TIMESTAMP    | ❌       | Ngày tạo                  |                  |
| updated_at    | TIMESTAMP    | ❌       | Ngày cập nhật             |                  |

**Mối quan hệ:**

- N Users → 1 Role (`role_id`) - REQUIRED
- N Users → 1 User (`created_by`) - Optional (người tạo user)
- 1 User → N Tours (`tours.created_by`)
- 1 User → N Bookings (`bookings.created_by`)
- 1 User → N Customer Checkins (`customer_checkins.checked_by`)

**Business Logic:**

- `email` phải unique trong hệ thống
- `password` phải được hash (bcrypt/argon2) trước khi lưu
- `last_login` được cập nhật mỗi lần user đăng nhập thành công
- Không được xóa user có dữ liệu liên quan, chỉ có thể set `status = 'inactive'` hoặc `'suspended'`

---

### **5. Bảng `roles`**

| Trường       | Loại         | Bắt buộc | Mô tả              | Validation     |
| ------------ | ------------ | -------- | ------------------ | -------------- |
| id           | INT          | ✅       | Primary key        | AUTO_INCREMENT |
| name         | VARCHAR(50)  | ✅       | Tên vai trò (code) | UNIQUE         |
| display_name | VARCHAR(100) | ✅       | Tên hiển thị       | NOT NULL       |
| description  | TEXT         | ❌       | Mô tả vai trò      |                |
| created_at   | TIMESTAMP    | ❌       | Ngày tạo           |                |
| updated_at   | TIMESTAMP    | ❌       | Ngày cập nhật      |                |

**Mối quan hệ:**

- 1 Role → N Users (`users.role_id`)

**Business Logic:**

- `name` phải unique (ví dụ: 'admin', 'staff', 'guide')
- Các role mặc định: 'admin', 'staff', 'guide'

---

### **6. Bảng `password_resets`**

| Trường     | Loại         | Bắt buộc | Mô tả                | Validation     |
| ---------- | ------------ | -------- | -------------------- | -------------- |
| id         | INT          | ✅       | Primary key          | AUTO_INCREMENT |
| email      | VARCHAR(100) | ✅       | Email người dùng     | NOT NULL       |
| token      | VARCHAR(255) | ✅       | Token reset (hashed) | NOT NULL       |
| created_at | TIMESTAMP    | ❌       | Ngày tạo token       |                |
| expires_at | TIMESTAMP    | ✅       | Ngày hết hạn         | NOT NULL       |
| used_at    | TIMESTAMP    | ❌       | Ngày sử dụng token   |                |

**Mối quan hệ:**

- Không có foreign key (email là string, không phải FK)

**Business Logic:**

- Token chỉ có hiệu lực trong khoảng thời gian nhất định (thường 24 giờ)
- Sau khi reset mật khẩu, `used_at` được set và token không còn hiệu lực

---

## ✅ VALIDATION RULES

### **Luồng 1: Tạo Khách hàng mới**

1. **Họ và tên (`full_name`):**

   - Required
   - Min length: 2 ký tự
   - Max length: 100 ký tự
   - Không được để trống

2. **Số điện thoại (`phone`):**

   - Required
   - Format: `(0|+84) + 9-10 chữ số`
   - Sau khi normalize (remove spaces, dashes): Phải match pattern `/^(0|\+84)[0-9]{9,10}$/`
   - Ví dụ hợp lệ: `0901234567`, `+84901234567`, `0987654321`
   - Không được trùng với customer khác

3. **Email (`email`):**

   - Optional
   - Format: Phải là email hợp lệ (dùng `filter_var($email, FILTER_VALIDATE_EMAIL)`)
   - Nếu có: Không được trùng với customer khác

4. **CMND/CCCD (`id_card`):**

   - Optional
   - Format: 9 hoặc 12 chữ số (sau khi remove spaces)
   - Pattern: `/^[0-9]{9}$|^[0-9]{12}$/`
   - Nếu có: Không được trùng với customer khác

5. **Hộ chiếu (`passport`):**

   - Optional
   - Format: Chữ cái in hoa + 7-8 chữ số
   - Pattern: `/^[A-Z][0-9]{7,8}$/`
   - Ví dụ: `B12345678`, `A9876543`

6. **Ngày sinh (`date_of_birth`):**

   - Optional
   - Phải là ngày trong quá khứ (không được là hôm nay hoặc tương lai)
   - Phải hợp lý (không quá 120 tuổi)

7. **Giới tính (`gender`):**

   - Optional
   - Enum: `male`, `female`, `other`
   - Nếu không chọn: Default `other` (tùy config)

8. **Loại khách hàng (`customer_type`):**

   - Optional
   - Enum: `individual`, `group`, `corporate`
   - Default: `individual`

9. **Nguồn (`source`):**
   - Optional
   - Enum: `phone`, `email`, `facebook`, `zalo`, `walk_in`, `other`
   - Default: `other`

---

### **Luồng 2: Import Khách hàng từ Excel**

1. **File Upload:**

   - File type: `.csv`, `.xlsx`, `.xls`
   - File size: Max (tùy config, thường 5-10MB)
   - File phải có dữ liệu (ít nhất header row)

2. **Header Row:**

   - Phải có header row (row đầu tiên)
   - Tự động detect delimiter: comma (`,`) hoặc semicolon (`;`)
   - Tự động map columns dựa trên tên cột (không phân biệt hoa/thường, có dấu/không dấu)

3. **Data Rows:**

   - Mỗi row phải có ít nhất `full_name` HOẶC `phone`
   - Validation giống như "Tạo khách hàng mới" cho từng row
   - Nếu row có lỗi: Ghi log, không tạo customer, tăng `error_count`

4. **Duplicate Handling:**

   - Nếu `phone` đã tồn tại: Skip row (không tạo duplicate, không tính là error)
   - Log: "Row X: Bỏ qua do trùng số điện thoại"

5. **Error Logging:**
   - Mỗi lỗi được ghi vào `error_details` JSON array:
     ```json
     [
       {
         "row_number": 2,
         "errors": ["Số điện thoại không hợp lệ", "Email không đúng format"]
       },
       {
         "row_number": 5,
         "errors": ["Thiếu tên hoặc SĐT"]
       }
     ]
     ```

---

### **Luồng 3: Check-in Khách hàng**

1. **Booking & Customer:**

   - `booking_id` phải tồn tại và valid
   - `customer_id` phải tồn tại và thuộc booking đó (từ `booking_customers`)
   - Guide phải được phân công cho tour schedule của booking này

2. **Trạng thái Check-in (`status`):**

   - Required
   - Enum: `present`, `absent`, `late`
   - Default: `present`

3. **Thời gian Check-in (`checkin_time`):**

   - Default: `NOW()` (thời gian thực tế check-in)
   - Có thể sửa thủ công (nếu cần backdate)

4. **Duplicate Prevention:**
   - Một customer chỉ có 1 check-in record cho 1 booking
   - Nếu đã check-in: Update record cũ (không tạo mới)

---

### **Luồng 5: Tạo Người dùng mới**

1. **Email (`email`):**

   - Required
   - Format: Phải là email hợp lệ
   - UNIQUE trong hệ thống (không trùng với user khác)

2. **Mật khẩu (`password`):**

   - Required
   - Min length: 8 ký tự
   - Recommended: Có chữ hoa, chữ thường, số, ký tự đặc biệt
   - Phải hash (bcrypt/argon2) trước khi lưu vào database

3. **Xác nhận mật khẩu:**

   - Required
   - Phải khớp với `password`

4. **Họ và tên (`full_name`):**

   - Required
   - Min length: 2 ký tự
   - Max length: 100 ký tự

5. **Vai trò (`role_id`):**

   - Required
   - Phải tồn tại trong bảng `roles`
   - Enum: admin, staff, guide

6. **Ảnh đại diện (`avatar`):**
   - Optional
   - File type: .jpg, .jpeg, .png, .gif
   - File size: Max (tùy config, thường 2-5MB)
   - Upload vào `public/uploads/avatars/`

---

## 🔒 BUSINESS RULES

1. **Customer Creation:**

   - Tối thiểu cần `full_name` HOẶC `phone` (nhưng code hiện tại yêu cầu cả hai)
   - `phone` KHÔNG được trùng (unique check)
   - `customer_code` tự động generate (CUS-YYYYMMDD-XXX) nếu không cung cấp
   - `total_bookings` và `total_spent` được cập nhật tự động khi có booking/thanh toán mới
   - Không được xóa customer có booking (soft delete: set `status = 'inactive'` hoặc `'blacklist'`)

2. **Customer Import:**

   - File Excel/CSV phải có header row
   - Các field bắt buộc: `full_name` hoặc `phone` (tối thiểu một trong hai)
   - Nếu trùng `phone`: Skip row, không tạo duplicate (không tính là error)
   - Transaction: Nếu có lỗi nghiêm trọng, rollback tất cả; Nếu chỉ một số row lỗi, commit các row thành công và log errors
   - Log tất cả errors vào `customer_import_logs.error_details` (JSON format)

3. **Customer Check-in:**

   - Check-in chỉ được thực hiện bởi Guide được phân công cho tour schedule
   - Một customer chỉ có 1 check-in record cho 1 booking (nếu check-in lại, update record cũ)
   - Status mặc định: `'present'`
   - `checkin_time` = thời gian thực tế check-in (có thể khác với `booking.start_date`)
   - Có thể check-in batch (nhiều khách cùng lúc)

4. **Customer History:**

   - Lịch sử booking được query từ `bookings` WHERE `customer_id` = customer.id
   - `total_bookings` = COUNT(\*) từ `bookings` WHERE `customer_id` AND `approval_status` = 'approved'
   - `total_spent` = SUM(`final_amount`) từ `bookings` WHERE `customer_id` AND `payment_status` != 'unpaid'

5. **User Management:**

   - Email phải unique trong hệ thống
   - Password phải đủ mạnh (min 8 ký tự) và được hash trước khi lưu
   - Mỗi user phải có 1 role (admin, staff, guide)
   - `last_login` được cập nhật mỗi lần đăng nhập thành công
   - Không được xóa user có dữ liệu liên quan (tours, bookings, checkins), chỉ có thể set `status = 'inactive'` hoặc `'suspended'`
   - Admin không thể thay đổi role của chính mình (hoặc cần confirm đặc biệt)

6. **Role Management:**
   - Role mặc định: `admin`, `staff`, `guide`
   - Mỗi role có quyền truy cập khác nhau:
     - **admin**: Full access (tất cả modules)
     - **staff**: Quản lý bookings, customers, tours (create/edit, không approve)
     - **guide**: Quản lý journals, check-in (chỉ tour được phân công)
   - Khi thay đổi role, user phải đăng nhập lại để áp dụng quyền mới

---

## ⚠️ TRƯỜNG HỢP ĐẶC BIỆT

1. **Customer Phone Duplicate trong Import:**

   - Nếu file import có nhiều row cùng phone: Chỉ import row đầu tiên, các row sau skip
   - Nếu phone đã tồn tại trong database: Skip tất cả row có phone đó
   - Log: "Row X: Bỏ qua do trùng số điện thoại" (không tính là error)

2. **Customer không có Booking nhưng có Check-in:**

   - Không thể xảy ra (do foreign key constraint `customer_checkins.booking_id` → `bookings.id`)
   - Check-in chỉ được tạo từ booking đã tồn tại

3. **Check-in nhiều lần cho cùng Booking:**

   - Nếu check-in lại: Update record cũ (không tạo mới)
   - `checkin_time` được update = thời gian check-in mới
   - `status` và `notes` được update
   - `checked_by` được update = user hiện tại

4. **Import File với Encoding khác nhau:**

   - Tự động detect encoding (UTF-8, Windows-1258, etc.)
   - Convert về UTF-8 trước khi parse
   - Xử lý BOM (Byte Order Mark) nếu có

5. **Customer với Special Requirements:**

   - Lưu trong `special_requirements` (text field)
   - Hiển thị khi tạo booking cho customer này
   - Có thể dùng để filter/search customers có yêu cầu đặc biệt

6. **User bị Suspended/Inactive:**

   - Không thể đăng nhập
   - Vẫn giữ nguyên dữ liệu (tours, bookings, checkins) để audit
   - Có thể reactivate bằng cách set `status = 'active'`

7. **Batch Check-in:**

   - Check-in nhiều khách cùng lúc (từ form checkbox)
   - Validate tất cả trước khi insert/update
   - Transaction: Nếu 1 check-in fail, rollback tất cả (hoặc continue với các check-in khác, tùy config)

8. **Customer Code Conflict:**
   - Nếu user tự nhập `customer_code` và trùng: Validate và reject
   - Nếu auto-generate bị trùng (rất hiếm): Retry với số sequence cao hơn

---

## 🔗 DEPENDENCIES

### **Phụ thuộc vào:**

- Module System: Roles (users.role_id)
- Module Booking: Bookings (customer_checkins.booking_id)

### **Ảnh hưởng đến:**

- Module Booking: Customers được dùng trong bookings
- Module Payment: Customers liên quan đến payments
- Module Tour: Users (guides) được phân công cho tours

---

## 📝 GHI CHÚ

1. **Customer Code Format:**

   - `customer_code`: CUS-YYYYMMDD-XXX (ví dụ: CUS-20241206-001)

2. **User Roles:**

   - **admin**: Quản trị viên (full access)
   - **staff**: Nhân viên (quản lý bookings, customers, tours)
   - **guide**: Hướng dẫn viên (quản lý journals, check-in)

3. **Import File Format:**
   - Hỗ trợ CSV (comma hoặc semicolon separated)
   - Excel files cần export sang CSV hoặc dùng thư viện PhpSpreadsheet

---

**Status:** ✅ Đã hoàn thành - Chờ user review và bổ sung
