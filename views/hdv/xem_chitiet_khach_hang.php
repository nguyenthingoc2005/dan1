<?php
// Kiểm tra dữ liệu đầu vào
$khachhang = $khachhang ?? [];
$listYeuCau = $listYeuCau ?? [];

if (empty($khachhang)) {
    echo "<div class='p-5 text-center'>Không có dữ liệu hiển thị. <a href='javascript:history.back()'>Quay lại</a></div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết khách hàng: <?= htmlspecialchars($khachhang['ho_ten']) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <link rel="stylesheet" href="./assets/css/sidebar.css">

    <style>
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', sans-serif;
        }

        .main-content {
            margin-left: 0;
            padding: 30px;
            min-height: 100vh;
        }

        .card-profile {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .profile-header {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            color: white;
            padding: 30px;
        }

        .avatar-circle {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-right: 20px;
        }

        .info-label {
            color: #6c757d;
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .info-value {
            color: #212529;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <?php include './views/parts/sidebarhdv.php'; ?>

    <div class="main-content">
        <div class="container-fluid">

            <div class="mb-4">
                <a href="<?= BASEURL ?>?act=chi_tiet_tour_hdv&lich_id=<?= $khachhang['lich_id'] ?>" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-2"></i>Quay lại danh sách
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-9">

                    <div class="card card-profile">
                        <div class="profile-header d-flex align-items-center">
                            <div class="avatar-circle"><i class="bi bi-person"></i></div>
                            <div>
                                <h3 class="mb-1 fw-bold"><?= htmlspecialchars($khachhang['ho_ten']) ?></h3>
                                <p class="mb-0 opacity-75">Booking ID: #<?= $khachhang['dat_tour_id'] ?></p>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="info-label">Giới tính</div>
                                        <div class="info-value">
                                            <?php if (strtolower($khachhang['gioi_tinh']) == 'nam'): ?>
                                                <span class="text-primary"><i class="bi bi-gender-male me-1"></i>Nam</span>
                                            <?php else: ?>
                                                <span class="text-danger"><i class="bi bi-gender-female me-1"></i>Nữ</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="info-label">Ngày sinh</div>
                                        <div class="info-value"><i class="bi bi-cake2 me-2 text-warning"></i><?= date('d/m/Y', strtotime($khachhang['ngay_sinh'])) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="info-label">Số điện thoại</div>
                                        <div class="info-value"><a href="tel:<?= $khachhang['lien_he'] ?>" class="text-decoration-none"><i class="bi bi-telephone-fill me-2 text-success"></i><?= htmlspecialchars($khachhang['lien_he']) ?></a></div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="info-label">Số CCCD / Passport</div>
                                        <div class="info-value"><?= htmlspecialchars($khachhang['so_giay_to'] ?? '---') ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-light border mt-2">
                                <strong><i class="bi bi-chat-quote-fill me-2 text-secondary"></i> Ghi chú đặt vé (Khách hàng):</strong>
                                <span class="text-dark fst-italic"><?= htmlspecialchars($khachhang['yeu_cau_ca_nhan'] ?: 'Không có ghi chú nào.') ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-danger fw-bold"><i class="bi bi-journal-check me-2"></i>Yêu Cầu Phục Vụ (HDV)</h5>
                            <button class="btn btn-danger btn-sm rounded-pill shadow-sm fw-bold px-3"
                                data-bs-toggle="modal" data-bs-target="#modalRequest"
                                onclick="resetModal()">
                                <i class="bi bi-plus-lg me-1"></i> Thêm Yêu Cầu
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Nội dung chi tiết</th>
                                            <th>Mức độ</th>
                                            <th>Trạng thái</th>
                                            <th>Ghi chú xử lý</th>
                                            <th class="text-end pe-4">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($listYeuCau)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-clipboard-x fs-1 opacity-25"></i>
                                                    <p class="mt-2 mb-0">Chưa có yêu cầu phục vụ nào được tạo.</p>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($listYeuCau as $yc): ?>
                                                <tr>
                                                    <td class="ps-4 fw-medium text-dark"><?= htmlspecialchars($yc['noi_dung']) ?></td>
                                                    <td>
                                                        <?php
                                                        $color = match ($yc['muc_do_uu_tien']) {
                                                            'cao', 'khan_cap' => 'danger',
                                                            'thap' => 'secondary',
                                                            default => 'primary'
                                                        };
                                                        $label = match ($yc['muc_do_uu_tien']) {
                                                            'trung_binh' => 'Trung bình',
                                                            'khan_cap' => 'Khẩn cấp',
                                                            'thap' => 'Thấp',
                                                            'cao' => 'Cao',
                                                            default => ucfirst($yc['muc_do_uu_tien'])
                                                        };
                                                        ?>
                                                        <span class="badge bg-<?= $color ?> bg-opacity-10 text-<?= $color ?> border border-<?= $color ?>"><?= $label ?></span>
                                                    </td>
                                                    <td>
                                                        <?php if ($yc['da_chuan_bi']): ?>
                                                            <span class="text-success small fw-bold"><i class="bi bi-check-circle-fill"></i> Đã chuẩn bị</span>
                                                        <?php else: ?>
                                                            <span class="text-secondary small"><i class="bi bi-circle"></i> Chưa xong</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-muted small fst-italic"><?= htmlspecialchars($yc['ghi_chu'] ?? '---') ?></td>
                                                    <td class="text-end pe-4">
                                                        <button class="btn btn-sm btn-light text-primary border me-1"
                                                            data-bs-toggle="modal" data-bs-target="#modalRequest"
                                                            data-id="<?= $yc['yeu_cau_id'] ?>"
                                                            data-content="<?= htmlspecialchars($yc['noi_dung']) ?>"
                                                            data-priority="<?= $yc['muc_do_uu_tien'] ?>"
                                                            data-prepared="<?= $yc['da_chuan_bi'] ?>"
                                                            data-note="<?= htmlspecialchars($yc['ghi_chu']) ?>"
                                                            onclick="editRequest(this)">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <a href="<?= BASEURL ?>?act=delete_service_request&req_id=<?= $yc['yeu_cau_id'] ?>&hk_id=<?= $khachhang['hanh_khach_id'] ?>"
                                                            class="btn btn-sm btn-light text-danger border"
                                                            onclick="return confirm('Bạn có chắc chắn muốn xóa yêu cầu này?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
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

    <?php require_once 'views/hdv/add_yeu_cau_khach_hang.php'; ?>

</body>

</html>