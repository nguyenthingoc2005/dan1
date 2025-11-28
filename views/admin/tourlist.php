<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Danh sách Tour</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    
    <link rel="stylesheet" href="./assets/css/sidebar.css">

    <style>
        .table-responsive {
            /* Quan trọng: Cần overflow: auto để có thanh cuộn ngang */
            overflow-x: auto; 
        }

        .tour-table {
            table-layout: fixed;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            /* Đã tăng min-width lên 1150px để chắc chắn chứa đủ cột */
            min-width: 1150px; 
        }

        .tour-table th, .tour-table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 12px 6px;
            vertical-align: middle;
            background-color: white; /* Cần màu nền để sticky hoạt động tốt */
        }

        .tour-table thead th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 700;
            border-bottom: 2px solid #0d6efd;
            text-transform: uppercase;
            font-size: 0.8rem;
            position: sticky; /* Cố định header */
            top: 0;
            z-index: 10;
        }

        /* ------------------------------------------- */
        /* CỐ ĐỊNH CỘT ĐỂ TRÁNH BỊ CHE KHUẤT (STICKY COLUMNS) */
        /* ------------------------------------------- */

        /* Cột ID (Cố định bên trái) */
        .tour-table th.col-id, 
        .tour-table td.col-id {
            position: sticky;
            left: 0;
            z-index: 11;
            background-color: #ffffff;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
        }
        .tour-table thead th.col-id {
            background-color: #f8f9fa;
            z-index: 12; 
        }

        /* Cột Thao tác (Cố định bên phải) */
        .tour-table th.col-actions-group, 
        .tour-table td.col-actions-group {
            position: sticky;
            right: 0;
            /* FIX LỖI: Tăng Z-index để đảm bảo nó luôn nằm trên các cột khác */
            z-index: 15; 
            background-color: #ffffff;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1); /* Bóng đậm hơn cho dễ nhìn */
        }
        .tour-table thead th.col-actions-group {
            background-color: #f8f9fa;
            z-index: 16;
        }
        
        /* FIX LỖI ĐÈ CỘT: Cột sát cột sticky phải có Z-index thấp hơn cột sticky */
        .tour-table td.col-date {
            z-index: 9; 
        }
        .tour-table th.col-date {
            z-index: 9;
        }


        /* ĐỘ RỘNG CỘT (Đã chuyển sang px để FIX lỗi đè cột) */
        .tour-table .col-id { width: 50px; min-width: 50px; }
        .tour-table .col-name { width: 220px; min-width: 220px; }
        .tour-table .col-cat { width: 100px; min-width: 100px; }
        .tour-table .col-price { width: 120px; min-width: 120px; }
        .tour-table .col-time { width: 80px; min-width: 80px; }
        .tour-table .col-start { width: 100px; min-width: 100px; }
        .tour-table .col-active { width: 130px; min-width: 140px; }
        .tour-table .col-date { width: 110px; min-width: 110px; }
        .tour-table .col-actions-group { width: 200px; min-width: 200px; }


        .action-group {
            display: flex;
            gap: 4px;
            justify-content: center;
            align-items: center;
        }

        /* STYLE NÚT HÀNH ĐỘNG (CÓ MÀU & NỀN NHẠT) */
        .btn-icon {
            border: none;
            padding: 5px 8px;
            border-radius: 6px;
            transition: all 0.2s;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-view { background-color: #e7f1ff; color: #0d6efd; }
        .btn-view:hover { background-color: #0d6efd; color: white; }

        .btn-edit { background-color: #fff3cd; color: #d68c09; }
        .btn-edit:hover { background-color: #ffc107; color: black; }

        .btn-delete { background-color: #ffe5e5; color: #dc3545; }
        .btn-delete:hover { background-color: #dc3545; color: white; }

        .btn-tools { background-color: #e0faff; color: #0dcaf0; }
        .btn-tools:hover { background-color: #0dcaf0; color: white; }


        .status-badge {
            display: inline-block;
            padding: 6px;
            font-size: 0.85rem;
            font-weight: 500; 
            border-radius: 50px;
            background-color: #ffffff;
            color: #127953ff !important; 
            border: 1px solid #0d6efd; 
            text-align: center;
            width: 100%;
            min-width: 110px;
            box-shadow: none; 
        }
        
        .status-inactive {
            border-color: #adb5bd;
            color: #6c757d !important;
            background-color: #f8f9fa;
        }

        .card-table {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        
        .text-primary-custom {
            color: #0d6efd !important;
            font-weight: 700;
        }
    </style>
</head>

<body class="bg-light">

    <?php include './views/parts/sidebar.php'; ?>

    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid py-4">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0 text-primary-custom">
                    <i class="bi bi-list-columns-reverse me-2"></i> Danh Sách Quản Lý Tour
                </h2>
                <a href="<?= BASEURL ?>?act=addtour" class="btn btn-success shadow-sm fw-bold">
                    <i class="bi bi-plus-circle"></i> Thêm tour mới
                </a>
            </div>
            <hr class="mb-4">

            <?php if (!empty($data1)): ?>
                <div class="card card-table">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle tour-table mb-0">
                                <thead>
                                    <tr>
                                        <th class="col-id">ID</th>
                                        <th class="col-name">Tên tour</th>
                                        <th class="col-cat">Danh mục</th>
                                        <th class="col-price">Giá cơ bản</th>
                                        <th class="col-time">Thời lượng</th>
                                        <th class="col-start">Khởi hành</th>
                                        <th class="col-active">Hoạt động</th>
                                        <th class="col-date">Ngày tạo</th>
                                        <th class="col-actions-group text-center">Thao tác</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data1 as $tour): ?>
                                        <tr>
                                            <td class="col-id text-center fw-bold text-secondary"><?= htmlspecialchars($tour['tour_id']) ?></td>
                                            
                                            <td class="col-name" title="<?= htmlspecialchars($tour['ten']) ?>">
                                                <span class="fw-bold text-dark"><?= htmlspecialchars($tour['ten']) ?></span>
                                            </td>
                                            
                                            <td class="col-cat text-secondary"><?= htmlspecialchars($tour['ten_danh_muc']) ?></td>
                                            
                                            <td class="col-price fw-bold text-danger">
                                                <?= number_format($tour['gia_co_ban']) ?> <span class="small text-muted">VND</span>
                                            </td>
                                            
                                            <td class="col-time"><?= htmlspecialchars($tour['thoi_luong_mac_dinh']) ?> ngày</td>
                                            
                                            <td class="col-start"><?= htmlspecialchars($tour['diem_khoi_hanh']) ?></td>
                                            
                                            <td class="col-active text-center" >
                                                <span class="status-badge <?= $tour['hoat_dong'] ? '' : 'status-inactive' ?>">
                                                    <?= $tour['hoat_dong'] ? 'Đang hoạt động' : 'Tạm dừng' ?>
                                                </span>
                                            </td>
                                            
                                            <td class="col-date text-secondary small">
                                                <?= date('d/m/Y', strtotime($tour['ngay_tao'])) ?>
                                            </td>

                                            <td class="col-actions-group text-center">
                                                <div class="action-group">
                                                    <a href="<?= BASEURL ?>?act=chitiettour&tour_id=<?= htmlspecialchars($tour['tour_id']) ?>" 
                                                        class="btn-icon btn-view" 
                                                        title="Xem chi tiết">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </a>

                                                    <a href="<?= BASEURL ?>?act=uppdatetour&tour_id=<?= htmlspecialchars($tour['tour_id']) ?>" 
                                                        class="btn-icon btn-edit" 
                                                        title="Chỉnh sửa">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    
                                                    <a href="<?= BASEURL ?>?act=deletetour&tour_id=<?= htmlspecialchars($tour['tour_id']) ?>" 
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa tour này không?')" 
                                                        class="btn-icon btn-delete" 
                                                        title="Xóa Tour">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </a>

                                                   
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info shadow-sm mt-4 p-4 text-center">
                    <h4 class="alert-heading"><i class="bi bi-info-circle"></i> Chưa có dữ liệu Tour nào.</h4>
                    <p>Hãy thêm tour đầu tiên để bắt đầu quản lý.</p>
                    <a href="<?= BASEURL ?>?act=addtour" class="btn btn-success btn-lg">
                        <i class="bi bi-plus-circle"></i> Thêm Tour Đầu Tiên
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>

</html>