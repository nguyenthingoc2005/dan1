# 📅 PHÂN TÍCH TRANG LỊCH TOUR CHO STAFF TƯ VẤN

**Ngày phân tích:** 2024-12-XX  
**Mục tiêu:** Phân tích yêu cầu trang hiển thị lịch tour để staff tư vấn khách hàng

---

## 🎯 MỤC ĐÍCH

Staff cần một trang hiển thị lịch tour để:
1. **Tư vấn khách hàng** - Xem tour nào có lịch khởi hành, còn chỗ không
2. **Tra cứu nhanh** - Tìm tour theo ngày, theo tour, theo trạng thái
3. **Tạo booking nhanh** - Click vào lịch để tạo booking ngay

---

## 📋 THÔNG TIN CẦN HIỂN THỊ

### Thông tin cơ bản (BẮT BUỘC):
1. ✅ **Tour name** - Tên tour
2. ✅ **Tour code** - Mã tour
3. ✅ **Ngày khởi hành** - Start date
4. ✅ **Ngày kết thúc** - End date
5. ✅ **Số chỗ còn lại** - Available spots (quota - booked)
6. ✅ **Giá người lớn** - Adult price
7. ✅ **Giá trẻ em** - Child price (nếu có)
8. ✅ **Giá em bé** - Infant price (nếu có)
9. ✅ **Trạng thái** - Status (open/closed/completed/cancelled)
10. ✅ **Điểm khởi hành** - Departure location

### Thông tin bổ sung (NÊN CÓ):
11. ⭐ **HDV** - Hướng dẫn viên (nếu đã phân công)
12. ⭐ **Số ngày/đêm** - Duration (X ngày Y đêm)
13. ⭐ **Tỷ lệ đầy** - Fill rate (booked/quota %)
14. ⭐ **Danh mục** - Category (Trong nước/Ngoài nước)
15. ⭐ **Loại tour** - Tour type (Public/Custom)

### Thông tin nâng cao (CÓ THỂ THÊM SAU):
16. 🔹 **Số booking** - Số lượng booking đã có
17. 🔹 **Ghi chú HDV** - Guide notes (nếu có)
18. 🔹 **Lịch sử thay đổi HDV** - Guide change history

---

## 🔍 FILTER CẦN CÓ

### Filter cơ bản (BẮT BUỘC):
1. ✅ **Tour** - Dropdown chọn tour (tất cả tours active + approved)
2. ✅ **Từ ngày** - Start date filter (>=)
3. ✅ **Đến ngày** - End date filter (<=)
4. ✅ **Trạng thái** - Status (open/closed/completed/cancelled)
5. ✅ **Danh mục** - Category (Trong nước/Ngoài nước)

### Filter nâng cao (NÊN CÓ):
6. ⭐ **HDV** - Filter theo hướng dẫn viên
7. ⭐ **Còn chỗ** - Chỉ hiển thị schedules còn chỗ (available > 0)
8. ⭐ **Sắp hết chỗ** - Chỉ hiển thị schedules có available < 10% quota

### Filter tìm kiếm (CÓ THỂ THÊM):
9. 🔹 **Search** - Tìm kiếm theo tour name, tour code

---

## 📊 VIEW MODES

### 1. **Table View** (Mặc định - giống admin)
- ✅ Dễ implement
- ✅ Hiển thị nhiều thông tin
- ✅ Dễ sort, filter
- ❌ Khó xem theo timeline

### 2. **Card View** (Nên có)
- ✅ Dễ xem, đẹp mắt
- ✅ Hiển thị đầy đủ thông tin quan trọng
- ✅ Dễ click để tạo booking
- ❌ Tốn không gian màn hình

### 3. **Calendar View** (Có thể thêm sau)
- ✅ Xem theo timeline rất trực quan
- ✅ Dễ thấy tour nào vào ngày nào
- ❌ Phức tạp implement
- ❌ Khó hiển thị nhiều thông tin

---

## 🎨 UI/UX ĐỀ XUẤT

### Layout:
```
┌─────────────────────────────────────────────────────────┐
│ 📅 LỊCH TOUR KHỞI HÀNH                    [Tạo Booking] │
├─────────────────────────────────────────────────────────┤
│ [Filter: Tour ▼] [Từ ngày] [Đến ngày] [Status ▼] [Tìm] │
├─────────────────────────────────────────────────────────┤
│ 📊 Stats: 15 đang mở | 8 sắp hết chỗ | 3 đã đầy        │
├─────────────────────────────────────────────────────────┤
│ [Table View] [Card View] [Calendar View]               │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  TABLE/CARD VIEW CONTENT                                │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Color Coding:
- 🟢 **Open** - Xanh lá (còn chỗ)
- 🟡 **Open (sắp hết)** - Vàng (available < 10% quota)
- 🔴 **Closed** - Đỏ (đóng bán)
- 🔵 **Completed** - Xanh dương (đã hoàn thành)
- ⚫ **Cancelled** - Xám (đã hủy)

### Actions:
- ✅ **Xem chi tiết** - Link đến schedule detail (read-only)
- ✅ **Tạo booking** - Link đến booking create với tour_id và start_date pre-filled
- ❌ **Không có Edit/Delete** - Staff không có quyền

---

## 🔐 PERMISSIONS

### Staff có thể:
- ✅ Xem **TẤT CẢ** schedules (không filter ownership)
- ✅ Filter, search schedules
- ✅ Xem chi tiết schedule (read-only)
- ✅ Tạo booking từ schedule (link đến booking create)

### Staff KHÔNG thể:
- ❌ Edit schedule
- ❌ Delete schedule
- ❌ Change status
- ❌ Assign guide

---

## 📍 ROUTING

### Route đề xuất:
```
?act=staff-schedules&action=index
```

### Controller:
```
app/controllers/staff/ScheduleController.php
```

### View:
```
app/views/staff/schedules/index.php
```

---

## 🔄 WORKFLOW

### Flow tư vấn khách hàng:

```
1. Khách hàng hỏi: "Tour Đà Lạt có lịch nào không?"
   ↓
