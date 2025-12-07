# FIX VALIDATION DAY SERVICES - ĐÃ ÁP DỤNG

## 🔧 CÁC THAY ĐỔI ĐÃ THỰC HIỆN

### 1. Sửa Logic Validation (Line 685-717)

**File:** `app/controllers/staff/TourController.php`

**Thay đổi:**
- **Trước:** Dùng `empty()` để check POST, không phân biệt POST không có field vs POST có field nhưng rỗng
- **Sau:** Dùng `isset()` để phân biệt:
  - POST có field và không rỗng → Ưu tiên POST (user đã chỉnh sửa)
  - POST không có field → Check session (user chưa chỉnh sửa, lấy từ template)
  - POST có field nhưng rỗng `[]` → Không check session (user đã xóa hết)

**Code mới:**
```php
// Lấy từ POST nếu có (ưu tiên user input)
if (isset($post['day_service_day_number']) && is_array($post['day_service_day_number']) && !empty($post['day_service_day_number'])) {
    // Parse POST data
}
// Nếu POST không có field (user chưa chỉnh sửa), lấy từ session
elseif (!isset($post['day_service_day_number']) && !empty($session_data['itinerary_day_services'])) {
    // Parse session data
}
```

### 2. Cải thiện Normalize Function (Line 850-910)

**File:** `app/controllers/staff/TourController.php`

**Thay đổi:**
- **Trước:** Logic normalize không handle tốt format từ database (indexed array)
- **Sau:** 
  - Handle đúng format từ database: `[{service_id: 1, day_number: 1, ...}, ...]`
  - Handle format associative: `{1: [...], 2: [...]}`
  - Đảm bảo có field `day_number` trong mỗi service

**Logic mới:**
1. Check nếu đã là indexed array từ database → Giữ nguyên, chỉ đảm bảo có `day_number`
2. Check nếu là associative array → Flatten thành indexed array
3. Return normalized array

---

## ✅ CÁC SCENARIO ĐƯỢC XỬ LÝ

### Scenario 1: Tạo Tour Mới (Không từ Template)
- User nhập dịch vụ → POST có data → ✅ Validate POST
- User không nhập dịch vụ → POST không có → ✅ Không validate (OK)

### Scenario 2: Tạo Tour Từ Template (User không chỉnh sửa)
- Template có dịch vụ → Lưu vào session
- User không chỉnh sửa → POST không có field
- ✅ Validation check session → Parse và validate → **FIXED**

### Scenario 3: Tạo Tour Từ Template (User chỉnh sửa)
- Template có dịch vụ → Lưu vào session
- User chỉnh sửa → POST có data
- ✅ Validation check POST → Validate POST → **OK**

### Scenario 4: Tạo Tour Từ Template (User xóa hết dịch vụ)
- Template có dịch vụ → Lưu vào session
- User xóa hết → POST có field nhưng là array rỗng `[]`
- ✅ Validation không check session (ưu tiên POST) → **OK**

### Scenario 5: Edit Tour
- Load tour từ database → POST có data
- ✅ Validation check POST → **OK**

---

## 🧪 TEST CHECKLIST

Sau khi deploy, cần test các scenario sau:

- [ ] Tạo tour mới (không từ template) - Có dịch vụ
- [ ] Tạo tour mới (không từ template) - Không có dịch vụ
- [ ] Tạo tour từ template - Không chỉnh sửa (QUAN TRỌNG)
- [ ] Tạo tour từ template - Chỉnh sửa dịch vụ
- [ ] Tạo tour từ template - Thêm dịch vụ mới
- [ ] Tạo tour từ template - Xóa dịch vụ
- [ ] Tạo tour từ template - Xóa hết dịch vụ
- [ ] Edit tour - Có dịch vụ
- [ ] Edit tour - Không có dịch vụ

---

## 📝 LƯU Ý

1. **Không ảnh hưởng đến các luồng khác:**
   - Tạo tour mới vẫn hoạt động bình thường
   - Edit tour vẫn hoạt động bình thường

2. **Chỉ ảnh hưởng đến:**
   - Tạo tour từ template (đã được fix)

3. **Cần test kỹ:**
   - Đặc biệt là scenario "Tạo tour từ template - Không chỉnh sửa"

---

## 🔍 DEBUG (Nếu cần)

Nếu vẫn còn lỗi, thêm debug logs:

```php
// Trong validateTourData(), sau line 704
error_log("POST day_service_day_number: " . json_encode($post['day_service_day_number'] ?? 'NOT SET'));
error_log("Session itinerary_day_services: " . json_encode($session_data['itinerary_day_services'] ?? 'NOT SET'));
error_log("Day services to validate: " . json_encode($day_services_to_validate));
```

---

## 📅 NGÀY ÁP DỤNG

- **Date:** 2024-12-XX
- **File changed:** `app/controllers/staff/TourController.php`
- **Lines changed:** 685-717, 850-910

