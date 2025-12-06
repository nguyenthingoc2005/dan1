# 📋 KẾ HOẠCH CẬP NHẬT: CUSTOMER & STAFF MODULE

## 🎯 MỤC TIÊU

Cập nhật code để đáp ứng đầy đủ các yêu cầu trong `FLOW_ANALYSIS_CUSTOMER_STAFF.md`

---

## 📅 KẾ HOẠCH THỰC HIỆN

### **PHASE 1: CẬP NHẬT CUSTOMER MODULE** (Ưu tiên cao)

#### **Task 1.1: Thêm field `special_requirements` vào form**

**Files cần sửa:**
- `app/views/admin/customers/create.php`
- `app/views/admin/customers/edit.php`
- `app/views/staff/customers/create.php`
- `app/views/staff/customers/edit.php`

**Thay đổi:**
- Thêm textarea field `special_requirements` vào form (sau field `notes`)

**Code mẫu:**
```php
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-1">Yêu cầu đặc biệt</label>
    <textarea name="special_requirements" rows="3"
        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
        placeholder="Nhập yêu cầu đặc biệt của khách hàng..."></textarea>
</div>
```

**Estimated time:** 15 phút

---

#### **Task 1.2: Sửa customer code format**

**File:** `app/models/Customer.php`

**Thay đổi:**
- Sửa method `generateCustomerCode()`:
  - Format cũ: `KH-YYYYMM-XXXX` (ví dụ: `KH-202412-0001`)
  - Format mới: `CUS-YYYYMMDD-XXX` (ví dụ: `CUS-20241206-001`)

**Code mới:**
```php
public function generateCustomerCode()
{
    $date = date('Ymd'); // YYYYMMDD
    $prefix = 'CUS-' . $date . '-';
    
    $sql = "SELECT customer_code FROM customers 
            WHERE customer_code LIKE :prefix 
            ORDER BY id DESC LIMIT 1";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['prefix' => $prefix . '%']);
    $lastCode = $stmt->fetchColumn();
    
    if ($lastCode) {
        $number = (int) substr($lastCode, -3); // Lấy 3 số cuối
        return $prefix . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }
    
    return $prefix . '001';
}
```

**Estimated time:** 10 phút

---

#### **Task 1.3: Thêm action `import()` trong CustomerController (Admin)**

**File:** `app/controllers/admin/CustomerController.php`

**Thêm methods:**
1. `import()` - Hiển thị form upload
2. `importStore()` - Xử lý upload và import
3. `importResult($log_id)` - Hiển thị kết quả import
4. `importLogs()` - Danh sách import logs

**Code structure:**
```php
/**
 * Trang import khách hàng
 */
public function import()
{
    require_admin();
    
    $page_title = 'Import Khách hàng từ Excel';
    $content_file = VIEWS_PATH . '/admin/customers/import.php';
    require VIEWS_PATH . '/layouts/admin_layout.php';
}

/**
 * Xử lý import
 */
public function importStore()
{
    require_admin();
    require_csrf_token();
    
    try {
        if (empty($_FILES['file'])) {
            throw new Exception("Vui lòng chọn file");
        }
        
        $file = $_FILES['file'];
        
        // Validate file
        $allowed = ['csv', 'xlsx', 'xls'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowed)) {
            throw new Exception("Chỉ chấp nhận file CSV, XLSX, XLS");
        }
        
        // Upload file
        $uploadDir = 'public/uploads/imports/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = 'import_' . time() . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;
        
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new Exception("Không thể upload file");
        }
        
        // Import
        require_once MODELS_PATH . '/CustomerImport.php';
        $importModel = new CustomerImport($this->db);
        $result = $importModel->importFromFile($filePath, $file['name'], get_user_id());
        
        // Redirect to result page
        redirect('?act=admin&module=customers&action=importResult&log_id=' . $result['log_id']);
        
    } catch (Exception $e) {
        set_error($e->getMessage());
        redirect('?act=admin&module=customers&action=import');
    }
}

/**
 * Hiển thị kết quả import
 */
public function importResult()
{
    require_admin();
    
    $log_id = $_GET['log_id'] ?? null;
    if (!$log_id) {
        redirect('?act=admin&module=customers&action=import');
    }
    
    require_once MODELS_PATH . '/CustomerImport.php';
    $importModel = new CustomerImport($this->db);
    
    // Get log details
    $sql = "SELECT * FROM customer_import_logs WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['id' => $log_id]);
    $log = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$log) {
        set_error("Không tìm thấy log import");
        redirect('?act=admin&module=customers&action=import');
    }
    
    $errors = json_decode($log['error_details'], true) ?? [];
    
    $page_title = 'Kết quả Import';
    $content_file = VIEWS_PATH . '/admin/customers/import_result.php';
    require VIEWS_PATH . '/layouts/admin_layout.php';
}

/**
 * Danh sách import logs
 */
public function importLogs()
{
    require_admin();
    
    $page = $_GET['page'] ?? 1;
    $limit = 20;
    
    require_once MODELS_PATH . '/CustomerImport.php';
    $importModel = new CustomerImport($this->db);
    $logs = $importModel->getImportLogs($page, $limit);
    
    $page_title = 'Lịch sử Import';
    $content_file = VIEWS_PATH . '/admin/customers/import_logs.php';
    require VIEWS_PATH . '/layouts/admin_layout.php';
}
```

