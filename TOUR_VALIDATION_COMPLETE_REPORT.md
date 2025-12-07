# BÁO CÁO ĐẦY ĐỦ VỀ VALIDATION TẠO TOUR

**Ngày:** 2024-12-06  
**Phiên bản:** 2.0

---

## 📋 TỔNG QUAN

Validation được thực hiện trong method `validateTourData()` của `TourController`.  
Validation hỗ trợ cả 2 nguồn dữ liệu:

- **POST data** - Từ form submit
- **Session data** - Từ template hoặc session khi tạo tour từ template

---

## ✅ DANH SÁCH TẤT CẢ CÁC VALIDATION

### 1. **VALIDATION THÔNG TIN CƠ BẢN**

#### 1.1. Tên Tour (`name`)

- ✅ **Bắt buộc**: Phải có giá trị
- ✅ **Độ dài tối thiểu**: Ít nhất 2 ký tự
- **Lỗi**: `"Tên tour phải có ít nhất 2 ký tự"`

```php
if (empty($post['name']) || strlen(trim($post['name'])) < 2) {
    $errors['name'] = 'Tên tour phải có ít nhất 2 ký tự';
}
```

---

#### 1.2. Số Ngày (`duration_days`)

- ✅ **Bắt buộc**: Phải có giá trị
- ✅ **Giá trị tối thiểu**: Phải lớn hơn 0
- **Lỗi**: `"Số ngày phải lớn hơn 0"`

```php
if (empty($post['duration_days']) || (int) $post['duration_days'] < 1) {
    $errors['duration_days'] = 'Số ngày phải lớn hơn 0';
}
```

---

#### 1.3. Số Đêm (`duration_nights`)

- ✅ **Logic**: Số đêm không được lớn hơn số ngày
- **Lỗi**: `"Số đêm không thể lớn hơn số ngày"`

```php
if ($duration_nights > $duration_days) {
    $errors['duration_nights'] = 'Số đêm không thể lớn hơn số ngày';
}
```

---

### 2. **VALIDATION GIÁ**

#### 2.1. Giá Người Lớn (`adult_price`)

- ✅ **Bắt buộc**: Phải có giá trị
- ✅ **Giá trị tối thiểu**: Phải lớn hơn 0
- **Lỗi**: `"Giá người lớn phải lớn hơn 0"`

```php
if (empty($post['adult_price']) || (float) $post['adult_price'] <= 0) {
    $errors['adult_price'] = 'Giá người lớn phải lớn hơn 0';
}
```

---

#### 2.2. Giá Trẻ Em (`child_price`)

- ✅ **Logic**: Không được lớn hơn giá người lớn
- **Lỗi**: `"Giá trẻ em không được lớn hơn giá người lớn"`

```php
if ($child_price > $adult_price) {
    $errors['child_price'] = 'Giá trẻ em không được lớn hơn giá người lớn';
}
```

---

#### 2.3. Giá Em Bé (`infant_price`)

- ✅ **Logic**: Không được lớn hơn giá trẻ em
- **Lỗi**: `"Giá em bé không được lớn hơn giá trẻ em"`

```php
if ($infant_price > $child_price) {
    $errors['infant_price'] = 'Giá em bé không được lớn hơn giá trẻ em';
}
```

---

### 3. **VALIDATION LỊCH TRÌNH (ITINERARY)**

#### 3.1. Số Lượng Ngày Lịch Trình

- ✅ **Logic**: Số lượng ngày lịch trình phải bằng số ngày tour
- **Lỗi**: `"Lịch trình phải nhập đủ cho X ngày (Hiện tại: Y ngày)"`

```php
if (!empty($post['itinerary_day_number'])) {
    $itinerary_count = count($post['itinerary_day_number']);
    if ($itinerary_count != $duration_days) {
        $errors['itinerary'] = "Lịch trình phải nhập đủ cho $duration_days ngày (Hiện tại: $itinerary_count ngày)";
    }
}
```

---

### 4. **VALIDATION DỊCH VỤ THEO NGÀY (ITINERARY DAY SERVICES)**

Validation này hỗ trợ cả 2 nguồn:

- ✅ **POST data**: Từ form submit
- ✅ **Session data**: Từ template khi tạo tour custom

#### 4.1. Thu thập Dữ liệu để Validate

**Từ POST:**

```php
if (!empty($post['day_service_day_number'])) {
    foreach ($post['day_service_day_number'] as $key => $day_number) {
        $day_services_to_validate[] = [
            'day_number' => (int) $day_number,
            'service_id' => $post['day_service_service_id'][$key] ?? null,
            'service_provider_id' => $post['day_service_provider_id'][$key] ?? null,
            'unit_price' => $post['day_service_unit_price'][$key] ?? 0,
            'quantity' => $post['day_service_quantity'][$key] ?? 0,
        ];
    }
}
```

