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
        /* Đảm bảo nội dung form nằm giữa trong main content */
        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Thẻ fieldset (phân nhóm) - Giữ màu Warning cho phân biệt nhóm nội dung */
        fieldset {
            border-color: #ffc107 !important;
        }
        .text-warning-custom {
            color: #ffc107 !important;
        }

        /* Header của card - Đổi sang Primary (xanh dương) */
        .card-header-custom {
            background-color: #0d6efd !important;
            color: white;
            border-bottom: 1px solid #0d6efd;
        }
        
        /* ------------------------------------------------ */
        /* CẢI THIỆN NÚT HÀNH ĐỘNG                                */
        /* ------------------------------------------------ */

        /* 1. Nút hành động chính (Cập nhật) - Đổi sang Primary, chữ vừa phải */
        .btn-update-next {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
            font-weight: 600; /* Giảm độ đậm của chữ */
            font-size: 1.1rem; /* Giảm kích thước chữ từ lg sang custom */
            transition: all 0.2s;
        }
        .btn-update-next:hover {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
        }

        /* 2. Nút hành động phụ (Quay lại) - Đổi sang màu xám nhạt, viền nhẹ, chữ vừa phải */
        .btn-return-detail {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            color: #495057;
            font-weight: normal; 
            font-size: 1.1rem; /* Giảm kích thước chữ từ lg sang custom */
            transition: all 0.2s;
        }
        .btn-return-detail:hover {
            background-color: #e9ecef;
            color: #212529;
            border-color: #adb5bd;
        }
    </style>
</head>
<body class="bg-light">

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid py-4">
            
            <div class="form-container">
                <div class="card shadow-lg border-0">
                    <div class="card-header card-header-custom d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil-square me-2"></i> Cập nhật Tour #<?= htmlspecialchars($tour['tour_id']) ?>
                        </h4>
                    </div>

                    <div class="card-body p-4">
                        <form action="<?= BASEURL ?>?act=uppdatetour1&tour_id=<?= htmlspecialchars($tour['tour_id']) ?>" method="POST">
                            
                            <input type="hidden" name="tour_id" value="<?= htmlspecialchars($tour['tour_id']) ?>">

                            <fieldset class="border p-3 mb-4 rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 text-warning-custom">Thông tin cơ bản</legend>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tên tour <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="ten" value="<?= htmlspecialchars($tour['ten']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Danh mục tour <span class="text-danger">*</span></label>
                                    <select class="form-select" name="danh_muc_id" required>
                                        <?php foreach($data as $cat): ?>
                                            <option value="<?= htmlspecialchars($cat['danh_muc_id']) ?>" <?= $cat['danh_muc_id'] == $tour['danh_muc_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['ten']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Giá cơ bản (VND)</label>
                                        <input type="number" step="1" class="form-control" name="gia_co_ban" value="<?= htmlspecialchars($tour['gia_co_ban']) ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Thời lượng (Ngày)</label>
                                        <input type="number" class="form-control" name="thoi_luong_mac_dinh" value="<?= htmlspecialchars($tour['thoi_luong_mac_dinh']) ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Điểm khởi hành</label>
                                    <input type="text" class="form-control" name="diem_khoi_hanh" value="<?= htmlspecialchars($tour['diem_khoi_hanh']) ?>">
                                </div>
                            </fieldset>

                            <fieldset class="border p-3 mb-4 rounded-3">
                                <legend class="float-none w-auto px-2 fs-6 text-warning-custom">Mô tả & Trạng thái</legend>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mô tả ngắn</label>
                                    <input type="text" class="form-control" name="mo_ta_ngan" value="<?= htmlspecialchars($tour['mo_ta_ngan']) ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mô tả chi tiết</label>
                                    <textarea class="form-control" name="mo_ta" rows="4"><?= htmlspecialchars($tour['mo_ta']) ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Trạng thái Tour</label>
                                    <select class="form-select" name="hoat_dong">
                                        <option value="1" <?= $tour['hoat_dong'] == 1 ? 'selected' : '' ?>>Đang hoạt động</option>
                                        <option value="0" <?= $tour['hoat_dong'] == 0 ? 'selected' : '' ?>>Tạm dừng</option>
                                    </select>
                                </div>
                            </fieldset>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="btn btn-update-next btn-lg flex-grow-1 me-2">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Cập nhật & Tiếp theo
                                </button>
                                <a href="<?= BASEURL ?>?act=chitiettour&tour_id=<?= htmlspecialchars($tour['tour_id']) ?>" 
                                   class="btn btn-return-detail btn-lg flex-grow-0">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Quay lại Chi tiết Tour
                                </a>
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