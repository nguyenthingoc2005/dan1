# 📊 PHÂN TÍCH LUỒNG: TOUR MODULE

## 🎯 THÔNG TIN CHUNG

- **Module:** Tour Management 🎫
- **Mục đích:** Quản lý tour, tạo tour mẫu (public) và tour custom, quản lý lịch trình, dịch vụ tour
- **Ngày tạo:** 2024-12-06
- **Status:** ⏳ Đang phân tích

---

## 📋 MÔ TẢ TỔNG QUAN

Module Tour bao gồm:

### **1. TOUR TYPES**

- **Tour Public (Tour Mẫu):**

  - `tour_type = 'public'`
  - Tour mẫu đã được duyệt (approved)
  - Dùng làm template để staff clone và tạo tour custom
  - Có thể tạo lịch trình (tour_schedules) để khách đặt

- **Tour Custom:**
  - `tour_type = 'custom'`
  - Tour được tạo từ tour mẫu (clone)
  - `parent_tour_id` link với tour mẫu gốc
  - Thường dành cho khách đặt tour riêng

### **2. TOUR STATUS & APPROVAL**

- **Status:** `draft`, `active`, `inactive`
- **Approval Status:** `pending`, `approved`, `rejected`
- Staff chỉ có thể tạo tour ở status `draft` hoặc `pending`
- Admin duyệt tour → set `approval_status = 'approved'` → tour có thể active

**Lưu ý:** Các chức năng CRUD cơ bản (xem, thêm, sửa, xóa) sẽ được bỏ qua, tập trung vào **luồng thao tác phức tạp của người dùng**.

---

## 🔄 CÁC LUỒNG CHÍNH

### **LUỒNG 1: TẠO TOUR MẪU MỚI (TOUR-002) - Public Tour**

**Mô tả luồng:**

**Bước 1: Chọn Phương thức Tạo Tour**

- User (Staff/Admin) vào module "Quản lý Tour"
- Click nút "Tạo tour mới"
- URL: `?act=staff-tours&action=selectTemplate` hoặc `?act=admin&module=tours&action=selectTemplate`
- Hiển thị 2 lựa chọn:
  - **Option 1:** "Tạo tour mới từ đầu" (Public Tour)
  - **Option 2:** "Tạo tour từ template" (Clone từ tour mẫu có sẵn)

**Bước 2: Vào Form Tạo Tour Mẫu**

- Chọn "Tạo tour mới từ đầu"
- URL: `?act=staff-tours&action=create` hoặc `?act=admin&module=tours&action=create`
- Form Wizard hiển thị (6 steps):
  - **Step 1:** Thông tin chung
  - **Step 2:** Lịch trình (Itinerary) - Bao gồm Timeline chi tiết và Dịch vụ theo ngày
  - **Step 3:** Bao gồm/Không bao gồm
  - **Step 4:** Chính sách (Policies)
  - **Step 5:** Hình ảnh
  - **Step 6:** Giá & Lưu

**Bước 3: Step 1 - Thông tin chung**

- Form fields:
  - **Tên Tour** - REQUIRED
  - **Điểm khởi hành** - Optional (text)
  - **Số ngày** - REQUIRED (number, min 1)
  - **Số đêm** - Optional (number, <= số ngày)
  - **Giới thiệu ngắn** - Optional (textarea)
  - **Mô tả chi tiết** - Optional (rich text editor)
  - Hidden: `tour_type = 'public'` (auto-set)

**Bước 4: Step 2 - Lịch trình (Itinerary)**

- Dựa trên **Số ngày** đã nhập ở Step 1, tự động tạo form cho N ngày
- **Giao diện Tabbed:**
  - **Tab 1: Tổng quan từng ngày**
  - **Tab 2: Timeline chi tiết** (MỚI)
  - **Tab 3: Dịch vụ theo ngày** (MỚI)

**Tab 1: Tổng quan từng ngày**

- Danh sách tất cả các ngày (Day 1, Day 2, Day 3...)
- Mỗi ngày có form:
  - **Ngày số:** Auto (Day 1, Day 2, ...)
  - **Tiêu đề ngày** - Optional (VD: "Ngày 1: Khởi hành Đà Lạt")
  - **Điểm đến (Destination)** - Optional (dropdown destinations)
  - **Mô tả lịch trình** - Optional (rich text editor)
  - **Giờ đến/Giờ khởi hành** - Optional (time picker) - DEPRECATED (dùng timeline chi tiết)
  - **Bữa ăn** - Optional (checkbox: Sáng/Trưa/Chiều/Tối) - DEPRECATED (dùng timeline chi tiết)
  - **Nơi nghỉ** - Optional (text) - DEPRECATED (dùng timeline chi tiết)

**Tab 2: Timeline chi tiết từng ngày** (MỚI)

- **Chọn ngày:** Dropdown hoặc tabs (Day 1, Day 2, Day 3...)
- **Timeline Editor:**
  - Hiển thị timeline của ngày đã chọn (sắp xếp theo giờ)
  - Mỗi timeline item có:
    - **Giờ:** Time picker (VD: 07:00, 08:30) - REQUIRED
    - **Hoạt động:** Text (VD: "Ăn sáng", "Check-in", "Tham quan") - REQUIRED
    - **Mô tả:** Textarea - Optional
    - **Địa điểm:** Text (VD: "Nhà hàng ABC") - Optional
    - **Loại timeline:** Dropdown:
      - `meal` (Bữa ăn)
      - `accommodation` (Nơi nghỉ)
      - `activity` (Hoạt động)
      - `transport` (Di chuyển)
    - **Nhà dịch vụ:** Dropdown service_providers (optional) - Nếu chọn → auto-fill địa điểm
    - **Địa điểm du lịch:** Dropdown destinations (optional)
    - **Dịch vụ:** Dropdown services (optional) - Link đến service để tính giá
  - **Actions:**
    - [+ Thêm timeline item] - Thêm mới
    - [↑] [↓] - Sắp xếp thứ tự (hoặc drag & drop)
    - [✏️] - Sửa
    - [🗑️] - Xóa
