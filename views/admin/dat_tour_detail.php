<!-- <?php
// Giả định: Controller đã gọi $data = $datTourModel->getDatTourDetailsWithRelations($id);
// Giả định: BASEURL đã được định nghĩa.

if (empty($data)) {
    echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>Lỗi</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="bg-light"><div class="container py-5"><div class="alert alert-danger shadow-sm">Không tìm thấy đơn đặt tour này hoặc không có dữ liệu.</div></body></html>';
    exit;
}

// --- HÀM HỖ TRỢ ---

// Hàm format tiền tệ
function formatCurrency($amount, $currency = 'VND') {
    if (empty($amount) || $amount == 0) return '0 đ';
    if ($currency == 'VND') {
        return number_format($amount, 0, ',', '.') . ' đ';
    }
    return $currency . ' ' . number_format($amount, 2, '.', ',');
}

// Logic định dạng trạng thái
$trang_thai = htmlspecialchars($data['trang_thai']);
$trang_thai_class = match ($trang_thai) {
    'pending' => 'bg-warning text-dark',
    'confirmed' => 'bg-info',
    'paid' => 'bg-success',
    'cancelled' => 'bg-danger',
    'completed' => 'bg-primary',
    default => 'bg-secondary',
};

// Tính toán chỉ Tổng Đặt Cọc
$tong_dat_coc = $data['tong_dat_coc'] ?? 0;

// Tính tổng tiền của tất cả Dịch vụ Đặt Thêm
$tong_dv_them = 0;
if (!empty($data['dich_vu_them'])) {
    foreach ($data['dich_vu_them'] as $dv) {
        $tong_dv_them += $dv['tong_tien_dv'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Đặt Tour #<?= htmlspecialchars($data['dat_tour_id']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .container { max-width: 1200px; margin-top: 20px; }
        .info-label { font-weight: 600; color: #6c757d; }
        .passenger-list { list-style: none; padding: 0; }
        .passenger-item { border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 5px; background-color: #f8f9fa; }
        .detail-card .list-group-item { padding: 8px 15px; }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-file-text"></i> Chi Tiết Đơn Đặt Tour #<?= htmlspecialchars($data['dat_tour_id']) ?></h2>
        <a href="<?= BASEURL ?>?act=dat_tour_list" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="card shadow-sm mb-4 detail-card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Thông Tin Đơn Hàng</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><span class="info-label">Số Lượng Người:</span> 
                            <span class="fw-bold text-primary"><?= htmlspecialchars($data['so_nguoi']) ?></span>
                        </li>
                        <li class="list-group-item"><span class="info-label">Loại Đặt:</span> 
                            <?= htmlspecialchars($data['loai']) ?>
                        </li>
                        <li class="list-group-item"><span class="info-label">Nguồn Đặt:</span> 
                            <?= htmlspecialchars($data['nguon']) ?>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><span class="info-label">Ngày Đặt:</span> 
                            <?= date('d/m/Y H:i', strtotime($data['ngay_tao'])) ?>
                        </li>
                        <li class="list-group-item"><span class="info-label">Trạng Thái:</span> 
                            <span class="badge <?= $trang_thai_class ?> fs-6"><?= $trang_thai ?></span>
                        </li>
                        <li class="list-group-item"><span class="info-label">Ghi Chú Đơn:</span> 
                            <?= !empty($data['ghi_chu']) ? htmlspecialchars($data['ghi_chu']) : 'Không có' ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        
        <div class="col-lg-7">
            
            <div class="card shadow-sm mb-4 detail-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-person-fill"></i> Khách Hàng Chủ Đơn</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><span class="info-label">CCCD:</span> 
                            <?= !empty($data['cccd']) ? htmlspecialchars($data['cccd']) : 'Chưa có dữ liệu' ?>
                        </li>
                        <li class="list-group-item"><span class="info-label">Địa chỉ:</span> 
                            <?= !empty($data['dia_chi']) ? htmlspecialchars($data['dia_chi']) : 'Chưa có dữ liệu' ?>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mb-4 detail-card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-people-fill"></i> Danh Sách Hành Khách (<?= count($data['hanh_khach_list'] ?? []) ?>/<?= htmlspecialchars($data['so_nguoi']) ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['hanh_khach_list'])): ?>
                    <ul class="passenger-list">
                        <?php foreach ($data['hanh_khach_list'] as $hk): ?>
                        <li class="passenger-item">
                            <div class="fw-bold text-success"><?= htmlspecialchars($hk['ho_ten']) ?></div>
                            <small>CCCD: <?= !empty($hk['cccd']) ? htmlspecialchars($hk['cccd']) : 'N/A' ?> | 
                            Sinh: <?= !empty($hk['ngay_sinh']) ? date('d/m/Y', strtotime($hk['ngay_sinh'])) : 'N/A' ?> | 
                            Ghế: <?= !empty($hk['so_ghe']) ? htmlspecialchars($hk['so_ghe']) : 'N/A' ?> | 
                            Ghi chú: <?= !empty($hk['ghi_chu']) ? htmlspecialchars($hk['ghi_chu']) : 'Không' ?></small>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <div class="alert alert-warning">Chưa có danh sách hành khách được nhập.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-5">
            
            <div class="card shadow-sm mb-4 detail-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-cash-stack"></i> Thanh Toán</h5>
                </div>
                <div class="card-body text-center">
                    <p class="info-label mb-2">Số tiền đã Đặt Cọc (Xác nhận):</p>
                    <div class="fw-bold text-success fs-3">
                        <?= formatCurrency($tong_dat_coc, $data['tien_te']) ?>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4 detail-card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-bag-plus-fill"></i> Chi Tiết Dịch Vụ Đặt Thêm</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['dich_vu_them'])): ?>
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Dịch vụ</th>
                                <th class="text-end">SL</th>
                                <th class="text-end">Giá ĐV</th>
                                <th class="text-end">Tổng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['dich_vu_them'] as $dv): ?>
                            <tr>
                                <td><?= htmlspecialchars($dv['ten_dich_vu']) ?></td>
                                <td class="text-end"><?= htmlspecialchars($dv['so_luong']) ?></td>
                                <td class="text-end"><?= formatCurrency($dv['gia_dat'], $data['tien_te']) ?></td>
                                <td class="text-end fw-bold text-primary"><?= formatCurrency($dv['tong_tien_dv'], $data['tien_te']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <td colspan="3" class="fw-bold">TỔNG DV THÊM:</td>
                                <td class="text-end fw-bold fs-6"><?= formatCurrency($tong_dv_them, $data['tien_te']) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php else: ?>
                    <div class="alert alert-light">Không có dịch vụ thêm nào được đặt.</div>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </div>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> -->