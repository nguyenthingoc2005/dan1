# 📱 KẾ HOẠCH RESPONSIVE DESIGN - PC & MOBILE

## 🎯 MỤC TIÊU
- **PC (Desktop)**: ≥ 1024px - Layout đầy đủ, sidebar luôn hiển thị
- **Mobile**: < 1024px - Layout tối ưu, sidebar ẩn/hiện, content stack vertical

---

## 📋 PHÂN TÍCH HIỆN TRẠNG

### ✅ Đã có:
- Viewport meta tag: `<meta name="viewport" content="width=device-width, initial-scale=1.0">`
- Tailwind CSS responsive utilities
- Button toggle sidebar (nhưng chưa hoàn chỉnh)

### ❌ Cần sửa:
1. **Sidebar**: Fixed width 260px, không responsive
2. **Header**: Padding cố định, chưa responsive
3. **Content**: Padding cố định, chưa responsive
4. **Tables**: Chưa có horizontal scroll trên mobile
5. **Forms**: Grid columns chưa responsive
6. **Cards**: Chưa responsive
7. **Buttons**: Chưa responsive
8. **Modals**: Chưa responsive

---

## 🎨 KẾ HOẠCH CHI TIẾT

### 1. SIDEBAR (Priority: HIGH)

**PC (≥1024px):**
- Fixed left, width: 260px
- Luôn hiển thị
- Margin-left cho main content: `ml-[260px]`

**Mobile (<1024px):**
- Fixed left, width: 280px (full width mobile)
- Ẩn mặc định: `hidden lg:flex` hoặc `-translate-x-full lg:translate-x-0`
- Overlay backdrop khi mở
- Close button hoặc click outside để đóng
- Z-index cao: `z-50`

**Implementation:**
```html
<!-- Sidebar -->
<div class="sidebar fixed left-0 top-0 bottom-0 w-[280px] lg:w-[260px] bg-sidebar border-r border-primary-100 flex flex-col z-50 lg:z-20 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
    <!-- Close button (mobile only) -->
    <button class="lg:hidden absolute top-4 right-4 p-2 text-primary-700 hover:bg-primary-50 rounded-xl" onclick="toggleSidebar()">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
    <!-- ... rest of sidebar ... -->
</div>

<!-- Overlay (mobile only) -->
<div id="sidebar-overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="toggleSidebar()"></div>
```

---

### 2. HEADER (Priority: HIGH)

**PC (≥1024px):**
- Padding: `px-8 py-4`
- Full header với tất cả icons

**Mobile (<1024px):**
- Padding: `px-4 py-3`
- Title nhỏ hơn: `text-xl` thay vì `text-2xl`
- Ẩn một số icon không cần thiết (messages)
- Menu button luôn hiển thị

**Implementation:**
```html
<header class="bg-panel border-b border-primary-100 sticky top-0 z-40 px-4 lg:px-8 py-3 lg:py-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 lg:gap-4">
            <button class="lg:hidden p-2 text-primary-500 hover:bg-primary-50 rounded-xl" onclick="toggleSidebar()">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <h2 class="text-xl lg:text-2xl font-bold text-primary-700"><?php echo $page_title ?? 'Dashboard'; ?></h2>
        </div>
        <div class="flex items-center gap-2 lg:gap-4">
            <!-- Notifications - always show -->
            <button class="p-2 text-primary-500 hover:bg-primary-50 rounded-xl">
                <i data-lucide="bell" class="w-5 h-5 lg:w-6 lg:h-6"></i>
            </button>
            <!-- Messages - hide on mobile -->
            <button class="hidden lg:block p-2 text-primary-500 hover:bg-primary-50 rounded-xl">
                <i data-lucide="mail" class="w-6 h-6"></i>
            </button>
            <?php render_user_menu(); ?>
        </div>
    </div>
</header>
```

---

### 3. MAIN CONTENT (Priority: HIGH)

**PC (≥1024px):**
- Margin-left: `ml-0 lg:ml-[260px]`
- Padding: `p-8`

**Mobile (<1024px):**
- Margin-left: `ml-0` (sidebar overlay)
- Padding: `p-4`

**Implementation:**
```html
<div class="flex-1 flex flex-col ml-0 lg:ml-[260px]">
    <!-- Header -->
    <!-- Content -->
    <main class="main-content">
        <div class="p-4 lg:p-8">
            <!-- Content -->
        </div>
    </main>
</div>
```

---

### 4. TABLES (Priority: HIGH)

**PC (≥1024px):**
- Full table hiển thị

**Mobile (<1024px):**
- Horizontal scroll container
- Hoặc card view (tùy chọn)

