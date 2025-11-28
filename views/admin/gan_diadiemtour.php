<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Gán Địa điểm Tour</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        /* Tăng cường UI cho nút Tiếp theo (Primary) */
        .btn-next-step {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
            font-weight: 600;
        }
        .btn-next-step:hover {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
            color: white;
        }

        /* Nút Quay lại/Hành động Phụ */
        .btn-nav-action {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            color: #495057;
            font-weight: 600;
        }
        .btn-nav-action:hover {
            background-color: #e9ecef;
            color: #212529;
            border-color: #adb5bd;
        }

        /* Nút Bỏ qua về Xem Chi tiết (Hành động kết thúc quy trình tạm thời) */
        .btn-skip-view {
            background-color: #198754; /* Xanh lá */
            border-color: #198754;
            color: white;
            font-weight: 600;
        }
        .btn-skip-view:hover {
            background-color: #157347;
            border-color: #146c43;
        }
    </style>
</head>
<body class="bg-light">

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid py-4">

            <?php 
                // Giả định $tour_id đã được định nghĩa
                $tour_id_safe = htmlspecialchars($tour_id ?? '');
            ?>
            
            <h2 class="mb-4 text-info">
                <i class="bi bi-pin-map-fill me-2"></i> Quản Lý Địa Điểm Tour #<?= $tour_id_safe ?>
            </h2>

            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check me-2"></i> Địa điểm đã gán cho Tour #<?= $tour_id_safe ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    // Giả định biến $diaDiemDaGan chứa kết quả từ hàm getDiaDiemByTourId()
                    if (empty($diaDiemDaGan)): 
                    ?>
                        <div class="alert alert-warning text-center">
                            Tour này chưa có địa điểm nào được gán.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle bg-white shadow-sm">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 10%;">Ảnh</th>
                                        <th style="width: 25%;">Địa điểm</th>
                                        <th style="width: 15%;">Quốc gia</th>
                                        <th style="width: 35%;">Mô tả ngắn</th>
                                        <th style="width: 10%;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($diaDiemDaGan as $index => $dd): ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td class="text-center">
                                            <?php if ($dd['hinh_anh']): ?>
                                                <img src="<?= htmlspecialchars($dd['hinh_anh']) ?>" 
                                                     alt="<?= htmlspecialchars($dd['ten_diadiem']) ?>" 
                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                            <?php else: ?>
                                                <i class="bi bi-image-fill text-muted fs-4" title="Không có ảnh"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($dd['ten_diadiem']) ?></strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary"><?= htmlspecialchars($dd['quoc_gia']) ?></span>
                                        </td>
                                        <td><?= nl2br(substr(htmlspecialchars($dd['mo_ta']), 0, 100)) . (strlen($dd['mo_ta']) > 100 ? '...' : '') ?></td>
                                        <td class="text-center">
                                            <a href="<?= BASEURL ?>?act=xoa_dia_diem&dia_diem_tour_id=<?= $dd['dia_diem_tour_id'] ?>&tour_id=<?= $tour_id_safe ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa địa điểm này?')" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i> Xóa
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i> Gắn địa điểm mới cho Tour #<?= $tour_id_safe ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASEURL ?>?act=luu_gan_diadiem&tour_id=<?= $tour_id_safe ?>" method="POST">

                        <input type="hidden" name="tour_id" value="<?= $tour_id_safe ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn địa điểm (nhiều)</label>

                            <div class="border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                                <?php if (empty($data)): ?>
                                    <p class="text-danger">Không có địa điểm nào để gán.</p>
                                <?php else: ?>
                                    <?php foreach($data as $dd): ?>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input"
                                                type="checkbox"
                                                name="dia_diem_id[]"
                                                value="<?= $dd['dia_diem_id'] ?>"
                                                id="dd_<?= $dd['dia_diem_id'] ?>">
                                            
                                            <label class="form-check-label d-flex align-items-center" for="dd_<?= $dd['dia_diem_id'] ?>">
                                                <strong><?= htmlspecialchars($dd['ten']) ?></strong>
                                                <input type="number" name="thu_tu[<?= $dd['dia_diem_id'] ?>]" placeholder="Thứ tự" class="form-control-sm ms-3 me-2" style="width: 80px;">
                                                <span class="text-muted small">(<?= htmlspecialchars($dd['quoc_gia']) ?>)</span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <small class="text-muted mt-2 d-block">
                                * Địa điểm đã gán sẽ không hiển thị lại trong danh sách này (Logic lọc phải nằm ở Controller/Model).
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="ghi_chu" class="form-label fw-bold">Ghi chú chung (tuỳ chọn)</label>
                            <textarea class="form-control" name="ghi_chu" id="ghi_chu"
                                        rows="2" placeholder="Áp dụng ghi chú này cho tất cả địa điểm vừa chọn..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 shadow-sm mt-3">
                            <i class="bi bi-plus-circle me-1"></i> Gắn địa điểm đã chọn
                        </button>
                        
                    </form>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center gap-2 mt-4">
                
                <a href="<?= BASEURL ?>?act=uppdatetour&tour_id=<?= $tour_id_safe ?>" 
                   class="btn btn-nav-action btn-lg">
                    <i class="bi bi-chevron-left me-2"></i> Quay lại: Cập nhật Tour
                </a>
                
                <div class="d-flex gap-2">
                    <a href="<?= BASEURL ?>?act=chitiettour&tour_id=<?= $tour_id_safe ?>" 
                       class="btn btn-skip-view btn-lg">
                        <i class="bi bi-eye me-2"></i> BỎ QUA & Về Xem Chi tiết Tour
                    </a>
                    
                    <a href="<?= BASEURL ?>?act=addlichtrinh&tour_id=<?= $tour_id_safe ?>" 
                       class="btn btn-next-step btn-lg">
                        <i class="bi bi-chevron-right me-2"></i> Tiếp theo: Gắn Lịch trình
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>
</html>