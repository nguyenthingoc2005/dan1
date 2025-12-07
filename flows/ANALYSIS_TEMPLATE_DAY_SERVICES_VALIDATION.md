# PHÂN TÍCH LỖI VALIDATION DỊCH VỤ KHI TẠO TOUR TỪ TEMPLATE

## 🔴 VẤN ĐỀ

**Lỗi:** "Dịch vụ ngày 1: Vui lòng chọn dịch vụ" khi tạo tour từ template, mặc dù template đã có dịch vụ.

**Ngữ cảnh:**
- User chọn template có sẵn dịch vụ
- User không chỉnh sửa dịch vụ (giữ nguyên từ template)
- Khi submit form, validation báo lỗi thiếu dịch vụ

---

## 📋 KẾ HOẠCH PHÂN TÍCH

### BƯỚC 1: Phân tích luồng dữ liệu khi tạo từ template

#### 1.1. Load template data
- **File:** `app/controllers/staff/TourController.php::createFromTemplate()`
- **Flow:**
  1. Gọi `$this->tourModel->getForClone($template_id)`
  2. `getForClone()` gọi `findById()` 
  3. `findById()` gọi `getItineraryDayServices($id)`
  4. `getItineraryDayServices()` trả về array từ database

- **Cần kiểm tra:**
  - Format dữ liệu trả về từ `getItineraryDayServices()`
  - Các field có trong mỗi service object
  - Có field `service_id` không? Có giá trị không?

#### 1.2. Lưu vào session
- **File:** `app/controllers/staff/TourController.php::createFromTemplate()` (line 186-188)
- **Code:**
  ```php
  $old_input['itinerary_day_services'] = $template['itinerary_day_services'] ?? [];
  $_SESSION['tour_form_data'] = array_merge($_SESSION['tour_form_data'] ?? [], $old_input);
  ```
- **Cần kiểm tra:**
  - Dữ liệu có được lưu đúng vào session không?
  - Format của `itinerary_day_services` trong session là gì?

#### 1.3. Hiển thị trên form
- **File:** `app/views/staff/tours/create.php`
- **Cần kiểm tra:**
  - Form có load và hiển thị day services từ template không?
  - Có tạo hidden inputs cho day services không?

---

### BƯỚC 2: Phân tích luồng submit form

#### 2.1. Submit form
- **File:** `app/views/staff/tours/create.php` (JavaScript)
- **Cần kiểm tra:**
  - Form có serialize day services từ template không?
  - Có tạo hidden inputs `day_service_day_number[]`, `day_service_service_id[]` không?
  - Nếu user không chỉnh sửa, form có submit day services không?

#### 2.2. Xử lý trong controller
- **File:** `app/controllers/staff/TourController.php::store()`
- **Flow:**
  1. Load session data: `$session_data = $this->loadTourSession()`
  2. Merge với POST: `$form_data = array_merge($session_data, $_POST)`
  3. Gọi validation: `$errors = $this->validateTourData($form_data, $session_data)`

- **Cần kiểm tra:**
  - POST có chứa day services không?
  - Session data có chứa day services không?
  - Merge có đúng không?

---

### BƯỚC 3: Phân tích validation logic

#### 3.1. Validation day services
- **File:** `app/controllers/staff/TourController.php::validateTourData()` (line 685-771)
- **Logic hiện tại:**
  1. Check POST trước: `if (!empty($post['day_service_day_number']))`
  2. Nếu không có trong POST, check session: `if (empty($day_services_to_validate) && !empty($session_data['itinerary_day_services']))`
  3. Normalize format: `normalizeDayServicesFormat()`
  4. Validate từng service: check `service_id`, `unit_price`, `quantity`

