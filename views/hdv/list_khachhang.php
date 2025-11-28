<?php
// Bắt đầu bằng việc include file dữ liệu giả lập


// Các liên kết ví dụ cho nút Thao tác (chỉ mang tính minh họa)
$detail_page = "chitiet.php";
$update_page = "capnhat.php";
$delete_action = "xoa.php";

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Khách Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">

    <link rel="stylesheet" href="./assets/css/sidebar.css">
    <style>
        /* CSS tùy chỉnh bổ sung */
        .badge.py-2 {
            min-width: 120px;
            /* Đảm bảo chiều rộng đồng đều cho badge */
        }


        .main-content {
            margin-left: 0;
            padding: 30px;
            transition: margin-left .32s ease;
            min-height: 100vh;
        }

        .table-tour-header-custom {
            background-color: #0d6efd !important;
            color: white !important;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tour-table-container {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            background-color: #fff;
        }

        .table> :not(caption)>*>* {
            padding: 1rem 1rem;
        }

        .table tbody tr:hover {
            background-color: #e9ecef;
        }

        .col-price {
            text-align: right;
            font-weight: bold;
            color: #198754;
        }

        .col-duration {
            text-align: center;
        }

        /* Căn giữa cột hành động */
        .col-action {
            text-align: center;
            width: 100px;
        }
    </style>
    </style>
</head>

<body>
    <?php include './views/parts/sidebarhdv.php'; ?>

    <div class="main-content">
        <div class="container mt-5">
            <h3 class="mb-4 border-start border-4 border-primary ps-3">
                Danh sách khách hàng của tour:
                <span class="badge bg-primary fs-6"><?= count($khachhang ?? []) ?> khách</span>
            </h3>

            <div class="card shadow border-0">
                <div class="card-body p-0">

                    <table class="table table-hover align-middle table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Họ tên</th>
                                <th class="text-nowrap">SĐT</th>
                                <th>Email</th>
                                <th>Ghi chú</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($khachhang)): ?>
                                <?php $i = 1;
                                foreach ($khachhang as $kh): ?>
                                    <tr>
                                        <td class="text-center"><?= $i++ ?></td>
                                        <td><?= htmlspecialchars($kh["ho_ten"] ?? '') ?></td>
                                        <td class="text-nowrap"><?= htmlspecialchars($kh["so_dien_thoai"] ?? '') ?></td>
                                        <td><?= htmlspecialchars($kh["email"] ?? '') ?></td>
                                        <td>
                                            <?= !empty($kh["ghichu"]) ? htmlspecialchars($kh["ghichu"]) : '<span class="text-muted fst-italic">N/A</span>' ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (($kh["trang_thai"] ?? 0) == 1): ?>
                                                <span class="badge bg-success py-2"><i class="bi bi-check-circle-fill"></i> Đã xác nhận</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark py-2"><i class="bi bi-hourglass-split"></i> Chờ xác nhận</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <a href="<?= $detail_page ?>?id=<?= $kh["khach_hang_id"] ?? '' ?>" class="btn btn-sm btn-info text-white" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= $update_page ?>?id=<?= $kh["khach_hang_id"] ?? '' ?>" class="btn btn-sm btn-warning" title="Cập nhật">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?= $delete_action ?>?id=<?= $kh["khach_hang_id"] ?? '' ?>"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng <?= htmlspecialchars($kh['ho_ten'] ?? 'này') ?>?')"
                                                class="btn btn-sm btn-danger" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-info-circle"></i> Không tìm thấy khách hàng nào trong tour này.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>

</html>