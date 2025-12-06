# 📊 PHÂN TÍCH LUỒNG: LOCATION SERVICES MODULE

## 🎯 THÔNG TIN CHUNG

- **Module:** Location Services 🌍
- **Mục đích:** Quản lý địa điểm, dịch vụ, nhà dịch vụ và bảng giá
- **Ngày cập nhật:** 2024-12-06
- **Status:** ✅ Đã cập nhật theo database mới

---

## 📋 MÔ TẢ TỔNG QUAN

Module Location Services quản lý hệ thống phân cấp:

```
Countries → Provinces → Service Providers → Services → Service Prices
                ↓
        Destinations
```

**Lưu ý quan trọng:**

- ❌ **Không còn khái niệm "Supplier"** - Đã bỏ hoàn toàn
- ❌ **Không còn khái niệm "Category"** - Đã bỏ hoàn toàn
- ✅ **Service Providers** là đơn vị độc lập, chỉ link với Province và Country
- ✅ **Destinations** có thể link với Province và Country (optional)

**Lưu ý:** Các chức năng CRUD cơ bản (xem, thêm, sửa, xóa) đã được bỏ qua, tập trung vào **luồng thao tác phức tạp của người dùng**.

---

## 🔄 LUỒNG 1: QUẢN LÝ TỈNH - TAB VIEW

### **Mô tả luồng:**

**Bước 1: Chọn Quốc gia**

- User vào module Location Services
- Xem danh sách các quốc gia **active** (không filter theo type)
- Click vào quốc gia → hiển thị danh sách tỉnh của quốc gia đó

**Bước 2: Chọn Tỉnh - Hiển thị Tab View**

- Xem danh sách tỉnh của quốc gia đã chọn
- Click vào tỉnh → hiển thị trang quản lý tỉnh với 2 tabs:

  **Tab 1: Nhà cung cấp dịch vụ (Service Providers)**

  - Hiển thị danh sách các nhà dịch vụ (service providers) thuộc tỉnh đó
  - Filter: `service_providers.province_id = [tỉnh đã chọn]`
  - Mỗi service provider hiển thị:
    - Mã nhà dịch vụ (service_code)
    - Tên nhà dịch vụ
    - Địa chỉ
    - Số lượng dịch vụ (badge)
    - Trạng thái (active/inactive)
    - Actions: Sửa, Xóa, Xem chi tiết
  - Có thể chưa có nhà dịch vụ nào → hiển thị "Chưa có nhà cung cấp nào"
  - Nút "Thêm nhà cung cấp dịch vụ" → chuyển sang Luồng 2

  **Tab 2: Địa điểm du lịch (Destinations)**

  - Hiển thị danh sách các địa điểm (destinations) thuộc tỉnh đó
  - Filter: `destinations.province_id = [tỉnh đã chọn]`
  - Mỗi destination hiển thị:
    - Tên địa điểm
    - Mô tả ngắn
    - Hình ảnh (nếu có)
    - Trạng thái (active/inactive)
    - Actions: Sửa, Xóa, Quản lý hình ảnh, Xem chi tiết
  - Nút "Thêm địa điểm du lịch" → Mở modal/form tạo destination mới (Luồng 3)
  - Có thể quản lý hình ảnh cho destination trong tab này

**URL Pattern:**

- Tab Service Providers: `?act=admin&module=location-services&country_id=X&province_id=Y&tab=providers`
- Tab Destinations: `?act=admin&module=location-services&country_id=X&province_id=Y&tab=destinations`
- Default: `?act=admin&module=location-services&country_id=X&province_id=Y` (tab=providers)

---

## 🔄 LUỒNG 2: TẠO NHÀ DỊCH VỤ MỚI

### **Mô tả luồng:**

**Trigger:** Từ Tab "Nhà cung cấp dịch vụ" → Click nút "Thêm nhà cung cấp dịch vụ"

**Bước 1: Mở Modal Form**

