# KẾ HOẠCH TRIỂN KHAI: CHI PHÍ CỐ ĐỊNH (FIXED COST) - ĐƠN GIẢN HÓA

## 📋 TỔNG QUAN

**Mục tiêu:** Chuyển đổi từ 4 cột chi phí cố định riêng biệt (`fixed_cost_guide`, `fixed_cost_management`, `fixed_cost_marketing`, `fixed_cost_other`) sang 1 cột tổng (`fixed_cost_total`) - nhập trực tiếp, không cần template.

**Flow mới:**

- Nhập trực tiếp `fixed_cost_total` khi tạo/sửa tour
- Tự động tính chi phí cố định/người = `fixed_cost_total ÷ min_participants`
- Đơn giản, không cần quản lý template

---

## 🔍 PHÂN TÍCH SO SÁNH

### Logic hiện tại:

1. **Database:**

   - `tours` có 4 cột: `fixed_cost_guide`, `fixed_cost_management`, `fixed_cost_marketing`, `fixed_cost_other`
   - Không có cột `fixed_cost_total`

2. **Controller:**

   - `TourController::store()` lấy 4 giá trị từ form
   - Tính tổng = guide + management + marketing + other

3. **View:**

   - Form có 4 input riêng cho từng loại chi phí
   - Không có input `fixed_cost_total`

4. **PricingHelper:**
   - `calculateFixedCostPerPerson()` nhận array 4 phần tử
   - Tính tổng từ 4 giá trị

### Logic mới (đơn giản hóa):

1. **Database:**

   - `tours` có: `fixed_cost_total` (DECIMAL)
   - Bỏ 4 cột cũ (hoặc giữ lại để backward compatible)

2. **Controller:**

   - `TourController::store()` nhận `fixed_cost_total` từ form
   - Lưu trực tiếp vào database

3. **View:**

   - 1 input `fixed_cost_total` (thay thế 4 input cũ)
   - Hiển thị chi phí cố định/người = `fixed_cost_total ÷ min_participants`

4. **PricingHelper:**
   - Sửa để nhận `fixed_cost_total` (float) thay vì array 4 phần tử
   - Tính: `fixed_cost_total / min_participants`

---

## 📝 KẾ HOẠCH TRIỂN KHAI

### PHASE 1: DATABASE (Ưu tiên cao)

#### 1.1. Kiểm tra database schema

- [x] Kiểm tra cột `tours.fixed_cost_total` đã có chưa (theo `database/03_tour.sql`)
- [x] Đã sửa `database/03_tour.sql` - bỏ `tour_cost_templates`, bỏ `tour_cost_template_id`, `use_template_cost`
- [ ] Nếu database chưa có cột `fixed_cost_total` → chạy migration:
  ```sql
  ALTER TABLE tours
  ADD COLUMN fixed_cost_total DECIMAL(15,2) DEFAULT 0.00
  COMMENT 'Tổng chi phí cố định (nhập trực tiếp)'
  AFTER deposit_percentage;
  ```

#### 1.2. Migration dữ liệu (nếu cần)

- [ ] Script migration: tính tổng từ 4 cột cũ → gán vào `fixed_cost_total`
  ```sql
  UPDATE tours
  SET fixed_cost_total = COALESCE(fixed_cost_guide, 0) +
                         COALESCE(fixed_cost_management, 0) +
                         COALESCE(fixed_cost_marketing, 0) +
                         COALESCE(fixed_cost_other, 0)
  WHERE fixed_cost_total = 0 OR fixed_cost_total IS NULL;
  ```

#### 1.3. Xóa cột cũ (tùy chọn - sau khi test xong)

- [ ] Giữ lại 4 cột cũ để backward compatible (khuyến nghị)
- [ ] Hoặc xóa sau khi đã test kỹ:
  ```sql
  ALTER TABLE tours
  DROP COLUMN fixed_cost_guide,
  DROP COLUMN fixed_cost_management,
  DROP COLUMN fixed_cost_marketing,
  DROP COLUMN fixed_cost_other;
  ```

---

### PHASE 2: CONTROLLER (Ưu tiên cao)

#### 2.1. Sửa TourController

- [ ] `store()`:

  - Bỏ logic lấy 4 cột `fixed_cost_*` cũ
  - Nhận `fixed_cost_total` từ form
  - Lưu trực tiếp vào database

  ```php
  'fixed_cost_total' => (float) ($form_data['fixed_cost_total'] ?? 0)
  ```

- [ ] `update()`: Tương tự `store()`

- [ ] `createFromTemplate()`:
  - Tính tổng từ 4 cột cũ của template (nếu có)
  - Hoặc lấy `fixed_cost_total` nếu template đã có

---

### PHASE 3: VIEW (Ưu tiên cao)

#### 3.1. Sửa view tạo tour (`app/views/admin/tours/create.php`)

- [ ] **Step 6 (Giá & Lưu):**

  - Thay 4 input (`fixed_cost_guide`, `fixed_cost_management`, `fixed_cost_marketing`, `fixed_cost_other`)
  - Bằng 1 input `fixed_cost_total`
  - Hiển thị chi phí cố định/người = `fixed_cost_total ÷ min_participants` (tự động cập nhật)

- [ ] JavaScript:
  - Update function `updatePricing()` để dùng `fixed_cost_total` thay vì tổng 4 cột
  - Tính toán: `fixed_cost_per_person = fixed_cost_total / min_participants`

#### 3.2. Sửa view sửa tour (`app/views/admin/tours/edit.php`)

- [ ] Tương tự như `create.php`

