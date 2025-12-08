# Nâng cấp Báo cáo Doanh thu - Tóm tắt

## Tổng quan
Đã nâng cấp module báo cáo doanh thu tại `http://localhost/duan1/?act=admin&module=reports&action=revenue` với các tính năng:

## Các tính năng đã triển khai

### 1. Metrics & Thống kê chi tiết
- ✅ **Tổng doanh thu**: Tính từ payments đã hoàn thành (completed)
- ✅ **Tổng chi phí**: Tính từ service_provider_payments
- ✅ **Lợi nhuận**: Doanh thu - Chi phí
- ✅ **Tổng số booking** và **tổng số khách hàng**

### 2. Bộ lọc nâng cao
- ✅ **Lọc theo khoảng thời gian**: Từ ngày - Đến ngày
- ✅ **Lọc theo Tour**: Dropdown chọn tour cụ thể
- ✅ **Lọc theo Khách hàng**: Dropdown chọn khách hàng
- ✅ **Lọc theo Phương thức thanh toán**: Tiền mặt, Chuyển khoản, Thẻ tín dụng, Khác
- ✅ **Lọc theo Trạng thái booking**: Đã thanh toán, Thanh toán một phần, Chưa thanh toán
- ✅ **Lọc theo Nguồn booking**: Điện thoại, Email, Facebook, Zalo, Tại quầy

### 3. Biểu đồ trực quan
- ✅ **Biểu đồ Doanh thu theo tháng**: Line chart sử dụng Chart.js
- ✅ **Biểu đồ Doanh thu theo phương thức thanh toán**: Doughnut chart

### 4. Bảng thống kê
- ✅ **Top 20 Tours có doanh thu cao nhất**: Bảng hiển thị mã tour, tên tour, số booking, doanh thu
- ✅ **Top 10 Khách hàng có doanh thu cao nhất**: Bảng hiển thị mã KH, tên KH, số booking, doanh thu

### 5. Xuất Excel
- ✅ **Xuất báo cáo ra file Excel (CSV)**: 
  - Tải về file CSV với tên `BaoCaoDoanhThu_YYYY-MM-DD_HHMMSS.csv`
  - Bao gồm tất cả các payments đã lọc
  - Cột: STT, Ngày thanh toán, Mã booking, Tour, Khách hàng, SĐT, Số tiền, Phương thức, Loại thanh toán, Mã giao dịch, Số hóa đơn, Trạng thái booking
  - Tổng kết tổng doanh thu ở cuối file
  - Hỗ trợ UTF-8 với BOM để mở đúng trong Excel

## Các file đã chỉnh sửa

### 1. `app/controllers/admin/ReportController.php`
- Nâng cấp method `revenue()` với:
  - 11 queries thống kê chi tiết
  - Xử lý bộ lọc nâng cao
  - Tính toán doanh thu, chi phí, lợi nhuận
- Thêm method `exportRevenueToExcel()` để xuất Excel
- Thêm helper methods: `getToursForDropdown()`, `getCustomersForDropdown()`

### 2. `app/views/admin/reports/revenue.php`
- UI/UX hoàn toàn mới với:
  - Header có nút xuất Excel
  - Bộ lọc nâng cao có thể ẩn/hiện
  - 4 cards metrics chính
  - 2 biểu đồ tương tác
  - 2 bảng thống kê top tours và customers
- Tích hợp Chart.js để hiển thị biểu đồ
- JavaScript để toggle bộ lọc nâng cao

## Cách sử dụng

### Xem báo cáo
1. Truy cập: `?act=admin&module=reports&action=revenue`
2. Chọn khoảng thời gian (mặc định: tháng hiện tại)
3. Click "Hiện thêm" để mở bộ lọc nâng cao
4. Áp dụng các bộ lọc cần thiết
5. Click "Xem báo cáo"

### Xuất Excel
1. Sau khi áp dụng các bộ lọc, click nút "Xuất Excel"
2. File CSV sẽ được tải về tự động
3. Mở file bằng Excel hoặc Google Sheets

## Các metrics được tính toán

### Doanh thu
- Từ bảng `payments` với điều kiện:
  - `status = 'completed'`
  - `payment_type != 'refund'`
  - Trong khoảng thời gian đã chọn

### Chi phí
- Từ bảng `service_provider_payments` với điều kiện:
  - `status = 'completed'`
  - Trong khoảng thời gian đã chọn
  - (Nếu lọc theo tour: chỉ tính booking của tour đó)

### Lợi nhuận
- `Lợi nhuận = Doanh thu - Chi phí`

## Công nghệ sử dụng
- **Backend**: PHP, PDO, MySQL
- **Frontend**: HTML5, CSS (Tailwind CSS), JavaScript
- **Charts**: Chart.js 3.9.1 (CDN)
- **Icons**: Lucide Icons
- **Excel Export**: CSV format với UTF-8 BOM

## Lưu ý
- File Excel thực chất là CSV format (compatible với Excel)
- Biểu đồ yêu cầu kết nối internet để load Chart.js từ CDN
- Tất cả dữ liệu được format tiền tệ theo chuẩn Việt Nam (VNĐ)
- Bộ lọc nâng cao mặc định ẩn để giao diện gọn gàng hơn

## Tương lai có thể mở rộng
- [ ] Thêm biểu đồ so sánh theo kỳ (tháng trước, năm trước)
- [ ] Thêm bộ lọc theo hướng dẫn viên (guide)
- [ ] Thêm xuất PDF
- [ ] Thêm dashboard tổng quan với nhiều metrics hơn
- [ ] Thêm báo cáo theo từng tour schedule cụ thể