- Mở modal form "Thêm nhà dịch vụ"
- Context hiển thị: "Đang thêm cho: [Tên Quốc gia] > [Tên Tỉnh]"
- Form fields:
  - **Tên nhà dịch vụ** (VD: Khách sạn ABC, Nhà hàng XYZ) - REQUIRED
  - **Loại dịch vụ (Service Type):** Dropdown - Optional
    - Hiển thị tất cả service_types active
    - Có thể để trống (không bắt buộc)
  - **Tỉnh/Thành phố:** Auto-fill (readonly) - [Tên Tỉnh]
  - **Quốc gia:** Auto-fill (readonly) - [Tên Quốc gia]
  - **Mô tả:** Textarea - Optional
  - **Thông tin liên hệ:**
    - Contact person - Optional
    - Email - Optional
    - Phone - Optional
    - Website - Optional
    - Address - Optional
  - **Trạng thái:** Active/Inactive (default: Active)

**Bước 2: Submit Form**

- Click "Lưu":
  - Validate: Tên nhà dịch vụ không được trùng với provider khác cùng tỉnh
  - Tạo service provider với:
    - `province_id` = tỉnh đã chọn (REQUIRED)
    - `country_id` = quốc gia đã chọn (REQUIRED)
    - `service_type_id` = service type đã chọn (optional)
  - Auto-generate `service_code` (format: **SP-YYYYMMDD-XXX**)
    - VD: SP-20241206-001, SP-20241206-002, ...
  - Redirect về Tab "Nhà cung cấp dịch vụ", hiển thị nhà dịch vụ mới trong danh sách

**Lưu ý:**

- ❌ **KHÔNG còn field Destination** - Service Provider không link với destination
- ❌ **KHÔNG còn field Supplier** - Đã bỏ hoàn toàn
- ✅ **Province và Country** được auto-fill từ context, không thể thay đổi

---

## 🔄 LUỒNG 3: TẠO ĐỊA ĐIỂM DU LỊCH

### **Mô tả luồng:**

**Bước 1: Vào Tab Destinations**

- Từ Bước 2 (Chọn Tỉnh) → Click Tab "Địa điểm du lịch"
- Hiển thị danh sách destinations của tỉnh đó

**Bước 2: Click "Thêm địa điểm du lịch"**

- Mở modal/form "Thêm địa điểm mới"
- Context hiển thị: "Đang thêm cho: [Tên Quốc gia] > [Tên Tỉnh]"

**Bước 3: Điền thông tin Destination**

- Form fields:
  - **Tên địa điểm** (VD: Hồ Xuân Hương, Chợ Đà Lạt, Tháp Eiffel) - REQUIRED
  - **Tỉnh/Thành phố:** Auto-fill (readonly) - [Tên Tỉnh]
  - **Quốc gia:** Auto-fill (readonly) - [Tên Quốc gia]
  - **Mô tả:** Textarea - Optional
  - **Vị trí cụ thể:** Textarea (số nhà, đường, phường/xã) - Optional
  - **Trạng thái:** Active/Inactive (default: Active)
- Click "Lưu":
  - Tạo destination với:
    - `province_id` = tỉnh đã chọn
    - `country_id` = quốc gia đã chọn
  - Redirect về Tab "Địa điểm du lịch", hiển thị destination mới trong danh sách

**Bước 4: Quản lý hình ảnh (Sau khi tạo)**

- Từ danh sách destinations → Click "Quản lý hình ảnh"
- Upload nhiều hình ảnh
- Đánh dấu hình ảnh chính (is_primary)
- Sắp xếp thứ tự hiển thị (display_order)
- Thêm caption cho mỗi hình

---

## 🔄 LUỒNG 4: THÊM DỊCH VỤ CHO NHÀ DỊCH VỤ

### **Mô tả luồng:**

**Bước 1: Chọn Service Provider**

- Từ Tab "Nhà cung cấp dịch vụ" → Click vào service provider
- Hoặc từ danh sách → Click "Xem chi tiết"

**Bước 2: Xem danh sách Services**

- Hiển thị danh sách services của service provider đó
- Filter: `services.service_provider_id = [service provider đã chọn]`
- Có thể chưa có service nào → hiển thị "Chưa có dịch vụ nào"
- Nút "Thêm dịch vụ" → chuyển sang Bước 3

**Bước 3: Thêm Dịch vụ cho Nhà dịch vụ**

