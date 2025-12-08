# FLOW: PHÂN PHÒNG (ROOM ASSIGNMENT)

## 📋 TỔNG QUAN

Tính năng phân phòng tự động cho khách hàng trong tour:
- Tự động phân phòng: nam/nam, nữ/nữ
- Yêu cầu đặc biệt: đơn phòng, cùng phòng với ai, tránh cùng phòng
- Phụ phí đơn phòng: cố định

---

## 🔄 WORKFLOW CHI TIẾT

### BƯỚC 1: THU THẬP YÊU CẦU PHÒNG

**Actor:** Sales/Admin, Customer

**Thời điểm:** Khi tạo booking hoặc sau khi booking

**Hành động:**
1. Khách điền yêu cầu phòng (nếu có)
   - Đơn phòng → `request_type = 'single_room'`
   - Cùng phòng với [tên khách] → `request_type = 'share_with'`, `target_customer_id = ?`
   - Không cùng phòng với [tên khách] → `request_type = 'avoid_sharing_with'`, `target_customer_id = ?`
2. Lưu vào `room_requests`
   - `status = 'pending'`
   - `single_room_supplement` = phụ phí đơn phòng (cố định, VD: 500.000)

**Dữ liệu tạo:**
- `room_requests` (status: pending)

---

### BƯỚC 2: PHÂN PHÒNG TỰ ĐỘNG

**Actor:** System (Tự động) hoặc Admin

**Thời điểm:** Trước ngày khởi hành 1-3 ngày (hoặc khi đủ danh sách khách)

**Trigger:** 
- Tất cả booking đã thanh toán đủ (`payment_status = 'paid'`)
- Hoặc admin chọn "Phân phòng tự động"

**Hành động:**

#### 2.1. Thu thập dữ liệu
```sql
-- Lấy danh sách khách đã thanh toán
SELECT 
    bc.id AS booking_customer_id,
    bc.booking_id,
    bc.customer_id,
    bc.age_type,
    c.gender,
    c.full_name
FROM booking_customers bc
JOIN customers c ON bc.customer_id = c.id
JOIN bookings b ON bc.booking_id = b.id
WHERE b.tour_schedule_id = ?
  AND b.payment_status = 'paid'
ORDER BY c.gender, bc.age_type;
```

#### 2.2. Lấy thông tin khách sạn
```sql
-- Lấy khách sạn từ itinerary_day_services
SELECT 
    i.id AS itinerary_id,
    i.day_number,
    ids.service_provider_id,
    sp.name AS hotel_name
FROM itineraries i
JOIN itinerary_day_services ids ON i.id = ids.itinerary_id
JOIN service_providers sp ON ids.service_provider_id = sp.id
WHERE i.tour_id = ?
  AND ids.service_type = 'accommodation' -- Giả sử có field này hoặc check service_type_id
ORDER BY i.day_number;
```

#### 2.3. Xử lý yêu cầu đặc biệt trước
```sql
-- Xử lý đơn phòng
SELECT * FROM room_requests
WHERE booking_id IN (SELECT id FROM bookings WHERE tour_schedule_id = ?)
  AND request_type = 'single_room'
  AND status = 'pending';

-- Với mỗi yêu cầu đơn phòng:
-- 1. Tạo room_assignment (room_type = 'single', max_capacity = 1)
-- 2. Tạo room_assignment_customers
-- 3. Cập nhật room_requests.status = 'fulfilled'
-- 4. Tính phụ phí đơn phòng (nếu chưa có trong booking)
```

```sql
-- Xử lý cùng phòng với ai
SELECT * FROM room_requests
WHERE request_type = 'share_with'
  AND status = 'pending';

-- Ghép 2 khách vào cùng 1 phòng
```

```sql
-- Xử lý tránh cùng phòng
SELECT * FROM room_requests
WHERE request_type = 'avoid_sharing_with'
  AND status = 'pending';

-- Đảm bảo 2 khách không ở cùng phòng
```

#### 2.4. Phân phòng tự động theo giới tính
```
Logic:
1. Nhóm khách theo giới tính:
   - Nam: danh sách khách nam
   - Nữ: danh sách khách nữ
   - Other: có thể ghép với nam hoặc nữ (tùy chọn)

2. Phân phòng:
   - Ưu tiên ghép 2 người/phòng (double/twin)
   - Nếu lẻ → ghép 3 người/phòng (triple)
   - Nếu vẫn lẻ → đơn phòng (có phụ phí)

3. Với mỗi đêm (itinerary):
   - Tạo room_assignments
   - Gán khách vào room_assignment_customers
```