**Estimated time:** 45 phút

---

#### **Task 1.4: Tạo views cho import**

**Files cần tạo:**
1. `app/views/admin/customers/import.php` - Form upload
2. `app/views/admin/customers/import_result.php` - Kết quả import
3. `app/views/admin/customers/import_logs.php` - Danh sách logs

**View structure:**

**import.php:**
- Form upload file
- Link download template
- Button submit

**import_result.php:**
- Summary: Tổng số dòng, thành công, lỗi
- Bảng chi tiết lỗi (nếu có)
- Link quay lại hoặc import tiếp

**import_logs.php:**
- Bảng danh sách logs
- Pagination
- Link xem chi tiết từng log

**Estimated time:** 60 phút

---

#### **Task 1.5: Thêm action `import()` trong CustomerController (Staff)**

**File:** `app/controllers/staff/CustomerController.php`

**Tương tự như Admin**, nhưng:
- Không cần `require_admin()`, chỉ cần check staff role
- Views tương tự nhưng trong folder `staff/customers/`

**Estimated time:** 30 phút

---

### **PHASE 2: CẬP NHẬT USER MODULE** (Ưu tiên cao)

#### **Task 2.1: Sửa password validation (min 6 → min 8)**

**File:** `app/controllers/admin/UserController.php`

**Thay đổi:**
- Method `store()`: Sửa `strlen($password) < 6` → `strlen($password) < 8`
- Method `update()`: Sửa `strlen($_POST['password']) < 6` → `strlen($_POST['password']) < 8`
- Update error message: "Mật khẩu tối thiểu 8 ký tự"

**Estimated time:** 5 phút

---

#### **Task 2.2: Thêm validation password confirmation**

**File:** `app/controllers/admin/UserController.php`

**Thay đổi trong `store()`:**
```php
// Password: chỉ check độ dài
$password = $_POST['password'];
if (strlen($password) < 8) {
    throw new Exception("Mật khẩu tối thiểu 8 ký tự.");
}

// Password confirmation
if (empty($_POST['password_confirmation'])) {
    throw new Exception("Vui lòng xác nhận mật khẩu.");
}

if ($password !== $_POST['password_confirmation']) {
    throw new Exception("Mật khẩu xác nhận không khớp.");
}
```

**File:** `app/views/admin/users/create.php`

**Thêm field:**
```php
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Xác nhận mật khẩu <span class="text-red-500">*</span>
    </label>
    <input type="password" name="password_confirmation" required
        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
        placeholder="Nhập lại mật khẩu">
</div>
```

**Estimated time:** 15 phút

---

#### **Task 2.3: Thêm business rule: Không đổi role của chính mình**

**File:** `app/controllers/admin/UserController.php`