- **Lưu ý:**
  - Nếu `timeline_type = 'meal'` → Khuyến khích chọn `service_provider_id` (nhà hàng)
  - Nếu `timeline_type = 'accommodation'` → Khuyến khích chọn `service_provider_id` (khách sạn)
  - Nếu chọn `service_provider_id` + `service_id` → Tự động hỏi có muốn thêm vào "Dịch vụ theo ngày" không?

**Tab 3: Dịch vụ theo ngày** (MỚI)

- **Chọn ngày:** Dropdown hoặc tabs (Day 1, Day 2, Day 3...)
- **Danh sách dịch vụ cho ngày đó:**
  - Hiển thị tất cả dịch vụ đã gán cho ngày đã chọn
  - Mỗi dịch vụ hiển thị:
    - **Checkbox:** `is_included_in_price` (Bao gồm trong giá tour)
    - **Tên dịch vụ:** Text (snapshot)
    - **Nhà dịch vụ:** Text (từ service_provider) - Optional
    - **Đơn giá/người:** Number - REQUIRED
    - **Số lượng:** Number (VD: 1 bữa, 1 đêm, 2 vé) - DEFAULT 1
    - **Đơn vị:** Text (VD: "bữa", "đêm", "vé") - Optional
    - **Ghi chú:** Textarea - Optional
    - **Tổng:** Auto = unit_price × quantity
  - **Tổng ngày:** Hiển thị tổng chi phí dịch vụ/người của ngày đó (chỉ tính các dịch vụ `is_included_in_price = 1`)
- **Actions:**
  - [+ Thêm dịch vụ] - Modal form:
    - **Chọn dịch vụ:** Dropdown services (REQUIRED)
    - **Chọn nhà dịch vụ:** Dropdown service_providers (optional)
    - **Đơn giá/người:** Number (REQUIRED) - Có thể auto-fill từ service
    - **Số lượng:** Number (DEFAULT 1)
    - **Đơn vị:** Text (optional)
    - **Bao gồm trong giá:** Checkbox (DEFAULT checked)
    - **Ghi chú:** Textarea (optional)
  - [✏️] - Sửa dịch vụ
  - [🗑️] - Xóa dịch vụ
- **Hiển thị breakdown:**
  ```
  ┌─────────────────────────────────────────┐
  │ Dịch vụ - Day 1                         │
  │ ─────────────────────────────────────── │
  │ [✓] Ăn sáng - Nhà hàng ABC              │
  │     100,000đ/người × 1 = 100,000đ       │
  │                                         │
  │ [✓] Khách sạn 3 sao - KS XYZ            │
  │     200,000đ/người × 1 = 200,000đ       │
  │                                         │
  │ [✓] Ăn trưa - Nhà hàng DEF              │
  │     150,000đ/người × 1 = 150,000đ       │
  │                                         │
  │ [✓] Vé tham quan Vườn Hoa               │
  │     50,000đ/người × 1 = 50,000đ         │
  │ ─────────────────────────────────────── │
  │ Tổng Day 1: 500,000đ/người             │
  │                                         │
  │ [+ Thêm dịch vụ]                        │
  └─────────────────────────────────────────┘
  ```
- **Auto-sync với Timeline:**
  - Nếu trong Tab 2 (Timeline) chọn `service_provider_id` + `service_id` → Tự động hỏi: "Có muốn thêm dịch vụ này vào danh sách dịch vụ của ngày không?"
  - Nếu chọn "Có" → Tự động tạo `itinerary_day_services` với giá từ service

**Bước 5: Step 3 - Bao gồm/Không bao gồm**

- **Điểm nổi bật (Highlights):**
  - Textarea (mỗi dòng là 1 highlight)
  - VD: "Tham quan Hồ Xuân Hương\nKhám phá Chợ Đà Lạt\n..."
- **Bao gồm (Included):**
  - Dynamic list (có thể thêm/xóa items)
  - Mỗi item là text
- **Không bao gồm (Excluded):**
  - Dynamic list (có thể thêm/xóa items)
  - Mỗi item là text

**Bước 6: Step 4 - Chính sách (Policies)**

- Hiển thị danh sách các chính sách có sẵn (từ bảng `policies`, filter: `status = 'active'`)
- Các loại chính sách có thể có:
  - **Chính sách hủy tour:** Cancellation policy
  - **Chính sách đổi tour:** Change policy
  - **Chính sách hoàn tiền:** Refund policy
  - **Chính sách đặt tour:** Booking policy
  - **Chính sách khác:** Other policies (tùy `policy_type`)
- User có thể:
  - **Chọn chính sách có sẵn:** Checkbox để chọn nhiều policies
  - Mỗi policy hiển thị:
    - Tên chính sách
    - Loại chính sách (`policy_type`)
    - Mô tả ngắn
    - Nội dung chi tiết (có thể xem preview)
  - **Tạo chính sách mới:** Button "Thêm chính sách mới" (mở modal)
    - Form tạo policy mới:
      - **Tên chính sách** - REQUIRED
      - **Loại chính sách** - Optional (dropdown: cancellation/change/refund/booking/other)
      - **Mô tả** - Optional
      - **Nội dung chi tiết** - REQUIRED (rich text editor)
    - Sau khi tạo, policy mới được tự động chọn
- Sau khi chọn, các policies sẽ được link với tour qua bảng `tour_policies`

**Bước 7: Step 5 - Hình ảnh**

