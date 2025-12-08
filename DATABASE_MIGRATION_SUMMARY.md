# TÓM TẮT DATABASE MIGRATION - TÍNH NĂNG MỚI

## 📋 TỔNG QUAN

File migration: `database_migration_new_features.sql`

**Tổng số bảng sau migration: 53 bảng** (38 bảng cũ + 15 bảng mới)

---

## 🔄 PHẦN 1: SỬA ĐỔI BẢNG HIỆN CÓ (5 bảng)

### 1.1. `tours` - Đơn giản hóa chi phí cố định (Phương án 3)

**Thay đổi:**

- ❌ Bỏ 4 cột: `fixed_cost_guide`, `fixed_cost_management`, `fixed_cost_marketing`, `fixed_cost_other`
- ✅ Thêm 1 cột: `fixed_cost_total` (tổng chi phí cố định)
- ✅ Thêm: `tour_cost_template_id` (link với template)
- ✅ Thêm: `use_template_cost` (1 = dùng template, 0 = nhập thủ công)

**Lý do:** Đơn giản hóa, không cần phân loại chi tiết, dùng template để tự động điền.

---

### 1.2. `tour_schedules` - Thêm status 'confirmed'

**Thay đổi:**

- ✅ Thêm status: `'confirmed'` (tour đã được xác nhận, sẵn sàng đi)

**Status flow:** `open` → `confirmed` → `in_progress` → `completed`

---

### 1.3. `incurred_expenses` - Link với tour_schedule

**Thay đổi:**

- ✅ `booking_id` có thể NULL (chi phí có thể theo tour, không chỉ theo booking)
- ✅ Thêm `tour_schedule_id` (link với tour schedule)

**Lý do:** Chi phí phát sinh là chi phí của công ty, có thể theo tour schedule.

---

### 1.4. `tour_assignments` - Đảm bảo tour_schedule_id NOT NULL

**Thay đổi:**

- ✅ `tour_schedule_id` = NOT NULL (bắt buộc)
- ✅ Thêm FK constraint

**Lý do:** Phân công HDV phải theo tour schedule, không theo booking riêng lẻ.

---

### 1.5. `service_provider_payments` - Thêm tour_schedule_id

**Thay đổi:**

- ✅ Thêm `tour_schedule_id` (có thể trả theo tour schedule, không chỉ theo booking)

**Lý do:** Có thể trả dịch vụ theo tour schedule (tất cả booking trong tour đó).

---

## 🆕 PHẦN 2: BẢNG MỚI - TEMPLATE & PHỤ CẤP (2 bảng)

### 2.1. `tour_cost_templates` - Template chi phí cố định

**Mục đích:** Tránh nhập lại chi phí cố định mỗi lần tạo tour.

**Cấu trúc:**

- `template_name`: Tên template (VD: "Tour trong nước 3 ngày")
- `fixed_cost_total`: Tổng chi phí cố định mặc định
- `is_default`: Template mặc định

**Cách dùng:**

1. Tạo tour → Chọn template
2. Hệ thống tự động điền `fixed_cost_total` từ template
3. Có thể chỉnh sửa nếu cần

---

### 2.2. `tour_allowance_rules` - Tính phụ cấp tự động

**Mục đích:** Tự động tính phụ cấp HDV và tài xế dựa vào quy mô tour.

**Cấu trúc:**

- `tour_type`: public/custom
- `duration_days_min/max`: Số ngày
- `participant_min/max`: Số khách
- `guide_allowance`: Phụ cấp HDV
- `driver_allowance`: Phụ cấp tài xế
- `priority`: Ưu tiên khi có nhiều rule khớp

**Cách dùng:**

1. Khi gán HDV/tài xế cho tour
2. Hệ thống tự động tìm rule phù hợp
3. Tự động điền vào `tour_assignments.salary_amount` và `vehicle_assignments.driver_salary`

---

## 🆕 PHẦN 3: BẢNG MỚI - PHÂN PHÒNG (4 bảng)

### 3.1. `room_assignments` - Phân phòng

**Mục đích:** Quản lý phân phòng cho từng đêm của tour.

**Cấu trúc:**

- `tour_schedule_id`: Tour schedule nào
- `itinerary_id`: Ngày nào (đêm nào)
- `service_provider_id`: Khách sạn nào
- `room_number`: Số phòng
- `room_type`: Loại phòng (single, double, twin, triple, quad, family)
- `max_capacity`: Số người tối đa
- `actual_occupancy`: Số người thực tế

---

### 3.2. `room_assignment_customers` - Khách trong phòng

**Mục đích:** Lưu danh sách khách trong từng phòng.

**Cấu trúc:**

- `room_assignment_id`: Phòng nào
- `booking_customer_id`: Khách nào (từ booking)
- `customer_id`: Snapshot để dễ query
- `role`: primary/companion

---

### 3.3. `room_requests` - Yêu cầu phòng

**Mục đích:** Lưu yêu cầu đặc biệt về phòng (đơn phòng, cùng phòng với ai, v.v.).

**Cấu trúc:**

- `booking_id`: Booking nào
- `customer_id`: Khách yêu cầu
- `request_type`: single_room/share_with/avoid_sharing_with
- `single_room_supplement`: Phụ phí đơn phòng (cố định)

---

### 3.4. `room_assignment_history` - Lịch sử phân phòng

**Mục đích:** Ghi log thay đổi phân phòng.

---

## 🆕 PHẦN 4: BẢNG MỚI - CHECK-IN CHI TIẾT (4 bảng)

### 4.1. `activity_checkpoint_templates` - Template checkpoint

**Mục đích:** Định nghĩa các điểm check-in mẫu cho tour.

**Cấu trúc:**

