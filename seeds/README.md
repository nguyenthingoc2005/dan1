# SEED DATA FILES

Các file seed data được tách riêng theo từng bảng để dễ quản lý và chạy từng phần.

## Thứ tự chạy

### Bước 1: Chạy seed location services (bắt buộc)

```bash
mysql -u root -p tour_managementss < seed_location_services.sql
```

### Bước 2: Chạy các file seed theo thứ tự

```bash
# 1. Roles (Vai trò)
mysql -u root -p tour_managementss < seeds/01_roles.sql

# 2. Users (Người dùng)
mysql -u root -p tour_managementss < seeds/02_users.sql

# 3. Customers (Khách hàng)
mysql -u root -p tour_managementss < seeds/03_customers.sql

# 4. Tours (Tour)
mysql -u root -p tour_managementss < seeds/04_tours.sql

# 5. Tour Schedules (Lịch khởi hành)
mysql -u root -p tour_managementss < seeds/05_tour_schedules.sql

# 6. Itineraries (Lịch trình)
mysql -u root -p tour_managementss < seeds/06_itineraries.sql

# 7. Itinerary Timelines (Timeline chi tiết)
mysql -u root -p tour_managementss < seeds/07_itinerary_timelines.sql

# 8. Itinerary Day Services (Dịch vụ theo ngày)
mysql -u root -p tour_managementss < seeds/08_itinerary_day_services.sql

# 9. Tour Services (Dịch vụ của tour)
mysql -u root -p tour_managementss < seeds/09_tour_services.sql

# 10. Bookings (Đặt tour)
mysql -u root -p tour_managementss < seeds/10_bookings.sql

# 11. Booking Customers (Khách hàng trong booking)
mysql -u root -p tour_managementss < seeds/11_booking_customers.sql

# 12. Booking Services (Dịch vụ đặt cho booking)
mysql -u root -p tour_managementss < seeds/12_booking_services.sql

# 13. Payments (Thanh toán)
mysql -u root -p tour_managementss < seeds/13_payments.sql

# 14. Cancellation Policies (Chính sách hủy)
mysql -u root -p tour_managementss < seeds/14_cancellation_policies.sql

# 15. Discount Codes (Mã giảm giá)
mysql -u root -p tour_managementss < seeds/15_discount_codes.sql

# 16. Policies (Chính sách)
mysql -u root -p tour_managementss < seeds/16_policies.sql

# 17. Vehicles (Xe)
mysql -u root -p tour_managementss < seeds/17_vehicles.sql

# 18. Tour Assignments (Phân công tour)
mysql -u root -p tour_managementss < seeds/18_tour_assignments.sql

# 19. Invoices (Hóa đơn)
mysql -u root -p tour_managementss < seeds/19_invoices.sql

# 20. Service Provider Payments (Thanh toán cho nhà dịch vụ)
mysql -u root -p tour_managementss < seeds/20_service_provider_payments.sql
```

## Chạy tất cả cùng lúc (Windows)

```bash
# Tạo file batch để chạy tất cả
for %f in (seeds\*.sql) do mysql -u root -p tour_managementss < %f
```

## Chạy tất cả cùng lúc (Linux/Mac)

```bash
# Chạy tất cả file seed theo thứ tự
for file in seeds/*.sql; do
    mysql -u root -p tour_managementss < "$file"
done
```

## Lưu ý

- **Bắt buộc**: Phải chạy `seed_location_services.sql` trước tất cả các file trong thư mục `seeds/`
- Các file được đánh số theo thứ tự phụ thuộc
- Mỗi file có comment rõ ràng về các phụ thuộc
- Có thể chạy từng file riêng lẻ để test hoặc debug