- Upload nhiều hình ảnh (tối đa 10 hình)
- Mỗi hình:
  - Upload file (jpg, png, gif, max 5MB mỗi file)
  - Caption (mô tả) - Optional
  - Đánh dấu hình chính (`is_primary`) - Chỉ 1 hình được chọn
  - Thứ tự hiển thị (`display_order`)
- Validation:
  - Tổng dung lượng không quá 10MB
  - Tối đa 10 hình

**Bước 8: Step 6 - Giá & Lưu**

- Form fields:
- **Số người tối thiểu:** Number (default: 15) - REQUIRED - Dùng để chia chi phí cố định và tính giá
- **Số người tối đa:** Number (default: 45)
- **Chi phí cố định của công ty:**
  - **Lương HDV:** Number (default: 0) - REQUIRED
  - **Chi phí quản lý tour:** Number (default: 0) - Optional
  - **Chi phí marketing:** Number (default: 0) - Optional
  - **Chi phí khác:** Number (default: 0) - Optional
  - **Tổng chi phí cố định:** Auto-calculate = Sum của các chi phí trên
  - **Chi phí cố định/người:** Auto-calculate = Tổng chi phí cố định ÷ Số người tối thiểu
  - **Tính toán giá tự động:**
    - Checkbox "Tự động tính giá từ chi phí"
    - **Chi phí dịch vụ/người:** Auto = Σ(unit_price × quantity) từ `itinerary_day_services` (Step 2 Tab 3) của tất cả các ngày
    - **Tổng chi phí/người:** Auto = Chi phí dịch vụ/người + Chi phí cố định/người
    - **Giá đề xuất/người:** Auto = Tổng chi phí/người
    - **Lưu ý:** Đã tính đủ chi phí (dịch vụ + lương HDV + marketing + quản lý) → Giá tour = Chi phí thực tế, không cần thêm markup/lợi nhuận
- **Giá bán (có thể chỉnh sửa):**
  - **Giá người lớn:** Number - REQUIRED (có thể click "Dùng giá đề xuất")
  - **Giá trẻ em:** Number (default: 0) - Optional
  - **Giá em bé:** Number (default: 0) - Optional
- **Hiển thị breakdown:**
  ```
  ┌─────────────────────────────────────────┐
  │ PHÂN TÍCH GIÁ TOUR                      │
  │ (Tính theo số người tối thiểu: 30)     │
  │ ─────────────────────────────────────── │
  │ Chi phí dịch vụ/người:                 │
  │   • Day 1: 500,000đ                     │
  │   • Day 2: 330,000đ                     │
  │   • Day 3: 250,000đ                     │
  │   Tổng: 1,080,000đ                      │
  │ ─────────────────────────────────────── │
  │ Chi phí cố định/người:     133,333đ    │
  │ ─────────────────────────────────────── │
  │ Tổng chi phí/người:      1,213,333đ    │
  │ ─────────────────────────────────────── │
  │ Giá đề xuất/người:       1,213,333đ    │
  │ ─────────────────────────────────────── │
  │ → Một người phải trả:    1,213,333đ    │
  │ (Đã tính đủ: dịch vụ + nhân sự +      │
  │  marketing + quản lý)                   │
  └─────────────────────────────────────────┘
  ```
- **Phần trăm đặt cọc:** Number (default: 30%)
- **Trạng thái:** Dropdown:
  - **Nháp (draft):** Chưa hoàn thiện, có thể sửa sau
  - **Chờ duyệt (pending):** Đã hoàn thiện, gửi admin duyệt
  - (Nếu Admin: Có thể chọn `active` trực tiếp)
- Click "Lưu Tour":

**Bước 9: Validate & Save**

- Server-side validation:
  - `name`: Required, min 2 ký tự
  - `duration_days`: Required, >= 1
  - `duration_nights`: <= `duration_days`
  - `adult_price`: Required, > 0
  - `child_price`: <= `adult_price`
  - `infant_price`: <= `child_price`
  - Itinerary count phải = `duration_days`
  - Images: Max 10, tổng size <= 10MB
- Nếu validation pass:
  - Auto-generate `tour_code` (format: TOUR-YYYYMMDD-XXX)
  - Insert vào `tours` table:
    - `tour_type` = 'public'
    - `status` = 'draft' hoặc 'pending' (tùy chọn)
    - `approval_status` = 'pending' (nếu status = 'pending')
    - `created_by` = current user ID
  - Insert Itinerary: Lưu từng ngày vào `itineraries`
  - **Insert Itinerary Timelines:** Lưu timeline chi tiết vào `itinerary_timelines` (từ Step 2 Tab 2)
  - **Insert Itinerary Day Services:** Lưu dịch vụ theo ngày vào `itinerary_day_services` (từ Step 2 Tab 3)
  - Insert Highlights: Lưu vào `tour_highlights`
  - Insert Included/Excluded: Lưu vào `tour_included_excluded`
  - Insert Tour Policies: Lưu các policies đã chọn vào `tour_policies` (many-to-many)
  - Upload Images: Lưu vào `tour_images`, upload file vào `public/uploads/tours/`
  - **Lưu ý:** Không lưu `tour_services` nữa (đã bỏ Step 3). Tất cả dịch vụ được lưu vào `itinerary_day_services` (Step 2 Tab 3)
  - Commit transaction
  - Hiển thị success: "Tạo tour thành công!"
  - Redirect về danh sách tours
- Nếu validation fail:
  - Hiển thị errors
  - Giữ lại dữ liệu đã nhập
  - Quay lại form với errors

---

### **LUỒNG 2: TẠO TOUR TỪ TEMPLATE (TOUR-002 - Clone)**

**Mô tả luồng:**

**Bước 1: Chọn Template**

