<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Hướng Dẫn Viên Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="./assets/css/sidebar.css">

    <style>
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', sans-serif;
        }

        .main-content {
            padding: 30px;
            margin-left: 0;
            margin-top: 70px;
            transition: margin-left .32s ease;
            min-height: 100vh;
        }

        nav {
            position: fixed;
            top: 0;
            left: 0;
            height: 70px;
            width: 100%;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, .08);
            z-index: 1030;
            display: flex;
            align-items: center;
            padding: 0 20px;
        }

        /* STAT CARDS */
        .stat-card {
            border: none;
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
            overflow: hidden;
            position: relative;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .card-blue .stat-icon {
            background: #e7f1ff;
            color: #0d6efd;
        }

        .card-green .stat-icon {
            background: #d1e7dd;
            color: #198754;
        }

        .card-orange .stat-icon {
            background: #fff3cd;
            color: #ffc107;
        }

        .card-purple .stat-icon {
            background: #e0cffc;
            color: #6f42c1;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* DASHBOARD TABLES */
        .dashboard-card {
            border: none;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .card-header-custom {
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-weight: 700;
            color: #344767;
            margin: 0;
            font-size: 1.1rem;
        }

        .table-custom thead th {
            background-color: #f8f9fa;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #8392ab;
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .table-custom tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f1f1;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <?php
    // --- DỮ LIỆU GIẢ LẬP CHO HƯỚNG DẪN VIÊN DASHBOARD ---

    // Giả lập dữ liệu thống kê cho Hướng dẫn viên
    $guideStats = [
        'tours_completed' => 25, // Tổng Tour Đã Dẫn
        'upcoming_tours_guide' => 3, // Tour Sắp Khởi Hành (trong 7 ngày tới)
        'customers_served' => 450, // Khách Hàng Phục Vụ
        'average_rating' => 4.8, // Đánh Giá TB
    ];

    // Giả lập dữ liệu cho biểu đồ số tour hàng tháng (Đã Dẫn)
    // Tương ứng với T1 -> T12
    $guideToursMonthlyData = [2, 1, 3, 2, 4, 5, 3, 4, 6, 4, 5, 7];

    // Giả lập dữ liệu cho biểu đồ trạng thái booking sắp đi của HDV (Đã xác nhận, Chờ, Hủy)
    $guideBookingStatusData = [10, 3, 1];

    // Giả lập danh sách booking mới
    $recentBookings = [
        [
            'dat_tour_id' => 105,
            'ten_khach_hang' => 'Trần Văn A',
            'ten_tour' => 'Hà Nội - Sapa 3N2D',
            'trang_thai_dat_tour' => 'Đã xác nhận',
            'ngay_dat_tour' => '2025-11-25 10:30:00'
        ],
        [
            'dat_tour_id' => 104,
            'ten_khach_hang' => 'Nguyễn Thị B',
            'ten_tour' => 'Hạ Long - Cát Bà 2N1D',
            'trang_thai_dat_tour' => 'Chờ xử lý',
            'ngay_dat_tour' => '2025-11-24 14:00:00'
        ],
        [
            'dat_tour_id' => 103,
            'ten_khach_hang' => 'Phạm Văn C',
            'ten_tour' => 'Tour Đà Nẵng 4N3D',
            'trang_thai_dat_tour' => 'Đã xác nhận',
            'ngay_dat_tour' => '2025-11-23 09:15:00'
        ],
        [
            'dat_tour_id' => 102,
            'ten_khach_hang' => 'Lê Thị D',
            'ten_tour' => 'Hà Nội - Sapa 3N2D',
            'trang_thai_dat_tour' => 'Đã xác nhận',
            'ngay_dat_tour' => '2025-11-22 18:45:00'
        ],
    ];

    // Giả lập danh sách tour sắp khởi hành của HDV
    $upcomingTours = [
        [
            'ten_tour' => 'Hà Nội - Sapa 3N2D',
            'ngay_bat_dau' => '2025-12-05',
        ],
        [
            'ten_tour' => 'Hạ Long - Cát Bà 2N1D',
            'ngay_bat_dau' => '2025-12-10',
        ],
        [
            'ten_tour' => 'Tour Fansipan 1 Ngày',
            'ngay_bat_dau' => '2025-12-15',
        ],
    ];

    // Dùng chung biến $stats
    $stats = $guideStats;
    ?>
    <nav>
        <div class="logo d-flex align-items-center">
            <i class="bx bx-menu menu-icon fs-3 me-3" style="cursor: pointer;"></i>
            <span class="logo-name fs-4 fw-bold">HDV Dashboard</span>
        </div>
    </nav>

    <?php
    if ($_SESSION['user']['vai_tro_id'] == 1) {
        include './views/parts/sidebar.php';
    } else {
        include './views/parts/sidebarhdv.php';
    }
    ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">

            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card card-blue p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-label">Tổng Tour Đã Dẫn</div>
                                <h3 class="stat-value"><?= $stats['tours_completed'] ?></h3>
                            </div>
                            <div class="stat-icon"><i class="bi bi-geo-alt"></i></div>
                        </div>
                        <div class="mt-2 text-primary small"><i class="bi bi-calendar-check"></i> Đã hoàn tất</div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card card-orange p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-label">Tour Sắp Khởi Hành</div>
                                <h3 class="stat-value"><?= $stats['upcoming_tours_guide'] ?></h3>
                            </div>
                            <div class="stat-icon"><i class="bi bi-clock"></i></div>
                        </div>
                        <div class="mt-2 text-warning small"><i class="bi bi-calendar"></i> Trong 7 ngày tới</div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card card-green p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-label">Khách Hàng Phục Vụ</div>
                                <h3 class="stat-value"><?= $stats['customers_served'] ?></h3>
                            </div>
                            <div class="stat-icon"><i class="bi bi-person-fill"></i></div>
                        </div>
                        <div class="mt-2 text-success small"><i class="bi bi-people"></i> Khách hàng của bạn</div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card card-purple p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-label">Đánh Giá TB</div>
                                <h3 class="stat-value"><?= number_format($stats['average_rating'], 1) ?> <small style="font-size: 1rem">/ 5.0</small></h3>
                            </div>
                            <div class="stat-icon"><i class="bi bi-star"></i></div>
                        </div>
                        <div class="mt-2 text-secondary small"><i class="bi bi-chat"></i> Từ khách hàng</div>
                    </div>
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="card-header-custom">
                            <h5 class="card-title">Biểu đồ Số Tour Hàng Tháng (2025)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="toursMonthlyChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="dashboard-card">
                        <div class="card-header-custom">
                            <h5 class="card-title">Trạng Thái Booking Khách Sắp Đi</h5>
                        </div>
                        <div class="card-body d-flex justify-content-center">
                            <div style="width: 250px; height: 250px;">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="card-header-custom">
                            <h5 class="card-title">Danh Sách Booking Mới Của Tour Phụ Trách</h5>
                            <a href="<?= BASEURL ?>?act=dattourlist_guide" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th>Mã Đơn</th>
                                        <th>Khách Hàng</th>
                                        <th>Tour</th>
                                        <th>Trạng Thái</th>
                                        <th>Ngày Đặt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recentBookings)): ?>
                                        <?php foreach ($recentBookings as $bk): ?>
                                            <tr>
                                                <td class="fw-bold text-primary">#<?= $bk['dat_tour_id'] ?></td>
                                                <td><?= htmlspecialchars($bk['ten_khach_hang'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($bk['ten_tour'] ?? 'N/A') ?></td>
                                                <td>
                                                    <?php
                                                    $st = strtolower($bk['trang_thai_dat_tour'] ?? '');
                                                    $cls = 'secondary';
                                                    if (strpos($st, 'hoàn tất') !== false || strpos($st, 'xác nhận') !== false) $cls = 'success';
                                                    if (strpos($st, 'chờ') !== false) $cls = 'warning';
                                                    if (strpos($st, 'hủy') !== false) $cls = 'danger';
                                                    ?>
                                                    <span class="badge bg-<?= $cls ?> bg-opacity-25 text-<?= $cls ?> rounded-pill">
                                                        <?= htmlspecialchars($bk['trang_thai_dat_tour']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-secondary"><?= date('d/m', strtotime($bk['ngay_dat_tour'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Chưa có đơn hàng nào liên quan.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="dashboard-card">
                        <div class="card-header-custom">
                            <h5 class="card-title">Tour Của Bạn Sắp Khởi Hành</h5>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php if (!empty($upcomingTours)): ?>
                                    <?php foreach ($upcomingTours as $t): ?>
                                        <li class="list-group-item p-3 border-bottom-0 border-top">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded p-2 me-3 text-primary">
                                                    <i class="bi bi-calendar-check fs-4"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 180px;">
                                                        <?= htmlspecialchars($t['ten_tour']) ?>
                                                    </h6>
                                                    <small class="text-muted">
                                                        Khởi hành: <span class="text-danger fw-bold"><?= date('d/m/Y', strtotime($t['ngay_bat_dau'])) ?></span>
                                                    </small>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-4 text-muted">Không có tour nào sắp đi trong 7 ngày tới.</div>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>

    <script>
        // Lấy dữ liệu PHP vào JavaScript
        const toursMonthlyData = <?= json_encode($guideToursMonthlyData) ?>;
        const bookingStatusData = <?= json_encode($guideBookingStatusData) ?>;

        // Biểu đồ Tour Hàng Tháng cho HDV
        const ctxTours = document.getElementById('toursMonthlyChart').getContext('2d');
        new Chart(ctxTours, {
            type: 'bar',
            data: {
                labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                datasets: [{
                    label: 'Số Tour Đã Dẫn',
                    data: toursMonthlyData, // Đã cập nhật
                    backgroundColor: '#0d6efd',
                    borderColor: '#0d6efd',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Biểu đồ Trạng thái đơn (Giữ nguyên logic)
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Đã xác nhận', 'Chờ xử lý', 'Đã hủy'],
                datasets: [{
                    data: bookingStatusData, // Đã cập nhật
                    backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '75%'
            }
        });
    </script>
</body>

</html>