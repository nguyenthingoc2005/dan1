<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Tour - <?= htmlspecialchars($tourDetail['ten'] ?? 'Tour') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        /* STYLE CHUNG CỦA TRANG QUẢN TRỊ */
        .text-primary-custom {
            color: #0d6efd !important;
            font-weight: 700;
        }
        
        /* STYLE NÚT QUAY LẠI ĐÃ ĐƯỢC CẢI THIỆN */
        .btn-return-list {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #495057;
            transition: all 0.2s;
        }
        .btn-return-list:hover {
            background-color: #e9ecef;
            color: #212529;
            border-color: #adb5bd;
        }
        
        .diadiem-img {
            height: 100px; /* Chiều cao cố định cho hình ảnh */
            width: 100%;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 5px;
        }
        
    </style>
</head>
<body class="bg-light">

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid py-4">
            <?php 
            // Giả định Controller đã gọi hàm Model và gán kết quả vào $tourDetail
            if (empty($tourDetail)): ?>
                <div class="alert alert-danger text-center">Không tìm thấy thông tin Tour.</div>
            <?php return; endif; ?>
            
            <?php $tour_id = htmlspecialchars($tourDetail['tour_id']); // Lấy ID Tour một lần ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0 text-primary-custom">
                    <i class="bi bi-map me-2"></i> **<?= htmlspecialchars($tourDetail['ten']) ?>**
                </h2>
                <a href="<?= BASEURL ?>?act=tour_list" class="btn btn-return-list shadow-sm fw-bold">
                    <i class="bi bi-arrow-left-circle me-2"></i> Quay lại Danh sách Tour
                </a>
            </div>
            <h4 class="text-muted">ID Tour: #<?= $tour_id ?></h4>
            <hr class="mb-4">

            <ul class="nav nav-tabs mb-4" id="tourTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="base-tab" data-bs-toggle="tab" data-bs-target="#base" type="button" role="tab" aria-controls="base" aria-selected="true">
                        <i class="bi bi-info-circle"></i> Thông tin cơ bản
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab" aria-controls="schedule" aria-selected="false">
                        <i class="bi bi-clock-history"></i> Lịch Trình (<?= count($tourDetail['lich_trinh'] ?? []) ?> ngày)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="locations-tab" data-bs-toggle="tab" data-bs-target="#locations" type="button" role="tab" aria-controls="locations" aria-selected="false">
                        <i class="bi bi-geo-alt"></i> Địa Điểm (<?= count($tourDetail['dia_diem'] ?? []) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button" role="tab" aria-controls="services" aria-selected="false">
                        <i class="bi bi-truck-flatbed"></i> Dịch Vụ (<?= count($tourDetail['dich_vu'] ?? []) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="policies-tab" data-bs-toggle="tab" data-bs-target="#policies" type="button" role="tab" aria-controls="policies" aria-selected="false">
                        <i class="bi bi-shield-check"></i> Chính Sách (<?= count($tourDetail['chinh_sach'] ?? []) ?>)
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="tourTabsContent">
                
                <div class="tab-pane fade show active" id="base" role="tabpanel" aria-labelledby="base-tab">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="section-header">
                                <h5 class="mb-0">Chi tiết Tour</h5>
                                <a href="<?= BASEURL ?>?act=uppdatetour&tour_id=<?= $tour_id ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i> Chỉnh sửa
                                </a>
                            </div>
                            <hr class="mt-0">
                            <p><strong>Loại Tour:</strong> <?= htmlspecialchars($tourDetail['loai_tour'] ?? 'N/A') ?></p>
                            <p><strong>Thời gian:</strong> <?= htmlspecialchars($tourDetail['thoi_gian'] ?? 'N/A') ?></p>
                            <p><strong>Phương tiện:</strong> <?= htmlspecialchars($tourDetail['phuong_tien'] ?? 'N/A') ?></p>
                            <p><strong>Giá:</strong> <?= number_format($tourDetail['gia'] ?? 0) ?> VND</p>
                            <h6>Mô tả chi tiết:</h6>
                            <p class="border p-3 bg-light rounded"><?= nl2br(htmlspecialchars($tourDetail['mo_ta'] ?? 'Không có mô tả')) ?></p>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="schedule" role="tabpanel" aria-labelledby="schedule-tab">
                    <div class="section-header">
                        <h5 class="mb-0">Quản lý Lịch Trình</h5>
                        <a href="<?= BASEURL ?>?act=addlichtrinh&tour_id=<?= $tour_id ?>" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil-square"></i> Chỉnh sửa Lịch Trình
                        </a>
                    </div>
                    <hr class="mt-0">
                    <?php if (empty($tourDetail['lich_trinh'])): ?>
                        <div class="alert alert-warning">Chưa có thông tin lịch trình.</div>
                    <?php else: ?>
                        <div class="accordion" id="accordionSchedule">
                            <?php foreach ($tourDetail['lich_trinh'] as $index => $lt): ?>
                                <div class="accordion-item shadow-sm mb-2">
                                    <h2 class="accordion-header" id="heading<?= $lt['lich_trinh_id'] ?>">
                                        <button class="accordion-button <?= $index == 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $lt['lich_trinh_id'] ?>" aria-expanded="<?= $index == 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $lt['lich_trinh_id'] ?>">
                                            Ngày **<?= htmlspecialchars($lt['ngay_thu']) ?>**: <?= htmlspecialchars($lt['tieu_de']) ?>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $lt['lich_trinh_id'] ?>" class="accordion-collapse collapse <?= $index == 0 ? 'show' : '' ?>" aria-labelledby="heading<?= $lt['lich_trinh_id'] ?>" data-bs-parent="#accordionSchedule">
                                        <div class="accordion-body bg-light">
                                            <?= nl2br(htmlspecialchars($lt['noi_dung'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="locations" role="tabpanel" aria-labelledby="locations-tab">
                    <div class="section-header">
                        <h5 class="mb-0">Quản lý Địa Điểm</h5>
                        <a href="<?= BASEURL ?>?act=gan_diadiem&tour_id=<?= $tour_id ?>" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil-square"></i> Chỉnh sửa Địa Điểm
                        </a>
                    </div>
                    <hr class="mt-0">
                    <?php if (empty($tourDetail['dia_diem'])): ?>
                        <div class="alert alert-warning">Chưa có địa điểm nào được gán.</div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($tourDetail['dia_diem'] as $index => $dd): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body">
                                            <h6 class="mb-2 border-bottom pb-2">
                                                <span class="badge bg-primary rounded-pill me-2"><?= $index + 1 ?></span>
                                                **<?= htmlspecialchars($dd['ten_diadiem']) ?>** <span class="text-muted small"> (<?= htmlspecialchars($dd['quoc_gia'] ?? 'N/A') ?>)</span>
                                            </h6>
                                            
                                            <p class="fw-bold mb-1">Hình ảnh:</p>
                                            <div class="row row-cols-3 g-2 mb-3">
                                                <?php if (!empty($dd['hinh_anh'])): ?>
                                                    <?php $hinh_count = 0; ?>
                                                    <?php foreach ($dd['hinh_anh'] as $hinh): ?>
                                                        <?php if ($hinh_count < 3): // Giới hạn 3 hình ảnh ?>
                                                            <div class="col">
                                                                <img src="<?= htmlspecialchars($hinh['url']) ?>" 
                                                                     class="diadiem-img img-fluid" 
                                                                     alt="<?= htmlspecialchars($hinh['alt_text'] ?? $dd['ten_diadiem']) ?>"
                                                                     title="<?= htmlspecialchars($hinh['alt_text'] ?? $dd['ten_diadiem']) ?>">
                                                            </div>
                                                        <?php $hinh_count++; endif; ?>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <div class="col-12">
                                                        <span class="text-danger small">Địa điểm chưa có hình ảnh.</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <p class="mb-1 small">
                                                **Mô tả:** <?= htmlspecialchars($dd['mo_ta'] ?? 'Không có mô tả') ?>
                                            </p>
                                            <span class="badge bg-info text-dark">Ghi chú Tour: <?= htmlspecialchars($dd['ghi_chu'] ?? 'N/A') ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="tab-pane fade" id="services" role="tabpanel" aria-labelledby="services-tab">
                    <div class="section-header">
                        <h5 class="mb-0">Quản lý Dịch Vụ</h5>
                        <a href="<?= BASEURL ?>?act=gandichvu&tour_id=<?= $tour_id ?>" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil-square"></i> Chỉnh sửa Dịch vụ
                        </a>
                    </div>
                    <hr class="mt-0">
                    <?php if (empty($tourDetail['dich_vu'])): ?>
                        <div class="alert alert-warning">Chưa có dịch vụ nào được gán cho tour này.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover bg-white shadow-sm">
                                <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>Tên Dịch vụ</th>
                                        <th>Giá (Tour)</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tourDetail['dich_vu'] as $index => $dv): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>**<?= htmlspecialchars($dv['ten']) ?>**</td>
                                            <td><?= number_format($dv['gia'] ?? 0, 0, ',', '.') ?> VND</td>
                                            <td><span class="text-muted small"><?= htmlspecialchars($dv['ghi_chu'] ?? 'Không') ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="policies" role="tabpanel" aria-labelledby="policies-tab">
                    <div class="section-header">
                        <h5 class="mb-0">Quản lý Chính Sách</h5>
                        <a href="<?= BASEURL ?>?act=chinhsach&tour_id=<?= $tour_id ?>" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil-square"></i> Chỉnh sửa Chính Sách
                        </a>
                    </div>
                    <hr class="mt-0">
                    <?php if (empty($tourDetail['chinh_sach'])): ?>
                        <div class="alert alert-warning">Chưa có chính sách nào được gán.</div>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($tourDetail['chinh_sach'] as $cs): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center shadow-sm mb-2">
                                    <div>
                                        **<?= htmlspecialchars($cs['ten']) ?>** <span class="badge bg-secondary"><?= htmlspecialchars($cs['loai']) ?></span>
                                    </div>
                                    <span class="text-muted small ms-4">Ghi chú: **<?= htmlspecialchars($cs['ghi_chu'] ?? 'Không') ?>**</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>
</html>