- Từ Bước 1 của Luồng 1 → Chọn "Tạo tour từ template"
- Hiển thị danh sách tour mẫu (tours với `tour_type = 'public'` AND `approval_status = 'approved'` AND `status = 'active'`)
- Mỗi template hiển thị:
  - Tên tour
  - Mã tour
  - Số ngày/đêm
  - Hình ảnh thumbnail
  - Giá người lớn
- Click vào template → chuyển sang Bước 2

**Bước 2: Clone Data & Pre-fill Form**

- URL: `?act=staff-tours&action=createFromTemplate&template_id=X`
- Load template data đầy đủ:
  - Thông tin cơ bản tour
  - Itinerary (tất cả ngày)
  - **Itinerary Timelines** (timeline chi tiết từng ngày) - MỚI
  - **Itinerary Day Services** (dịch vụ theo ngày) - MỚI
  - Highlights
  - Included/Excluded
  - Tour Policies
  - Tour Images (chỉ load URL, không clone file)
  - **Lưu ý:** Không clone `tour_services` nữa (đã bỏ Step 3). Tất cả dịch vụ được clone từ `itinerary_day_services`
- Pre-fill form với dữ liệu template:
  - `name` = "[Custom] " + template.name
  - `tour_type` = 'custom' (force)
  - `parent_tour_id` = template.id
  - `status` = 'draft' (force)
  - Các field khác copy từ template
- Form giống như Luồng 1 (7 steps), nhưng đã có data sẵn

**Bước 3: Chỉnh sửa & Customize**

- User có thể:
  - Sửa tất cả thông tin
  - Thêm/bớt ngày trong itinerary
  - Thêm/bớt dịch vụ
  - Thêm/bớt chính sách (policies)
  - Upload hình ảnh mới (không dùng hình của template)
  - Điều chỉnh giá, số người, etc.
- Validation và Save giống Luồng 1

**Bước 4: Lưu Tour Custom**

- Khi lưu:
  - Tạo tour mới với `tour_type = 'custom'`
  - `parent_tour_id` = template.id (link với tour mẫu)
  - Tất cả dữ liệu được lưu như tour mới (không reference đến template)
  - Hình ảnh: Phải upload mới (không clone từ template)

---

### **LUỒNG 3: DUYỆT TOUR (TOUR-004)**

**Mô tả luồng:**

**Bước 1: Xem Tour Chờ Duyệt**

- Admin vào module "Quản lý Tour"
- Filter: `approval_status = 'pending'`
- Xem danh sách tours chờ duyệt

**Bước 2: Xem Chi tiết Tour**

- Click vào tour → xem đầy đủ thông tin
- Review:
  - Thông tin cơ bản
  - Lịch trình
  - Dịch vụ
  - Giá cả
  - Hình ảnh

**Bước 3: Duyệt hoặc Từ chối**

- **Nút "Duyệt":**
  - Set `approval_status` = 'approved'
  - Set `approved_by` = current admin ID
  - Set `approved_at` = NOW()
  - Set `status` = 'active' (nếu tour đã hoàn thiện)
  - Hiển thị success: "Đã duyệt tour thành công!"
- **Nút "Từ chối":**
  - Mở modal nhập lý do từ chối
  - Set `approval_status` = 'rejected'
  - Set `rejection_reason` = lý do
  - Set `status` = 'draft' (để staff có thể sửa lại)
  - Hiển thị success: "Đã từ chối tour"

---

## 📊 CÁC TRƯỜNG DỮ LIỆU LIÊN QUAN

### **1. Bảng `tours`**

| Trường                    | Loại          | Bắt buộc | Mô tả                                                 | Validation                                    |
| ------------------------- | ------------- | -------- | ----------------------------------------------------- | --------------------------------------------- |
| id                        | INT           | ✅       | Primary key                                           | AUTO_INCREMENT                                |
| tour_code                 | VARCHAR(50)   | ❌       | Mã tour                                               | UNIQUE                                        |
| name                      | VARCHAR(200)  | ✅       | Tên tour                                              | NOT NULL                                      |
| thumbnail                 | VARCHAR(255)  | ❌       | Ảnh đại diện                                          |                                               |
| introduction              | TEXT          | ❌       | Giới thiệu ngắn                                       |                                               |
| description               | TEXT          | ❌       | Mô tả chi tiết                                        |                                               |
| duration_days             | INT           | ✅       | Số ngày                                               | NOT NULL, >= 1                                |
| duration_nights           | INT           | ✅       | Số đêm                                                | NOT NULL, <= duration_days                    |
| departure_location        | VARCHAR(200)  | ❌       | Điểm khởi hành                                        |                                               |
| min_participants          | INT           | ✅       | Số người tối thiểu                                    | DEFAULT 15                                    |
| max_participants          | INT           | ✅       | Số người tối đa                                       | DEFAULT 45                                    |
| adult_price               | DECIMAL(15,2) | ✅       | Giá người lớn                                         | NOT NULL, > 0                                 |
| child_price               | DECIMAL(15,2) | ✅       | Giá trẻ em                                            | DEFAULT 0.00                                  |
| infant_price              | DECIMAL(15,2) | ✅       | Giá em bé                                             | DEFAULT 0.00                                  |
| estimated_cost_per_person | DECIMAL(15,2) | ❌       | Giá ước tính chi phí/người                            |                                               |
| markup_percentage         | DECIMAL(5,2)  | ❌       | DEPRECATED - Không dùng nữa                           | DEFAULT 0.00 (giữ lại để backward compatible) |
| deposit_percentage        | DECIMAL(5,2)  | ✅       | Phần trăm đặt cọc                                     | DEFAULT 30.00                                 |
| fixed_cost_guide          | DECIMAL(15,2) | ❌       | Chi phí lương HDV (cố định)                           | DEFAULT 0.00                                  |
| fixed_cost_management     | DECIMAL(15,2) | ❌       | Chi phí quản lý (cố định)                             | DEFAULT 0.00                                  |
| fixed_cost_marketing      | DECIMAL(15,2) | ❌       | Chi phí marketing (cố định)                           | DEFAULT 0.00                                  |
| fixed_cost_other          | DECIMAL(15,2) | ❌       | Chi phí khác (cố định)                                | DEFAULT 0.00                                  |
| booking_deadline_days     | INT           | ❌       | Số ngày tối thiểu trước ngày khởi hành để đặt booking | DEFAULT 1                                     |
| tour_type                 | ENUM          | ✅       | public/custom                                         | DEFAULT 'public'                              |
| approval_status           | ENUM          | ✅       | pending/approved/rejected                             | DEFAULT 'pending'                             |
| approved_by               | INT           | ❌       | Foreign key → users                                   | FK, NULL                                      |
| approved_at               | TIMESTAMP     | ❌       | Thời gian duyệt                                       |                                               |
| rejection_reason          | TEXT          | ❌       | Lý do từ chối                                         |                                               |
| status                    | ENUM          | ✅       | active/inactive/draft                                 | DEFAULT 'draft'                               |
| created_by                | INT           | ❌       | Foreign key → users                                   | FK, NULL                                      |
| created_at                | TIMESTAMP     | ❌       | Ngày tạo                                              |                                               |
| updated_at                | TIMESTAMP     | ❌       | Ngày cập nhật                                         |                                               |
| parent_tour_id            | INT           | ❌       | Foreign key → tours (template)                        | FK, NULL                                      |

