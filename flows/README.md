# FLOWS - LUỒNG CÁC TÍNH NĂNG MỚI

## 📋 TỔNG QUAN

Tài liệu mô tả chi tiết workflow của các tính năng mới:

1. **Phân phòng (Room Assignment)**
2. **Check-in chi tiết (Activity Check-in)**
3. **Quản lý xe và tài xế (Vehicle & Driver)**
4. **Trang quản lý tour schedule (Tour Management Page)**
5. **Template chi phí cố định (Tour Cost Template)**
6. **Tính phụ cấp tự động (Tour Allowance)**

---

## 📁 CẤU TRÚC FILE

```
flows/
├── README.md (file này)
├── FLOW_ROOM_ASSIGNMENT.md
├── FLOW_ACTIVITY_CHECKIN.md
├── FLOW_VEHICLE_DRIVER.md
├── FLOW_TOUR_MANAGEMENT_PAGE.md
├── FLOW_TOUR_COST_TEMPLATE.md
└── FLOW_TOUR_ALLOWANCE.md
```

---

## 🔗 LIÊN KẾT

### 1. Phân phòng
📄 [FLOW_ROOM_ASSIGNMENT.md](./FLOW_ROOM_ASSIGNMENT.md)

**Tính năng:**
- Phân phòng tự động: nam/nam, nữ/nữ
- Yêu cầu đặc biệt: đơn phòng, cùng phòng, tránh cùng phòng
- Phụ phí đơn phòng: cố định

**Bảng liên quan:**
- `room_assignments`
- `room_assignment_customers`
- `room_requests`
- `room_assignment_history`

---

### 2. Check-in chi tiết
📄 [FLOW_ACTIVITY_CHECKIN.md](./FLOW_ACTIVITY_CHECKIN.md)

**Tính năng:**
- Check-in theo hoạt động: lên xe, ăn, ngủ, di chuyển, hoạt động
- Qua web, không cần GPS, không real-time

**Bảng liên quan:**
- `activity_checkpoint_templates`
- `activity_checkpoints`
- `activity_checkins`
- `activity_checkin_summary`

---

### 3. Quản lý xe và tài xế
📄 [FLOW_VEHICLE_DRIVER.md](./FLOW_VEHICLE_DRIVER.md)

**Tính năng:**
- Quản lý xe công ty
- Quản lý tài xế
- Phụ cấp tài xế tự động tính
- Tránh trùng lịch

**Bảng liên quan:**
- `drivers`
- `vehicle_assignments`
- `driver_schedules`
- `vehicle_maintenance`
- `vehicle_assignment_history`

---

### 4. Trang quản lý tour schedule
📄 [FLOW_TOUR_MANAGEMENT_PAGE.md](./FLOW_TOUR_MANAGEMENT_PAGE.md)

**Tính năng:**
- Hiển thị thông tin tổng hợp tour schedule
- Danh sách booking đã chốt
- Danh sách khách hàng
- Gán xe, tài xế, HDV
- Tính toán tài chính dự kiến
- Đổi HDV/tài xế

**Bảng liên quan:**
- `tour_schedules`
- `bookings`
- `booking_customers`
- `vehicle_assignments`
- `tour_assignments`
- `tour_allowance_rules`

---

### 5. Template chi phí cố định
📄 [FLOW_TOUR_COST_TEMPLATE.md](./FLOW_TOUR_COST_TEMPLATE.md)

**Tính năng:**
- Template chi phí cố định
- Tự động điền khi tạo tour
- Có thể chỉnh sửa

**Bảng liên quan:**
- `tour_cost_templates`
- `tours`

---

### 6. Tính phụ cấp tự động
📄 [FLOW_TOUR_ALLOWANCE.md](./FLOW_TOUR_ALLOWANCE.md)

**Tính năng:**
- Tự động tính phụ cấp HDV và tài xế
- Dựa vào quy mô tour

**Bảng liên quan:**
- `tour_allowance_rules`
- `tour_assignments`
- `vehicle_assignments`

---

## 🎯 WORKFLOW TỔNG QUAN

```
1. TẠO TOUR
   ├─ Chọn template chi phí (FLOW_TOUR_COST_TEMPLATE)
   └─ Tạo activity checkpoint templates

2. LÊN LỊCH TOUR
   ├─ Tạo tour_schedule
   └─ Copy activity checkpoints từ templates

3. TẠO BOOKING
   ├─ Tạo booking
   ├─ Thu thập yêu cầu phòng (FLOW_ROOM_ASSIGNMENT)
   └─ Khách thanh toán đủ 100%

4. PHÂN PHÒNG (FLOW_ROOM_ASSIGNMENT)
   ├─ Xử lý yêu cầu đặc biệt
   ├─ Phân phòng tự động
   └─ Xác nhận phân phòng

5. TRANG QUẢN LÝ TOUR SCHEDULE (FLOW_TOUR_MANAGEMENT_PAGE)
   ├─ Xem thông tin tổng hợp
   ├─ Gán xe và tài xế (FLOW_VEHICLE_DRIVER)
   │  └─ Tự động tính phụ cấp (FLOW_TOUR_ALLOWANCE)
   ├─ Gán HDV (FLOW_TOUR_ALLOWANCE)
   ├─ Tính toán tài chính dự kiến
   └─ Xác nhận tour schedule

6. ĐI TOUR
   ├─ Check-in khách (FLOW_ACTIVITY_CHECKIN)
   ├─ Chi phí phát sinh
   └─ Nhật ký tour

7. VỀ
   └─ Cập nhật status: completed

8. THANH TOÁN
   └─ (Đã thanh toán đủ ở bước 3)

9. TRẢ DỊCH VỤ
   └─ Tạo service_provider_payments

10. BÁO CÁO
    └─ Tính toán tài chính thực tế
```

---

## 📝 GHI CHÚ

- Tất cả các flow đều có query SQL mẫu
- Có thể tùy chỉnh theo nhu cầu thực tế
- Các business rules được mô tả rõ ràng trong từng flow

