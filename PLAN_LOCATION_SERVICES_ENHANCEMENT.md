# KẾ HOẠCH BỔ SUNG CHỨC NĂNG CHO MODULE LOCATION-SERVICES

## URL: `?act=admin&module=location-services`

**Ngày lập kế hoạch:** 2024-12-XX  
**Mục tiêu:** Đạt 100% chức năng so với database schema

---

## TỔNG QUAN

### Trạng thái hiện tại: **75%**

### Chức năng cần bổ sung:

1. ✅ **CRUD cho Countries** - Quản lý quốc gia
2. ✅ **CRUD cho Provinces** - Quản lý tỉnh thành
3. ✅ **Quản lý ảnh cho Destinations** - Upload, xóa, set primary, caption, display_order

---

## PHẦN 1: CRUD CHO COUNTRIES

### 1.1. Routes cần thêm (routes/admin.php)

```php
// Trong case 'location-services':
case 'create-country':
    $locationServiceController->createCountry();
    break;
case 'store-country':
    $locationServiceController->storeCountry();
    break;
case 'edit-country':
    $locationServiceController->editCountry();
    break;
case 'update-country':
    $locationServiceController->updateCountry();
    break;
case 'delete-country':
    $locationServiceController->deleteCountry();
    break;
case 'toggle-country-status':
    $locationServiceController->toggleCountryStatus();
    break;
```

### 1.2. Methods cần implement (LocationServiceController.php)

#### 1.2.1. `createCountry()`
- **Mục đích:** Hiển thị form tạo country mới
- **View:** `app/views/admin/location-services/create-country.php`
- **Fields:**
  - `code` (required, unique, max 10 chars, uppercase)
  - `name` (required, max 100 chars)
  - `name_en` (optional, max 100 chars)
  - `status` (enum: active/inactive, default: active)
  - `display_order` (int, default: 0)

#### 1.2.2. `storeCountry()`
- **Mục đích:** Xử lý tạo country mới
- **Validation:**
  - Code: required, unique, uppercase, max 10 chars
  - Name: required, max 100 chars
  - Name_en: optional, max 100 chars
  - Display_order: int >= 0
- **Model:** `$this->countryModel->create($data)`
- **Redirect:** `?act=admin&module=location-services&country_id={id}`

#### 1.2.3. `editCountry()`
- **Mục đích:** Hiển thị form sửa country
- **View:** `app/views/admin/location-services/edit-country.php`
- **Note:** Code không được sửa (readonly)

#### 1.2.4. `updateCountry()`
- **Mục đích:** Xử lý cập nhật country
- **Validation:** Tương tự storeCountry (trừ code)
- **Model:** `$this->countryModel->update($id, $data)`
- **Redirect:** `?act=admin&module=location-services&country_id={id}`

#### 1.2.5. `deleteCountry()`
- **Mục đích:** Xóa country (soft delete)
- **Validation:** Kiểm tra có provinces không (không cho xóa nếu có)
- **Model:** `$this->countryModel->delete($id)`
- **Redirect:** `?act=admin&module=location-services`

#### 1.2.6. `toggleCountryStatus()`
- **Mục đích:** Toggle status active/inactive
- **Model:** `$this->countryModel->toggleStatus($id)`
- **Response:** JSON hoặc redirect

### 1.3. Views cần tạo

#### 1.3.1. `create-country.php`
- Form tạo country mới
- Layout tương tự `create-provider.php`
- Fields: code, name, name_en, status, display_order

#### 1.3.2. `edit-country.php`
- Form sửa country
- Code hiển thị readonly
- Fields: name, name_en, status, display_order

### 1.4. Cập nhật UI hiện tại

#### 1.4.1. `index.php` (Tree view)
- Thêm nút "Thêm quốc gia" ở sidebar
- Thêm nút "Sửa" và "Xóa" cho mỗi country trong tree
- Thêm nút "Toggle status" (icon on/off)

---

## PHẦN 2: CRUD CHO PROVINCES

### 2.1. Routes cần thêm (routes/admin.php)

```php
// Trong case 'location-services':
case 'create-province':
    $locationServiceController->createProvince();
    break;
case 'store-province':
    $locationServiceController->storeProvince();
    break;
case 'edit-province':
    $locationServiceController->editProvince();
    break;
case 'update-province':
    $locationServiceController->updateProvince();
    break;
case 'delete-province':
    $locationServiceController->deleteProvince();
    break;
case 'toggle-province-status':
    $locationServiceController->toggleProvinceStatus();
    break;
```

### 2.2. Methods cần implement (LocationServiceController.php)

#### 2.2.1. `createProvince()`
- **Mục đích:** Hiển thị form tạo province mới
- **View:** `app/views/admin/location-services/create-province.php`
- **Required:** `country_id` (từ URL parameter)
- **Fields:**
  - `country_id` (hidden, từ URL)
  - `code` (optional, unique, max 20 chars)
  - `name` (required, max 100 chars)
  - `name_en` (optional, max 100 chars)
  - `status` (enum: active/inactive, default: active)
  - `display_order` (int, default: 0)

