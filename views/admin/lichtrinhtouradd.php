<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Lịch Trình Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container-fluid mt-4">
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
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📋 Danh Sách Lịch Trình Tour #<?= htmlspecialchars($tour['tour_id']) ?>: <?= htmlspecialchars($tour['ten']) ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle bg-white shadow-sm">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Ngày Thứ</th>
                                    <th>Tiêu Đề</th>
                                    <th>Nội Dung</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Giả định $data là danh sách lịch trình đã được Model trả về
                                if (!empty($data)): 
                                ?>
                                    <?php foreach ($data as $index => $lichTrinh): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td class="text-center">Ngày **<?= htmlspecialchars($lichTrinh['ngay_thu']) ?>**</td>
                                            <td>**<?= htmlspecialchars($lichTrinh['tieu_de']) ?>**</td>
                                            <td><?= nl2br(htmlspecialchars($lichTrinh['noi_dung'])) ?></td>
                                            <td class="text-center">
                                                <div class="d-grid gap-2">
                                                    <a href="<?= BASEURL ?>?act=editlichtrinh&lich_trinh_id=<?= $lichTrinh['lich_trinh_id'] ?>&tour_id=<?= $lichTrinh['tour_id'] ?>" class="btn btn-warning btn-sm">
                                                        <i class="bi bi-pencil-square"></i> Sửa
                                                    </a>
                                                    <a href="<?= BASEURL ?>?act=deletelichtrinh&lich_trinh_id=<?= $lichTrinh['lich_trinh_id'] ?>&tour_id=<?= $lichTrinh['tour_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xoá lịch trình này?')">
                                                        <i class="bi bi-trash"></i> Xoá
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Chưa có lịch trình tour nào</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">➕ Thêm Lịch Trình Mới</h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASEURL ?>?act=createlichtrinh&tour_id=<?= $tour['tour_id'] ?>" method="POST">
                        
                        <input type="hidden" name="tour_id" value="<?= htmlspecialchars($tour['tour_id']) ?>">

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Ngày Thứ</label>
                                <input type="number" name="ngay_thu" class="form-control" placeholder="VD: 1, 2, 3..." required>
                            </div>
                            <div class="col-md-9 mb-3">
                                <label class="form-label">Tiêu Đề</label>
                                <input type="text" name="tieu_de" class="form-control" placeholder="Nhập tiêu đề lịch trình (VD: Tham quan Vịnh Hạ Long)" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nội Dung</label>
                            <textarea name="noi_dung" class="form-control" rows="4" placeholder="Nhập chi tiết hoạt động trong ngày (VD: 8h sáng: Ăn sáng, 9h: Khởi hành đi...) " required></textarea>
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <button class="btn btn-success">
                                <i class="bi bi-save"></i> Lưu Lịch Trình
                            </button>
                            <a href="<?= BASEURL ?>?act=tour_list" class="btn btn-secondary">
                                <i class="bi bi-list-ul"></i> HOÀN TẤT & Về Tour List
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>