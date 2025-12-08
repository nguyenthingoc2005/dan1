# PHÂN TÍCH LUỒNG: `?act=guide-tours&action=show&id=17&tab=vehicles`

## TỔNG QUAN

URL này hiển thị trang chi tiết tour với tab "Xe & Tài xế" được active.

---

## 1. LUỒNG ROUTING

### 1.1. Entry Point

```
index.php
├─ Parse: $act = 'guide-tours'
├─ Check: strpos($act, 'guide-') === 0 → TRUE
└─ require routes/guide.php
```

### 1.2. Route Parser

```
routes/guide.php
├─ require_guide() → Check authentication
├─ Parse: module = 'tours', action = 'show'
├─ new Guide\TourController($pdo)
└─ $controller->show()
```

---

## 2. CONTROLLER: `TourController->show()`

### 2.1. Parameters từ URL

```php
$id = $_GET['id'] ?? null;        // 17
$tab = $_GET['tab'] ?? null;      // 'vehicles' (KHÔNG được xử lý ở controller)
```

### 2.2. Luồng xử lý trong Controller

#### Bước 1: Authentication & Validation

```php
require_guide();                    // Check role
$user_id = get_user_id();          // Lấy user_id từ session
$id = $_GET['id'] ?? null;          // id = 17

if (!$id) redirect('?act=guide-tours');
```

#### Bước 2: Lấy Schedule

```php
$schedule = $this->scheduleModel->getById($id);
// Verify ownership
if ($schedule['guide_id'] != $user_id) {
    redirect('?act=guide-tours');
}
```

#### Bước 3: Lấy Vehicle Assignments (QUAN TRỌNG)

```php
// Dòng 278-304 trong TourController.php
$vehicle_assignments = [];
try {
    $vehicle_assignments_sql = "SELECT
        va.id,
        v.vehicle_code,
        v.vehicle_type,
        v.license_plate,
        v.capacity,
        d.full_name AS driver_name,
        d.phone AS driver_phone,
        d.license_type,
        va.driver_salary,
        va.estimated_fuel_cost,
        va.status
    FROM vehicle_assignments va
    JOIN vehicles v ON va.vehicle_id = v.id
    JOIN drivers d ON va.driver_id = d.id
    WHERE va.tour_schedule_id = :schedule_id
      AND va.status != 'cancelled'";

    $stmt = $this->db->prepare($vehicle_assignments_sql);
    $stmt->execute(['schedule_id' => $id]);
    $vehicle_assignments = $stmt->fetchAll(\PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    // Nếu bảng chưa tồn tại → $vehicle_assignments = []
    $vehicle_assignments = [];
}
```

**Lưu ý quan trọng:**

- Query có thể fail nếu bảng `vehicle_assignments` chưa tồn tại
- Nếu fail → `$vehicle_assignments = []` (mảng rỗng)
- Không có error logging → khó debug

---

## 3. VIEW: `app/views/guide/tours/show.php`

### 3.1. Tab Button (Conditional Rendering)

```php
// Dòng 69-75
<?php if (!empty($vehicle_assignments)): ?>
    <button data-tab="vehicles"
        class="nav-section-link ...">
        <i data-lucide="car" class="w-4 h-4 inline mr-1"></i>
        Xe & Tài xế
    </button>
<?php endif; ?>
```

**Vấn đề:** Tab button CHỈ hiển thị nếu `$vehicle_assignments` không rỗng.

### 3.2. Section Content (Conditional Rendering)

```php
// Dòng 825-876
<?php if (!empty($vehicle_assignments)): ?>
    <div id="section-vehicles" class="tab-component hidden ...">
        <div class="bg-panel ...">
            <h2>Xe và Tài xế</h2>
            <div class="grid ...">
                <?php foreach ($vehicle_assignments as $va): ?>
                    <!-- Render vehicle card -->
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
```

**Vấn đề nghiêm trọng:**

- Dòng 875: `<?php endif; ?>` đóng if statement
- Dòng 876: `</div>` đóng div section-vehicles
- **NHƯNG** `</div>` nằm NGOÀI `<?php endif; ?>` → Luôn được render!

**Cấu trúc hiện tại (SAI):**

