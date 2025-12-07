# PHÂN TÍCH MODULE LOCATION-SERVICES

## URL: `?act=admin&module=location-services`

**Ngày phân tích:** 2024-12-XX

---

## 1. TỔNG QUAN CHỨC NĂNG HIỆN TẠI

### 1.1. Các Entity Được Quản Lý

| Entity                | Chức năng                                | Trạng thái              |
| --------------------- | ---------------------------------------- | ----------------------- |
| **Countries**         | Chỉ xem (read-only)                      | ⚠️ Thiếu CRUD           |
| **Provinces**         | Chỉ xem (read-only)                      | ⚠️ Thiếu CRUD           |
| **Service Providers** | Full CRUD (Create, Read, Update, Delete) | ✅ Đầy đủ               |
| **Services**          | Full CRUD (Create, Read, Update, Delete) | ✅ Đầy đủ               |
| **Service Prices**    | Full CRUD (Create, Read, Update, Delete) | ✅ Đầy đủ               |
| **Destinations**      | Full CRUD (Create, Read, Update, Delete) | ⚠️ Thiếu quản lý ảnh    |
| **Service Types**     | Chỉ đọc (dropdown)                       | ✅ Đủ (có module riêng) |

---

## 2. SO SÁNH VỚI DATABASE SCHEMA

### 2.1. Bảng `countries`

**Database Schema:**

```sql
CREATE TABLE `countries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `name_en` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
)
```

**Chức năng hiện tại:**

- ✅ Đọc danh sách countries (để hiển thị trong tree view)
- ✅ Đếm số provinces của mỗi country
- ❌ **THIẾU:** Tạo country mới
- ❌ **THIẾU:** Sửa country (code, name, name_en, status, display_order)
- ❌ **THIẾU:** Xóa country
- ❌ **THIẾU:** Toggle status (active/inactive)
- ❌ **THIẾU:** Quản lý display_order

**Routes cần thêm:**

- `create-country` / `store-country`
- `edit-country` / `update-country`
- `delete-country`
- `toggle-country-status`

---

### 2.2. Bảng `provinces`

**Database Schema:**

```sql
CREATE TABLE `provinces` (
  `id` int NOT NULL AUTO_INCREMENT,
  `country_id` int NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `name_en` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`)
)
```

**Chức năng hiện tại:**

- ✅ Đọc danh sách provinces theo country_id
- ✅ Đếm số service providers của mỗi province
- ❌ **THIẾU:** Tạo province mới
- ❌ **THIẾU:** Sửa province (name, name_en, status, display_order)
- ❌ **THIẾU:** Xóa province
- ❌ **THIẾU:** Toggle status (active/inactive)
- ❌ **THIẾU:** Quản lý display_order

**Routes cần thêm:**

- `create-province` / `store-province`
- `edit-province` / `update-province`
- `delete-province`
- `toggle-province-status`

---

### 2.3. Bảng `service_providers`

**Database Schema:**

```sql
CREATE TABLE `service_providers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `province_id` int NOT NULL,
  `country_id` int NOT NULL,
  `service_code` varchar(50) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `service_type_id` int DEFAULT NULL,
  `description` text,
  `address` text,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_code` (`service_code`)
)
```

**Chức năng hiện tại:**

- ✅ Tạo service provider (có form riêng)
- ✅ Sửa service provider (có form riêng)
- ✅ Xóa service provider
- ✅ Đọc danh sách service providers
- ✅ Đếm số services của mỗi provider
- ✅ Quản lý các trường: name, service_type_id, description, address, phone, email, website, contact_person, status
- ✅ `service_code` được tự động generate trong model nếu không có (không cần nhập thủ công)
- ✅ `created_by` được tự động set (từ model)

**Đánh giá:** ✅ **ĐẦY ĐỦ**

---

### 2.4. Bảng `services`

**Database Schema:**

```sql
CREATE TABLE `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_provider_id` int NOT NULL,
  `service_type_id` int DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `description` text,
  `unit` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)
```

**Chức năng hiện tại:**

- ✅ Tạo service (có form riêng)
- ✅ Sửa service (có form riêng)
- ✅ Xóa service
- ✅ Đọc danh sách services
- ✅ Quản lý các trường: name, service_type_id, description, unit, status
- ✅ `created_by` được tự động set (từ model)

**Đánh giá:** ✅ **ĐẦY ĐỦ**

---

### 2.5. Bảng `service_prices`

**Database Schema:**

```sql
CREATE TABLE `service_prices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `price_type` enum('standard','peak','low') DEFAULT 'standard',
  `unit_price` decimal(15,2) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `notes` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)
