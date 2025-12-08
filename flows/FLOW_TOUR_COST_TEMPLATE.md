# FLOW: CHI PHÍ CỐ ĐỊNH (FIXED COST)

## 📋 TỔNG QUAN

Tính năng chi phí cố định cho tour:
- Nhập trực tiếp tổng chi phí cố định (`fixed_cost_total`)
- Đơn giản, không cần quản lý template
- Tự động tính chi phí/người = `fixed_cost_total ÷ min_participants`

---

## 🔄 WORKFLOW CHI TIẾT

### BƯỚC 1: TẠO TOUR MỚI

**Actor:** Admin/Tour Manager

**Hành động:**
1. Tạo tour mới
2. Nhập `fixed_cost_total` (tổng chi phí cố định)
   - VD: 2.000.000 (cho tour trong nước 3 ngày)
   - VD: 3.000.000 (cho tour trong nước 5-7 ngày)
   - VD: 5.000.000 (cho tour quốc tế)

3. Hệ thống tự động tính chi phí cố định/người:
   ```sql
   -- Chi phí cố định/người = fixed_cost_total ÷ min_participants
   SELECT 
       fixed_cost_total,
       min_participants,
       (fixed_cost_total / min_participants) AS fixed_cost_per_person
   FROM tours
   WHERE id = ?;
   ```

---

### BƯỚC 2: SỬA TOUR

**Actor:** Admin/Tour Manager

**Hành động:**
1. Sửa tour
2. Chỉnh sửa `fixed_cost_total` nếu cần
3. Hệ thống tự động tính lại chi phí cố định/người

---

## 📊 QUERY HỮU ÍCH

### Xem chi phí cố định của tour
```sql
SELECT 
    id,
    name,
    fixed_cost_total,
    min_participants,
    (fixed_cost_total / min_participants) AS fixed_cost_per_person
FROM tours
WHERE id = ?;
```

### Tính tổng chi phí/người (bao gồm dịch vụ + chi phí cố định)
```sql
SELECT 
    t.id,
    t.name,
    -- Chi phí dịch vụ/người
    COALESCE(SUM(ids.unit_price * ids.quantity), 0) AS service_cost_per_person,
    -- Chi phí cố định/người
    (t.fixed_cost_total / t.min_participants) AS fixed_cost_per_person,
    -- Tổng chi phí/người
    COALESCE(SUM(ids.unit_price * ids.quantity), 0) + (t.fixed_cost_total / t.min_participants) AS total_cost_per_person
FROM tours t
LEFT JOIN itineraries i ON t.id = i.tour_id
LEFT JOIN itinerary_day_services ids ON i.id = ids.itinerary_id AND ids.is_included_in_price = 1
WHERE t.id = ?
GROUP BY t.id;
```

---

## ⚠️ BUSINESS RULES

1. **`fixed_cost_total` >= 0**: Chi phí cố định không được âm
2. **Tự động tính**: Chi phí cố định/người = `fixed_cost_total ÷ min_participants`
3. **Validation**: `min_participants` phải > 0 để tránh chia cho 0

---

## 💡 VÍ DỤ

### Tour trong nước 3 ngày
- `fixed_cost_total` = 2.000.000
- `min_participants` = 15
- Chi phí cố định/người = 2.000.000 ÷ 15 = 133.333 VNĐ/người

### Tour quốc tế 7 ngày
- `fixed_cost_total` = 5.000.000
- `min_participants` = 20
- Chi phí cố định/người = 5.000.000 ÷ 20 = 250.000 VNĐ/người
