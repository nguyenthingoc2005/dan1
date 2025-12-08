# Giải Thích Các Thông Số Dashboard & Báo Cáo

Tài liệu này giải thích chi tiết về các metrics/thông số được hiển thị trên Dashboard và các báo cáo trong hệ thống quản lý Tour.

---

## 📊 PHẦN 1: ADMIN DASHBOARD

**Đường dẫn:** `?act=admin` (Dashboard chính của Admin)

### 1. Tổng Booking (Total Bookings)

**Định nghĩa:**
- Tổng số lượng booking trong toàn bộ hệ thống (tất cả các booking từ trước đến nay)

**Công thức tính:**
```
COUNT(*) FROM bookings
```

**Giải thích:**
- Đếm tất cả các booking không phân biệt trạng thái
- Bao gồm: unpaid, partial, paid, cancelled, refunded, rejected

**Ý nghĩa:**
- Cho biết tổng số đơn đặt tour đã được tạo trong hệ thống
- Thể hiện quy mô hoạt động của công ty

**Sub-metric: Đã duyệt**
- Số booking đã thanh toán (partial hoặc paid)
- Công thức: `COUNT(*) WHERE payment_status IN ('partial', 'paid')`

---

### 2. Chờ Duyệt (Pending Bookings)

**Định nghĩa:**
- Số lượng booking chưa thanh toán, cần xử lý

**Công thức tính:**
```
COUNT(*) FROM bookings WHERE payment_status = 'unpaid'
```

**Giải thích:**
- Booking mới được tạo nhưng chưa có thanh toán nào
- Cần theo dõi và xử lý để thu tiền

**Ý nghĩa:**
- Công việc cần làm ngay (action items)
- Tỷ lệ pending cao → cần cải thiện quy trình thu tiền

---

### 3. Tổng Doanh Thu (Total Revenue)

**Định nghĩa:**
- Tổng số tiền đã thu được từ các thanh toán thành công

**Công thức tính:**
```
SUM(amount) FROM payments 
WHERE status = 'completed' 
AND payment_type != 'refund'
```

**Giải thích:**
- Chỉ tính các payment có trạng thái `completed`
- Không tính refund (hoàn tiền)
- Bao gồm cả deposit, installment, full payment

**Ý nghĩa:**
- Doanh thu thực tế đã thu được
- Là tiền đã vào tài khoản công ty

**Lưu ý:**
- Đây là doanh thu từ payments, không phải từ final_amount của booking
- Một booking có thể có nhiều payments (trả góp)

---

### 4. Tours Hoạt động (Active Tours)

**Định nghĩa:**
- Số lượng tour đang trong trạng thái active, sẵn sàng nhận booking

**Công thức tính:**
```
COUNT(*) FROM tours WHERE status = 'active'
```

**Giải thĩa:**
- Tour đã được duyệt và đang hoạt động
- Khách hàng có thể đặt tour này

**Ý nghĩa:**
- Số sản phẩm đang bán
- Tour càng nhiều → càng nhiều lựa chọn cho khách hàng

---

### 5. Tours Chờ Duyệt (Pending Tours)

**Định nghĩa:**
- Số lượng tour đang chờ admin duyệt

**Công thức tính:**
```
COUNT(*) FROM tours WHERE status = 'pending'
```

**Giải thích:**
- Tour được staff tạo nhưng chưa được admin duyệt
- Chưa thể đặt booking cho tour này

**Ý nghĩa:**
- Công việc cần xử lý (duyệt hoặc từ chối)
- Tỷ lệ pending cao → cần cải thiện quy trình duyệt

---

## 📈 PHẦN 2: BÁO CÁO DOANH THU

**Đường dẫn:** `?act=admin&module=reports&action=revenue`

### 1. Tổng Doanh Thu (Total Revenue)

**Định nghĩa:**
- Tổng số tiền thu được từ payments trong khoảng thời gian đã chọn

**Công thức:**
```
SUM(p.amount) FROM payments p
INNER JOIN bookings b ON p.booking_id = b.id
WHERE p.status = 'completed' 
AND p.payment_type != 'refund'
AND DATE(p.payment_date) BETWEEN start_date AND end_date
```

**Ý nghĩa:**
- Doanh thu thực tế trong kỳ báo cáo
- Là tiền đã vào tài khoản (không phải công nợ)

---

### 2. Tổng Chi Phí (Total Costs)

**Định nghĩa:**
- Tổng các khoản chi trong kỳ báo cáo

**Công thức:**
```
Total Costs = Tiền trả dịch vụ + Hoàn tiền + Lương HDV + Lương tài xế
```

**Chi tiết:**

