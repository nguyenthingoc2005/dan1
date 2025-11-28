<?php
// =================================================================================
// PHẦN 1: HELPER FUNCTIONS & XỬ LÝ DỮ LIỆU
// =================================================================================

// Kiểm tra dữ liệu
if (empty($data)) {
    echo '<div class="container mt-5"><div class="alert alert-danger shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Không tìm thấy dữ liệu đơn đặt tour này.
          </div></div>';
    return;
}

// Hàm format tiền tệ
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        return number_format($amount ?? 0, 0, ',', '.') . ' VNĐ';
    }
}

// Hàm hiển thị trạng thái (Fix lỗi null cho PHP 8.1+)
if (!function_exists('renderStatusBadge')) {
    function renderStatusBadge($status) {
        $s = strtolower($status ?? ''); // Fix lỗi passing null
        return match ($s) {
            'hoàn tất', 'completed', 'success', 'paid' => '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Hoàn tất</span>',
            'chờ xác nhận', 'pending' => '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Chờ xác nhận</span>',
            'đã xác nhận', 'confirmed' => '<span class="badge bg-info text-dark"><i class="bi bi-check2-all"></i> Đã xác nhận</span>',
            'đã hủy', 'cancelled' => '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Đã hủy</span>',
            '' => '<span class="badge bg-secondary">Chưa cập nhật</span>',
            default => '<span class="badge bg-secondary">' . htmlspecialchars($s) . '</span>',
        };
    }
}