**Mối quan hệ:**

- N Tours → 1 User (`created_by`) - Optional
- N Tours → 1 User (`approved_by`) - Optional
- N Tours → 1 Tour (`parent_tour_id`) - Optional (nếu là tour custom, link với tour mẫu)
- 1 Tour → N Tour Schedules (`tour_schedules.tour_id`)
- 1 Tour → N Itineraries (`itineraries.tour_id`)
- 1 Tour → N Itinerary Day Services (`itinerary_day_services.itinerary_id` → `itineraries.tour_id`)
- 1 Tour → N Tour Images (`tour_images.tour_id`)
- 1 Tour → N Tour Highlights (`tour_highlights.tour_id`)
- 1 Tour → N Tour Included/Excluded (`tour_included_excluded.tour_id`)
- 1 Tour → N Tour FAQs (`tour_faqs.tour_id`)
- 1 Tour → N Bookings (`bookings.tour_id`)

**Business Logic:**

- `tour_code` = auto-generate (TOUR-YYYYMMDD-XXX)
- `child_price` <= `adult_price`
- `infant_price` <= `child_price`
- `duration_nights` <= `duration_days`
- Tour Public: `parent_tour_id` = NULL
- Tour Custom: `parent_tour_id` = ID của tour mẫu
- Staff chỉ có thể tạo tour với `status` = 'draft' hoặc 'pending'
- Admin mới có thể approve/reject tour

---

### **2. Bảng `itineraries`**

| Trường         | Loại         | Bắt buộc | Mô tả                      | Validation     |
| -------------- | ------------ | -------- | -------------------------- | -------------- |
| id             | INT          | ✅       | Primary key                | AUTO_INCREMENT |
| tour_id        | INT          | ✅       | Foreign key → tours        | NOT NULL, FK   |
| destination_id | INT          | ❌       | Foreign key → destinations | FK, NULL       |
| day_number     | INT          | ✅       | Số ngày (1, 2, 3...)       | NOT NULL       |
| title          | VARCHAR(200) | ❌       | Tiêu đề ngày               |                |
| description    | TEXT         | ❌       | Mô tả lịch trình           |                |
| meals          | JSON         | ❌       | Bữa ăn (array)             |                |
| accommodation  | VARCHAR(200) | ❌       | Nơi nghỉ                   |                |
| arrival_time   | TIME         | ❌       | Giờ đến                    |                |
| departure_time | TIME         | ❌       | Giờ khởi hành              |                |
| display_order  | INT          | ❌       | Thứ tự hiển thị            | DEFAULT 0      |

**Mối quan hệ:**

- N Itineraries → 1 Tour (`tour_id`) - REQUIRED
- N Itineraries → 1 Destination (`destination_id`) - Optional

**Business Logic:**

- `day_number` phải từ 1 đến `tours.duration_days`
- `meals` lưu dạng JSON array: `["breakfast", "lunch", "dinner"]`
- Số lượng itineraries phải = `tours.duration_days` (validate khi tạo/sửa)

---

### **3. Bảng `tour_services`**

| Trường               | Loại          | Bắt buộc | Mô tả                     | Validation           |
| -------------------- | ------------- | -------- | ------------------------- | -------------------- |
| id                   | INT           | ✅       | Primary key               | AUTO_INCREMENT       |
| tour_id              | INT           | ✅       | Foreign key → tours       | NOT NULL, FK         |
| service_id           | INT           | ✅       | Foreign key → services    | NOT NULL, FK         |
| service_name         | VARCHAR(200)  | ❌       | Tên dịch vụ (snapshot)    |                      |
| calculation_type     | ENUM          | ✅       | per_person (đơn giản hóa) | DEFAULT 'per_person' |
| fixed_quantity       | INT           | ❌       | Không dùng (deprecated)   | DEFAULT 1            |
| group_size           | INT           | ❌       | Không dùng (deprecated)   |                      |
| unit_price           | DECIMAL(15,2) | ✅       | Đơn giá/người             | NOT NULL, > 0        |
| unit                 | VARCHAR(50)   | ❌       | Đơn vị                    |                      |
| notes                | TEXT          | ❌       | Ghi chú                   |                      |
| is_included_in_price | TINYINT(1)    | ✅       | Bao gồm trong giá         | DEFAULT 1            |

**Mối quan hệ:**

