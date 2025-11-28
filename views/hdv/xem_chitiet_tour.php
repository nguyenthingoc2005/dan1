<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Tour Du Lịch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    <style>
        body {
            background-color: #f5f7fb;
        }

        .main-content {
            margin-left: 0;
            padding: 30px;
            transition: margin-left .32s ease;
            min-height: 100vh;
        }

        .card-detail {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .schedule-item {
            border-left: 3px solid #0d6efd;
            padding-left: 15px;
            margin-bottom: 20px;
        }

        .schedule-day {
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 5px;
        }

        .nav-tabs .nav-link.active {
            font-weight: bold;
            color: #0d6efd;
            border-color: #0d6efd #dee2e6 #fff;
        }
    </style>
</head>

<body>
    <?php
    // Giả lập dữ liệu chi tiết Tour (Bạn cần thay thế bằng logic database)
    $tourDetail = [
        "id" => 1,
        "ten_tour" => "Hà Nội - Sapa 3 Ngày 2 Đêm (Khởi hành 05/12/2025)",
        "danh_muc" => "Tour Miền Bắc",
        "mo_ta_ngan" => "Khám phá Fansipan, bản Cát Cát và tận hưởng không khí núi rừng Tây Bắc.",
        "diem_khoi_hanh" => "Hà Nội",
        "ngay_khoi_hanh" => "2025-12-05",
        "thoi_luong" => 3,
        "gia" => 3500000,
        "huong_dan_vien" => "Nguyễn Văn Tèo",
        "tong_so_khach" => 25,
        "lich_trinh" => [
            ["ngay" => 1, "tieu_de" => "Hà Nội - Sapa: Chinh phục đèo Ô Quy Hồ", "chi_tiet" => "Đón khách, di chuyển lên Sapa. Chiều tham quan Thác Bạc và đèo Ô Quy Hồ. Tối nghỉ tại khách sạn."],
            ["ngay" => 2, "tieu_de" => "Fansipan - Bản Cát Cát: Mái nhà Đông Dương", "chi_tiet" => "Sáng đi cáp treo lên đỉnh Fansipan. Chiều thăm bản Cát Cát của người H'Mông. Tối tự do khám phá chợ đêm."],
            ["ngay" => 3, "tieu_de" => "Sapa - Hà Nội: Kết thúc hành trình", "chi_tiet" => "Tham quan nhà thờ đá Sapa và chợ. Trưa trả phòng, lên xe về Hà Nội. Kết thúc tour."],
        ],
        "danh_sach_khach" => [
            ["stt" => 1, "ten_khach" => "Trần Văn A", "sdt" => "090xxxxxxx", "so_luong" => 2, "trang_thai" => "Đã thanh toán"],
            ["stt" => 2, "ten_khach" => "Nguyễn Thị B", "sdt" => "091xxxxxxx", "so_luong" => 4, "trang_thai" => "Đã thanh toán"],
            ["stt" => 3, "ten_khach" => "Phạm Văn C", "sdt" => "092xxxxxxx", "so_luong" => 1, "trang_thai" => "Đã thanh toán"],
            ["stt" => 4, "ten_khach" => "Lê Thị D", "sdt" => "093xxxxxxx", "so_luong" => 3, "trang_thai" => "Đã thanh toán"],
        ]
    ];

    // Đặt BASEURL giả định nếu chưa có
    if (!defined('BASEURL')) {
        define('BASEURL', '');
    }
    ?>
    <?php include './views/parts/sidebarhdv.php'; ?>

    <div class="main-content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark"><i class="bi bi-map-fill me-2"></i>Chi Tiết Tour: <?= htmlspecialchars($tourDetail['ten_tour']) ?></h2>
                <a href="<?= BASEURL ?>?act=tourlist" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left-circle me-1"></i> Quay lại Danh sách
                </a>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card card-detail h-100">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Thông Tin Chung</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong><i class="bi bi-calendar-check me-2"></i>Khởi Hành:</strong>
                                    <span><?= date('d/m/Y', strtotime($tourDetail['ngay_khoi_hanh'])) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong><i class="bi bi-clock me-2"></i>Thời Lượng:</strong>
                                    <span><?= $tourDetail['thoi_luong'] ?> Ngày</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong><i class="bi bi-person-badge me-2"></i>HDV Phụ Trách:</strong>
                                    <span class="fw-bold text-success"><?= htmlspecialchars($tourDetail['huong_dan_vien']) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong><i class="bi bi-people-fill me-2"></i>Tổng Khách:</strong>
                                    <span class="badge bg-danger fs-6"><?= $tourDetail['tong_so_khach'] ?></span>
                                </li>
                                <li class="list-group-item">
                                    <strong><i class="bi bi-tag-fill me-2"></i>Danh Mục:</strong>
                                    <span><?= htmlspecialchars($tourDetail['danh_muc']) ?></span>
                                </li>
                                <li class="list-group-item">
                                    <strong><i class="bi bi-journal-text me-2"></i>Mô Tả:</strong>
                                    <p class="mt-2 text-muted"><?= htmlspecialchars($tourDetail['mo_ta_ngan']) ?></p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card card-detail">
                        <div class="card-header p-0">
                            <ul class="nav nav-tabs" id="tourTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab" aria-controls="schedule" aria-selected="true"><i class="bi bi-list-task me-1"></i>Lịch Trình Chi Tiết</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="guests-tab" data-bs-toggle="tab" data-bs-target="#guests" type="button" role="tab" aria-controls="guests" aria-selected="false"><i class="bi bi-person-check-fill me-1"></i>Danh Sách Khách Hàng</button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="tourTabContent">

                                <div class="tab-pane fade show active" id="schedule" role="tabpanel" aria-labelledby="schedule-tab">
                                    <?php foreach ($tourDetail['lich_trinh'] as $item): ?>
                                        <div class="schedule-item">
                                            <div class="schedule-day">Ngày <?= $item['ngay'] ?>: <?= htmlspecialchars($item['tieu_de']) ?></div>
                                            <p class="text-muted small mb-0"><?= htmlspecialchars($item['chi_tiet']) ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="tab-pane fade" id="guests" role="tabpanel" aria-labelledby="guests-tab">
                                    <div class="d-flex justify-content-between mb-3">
                                        <p class="text-muted mb-0">Hiển thị các booking **Đã thanh toán/Xác nhận** để HDV tiện quản lý.</p>
                                        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark-arrow-down-fill me-1"></i> Xuất Excel</button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover align-middle">
                                            <thead>
                                                <tr class="table-light">
                                                    <th>#</th>
                                                    <th>Tên Khách Hàng (Booking)</th>
                                                    <th>SĐT</th>
                                                    <th class="text-center">Số Lượng Khách</th>
                                                    <th>Trạng Thái</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($tourDetail['danh_sach_khach'] as $guest): ?>
                                                    <tr>
                                                        <td><?= $guest['stt'] ?></td>
                                                        <td class="fw-bold"><?= htmlspecialchars($guest['ten_khach']) ?></td>
                                                        <td><?= $guest['sdt'] ?></td>
                                                        <td class="text-center"><span class="badge bg-primary"><?= $guest['so_luong'] ?></span></td>
                                                        <td><span class="badge bg-success"><?= htmlspecialchars($guest['trang_thai']) ?></span></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-end text-muted small mt-3">Tổng cộng: <?= count($tourDetail['danh_sach_khach']) ?> Booking</div>
                                </div>

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