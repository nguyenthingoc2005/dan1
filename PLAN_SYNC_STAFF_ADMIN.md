# KẾ HOẠCH ĐỒNG BỘ STAFF VỚI ADMIN - TOURS MODULE

## 📋 PHÂN TÍCH HIỆN TRẠNG

### ✅ Đã có và đúng:
1. **Controller**: `app/controllers/staff/TourController.php` - Đã cập nhật đầy đủ
2. **Routes**: `routes/staff.php` - Đã có đầy đủ routes
3. **Component**: `app/views/components/policy-selector.php` - Dùng chung
4. **View create.php**: Đã copy mới từ admin

### ❌ Còn thiếu/sai:
1. **View create_from_template.php**: Staff version cũ hơn admin (thiếu logic mới)
2. **View edit.php**: Staff version cũ hơn admin (thiếu TinyMCE, day services, policies)
3. **View select_template.php**: Cần kiểm tra xem có giống admin không
4. **View show.php**: Cần kiểm tra xem có giống admin không
5. **View index.php**: Cần kiểm tra xem có giống admin không

---

## 🎯 KẾ HOẠCH THỰC HIỆN

### BƯỚC 1: Copy và cập nhật các view files

#### 1.1. create_from_template.php
- [ ] Copy từ `app/views/admin/tours/create_from_template.php`
- [ ] Thay `?act=admin&module=tours` → `?act=staff-tours`
- [ ] Thay `require_admin()` → `require_staff_or_admin()`
- [ ] Điều chỉnh status options (chỉ draft/pending)
- [ ] Kiểm tra các AJAX endpoints

#### 1.2. edit.php
- [ ] Copy từ `app/views/admin/tours/edit.php`
- [ ] Thay tất cả URL admin → staff
- [ ] Điều chỉnh status options
- [ ] Đảm bảo có đầy đủ:
  - TinyMCE editor cho itinerary
  - Day services management
  - Policies management
  - Fixed costs
  - Pricing calculation

#### 1.3. select_template.php
- [ ] Copy từ `app/views/admin/tours/select_template.php`
- [ ] Thay URL admin → staff

#### 1.4. show.php
- [ ] Copy từ `app/views/admin/tours/show.php`
- [ ] Thay URL admin → staff
- [ ] Loại bỏ các action chỉ admin có (changeStatus, delete)

#### 1.5. index.php
- [ ] Copy từ `app/views/admin/tours/index.php`
- [ ] Thay URL admin → staff
- [ ] Điều chỉnh filters (chỉ hiển thị tours của staff)
- [ ] Loại bỏ các action chỉ admin có

---

### BƯỚC 2: Kiểm tra và cập nhật JavaScript/AJAX

#### 2.1. Các AJAX endpoints cần thay đổi:
- [ ] `getDestinations` → `?act=staff-tours&action=getDestinations`
- [ ] `getServiceInfo` → `?act=staff-tours&action=getServiceInfo`
- [ ] `getServiceProviders` → `?act=staff-tours&action=getServiceProviders`
- [ ] `createPolicy` → `?act=staff-tours&action=createPolicy`
- [ ] `getPolicy` → `?act=staff-tours&action=getPolicy`
- [ ] `loadDayServicesEditor` → `?act=staff-tours&action=loadDayServicesEditor`
- [ ] `loadItineraryManager` → `?act=staff-tours&action=loadItineraryManager`
- [ ] `saveFormSession` → `?act=staff-tours&action=saveFormSession`
- [ ] `clearTourSession` → `?act=staff-tours&action=clearTourSession`
- [ ] `uploadImage` → `?act=staff-tours&action=uploadImage`

#### 2.2. TinyMCE configuration:
- [ ] Đảm bảo `images_upload_url` trỏ đúng staff endpoint
- [ ] Kiểm tra các callback functions

---

### BƯỚC 3: Kiểm tra Controller methods

