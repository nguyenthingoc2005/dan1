# DATABASE SCHEMA - TOUR MANAGEMENT SYSTEM V2

## 📋 TỔNG QUAN

Database schema hoàn chỉnh với tất cả tính năng mới, được tách thành các module riêng biệt.

**Tổng số bảng: 52 bảng**

---

## 📁 CẤU TRÚC FILE

```
database/
├── README.md (file này)
├── 00_main.sql (File chính - import tất cả)
├── 01_system.sql (Users & Roles)
├── 02_location_services.sql (Countries, Provinces, Destinations, Services)
├── 03_tour.sql (Tours, Tour Schedules, Itineraries, Templates)
├── 04_customer.sql (Customers)
├── 05_booking.sql (Bookings, Booking Customers)
├── 06_payment.sql (Payments, Invoices, Refunds, Service Provider Payments)
├── 07_operations.sql (Tour Assignments, Journals, Expenses, Allowance Rules)
├── 08_room_assignment.sql (MỚI - Phân phòng)
├── 09_activity_checkin.sql (MỚI - Check-in chi tiết)
├── 10_vehicle_driver.sql (MỚI - Xe và tài xế)
└── 11_system_other.sql (Email Templates, Vehicles)
```

---

## 🚀 CÁCH SỬ DỤNG

### Option 1: Import từng module
```sql
SOURCE database/01_system.sql;
SOURCE database/02_location_services.sql;
SOURCE database/03_tour.sql;
...
```

### Option 2: Import tất cả (Khuyến nghị)
```sql
SOURCE database/00_main.sql;
```

---

## 📊 DANH SÁCH BẢNG THEO MODULE

### Module 1: System (3 bảng)
- `roles`
- `users`
- `password_resets`

### Module 2: Location Services (7 bảng)
- `countries`
- `provinces`
- `destinations`
- `destination_images`
- `service_types`
- `service_providers`
- `services`
- `service_prices`

### Module 3: Tour (12 bảng)
- `tours` (ĐÃ SỬA)
- `tour_schedules` (ĐÃ SỬA)
- `itineraries`
- `itinerary_day_services`
- `tour_services`
- `tour_images`
- `tour_highlights`
- `tour_included_excluded`
- `tour_faqs`
- `policies`
- `tour_policies`

### Module 4: Customer (3 bảng)
- `customers`
- `customer_checkins` (DEPRECATED)
- `customer_import_logs`

### Module 5: Booking (4 bảng)
- `cancellation_policies`
- `discount_codes`
- `bookings`
- `booking_customers`
- `booking_services`
- `booking_status_history`

### Module 6: Payment (6 bảng)
- `payments`
- `payment_logs`
- `invoices`
- `refunds`
- `service_provider_payments` (ĐÃ SỬA)
- `service_provider_payment_details`

### Module 7: Operations (6 bảng)
- `tour_allowance_rules` ⭐ MỚI
- `tour_assignments` (ĐÃ SỬA)
- `journals`
- `journal_images`
- `incurred_expenses` (ĐÃ SỬA)
- `schedule_guide_history`

### Module 8: Room Assignment ⭐ MỚI (4 bảng)
- `room_assignments`
- `room_assignment_customers`
- `room_requests`
- `room_assignment_history`

### Module 9: Activity Check-in ⭐ MỚI (4 bảng)
- `activity_checkpoint_templates`
- `activity_checkpoints`
- `activity_checkins`
- `activity_checkin_summary`

### Module 10: Vehicle & Driver ⭐ MỚI (5 bảng)
- `vehicles`
- `drivers`
- `vehicle_assignments`
- `driver_schedules`
- `vehicle_maintenance`
- `vehicle_assignment_history`

### Module 11: System Other (3 bảng)
- `email_templates`
- `email_logs`
- `vehicles` (đã có trong module 10)

---

## ⚠️ LƯU Ý

1. **Thứ tự import:** Phải import theo thứ tự (01 → 11) vì có foreign key dependencies
2. **File 00_main.sql:** Import tất cả các module tự động
3. **Dữ liệu mẫu:** Đã có trong file 00_main.sql (allowance rules mẫu)

---

## 🔄 THAY ĐỔI SO VỚI SCHEMA CŨ

### Bảng mới (14 bảng):
1. `tour_allowance_rules`
3. `room_assignments`
4. `room_assignment_customers`
5. `room_requests`
6. `room_assignment_history`
7. `activity_checkpoint_templates`
8. `activity_checkpoints`
9. `activity_checkins`
10. `activity_checkin_summary`
11. `drivers`
12. `vehicle_assignments`
13. `driver_schedules`
14. `vehicle_maintenance`
15. `vehicle_assignment_history`

### Bảng sửa (5 bảng):
1. `tours` - Bỏ 4 cột fixed_cost, thay bằng 1 cột fixed_cost_total (nhập trực tiếp)
2. `tour_schedules` - Thêm status 'confirmed'
3. `incurred_expenses` - Thêm tour_schedule_id
4. `tour_assignments` - tour_schedule_id NOT NULL
5. `service_provider_payments` - Thêm tour_schedule_id

---

## ✅ HOÀN THÀNH

Tất cả các file đã sẵn sàng để sử dụng! 🎉

