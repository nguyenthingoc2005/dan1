<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật Dịch Vụ</title>
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
            border: none; border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05); background: #fff;
        }
        
        .form-label { font-weight: 600; color: #495057; font-size: 0.9rem; margin-bottom: 6px; }
        .required::after { content: " *"; color: #dc3545; }
        
        .form-control, .form-select {
            border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px; font-size: 0.95rem;
            transition: border-color 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #ffc107; box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25); /* Màu vàng warning */
        }

        /* INPUT GROUP ICONS */
        .input-group-text {
            background-color: #f8f9fa; border: 1px solid #dee2e6; color: #6c757d; border-radius: 8px 0 0 8px;
        }
        
        /* SECTION DIVIDER */
        .section-title {
            font-size: 1rem; font-weight: 700; color: #ffc107; /* Màu vàng */
            border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 20px; margin-top: 10px;
        }
    </style>
</head>
<body>

    <?php include_once './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Cập nhật Dịch Vụ</h3>
                    <p class="text-muted mb-0">Chỉnh sửa thông tin dịch vụ #<?= $dichvu['dich_vu_id'] ?></p>
                </div>
                <a href="<?= BASEURL ?>?act=lay_dich_vu_ncc&ncc_id=<?= $dichvu['ncc_id'] ?>" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card card-custom p-4">
                        <form action="?act=capnhat_dich_vu&id=<?= $dichvu['dich_vu_id'] ?>" method="POST">
                            
                            <input type="hidden" name="dich_vu_id" value="<?= $dichvu['dich_vu_id'] ?>">

                            <div class="row g-4">
                                
                                <div class="col-md-6">
                                    <div class="section-title"><i class="bi bi-info-circle me-2"></i>Thông tin Dịch vụ</div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label required">Tên Dịch Vụ</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                            <input type="text" class="form-control" name="ten_dich_vu" 
                                                   value="<?= htmlspecialchars($dichvu['ten_dich_vu']) ?>" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label required">Mã Dịch Vụ</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                            <input type="text" class="form-control" name="ma" 
                                                   value="<?= htmlspecialchars($dichvu['ma']) ?>" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label required">Loại Dịch Vụ</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-collection"></i></span>
                                            <input type="text" class="form-control" name="loai_dich_vu" 
                                                   value="<?= htmlspecialchars($dichvu['loai_dich_vu']) ?>" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label required">Nhà Cung Cấp</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                                            <select name="ncc_id" class="form-select" required>
                                                <option value="">-- Chọn Nhà Cung Cấp --</option>
                                                <?php foreach ($nccList as $ncc): ?>
                                                    <option value="<?= $ncc['ncc_id'] ?>" 
                                                        <?= $ncc['ncc_id'] == $dichvu['ncc_id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($ncc['ten']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="section-title"><i class="bi bi-cash-coin me-2"></i>Chi tiết & Giá cả</div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required">Giá Mặc Định</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control fw-bold text-primary" name="gia_mac_dinh" 
                                                       value="<?= htmlspecialchars($dichvu['gia_mac_dinh']) ?>" required>
                                                <span class="input-group-text fw-bold">VNĐ</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required">Đơn Vị Tính</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-rulers"></i></span>
                                                <input type="text" class="form-control" name="don_vi" 
                                                       value="<?= htmlspecialchars($dichvu['don_vi']) ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Mô Tả Chi Tiết</label>
                                        <textarea class="form-control" name="mo_ta" rows="6"><?= htmlspecialchars($dichvu['mo_ta']) ?></textarea>
                                    </div>
                                </div>
                                
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-3">
                                <a href="<?= BASEURL ?>?act=lay_dich_vu" class="btn btn-light border px-4">Hủy bỏ</a>
                                <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm text-dark">
                                    <i class="bi bi-check-circle me-2"></i> Cập Nhật Dịch Vụ
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="./assets/js/sidebar.js"></script>
</body>
</html>