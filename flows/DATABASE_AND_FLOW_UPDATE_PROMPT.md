# 📋 PROMPT: CẬP NHẬT FLOWS THEO DATABASE MỚI

## 🎯 MỤC ĐÍCH

Dự án đã có một số thay đổi quan trọng về database schema và luồng nghiệp vụ. Cần cập nhật lại tất cả các file flows trong thư mục `flows/` để phù hợp với:

1. **Database schema mới** (`setup_database_complete.sql`)
2. **Luồng nghiệp vụ đã được phân tích và cải thiện**

---

## 📊 TÓM TẮT THAY ĐỔI DATABASE

### **1. CLEANUP (Đã bỏ):**

- ❌ **Bảng `categories`** - Không còn dùng nữa
- ❌ **Bảng `tour_journals`** - Chỉ dùng `journals` (link với `booking_id`)
- ❌ **Bảng `suppliers`** - Không còn khái niệm nhà cung cấp lớn
- ❌ **Trường `countries.type`** - Không phân loại domestic/international
- ❌ **Trường `destinations.category_id`** - Không phân loại destination theo category
- ❌ **Trường `tours.category_id`** - Dùng `tour_type` (public/custom) thay thế
- ❌ **Trường `tours.price_based_on_pax`** - Không cần nữa
- ❌ **Trường `services.supplier_id`** - Bỏ, chỉ dùng `service_provider_id`
- ❌ **Trường `service_providers.supplier_id`** - Bỏ

### **2. CHANGES (Đã thay đổi):**

- ✅ **`supplier_payments`** → `service_provider_payments`
- ✅ **`supplier_payment_details`** → `service_provider_payment_details`
- ✅ **`booking_services.supplier_id`** → `service_provider_id`

### **3. ADDITIONS (Đã thêm mới):**

- ✅ **`bookings.tour_schedule_id`** - Link booking với tour schedule cụ thể
- ✅ **`tours.fixed_cost_guide`** - Chi phí lương HDV cố định (không theo người)
- ✅ **`tours.fixed_cost_management`** - Chi phí quản lý cố định
- ✅ **`tours.fixed_cost_marketing`** - Chi phí marketing cố định
- ✅ **`tours.fixed_cost_other`** - Chi phí khác cố định
- ✅ **`tours.booking_deadline_days`** - Số ngày tối thiểu trước ngày khởi hành để đặt booking (default: 1 ngày)
- ✅ **Bảng `itinerary_timelines`** - Timeline chi tiết cho từng ngày của tour (giờ, địa điểm, hoạt động)
- ✅ **Bảng `itinerary_day_services`** - Dịch vụ theo từng ngày để tính chi phí chính xác
- ✅ **`tour_schedules.status`**: Thêm `'pending'`, `'in_progress'` (nâng cấp từ 4 status lên 6 status)

### **4. DEPRECATED (Giữ lại nhưng không dùng):**

- ⚠️ **`tours.markup_percentage`** - Default = 0.00, không dùng nữa (giữ lại để backward compatible)

---

## 🔄 THAY ĐỔI LUỒNG NGHIỆP VỤ

### **MODULE 1: LOCATION SERVICES**

**Thay đổi chính:**

1. **Bỏ Categories:**

   - Không còn filter destinations theo category
   - Không còn filter countries theo type (domestic/international)
   - Hiển thị tất cả countries active

2. **Bỏ Suppliers:**

   - Không còn tạo/select Supplier khi tạo Service Provider
   - Service Provider độc lập, chỉ link với Province và Country

3. **Destinations:**

   - Thêm `province_id` và `country_id` vào `destinations`
   - Hiển thị destinations theo tab trong Province view
   - Tab 1: Service Providers
   - Tab 2: Destinations

4. **Service Provider:**
   - Auto-generate `service_code` (format: SP-YYYYMMDD-XXX)
   - Không cần chọn Supplier
   - Chỉ cần chọn Province và Country

### **MODULE 2: TOUR**

**Thay đổi chính:**

1. **Tour Creation Wizard (6 steps):**

   - Step 1: Thông tin chung
   - Step 2: Lịch trình (Itinerary) - **MỚI: Có 3 tabs**
     - Tab 1: Tổng quan từng ngày
     - Tab 2: Timeline chi tiết (itinerary_timelines)
     - Tab 3: Dịch vụ theo ngày (itinerary_day_services)
   - Step 3: Bao gồm/Không bao gồm
   - Step 4: Chính sách (Policies)
   - Step 5: Hình ảnh
   - Step 6: Giá & Lưu

2. **Pricing Formula (MỚI):**

   - **Chi phí dịch vụ/người** = SUM(`itinerary_day_services.unit_price * quantity`) WHERE `is_included_in_price = 1`
   - **Chi phí cố định/người** = (`fixed_cost_guide` + `fixed_cost_management` + `fixed_cost_marketing` + `fixed_cost_other`) / `min_participants`
   - **`adult_price`** = Chi phí dịch vụ/người + Chi phí cố định/người
   - **Bỏ `markup_percentage`** - Không dùng nữa
   - **Bỏ `price_based_on_pax`** - Dùng `min_participants` thay thế

3. **Itinerary Timeline:**

   - Mỗi ngày có thể có nhiều timeline items (theo giờ)
   - Mỗi timeline item có: giờ, hoạt động, địa điểm, service_provider, service
   - Timeline type: meal, accommodation, activity, transport