- `tour_id`: Tour nào
- `checkpoint_type`: boarding/meal/accommodation/transfer/activity
- `scheduled_time`: Thời gian dự kiến
- `location_name`: Địa điểm

---

### 4.2. `activity_checkpoints` - Checkpoint thực tế

**Mục đích:** Checkpoint thực tế cho tour schedule (copy từ template).

---

### 4.3. `activity_checkins` - Check-in từng khách

**Mục đích:** Lưu check-in chi tiết của từng khách tại mỗi checkpoint.

**Cấu trúc:**

- `activity_checkpoint_id`: Checkpoint nào
- `booking_customer_id`: Khách nào
- `status`: present/absent/late/early/excused
- `actual_time`: Thời gian thực tế
- `checked_by`: HDV nào check-in

---

### 4.4. `activity_checkin_summary` - Tổng hợp checkpoint

**Mục đích:** Tổng hợp thống kê check-in của checkpoint.

**Cấu trúc:**

- `total_customers`: Tổng số khách
- `present_count`: Số người có mặt
- `absent_count`: Số người vắng
- `late_count`: Số người muộn

---

## 🆕 PHẦN 5: BẢNG MỚI - QUẢN LÝ XE VÀ TÀI XẾ (5 bảng)

### 5.1. `drivers` - Tài xế

**Mục đích:** Quản lý thông tin tài xế.

**Cấu trúc:**

- `driver_code`: Mã tài xế
- `license_number`: Số bằng lái
- `license_type`: Hạng bằng (A1, A2, B1, B2, C, D, E, F)
- `status`: active/on_trip/off_duty/suspended/inactive

---

### 5.2. `vehicle_assignments` - Phân công xe và tài xế

**Mục đích:** Phân công xe và tài xế cho tour schedule.

**Cấu trúc:**

- `tour_schedule_id`: Tour schedule nào
- `vehicle_id`: Xe nào
- `driver_id`: Tài xế nào
- `driver_salary`: Phụ cấp tài xế (tự động từ tour_allowance_rules)
- `estimated_fuel_cost`: Chi phí nhiên liệu dự kiến
- `actual_fuel_cost`: Chi phí nhiên liệu thực tế

---

### 5.3. `driver_schedules` - Lịch tài xế

**Mục đích:** Tránh trùng lịch tài xế.

---

### 5.4. `vehicle_maintenance` - Bảo dưỡng xe

**Mục đích:** Quản lý lịch bảo dưỡng xe.

---

### 5.5. `vehicle_assignment_history` - Lịch sử phân công

**Mục đích:** Ghi log thay đổi phân công xe/tài xế.

---

## 📊 TỔNG KẾT

### Bảng mới (15 bảng):

1. ✅ `tour_cost_templates`
2. ✅ `tour_allowance_rules`
3. ✅ `room_assignments`
4. ✅ `room_assignment_customers`
5. ✅ `room_requests`
6. ✅ `room_assignment_history`
7. ✅ `activity_checkpoint_templates`
8. ✅ `activity_checkpoints`
9. ✅ `activity_checkins`
10. ✅ `activity_checkin_summary`
11. ✅ `drivers`
12. ✅ `vehicle_assignments`
13. ✅ `driver_schedules`
14. ✅ `vehicle_maintenance`
15. ✅ `vehicle_assignment_history`

### Bảng sửa (5 bảng):

1. ✅ `tours` - Đơn giản hóa chi phí cố định
2. ✅ `tour_schedules` - Thêm status 'confirmed'
3. ✅ `incurred_expenses` - Thêm tour_schedule_id
4. ✅ `tour_assignments` - Đảm bảo tour_schedule_id NOT NULL
5. ✅ `service_provider_payments` - Thêm tour_schedule_id

---

## 🚀 CÁCH SỬ DỤNG

### 1. Chạy migration:

```sql
SOURCE database_migration_new_features.sql;
```

### 2. Kiểm tra:

```sql
-- Kiểm tra số bảng
SELECT COUNT(*) FROM information_schema.tables
WHERE table_schema = 'tour_managementss';

-- Kết quả mong đợi: 53 bảng
```

### 3. Insert dữ liệu mẫu (đã có trong file):

- Template chi phí mẫu
- Quy tắc phụ cấp mẫu

---

## ⚠️ LƯU Ý

1. **Backup database trước khi chạy migration**
2. **Kiểm tra foreign key constraints** - Đảm bảo các bảng reference đã tồn tại
3. **Test trên môi trường dev trước** - Không chạy trực tiếp trên production
4. **Dữ liệu cũ:** Các tour cũ sẽ có `fixed_cost_total = 0`, cần cập nhật thủ công hoặc dùng template

---

## 📝 BUSINESS RULES

### 1. Chi phí cố định:

- Dùng template để tự động điền
- Có thể override nếu cần
- Chỉ 1 cột `fixed_cost_total` (đơn giản)

### 2. Phụ cấp tour:

- Tự động tính từ `tour_allowance_rules`
- Dựa vào: tour_type, duration_days, số khách
- Tự động điền vào `tour_assignments` và `vehicle_assignments`

### 3. Phân phòng:

- Tự động: nam/nam, nữ/nữ
- Yêu cầu đặc biệt: đơn phòng, cùng phòng, tránh cùng phòng
- Phụ phí đơn phòng: cố định

### 4. Check-in:

- Qua web, không cần GPS
- Theo từng checkpoint (lên xe, ăn, ngủ, di chuyển, hoạt động)
- Tổng hợp thống kê

### 5. Xe và tài xế:

- Chỉ quản lý xe công ty
- Phụ cấp tài xế tự động tính
- Tránh trùng lịch

---

## ✅ HOÀN THÀNH

File migration đã sẵn sàng để chạy! 🎉