#### 2.1. Tiền trả dịch vụ (Service Provider Payments)
```
SUM(amount) FROM service_provider_payments
WHERE status = 'completed'
AND DATE(payment_date) BETWEEN start_date AND end_date
```
- Tiền đã trả cho các nhà cung cấp dịch vụ (khách sạn, nhà hàng, xe, vé...)

#### 2.2. Hoàn tiền (Refunds)
```
SUM(p.amount) FROM payments p
WHERE p.status = 'completed' 
AND p.payment_type = 'refund'
AND DATE(p.payment_date) BETWEEN start_date AND end_date
```
- Tiền đã hoàn lại cho khách hàng (khi hủy booking)

#### 2.3. Lương HDV (Guide Salary)
```
SUM(ta.salary_amount) FROM tour_assignments ta
INNER JOIN tour_schedules ts ON ta.tour_schedule_id = ts.id
WHERE ta.salary_status = 'paid'
AND DATE(ts.end_date) BETWEEN start_date AND end_date
```
- Lương/phụ cấp đã trả cho hướng dẫn viên

#### 2.4. Lương tài xế (Driver Salary)
```
SUM(va.driver_salary) FROM vehicle_assignments va
INNER JOIN tour_schedules ts ON va.tour_schedule_id = ts.id
WHERE va.status = 'completed'
AND DATE(ts.end_date) BETWEEN start_date AND end_date
```
- Lương/phụ cấp đã trả cho tài xế

---

### 3. Lợi Nhuận (Profit)

**Định nghĩa:**
- Số tiền lãi/thực thu sau khi trừ chi phí

**Công thức:**
```
Profit = Tổng Doanh Thu - Tổng Chi Phí
```

**Ý nghĩa:**
- Profit > 0: Lãi
- Profit < 0: Lỗ
- Profit = 0: Hòa vốn

**Tỷ suất lợi nhuận (Profit Margin):**
```
Tỷ suất = (Profit / Total Revenue) × 100%
```
- Cho biết % lợi nhuận trên doanh thu
- Tỷ suất cao → hiệu quả kinh doanh tốt

---

### 4. Tổng Booking & Tổng Khách Hàng

**Tổng Booking:**
```
COUNT(DISTINCT b.id) FROM bookings b
INNER JOIN payments p ON b.id = p.booking_id
WHERE DATE(p.payment_date) BETWEEN start_date AND end_date
```
- Số booking có thanh toán trong kỳ

**Tổng Khách Hàng:**
```
COUNT(DISTINCT b.customer_id) FROM bookings b
INNER JOIN payments p ON b.id = p.booking_id
WHERE DATE(p.payment_date) BETWEEN start_date AND end_date
```
- Số khách hàng đã thanh toán trong kỳ

---

### 5. Doanh Thu Theo Tour

**Định nghĩa:**
- Doanh thu được phân loại theo từng tour

**Công thức:**
```
SELECT t.id, t.name, SUM(p.amount) as revenue
FROM tours t
LEFT JOIN bookings b ON t.id = b.tour_id
LEFT JOIN payments p ON b.id = p.booking_id
WHERE p.status = 'completed' AND p.payment_type != 'refund'
GROUP BY t.id
```

**Ý nghĩa:**
- Biết tour nào bán chạy, mang lại nhiều doanh thu
- Định hướng marketing, tập trung vào tour hot

---

### 6. Doanh Thu Theo Phương Thức Thanh Toán

**Định nghĩa:**
- Doanh thu phân loại theo cách khách hàng thanh toán

**Các phương thức:**
- **Tiền mặt (Cash)**: Thanh toán trực tiếp
- **Chuyển khoản (Bank Transfer)**: Chuyển khoản ngân hàng
- **Thẻ tín dụng (Credit Card)**: Thanh toán qua thẻ
- **Khác (Other)**: Các phương thức khác

**Ý nghĩa:**
- Biết khách hàng ưa chuộng phương thức nào
- Tối ưu quy trình thanh toán

---

### 7. Doanh Thu Theo Khách Hàng (Top 10)

**Định nghĩa:**
- Top 10 khách hàng chi tiêu nhiều nhất

**Công thức:**
```
SELECT c.id, c.full_name, SUM(p.amount) as revenue
FROM customers c
INNER JOIN bookings b ON c.id = b.customer_id
INNER JOIN payments p ON b.id = p.booking_id
WHERE p.status = 'completed'
GROUP BY c.id
ORDER BY revenue DESC
LIMIT 10
```

**Ý nghĩa:**
- Khách hàng VIP, cần chăm sóc đặc biệt
- Đề xuất chương trình ưu đãi cho khách hàng thân thiết

---

## 📋 PHẦN 3: BÁO CÁO ĐẶT TOUR

