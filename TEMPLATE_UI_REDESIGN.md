# TEMPLATE CODE - UI REDESIGN HORIZON UI STYLE

## 📋 TEMPLATE HEAD SECTION (Cho tất cả Layouts)

```html
<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $page_title ?? 'Dashboard'; ?> - Tour Management</title>

    <!-- DM Sans Font (Horizon UI) -->
    <link
      href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap"
      rel="stylesheet"
    />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tailwind Config - HORIZON UI PALETTE -->
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              // === PRIMARY (Navy/Purple) ===
              primary: {
                50: "#F4F7FE", // Background chính
                100: "#E9EDF7", // Hover background
                300: "#A3AED0", // Text secondary/disabled
                500: "#707EAE", // Text muted
                700: "#2B3674", // Text primary/Headings
                900: "#1B2559", // Sidebar dark
              },

              // === ACCENT (Purple Gradient) ===
              accent: {
                DEFAULT: "#4318FF", // Button primary
                hover: "#3311DB", // Button hover
                light: "#7551FF", // Lighter variant
                gradient: {
                  from: "#868CFF", // Gradient start
                  to: "#4318FF", // Gradient end
                },
              },

              // === CHART COLORS ===
              chart: {
                primary: "#4318FF", // Tím
                secondary: "#6AD2FF", // Xanh dương nhạt
                tertiary: "#01B574", // Xanh lá
                quaternary: "#FFB547", // Cam
              },

              // === STATUS COLORS ===
              success: {
                DEFAULT: "#05CD99",
                bg: "#E6FAF5",
                text: "#01B574",
              },
              warning: {
                DEFAULT: "#FFCE20",
                bg: "#FFF9E6",
                text: "#FFB547",
              },
              danger: {
                DEFAULT: "#EE5D50",
                bg: "#FDEEED",
                text: "#E31A1A",
              },
              info: {
                DEFAULT: "#4299E1",
                bg: "#EBF8FF",
                text: "#3182CE",
              },

              // === BACKGROUNDS ===
              main: "#F4F7FE", // Background chính
              panel: "#FFFFFF", // Card/Panel
              sidebar: "#FFFFFF", // Sidebar background (hoặc dùng primary-900 nếu muốn tối)
            },
          },
        },
      };
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Custom Styles -->
    <style>
      * {
        font-family: "DM Sans", -apple-system, sans-serif;
        letter-spacing: -0.01em; /* Tighter spacing */
      }

      /* Headings - Font đậm hơn */
      h1,
      h2,
      h3,
      h4,
      h5,
      h6 {
        font-weight: 700;
        color: #2b3674;
      }

      /* Body text */
      body {
        color: #707eae;
        font-size: 14px;
      }

      /* Numbers/Stats - Dùng font tabular */
      .font-mono {
        font-family: "Monaco", "Courier New", monospace;
        font-variant-numeric: tabular-nums;
      }

      /* Custom Scrollbar */
      .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
      }
      .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
      }
      .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #a3aed0;
        border-radius: 3px;
      }
      .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #707eae;
      }
    </style>
  </head>
</html>
```

---

## 📋 TEMPLATE SIDEBAR (Horizon UI Style)

```html
<!-- SIDEBAR -->
<div
  class="w-[260px] bg-sidebar border-r border-primary-100 flex flex-col z-20 relative"
>
  <!-- Logo -->
  <div class="p-6 border-b border-primary-100">
    <h1 class="text-2xl font-bold text-primary-700 flex items-center gap-2">
      <i data-lucide="compass" class="w-7 h-7 text-accent"></i>
      TourManager
    </h1>
  </div>

  <!-- Menu -->
  <nav class="flex-1 py-6 overflow-y-auto custom-scrollbar px-4 space-y-1">
    <?php render_menu(); ?>
  </nav>

  <!-- User Info -->
  <div class="p-4 border-t border-primary-100">
    <div class="flex items-center gap-3 mb-4">
      <div
        class="w-10 h-10 rounded-full bg-accent flex items-center justify-center text-white font-bold"
      >
        <?php echo strtoupper(substr($current_user['full_name'], 0, 1)); ?>
      </div>
      <div class="flex-1 overflow-hidden">
        <div class="text-sm font-semibold truncate text-primary-700">
          <?php echo $current_user['full_name']; ?>
        </div>
        <div class="text-xs text-primary-500 truncate">
          <?php echo $current_user['role_display']; ?>
        </div>
      </div>
    </div>
    <a
      href="<?php echo BASE_URL; ?>/?act=logout"
      class="block w-full px-4 py-2 bg-danger hover:bg-red-600 text-white rounded-xl text-sm text-center transition-colors font-semibold"
    >
      Đăng xuất
    </a>
  </div>
</div>
```

