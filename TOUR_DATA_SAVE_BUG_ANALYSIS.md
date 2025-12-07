# 🔍 PHÂN TÍCH BUG TIỀM ẨN - LƯU DỮ LIỆU TOUR

## 📋 TỔNG QUAN

Phân tích từng bước flow lưu và restore dữ liệu để tìm các bug tiềm ẩn.

---

## 🔄 FLOW LƯU DỮ LIỆU - TỪNG BƯỚC

### **BƯỚC 1: User nhập dữ liệu trên form**

**Frontend (JavaScript):**
- User nhập vào các input fields
- Data được lưu trong DOM (chưa lưu vào session)

**Vị trí:**
- File: `app/views/admin/tours/create.php`
- Các input fields trong form

---

### **BƯỚC 2: User chuyển step hoặc reload**

**Frontend → Backend (AJAX):**
```javascript
// File: app/views/admin/tours/create.php
function saveFormDataToSession(additionalData = {}) {
    // Collect all form data
    const formData = {
        name: document.getElementById('name')?.value || '',
        // ... các fields khác
        fixed_cost_guide: parseFloat(document.getElementById('fixed_cost_guide')?.value || 0),
        fixed_cost_management: parseFloat(document.getElementById('fixed_cost_management')?.value || 0),
        fixed_cost_marketing: parseFloat(document.getElementById('fixed_cost_marketing')?.value || 0),
        fixed_cost_other: parseFloat(document.getElementById('fixed_cost_other')?.value || 0),
    };
    
    // Collect itinerary, day services, highlights, etc.
    
    // Send to backend via AJAX
    fetch('?act=admin&module=tours&action=saveFormSession', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dataToSave)
    })
}
```

**✅ ĐÃ KIỂM TRA:**
- ✅ Fixed costs đã được thu thập
- ✅ Itinerary đã được thu thập (bao gồm TinyMCE content)
- ✅ Day services đã được thu thập
- ✅ Highlights, included, excluded đã được thu thập
- ✅ Policy IDs đã được thu thập

---

### **BƯỚC 3: Backend nhận và lưu vào Session**

**Backend (PHP):**
```php
// File: app/controllers/admin/TourController.php
public function saveFormSession() {
    $data = json_decode($input, true);
    
    // Lưu từng phần dữ liệu
    if (isset($data['form_data']) && is_array($data['form_data'])) {
        foreach ($data['form_data'] as $key => $value) {
            $_SESSION['tour_form_data'][$key] = $value;
        }
    }
    
    if (isset($data['itinerary']) && is_array($data['itinerary'])) {
        $_SESSION['tour_form_data']['itinerary'] = $data['itinerary'];
    }
    
    if (isset($data['itinerary_day_services'])) {
        // Format: Array hoặc Object
        $_SESSION['tour_form_data']['itinerary_day_services'] = $data['itinerary_day_services'];
    }
    
    // ... highlights, included, excluded, policy_ids
}
```

**✅ ĐÃ KIỂM TRA:**
- ✅ Form data được lưu đúng (bao gồm fixed costs)
- ✅ Itinerary được lưu đúng
- ✅ Day services được lưu đúng

---

### **BƯỚC 4: Page reload - Restore dữ liệu**

**Backend (PHP):**
```php
// File: app/controllers/admin/TourController.php
public function create() {
    // Load dữ liệu từ session
    $session_data = $this->loadTourSession();
    
    // Merge vào old_input để pre-fill form
    $old_input = $session_data;
}
```

**Frontend (PHP → JavaScript):**
```php
// File: app/views/admin/tours/create.php
$old = $old_input ?? [];

// Prepare data for JavaScript
$old_itinerary = $old['itinerary'] ?? [];
$old_day_services = $old['itinerary_day_services'] ?? [];
// ...
```

**Input fields restore:**
```php
// Fixed costs
value="<?= $old['fixed_cost_guide'] ?? '0' ?>"
value="<?= $old['fixed_cost_management'] ?? '0' ?>"
value="<?= $old['fixed_cost_marketing'] ?? '0' ?>"
value="<?= $old['fixed_cost_other'] ?? '0' ?>"
```

**✅ ĐÃ KIỂM TRA:**
- ✅ Fixed costs được restore từ `$old`
- ✅ Itinerary được restore
- ✅ Day services được restore

---

### **BƯỚC 5: Submit form - Merge Session + POST**

