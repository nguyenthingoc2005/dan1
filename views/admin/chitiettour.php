<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Tour - <?= htmlspecialchars($tourDetail['ten']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container my-5">
    <?php 
    // Giả định Controller đã gọi hàm Model và gán kết quả vào $tourDetail
    if (empty($tourDetail)): ?>
        <div class="alert alert-danger text-center">Không tìm thấy thông tin Tour.</div>
    <?php return; endif; ?>

    <h1 class="mb-4 text-primary">
        <i class="bi bi-map me-2"></i> **<?= htmlspecialchars($tourDetail['ten']) ?>** </h1>
    <h4 class="text-muted">ID Tour: #<?= htmlspecialchars($tourDetail['tour_id']) ?></h4>
    <hr>

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
            <button class="nav-link" id="policies-tab" data-bs-toggle="tab" data-bs-target="#policies" type="button" role="tab" aria-controls="policies" aria-selected="false">
                <i class="bi bi-shield-check"></i> Chính Sách (<?= count($tourDetail['chinh_sach'] ?? []) ?>)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="tourTabsContent">
        
        <div class="tab-pane fade show active" id="base" role="tabpanel" aria-labelledby="base-tab">
            <div class="card shadow-sm">
                <div class="card-body">
                    <p><strong>Loại Tour:</strong> <?= htmlspecialchars($tourDetail['loai_tour'] ?? 'N/A') ?></p>
                    <p><strong>Thời gian:</strong> <?= htmlspecialchars($tourDetail['thoi_gian'] ?? 'N/A') ?></p>
                    <p><strong>Phương tiện:</strong> <?= htmlspecialchars($tourDetail['phuong_tien'] ?? 'N/A') ?></p>
                    <p><strong>Giá:</strong> <?= number_format($tourDetail['gia'] ?? 0) ?> VND</p>
                    <h5>Mô tả chi tiết:</h5>
                    <p class="border p-3 bg-light rounded"><?= nl2br(htmlspecialchars($tourDetail['mo_ta'] ?? 'Không có mô tả')) ?></p>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="schedule" role="tabpanel" aria-labelledby="schedule-tab">
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
            <?php if (empty($tourDetail['dia_diem'])): ?>
                <div class="alert alert-warning">Chưa có địa điểm nào được gán.</div>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($tourDetail['dia_diem'] as $index => $dd): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-start shadow-sm mb-2">
                            <div>
                                <h6 class="mb-1">
                                    <span class="badge bg-primary rounded-pill me-2"><?= $index + 1 ?></span>
                                    **<?= htmlspecialchars($dd['ten_diadiem']) ?>** <span class="text-muted small"> (<?= htmlspecialchars($dd['quoc_gia'] ?? 'N/A') ?>)</span>
                                </h6>
                                <p class="mb-0 small text-muted ms-4">
                                    Mô tả: <?= htmlspecialchars($dd['mo_ta'] ?? 'Không có') ?>
                                </p>
                            </div>
                            <span class="badge bg-info text-dark align-self-center">Ghi chú: <?= htmlspecialchars($dd['ghi_chu'] ?? 'N/A') ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="policies" role="tabpanel" aria-labelledby="policies-tab">
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

    <div class="mt-5 text-center">
        <a href="<?= BASEURL ?>?act=tour_list" class="btn btn-secondary btn-lg">
            <i class="bi bi-arrow-left-circle me-2"></i> Quay lại Danh sách Tour
        </a>
    </div>
    <div class="mt-5 text-center">
        <a href="<?= BASEURL ?>?act=uppdatetour&tour_id=<?= htmlspecialchars($tourDetail['tour_id']) ?>">
            <i class="bi bi-arrow-left-circle me-2"></i> Chỉnh sửa
        </a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>