#### 3.1. Đảm bảo các method sau có trong Staff TourController:
- [x] `index()` - ✅ Đã có
- [x] `selectTemplate()` - ✅ Đã có
- [x] `create()` - ✅ Đã có
- [x] `createFromTemplate()` - ✅ Đã có
- [x] `store()` - ✅ Đã có
- [x] `show()` - ✅ Đã có
- [x] `edit()` - ✅ Đã có
- [x] `update()` - ✅ Đã có
- [x] `getServiceInfo()` - ✅ Đã có
- [x] `getDestinations()` - ✅ Đã có
- [x] `getServiceProviders()` - ✅ Đã có
- [x] `createPolicy()` - ✅ Đã có
- [x] `getPolicy()` - ✅ Đã có
- [x] `loadDayServicesEditor()` - ✅ Đã có
- [x] `loadItineraryManager()` - ✅ Đã có
- [x] `saveFormSession()` - ✅ Đã có
- [x] `clearTourSession()` - ✅ Đã có
- [x] `uploadImage()` - ✅ Đã có

#### 3.2. Các method KHÔNG có (đúng - chỉ admin):
- [x] `changeStatus()` - ❌ Không có (đúng)
- [x] `delete()` - ❌ Không có (đúng)

---

### BƯỚC 4: Kiểm tra Routes

#### 4.1. Đảm bảo routes/staff.php có đầy đủ:
- [x] `index` - ✅
- [x] `selectTemplate` - ✅
- [x] `create` - ✅
- [x] `createFromTemplate` - ✅
- [x] `store` - ✅
- [x] `show` - ✅
- [x] `edit` - ✅
- [x] `update` - ✅
- [x] `getDestinations` - ✅
- [x] `getServiceInfo` - ✅
- [x] `getServiceProviders` - ✅
- [x] `createPolicy` - ✅
- [x] `getPolicy` - ✅
- [x] `loadDayServicesEditor` - ✅
- [x] `loadItineraryManager` - ✅
- [x] `saveFormSession` - ✅
- [x] `clearTourSession` - ✅
- [x] `uploadImage` - ✅

---

## 🔧 CÁC THAY ĐỔI CẦN THỰC HIỆN

### Pattern thay đổi chung:

1. **URL Pattern:**
   ```php
   // Từ:
   ?act=admin&module=tours&action=XXX
   // Thành:
   ?act=staff-tours&action=XXX
   ```

2. **Access Control:**
   ```php
   // Từ:
   if (!is_admin()) redirect('?act=access-denied');
   // Thành:
   require_staff_or_admin();
   ```

3. **Status Options:**
   ```php
   // Từ:
   <option value="active">Hoạt động (Active)</option>
   // Thành:
   <!-- Loại bỏ option active -->
   <p class="text-xs text-gray-500 mt-1">Staff chỉ có thể tạo nháp hoặc gửi duyệt.</p>
   ```

4. **Action Buttons:**
   ```php
   // Loại bỏ các button:
   - changeStatus
   - delete
   - approve/reject (nếu có)
   ```

---

## 📝 CHECKLIST HOÀN THÀNH

### Views:
- [ ] create.php - ✅ Đã hoàn thành
- [ ] create_from_template.php - ⏳ Cần cập nhật
- [ ] edit.php - ⏳ Cần cập nhật
- [ ] select_template.php - ⏳ Cần kiểm tra
- [ ] show.php - ⏳ Cần kiểm tra
- [ ] index.php - ⏳ Cần kiểm tra

### Controller:
- [x] TourController.php - ✅ Đã hoàn thành

### Routes:
- [x] routes/staff.php - ✅ Đã hoàn thành

### Components:
- [x] policy-selector.php - ✅ Dùng chung

---

## 🚀 THỨ TỰ THỰC HIỆN

1. **Ưu tiên cao**: `edit.php` (quan trọng nhất, có nhiều logic phức tạp)
2. **Ưu tiên trung bình**: `create_from_template.php`, `select_template.php`
3. **Ưu tiên thấp**: `show.php`, `index.php` (chủ yếu là hiển thị)

---

## ⚠️ LƯU Ý

1. **Backup**: Luôn backup file cũ trước khi thay thế
2. **Testing**: Test từng file sau khi cập nhật
3. **Consistency**: Đảm bảo tất cả URL và logic nhất quán
4. **Staff Restrictions**: Nhớ giữ các giới hạn của staff (ownership, status, actions)

---

## 📌 KẾT LUẬN

Sau khi hoàn thành tất cả các bước trên, staff tours module sẽ có đầy đủ tính năng như admin, với các giới hạn phù hợp:
- ✅ Đầy đủ CRUD operations
- ✅ Đầy đủ AJAX endpoints
- ✅ Đầy đủ UI components
- ✅ Giới hạn status (draft/pending)
- ✅ Giới hạn ownership (chỉ tours của mình)
- ✅ Không có changeStatus/delete