#### 2.2.2. `storeProvince()`
- **Mục đích:** Xử lý tạo province mới
- **Validation:**
  - Country_id: required, exists, active
  - Code: optional, unique, max 20 chars
  - Name: required, max 100 chars
  - Name_en: optional, max 100 chars
  - Display_order: int >= 0
- **Model:** `$this->provinceModel->create($data)`
- **Redirect:** `?act=admin&module=location-services&country_id={country_id}&province_id={id}`

#### 2.2.3. `editProvince()`
- **Mục đích:** Hiển thị form sửa province
- **View:** `app/views/admin/location-services/edit-province.php`
- **Note:** Code và country_id không được sửa (readonly)

#### 2.2.4. `updateProvince()`
- **Mục đích:** Xử lý cập nhật province
- **Validation:** Tương tự storeProvince (trừ code và country_id)
- **Model:** `$this->provinceModel->update($id, $data)`
- **Redirect:** `?act=admin&module=location-services&country_id={country_id}&province_id={id}`

#### 2.2.5. `deleteProvince()`
- **Mục đích:** Xóa province (soft delete)
- **Validation:** Kiểm tra có destinations hoặc service_providers không
- **Model:** `$this->provinceModel->delete($id)`
- **Redirect:** `?act=admin&module=location-services&country_id={country_id}`

#### 2.2.6. `toggleProvinceStatus()`
- **Mục đích:** Toggle status active/inactive
- **Model:** `$this->provinceModel->toggleStatus($id)`
- **Response:** JSON hoặc redirect

### 2.3. Views cần tạo

#### 2.3.1. `create-province.php`
- Form tạo province mới
- Layout tương tự `create-provider.php`
- Fields: country_id (hidden), code, name, name_en, status, display_order

#### 2.3.2. `edit-province.php`
- Form sửa province
- Code và country_id hiển thị readonly
- Fields: name, name_en, status, display_order

### 2.4. Cập nhật UI hiện tại

#### 2.4.1. `index.php` (Tree view)
- Thêm nút "Thêm tỉnh thành" khi chọn country
- Thêm nút "Sửa" và "Xóa" cho mỗi province trong tree
- Thêm nút "Toggle status" (icon on/off)

---

## PHẦN 3: QUẢN LÝ ẢNH CHO DESTINATIONS

### 3.1. Routes cần thêm (routes/admin.php)

```php
// Trong case 'location-services':
case 'upload-destination-image':
    $locationServiceController->uploadDestinationImage();
    break;
case 'delete-destination-image':
    $locationServiceController->deleteDestinationImage();
    break;
case 'set-primary-destination-image':
    $locationServiceController->setPrimaryDestinationImage();
    break;
case 'update-destination-image-caption':
    $locationServiceController->updateDestinationImageCaption();
    break;
case 'reorder-destination-images':
    $locationServiceController->reorderDestinationImages();
    break;
```

### 3.2. Methods cần implement (LocationServiceController.php)

#### 3.2.1. `uploadDestinationImage()`
- **Mục đích:** Upload ảnh cho destination
- **Method:** POST (multipart/form-data)
- **Parameters:**
  - `destination_id` (required)
  - `images[]` (file array)
  - `is_primary` (optional, boolean)
- **Validation:**
  - File type: jpg, jpeg, png, webp
  - File size: max 5MB
  - Image dimensions: validate
- **Model:** `$this->destinationModel->addImage($destination_id, $image_url, $is_primary)`
- **Response:** JSON với thông tin ảnh đã upload

#### 3.2.2. `deleteDestinationImage()`
- **Mục đích:** Xóa ảnh destination
- **Method:** GET hoặc POST
- **Parameters:** `image_id` (required)
- **Model:** `$this->destinationModel->deleteImage($image_id)`
- **Note:** Xóa file vật lý nếu cần
- **Response:** JSON success/error

#### 3.2.3. `setPrimaryDestinationImage()`
- **Mục đích:** Set ảnh làm primary
- **Method:** POST
- **Parameters:** `image_id` (required)
- **Model:** `$this->destinationModel->setPrimaryImage($image_id)`
- **Response:** JSON success/error

#### 3.2.4. `updateDestinationImageCaption()`
- **Mục đích:** Cập nhật caption cho ảnh
- **Method:** POST
- **Parameters:**
  - `image_id` (required)
  - `caption` (optional, max 255 chars)
- **Model:** Cần thêm method `updateImageCaption()` trong Destination model
- **Response:** JSON success/error

#### 3.2.5. `reorderDestinationImages()`
- **Mục đích:** Sắp xếp lại thứ tự ảnh
- **Method:** POST
- **Parameters:** `image_ids[]` (array of image IDs in order)
- **Model:** Cần thêm method `reorderImages()` trong Destination model
- **Response:** JSON success/error

### 3.3. Model methods cần bổ sung (Destination.php)

#### 3.3.1. `updateImageCaption($image_id, $caption)`
```php
public function updateImageCaption($image_id, $caption)
{
    // UPDATE destination_images SET caption = :caption WHERE id = :id
}
```

