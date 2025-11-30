<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập Nhật Tour</title>
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
            border-color: #ffc107; /* Màu vàng cam cho hành động Sửa */
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.2);
        }

        /* INPUT GROUP ICONS */
        .input-group-text {
            background-color: #f8f9fa; border: 1px solid #dee2e6; color: #6c757d; border-radius: 8px 0 0 8px;
        }
        
        /* SECTION DIVIDER */
        .section-title {
            font-size: 1rem; font-weight: 700; color: #ffc107; /* Màu vàng cam */
            border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 20px; margin-top: 10px;
        }

        /* BUTTONS */
        .btn-warning-custom {
            background-color: #ffc107; border-color: #ffc107; color: #212529; font-weight: 600;
        }
        .btn-warning-custom:hover {
            background-color: #ffca2c; border-color: #ffc720;
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
                    <h3 class="fw-bold text-dark mb-1">Cập Nhật Tour</h3>
                    <p class="text-muted mb-0">Chỉnh sửa thông tin Tour <strong>#<?= htmlspecialchars($tour['tour_id']) ?></strong></p>
                </div>
                <a href="<?= BASEURL ?>?act=tour_list" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card card-custom p-4">
                        
                        <form action="<?= BASEURL ?>?act=uppdatetour1&tour_id=<?= htmlspecialchars($tour['tour_id']) ?>" method="POST">
                            <input type="hidden" name="tour_id" value="<?= htmlspecialchars($tour['tour_id']) ?>">
                            
                            <div class="row g-4">
                                <div class="col-md-8">
                                    <div class="section-title"><i class="bi bi-info-circle me-2"></i>Thông tin Chung</div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Tên Tour <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-tag-fill"></i></span>
                                            <input type="text" class="form-control fw-bold" name="ten" 
                                                   value="<?= htmlspecialchars($tour['ten']) ?>" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">

                                        <label class="form-label">Mô tả ngắn</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                            <input type="text" class="form-control" name="mo_ta_ngan" 
                                                   value="<?= htmlspecialchars($tour['mo_ta_ngan']) ?>">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Mô tả chi tiết</label>
                                        <textarea class="form-control" name="mo_ta" rows="6"><?= htmlspecialchars($tour['mo_ta']) ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="section-title"><i class="bi bi-gear me-2"></i>Thiết lập & Giá</div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-list-ul"></i></span>
                                            <select class="form-select" name="danh_muc_id" required>
                                                <option value="" disabled>-- Chọn danh mục --</option>
                                                <?php if(!empty($data)): ?>
                                                    <?php foreach($data as $cat): ?>
                                                        <option value="<?= $cat['danh_muc_id'] ?>" 
                                                            <?= $cat['danh_muc_id'] == $tour['danh_muc_id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($cat['ten']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Điểm khởi hành</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                                            <input type="text" class="form-control" name="diem_khoi_hanh" 
                                                   value="<?= htmlspecialchars($tour['diem_khoi_hanh']) ?>">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Giá cơ bản <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                            <input type="number" step="1000" min="0" class="form-control fw-bold text-success" name="gia_co_ban" 
                                                   value="<?= htmlspecialchars($tour['gia_co_ban']) ?>" required>
                                            <span class="input-group-text fw-bold bg-light">VNĐ</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Thời lượng</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-clock-fill"></i></span>
                                            <input type="number" min="1" class="form-control" name="thoi_luong_mac_dinh" 
                                                   value="<?= htmlspecialchars($tour['thoi_luong_mac_dinh']) ?>">
                                            <span class="input-group-text">Ngày</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Trạng thái</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-toggle-on"></i></span>
                                            <select class="form-select" name="hoat_dong">
                                                <option value="1" <?= $tour['hoat_dong'] == 1 ? 'selected' : '' ?>>Đang hoạt động</option>
                                                <option value="0" <?= $tour['hoat_dong'] == 0 ? 'selected' : '' ?>>Tạm dừng</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-3">
                                <a href="<?= BASEURL ?>?act=tour_list" class="btn btn-light border px-4">Hủy bỏ</a>
                                <button type="submit" class="btn btn-warning-custom px-5 shadow-sm">
                                    <i class="bi bi-save me-2"></i> Lưu Thay Đổi
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>
</html>