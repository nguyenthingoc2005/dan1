# PHÂN TÍCH LUỒNG DỮ LIỆU: `?act=guide-tours`

## TỔNG QUAN

URL: `http://localhost/duan1/?act=guide-tours` hiển thị danh sách các tour được phân công cho guide đang đăng nhập.

---

## 1. LUỒNG ROUTING (Entry Point)

### 1.1. Entry Point: `index.php`

```
index.php (dòng 101-104)
├─ Kiểm tra: strpos($act, 'guide-') === 0
├─ Nếu đúng → require routes/guide.php
└─ Exit
```

**Code:**

```php
if (strpos($act, 'guide-') === 0) {
    require __DIR__ . '/routes/guide.php';
    exit;
}
```

### 1.2. Route Parser: `routes/guide.php`

```
routes/guide.php
├─ require_guide() → Kiểm tra user phải là guide (hoặc admin)
├─ Parse act parameter: ?act=guide-tours → module = 'tours'
├─ Parse action: ?action=index (mặc định nếu không có)
└─ Switch module:
   └─ case 'tours':
      ├─ require TourController.php
      ├─ new Guide\TourController($pdo)
      └─ switch action:
         └─ case 'index': $controller->index()
```

**Code:**

```php
// Parse act parameter
$act = $_GET['act'] ?? '';  // 'guide-tours'
$module = str_replace('guide-', '', $act);  // 'tours'
$action = $_GET['action'] ?? 'index';  // 'index'

// Route to controller
case 'tours':
    require_once CONTROLLERS_PATH . '/guide/TourController.php';
    $controller = new Guide\TourController($pdo);
    $controller->index();
```

---

## 2. AUTHENTICATION & AUTHORIZATION

### 2.1. Kiểm tra đăng nhập

```
index.php (dòng 77)
└─ require_login() → Redirect về ?act=login nếu chưa login
```

### 2.2. Kiểm tra quyền Guide

```
routes/guide.php (dòng 9)
└─ require_guide() → Redirect về ?act=access-denied nếu không phải guide
```

**Logic:**

```php
function require_guide() {
    require_login();  // Phải login trước
    if (!is_guide()) {
        redirect('?act=access-denied');
    }
}
```

---

## 3. CONTROLLER: `TourController->index()`

### 3.1. Khởi tạo Controller

```
app/controllers/guide/TourController.php
├─ Constructor:
│  ├─ $this->db = $pdo
│  ├─ require TourSchedule model
│  ├─ require Booking model
│  ├─ $this->scheduleModel = new TourSchedule($pdo)
│  └─ $this->bookingModel = new Booking($pdo)
```

### 3.2. Method `index()` - Luồng xử lý

#### Bước 1: Kiểm tra quyền và lấy user_id

```php
require_guide();  // Double check
$user_id = get_user_id();  // Lấy từ $_SESSION['user_id']
```

#### Bước 2: Xử lý phân trang

```php
$page = $_GET['page'] ?? 1;  // Trang hiện tại
$limit = 10;  // Số record mỗi trang
```

#### Bước 3: Xây dựng filters

```php
$filters = [
    'guide_id' => $user_id  // CHỈ lấy tours được gán cho guide này
];

// Filter theo thời gian (optional)
$filter_type = $_GET['filter'] ?? 'all';  // all, upcoming, history
$today = date('Y-m-d');

if ($filter_type === 'upcoming') {
    $filters['start_date'] = $today;  // Tours từ hôm nay trở đi
} elseif ($filter_type === 'history') {
    $filters['end_date'] = date('Y-m-d', strtotime('-1 day'));  // Tours trước hôm nay
}
// Nếu 'all' → không filter thời gian
```

#### Bước 4: Gọi Model để lấy dữ liệu

```php
$result = $this->scheduleModel->getAll($filters, $page, $limit);
$schedules = $result['data'];  // Danh sách tour schedules
$total_pages = $result['pages'];  // Tổng số trang
$current_page = $result['current_page'];  // Trang hiện tại
```

#### Bước 5: Render view

```php
$page_title = 'Lịch Tour Của Tôi';
$content_file = VIEWS_PATH . '/guide/tours/index.php';
require VIEWS_PATH . '/layouts/guide_layout.php';
```

---

## 4. MODEL: `TourSchedule->getAll()`

### 4.1. File: `app/models/TourSchedule.php`

### 4.2. Method `getAll($filters, $page, $limit)`

#### Bước 1: Xây dựng WHERE clause

```php
$where = ["1=1"];  // Base condition
$params = [];

// Filter: guide_id
if (!empty($filters['guide_id'])) {
    $where[] = "ts.guide_id = :guide_id";
    $params['guide_id'] = $filters['guide_id'];
}

// Filter: start_date (>=)
if (!empty($filters['start_date'])) {
    $where[] = "ts.start_date >= :start_date";
    $params['start_date'] = $filters['start_date'];
}

// Filter: end_date (<=)
if (!empty($filters['end_date'])) {
    $where[] = "ts.start_date <= :end_date";
    $params['end_date'] = $filters['end_date'];
}
```

