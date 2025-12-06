# 📊 BÁO CÁO SO SÁNH: TOUR SCHEDULE MODULE

**Ngày so sánh:** 2024-12-06  
**File Flow:** `FLOW_ANALYSIS_TOUR_SCHEDULE.md`  
**File Code:** `app/controllers/admin/TourScheduleController.php`, `app/models/TourSchedule.php`

---

## 📋 TỔNG QUAN

| Luồng | Mô tả | Trạng thái | Ghi chú |
|-------|------|------------|---------|
| **TOUR-008** | Tạo lịch trình tour mới | ⚠️ **THIẾU/SAI** | Có code nhưng thiếu validation, sai công thức tính end_date |
| **TOUR-010** | Phân công guide cho lịch trình | ❌ **CHƯA CÓ** | Không có method riêng, chỉ có trong edit() |
| **TOUR-012** | Đóng/mở lịch trình | ⚠️ **THIẾU** | Có code nhưng thiếu validation đầy đủ |
| **TOUR-013** | Hủy lịch trình | ❌ **CHƯA CÓ** | Không có method riêng để hủy với xử lý bookings |

---

## 🔍 CHI TIẾT TỪNG LUỒNG

### **LUỒNG 1: TẠO LỊCH TRÌNH TOUR MỚI (TOUR-008)**

#### ✅ **ĐÃ LÀM ĐÚNG:**

1. ✅ **Validation tour status:** Kiểm tra `status = 'active'` (dòng 82-84)
2. ✅ **Validation tour approval:** Kiểm tra `approval_status = 'approved'` (dòng 87-89)
3. ✅ **Validation start_date:** Kiểm tra `start_date >= today` (dòng 92-95)
4. ✅ **Check overlap:** Kiểm tra lịch trùng cho public tour (dòng 121-125)
5. ✅ **Check guide availability:** Kiểm tra guide có trùng lịch không (dòng 139-149)
6. ✅ **Validation quota:** Kiểm tra quota <= max_participants (dòng 112-116)
7. ✅ **Custom tour check:** Kiểm tra custom tour chỉ có 1 schedule (dòng 103-109)
8. ✅ **Giá mặc định:** Dùng giá từ tour nếu schedule không có giá (dòng 157-159)
9. ✅ **Log guide change:** Lưu lịch sử khi gán guide (dòng 167-176)

#### ❌ **SAI:**

1. ❌ **Công thức tính end_date SAI:**
   - **Flow yêu cầu:** `end_date = start_date + duration_days - 1`
   - **Code hiện tại:** `end_date = start_date + duration_days` (dòng 98, 250)
   - **Ví dụ:** Tour 3 ngày 2 đêm, khởi hành 15/12 → Flow: 17/12, Code: 18/12 (SAI 1 ngày)
   - **Cần sửa:** `date('Y-m-d', strtotime($start_date . " + " . ($duration - 1) . " days"))`

#### ⚠️ **THIẾU:**

1. ⚠️ **Validation quota >= min_participants:**
   - **Flow yêu cầu:** `quota >= min_participants` (dòng 62, 86)
   - **Code hiện tại:** CHƯA CÓ validation này
   - **Cần thêm:** Kiểm tra `$quota >= $tour['min_participants']` trong `store()`

2. ⚠️ **Validation end_date:**
   - **Flow yêu cầu:** `end_date - start_date + 1 = duration_days` (dòng 60, 85)
   - **Code hiện tại:** CHƯA CÓ validation này
   - **Cần thêm:** Validate sau khi tính end_date

3. ⚠️ **Status default:**
   - **Flow yêu cầu:** `status = 'open'` (DEFAULT) khi tạo (dòng 76, 92)
   - **Code hiện tại:** Model `create()` không set status, database có DEFAULT nhưng không rõ ràng
   - **Cần thêm:** Đảm bảo `status = 'open'` khi tạo

4. ⚠️ **Booked default:**
   - **Flow yêu cầu:** `booked = 0` (DEFAULT) khi tạo (dòng 64, 87, 93)
   - **Code hiện tại:** Model `create()` không set booked, database có DEFAULT nhưng không rõ ràng
   - **Cần thêm:** Đảm bảo `booked = 0` khi tạo

