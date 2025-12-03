# 📊 PHÂN TÍCH CHI TIẾT CÁC MODULE QUẢN TRỊ

**Ngày phân tích:** 2024-12-XX  
**Mục tiêu:** Phân tích các module: Nhân viên, Danh mục, Địa điểm, Dịch vụ, Loại dịch vụ, Nhà cung cấp

---

## 📋 MỤC LỤC

1. [Module 1: Nhân viên (Users)](#module-1-nhân-viên-users)
2. [Module 2: Danh mục (Categories)](#module-2-danh-mục-categories)
3. [Module 3: Địa điểm (Destinations)](#module-3-địa-điểm-destinations)
4. [Module 4: Loại dịch vụ (Service Types)](#module-4-loại-dịch-vụ-service-types)
5. [Module 5: Nhà cung cấp (Suppliers)](#module-5-nhà-cung-cấp-suppliers)
6. [Module 6: Dịch vụ (Services)](#module-6-dịch-vụ-services)

---

## 🔍 MODULE 1: NHÂN VIÊN (USERS)

### ✅ ĐÃ CÓ

**Model (`User.php`):**
- ✅ `findByEmail()` - Tìm user theo email
- ✅ `findById()` - Tìm user theo ID
- ✅ `verifyPassword()` - Xác thực password
- ✅ `updateLastLogin()` - Cập nhật last_login
- ✅ `getAll()` - Lấy danh sách với filters (role, status, search) và pagination
- ✅ `create()` - Tạo user mới
- ✅ `update()` - Cập nhật user (có thể update password)
- ✅ `delete()` - Soft delete (set status = 'inactive')
- ✅ `hardDelete()` - Hard delete

**Controller (`UserController.php`):**
- ✅ `index()` - Danh sách users với filters
- ✅ `create()` - Form tạo user
- ✅ `store()` - Xử lý tạo user với validation:
  - Email format + unique
  - Password min 6 chars
  - Avatar upload
- ✅ `edit()` - Form sửa user
- ✅ `update()` - Xử lý update user
- ✅ `delete()` - Soft delete
- ✅ `toggleStatus()` - AJAX toggle status
- ✅ `uploadAvatar()` - Upload avatar helper

### ❌ THIẾU SÓT

#### 🔴 CRITICAL

1. **Validation email unique khi update**
   - **Vấn đề:** `UserController::update()` không check email unique khi update
   - **Hiện tại:** Chỉ validate format, không check duplicate
   - **Cần:** Thêm check `findByEmail()` và exclude current user ID

2. **Thiếu method `isEmailExists()` trong User model**
   - **Vấn đề:** Không có method riêng để check email exists (exclude current user)
   - **Cần:** Thêm method `isEmailExists($email, $excludeId = null)`

3. **Thiếu validation phone unique (nếu cần)**
   - **Vấn đề:** Phone có thể trùng giữa các users
   - **Cần:** Quyết định có cần unique không (thường không cần)

#### 🟡 HIGH

4. **Thiếu user activity log**
   - **Vấn đề:** Không track các hành động của user (login, logout, actions)
   - **Cần:** Tạo bảng `user_activity_logs` và log các actions quan trọng

5. **Thiếu password reset functionality**
   - **Vấn đề:** Không có chức năng reset password
   - **Cần:** 
     - Tạo bảng `password_resets` (token, email, expires_at)
     - Method `requestPasswordReset($email)`
     - Method `resetPassword($token, $newPassword)`
     - Controller actions: `forgotPassword()`, `resetPassword()`

6. **Thiếu role permissions check**
   - **Vấn đề:** Chỉ check `require_admin()`, không có fine-grained permissions
   - **Cần:** (Optional) Implement role-based permissions nếu cần

#### 🟢 MEDIUM

7. **Thiếu bulk actions (activate/deactivate multiple users)**
   - **Vấn đề:** Chỉ có toggle từng user một
   - **Cần:** (Optional) Bulk activate/deactivate

8. **Thiếu export users to Excel/CSV**
   - **Vấn đề:** Không có chức năng export
   - **Cần:** (Optional) Export functionality

---

## 🔍 MODULE 2: DANH MỤC (CATEGORIES)

### ✅ ĐÃ CÓ

**Model (`Category.php`):**
- ✅ `getAll()` - Lấy danh sách với filters (status, search) và pagination
- ✅ `findById()` - Tìm category theo ID
- ✅ `findByName()` - Tìm category theo tên
- ✅ `create()` - Tạo category mới
- ✅ `update()` - Cập nhật category
- ✅ `delete()` - Soft delete (set status = 'inactive')
- ✅ `toggleStatus()` - Toggle status
- ✅ `getForDropdown()` - Lấy categories cho dropdown

**Controller (`CategoryController.php`):**
- ✅ `index()` - Danh sách categories
- ✅ `create()` - Form tạo category
- ✅ `store()` - Xử lý tạo với validation:
  - Name required + unique
  - Display order uniqueness check
- ✅ `edit()` - Form sửa category
- ✅ `update()` - Xử lý update với validation
- ✅ `delete()` - Soft delete

### ❌ THIẾU SÓT

#### 🟡 HIGH

1. **Thiếu parent-child relationship handling**
   - **Vấn đề:** Database có `parent_id` nhưng code không xử lý
   - **Cần:**
     - Method `getChildren($parent_id)` - Lấy danh sách categories con
     - Method `getParent($id)` - Lấy category cha
     - Validation: Không cho phép set parent = chính nó (circular reference)
     - Validation: Không cho phép set parent = con của nó (prevent cycles)
     - UI: Dropdown để chọn parent category trong create/edit form

2. **Thiếu check usage trước khi delete**
   - **Vấn đề:** `delete()` không check xem category có đang được sử dụng bởi destinations/tours không
   - **Cần:** 
     - Check `destinations.category_id`
     - Check `tours.category_id` (nếu có)
     - Throw exception nếu đang được sử dụng

3. **Thiếu method `getTree()` - Lấy categories dạng tree**
   - **Vấn đề:** Không có method để lấy categories dạng hierarchical
   - **Cần:** Method để build tree structure cho UI

#### 🟢 MEDIUM

4. **Thiếu reorder display_order**
   - **Vấn đề:** Không có chức năng sắp xếp lại thứ tự hiển thị
   - **Cần:** (Optional) Drag & drop hoặc up/down buttons

5. **Thiếu count destinations/tours per category**
   - **Vấn đề:** Không hiển thị số lượng destinations/tours trong mỗi category
   - **Cần:** (Optional) Join và count trong `getAll()`

---

## 🔍 MODULE 3: ĐỊA ĐIỂM (DESTINATIONS)

### ✅ ĐÃ CÓ

**Model (`Destination.php`):**
- ✅ `getAll()` - Lấy danh sách với filters (category_id, status, search) và pagination
- ✅ `findById()` - Tìm destination theo ID
- ✅ `getImages()` - Lấy danh sách ảnh
- ✅ `create()` - Tạo destination mới
- ✅ `update()` - Cập nhật destination
- ✅ `addImage()` - Thêm ảnh (có set primary)
- ✅ `deleteImage()` - Xóa ảnh
- ✅ `setPrimaryImage()` - Set ảnh chính
- ✅ `delete()` - Soft delete với check usage trong itineraries
- ✅ `getForDropdown()` - Lấy destinations cho dropdown
- ✅ `getByCategory()` - Lấy destinations theo category (cho AJAX)

**Controller (`DestinationController.php`):**
- ✅ `index()` - Danh sách destinations với filters
- ✅ `create()` - Form tạo destination
- ✅ `store()` - Xử lý tạo + upload ảnh
- ✅ `edit()` - Form sửa destination
- ✅ `update()` - Xử lý update + upload ảnh mới
- ✅ `delete()` - Soft delete (chưa implement đầy đủ)
- ✅ `setPrimaryImage()` - AJAX set primary (chưa implement)
- ✅ `deleteImage()` - AJAX delete image (chưa implement)
- ✅ `handleImageUploads()` - Helper upload multiple images

### ❌ THIẾU SÓT

#### 🔴 CRITICAL

1. **Controller `delete()` chưa implement đầy đủ**
   - **Vấn đề:** Method chỉ redirect, không gọi model `delete()`
   - **Cần:** Implement logic gọi `$this->destinationModel->delete($id)`

2. **Controller `setPrimaryImage()` chưa implement**
   - **Vấn đề:** Method rỗng
   - **Cần:** 
     - Validate `image_id` và `destination_id`
     - Gọi `$this->destinationModel->setPrimaryImage($image_id, $destination_id)`
     - Return JSON response

3. **Controller `deleteImage()` chưa implement**
   - **Vấn đề:** Method rỗng
   - **Cần:**
     - Validate `image_id`
     - Gọi `$this->destinationModel->deleteImage($image_id)`
     - (Optional) Xóa file vật lý từ server
     - Return JSON response

#### 🟡 HIGH

4. **Thiếu validation category_id khi update**
   - **Vấn đề:** `update()` không validate category có tồn tại và active không
   - **Cần:** Check giống như trong `store()`

5. **Thiếu reorder images (display_order)**
   - **Vấn đề:** Không có chức năng sắp xếp lại thứ tự ảnh
   - **Cần:** (Optional) Method `reorderImages($destination_id, $image_ids)` và UI drag & drop

6. **Thiếu xóa file vật lý khi delete image**
   - **Vấn đề:** Chỉ xóa record trong DB, không xóa file
   - **Cần:** Helper function để xóa file từ server

#### 🟢 MEDIUM

7. **Thiếu bulk upload images**
   - **Vấn đề:** Chỉ upload khi create/edit, không có chức năng upload riêng
   - **Cần:** (Optional) Separate action để upload thêm ảnh cho destination đã có

8. **Thiếu image caption editing**
   - **Vấn đề:** Không có chức năng sửa caption của ảnh
   - **Cần:** (Optional) Method `updateImageCaption($image_id, $caption)`

---

## 🔍 MODULE 4: LOẠI DỊCH VỤ (SERVICE TYPES)

### ✅ ĐÃ CÓ

**Model (`ServiceType.php`):**
- ✅ `getAll()` - Lấy danh sách với filters (status, search) và pagination
- ✅ `findById()` - Tìm service type theo ID
- ✅ `findByCode()` - Tìm service type theo code
- ✅ `create()` - Tạo service type mới
- ✅ `update()` - Cập nhật service type (không cho sửa code)
- ✅ `delete()` - Soft delete với check usage trong services
- ✅ `toggleStatus()` - Toggle status
- ✅ `getForDropdown()` - Lấy service types cho dropdown

**Controller (`ServiceTypeController.php`):**
- ✅ Cần kiểm tra xem controller có tồn tại không

### ❌ THIẾU SÓT

#### 🟡 HIGH

1. **Thiếu ServiceTypeController**
   - **Vấn đề:** Chưa thấy controller file
   - **Cần:** Tạo `ServiceTypeController.php` với các actions:
     - `index()` - Danh sách service types
     - `create()` - Form tạo
     - `store()` - Xử lý tạo với validation:
       - Name required + unique
       - Code optional + unique (nếu có)
     - `edit()` - Form sửa
     - `update()` - Xử lý update
     - `delete()` - Soft delete

2. **Thiếu validation code unique khi create**
   - **Vấn đề:** Model `create()` không check code unique
   - **Cần:** Check `findByCode()` trước khi create

3. **Thiếu count services per type**
   - **Vấn đề:** Không hiển thị số lượng services trong mỗi type
   - **Cần:** (Optional) Join và count trong `getAll()`

#### 🟢 MEDIUM

4. **Thiếu default service types seeding**
   - **Vấn đề:** Không có script để tạo các service types mặc định
   - **Cần:** (Optional) Migration hoặc seeder để tạo HOTEL, RESTAURANT, VEHICLE, etc.

---

## 🔍 MODULE 5: NHÀ CUNG CẤP (SUPPLIERS)

### ✅ ĐÃ CÓ

**Model (`Supplier.php`):**
- ✅ `getAll()` - Lấy danh sách với filters (status, search) và pagination
- ✅ `findById()` - Tìm supplier theo ID
- ✅ `findByEmail()` - Tìm supplier theo email
- ✅ `findByTaxCode()` - Tìm supplier theo tax code
- ✅ `generateSupplierCode()` - Auto-generate supplier code
- ✅ `create()` - Tạo supplier mới
- ✅ `update()` - Cập nhật supplier
- ✅ `delete()` - Soft delete với check usage trong services và booking_services
- ✅ `toggleStatus()` - Toggle status
- ✅ `getForDropdown()` - Lấy suppliers cho dropdown

**Controller (`SupplierController.php`):**
- ✅ `index()` - Danh sách suppliers với filters
- ✅ `create()` - Form tạo supplier
- ✅ `store()` - Xử lý tạo với validation đầy đủ:
  - Company name required
  - Email optional + format + unique
  - Phone optional + min 10 chars
  - Tax code optional + 10 digits + unique
  - Contract dates: end >= start
- ✅ `edit()` - Form sửa supplier
- ✅ `update()` - Xử lý update với validation (exclude current supplier)
- ✅ `delete()` - Soft delete

### ❌ THIẾU SÓT

#### 🟡 HIGH

1. **Thiếu check contract expiry**
   - **Vấn đề:** Không có warning khi contract sắp hết hạn
   - **Cần:** 
     - Method `getExpiringContracts($days = 30)` - Lấy contracts sắp hết hạn
     - Hiển thị warning trong `index()` nếu contract sắp hết hạn

2. **Thiếu supplier payment history**
   - **Vấn đề:** Không có link đến payment history của supplier
   - **Cần:** (Optional) Method `getPaymentHistory($supplier_id)` và hiển thị trong `show()` view

3. **Thiếu count services per supplier**
   - **Vấn đề:** Không hiển thị số lượng services của mỗi supplier
   - **Cần:** (Optional) Join và count trong `getAll()`

#### 🟢 MEDIUM

4. **Thiếu supplier rating/review**
   - **Vấn đề:** Không có chức năng đánh giá nhà cung cấp
   - **Cần:** (Optional) Bảng `supplier_reviews` và UI

5. **Thiếu export suppliers to Excel**
   - **Vấn đề:** Không có chức năng export
   - **Cần:** (Optional) Export functionality

---

## 🔍 MODULE 6: DỊCH VỤ (SERVICES)

### ✅ ĐÃ CÓ

**Model (`Service.php`):**
- ✅ `getAll()` - Lấy danh sách với filters (service_type_id, supplier_id, status, search) và pagination
- ✅ `findById()` - Tìm service theo ID (with joins)
- ✅ `create()` - Tạo service mới (auto-generate service_code)
- ✅ `update()` - Cập nhật service
- ✅ `delete()` - Soft delete với check usage trong tour_services và booking_services
- ✅ `toggleStatus()` - Toggle status
- ✅ `getByServiceType()` - Lấy services theo service type
- ✅ `getBySupplier()` - Lấy services theo supplier
- ✅ `generateServiceCode()` - Auto-generate service code
- ✅ `findByNameAndSupplier()` - Check duplicate (name + supplier + type)

**Controller (`ServiceController.php`):**
- ✅ `index()` - Danh sách services với filters
- ✅ `create()` - Form tạo service
- ✅ `store()` - Xử lý tạo với validation đầy đủ:
  - Name, service_type_id, supplier_id required
  - Check service_type và supplier exists + active
  - Check duplicate (name + supplier + type)
  - Validate price (>= 0, range 1,000 - 1,000,000,000)
  - Validate unit (from predefined list)
- ✅ `edit()` - Form sửa service
- ✅ `update()` - Xử lý update với validation:
  - Check duplicate (exclude current service)
  - Warn nếu thay đổi supplier/type khi đang được dùng trong bookings
- ✅ `delete()` - Soft delete

### ❌ THIẾU SÓT

#### 🟡 HIGH

1. **Thiếu method `getServiceInfo()` cho AJAX**
   - **Vấn đề:** Trong `TourController` có gọi `getServiceInfo()` nhưng không thấy trong `ServiceController`
   - **Cần:** 
     - Method `getServiceInfo()` trong `ServiceController` để return JSON (id, name, unit, estimated_price)
     - Route: `?act=admin&module=services&action=getServiceInfo&id=:id`

2. **Thiếu validation availability**
   - **Vấn đề:** Database có thể có field `availability` nhưng code không xử lý
   - **Cần:** (Nếu có field) Validate và update availability status

3. **Thiếu service pricing history**
   - **Vấn đề:** Không track lịch sử thay đổi giá
   - **Cần:** (Optional) Bảng `service_price_history` để track price changes

#### 🟢 MEDIUM

4. **Thiếu bulk import services**
   - **Vấn đề:** Không có chức năng import services từ Excel/CSV
   - **Cần:** (Optional) Import functionality

5. **Thiếu service images**
   - **Vấn đề:** Không có chức năng upload ảnh cho service
   - **Cần:** (Optional) Bảng `service_images` và upload UI

---

## 📊 TÓM TẮT THIẾU SÓT THEO ĐỘ ƯU TIÊN

### 🔴 CRITICAL (Cần fix ngay)

1. **User Module:**
   - Fix validation email unique khi update
   - Thêm method `isEmailExists()` trong User model

2. **Destination Module:**
   - Implement `delete()` trong controller
   - Implement `setPrimaryImage()` trong controller
   - Implement `deleteImage()` trong controller

3. **Service Type Module:**
   - Tạo `ServiceTypeController.php` với đầy đủ CRUD actions

### 🟡 HIGH (Nên fix sớm)

1. **User Module:**
   - User activity log
   - Password reset functionality

2. **Category Module:**
   - Parent-child relationship handling
   - Check usage trước khi delete

3. **Destination Module:**
   - Validate category_id khi update
   - Xóa file vật lý khi delete image

4. **Service Type Module:**
   - Validation code unique khi create

5. **Supplier Module:**
   - Check contract expiry
   - Count services per supplier

6. **Service Module:**
   - Method `getServiceInfo()` cho AJAX
   - Validation availability (nếu có field)

### 🟢 MEDIUM (Có thể làm sau)

- Bulk actions
- Export to Excel/CSV
- Reorder functionality
- Image caption editing
- Service pricing history
- Bulk import
- Service images

---

**Kết thúc phân tích**

