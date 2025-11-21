<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chính sách Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid py-5">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white text-center">
                    <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Danh mục quản lý</h6>
                </div>
                <div class="card-body px-3 py-4">
                    <div class="d-grid gap-3">
                        <a href="<?= BASEURL ?>?act=ncc_list" class="btn btn-outline-primary">
                            <i class="bi bi-building"></i> Nhà cung cấp
                        </a>
                        <a href="<?= BASEURL ?>?act=hdv" class="btn btn-outline-primary">
                            <i class="bi bi-person-badge"></i> Hướng dẫn viên
                        </a>
                        <a href="<?= BASEURL ?>?act=admin" class="btn btn-outline-primary">
                            <i class="bi bi-map"></i> Tour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Chính sách áp dụng cho Tour #<?= htmlspecialchars($tour_id) ?></h5>
                </div>
                <div class="card-body">
                    <?php if (empty($chinhSachList)): ?>
                        <div class="alert alert-warning text-center">
                            Chưa có chính sách nào được áp dụng cho tour này.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center bg-white shadow-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Tên chính sách</th>
                                        <th>Loại</th>
                                        <th>Ghi chú</th>
                                        <th>Hoạt động</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($chinhSachList as $index => $cs): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><strong><?= htmlspecialchars($cs['ten']) ?></strong></td>
                                            <td><span class="badge bg-secondary"><?= $cs['loai'] ?></span></td>
                                            <td><?= nl2br(htmlspecialchars($cs['ghi_chu'])) ?></td>
                                            <td>
                                                <?= $cs['hoat_dong'] ? '<span class="text-success">✔</span>' : '<span class="text-danger">✘</span>' ?>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($cs['ngay_tao'])) ?></td>
                                            <td>
                                                <a href="?act=xoa_chinh_sach_tour&id=<?= $cs['tour_chinh_sach_id'] ?>&tour_id=<?= $tour_id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa chính sách này khỏi tour?')">
                                                    <i class="bi bi-trash"></i>
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
                    <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Gắn chính sách mới</h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASEURL ?>?act=luu_chinh_sach_tour&tour_id=<?= htmlspecialchars($tour_id) ?>" method="POST">
                        <input type="hidden" name="tour_id" value="<?= htmlspecialchars($tour_id) ?>">

                        <label class="form-label">Chọn (nhiều) Chính sách áp dụng:</label>
                        <div class="mb-3 border p-3 rounded bg-light" style="max-height: 280px; overflow-y: auto;">
                            <?php if (empty($danhsachchinhsach)): ?>
                                <p class="text-danger text-center">Không tìm thấy Chính sách nào để gắn.</p>
                            <?php else: ?>
                                <?php foreach($danhsachchinhsach as $cs): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="chinh_sach_ids[]" 
                                               value="<?= $cs['chinh_sach_id'] ?>" id="cs_<?= $cs['chinh_sach_id'] ?>">
                                        <label class="form-check-label" for="cs_<?= $cs['chinh_sach_id'] ?>">
                                            <strong><?= htmlspecialchars($cs['ten']) ?></strong> 
                                            <span class="badge bg-secondary"><?= $cs['loai'] ?></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="ghi_chu" class="form-label">Ghi chú chung (tuỳ chọn)</label>
                            <textarea class="form-control" name="ghi_chu" id="ghi_chu" rows="2" placeholder="Áp dụng ghi chú này cho tất cả các chính sách vừa chọn..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle me-1"></i> Gắn các Chính sách đã chọn
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-3 mt-4">
                <a href="<?= BASEURL ?>?act=gan_diadiem&tour_id=<?= htmlspecialchars($tour_id) ?>" 
                   class="btn btn-primary btn-lg">
                    <i class="bi bi-geo-alt me-2"></i> Quay lại gắn: Gán Địa điểm
                </a>
                
                <a href="<?= BASEURL ?>?act=admin" 
                   class="btn btn-secondary btn-lg">
                    <i class="bi bi-list-ul me-2"></i> HOÀN TẤT (Về Tour List)
                </a>
                <a href="<?= BASEURL ?>?act=addlichtrinh&tour_id=<?= htmlspecialchars($tour_id) ?>" 
                   class="btn btn-secondary btn-lg">
                    <i class="bi bi-list-ul me-2"></i> Thêm lịch trình
                </a>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>