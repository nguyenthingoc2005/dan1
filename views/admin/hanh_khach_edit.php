<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Hành Khách cho Đơn #<?= htmlspecialchars($data['dat_tour_id']) ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        .container { 
            max-width: 900px; 
            margin-top: 30px; 
        }
        .passenger-card { 
            border-left: 5px solid #ffc107; /* Màu Vàng Warning cho Edit */
            background-color: #fcf8e3; /* Nền vàng nhạt */
        }
        .form-control-label {
            font-weight: 600; 
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-warning text-dark py-3">
            <h4 class="mb-0"><i class="bi bi-person-lines-fill"></i> Chỉnh Sửa Danh Sách Hành Khách</h4>
            <div class="small">
                Đơn ID: **#<?= htmlspecialchars($data['dat_tour_id']) ?>** | Tổng Số Người: **<?= htmlspecialchars($data['so_nguoi']) ?>**
            </div>
        </div>
        
        <div class="card-body p-4">
            
            <form action="<?= BASEURL ?>?act=hanh_khach_update&dat_tour_id=<?= htmlspecialchars($data['dat_tour_id']) ?>" method="POST">
                <input type="hidden" name="dat_tour_id" value="<?= htmlspecialchars($data['dat_tour_id']) ?>">
                
                <?php 
                // GIẢ ĐỊNH: $hanhKhachList là mảng chứa dữ liệu hành khách hiện tại
                // Lấy số lượng người cần hiển thị từ đơn đặt tour
                $so_luong_nguoi = (int)($data['so_nguoi'] ?? 0);

                // Lặp qua số lượng người
                for ($i = 0; $i < $so_luong_nguoi; $i++): 
                    // Lấy dữ liệu cho hành khách hiện tại (dùng index $i)
                    $hk = $hanhKhachList[$i] ?? [];
                    $stt = $i + 1; // Số thứ tự hiển thị
                ?>
                    <div class="card passenger-card mb-4 border-radius-lg">
                        <div class="card-body p-3">
                            <h5 class="card-title text-warning mb-3">Hành Khách Số <?= $stt ?></h5>
                            
                            <input type="hidden" name="hanh_khach[<?= $i ?>][hanh_khach_id]" 
                                value="<?= htmlspecialchars($hk['hanh_khach_id'] ?? 0) ?>">
                            
                            <div class="row g-3">
                                
                                <div class="col-md-6">
                                    <label for="ho_ten_<?= $stt ?>" class="form-label form-control-label required">Họ và Tên</label>
                                    <input type="text" class="form-control" name="hanh_khach[<?= $i ?>][ho_ten]" id="ho_ten_<?= $stt ?>" 
                                        value="<?= htmlspecialchars($hk['ho_ten'] ?? '') ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="cccd_<?= $stt ?>" class="form-label form-control-label">CCCD/Hộ Chiếu</label>
                                    <input type="text" class="form-control" name="hanh_khach[<?= $i ?>][cccd]" id="cccd_<?= $stt ?>"
                                        value="<?= htmlspecialchars($hk['cccd'] ?? '') ?>">
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="ngay_sinh_<?= $stt ?>" class="form-label form-control-label">Ngày Sinh</label>
                                    <input type="date" class="form-control" name="hanh_khach[<?= $i ?>][ngay_sinh]" id="ngay_sinh_<?= $stt ?>"
                                        value="<?= htmlspecialchars($hk['ngay_sinh'] ?? '') ?>">
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="sdt_<?= $stt ?>" class="form-label form-control-label">Số điện thoại</label>
                                    <input type="text" class="form-control" name="hanh_khach[<?= $i ?>][sdt]" id="sdt_<?= $stt ?>"
                                        value="<?= htmlspecialchars($hk['sdt'] ?? '') ?>">
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="ghi_chu_<?= $stt ?>" class="form-label form-control-label">Ghi Chú</label>
                                    <input type="text" class="form-control" name="hanh_khach[<?= $i ?>][ghi_chu]" id="ghi_chu_<?= $stt ?>"
                                        value="<?= htmlspecialchars($hk['ghi_chu'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
                
                <div class="d-flex justify-content-end pt-3">
                    <a href="<?= BASEURL ?>?act=dat_tour_detail&id=<?= htmlspecialchars($data['dat_tour_id']) ?>" class="btn btn-secondary me-3">
                        <i class="bi bi-arrow-left-circle"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-circle"></i> Lưu Cập Nhật Hành Khách
                    </button>
                </div>
            </form>
            
            <?php if ($so_luong_nguoi == 0): ?>
                <div class="alert alert-info">
                    Đơn đặt tour này không yêu cầu thông tin hành khách (Số lượng người là 0).
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>