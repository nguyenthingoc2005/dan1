# TOÀN BỘ LUỒNG PHÂN PHÒNG

## 🔍 KIỂM TRA TOÀN BỘ LUỒNG

### **BƯỚC 1: AUTO ASSIGN - INPUT**

1. `$schedule_id`: ID của tour schedule
2. `$options`:
   - `manual_customer_ids`: Array các `booking_customer_id` cần xử lý thủ công (sẽ BỎ QUA)
   - `max_customers_per_room`: Số người/phòng (2, 3, hoặc 4)
   - `prioritize_same_booking`: Boolean - ưu tiên ghép cùng booking
   - `auto_single_room`: Boolean - tự động tạo phòng đơn nếu lẻ

### **BƯỚC 2: LẤY DỮ LIỆU**

1. **Lấy tour schedule**: `tour_schedules` + `tours` (để lấy `start_date`, `duration_days`)
2. **Lấy itineraries**: Tất cả các đêm của tour (`itineraries`)
3. **Lấy khách đã thanh toán**:
   - Query: `booking_customers` JOIN `customers` JOIN `bookings`
   - Filter: `payment_status = 'paid'`
   - Loại trừ: Đã phân phòng (`room_assignment_customers`)
   - Loại trừ: `manual_customer_ids` (nếu có)
4. **Lấy yêu cầu đặc biệt**: `room_requests` (single_room, share_with, avoid_sharing_with)

### **BƯỚC 3: PHÂN PHÒNG CHO TỪNG ĐÊM**

Với mỗi `itinerary` (đêm):

1. **Tính ngày check-in/check-out**:

   - `check_in_date = start_date + (day_number - 1) days`
   - `check_out_date = check_in_date + 1 day`

2. **Lấy khách chưa phân phòng cho đêm này**:

   - Query tương tự BƯỚC 2.3 nhưng thêm filter `itinerary_id`
   - Loại trừ khách đã phân phòng cho đêm này

3. **Xử lý yêu cầu đơn phòng** (5.1):

   - Tìm khách có `request_type = 'single_room'`
   - Tạo phòng đơn (max_capacity = 1)
   - Gán khách vào phòng
   - Xóa khách khỏi danh sách chưa phân phòng

4. **Phân phòng theo booking** (5.2) - Nếu `prioritize_same_booking = true`:

   - Nhóm khách theo `booking_id`
   - Với mỗi booking:
     - Nhóm theo giới tính (Nam/Nữ)
     - Ghép nam: `array_chunk($maleInBooking, $max_customers_per_room)`
     - Ghép nữ: `array_chunk($femaleInBooking, $max_customers_per_room)`
     - Tạo phòng và gán khách
     - Xóa khách khỏi danh sách chưa phân phòng

5. **Phân phòng tự động cho số còn lại** (5.3):

   - Nhóm theo giới tính (Nam/Nữ/Other)
   - Phân phòng nam: `array_chunk($maleCustomers, $max_customers_per_room)`
   - Phân phòng nữ: `array_chunk($femaleCustomers, $max_customers_per_room)`
   - Phân phòng other: Đơn phòng

6. **Xử lý yêu cầu cùng phòng** (5.4):
   - Tìm khách có `request_type = 'share_with'`
   - Ghép 2 khách vào cùng phòng
   - Cập nhật `room_requests.status = 'fulfilled'`

### **BƯỚC 4: TẠO PHÒNG & GÁN KHÁCH**

1. `createRoomForAutoAssign()`: Tạo record trong `room_assignments`
2. `assignCustomerToRoomForAutoAssign()`: Tạo record trong `room_assignment_customers`
3. Cập nhật `actual_occupancy` trong `room_assignments`

---

## ⚠️ VẤN ĐỀ ĐÃ SỬA

### **1. Mixed Named/Positional Parameters**

- **Lỗi**: Mix `:param` với `?`
- **Đã sửa**: Dùng toàn bộ named parameters (`:param_name`)

### **2. Parameter Name Trùng**

- **Lỗi**: Trong vòng lặp, parameter name có thể trùng
- **Đã sửa**: Dùng `:manual_id_day{day_number}_{idx}` để unique

### **3. Biến `$max_customers_per_room` undefined**

- **Lỗi**: Không parse từ `$options`
- **Đã sửa**: Parse ở đầu function với validation

---

## ✅ KIỂM TRA CUỐI CÙNG

1. ✅ Tất cả SQL queries dùng named parameters
2. ✅ Parameter names unique (không trùng)
3. ✅ `$max_customers_per_room` được parse và validate
4. ✅ `manual_customer_ids` được filter trong cả 2 queries (tổng và theo đêm)