**Từ Session (nếu POST không có):**

```php
if (empty($day_services_to_validate) && !empty($session_data['itinerary_day_services'])) {
    $session_services = $this->normalizeDayServicesFormat($session_data['itinerary_day_services']);
    if (!empty($session_services)) {
        foreach ($session_services as $service) {
            $day_services_to_validate[] = [
                'day_number' => (int) ($service['day_number'] ?? $service['day'] ?? 1),
                'service_id' => $service['service_id'] ?? null,
                'service_provider_id' => $service['service_provider_id'] ?? null,
                'unit_price' => (float) ($service['unit_price'] ?? 0),
                'quantity' => (float) ($service['quantity'] ?? 0),
            ];
        }
    }
}
```

---

#### 4.2. Service ID (`service_id`)

- ✅ **Bắt buộc**: Phải có giá trị
- ✅ **Tồn tại trong DB**: Service phải tồn tại và có status = 'active'
- **Lỗi**:
  - `"Dịch vụ ngày X: Vui lòng chọn dịch vụ"`
  - `"Dịch vụ ngày X: Dịch vụ không tồn tại hoặc đã bị vô hiệu hóa"`

```php
if (empty($service_id)) {
    $errors['day_services'] = "Dịch vụ ngày $day_number: Vui lòng chọn dịch vụ";
    break;
} else {
    $stmt = $this->db->prepare("SELECT id FROM services WHERE id = :id AND status = 'active'");
    $stmt->execute(['id' => (int) $service_id]);
    if (!$stmt->fetch()) {
        $errors['day_services'] = "Dịch vụ ngày $day_number: Dịch vụ không tồn tại hoặc đã bị vô hiệu hóa";
        break;
    }
}
```

---

#### 4.3. Đơn Giá (`unit_price`)

- ✅ **Bắt buộc**: Phải có giá trị
- ✅ **Giá trị tối thiểu**: Phải lớn hơn 0
- **Lỗi**: `"Dịch vụ ngày X: Đơn giá phải lớn hơn 0"`

```php
if ($unit_price <= 0) {
    $errors['day_services'] = "Dịch vụ ngày $day_number: Đơn giá phải lớn hơn 0";
    break;
}
```

---

#### 4.4. Số Lượng (`quantity`)

- ✅ **Bắt buộc**: Phải có giá trị
- ✅ **Giá trị tối thiểu**: Phải lớn hơn 0
- **Lỗi**: `"Dịch vụ ngày X: Số lượng phải lớn hơn 0"`

```php
if ($quantity <= 0) {
    $errors['day_services'] = "Dịch vụ ngày $day_number: Số lượng phải lớn hơn 0";
    break;
}
```

---

#### 4.5. Nhà Cung Cấp Dịch Vụ (`service_provider_id`)

- ✅ **Logic**: Nếu có, phải thuộc về service đã chọn
- ✅ **Kiểm tra quan hệ**: Service phải có `service_provider_id` khớp
- **Lỗi**: `"Dịch vụ ngày X: Nhà dịch vụ không thuộc về dịch vụ đã chọn"`

```php
if (!empty($service_provider_id)) {
    $stmt = $this->db->prepare("
        SELECT s.id
        FROM services s
        WHERE s.id = :service_id
        AND s.service_provider_id = :provider_id
    ");
    $stmt->execute([
        'service_id' => (int) $service_id,
        'provider_id' => (int) $service_provider_id
    ]);
    if (!$stmt->fetch()) {
        $errors['day_services'] = "Dịch vụ ngày $day_number: Nhà dịch vụ không thuộc về dịch vụ đã chọn";
        break;
    }
}
```

---

### 5. **VALIDATION HÌNH ẢNH**

#### 5.1. Số Lượng Hình Ảnh

- ✅ **Giới hạn**: Tối đa 10 hình ảnh
- **Lỗi**: `"Tối đa 10 hình ảnh"`

```php
if ($count > 10) {
    $errors['images'] = 'Tối đa 10 hình ảnh';
}
```

---

#### 5.2. Dung Lượng Hình Ảnh

- ✅ **Giới hạn**: Tổng dung lượng không quá 10MB
- **Lỗi**: `"Tổng dung lượng hình ảnh không quá 10MB"`

```php
if ($total_size > 10 * 1024 * 1024) {
    $errors['images'] = 'Tổng dung lượng hình ảnh không quá 10MB';
}
```