**Thay đổi trong `update()`:**
```php
// Business rule: Không được thay đổi role của chính mình
$user_id = (int) $_POST['id'];
$current_user_id = get_user_id();

if ($user_id == $current_user_id && $_POST['role_id'] != $user['role_id']) {
    throw new Exception("Bạn không thể thay đổi vai trò của chính mình!");
}
```

**Estimated time:** 5 phút

---

#### **Task 2.4: Bổ sung password strength hint (Optional)**

**File:** `app/views/admin/users/create.php`

**Thêm hint text:**
```php
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Mật khẩu <span class="text-red-500">*</span>
    </label>
    <input type="password" name="password" required minlength="8"
        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
        placeholder="Tối thiểu 8 ký tự">
    <p class="mt-1 text-xs text-gray-500">
        Khuyến nghị: Có chữ hoa, chữ thường, số và ký tự đặc biệt
    </p>
</div>
```

**Estimated time:** 5 phút

---

### **PHASE 3: CẬP NHẬT ROUTES** (Ưu tiên cao)

#### **Task 3.1: Thêm routes cho import**

**File:** `routes/admin.php`

**Thêm routes:**
```php
// Customer Import
if ($module === 'customers' && $action === 'import') {
    $controller->import();
} elseif ($module === 'customers' && $action === 'importStore') {
    $controller->importStore();
} elseif ($module === 'customers' && $action === 'importResult') {
    $controller->importResult();
} elseif ($module === 'customers' && $action === 'importLogs') {
    $controller->importLogs();
}
```

**File:** `routes/staff.php`

**Tương tự cho staff routes**

**Estimated time:** 10 phút

---

### **PHASE 4: TESTING & VERIFICATION** (Ưu tiên trung bình)

#### **Task 4.1: Test Customer Module**

- [ ] Test tạo customer với đầy đủ fields (bao gồm special_requirements)
- [ ] Test customer code format mới
- [ ] Test import customer từ CSV
- [ ] Test import với duplicate phone (skip)
- [ ] Test import với validation errors
- [ ] Test xem import result và logs
- [ ] Test check-in customer

**Estimated time:** 30 phút

---

#### **Task 4.2: Test User Module**

- [ ] Test tạo user với password min 8 ký tự
- [ ] Test password confirmation validation
- [ ] Test không thể đổi role của chính mình
- [ ] Test các validation khác

**Estimated time:** 20 phút

---

## 📊 TỔNG KẾT

### **Thời gian ước tính:**
- Phase 1: ~2.5 giờ
- Phase 2: ~30 phút
- Phase 3: ~10 phút
- Phase 4: ~50 phút

**Tổng:** ~4 giờ

### **Thứ tự ưu tiên:**
1. ✅ Task 1.1: Thêm special_requirements (15 phút)
2. ✅ Task 1.2: Sửa customer code format (10 phút)
3. ✅ Task 2.1: Sửa password validation (5 phút)
4. ✅ Task 2.2: Thêm password confirmation (15 phút)
5. ✅ Task 2.3: Business rule role (5 phút)
6. ✅ Task 1.3: Import action Admin (45 phút)
7. ✅ Task 1.4: Import views (60 phút)
8. ✅ Task 1.5: Import action Staff (30 phút)
9. ✅ Task 3.1: Routes (10 phút)
10. ✅ Task 4.1 & 4.2: Testing (50 phút)

---

## ✅ CHECKLIST HOÀN THÀNH

### Customer Module
- [ ] Task 1.1: Thêm special_requirements
- [ ] Task 1.2: Sửa customer code format
- [ ] Task 1.3: Import action Admin
- [ ] Task 1.4: Import views
- [ ] Task 1.5: Import action Staff

### User Module
- [ ] Task 2.1: Sửa password validation
- [ ] Task 2.2: Password confirmation
- [ ] Task 2.3: Business rule role
- [ ] Task 2.4: Password strength hint (optional)

### Routes
- [ ] Task 3.1: Thêm routes

### Testing
- [ ] Task 4.1: Test Customer Module
- [ ] Task 4.2: Test User Module

---

**Status:** 📝 Sẵn sàng triển khai

**Ngày tạo:** 2024-12-06

