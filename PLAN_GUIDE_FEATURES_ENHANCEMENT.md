# KẾ HOẠCH BỔ SUNG TÍNH NĂNG CHO HƯỚNG DẪN VIÊN

## 📋 PHÂN TÍCH HIỆN TRẠNG

### Vấn đề hiện tại:
1. **Validation thiếu sót:**
   - Chi phí phát sinh có thể được tạo khi tour chưa xảy ra
   - Nhật ký tour có thể được tạo khi tour chưa xảy ra
   - Cần validate: chỉ cho phép khi `tour_schedule.start_date <= today`

2. **UX chưa tối ưu:**
   - Chi phí phát sinh và nhật ký đang ở module riêng
   - Nên tích hợp vào trang Tour Detail để dễ quản lý

### Tính năng mới cần bổ sung (theo flow):
1. **Activity Check-in** - Check-in chi tiết theo hoạt động
2. **Room Assignment** - Xem phân phòng (read-only cho guide)
3. **Vehicle & Driver** - Xem thông tin xe và tài xế (read-only cho guide)

---

## 🎯 KẾ HOẠCH THỰC HIỆN

### PHASE 1: SỬA VALIDATION VÀ TÍCH HỢP (Ưu tiên cao)

#### 1.1. Sửa validation trong ExpenseController
**File:** `app/controllers/guide/ExpenseController.php`

**Thay đổi:**
- Trong `create()`: Kiểm tra `schedule['start_date'] <= today` trước khi cho phép tạo chi phí
- Trong `store()`: Thêm validation tương tự
- Hiển thị thông báo rõ ràng nếu tour chưa bắt đầu

**Code mẫu:**
```php
// Kiểm tra tour đã bắt đầu chưa
$today = date('Y-m-d');
if ($schedule['start_date'] > $today) {
    throw new \Exception("Tour chưa bắt đầu. Chỉ có thể ghi chi phí phát sinh từ ngày " . date('d/m/Y', strtotime($schedule['start_date'])) . " trở đi.");
}
```

#### 1.2. Sửa validation trong JournalController
**File:** `app/controllers/guide/JournalController.php`

**Thay đổi:**
- Trong `create()`: Chỉ cho phép chọn tour đã bắt đầu (`start_date <= today`)
- Trong `store()`: Thêm validation tương tự
- Filter danh sách tour trong form create: chỉ hiển thị tour đã bắt đầu

**Code mẫu:**
```php
// Chỉ lấy tour đã bắt đầu
$filters = [
    'guide_id' => $user_id,
    'start_date' => date('Y-m-d') // Chỉ tours đã bắt đầu
];
```

#### 1.3. Tích hợp vào Tour Detail
**File:** `app/controllers/guide/TourController.php` và `app/views/guide/tours/show.php`

**Thay đổi:**
- Thêm tabs hoặc sections trong trang tour detail:
  - Tab "Thông tin" (hiện tại)
  - Tab "Chi phí phát sinh" (mới)
  - Tab "Nhật ký tour" (mới)
  - Tab "Check-in" (mới - nếu có activity check-in)
- Load dữ liệu chi phí và nhật ký trong controller
- Hiển thị danh sách với action buttons (thêm mới, xem chi tiết, sửa, xóa)

**Cấu trúc mới:**
```
Tour Detail Page
├── Thông tin tour
├── Danh sách hành khách
├── Tabs:
│   ├── Chi phí phát sinh
│   │   ├── Danh sách chi phí
│   │   └── Button "Thêm chi phí"
│   ├── Nhật ký tour
│   │   ├── Danh sách nhật ký
│   │   └── Button "Viết nhật ký"
│   └── Check-in hoạt động (nếu có)
│       └── Danh sách checkpoint
```

---

### PHASE 2: THÊM TÍNH NĂNG ACTIVITY CHECK-IN

#### 2.1. Tạo ActivityCheckinController
**File mới:** `app/controllers/guide/ActivityCheckinController.php`

