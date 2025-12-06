# THAY ĐỔI: BỎ TIMELINE CHI TIẾT, KHÔI PHỤC DỊCH VỤ THEO NGÀY, TÍCH HỢP TINYMCE

## TÓM TẮT
- ❌ Bỏ bảng `itinerary_timelines` (timeline chi tiết)
- ✅ Giữ lại bảng `itinerary_day_services` (dịch vụ theo ngày)
- ✅ Tích hợp TinyMCE vào phần mô tả lịch trình để có thể thêm ảnh và format text

## 1. DATABASE CHANGES

### 1.1. File SQL để DROP bảng
**File:** `drop_itinerary_timelines.sql`

Chạy file này để xóa bảng `itinerary_timelines` và index liên quan:
```sql
-- Drop index trước
DROP INDEX IF EXISTS `idx_itinerary_time_order` ON `itinerary_timelines`;

-- Drop bảng
DROP TABLE IF EXISTS `itinerary_timelines`;
```

### 1.2. Cập nhật setup_database_complete.sql
- ✅ Đã bỏ phần CREATE TABLE cho `itinerary_timelines`
- ✅ Đã bỏ index `idx_itinerary_time_order`
- ✅ Giữ lại bảng `itinerary_day_services` và index của nó

## 2. CODE CHANGES

### 2.1. Layout (admin_layout.php)
- ✅ Đã thêm TinyMCE script:
```html
<script src="<?= BASE_URL ?>/tinymce/js/tinymce/tinymce.min.js"></script>
```

### 2.2. Form tạo tour (create.php)

#### 2.2.1. Bỏ phần Timeline Manager
- ❌ Bỏ component `itinerary-manager.php` (không load nữa)
- ❌ Bỏ các functions liên quan đến timeline:
  - `loadItineraryManagerForDay()`
  - `saveTimelineItem()`
  - `openAddTimelineModal()`
  - `closeAddTimelineModal()`
  - `loadServiceInfoForTimeline()`
  - `calculateTimelineServiceTotal()`

#### 2.2.2. Tích hợp TinyMCE
- ✅ Thêm function `initTinyMCEForItinerary(dayNumber)` để khởi tạo TinyMCE cho mỗi textarea mô tả
- ✅ TinyMCE được khởi tạo tự động khi generate itinerary days
- ✅ Cấu hình TinyMCE:
  - Plugins: advlist, autolink, lists, link, image, charmap, preview, anchor, searchreplace, visualblocks, code, fullscreen, insertdatetime, media, table, code, help, wordcount
  - Toolbar: format, bold, italic, align, lists, image, link, code
  - Language: Vietnamese
  - Upload images: `?act=admin&module=tours&action=uploadImage`

#### 2.2.3. Khôi phục phần Dịch vụ theo ngày
- ✅ Giữ lại phần "Dịch vụ theo ngày" trong mỗi ngày
- ✅ Modal thêm dịch vụ được tạo động bằng JavaScript
- ✅ Functions quản lý dịch vụ:
  - `openAddServiceModal(dayNumber)` - Mở modal thêm dịch vụ
  - `createServiceModal(dayNumber)` - Tạo modal HTML động
  - `saveDayService(event, dayNumber)` - Lưu dịch vụ vào danh sách
  - `updateDayServiceTotal(dayNumber)` - Cập nhật tổng giá dịch vụ
  - `loadServiceInfoForDay(dayNumber)` - Load thông tin dịch vụ khi chọn
  - `buildServiceOptions(dayNumber)` - Build options cho dropdown dịch vụ
  - `buildServiceProviderOptions(dayNumber)` - Build options cho dropdown nhà dịch vụ

#### 2.2.4. Validation
- ✅ Sửa validation Step 2: Kiểm tra mô tả lịch trình (thay vì timeline)
- ✅ Lấy nội dung từ TinyMCE editor nếu đã khởi tạo
- ✅ Bỏ validation timeline

### 2.3. Component itinerary-manager.php
- ⚠️ Component này không còn được sử dụng nữa
- Có thể xóa hoặc giữ lại để tham khảo

## 3. CẤU TRÚC FORM MỚI

### Step 2: Lịch trình
Mỗi ngày bao gồm:
1. **Tiêu đề ngày** (input text)
2. **Điểm đến** (select dropdown)
3. **Mô tả lịch trình** (TinyMCE editor - có thể thêm ảnh)
4. **Dịch vụ theo ngày** (section):
   - Button "Thêm dịch vụ"
   - Danh sách dịch vụ đã thêm
   - Tổng giá dịch vụ (chỉ tính các dịch vụ được đánh dấu "Bao gồm")

## 4. CÁCH SỬ DỤNG

### 4.1. Chạy SQL để xóa bảng
```bash
mysql -u username -p database_name < drop_itinerary_timelines.sql
```

### 4.2. Test form tạo tour
1. Vào form tạo tour
2. Nhập số ngày ở Step 1
3. Ở Step 2, mỗi ngày sẽ có:
   - TinyMCE editor cho mô tả (có thể thêm ảnh)
   - Section "Dịch vụ theo ngày" với button "Thêm dịch vụ"
4. Click "Thêm dịch vụ" → Modal hiển thị → Chọn dịch vụ → Nhập thông tin → Lưu
5. Dịch vụ sẽ hiển thị trong danh sách và tự động tính tổng

## 5. LƯU Ý

- ✅ TinyMCE cần được load trước khi khởi tạo editor
- ✅ Mỗi textarea mô tả có ID duy nhất: `itinerary-description-day-{dayNumber}`
- ✅ TinyMCE tự động save content khi thay đổi
- ✅ Khi submit form, cần lấy content từ TinyMCE bằng `tinymce.get(id).getContent()`
- ⚠️ Cần implement endpoint `uploadImage` để upload ảnh trong TinyMCE

## 6. FILES CHANGED

1. `setup_database_complete.sql` - Bỏ bảng itinerary_timelines
2. `drop_itinerary_timelines.sql` - File SQL để drop bảng (mới)
3. `app/views/layouts/admin_layout.php` - Thêm TinyMCE script
4. `app/views/admin/tours/create.php` - Bỏ timeline, thêm TinyMCE, khôi phục day services

## 7. TODO

- [ ] Implement endpoint `uploadImage` cho TinyMCE
- [ ] Test TinyMCE với nhiều ngày
- [ ] Test thêm/xóa dịch vụ theo ngày
- [ ] Test validation mô tả lịch trình
- [ ] Test lưu form với TinyMCE content

