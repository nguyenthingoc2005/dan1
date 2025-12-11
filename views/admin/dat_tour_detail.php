<?php
// =================================================================================
// PHẦN 1: HELPER FUNCTIONS & XỬ LÝ AN TOÀN DỮ LIỆU
// =================================================================================

// 1. Kiểm tra dữ liệu đầu vào
if (empty($data) || !is_array($data)) {
    echo '<div class="container mt-5"><div class="alert alert-danger shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Không tìm thấy dữ liệu đơn đặt tour hoặc dữ liệu bị lỗi.
          </div></div>';
    return;
}

// 2. Hàm format tiền tệ an toàn
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount)
    {
        return number_format((float)($amount ?? 0), 0, ',', '.') . ' VNĐ';
    }
}

// 3. Hàm hiển thị trạng thái
if (!function_exists('renderStatusBadge')) {
    function renderStatusBadge($status)
    {
        $s = strtolower((string)($status ?? ''));
        return match ($s) {
            'hoàn tất', 'completed', 'success', 'paid' => '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Hoàn tất</span>',
            'chờ xác nhận', 'pending' => '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Chờ xác nhận</span>',
            'đã xác nhận', 'confirmed', 'đã đặt cọc' => '<span class="badge bg-info text-dark"><i class="bi bi-check2-all"></i> Đã xác nhận</span>',
            'đã hủy', 'cancelled', 'hủy' => '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Đã hủy</span>',
            '' => '<span class="badge bg-secondary">Chưa cập nhật</span>',
            default => '<span class="badge bg-secondary">' . htmlspecialchars($s) . '</span>',
        };
    }
}

// 4. Lấy dữ liệu an toàn
$danhSachHanhKhach = $data['danh_sach_hanh_khach'] ?? [];
$dichVuTour = $data['dich_vu_tour'] ?? [];
$huongDanVien = $data['huong_dan_vien'] ?? [];

// 5. Tính tổng giá dịch vụ phụ trợ
$tong_gia_dv_phu_tro = 0;
foreach ($dichVuTour as $dv) {
    $tong_gia_dv_phu_tro += (float)($dv['gia'] ?? 0);
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Đơn #<?= htmlspecialchars($data['dat_tour_id'] ?? 'N/A') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">

    <link rel="stylesheet" href="./assets/css/sidebar.css">

    <style>
        /* MAIN LAYOUT */
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

        /* CARD STYLE */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #eee;
            font-weight: 600;
            padding: 15px 20px;
        }

        /* TYPOGRAPHY */
        .text-primary-custom {
            color: #0d6efd !important;
            font-weight: 700;
        }

        .price-highlight {
            font-size: 1.3rem;
            font-weight: 800;
            color: #dc3545;
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
            min-width: 130px;
            display: inline-block;
        }

        /* TABS */
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.2s;
        }

        .nav-tabs .nav-link:hover {
            color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.05);
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            border-bottom: 3px solid #0d6efd;
            background: transparent;
        }

        /* TABLE & AVATAR */
        .table-middle td {
            vertical-align: middle;
        }

        .avatar-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 1.1rem;
        }
    </style>
</head>

