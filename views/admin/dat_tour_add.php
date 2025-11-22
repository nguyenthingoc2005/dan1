<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo Đơn Đặt Tour Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .container { max-width: 700px; margin-top: 20px; }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-calendar-plus"></i> Tạo Đơn Đặt Tour Mới</h4>
        </div>
        <div class="card-body">
            <form action="<?= BASEURL ?>?act=dat_tour_save" method="POST">
                
                <h5 class="mb-3 text-primary">Thông tin Khởi tạo</h5>
                <div class="row g-3">
                    
                    <div class="col-md-12">
                        <label for="khach_hang_id" class="form-label fw-bold">1. Khách Hàng Đặt Tour</label>
                        <select class="form-select" id="khach_hang_id" name="khach_hang_id" required>
                            <option value="">-- Chọn Khách Hàng --</option>
                            <?php 
                            // Giả định $allKhachHang là mảng kết quả từ hàm getAllKhachHang()
                            if (isset($data) && is_array($data)):
                                foreach ($data as $kh): 
                            ?>
                                <option value="<?= htmlspecialchars($kh['khach_hang_id']) ?>">
                                    <?= htmlspecialchars($kh['ho_ten']) ?> (<?= htmlspecialchars($kh['so_dien_thoai']) ?>)
                                </option>
                            <?php 
                                endforeach;
                            endif; 
                            ?>
                        </select>
                        <small class="form-text text-muted">Chỉ khách hàng đang hoạt động mới hiển thị.</small>
                    </div>

                    <div class="col-md-6">
                        <label for="so_nguoi" class="form-label fw-bold">2. Số Lượng Người</label>
                        <input type="number" class="form-control" id="so_nguoi" name="so_nguoi" min="1" required>
                    </div>

                    <div class="col-md-6">
                        <label for="loai" class="form-label fw-bold">3. Loại Đặt Tour</label>
                        <select class="form-select" id="loai" name="loai" required >
                            <option value="individual" selected>Cá nhân (Individual)</option>
                            <option value="group">Nhóm (Group)</option>
                        </select>
                    </div>
                </div>
                
                <hr class="my-4">

                <h5 class="mb-3 text-primary">Trạng thái Khởi tạo và Nguồn</h5>
                <div class="row g-3">
                    
                    <div class="col-md-6">
                        <label for="trang_thai" class="form-label fw-bold">4. Trạng Thái Đặt Tour</label>
                        <select class="form-select" id="trang_thai" name="trang_thai" required>
                            <option value="chờ xác nhận" selected>Chờ Xác Nhận</option>
                            <option value="đã đặt cọc">Đã Đặt Cọc</option>
                            <option value="hoàn tất">Hoàn Tất Thanh Toán</option>
                            <option value="hủy">Hủy</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="nguon" class="form-label fw-bold">5. Nguồn Đặt Tour</label>
                        <select class="form-select" id="nguon" name="nguon" required>
                            <option value="web">Web (Online)</option>
                            <option value="phone">Phone (Điện thoại)</option>
                            <option value="agency">Agency (Đại lý)</option>
                            <option value="walkin">Walkin (Tại quầy)</option>
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <label for="ghi_chu" class="form-label">Ghi Chú</label>
                        <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="3"></textarea>
                    </div>

                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="<?= BASEURL ?>?act=dat_tour_list" class="btn btn-secondary me-2">
                        <i class="bi bi-x-circle"></i> Hủy
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Lưu & Tiếp tục Thêm Hành Khách
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const soNguoiInput = document.getElementById('so_nguoi');
        const loaiSelect = document.getElementById('loai');
        const form = document.querySelector('form');

        function updateLoaiDatTour() {
            const soNguoi = parseInt(soNguoiInput.value); 
            
            // 1. Logic tự động thiết lập loại và khóa/mở khóa
            if (isNaN(soNguoi) || soNguoi < 1) {
                // Nếu giá trị không hợp lệ hoặc rỗng, đặt mặc định là Cá nhân và mở khóa
                loaiSelect.value = 'individual';
                loaiSelect.disabled = false;
                return;
            }

            if (soNguoi === 1) {
                // Nếu là 1 người, luôn là Cá nhân (individual) và khóa trường lại
                loaiSelect.value = 'individual';
                loaiSelect.disabled = true; 
            } else if (soNguoi > 1) {
                // Nếu lớn hơn 1 người, luôn là Nhóm (group) và khóa trường lại
                loaiSelect.value = 'group';
                loaiSelect.disabled = true; // <--- Đã khóa để ngăn chỉnh về Cá nhân
            }
        }

        // 2. Thêm event listener cho sự kiện thay đổi (input)
        soNguoiInput.addEventListener('input', updateLoaiDatTour);
        
        // 3. Khắc phục vấn đề "disabled field không được gửi đi" (RẤT QUAN TRỌNG)
        form.addEventListener('submit', function() {
            // Mở khóa trường Loại ngay trước khi gửi đi để giá trị được bao gồm trong POST
            loaiSelect.disabled = false;
        });

        // 4. Chạy hàm khi load trang
        updateLoaiDatTour(); 
    });
</script>