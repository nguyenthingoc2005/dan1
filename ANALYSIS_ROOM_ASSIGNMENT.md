# PHÂN TÍCH: CHỨC NĂNG PHÂN PHÒNG (ROOM ASSIGNMENT)

## 📋 TỔNG QUAN HIỆN TRẠNG

### ✅ ĐÃ CÓ TRONG DATABASE:

1. **Bảng `room_assignments`** - Lưu thông tin phòng được phân

   - `tour_schedule_id`, `itinerary_id` (đêm nào)
   - `service_provider_id` (khách sạn)
   - `room_number`, `room_type` (single/double/twin/triple/quad/family)
   - `max_capacity`, `actual_occupancy`
   - `check_in_date`, `check_out_date`
   - `status` (pending/assigned/confirmed/cancelled)

2. **Bảng `room_assignment_customers`** - Lưu khách hàng trong từng phòng

   - Liên kết: `room_assignment_id` → `room_assignments`
   - `booking_customer_id`, `customer_id`, `booking_id` (snapshot)
   - `role` (primary/companion)
   - `room_preference`, `special_notes`

3. **Bảng `room_requests`** - Lưu yêu cầu đặc biệt

   - `request_type`: single_room / share_with / avoid_sharing_with
   - `target_customer_id` (nếu share_with hoặc avoid_sharing_with)
   - `single_room_supplement` (phụ phí đơn phòng)
   - `status` (pending/approved/rejected/fulfilled)

4. **Bảng `room_assignment_history`** - Lưu lịch sử thay đổi
   - `action`: created/updated/customer_added/customer_removed/cancelled
   - `old_values`, `new_values` (JSON)
   - `changed_by`, `reason`

### ❌ CHƯA CÓ:

1. **Model `RoomAssignment.php`** - Chưa có model xử lý phân phòng
2. **Controller logic** - Chưa có trong `TourOperationsController`
3. **View/Tab "Phòng"** - Chưa có tab hiển thị phân phòng trong `show.php`
4. **Chức năng phân phòng tự động** - Chưa có logic tự động phân phòng

---

## 🔄 LUỒNG PHÂN PHÒNG (THEO FLOW_ROOM_ASSIGNMENT.md)

### **BƯỚC 1: THU THẬP YÊU CẦU PHÒNG**

- **Khi:** Khi tạo booking hoặc sau khi booking
- **Ai:** Sales/Admin hoặc Customer
- **Hành động:**
  - Khách điền yêu cầu: đơn phòng / cùng phòng với ai / tránh cùng phòng với ai
  - Lưu vào `room_requests` với `status = 'pending'`

### **BƯỚC 2: PHÂN PHÒNG TỰ ĐỘNG**

- **Khi:** Trước ngày khởi hành 1-3 ngày (khi tour đã chốt)
- **Trigger:**
  - Tất cả booking đã thanh toán (`payment_status = 'paid'`)
  - Hoặc admin chọn "Phân phòng tự động"
- **Logic:**
  1. Xử lý yêu cầu đặc biệt trước (đơn phòng, cùng phòng, tránh cùng phòng)
  2. Phân phòng tự động theo giới tính:
     - Nhóm theo giới tính (Nam/Nữ)
     - Ưu tiên ghép 2 người/phòng (double/twin)
     - Nếu lẻ → ghép 3 người (triple)
     - Nếu vẫn lẻ → đơn phòng (có phụ phí)
  3. Tạo `room_assignments` cho từng đêm (itinerary)
  4. Gán khách vào `room_assignment_customers`

### **BƯỚC 3: XEM XÉT VÀ ĐIỀU CHỈNH**

- **Ai:** Admin/Operations
- **Hành động:**
  - Xem danh sách phân phòng theo từng đêm
  - Điều chỉnh: di chuyển khách, thêm/xóa khách, thay đổi loại phòng
  - Ghi log vào `room_assignment_history`

### **BƯỚC 4: XÁC NHẬN PHÂN PHÒNG**

- **Ai:** Admin/Operations
- **Hành động:**
  - Cập nhật `room_assignments.status = 'confirmed'`
  - Gửi thông tin phòng cho khách (Email/SMS)
  - Cập nhật `room_requests.status = 'fulfilled'`

---

## 🎯 KẾ HOẠCH TRIỂN KHAI

### **PHASE 1: TẠO MODEL VÀ CƠ BẢN**

1. **Tạo Model `RoomAssignment.php`**

   - Method `getByScheduleId($schedule_id)` - Lấy tất cả phòng phân cho schedule
   - Method `getByItinerary($schedule_id, $itinerary_id)` - Lấy phòng theo đêm
   - Method `getRoomRequests($schedule_id)` - Lấy yêu cầu phòng
   - Method `autoAssign($schedule_id)` - Phân phòng tự động
   - Method `assignCustomerToRoom($room_id, $booking_customer_id)` - Gán khách vào phòng
   - Method `removeCustomerFromRoom($room_assignment_customer_id)` - Xóa khách khỏi phòng
   - Method `updateRoom($room_id, $data)` - Cập nhật thông tin phòng
   - Method `createRoom($data)` - Tạo phòng mới