- Click "Thêm dịch vụ"
- Form fields:
  - **Loại dịch vụ (Service Type):** Dropdown - REQUIRED
    - Hiển thị tất cả service_types active
  - **Tên dịch vụ:** VD "Phòng Deluxe", "Buffet sáng", "Vé tham quan" - REQUIRED
  - **Đơn vị:** VD "phòng", "người", "suất", "vé" - Optional
  - **Giá ước tính:** Optional
  - **Mô tả:** Optional
  - **Ghi chú:** Optional
- Click "Lưu":
  - Tạo service, link với service provider
  - Auto-generate `service_code` (format: **SRV-YYYYMMDD-XXX**)
    - VD: SRV-20241206-001, SRV-20241206-002, ...

**Bước 4: Thêm Bảng giá cho Dịch vụ**

- Từ danh sách dịch vụ, click "Quản lý giá" hoặc "Thêm giá"
- Form fields:
  - **Địa điểm (Destination):** Dropdown - Optional
    - Hiển thị tất cả destinations active
    - Có thể để trống (giá chung)
  - **Tỉnh (Province):** Auto-fill từ service → service_provider → province - Optional
  - **Đơn giá:** REQUIRED
  - **Đơn vị tiền tệ:** Mặc định VND
  - **Loại giá:**
    - Standard (mặc định)
    - Peak (mùa cao điểm)
    - Low (mùa thấp điểm)
    - Custom
  - **Ngày hiệu lực từ/đến:** Optional (nếu có giá theo thời gian)
  - **Trạng thái:** Active/Inactive (default: Active)
  - **Ghi chú:** Optional
- Click "Lưu": Tạo service_price

---

## 📊 CÁC TRƯỜNG DỮ LIỆU LIÊN QUAN

### **1. Bảng `countries`**

| Trường        | Loại         | Bắt buộc | Mô tả           | Validation       |
| ------------- | ------------ | -------- | --------------- | ---------------- |
| id            | INT          | ✅       | Primary key     | AUTO_INCREMENT   |
| code          | VARCHAR(10)  | ✅       | Mã quốc gia     | UNIQUE, NOT NULL |
| name          | VARCHAR(100) | ✅       | Tên quốc gia    | NOT NULL         |
| name_en       | VARCHAR(100) | ❌       | Tên tiếng Anh   |                  |
| status        | ENUM         | ✅       | active/inactive | DEFAULT 'active' |
| display_order | INT          | ❌       | Thứ tự hiển thị | DEFAULT 0        |

**Mối quan hệ:**

- 1 Country → Nhiều Provinces (`provinces.country_id`)
- 1 Country → Nhiều Service Providers (`service_providers.country_id`)
- 1 Country → Nhiều Destinations (`destinations.country_id`)

**Lưu ý:**

- ❌ **KHÔNG còn trường `type`** - Đã bỏ phân loại domestic/international

---

### **2. Bảng `provinces`**

| Trường        | Loại         | Bắt buộc | Mô tả                   | Validation       |
| ------------- | ------------ | -------- | ----------------------- | ---------------- |
| id            | INT          | ✅       | Primary key             | AUTO_INCREMENT   |
| country_id    | INT          | ✅       | Foreign key → countries | NOT NULL, FK     |
| code          | VARCHAR(20)  | ❌       | Mã tỉnh                 | UNIQUE           |
| name          | VARCHAR(100) | ✅       | Tên tỉnh                | NOT NULL         |
| name_en       | VARCHAR(100) | ❌       | Tên tiếng Anh           |                  |
| status        | ENUM         | ✅       | active/inactive         | DEFAULT 'active' |
| display_order | INT          | ❌       | Thứ tự hiển thị         | DEFAULT 0        |

**Mối quan hệ:**

- N Provinces → 1 Country (`country_id`)
- 1 Province → N Service Providers (`service_providers.province_id`)
- 1 Province → N Destinations (`destinations.province_id`)

---

### **3. Bảng `destinations`**