**Chức năng:**
- `index()`: Danh sách tour có checkpoint
- `checkpoints()`: Danh sách checkpoint của tour schedule
- `startCheckpoint()`: Bắt đầu checkpoint
- `checkin()`: Check-in khách cho checkpoint
- `completeCheckpoint()`: Hoàn thành checkpoint
- `summary()`: Xem tổng hợp check-in

#### 2.2. Tạo views
**Files mới:**
- `app/views/guide/activity-checkin/index.php`
- `app/views/guide/activity-checkin/checkpoints.php`
- `app/views/guide/activity-checkin/checkin.php`
- `app/views/guide/activity-checkin/summary.php`

#### 2.3. Tạo Model (nếu cần)
**File:** `app/models/ActivityCheckpoint.php` hoặc sử dụng trực tiếp trong controller

---

### PHASE 3: THÊM XEM THÔNG TIN PHỤ (READ-ONLY)

#### 3.1. Xem phân phòng (Room Assignment)
**Trong Tour Detail:**
- Thêm section "Phân phòng" (read-only)
- Hiển thị danh sách phòng theo từng đêm
- Hiển thị khách trong từng phòng

**Query:**
```sql
SELECT 
    ra.room_number,
    ra.room_type,
    i.day_number,
    sp.name AS hotel_name,
    GROUP_CONCAT(c.full_name SEPARATOR ', ') AS customers
FROM room_assignments ra
JOIN itineraries i ON ra.itinerary_id = i.id
LEFT JOIN service_providers sp ON ra.service_provider_id = sp.id
LEFT JOIN room_assignment_customers rac ON ra.id = rac.room_assignment_id
LEFT JOIN customers c ON rac.customer_id = c.id
WHERE ra.tour_schedule_id = ?
GROUP BY ra.id
ORDER BY i.day_number, ra.room_number;
```

#### 3.2. Xem thông tin xe và tài xế
**Trong Tour Detail:**
- Thêm section "Xe và tài xế" (read-only)
- Hiển thị thông tin xe, tài xế, số điện thoại

**Query:**
```sql
SELECT 
    v.vehicle_code,
    v.vehicle_type,
    v.license_plate,
    d.full_name AS driver_name,
    d.phone AS driver_phone,
    va.driver_salary,
    va.estimated_fuel_cost
FROM vehicle_assignments va
JOIN vehicles v ON va.vehicle_id = v.id
JOIN drivers d ON va.driver_id = d.id
WHERE va.tour_schedule_id = ?;
```

---

### PHASE 4: CẬP NHẬT MENU VÀ ROUTING

#### 4.1. Cập nhật MenuHelper
**File:** `common/MenuHelper.php`

**Thay đổi menu Guide:**
```php
'guide' => [
    ['icon' => 'layout-dashboard', 'label' => 'Dashboard', ...],
    ['icon' => 'calendar', 'label' => 'Lịch Tour', ...],
    ['icon' => 'check-circle', 'label' => 'Check-in', ...],
    ['icon' => 'book-open', 'label' => 'Nhật ký Tour', ...],
    ['icon' => 'dollar-sign', 'label' => 'Chi phí phát sinh', ...],
    // Có thể giữ hoặc ẩn vì đã tích hợp vào tour detail
],
```

#### 4.2. Cập nhật routes
**File:** `routes/guide.php`

**Thêm routes mới:**
```php
case 'activity-checkin':
    require_once CONTROLLERS_PATH . '/guide/ActivityCheckinController.php';
    $controller = new Guide\ActivityCheckinController($pdo);
    // ...
    break;
```

---

## 📝 CHI TIẾT IMPLEMENTATION

### 1. Sửa ExpenseController - Validation

**File:** `app/controllers/guide/ExpenseController.php`

**Thay đổi trong `create()`:**
```php
public function create()
{
    // ... existing code ...
    
    // Kiểm tra tour đã bắt đầu chưa
    $today = date('Y-m-d');
    if ($schedule['start_date'] > $today) {
        set_error("Tour chưa bắt đầu. Chỉ có thể ghi chi phí phát sinh từ ngày " . date('d/m/Y', strtotime($schedule['start_date'])) . " trở đi.");
        redirect('?act=guide-expenses');
        return;
    }
    
    // ... rest of code ...
}
```