5. ⚠️ **Filter tour trong create form:**
   - **Flow yêu cầu:** Chỉ hiển thị tour `status = 'active'` AND `approval_status = 'approved'` (dòng 40)
   - **Code hiện tại:** Chỉ filter `status = 'active'` (dòng 50), thiếu `approval_status = 'approved'`
   - **Cần sửa:** Thêm filter `approval_status = 'approved'` trong `create()`

6. ⚠️ **Unique constraint validation:**
   - **Flow yêu cầu:** Unique constraint `(tour_id, start_date, end_date)` (dòng 89)
   - **Code hiện tại:** Chỉ check overlap (start_date, end_date), không check exact match
   - **Cần thêm:** Validate unique constraint rõ ràng hơn

7. ⚠️ **Redirect sau khi tạo:**
   - **Flow yêu cầu:** Redirect về danh sách lịch trình của tour đó `?act=admin&module=tour-schedules&tour_id=X` (dòng 95, 103)
   - **Code hiện tại:** Redirect về `?act=admin&module=schedules` (dòng 179)
   - **Cần sửa:** Redirect về danh sách lịch trình của tour cụ thể

---

### **LUỒNG 2: PHÂN CÔNG GUIDE CHO LỊCH TRÌNH (TOUR-010)**

#### ❌ **CHƯA CÓ (THIẾU HOÀN TOÀN):**

1. ❌ **Không có method riêng để phân công guide:**
   - **Flow yêu cầu:** Có form/modal riêng để phân công guide (dòng 119-173)
   - **Code hiện tại:** Chỉ có trong `edit()`, không có method riêng `assignGuide()` hoặc `assignGuideForm()`
   - **Cần thêm:** 
     - Method `assignGuideForm()` - Hiển thị form phân công
     - Method `assignGuide()` - Xử lý phân công guide

2. ❌ **Thiếu kiểm tra điều kiện phân công:**
   - **Flow yêu cầu:** Chỉ phân công khi `booked >= min_participants` (dòng 127-135)
   - **Code hiện tại:** CHƯA CÓ validation này ở bất kỳ đâu
   - **Cần thêm:** 
     - Kiểm tra `$schedule['booked'] >= $tour['min_participants']` trước khi cho phép phân công
     - Hiển thị cảnh báo nếu chưa đủ số người
     - Disable button "Lưu" nếu chưa đủ số người

3. ❌ **Thiếu tự động set status = 'pending':**
   - **Flow yêu cầu:** Sau khi phân công guide → Tự động set `status = 'pending'` (nếu hiện tại là 'open' hoặc 'closed') (dòng 163)
   - **Code hiện tại:** CHƯA CÓ logic này
   - **Cần thêm:** Sau khi phân công guide thành công, tự động update status

4. ❌ **Thiếu hiển thị thông tin số người:**
   - **Flow yêu cầu:** Hiển thị `min_participants`, `booked`, còn thiếu bao nhiêu (dòng 132-134)
   - **Code hiện tại:** CHƯA CÓ
   - **Cần thêm:** Hiển thị trong form phân công guide

5. ❌ **Thiếu cảnh báo khi chưa đủ số người:**
   - **Flow yêu cầu:** Hiển thị cảnh báo rõ ràng khi `booked < min_participants` (dòng 129-130)
   - **Code hiện tại:** CHƯA CÓ
   - **Cần thêm:** Hiển thị message cảnh báo trong UI

#### ⚠️ **CÓ TRONG EDIT() NHƯNG KHÔNG ĐÚNG FLOW:**

- Code hiện tại cho phép gán guide trong `edit()`, nhưng flow yêu cầu có **form riêng** để phân công guide
- Flow yêu cầu có **button riêng "Phân công guide"** trong danh sách lịch trình (dòng 111, 122)
- Cần tách riêng chức năng phân công guide thành method riêng

---

### **LUỒNG 3: ĐÓNG/MỞ LỊCH TRÌNH (TOUR-012)**

#### ✅ **ĐÃ LÀM ĐÚNG:**

1. ✅ **Có method changeStatus():** (dòng 423-502)
2. ✅ **Validation bookings khi hủy:** Kiểm tra có booking confirmed không (dòng 454-476)
3. ✅ **Cảnh báo khi đóng có booking:** Hiển thị cảnh báo (dòng 479-481, 492-494)

