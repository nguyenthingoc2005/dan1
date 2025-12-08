# HƯỚNG DẪN SETUP DATABASE VÀ FLOWS

## 📋 TỔNG QUAN

Hệ thống đã được tách thành các module riêng biệt để dễ quản lý:
- **Database:** Tách theo module (11 file SQL)
- **Flows:** Tách theo tính năng (6 file flow)

---

## 📁 CẤU TRÚC THƯ MỤC

```
project/
├── database/
│   ├── README.md
│   ├── 00_main.sql (File chính - import tất cả)
│   ├── 01_system.sql
│   ├── 02_location_services.sql
│   ├── 03_tour.sql
│   ├── 04_customer.sql
│   ├── 05_booking.sql
│   ├── 06_payment.sql
│   ├── 07_operations.sql
│   ├── 08_room_assignment.sql ⭐ MỚI
│   ├── 09_activity_checkin.sql ⭐ MỚI
│   ├── 10_vehicle_driver.sql ⭐ MỚI
│   └── 11_system_other.sql
│
└── flows/
    ├── README.md
    ├── FLOW_ROOM_ASSIGNMENT.md
    ├── FLOW_ACTIVITY_CHECKIN.md
    ├── FLOW_VEHICLE_DRIVER.md
    ├── FLOW_TOUR_MANAGEMENT_PAGE.md
    ├── FLOW_TOUR_COST_TEMPLATE.md
    └── FLOW_TOUR_ALLOWANCE.md
```

---

## 🚀 CÁCH SETUP DATABASE

### Bước 1: Tạo database mới

```sql
-- Option 1: Dùng file chính (Khuyến nghị)
SOURCE database/00_main.sql;

-- Option 2: Import từng module
SOURCE database/01_system.sql;
SOURCE database/02_location_services.sql;
SOURCE database/03_tour.sql;
SOURCE database/04_customer.sql;
SOURCE database/05_booking.sql;
SOURCE database/06_payment.sql;
SOURCE database/07_operations.sql;
SOURCE database/08_room_assignment.sql;
SOURCE database/09_activity_checkin.sql;
SOURCE database/10_vehicle_driver.sql;
SOURCE database/11_system_other.sql;
```

### Bước 2: Kiểm tra

```sql
-- Kiểm tra số bảng
SELECT COUNT(*) FROM information_schema.tables 
WHERE table_schema = 'tour_managementss';
-- Kết quả mong đợi: 53 bảng

-- Kiểm tra dữ liệu mẫu
SELECT * FROM tour_cost_templates;
SELECT * FROM tour_allowance_rules;
```

---

## 📖 ĐỌC FLOWS

### 1. Phân phòng
📄 `flows/FLOW_ROOM_ASSIGNMENT.md`
- Workflow phân phòng tự động
- Xử lý yêu cầu đặc biệt
- Query SQL mẫu

### 2. Check-in chi tiết
📄 `flows/FLOW_ACTIVITY_CHECKIN.md`
- Workflow check-in theo hoạt động
- Tạo checkpoint templates
- Check-in từng khách

### 3. Quản lý xe và tài xế
📄 `flows/FLOW_VEHICLE_DRIVER.md`
- Quản lý xe công ty
- Quản lý tài xế
- Phân công xe/tài xế cho tour

### 4. Trang quản lý tour schedule
📄 `flows/FLOW_TOUR_MANAGEMENT_PAGE.md`
- Hiển thị thông tin tổng hợp
- Gán HDV, xe, tài xế
- Tính toán tài chính

### 5. Template chi phí
📄 `flows/FLOW_TOUR_COST_TEMPLATE.md`
- Tạo và sử dụng template
- Tự động điền chi phí

### 6. Tính phụ cấp tự động
📄 `flows/FLOW_TOUR_ALLOWANCE.md`
- Thiết lập quy tắc
- Tự động tính phụ cấp

---

## 🎯 WORKFLOW TỔNG QUAN

```
1. TẠO TOUR
   ├─ Chọn template chi phí
   └─ Tạo activity checkpoint templates

2. LÊN LỊCH TOUR
   ├─ Tạo tour_schedule
   └─ Copy activity checkpoints

3. TẠO BOOKING
   ├─ Tạo booking
   ├─ Thu thập yêu cầu phòng
   └─ Khách thanh toán đủ 100%

4. PHÂN PHÒNG
   ├─ Xử lý yêu cầu đặc biệt
   ├─ Phân phòng tự động
   └─ Xác nhận phân phòng

5. TRANG QUẢN LÝ TOUR SCHEDULE
   ├─ Xem thông tin tổng hợp
   ├─ Gán xe và tài xế
   ├─ Gán HDV
   ├─ Tính toán tài chính
   └─ Xác nhận tour schedule

6. ĐI TOUR
   ├─ Check-in khách
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

## 📊 TỔNG KẾT

### Database:
- ✅ 11 file SQL theo module
- ✅ File chính `00_main.sql` để import tất cả
- ✅ Dữ liệu mẫu đã có sẵn

### Flows:
- ✅ 6 file flow chi tiết
- ✅ Query SQL mẫu trong mỗi flow
- ✅ Business rules rõ ràng

### Tính năng mới:
- ✅ Phân phòng tự động
- ✅ Check-in chi tiết
- ✅ Quản lý xe và tài xế
- ✅ Template chi phí (Phương án 3)
- ✅ Tính phụ cấp tự động

---

## ✅ SẴN SÀNG SỬ DỤNG

Tất cả các file đã được tạo và sẵn sàng! 🎉