**Thay đổi trong `store()`:**
```php
public function store()
{
    // ... existing code ...
    
    // Kiểm tra tour đã bắt đầu chưa
    $today = date('Y-m-d');
    if ($schedule['start_date'] > $today) {
        throw new \Exception("Tour chưa bắt đầu. Chỉ có thể ghi chi phí phát sinh từ ngày " . date('d/m/Y', strtotime($schedule['start_date'])) . " trở đi.");
    }
    
    // ... rest of code ...
}
```

### 2. Sửa JournalController - Validation

**File:** `app/controllers/guide/JournalController.php`

**Thay đổi trong `create()`:**
```php
public function create()
{
    // ... existing code ...
    
    // Chỉ lấy tour đã bắt đầu
    $filters = [
        'guide_id' => $user_id,
        'start_date' => date('Y-m-d') // Chỉ tours đã bắt đầu
    ];
    $schedules = $this->scheduleModel->getAll($filters, 1, 100)['data'];
    
    // ... rest of code ...
}
```

**Thay đổi trong `store()`:**
```php
public function store()
{
    // ... existing code ...
    
    // Verify guide is assigned to this schedule
    $schedule = $this->scheduleModel->getById($schedule_id);
    if (!$schedule || $schedule['guide_id'] != $user_id) {
        throw new \Exception("Bạn không được phân công tour này.");
    }
    
    // Kiểm tra tour đã bắt đầu chưa
    $today = date('Y-m-d');
    if ($schedule['start_date'] > $today) {
        throw new \Exception("Tour chưa bắt đầu. Chỉ có thể viết nhật ký từ ngày " . date('d/m/Y', strtotime($schedule['start_date'])) . " trở đi.");
    }
    
    // ... rest of code ...
}
```

### 3. Tích hợp vào Tour Detail

**File:** `app/controllers/guide/TourController.php`

**Thay đổi trong `show()`:**
```php
public function show()
{
    // ... existing code ...
    
    // Get expenses for this schedule
    require_once MODELS_PATH . '/IncurredExpense.php';
    $expenseModel = new \IncurredExpense($this->db);
    $expenses = $expenseModel->getByScheduleId($id);
    $expense_total = $expenseModel->getTotalByScheduleId($id);
    
    // Get journals for this schedule
    require_once MODELS_PATH . '/Journal.php';
    $journalModel = new \Journal($this->db);
    $journals = $journalModel->getAll(['tour_schedule_id' => $id], 1, 100);
    
    // Get room assignments (read-only)
    $room_assignments = [];
    $room_assignments_sql = "SELECT 
        ra.id,
        ra.room_number,
        ra.room_type,
        ra.actual_occupancy,
        i.day_number,
        sp.name AS hotel_name,
        GROUP_CONCAT(c.full_name SEPARATOR ', ') AS customers
    FROM room_assignments ra
    JOIN itineraries i ON ra.itinerary_id = i.id
    LEFT JOIN service_providers sp ON ra.service_provider_id = sp.id
    LEFT JOIN room_assignment_customers rac ON ra.id = rac.room_assignment_id
    LEFT JOIN customers c ON rac.customer_id = c.id
    WHERE ra.tour_schedule_id = :schedule_id
    GROUP BY ra.id
    ORDER BY i.day_number, ra.room_number";
    $stmt = $this->db->prepare($room_assignments_sql);
    $stmt->execute(['schedule_id' => $id]);
    $room_assignments = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    // Get vehicle assignments (read-only)
    $vehicle_assignments = [];
    $vehicle_assignments_sql = "SELECT 
        v.vehicle_code,
        v.vehicle_type,
        v.license_plate,
        d.full_name AS driver_name,
        d.phone AS driver_phone,
        va.driver_salary,
        va.estimated_fuel_cost
    FROM vehicle_assignments va
    JOIN vehicles v ON va.vehicle_id = v.id
    JOIN drivers d ON va.driver_id = d.id
    WHERE va.tour_schedule_id = :schedule_id";
    $stmt = $this->db->prepare($vehicle_assignments_sql);
    $stmt->execute(['schedule_id' => $id]);
    $vehicle_assignments = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    // Check if tour has started
    $today = date('Y-m-d');
    $can_add_expense = ($schedule['start_date'] <= $today);
    $can_add_journal = ($schedule['start_date'] <= $today);
    
    // ... rest of code ...
}
```

