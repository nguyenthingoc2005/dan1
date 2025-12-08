# KẾ HOẠCH: GỘP TẤT CẢ VÀO 1 TAB DUY NHẤT

## 📋 YÊU CẦU

1. **Chỉ có 1 tab**: "Thông tin Tour"
2. **Tất cả nội dung trong 1 tab**:
   - Thông tin tour cơ bản
   - Check-in
   - Chi phí phát sinh
   - Nhật ký tour
   - Dịch vụ
   - Hành khách
   - Phân phòng (nếu có)
   - Xe & Tài xế (nếu có)
3. **Navigation bên trong**: Menu sticky để jump đến sections
4. **URL parameter**: `?tab=xxx` để scroll đến section tương ứng

---

## 🎯 CẤU TRÚC MỚI

```
Tab "Thông tin Tour" (duy nhất)
├── Navigation Menu (sticky, bên trong tab)
│   ├── Thông tin Tour
│   ├── Check-in
│   ├── Chi phí phát sinh
│   ├── Nhật ký tour
│   ├── Dịch vụ
│   ├── Hành khách
│   ├── Phân phòng (nếu có)
│   └── Xe & Tài xế (nếu có)
│
└── Content Sections
    ├── Section: Thông tin Tour
    ├── Section: Check-in
    ├── Section: Chi phí phát sinh
    ├── Section: Nhật ký tour
    ├── Section: Dịch vụ
    ├── Section: Hành khách
    ├── Section: Phân phòng
    └── Section: Xe & Tài xế
```

---

## 📝 IMPLEMENTATION

### 1. Bỏ Navigation Tabs (bên ngoài)
- Xóa tất cả các tab buttons
- Chỉ giữ 1 container duy nhất

### 2. Thêm Navigation Menu (bên trong tab)
- Sticky menu ở đầu tab
- Click menu item → scroll đến section tương ứng
- Highlight menu item khi scroll đến section đó

### 3. Xử lý URL Parameter `?tab=xxx`
- Đọc `$_GET['tab']` trong PHP
- JavaScript scroll đến section tương ứng khi load page
- Update URL khi click menu item (không reload page)

### 4. Gộp tất cả content vào 1 tab
- Bỏ tất cả `tab-content` containers
- Tất cả sections nằm trong 1 container duy nhất
- Mỗi section có `id="section-xxx"` để scroll đến

---

## 🔧 CODE STRUCTURE

### Navigation Menu (Sticky)
```html
<div class="sticky top-0 bg-panel border-b border-primary-100 z-10 mb-6">
    <div class="flex gap-2 overflow-x-auto py-3">
        <a href="#section-tour-info" class="nav-link active">Thông tin Tour</a>
        <a href="#section-checkin" class="nav-link">Check-in</a>
        <a href="#section-expenses" class="nav-link">Chi phí phát sinh</a>
        <a href="#section-journals" class="nav-link">Nhật ký tour</a>
        <a href="#section-services" class="nav-link">Dịch vụ</a>
        <a href="#section-passengers" class="nav-link">Hành khách</a>
        ...
    </div>
</div>
```

### Sections với ID
```html
<div id="section-tour-info">...</div>
<div id="section-checkin">...</div>
<div id="section-expenses">...</div>
...
```

### JavaScript
```javascript
// Đọc ?tab=xxx từ URL và scroll đến section
const urlParams = new URLSearchParams(window.location.search);
const tab = urlParams.get('tab');
if (tab) {
    scrollToSection('section-' + tab);
}

// Smooth scroll với offset cho sticky menu
function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if (element) {
        const offset = 100; // Offset cho sticky menu
        const elementPosition = element.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - offset;
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }
}
```