4. **Itinerary Day Services:**
   - Gán dịch vụ cụ thể vào từng ngày
   - Tính chi phí chính xác dựa trên dịch vụ thực tế sử dụng
   - Mỗi service có: service_id, service_provider_id, unit_price, quantity, is_included_in_price

### **MODULE 3: TOUR SCHEDULE**

**Thay đổi chính:**

1. **Status mới (6 status):**

   - `open` - Mở đặt (chưa có guide)
   - `closed` - Đóng đặt (đủ người hoặc hủy)
   - `pending` - Đã đủ người, chờ phân công guide
   - `in_progress` - Đang trong chuyến đi
   - `completed` - Đã hoàn thành
   - `cancelled` - Đã hủy

2. **Guide Assignment Logic:**

   - Chỉ có thể phân công guide khi `booked >= min_participants`
   - Khi phân công guide → status tự động chuyển thành `pending`
   - Khi `start_date` đến và có guide → status tự động chuyển thành `in_progress`

3. **Cancellation Handling:**
   - Nếu hủy schedule → xử lý refund cho các booking đã đặt
   - Options: 100% refund, transfer sang schedule khác, hoặc refund theo policy

### **MODULE 4: BOOKING**

**Thay đổi chính:**

1. **Booking Deadline:**

   - **Quy tắc:** Phải đặt trước 1 ngày so với ngày khởi hành
   - Validation: `start_date >= ngày hiện tại + 1 ngày`
   - Configurable: `tours.booking_deadline_days` (default: 1)

2. **Check-in Conditions:**

   - Booking phải `approved` (approval_status = 'approved')
   - Booking phải **thanh toán đủ** (payment_status = 'paid' AND remaining_amount = 0)
   - Ngày check-in phải = ngày khởi hành (start_date = current_date)

3. **Tour Schedule Link:**
   - Booking link trực tiếp với `tour_schedule_id`
   - Khi chọn tour → Hiển thị danh sách schedules của tour đó
   - Chỉ cho phép chọn schedule có `status = 'open'` hoặc `'pending'`

---

## 📝 YÊU CẦU CẬP NHẬT

### **File cần cập nhật:**

1. ✅ **FLOW_ANALYSIS_LOCATION_SERVICES.md**

   - Bỏ tất cả references đến `categories`
   - Bỏ tất cả references đến `suppliers`
   - Cập nhật flow tạo Service Provider (không cần Supplier)
   - Cập nhật flow tạo Destination (có province_id, country_id)

2. ✅ **FLOW_ANALYSIS_TOUR.md**

   - Cập nhật Tour Creation Wizard (6 steps thay vì 7)
   - Thêm mô tả chi tiết về `itinerary_timelines` và `itinerary_day_services`
   - Cập nhật pricing formula (bỏ markup, bỏ price_based_on_pax)
   - Thêm fixed_cost fields vào Step 6

3. ✅ **FLOW_ANALYSIS_TOUR_SCHEDULE.md**

   - Cập nhật status enum (6 status)
   - Cập nhật guide assignment logic (chỉ khi booked >= min_participants)
   - Thêm mô tả về status transitions

4. ✅ **FLOW_ANALYSIS_BOOKING.md**

   - Thêm `tour_schedule_id` vào booking creation flow
   - Cập nhật booking deadline rule (1 ngày)
   - Cập nhật check-in conditions (phải paid)

5. ✅ **FLOW_ANALYSIS_CUSTOMER_STAFF.md**

   - Kiểm tra và cập nhật nếu có references đến các bảng đã bỏ

6. ✅ **TOUR_PRICING_FORMULA.md**
   - Cập nhật công thức tính giá (bỏ markup, thêm fixed_cost)

---

## ✅ CHECKLIST KHI CẬP NHẬT

Khi cập nhật mỗi file flow, cần kiểm tra:

- [ ] Đã bỏ tất cả references đến `categories`?
- [ ] Đã bỏ tất cả references đến `suppliers`?
- [ ] Đã bỏ references đến `price_based_on_pax`?
- [ ] Đã bỏ references đến `markup_percentage` (trừ phần DEPRECATED)?
- [ ] Đã thêm `itinerary_timelines` và `itinerary_day_services` vào Tour flow?
- [ ] Đã cập nhật pricing formula?
- [ ] Đã cập nhật tour_schedules.status (6 status)?
- [ ] Đã thêm `bookings.tour_schedule_id`?
- [ ] Đã cập nhật booking deadline rule?
- [ ] Đã cập nhật check-in conditions?

---

## 📌 LƯU Ý

1. **Giữ nguyên cấu trúc file** - Chỉ cập nhật nội dung, không thay đổi format
2. **Giữ nguyên các ví dụ** - Nếu có ví dụ, cập nhật cho phù hợp với schema mới
3. **Giữ nguyên validation rules** - Chỉ cập nhật nếu cần thiết
4. **Cập nhật tất cả bảng mô tả fields** - Đảm bảo khớp với database mới

---

## 🎯 KẾT QUẢ MONG MUỐN

Sau khi cập nhật, tất cả các file flows phải:

- ✅ Khớp 100% với database schema mới
- ✅ Phản ánh đúng luồng nghiệp vụ mới
- ✅ Không còn references đến các bảng/trường đã bỏ
- ✅ Có đầy đủ mô tả về các bảng/trường mới
