# TEMPLATE TABLE - HORIZON UI STYLE

## 📋 TEMPLATE TABLE MỚI (Horizon UI Style)

### Table với Header đẹp (Primary-50 background, Primary-700 text)

```html
<!-- Table Container - Responsive -->
<div
  class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden"
>
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse min-w-[600px]">
      <thead>
        <tr class="bg-primary-50">
          <th
            class="px-3 lg:px-6 py-3 lg:py-4 border-b border-primary-100 text-primary-700 uppercase text-xs font-semibold tracking-wider"
          >
            Mã Tour
          </th>
          <th
            class="px-3 lg:px-6 py-3 lg:py-4 border-b border-primary-100 text-primary-700 uppercase text-xs font-semibold tracking-wider"
          >
            Tên Tour
          </th>
          <th
            class="px-3 lg:px-6 py-3 lg:py-4 border-b border-primary-100 text-primary-700 uppercase text-xs font-semibold tracking-wider text-right"
          >
            Hành động
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-primary-100">
        <tr
          class="border-b border-primary-100 hover:bg-primary-50 transition-colors"
        >
          <td
            class="px-3 lg:px-6 py-3 lg:py-4 font-mono text-accent font-semibold text-sm"
          >
            TOUR001
          </td>
          <td
            class="px-3 lg:px-6 py-3 lg:py-4 text-sm font-semibold text-primary-700"
          >
            Tour Đà Lạt 3N2Đ
          </td>
          <td class="px-3 lg:px-6 py-3 lg:py-4 text-right">
            <div class="flex gap-2 justify-end">
              <a
                href="#"
                class="text-accent hover:text-accent-hover transition-colors"
              >
                <i data-lucide="eye" class="w-4 h-4"></i>
              </a>
              <a
                href="#"
                class="text-primary-700 hover:text-primary-900 transition-colors"
              >
                <i data-lucide="pencil" class="w-4 h-4"></i>
              </a>
              <a
                href="#"
                class="text-danger hover:text-red-600 transition-colors"
              >
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

### Table với Zebra Stripe (Alternating rows)

```html
<div
  class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden"
>
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-primary-50">
          <th
            class="px-6 py-4 border-b border-primary-100 text-primary-700 uppercase text-xs font-semibold tracking-wider"
          >
            Mã
          </th>
          <th
            class="px-6 py-4 border-b border-primary-100 text-primary-700 uppercase text-xs font-semibold tracking-wider"
          >
            Tên
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-primary-100">
        <?php foreach ($items as $index =>
        $item): ?>
        <tr
          class="<?= $index % 2 === 0 ? 'bg-panel' : 'bg-primary-50' ?> hover:bg-primary-100 transition-colors"
        >
          <td class="px-6 py-4 text-primary-700 font-semibold">
            <?= $item['code'] ?>
          </td>
          <td class="px-6 py-4 text-primary-700 font-semibold">
            <?= $item['name'] ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
```

### Table với Checkbox (Selection)

```html
<div
  class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden"
>
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-primary-50">
          <th class="px-6 py-4 border-b border-primary-100">
            <input
              type="checkbox"
              class="w-4 h-4 rounded border-primary-300 text-accent focus:ring-accent"
            />
          </th>
          <th
            class="px-6 py-4 border-b border-primary-100 text-primary-700 uppercase text-xs font-semibold tracking-wider"
          >
            Mã Tour
          </th>
          <th
            class="px-6 py-4 border-b border-primary-100 text-primary-700 uppercase text-xs font-semibold tracking-wider"
          >
            Tên Tour
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-primary-100">
        <tr
          class="border-b border-primary-100 hover:bg-primary-50 transition-colors"
        >
          <td class="px-6 py-4">
            <input
              type="checkbox"
              class="w-4 h-4 rounded border-primary-300 text-accent focus:ring-accent"
            />
          </td>
          <td class="px-6 py-4 font-mono text-accent font-semibold">TOUR001</td>
          <td class="px-6 py-4 text-sm font-semibold text-primary-700">
            Tour Đà Lạt 3N2Đ
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
```

## 🎨 MÀU SẮC HORIZON UI

### Primary Colors:

- **Primary-50:** `#F4F7FE` - Background chính, table header
- **Primary-100:** `#E9EDF7` - Border, divider
- **Primary-300:** `#A3AED0` - Text secondary/disabled
- **Primary-500:** `#707EAE` - Text muted
- **Primary-700:** `#2B3674` - Text primary/Headings
- **Primary-900:** `#1B2559` - Sidebar dark (optional)

### Accent Colors:

- **Accent:** `#4318FF` - Button primary, links
- **Accent Hover:** `#3311DB` - Button hover
- **Accent Gradient From:** `#868CFF` - Gradient start
- **Accent Gradient To:** `#4318FF` - Gradient end

### Status Colors:

- **Success:** `#05CD99` / **Success BG:** `#E6FAF5` / **Success Text:** `#01B574`
- **Warning:** `#FFCE20` / **Warning BG:** `#FFF9E6` / **Warning Text:** `#FFB547`
- **Danger:** `#EE5D50` / **Danger BG:** `#FDEEED` / **Danger Text:** `#E31A1A`
- **Info:** `#4299E1` / **Info BG:** `#EBF8FF` / **Info Text:** `#3182CE`

## 📝 CÁCH SỬ DỤNG

Thay thế các table cũ:

- `bg-gray-50` → `bg-primary-50` (cho header)
- `text-gray-700` → `text-primary-700` (cho text)
- `hover:bg-gray-50` → `hover:bg-primary-50` (cho row hover)
- `border-gray-200` → `border-primary-100` (cho border)
- `divide-gray-200` → `divide-primary-100` (cho divider)
- `rounded` → `rounded-2xl` (cho container)
- Thêm `shadow-sm` cho container
- Font: `font-semibold` cho text trong table

## ✨ ĐẶC ĐIỂM HORIZON UI TABLE

1. **Border Radius:** `rounded-2xl` (16px) cho container
2. **Shadow:** `shadow-sm` nhẹ cho depth
3. **Border:** `border-primary-100` (1px) mỏng
4. **Hover Effect:** `hover:bg-primary-50` mượt mà
5. **Typography:** `font-semibold` cho text, `font-mono` cho mã
6. **Colors:** Primary-700 cho text, Accent cho links/actions

## 📱 RESPONSIVE DESIGN

### Mobile (< 1024px):

- **Horizontal Scroll:** Table có `min-w-[600px]` để scroll ngang trên mobile
- **Padding:** `px-3 py-3` (nhỏ hơn) thay vì `px-6 py-4`
- **Text Size:** `text-sm` cho cells

### PC (≥ 1024px):

- **Full Width:** Table hiển thị đầy đủ
- **Padding:** `px-6 py-4` (đầy đủ)
- **Text Size:** Mặc định

### Implementation:

```html
<!-- Responsive padding -->
<th class="px-3 lg:px-6 py-3 lg:py-4 ..."></th>
<td class="px-3 lg:px-6 py-3 lg:py-4 ...">
  <!-- Horizontal scroll container -->
  <div class="overflow-x-auto">
    <table class="w-full min-w-[600px] ..."></table>
  </div>
</td>
```