| Trường      | Loại         | Bắt buộc | Mô tả                   | Validation       |
| ----------- | ------------ | -------- | ----------------------- | ---------------- |
| id          | INT          | ✅       | Primary key             | AUTO_INCREMENT   |
| province_id | INT          | ❌       | Foreign key → provinces | FK, NULL         |
| country_id  | INT          | ❌       | Foreign key → countries | FK, NULL         |
| name        | VARCHAR(200) | ✅       | Tên địa điểm            | NOT NULL         |
| description | TEXT         | ❌       | Mô tả                   |                  |
| locations   | TEXT         | ❌       | Vị trí cụ thể           |                  |
| status      | ENUM         | ✅       | active/inactive         | DEFAULT 'active' |
| created_by  | INT          | ❌       | Người tạo               | FK → users       |
| updated_by  | INT          | ❌       | Người cập nhật          | FK → users       |

**Mối quan hệ:**

- N Destinations → 1 Province (`province_id`) - Optional
- N Destinations → 1 Country (`country_id`) - Optional
- N Destinations → N Service Prices (`service_prices.destination_id`) - Optional

**Lưu ý:**

- Khi tạo Destination từ Tab Destinations: Tự động set `province_id` và `country_id`
- Khi query Destinations trong Tab: Filter theo `province_id`
- ❌ **KHÔNG còn trường `category_id`** - Đã bỏ hoàn toàn

---

### **3.1. Bảng `destination_images`**

| Trường         | Loại         | Bắt buộc | Mô tả                      | Validation     |
| -------------- | ------------ | -------- | -------------------------- | -------------- |
| id             | INT          | ✅       | Primary key                | AUTO_INCREMENT |
| destination_id | INT          | ✅       | Foreign key → destinations | NOT NULL, FK   |
| image_url      | VARCHAR(255) | ✅       | Đường dẫn hình ảnh         | NOT NULL       |
| caption        | VARCHAR(255) | ❌       | Mô tả hình ảnh             |                |
| is_primary     | TINYINT(1)   | ❌       | Hình ảnh chính             | DEFAULT 0      |
| display_order  | INT          | ❌       | Thứ tự hiển thị            | DEFAULT 0      |
| created_at     | TIMESTAMP    | ❌       | Ngày tạo                   |                |

**Mối quan hệ:**

- N Destination Images → 1 Destination (`destination_id`) - REQUIRED

**Business Logic:**

- Một destination có thể có nhiều hình ảnh
- Chỉ có 1 hình ảnh được đánh dấu `is_primary = 1` (hình chính)
- `display_order` để sắp xếp thứ tự hiển thị

---

### **4. Bảng `service_providers`**

| Trường          | Loại         | Bắt buộc | Mô tả                       | Validation       |
| --------------- | ------------ | -------- | --------------------------- | ---------------- |
| id              | INT          | ✅       | Primary key                 | AUTO_INCREMENT   |
| service_code    | VARCHAR(50)  | ❌       | Mã nhà dịch vụ              | UNIQUE           |
| name            | VARCHAR(200) | ✅       | Tên nhà dịch vụ             | NOT NULL         |
| service_type_id | INT          | ❌       | Foreign key → service_types | FK, NULL         |
| description     | TEXT         | ❌       | Mô tả                       |                  |
| province_id     | INT          | ✅       | Foreign key → provinces     | NOT NULL, FK     |
| country_id      | INT          | ✅       | Foreign key → countries     | NOT NULL, FK     |
| address         | TEXT         | ❌       | Địa chỉ                     |                  |
| phone           | VARCHAR(20)  | ❌       | Số điện thoại               |                  |
| email           | VARCHAR(100) | ❌       | Email                       |                  |
| website         | VARCHAR(255) | ❌       | Website                     |                  |
| contact_person  | VARCHAR(100) | ❌       | Người liên hệ               |                  |
| status          | ENUM         | ✅       | active/inactive             | DEFAULT 'active' |
| created_by      | INT          | ❌       | Người tạo                   | FK → users       |
| created_at      | TIMESTAMP    | ❌       | Ngày tạo                    |                  |
| updated_at      | TIMESTAMP    | ❌       | Ngày cập nhật               |                  |

**Mối quan hệ:**

