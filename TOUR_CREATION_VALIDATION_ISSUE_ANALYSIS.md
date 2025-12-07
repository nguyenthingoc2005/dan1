# PHÂN TÍCH LỖI VALIDATION KHI TẠO TOUR CUSTOM

## VẤN ĐỀ

Khi tạo tour custom từ template, bị lỗi:
```
Dịch vụ ngày 1: Vui lòng chọn dịch vụ
```

Mặc dù các dịch vụ đã được hiển thị trên form.

## PHÂN TÍCH TỪNG BƯỚC

### Bước 1: Load template data
- File: `app/controllers/admin/TourController.php::createFromTemplate()`
- Template data được load vào `$old_input`:
  ```php
  'itinerary_day_services' => $template['itinerary_day_services'] ?? []
  ```
- Format: Array of objects `[{day_number: 1, service_id: 123, ...}, ...]`

### Bước 2: Form hiển thị
- File: `app/views/admin/tours/create.php`
- Dịch vụ được restore từ `$old_input['itinerary_day_services']`
- JavaScript restore và hiển thị dịch vụ trên form

### Bước 3: Submit form
- Form submit với các hidden fields: `day_service_day_number[]`, `day_service_service_id[]`, etc.
- **VẤN ĐỀ**: Nếu form không submit đúng format hoặc dịch vụ chỉ có trong `$old_input` (chưa vào session), POST sẽ không có data

### Bước 4: Validation
- File: `app/controllers/admin/TourController.php::validateTourData()`
- Validation chỉ check:
  ```php
  if (!empty($post['day_service_day_number'])) {
      // Validate từ POST
  }
  ```
- **VẤN ĐỀ**: Không check từ session data hoặc template data

### Bước 5: Prepare data
- Sau validation, data được prepare từ session hoặc POST
- Nhưng validation đã fail ở bước trước!

## NGUYÊN NHÂN

1. **Validation chỉ check POST format**, không check session/template data
2. **Data từ template** có thể chưa được lưu vào session đúng cách
3. **Form submit** có thể không include đầy đủ các hidden fields

## GIẢI PHÁP

### Option 1: Sửa validation để check cả session data
- Validation cần check cả từ session nếu POST không có
- Sử dụng `normalizeDayServicesFormat()` để chuẩn hóa format

### Option 2: Đảm bảo form submit đúng format
- Kiểm tra JavaScript restore dịch vụ có tạo đúng hidden fields không
- Đảm bảo tất cả hidden fields được submit

### Option 3: Lưu template data vào session ngay từ đầu
- Khi load template, lưu data vào session ngay
- Validation và prepare đều dùng session data

## KHUYẾN NGHỊ

**Option 1 + Option 3** là tốt nhất:
1. Lưu template data vào session khi load template
2. Validation check cả từ session nếu POST không có
3. Đảm bảo form submit đúng format

