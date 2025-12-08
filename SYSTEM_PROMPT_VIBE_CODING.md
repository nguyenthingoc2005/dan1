3. UI/UX GUIDELINES (HORIZON UI STYLE - TOUR TRAVEL ADAPTATION)

Áp dụng phong cách Horizon UI (Modern Dashboard) với điều chỉnh phù hợp chủ đề Tour Du Lịch:

**Layout:**

- Sidebar cố định trái (width: 260px)
- Header cố định trên với search bar
- Content cuộn độc lập
- Bo góc: `rounded-xl` (12px) cho card, `rounded-2xl` (16px) cho modal

**Màu sắc chủ đạo (Horizon UI Palette):**

```html
<script src="https://cdn.tailwindcss.com"></script>
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

          // === CHART COLORS (Để vẽ biểu đồ) ===
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
```

**Typography (Giống Horizon UI):**

```html
<!-- DM Sans - Font chính của Horizon UI -->
<link
  href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap"
  rel="stylesheet"
/>

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
    font-variant-numeric: tabular-nums;
  }
</style>
```

**Icon System:**
Dùng **Lucide Icons** (tương tự Horizon UI dùng icon outline):

```html
<script src="https://unpkg.com/lucide@latest"></script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    lucide.createIcons();
  });
</script>
```

**Design Rules (Khác với Flat thuần túy):**

✅ **CHO PHÉP:**

- Gradient backgrounds cho button/card highlight
- Box-shadow nhẹ: `shadow-sm` (0 1px 3px rgba(0,0,0,0.1))
- Gradient cho biểu đồ/chart
- Border radius lớn: `rounded-xl`, `rounded-2xl`

⛔ **VẪN CẤM:**

- Shadow quá đậm (không dùng `shadow-xl`)
- Border dày >2px
- Gradient quá nhiều màu (tối đa 2 màu)

**Component Patterns:**

**1. Button Primary (Gradient):**

```html
<button
  class="px-5 py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all"
>
  <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>
  Tạo Tour Mới
</button>
```

**2. Button Secondary:**

```html
<button
  class="px-5 py-2.5 bg-white border-2 border-primary-300 text-primary-700 hover:bg-primary-50 rounded-xl font-semibold transition-colors"
>
  Xem chi tiết
</button>
```

**3. Card/Panel:**

```html
<div class="bg-panel rounded-2xl p-6 shadow-sm border border-primary-100">
  <!-- Content -->
</div>
```

**4. Status Badge (Bo tròn hoàn toàn):**

```html
<!-- Approved -->
<span
  class="px-4 py-1.5 bg-success-bg text-success-text text-xs font-bold rounded-full inline-flex items-center gap-1"
>
  <i data-lucide="check-circle" class="w-3 h-3"></i>
  Đã duyệt
</span>

<!-- Pending -->
<span
  class="px-4 py-1.5 bg-warning-bg text-warning-text text-xs font-bold rounded-full"
>
  Chờ duyệt
</span>
```

**5. Input Field:**

```html
<input
  type="text"
  placeholder="Tìm kiếm..."
  class="w-full px-4 py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300"
/>
```

**6. Table Row (Có checkbox + hover effect):**

```html
<tr class="border-b border-primary-100 hover:bg-primary-50 transition-colors">
  <td class="px-4 py-4">
    <input
      type="checkbox"
      class="w-4 h-4 rounded border-primary-300 text-accent focus:ring-accent"
    />
  </td>
  <td class="px-4 py-4 text-sm font-semibold text-primary-700">TOUR001</td>
  <td class="px-4 py-4 text-sm text-primary-500">Hạ Long 3N2Đ</td>
  <!-- ... -->
</tr>
```

**Sidebar Style (Horizon UI):**

```html
<!-- Sidebar sáng với active state tím gradient -->
<aside class="w-64 bg-sidebar border-r border-primary-100">
  <!-- Logo -->
  <div class="p-6 border-b border-primary-100">
    <h1 class="text-2xl font-bold text-primary-700 flex items-center gap-2">
      <i data-lucide="compass" class="w-7 h-7 text-accent"></i>
      TourManager
    </h1>
  </div>

  <!-- Menu -->
  <nav class="p-4 space-y-1">
    <!-- Active -->
    <a
      href="#"
      class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white font-semibold"
    >
      <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
      Dashboard
    </a>

    <!-- Inactive -->
    <a
      href="#"
      class="flex items-center gap-3 px-4 py-3 rounded-xl text-primary-500 hover:bg-primary-50 hover:text-primary-700 transition-colors"
    >
      <i data-lucide="map-pin" class="w-5 h-5"></i>
      Tours
    </a>
  </nav>
</aside>
```

**Chart Colors (Dùng cho biểu đồ Recharts):**

```javascript
// Gradient definition
<defs>
  <linearGradient id="colorPrimary" x1="0" y1="0" x2="0" y2="1">
    <stop offset="5%" stopColor="#4318FF" stopOpacity={0.8}/>
    <stop offset="95%" stopColor="#4318FF" stopOpacity={0}/>
  </linearGradient>
</defs>

// Apply to Area chart
<Area
  type="monotone"
  dataKey="revenue"
  stroke="#4318FF"
  fillOpacity={1}
  fill="url(#colorPrimary)"
/>
```

```

**Icon mapping theo chức năng (Tour Du Lịch context):**

| Chức năng      | Icon Lucide      | Code                                     |
| -------------- | ---------------- | ---------------------------------------- |
| Dashboard      | layout-dashboard | `<i data-lucide="layout-dashboard"></i>` |
| Tours          | map-pin          | `<i data-lucide="map-pin"></i>`          |
| Bookings       | calendar-check   | `<i data-lucide="calendar-check"></i>`   |
| Khách hàng     | users            | `<i data-lucide="users"></i>`            |
| Hướng dẫn viên | user-check       | `<i data-lucide="user-check"></i>`       |
| Thanh toán     | credit-card      | `<i data-lucide="credit-card"></i>`      |
| Báo cáo        | bar-chart-3      | `<i data-lucide="bar-chart-3"></i>`      |
| Cài đặt        | settings         | `<i data-lucide="settings"></i>`         |
| Thêm mới       | plus-circle      | `<i data-lucide="plus-circle"></i>`      |
| Chỉnh sửa      | pencil           | `<i data-lucide="pencil"></i>`           |
| Xóa            | trash-2          | `<i data-lucide="trash-2"></i>`          |
| Xem chi tiết   | eye              | `<i data-lucide="eye"></i>`              |
| Tìm kiếm       | search           | `<i data-lucide="search"></i>`           |
| Lọc            | filter           | `<i data-lucide="filter"></i>`           |
| Tải xuống      | download         | `<i data-lucide="download"></i>`         |
| Check-in       | check-circle     | `<i data-lucide="check-circle"></i>`     |
| Vị trí         | map              | `<i data-lucide="map"></i>`              |
| Ngày tháng     | calendar         | `<i data-lucide="calendar"></i>`         |
| Thông báo      | bell             | `<i data-lucide="bell"></i>`             |
```