- N Tour Services → 1 Tour (`tour_id`) - REQUIRED
- N Tour Services → 1 Service (`service_id`) - REQUIRED

**Business Logic:**

- `service_name` lưu snapshot tên dịch vụ tại thời điểm tạo (để phòng khi tên service thay đổi)
- `calculation_type`:
  - `per_person`: Tính theo số người trong booking
  - `per_group`: Tính cố định cho cả nhóm
  - `per_day`: Tính theo số ngày tour
  - `fixed`: Tính cố định (dùng `fixed_quantity`)
- `is_included_in_price` = 1: Dịch vụ đã tính trong giá tour
- `is_included_in_price` = 0: Dịch vụ tính riêng (có thể thêm vào booking sau)

---

### **4. Bảng `tour_highlights`**

| Trường        | Loại | Bắt buộc | Mô tả               | Validation     |
| ------------- | ---- | -------- | ------------------- | -------------- |
| id            | INT  | ✅       | Primary key         | AUTO_INCREMENT |
| tour_id       | INT  | ✅       | Foreign key → tours | NOT NULL, FK   |
| highlight     | TEXT | ✅       | Nội dung highlight  | NOT NULL       |
| display_order | INT  | ❌       | Thứ tự hiển thị     | DEFAULT 0      |

**Mối quan hệ:**

- N Tour Highlights → 1 Tour (`tour_id`) - REQUIRED

**Business Logic:**

- Một tour có thể có nhiều highlights
- `display_order` để sắp xếp thứ tự hiển thị

---

### **5. Bảng `tour_included_excluded`**

| Trường        | Loại | Bắt buộc | Mô tả               | Validation     |
| ------------- | ---- | -------- | ------------------- | -------------- |
| id            | INT  | ✅       | Primary key         | AUTO_INCREMENT |
| tour_id       | INT  | ✅       | Foreign key → tours | NOT NULL, FK   |
| type          | ENUM | ✅       | included/excluded   | NOT NULL       |
| item          | TEXT | ✅       | Nội dung item       | NOT NULL       |
| display_order | INT  | ❌       | Thứ tự hiển thị     | DEFAULT 0      |

**Mối quan hệ:**

- N Tour Included/Excluded → 1 Tour (`tour_id`) - REQUIRED

**Business Logic:**

- `type` = 'included': Các dịch vụ bao gồm trong giá
- `type` = 'excluded': Các dịch vụ không bao gồm trong giá
- Một tour có thể có nhiều items cho mỗi type

---

### **6. Bảng `tour_images`**

| Trường        | Loại         | Bắt buộc | Mô tả               | Validation     |
| ------------- | ------------ | -------- | ------------------- | -------------- |
| id            | INT          | ✅       | Primary key         | AUTO_INCREMENT |
| tour_id       | INT          | ✅       | Foreign key → tours | NOT NULL, FK   |
| image_url     | VARCHAR(255) | ✅       | Đường dẫn hình ảnh  | NOT NULL       |
| caption       | VARCHAR(255) | ❌       | Mô tả hình ảnh      |                |
| is_primary    | TINYINT(1)   | ❌       | Hình ảnh chính      | DEFAULT 0      |
| display_order | INT          | ❌       | Thứ tự hiển thị     | DEFAULT 0      |
| created_at    | TIMESTAMP    | ❌       | Ngày tạo            |                |

**Mối quan hệ:**

- N Tour Images → 1 Tour (`tour_id`) - REQUIRED

**Business Logic:**

- Một tour có thể có nhiều hình ảnh (tối đa 10 khi upload)
- Chỉ có 1 hình được đánh dấu `is_primary = 1` (hình đại diện)
- Hình ảnh được upload vào `public/uploads/tours/tour_{tour_id}_{timestamp}.{ext}`
- `thumbnail` trong `tours` table = URL của hình có `is_primary = 1`

---

### **7. Bảng `tour_faqs`**

| Trường        | Loại | Bắt buộc | Mô tả               | Validation     |
| ------------- | ---- | -------- | ------------------- | -------------- |
| id            | INT  | ✅       | Primary key         | AUTO_INCREMENT |
| tour_id       | INT  | ✅       | Foreign key → tours | NOT NULL, FK   |
| question      | TEXT | ✅       | Câu hỏi             | NOT NULL       |
| answer        | TEXT | ✅       | Câu trả lời         | NOT NULL       |
| display_order | INT  | ❌       | Thứ tự hiển thị     | DEFAULT 0      |

**Mối quan hệ:**

- N Tour FAQs → 1 Tour (`tour_id`) - REQUIRED

**Business Logic:**

- Một tour có thể có nhiều FAQs
- `display_order` để sắp xếp thứ tự hiển thị

---

### **8. Bảng `policies`**

| Trường      | Loại         | Bắt buộc | Mô tả             | Validation       |
| ----------- | ------------ | -------- | ----------------- | ---------------- |
| id          | INT          | ✅       | Primary key       | AUTO_INCREMENT   |
| name        | VARCHAR(200) | ✅       | Tên chính sách    | NOT NULL         |
| description | TEXT         | ❌       | Mô tả ngắn        |                  |
| policy_type | VARCHAR(50)  | ❌       | Loại chính sách   |                  |
| content     | TEXT         | ✅       | Nội dung chi tiết | NOT NULL         |
| status      | ENUM         | ✅       | active/inactive   | DEFAULT 'active' |
| created_at  | TIMESTAMP    | ❌       | Ngày tạo          |                  |
| updated_at  | TIMESTAMP    | ❌       | Ngày cập nhật     |                  |

**Mối quan hệ:**

- 1 Policy → N Tour Policies (`tour_policies.policy_id`)

**Business Logic:**