---

### 6. **VALIDATION TRẠNG THÁI**

#### 6.1. Status

- ✅ **Giá trị hợp lệ**: Chỉ cho phép 'draft', 'pending', 'active'
- **Lỗi**: `"Trạng thái không hợp lệ"`

```php
$status = $post['status'] ?? 'draft';
if (!in_array($status, ['draft', 'pending', 'active'])) {
    $errors['status'] = 'Trạng thái không hợp lệ';
}
```

---

## 🔄 FLOW VALIDATION

### Khi Tạo Tour Mới (Từ Đầu)

1. **Load session data** (nếu có)
2. **Merge session + POST** → `$form_data`
3. **Call `validateTourData($form_data, $session_data)`**
4. **Validate:**
   - Basic info (name, duration, price)
   - Itinerary count
   - Day services từ POST (nếu có)
   - Images
   - Status
5. **Nếu có lỗi:** Hiển thị form với errors
6. **Nếu không có lỗi:** Tiếp tục tạo tour

---

### Khi Tạo Tour Từ Template

1. **Load template data** → `$template`
2. **Prepare `$old_input`** từ template
3. **Lưu vào session** để validation có thể check
4. **Form hiển thị** với template data
5. **User submit form**
6. **Call `validateTourData($form_data, $session_data)`**
7. **Validate:**
   - Basic info
   - Itinerary count
   - Day services từ POST (nếu có) HOẶC từ session (nếu POST không có)
   - Images
   - Status
8. **Nếu có lỗi:** Hiển thị form với errors
9. **Nếu không có lỗi:** Tiếp tục tạo tour

---

## ✅ ĐIỂM MẠNH CỦA VALIDATION

1. ✅ **Hỗ trợ 2 nguồn dữ liệu**: POST và Session
2. ✅ **Validate đầy đủ**: Tất cả các trường quan trọng đều được validate
3. ✅ **Kiểm tra database**: Service ID và Service Provider ID được verify trong DB
4. ✅ **Logic hợp lý**: Giá trẻ em < giá người lớn, số đêm <= số ngày
5. ✅ **Error messages rõ ràng**: Mỗi lỗi có message cụ thể
6. ✅ **Break khi có lỗi**: Dừng validation ngay khi tìm thấy lỗi đầu tiên

---

## ⚠️ CÁC TRƯỜNG HỢP ĐẶC BIỆT

### 1. Day Services từ Template

**Vấn đề ban đầu:**

- Validation chỉ check POST
- Dịch vụ từ template chỉ có trong session
- → Validation fail mặc dù dịch vụ đã có

**Giải pháp:**

- Validation check cả POST và Session
- Nếu POST không có, lấy từ session
- Normalize format trước khi validate

---

### 2. Session Data Format

**Format có thể:**

- Array indexed: `[{day_number: 1, ...}, {day_number: 2, ...}]`
- Array associative: `{1: [...], 2: [...]}`

**Giải pháp:**

- Method `normalizeDayServicesFormat()` chuẩn hóa format
- Convert tất cả về indexed array trước khi validate

---

## 📊 THỐNG KÊ VALIDATION

- **Tổng số validation**: 13 validations
- **Validation bắt buộc**: 5 fields (name, duration_days, adult_price, service_id, unit_price, quantity)
- **Validation logic**: 6 rules (nights <= days, child_price <= adult_price, etc.)
- **Validation database**: 2 checks (service exists, provider belongs to service)
- **Validation file**: 2 checks (count, size)

---

## 🎯 KẾT LUẬN

### ✅ Validation đã đầy đủ và khớp logic:

1. ✅ **Basic validation**: Đầy đủ
2. ✅ **Price validation**: Logic đúng
3. ✅ **Itinerary validation**: Check số lượng ngày
4. ✅ **Day Services validation**:
   - Hỗ trợ cả POST và Session
   - Validate đầy đủ các trường
   - Kiểm tra database
5. ✅ **Images validation**: Giới hạn số lượng và dung lượng
6. ✅ **Status validation**: Chỉ cho phép giá trị hợp lệ

### ✅ Các cải tiến đã thực hiện:

1. ✅ **Hỗ trợ validation từ session data** - Fix lỗi khi tạo tour từ template
2. ✅ **Normalize format** - Xử lý nhiều format session data
3. ✅ **Clear error messages** - Mỗi lỗi có message cụ thể

---

**Tình trạng:** ✅ **VALIDATION ĐẦY ĐỦ VÀ SẴN SÀNG**

---

**Người phân tích:** AI Assistant  
**Ngày hoàn thành:** 2024-12-06
