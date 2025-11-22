<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách Đơn Đặt Tour</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        .main-content {
            padding: 20px;
            margin-top: 70px;
            margin-left: 0;
            transition: margin-left .32s ease;
        }
        nav { 
            position: fixed; top: 0; left: 0; height: 70px; width: 100%;
            background: #fff; box-shadow: 0 0 10px rgba(0,0,0,.08); z-index: 1030;
        }
    </style>
</head>
<body class="bg-light">

    <nav>
        <div class="logo">
            <i class="bx bx-menu menu-icon"></i>
            <span class="logo-name">Quản lý Booking</span>
        </div>
    </nav>

    <?php include_once './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid py-4">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0"><i class="bi bi-card-checklist"></i> Danh sách Đơn Đặt Tour</h2>
               <div class="d-flex"> 
                <a href="<?= BASEURL ?>?act=dat_tour_add" class="btn btn-primary me-2">
                    <i class="bi bi-calendar-plus"></i> Tạo Booking Mới
                </a> 
                <a href="<?= BASEURL ?>?act=dat_tour_deleted_list" class="btn btn-warning">
                    <i class="bi bi-trash"></i> Thùng rác
                </a>
               </div>
            </div>

            <?php 
            // Giả định biến $data chứa kết quả từ Model::getAllDatTour()
            if (!empty($data)): 
            ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle bg-white shadow-sm">
                        <thead class="table-primary">
                            <tr>
                                <th>ID</th>
                                <th>Tên Tour</th>
                                <th>Khách Hàng</th>
                                <th>Ngày Khởi Hành</th>
                                <th>SL Người</th>
                                <th>Trạng Thái</th>
                                <th>Ngày Đặt</th>
                                <th width="180">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $row): ?>
                                <?php
                                    // --------------------------------------------------------
                                    // LOGIC HIỂN THỊ DỮ LIỆU ĐÃ CẬP NHẬT
                                    // --------------------------------------------------------
                                    
                                    // 1. Xử lý Tên Tour (lấy từ DT.tour_id -> T.ten)
                                    $ten_tour = !empty($row['ten_tour']) 
                                        ? htmlspecialchars($row['ten_tour']) 
                                        : '<span class="text-danger fw-bold">Chưa chọn Tour</span>';
                                    
                                    // 2. Xử lý Ngày Khởi Hành (lấy từ LKH)
                                    $ngay_bat_dau = !empty($row['ngay_bat_dau']) 
                                        ? date('d/m/Y', strtotime($row['ngay_bat_dau'])) 
                                        : '<span class="text-danger fst-italic">Chưa xếp lịch</span>';
                                    
                                    // 3. Xử lý Trạng Thái (Tạo badge màu)
                                    $trang_thai = htmlspecialchars($row['trang_thai_dat_tour']);
                                    $trang_thai_class = match ($trang_thai) {
                                        'chờ xác nhận' => 'bg-warning text-dark',
                                        'đã đặt cọc' => 'bg-info',
                                        'hoàn tất' => 'bg-success',
                                        'hủy' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                    
                                    // 4. Định dạng ngày đặt
                                    $ngay_dat_tour = date('d/m/Y', strtotime($row['ngay_dat_tour']));
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['dat_tour_id']) ?></td>
                                    <td>
                                        <div class="fw-bold"><?= $ten_tour ?></div>
                                    </td>
                                    <td>
                                        <div class="text-nowrap fw-medium"><?= htmlspecialchars($row['ten_khach_hang']) ?></div>
                                        <small class="text-muted">CCCD: <?= htmlspecialchars($row['cccd']) ?></small>
                                    </td>
                                    <td>
                                        <?= $ngay_bat_dau ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['so_nguoi']) ?></td>
                                    <td>
                                        <span class="badge <?= $trang_thai_class ?>">
                                            <?= $trang_thai ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= $ngay_dat_tour ?>
                                    </td>
                                    <td>
                                        <a href="<?= BASEURL ?>?act=dat_tour_edit&id=<?= $row['dat_tour_id'] ?>" class="btn btn-warning btn-sm mb-1 w-100">
                                            <i class="bi bi-pencil-square"></i> Sửa
                                        </a>
                                        
                                        <a href="<?= BASEURL ?>?act=dat_tour_detail&id=<?= $row['dat_tour_id'] ?>" class="btn btn-info btn-sm mb-1 w-100">
                                            <i class="bi bi-eye"></i> Chi tiết
                                        </a>
                                        <a href="<?= BASEURL ?>?act=dat_tour_delete&id=<?= $row['dat_tour_id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa mềm đơn hàng này không?')" class="btn btn-danger btn-sm w-100">
                                            <i class="bi bi-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info shadow-sm mt-4">
                    <i class="bi bi-info-circle"></i> Hiện tại không có đơn đặt tour đang hoạt động.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>
</html>