#### 3.3. Sửa view chi tiết tour (`app/views/admin/tours/show.php`)

- [ ] Hiển thị `fixed_cost_total` thay vì 4 cột riêng
- [ ] Hiển thị chi phí cố định/người = `fixed_cost_total ÷ min_participants`

---

### PHASE 4: PRICING HELPER (Ưu tiên cao)

#### 4.1. Sửa PricingHelper

- [ ] Sửa `calculateFixedCostPerPerson()`:
  - **Cũ:** Nhận array `['guide' => float, 'management' => float, ...]`
  - **Mới:** Nhận `fixed_cost_total` (float) và `min_participants` (int)
  - Return: `fixed_cost_total / min_participants`
  ```php
  function calculateFixedCostPerPerson($fixed_cost_total, $min_participants)
  {
      if ($min_participants <= 0) {
          return 0;
      }
      return (float) $fixed_cost_total / $min_participants;
  }
  ```

#### 4.2. Sửa `calculateTotalCostPerPerson()`

- [ ] Thay đổi cách gọi:
  - **Cũ:** `calculateTotalCostPerPerson($pdo, $tour_id, $fixed_costs, $min_participants)`
  - **Mới:** `calculateTotalCostPerPerson($pdo, $tour_id, $fixed_cost_total, $min_participants)`

#### 4.3. Sửa các hàm gọi PricingHelper

- [ ] `TourController::store()`:
  ```php
  $estimated_cost = calculateTotalCostPerPerson(
      $this->db,
      $tour_id,
      $fixed_cost_total,  // Thay vì $fixed_costs array
      $min_participants
  );
  ```
- [ ] `TourController::update()`: Tương tự
- [ ] Các nơi khác sử dụng PricingHelper (nếu có)

---

### PHASE 5: MODEL (Ưu tiên trung bình)

#### 5.1. Sửa Tour Model

- [ ] `create()`: Lưu `fixed_cost_total` thay vì 4 cột cũ
- [ ] `update()`: Tương tự
- [ ] `findById()`: Đảm bảo trả về `fixed_cost_total`

---

### PHASE 6: TESTING & VALIDATION (Ưu tiên cao)

#### 6.1. Test tạo tour

- [ ] Tạo tour mới, nhập `fixed_cost_total` → kiểm tra lưu đúng
- [ ] Kiểm tra tính toán chi phí cố định/người
- [ ] Kiểm tra hiển thị trên form

#### 6.2. Test sửa tour

- [ ] Sửa tour, chỉnh sửa `fixed_cost_total` → kiểm tra cập nhật đúng
- [ ] Kiểm tra tính toán lại chi phí cố định/người

#### 6.3. Test tính toán giá

- [ ] Kiểm tra `calculateTotalCostPerPerson()` trả về đúng
- [ ] Kiểm tra hiển thị giá trên form tạo tour
- [ ] Kiểm tra hiển thị giá trên view chi tiết tour

#### 6.4. Test migration dữ liệu

- [ ] Kiểm tra dữ liệu cũ đã được migrate đúng (nếu có)

---

### PHASE 7: CLEANUP (Ưu tiên thấp)

#### 7.1. Xóa code cũ (sau khi test xong)

- [ ] Xóa 4 cột `fixed_cost_*` khỏi database (hoặc giữ lại để backward compatible)
- [ ] Xóa code liên quan đến 4 cột cũ trong Controller/Model/View

---

## 📊 CHECKLIST TỔNG QUAN

### Database

- [x] Cột `tours.fixed_cost_total` đã có trong schema
- [ ] Migration dữ liệu từ 4 cột cũ (nếu cần)
- [ ] Xóa 4 cột cũ (tùy chọn)

### Backend

- [ ] `TourController` đã sửa (create, store, update)
- [ ] `Tour Model` đã sửa (create, update)
- [ ] `PricingHelper` đã sửa

### Frontend

- [ ] View tạo tour đã sửa (1 input fixed_cost_total)
- [ ] View sửa tour đã sửa
- [ ] View chi tiết tour đã sửa
- [ ] JavaScript tính toán chi phí cố định/người

### Testing

- [ ] Test tạo tour
- [ ] Test sửa tour
- [ ] Test tính toán giá
- [ ] Test migration dữ liệu

---

## ⚠️ LƯU Ý QUAN TRỌNG

1. **Backward Compatibility:**

   - Giữ lại 4 cột cũ trong database để không ảnh hưởng đến dữ liệu hiện có
   - Có thể migration dữ liệu: `fixed_cost_total = fixed_cost_guide + fixed_cost_management + fixed_cost_marketing + fixed_cost_other`

2. **Validation:**

   - `fixed_cost_total` phải >= 0
   - `min_participants` phải > 0 để tránh chia cho 0

3. **Tính toán:**
   - Chi phí cố định/người = `fixed_cost_total ÷ min_participants`
   - Tự động cập nhật khi thay đổi `fixed_cost_total` hoặc `min_participants`

---

## 🎯 THỨ TỰ THỰC HIỆN

1. **Bước 1:** Kiểm tra và migration database (nếu cần)
2. **Bước 2:** Sửa PricingHelper
3. **Bước 3:** Sửa TourController
4. **Bước 4:** Sửa Tour Model
5. **Bước 5:** Sửa view tạo/sửa tour
6. **Bước 6:** Test toàn bộ flow
7. **Bước 7:** Cleanup (nếu cần)

---

## 📚 TÀI LIỆU THAM KHẢO

- Flow: `flows/FLOW_TOUR_COST_TEMPLATE.md` (đã cập nhật)
- Database schema: `database/03_tour.sql` (đã cập nhật)
