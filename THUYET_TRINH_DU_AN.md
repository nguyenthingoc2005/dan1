# THUYẾT TRÌNH HỆ THỐNG QUẢN LÝ TOUR DU LỊCH

**Thời lượng: ~4 phút**

---

## 1. TỔNG QUAN HỆ THỐNG

Hệ thống quản lý tour du lịch là một phần mềm web giúp công ty du lịch quản lý toàn bộ quy trình từ khi tạo tour đến khi tour kết thúc. Hệ thống có 2 loại người dùng:

- **Admin (Quản trị viên)**: Quản lý toàn bộ hệ thống, tạo tour, xử lý booking, quản lý khách hàng
- **Guide (Hướng dẫn viên)**: Xem lịch tour, điểm danh khách, ghi nhật ký tour

---

## 2. CÁC CHỨC NĂNG CHÍNH TRONG SIDEBAR

### A. DASHBOARD (Trang chủ)

- Hiển thị tổng quan: số tour, booking, doanh thu, khách hàng
- Thống kê nhanh theo ngày/tuần/tháng

### B. QUẢN LÝ TOUR (Admin)

**Quản lý Tour**: Tạo và quản lý các tour du lịch

- Tạo tour mới với thông tin: tên tour, điểm đến, lịch trình, giá
- Quản lý dịch vụ: khách sạn, nhà hàng, phương tiện
- Thiết lập chính sách hủy, điều khoản

**Lịch Khởi Hành**: Tạo lịch cho từng tour

- Chọn ngày khởi hành, số chỗ trống
- Xem các tour sắp khởi hành

**Tour Đã Chốt**: Quản lý tour đã có đủ khách

- Gán hướng dẫn viên cho tour
- Gán xe, tài xế
- Phân phòng khách sạn cho khách

### C. QUẢN LÝ BOOKING (Admin)

**Quản lý Đặt Tour**: Xử lý đặt tour của khách

- Tạo booking mới
- Thêm danh sách khách vào booking (có thể import từ Excel)
- Xem chi tiết booking, thanh toán

**Chính sách Hủy**: Quản lý các chính sách hủy tour

- Tạo quy định về phí hủy theo thời gian

**Hủy Booking**: Xử lý các booking bị hủy

- Xem danh sách booking đã hủy
- Tính toán và hoàn tiền

### D. KHÁCH HÀNG

- Quản lý thông tin khách hàng
- Thêm, sửa, xóa khách
- Import khách hàng từ file Excel

### E. QUẢN LÝ TÀI CHÍNH

**Thanh toán**: Quản lý các khoản thanh toán

- Xem lịch sử thanh toán
- Ghi nhận thanh toán từ khách

**Mã giảm giá**: Tạo và quản lý mã giảm giá

- Tạo mã giảm giá cho khách
- Áp dụng mã khi đặt tour

**Chi phí phát sinh**: Quản lý chi phí trong tour

- Hướng dẫn viên ghi nhận chi phí
- Admin duyệt chi phí

### F. NHẬT KÝ TOUR

- Xem nhật ký tour do hướng dẫn viên viết
- Theo dõi diễn biến tour

### G. BÁO CÁO

- Báo cáo doanh thu
- Báo cáo số lượng booking
- Thống kê tour

### H. NHÂN VIÊN

- Quản lý tài khoản hướng dẫn viên
- Phân quyền cho từng người

### I. CẤU HÌNH HỆ THỐNG

**Địa điểm & Dịch vụ**: Quản lý điểm đến và dịch vụ

- Thêm điểm du lịch (tỉnh/thành phố)
- Quản lý nhà cung cấp dịch vụ (khách sạn, nhà hàng)
- Thiết lập giá dịch vụ

**Loại dịch vụ**: Phân loại dịch vụ (ăn uống, nghỉ dưỡng, vận chuyển...)

**Chính sách**: Quản lý các chính sách chung

**Quản lý Xe**: Thêm, sửa thông tin xe

**Quản lý Tài xế**: Quản lý thông tin tài xế

---

## 3. LUỒNG HOẠT ĐỘNG HOÀN CHỈNH

### Bước 1: TẠO TOUR (Admin)

- Admin tạo tour mới
- Nhập thông tin: tên tour, điểm đến, số ngày, giá
- Thiết lập lịch trình chi tiết từng ngày
- Thêm các dịch vụ: khách sạn, nhà hàng, phương tiện
- Lưu tour

### Bước 2: TẠO LỊCH KHỞI HÀNH (Admin)

- Chọn tour vừa tạo
- Tạo lịch khởi hành: chọn ngày bắt đầu, số chỗ
- Tour xuất hiện trong danh sách lịch khởi hành

### Bước 3: TẠO BOOKING (Admin)

- Khách hàng muốn đặt tour → Admin tạo booking
- Chọn tour và lịch khởi hành
- Thêm danh sách khách (có thể import từ Excel)
- Tính tổng tiền, áp dụng mã giảm giá nếu có
- Ghi nhận thanh toán (có thể thanh toán nhiều lần)

### Bước 4: CHỐT TOUR VÀ PHÂN CÔNG (Admin)

- Khi đủ số lượng khách, tour được chốt
- **Gán Hướng dẫn viên**: Chọn hướng dẫn viên cho tour (hệ thống kiểm tra lịch trùng)
- **Gán Xe và Tài xế**: Chọn xe và tài xế phù hợp
- **Phân phòng**: Hệ thống tự động hoặc thủ công phân phòng khách sạn cho khách

### Bước 5: HƯỚNG DẪN VIÊN THỰC HIỆN TOUR

- Hướng dẫn viên đăng nhập vào hệ thống
- Xem lịch tour được phân công
- **Điểm danh/Check-in**:
  - Điểm danh khách khi khởi hành
  - Check-in tại các điểm tham quan (checkpoint)
  - Ghi nhận khách có mặt hay vắng mặt
- **Ghi Nhật ký Tour**: Ghi lại diễn biến tour hàng ngày
- **Ghi Chi phí phát sinh**: Ghi các chi phí phát sinh trong tour (Admin sẽ duyệt sau)

### Bước 6: KẾT THÚC TOUR

- Tour kết thúc, hướng dẫn viên hoàn tất nhật ký
- Admin xem báo cáo, duyệt chi phí
- Hệ thống lưu lại toàn bộ thông tin để tham khảo sau này

---

## 4. ĐIỂM NỔI BẬT

✅ **Quản lý toàn diện**: Từ tạo tour đến kết thúc tour, tất cả đều được quản lý trên hệ thống

✅ **Phân quyền rõ ràng**: Mỗi vai trò có quyền hạn riêng, đảm bảo an toàn dữ liệu

✅ **Tự động hóa**:

- Tự động tính giá tour
- Tự động kiểm tra lịch trùng khi gán hướng dẫn viên
- Tự động phân phòng khách sạn

✅ **Dễ sử dụng**: Giao diện trực quan, dễ thao tác

✅ **Báo cáo đầy đủ**: Có đầy đủ báo cáo doanh thu, booking để quản lý

---

## 5. KẾT LUẬN

Hệ thống quản lý tour du lịch này giúp công ty du lịch:

- Quản lý tour một cách chuyên nghiệp và hiệu quả
- Giảm thiểu sai sót trong quá trình vận hành
- Theo dõi được toàn bộ quy trình từ đầu đến cuối
- Tăng năng suất làm việc

**Hệ thống đã hoàn thiện đầy đủ các chức năng cần thiết cho một công ty du lịch vận hành tour.**

---

_Tài liệu này được tạo để thuyết trình về hệ thống quản lý tour du lịch_
