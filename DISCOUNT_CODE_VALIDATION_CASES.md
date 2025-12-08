# CÁC CASE VALIDATION MÃ GIẢM GIÁ

## Tổng quan
Hệ thống validate mã giảm giá với các case sau:

---

## ✅ CASE 1: Mã rỗng (Cho phép)
- **Điều kiện**: `discount_code` = rỗng hoặc null
- **Hành động**: Cho phép giảm trực tiếp (nhập số tiền thủ công)
- **Kết quả**: `valid = true`, `discount_amount = 0` (hoặc số tiền user nhập)

---

## ❌ CASE 2: Mã không tồn tại
- **Điều kiện**: Mã không có trong bảng `discount_codes`
- **Thông báo**: "Mã giảm giá không tồn tại"
- **Kết quả**: `valid = false`

---

## ❌ CASE 3: Mã bị vô hiệu hóa
- **Điều kiện**: `status != 'active'`
- **Thông báo**: "Mã giảm giá đã bị vô hiệu hóa"
- **Kết quả**: `valid = false`

---

## ❌ CASE 4: Mã chưa có hiệu lực
- **Điều kiện**: `start_date > today` (nếu có start_date)
- **Thông báo**: "Mã giảm giá chưa có hiệu lực (bắt đầu từ DD/MM/YYYY)"
- **Kết quả**: `valid = false`

---

## ❌ CASE 5: Mã đã hết hạn
- **Điều kiện**: `end_date < today` (nếu có end_date)
- **Thông báo**: "Mã giảm giá đã hết hạn (hết hạn ngày DD/MM/YYYY)"
- **Kết quả**: `valid = false`

---

## ❌ CASE 6: Mã đã hết số lần sử dụng
- **Điều kiện**: `usage_limit > 0` VÀ `used_count >= usage_limit`
- **Thông báo**: "Mã giảm giá đã hết số lần sử dụng (X/Y)"
- **Kết quả**: `valid = false`
- **Lưu ý**: Nếu `usage_limit = 0` → không giới hạn

---

## ❌ CASE 7: Đơn hàng không đạt giá trị tối thiểu
- **Điều kiện**: `total_amount < min_purchase`
- **Thông báo**: "Đơn hàng phải có giá trị tối thiểu X đ để sử dụng mã này"
- **Kết quả**: `valid = false`

---

## ❌ CASE 8: Booking đã dùng mã khác
- **Điều kiện**: Booking đã có `discount_code` khác với mã đang nhập
- **Thông báo**: "Booking này đã sử dụng mã giảm giá khác (CODE). Mỗi booking chỉ được dùng 1 mã."
- **Kết quả**: `valid = false`
- **Lưu ý**: Chỉ check khi đang update booking (có `booking_id`)

---

## ✅ CASE 9: Tính toán số tiền giảm

### 9.1. Loại Percentage (%)
- **Công thức**: `discount_amount = total_amount × (discount_value / 100)`
- **Ví dụ**: 
  - `total_amount = 1,000,000 đ`
  - `discount_value = 10` (%)
  - `discount_amount = 1,000,000 × 10% = 100,000 đ`

### 9.2. Loại Fixed (Số tiền cố định)
- **Công thức**: `discount_amount = min(discount_value, total_amount)`
- **Ví dụ**:
  - `total_amount = 1,000,000 đ`
  - `discount_value = 200,000 đ`
  - `discount_amount = min(200,000, 1,000,000) = 200,000 đ`
- **Lưu ý**: Không giảm quá tổng tiền

---

## ✅ CASE 10: Validate số tiền giảm
- **Điều kiện**: `discount_amount <= total_amount`
- **Điều kiện**: `discount_amount >= 0`
- **Kết quả**: Nếu vi phạm → throw Exception

---

## 🔄 XỬ LÝ USED_COUNT

### Khi áp dụng mã mới:
- Nếu booking chưa có mã → `used_count++` của mã mới
- Nếu booking đã có mã khác → `used_count--` của mã cũ, `used_count++` của mã mới

### Khi xóa mã:
- `used_count--` của mã cũ (nếu có)

### Khi hủy booking:
- Cần xử lý riêng (có thể thêm logic sau)

---

## 📋 LUỒNG XỬ LÝ TRONG CONTROLLER

1. **Validate booking_id** → Throw nếu không hợp lệ
2. **Validate booking status** → Không cho áp dụng nếu `cancelled/rejected/refunded`
3. **Validate mã giảm giá** (nếu có) → Gọi `DiscountCode::validateForBooking()`
4. **Tính discount_amount** → Tự động từ mã hoặc nhập thủ công
5. **Xử lý used_count** → Tăng/giảm trong transaction
6. **Update booking** → Lưu `discount_code`, `discount_amount`, `final_amount`
7. **Tính lại payment status** → Gọi `updatePaymentStatus()`
8. **Log history** → Ghi lại hành động

---

## 🎯 RÀNG BUỘC: 1 BOOKING = 1 MÃ

- Mỗi booking chỉ được sử dụng **1 mã giảm giá** tại một thời điểm
- Nếu booking đã có mã → Phải xóa mã cũ trước khi áp dụng mã mới
- Logic được xử lý trong `validateForBooking()` (CASE 8)

---

## 📝 GHI CHÚ

- Mã giảm giá có thể để trống → Cho phép giảm trực tiếp (không cần mã)
- `discount_amount` có thể nhập thủ công nếu không có mã
- Nếu có mã → Tự động tính `discount_amount` từ mã
- Nếu user thay đổi `discount_amount` sau khi validate mã → Cảnh báo (nhưng vẫn cho phép)

