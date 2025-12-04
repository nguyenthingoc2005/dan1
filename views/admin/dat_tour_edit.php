<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập Nhật Đơn Đặt Tour</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        /* MAIN LAYOUT */
        body { background-color: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .main-content { padding: 30px; margin-top: 70px; margin-left: 0; min-height: 100vh; }
        
        /* CARD & FORM STYLE */
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            background: #fff;
        }
        
        .form-label { font-weight: 600; color: #495057; font-size: 0.9rem; margin-bottom: 6px; }
        
        .form-control, .form-select {
            border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px; font-size: 0.95rem;
            transition: border-color 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        /* INPUT GROUP ICONS */
        .input-group-text {
            background-color: #f8f9fa; border: 1px solid #dee2e6; color: #6c757d; border-radius: 8px 0 0 8px;
        }
        
        /* SECTION DIVIDER */
        .section-title {
            font-size: 1rem; font-weight: 700; color: #0d6efd;
            border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 20px; margin-top: 10px;
        }
    </style>
</head>
<body>

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Cập Nhật Booking</h3>
                    <p class="text-muted mb-0">Chỉnh sửa thông tin đơn đặt tour #<?= htmlspecialchars($data['dat_tour_id'] ?? '') ?></p>
                </div>
                <a href="<?= BASEURL ?>?act=dat_tour_list" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại
                </a>
            </div>

            <?php if (isset($data['dat_tour_id'])): ?>
                
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card card-custom p-4">
                            <form action="<?= BASEURL ?>?act=dat_tour_update&dat_tour_id=<?= htmlspecialchars($data['dat_tour_id']) ?>" method="POST">
                                <input type="hidden" name="dat_tour_id" value="<?= htmlspecialchars($data['dat_tour_id']) ?>">

                                <div class="row g-4">
                                    
                                    <div class="col-md-7">
                                        <div class="section-title"><i class="bi bi-person-vcard me-2"></i>Thông tin Khách & Tour</div>
                                        
                                        <div class="mb-3">
                                            <label for="khach_hang_id" class="form-label">Khách Hàng <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
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
                                                    <?php endforeach; endif; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="tour_id" class="form-label">Tour Đăng Ký <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-map-fill"></i></span>
                                                <select class="form-select" id="tour_id" name="tour_id" required>
                                                    <option value="">-- Chọn Tour --</option>
                                                    <?php 
                                                    $current_tour_id = $data['tour_id'] ?? '';
                                                    if (isset($dataTour) && is_array($dataTour)):
                                                        foreach ($dataTour as $tour): 
                                                            $selected = ($current_tour_id == $tour['tour_id']) ? 'selected' : '';
                                                    ?>
                                                        <option value="<?= htmlspecialchars($tour['tour_id']) ?>" <?= $selected ?>>
                                                            <?= htmlspecialchars($tour['ten']) ?> (<?= htmlspecialchars($tour['thoi_luong_mac_dinh']) ?> ngày)
                                                        </option>
                                                    <?php endforeach; endif; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="so_nguoi" class="form-label">Số Lượng Khách <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-people-fill"></i></span>
                                                    <input type="number" class="form-control fw-bold text-primary" id="so_nguoi" name="so_nguoi" min="1" 
                                                           value="<?= htmlspecialchars($data['so_nguoi'] ?? 1) ?>" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="loai" class="form-label">Loại Đặt Tour</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-tags-fill"></i></span>
                                                    <?php $current_loai = $data['loai'] ?? 'individual'; ?>
                                                    <select class="form-select bg-light" id="loai" name="loai" required>
                                                        <option value="individual" <?= $current_loai == 'individual' ? 'selected' : '' ?>>Cá nhân</option>
                                                        <option value="group" <?= $current_loai == 'group' ? 'selected' : '' ?>>Nhóm</option>
                                                    </select>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.75rem;">* Tự động chuyển đổi theo số lượng khách</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="section-title"><i class="bi bi-sliders me-2"></i>Trạng thái & Ghi chú</div>

                                        <div class="mb-3">
                                            <label for="trang_thai" class="form-label">Trạng Thái</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-lightning-charge-fill"></i></span>
                                                <?php $current_trang_thai = $data['trang_thai'] ?? 'chờ xác nhận'; ?>
                                                <select class="form-select" id="trang_thai" name="trang_thai" required>
                                                    <option value="chờ xác nhận" <?= $current_trang_thai == 'chờ xác nhận' ? 'selected' : '' ?>>Chờ Xác Nhận</option>
                                                    <option value="đã đặt cọc" <?= $current_trang_thai == 'đã đặt cọc' ? 'selected' : '' ?>>Đã Đặt Cọc</option>
                                                    <option value="hoàn tất" <?= $current_trang_thai == 'hoàn tất' ? 'selected' : '' ?>>Hoàn Tất</option>
                                                    <option value="hủy" <?= $current_trang_thai == 'hủy' ? 'selected' : '' ?>>Hủy Đơn</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="nguon" class="form-label">Nguồn Đặt</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-globe"></i></span>
                                                <?php $current_nguon = $data['nguon'] ?? 'web'; ?>
                                                <select class="form-select" id="nguon" name="nguon" required>
                                                    <option value="web" <?= $current_nguon == 'web' ? 'selected' : '' ?>>Website</option>
                                                    <option value="phone" <?= $current_nguon == 'phone' ? 'selected' : '' ?>>Điện thoại</option>
                                                    <option value="agency" <?= $current_nguon == 'agency' ? 'selected' : '' ?>>Đại lý</option>
                                                    <option value="walkin" <?= $current_nguon == 'walkin' ? 'selected' : '' ?>>Tại quầy</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="ghi_chu" class="form-label">Ghi Chú</label>
                                            <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="4" placeholder="Ghi chú thêm về đơn hàng..."><?= htmlspecialchars($data['ghi_chu'] ?? '') ?></textarea>
                                        </div>
                                    </div>

                                </div>

                                <hr class="my-4">

                                <div class="d-flex justify-content-end gap-3">
                                    <a href="<?= BASEURL ?>?act=dattourlist" class="btn btn-light border px-4">Hủy bỏ</a>
                                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                                        <i class="bi bi-save me-2"></i> Lưu Cập Nhật
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="alert alert-danger shadow-sm text-center py-5">
                    <i class="bi bi-exclamation-octagon display-1 text-danger opacity-50"></i>
                    <h4 class="mt-3">Không tìm thấy dữ liệu!</h4>
                    <p class="text-muted">Đơn đặt tour này không tồn tại hoặc đã bị xóa.</p>
                    <a href="<?= BASEURL ?>?act=dattourlist" class="btn btn-secondary mt-2">Quay lại danh sách</a>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const soNguoiInput = document.getElementById('so_nguoi');
            const loaiSelect = document.getElementById('loai');
            const form = document.querySelector('form');

            function updateLoaiDatTour() {
                const soNguoi = parseInt(soNguoiInput.value);
                
                // Mặc định disable để user không chọn sai
                // Chỉ mở khi cần thiết, hoặc dùng JS set value và style readonly giả
                
                if (isNaN(soNguoi) || soNguoi < 1) {
                    loaiSelect.value = 'individual';
                    // loaiSelect.disabled = false; // Có thể mở nếu muốn user tự chọn khi lỗi
                    return;
                }

                if (soNguoi === 1) {
                    loaiSelect.value = 'individual';
                    // Giao diện: Khóa lại để user thấy hệ thống tự động
                    loaiSelect.style.pointerEvents = 'none'; 
                    loaiSelect.style.backgroundColor = '#e9ecef';
                } else if (soNguoi > 1) {
                    loaiSelect.value = 'group';
                    loaiSelect.style.pointerEvents = 'none';
                    loaiSelect.style.backgroundColor = '#e9ecef';
                }
            }

            // Chạy khi nhập liệu
            soNguoiInput.addEventListener('input', updateLoaiDatTour);
            
            // Chạy khi load trang
            updateLoaiDatTour(); 
        });
    </script>
</body>
</html>