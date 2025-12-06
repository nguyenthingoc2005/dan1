# GIẢI THÍCH CÁC BẢNG VÀ CỘT LIÊN QUAN ĐẾN TẠO TOUR

## 📋 TỔNG QUAN

Khi tạo tour, hệ thống sử dụng các bảng sau:
1. **tours** - Thông tin chính của tour
2. **itineraries** - Lịch trình từng ngày
3. **itinerary_day_services** - Dịch vụ theo từng ngày (để tính chi phí)
4. **tour_images** - Hình ảnh tour
5. **tour_highlights** - Điểm nổi bật
6. **tour_included_excluded** - Bao gồm/Không bao gồm
7. **tour_faqs** - Câu hỏi thường gặp
8. **tour_policies** - Chính sách tour
9. **tour_services** - Dịch vụ tour (DEPRECATED - có thể bỏ)
10. **tour_schedules** - Lịch khởi hành (không dùng khi tạo tour)

---

## 1. BẢNG `tours` - THÔNG TIN CHÍNH TOUR

### ✅ CÁC CỘT ĐANG DÙNG:

| Cột | Kiểu | Mô tả | Bắt buộc |
|-----|------|-------|-----------|
| `id` | INT | ID tự động | ✅ |
| `tour_code` | VARCHAR(50) | Mã tour (tự động generate) | ✅ |
| `name` | VARCHAR(200) | Tên tour | ✅ |
| `thumbnail` | VARCHAR(255) | Ảnh đại diện | ⚠️ Optional |
| `introduction` | TEXT | Giới thiệu ngắn | ⚠️ Optional |
| `description` | TEXT | Mô tả chi tiết (HTML) | ⚠️ Optional |
| `duration_days` | INT | Số ngày | ✅ |
| `duration_nights` | INT | Số đêm | ✅ |
| `departure_location` | VARCHAR(200) | Điểm khởi hành | ⚠️ Optional |
| `min_participants` | INT | Số người tối thiểu | ✅ (default: 15) |
| `max_participants` | INT | Số người tối đa | ✅ (default: 45) |
| `adult_price` | DECIMAL(15,2) | Giá người lớn | ✅ |
| `child_price` | DECIMAL(15,2) | Giá trẻ em | ✅ |
| `infant_price` | DECIMAL(15,2) | Giá em bé | ✅ (default: 0) |
| `estimated_cost_per_person` | DECIMAL(15,2) | Chi phí ước tính/người | ⚠️ Optional |
| `deposit_percentage` | DECIMAL(5,2) | % đặt cọc | ✅ (default: 30) |
| `booking_deadline_days` | INT | Deadline đặt tour (ngày) | ✅ (default: 1) |
| `fixed_cost_guide` | DECIMAL(15,2) | Chi phí HDV cố định | ✅ (default: 0) |
| `fixed_cost_management` | DECIMAL(15,2) | Chi phí quản lý cố định | ✅ (default: 0) |
| `fixed_cost_marketing` | DECIMAL(15,2) | Chi phí marketing cố định | ✅ (default: 0) |
| `fixed_cost_other` | DECIMAL(15,2) | Chi phí khác cố định | ✅ (default: 0) |
| `tour_type` | ENUM | Loại tour: 'public' hoặc 'custom' | ✅ (default: 'public') |
| `approval_status` | ENUM | Trạng thái duyệt | ✅ (default: 'pending') |
| `approved_by` | INT | Người duyệt | ⚠️ Optional |
| `approved_at` | TIMESTAMP | Thời gian duyệt | ⚠️ Optional |
| `rejection_reason` | TEXT | Lý do từ chối | ⚠️ Optional |
| `status` | ENUM | Trạng thái: 'draft', 'active', 'inactive' | ✅ (default: 'draft') |
| `created_by` | INT | Người tạo | ✅ |
| `created_at` | TIMESTAMP | Thời gian tạo | ✅ |
| `updated_at` | TIMESTAMP | Thời gian cập nhật | ✅ |
| `parent_tour_id` | INT | Tour gốc (nếu clone) | ⚠️ Optional |

### ❌ CÁC CỘT CÓ THỂ BỎ:

| Cột | Lý do |
|-----|-------|
| `markup_percentage` | ⚠️ **DEPRECATED** - Không dùng nữa, chỉ giữ để backward compatible |

### 💡 GỢI Ý:

- **Có thể bỏ**: `markup_percentage` (đã có comment DEPRECATED)
- **Có thể tối ưu**: `estimated_cost_per_person` - có thể tính tự động từ day_services

---

## 2. BẢNG `itineraries` - LỊCH TRÌNH TỪNG NGÀY

### ✅ CÁC CỘT ĐANG DÙNG:

| Cột | Kiểu | Mô tả | Bắt buộc |
|-----|------|-------|-----------|
| `id` | INT | ID tự động | ✅ |
| `tour_id` | INT | Foreign key → tours | ✅ |
| `destination_id` | INT | Foreign key → destinations | ⚠️ Optional |
| `day_number` | INT | Số thứ tự ngày (1, 2, 3...) | ✅ |
| `title` | VARCHAR(200) | Tiêu đề ngày | ⚠️ Optional |
| `description` | TEXT | Mô tả chi tiết (HTML với TinyMCE) | ✅ |
| `display_order` | INT | Thứ tự hiển thị | ✅ (default: 0) |

### ❌ CÁC CỘT CÓ THỂ BỎ:

| Cột | Lý do |
|-----|-------|
| `meals` | JSON - Có thể lưu trong description hoặc dùng day_services |
| `accommodation` | VARCHAR(200) - Có thể lưu trong description hoặc dùng day_services |
| `arrival_time` | TIME - Không cần thiết, có thể ghi trong description |
| `departure_time` | TIME - Không cần thiết, có thể ghi trong description |

### 💡 GỢI Ý:

- **Nên bỏ**: `meals`, `accommodation`, `arrival_time`, `departure_time` 
  - Lý do: Thông tin này có thể ghi trong `description` (TinyMCE) hoặc quản lý qua `itinerary_day_services`
- **Giữ lại**: `destination_id`, `title`, `description`, `day_number`

---

## 3. BẢNG `itinerary_day_services` - DỊCH VỤ THEO TỪNG NGÀY

### ✅ TẤT CẢ CỘT ĐANG DÙNG:

| Cột | Kiểu | Mô tả | Bắt buộc |
|-----|------|-------|-----------|
| `id` | INT | ID tự động | ✅ |
| `itinerary_id` | INT | Foreign key → itineraries | ✅ |
| `service_id` | INT | Foreign key → services | ✅ |
| `service_provider_id` | INT | Foreign key → service_providers | ⚠️ Optional |
| `service_name` | VARCHAR(200) | Tên dịch vụ (snapshot) | ⚠️ Optional |
| `unit_price` | DECIMAL(15,2) | Đơn giá/người | ✅ |
| `quantity` | DECIMAL(10,2) | Số lượng (VD: 1 bữa, 1 đêm) | ✅ (default: 1) |
| `unit` | VARCHAR(50) | Đơn vị (VD: "bữa", "đêm", "vé") | ⚠️ Optional |
| `is_included_in_price` | TINYINT(1) | Bao gồm trong giá tour | ✅ (default: 1) |
| `notes` | TEXT | Ghi chú | ⚠️ Optional |
| `created_at` | TIMESTAMP | Thời gian tạo | ✅ |
| `updated_at` | TIMESTAMP | Thời gian cập nhật | ✅ |

### 💡 GỢI Ý:

- **Tất cả cột đều cần thiết** - Bảng này dùng để tính chi phí tour
- **Không nên bỏ cột nào**

---

## 4. BẢNG `tour_images` - HÌNH ẢNH TOUR

### ✅ TẤT CẢ CỘT ĐANG DÙNG:

| Cột | Kiểu | Mô tả | Bắt buộc |
|-----|------|-------|-----------|
| `id` | INT | ID tự động | ✅ |
| `tour_id` | INT | Foreign key → tours | ✅ |
| `image_url` | VARCHAR(255) | Đường dẫn ảnh | ✅ |
| `caption` | VARCHAR(255) | Chú thích ảnh | ⚠️ Optional |
| `is_primary` | TINYINT(1) | Ảnh chính | ✅ (default: 0) |
| `display_order` | INT | Thứ tự hiển thị | ✅ (default: 0) |
| `created_at` | TIMESTAMP | Thời gian tạo | ✅ |

### 💡 GỢI Ý:

- **Tất cả cột đều cần thiết** - Quản lý gallery ảnh tour
- **Không nên bỏ cột nào**

---

## 5. BẢNG `tour_highlights` - ĐIỂM NỔI BẬT

### ✅ CÁC CỘT ĐANG DÙNG:

| Cột | Kiểu | Mô tả | Bắt buộc |
|-----|------|-------|-----------|
| `id` | INT | ID tự động | ✅ |
| `tour_id` | INT | Foreign key → tours | ✅ |
| `highlight` | TEXT | Nội dung điểm nổi bật | ✅ |
| `display_order` | INT | Thứ tự hiển thị | ✅ (default: 0) |

### 💡 GỢI Ý:

- **Tất cả cột đều cần thiết**
- **Có thể tối ưu**: Lưu dạng JSON trong bảng `tours` thay vì bảng riêng (nếu không cần query riêng)

---

## 6. BẢNG `tour_included_excluded` - BAO GỒM/KHÔNG BAO GỒM

### ✅ CÁC CỘT ĐANG DÙNG:

| Cột | Kiểu | Mô tả | Bắt buộc |
|-----|------|-------|-----------|
| `id` | INT | ID tự động | ✅ |
| `tour_id` | INT | Foreign key → tours | ✅ |
| `type` | ENUM | 'included' hoặc 'excluded' | ✅ |
| `item` | TEXT | Nội dung | ✅ |
| `display_order` | INT | Thứ tự hiển thị | ✅ (default: 0) |