#### Bước 2: Đếm tổng số records

```php
$countSql = "SELECT COUNT(*) FROM tour_schedules ts WHERE " . implode(" AND ", $where);
$stmt = $this->pdo->prepare($countSql);
$stmt->execute($params);
$total = $stmt->fetchColumn();
```

#### Bước 3: Query dữ liệu với JOIN

```php
$offset = ($page - 1) * $limit;

$sql = "SELECT
    ts.*,                                    -- Tất cả fields từ tour_schedules
    t.name as tour_name,                     -- Tên tour
    t.tour_code,                             -- Mã tour
    t.duration_days,                         -- Số ngày
    t.duration_nights,                       -- Số đêm
    t.departure_location,                    -- Điểm khởi hành
    u.full_name as guide_name,               -- Tên guide
    u.phone as guide_phone,                  -- SĐT guide
    u.email as guide_email                   -- Email guide
FROM tour_schedules ts
JOIN tours t ON ts.tour_id = t.id            -- JOIN với bảng tours
LEFT JOIN users u ON ts.guide_id = u.id     -- JOIN với bảng users (guide)
WHERE " . implode(" AND ", $where) . "
ORDER BY ts.start_date ASC                   -- Sắp xếp theo ngày khởi hành
LIMIT $limit OFFSET $offset";

$stmt = $this->pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

#### Bước 4: Trả về kết quả

```php
return [
    'data' => $data,                    // Danh sách schedules
    'total' => $total,                  // Tổng số records
    'pages' => ceil($total / $limit),   // Tổng số trang
    'current_page' => $page              // Trang hiện tại
];
```

---

## 5. DATABASE QUERY - Chi tiết

### 5.1. Bảng liên quan

- `tour_schedules` (ts) - Lịch tour
- `tours` (t) - Thông tin tour
- `users` (u) - Thông tin guide

### 5.2. Cấu trúc dữ liệu trả về

Mỗi record trong `$schedules` chứa:

```php
[
    'id' => 1,                          // ID schedule
    'tour_id' => 5,                     // ID tour
    'start_date' => '2024-12-15',       // Ngày khởi hành
    'end_date' => '2024-12-20',         // Ngày kết thúc
    'quota' => 30,                      // Số chỗ
    'booked' => 25,                     // Số chỗ đã đặt
    'status' => 'open',                 // Trạng thái
    'guide_id' => 3,                    // ID guide
    'guide_notes' => 'Ghi chú...',      // Ghi chú cho guide
    'tour_name' => 'Tour Đà Lạt',       // Tên tour (từ JOIN)
    'tour_code' => 'DL-001',            // Mã tour (từ JOIN)
    'duration_days' => 5,               // Số ngày (từ JOIN)
    'duration_nights' => 4,             // Số đêm (từ JOIN)
    'departure_location' => 'Hà Nội',   // Điểm khởi hành (từ JOIN)
    'guide_name' => 'Nguyễn Văn A',     // Tên guide (từ JOIN)
    'guide_phone' => '0123456789',      // SĐT guide (từ JOIN)
    'guide_email' => 'guide@example.com' // Email guide (từ JOIN)
]
```

---

## 6. VIEW: `app/views/guide/tours/index.php`

### 6.1. Layout Structure

```
guide_layout.php
└─ include $content_file  → index.php
```

### 6.2. View Components

#### 6.2.1. Header Section

- Tiêu đề: "Lịch Tour Của Tôi"
- Filter buttons: Tất cả / Sắp tới / Đã qua

#### 6.2.2. Table Section

Hiển thị các cột:

1. **Mã Tour** (`tour_code`)
2. **Tên Tour** (`tour_name`) + Ghi chú (`guide_notes` nếu có)
3. **Khởi hành** (`start_date` - format: d/m/Y)
4. **Kết thúc** (`end_date` - format: d/m/Y)
5. **Khách** (`booked / quota`)
6. **Hành động** (Link "Xem chi tiết")

#### 6.2.3. Pagination Section

- Hiển thị nếu `$total_pages > 1`
- Giữ nguyên filter parameter khi chuyển trang

### 6.3. Code Flow trong View

```php
// Kiểm tra empty
<?php if (empty($schedules)): ?>
    // Hiển thị message "Không tìm thấy..."
<?php else: ?>
    // Loop qua $schedules
    <?php foreach ($schedules as $s): ?>
        // Render từng row
    <?php endforeach; ?>
<?php endif; ?>

// Pagination
<?php if ($total_pages > 1): ?>
    // Render pagination links