- **Vấn đề có thể xảy ra:**

  **Vấn đề 1: POST có field nhưng rỗng**
  - Nếu form submit `day_service_day_number[]` nhưng là array rỗng `[]`
  - `empty([])` = `true` → không vào block check POST
  - Nhưng có thể có logic khác check `isset()` thay vì `empty()`

  **Vấn đề 2: Format không đúng**
  - Dữ liệu từ template có format: `[{service_id: 1, day_number: 1, ...}, ...]`
  - Nhưng `normalizeDayServicesFormat()` expect format khác
  - Hoặc dữ liệu từ template không có field `service_id` (có thể là `service_id` = null)

  **Vấn đề 3: Service ID bị null/empty**
  - Template có dịch vụ nhưng `service_id` = null hoặc 0
  - Validation check `if (empty($service_id))` → fail

  **Vấn đề 4: Session data không được truyền đúng**
  - `$session_data` không có `itinerary_day_services`
  - Hoặc có nhưng format sai

---

### BƯỚC 4: Kiểm tra cụ thể

#### 4.1. Debug points cần thêm

1. **Trong `createFromTemplate()`:**
   ```php
   // Sau line 188
   error_log("Template day services: " . json_encode($old_input['itinerary_day_services']));
   error_log("Session day services: " . json_encode($_SESSION['tour_form_data']['itinerary_day_services']));
   ```

2. **Trong `store()`:**
   ```php
   // Sau line 212
   error_log("POST day_service_day_number: " . json_encode($_POST['day_service_day_number'] ?? 'NOT SET'));
   error_log("Session data itinerary_day_services: " . json_encode($session_data['itinerary_day_services'] ?? 'NOT SET'));
   error_log("Form data after merge: " . json_encode($form_data['itinerary_day_services'] ?? 'NOT SET'));
   ```

3. **Trong `validateTourData()`:**
   ```php
   // Sau line 700
   error_log("Day services from POST: " . json_encode($day_services_to_validate));
   // Sau line 716
   error_log("Day services after session check: " . json_encode($day_services_to_validate));
   // Sau line 704
   error_log("Normalized session services: " . json_encode($session_services));
   ```

#### 4.2. Kiểm tra database

- **Query để kiểm tra template có dịch vụ không:**
  ```sql
  SELECT ids.*, i.day_number, i.tour_id
  FROM itinerary_day_services ids
  JOIN itineraries i ON ids.itinerary_id = i.id
  WHERE i.tour_id = [template_id]
  ORDER BY i.day_number;
  ```

- **Kiểm tra:**
  - Template có `itinerary_day_services` không?
  - `service_id` có giá trị hợp lệ không?
  - `unit_price` và `quantity` > 0 không?

---

### BƯỚC 5: Phân tích nguyên nhân có thể

#### Nguyên nhân 1: Form không submit day services từ template
- **Triệu chứng:** POST không có `day_service_day_number[]`
- **Nguyên nhân:** JavaScript không tạo hidden inputs cho day services từ template
- **Giải pháp:** Đảm bảo form load và serialize day services từ `old_input['itinerary_day_services']`

#### Nguyên nhân 2: Format dữ liệu không đúng
- **Triệu chứng:** Session có data nhưng `normalizeDayServicesFormat()` không parse được
- **Nguyên nhân:** Format từ database khác với format mà normalize function expect
- **Giải pháp:** Fix `normalizeDayServicesFormat()` hoặc format dữ liệu khi lưu vào session

#### Nguyên nhân 3: Service ID bị null
- **Triệu chứng:** `service_id` = null hoặc 0 trong dữ liệu template
- **Nguyên nhân:** Database có dữ liệu không hợp lệ
- **Giải pháp:** Validate và filter dữ liệu khi load từ template

#### Nguyên nhân 4: Validation check POST trước, bỏ qua session
- **Triệu chứng:** POST có field nhưng rỗng, validation không check session
- **Nguyên nhân:** Logic validation check `empty()` nhưng có thể có edge case
- **Giải pháp:** Fix logic validation để check session ngay cả khi POST có field nhưng rỗng

---

## 🔍 CÁC ĐIỂM CẦN KIỂM TRA CỤ THỂ

