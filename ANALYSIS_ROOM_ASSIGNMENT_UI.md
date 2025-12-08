# PHÂN TÍCH: GIAO DIỆN PHÂN PHÒNG TỰ ĐỘNG

## 🎯 YÊU CẦU MỚI

1. **Hiển thị danh sách khách hàng** trước khi phân phòng
2. **Hiển thị yêu cầu đặc biệt** (đơn phòng, cùng phòng, tránh cùng phòng)
3. **Admin chọn khách** cần xử lý thủ công → loại bỏ khỏi danh sách tự động
4. **Tùy chỉnh tham số phân phòng**: Số người/phòng (2, 3, hoặc tùy chọn)
5. **Phân phòng tự động** cho phần còn lại
6. **Sửa đổi phân phòng** sau khi tự động

---

## 📋 WORKFLOW MỚI

```
BƯỚC 1: XEM DANH SÁCH KHÁCH
├── Hiển thị tất cả khách đã thanh toán
├── Đánh dấu yêu cầu đặc biệt (badge/icon)
└── Cho phép chọn khách cần xử lý thủ công (checkbox)

BƯỚC 2: CẤU HÌNH PHÂN PHÒNG
├── Nhập số người/phòng mặc định (2, 3, hoặc tùy chọn)
├── Xem lại danh sách khách đã chọn (thủ công)
└── Danh sách khách sẽ tự động phân phòng

BƯỚC 3: PHÂN PHÒNG TỰ ĐỘNG
├── Hệ thống tự động phân phòng
├── Hiển thị kết quả theo từng đêm
└── Cho phép sửa đổi ngay

BƯỚC 4: CHỈNH SỬA (Nếu cần)
├── Kéo thả hoặc chọn để di chuyển khách
└── Xóa/thêm khách vào phòng
```

---

## 🎨 THIẾT KẾ GIAO DIỆN

### **OPTION 1: Tab Navigation (Recommended)**
```
┌─────────────────────────────────────────────────┐
│ [Danh sách khách] [Cấu hình] [Kết quả phân phòng] │
└─────────────────────────────────────────────────┘

Tab 1: Danh sách khách
├── Bộ lọc: Tất cả / Có yêu cầu / Chưa có yêu cầu
├── Checkbox "Chọn tất cả"
├── Danh sách khách:
│   ├── ☐ Nguyễn Văn A (Nam) - Booking: BK001
│   │   └── 🏷️ Đơn phòng (+500k)
│   ├── ☐ Trần Thị B (Nữ) - Booking: BK001
│   │   └── 🏷️ Cùng phòng với: Nguyễn Văn C
│   └── ☐ ...
└── Button "Tiếp tục với danh sách đã chọn"

Tab 2: Cấu hình phân phòng
├── Số người/phòng mặc định: [2] người
├── Tùy chọn nâng cao:
│   ├── Ưu tiên ghép cùng booking: ☑
│   └── Tự động tạo phòng đơn nếu lẻ: ☑
└── Button "Phân phòng tự động"

Tab 3: Kết quả phân phòng
├── Hiển thị theo từng đêm (như hiện tại)
└── Cho phép sửa đổi
```

### **OPTION 2: Wizard Steps (Alternative)**
```
Step 1/3: Chọn khách cần xử lý thủ công
Step 2/3: Cấu hình tham số
Step 3/3: Xem và chỉnh sửa kết quả
```

### **OPTION 3: Single Page với Sections (Simplest)**
```
Section 1: Danh sách khách + chọn thủ công
Section 2: Cấu hình + Phân phòng tự động
Section 3: Kết quả + Chỉnh sửa
```

---

## 💡 ĐỀ XUẤT: OPTION 1 (Tab Navigation)

**Lý do:**
- Dễ điều hướng, không cần reload page
- Có thể quay lại tab trước để chỉnh sửa
- UI quen thuộc với user

---

## 🔧 IMPLEMENTATION PLAN

### **Phase 1: Tab "Danh sách khách"**
1. Hiển thị danh sách khách với:
   - Checkbox để chọn
   - Thông tin: Tên, Giới tính, Booking code
   - Badge yêu cầu đặc biệt
   - Filter: Tất cả / Có yêu cầu / Không yêu cầu
2. Lưu danh sách khách đã chọn vào session/localStorage

### **Phase 2: Tab "Cấu hình"**
1. Form nhập tham số:
   - Số người/phòng mặc định (dropdown: 2, 3, 4)
   - Checkbox: Ưu tiên ghép cùng booking
   - Checkbox: Tự động tạo phòng đơn nếu lẻ
2. Hiển thị preview:
   - Số khách đã chọn (thủ công)
   - Số khách sẽ tự động phân phòng

### **Phase 3: Cập nhật logic `autoAssign()`**
1. Nhận tham số:
   - `$manual_customer_ids` (danh sách ID khách đã chọn thủ công)
   - `$max_customers_per_room` (số người/phòng, default 2)
2. Logic:
   - Bỏ qua khách trong `$manual_customer_ids`
   - Phân phòng với `max_capacity = $max_customers_per_room`

### **Phase 4: Tab "Kết quả" + Chỉnh sửa**
1. Hiển thị kết quả phân phòng (giữ như hiện tại)
2. Thêm chức năng:
   - Di chuyển khách giữa các phòng (dropdown hoặc drag-drop)
   - Xóa khách khỏi phòng
   - Thêm khách vào phòng (chọn từ danh sách chưa phân phòng)

---

## 📊 DATABASE CHANGES

**Không cần thay đổi database**, chỉ cần:
- Lưu `manual_customer_ids` trong session hoặc pass qua POST
- Lưu `max_customers_per_room` trong session hoặc pass qua POST

---

## 🎨 UI COMPONENTS CẦN

1. **Checkbox list** với select all
2. **Badge/Tag** để hiển thị yêu cầu đặc biệt
3. **Tab navigation** (có thể dùng JavaScript hoặc CSS)
4. **Form inputs** (number, checkbox, dropdown)
5. **Modal** hoặc **inline form** để chỉnh sửa phân phòng

---

## 🚀 NEXT STEPS

1. Implement Tab "Danh sách khách" với checkbox
2. Implement Tab "Cấu hình" với form
3. Update `autoAssign()` method để nhận tham số mới
4. Implement Tab "Kết quả" với chức năng chỉnh sửa

