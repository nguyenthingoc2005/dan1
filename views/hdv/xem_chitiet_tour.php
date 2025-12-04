<?php
// --- PHẦN XỬ LÝ DỮ LIỆU ĐẦU VÀO (GIỮ NGUYÊN ĐỂ TRÁNH LỖI VIEW) ---
// Đảm bảo các biến này được truyền từ Controller. 
// Nếu chưa có, khởi tạo mảng rỗng để không bị lỗi "Undefined variable".

$listKhachHang = $listKhachHang ?? [];
$tourDetails = $tourDetails ?? [];
$lich_trinh = isset($tourDetails['lich_trinh']) ? $tourDetails['lich_trinh'] : [];

// Định nghĩa BASEURL nếu chưa có (tránh lỗi khi chạy độc lập)
if (!defined('BASEURL')) define('BASEURL', '');
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Tour & Điểm Danh</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">

    <link rel="stylesheet" href="./assets/css/sidebar.css">

    <style>
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-content {
            margin-left: 0;
            /* Lưu ý: Chỉnh lại theo sidebar của bạn nếu cần (250px) */
            padding: 30px;
            min-height: 100vh;
        }

        /* --- Custom Styles cho Card --- */
        .card-detail {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            border: none;
            background: #fff;
        }

        /* --- Styles cho Lịch trình (Timeline) --- */
        .schedule-item {
            border-left: 3px solid #0d6efd;
            padding-left: 15px;
            margin-bottom: 25px;
            position: relative;
        }

        /* Chấm tròn trang trí trên timeline */
        .schedule-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 0;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #0d6efd;
        }

        .schedule-day {
            font-weight: 700;
            color: #0d6efd;
            font-size: 1.05rem;
        }

        /* --- Styles cho Modal Điểm Danh --- */
        #modalDiemDanh .modal-header {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            color: white;
        }

        /* Checkbox to hơn một chút cho dễ bấm */
        .form-check-input-lg {
            width: 1.3em;
            height: 1.3em;
            margin-top: 0.2em;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php include './views/parts/sidebarhdv.php'; ?>

    <div class="main-content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">
                        <i class="bi bi-map-fill me-2 text-primary"></i>Chi Tiết Chuyến Đi
                    </h2>
                    <p class="text-muted mb-0"><?= htmlspecialchars($tourDetails['ten'] ?? 'Đang cập nhật...') ?></p>
                </div>
                <a href="<?= BASEURL ?>?act=danh_sach_tour" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                </a>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card card-detail h-100">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle me-2"></i>Thông Tin Tour</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                    <span class="text-muted"><i class="bi bi-geo-alt-fill me-2"></i>Khởi Hành:</span>
                                    <span class="fw-medium text-end"><?= htmlspecialchars($tourDetails['diem_khoi_hanh'] ?? '---') ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                    <span class="text-muted"><i class="bi bi-clock-history me-2"></i>Thời Lượng:</span>
                                    <span class="fw-medium"><?= $tourDetails['thoi_luong_mac_dinh'] ?? 0 ?> Ngày</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                    <span class="text-muted"><i class="bi bi-people-fill me-2"></i>Tổng khách:</span>
                                    <span class="badge bg-primary rounded-pill"><?= count($listKhachHang) ?> người</span>
                                </li>
                                <li class="list-group-item px-0 py-3">
                                    <span class="text-muted d-block mb-2"><i class="bi bi-file-text me-2"></i>Ghi chú ngắn:</span>
                                    <div class="bg-light p-3 rounded small text-secondary">
                                        <?= htmlspecialchars($tourDetails['mo_ta_ngan'] ?? 'Không có mô tả.') ?>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card card-detail">
                        <div class="card-header bg-white p-0 border-bottom-0">
                            <ul class="nav nav-tabs px-3 pt-3" id="tourTab" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active fw-bold" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button">
                                        <i class="bi bi-calendar3 me-1"></i>Lịch Trình
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="guests-tab" data-bs-toggle="tab" data-bs-target="#guests" type="button">
                                        <i class="bi bi-person-lines-fill me-1"></i>Danh Sách Khách
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="desc-tab" data-bs-toggle="tab" data-bs-target="#full-desc" type="button">
                                        <i class="bi bi-file-text me-1"></i>Chi Tiết
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body p-4">
                            <div class="tab-content">

                                <div class="tab-pane fade show active" id="schedule">
                                    <?php if (!empty($lich_trinh)): ?>
                                        <div class="timeline ps-2">
                                            <?php foreach ($lich_trinh as $item): ?>
                                                <div class="schedule-item">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div class="schedule-day">
                                                            Ngày <?= $item['ngay_thu'] ?>: <?= htmlspecialchars($item['tieu_de']) ?>
                                                        </div>

                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary fw-bold px-3 shadow-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDiemDanh"
                                                            data-id="<?= $item['lich_trinh_id'] ?? $item['ngay_thu'] ?>"
                                                            data-title="Ngày <?= $item['ngay_thu'] ?>: <?= htmlspecialchars($item['tieu_de']) ?>">
                                                            <i class="bi bi-clipboard-check me-1"></i>Điểm danh
                                                        </button>
                                                    </div>

                                                    <p class="text-secondary mb-0"><?= htmlspecialchars($item['noi_dung']) ?></p>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-5 text-muted">
                                            <i class="bi bi-calendar-x fs-1 opacity-50"></i>
                                            <p class="mt-2">Chưa có dữ liệu lịch trình.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="tab-pane fade" id="guests">
                                    <?php if (!empty($listKhachHang)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="5%">STT</th>
                                                        <th>Họ Tên & CCCD</th>
                                                        <th>Giới tính</th>
                                                        <th>Liên hệ</th>
                                                        <th>Ghi chú</th>
                                                        <th>Trạng thái Đơn</th>
                                                        <th>Hành động</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($listKhachHang as $index => $khach): ?>
                                                        <tr>
                                                            <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                                            <td>
                                                                <div class="fw-bold text-dark"><?= htmlspecialchars($khach['ho_ten']) ?></div>
                                                                <small class="text-muted"><i class="bi bi-card-heading me-1"></i><?= htmlspecialchars($khach['so_giay_to']) ?></small>
                                                            </td>
                                                            <td>
                                                                <?= (strtolower($khach['gioi_tinh']) == 'nam')
                                                                    ? '<span class="badge bg-primary bg-opacity-10 text-primary">Nam</span>'
                                                                    : '<span class="badge bg-danger bg-opacity-10 text-danger">Nữ</span>' ?>
                                                            </td>
                                                            <td>
                                                                <a href="tel:<?= $khach['lien_he'] ?>" class="text-decoration-none">
                                                                    <?= htmlspecialchars($khach['lien_he']) ?>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <?php if (!empty($khach['yeu_cau_ca_nhan'])): ?>
                                                                    <span class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($khach['yeu_cau_ca_nhan']) ?></span>
                                                                <?php else: ?>
                                                                    <span class="text-muted small">---</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-success rounded-pill px-3">
                                                                    <?= htmlspecialchars($khach['trang_thai_don'] ?? 'Active') ?>
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <a href="<?= BASEURL ?>?act=chitiet_khach_hang&id=<?= $khach['hanh_khach_id'] ?>"
                                                                    class="btn btn-sm btn-outline-info"
                                                                    title="Xem hồ sơ chi tiết">
                                                                    <i class="bi bi-person-lines-fill me-1"></i> Chi tiết
                                                                </a>
                                                            </td>

                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info text-center m-3">
                                            Chưa có khách hàng nào trong danh sách.
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="tab-pane fade" id="full-desc">
                                    <div class="p-3 bg-light rounded text-secondary">
                                        <?= htmlspecialchars_decode($tourDetails['mo_ta'] ?? 'Đang cập nhật...') ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="modalDiemDanh" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">

                <form action="<?= BASEURL ?>?act=luu_diem_danh" method="POST">

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-clipboard-check me-2"></i>Điểm Danh: <span id="modalDateTitle" class="text-warning">...</span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-0">
                        <input type="hidden" name="lich_trinh_id" id="inputLichTrinhId" value="">
                        <input type="hidden" name="tour_id" value="<?= $_GET['tour_id'] ?? $tourDetails['tour_id'] ?>">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead class="table-light sticky-top" style="z-index: 1;">
                                    <tr>
                                        <th class="ps-4">Họ và Tên</th>
                                        <th>Liên hệ</th>
                                        <th class="text-center">Xác nhận</th>
                                        <th>Ghi chú (Lý do vắng)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($listKhachHang)): ?>
                                        <?php foreach ($listKhachHang as $kh): ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold"><?= htmlspecialchars($kh['ho_ten']) ?></div>
                                                    <small class="text-muted">Nhóm: #<?= $kh['dat_tour_id'] ?></small>
                                                </td>
                                                <td><?= htmlspecialchars($kh['lien_he']) ?></td>

                                                <td class="text-center">
                                                    <input class="form-check-input form-check-input-lg border-2 border-primary"
                                                        type="checkbox"
                                                        name="status[<?= $kh['hanh_khach_id'] ?>]"
                                                        value="present"
                                                        checked
                                                        title="Bỏ chọn nếu khách vắng">
                                                </td>

                                                <td>
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="note[<?= $kh['hanh_khach_id'] ?>]"
                                                        placeholder="...">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">Không có khách nào để điểm danh.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-save me-1"></i> Lưu Điểm Danh
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modalDiemDanh = document.getElementById('modalDiemDanh');

            modalDiemDanh.addEventListener('show.bs.modal', function(event) {
                // Nút bấm kích hoạt modal
                var button = event.relatedTarget;

                // Lấy dữ liệu từ data attribute của nút
                var dateTitle = button.getAttribute('data-title');
                var scheduleId = button.getAttribute('data-id');

                // Cập nhật tiêu đề và input ẩn trong modal
                modalDiemDanh.querySelector('#modalDateTitle').textContent = dateTitle;
                modalDiemDanh.querySelector('#inputLichTrinhId').value = scheduleId;
            });
        });
    </script>
</body>

</html>