**Đường dẫn:** `?act=admin&module=reports&action=bookings`

### 1. Tổng Số Booking

**Định nghĩa:**
- Tổng số booking được tạo trong khoảng thời gian đã chọn

**Công thức:**
```
COUNT(*) FROM bookings
WHERE DATE(created_at) BETWEEN start_date AND end_date
```

**Ý nghĩa:**
- Lượng đơn đặt tour trong kỳ
- Đánh giá hiệu quả marketing

---

### 2. Tổng Số Khách (Passengers)

**Định nghĩa:**
- Tổng số người đã đặt tour (phân loại theo độ tuổi)

**Công thức:**
```
- Tổng Người lớn: SUM(adult_count)
- Tổng Trẻ em: SUM(child_count)
- Tổng Em bé: SUM(infant_count)
- Tổng số khách: SUM(adult_count + child_count + infant_count)
```

**Ý nghĩa:**
- Số lượng khách thực tế đi tour
- Phân tích độ tuổi khách hàng
- Tính toán capacity (sức chứa tour)

---

### 3. Tỷ Lệ Hủy (Cancellation Rate)

**Định nghĩa:**
- Phần trăm booking bị hủy hoặc hoàn tiền

**Công thức:**
```
Cancellation Rate = (Số booking bị hủy / Tổng số booking) × 100%

Trong đó:
Số booking bị hủy = COUNT(*) WHERE payment_status IN ('cancelled', 'refunded')
```

**Ý nghĩa:**
- Tỷ lệ hủy < 10%: Tốt
- Tỷ lệ hủy 10-20%: Bình thường, cần theo dõi
- Tỷ lệ hủy > 20%: Cần cải thiện (chất lượng tour, chính sách hủy...)

---

### 4. Tổng Giá Trị Booking

**Định nghĩa:**
- Tổng giá trị các booking (theo final_amount)

**Công thức:**
```
- Tổng giá trị: SUM(final_amount)
- Đã thu: SUM(paid_amount)
- Còn lại: SUM(remaining_amount)
```

**Lưu ý:**
- Đây là giá trị booking (final_amount), không phải doanh thu thực tế
- Có thể khác với doanh thu nếu booking chưa thanh toán đủ hoặc bị hủy

---

### 5. Booking Theo Trạng Thái

**Các trạng thái:**
- **Chưa thanh toán (Unpaid)**: Booking mới, chưa có payment nào
- **Thanh toán một phần (Partial)**: Đã thanh toán nhưng chưa đủ
- **Đã thanh toán (Paid)**: Đã thanh toán đủ
- **Đã hủy (Cancelled)**: Booking bị hủy, không hoàn tiền
- **Đã hoàn tiền (Refunded)**: Booking bị hủy, có hoàn tiền
- **Từ chối (Rejected)**: Booking bị từ chối

**Ý nghĩa:**
- Phân tích tình hình thanh toán
- Theo dõi công nợ (remaining_amount)

---

### 6. Booking Theo Nguồn (Source)

**Các nguồn:**
- **Điện thoại (Phone)**: Gọi điện đặt tour
- **Email**: Đặt qua email
- **Facebook**: Đặt qua Facebook
- **Zalo**: Đặt qua Zalo
- **Tại quầy (Walk-in)**: Đến trực tiếp đặt tour
- **Khác (Other)**: Nguồn khác

**Ý nghĩa:**
- Đánh giá hiệu quả kênh marketing
- Phân bổ ngân sách marketing hợp lý
- Biết khách hàng tìm đến qua kênh nào

---

### 7. Booking Theo Tour (Top 20)

**Định nghĩa:**
- Top 20 tour được đặt nhiều nhất

**Metrics:**
- **Số booking**: Số lượng đơn đặt tour
- **Tổng khách**: Tổng số người đã đặt
- **Giá trị**: Tổng giá trị các booking

**Ý nghĩa:**
- Tour hot, cần tăng capacity
- Tour ít người đặt, cần cải thiện marketing hoặc chất lượng

---

## 🔍 PHẦN 4: CÁC THUẬT NGỮ VÀ KHÁI NIỆM

### Payment Status (Trạng thái Thanh toán)

| Trạng thái | Mô tả | Khi nào xuất hiện |
|------------|-------|-------------------|
| `unpaid` | Chưa thanh toán | Booking mới được tạo, chưa có payment nào |
| `partial` | Thanh toán một phần | Đã có payment nhưng chưa đủ final_amount |
| `paid` | Đã thanh toán đủ | Tổng payments >= final_amount |
| `cancelled` | Đã hủy | Booking bị hủy, không hoàn tiền |
| `refunded` | Đã hoàn tiền | Booking bị hủy, có hoàn lại tiền |
| `rejected` | Từ chối | Booking bị từ chối bởi admin |