#### ⚠️ **THIẾU:**

1. ⚠️ **Validation status cho phép đóng/mở:**
   - **Flow yêu cầu:** Chỉ cho phép đóng/mở khi `status = 'open'` hoặc `'closed'` (dòng 391-392)
   - **Code hiện tại:** Cho phép đổi sang bất kỳ status nào trong `allowed_statuses` (dòng 443)
   - **Cần thêm:** Validate chỉ cho phép đóng/mở (`open` ↔ `closed`), không cho phép đổi sang `completed` hoặc `cancelled` từ đây

2. ⚠️ **Xác nhận trước khi đóng/mở:**
   - **Flow yêu cầu:** Có form xác nhận với cảnh báo (dòng 185-192)
   - **Code hiện tại:** CHƯA CÓ form xác nhận, chỉ có validation
   - **Cần thêm:** Hiển thị modal/form xác nhận trước khi đóng/mở

3. ⚠️ **Cảnh báo khi đóng có booking:**
   - **Flow yêu cầu:** Cảnh báo rõ ràng: "Lịch trình này sẽ không thể đặt thêm booking mới. Bạn có chắc chắn?" (dòng 188)
   - **Code hiện tại:** Có cảnh báo nhưng không rõ ràng trong UI
   - **Cần cải thiện:** Hiển thị message cảnh báo rõ ràng hơn

---

### **LUỒNG 4: HỦY LỊCH TRÌNH (TOUR-013)**

#### ❌ **CHƯA CÓ (THIẾU HOÀN TOÀN):**

1. ❌ **Không có method riêng để hủy schedule:**
   - **Flow yêu cầu:** Có method riêng để hủy schedule với xử lý bookings (dòng 206-291)
   - **Code hiện tại:** Chỉ có `delete()` (xóa schedule) và validation trong `update()` (không cho hủy nếu có booking)
   - **Cần thêm:** 
     - Method `cancel()` hoặc `cancelSchedule()` - Xử lý hủy schedule
     - Method `cancelForm()` - Hiển thị form hủy với options

2. ❌ **Thiếu xử lý bookings khi hủy:**
   - **Flow yêu cầu:** Có 3 options xử lý bookings (dòng 246-278):
     - Option 1: Tự động hủy bookings & Hoàn tiền 100%
     - Option 2: Chuyển bookings sang schedule khác
     - Option 3: Hủy bookings & Hoàn tiền theo chính sách hủy
   - **Code hiện tại:** CHƯA CÓ xử lý này
   - **Cần thêm:** 
     - Hiển thị danh sách bookings của schedule
     - Cho phép chọn option xử lý
     - Tự động tạo refunds khi hủy bookings

3. ❌ **Thiếu hiển thị thông tin bookings:**
   - **Flow yêu cầu:** Hiển thị danh sách bookings với thông tin chi tiết (dòng 237-244)
   - **Code hiện tại:** CHƯA CÓ
   - **Cần thêm:** Hiển thị trong form hủy schedule

4. ❌ **Thiếu tạo refunds:**
   - **Flow yêu cầu:** Tự động tạo records trong bảng `refunds` khi hủy bookings (dòng 254-258)
   - **Code hiện tại:** CHƯA CÓ
   - **Cần thêm:** Tạo refunds khi hủy schedule

5. ❌ **Thiếu cập nhật quota:**
   - **Flow yêu cầu:** Trả lại quota khi hủy bookings (dòng 259-260)
   - **Code hiện tại:** CHƯA CÓ logic này trong hủy schedule
   - **Cần thêm:** Cập nhật `booked = 0` sau khi hủy tất cả bookings

6. ❌ **Thiếu validation status:**
   - **Flow yêu cầu:** Chỉ cho phép hủy khi `status != 'completed'` (dòng 214, 409)
   - **Code hiện tại:** Có validation trong `update()` nhưng không đầy đủ
   - **Cần thêm:** Validate status trước khi hủy

7. ❌ **Thiếu lý do hủy:**
   - **Flow yêu cầu:** Có field "Lý do hủy" (dòng 230)
   - **Code hiện tại:** CHƯA CÓ
   - **Cần thêm:** Field lý do hủy trong form