2. **Thêm vào Controller `TourOperationsController.php`**
   - Lấy danh sách phân phòng trong method `show()`
   - Thêm method `autoAssignRooms()` - Xử lý phân phòng tự động
   - Thêm method `assignRoom()` - Gán khách vào phòng
   - Thêm method `updateRoom()` - Cập nhật phòng
   - Thêm method `removeCustomerFromRoom()` - Xóa khách khỏi phòng

### **PHASE 2: GIAO DIỆN HIỂN THỊ**

3. **Thêm Tab "Phòng" vào View `show.php`**
   - Hiển thị phân phòng theo từng đêm (itinerary)
   - Mỗi đêm hiển thị:
     - Tên khách sạn
     - Danh sách phòng (room_number, room_type, số người)
     - Danh sách khách trong từng phòng
   - Nút "Phân phòng tự động"
   - Form điều chỉnh: thêm/xóa khách, thay đổi phòng

### **PHASE 3: LOGIC PHÂN PHÒNG TỰ ĐỘNG**

4. **Implement `autoAssign()`**
   - Lấy danh sách khách đã thanh toán
   - Lấy thông tin khách sạn từ itineraries
   - Xử lý yêu cầu đặc biệt trước
   - Phân phòng tự động theo giới tính
   - Tạo `room_assignments` và `room_assignment_customers`
   - Ghi log vào `room_assignment_history`

---

## 📊 CẤU TRÚC DỮ LIỆU CẦN THIẾT

### **Query lấy phân phòng hiện tại:**

```sql
SELECT
    ra.id AS room_id,
    ra.room_number,
    ra.room_type,
    ra.actual_occupancy,
    ra.max_capacity,
    ra.status,
    i.id AS itinerary_id,
    i.day_number,
    sp.name AS hotel_name,
    GROUP_CONCAT(
        CONCAT(c.full_name, ' (', bc.age_type, ')')
        ORDER BY rac.role DESC
        SEPARATOR ', '
    ) AS customers,
    GROUP_CONCAT(c.id) AS customer_ids
FROM room_assignments ra
JOIN itineraries i ON ra.itinerary_id = i.id
LEFT JOIN service_providers sp ON ra.service_provider_id = sp.id
LEFT JOIN room_assignment_customers rac ON ra.id = rac.room_assignment_id
LEFT JOIN booking_customers bc ON rac.booking_customer_id = bc.id
LEFT JOIN customers c ON rac.customer_id = c.id
WHERE ra.tour_schedule_id = :schedule_id
GROUP BY ra.id
ORDER BY i.day_number, ra.room_number;
```

### **Query lấy khách chưa phân phòng:**

```sql
SELECT
    bc.id AS booking_customer_id,
    bc.booking_id,
    bc.customer_id,
    bc.age_type,
    c.full_name,
    c.gender,
    b.booking_code
FROM booking_customers bc
JOIN customers c ON bc.customer_id = c.id
JOIN bookings b ON bc.booking_id = b.id
WHERE b.tour_schedule_id = :schedule_id
  AND b.payment_status = 'paid'
  AND bc.id NOT IN (
      SELECT booking_customer_id
      FROM room_assignment_customers
      WHERE booking_customer_id IN (
          SELECT id FROM booking_customers WHERE booking_id IN (
              SELECT id FROM bookings WHERE tour_schedule_id = :schedule_id
          )
      )
  )
ORDER BY c.gender, bc.age_type;
```

### **Query lấy yêu cầu phòng:**

```sql
SELECT
    rr.*,
    b.booking_code,
    c.full_name AS customer_name,
    tc.full_name AS target_customer_name
FROM room_requests rr
JOIN bookings b ON rr.booking_id = b.id
JOIN customers c ON rr.customer_id = c.id
LEFT JOIN customers tc ON rr.target_customer_id = tc.id
WHERE b.tour_schedule_id = :schedule_id
  AND rr.status IN ('pending', 'approved')
ORDER BY rr.request_type, rr.created_at;
```

---

## ⚠️ LƯU Ý QUAN TRỌNG

1. **Kiểm tra điều kiện thao tác:**

   - Chỉ cho phép phân phòng khi tour đã chốt hoặc đã qua deadline booking
   - Sử dụng `checkReadyForOperations()` đã có sẵn

2. **Xử lý theo từng đêm:**

   - Mỗi đêm (itinerary) có thể ở khách sạn khác nhau
   - Cần phân phòng riêng cho từng đêm

3. **Validate:**

   - Một khách chỉ ở 1 phòng trong 1 đêm
   - Số người trong phòng ≤ max_capacity
   - Phân phòng tự động: ưu tiên ghép cùng giới tính

4. **Lịch sử:**
   - Ghi log mọi thay đổi vào `room_assignment_history`
   - Lưu `old_values`, `new_values`, `reason`, `changed_by`

---

## 🚀 NEXT STEPS

1. Tạo Model `RoomAssignment.php` với các method cơ bản
2. Thêm logic vào Controller `TourOperationsController.php`
3. Thêm Tab "Phòng" vào View `show.php`
4. Implement chức năng "Phân phòng tự động"
5. Implement chức năng điều chỉnh phân phòng (drag & drop hoặc form)
