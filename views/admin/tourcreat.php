<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Tour Mới</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        /* Layout Style */
        body { background-color: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        
        .main-content {
            padding: 30px;
            margin-top: 70px;
            margin-left: 0;   
            transition: margin-left .32s ease;
            min-height: 100vh;
        }

        /* Card Style */
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            background: #fff;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 10px 15px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
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
                    <h3 class="fw-bold text-dark mb-1">Tạo Tour Mới</h3>
                    <p class="text-muted mb-0">Điền thông tin để thêm tour du lịch mới vào hệ thống.</p>
                </div>
                <a href="<?= BASEURL ?>?act=tour_list" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card card-custom p-4">
                        <form action="<?= BASEURL ?>?act=createtour" method="POST">
                            
                            <div class="row g-4">
                                <div class="col-md-8">
                                    <h5 class="mb-3 text-primary"><i class="bi bi-info-circle me-2"></i>Thông tin chung</h5>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Tên Tour <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="ten" placeholder="Ví dụ: Tour Hà Nội - Sapa 3N2Đ" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Mô tả ngắn</label>
                                        <input type="text" class="form-control" name="mo_ta_ngan" placeholder="Mô tả tóm tắt điểm nổi bật...">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Mô tả chi tiết</label>
                                        <textarea class="form-control" name="mo_ta" rows="6" placeholder="Nhập chi tiết về tour..."></textarea>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <h5 class="mb-3 text-primary"><i class="bi bi-gear me-2"></i>Cấu hình</h5>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                                        <select class="form-select" name="danh_muc_id" required>
                                            <option value="" selected disabled>-- Chọn danh mục --</option>
                                            <?php if(!empty($data)): ?>
                                                <?php foreach($data as $cat): ?>
                                                    <option value="<?= $cat['danh_muc_id'] ?>"><?= htmlspecialchars($cat['ten']) ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Điểm khởi hành</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                            <input type="text" class="form-control" name="diem_khoi_hanh" placeholder="TP. Hồ Chí Minh">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Giá cơ bản <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" step="1000" min="0" class="form-control fw-bold text-danger" name="gia_co_ban" placeholder="0" required>
                                            <span class="input-group-text fw-bold">VNĐ</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Thời lượng</label>
                                        <div class="input-group">
                                            <input type="number" min="1" class="form-control" name="thoi_luong_mac_dinh" placeholder="Số ngày">
                                            <span class="input-group-text">Ngày</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Trạng thái</label>
                                        <select class="form-select" name="hoat_dong">
                                            <option value="1" selected>Đang hoạt động</option>
                                            <option value="0">Tạm dừng</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-3">
                                <a href="<?= BASEURL ?>?act=tour_list" class="btn btn-light border px-4">Hủy bỏ</a>
                                <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                                    <i class="bi bi-check-lg me-2"></i> Lưu Tour
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