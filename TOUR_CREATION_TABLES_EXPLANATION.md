# 📋 GIẢI THÍCH CÁC BẢNG LIÊN QUAN ĐẾN TẠO TOUR

## 📚 TỔNG QUAN

Khi tạo tour, hệ thống sử dụng **9 bảng chính** để lưu trữ thông tin:

1. **`tours`** - Thông tin cơ bản của tour
2. **`itineraries`** - Lịch trình từng ngày
3. **`itinerary_day_services`** - Dịch vụ theo từng ngày (để tính chi phí)
4. **`tour_images`** - Hình ảnh tour
5. **`tour_highlights`** - Điểm nổi bật
6. **`tour_included_excluded`** - Bao gồm/Không bao gồm
7. **`tour_policies`** - Chính sách tour (junction table)
8. **`policies`** - Bảng chính sách (tham khảo)
9. **`tour_services`** - Dịch vụ tour (backward compatible - ít dùng)

---

## 1️⃣ BẢNG `tours` - THÔNG TIN CHÍNH CỦA TOUR

**Mục đích:** Lưu trữ tất cả thông tin cơ bản của tour.

### Các cột quan trọng:

| Cột | Kiểu | Mô tả | Ví dụ |
|-----|------|-------|-------|
| `id` | INT (PK) | ID tự tăng | 1, 2, 3... |
| `tour_code` | VARCHAR(50) | Mã tour (unique) | "TOUR_2024_001" |
| `name` | VARCHAR(200) | Tên tour | "Tour Đà Lạt 3 ngày 2 đêm" |
| `thumbnail` | VARCHAR(255) | URL hình đại diện | "/uploads/tours/thumb.jpg" |
| `introduction` | TEXT | Giới thiệu ngắn | "Tour khám phá..." |
| `description` | TEXT | Mô tả chi tiết (HTML) | "<p>Chi tiết tour...</p>" |

### Thông tin thời gian:

| Cột | Kiểu | Mô tả | Default |
|-----|------|-------|---------|
| `duration_days` | INT | Số ngày | **Bắt buộc** |
| `duration_nights` | INT | Số đêm | 0 |
| `departure_location` | VARCHAR(200) | Điểm khởi hành | NULL |

### Thông tin số lượng khách:

| Cột | Kiểu | Mô tả | Default |
|-----|------|-------|---------|
| `min_participants` | INT | Số người tối thiểu | 15 |
| `max_participants` | INT | Số người tối đa | 45 |
| `booking_deadline_days` | INT | Số ngày tối thiểu trước khi khởi hành để đặt | 1 |

### Giá tour:

| Cột | Kiểu | Mô tả | Default |
|-----|------|-------|---------|
| `adult_price` | DECIMAL(15,2) | Giá người lớn | **Bắt buộc** |
| `child_price` | DECIMAL(15,2) | Giá trẻ em | 0.00 |
| `infant_price` | DECIMAL(15,2) | Giá em bé | 0.00 |
| `estimated_cost_per_person` | DECIMAL(15,2) | Chi phí ước tính/người (tự tính) | NULL |

### Chi phí cố định (Fixed Costs):

| Cột | Kiểu | Mô tả | Default |
|-----|------|-------|---------|
| `fixed_cost_guide` | DECIMAL(15,2) | Lương HDV (chia cho số người) | 0.00 |
| `fixed_cost_management` | DECIMAL(15,2) | Chi phí quản lý | 0.00 |
| `fixed_cost_marketing` | DECIMAL(15,2) | Chi phí marketing | 0.00 |
| `fixed_cost_other` | DECIMAL(15,2) | Chi phí khác | 0.00 |

**Công thức tính:**
```
Chi phí cố định/người = (fixed_cost_guide + fixed_cost_management + fixed_cost_marketing + fixed_cost_other) ÷ min_participants
```

### Phần trăm & Trạng thái:

| Cột | Kiểu | Giá trị | Mô tả |
|-----|------|---------|-------|
| `deposit_percentage` | DECIMAL(5,2) | 30.00 | Phần trăm đặt cọc |
| `markup_percentage` | DECIMAL(5,2) | 0.00 | ⚠️ DEPRECATED - Không dùng nữa |
| `tour_type` | ENUM | 'public', 'custom' | Loại tour |
| `approval_status` | ENUM | 'pending', 'approved', 'rejected' | Trạng thái duyệt |
| `status` | ENUM | 'active', 'inactive', 'draft' | Trạng thái tour |

### Metadata:

| Cột | Kiểu | Mô tả |
|-----|------|-------|
| `approved_by` | INT (FK) | User ID người duyệt |
| `approved_at` | TIMESTAMP | Thời gian duyệt |
| `rejection_reason` | TEXT | Lý do từ chối |
| `parent_tour_id` | INT (FK) | ID tour template (nếu tạo từ template) |
| `created_by` | INT (FK) | User ID người tạo |
| `created_at` | TIMESTAMP | Thời gian tạo |
| `updated_at` | TIMESTAMP | Thời gian cập nhật |

---

## 2️⃣ BẢNG `itineraries` - LỊCH TRÌNH TỪNG NGÀY

**Mục đích:** Lưu trữ lịch trình chi tiết cho từng ngày của tour.

| Cột | Kiểu | Mô tả | Ví dụ |
|-----|------|-------|-------|
| `id` | INT (PK) | ID tự tăng | 1, 2, 3... |
| `tour_id` | INT (FK) | ID tour | Foreign key → tours.id |
| `destination_id` | INT (FK) | ID điểm đến | Foreign key → destinations.id |
| `day_number` | INT | Số thứ tự ngày | 1, 2, 3... |
| `title` | VARCHAR(200) | Tiêu đề ngày | "Ngày 1: Khởi hành Đà Lạt" |
| `description` | TEXT | Mô tả chi tiết (HTML) | "<p>Buổi sáng...</p>" |
| `meals` | JSON | Thông tin bữa ăn | `{"breakfast": true, "lunch": true}` |
| `accommodation` | VARCHAR(200) | Nơi ở | "Khách sạn 3 sao" |
| `arrival_time` | TIME | Giờ đến | "08:00:00" |
| `departure_time` | TIME | Giờ khởi hành | "17:00:00" |
| `display_order` | INT | Thứ tự hiển thị | 0, 1, 2... |

**Lưu ý:**
- Mỗi tour có số bản ghi = `duration_days`
- `day_number` phải là 1, 2, 3... liên tục
- `destination_id` có thể NULL nếu chưa chọn điểm đến

---

## 3️⃣ BẢNG `itinerary_day_services` - DỊCH VỤ THEO TỪNG NGÀY ⭐

**Mục đích:** Lưu trữ các dịch vụ (khách sạn, ăn uống, vé tham quan...) cho từng ngày. **Đây là bảng quan trọng để tính chi phí tour.**

| Cột | Kiểu | Mô tả | Ví dụ |
|-----|------|-------|-------|
| `id` | INT (PK) | ID tự tăng | 1, 2, 3... |
| `itinerary_id` | INT (FK) | ID lịch trình | Foreign key → itineraries.id |
| `service_id` | INT (FK) | ID dịch vụ | Foreign key → services.id |
| `service_provider_id` | INT (FK) | ID nhà cung cấp | Foreign key → service_providers.id (NULL được) |
| `service_name` | VARCHAR(200) | Tên dịch vụ (snapshot) | "Khách sạn 3 sao" |
| `unit_price` | DECIMAL(15,2) | **Đơn giá/người** | 200000.00 |
| `quantity` | DECIMAL(10,2) | Số lượng | 1.00 (1 bữa, 1 đêm...) |
| `unit` | VARCHAR(50) | Đơn vị | "bữa", "đêm", "vé" |
| `is_included_in_price` | TINYINT(1) | Bao gồm trong giá tour? | 1 = Có, 0 = Không |
| `notes` | TEXT | Ghi chú | "Bao gồm bữa sáng" |
| `created_at` | TIMESTAMP | Thời gian tạo | Auto |
| `updated_at` | TIMESTAMP | Thời gian cập nhật | Auto |

**Công thức tính chi phí dịch vụ/người:**
```
Chi phí dịch vụ/người = Σ(unit_price × quantity) 
                       với điều kiện is_included_in_price = 1
```

**Ví dụ:**
- Ngày 1: Khách sạn (200,000đ/người), Ăn trưa (50,000đ/người)
- Ngày 2: Vé tham quan (100,000đ/người), Ăn tối (80,000đ/người)
- **Tổng chi phí dịch vụ/người = 200,000 + 50,000 + 100,000 + 80,000 = 430,000đ**

---

## 4️⃣ BẢNG `tour_images` - HÌNH ẢNH TOUR

**Mục đích:** Lưu trữ các hình ảnh của tour.