### 1. Format dữ liệu từ `getItineraryDayServices()`
- **File:** `app/models/ItineraryDayService.php::getByTourId()`
- **Kiểm tra:** Các field trả về có đầy đủ không?
  - `service_id` (required)
  - `day_number` (required)
  - `unit_price` (required, > 0)
  - `quantity` (required, > 0)
  - `service_provider_id` (optional)

### 2. Format dữ liệu trong session
- **Kiểm tra:** Sau khi lưu vào session, format có đúng không?
- **Expected format:** 
  ```php
  [
      ['day_number' => 1, 'service_id' => 1, 'unit_price' => 100, 'quantity' => 1, ...],
      ['day_number' => 1, 'service_id' => 2, 'unit_price' => 200, 'quantity' => 1, ...],
      ['day_number' => 2, 'service_id' => 3, 'unit_price' => 150, 'quantity' => 1, ...],
  ]
  ```

### 3. Logic validation
- **File:** `app/controllers/staff/TourController.php::validateTourData()` (line 689-716)
- **Vấn đề:** 
  - Line 690: `if (!empty($post['day_service_day_number']))` - check POST trước
  - Line 703: `if (empty($day_services_to_validate) && !empty($session_data['itinerary_day_services']))` - chỉ check session nếu POST rỗng
  - **Nhưng:** Nếu POST có field nhưng là array rỗng `[]`, thì `empty([])` = `true`, nhưng có thể có logic khác

### 4. Normalize function
- **File:** `app/controllers/staff/TourController.php::normalizeDayServicesFormat()` (line 853-884)
- **Kiểm tra:** Function có handle đúng format từ database không?
- **Format từ database:** Indexed array `[{...}, {...}]`
- **Format expected:** Indexed array `[{...}, {...}]`
- **Vấn đề có thể:** Function check `array_key_first()` và có logic phức tạp, có thể miss một số format

---

## 🎯 KẾ HOẠCH SỬA LỖI (SAU KHI PHÂN TÍCH)

### Option 1: Fix validation logic
- Đảm bảo validation check session ngay cả khi POST có field nhưng rỗng
- Hoặc merge POST và session trước khi validate

### Option 2: Fix form submission
- Đảm bảo form luôn submit day services từ template (tạo hidden inputs)

### Option 3: Fix normalize function
- Đảm bảo `normalizeDayServicesFormat()` handle đúng format từ database

### Option 4: Fix data loading
- Đảm bảo dữ liệu từ template được load và format đúng trước khi lưu vào session

---

## 📝 CHECKLIST PHÂN TÍCH

- [ ] 1. Kiểm tra format dữ liệu từ `getItineraryDayServices()`
- [ ] 2. Kiểm tra dữ liệu trong session sau khi load template
- [ ] 3. Kiểm tra form có submit day services không
- [ ] 4. Kiểm tra POST data khi submit
- [ ] 5. Kiểm tra session data trong `store()`
- [ ] 6. Kiểm tra logic validation
- [ ] 7. Kiểm tra normalize function
- [ ] 8. Thêm debug logs để trace flow
- [ ] 9. Test với template có dịch vụ
- [ ] 10. Test với template không có dịch vụ

---

## 🔧 CÔNG CỤ DEBUG

1. **Error logs:** Thêm `error_log()` ở các điểm quan trọng
2. **Var dump:** Dump dữ liệu trong view để xem format
3. **Database query:** Kiểm tra dữ liệu trong database
4. **Browser console:** Kiểm tra JavaScript serialize data đúng không

---

## 📌 LƯU Ý

- **Không code ngay:** Chỉ phân tích và lên kế hoạch
- **Test kỹ:** Sau khi sửa, test với nhiều trường hợp:
  - Template có dịch vụ
  - Template không có dịch vụ
  - User thêm dịch vụ mới
  - User xóa dịch vụ từ template
  - User không chỉnh sửa gì

