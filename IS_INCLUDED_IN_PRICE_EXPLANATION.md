# GIẢI THÍCH: BAO GỒM TRONG GIÁ TOUR (is_included_in_price)

## TỔNG QUAN

Trường `is_included_in_price` trong bảng `itinerary_day_services` quyết định xem dịch vụ có được tính vào giá tour hay không.

## LOGIC CHI TIẾT

### 1. KHI TÍCH (is_included_in_price = 1) ✅

**Ý nghĩa:** Dịch vụ này ĐƯỢC TÍNH vào giá tour ban đầu.

**Hành vi:**
- ✅ Dịch vụ được tính vào `estimated_cost_per_person` khi tính giá tour
- ✅ Khách KHÔNG phải trả thêm khi sử dụng dịch vụ này
- ✅ Dịch vụ được hiển thị trong mục "Bao gồm" của tour
- ✅ Giá tour đã bao gồm chi phí dịch vụ này

**Ví dụ:**
- Phòng khách sạn: TÍCH → Giá tour đã bao gồm phòng
- Bữa sáng: TÍCH → Giá tour đã bao gồm bữa sáng
- Vé tham quan: TÍCH → Giá tour đã bao gồm vé
- Xe đưa đón: TÍCH → Giá tour đã bao gồm xe

**Công thức tính giá:**
```
estimated_cost_per_person = Σ(unit_price × quantity) 
WHERE is_included_in_price = 1
```

### 2. KHI KHÔNG TÍCH (is_included_in_price = 0) ❌

**Ý nghĩa:** Dịch vụ này KHÔNG tính vào giá tour ban đầu.

**Hành vi:**
- ❌ Dịch vụ KHÔNG tính vào `estimated_cost_per_person`
- ❌ Khách PHẢI TRẢ THÊM khi sử dụng dịch vụ này
- ❌ Dịch vụ được hiển thị trong mục "Không bao gồm" của tour
- ❌ Giá tour KHÔNG bao gồm chi phí dịch vụ này

**Ví dụ:**
- Bữa trưa: KHÔNG TÍCH → Khách tự trả khi ăn
- Đồ uống: KHÔNG TÍCH → Khách tự trả khi uống
- Dịch vụ spa: KHÔNG TÍCH → Khách tự trả nếu dùng
- Phụ thu phòng đơn: KHÔNG TÍCH → Khách tự trả nếu ở phòng đơn

**Công thức tính giá:**
```
estimated_cost_per_person = Σ(unit_price × quantity) 
WHERE is_included_in_price = 1
// Dịch vụ is_included_in_price = 0 KHÔNG được tính
```

## VÍ DỤ THỰC TẾ

### Tour Đà Lạt 3 ngày 2 đêm

**Dịch vụ TÍCH (is_included_in_price = 1):**
- Phòng khách sạn 2 đêm: 1,500,000đ/người → Tính vào giá
- Bữa sáng 2 ngày: 200,000đ/người → Tính vào giá
- Vé tham quan: 500,000đ/người → Tính vào giá
- Xe đưa đón: 300,000đ/người → Tính vào giá
- **Tổng tính vào giá:** 2,500,000đ/người

**Dịch vụ KHÔNG TÍCH (is_included_in_price = 0):**
- Bữa trưa: 350,000đ/người → Khách tự trả
- Bữa tối: 400,000đ/người → Khách tự trả
- Đồ uống: 100,000đ/người → Khách tự trả
- **Tổng không tính vào giá:** 850,000đ/người (khách tự trả)

**Giá tour cuối cùng:**
- Giá bán: 3,000,000đ/người (đã bao gồm 2,500,000đ dịch vụ)
- Khách tự trả thêm: 850,000đ/người (nếu dùng các dịch vụ không bao gồm)

## TRONG CODE

### Database
```sql
-- Bảng itinerary_day_services
is_included_in_price TINYINT(1) NOT NULL DEFAULT 1
-- 1 = Tích (tính vào giá)
-- 0 = Không tích (không tính vào giá)
```

### Tính giá tour
```php
// Chỉ tính các dịch vụ is_included_in_price = 1
$cost = calculateTotalCostPerPerson($tour_id);
// Function này chỉ SUM các dịch vụ có is_included_in_price = 1
```

### Hiển thị
- **Bao gồm:** Lấy từ `itinerary_day_services` WHERE `is_included_in_price = 1`
- **Không bao gồm:** Lấy từ `itinerary_day_services` WHERE `is_included_in_price = 0`

## LƯU Ý

1. **Mặc định:** `is_included_in_price = 1` (tính vào giá)
2. **Khi tạo booking:** Chỉ tính các dịch vụ `is_included_in_price = 1` vào giá booking
3. **Khi khách dùng dịch vụ không bao gồm:** Tạo `booking_services` riêng với giá riêng
4. **Báo cáo:** Phân biệt rõ dịch vụ bao gồm và không bao gồm trong báo cáo tài chính