#### 3.3.2. `reorderImages($destination_id, $image_ids)`
```php
public function reorderImages($destination_id, $image_ids)
{
    // UPDATE destination_images SET display_order = :order WHERE id = :id
}
```

### 3.4. Views cần cập nhật

#### 3.4.1. `edit-destination.php`
- Thêm section "Quản lý ảnh"
- Hiển thị gallery ảnh hiện có
- Upload multiple images
- Drag & drop để sắp xếp
- Set primary image
- Edit caption
- Delete image

#### 3.4.2. Component: `destination-image-gallery.php` (mới)
- Component hiển thị và quản lý gallery ảnh
- Có thể tái sử dụng

### 3.5. JavaScript cần bổ sung

- Upload ảnh với preview
- Drag & drop để reorder
- Set primary image
- Edit caption inline
- Delete với confirmation

---

## THỨ TỰ THỰC HIỆN (ƯU TIÊN)

### Phase 1: CRUD Countries (Ưu tiên cao)
1. ✅ Thêm routes
2. ✅ Implement methods trong controller
3. ✅ Tạo views (create, edit)
4. ✅ Cập nhật UI tree view
5. ✅ Test

**Ước tính:** 2-3 giờ

### Phase 2: CRUD Provinces (Ưu tiên cao)
1. ✅ Thêm routes
2. ✅ Implement methods trong controller
3. ✅ Tạo views (create, edit)
4. ✅ Cập nhật UI tree view
5. ✅ Test

**Ước tính:** 2-3 giờ

### Phase 3: Quản lý ảnh Destinations (Ưu tiên trung bình)
1. ✅ Thêm routes
2. ✅ Bổ sung model methods
3. ✅ Implement controller methods
4. ✅ Tạo component gallery
5. ✅ Cập nhật edit-destination view
6. ✅ JavaScript cho upload/reorder
7. ✅ Test

**Ước tính:** 4-5 giờ

---

## TỔNG THỜI GIAN ƯỚC TÍNH

- **Phase 1 (Countries):** 2-3 giờ
- **Phase 2 (Provinces):** 2-3 giờ
- **Phase 3 (Destination Images):** 4-5 giờ

**Tổng:** 8-11 giờ

---

## CHECKLIST IMPLEMENTATION

### Countries CRUD
- [ ] Routes trong admin.php
- [ ] Method: createCountry()
- [ ] Method: storeCountry()
- [ ] Method: editCountry()
- [ ] Method: updateCountry()
- [ ] Method: deleteCountry()
- [ ] Method: toggleCountryStatus()
- [ ] View: create-country.php
- [ ] View: edit-country.php
- [ ] Cập nhật index.php (tree view)
- [ ] Test tạo country
- [ ] Test sửa country
- [ ] Test xóa country
- [ ] Test toggle status

### Provinces CRUD
- [ ] Routes trong admin.php
- [ ] Method: createProvince()
- [ ] Method: storeProvince()
- [ ] Method: editProvince()
- [ ] Method: updateProvince()
- [ ] Method: deleteProvince()
- [ ] Method: toggleProvinceStatus()
- [ ] View: create-province.php
- [ ] View: edit-province.php
- [ ] Cập nhật index.php (tree view)
- [ ] Test tạo province
- [ ] Test sửa province
- [ ] Test xóa province
- [ ] Test toggle status

### Destination Images
- [ ] Routes trong admin.php
- [ ] Model method: updateImageCaption()
- [ ] Model method: reorderImages()
- [ ] Method: uploadDestinationImage()
- [ ] Method: deleteDestinationImage()
- [ ] Method: setPrimaryDestinationImage()
- [ ] Method: updateDestinationImageCaption()
- [ ] Method: reorderDestinationImages()
- [ ] Component: destination-image-gallery.php
- [ ] Cập nhật edit-destination.php
- [ ] JavaScript: upload với preview
- [ ] JavaScript: drag & drop reorder
- [ ] JavaScript: set primary
- [ ] JavaScript: edit caption
- [ ] JavaScript: delete image
- [ ] Test upload ảnh
- [ ] Test xóa ảnh
- [ ] Test set primary
- [ ] Test reorder
- [ ] Test edit caption

---

## GHI CHÚ

1. **Models đã sẵn sàng:** Country và Province models đã có đầy đủ methods (create, update, delete, toggleStatus)
2. **Destination model:** Đã có methods cơ bản (getImages, addImage, deleteImage, setPrimaryImage), cần bổ sung updateImageCaption và reorderImages
3. **UI/UX:** Cần đảm bảo giao diện nhất quán với các form hiện có
4. **Validation:** Cần validate kỹ trước khi lưu database
5. **Error handling:** Cần xử lý lỗi đầy đủ và hiển thị thông báo rõ ràng

---

## KẾT QUẢ MONG ĐỢI

Sau khi hoàn thành, module `location-services` sẽ có:
- ✅ **100%** chức năng so với database schema
- ✅ Quản lý đầy đủ Countries, Provinces, Service Providers, Services, Prices, Destinations
- ✅ Quản lý ảnh cho Destinations
- ✅ UI/UX nhất quán và dễ sử dụng

