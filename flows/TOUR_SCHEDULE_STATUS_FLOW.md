# 🔄 LUỒNG TRẠNG THÁI TOUR SCHEDULE

## 📊 CÁC TRẠNG THÁI

1. **`open`** - Mở đặt booking
2. **`closed`** - Đóng đặt (không nhận booking mới)
3. **`pending`** - Trước đi (đã đủ số người, đã phân công guide, chờ khởi hành)
4. **`in_progress`** - Đang đi (tour đang diễn ra)
5. **`completed`** - Đã đi xong (tour đã hoàn thành)
6. **`cancelled`** - Tour bị hủy

---

## 🔄 LUỒNG CHUYỂN TRẠNG THÁI

### **1. open → pending**

**Điều kiện:**

- `booked >= min_participants` (đủ số người tối thiểu)
- Đã phân công guide (`guide_id` != NULL)

**Cách chuyển:**

- Tự động: Khi phân công guide và đủ số người
- Hoặc thủ công: Staff có thể set `status = 'pending'` nếu đủ điều kiện

**Ý nghĩa:**

- Tour đã sẵn sàng khởi hành
- Không nhận booking mới (tùy chọn, có thể vẫn mở)

---

### **2. pending → in_progress**

**Điều kiện:**

- `start_date = ngày hiện tại`
- `status = 'pending'`

**Cách chuyển:**

- **Tự động:** Cron job chạy hàng ngày để check và update

**Ý nghĩa:**

- Tour đã khởi hành
- Đang trong quá trình tour diễn ra

---

### **3. in_progress → completed**

**Điều kiện:**

- `end_date < ngày hiện tại`
- `status = 'in_progress'`

**Cách chuyển:**

- **Tự động:** Cron job chạy hàng ngày để check và update

**Ý nghĩa:**

- Tour đã kết thúc
- Có thể thu thập feedback, thanh toán guide...

---

### **4. open/closed/pending → cancelled**

**Điều kiện:**

- Hủy tour vì:
  - Không đủ số người (tự động khi đến ngày khởi hành)
  - Lý do khác (thủ công bởi staff/admin)

**Cách chuyển:**

- **Tự động:** Khi đến ngày khởi hành và `booked < min_participants`
- **Thủ công:** Staff/Admin hủy tour

**Xử lý:**

- Hủy tất cả bookings
- Hoàn tiền (100% hoặc theo chính sách)
- Gửi thông báo đến khách hàng

---

## 📋 BIỂU ĐỒ TRẠNG THÁI

```
[open] ────────┐
               │ (đủ số người + có guide)
               ▼
[closed] ────→ [pending] ──(start_date)──→ [in_progress] ──(end_date)──→ [completed]
               │                                    │
               │ (hủy tour)                        │ (hủy tour)
               └───────────────────────────────────┴────────────────────→ [cancelled]
```

---

## ⚠️ LƯU Ý

1. **Không thể quay lại:**

   - `completed` → Không thể đổi sang trạng thái khác
   - `cancelled` → Không thể đổi sang trạng thái khác (trừ khi tạo schedule mới)

2. **Tự động chuyển:**

   - `pending` → `in_progress`: Tự động khi đến ngày khởi hành
   - `in_progress` → `completed`: Tự động khi tour kết thúc
   - → `cancelled`: Tự động khi đến ngày khởi hành nhưng chưa đủ số người

3. **Phân công Guide:**
   - Chỉ cho phép khi `booked >= min_participants`
   - Sau khi phân công → Tự động set `status = 'pending'`

---

**Ngày tạo:** 2024-12-06
