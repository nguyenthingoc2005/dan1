<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập Nhật Đơn Đặt Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .container { max-width: 700px; margin-top: 20px; }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Cập Nhật Đơn Đặt Tour #<?= htmlspecialchars($data['dat_tour_id'] ?? 'N/A') ?></h4>
        </div>
        <div class="card-body">
            
            <?php if (isset($data['dat_tour_id'])): ?>
            
                <form action="<?= BASEURL ?>?act=dat_tour_update&dat_tour_id=<?= htmlspecialchars($data['dat_tour_id']) ?>" method="POST">
                    <input type="hidden" name="dat_tour_id" value="<?= htmlspecialchars($data['dat_tour_id']) ?>">
                    
                    <h5 class="mb-3 text-primary">Thông tin Khởi tạo</h5>
                    <div class="row g-3">
                        
                        <div class="col-md-12">
                            <label for="khach_hang_id" class="form-label fw-bold">1. Khách Hàng Đặt Tour</label>
                            <select class="form-select" id="khach_hang_id" name="khach_hang_id" required>
                                <option value="">-- Chọn Khách Hàng --</option>
                                <?php 
                                $current_kh_id = $data['khach_hang_id'] ?? '';
                                if (isset($dataKhachHang) && is_array($dataKhachHang)):
                                    foreach ($dataKhachHang as $kh): 
                                        $selected = ($current_kh_id == $kh['khach_hang_id']) ? 'selected' : '';
                                ?>
                                    <option value="<?= htmlspecialchars($kh['khach_hang_id']) ?>" <?= $selected ?>>
                                        <?= htmlspecialchars($kh['ho_ten']) ?> (<?= htmlspecialchars($kh['so_dien_thoai']) ?>)
                                    </option>
                                <?php 
                                    endforeach;
                                endif; 
                                ?>
                            </select>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="tour_id" class="form-label fw-bold">2. Chọn Tour</label>
                            <select class="form-select" id="tour_id" name="tour_id" required>
                                <option value="">-- Chọn Tour --</option>
                                <?php 
                                $current_tour_id = $data['tour_id'] ?? '';
                                if (isset($dataTour) && is_array($dataTour)):
                                    foreach ($dataTour as $tour): 
                                        $selected = ($current_tour_id == $tour['tour_id']) ? 'selected' : '';
                                ?>
                                    <option value="<?= htmlspecialchars($tour['tour_id']) ?>" <?= $selected ?>>
                                        <?= htmlspecialchars($tour['ten']) ?> 
                                        (<?= htmlspecialchars($tour['thoi_luong_mac_dinh']) ?>)
                                    </option>
                                <?php 
                                    endforeach;
                                endif; 
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="so_nguoi" class="form-label fw-bold">3. Số Lượng Người</label>
                            <input type="number" class="form-control" id="so_nguoi" name="so_nguoi" min="1" 
                                value="<?= htmlspecialchars($data['so_nguoi'] ?? 1) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="loai" class="form-label fw-bold">4. Loại Đặt Tour</label>
                            <?php $current_loai = $data['loai'] ?? 'individual'; ?>
                            <select class="form-select" id="loai" name="loai" required >
                                <option value="individual" <?= $current_loai == 'individual' ? 'selected' : '' ?>>Cá nhân (Individual)</option>
                                <option value="group" <?= $current_loai == 'group' ? 'selected' : '' ?>>Nhóm (Group)</option>
                            </select>
                        </div>
                    </div>
                    
                    <hr class="my-4">

                    <h5 class="mb-3 text-primary">Trạng thái & Nguồn</h5>
                    <div class="row g-3">
                        
                        <div class="col-md-6">
                            <label for="trang_thai" class="form-label fw-bold">5. Trạng Thái Đặt Tour</label>
                            <?php $current_trang_thai = $data['trang_thai'] ?? 'chờ xác nhận'; ?>
                            <select class="form-select" id="trang_thai" name="trang_thai" required>
                                <option value="chờ xác nhận" <?= $current_trang_thai == 'chờ xác nhận' ? 'selected' : '' ?>>Chờ Xác Nhận</option>
                                <option value="đã đặt cọc" <?= $current_trang_thai == 'đã đặt cọc' ? 'selected' : '' ?>>Đã Đặt Cọc</option>
                                <option value="hoàn tất" <?= $current_trang_thai == 'hoàn tất' ? 'selected' : '' ?>>Hoàn Tất Thanh Toán</option>
                                <option value="hủy" <?= $current_trang_thai == 'hủy' ? 'selected' : '' ?>>Hủy</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="nguon" class="form-label fw-bold">6. Nguồn Đặt Tour</label>
                            <?php $current_nguon = $data['nguon'] ?? 'web'; ?>
                            <select class="form-select" id="nguon" name="nguon" required>
                                <option value="web" <?= $current_nguon == 'web' ? 'selected' : '' ?>>Web (Online)</option>
                                <option value="phone" <?= $current_nguon == 'phone' ? 'selected' : '' ?>>Phone (Điện thoại)</option>
                                <option value="agency" <?= $current_nguon == 'agency' ? 'selected' : '' ?>>Agency (Đại lý)</option>
                                <option value="walkin" <?= $current_nguon == 'walkin' ? 'selected' : '' ?>>Walkin (Tại quầy)</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <label for="ghi_chu" class="form-label">Ghi Chú</label>
                            <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="3"><?= htmlspecialchars($data['ghi_chu'] ?? '') ?></textarea>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="<?= BASEURL ?>?act=dat_tour_list" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left-circle"></i> Thoát
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle"></i> Cập Nhật Đơn Tour & Tiếp tục
                        </button>
                    </div>
                </form>

            <?php else: ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> Không tìm thấy dữ liệu đơn đặt tour.
                </div>
                <a href="<?= BASEURL ?>?act=dat_tour_list" class="btn btn-secondary">Quay lại danh sách</a>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const soNguoiInput = document.getElementById('so_nguoi');
        const loaiSelect = document.getElementById('loai');
        const form = document.querySelector('form');

        function updateLoaiDatTour() {
            const soNguoi = parseInt(soNguoiInput.value); 
            
            if (isNaN(soNguoi) || soNguoi < 1) {
                // Nếu giá trị không hợp lệ hoặc rỗng, đặt mặc định là Cá nhân và mở khóa
                loaiSelect.value = 'individual';
                loaiSelect.disabled = false;
                return;
            }

            if (soNguoi === 1) {
                loaiSelect.value = 'individual';
                loaiSelect.disabled = true; 
            } else if (soNguoi > 1) {
                loaiSelect.value = 'group';
                loaiSelect.disabled = true;
            }
        }

        // 1. Thêm event listener cho sự kiện thay đổi (input)
        soNguoiInput.addEventListener('input', updateLoaiDatTour);
        
        // 2. Khắc phục vấn đề "disabled field không được gửi đi"
        form.addEventListener('submit', function() {
            loaiSelect.disabled = false;
        });

        // 3. Chạy hàm khi load trang để thiết lập trạng thái ban đầu (RẤT QUAN TRỌNG VÌ LÀ FORM CẬP NHẬT)
        updateLoaiDatTour(); 
    });
</script>
</body>
</html>