- `policy_type` có thể là: `cancellation`, `change`, `refund`, `booking`, `other`
- Một policy có thể được sử dụng cho nhiều tours
- Khi policy bị xóa hoặc inactive, các tour vẫn giữ link (nhưng không hiển thị trong form chọn)

---

### **9. Bảng `tour_policies`**

| Trường    | Loại | Bắt buộc | Mô tả                  | Validation     |
| --------- | ---- | -------- | ---------------------- | -------------- |
| id        | INT  | ✅       | Primary key            | AUTO_INCREMENT |
| tour_id   | INT  | ✅       | Foreign key → tours    | NOT NULL, FK   |
| policy_id | INT  | ✅       | Foreign key → policies | NOT NULL, FK   |

**Mối quan hệ:**

- N Tour Policies → 1 Tour (`tour_id`) - REQUIRED
- N Tour Policies → 1 Policy (`policy_id`) - REQUIRED

**Business Logic:**

- Bảng junction để link nhiều policies với một tour (many-to-many)
- Một tour có thể có nhiều policies
- Một policy có thể được dùng cho nhiều tours
- Khi tour hoặc policy bị xóa, records trong bảng này tự động xóa (CASCADE)

---

### **10. Bảng `tour_schedules`**

| Trường       | Loại          | Bắt buộc | Mô tả                           | Validation     |
| ------------ | ------------- | -------- | ------------------------------- | -------------- |
| id           | INT           | ✅       | Primary key                     | AUTO_INCREMENT |
| tour_id      | INT           | ✅       | Foreign key → tours             | NOT NULL, FK   |
| start_date   | DATE          | ✅       | Ngày khởi hành                  | NOT NULL       |
| end_date     | DATE          | ✅       | Ngày kết thúc                   | NOT NULL       |
| quota        | INT           | ✅       | Số chỗ có sẵn                   | DEFAULT 20     |
| booked       | INT           | ✅       | Số chỗ đã đặt                   | DEFAULT 0      |
| adult_price  | DECIMAL(15,2) | ❌       | Giá người lớn (override)        |                |
| child_price  | DECIMAL(15,2) | ❌       | Giá trẻ em (override)           |                |
| infant_price | DECIMAL(15,2) | ❌       | Giá em bé (override)            |                |
| status       | ENUM          | ✅       | open/closed/completed/cancelled | DEFAULT 'open' |
| guide_id     | INT           | ❌       | Foreign key → users (guide)     | FK, NULL       |
| guide_notes  | TEXT          | ❌       | Ghi chú cho guide               |                |
| created_at   | TIMESTAMP     | ❌       | Ngày tạo                        |                |
| updated_at   | TIMESTAMP     | ❌       | Ngày cập nhật                   |                |

**Mối quan hệ:**

- N Tour Schedules → 1 Tour (`tour_id`) - REQUIRED
- N Tour Schedules → 1 User (`guide_id`) - Optional (hướng dẫn viên)

**Business Logic:**

- `end_date` = `start_date` + `tours.duration_days` - 1
- Unique constraint: (`tour_id`, `start_date`, `end_date`) - không được trùng
- `booked` <= `quota`
- Nếu `adult_price` NULL: Lấy từ `tours.adult_price`
- Nếu `adult_price` có giá trị: Override giá từ tour (dùng giá schedule)
- Khi tạo booking từ schedule, ưu tiên giá từ schedule

---

## ✅ VALIDATION RULES

### **Luồng 1: Tạo Tour Mẫu**

1. **Tên Tour (`name`):**

   - Required
   - Min length: 2 ký tự
   - Max length: 200 ký tự

2. **Số ngày (`duration_days`):**

   - Required
   - > = 1
   - Integer

3. **Số đêm (`duration_nights`):**

   - Optional
   - <= `duration_days`
   - Integer, >= 0

4. **Giá người lớn (`adult_price`):**

   - Required
   - > 0
   - Format: DECIMAL(15,2)

5. **Giá trẻ em (`child_price`):**

   - Optional (default: 0)
   - <= `adult_price`
   - > = 0

6. **Giá em bé (`infant_price`):**

   - Optional (default: 0)
   - <= `child_price`
   - > = 0

7. **Lịch trình (Itinerary):**

   - Số lượng itinerary phải = `duration_days`
   - Mỗi ngày phải có `day_number` từ 1 đến `duration_days`
   - Không được trùng `day_number`

8. **Hình ảnh (Images):**

   - Tối đa 10 hình
   - Tổng dung lượng <= 10MB
   - File type: .jpg, .jpeg, .png, .gif
   - Mỗi file max 5MB

9. **Trạng thái (`status`):**

   - Staff chỉ được chọn: `draft` hoặc `pending`
   - Admin có thể chọn: `draft`, `pending`, `active`

10. **Tour Type (`tour_type`):**
    - Khi tạo mới từ đầu: Auto-set = 'public'
    - Khi clone từ template: Auto-set = 'custom'

---

## 🔒 BUSINESS RULES

1. **Tour Creation:**

   - `tour_code` tự động generate (TOUR-YYYYMMDD-XXX), không được trùng
   - Tour Public (`tour_type = 'public'`): `parent_tour_id` = NULL
   - Tour Custom (`tour_type = 'custom'`): `parent_tour_id` = ID tour mẫu
   - Staff chỉ có thể tạo tour với `status` = 'draft' hoặc 'pending'
   - Admin có thể tạo tour với `status` = 'active' (skip approval)

2. **Tour Approval:**

   - Tour với `approval_status = 'pending'` mới được duyệt
   - Chỉ Admin mới có quyền approve/reject
   - Khi approve: Set `approved_by`, `approved_at`, `approval_status = 'approved'`
   - Khi reject: Set `approval_status = 'rejected'`, `rejection_reason`, `status = 'draft'`