```php
<?php if (!empty($vehicle_assignments)): ?>
    <div id="section-vehicles" class="tab-component hidden">
        <!-- Content -->
    <?php endif; ?>  <!-- Đóng if -->
</div>  <!-- Đóng div - NHƯNG nằm ngoài if! -->
```

**Cấu trúc đúng phải là:**

```php
<?php if (!empty($vehicle_assignments)): ?>
    <div id="section-vehicles" class="tab-component hidden">
        <!-- Content -->
    </div>  <!-- Đóng div TRƯỚC endif -->
<?php endif; ?>
```

---

## 4. JAVASCRIPT: Tab Switching

### 4.1. Tab Map

```javascript
// Dòng 880-889
const tabMap = {
  "tour-info": "section-tour-info",
  checkin: "section-checkin",
  expenses: "section-expenses",
  journals: "section-journals",
  services: "section-services",
  passengers: "section-passengers",
  rooms: "section-rooms",
  vehicles: "section-vehicles", // ← Map này
};
```

### 4.2. Initialize từ URL

```javascript
// Dòng 966-977
(function initTabs() {
  const urlParams = new URLSearchParams(window.location.search);
  const tab = urlParams.get("tab") || "tour-info"; // Lấy 'vehicles' từ URL

  if (tab && tabMap[tab]) {
    showComponent(tab); // Gọi showComponent('vehicles')
  } else {
    showComponent("tour-info");
  }
})();
```

### 4.3. showComponent() Function

```javascript
// Dòng 892-962
function showComponent(tabName) {
  // Validate
  if (!tabName || !tabMap[tabName]) {
    tabName = "tour-info"; // Fallback
  }

  // Ẩn tất cả
  document.querySelectorAll(".tab-component").forEach((component) => {
    component.classList.add("hidden");
  });

  // Hiển thị component được chọn
  const sectionId = tabMap[tabName]; // 'section-vehicles'
  const component = document.getElementById(sectionId);

  if (component) {
    component.classList.remove("hidden");
  } else {
    console.warn("Section not found:", sectionId);
    // Fallback to tour-info
    const fallbackSection = document.getElementById("section-tour-info");
    if (fallbackSection) {
      fallbackSection.classList.remove("hidden");
    }
  }

  // Update active nav link
  // Update URL
}
```

---

## 5. CÁC VẤN ĐỀ PHÁT HIỆN

### ❌ VẤN ĐỀ 1: Cấu trúc HTML sai

**Vị trí:** `app/views/guide/tours/show.php` dòng 825-876

**Mô tả:**

- `</div>` đóng `section-vehicles` nằm NGOÀI `<?php endif; ?>`
- Nếu `$vehicle_assignments` rỗng → div vẫn được render nhưng không có content
- Gây lỗi HTML structure

**Code hiện tại (SAI):**

```php
<?php if (!empty($vehicle_assignments)): ?>
    <div id="section-vehicles" class="tab-component hidden">
        <!-- Content -->
    <?php endif; ?>  <!-- Đóng if -->
</div>  <!-- Đóng div - SAI VỊ TRÍ! -->
```

**Fix:**

```php
<?php if (!empty($vehicle_assignments)): ?>
    <div id="section-vehicles" class="tab-component hidden">
        <!-- Content -->
    </div>  <!-- Đóng div TRƯỚC endif -->
<?php endif; ?>
```

### ❌ VẤN ĐỀ 2: Tab không hiển thị nếu không có data

**Vị trí:** Dòng 69-75

**Mô tả:**

- Tab button chỉ hiển thị nếu `!empty($vehicle_assignments)`
- Nếu user truy cập `?tab=vehicles` nhưng không có data → tab button không có → không thể click
- JavaScript vẫn cố tìm `section-vehicles` → không tìm thấy → fallback về `tour-info`

**Giải pháp:**

- Luôn hiển thị tab button (có thể disable nếu không có data)
- Hoặc hiển thị section rỗng với message "Chưa có thông tin xe"

### ❌ VẤN ĐỀ 3: Không có error logging

**Vị trí:** `TourController.php` dòng 301-303

**Mô tả:**

- Nếu query fail (bảng không tồn tại) → catch exception nhưng không log
- Khó debug khi có lỗi database

**Fix:**

```php
} catch (\PDOException $e) {
    error_log("TourController::show() - Error loading vehicle assignments: " . $e->getMessage());
    $vehicle_assignments = [];
}
```

