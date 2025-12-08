# KẾ HOẠCH REDESIGN TAB SYSTEM CHO TOUR DETAIL

## 📋 PHÂN TÍCH HIỆN TRẠNG

### Vấn đề hiện tại:
1. **Đang dùng anchor links (#id)**: Click tab sẽ scroll đến section, không ẩn/hiện content
2. **Check-in chưa có trong tour detail**: Phải vào module riêng
3. **Nội dung rải rác**: Check-in, nhật ký, chi phí ở các tab riêng, không tập trung

### Yêu cầu mới:
1. **Tab system thực sự**: Click tab nào chỉ hiển thị content của tab đó, ẩn các tab khác
2. **Tab "Thông tin Tour" bao gồm**: 
   - Thông tin tour cơ bản
   - Check-in (thêm mới)
   - Nhật ký tour (thêm mới)
   - Chi phí phát sinh (thêm mới)
3. **Các tab khác**: Dịch vụ, Hành khách, Phân phòng, Xe & Tài xế

---

## 🎯 KẾ HOẠCH THỰC HIỆN

### PHASE 1: Thêm dữ liệu Check-in vào Controller

**File:** `app/controllers/guide/TourController.php`

**Thay đổi:**
- Thêm logic lấy check-in data tương tự CheckinController
- Lấy passengers với check-in status
- Lấy check-in stats

**Code mẫu:**
```php
// Get check-in data
require_once MODELS_PATH . '/Checkin.php';
$checkinModel = new \Checkin($this->db);

// Get passengers with check-in status
$checkin_passengers = [];
foreach ($bookings as $booking) {
    $p_list = $this->bookingModel->getPassengers($booking['id']);
    foreach ($p_list as $p) {
        if (!empty($p['customer_id']) && !empty($p['id'])) {
            $checkin = $checkinModel->getCustomerCheckin($booking['id'], $p['id']);
            $p['booking_id'] = $booking['id'];
            $p['booking_code'] = $booking['booking_code'];
            $p['checkin_status'] = $checkin ? $checkin['status'] : null;
            $p['checkin_time'] = $checkin ? $checkin['checkin_time'] : null;
            $p['checkin_notes'] = $checkin ? $checkin['notes'] : null;
            $checkin_passengers[] = $p;
        }
    }
}

// Get check-in stats
$checkin_stats = $checkinModel->getStatsBySchedule($id);
$can_checkin = ($schedule['start_date'] <= date('Y-m-d'));
```

---

### PHASE 2: Redesign Tab System

**File:** `app/views/guide/tours/show.php`

#### 2.1. Thay đổi Navigation Tabs

**Từ:**
```html
<a href="#tour-info">Thông tin Tour</a>
```

**Thành:**
```html
<button onclick="switchTab('tour-info')" class="tab-button active" data-tab="tour-info">
    Thông tin Tour
</button>
```

#### 2.2. Wrap content trong tab containers

**Từ:**
```html
<div id="tour-info">...</div>
```

**Thành:**
```html
<div id="tab-tour-info" class="tab-content active">...</div>
<div id="tab-services" class="tab-content hidden">...</div>
<div id="tab-passengers" class="tab-content hidden">...</div>
```

#### 2.3. Thêm JavaScript để xử lý tab switching

```javascript
function switchTab(tabName) {
    // Ẩn tất cả tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
        tab.classList.remove('active');
    });
    
    // Ẩn tất cả tab buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
        btn.classList.remove('bg-gradient-to-r', 'from-accent-gradient-from', 'to-accent-gradient-to', 'text-white');
        btn.classList.add('bg-primary-100', 'text-primary-700');
    });
    
    // Hiển thị tab được chọn
    const selectedTab = document.getElementById('tab-' + tabName);
    if (selectedTab) {
        selectedTab.classList.remove('hidden');
        selectedTab.classList.add('active');
    }
    
    // Active button
    const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
    if (activeButton) {
        activeButton.classList.add('active');
        activeButton.classList.add('bg-gradient-to-r', 'from-accent-gradient-from', 'to-accent-gradient-to', 'text-white');
        activeButton.classList.remove('bg-primary-100', 'text-primary-700');
    }
    
    // Update URL (optional, không dùng hash)
    // history.pushState({}, '', '?act=guide-tours&action=show&id=<?= $schedule['id'] ?>&tab=' + tabName);
}
```

---

### PHASE 3: Tổ chức lại Tab "Thông tin Tour"

**Cấu trúc mới:**

```
Tab "Thông tin Tour"
├── Thông tin tour cơ bản (hiện tại)
├── Section: Check-in
│   ├── Stats (Tổng / Đã check-in / Chưa check-in)
│   ├── Danh sách passengers với check-in status
│   └── Button "Check-in" (nếu can_checkin)
├── Section: Chi phí phát sinh
│   ├── Tổng chi phí
│   ├── Danh sách chi phí (top 5 gần nhất)
│   └── Button "Xem tất cả" hoặc "Thêm chi phí"
└── Section: Nhật ký tour
    ├── Danh sách nhật ký (top 3 gần nhất)
    └── Button "Xem tất cả" hoặc "Viết nhật ký"
```

---

## 📝 CHI TIẾT IMPLEMENTATION

### 1. Controller Changes

**File:** `app/controllers/guide/TourController.php`

**Thêm vào hàm `show()`:**

```php
// Get check-in data
require_once MODELS_PATH . '/Checkin.php';
$checkinModel = new \Checkin($this->db);

// Get passengers with check-in status (chỉ lấy từ bookings đã thanh toán đủ)
$checkin_passengers = [];
$checkin_bookings = [];
foreach ($bookings as $booking) {
    if (in_array($booking['payment_status'], ['partial', 'paid']) 
        && (float)$booking['remaining_amount'] == 0) {
        $checkin_bookings[] = $booking;
    }
}

foreach ($checkin_bookings as $booking) {
    $p_list = $this->bookingModel->getPassengers($booking['id']);
    foreach ($p_list as $p) {
        if (!empty($p['customer_id']) && !empty($p['id'])) {
            $checkin = $checkinModel->getCustomerCheckin($booking['id'], $p['id']);
            $p['booking_id'] = $booking['id'];
            $p['booking_code'] = $booking['booking_code'];
            $p['checkin_status'] = $checkin ? $checkin['status'] : null;
            $p['checkin_time'] = $checkin ? $checkin['checkin_time'] : null;
            $p['checkin_notes'] = $checkin ? $checkin['notes'] : null;
            $checkin_passengers[] = $p;
        }
    }
}

// Get check-in stats
$checkin_stats = $checkinModel->getStatsBySchedule($id);
$can_checkin = ($schedule['start_date'] <= date('Y-m-d'));
```

---

### 2. View Changes - Tab Navigation

**File:** `app/views/guide/tours/show.php`

**Thay đổi navigation:**

```php
<!-- Navigation Tabs - Responsive -->
<div class="bg-panel rounded-2xl p-2 lg:p-3 mb-4 lg:mb-6 border border-primary-100">
    <div class="flex gap-2 overflow-x-auto">
        <button onclick="switchTab('tour-info')" 
                class="tab-button active px-3 lg:px-4 py-2 rounded-xl whitespace-nowrap bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white font-semibold transition-colors text-xs lg:text-sm"
                data-tab="tour-info">
            <i data-lucide="file-text" class="w-4 h-4 inline mr-1"></i>
            Thông tin Tour
        </button>
        <button onclick="switchTab('services')" 
                class="tab-button px-3 lg:px-4 py-2 rounded-xl whitespace-nowrap bg-primary-100 text-primary-700 font-semibold hover:bg-primary-200 transition-colors text-xs lg:text-sm"
                data-tab="services">
            <i data-lucide="briefcase" class="w-4 h-4 inline mr-1"></i>
            Dịch vụ (<?= count($bookingServices ?? []) ?>)
        </button>
        <button onclick="switchTab('passengers')" 
                class="tab-button px-3 lg:px-4 py-2 rounded-xl whitespace-nowrap bg-primary-100 text-primary-700 font-semibold hover:bg-primary-200 transition-colors text-xs lg:text-sm"
                data-tab="passengers">
            <i data-lucide="users" class="w-4 h-4 inline mr-1"></i>
            Hành khách (<?= count($passengers ?? []) ?>)
        </button>
        <?php if (!empty($room_assignments)): ?>
        <button onclick="switchTab('rooms')" 
                class="tab-button px-3 lg:px-4 py-2 rounded-xl whitespace-nowrap bg-primary-100 text-primary-700 font-semibold hover:bg-primary-200 transition-colors text-xs lg:text-sm"
                data-tab="rooms">
            <i data-lucide="home" class="w-4 h-4 inline mr-1"></i>
            Phân phòng
        </button>
        <?php endif; ?>
        <?php if (!empty($vehicle_assignments)): ?>
        <button onclick="switchTab('vehicles')" 
                class="tab-button px-3 lg:px-4 py-2 rounded-xl whitespace-nowrap bg-primary-100 text-primary-700 font-semibold hover:bg-primary-200 transition-colors text-xs lg:text-sm"
                data-tab="vehicles">
            <i data-lucide="car" class="w-4 h-4 inline mr-1"></i>
            Xe & Tài xế
        </button>
        <?php endif; ?>
    </div>
</div>
```

---

### 3. View Changes - Tab Content

**Wrap mỗi section trong tab container:**

```php
<!-- Tab: Thông tin Tour -->
<div id="tab-tour-info" class="tab-content active">
    <!-- Thông tin tour cơ bản -->
    <div class="space-y-4 lg:space-y-8 mb-6 lg:mb-8">
        <!-- Existing tour info sections -->
    </div>
    
    <!-- Section: Check-in -->
    <?php if ($can_checkin || !empty($checkin_passengers)): ?>
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
            <h2 class="text-base lg:text-lg font-bold text-primary-700">Check-in Hành khách</h2>
            <?php if ($can_checkin): ?>
            <a href="?act=guide-checkin&action=show&schedule_id=<?= $schedule['id'] ?>" 
               class="px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-xl font-semibold transition-colors text-sm">
                Check-in
            </a>
            <?php endif; ?>
        </div>
        
        <!-- Stats -->
        <?php if (!empty($checkin_stats)): ?>
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div class="text-center p-3 bg-primary-50 rounded-lg">
                <div class="text-2xl font-bold text-primary-700"><?= $checkin_stats['total'] ?? 0 ?></div>
                <div class="text-xs text-primary-500">Tổng số</div>
            </div>
            <div class="text-center p-3 bg-success-bg rounded-lg">
                <div class="text-2xl font-bold text-success"><?= $checkin_stats['checked_in'] ?? 0 ?></div>
                <div class="text-xs text-success-text">Đã check-in</div>
            </div>
            <div class="text-center p-3 bg-warning-bg rounded-lg">
                <div class="text-2xl font-bold text-warning"><?= $checkin_stats['not_checked_in'] ?? 0 ?></div>
                <div class="text-xs text-warning-text">Chưa check-in</div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Danh sách passengers với check-in status -->
        <!-- ... -->
    </div>
    <?php endif; ?>
    
    <!-- Section: Chi phí phát sinh -->
    <!-- ... -->
    
    <!-- Section: Nhật ký tour -->
    <!-- ... -->
</div>

<!-- Tab: Dịch vụ -->
<div id="tab-services" class="tab-content hidden">
    <!-- Existing services content -->
</div>

<!-- Tab: Hành khách -->
<div id="tab-passengers" class="tab-content hidden">
    <!-- Existing passengers content -->
</div>
```

---

### 4. JavaScript Tab Switching

**Thêm vào cuối file:**

```javascript
<script>
function switchTab(tabName) {
    // Ẩn tất cả tab content
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
        tab.classList.remove('active');
    });
    
    // Reset tất cả tab buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
        btn.classList.remove('bg-gradient-to-r', 'from-accent-gradient-from', 'to-accent-gradient-to', 'text-white');
        btn.classList.add('bg-primary-100', 'text-primary-700');
    });
    
    // Hiển thị tab được chọn
    const selectedTab = document.getElementById('tab-' + tabName);
    if (selectedTab) {
        selectedTab.classList.remove('hidden');
        selectedTab.classList.add('active');
    }
    
    // Active button
    const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
    if (activeButton) {
        activeButton.classList.add('active');
        activeButton.classList.remove('bg-primary-100', 'text-primary-700');
        activeButton.classList.add('bg-gradient-to-r', 'from-accent-gradient-from', 'to-accent-gradient-to', 'text-white');
    }
}

// Initialize: Show first tab on load
document.addEventListener('DOMContentLoaded', function() {
    // Tab mặc định là 'tour-info'
    switchTab('tour-info');
    
    // Re-initialize Lucide icons sau khi switch tab
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
```

---

## ✅ CHECKLIST

### Phase 1: Controller
- [ ] Thêm check-in data vào TourController
- [ ] Lấy check-in passengers với status
- [ ] Lấy check-in stats
- [ ] Test data

### Phase 2: View - Navigation
- [ ] Thay đổi anchor links thành buttons
- [ ] Thêm data-tab attributes
- [ ] Thêm active state cho tab đầu tiên

### Phase 3: View - Content
- [ ] Wrap content trong tab containers
- [ ] Thêm class 'hidden' cho các tab không active
- [ ] Thêm class 'active' cho tab đầu tiên

### Phase 4: JavaScript
- [ ] Tạo function switchTab()
- [ ] Xử lý ẩn/hiện content
- [ ] Xử lý active state cho buttons
- [ ] Initialize tab mặc định

### Phase 5: Tab "Thông tin Tour" Content
- [ ] Thêm section Check-in
- [ ] Thêm section Chi phí phát sinh (summary)
- [ ] Thêm section Nhật ký tour (summary)
- [ ] Test hiển thị

---

## 🎯 KẾT QUẢ MONG ĐỢI

Sau khi hoàn thành:
- ✅ Tab system hoạt động đúng: click tab nào chỉ hiển thị content của tab đó
- ✅ Tab "Thông tin Tour" bao gồm đầy đủ: thông tin tour, check-in, nhật ký, chi phí
- ✅ UX tốt hơn: tất cả thông tin quan trọng ở một nơi
- ✅ Không dùng anchor links, không scroll

---

## 📌 LƯU Ý

1. **CSS**: Cần thêm class `hidden` vào Tailwind (hoặc dùng `display: none`)
2. **JavaScript**: Cần re-initialize Lucide icons sau khi switch tab
3. **Performance**: Có thể lazy load content của các tab không active
4. **Mobile**: Đảm bảo tab navigation scroll được trên mobile

