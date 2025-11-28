<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Tour Du lịch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">

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

        .table-tour-header-custom {
            background-color: #0d6efd !important;
            color: white !important;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tour-table-container {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            background-color: #fff;
        }

        .table> :not(caption)>*>* {
            padding: 1rem 1rem;
        }

        .table tbody tr:hover {
            background-color: #e9ecef;
        }

        .col-price {
            text-align: right;
            font-weight: bold;
            color: #198754;
        }

        .col-duration {
            text-align: center;
        }

        /* Căn giữa cột hành động */
        .col-action {
            text-align: center;
            width: 100px;
        }
    </style>
</head>

<body>
    <?php include './views/parts/sidebarhdv.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4 fw-bold text-dark">🌍 Danh Sách Tour Du Lịch</h2>

            <div class="card tour-table-container">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary"><i class="bi bi-list-stars me-2"></i>Quản lý sản phẩm Tour</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-tour-header-custom">
                                <tr>
                                    <th><i class="bi bi-card-heading"></i> Tên tour</th>
                                    <th><i class="bi bi-folder-fill"></i> Danh mục</th>
                                    <th><i class="bi bi-journals"></i> Mô tả ngắn</th>
                                    <th><i class="bi bi-geo-alt-fill"></i> Khởi hành</th>
                                    <th class="col-price"><i class="bi bi-cash-coin"></i> Giá</th>
                                    <th class="col-duration"><i class="bi bi-clock-fill"></i> Thời lượng</th>
                                    <th class="col-action"><i class="bi bi-gear-fill"></i> Hành động</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                // Dữ liệu giả lập cho biến $tours (Đã thêm trường "id" giả định)
                                $tours = $tours ?? [
                                    [
                                        "id" => 1, // ID giả định
                                        "ten" => "Hà Nội - Sapa 3N2D",
                                        "ten_danh_muc" => "Tour Miền Bắc",
                                        "mo_ta_ngan" => "Khám phá Fansipan và nét văn hóa độc đáo của Sapa.",
                                        "diem_khoi_hanh" => "Hà Nội",
                                        "gia_co_ban" => 3500000,
                                        "thoi_luong_mac_dinh" => 3
                                    ],
                                    [
                                        "id" => 2, // ID giả định
                                        "ten" => "Đà Nẵng - Hội An 4N3D",
                                        "ten_danh_muc" => "Tour Miền Trung",
                                        "mo_ta_ngan" => "Tận hưởng biển Mỹ Khê và phố cổ Hội An lãng mạn.",
                                        "diem_khoi_hanh" => "Đà Nẵng",
                                        "gia_co_ban" => 5200000,
                                        "thoi_luong_mac_dinh" => 4
                                    ],
                                    [
                                        "id" => 3, // ID giả định
                                        "ten" => "TP.HCM - Phú Quốc 3N2D",
                                        "ten_danh_muc" => "Tour Miền Nam",
                                        "mo_ta_ngan" => "Nghỉ dưỡng tại đảo ngọc, vui chơi tại VinWonders.",
                                        "diem_khoi_hanh" => "TP.HCM",
                                        "gia_co_ban" => 4800000,
                                        "thoi_luong_mac_dinh" => 3
                                    ],
                                ];
                                ?>
                                <?php foreach ($tours as $tour): ?>
                                    <tr>
                                        <td class="fw-bold text-primary"><?= htmlspecialchars($tour["ten"]) ?></td>
                                        <td><?= htmlspecialchars($tour["ten_danh_muc"]) ?></td>
                                        <td><?= htmlspecialchars($tour["mo_ta_ngan"]) ?></td>
                                        <td><?= htmlspecialchars($tour["diem_khoi_hanh"]) ?></td>
                                        <td class="col-price"><?= number_format($tour["gia_co_ban"], 0, ',', '.') ?> đ</td>
                                        <td class="col-duration"><?= htmlspecialchars($tour["thoi_luong_mac_dinh"]) ?> ngày</td>
                                        <td class="col-action">
                                            <a href="<?= BASEURL ?>?act=xem_chitiet_tour&id=<?= $tour["tour_id"] ?>" class="btn btn-sm btn-primary"> Xem chi tiết </a>

                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-light border-0 py-3 text-muted">
                    Tổng cộng: <?= count($tours) ?> tour đang hoạt động.
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>

</html>