<?php endif; ?>
```

---

## 7. SƠ ĐỒ LUỒNG DỮ LIỆU TỔNG QUAN

```
┌─────────────────────────────────────────────────────────────┐
│ 1. USER REQUEST: ?act=guide-tours                           │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. index.php                                                │
│    - Parse $act = 'guide-tours'                             │
│    - Check: strpos($act, 'guide-') === 0 → TRUE             │
│    - require routes/guide.php                                │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. routes/guide.php                                         │
│    - require_guide() → Check authentication                 │
│    - Parse: module = 'tours', action = 'index'              │
│    - new Guide\TourController($pdo)                        │
│    - $controller->index()                                   │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. TourController->index()                                  │
│    - require_guide() → Double check                          │
│    - $user_id = get_user_id()                               │
│    - Build filters: ['guide_id' => $user_id]               │
│    - Apply date filter (if filter_type provided)            │
│    - $result = $scheduleModel->getAll($filters, $page, 10)  │
│    - Extract: $schedules, $total_pages, $current_page      │
│    - Set: $page_title, $content_file                        │
│    - require guide_layout.php                               │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. TourSchedule->getAll($filters, $page, $limit)            │
│    - Build WHERE clause từ $filters                         │
│    - COUNT(*) để lấy total                                  │
│    - SELECT với JOIN tours + users                          │
│    - ORDER BY start_date ASC                                │
│    - LIMIT + OFFSET                                         │
│    - Return: ['data', 'total', 'pages', 'current_page']     │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 6. DATABASE QUERY                                           │
│    SELECT ts.*, t.name, t.tour_code, ...                    │
│    FROM tour_schedules ts                                    │
│    JOIN tours t ON ts.tour_id = t.id                        │
│    LEFT JOIN users u ON ts.guide_id = u.id                  │
│    WHERE ts.guide_id = :guide_id                            │
│    ORDER BY ts.start_date ASC                               │
│    LIMIT 10 OFFSET 0                                        │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 7. guide_layout.php                                         │
│    - Include header, sidebar, etc.                          │
│    - Include $content_file (index.php)                     │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 8. index.php (View)                                         │
│    - Render header với filter buttons                       │
│    - Render table với $schedules                            │
│    - Render pagination (nếu cần)                           │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│ 9. HTML OUTPUT → Browser                                    │
└─────────────────────────────────────────────────────────────┘
```

---

## 8. CÁC THAM SỐ URL HỖ TRỢ

### 8.1. Phân trang

- `?act=guide-tours&page=2` - Trang 2

### 8.2. Filter theo thời gian

- `?act=guide-tours&filter=all` - Tất cả tours (mặc định)
- `?act=guide-tours&filter=upcoming` - Tours sắp tới (từ hôm nay)
- `?act=guide-tours&filter=history` - Tours đã qua (trước hôm nay)

### 8.3. Kết hợp

- `?act=guide-tours&filter=upcoming&page=2` - Trang 2 của tours sắp tới

---

## 9. ĐIỂM QUAN TRỌNG

### 9.1. Security

- ✅ **Double check authentication**: `require_guide()` được gọi ở cả route và controller
- ✅ **Filter by user_id**: Chỉ hiển thị tours được gán cho guide đang login
- ✅ **SQL Injection protection**: Sử dụng prepared statements với PDO

### 9.2. Performance

- ✅ **Pagination**: Chỉ load 10 records mỗi trang
- ✅ **Efficient JOIN**: JOIN trực tiếp trong query thay vì multiple queries
- ✅ **Index-friendly**: Filter theo `guide_id` và `start_date` (nên có index)

### 9.3. User Experience

- ✅ **Filter options**: Cho phép filter theo thời gian
- ✅ **Responsive design**: Table có overflow-x-auto cho mobile
- ✅ **Clear navigation**: Pagination giữ nguyên filter khi chuyển trang

---

## 10. CẢI TIẾN CÓ THỂ THỰC HIỆN

1. **Caching**: Cache kết quả query nếu dữ liệu không thay đổi thường xuyên
2. **Search**: Thêm tìm kiếm theo tên tour hoặc mã tour
3. **Sorting**: Cho phép sort theo các cột khác nhau
4. **Export**: Export danh sách ra Excel/PDF
5. **Notifications**: Hiển thị số tours mới được gán

---

## KẾT LUẬN

Luồng dữ liệu từ URL `?act=guide-tours` đến hiển thị kết quả:

1. **Routing**: index.php → routes/guide.php → TourController
2. **Authentication**: Kiểm tra login và role guide
3. **Data Retrieval**: TourSchedule model query database với filters
4. **Data Processing**: Pagination, filtering, JOIN với tours và users
5. **Rendering**: View render HTML với dữ liệu đã xử lý

Toàn bộ luồng tuân theo pattern MVC rõ ràng và có security checks đầy đủ.