**Menu Item Active:**

```html
<a
  href="#"
  class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white font-semibold"
>
  <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
  Dashboard
</a>
```

**Menu Item Inactive:**

```html
<a
  href="#"
  class="flex items-center gap-3 px-4 py-3 rounded-xl text-primary-500 hover:bg-primary-50 hover:text-primary-700 transition-colors"
>
  <i data-lucide="map-pin" class="w-5 h-5"></i>
  Tours
</a>
```

---

## 📋 TEMPLATE HEADER (Horizon UI Style)

```html
<!-- HEADER -->
<header
  class="bg-panel border-b border-primary-100 px-8 py-4 flex justify-between items-center z-10 sticky top-0"
>
  <div class="flex items-center gap-4">
    <button
      class="md:hidden p-2 text-primary-500 hover:bg-primary-50 rounded-xl"
    >
      <i data-lucide="menu" class="w-6 h-6"></i>
    </button>
    <h2 class="text-2xl font-bold text-primary-700">
      <?php echo $page_title ?? 'Dashboard'; ?>
    </h2>
  </div>

  <div class="flex items-center gap-4">
    <button
      class="p-2 text-primary-500 hover:text-primary-700 transition-colors relative rounded-xl hover:bg-primary-50"
    >
      <i data-lucide="bell" class="w-6 h-6"></i>
      <span
        class="absolute top-2 right-2 w-2 h-2 bg-danger rounded-full"
      ></span>
    </button>
    <?php render_user_menu(); ?>
  </div>
</header>
```

---

## 📋 TEMPLATE BUTTONS (Responsive)

### Primary Button (Gradient - Purple)

```html
<button
  class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base"
>
  <i data-lucide="plus-circle" class="w-4 h-4 inline mr-2"></i>
  Tạo Tour Mới
</button>
```

### Secondary Button

```html
<button
  class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-white border-2 border-primary-300 text-primary-700 hover:bg-primary-50 rounded-xl font-semibold transition-colors text-sm lg:text-base"
>
  Xem chi tiết
</button>
```

### Danger Button

```html
<button
  class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-danger hover:bg-red-600 text-white rounded-xl font-semibold transition-colors text-sm lg:text-base"
>
  <i data-lucide="trash-2" class="w-4 h-4 inline mr-2"></i>
  Xóa
</button>
```

### Outline Button

```html
<button
  class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 border-2 border-primary-300 text-primary-700 hover:bg-primary-50 rounded-xl font-semibold transition-colors text-sm lg:text-base"
>
  Hủy
</button>
```

---

## 📋 TEMPLATE CARDS (Horizon UI Style - Responsive)

```html
<!-- Single Card -->
<div class="bg-panel rounded-2xl p-4 lg:p-6 shadow-sm border border-primary-100">
  <h3 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4">Tiêu đề</h3>
  <p class="text-sm lg:text-base text-primary-500">Nội dung...</p>
</div>

<!-- Stat Cards Grid - Responsive -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
  <!-- Stat Card -->
  <div class="bg-panel rounded-2xl p-4 lg:p-6 shadow-sm border border-primary-100">
    <div class="flex items-center justify-between mb-3 lg:mb-4">
      <h3 class="text-xs lg:text-sm text-primary-500 font-medium">Tổng Booking</h3>
      <i data-lucide="calendar-check" class="w-6 h-6 lg:w-8 lg:h-8 text-accent"></i>
    </div>
    <div class="text-2xl lg:text-3xl font-bold text-primary-700">1,234</div>
    <p class="text-xs lg:text-sm text-primary-500 mt-2">Tăng 12% so với tháng trước</p>
  </div>
  
  <!-- More stat cards... -->
</div>

<!-- Card Grid - 2 columns mobile, 3 columns PC -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
  <div class="bg-panel rounded-2xl p-4 lg:p-6 shadow-sm border border-primary-100">
    <!-- Card content -->
  </div>
</div>
```

---

## 📋 TEMPLATE STATUS BADGES (Rounded Full)