// Tính tổng giá dịch vụ phụ trợ để hiển thị
$tong_gia_dv_phu_tro = 0;
if (!empty($data['dich_vu_tour'])) {
    foreach ($data['dich_vu_tour'] as $dv) {
        $tong_gia_dv_phu_tro += $dv['gia'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Đơn #<?= htmlspecialchars($data['dat_tour_id']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .main-content { padding: 20px; min-height: 100vh; }
        
        /* Typography */
        .text-primary-custom { color: #0d6efd !important; font-weight: 700; }
        .info-label { font-weight: 600; color: #6c757d; min-width: 140px; display: inline-block; }
        .price-highlight { font-size: 1.25rem; font-weight: 800; color: #dc3545; }
        
        /* Components */
        .card { border: none; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 20px; border-radius: 8px; }
        .card-header { background-color: #fff; border-bottom: 1px solid #eee; font-weight: 600; padding: 15px 20px; }
        
        /* Tabs */
        .nav-tabs { border-bottom: 2px solid #dee2e6; margin-bottom: 20px; }
        .nav-tabs .nav-link { border: none; color: #6c757d; font-weight: 500; padding: 10px 20px; }
        .nav-tabs .nav-link.active { color: #0d6efd; border-bottom: 3px solid #0d6efd; background: transparent; }
        
        .table-middle td { vertical-align: middle; }
    </style>
</head>
<body>

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0 text-primary-custom">
                        <i class="bi bi-ticket-detailed"></i> Đơn Đặt Tour #<?= htmlspecialchars($data['dat_tour_id']) ?>
                    </h2>
                    <div class="text-muted mt-1 small">
                        <i class="bi bi-calendar-event"></i> Ngày tạo: <?= date('H:i - d/m/Y', strtotime($data['ngay_dat'])) ?>
                    </div>
                </div>
                <a href="<?= BASEURL ?>?act=dattourlist" class="btn btn-outline-secondary fw-bold">
                    <i class="bi bi-arrow-return-left"></i> Quay lại danh sách
                </a>
            </div>

            <ul class="nav nav-tabs" id="bookingTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button">
                        <i class="bi bi-info-circle me-1"></i> Thông Tin Chung
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="passengers-tab" data-bs-toggle="tab" data-bs-target="#passengers" type="button">
                        <i class="bi bi-people me-1"></i> Hành Khách (<?= count($data['danh_sach_hanh_khach'] ?? []) ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button">
                        <i class="bi bi-box-seam me-1"></i> Dịch Vụ Tour
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="guide-tab" data-bs-toggle="tab" data-bs-target="#guide" type="button">
                        <i class="bi bi-person-badge me-1"></i> Hướng Dẫn Viên
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="bookingTabsContent">

                <div class="tab-pane fade show active" id="general">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span class="text-primary"><i class="bi bi-map"></i> Thông tin Tour</span>
                                    <a href="<?= BASEURL ?>?act=dat_tour_edit&dat_tour_id=<?= $data['dat_tour_id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square"></i> Chỉnh sửa
                                    </a>
                                </div>
                                <div class="card-body">
                                    <p><span class="info-label">Tên Tour:</span> <strong><?= htmlspecialchars($data['tour_info']['ten_tour']) ?></strong></p>
                                    <p><span class="info-label">Mã Tour:</span> #<?= htmlspecialchars($data['tour_info']['tour_id']) ?></p>
                                    <p><span class="info-label">Thời lượng:</span> <?= htmlspecialchars($data['tour_info']['thoi_gian']) ?> ngày</p>
                                    <hr>
                                    
                                    <div class="mb-2">
                                        <span class="info-label">Tình trạng Lịch:</span>
                                        <?php 
                                            $lich_id = $data['lich_khoi_hanh']['lich_id'] ?? null;
                                            $trang_thai_lich = $data['lich_khoi_hanh']['trang_thai'] ?? '';
                                        ?>

                                        <?php if (empty($lich_id)): ?>
                                            <span class="text-danger fst-italic me-2"><i class="bi bi-exclamation-circle"></i> Chưa được lên lịch</span>
                                            <a href="<?= BASEURL ?>?act=add_schedule&tour_id=<?= $data['tour_info']['tour_id'] ?>" class="btn btn-sm btn-primary shadow-sm py-0 px-2 fw-bold">
                                                <i class="bi bi-calendar-plus"></i> Đặt lịch ngay
                                            </a>
                                        <?php else: ?>
                                            <div class="d-inline-flex align-items-center gap-2">
                                                <?= renderStatusBadge($trang_thai_lich) ?>
                                                <a href="<?= BASEURL ?>?act=edit_schedule&id=<?= $lich_id ?>" class="btn btn-sm btn-outline-warning py-0 px-2" title="Sửa lịch">
                                                    <i class="bi bi-pencil-square"></i> Sửa
                                                </a>
                                                <a href="<?= BASEURL ?>?act=delete_schedule&id=<?= $lich_id ?>" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('Xóa lịch này?')" title="Xóa lịch">
                                                    <i class="bi bi-trash"></i> Xóa
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <p><span class="info-label">Thời gian:</span> 
                                        <?php if(!empty($data['lich_khoi_hanh']['ngay_bat_dau'])): ?>
                                            <?= date('d/m/Y', strtotime($data['lich_khoi_hanh']['ngay_bat_dau'])) ?> - <?= date('d/m/Y', strtotime($data['lich_khoi_hanh']['ngay_ket_thuc'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">...</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header text-success">
                                    <i class="bi bi-cash-coin"></i> Tài chính & Trạng thái
                                </div>
                                <div class="card-body">
                                    <p><span class="info-label">Trạng thái Đơn:</span> <?= renderStatusBadge($data['trang_thai']) ?></p>
                                    <p><span class="info-label">Số khách:</span> <strong><?= htmlspecialchars($data['so_khach']) ?></strong> người</p>
                                    
                                    <div class="bg-light p-3 rounded border mt-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Giá vé cơ bản:</span>
                                            <span><?= formatCurrency($data['tour_info']['gia_co_ban']) ?> / khách</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Dịch vụ đi kèm:</span>
                                            <span class="text-muted">+ <?= formatCurrency($tong_gia_dv_phu_tro) ?> / khách</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">TỔNG TIỀN (Ước tính):</span>
                                            <span class="price-highlight"><?= formatCurrency($data['tong_tien_uoc_tinh']) ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 d-flex justify-content-between align-items-center">
                                        <span class="info-label">Đã thanh toán/cọc:</span> 
                                        <span class="badge bg-success fs-6"><?= formatCurrency($data['da_dat_coc']) ?></span>
                                    </div>
                                    <?php if(!empty($data['ghi_chu'])): ?>
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
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-dark"><i class="bi bi-person-lines-fill"></i> Danh sách hành khách</h6>
                            <a href="<?= BASEURL ?>?act=hanh_khach_edit&dat_tour_id=<?= $data['dat_tour_id'] ?>" class="btn btn-sm btn-warning fw-bold">
                                <i class="bi bi-person-fill-gear"></i> Sửa thông tin khách
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered mb-0 table-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" width="50">#</th>
                                            <th width="25%">Họ và Tên</th>
                                            <th width="20%">Thông tin cá nhân</th>
                                            <th width="35%">Yêu cầu phục vụ</th>
                                            <th class="text-center" width="15%">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($data['danh_sach_hanh_khach'])): ?>
                                            <tr><td colspan="5" class="text-center py-4 text-muted">Chưa có thông tin hành khách</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($data['danh_sach_hanh_khach'] as $idx => $hk): ?>
                                            <tr>
                                                <td class="text-center fw-bold text-muted"><?= $idx + 1 ?></td>
                                                <td><div class="fw-bold text-primary"><?= htmlspecialchars($hk['ho_ten'] ?? 'Chưa cập nhật') ?></div></td>
                                                <td>
                                                    <div class="small text-muted"><i class="bi bi-telephone"></i> <?= htmlspecialchars($hk['sdt'] ?? '-') ?></div>
                                                    <div class="small text-muted"><i class="bi bi-card-heading"></i> <?= htmlspecialchars($hk['cccd'] ?? '-') ?></div>
                                                </td>
                                                <td>
                                                    <?php if(!empty($hk['yeu_cau_noi_dung'])): ?>
                                                        <div class="alert alert-info py-1 px-2 mb-0 small border-0 bg-opacity-10">
                                                            <i class="bi bi-chat-quote-fill me-1"></i> <?= htmlspecialchars($hk['yeu_cau_noi_dung']) ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted small fst-italic">Không có yêu cầu</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php 
                                                        $action = !empty($hk['yeu_cau_id']) ? 'edit_request' : 'add_request';
                                                        $param_id = !empty($hk['yeu_cau_id']) ? '&id=' . $hk['yeu_cau_id'] : '&hanh_khach_id=' . $hk['id'] . '&dat_tour_id=' . $data['dat_tour_id'];
                                                    ?>
                                                    <a href="<?= BASEURL ?>?act=<?= $action . $param_id ?>" class="btn btn-sm btn-outline-secondary" title="Chỉnh sửa yêu cầu">
                                                        <i class="bi bi-pencil-square"></i> Yêu cầu
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

                <div class="tab-pane fade" id="services">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h6 class="mb-0 text-dark"><i class="bi bi-box-seam"></i> Dịch vụ đi kèm</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0 table-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" width="50">#</th>
                                            <th>Tên Dịch Vụ</th>
                                            <th class="text-end">Đơn giá</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($data['dich_vu_tour'])): ?>
                                            <tr><td colspan="4" class="text-center py-3 text-muted">Không có dịch vụ đi kèm</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($data['dich_vu_tour'] as $idx => $dv): ?>
                                            <tr>
                                                <td class="text-center"><?= $idx + 1 ?></td>
                                                <td><?= htmlspecialchars($dv['ten']) ?></td>
                                                <td class="text-end fw-bold text-primary"><?= formatCurrency($dv['gia']) ?></td>
                                                <td class="text-muted small"><?= htmlspecialchars($dv['ghi_chu'] ?? '') ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <tr class="table-active">
                                                <td colspan="2" class="text-end fw-bold">TỔNG CỘNG:</td>
                                                <td class="text-end fw-bold text-danger"><?= formatCurrency($tong_gia_dv_phu_tro) ?></td>
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
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-dark"><i class="bi bi-person-badge"></i> Hướng dẫn viên</h6>
                            
                            <?php 
                                $has_schedule = !empty($data['lich_khoi_hanh']['lich_id']);
                                $has_guide = !empty($data['huong_dan_vien']);
                            ?>
                            <?php if ($has_schedule && !$has_guide): ?>
                                <a href="<?= BASEURL ?>?act=assign_guide&lich_id=<?= $data['lich_khoi_hanh']['lich_id'] ?>" class="btn btn-sm btn-success fw-bold">
                                    <i class="bi bi-person-plus-fill"></i> Thêm HDV
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <?php if (!$has_schedule): ?>
                                <div class="alert alert-danger border-danger text-center py-4 mb-0">
                                    <i class="bi bi-calendar-x display-4 text-danger opacity-50"></i>
                                    <h5 class="mt-2 text-danger">Chưa có lịch trình</h5>
                                    <p class="mb-0 text-muted">Vui lòng <strong>Lên lịch khởi hành</strong> (Tab Thông tin chung) trước.</p>
                                </div>
                            <?php elseif (!$has_guide): ?>
                                <div class="alert alert-light border text-center py-4 mb-0">
                                    <i class="bi bi-person-x display-4 text-muted"></i>
                                    <p class="mt-2 mb-0 text-muted">Chưa có HDV nào được phân công.</p>
                                </div>
                            <?php else: ?>
                                <div class="row g-4">
                                    <?php foreach ($data['huong_dan_vien'] as $hdv): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 shadow-sm border border-success bg-light">
                                            <div class="card-body position-relative">
                                                <div class="position-absolute top-0 end-0 m-2 bg-white rounded shadow-sm p-1 border">
                                                    <a href="<?= BASEURL ?>?act=edit_guide_assignment&phan_cong_id=<?= $hdv['id'] ?>" class="btn btn-sm btn-link text-warning p-1" title="Đổi HDV">
                                                        <i class="bi bi-pencil-square fs-6"></i>
                                                    </a>
                                                    <a href="<?= BASEURL ?>?act=remove_guide&hdv_id=<?= $hdv['id'] ?>&lich_id=<?= $data['lich_khoi_hanh']['lich_id'] ?>" class="btn btn-sm btn-link text-danger p-1" onclick="return confirm('Gỡ HDV này?')" title="Gỡ HDV">
                                                        <i class="bi bi-trash fs-6"></i>
                                                    </a>
                                                </div>
                                                
                                                <div class="d-flex align-items-center mt-2">
                                                    <div class="flex-shrink-0">
                                                        <div class="bg-success text-white rounded-circle d-flex justify-content-center align-items-center shadow-sm" style="width: 55px; height: 55px; font-size: 22px; font-weight: bold;">
                                                            <?= strtoupper(substr($hdv['ho_ten'] ?? 'G', 0, 1)) ?>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-1 fw-bold text-success"><?= htmlspecialchars($hdv['ho_ten']) ?></h6>
                                                        <div class="small text-muted mb-1"><i class="bi bi-telephone-fill me-1"></i> <?= htmlspecialchars($hdv['sdt'] ?? '---') ?></div>
                                                        <div class="small text-muted"><i class="bi bi-envelope-fill me-1"></i> <?= htmlspecialchars($hdv['email'] ?? '---') ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div> </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>
</html>