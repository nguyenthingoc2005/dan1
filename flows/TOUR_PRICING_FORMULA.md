# 💰 CÔNG THỨC TÍNH GIÁ TOUR

## 🎯 MỤC TIÊU

**Khi tạo một tour, hệ thống phải tự động tính và hiển thị: "Số tiền một người phải bỏ ra để đi tour đó"**

**Logic:** Đã tính đủ chi phí (dịch vụ + lương HDV + marketing + quản lý) → Giá tour = Chi phí thực tế, không cần thêm markup/lợi nhuận nữa.

---

## 📊 CÔNG THỨC CHI TIẾT

### **BƯỚC 1: Tính Chi phí Dịch vụ/người**

**Công thức:**

```
Chi phí dịch vụ/người = Σ(unit_price)
```

**Trong đó:**

- `unit_price` = Đơn giá của từng service (tính cho 1 người)
- Chỉ tính các services có `is_included_in_price = 1`

**Ví dụ:**

```
Services trong tour:
- Khách sạn: 200,000đ/người
- Vé tham quan: 50,000đ/người
- Ăn uống: 150,000đ/người
──────────────────────────────────────
Chi phí dịch vụ/người = 200,000 + 50,000 + 150,000 = 400,000đ/người
```

**Lưu ý:**

- Tất cả services đều tính `per_person` (theo từng người)
- Không cần tính theo nhóm, theo ngày, hay cố định
- Mỗi người đi tour đều có chi phí dịch vụ riêng

---

### **BƯỚC 2: Tính Chi phí Cố định/người**

**Công thức:**

```
Tổng chi phí cố định = fixed_cost_guide + fixed_cost_management + fixed_cost_marketing + fixed_cost_other

Chi phí cố định/người = Tổng chi phí cố định ÷ min_participants
```

**Trong đó:**

- `fixed_cost_guide` = Lương HDV (Hướng dẫn viên)
- `fixed_cost_management` = Chi phí quản lý tour
- `fixed_cost_marketing` = Chi phí marketing
- `fixed_cost_other` = Chi phí khác
- `min_participants` = Số người tối thiểu để tổ chức tour (VD: 30 người)

**Giải thích tại sao cần chia cho số người:**

- Chi phí cố định (lương HDV, quản lý, marketing) là chi phí chung cho cả tour, không phải per_person
- **Ví dụ:** Lương HDV 2,000,000đ cho tour → nếu 30 người thì 66,667đ/người, nếu 20 người thì 100,000đ/người
- **Logic:** Giá tour được tính cố định dựa trên `min_participants`. Nếu booking có nhiều người hơn → lợi nhuận tự động tăng (giá không đổi)

**→ KHÔNG cần field `price_based_on_pax`, chỉ cần `min_participants`**

**Ví dụ:**

```
Chi phí cố định:
- Lương HDV: 2,000,000đ
- Chi phí quản lý: 1,500,000đ
- Chi phí marketing: 500,000đ
- Chi phí khác: 0đ
──────────────────────────────────────
Tổng chi phí cố định = 2,000,000 + 1,500,000 + 500,000 = 4,000,000đ

Giá tính theo: 40 người
Chi phí cố định/người = 4,000,000 ÷ 40 = 100,000đ/người
```

**Lý do chia đều:**

- Chi phí cố định (lương HDV, quản lý, marketing) là chi phí chung cho cả tour
- Chia đều cho số người để tính chi phí/người
- Nếu tour có nhiều người hơn, chi phí cố định/người sẽ giảm (kinh tế theo quy mô)

---

### **BƯỚC 3: Tính Tổng Chi phí/người**

**Công thức:**

```
Tổng chi phí/người = Chi phí dịch vụ/người + Chi phí cố định/người
```

**Ví dụ:**

```
Chi phí dịch vụ/người: 400,000đ
Chi phí cố định/người: 133,333đ (tính theo min 30 người)
──────────────────────────────────────
Tổng chi phí/người = 400,000 + 133,333 = 533,333đ/người
```

**Lưu ý:**

- Đây là chi phí thực tế để tổ chức tour cho 1 người
- Chưa bao gồm lợi nhuận (markup)
- Lưu vào `estimated_cost_per_person` trong database

---

### **BƯỚC 4: Tính Giá Bán/người**

**Công thức (KHÔNG có Markup):**

```
Giá bán/người = Tổng chi phí/người
```

**Giải thích:**

- Đã tính đủ chi phí:
  - Chi phí dịch vụ (khách sạn, xe, ăn, vé...)
  - Chi phí cố định (lương HDV, marketing, quản lý)
- → **Giá tour = Chi phí thực tế đầy đủ**
- → **KHÔNG cần thêm markup/lợi nhuận nữa**

**Ví dụ:**