```html
<!-- Success -->
<span
  class="px-4 py-1.5 bg-success-bg text-success-text text-xs font-bold rounded-full inline-flex items-center gap-1"
>
  <i data-lucide="check-circle" class="w-3 h-3"></i>
  Đã duyệt
</span>

<!-- Warning -->
<span
  class="px-4 py-1.5 bg-warning-bg text-warning-text text-xs font-bold rounded-full"
>
  Chờ duyệt
</span>

<!-- Danger -->
<span
  class="px-4 py-1.5 bg-danger-bg text-danger-text text-xs font-bold rounded-full"
>
  Đã hủy
</span>

<!-- Info -->
<span
  class="px-4 py-1.5 bg-info-bg text-info-text text-xs font-bold rounded-full"
>
  Đang xử lý
</span>
```

---

## 📋 TEMPLATE ALERTS (Horizon UI Style)

```html
<!-- Success Alert -->
<div
  class="mb-6 p-4 bg-success-bg border-l-4 border-success rounded-r flex items-center gap-3"
>
  <i data-lucide="check-circle" class="w-5 h-5 text-success"></i>
  <p class="text-success-text font-semibold">
    <?php echo sanitize($success); ?>
  </p>
</div>

<!-- Error Alert -->
<div
  class="mb-6 p-4 bg-danger-bg border-l-4 border-danger rounded-r flex items-center gap-3"
>
  <i data-lucide="alert-circle" class="w-5 h-5 text-danger"></i>
  <p class="text-danger-text font-semibold"><?php echo sanitize($error); ?></p>
</div>

<!-- Warning Alert -->
<div
  class="mb-6 p-4 bg-warning-bg border-l-4 border-warning rounded-r flex items-center gap-3"
>
  <i data-lucide="alert-triangle" class="w-5 h-5 text-warning"></i>
  <p class="text-warning-text font-semibold">Cảnh báo...</p>
</div>

<!-- Info Alert -->
<div
  class="mb-6 p-4 bg-info-bg border-l-4 border-info rounded-r flex items-center gap-3"
>
  <i data-lucide="info" class="w-5 h-5 text-info"></i>
  <p class="text-info-text font-semibold">Thông tin...</p>
</div>
```

---

## 📋 TEMPLATE TABLES (Horizon UI Style)

```html
<div
  class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden"
>
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-primary-50">
          <th
            class="px-6 py-4 border-b border-primary-100 text-primary-700 font-semibold text-sm uppercase"
          >
            Mã Tour
          </th>
          <th
            class="px-6 py-4 border-b border-primary-100 text-primary-700 font-semibold text-sm uppercase"
          >
            Tên Tour
          </th>
          <th
            class="px-6 py-4 border-b border-primary-100 text-primary-700 font-semibold text-sm uppercase text-right"
          >
            Hành động
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-primary-100">
        <tr
          class="border-b border-primary-100 hover:bg-primary-50 transition-colors"
        >
          <td class="px-6 py-4 font-mono text-accent font-semibold">TOUR001</td>
          <td class="px-6 py-4 text-sm font-semibold text-primary-700">
            Tour Đà Lạt 3N2Đ
          </td>
          <td class="px-6 py-4 text-right">
            <div class="flex gap-2 justify-end">
              <a href="#" class="text-accent hover:text-accent-hover">
                <i data-lucide="eye" class="w-4 h-4"></i>
              </a>
              <a href="#" class="text-primary-700 hover:text-primary-900">
                <i data-lucide="pencil" class="w-4 h-4"></i>
              </a>
              <a href="#" class="text-danger hover:text-red-600">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
              </a>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
```

---

## 📋 TEMPLATE FORMS (Horizon UI Style - Responsive)