| Cột | Kiểu | Mô tả | Ví dụ |
|-----|------|-------|-------|
| `id` | INT (PK) | ID tự tăng | 1, 2, 3... |
| `tour_id` | INT (FK) | ID tour | Foreign key → tours.id |
| `image_url` | VARCHAR(255) | URL hình ảnh | "/uploads/tours/123/image1.jpg" |
| `caption` | VARCHAR(255) | Chú thích | "Cảnh đẹp Đà Lạt" |
| `is_primary` | TINYINT(1) | Hình chính? | 1 = Hình đầu tiên (thumbnail) |
| `display_order` | INT | Thứ tự hiển thị | 0, 1, 2... |
| `created_at` | TIMESTAMP | Thời gian tạo | Auto |

**Lưu ý:**
- Hình đầu tiên (`display_order = 0` và `is_primary = 1`) sẽ được dùng làm thumbnail
- Tối đa 10 hình/tour

---

## 5️⃣ BẢNG `tour_highlights` - ĐIỂM NỔI BẬT

**Mục đích:** Lưu trữ các điểm nổi bật của tour (mỗi dòng = 1 điểm).

| Cột | Kiểu | Mô tả | Ví dụ |
|-----|------|-------|-------|
| `id` | INT (PK) | ID tự tăng | 1, 2, 3... |
| `tour_id` | INT (FK) | ID tour | Foreign key → tours.id |
| `highlight` | TEXT | Nội dung điểm nổi bật | "Tham quan vườn hoa Đà Lạt" |
| `display_order` | INT | Thứ tự hiển thị | 0, 1, 2... |

**Lưu ý:**
- Mỗi tour có nhiều highlights (1-many)
- Thường lưu từ textarea (mỗi dòng = 1 highlight)

---

## 6️⃣ BẢNG `tour_included_excluded` - BAO GỒM/KHÔNG BAO GỒM

**Mục đích:** Lưu trữ danh sách các mục bao gồm và không bao gồm trong giá tour.

| Cột | Kiểu | Mô tả | Ví dụ |
|-----|------|-------|-------|
| `id` | INT (PK) | ID tự tăng | 1, 2, 3... |
| `tour_id` | INT (FK) | ID tour | Foreign key → tours.id |
| `type` | ENUM | Loại | 'included' hoặc 'excluded' |
| `item` | TEXT | Nội dung mục | "Xe đưa đón" hoặc "Chi phí cá nhân" |
| `display_order` | INT | Thứ tự hiển thị | 0, 1, 2... |

**Ví dụ:**
```
type = 'included':
- "Xe đưa đón theo chương trình"
- "Khách sạn 3 sao"
- "Các bữa ăn theo chương trình"

type = 'excluded':
- "Chi phí cá nhân"
- "Phụ thu phòng đơn"
- "Tiền tip cho HDV"
```

---

## 7️⃣ BẢNG `policies` - CHÍNH SÁCH

**Mục đích:** Lưu trữ các chính sách có sẵn (hủy tour, đổi tour, hoàn tiền...).

| Cột | Kiểu | Mô tả | Ví dụ |
|-----|------|-------|-------|
| `id` | INT (PK) | ID tự tăng | 1, 2, 3... |
| `name` | VARCHAR(200) | Tên chính sách | "Chính sách hủy tour" |
| `description` | TEXT | Mô tả ngắn | "Quy định về hủy tour..." |
| `policy_type` | VARCHAR(50) | Loại chính sách | "cancellation", "refund" |
| `content` | TEXT | Nội dung chi tiết (HTML) | "<p>Chi tiết chính sách...</p>" |
| `status` | ENUM | Trạng thái | 'active', 'inactive' |
| `created_at` | TIMESTAMP | Thời gian tạo | Auto |
| `updated_at` | TIMESTAMP | Thời gian cập nhật | Auto |

**Lưu ý:**
- Bảng này là **master data** (dữ liệu chung, không thuộc tour cụ thể)
- Admin tạo một lần, sau đó các tour có thể chọn (many-to-many)

---

## 8️⃣ BẢNG `tour_policies` - TOUR - CHÍNH SÁCH (JUNCTION)

**Mục đích:** Liên kết tour với các chính sách (many-to-many).

| Cột | Kiểu | Mô tả |
|-----|------|-------|
| `id` | INT (PK) | ID tự tăng |
| `tour_id` | INT (FK) | ID tour → tours.id |
| `policy_id` | INT (FK) | ID chính sách → policies.id |

**Lưu ý:**
- Đây là **junction table** (bảng trung gian)
- 1 tour có thể có nhiều policies
- 1 policy có thể được dùng bởi nhiều tours

---

