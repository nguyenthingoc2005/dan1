# KẾ HOẠCH IMPLEMENT SESSION CHO TOUR CREATION

## PHÂN TÍCH YÊU CẦU

### Yêu cầu của người dùng:

1. **Mô tả lịch trình** chỉ là 1 cột trong bảng `itineraries` (field `description`)
2. **Dịch vụ theo ngày** chỉ là dữ liệu đi kèm, không phải form riêng
3. **Chỉ có 1 form cha là tour** - tất cả dữ liệu lưu khi submit cuối cùng
4. **Session để lưu** toàn bộ thông tin tour trong quá trình tạo
5. **Khi load lại trang** → Lấy dữ liệu từ session đổ ra
6. **Xóa session** khi tạo xong hoặc nếu lỗi thì giữ lại để đổ ra

## PHÂN TÍCH LOGIC HIỆN TẠI

### ✅ Đã có:

1. Session implementation (`initTourSession`, `loadTourSession`, `saveFormSession`)
2. JavaScript function `saveFormDataToSession()` để lưu vào session
3. Function `saveDayServiceToSession()` để lưu dịch vụ theo ngày
4. Function `restoreDayServicesFromSession()` để restore dữ liệu

### ❌ Vấn đề:

1. **Mô tả lịch trình**: Hiện tại lưu vào `itineraries.description` - ĐÚNG
2. **Dịch vụ theo ngày**: Hiện tại lưu vào `itinerary_day_services` - ĐÚNG
3. **Session**: Đã có nhưng có thể không được gọi đúng cách
4. **Form submit**: Có thể không lưu TinyMCE content vào session trước khi submit

## SO SÁNH CODE HIỆN TẠI VỚI YÊU CẦU

### Database Schema:

```sql
-- Bảng itineraries
CREATE TABLE itineraries (
    id INT,
    tour_id INT,
    day_number INT,
    title VARCHAR(200),
    description TEXT,  -- ✅ Mô tả lịch trình (TinyMCE content)
    destination_id INT,
    ...
)

-- Bảng itinerary_day_services
CREATE TABLE itinerary_day_services (
    id INT,
    itinerary_id INT,  -- ✅ Link với itinerary (ngày)
    service_id INT,
    service_provider_id INT,
    unit_price DECIMAL,
    quantity DECIMAL,
    is_included_in_price TINYINT,
    ...
)
```

### Logic hiện tại:

- ✅ Itinerary description lưu vào `itineraries.description` - ĐÚNG
- ✅ Day services lưu vào `itinerary_day_services` - ĐÚNG
- ✅ Chỉ có 1 form tour - ĐÚNG
- ⚠️ Session có thể không được lưu đầy đủ khi user nhập

## KẾ HOẠCH THỰC HIỆN

### Bước 1: Đảm bảo Session được lưu đầy đủ

- ✅ Khi user thay đổi bất kỳ field nào → Auto-save vào session
- ✅ Khi user thay đổi step → Save vào session
- ✅ Khi user thêm/xóa dịch vụ → Save vào session
- ✅ Khi TinyMCE content thay đổi → Save vào session

### Bước 2: Đảm bảo Session được restore đúng

- ✅ Khi load trang → Load từ session và đổ vào form
- ✅ Restore TinyMCE content từ session
- ✅ Restore day services từ session
- ✅ Restore tất cả các field khác

### Bước 3: Sửa logic lưu vào database

- ✅ Khi submit form → Lấy từ session (đã có TinyMCE content)
- ✅ Merge session + POST (POST priority cao hơn)
- ✅ Lưu vào database
- ✅ Xóa session sau khi lưu thành công

### Bước 4: Xử lý lỗi

- ✅ Nếu có lỗi validation → Giữ session, hiển thị lỗi + dữ liệu cũ
- ✅ Nếu có lỗi database → Giữ session, hiển thị lỗi + dữ liệu cũ

## CẤU TRÚC SESSION

```php
$_SESSION['tour_form_data'] = [
    // Basic info
    'name' => '',
    'introduction' => '',
    'description' => '',
    'duration_days' => 0,
    'duration_nights' => 0,
    'departure_location' => '',
    'min_participants' => 15,
    'max_participants' => 45,
    'adult_price' => 0,
    'child_price' => 0,
    'infant_price' => 0,
    'deposit_percentage' => 30,
    'booking_deadline_days' => 1,
    'status' => 'draft',
    'tour_type' => 'public',

    // Itinerary (theo ngày)
    'itinerary' => [
        [
            'day_number' => 1,
            'title' => 'Ngày 1: ...',
            'description' => '<p>TinyMCE content...</p>',  // ✅ Lưu HTML từ TinyMCE
            'destination_id' => 1
        ],
        ...
    ],

    // Day Services (theo ngày)
    'itinerary_day_services' => [
        [
            'day_number' => 1,
            'service_id' => 1,
            'service_provider_id' => 1,
            'service_name' => 'Phòng Deluxe',
            'unit_price' => 1500000,
            'quantity' => 1,
            'unit' => 'phòng/đêm',
            'is_included_in_price' => 1,
            'notes' => ''
        ],
        ...
    ],

    // Highlights, Included, Excluded
    'highlights' => [],
    'included' => [],
    'excluded' => [],
    'policy_ids' => [],

    // Metadata
    'created_at' => '2024-12-06 10:00:00',
    'last_updated' => '2024-12-06 10:30:00'
];
```

## CÁC THAY ĐỔI CẦN THỰC HIỆN

### 1. Controller: `app/controllers/admin/TourController.php`

- ✅ Đã có `initTourSession()` - OK
- ✅ Đã có `loadTourSession()` - OK
- ✅ Đã có `saveFormSession()` - OK
- ✅ Đã có `clearTourSessionInternal()` - OK
- ⚠️ Cần đảm bảo `store()` merge session + POST đúng

### 2. View: `app/views/admin/tours/create.php`

- ✅ Đã có `saveFormDataToSession()` - OK
- ✅ Đã có `saveDayServiceToSession()` - OK
- ⚠️ Cần thêm auto-save khi user thay đổi
- ⚠️ Cần đảm bảo restore từ session khi load
- ⚠️ Cần đảm bảo TinyMCE content được lưu vào session

### 3. JavaScript Events cần thêm:

- `onchange` cho tất cả input fields → Auto-save
- `onblur` cho TinyMCE → Auto-save
- `beforeunload` → Save trước khi rời trang
- Khi thêm/xóa dịch vụ → Save ngay

## IMPLEMENTATION CHECKLIST

- [ ] Thêm auto-save cho tất cả input fields
- [ ] Thêm auto-save cho TinyMCE (on change)
- [ ] Đảm bảo restore itinerary từ session
- [ ] Đảm bảo restore day services từ session
- [ ] Đảm bảo restore TinyMCE content từ session
- [ ] Test: Nhập dữ liệu → Reload trang → Kiểm tra dữ liệu còn
- [ ] Test: Submit form → Kiểm tra session được xóa
- [ ] Test: Validation error → Kiểm tra session được giữ lại