**Implementation:**
```html
<!-- Option 1: Horizontal Scroll -->
<div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <!-- Table content -->
        </table>
    </div>
</div>

<!-- Option 2: Card View (for complex tables) -->
<div class="lg:hidden space-y-4">
    <!-- Card for each row -->
</div>
<div class="hidden lg:block">
    <!-- Table -->
</div>
```

---

### 5. FORMS (Priority: MEDIUM)

**PC (≥1024px):**
- Grid columns: `grid-cols-2`, `grid-cols-3`, etc.

**Mobile (<1024px):**
- Single column: `grid-cols-1`

**Implementation:**
```html
<form class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
    <!-- Form fields -->
</form>
```

---

### 6. CARDS & STATS (Priority: MEDIUM)

**PC (≥1024px):**
- Grid: `grid-cols-3`, `grid-cols-4`

**Mobile (<1024px):**
- Stack: `grid-cols-1` hoặc `grid-cols-2`

**Implementation:**
```html
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
    <!-- Stat cards -->
</div>
```

---

### 7. BUTTONS (Priority: LOW)

**PC (≥1024px):**
- Full size buttons

**Mobile (<1024px):**
- Full width buttons: `w-full lg:w-auto`
- Smaller padding: `px-4 py-2 lg:px-5 lg:py-2.5`

**Implementation:**
```html
<button class="w-full lg:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-accent text-white rounded-xl">
    Action
</button>
```

---

### 8. MODALS (Priority: MEDIUM)

**PC (≥1024px):**
- Max width: `max-w-2xl`, `max-w-4xl`
- Centered

**Mobile (<1024px):**
- Full width: `w-full`
- Padding: `p-4` thay vì `p-6`

**Implementation:**
```html
<div class="bg-panel rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-4 lg:p-6">
    <!-- Modal content -->
</div>
```

---

### 9. ALERTS (Priority: LOW)

**PC & Mobile:**
- Responsive padding: `p-4 lg:p-6`
- Text size: `text-sm lg:text-base`

---

### 10. NAVIGATION MENU (Priority: HIGH)

**PC (≥1024px):**
- Full menu items

**Mobile (<1024px):**
- Menu items có thể scroll
- Icon và text vẫn hiển thị đầy đủ

---

## 🔧 JAVASCRIPT CẦN THIẾT

### Toggle Sidebar Function:
```javascript
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
    
    // Prevent body scroll when sidebar is open
    if (!sidebar.classList.contains('-translate-x-full')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
}

// Close sidebar when clicking outside (mobile)
document.addEventListener('click', function(e) {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const menuButton = document.querySelector('[onclick="toggleSidebar()"]');
    
    if (window.innerWidth < 1024) {
        if (!sidebar.contains(e.target) && !menuButton.contains(e.target)) {
            if (!sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    if (window.innerWidth >= 1024) {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }
});
```

---

## 📝 CHECKLIST IMPLEMENTATION

### Phase 1: Core Layout (HIGH Priority)
- [ ] Update sidebar với responsive classes
- [ ] Add overlay backdrop cho mobile
- [ ] Update header với responsive padding và text size
- [ ] Update main content margin và padding
- [ ] Update JavaScript toggle sidebar function

### Phase 2: Components (MEDIUM Priority)
- [ ] Update tables với horizontal scroll
- [ ] Update forms với responsive grid
- [ ] Update cards/stats với responsive grid
- [ ] Update modals với responsive sizing

### Phase 3: Polish (LOW Priority)
- [ ] Update buttons với responsive sizing
- [ ] Update alerts với responsive padding
- [ ] Test trên các kích thước màn hình khác nhau

---

## 🎯 BREAKPOINTS SỬ DỤNG

- **Mobile**: `< 1024px` (dùng `lg:` prefix)
- **PC**: `≥ 1024px` (default, không cần prefix)

**Lý do chọn 1024px:**
- Phù hợp với màn hình laptop nhỏ nhất (1366x768)
- Đảm bảo sidebar 260px + content vẫn đủ rộng
- Phân biệt rõ ràng giữa mobile và desktop

---

## 🚀 THỨ TỰ THỰC HIỆN

1. **Layout Files** (main_layout.php, admin_layout.php, guide_layout.php, staff_layout.php)
2. **Menu Helper** (nếu cần)
3. **Common Components** (tables, forms, cards)
4. **Individual Views** (nếu cần điều chỉnh riêng)

---

## ✅ KẾT QUẢ MONG ĐỢI

- ✅ Sidebar ẩn/hiện mượt mà trên mobile
- ✅ Content responsive, không bị overflow
- ✅ Tables scroll horizontal trên mobile
- ✅ Forms stack vertical trên mobile
- ✅ Buttons và inputs dễ click trên mobile
- ✅ Trải nghiệm tốt trên cả PC và Mobile

