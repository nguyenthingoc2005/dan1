<?php
// Giả định dữ liệu (giữ nguyên logic của bạn)
// $tours = [...]; 
$tours = $tours ?? [];
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Tour được Phân công</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">

    <link rel="stylesheet" href="./assets/css/sidebar.css">

    <style>
        /* --- Cấu trúc chung --- */
        body {
            background-color: #f0f2f5;
            /* Màu nền xám xanh hiện đại */
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        .main-content {
            margin-left: 0;
            /* Sẽ thay đổi theo JS của sidebar */
            padding: 2rem;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* --- Card Container --- */
        .tour-table-container {
            background: #fff;
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            /* Đổ bóng mềm */
            overflow: hidden;
            /* Để bo góc hoạt động với bảng bên trong */
        }

        .card-header {
            background-color: #fff;
            padding: 1.5rem;
            border-bottom: 1px solid #f1f1f1;
        }

        .card-title {
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
            font-size: 1.25rem;
        }

        /* --- Table Styling --- */
        .table {
            margin-bottom: 0;
        }

        /* Header Bảng */
        .table-tour-header-custom th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            /* Gradient tím xanh */
            /* Hoặc dùng màu xanh FPT: background: linear-gradient(135deg, #f37021 0%, #00529c 100%); */
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 15px;
            border: none;
            white-space: nowrap;
        }

        /* Dữ liệu Bảng */
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            color: #555;
            border-bottom: 1px solid #f8f9fa;
            font-size: 0.95rem;
        }

        /* Hiệu ứng Hover dòng */
        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9ff;
            transform: scale(1.005);
            /* Phóng to cực nhẹ khi hover */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            z-index: 1;
            position: relative;
        }

        /* --- Custom Columns --- */
        .tour-name {
            font-weight: 600;
            color: #2c3e50;
            min-width: 200px;
        }

        .tour-date {
            font-family: 'Consolas', monospace;
            /* Font số cho ngày tháng */
            color: #666;
            font-size: 0.9rem;
        }

        /* --- Badges (Trạng thái) --- */
        /* Ghi đè style của bootstrap badge để làm Soft UI */
        .badge {
            padding: 8px 12px;
            border-radius: 30px;
            font-weight: 500;
            font-size: 0.8rem;
            letter-spacing: 0.3px;
        }

        .badge.bg-success {
            background-color: rgba(25, 135, 84, 0.1) !important;
            color: #198754 !important;
            border: 1px solid rgba(25, 135, 84, 0.2);
        }

        .badge.bg-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
            color: #d68100 !important;
            /* Màu cam đậm hơn cho dễ đọc */
            border: 1px solid rgba(255, 193, 7, 0.2);
        }

        .badge.bg-secondary {
            background-color: rgba(108, 117, 125, 0.1) !important;
            color: #6c757d !important;
        }

        /* --- Button Action --- */
        .btn-view-detail {
            background-color: #fff;
            color: #667eea;
            border: 1px solid #e1e1e1;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 0.85rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-view-detail:hover {
            background-color: #667eea;
            color: #fff;
            border-color: #667eea;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
            transform: translateY(-2px);
        }

        /* --- Responsive fix --- */
        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }

            .table-responsive {
                border-radius: 15px;
            }
        }
    </style>
</head>

<body>
    <?php include './views/parts/sidebarhdv.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark m-0">Lịch Trình Của Tôi</h2>
                    <p class="text-muted m-0 mt-1 small">Quản lý các tour du lịch bạn được phân công</p>
                </div>
            </div>

            <div class="card tour-table-container">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">
                        <i class="bi bi-briefcase me-2 text-primary"></i> Danh sách Tour hiện có
                    </h5>
                    <span class="badge bg-primary rounded-pill"><?= count($tours) ?> Tour</span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-tour-header-custom">
                                <tr>
                                    <th>Tên Tour</th>
                                    <th class="text-center">Ngày Khởi Hành</th>
                                    <th class="text-center">Ngày Kết Thúc</th>
                                    <th>Hướng Dẫn Viên</th>
                                    <th class="text-center">Trạng Thái</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($tours)): ?>
                                    <?php foreach ($tours as $tour): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="d-flex justify-content-center align-items-center bg-light rounded-circle me-3" style="width: 40px; height: 40px; min-width: 40px;">
                                                        <i class="bi bi-geo-alt-fill text-danger"></i>
                                                    </div>
                                                    <div>
                                                        <div class="tour-name"><?= htmlspecialchars($tour["Ten_Tour"]) ?></div>
                                                        <small class="text-muted">Mã: #<?= $tour["tour_id"] ?? 'N/A' ?></small>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="text-center tour-date">
                                                <i class="bi bi-calendar2-event me-1 text-info"></i>
                                                <?= date('d/m/Y', strtotime($tour["Ngay_Khoi_Hanh"])) ?>
                                            </td>

                                            <td class="text-center tour-date">
                                                <i class="bi bi-calendar2-check me-1 text-danger"></i>
                                                <?= date('d/m/Y', strtotime($tour["Ngay_Ket_Thuc"])) ?>
                                            </td>

                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-person-circle fs-5 me-2 text-secondary"></i>
                                                    <span class="fw-medium"><?= htmlspecialchars($tour["Ten_HDV"]) ?></span>
                                                </div>
                                            </td>

                                            <td class="text-center">
                                                <?php
                                                $status = strtolower($tour["Trang_Thai_Phan_Cong"]);
                                                if ($status == 'confirmed' || $status == 'is_active') {
                                                    echo '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Đã xác nhận</span>';
                                                } elseif ($status == 'pending') {
                                                    echo '<span class="badge bg-warning"><i class="bi bi-hourglass-split me-1"></i>Chờ duyệt</span>';
                                                } else {
                                                    echo '<span class="badge bg-secondary">' . htmlspecialchars($tour["Trang_Thai_Phan_Cong"]) . '</span>';
                                                }
                                                ?>
                                            </td>


                                            <td class="text-center">
                                                <a
                                                    href="<?= BASEURL ?>?act=xem_chitiet_tour&tour_id=<?= $tour['tour_id'] ?? '' ?>"
                                                    class="btn-view-detail text-decoration-none">
                                                    Xem chi tiết <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" alt="Empty" width="80" class="mb-3 opacity-50">
                                            <p class="text-muted fw-bold">Chưa có lịch trình nào được phân công.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if (!empty($tours)): ?>
                    <div class="card-footer bg-white border-top-0 py-3">
                        <small class="text-muted">Hiển thị <?= count($tours) ?> kết quả</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>

</html>