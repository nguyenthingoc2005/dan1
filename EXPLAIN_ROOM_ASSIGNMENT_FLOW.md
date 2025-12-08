# GIẢI THÍCH: LUỒNG PHÂN PHÒNG

## 🔍 VẤN ĐỀ HIỆN TẠI

### **Lỗi đã sửa:**
- `check_in_date` và `check_out_date` là NOT NULL nhưng đang bị NULL
- **Giải pháp:** Tự động tính từ `itinerary_id` và `tour_schedule_id`

### **Cách tính ngày:**
```php
// check_in_date = start_date + (day_number - 1) ngày
// Ví dụ:
// - Tour bắt đầu: 01/01/2025
// - Đêm 1 (day_number = 1): check-in = 01/01/2025
// - Đêm 2 (day_number = 2): check-in = 02/01/2025
// - Đêm 3 (day_number = 3): check-in = 03/01/2025

// check_out_date = check_in_date + 1 ngày (check out ngày hôm sau)
```

---

## 📋 LUỒNG PHÂN PHÒNG HIỆN TẠI

### **BƯỚC 1: TẠO PHÒNG**
**Cách hiện tại:**
1. Admin vào tab "Phòng"
2. Chọn đêm (itinerary) từ dropdown
3. Điền thông tin:
   - Số phòng (optional)
   - Loại phòng (single/double/twin/triple/quad/family)
   - Sức chứa tối đa
   - Khách sạn (optional)
4. Click "Tạo phòng"

**Sau khi tạo:**
- Phòng được tạo với `status = 'pending'`
- `check_in_date` và `check_out_date` được tự động tính từ đêm đã chọn
- `actual_occupancy = 0` (chưa có khách)

---

### **BƯỚC 2: GÁN KHÁCH VÀO PHÒNG**
**Cách hiện tại:**
- ❌ **CHƯA CÓ** - Cần thêm chức năng này vào UI
- Hiện tại chỉ có logic trong Model (`assignCustomerToRoom()`)

**Cần thêm:**
1. Hiển thị danh sách khách chưa phân phòng
2. Form/Button để gán khách vào phòng
3. Hoặc drag & drop khách vào phòng

---

### **BƯỚC 3: PHÂN PHÒNG TỰ ĐỘNG**
**Cách hiện tại:**
- Click nút "Phân phòng tự động"
- ❌ **CHƯA CÓ LOGIC** - Chỉ có button, chưa implement

**Sẽ làm (Phase 3):**
1. Lấy danh sách khách đã thanh toán
2. Xử lý yêu cầu đặc biệt trước (đơn phòng, cùng phòng, tránh cùng phòng)
3. Phân phòng tự động theo giới tính:
   - Nhóm theo giới tính (Nam/Nữ)
   - Ưu tiên ghép 2 người/phòng (double/twin)
   - Nếu lẻ → ghép 3 người (triple)
   - Nếu vẫn lẻ → đơn phòng (có phụ phí)
4. Tạo `room_assignments` cho từng đêm
5. Gán khách vào `room_assignment_customers`

---

## 🤔 VẤN ĐỀ VỀ LUỒNG HIỆN TẠI

### **Luồng hiện tại có vẻ phức tạp vì:**
1. **Tạo phòng thủ công:** Admin phải tạo từng phòng một, sau đó mới gán khách
2. **Chưa có cách gán khách:** Chưa có UI để gán khách vào phòng
3. **Phân phòng tự động chưa hoạt động:** Chưa có logic

### **Luồng lý tưởng nên là:**

#### **OPTION 1: Phân phòng tự động hoàn toàn**
```
1. Admin click "Phân phòng tự động"
2. Hệ thống tự động:
   - Tạo phòng cần thiết
   - Phân khách vào phòng
   - Xử lý yêu cầu đặc biệt
3. Admin chỉ cần xem và điều chỉnh (nếu cần)
```

#### **OPTION 2: Tạo phòng trước, sau đó phân khách**
```
1. Admin tạo phòng (hoặc dùng phân phòng tự động để tạo phòng)
2. Admin gán khách vào phòng (thủ công hoặc tự động)
```

---

## 💡 ĐỀ XUẤT CẢI THIỆN

### **1. Thêm chức năng gán khách vào UI:**
- Hiển thị danh sách khách chưa phân phòng
- Có thể kéo thả hoặc click để gán vào phòng
- Hoặc form chọn phòng để gán khách

### **2. Cải thiện phân phòng tự động:**
- Implement đầy đủ logic (Phase 3)
- Có 2 mode:
  - **Mode 1:** Tự động tạo phòng + phân khách
  - **Mode 2:** Chỉ phân khách vào phòng đã có

### **3. Hiển thị trực quan hơn:**
- Hiển thị danh sách khách chưa phân phòng
- Hiển thị số chỗ trống trong từng phòng
- Có thể drag & drop khách vào phòng

---

## 🎯 NEXT STEPS

1. ✅ **Đã sửa:** Tự động tính `check_in_date` và `check_out_date`
2. ⏳ **Cần làm:** Thêm UI để gán khách vào phòng
3. ⏳ **Cần làm:** Implement logic phân phòng tự động (Phase 3)

