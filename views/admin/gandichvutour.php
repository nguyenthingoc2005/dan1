<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Gán Dịch Vụ Tour</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        /* CSS cho các nút điều hướng (Giống các trang trước) */
        /* Nút Quay lại/Hành động Phụ (Secondary) */
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

        /* Nút Xem Chi tiết Tour (Primary) */
        .btn-view-detail {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
            font-weight: 600;
        }
        .btn-view-detail:hover {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
            color: white;
        }

        /* Nút HOÀN TẤT & Về Tour List (Kết thúc quy trình) */
        .btn-complete {
            background-color: #198754; /* Xanh lá */
            border-color: #198754;
            color: white;
            font-weight: 600;
        }
        .btn-complete:hover {
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
            <div class="row justify-content-center">
                <div class="col-md-12">
                    
                    <?php 
                        // Giả định $tour_id đã được định nghĩa
                        $tour_id_safe = htmlspecialchars($tour_id ?? 2);
                    ?>

                    <h2 class="mb-4 text-info">
                        <i class="bi bi-tools me-2"></i> Quản Lý Gán Dịch Vụ Tour #<?= $tour_id_safe ?>
                    </h2>

                    <div class="card shadow border-0 mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-gear-wide-connected me-2"></i>
                                Dịch vụ đã gán cho Tour #<?= $tour_id_safe ?>
                            </h5>
                        </div>

                        <div class="card-body">
                            <?php if (empty($dichVuDaGan)): ?>
                                <div class="alert alert-warning text-center">
                                    Tour này chưa có dịch vụ nào được gán.
                                </div>

                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle bg-white shadow-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Dịch vụ</th>
                                                <th>Giá</th>
                                                <th>Nhà cung cấp</th>
                                                <th>Ghi chú</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php foreach ($dichVuDaGan as $index => $dv): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td><strong><?= htmlspecialchars($dv['ten_dich_vu']) ?></strong></td>
                                                    <td><?= number_format($dv['gia_mac_dinh']) ?> VND</td>
                                                    <td>
                                                        <span class="badge bg-secondary">
                                                            <?= htmlspecialchars($dv['nha_cung_cap'] ?? "Không rõ") ?>
                                                        </span>
                                                    </td>
                                                    <td><?= nl2br(htmlspecialchars($dv['ghi_chu'] ?? "")) ?></td>
                                                    <td class="text-center">
                                                        <a href="<?= BASEURL ?>?act=XoaGanDichVuTour&dich_vu_id=<?= $dv['gia_dv_id'] ?>&tour_id=<?= $tour_id_safe ?>"
                                                            onclick="return confirm('Bạn có chắc chắn muốn xóa dịch vụ này khỏi tour?')"
                                                            class="btn btn-danger btn-sm">
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
                                <i class="bi bi-plus-circle me-2"></i>
                                Gán dịch vụ mới cho Tour #<?= $tour_id_safe ?>
                            </h5>
                        </div>

                        <div class="card-body">
                            <form action="<?= BASEURL ?>?act=luuGanDichVuTour&tour_id=<?= $tour_id_safe ?>" method="POST">

                                <input type="hidden" name="tour_id" value="<?= $tour_id_safe ?>">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Chọn dịch vụ (có thể chọn nhiều)</label>

                                    <div class="border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto;">

                                        <?php if (empty($data)): ?>
                                            <p class="text-danger">Không có dịch vụ nào để gán.</p>

                                        <?php else: ?>
                                            <?php foreach ($data as $dv): ?>
                                                <div class="form-check mb-3">

                                                    <input class="form-check-input"
                                                        type="checkbox"
                                                        name="dich_vu_id[]"
                                                        value="<?= $dv['dich_vu_id'] ?>"
                                                        id="dv_<?= $dv['dich_vu_id'] ?>">

                                                    <label class="form-check-label" for="dv_<?= $dv['dich_vu_id'] ?>">
                                                        <strong><?= $dv['ten_dich_vu'] ?></strong>
                                                        <span class="text-muted small">(<?= number_format($dv['gia_mac_dinh']) ?> VND)</span>
                                                    </label>

                                                    <div class="mt-1 d-flex align-items-center">
                                                        <input type="number"
                                                            name="thu_tu[<?= $dv['dich_vu_id'] ?>]"
                                                            class="form-control form-control-sm"
                                                            placeholder="Thứ tự"
                                                            style="width: 80px;">
                                                    </div>

                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                    </div>

                                    <small class="text-muted">
                                        * Dịch vụ đã gán sẽ không xuất hiện lại ở danh sách này (xử lý ở Controller/Model).
                                    </small>
                                </div>

                                <div class="mb-3">
                                    <label for="ghi_chu" class="form-label">Ghi chú chung (tùy chọn)</label>
                                    <textarea class="form-control" name="ghi_chu" id="ghi_chu"
                                        rows="2" placeholder="Ghi chú áp dụng cho toàn bộ dịch vụ được chọn..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-plus-circle me-1"></i> Gán dịch vụ đã chọn
                                </button>

                            </form>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center gap-3 mt-4">
                        
                        <a href="<?= BASEURL ?>?act=chinhsach&tour_id=<?= $tour_id_safe ?>" 
                           class="btn btn-nav-action btn-lg">
                            <i class="bi bi-chevron-left me-2"></i> Quay lại: Gắn Chính sách
                        </a>

                        <div class="d-flex gap-2">
                            <a href="<?= BASEURL ?>?act=chitiettour&tour_id=<?= $tour_id_safe ?>" 
                               class="btn btn-view-detail btn-lg">
                                <i class="bi bi-eye me-2"></i> Về Xem Chi tiết Tour
                            </a>

                            <a href="<?= BASEURL ?>?act=tour_list" 
                               class="btn btn-complete btn-lg">
                                <i class="bi bi-check-circle me-2"></i> HOÀN TẤT & Về Tour List
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>
</html>