### 💡 GỢI Ý:

- **Tất cả cột đều cần thiết**
- **Có thể tối ưu**: Lưu dạng JSON trong bảng `tours` thay vì bảng riêng (nếu không cần query riêng)

---

## 7. BẢNG `tour_faqs` - CÂU HỎI THƯỜNG GẶP

### ✅ CÁC CỘT ĐANG DÙNG:

| Cột | Kiểu | Mô tả | Bắt buộc |
|-----|------|-------|-----------|
| `id` | INT | ID tự động | ✅ |
| `tour_id` | INT | Foreign key → tours | ✅ |
| `question` | TEXT | Câu hỏi | ✅ |
| `answer` | TEXT | Câu trả lời | ✅ |
| `display_order` | INT | Thứ tự hiển thị | ✅ (default: 0) |

### 💡 GỢI Ý:

- **Tất cả cột đều cần thiết**
- **Có thể tối ưu**: Lưu dạng JSON trong bảng `tours` thay vì bảng riêng (nếu không cần query riêng)

---

## 8. BẢNG `tour_policies` - CHÍNH SÁCH TOUR

### ✅ CÁC CỘT ĐANG DÙNG:

| Cột | Kiểu | Mô tả | Bắt buộc |
|-----|------|-------|-----------|
| `id` | INT | ID tự động | ✅ |
| `tour_id` | INT | Foreign key → tours | ✅ |
| `policy_id` | INT | Foreign key → policies | ✅ |

### 💡 GỢI Ý:

- **Tất cả cột đều cần thiết** - Bảng liên kết many-to-many
- **Không nên bỏ cột nào**

---

## 9. BẢNG `tour_services` - DỊCH VỤ TOUR (DEPRECATED)

### ❌ BẢNG NÀY CÓ THỂ BỎ:

| Cột | Lý do |
|-----|-------|
| Tất cả | ⚠️ **DEPRECATED** - Đã thay thế bằng `itinerary_day_services` |

### 💡 GỢI Ý:

- **Nên bỏ hoàn toàn** - Đã có `itinerary_day_services` thay thế
- **Lưu ý**: Kiểm tra xem có code nào còn dùng bảng này không trước khi xóa

---

## 10. BẢNG `tour_schedules` - LỊCH KHỞI HÀNH

### ⚠️ KHÔNG DÙNG KHI TẠO TOUR:

Bảng này dùng để quản lý lịch khởi hành sau khi tour đã được tạo, không liên quan đến form tạo tour.

---

## 📊 TÓM TẮT - CÁC CỘT CÓ THỂ BỎ:

### Bảng `tours`:
- ❌ `markup_percentage` (DEPRECATED)

### Bảng `itineraries`:
- ❌ `meals` (JSON) - Có thể ghi trong description
- ❌ `accommodation` (VARCHAR) - Có thể ghi trong description
- ❌ `arrival_time` (TIME) - Có thể ghi trong description
- ❌ `departure_time` (TIME) - Có thể ghi trong description

### Bảng `tour_services`:
- ❌ **Bỏ toàn bộ bảng** (DEPRECATED - đã thay bằng `itinerary_day_services`)

---

## 🔧 CÂU LỆNH SQL ĐỂ BỎ CÁC CỘT:

```sql
-- Bỏ cột trong bảng tours
ALTER TABLE `tours` DROP COLUMN `markup_percentage`;

-- Bỏ cột trong bảng itineraries
ALTER TABLE `itineraries` DROP COLUMN `meals`;
ALTER TABLE `itineraries` DROP COLUMN `accommodation`;
ALTER TABLE `itineraries` DROP COLUMN `arrival_time`;
ALTER TABLE `itineraries` DROP COLUMN `departure_time`;

-- Bỏ toàn bộ bảng tour_services (sau khi kiểm tra không còn code nào dùng)
DROP TABLE IF EXISTS `tour_services`;
```

---

## ⚠️ LƯU Ý TRƯỚC KHI BỎ:

1. **Kiểm tra code**: Tìm tất cả nơi sử dụng các cột/bảng này
2. **Backup database**: Luôn backup trước khi thay đổi
3. **Test kỹ**: Test lại toàn bộ flow tạo tour sau khi bỏ
4. **Migration**: Nếu có dữ liệu cũ, cần migrate sang format mới

---

## 📝 GỢI Ý TỐI ƯU THÊM:

1. **Có thể gộp bảng nhỏ**: `tour_highlights`, `tour_included_excluded`, `tour_faqs` có thể lưu dạng JSON trong bảng `tours` để giảm số bảng
2. **Tối ưu indexes**: Đảm bảo có indexes phù hợp cho các truy vấn thường dùng
3. **Normalize/Denormalize**: Cân nhắc giữa normalize (nhiều bảng) và denormalize (ít bảng, dữ liệu lặp lại)

