# KẾ HOẠCH XÓA CATEGORY KHỎI TRANG LIST TOUR (STAFF)

## 🔍 PHÂN TÍCH VẤN ĐỀ

### Vấn đề hiện tại:
- Trang list tour (`app/views/staff/tours/index.php`) đang hiển thị category
- Category đã bị bỏ khỏi hệ thống (không còn trong database schema)
- View đang cố hiển thị `$tour['category_name'] ?? 'Chưa phân loại'` → Hiển thị "Chưa phân loại"
- Form filter có dropdown category nhưng controller không load `$categories` → Có thể gây lỗi

### Các vị trí cần sửa:

#### 1. File: `app/views/staff/tours/index.php`
- **Line 46-73:** Form filter có category dropdown (cần xóa)
- **Line 113-115:** Hiển thị category name trong table (cần xóa)
- **Line 47-48:** Form action đang dùng `act=admin&module=tours` (sai, cần sửa thành `act=staff-tours`)

#### 2. File: `app/controllers/staff/TourController.php`
- **Line 62-89:** Method `index()` không load categories (OK, không cần sửa)
- Không có filter category trong controller (OK)

#### 3. Các file khác có thể liên quan:
- `app/views/staff/tours/select_template.php` - Có hiển thị category (cần kiểm tra)
- `app/views/staff/tours/create_from_template.php` - Có category filter (cần kiểm tra)

---

## 📋 KẾ HOẠCH SỬA LỖI

### BƯỚC 1: Sửa trang list tour (`index.php`)

#### 1.1. Xóa category filter trong form
- **Line 56-66:** Xóa toàn bộ div chứa category dropdown
- **Line 50:** Sửa grid từ `grid-cols-4` thành `grid-cols-3` (vì bỏ 1 cột)
- **Line 51:** Sửa `md:col-span-2` thành `md:col-span-2` (giữ nguyên)

#### 1.2. Xóa hiển thị category trong table
- **Line 113-115:** Xóa span hiển thị category name
- Giữ lại chỉ tên tour

#### 1.3. Sửa form action
- **Line 47-48:** Sửa `act=admin&module=tours` thành `act=staff-tours`

### BƯỚC 2: Kiểm tra các file khác

#### 2.1. `select_template.php`
- Kiểm tra có hiển thị category không
- Nếu có → Xóa hoặc ẩn

#### 2.2. `create_from_template.php`
- Kiểm tra có category filter không
- Nếu có → Xóa

---

## ✅ CHECKLIST

### File: `app/views/staff/tours/index.php`
- [ ] Xóa category dropdown trong form filter (line 56-66)
- [ ] Sửa grid layout từ 4 cột thành 3 cột
- [ ] Xóa hiển thị category name trong table (line 113-115)
- [ ] Sửa form action từ `act=admin` thành `act=staff-tours`
- [ ] Kiểm tra pagination có dùng category_id không (line 149)

### File: `app/views/staff/tours/select_template.php`
- [ ] Kiểm tra có hiển thị category không
- [ ] Nếu có → Xóa

### File: `app/views/staff/tours/create_from_template.php`
- [ ] Kiểm tra có category filter không
- [ ] Nếu có → Xóa

---

## 🎯 KẾT QUẢ MONG ĐỢI

### Trước khi sửa:
```
[Form Filter]
- Search: [________]
- Category: [Dropdown -- Tất cả danh mục --]
- [Lọc dữ liệu]

[Table]
- Tên Tour
  [Custom] Tour Đà Lạt...
  Chưa phân loại  ← Cần xóa
```

### Sau khi sửa:
```
[Form Filter]
- Search: [________]
- [Lọc dữ liệu]

[Table]
- Tên Tour
  [Custom] Tour Đà Lạt...
  ← Không còn category
```

---

## ⚠️ LƯU Ý

1. **Không ảnh hưởng đến:**
   - Controller logic (không có filter category)
   - Database (category đã bị bỏ)
   - Các chức năng khác

2. **Cần kiểm tra:**
   - Form filter vẫn hoạt động sau khi xóa category
   - Pagination vẫn hoạt động
   - Search vẫn hoạt động

3. **Có thể cần sửa:**
   - Pagination link có thể có `category_id` parameter (cần xóa)

---

## 📝 FILES CẦN SỬA

1. ✅ `app/views/staff/tours/index.php` - **QUAN TRỌNG**
2. ⚠️ `app/views/staff/tours/select_template.php` - Kiểm tra
3. ⚠️ `app/views/staff/tours/create_from_template.php` - Kiểm tra

---

## 🔧 CÁC THAY ĐỔI CHI TIẾT

### 1. Form Filter (Line 46-73)
**Trước:**
```php
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="md:col-span-2">
        <input type="text" name="search" ...>
    </div>
    <div>
        <select name="category_id" ...>
            <option value="">-- Tất cả danh mục --</option>
            <?php foreach ($categories as $id => $name): ?>
                ...
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <button type="submit">Lọc dữ liệu</button>
    </div>
</div>
```

**Sau:**
```php
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="md:col-span-2">
        <input type="text" name="search" ...>
    </div>
    <div>
        <button type="submit">Lọc dữ liệu</button>
    </div>
</div>
```

### 2. Table Cell (Line 111-116)
**Trước:**
```php
<td class="px-4 py-3">
    <div class="font-medium text-gray-800 mb-1"><?= htmlspecialchars($tour['name']) ?></div>
    <span class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded">
        <?= htmlspecialchars($tour['category_name'] ?? 'Chưa phân loại') ?>
    </span>
</td>
```

**Sau:**
```php
<td class="px-4 py-3">
    <div class="font-medium text-gray-800"><?= htmlspecialchars($tour['name']) ?></div>
</td>
```

### 3. Form Action (Line 47-48)
**Trước:**
```php
<input type="hidden" name="act" value="admin">
<input type="hidden" name="module" value="tours">
```

**Sau:**
```php
<input type="hidden" name="act" value="staff-tours">
```

### 4. Pagination (Line 149)
**Trước:**
```php
<a href="?act=staff-tours&page=<?= $i ?>&search=<?= $_GET['search'] ?? '' ?>&approval_status=<?= $_GET['approval_status'] ?? '' ?>&tour_type=<?= $_GET['tour_type'] ?? '' ?>">
```

**Sau:**
```php
<a href="?act=staff-tours&page=<?= $i ?>&search=<?= $_GET['search'] ?? '' ?>&status=<?= $_GET['status'] ?? '' ?>&approval_status=<?= $_GET['approval_status'] ?? '' ?>&tour_type=<?= $_GET['tour_type'] ?? '' ?>">
```
(Thêm `status` parameter nếu cần, xóa `category_id` nếu có)

---

## 🧪 TEST CHECKLIST

Sau khi sửa, cần test:
- [ ] Form filter hoạt động (search)
- [ ] Table hiển thị đúng (không có category)
- [ ] Pagination hoạt động
- [ ] Không có lỗi PHP (undefined variable `$categories`)
- [ ] URL routing đúng (`act=staff-tours`)