- N Service Providers → 1 Province (`province_id`) - **REQUIRED**
- N Service Providers → 1 Country (`country_id`) - **REQUIRED**
- N Service Providers → 1 Service Type (`service_type_id`) - Optional
- 1 Service Provider → N Services (`services.service_provider_id`)
- N Service Providers → 1 User (`created_by`) - Optional

**Business Logic:**

- `service_code` = auto-generate (format: **SP-YYYYMMDD-XXX**)
  - VD: SP-20241206-001, SP-20241206-002, ...
- Khi tạo từ tỉnh: `province_id` và `country_id` = tỉnh/quốc gia đã chọn (REQUIRED)
- Context: Luôn biết đang ở quốc gia/tỉnh nào
- Service Provider là đơn vị độc lập, **KHÔNG phụ thuộc vào Supplier**
- ❌ **KHÔNG còn trường `destination_id`** - Service Provider không link với destination
- ❌ **KHÔNG còn trường `supplier_id`** - Đã bỏ hoàn toàn

---

### **5. Bảng `service_types`**

| Trường        | Loại         | Bắt buộc | Mô tả            | Validation       |
| ------------- | ------------ | -------- | ---------------- | ---------------- |
| id            | INT          | ✅       | Primary key      | AUTO_INCREMENT   |
| name          | VARCHAR(100) | ✅       | Tên loại dịch vụ | NOT NULL         |
| description   | TEXT         | ❌       | Mô tả            |                  |
| status        | ENUM         | ✅       | active/inactive  | DEFAULT 'active' |
| display_order | INT          | ❌       | Thứ tự hiển thị  | DEFAULT 0        |

**Ví dụ:**

- Hotel, Restaurant, Ticket, Transportation, Guide, etc.

**Lưu ý:**

- ❌ **KHÔNG còn trường `code`** - Đã bỏ (nếu có trong schema cũ)

---

### **6. Bảng `services`**

| Trường              | Loại          | Bắt buộc | Mô tả                           | Validation       |
| ------------------- | ------------- | -------- | ------------------------------- | ---------------- |
| id                  | INT           | ✅       | Primary key                     | AUTO_INCREMENT   |
| service_code        | VARCHAR(50)   | ❌       | Mã dịch vụ                      | UNIQUE           |
| service_type_id     | INT           | ✅       | Foreign key → service_types     | NOT NULL, FK     |
| service_provider_id | INT           | ❌       | Foreign key → service_providers | FK, NULL         |
| name                | VARCHAR(200)  | ✅       | Tên dịch vụ                     | NOT NULL         |
| description         | TEXT          | ❌       | Mô tả                           |                  |
| unit                | VARCHAR(50)   | ❌       | Đơn vị (phòng, người, suất)     |                  |
| estimated_price     | DECIMAL(15,2) | ❌       | Giá ước tính                    |                  |
| notes               | TEXT          | ❌       | Ghi chú                         |                  |
| status              | ENUM          | ✅       | active/inactive                 | DEFAULT 'active' |
| created_at          | TIMESTAMP     | ❌       | Ngày tạo                        |                  |
| updated_at          | TIMESTAMP     | ❌       | Ngày cập nhật                   |                  |

**Mối quan hệ:**

- N Services → 1 Service Type (`service_type_id`) - REQUIRED
- N Services → 1 Service Provider (`service_provider_id`) - Optional (khuyến khích)
- 1 Service → N Service Prices (`service_prices.service_id`)

**Business Logic:**

- `service_code` = auto-generate (format: **SRV-YYYYMMDD-XXX**)
  - VD: SRV-20241206-001, SRV-20241206-002, ...
- Khi tạo từ Service Provider: `service_provider_id` = provider đã chọn
- Service có thể không có service_provider_id (dịch vụ chung), nhưng khuyến khích link với provider
- ❌ **KHÔNG còn trường `supplier_id`** - Đã bỏ hoàn toàn

---

### **7. Bảng `service_prices`**

