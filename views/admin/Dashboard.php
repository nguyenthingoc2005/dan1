<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        body { background-color: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .main-content { padding: 30px; margin-left: 0; margin-top: 70px; transition: margin-left .32s ease; min-height: 100vh; }
        nav { position: fixed; top: 0; left: 0; height: 70px; width: 100%; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,.08); z-index: 1030; display: flex; align-items: center; padding: 0 20px; }

        /* STAT CARDS */
        .stat-card {
            border: none; border-radius: 15px; background: #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: transform 0.3s;
            overflow: hidden; position: relative; height: 100%;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon {
            width: 50px; height: 50px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; margin-bottom: 10px;
        }
        
        .card-blue .stat-icon { background: #e7f1ff; color: #0d6efd; }
        .card-green .stat-icon { background: #d1e7dd; color: #198754; }
        .card-orange .stat-icon { background: #fff3cd; color: #ffc107; }
        .card-purple .stat-icon { background: #e0cffc; color: #6f42c1; }

        .stat-value { font-size: 1.8rem; font-weight: 700; color: #2c3e50; margin-bottom: 0; }
        .stat-label { color: #6c757d; font-size: 0.9rem; font-weight: 500; }

        /* DASHBOARD TABLES */
        .dashboard-card {
            border: none; border-radius: 12px; background: #fff;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05); height: 100%;
        }
        .card-header-custom {
            padding: 20px; border-bottom: 1px solid #f0f0f0;
            display: flex; justify-content: space-between; align-items: center;
        }
        .card-title { font-weight: 700; color: #344767; margin: 0; font-size: 1.1rem; }
        
        .table-custom thead th {
            background-color: #f8f9fa; font-size: 0.75rem; text-transform: uppercase;
            color: #8392ab; padding: 12px 15px; border-bottom: 1px solid #e9ecef;
        }
        .table-custom tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #f1f1f1; font-size: 0.9rem; }
    </style>
</head>
<body>

    <nav>
        <div class="logo d-flex align-items-center">
            <i class="bx bx-menu menu-icon fs-3 me-3" style="cursor: pointer;"></i>
            <span class="logo-name fs-4 fw-bold">Admin Dashboard</span>
        </div>
    </nav>

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card card-green p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-label">Tổng Doanh Thu</div>
                                <h3 class="stat-value"><?= number_format($stats['revenue']) ?> <small style="font-size: 1rem">đ</small></h3>
                            </div>
                            <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                        </div>
                        <div class="mt-2 text-success small"><i class="bi bi-graph-up-arrow"></i> Ước tính</div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card card-blue p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-label">Tổng Booking</div>
                                <h3 class="stat-value"><?= $stats['bookings'] ?></h3>
                            </div>
                            <div class="stat-icon"><i class="bi bi-receipt"></i></div>
                        </div>
                        <div class="mt-2 text-primary small"><i class="bi bi-clock-history"></i> Toàn thời gian</div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card card-orange p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-label">Tour Đang Chạy</div>
                                <h3 class="stat-value"><?= $stats['active_tours'] ?></h3>
                            </div>
                            <div class="stat-icon"><i class="bi bi-map"></i></div>
                        </div>
                        <div class="mt-2 text-warning small"><i class="bi bi-check-circle"></i> Sản phẩm active</div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card card-purple p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-label">Khách Hàng</div>
                                <h3 class="stat-value"><?= $stats['customers'] ?></h3>
                            </div>
                            <div class="stat-icon"><i class="bi bi-people"></i></div>
                        </div>
                        <div class="mt-2 text-secondary small"><i class="bi bi-person-plus"></i> Đã đăng ký</div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="card-header-custom">
                            <h5 class="card-title">Biểu đồ Doanh Thu (2025)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="dashboard-card">
                        <div class="card-header-custom">
                            <h5 class="card-title">Trạng Thái Đơn</h5>
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
                            <h5 class="card-title">Booking Mới Nhất</h5>
                            <a href="<?= BASEURL ?>?act=dattourlist" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
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
                                    <?php if(!empty($recentBookings)): ?>
                                        <?php foreach($recentBookings as $bk): ?>
                                        <tr>
                                            <td class="fw-bold text-primary">#<?= $bk['dat_tour_id'] ?></td>
                                            <td><?= htmlspecialchars($bk['ten_khach_hang'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($bk['ten_tour'] ?? 'N/A') ?></td>
                                            <td>
                                                <?php 
                                                    $st = strtolower($bk['trang_thai_dat_tour'] ?? '');
                                                    $cls = 'secondary';
                                                    if(strpos($st, 'hoàn tất')!==false) $cls='success';
                                                    if(strpos($st, 'chờ')!==false) $cls='warning';
                                                    if(strpos($st, 'hủy')!==false) $cls='danger';
                                                ?>
                                                <span class="badge bg-<?= $cls ?> bg-opacity-25 text-<?= $cls ?> rounded-pill">
                                                    <?= htmlspecialchars($bk['trang_thai_dat_tour']) ?>
                                                </span>
                                            </td>
                                            <td class="text-secondary"><?= date('d/m', strtotime($bk['ngay_dat_tour'])) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center text-muted">Chưa có đơn hàng nào.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="dashboard-card">
                        <div class="card-header-custom">
                            <h5 class="card-title">Sắp Khởi Hành</h5>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php if(!empty($upcomingTours)): ?>
                                    <?php foreach($upcomingTours as $t): ?>
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
        const ctxRev = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctxRev, {
            type: 'line',
            data: {
                labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                datasets: [{
                    label: 'Doanh Thu (Triệu VNĐ)',
                    data: [12, 19, 3, 5, 2, 3, 15, 20, 25, 22, 30, 45],
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Thành công', 'Chờ xử lý', 'Hủy'],
                datasets: [{
                    data: [<?= $stats['bookings'] - 5 ?>, 3, 2], // Logic giả lập tỉ lệ
                    backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, cutout: '75%' }
        });
    </script>
</body>
</html>