# Kế hoạch Refactor: Xóa cột `approval_status` từ bảng tours

## Mục tiêu
- Đơn giản hóa trạng thái tour: chỉ dùng 1 cột `status`
- Giữ lại các cột audit trail: `approved_by`, `approved_at`, `rejection_reason`
- Cập nhật toàn bộ code liên quan

## Trạng thái mới (1 cột `status`)

| Trạng thái | Ý nghĩa | Có thể booking? |
|------------|---------|-----------------|
| `draft` | Tour nháp, chưa gửi duyệt | ❌ |
| `pending` | Tour đang chờ duyệt | ❌ |
| `active` | Tour đã duyệt, đang hoạt động | ✅ |
| `rejected` | Tour bị từ chối | ❌ |
| `inactive` | Tour bị ẩn/tạm dừng | ❌ |

## Migration Data

### Mapping từ cũ sang mới:
```
approval_status = 'pending' → status = 'pending'
approval_status = 'approved' + status = 'active' → status = 'active'
approval_status = 'approved' + status = 'draft' → status = 'active'
approval_status = 'rejected' → status = 'rejected'
approval_status = NULL + status = 'draft' → status = 'draft' (giữ nguyên)
```

## Danh sách file cần sửa

### 1. Database Migration
- [x] `fixes/remove_approval_status_from_tours.sql` - Migration script

### 2. Model Layer (`app/models/Tour.php`)
- [ ] Dòng 46-48: Xóa filter `approval_status`
- [ ] Dòng 179: Sửa `getTemplates()` - thay `approval_status = 'approved'` bằng `status = 'active'`
- [ ] Dòng 222, 229: Xóa `approval_status` khỏi INSERT statement
- [ ] Dòng 254: Xóa `approval_status` khỏi default value

### 3. Controller Layer

#### `app/controllers/admin/TourController.php`
- [ ] Dòng 60-61: Xóa filter `approval_status` trong `index()`
- [ ] Dòng 307-308: Sửa logic tạo tour - thay `approval_status` bằng `status`
  - Nếu user chọn "pending" → `status = 'pending'`
  - Nếu user chọn "draft" → `status = 'draft'`
  - Nếu user chọn "active" → `status = 'active'`
- [ ] Dòng 970: Sửa `approve` action - chỉ set `status = 'active'` (không cần `approval_status`)
- [ ] Dòng 979: Sửa `reject` action - chỉ set `status = 'rejected'` (không cần `approval_status`)

#### `app/controllers/staff/TourController.php`
- [ ] Kiểm tra và sửa tương tự như admin controller

### 4. View Layer

#### `app/views/admin/tours/index.php`
- [ ] Dòng 112-130: Sửa logic hiển thị trạng thái
  - Xóa check `approval_status`
  - Chỉ dùng `status` để hiển thị
- [ ] Dòng 143: Sửa điều kiện hiển thị nút "Quick Approve"
  - Thay `approval_status == 'pending'` bằng `status == 'pending'`
- [ ] Dòng 172: Xóa `approval_status` khỏi URL pagination

#### `app/views/admin/tours/show.php`
- [ ] Dòng 259-261: Sửa logic hiển thị trạng thái duyệt
- [ ] Dòng 273: Sửa điều kiện hiển thị nút duyệt/từ chối
- [ ] Dòng 288: Sửa logic hiển thị nút ẩn/hiện

#### `app/views/admin/tours/create.php`
- [ ] Không cần sửa (đã dùng `status`)

#### `app/views/admin/tours/edit.php`
- [ ] Kiểm tra và sửa nếu có reference đến `approval_status`

### 5. Validation
- [ ] `app/controllers/admin/TourController.php` - `validateTourData()`
  - Dòng 577: Cập nhật validation cho phép `'pending'` và `'rejected'` trong `status`

## Chi tiết thay đổi code

### 1. Tạo Tour (`store()`)

**Trước:**
```php
'approval_status' => ($form_data['status'] ?? 'draft') == 'pending' ? 'pending' : null,
'status' => ($form_data['status'] ?? 'draft') == 'pending' ? 'draft' : ($form_data['status'] ?? 'draft'),
```

**Sau:**
```php
// Đơn giản: status = giá trị user chọn
'status' => $form_data['status'] ?? 'draft',
// Không cần approval_status nữa
```

### 2. Duyệt Tour (`changeStatus()` - approve)

**Trước:**
```php
case 'approve':
    $data = [
        'approval_status' => 'approved',
        'approved_by' => get_user_id(),
        'approved_at' => date('Y-m-d H:i:s'),
        'status' => 'active'
    ];
```

**Sau:**
```php
case 'approve':
    $data = [
        'status' => 'active',
        'approved_by' => get_user_id(),
        'approved_at' => date('Y-m-d H:i:s'),
        'rejection_reason' => null  // Xóa lý do từ chối cũ (nếu có)
    ];
```

### 3. Từ chối Tour (`changeStatus()` - reject)

**Trước:**
```php
case 'reject':
    $data = [
        'approval_status' => 'rejected',
        'rejection_reason' => $_POST['reason'] ?? '',
        'status' => 'draft'
    ];
```

**Sau:**
```php
case 'reject':
    $data = [
        'status' => 'rejected',
        'rejection_reason' => $_POST['reason'] ?? '',
        'approved_by' => null,  // Xóa thông tin duyệt cũ
        'approved_at' => null
    ];
```

### 4. Hiển thị Trạng thái

**Trước:**
```php
if (!empty($tour['approval_status'])) {
    if ($tour['approval_status'] == 'pending') {
        // Hiển thị "Chờ duyệt"
    } elseif ($tour['approval_status'] == 'approved') {
        // Hiển thị status
    }
}
```

**Sau:**
```php
switch ($tour['status']) {
    case 'pending':
        echo '<span class="...">Chờ duyệt</span>';
        break;
    case 'active':
        echo '<span class="...">Hoạt động</span>';
        break;
    case 'rejected':
        echo '<span class="...">Từ chối</span>';
        break;
    case 'draft':
        echo '<span class="...">Nháp</span>';
        break;
    case 'inactive':
        echo '<span class="...">Đã ẩn</span>';
        break;
}
```

## Thứ tự thực hiện

1. ✅ Tạo migration script
2. ⏳ Chạy migration trên database test
3. ⏳ Cập nhật Model (Tour.php)
4. ⏳ Cập nhật Controller (TourController.php)
5. ⏳ Cập nhật Views
6. ⏳ Test toàn bộ flow
7. ⏳ Chạy migration trên production

## Testing Checklist

- [ ] Tạo tour mới với status = 'draft'
- [ ] Tạo tour mới với status = 'pending'
- [ ] Tạo tour mới với status = 'active' (admin only)
- [ ] Duyệt tour (pending → active)
- [ ] Từ chối tour (pending → rejected)
- [ ] Ẩn tour (active → inactive)
- [ ] Hiện tour (inactive → active)
- [ ] Sửa tour với các trạng thái khác nhau
- [ ] Filter tours theo status
- [ ] Hiển thị danh sách tours
- [ ] Hiển thị chi tiết tour
- [ ] getTemplates() - chỉ lấy active tours

## Notes

- Giữ lại `approved_by`, `approved_at`, `rejection_reason` để audit trail
- Các tour đã có `approval_status = 'approved'` sẽ được chuyển thành `status = 'active'`
- Các tour đã có `approval_status = 'pending'` sẽ được chuyển thành `status = 'pending'`
- Các tour đã có `approval_status = 'rejected'` sẽ được chuyển thành `status = 'rejected'`