**Ví dụ:**
```
Tour có 25 khách:
- 12 nam, 13 nữ

Phân phòng:
- Nam: 6 phòng double (12 người)
- Nữ: 6 phòng double (12 người) + 1 phòng single (1 người, có phụ phí)
```

**Dữ liệu tạo:**
- `room_assignments` (theo từng đêm)
- `room_assignment_customers` (khách trong từng phòng)
- `room_assignment_history` (log)

---

### BƯỚC 3: XEM XÉT VÀ ĐIỀU CHỈNH

**Actor:** Admin/Operations

**Hành động:**
1. Xem danh sách phân phòng
   ```sql
   SELECT 
       ra.id,
       ra.room_number,
       ra.room_type,
       ra.actual_occupancy,
       ra.max_capacity,
       i.day_number,
       sp.name AS hotel_name,
       GROUP_CONCAT(c.full_name SEPARATOR ', ') AS customers
   FROM room_assignments ra
   JOIN itineraries i ON ra.itinerary_id = i.id
   LEFT JOIN service_providers sp ON ra.service_provider_id = sp.id
   LEFT JOIN room_assignment_customers rac ON ra.id = rac.room_assignment_id
   LEFT JOIN customers c ON rac.customer_id = c.id
   WHERE ra.tour_schedule_id = ?
   GROUP BY ra.id
   ORDER BY i.day_number, ra.room_number;
   ```

2. Điều chỉnh (nếu cần)
   - Di chuyển khách sang phòng khác
   - Thêm/xóa khách trong phòng
   - Thay đổi loại phòng

3. Ghi log vào `room_assignment_history`

---

### BƯỚC 4: XÁC NHẬN PHÂN PHÒNG

**Actor:** Admin/Operations

**Hành động:**
1. Xác nhận phân phòng
   ```sql
   UPDATE room_assignments
   SET status = 'confirmed'
   WHERE tour_schedule_id = ? AND status = 'assigned';
   ```

2. Gửi thông tin phòng cho khách
   - Email/SMS thông tin phòng
   - Số phòng, loại phòng, người cùng phòng

3. Cập nhật `room_requests.status = 'fulfilled'` (nếu có)

---

## 📊 QUERY HỮU ÍCH

### Xem phân phòng theo tour schedule
```sql
SELECT 
    ra.id,
    i.day_number,
    ra.room_number,
    ra.room_type,
    ra.actual_occupancy,
    ra.max_capacity,
    sp.name AS hotel_name,
    GROUP_CONCAT(c.full_name SEPARATOR ', ') AS customers
FROM room_assignments ra
JOIN itineraries i ON ra.itinerary_id = i.id
LEFT JOIN service_providers sp ON ra.service_provider_id = sp.id
LEFT JOIN room_assignment_customers rac ON ra.id = rac.room_assignment_id
LEFT JOIN customers c ON rac.customer_id = c.id
WHERE ra.tour_schedule_id = ?
GROUP BY ra.id
ORDER BY i.day_number, ra.room_number;
```

### Xem yêu cầu phòng chưa xử lý
```sql
SELECT 
    rr.id,
    b.booking_code,
    c.full_name AS customer_name,
    rr.request_type,
    tc.full_name AS target_customer_name,
    rr.single_room_supplement,
    rr.status
FROM room_requests rr
JOIN bookings b ON rr.booking_id = b.id
JOIN customers c ON rr.customer_id = c.id
LEFT JOIN customers tc ON rr.target_customer_id = tc.id
WHERE b.tour_schedule_id = ?
  AND rr.status = 'pending';
```

---

## ⚠️ BUSINESS RULES

1. **Một khách chỉ ở 1 phòng trong 1 đêm**
2. **Số người trong phòng ≤ max_capacity**
3. **Phân phòng tự động: ưu tiên ghép cùng giới tính**
4. **Đơn phòng: tính phụ phí cố định**
5. **Yêu cầu đặc biệt: ưu tiên xử lý trước khi phân phòng tự động**
6. **Thay đổi: ghi log vào room_assignment_history**

---

## 🔄 TRƯỜNG HỢP ĐẶC BIỆT

### Khách hủy booking
1. Xóa khách khỏi `room_assignment_customers`
2. Cập nhật `room_assignments.actual_occupancy`
3. Có thể phân phòng lại nếu cần

### Khách thêm vào booking
1. Thêm khách vào `booking_customers`
2. Phân phòng lại hoặc thêm vào phòng có chỗ

### Thay đổi yêu cầu phòng
1. Cập nhật `room_requests`
2. Phân phòng lại nếu cần

