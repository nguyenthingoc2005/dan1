<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Chính Sách Tour</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        /* Nút Tiếp theo (Primary) */
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

        /* Nút Quay lại/Hành động Phụ (Xem chi tiết) */
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
        
        /* Nút HOÀN TẤT & VỀ TOUR LIST (Kết thúc quy trình tạo tour) */
        .btn-complete {
            background-color: #dc3545; /* Đỏ - Cảnh báo kết thúc quá trình */
            border-color: #dc3545;
            color: white;
            font-weight: 600;
        }
        .btn-complete:hover {
            background-color: #c82333;
            border-color: #bd2130;
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
                $tour_id_safe = htmlspecialchars($tour_id ?? 2);
            ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0 text-info">
                    <i class="bi bi-shield-check me-2"></i> Quản Lý Chính Sách Tour #<?= $tour_id_safe ?>
                </h2>
            </div>

            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-list-check me-2"></i> Chính sách đã áp dụng</h5>
                </div>
                <div class="card-body">
                    <?php 
                    // Giả định biến $chinhSachList chứa dữ liệu từ DB
                    if (empty($chinhSachList)): 
                    ?>
                        <div class="alert alert-warning text-center">
                            Chưa có chính sách nào được áp dụng cho tour này.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center bg-white shadow-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 25%;">Tên chính sách</th>
                                        <th style="width: 10%;">Loại</th>
                                        <th style="width: 30%;">Ghi chú</th>
                                        <th style="width: 10%;">Hoạt động</th>
                                        <th style="width: 10%;">Ngày tạo</th>
                                        <th style="width: 10%;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($chinhSachList as $index => $cs): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><strong><?= htmlspecialchars($cs['ten']) ?></strong></td>
                                            <td><span class="badge bg-secondary"><?= $cs['loai'] ?></span></td>
                                            <td class="text-start small"><?= nl2br(htmlspecialchars($cs['ghi_chu'])) ?></td>
                                            <td>
                                                <?= $cs['hoat_dong'] ? '<span class="text-success fw-bold">✔</span>' : '<span class="text-danger fw-bold">✘</span>' ?>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($cs['ngay_tao'])) ?></td>
                                            <td>
                                                <a href="?act=xoa_chinh_sach_tour&id=<?= $cs['tour_chinh_sach_id'] ?>&tour_id=<?= $tour_id_safe ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xoá chính sách này khỏi tour?')">
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

            <div class="card shadow border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i> Gắn Chính sách mới</h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASEURL ?>?act=luu_chinh_sach_tour&tour_id=<?= $tour_id_safe ?>" method="POST">
                        <input type="hidden" name="tour_id" value="<?= $tour_id_safe ?>">

                        <label class="form-label fw-bold">Chọn (nhiều) Chính sách áp dụng:</label>
                        <div class="mb-3 border p-3 rounded bg-light" style="max-height: 280px; overflow-y: auto;">
                            <?php 
                            // Giả định biến $danhsachchinhsach chứa danh sách chính sách có thể chọn
                            if (empty($danhsachchinhsach)): 
                            ?>
                                <p class="text-danger text-center">Không tìm thấy Chính sách nào để gắn.</p>
                            <?php else: ?>
                                <?php foreach($danhsachchinhsach as $cs): ?>
                                    <div class="form-check my-2">
                                        <input class="form-check-input" type="checkbox" name="chinh_sach_ids[]" 
                                               value="<?= $cs['chinh_sach_id'] ?>" id="cs_<?= $cs['chinh_sach_id'] ?>">
                                        <label class="form-check-label" for="cs_<?= $cs['chinh_sach_id'] ?>">
                                            <strong><?= htmlspecialchars($cs['ten']) ?></strong> 
                                            <span class="badge bg-secondary ms-2"><?= $cs['loai'] ?></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="ghi_chu" class="form-label fw-bold">Ghi chú chung (tuỳ chọn)</label>
                            <textarea class="form-control" name="ghi_chu" id="ghi_chu" rows="2" placeholder="Áp dụng ghi chú này cho tất cả các chính sách vừa chọn..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 shadow-sm mt-3">
                            <i class="bi bi-check-circle me-1"></i> Gắn các Chính sách đã chọn
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center gap-2 mt-4">
                
                <a href="<?= BASEURL ?>?act=addlichtrinh&tour_id=<?= $tour_id_safe ?>" 
                   class="btn btn-nav-action btn-lg">
                    <i class="bi bi-chevron-left me-2"></i> Quay lại: Gán Lịch trình
                </a>
                
                <div class="d-flex gap-2">
                    <a href="<?= BASEURL ?>?act=gandichvu&tour_id=<?= $tour_id_safe ?>" 
                       class="btn btn-next-step btn-lg">
                        <i class="bi bi-chevron-right me-2"></i> Tiếp theo: Gán Dịch vụ
                    </a>
                    
                    <a href="<?= BASEURL ?>?act=tour_list" 
                       class="btn btn-complete btn-lg">
                        <i class="bi bi-box-arrow-right me-2"></i> HOÀN TẤT & Về Tour List
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>
</html>