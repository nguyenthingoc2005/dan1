<?php
// PHP LOGIC (Giữ nguyên)
$tour_id = htmlspecialchars($tour['tour_id'] ?? 0);
$tour_name = htmlspecialchars($tour['ten'] ?? 'Chưa cập nhật tên');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Lịch Trình</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        body { background-color: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .main-content { padding: 30px; margin-top: 70px; min-height: 100vh; }
        
        /* TIMELINE CARD STYLE */
        .day-card {
            border: none;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            margin-bottom: 20px;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
            border-left: 5px solid #0d6efd; /* Đường viền màu xanh bên trái */
        }
        .day-card:hover { transform: translateY(-3px); box-shadow: 0 8px 16px rgba(0,0,0,0.08); }
        
        .day-badge {
            background-color: #0d6efd; color: white;
            padding: 5px 15px;
            border-radius: 0 0 10px 0;
            font-weight: 700; font-size: 0.9rem;
            position: absolute; top: 0; left: 0;
        }
        
        .card-actions {
            position: absolute; top: 15px; right: 15px;
            opacity: 0.4; transition: opacity 0.2s;
        }
        .day-card:hover .card-actions { opacity: 1; }

        /* FORM CARD STYLE */
        .form-card {
            border: none; border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            background: #fff;
            position: sticky; top: 90px; /* Ghim form khi cuộn */
        }

        /* BUTTON ICONS */
        .btn-icon-circle {
            width: 35px; height: 35px;
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .btn-edit { background: #fff3cd; color: #ffc107; }
        .btn-edit:hover { background: #ffc107; color: #000; }
        
        .btn-delete { background: #ffe5e5; color: #dc3545; }
        .btn-delete:hover { background: #dc3545; color: #fff; }

        .btn-nav { padding: 8px 20px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; }
        .btn-nav-back { background: #fff; border: 1px solid #dee2e6; color: #6c757d; }
        .btn-nav-back:hover { background: #f8f9fa; color: #000; }
        
        .btn-nav-next { background: #0d6efd; color: #fff; border: none; }
        .btn-nav-next:hover { background: #0b5ed7; color: #fff; }
    </style>
</head>
<body class="bg-light">

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-primary mb-1">
                        <i class="bi bi-journal-text me-2"></i>Lịch Trình Tour
                    </h3>
                    <p class="text-muted mb-0">Tour #<?= $tour_id ?>: <strong><?= $tour_name ?></strong></p>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="<?= BASEURL ?>?act=chitiettour&tour_id=<?= $tour_id ?>" class="btn btn-nav btn-nav-back shadow-sm">
                        <i class="bi bi-eye"></i> Xem chi tiết
                    </a>
                </div>
            </div>

            <div class="row">
                
                <div class="col-lg-7 mb-4">
                    <h5 class="mb-3 text-secondary fw-bold">Danh sách ngày đi</h5>
                    
                    <?php if (empty($data)): ?>
                        <div class="text-center py-5 bg-white rounded-3 shadow-sm border border-dashed">
                            <i class="bi bi-calendar-plus display-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-3">Chưa có lịch trình nào.</p>
                            <p class="small text-muted">Hãy thêm ngày đầu tiên ở form bên phải.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($data as $lt): ?>
                            <div class="day-card p-4 pt-5">
                                <div class="day-badge">Ngày <?= htmlspecialchars($lt['ngay_thu']) ?></div>
                                
                                <div class="card-actions">
                                    <a href="<?= BASEURL ?>?act=editlichtrinh&lich_trinh_id=<?= $lt['lich_trinh_id'] ?>&tour_id=<?= $tour_id ?>" 
                                       class="btn-icon-circle btn-edit me-1" title="Chỉnh sửa">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <a href="<?= BASEURL ?>?act=deletelichtrinh&lich_trinh_id=<?= $lt['lich_trinh_id'] ?>&tour_id=<?= $tour_id ?>" 
                                       class="btn-icon-circle btn-delete" title="Xóa"
                                       onclick="return confirm('Bạn chắc chắn muốn xóa lịch trình Ngày <?= $lt['ngay_thu'] ?>?')">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>

                                <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($lt['tieu_de']) ?></h5>
                                <div class="text-secondary" style="white-space: pre-line; line-height: 1.6;">
                                    <?= htmlspecialchars($lt['noi_dung']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="col-lg-5">
                    <div class="form-card p-4">
                        <h5 class="mb-3 text-success fw-bold"><i class="bi bi-plus-circle me-2"></i>Thêm Ngày Mới</h5>
                        <form action="<?= BASEURL ?>?act=createlichtrinh&tour_id=<?= $tour_id ?>" method="POST">
                            <input type="hidden" name="tour_id" value="<?= $tour_id ?>">

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Thứ tự ngày</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-event"></i></span>
                                    <input type="number" name="ngay_thu" class="form-control bg-light border-start-0" 
                                           placeholder="VD: 1" min="1" required 
                                           value="<?= isset($data) ? count($data) + 1 : 1 ?>"> </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Tiêu đề ngắn</label>
                                <input type="text" name="tieu_de" class="form-control" placeholder="VD: Khám phá Phố Cổ" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Nội dung chi tiết</label>
                                <textarea name="noi_dung" class="form-control" rows="6" placeholder="Mô tả các hoạt động trong ngày..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                                <i class="bi bi-save me-2"></i> Lưu Lịch Trình
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                <a href="<?= BASEURL ?>?act=gan_diadiem&tour_id=<?= $tour_id ?>" class="btn btn-nav btn-nav-back">
                    <i class="bi bi-arrow-left"></i> Quay lại: Gán Địa điểm
                </a>
                
                <a href="<?= BASEURL ?>?act=chinhsach&tour_id=<?= $tour_id ?>" class="btn btn-nav btn-nav-next shadow">
                    Tiếp theo: Gắn Chính sách <i class="bi bi-arrow-right"></i>
                </a>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>
</html>