**Backend (PHP):**
```php
// File: app/controllers/admin/TourController.php
public function store() {
    // 1. Load session data
    $session_data = $this->loadTourSession();
    
    // 2. Merge session + POST (POST có priority cao hơn)
    $form_data = array_merge($session_data, $_POST);
    
    // 3. Đảm bảo POST được ưu tiên
    foreach ($_POST as $key => $value) {
        $form_data[$key] = $value;
    }
    
    // 4. Validate và save
}
```

**✅ ĐÃ KIỂM TRA:**
- ✅ Merge logic đúng (POST ưu tiên hơn session)
- ✅ Fixed costs được lấy từ `$form_data`

---

## ❌ BUG TIỀM ẨN ĐÃ PHÁT HIỆN

### **BUG 1: Day Services Format Mismatch** ⚠️ POTENTIAL

**Vấn đề:**
- Khi lưu vào session, `itinerary_day_services` có thể là:
  - Array: `[{day_number: 1, ...}, {day_number: 2, ...}]`
  - Object: `{1: [...], 2: [...]}`
- Khi restore, code xử lý cả 2 format, nhưng có thể bị lỗi nếu format không đúng

**Vị trí:**
- File: `app/views/admin/tours/create.php`
- Function: `restoreDayServicesFromSession()`

**Kiểm tra:**
```javascript
// Line 2170-2199
let servicesByDay = {};

if (Array.isArray(oldDayServices)) {
    // Format: [{day_number: 1, ...}, {day_number: 2, ...}]
    oldDayServices.forEach(service => {
        const dayNum = service.day_number || 1;
        if (!servicesByDay[dayNum]) {
            servicesByDay[dayNum] = [];
        }
        servicesByDay[dayNum].push(service);
    });
} else if (typeof oldDayServices === 'object' && oldDayServices !== null) {
    // Format: { 1: [...], 2: [...] }
    servicesByDay = oldDayServices;
}
```

**✅ ĐÃ XỬ LÝ:**
- Code đã xử lý cả 2 format
- Có check null/undefined

---

### **BUG 2: Session Data Có Thể Bị Ghi Đè Không Đúng** ⚠️ POTENTIAL

**Vấn đề:**
- Khi save session, nếu có 2 request cùng lúc (race condition), có thể bị ghi đè
- Khi merge session + POST, nếu POST có key rỗng, có thể ghi đè session data

**Vị trí:**
- File: `app/controllers/admin/TourController.php`
- Function: `saveFormSession()`

**Kiểm tra:**
```php
// Line 1172-1177
if (isset($data['form_data']) && is_array($data['form_data'])) {
    foreach ($data['form_data'] as $key => $value) {
        $_SESSION['tour_form_data'][$key] = $value; // ⚠️ Ghi đè trực tiếp
    }
}
```

**Phân tích:**
- ✅ Không có race condition vì PHP xử lý tuần tự
- ⚠️ Nếu value = null hoặc empty string, vẫn ghi đè (có thể là bug)

**Ví dụ bug:**
```php
// Session có: fixed_cost_guide = 1000000
// POST có: fixed_cost_guide = '' (empty)
// → Ghi đè thành empty string → Mất dữ liệu
```

**Cần kiểm tra:**
- Khi merge, nếu POST value là empty, có nên giữ session value không?

---

### **BUG 3: Fixed Costs Không Được Restore Từ Session** ✅ ĐÃ FIX

**Vấn đề:**
- Function `saveFormDataToSession()` trước đây KHÔNG lưu fixed costs
- ✅ **ĐÃ FIX:** Đã thêm 4 fields fixed costs vào function

---

### **BUG 4: Merge Logic Có Thể Bị Ghi Đè Ngược** ⚠️ POTENTIAL

**Vấn đề:**
```php
// Line 210-215
$form_data = array_merge($session_data, $_POST);

// Đảm bảo các field từ POST được ưu tiên
foreach ($_POST as $key => $value) {
    $form_data[$key] = $value;
}
```

**Phân tích:**
- `array_merge($session_data, $_POST)` → POST đã ưu tiên
- Loop `foreach ($_POST as $key => $value)` → Ghi đè lại lần nữa (thừa)
- ✅ **KHÔNG PHẢI BUG:** Chỉ là code thừa, không ảnh hưởng logic

**Nhưng có vấn đề:**
- Nếu POST có key nhưng value = null/empty → Vẫn ghi đè session
- Có thể mất dữ liệu từ session

**Ví dụ:**
```php
// Session: fixed_cost_guide = 1000000
// POST: fixed_cost_guide = '' (empty từ form)
// → form_data['fixed_cost_guide'] = '' (mất dữ liệu)
```

