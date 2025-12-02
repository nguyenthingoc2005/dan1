<?php
// Kiểm tra dữ liệu đầu vào để tránh lỗi
$khachhang = $khachhang ?? [];
if (empty($khachhang)) {
    echo "Không có dữ liệu hiển thị.";
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
                <a href="<?= BASEURL ?>?act=chi_tiet_tour&lich_id=<?= $khachhang['lich_id'] ?>" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-2"></i>Quay lại danh sách
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card card-profile">
                        <div class="profile-header d-flex align-items-center">
                            <div class="avatar-circle">
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold"><?= htmlspecialchars($khachhang['ho_ten']) ?></h3>
                                <p class="mb-0 opacity-75">
                                    <i class="bi bi-ticket-perforated me-1"></i>
                                    Mã vé/Booking ID: #<?= $khachhang['dat_tour_id'] ?>
                                </p>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <h5 class="text-primary fw-bold mb-4 border-bottom pb-2">
                                <i class="bi bi-person-lines-fill me-2"></i>Thông Tin Cá Nhân
                            </h5>

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
                                        <div class="info-value">
                                            <i class="bi bi-cake2 me-2 text-warning"></i>
                                            <?= date('d/m/Y', strtotime($khachhang['ngay_sinh'])) ?>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="info-label">Số CCCD / Passport</div>
                                        <div class="info-value">
                                            <?= htmlspecialchars($khachhang['so_giay_to'] ?? '---') ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="info-label">Số điện thoại</div>
                                        <div class="info-value">
                                            <a href="tel:<?= $khachhang['lien_he'] ?>" class="text-decoration-none">
                                                <i class="bi bi-telephone-fill me-2 text-success"></i>
                                                <?= htmlspecialchars($khachhang['lien_he']) ?>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="info-label">Trạng thái vé</div>
                                        <div class="info-value">
                                            <span class="badge bg-success px-3 py-2 rounded-pill">
                                                <?= htmlspecialchars($khachhang['trang_thai_don']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h5 class="text-danger fw-bold mt-4 mb-3 border-bottom pb-2">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>Lưu Ý Đặc Biệt
                            </h5>

                            <div class="alert <?= !empty($khachhang['yeu_cau_ca_nhan']) ? 'alert-warning' : 'alert-light text-muted' ?>">
                                <?php if (!empty($khachhang['yeu_cau_ca_nhan'])): ?>
                                    <i class="bi bi-stars me-2"></i>
                                    <strong>Yêu cầu:</strong> <?= htmlspecialchars($khachhang['yeu_cau_ca_nhan']) ?>
                                <?php else: ?>
                                    <i class="bi bi-check-circle me-2"></i>Khách hàng không có yêu cầu đặc biệt nào.
                                <?php endif; ?>
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