```

**Lưu ý:** Đã bỏ 2 cột `min_quantity` và `max_quantity` khỏi database (không sử dụng)

**Chức năng hiện tại:**

- ✅ Tạo price (AJAX modal)
- ✅ Sửa price (AJAX modal)
- ✅ Xóa price
- ✅ Đọc danh sách prices
- ✅ Quản lý các trường: unit_price, price_type, start_date, end_date, notes, status
- ✅ `created_by` được tự động set (từ model)

**Đánh giá:** ✅ **ĐẦY ĐỦ**

---

### 2.6. Bảng `destinations`

**Database Schema:**

```sql
CREATE TABLE `destinations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `province_id` int DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `description` text,
  `locations` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)
```

**Chức năng hiện tại:**

- ✅ Tạo destination (có form riêng)
- ✅ Sửa destination (có form riêng)
- ✅ Xóa destination
- ✅ Đọc danh sách destinations
- ✅ Quản lý các trường: name, description, locations, status
- ✅ `created_by` được tự động set (từ model)
- ⚠️ **THIẾU:** Quản lý `destination_images` (bảng riêng)

**Đánh giá:** ⚠️ **THIẾU QUẢN LÝ ẢNH**

---

### 2.7. Bảng `destination_images`

**Database Schema:**

```sql
CREATE TABLE `destination_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `destination_id` int NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT '0',
  `display_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE
)
```

**Chức năng hiện tại:**

- ❌ **THIẾU HOÀN TOÀN:** Không có chức năng quản lý ảnh cho destinations
- ❌ **THIẾU:** Upload ảnh
- ❌ **THIẾU:** Xóa ảnh
- ❌ **THIẾU:** Set primary image
- ❌ **THIẾU:** Quản lý caption
- ❌ **THIẾU:** Quản lý display_order

**Lưu ý:**

- Model `Destination` đã có methods: `getImages()`, `addImage()`, `deleteImage()`, `setPrimaryImage()`
- Controller `DestinationController` (module riêng) đã có xử lý upload ảnh
- Nhưng trong `LocationServiceController` chưa có chức năng này

**Routes cần thêm:**

- `upload-destination-image`
- `delete-destination-image`
- `set-primary-destination-image`
- `update-destination-image-caption`
- `reorder-destination-images`

---

## 3. TỔNG KẾT CHỨC NĂNG THIẾU

### 3.1. CRUD cho Countries

- [ ] Tạo country mới
- [ ] Sửa country (code, name, name_en, status, display_order)
- [ ] Xóa country
- [ ] Toggle status country

### 3.2. CRUD cho Provinces

- [ ] Tạo province mới
- [ ] Sửa province (name, name_en, status, display_order)
- [ ] Xóa province
- [ ] Toggle status province

### 3.3. Quản lý Destination Images

- [ ] Upload ảnh cho destination
- [ ] Xóa ảnh destination
- [ ] Set primary image
- [ ] Quản lý caption
- [ ] Quản lý display_order
- [ ] Hiển thị gallery ảnh trong form edit destination

---

## 4. ĐỀ XUẤT IMPLEMENTATION

### 4.1. Ưu tiên cao

1. **CRUD cho Countries** - Cần thiết để quản lý đầy đủ hệ thống
2. **CRUD cho Provinces** - Cần thiết để quản lý đầy đủ hệ thống
3. **Quản lý Destination Images** - Tính năng quan trọng cho du lịch

---

## 5. GHI CHÚ

- Module `location-services` là module quản lý thống nhất, tập trung vào việc quản lý địa điểm và dịch vụ theo cấu trúc: Countries → Provinces → Service Providers/Destinations → Services → Prices
- Hiện tại module đã có đầy đủ chức năng cho Service Providers, Services, và Service Prices
- Cần bổ sung CRUD cho Countries và Provinces để hoàn thiện module
- Cần bổ sung quản lý ảnh cho Destinations để tăng tính năng

---

**Kết luận:** Module `location-services` đã có khoảng **75%** chức năng so với database schema. Cần bổ sung:

1. **CRUD cho Countries** - Quản lý quốc gia (code, name, name_en, status, display_order)
2. **CRUD cho Provinces** - Quản lý tỉnh thành (name, name_en, status, display_order)
3. **Quản lý ảnh cho Destinations** - Upload, xóa, set primary, quản lý caption và display_order

Để đạt **100%**.