2. Staff vào: ?act=staff-schedules
   ↓
3. Filter: Tour = "Đà Lạt 3N2Đ", Status = "open"
   ↓
4. Xem danh sách:
   - 15/12/2024: Còn 5 chỗ, Giá: 2.500.000đ/người lớn
   - 20/12/2024: Còn 10 chỗ, Giá: 2.500.000đ/người lớn
   - 25/12/2024: Đã đầy
   ↓
5. Tư vấn khách: "Có 2 lịch còn chỗ, ngày 15/12 và 20/12"
   ↓
6. Khách chọn: "Tôi muốn ngày 20/12"
   ↓
7. Staff click "Tạo booking" → Pre-fill tour_id và start_date
   ↓
8. Tạo booking cho khách
```

---

## 📊 SO SÁNH VỚI ADMIN

| Feature | Admin | Staff | Ghi chú |
|---------|-------|-------|---------|
| **Xem schedules** | ✅ Tất cả | ✅ Tất cả | Giống nhau |
| **Filter** | ✅ Đầy đủ | ✅ Đầy đủ | Giống nhau |
| **Create schedule** | ✅ | ❌ | Staff không tạo schedule |
| **Edit schedule** | ✅ | ❌ | Staff không edit |
| **Delete schedule** | ✅ | ❌ | Staff không delete |
| **Change status** | ✅ | ❌ | Staff không change status |
| **Assign guide** | ✅ | ❌ | Staff không assign guide |
| **Tạo booking từ schedule** | ✅ | ✅ | Giống nhau |
| **Xem chi tiết** | ✅ | ✅ (read-only) | Staff chỉ xem |

---

## ✅ IMPLEMENTATION PLAN

### Bước 1: Tạo Controller
- File: `app/controllers/staff/ScheduleController.php`
- Method: `index()` - Hiển thị danh sách schedules với filters

### Bước 2: Tạo View
- File: `app/views/staff/schedules/index.php`
- Layout: Table view (giống admin nhưng không có action buttons edit/delete)
- Stats cards: Số schedules đang mở, sắp hết chỗ, đã đầy

### Bước 3: Thêm Route
- File: `routes/staff.php`
- Route: `case 'schedules':`

### Bước 4: Thêm Menu Item
- File: `common/MenuHelper.php`
- Thêm "Lịch Tour" vào staff menu

### Bước 5: Thêm Link "Tạo Booking"
- Trong view, mỗi schedule có button "Tạo Booking"
- Link: `?act=staff-bookings&action=create&tour_id=X&start_date=Y`
- Pre-fill tour và start_date trong booking form

---

## 🎯 THÔNG TIN QUAN TRỌNG CHO TƯ VẤN

### Khi khách hỏi "Tour này có lịch nào?"
Staff cần trả lời:
1. ✅ **Có bao nhiêu lịch?** - Số lượng schedules open
2. ✅ **Ngày nào?** - Start dates
3. ✅ **Còn chỗ không?** - Available spots
4. ✅ **Giá bao nhiêu?** - Adult/Child/Infant prices
5. ✅ **HDV nào?** - Guide name (nếu đã phân công)

### Khi khách hỏi "Ngày X có tour nào không?"
Staff cần:
1. ✅ Filter theo start_date = X
2. ✅ Hiển thị tất cả tours có lịch ngày X
3. ✅ Hiển thị số chỗ còn lại, giá cả

### Khi khách hỏi "Tour này giá bao nhiêu?"
Staff cần:
1. ✅ Hiển thị giá từ schedule (nếu có)
2. ✅ Hoặc giá từ tour (nếu schedule không có giá riêng)
3. ✅ Phân biệt giá adult/child/infant

---

## 🔴 VẤN ĐỀ CẦN XỬ LÝ

### 1. **Custom Tour không có schedule cố định**
- **Vấn đề:** Custom tour không có schedule trước, chỉ tạo khi có booking
- **Giải pháp:** 
  - Hiển thị custom tours riêng (không có schedule)
  - Hoặc hiển thị "Có thể đặt theo yêu cầu" với giá từ tour

### 2. **Schedule chưa có giá riêng**
- **Vấn đề:** Schedule có thể không có giá, dùng giá từ tour
- **Giải pháp:** 
  - Priority: Schedule price > Tour price
  - Hiển thị rõ "Giá từ tour" hoặc "Giá đặc biệt"

### 3. **Schedule status = 'cancelled'**
- **Vấn đề:** Có nên hiển thị cancelled schedules không?
- **Giải pháp:** 
  - Mặc định: KHÔNG hiển thị cancelled
  - Có filter để xem cancelled (nếu cần)

---

## 📊 TÓM TẮT

### Cần implement:
1. ✅ **Controller:** `Staff\ScheduleController`
2. ✅ **View:** `staff/schedules/index.php`
3. ✅ **Route:** `staff-schedules`
4. ✅ **Menu:** Thêm "Lịch Tour" vào staff menu
5. ✅ **Link:** Button "Tạo Booking" với pre-filled data

### Thông tin hiển thị:
- Tour name, code, dates
- Available spots, prices
- Status, guide (nếu có)
- Link tạo booking

### Filter:
- Tour, dates, status, category
- Còn chỗ, sắp hết chỗ

---

**Kết thúc phân tích**