3. **Itinerary:**

   - Số lượng itinerary phải đúng bằng `duration_days`
   - `day_number` bắt đầu từ 1, không được nhảy số
   - Có thể có nhiều ngày đến cùng một destination

4. **Tour Services:**

   - Services phải tồn tại trong bảng `services` và `status = 'active'`
   - `service_name` lưu snapshot để phòng khi service bị xóa hoặc đổi tên
   - **Đơn giản hóa:** Tất cả services đều tính `per_person` (theo từng người)
   - `unit_price` > 0 (REQUIRED) - Đơn giá cho 1 người
   - **Tính tổng chi phí dịch vụ/người:**
     - Chi phí dịch vụ/người = Σ(`unit_price`) của tất cả services có `is_included_in_price = 1`

5. **Chi phí cố định của công ty:**

   - Bao gồm: Lương HDV, Chi phí quản lý tour, Chi phí marketing, Chi phí khác
   - **Tính chi phí cố định/người:**
     - Tổng chi phí cố định = `fixed_cost_guide` + `fixed_cost_management` + `fixed_cost_marketing` + `fixed_cost_other`
     - Chi phí cố định/người = Tổng chi phí cố định ÷ `min_participants`

6. **Tour Images:**

   - Khi upload, tự động set hình đầu tiên làm `is_primary = 1`
   - Có thể thay đổi `is_primary` (chỉ 1 hình được chọn)
   - `thumbnail` trong `tours` table = URL của hình có `is_primary = 1`

7. **Tour Policies:**

   - Một tour có thể có nhiều policies (chính sách hủy, đổi, hoàn tiền, etc.)
   - Policies được chọn từ danh sách policies có sẵn (`policies` table, `status = 'active'`)
   - Có thể tạo policy mới ngay trong form tạo tour (modal)
   - Policies được link với tour qua bảng `tour_policies` (many-to-many)
   - Khi hiển thị tour cho khách, các policies sẽ được hiển thị theo `policy_type`

8. **Tour from Template (Clone):**
   - Clone tất cả dữ liệu: Itinerary, Highlights, Included/Excluded, Services, Policies
   - KHÔNG clone hình ảnh (phải upload mới)
   - `parent_tour_id` link với tour mẫu
   - Tất cả dữ liệu được lưu độc lập (không reference, nên nếu template bị xóa, tour custom vẫn còn)

---

## ⚠️ TRƯỜNG HỢP ĐẶC BIỆT

1. **Tour Code Conflict:**

   - Nếu auto-generate bị trùng (rất hiếm): Retry với sequence cao hơn
   - Nếu user tự nhập và trùng: Validate và reject

2. **Itinerary Không đủ số ngày:**

   - Validate: Số lượng itinerary phải = `duration_days`
   - Nếu thiếu: Hiển thị error: "Lịch trình phải nhập đủ cho X ngày"
   - Nếu thừa: Hiển thị cảnh báo hoặc tự động xóa các ngày thừa

3. **Upload Hình ảnh Lỗi:**

   - Nếu 1 hình upload fail: Skip hình đó, tiếp tục upload các hình khác
   - Log lỗi để user biết hình nào không upload được
   - Nếu tất cả hình đều fail: Vẫn tạo tour (tour có thể không có hình)

4. **Tour Service bị Xóa:**

   - Nếu service trong `services` bị xóa/ inactive: Tour service vẫn giữ nguyên (do có snapshot `service_name`)
   - Khi hiển thị: Nếu `service_id` không tồn tại, chỉ hiển thị `service_name`

5. **Clone Tour với Parent Tour bị Xóa:**

   - Tour custom vẫn hoạt động bình thường (do đã clone đầy đủ dữ liệu)
   - `parent_tour_id` có thể NULL (nếu parent tour bị xóa) hoặc link đến tour không còn tồn tại
   - Có thể hiển thị cảnh báo "Tour mẫu không còn tồn tại" nhưng không ảnh hưởng tour custom

6. **Thay đổi Duration Days sau khi đã có Itinerary:**
   - Khi edit tour: Nếu thay đổi `duration_days`:
     - Nếu tăng: Thêm các ngày mới (có thể để trống)
     - Nếu giảm: Cảnh báo và xóa các ngày thừa (hoặc không cho phép giảm nếu đã có booking)

---

## 🔗 DEPENDENCIES

### **Phụ thuộc vào:**

- Module Location Services: Destinations (itineraries.destination_id)
- Module Location Services: Services (tour_services.service_id)
- Module System: Users (tours.created_by, tours.approved_by, tour_schedules.guide_id)
- Module System: Policies (tour_policies.policy_id)

### **Ảnh hưởng đến:**

- Module Booking: Tours được dùng trong bookings
- Module Tour Schedules: Tours có nhiều schedules (ngày khởi hành)
- Module Operations: Tours được phân công guide

---

## 📝 GHI CHÚ

1. **Tour Code Format:**

   - `tour_code`: TOUR-YYYYMMDD-XXX (ví dụ: TOUR-20241206-001)

2. **Wizard UI:**

   - Form được chia thành 6 steps
   - User có thể điều hướng qua lại giữa các steps
   - Validation được thực hiện ở Step 6 (trước khi lưu)
   - Có thể lưu tạm (draft) và quay lại chỉnh sửa sau

3. **Clone từ Template:**

   - Chỉ clone tour với `tour_type = 'public'` AND `approval_status = 'approved'`
   - Clone tất cả dữ liệu ngoại trừ hình ảnh
   - Tour mới có `tour_type = 'custom'` và `parent_tour_id` = template.id

4. **Itinerary Meals Format:**
   - `meals` lưu dạng JSON: `["breakfast", "lunch", "dinner"]`
   - Có thể là: "breakfast", "lunch", "dinner", "snack"

---

**Status:** ⏳ Đang phân tích - Chờ user review và bổ sung