### Payment Type (Loại Thanh toán)

| Loại | Mô tả |
|------|-------|
| `deposit` | Đặt cọc |
| `installment` | Trả góp |
| `full` | Thanh toán đủ |
| `refund` | Hoàn tiền |

### Payment Method (Phương thức Thanh toán)

| Phương thức | Mô tả |
|-------------|-------|
| `cash` | Tiền mặt |
| `bank_transfer` | Chuyển khoản ngân hàng |
| `credit_card` | Thẻ tín dụng |
| `other` | Khác |

---

## 📐 PHẦN 5: CÁC CÔNG THỨC TÍNH TOÁN QUAN TRỌNG

### 1. Doanh Thu Thực Tế
```
Doanh Thu Thực Tế = SUM(payments.amount) 
WHERE status = 'completed' 
AND payment_type != 'refund'
```

### 2. Lợi Nhuận
```
Lợi Nhuận = Doanh Thu - Chi Phí

Chi Phí = Tiền trả dịch vụ + Hoàn tiền + Lương HDV + Lương tài xế
```

### 3. Tỷ Suất Lợi Nhuận
```
Tỷ Suất = (Lợi Nhuận / Doanh Thu) × 100%
```

### 4. Tỷ Lệ Hủy
```
Tỷ Lệ Hủy = (Số booking bị hủy / Tổng số booking) × 100%
```

### 5. Tỷ Lệ Thanh Toán
```
Tỷ Lệ Thanh Toán = (Số booking đã thanh toán / Tổng số booking) × 100%
```

### 6. Giá Trị Booking Trung Bình
```
Giá Trị TB = SUM(final_amount) / COUNT(bookings)
```

### 7. Số Khách Trung Bình Mỗi Booking
```
Khách TB = SUM(adult_count + child_count + infant_count) / COUNT(bookings)
```

---

## 💡 PHẦN 6: CÁCH ĐỌC VÀ PHÂN TÍCH DỮ LIỆU

### Dashboard Metrics
1. **Tổng Booking tăng** → Công ty đang phát triển tốt
2. **Chờ Duyệt nhiều** → Cần xử lý nhanh, cải thiện quy trình
3. **Doanh Thu tăng** → Hiệu quả kinh doanh tốt
4. **Tours Hoạt động nhiều** → Nhiều sản phẩm, đa dạng
5. **Tours Chờ Duyệt nhiều** → Cần tăng tốc độ duyệt

### Báo Cáo Doanh Thu
1. **Lợi nhuận > 0** → Công ty có lãi
2. **Tỷ suất lợi nhuận cao (20%+)** → Hiệu quả kinh doanh rất tốt
3. **Tour nào doanh thu cao** → Tập trung marketing tour đó
4. **Phương thức thanh toán nào nhiều** → Tối ưu kênh đó

### Báo Cáo Đặt Tour
1. **Tỷ lệ hủy thấp (<10%)** → Dịch vụ tốt, khách hàng hài lòng
2. **Nguồn nào nhiều booking** → Tăng cường marketing nguồn đó
3. **Tour nào được đặt nhiều** → Tăng capacity, tạo lịch trình nhiều hơn
4. **Số khách tăng** → Công ty đang phát triển

---

## ⚠️ LƯU Ý QUAN TRỌNG

### 1. Sự Khác Biệt Giữa Doanh Thu và Giá Trị Booking
- **Doanh Thu**: Tiền thực tế đã thu (từ payments)
- **Giá Trị Booking**: Số tiền khách hàng phải trả (final_amount)
- Có thể khác nhau nếu booking chưa thanh toán đủ hoặc bị hủy

### 2. Thời Gian Tính Toán
- Dashboard: Tính tất cả dữ liệu (không filter thời gian)
- Báo cáo: Tính theo khoảng thời gian đã chọn (start_date → end_date)

### 3. Dữ Liệu Thời Gian Thực
- Dashboard và báo cáo hiển thị dữ liệu thời gian thực
- Khi có thay đổi (thanh toán, hủy booking...), metrics sẽ tự động cập nhật

### 4. Bộ Lọc Nâng Cao
- Báo cáo hỗ trợ lọc theo nhiều tiêu chí
- Kết quả sẽ thay đổi theo bộ lọc đã chọn
- Xuất Excel sẽ theo đúng dữ liệu đã lọc

---

## 📞 HỖ TRỢ

Nếu có thắc mắc về các metrics, vui lòng liên hệ:
- Admin hệ thống
- Đội ngũ phát triển

---

**Cập nhật lần cuối:** 2024-12-03
**Phiên bản:** 1.0

