<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản Lý Khách Hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/css/sidebar.css">

    <style>
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', sans-serif;
        }

        .main-content {
            padding: 30px;
            margin-top: 70px;
            margin-left: 0;
            min-height: 100vh;
        }

        /* SEARCH BOX */
        .search-box .form-control {
            border-radius: 20px;
            padding-left: 40px;
            border: 1px solid #e9ecef;
            background-color: #f8f9fa;
        }

        .search-box .bi-search {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
        }

        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            background: #fff;
            overflow: hidden;
        }

        .table-custom thead th {
            background-color: #f8f9fa;
            color: #6c757d;
            font-weight: 600;
            font-size: 0.75rem;
            border-bottom: 2px solid #e9ecef;
            padding: 15px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .table-custom tbody td {
            vertical-align: middle;
            padding: 15px;
            border-bottom: 1px solid #f1f1f1;
            font-size: 0.9rem;
        }

        /* Avatar Styles */
        .avatar-wrapper {
            width: 45px;
            height: 45px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .avatar-circle {
            width: 100%;
            height: 100%;
            background-color: #e7f1ff;
            color: #0d6efd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
        }

        /* Badges */
        .badge-soft {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-soft-primary {
            background-color: #cff4fc;
            color: #055160;
        }

        .badge-soft-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .badge-soft-warning {
            background-color: #fff3cd;
            color: #664d03;
        }

        .badge-soft-danger {
            background-color: #f8d7da;
            color: #842029;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #6c757d;
            background: #f8f9fa;
            transition: all 0.2s;
        }

        .btn-icon:hover {
            transform: translateY(-2px);
        }

        .btn-icon.edit:hover {
            color: #ffc107;
            background: #fff3cd;
        }

        .btn-icon.delete:hover {
            color: #dc3545;
            background: #f8d7da;
        }
    </style>
</head>

<body>

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Danh Sách Khách Hàng</h3>
                    <p class="text-muted mb-0">Quản lý thông tin tài khoản và hồ sơ cá nhân khách hàng.</p>
                </div>
                <a href="index.php?act=user_create" class="btn btn-primary shadow-sm px-4">
                    <i class="bi bi-person-plus-fill me-2"></i> Thêm Khách Hàng
                </a>
            </div>

            <div class="card card-custom mb-4">
                <div class="card-body py-3">
                    <form action="" method="GET" class="row g-3 align-items-center">
                        <input type="hidden" name="act" value="khachhang_list">
                        <div class="col-md-6">
                            <div class="position-relative search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" name="keyword" class="form-control"
                                    placeholder="Tìm kiếm theo tên, email, SĐT..."
                                    value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-light w-100 border text-secondary fw-medium">
                                <i class="bi bi-funnel"></i> Lọc
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-custom">
                <div class="card-body p-0">
                    <?php if (!empty($data)): ?>
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Thông tin khách hàng</th>
                                        <th>Liên hệ</th>
                                        <th>Giấy tờ tùy thân</th>
                                        <th>Trạng thái</th>
                                        <th class="text-end pe-4">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data as $kh): ?>
                                        <?php
                                        // Lấy chữ cái đầu của tên để làm avatar mặc định
                                        $char = strtoupper(substr($kh['ho_ten'] ?? 'K', 0, 1));
                                        ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar-circle"><?= $char ?></div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= htmlspecialchars($kh['ho_ten'] ?? 'Chưa cập nhật') ?></div>
                                                        <div class="small text-muted">
                                                            <?php if (isset($kh['gioi_tinh'])): ?>
                                                                <span class="me-2"><i class="bi bi-gender-ambiguous"></i> <?= $kh['gioi_tinh'] ?></span>
                                                            <?php endif; ?>
                                                            <?php if (isset($kh['ngay_sinh'])): ?>
                                                                <span><i class="bi bi-cake2"></i> <?= date('d/m/Y', strtotime($kh['ngay_sinh'])) ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="text-dark small fw-medium">
                                                        <i class="bi bi-telephone me-2 text-primary"></i> <?= htmlspecialchars($kh['so_dien_thoai'] ?? '---') ?>
                                                    </span>
                                                    <span class="text-muted small">
                                                        <i class="bi bi-envelope me-2"></i> <?= htmlspecialchars($kh['email'] ?? '---') ?>
                                                    </span>
                                                    <span class="text-muted small text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($kh['dia_chi'] ?? '') ?>">
                                                        <i class="bi bi-geo-alt me-2"></i> <?= htmlspecialchars($kh['dia_chi'] ?? 'Chưa có địa chỉ') ?>
                                                    </span>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="mb-1">
                                                    <span class="badge badge-soft badge-soft-primary">
                                                        CCCD: <?= htmlspecialchars($kh['cccd'] ?? 'Chưa có') ?>
                                                    </span>
                                                </div>
                                            </td>

                                            <td>
                                                <?php
                                                // Giả sử cột trạng thái từ bảng NguoiDung (Active/Inactive)
                                                $status = $kh['trang_thai'] ?? 'active';
                                                if ($status == 'active' || $status == 1) {
                                                    echo '<span class="badge badge-soft-success">Hoạt động</span>';
                                                } else {
                                                    echo '<span class="badge badge-soft-danger">Đã khóa</span>';
                                                }
                                                ?>
                                            </td>

                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="index.php?act=edit_khach_hang&khach_hang_id=<?= $kh['khach_hang_id'] ?>"
                                                        class="btn-icon edit" title="Sửa thông tin">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-people display-1 opacity-25"></i>
                            <p class="mt-3">Chưa có dữ liệu khách hàng nào.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <script src="./assets/js/sidebar.js"></script>
</body>

</html>