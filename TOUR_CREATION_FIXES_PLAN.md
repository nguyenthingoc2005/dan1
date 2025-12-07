# KẾ HOẠCH SỬA LỖI TẠO TOUR

## PHÂN TÍCH VẤN ĐỀ

### 1. Form không submit được

**Nguyên nhân có thể:**

- Validation JavaScript chặn submit
- Event listener preventDefault
- Thiếu TinyMCE content khi submit
- Validation backend quá strict

**Giải pháp:**

- Kiểm tra console errors
- Đảm bảo TinyMCE content được lưu trước khi submit
- Relax validation nếu cần

### 2. Chọn dịch vụ không đổ ra dịch vụ

**Nguyên nhân:**

- Services không được load đúng format
- JavaScript không parse được data
- Dropdown không được populate

**Giải pháp:**

- Kiểm tra format data từ PHP
- Đảm bảo services array được format đúng
- Debug JavaScript console

### 3. Logic chọn dịch vụ → nhà cung cấp

**Yêu cầu:**

- Chọn dịch vụ → Filter và chỉ hiển thị các nhà cung cấp dịch vụ đó
- Relationship: services.service_provider_id → service_providers.id

**Giải pháp:**

- Khi chọn service, lấy service_provider_id từ service
- Filter service_providers dropdown theo service_provider_id
- Hoặc load providers qua AJAX khi chọn service

### 4. Giá theo mùa/tháng

**Database:**

- `service_prices` table có:
  - `price_type`: 'standard', 'peak', 'low'
  - `start_date`, `end_date`: Khoảng thời gian áp dụng
  - `unit_price`: Giá

**Logic:**

- Khi chọn service và ngày tour, load giá phù hợp
- Ưu tiên: peak > standard > low
- Check date range

### 5. Bao gồm trong giá tour (is_included_in_price)

**Logic:**

- **TÍCH (is_included_in_price = 1)**:

  - Dịch vụ này ĐƯỢC TÍNH vào giá tour
  - Khách KHÔNG phải trả thêm khi sử dụng
  - Được tính vào `estimated_cost_per_person`
  - Hiển thị trong mục "Bao gồm" của tour

- **KHÔNG TÍCH (is_included_in_price = 0)**:
  - Dịch vụ này KHÔNG tính vào giá tour
  - Khách PHẢI TRẢ THÊM khi sử dụng
  - KHÔNG tính vào `estimated_cost_per_person`
  - Hiển thị trong mục "Không bao gồm" của tour

**Ví dụ:**

- Phòng khách sạn: TÍCH → Tính vào giá tour
- Bữa sáng: TÍCH → Tính vào giá tour
- Bữa trưa: KHÔNG TÍCH → Khách tự trả
- Vé tham quan: TÍCH → Tính vào giá tour
- Đồ uống: KHÔNG TÍCH → Khách tự trả

## KẾ HOẠCH THỰC HIỆN

### Bước 1: Tạo TinyMCE Component

- File: `app/views/components/tinymce-editor.php`
- Nhận params: `$id`, `$name`, `$content`, `$height`, `$config`
- Reusable cho tất cả textarea cần TinyMCE

### Bước 2: Sửa getServiceInfo

- Trả về `service_provider_id`
- Load giá từ `service_prices` theo date và price_type
- Trả về danh sách providers cho service đó

### Bước 3: Sửa getServiceProviders

- Thêm filter theo `service_id`
- Khi có service_id, chỉ trả về provider của service đó

### Bước 4: Sửa JavaScript chọn dịch vụ

- Khi chọn service → Load providers qua AJAX
- Filter providers dropdown
- Load giá tự động

### Bước 5: Sửa form submit

- Đảm bảo TinyMCE content được lưu
- Relax validation nếu cần
- Debug console errors

### Bước 6: Cập nhật day-services-editor

- Sử dụng TinyMCE component mới
- Sửa logic load providers
- Sửa logic load giá
