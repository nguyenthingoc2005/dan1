<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách Tour</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <link rel="stylesheet" href="./assets/css/sidebar.css">
</head>
<body class="bg-light">

    <?php include './views/parts/sidebar.php'; ?>

    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid py-4">
          <br>
          <br>
          <br>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">📋 Danh sách Tour</h2>
                <a href="<?= BASEURL ?>?act=addtour" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Thêm tour mới
                </a>
            </div>

            <?php if (!empty($data1)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center bg-white shadow-sm">
                        <thead class="table-primary">
                            <tr>
                                <th>ID</th>
                                <th>Tên tour</th>
                                <th>Danh mục</th>
                                <th>Giá cơ bản</th>
                                <th>Mô tả ngắn</th>
                                <th>Mô tả</th>
                                <th>Thời lượng</th>
                                <th>Điểm khởi hành</th>
                                <th>Hoạt động</th> <th>Ngày tạo</th>
                                <th colspan="2">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data1 as $tour): ?>
                                <tr>
                                    <td><?= htmlspecialchars($tour['tour_id']) ?></td>
                                    <td><?= htmlspecialchars($tour['ten']) ?></td>
                                    <td><?= htmlspecialchars($tour['ten_danh_muc']) ?></td>
                                    <td><?= number_format($tour['gia_co_ban']) ?> VND</td>
                                    <td><?= htmlspecialchars($tour['mo_ta_ngan']) ?></td>
                                    <td><?= htmlspecialchars($tour['mo_ta']) ?></td>
                                    <td><?= htmlspecialchars($tour['thoi_luong_mac_dinh']) ?> ngày</td>
                                    <td><?= htmlspecialchars($tour['diem_khoi_hanh']) ?></td>
                                    <td>
                                        <span class="badge <?= $tour['hoat_dong'] ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= $tour['hoat_dong'] ? 'Đang hoạt động' : 'Tạm dừng' ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($tour['ngay_tao']) ?></td>
                                    
                                    <td>
                                        <a href="<?= BASEURL ?>?act=uppdatetour&tour_id=<?= htmlspecialchars($tour['tour_id']) ?>" class="btn btn-warning btn-sm w-100 mb-1">
                                            <i class="bi bi-pencil-square"></i> Sửa
                                        </a>
                                        <a href="<?= BASEURL ?>?act=deletetour&tour_id=<?= htmlspecialchars($tour['tour_id']) ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa tour này không?')" class="btn btn-danger btn-sm w-100">
                                            <i class="bi bi-trash"></i> Xóa
                                        </a>
                                    </td>
                                    
                                    <td>
                                        <a href="<?= BASEURL ?>?act=chitiettour&tour_id=<?= htmlspecialchars($tour['tour_id']) ?>" class="btn btn-info btn-sm w-100">
                                            <i class="bi bi-geo-alt"></i> Chi tiết
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Không có tour nào.</div>
                <a href="<?= BASEURL ?>?act=addtour" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Thêm tour đầu tiên
                </a>
            <?php endif; ?>
        </div>
    </div>

    <script src="./assets/js/sidebar.js"></script>
</body>
</html>