<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật Nhà Cung Cấp</title>
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
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            background: #fff;
        }
        
        .form-label { font-weight: 600; color: #495057; font-size: 0.9rem; margin-bottom: 6px; }
        .required::after { content: " *"; color: #dc3545; }
        
        .form-control {
            border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px; font-size: 0.95rem;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: #ffc107; box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25); /* Màu vàng cho Update */
        }

        /* INPUT GROUP ICONS */
        .input-group-text {
            background-color: #f8f9fa; border: 1px solid #dee2e6; color: #6c757d; border-radius: 8px 0 0 8px;
        }
        
        /* SECTION TITLE */
        .section-title {
            font-size: 1rem; font-weight: 700; color: #ffc107;
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
                    <h3 class="fw-bold text-dark mb-1">Cập nhật Nhà Cung Cấp</h3>
                    <p class="text-muted mb-0">Chỉnh sửa thông tin đối tác #<?= htmlspecialchars($ncc['ncc_id']) ?></p>
                </div>
                <a href="<?= BASEURL ?>?act=ncc_list" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card card-custom p-4">
                        <div class="section-title"><i class="bi bi-pencil-square me-2"></i>Thông tin Đối tác</div>

                        <form action="<?= BASEURL ?>?act=ncc_update_save&id=<?= $ncc['ncc_id'] ?>" method="POST">
                            
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="ten" class="form-label required">Tên Nhà Cung Cấp</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                                        <input type="text" id="ten" name="ten" class="form-control fw-bold text-dark" 
                                               value="<?= htmlspecialchars($ncc['ten']) ?>" required placeholder="Nhập tên công ty/đối tác">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="ma_so_thue" class="form-label">Mã Số Thuế</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-receipt"></i></span>
                                        <input type="text" id="ma_so_thue" name="ma_so_thue" class="form-control" 
                                               value="<?= htmlspecialchars($ncc['ma_so_thue']) ?>" placeholder="VD: 010xxxxxxx">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="lien_he" class="form-label">Thông Tin Liên Hệ</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="text" id="lien_he" name="lien_he" class="form-control" 
                                               value="<?= htmlspecialchars($ncc['lien_he']) ?>" placeholder="SĐT hoặc người đại diện">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="dia_chi" class="form-label">Địa Chỉ</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                        <input type="text" id="dia_chi" name="dia_chi" class="form-control" 
                                               value="<?= htmlspecialchars($ncc['dia_chi']) ?>" placeholder="Địa chỉ văn phòng/trụ sở">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-3">
                                <a href="<?= BASEURL ?>?act=ncc_list" class="btn btn-light border px-4">Hủy bỏ</a>
                                <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm text-dark">
                                    <i class="bi bi-save me-2"></i> Lưu Thay Đổi
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