```html
<!-- Single Column Form (Mobile) / Multi Column (PC) -->
<form class="space-y-4 lg:space-y-6">
  <!-- Grid Layout: 1 column mobile, 2 columns PC -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
    <!-- Input Field -->
    <div>
      <label for="name" class="block text-sm font-semibold text-primary-700 mb-2">
        Tên Tour <span class="text-danger">*</span>
      </label>
      <input
        type="text"
        id="name"
        name="name"
        required
        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
      />
    </div>

    <!-- Select Field -->
    <div>
      <label
        for="status"
        class="block text-sm font-semibold text-primary-700 mb-2"
      >
        Trạng thái
      </label>
      <select
        id="status"
        name="status"
        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all bg-panel text-primary-700 text-sm lg:text-base"
      >
        <option value="active">Hoạt động</option>
        <option value="inactive">Đã ẩn</option>
      </select>
    </div>
  </div>

  <!-- Full Width Field -->
  <div>
    <label
      for="description"
      class="block text-sm font-semibold text-primary-700 mb-2"
    >
      Mô tả
    </label>
    <textarea
      id="description"
      name="description"
      rows="4"
      class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base"
    ></textarea>
  </div>

  <!-- Submit Button - Responsive -->
  <div class="flex flex-col sm:flex-row gap-3">
    <button
      type="submit"
      class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl transition-all font-semibold text-sm lg:text-base"
    >
      <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
      Lưu
    </button>
    <button
      type="button"
      class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 border-2 border-primary-300 text-primary-700 hover:bg-primary-50 rounded-xl transition-colors font-semibold text-sm lg:text-base"
    >
      Hủy
    </button>
  </div>
</form>
```

---

## 📋 TEMPLATE TABS (Horizon UI Style)

```html
<div class="border-b border-primary-100 mb-6">
  <nav class="-mb-px flex space-x-8" aria-label="Tabs">
    <a
      href="#"
      class="border-b-2 border-accent text-accent font-semibold text-sm py-4 px-1"
    >
      Tất cả
    </a>
    <a
      href="#"
      class="border-b-2 border-transparent text-primary-500 hover:text-primary-700 hover:border-primary-300 font-semibold text-sm py-4 px-1"
    >
      Đang bán
    </a>
    <a
      href="#"
      class="border-b-2 border-transparent text-primary-500 hover:text-primary-700 hover:border-primary-300 font-semibold text-sm py-4 px-1"
    >
      Chờ duyệt
    </a>
  </nav>
</div>
```

---

## 📋 TEMPLATE MODAL (Horizon UI Style - Responsive)

```html
<!-- Modal Overlay -->
<div
  id="modal"
  class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
>
  <!-- Modal Content - Responsive -->
  <div
    class="bg-panel rounded-2xl shadow-sm border border-primary-100 max-w-2xl w-full max-h-[90vh] overflow-y-auto"
  >
    <!-- Modal Header -->
    <div
      class="p-4 lg:p-6 border-b border-primary-100 flex justify-between items-center"
    >
      <h3 class="text-base lg:text-lg font-bold text-primary-700">Tiêu đề Modal</h3>
      <button
        onclick="closeModal()"
        class="text-primary-500 hover:text-primary-700 p-1"
      >
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <!-- Modal Body -->
    <div class="p-4 lg:p-6">
      <!-- Content here -->
    </div>

    <!-- Modal Footer - Responsive -->
    <div class="p-4 lg:p-6 border-t border-primary-100 flex flex-col sm:flex-row justify-end gap-3">
      <button
        onclick="closeModal()"
        class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 border-2 border-primary-300 text-primary-700 hover:bg-primary-50 rounded-xl transition-colors font-semibold text-sm lg:text-base"
      >
        Hủy
      </button>
      <button
        class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl transition-all font-semibold text-sm lg:text-base"
      >
        Xác nhận
      </button>
    </div>
  </div>
</div>
```

---

## 📋 INITIALIZE LUCIDE ICONS

```html
<script>
  document.addEventListener("DOMContentLoaded", () => {
    lucide.createIcons();
  });
</script>
```

---

## ⚠️ QUY TẮC THIẾT KẾ (Horizon UI Style)

### ✅ CHO PHÉP:

- Gradient backgrounds cho button/card highlight (`bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to`)
- Box-shadow nhẹ: `shadow-sm` (0 1px 3px rgba(0,0,0,0.1))
- Gradient cho biểu đồ/chart
- Border radius lớn: `rounded-xl` (12px), `rounded-2xl` (16px)
- Border: `border-primary-100` (1px)

### ❌ KHÔNG DÙNG:

- Shadow quá đậm (không dùng `shadow-xl`, `shadow-2xl`)
- Border dày >2px
- Gradient quá nhiều màu (tối đa 2 màu)
- Font khác DM Sans (trừ monospace cho numbers)

---

**Lưu ý:** Tất cả code trên đã tuân thủ Horizon UI Design System với bộ màu Navy/Purple và typography DM Sans.