<body class="bg-light">

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0 text-primary-custom">
                        <i class="bi bi-ticket-detailed"></i> Đơn Đặt Tour #<?= htmlspecialchars($data['dat_tour_id'] ?? 'N/A') ?>
                    </h2>
                    <div class="text-muted mt-1 small">
                        <i class="bi bi-calendar-event"></i> Ngày tạo:
                        <?= !empty($data['ngay_dat']) ? date('H:i - d/m/Y', strtotime($data['ngay_dat'])) : '---' ?>
                    </div>
                </div>
                <div>
                    <a href="<?= BASEURL ?>?act=dattourlist" class="btn btn-outline-secondary fw-bold">
                        <i class="bi bi-arrow-return-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <ul class="nav nav-tabs" id="bookingTabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#general">Thông Tin Chung</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#passengers">Hành Khách (<?= count($danhSachHanhKhach) ?>)</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#services">Dịch Vụ</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#guide">HDV & Lịch</button></li>
            </ul>

            <div class="tab-content pt-3">

                <div class="tab-pane fade show active" id="general">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span class="text-primary"><i class="bi bi-map"></i> Chi tiết Tour</span>
                                    <?php if (!empty($data['dat_tour_id'])): ?>
                                        <a href="<?= BASEURL ?>?act=dat_tour_edit&dat_tour_id=<?= $data['dat_tour_id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Sửa Đơn
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <h5 class="fw-bold text-dark mb-3">
                                        <?= htmlspecialchars($data['tour_info']['ten_tour'] ?? 'Tên tour không xác định') ?>
                                    </h5>

                                    <p><span class="info-label">Mã Tour:</span> #<?= htmlspecialchars($data['tour_info']['tour_id'] ?? 'N/A') ?></p>
                                    <p><span class="info-label">Thời lượng:</span> <?= htmlspecialchars($data['tour_info']['thoi_gian'] ?? '0') ?> ngày</p>

                                    <hr class="my-3">

                                    <div class="mb-2">
                                        <span class="info-label">Lịch trình:</span>
                                        <?php
                                        $ngayDi = $data['lich_khoi_hanh']['ngay_bat_dau'] ?? null;
                                        $ngayVe = $data['lich_khoi_hanh']['ngay_ket_thuc'] ?? null;
                                        ?>
                                        <?php if ($ngayDi): ?>
                                            <span class="fw-bold text-success">
                                                <?= date('d/m/Y', strtotime($ngayDi)) ?>
                                                <?= $ngayVe ? ' - ' . date('d/m/Y', strtotime($ngayVe)) : '' ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Chưa lên lịch</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mb-2 d-flex align-items-center">
                                        <span class="info-label">Tình trạng Lịch:</span>
                                        <?php
                                        $lich_id = $data['lich_khoi_hanh']['lich_id'] ?? null;
                                        $trang_thai_lich = $data['lich_khoi_hanh']['trang_thai'] ?? '';
                                        ?>
                                        <?php if (empty($lich_id)): ?>
                                            <span class="text-danger fst-italic me-2"><i class="bi bi-exclamation-circle"></i> Chưa có</span>
                                            <a href="<?= BASEURL ?>?act=add_schedule&tour_id=<?= $data['tour_info']['tour_id'] ?? '' ?>" class="btn btn-sm btn-primary py-0 px-2 fw-bold">Lên lịch ngay</a>
                                        <?php else: ?>
                                            <?php if ($trang_thai_lich == 0): ?>
                                                <span class="badge bg-secondary">Chưa xác nhận</span>
                                            <?php elseif ($trang_thai_lich == 1): ?>
                                                <span class="badge bg-success">Đã xác nhận</span>
                                            <?php endif; ?>

                                            <div class="ms-2">

                                                <a href="<?= BASEURL ?>?act=edit_schedule&lich_id=<?= $lich_id ?>" class="text-warning me-1" title="Sửa"><i class="bi bi-pencil-square"></i></a>
                                                <a href="<?= BASEURL ?>?act=delete_schedule&lich_id=<?= $lich_id ?>" class="text-danger" onclick="return confirm('Xóa lịch?')" title="Xóa"><i class="bi bi-trash"></i></a>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card h-100 border-start border-4 border-success">
                                <div class="card-header text-success fw-bold">
                                    <i class="bi bi-cash-coin"></i> Tài chính & Trạng thái
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="info-label">Trạng thái Đơn:</span>
                                        <?= renderStatusBadge($data['trang_thai'] ?? '') ?>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="info-label">Số khách:</span>
                                        <strong class="fs-5"><?= htmlspecialchars($data['so_khach'] ?? 0) ?></strong>
                                    </div>

                                    <div class="bg-light p-3 rounded border">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Giá vé cơ bản:</span>
                                            <span><?= formatCurrency($data['tour_info']['gia_co_ban'] ?? 0) ?> / khách</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Dịch vụ đi kèm:</span>
                                            <span class="text-muted">+ <?= formatCurrency($tong_gia_dv_phu_tro) ?> / khách</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-dark">TỔNG TIỀN (Ước tính):</span>
                                            <span class="price-highlight"><?= formatCurrency($data['tong_tien_uoc_tinh'] ?? 0) ?></span>
                                        </div>
                                    </div>

                                    <div class="mt-4 d-flex justify-content-between align-items-center">
                                        <span class="info-label">Đã thanh toán/cọc:</span>
                                        <span class="badge bg-success fs-6 px-3 py-2"><?= formatCurrency($data['da_dat_coc'] ?? 0) ?></span>
                                    </div>

                                    <?php if (!empty($data['ghi_chu'])): ?>
                                        <div class="alert alert-warning mt-3 mb-0 py-2 small">
                                            <i class="bi bi-sticky-fill"></i> <strong>Ghi chú:</strong> <?= htmlspecialchars($data['ghi_chu']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="passengers">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center bg-white">
                            <h6 class="mb-0"><i class="bi bi-people-fill text-primary"></i> Danh sách hành khách</h6>
                            <?php if (!empty($data['dat_tour_id'])): ?>
                                <a href="<?= BASEURL ?>?act=hanh_khach_edit&dat_tour_id=<?= $data['dat_tour_id'] ?>" class="btn btn-sm btn-warning fw-bold shadow-sm">
                                    <i class="bi bi-person-gear"></i> Cập nhật khách
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 table-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" width="50">#</th>
                                            <th width="30%">Họ Tên & Giới tính</th>
                                            <th width="35%">Liên hệ / Giấy tờ</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($danhSachHanhKhach)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-5">Chưa có thông tin hành khách.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($danhSachHanhKhach as $i => $hk): ?>
                                                <tr>
                                                    <td class="text-center text-secondary fw-bold"><?= $i + 1 ?></td>
                                                    <td>
                                                        <div class="fw-bold text-primary"><?= htmlspecialchars($hk['ho_ten'] ?? '---') ?></div>
                                                        <small class="text-muted"><?= htmlspecialchars($hk['gioi_tinh'] ?? '') ?></small>
                                                    </td>
                                                    <td>
                                                        <div class="small text-muted"><i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($hk['sdt'] ?? '---') ?></div>
                                                        <div class="small text-muted"><i class="bi bi-card-heading me-1"></i> <?= htmlspecialchars($hk['cccd'] ?? '---') ?></div>
                                                    </td>

                                                    <td>
                                                        <?php if (!empty($hk['yeu_cau_noi_dung'])): ?>
                                                            <span class="d-inline-block bg-light text-dark small px-2 py-1 rounded border">
                                                                <?= htmlspecialchars($hk['yeu_cau_noi_dung']) ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted small fst-italic opacity-50">---</span>
                                                        <?php endif; ?>
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

                <div class="tab-pane fade" id="services">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h6 class="mb-0 text-dark"><i class="bi bi-box-seam text-primary"></i> Dịch vụ đi kèm (Mặc định theo Tour)</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0 table-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Tên Dịch Vụ</th>
                                            <th class="text-end">Đơn Giá</th>
                                            <th>Ghi Chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($dichVuTour)): ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">Không có dịch vụ đi kèm nào.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($dichVuTour as $dv): ?>
                                                <tr>
                                                    <td class="ps-4 fw-medium"><?= htmlspecialchars($dv['ten'] ?? 'Dịch vụ') ?></td>
                                                    <td class="text-end fw-bold text-success"><?= formatCurrency($dv['gia'] ?? 0) ?></td>
                                                    <td class="text-muted small fst-italic"><?= htmlspecialchars($dv['ghi_chu'] ?? '') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <tr class="table-active border-top border-2">
                                                <td class="ps-4 fw-bold text-end">TỔNG CỘNG DỊCH VỤ:</td>
                                                <td class="text-end fw-bold text-danger fs-6"><?= formatCurrency($tong_gia_dv_phu_tro) ?></td>
                                                <td></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="guide">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center bg-white">
                            <h6 class="mb-0"><i class="bi bi-person-badge text-primary"></i> Hướng Dẫn Viên & Lịch Trình</h6>
                            <?php
                            $lich_id = $data['lich_khoi_hanh']['lich_id'] ?? null;
                            $has_guide = !empty($huongDanVien);
                            if ($lich_id && empty($huongDanVien)):
                            ?>
                                <a href="<?= BASEURL ?>?act=assign_guide&lich_id=<?= $lich_id ?>" class="btn btn-sm btn-success fw-bold shadow-sm">
                                    <i class="bi bi-person-plus-fill"></i> Phân công HDV
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if (!$lich_id): ?>
                                <div class="alert alert-danger border-0 shadow-sm text-center">
                                    <i class="bi bi-exclamation-circle-fill me-2"></i> Tour này chưa được lên lịch khởi hành.
                                    <div class="mt-2"><a href="<?= BASEURL ?>?act=add_schedule&tour_id=<?= $data['tour_info']['tour_id'] ?? '' ?>" class="btn btn-sm btn-danger">Lên lịch ngay</a></div>
                                </div>
                            <?php elseif (empty($huongDanVien)): ?>
                                <div class="text-center text-muted py-4 border rounded bg-light">
                                    <i class="bi bi-person-x display-4 opacity-25"></i>
                                    <p class="mt-2 mb-0">Chưa có hướng dẫn viên nào được phân công.</p>
                                </div>
                            <?php else: ?>

                                <div class="row g-3">

                                    <?php foreach ($huongDanVien as $hdv): ?>
                                        <div class="col-md-6 col-lg-4">

                                            <div class="border rounded p-3 d-flex align-items-center shadow-sm h-100 bg-white position-relative">

                                                <a href="<?= BASEURL ?>?act=remove_guide&hdv_id=<?= $hdv['id'] ?? 0 ?>&lich_id=<?= $lich_id ?>"
                                                    onclick="return confirm('Gỡ HDV này khỏi lịch trình?')"
                                                    class="position-absolute top-0 end-0 m-2 text-secondary" title="Gỡ bỏ">
                                                    <i class="bi bi-x-lg"></i>
                                                </a>

                                                <div class="avatar-circle bg-success me-3 flex-shrink-0">
                                                    <?= strtoupper(substr($hdv['ho_ten'] ?? 'G', 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold text-success mb-1"><?= htmlspecialchars($hdv['ho_ten'] ?? 'Chưa cập nhật') ?></h6>
                                                    <div class="small text-muted mb-1"><i class="bi bi-phone me-1"></i> <?= htmlspecialchars($hdv['sdt'] ?? '---') ?></div>
                                                    <div class="small text- muted"><i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($hdv['email'] ?? '---') ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>

</html>