| Trường         | Loại          | Bắt buộc | Mô tả                      | Validation         |
| -------------- | ------------- | -------- | -------------------------- | ------------------ |
| id             | INT           | ✅       | Primary key                | AUTO_INCREMENT     |
| service_id     | INT           | ✅       | Foreign key → services     | NOT NULL, FK       |
| destination_id | INT           | ❌       | Foreign key → destinations | FK, NULL           |
| province_id    | INT           | ❌       | Foreign key → provinces    | FK, NULL           |
| unit_price     | DECIMAL(15,2) | ✅       | Đơn giá                    | NOT NULL           |
| currency       | VARCHAR(10)   | ✅       | Đơn vị tiền tệ             | DEFAULT 'VND'      |
| valid_from     | DATE          | ❌       | Ngày hiệu lực từ           |                    |
| valid_to       | DATE          | ❌       | Ngày hiệu lực đến          |                    |
| price_type     | ENUM          | ✅       | standard/peak/low/custom   | DEFAULT 'standard' |
| status         | ENUM          | ✅       | active/inactive            | DEFAULT 'active'   |
| notes          | TEXT          | ❌       | Ghi chú                    |                    |
| created_at     | TIMESTAMP     | ❌       | Ngày tạo                   |                    |
| updated_at     | TIMESTAMP     | ❌       | Ngày cập nhật              |                    |

**Mối quan hệ:**

- N Service Prices → 1 Service (`service_id`) - REQUIRED
- N Service Prices → 1 Destination (`destination_id`) - Optional
- N Service Prices → 1 Province (`province_id`) - Optional

**Business Logic:**

- 1 Service có thể có nhiều giá:
  - Theo destination khác nhau
  - Theo mùa (peak/low)
  - Theo thời gian (valid_from/valid_to)
- Khi tạo từ Service: `service_id` = service đã chọn
- Có thể auto-fill `province_id` từ service → service_provider → province

---

## ✅ VALIDATION RULES

### **Luồng 2: Tạo Service Provider**

1. **Tên nhà dịch vụ:**

   - Required
   - Min length: 3
   - Max length: 200
   - Không được trùng với provider khác cùng tỉnh

2. **Province:**

   - Required (tự động từ context)
   - Phải là province active

3. **Country:**

   - Required (tự động từ context)
   - Phải là country active

4. **Service Type:**
   - Optional
   - Nếu chọn: Phải là service_type active

---

### **Luồng 3: Tạo Destination**

1. **Tên địa điểm:**

   - Required
   - Min length: 3
   - Max length: 200

2. **Province:**

   - Required (tự động từ context)
   - Phải là province active

3. **Country:**
   - Required (tự động từ context)
   - Phải là country active

---

### **Luồng 4: Tạo Service**

1. **Tên dịch vụ:**

   - Required
   - Min length: 3
   - Max length: 200

2. **Service Type:**

   - Required
   - Phải là service_type active

3. **Service Provider:**
   - Required (tự động từ context)
   - Phải là service provider active

---

### **Luồng 4: Tạo Service Price**

1. **Service:**

   - Required (tự động từ context)

2. **Unit Price:**

   - Required
   - > = 0
   - Format: DECIMAL(15,2)

3. **Price Type:**

   - Required
   - Enum: standard/peak/low/custom

4. **Valid Dates:**
   - Nếu có `valid_from`: Phải là date hợp lệ
   - Nếu có `valid_to`: Phải >= `valid_from`

---

## 🔒 BUSINESS RULES

1. **Service Provider Creation:**

   - Một provider **PHẢI** thuộc 1 tỉnh và 1 quốc gia (REQUIRED)
   - Provider có thể có service_type (optional)
   - Provider là đơn vị độc lập, **KHÔNG phụ thuộc Supplier**
   - ❌ **KHÔNG còn link với destination**

2. **Destination Creation:**

   - Destination có thể thuộc 1 tỉnh và 1 quốc gia (optional, nhưng nên set khi tạo từ tab)
   - ❌ **KHÔNG còn phân loại theo category**

3. **Service Creation:**

   - Service phải có service type (REQUIRED)
   - Service nên thuộc 1 service provider (khuyến khích)
   - Service có thể không có provider (dịch vụ chung)