**File:** `app/views/guide/tours/show.php`

**Thêm tabs:**
```php
<!-- Tabs -->
<div class="mb-6">
    <div class="border-b border-primary-100">
        <nav class="flex space-x-4">
            <button onclick="switchTab('info')" class="tab-button active px-4 py-2 border-b-2 border-accent">
                Thông tin
            </button>
            <button onclick="switchTab('expenses')" class="tab-button px-4 py-2">
                Chi phí phát sinh
            </button>
            <button onclick="switchTab('journals')" class="tab-button px-4 py-2">
                Nhật ký tour
            </button>
            <?php if (!empty($room_assignments)): ?>
            <button onclick="switchTab('rooms')" class="tab-button px-4 py-2">
                Phân phòng
            </button>
            <?php endif; ?>
            <?php if (!empty($vehicle_assignments)): ?>
            <button onclick="switchTab('vehicles')" class="tab-button px-4 py-2">
                Xe & Tài xế
            </button>
            <?php endif; ?>
        </nav>
    </div>
</div>

<!-- Tab Content -->
<div id="tab-info" class="tab-content">
    <!-- Existing tour info -->
</div>

<div id="tab-expenses" class="tab-content hidden">
    <!-- Expenses list -->
    <?php if ($can_add_expense): ?>
    <a href="?act=guide-expenses&action=create&schedule_id=<?= $schedule['id'] ?>" class="btn-primary">
        Thêm chi phí phát sinh
    </a>
    <?php endif; ?>
    <!-- List expenses -->
</div>

<div id="tab-journals" class="tab-content hidden">
    <!-- Journals list -->
    <?php if ($can_add_journal): ?>
    <a href="?act=guide-journals&action=create&schedule_id=<?= $schedule['id'] ?>" class="btn-primary">
        Viết nhật ký
    </a>
    <?php endif; ?>
    <!-- List journals -->
</div>
```

---

## ✅ CHECKLIST

### Phase 1: Validation & Integration
- [ ] Sửa validation trong ExpenseController
- [ ] Sửa validation trong JournalController
- [ ] Tích hợp chi phí phát sinh vào Tour Detail
- [ ] Tích hợp nhật ký vào Tour Detail
- [ ] Thêm tabs trong Tour Detail view
- [ ] Test validation

### Phase 2: Activity Check-in
- [ ] Tạo ActivityCheckinController
- [ ] Tạo views cho activity check-in
- [ ] Thêm routes
- [ ] Test tính năng

### Phase 3: Read-only Info
- [ ] Thêm section phân phòng (read-only)
- [ ] Thêm section xe & tài xế (read-only)
- [ ] Test hiển thị

### Phase 4: Menu & Routing
- [ ] Cập nhật menu (nếu cần)
- [ ] Cập nhật routes
- [ ] Test navigation

---

## 📌 LƯU Ý

1. **Validation quan trọng:** Luôn kiểm tra `start_date <= today` trước khi cho phép tạo chi phí/nhật ký
2. **UX:** Tích hợp vào tour detail giúp guide dễ quản lý hơn
3. **Read-only:** Guide chỉ xem thông tin phân phòng và xe/tài xế, không được sửa
4. **Backward compatible:** Giữ nguyên các module riêng để không ảnh hưởng code cũ

---

## 🎯 KẾT QUẢ MONG ĐỢI

Sau khi hoàn thành:
- ✅ Validation đúng: chỉ cho phép tạo chi phí/nhật ký khi tour đã bắt đầu
- ✅ UX tốt hơn: tất cả thông tin tour ở một nơi
- ✅ Tính năng đầy đủ: có activity check-in, xem phân phòng, xem xe/tài xế
- ✅ Code sạch: dễ maintain và mở rộng

