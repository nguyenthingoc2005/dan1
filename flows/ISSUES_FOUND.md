# 🔍 CÁC VẤN ĐỀ PHÁT HIỆN

## ❌ VẤN ĐỀ 1: Thiếu CSRF Token trong form create/edit customer

**Files:**
- `app/views/admin/customers/create.php`
- `app/views/admin/customers/edit.php`
- `app/views/staff/customers/create.php`
- `app/views/staff/customers/edit.php`

**Vấn đề:** Form không có CSRF token field

---

## ❌ VẤN ĐỀ 2: Thiếu CSRF Validation trong CustomerController

**Files:**
- `app/controllers/admin/CustomerController.php::store()`
- `app/controllers/admin/CustomerController.php::update()`
- `app/controllers/staff/CustomerController.php::store()`
- `app/controllers/staff/CustomerController.php::update()`

**Vấn đề:** Chưa có `require_csrf_token()` để validate CSRF token

---

## ❌ VẤN ĐỀ 3: Thiếu method downloadTemplate() trong CustomerController

**Files:**
- `app/controllers/admin/CustomerController.php`
- `app/controllers/staff/CustomerController.php`

**Vấn đề:** View import có link download template nhưng controller chưa có method

---

## ⚠️ VẤN ĐỀ 4: Customer code format có thể conflict nếu nhiều customer cùng ngày

**File:** `app/models/Customer.php::generateCustomerCode()`

**Vấn đề:** Nếu có nhiều hơn 999 customer trong 1 ngày, sẽ bị conflict. Cần xử lý edge case này.

---

## ✅ CÁC VẤN ĐỀ KHÁC ĐÃ OK

- ✅ Special requirements đã được thêm vào form và controller
- ✅ Password validation đã được sửa (min 8)
- ✅ Password confirmation đã được thêm
- ✅ Business rule role đã được thêm
- ✅ Import actions đã được thêm
- ✅ Routes đã được thêm

