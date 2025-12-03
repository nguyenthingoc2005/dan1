<?php
// 1. Kiểm tra dữ liệu đầu vào
$khachhang = $khachhang ?? [];
$listYeuCau = $listYeuCau ?? [];

// Nếu không có dữ liệu khách (hoặc sai tên biến), hiện thông báo đẹp
if (empty($khachhang)) {
    echo "<div class='container d-flex justify-content-center align-items-center vh-100'>
            <div class='text-center'>
                <i class='bi bi-search display-1 text-muted mb-3'></i>
                <h4 class='fw-bold text-secondary'>Không tìm thấy dữ liệu!</h4>
                <p class='text-muted'>Vui lòng kiểm tra lại ID trên đường dẫn.</p>
                <a href='index.php?act=list_tour' class='btn btn-primary rounded-pill px-4 mt-3 shadow-sm'>
                    <i class='bi bi-arrow-left me-2'></i>Quay lại
                </a>
            </div>
          </div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ: <?= htmlspecialchars($khachhang['ho_ten']) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">

    <link rel="stylesheet" href="./assets/css/sidebar.css">

    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Poppins', sans-serif;
            color: #5a5c69;
        }

        .main-content {
            padding: 2rem;
        }

        /* Thẻ Card đẹp */
        .card-custom {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            background: #fff;
        }

        /* Phần Header Profile */
        .profile-cover {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            height: 100px;
            border-radius: 1rem 1rem 0 0;
        }

        .avatar-wrapper {
            margin-top: -50px;
            text-align: center;
        }

        .avatar-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid #fff;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: #4e73df;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Badge màu mè (Soft UI) */
        .badge-soft {
            padding: 0.5em 0.8em;
            border-radius: 50rem;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .bg-soft-primary {
            background: rgba(78, 115, 223, 0.1);
            color: #4e73df;
        }

        .bg-soft-success {
            background: rgba(28, 200, 138, 0.1);
            color: #1cc88a;
        }

        .bg-soft-danger {
            background: rgba(231, 74, 59, 0.1);
            color: #e74a3b;
        }

        .bg-soft-warning {
            background: rgba(246, 194, 62, 0.1);
            color: #f6c23e;
        }

        /* Table đẹp */
        .table thead th {
            border-bottom: 2px solid #e3e6f0;
            font-weight: 600;
            color: #4e73df;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        .table tbody td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: 0.2s;
        }

        .btn-icon:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <?php include './views/parts/sidebarhdv.php'; ?>

    <div class="main-content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Hồ Sơ Khách Hàng</h4>
                    <p class="text-muted small mb-0">Quản lý yêu cầu phục vụ riêng biệt</p>
                </div>
                <a href="index.php?act=list_tour" class="btn btn-light text-primary fw-bold shadow-sm rounded-pill px-4">
                    Quay lại
                </a>
            </div>

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card card-custom h-100">
                        <div class="profile-cover"></div>
                        <div class="card-body pt-0">
                            <div class="avatar-wrapper">
                                <div class="avatar-img"><i class="bi bi-person-fill"></i></div>
                                <h5 class="mt-2 fw-bold text-dark"><?= htmlspecialchars($khachhang['ho_ten']) ?></h5>
                                <span class="badge bg-light text-secondary border">ID: <?= $khachhang['hanh_khach_id'] ?></span>
                            </div>

                            <div class="mt-4 px-2">
                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-secondary small fw-bold">GIỚI TÍNH</span>
                                    <span class="fw-bold text-dark"><?= $khachhang['gioi_tinh'] ?? '---' ?></span>
                                </div>
                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-secondary small fw-bold">NGÀY SINH</span>
                                    <span class="fw-bold text-dark">
                                        <?= !empty($khachhang['ngay_sinh']) ? date('d/m/Y', strtotime($khachhang['ngay_sinh'])) : '---' ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-secondary small fw-bold">LIÊN HỆ</span>
                                    <span class="fw-bold text-primary"><?= htmlspecialchars($khachhang['lien_he'] ?? '---') ?></span>
                                </div>

                                <div class="mt-3 bg-warning bg-opacity-10 p-3 rounded-3 border border-warning border-opacity-25">
                                    <small class="text-warning fw-bold d-block mb-1"><i class="bi bi-sticky-fill"></i> GHI CHÚ TỪ KHÁCH:</small>
                                    <span class="small text-dark fst-italic">
                                        "<?= htmlspecialchars($khachhang['yeu_cau_ca_nhan'] ?? 'Không có ghi chú.') ?>"
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 mb-4">
                    <div class="card card-custom h-100">
                        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-primary text-uppercase"><i class="bi bi-list-task me-2"></i>Danh sách yêu cầu (HDV)</h6>
                            <button class="btn btn-primary btn-sm rounded-pill shadow-sm px-3 fw-bold"
                                data-bs-toggle="modal" data-bs-target="#modalRequest" onclick="resetModal()">
                                <i class="bi bi-plus-lg me-1"></i> Thêm mới
                            </button>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Nội dung</th>
                                            <th class="text-center">Mức độ</th>
                                            <th class="text-center">Trạng thái</th>
                                            <th>Ghi chú HDV</th>
                                            <th class="text-end pe-4">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($listYeuCau)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <i class="bi bi-clipboard-x text-muted display-6 d-block mb-2 opacity-50"></i>
                                                    <span class="text-muted small">Chưa có yêu cầu nào.</span>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($listYeuCau as $yc): ?>
                                                <tr>
                                                    <td class="ps-4 fw-bold text-dark"><?= htmlspecialchars($yc['noi_dung']) ?></td>
                                                    <td class="text-center">
                                                        <?php
                                                        $mucDo = $yc['muc_do_uu_tien'] ?? 'Vừa';
                                                        $cls = match ($mucDo) {
                                                            'Cao', 'Khẩn cấp' => 'bg-soft-danger',
                                                            'Thấp' => 'bg-soft-secondary',
                                                            default => 'bg-soft-primary'
                                                        };
                                                        ?>
                                                        <span class="badge-soft <?= $cls ?>"><?= $mucDo ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($yc['da_chuan_bi'] == 1): ?>
                                                            <span class="badge-soft bg-soft-success"><i class="bi bi-check2"></i> Xong</span>
                                                        <?php else: ?>
                                                            <span class="badge-soft bg-soft-warning">Chờ</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="small text-secondary fst-italic"><?= htmlspecialchars($yc['ghi_chu'] ?? '') ?></td>
                                                    <td class="text-end pe-4">
                                                        <a href="<? BASEURL ?>?act=xoa_yeu_cau&id" class="btn btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa')">Xóa</a>


                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>

</html>