### ⚠️ VẤN ĐỀ 4: JavaScript fallback có thể không hoạt động đúng

**Vị trí:** `show.php` dòng 916-923

**Mô tả:**

- Nếu `section-vehicles` không tồn tại → JavaScript fallback về `tour-info`
- Nhưng URL vẫn giữ `?tab=vehicles` → user có thể bị confused

**Giải pháp:**

- Update URL khi fallback
- Hoặc hiển thị message "Tab này không có dữ liệu"

---

## 6. LUỒNG HOẠT ĐỘNG HIỆN TẠI

### Scenario 1: Có vehicle_assignments

```
1. Controller query → $vehicle_assignments = [data]
2. View render:
   ✅ Tab button hiển thị (dòng 69-75)
   ✅ Section vehicles hiển thị (dòng 825-876)
3. JavaScript:
   ✅ Đọc ?tab=vehicles từ URL
   ✅ Tìm section-vehicles → Tìm thấy
   ✅ Remove 'hidden' class → Hiển thị
   ✅ Update active nav link
   ✅ Update URL (pushState)
```

### Scenario 2: KHÔNG có vehicle_assignments

```
1. Controller query → $vehicle_assignments = [] (rỗng)
2. View render:
   ❌ Tab button KHÔNG hiển thị (dòng 69-75)
   ❌ Section vehicles KHÔNG hiển thị (dòng 825-876)
   ⚠️ NHƯNG có </div> thừa được render (dòng 876)
3. JavaScript:
   ⚠️ Đọc ?tab=vehicles từ URL
   ⚠️ Tìm section-vehicles → KHÔNG tìm thấy
   ⚠️ Fallback về tour-info
   ⚠️ NHƯNG URL vẫn giữ ?tab=vehicles
```

---

## 7. SỬA LỖI

### Fix 1: Sửa cấu trúc HTML

```php
<!-- Section: Xe & Tài xế -->
<?php if (!empty($vehicle_assignments)): ?>
    <div id="section-vehicles" class="tab-component hidden space-y-4 lg:space-y-8 mb-6 lg:mb-8">
        <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
            <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-4 lg:mb-6">Xe và Tài xế</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($vehicle_assignments as $va): ?>
                    <!-- Vehicle card -->
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Hiển thị section rỗng nếu không có data -->
    <div id="section-vehicles" class="tab-component hidden space-y-4 lg:space-y-8 mb-6 lg:mb-8">
        <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
            <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-4 lg:mb-6">Xe và Tài xế</h2>
            <div class="text-center py-8">
                <p class="text-primary-500 text-sm">Chưa có thông tin xe và tài xế cho tour này.</p>
            </div>
        </div>
    </div>
<?php endif; ?>
```

### Fix 2: Luôn hiển thị tab button

```php
<!-- Luôn hiển thị tab, có thể disable nếu không có data -->
<button data-tab="vehicles"
    class="nav-section-link px-3 lg:px-4 py-2 rounded-xl whitespace-nowrap <?= !empty($vehicle_assignments) ? 'bg-primary-100 text-primary-700 hover:bg-primary-200' : 'bg-primary-50 text-primary-400 cursor-not-allowed' ?> font-semibold transition-colors text-xs lg:text-sm"
    <?= empty($vehicle_assignments) ? 'disabled' : '' ?>>
    <i data-lucide="car" class="w-4 h-4 inline mr-1"></i>
    Xe & Tài xế
</button>
```

### Fix 3: Thêm error logging

```php
} catch (\PDOException $e) {
    error_log("TourController::show() - Error loading vehicle assignments for schedule_id $id: " . $e->getMessage());
    $vehicle_assignments = [];
}
```

---

## 8. KẾT LUẬN

### Luồng hoạt động:

1. ✅ Routing đúng
2. ✅ Controller query đúng (có try-catch)
3. ⚠️ View có lỗi cấu trúc HTML
4. ⚠️ Tab button conditional → không hiển thị nếu không có data
5. ⚠️ JavaScript có fallback nhưng không perfect

### Lỗi cần sửa:

1. **CẤP THIẾT:** Sửa cấu trúc HTML (đóng div đúng vị trí)
2. **QUAN TRỌNG:** Luôn hiển thị tab button (hoặc section rỗng)
3. **NÊN CÓ:** Thêm error logging trong controller