4. **Price Management:**

   - 1 service có thể có nhiều giá:
     - Khác destination
     - Khác price_type (peak/low)
     - Khác thời gian (valid_from/valid_to)
   - Khi query giá: Ưu tiên:
     1. Giá có destination_id match
     2. Giá có province_id match
     3. Giá standard
     4. Giá theo valid_from/valid_to

5. **Context Preservation:**
   - URL luôn giữ `country_id` và `province_id`
   - Khi thêm/sửa, luôn biết context (quốc gia/tỉnh)
   - Breadcrumb hiển thị: Quốc gia > Tỉnh > Provider > Service

---

## ⚠️ TRƯỜNG HỢP ĐẶC BIỆT

1. **Service Provider không link với Destination:**

   - Service Provider chỉ link với Province và Country
   - Nếu cần link với destination cụ thể, có thể dùng trong Service Price (destination_id)

2. **Multiple Prices:**

   - 1 service có thể có nhiều giá
   - Khi thêm giá mới, kiểm tra xem đã có giá với destination/price_type/valid_dates tương tự chưa
   - Có thể cảnh báo nếu trùng

3. **Countries không filter theo type:**
   - Hiển thị tất cả countries active
   - Không còn phân loại domestic/international

---

## 🔗 DEPENDENCIES

### **Phụ thuộc vào:**

- Module System: Users (created_by)
- Countries, Provinces đã được tạo trước
- Service Types đã được setup

### **Ảnh hưởng đến:**

- Tour Module: Services được dùng trong tour_services
- Booking Module: Services được đặt trong booking_services (link với service_provider_id)
- Payment Module: Service prices ảnh hưởng đến giá booking
- Payment Module: Thanh toán cho Service Providers qua `service_provider_payments`

---

## 📝 GHI CHÚ

1. **Navigation:**

   - URL pattern: `?act=admin&module=location-services&country_id=X&province_id=Y`
   - Context luôn được preserve trong URL

2. **UI/UX:**

   - Dùng modals cho create/edit (không reload page)
   - Toast notifications thay vì alert()
   - Context breadcrumb trong mỗi modal
   - Tab navigation khi quản lý tỉnh (Service Providers / Destinations)
   - URL preserve tab state (`&tab=providers` hoặc `&tab=destinations`)

3. **Auto-generation:**
   - `service_code` (Service Provider): **SP-YYYYMMDD-XXX**
   - `service_code` (Service): **SRV-YYYYMMDD-XXX**

---

## 💳 THANH TOÁN CHO SERVICE PROVIDERS

Thay vì thanh toán cho Supplier, hệ thống sẽ thanh toán trực tiếp cho Service Provider:

### **8. Bảng `booking_services`**

| Trường              | Loại          | Bắt buộc | Mô tả                           | Validation        |
| ------------------- | ------------- | -------- | ------------------------------- | ----------------- |
| id                  | INT           | ✅       | Primary key                     | AUTO_INCREMENT    |
| booking_id          | INT           | ✅       | Foreign key → bookings          | NOT NULL, FK      |
| service_id          | INT           | ✅       | Foreign key → services          | NOT NULL, FK      |
| service_provider_id | INT           | ❌       | Foreign key → service_providers | FK, NULL          |
| service_name        | VARCHAR(200)  | ❌       | Tên dịch vụ (snapshot)          |                   |
| quantity            | INT           | ✅       | Số lượng                        | NOT NULL          |
| unit                | VARCHAR(50)   | ❌       | Đơn vị                          |                   |
| unit_price          | DECIMAL(15,2) | ❌       | Đơn giá                         |                   |
| total_price         | DECIMAL(15,2) | ❌       | Tổng giá                        |                   |
| service_date        | DATE          | ❌       | Ngày sử dụng dịch vụ            |                   |
| from_date           | DATE          | ❌       | Từ ngày                         |                   |
| to_date             | DATE          | ❌       | Đến ngày                        |                   |
| payment_status      | ENUM          | ✅       | pending/partial/paid            | DEFAULT 'pending' |
| paid_amount         | DECIMAL(15,2) | ✅       | Số tiền đã thanh toán           | DEFAULT '0.00'    |
| notes               | TEXT          | ❌       | Ghi chú                         |                   |
| created_by          | INT           | ❌       | Foreign key → users             | FK, NULL          |
| created_at          | TIMESTAMP     | ❌       | Ngày tạo                        |                   |

