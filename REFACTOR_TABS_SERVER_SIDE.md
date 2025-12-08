# REFACTOR: Chuyển từ JavaScript Tabs sang Server-Side Tabs

## YÊU CẦU

- Không dùng JavaScript để show/hide tabs
- Khi click tab → reload trang với `?tab=xxx` trong URL
- Controller chỉ load dữ liệu cho tab được chọn
- View chỉ render content của tab active

## CÁC THAY ĐỔI CẦN THỰC HIỆN

### 1. Controller Changes

- Đọc `$_GET['tab']` và validate
- Chỉ load dữ liệu cho tab được chọn
- Pass `$active_tab` to view

### 2. View Changes

- Thay `<button>` thành `<a href>`
- Chỉ render content của tab active (không dùng hidden class)
- Loại bỏ JavaScript tab switching

## CODE CHANGES

### Controller: app/controllers/guide/TourController.php

```php
// Get active tab from URL
$active_tab = $_GET['tab'] ?? 'tour-info';
$allowed_tabs = ['tour-info', 'checkin', 'expenses', 'journals', 'services', 'passengers', 'rooms', 'vehicles'];
if (!in_array($active_tab, $allowed_tabs)) {
    $active_tab = 'tour-info';
}

// Chỉ load dữ liệu cho tab được chọn
if ($active_tab === 'passengers') {
    // Load bookings và passengers
}
if ($active_tab === 'checkin') {
    // Load bookings và checkin data
}
// ... etc
```

### View: app/views/guide/tours/show.php

```php
<?php
$active_tab = $_GET['tab'] ?? 'tour-info';
$base_url = "?act=guide-tours&action=show&id=" . $schedule['id'];
?>

<!-- Tab Navigation - Links thay vì buttons -->
<a href="<?= $base_url ?>&tab=tour-info"
    class="<?= $active_tab === 'tour-info' ? 'active-class' : 'inactive-class' ?>">
    Thông tin Tour
</a>

<!-- Render content based on active tab -->
<?php if ($active_tab === 'tour-info'): ?>
    <!-- Tour info content -->
<?php elseif ($active_tab === 'checkin'): ?>
    <!-- Checkin content -->
<?php endif; ?>
```