## 9️⃣ BẢNG `tour_services` - DỊCH VỤ TOUR (BACKWARD COMPATIBLE)

**Mục đích:** ⚠️ **DEPRECATED** - Giữ lại để backward compatible. **Nên dùng `itinerary_day_services` thay vì bảng này.**

| Cột | Kiểu | Mô tả |
|-----|------|-------|
| `id` | INT (PK) | ID tự tăng |
| `tour_id` | INT (FK) | ID tour |
| `service_id` | INT (FK) | ID dịch vụ |
| `service_name` | VARCHAR(200) | Tên dịch vụ |
| `calculation_type` | ENUM | Loại tính: 'per_person', 'per_group', 'per_day', 'fixed' |
| `fixed_quantity` | INT | Số lượng cố định |
| `group_size` | INT | Kích thước nhóm |
| `unit_price` | DECIMAL(15,2) | Đơn giá |
| `unit` | VARCHAR(50) | Đơn vị |
| `notes` | TEXT | Ghi chú |
| `is_included_in_price` | TINYINT(1) | Bao gồm trong giá? |

**Lưu ý:**
- ❌ **Không nên dùng bảng này nữa**
- ✅ **Dùng `itinerary_day_services` thay thế** (chi tiết hơn, theo từng ngày)

---

## 🔗 MỐI QUAN HỆ GIỮA CÁC BẢNG

```
tours (1)
  ├── itineraries (many) - 1 tour có nhiều ngày
  │     └── itinerary_day_services (many) - Mỗi ngày có nhiều dịch vụ
  │
  ├── tour_images (many) - 1 tour có nhiều hình
  ├── tour_highlights (many) - 1 tour có nhiều điểm nổi bật
  ├── tour_included_excluded (many) - 1 tour có nhiều mục bao gồm/không
  │
  └── tour_policies (many) - Junction table
        └── policies (many) - 1 tour có thể chọn nhiều policies
```

---

## 📝 QUY TRÌNH TẠO TOUR (THEO THỨ TỰ)

1. **Tạo bản ghi trong `tours`** → Lấy `tour_id`
2. **Tạo các bản ghi trong `itineraries`** (số lượng = `duration_days`)
3. **Tạo các bản ghi trong `itinerary_day_services`** (dịch vụ cho từng ngày)
4. **Tạo các bản ghi trong `tour_images`** (hình ảnh)
5. **Tạo các bản ghi trong `tour_highlights`** (điểm nổi bật)
6. **Tạo các bản ghi trong `tour_included_excluded`** (bao gồm/không)
7. **Tạo các bản ghi trong `tour_policies`** (chọn policies)

---

## 💡 LƯU Ý QUAN TRỌNG

### 1. Tính chi phí tour:

```
Chi phí dịch vụ/người = Σ(unit_price × quantity) từ itinerary_day_services
                        với điều kiện is_included_in_price = 1

Chi phí cố định/người = (fixed_cost_guide + fixed_cost_management + 
                          fixed_cost_marketing + fixed_cost_other) ÷ min_participants

Tổng chi phí/người = Chi phí dịch vụ/người + Chi phí cố định/người

Giá bán/người = Tổng chi phí/người (không có markup)
```

### 2. Foreign Keys quan trọng:

- `itineraries.tour_id` → `tours.id` (CASCADE DELETE)
- `itinerary_day_services.itinerary_id` → `itineraries.id` (CASCADE DELETE)
- `itinerary_day_services.service_id` → `services.id`
- `tour_policies.tour_id` → `tours.id` (CASCADE DELETE)
- `tour_policies.policy_id` → `policies.id` (CASCADE DELETE)

### 3. Constraints:

- `tour_code` phải UNIQUE
- `duration_days` phải > 0
- `duration_nights` ≤ `duration_days`
- `adult_price` phải > 0
- Số bản ghi `itineraries` = `duration_days`

---

## ✅ KẾT LUẬN

Các bảng trên đủ để tạo một tour hoàn chỉnh. Quan trọng nhất là:
- ✅ **`tours`** - Thông tin cơ bản
- ✅ **`itineraries`** - Lịch trình
- ✅ **`itinerary_day_services`** - Dịch vụ (để tính chi phí)
- ✅ **`tour_images`** - Hình ảnh
- ✅ **`tour_highlights`** - Điểm nổi bật
- ✅ **`tour_included_excluded`** - Bao gồm/không
- ✅ **`tour_policies`** - Chính sách

---

**Ngày tạo:** 2024-12-06  
**Phiên bản:** 1.0