**Mối quan hệ:**

- N Booking Services → 1 Booking (`booking_id`) - REQUIRED
- N Booking Services → 1 Service (`service_id`) - REQUIRED
- N Booking Services → 1 Service Provider (`service_provider_id`) - Optional (lấy từ service)
- N Booking Services → 1 User (`created_by`) - Optional

**Business Logic:**

- `service_provider_id` được lấy từ `services.service_provider_id` khi tạo booking service
- `service_name` lưu snapshot tên dịch vụ tại thời điểm booking (để phòng khi tên service thay đổi)
- `payment_status` và `paid_amount` track thanh toán cho từng booking service

---

### **9. Bảng `service_provider_payments`**

| Trường              | Loại          | Bắt buộc | Mô tả                           | Validation              |
| ------------------- | ------------- | -------- | ------------------------------- | ----------------------- |
| id                  | INT           | ✅       | Primary key                     | AUTO_INCREMENT          |
| payment_code        | VARCHAR(50)   | ❌       | Mã thanh toán                   | UNIQUE                  |
| service_provider_id | INT           | ✅       | Foreign key → service_providers | NOT NULL, FK            |
| booking_id          | INT           | ❌       | Foreign key → bookings          | FK, NULL                |
| amount              | DECIMAL(15,2) | ✅       | Số tiền thanh toán              | NOT NULL                |
| payment_method      | ENUM          | ✅       | cash/bank_transfer/check        | DEFAULT 'bank_transfer' |
| payment_date        | DATE          | ✅       | Ngày thanh toán                 | NOT NULL                |
| invoice_number      | VARCHAR(100)  | ❌       | Số hóa đơn                      |                         |
| receipt_file        | VARCHAR(255)  | ❌       | File hóa đơn/chứng từ           |                         |
| notes               | TEXT          | ❌       | Ghi chú                         |                         |
| status              | ENUM          | ✅       | pending/completed/cancelled     | DEFAULT 'pending'       |
| created_by          | INT           | ❌       | Foreign key → users             | FK, NULL                |
| created_at          | TIMESTAMP     | ❌       | Ngày tạo                        |                         |

**Mối quan hệ:**

- N Service Provider Payments → 1 Service Provider (`service_provider_id`) - REQUIRED
- N Service Provider Payments → 1 Booking (`booking_id`) - Optional
- N Service Provider Payments → 1 User (`created_by`) - Optional
- 1 Service Provider Payment → N Payment Details (`service_provider_payment_details.payment_id`)

**Business Logic:**

- `payment_code` = auto-generate (SPP-YYYYMMDD-XXX)
- Dùng để thanh toán cho Service Provider khi booking services được thực hiện
- Có thể thanh toán cho nhiều booking_services trong 1 payment (qua payment_details)

---

### **10. Bảng `service_provider_payment_details`**

| Trường             | Loại          | Bắt buộc | Mô tả                                   | Validation     |
| ------------------ | ------------- | -------- | --------------------------------------- | -------------- |
| id                 | INT           | ✅       | Primary key                             | AUTO_INCREMENT |
| payment_id         | INT           | ✅       | Foreign key → service_provider_payments | NOT NULL, FK   |
| booking_service_id | INT           | ✅       | Foreign key → booking_services          | NOT NULL, FK   |
| amount             | DECIMAL(15,2) | ✅       | Số tiền thanh toán cho dịch vụ này      | NOT NULL       |
| notes              | TEXT          | ❌       | Ghi chú                                 |                |

**Mối quan hệ:**

- N Payment Details → 1 Service Provider Payment (`payment_id`) - REQUIRED
- N Payment Details → 1 Booking Service (`booking_service_id`) - REQUIRED

**Business Logic:**

- Một payment có thể thanh toán cho nhiều booking_services
- `amount` là số tiền thanh toán cho từng booking_service cụ thể
- Tổng `amount` trong payment_details = `amount` trong service_provider_payments

---

**Status:** ✅ Đã cập nhật hoàn toàn theo database mới - Loại bỏ Categories và Suppliers