```
Tổng chi phí/người: 533,333đ
──────────────────────────────────────
Giá bán/người = 533,333đ/người ✅
```

**Kết quả:**

- `adult_price` = 533,333đ
- **Đây chính là số tiền một người phải bỏ ra để đi tour**
- **Giá = Chi phí thực tế (đã bao gồm tất cả: dịch vụ + nhân sự + marketing + quản lý)**

---

## 📋 VÍ DỤ HOÀN CHỈNH

### **Tour: Đà Lạt 3 ngày 2 đêm (40 người)**

**A. Chi phí dịch vụ (theo từng người):**

```
- Khách sạn 3 sao:       200,000đ/người
- Vé tham quan:           50,000đ/người
- Ăn uống (3 ngày):      150,000đ/người
────────────────────────────────────────
Chi phí dịch vụ/người =  400,000đ/người
```

**B. Chi phí cố định (chia đều):**

```
- Lương HDV:           2,000,000đ
- Chi phí quản lý:     1,500,000đ
- Chi phí marketing:     500,000đ
────────────────────────────────────────
Tổng chi phí cố định = 4,000,000đ

Chia cho số người tối thiểu (30 người):
Chi phí cố định/người = 4,000,000 ÷ 30 = 133,333đ/người
```

**C. Tổng chi phí/người:**

```
Chi phí dịch vụ/người:    400,000đ
Chi phí cố định/người:    100,000đ
────────────────────────────────────────
Tổng chi phí/người    =   500,000đ/người
```

**D. Giá bán (Markup 20%):**

```
Tổng chi phí/người:       533,333đ
Markup 20%:               106,667đ
────────────────────────────────────────
Giá bán/người lớn     =   640,000đ/người ✅
```

**Kết luận:**

- **Một người muốn đi tour này phải bỏ ra: 640,000đ** (tính dựa trên min 30 người)
- Lợi nhuận của công ty: 106,667đ/người (20%)
- Nếu booking có > 30 người → Lợi nhuận tự động tăng (giá không đổi)

---

## 💡 LOGIC TÍNH TOÁN

### **Khi số người thay đổi:**

**Nếu số người thực tế khác `price_based_on_pax`:**

- **Chi phí dịch vụ/người:** KHÔNG đổi (mỗi người vẫn có chi phí riêng)
- **Chi phí cố định/người:** THAY ĐỔI (chia đều cho số người thực tế)

**Ví dụ:**

```
Tour tính với 40 người:
- Chi phí dịch vụ/người: 400,000đ (không đổi)
- Chi phí cố định/người: 100,000đ (4,000,000 ÷ 40)

Nếu booking thực tế chỉ có 30 người:
- Chi phí dịch vụ/người: 400,000đ (không đổi)
- Chi phí cố định/người: 133,333đ (4,000,000 ÷ 30)
- Tổng chi phí/người: 533,333đ (tăng lên do ít người hơn)
- Giá bán/người: 533,333đ (chi phí thực tế, không markup)
```

**→ Lý do cần `min_participants`:** Đảm bảo số người đủ để chia đều chi phí cố định

---

## ✅ KẾT LUẬN

**Công thức tổng quát:**

```
Chi phí dịch vụ/người = Σ(unit_price)

Chi phí cố định/người = Σ(fixed_costs) ÷ min_participants

Tổng chi phí/người = Chi phí dịch vụ/người + Chi phí cố định/người

Giá bán/người = Tổng chi phí/người
```

**Hoặc đơn giản:**

```
Giá bán/người = Σ(unit_price) + (Σ(fixed_costs) ÷ min_participants)
```

**Lưu ý:**

- Đã tính đủ: Chi phí dịch vụ + Chi phí cố định (lương HDV, marketing, quản lý)
- → **Giá = Chi phí thực tế đầy đủ**
- → **KHÔNG cần thêm markup/lợi nhuận**

**Kết quả:**

- **"Số tiền một người phải bỏ ra để đi tour đó"** = `Giá bán/người` (lưu vào `adult_price`)
- Giá này được tính cố định dựa trên `min_participants` (số người tối thiểu)
- Áp dụng cho tất cả booking, dù có ít hay nhiều người hơn `min_participants`
- Hệ thống tự động tính và hiển thị: **"Một người phải bỏ ra XXX,XXXđ để đi tour này"**
- Staff có thể xem breakdown chi tiết và điều chỉnh nếu cần

**Đơn giản hóa:**

- ❌ KHÔNG cần `price_based_on_pax` nữa
- ✅ CHỈ cần `min_participants` để chia chi phí cố định
- ❌ KHÔNG cần `markup_percentage` nữa (đã tính đủ chi phí rồi, không cần lợi nhuận)
- ✅ Giá tour = Chi phí thực tế đầy đủ (dịch vụ + nhân sự + marketing + quản lý)

---

**Ngày tạo:** 2024-12-06
