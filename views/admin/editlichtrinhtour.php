<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Lịch Trình Tour</title>
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
        .required::after { content: " *"; color: #dc3545; }
        
        .form-control {
            border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px; font-size: 0.95rem;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: #ffc107; box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
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
                    <h3 class="fw-bold text-dark mb-1">Cập Nhật Lịch Trình</h3>
                    <p class="text-muted mb-0">Chỉnh sửa thông tin chi tiết cho ngày đi cụ thể.</p>
                </div>
                <a href="<?= BASEURL ?>?act=addlichtrinh&tour_id=<?= $info['tour_id'] ?>" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card card-custom p-4">
                        <div class="section-title"><i class="bi bi-pencil-square me-2"></i>Thông tin Lịch trình</div>

                        <form action="<?= BASEURL ?>?act=updatelichtrinh&lich_trinh_id=<?= $info['lich_trinh_id'] ?>" method="POST">
                            
                            <input type="hidden" name="lich_trinh_id" value="<?= $info['lich_trinh_id'] ?>">
                            <input type="hidden" name="tour_id" value="<?= $info['tour_id'] ?>">

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label required">Ngày Thứ</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                        <input type="number" name="ngay_thu" class="form-control fw-bold text-primary" 
                                               value="<?= $info['ngay_thu'] ?>" min="1" required placeholder="VD: 1">
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label required">Tiêu Đề Ngắn</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                                        <input type="text" name="tieu_de" class="form-control" 
                                               value="<?= htmlspecialchars($info['tieu_de']) ?>" required placeholder="VD: Khám phá Phố Cổ">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label required">Nội Dung Chi Tiết</label>
                                    <textarea name="noi_dung" class="form-control" rows="8" required placeholder="Mô tả chi tiết các hoạt động..."><?= htmlspecialchars($info['noi_dung']) ?></textarea>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-3">
                                <a href="<?= BASEURL ?>?act=addlichtrinh&tour_id=<?= $info['tour_id'] ?>" class="btn btn-light border px-4">Hủy bỏ</a>
                                <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm text-dark">
                                    <i class="bi bi-save me-2"></i> Cập Nhật
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