**⚠️ CẦN KIỂM TRA:**
- Khi POST value = empty, có nên giữ session value không?

---

### **BUG 5: TinyMCE Content Có Thể Bị Mất** ⚠️ POTENTIAL

**Vấn đề:**
- Khi save session, TinyMCE content được lấy bằng `editor.getContent()`
- Nếu editor chưa ready, có thể lấy được empty string

**Vị trí:**
- File: `app/views/admin/tours/create.php`
- Function: `saveFormDataToSession()`

**Kiểm tra:**
```javascript
// Line 2330-2340
if (typeof tinymce !== 'undefined') {
    const editor = tinymce.get(`itinerary-description-day-${dayNumber}`);
    if (editor) {
        description = editor.getContent();
    } else {
        // Fallback: lấy từ textarea
        const textarea = document.getElementById(`itinerary-description-day-${dayNumber}`);
        if (textarea) {
            description = textarea.value;
        }
    }
}
```

**✅ ĐÃ XỬ LÝ:**
- Có fallback về textarea nếu editor chưa ready
- Có check `typeof tinymce !== 'undefined'`

---

### **BUG 6: Day Services Có Thể Bị Mất Khi Xóa Item** ⚠️ POTENTIAL

**Vấn đề:**
- Khi xóa một day service item, code gọi `saveDayServiceToSession()`
- Nhưng nếu container rỗng, có thể không lưu gì cả → Mất tất cả services của ngày đó

**Vị trí:**
- File: `app/views/admin/tours/create.php`
- Function: `removeDayServiceItem()`

**Kiểm tra:**
```javascript
// Line 2445-2452
function removeDayServiceItem(button, dayNumber) {
    if (confirm('Bạn có chắc muốn xóa dịch vụ này?')) {
        button.closest('.day-service-item').remove();
        updateDayServiceTotal(dayNumber);
        // Lưu lại vào session sau khi xóa
        saveDayServiceToSession(dayNumber); // ✅ OK - Sẽ lưu lại các items còn lại
    }
}
```

**✅ ĐÃ XỬ LÝ:**
- Function `saveDayServiceToSession()` sẽ collect tất cả items còn lại và lưu
- Nếu không còn item nào, sẽ lưu array rỗng → Đúng logic

---

## 🔍 CÁC VẤN ĐỀ CẦN KIỂM TRA THÊM

### **1. Empty Value Handling**

**Câu hỏi:**
- Khi POST có value = empty string, có nên giữ session value không?
- Hay nên ghi đè bằng empty (user muốn xóa)?

**Hiện tại:**
- Code ghi đè trực tiếp → Có thể mất dữ liệu

**Đề xuất:**
- Nếu POST value = empty VÀ field là optional → Giữ session value
- Nếu POST value = empty VÀ field là required → Validate và báo lỗi

---

### **2. Session Size Limit**

**Câu hỏi:**
- Session có giới hạn size không?
- Nếu tour có nhiều day services, session có quá lớn không?

**Phân tích:**
- PHP session default limit: thường là 128KB hoặc không giới hạn (tùy config)
- Một tour với 10 ngày, mỗi ngày 5 services → ~10KB → OK

**✅ KHÔNG CÓ VẤN ĐỀ**

---

### **3. Session Timeout**

**Câu hỏi:**
- Session có bị timeout không?
- User có thể mất dữ liệu nếu timeout?

**Phân tích:**
- PHP session timeout mặc định: 24 phút (1440 giây)
- User tạo tour lâu hơn 24 phút → Có thể mất session

**⚠️ CẦN XỬ LÝ:**
- Auto-save thường xuyên
- Hoặc tăng session timeout
- Hoặc lưu vào database tạm thời

---

## ✅ KẾT LUẬN

### **Các bug đã phát hiện:**
1. ✅ **Fixed costs không lưu session** → ĐÃ FIX
2. ⚠️ **Empty value có thể ghi đè session** → CẦN KIỂM TRA
3. ⚠️ **Session timeout có thể mất dữ liệu** → CẦN XỬ LÝ

### **Các phần đã đúng:**
- ✅ Save session flow đúng
- ✅ Restore session flow đúng
- ✅ Merge logic đúng (POST ưu tiên)
- ✅ Format handling đúng (array/object)

### **Các cải thiện đề xuất:**
1. Xử lý empty value khi merge
2. Tăng session timeout hoặc auto-save
3. Validate session data trước khi restore

---

**Ngày phân tích:** 2024-12-06