8. ❌ **Thiếu chuyển bookings sang schedule khác:**
   - **Flow yêu cầu:** Option 2: Cho phép chọn schedule khác để chuyển bookings (dòng 263-271)
   - **Code hiện tại:** CHƯA CÓ
   - **Cần thêm:** 
     - Hiển thị danh sách schedule khác (cùng tour, status = 'open' hoặc 'pending')
     - Kiểm tra quota của schedule mới
     - Cập nhật `tour_schedule_id` của bookings

#### ⚠️ **CÓ TRONG UPDATE() NHƯNG KHÔNG ĐÚNG FLOW:**

- Code hiện tại có validation trong `update()` không cho hủy nếu có booking (dòng 268-290), nhưng flow yêu cầu có **method riêng** với **xử lý bookings đầy đủ**

---

## 📊 TỔNG KẾT

### **ĐÃ LÀM:**
- ✅ CRUD cơ bản (create, read, update, delete)
- ✅ Validation tour status và approval
- ✅ Check overlap schedule
- ✅ Check guide availability
- ✅ Đóng/mở schedule (cơ bản)
- ✅ Log guide change history

### **SAI CẦN SỬA:**
1. ❌ **Công thức tính end_date:** `+ duration_days` → `+ (duration_days - 1)`
2. ❌ **Redirect sau khi tạo:** Về danh sách lịch trình của tour cụ thể

### **THIẾU CẦN BỔ SUNG:**

#### **LUỒNG 1 (TOUR-008):**
1. ⚠️ Validation `quota >= min_participants`
2. ⚠️ Validation `end_date - start_date + 1 = duration_days`
3. ⚠️ Đảm bảo `status = 'open'` và `booked = 0` khi tạo
4. ⚠️ Filter tour `approval_status = 'approved'` trong create form
5. ⚠️ Unique constraint validation rõ ràng hơn

#### **LUỒNG 2 (TOUR-010):**
1. ❌ **Method riêng phân công guide:** `assignGuideForm()`, `assignGuide()`
2. ❌ **Kiểm tra `booked >= min_participants`** trước khi phân công
3. ❌ **Tự động set `status = 'pending'`** sau khi phân công guide
4. ❌ **Hiển thị thông tin số người** trong form phân công
5. ❌ **Cảnh báo khi chưa đủ số người**

#### **LUỒNG 3 (TOUR-012):**
1. ⚠️ **Validation chỉ cho phép đóng/mở** (`open` ↔ `closed`)
2. ⚠️ **Form xác nhận** trước khi đóng/mở
3. ⚠️ **Cảnh báo rõ ràng hơn** trong UI

#### **LUỒNG 4 (TOUR-013):**
1. ❌ **Method riêng hủy schedule:** `cancelForm()`, `cancel()`
2. ❌ **Xử lý bookings khi hủy:** 3 options (hủy & hoàn tiền 100%, chuyển schedule, hủy theo chính sách)
3. ❌ **Hiển thị danh sách bookings** trong form hủy
4. ❌ **Tạo refunds** khi hủy bookings
5. ❌ **Cập nhật quota** sau khi hủy bookings
6. ❌ **Validation status** trước khi hủy
7. ❌ **Field lý do hủy**
8. ❌ **Chuyển bookings sang schedule khác**

---

## 🎯 ĐỀ XUẤT ƯU TIÊN

### **Ưu tiên CAO (Sai/Thiếu nghiêm trọng):**
1. 🔴 **Sửa công thức tính end_date** (SAI)
2. 🔴 **Thêm validation `quota >= min_participants`** (THIẾU)
3. 🔴 **Thêm method phân công guide riêng** (THIẾU HOÀN TOÀN)
4. 🔴 **Thêm method hủy schedule với xử lý bookings** (THIẾU HOÀN TOÀN)

### **Ưu tiên TRUNG BÌNH:**
1. 🟡 **Validation đầy đủ cho đóng/mở schedule**
2. 🟡 **Tự động set status = 'pending' sau khi phân công guide**
3. 🟡 **Filter tour approval_status trong create form**

### **Ưu tiên THẤP:**
1. 🟢 **Cải thiện UI/UX (cảnh báo, xác nhận)**
2. 🟢 **Unique constraint validation rõ ràng hơn**

---

**Ngày tạo báo cáo:** 2024-12-06

