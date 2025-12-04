<?php
// =================================================================================
// PHẦN 1: HÀM LỌC DỮ LIỆU (SEARCH + FILTER STATUS LOGIC MỚI)
// =================================================================================

function filterDatTourData($data) {
    // Lấy từ khóa và trạng thái từ URL (GET)
    $keyword = isset($_GET['keyword']) ? mb_strtolower(trim($_GET['keyword']), 'UTF-8') : '';
    $status  = isset($_GET['status']) ? mb_strtolower(trim($_GET['status']), 'UTF-8') : '';

    // Nếu không nhập gì và không chọn trạng thái -> Trả về data gốc
    if (empty($keyword) && empty($status)) {
        return $data;
    }

    $filtered_list = [];

    foreach ($data as $row) {
        // Chuẩn hóa dữ liệu dòng hiện tại về chữ thường để so sánh
        $id         = mb_strtolower((string)($row['dat_tour_id'] ?? ''), 'UTF-8');
        $ten_tour   = mb_strtolower((string)($row['ten_tour'] ?? ''), 'UTF-8');
        $ten_khach  = mb_strtolower((string)($row['ten_khach_hang'] ?? ''), 'UTF-8');
        $sdt        = mb_strtolower((string)($row['sdt'] ?? ''), 'UTF-8');
        $cccd       = mb_strtolower((string)($row['cccd'] ?? ''), 'UTF-8');
        
        // Lấy trạng thái trong DB
        $row_status = mb_strtolower((string)($row['trang_thai_dat_tour'] ?? ''), 'UTF-8'); 

        // 1. Kiểm tra Từ khóa (Nếu có nhập)
        $match_keyword = true; 
        if (!empty($keyword)) {
            $match_keyword = (
                strpos($id, $keyword) !== false || 
                strpos($ten_tour, $keyword) !== false || 
                strpos($ten_khach, $keyword) !== false || 
                strpos($sdt, $keyword) !== false || 
                strpos($cccd, $keyword) !== false
            );
        }

        // 2. Kiểm tra Trạng thái (THEO LOGIC YÊU CẦU)
        $match_status = true; 
        if (!empty($status)) {
            if ($status == 'chờ xác nhận') {
                // Nếu chọn "Chờ xác nhận": Chỉ lấy đúng trạng thái chờ/pending
                $match_status = ($row_status == 'chờ xác nhận' || $row_status == 'pending');
            } 
            elseif ($status == 'đã xác nhận') {
                // Nếu chọn "Đã xác nhận": Lấy TẤT CẢ trạng thái khác, TRỪ thằng chờ xác nhận
                $match_status = ($row_status != 'chờ xác nhận' && $row_status != 'pending');
            }
        }

        // Nếu thỏa mãn CẢ HAI điều kiện -> Thêm vào danh sách kết quả
        if ($match_keyword && $match_status) {
            $filtered_list[] = $row;
        }
    }
    
    return $filtered_list;
}

// Gọi hàm lọc nếu có dữ liệu
if (!empty($data)) {
    $data = filterDatTourData($data);
}
?>

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
        /* GIỮ NGUYÊN CSS GỐC ĐỂ KHÔNG MẤT SIDEBAR */
        .main-content {
            padding: 30px;
            margin-top: 70px;
            margin-left: 0;   
            transition: margin-left .32s ease;
            background-color: #f5f7fb; 
            min-height: 100vh;
        }
        
        nav { 
            position: fixed; top: 0; left: 0; height: 70px; width: 100%;
            background: #fff; box-shadow: 0 0 10px rgba(0,0,0,.08); z-index: 1030;
            display: flex; align-items: center; padding: 0 20px;
        }

        /* Card & Table Custom Style */
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); background: #fff; overflow: hidden; }
        .table-custom thead th { background-color: #f8f9fa; color: #6c757d; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 2px solid #e9ecef; padding: 15px; }
        .table-custom tbody td { vertical-align: middle; padding: 15px; border-bottom: 1px solid #f1f1f1; font-size: 0.9rem; }
        .table-custom tbody tr:hover { background-color: #fcfcfc; }

        /* UI Elements */
        .avatar-circle { width: 40px; height: 40px; background-color: #e9ecef; color: #495057; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; }
        
        /* Soft Badges */
        .badge-soft { padding: 8px 12px; border-radius: 6px; font-weight: 500; }
        .badge-soft-success { background-color: #d1e7dd; color: #0f5132; }
        .badge-soft-warning { background-color: #fff3cd; color: #664d03; }
        .badge-soft-info { background-color: #cff4fc; color: #055160; }
        .badge-soft-danger { background-color: #f8d7da; color: #842029; }
        .badge-soft-secondary { background-color: #e2e3e5; color: #41464b; }

        /* Buttons */
        .btn-icon { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; transition: all 0.2s; color: #6c757d; background: #f8f9fa; }
        .btn-icon:hover { transform: translateY(-2px); }
        .btn-icon.edit:hover { color: #ffc107; background: #fff3cd; }
        .btn-icon.view:hover { color: #0dcaf0; background: #cff4fc; }
        .btn-icon.delete:hover { color: #dc3545; background: #f8d7da; }

        /* Search Input */
        .search-box .form-control { border-radius: 20px; padding-left: 40px; border: 1px solid #e9ecef; background-color: #f8f9fa; }
        .search-box .bi-search { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #adb5bd; }
    </style>
</head>
<body>

    <nav>
        <div class="logo d-flex align-items-center">
            <i class="bx bx-menu menu-icon fs-3 me-3" style="cursor: pointer;"></i>
            <span class="logo-name fs-4 fw-bold">Quản lý Booking</span>
        </div>
    </nav>

    <?php include_once './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Danh Sách Đặt Tour</h3>
                    <p class="text-muted mb-0">Quản lý các đơn đặt tour của khách hàng.</p>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="<?= BASEURL ?>?act=dat_tour_deleted_list" class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="bi bi-trash me-2"></i> Thùng rác
                    </a>
                    <a href="<?= BASEURL ?>?act=dat_tour_add" class="btn btn-primary d-flex align-items-center px-4 shadow-sm">
                        <i class="bi bi-plus-lg me-2"></i> Tạo Booking Mới
                    </a>
                </div>
            </div>

            <div class="card card-custom mb-4">
                <div class="card-body py-3">
                    <form action="" method="GET">
                        <input type="hidden" name="act" value="dattourlist">

                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <div class="position-relative search-box">
                                    <i class="bi bi-search"></i>
                                    <input type="text" name="keyword" class="form-control" 
                                           placeholder="Tìm theo tên khách, mã tour, SĐT, CCCD..."
                                           value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <select name="status" class="form-select border-light bg-light text-muted">
                                    <option value="">-- Tất cả trạng thái --</option>
                                    
                                    <option value="chờ xác nhận" <?= (isset($_GET['status']) && $_GET['status'] == 'chờ xác nhận') ? 'selected' : '' ?>>
                                        Chờ xác nhận
                                    </option>
                                    
                                    <option value="đã xác nhận" <?= (isset($_GET['status']) && $_GET['status'] == 'đã xác nhận') ? 'selected' : '' ?>>
                                        Đã xác nhận 
                                    </option>
                                </select>
                            </div>
                            
                            <div class="col-md-2 text-end">
                                <button type="submit" class="btn btn-light w-100 border fw-medium text-secondary">
                                    <i class="bi bi-funnel"></i> Lọc
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-custom">
                <div class="card-body p-0">
                    <?php if (!empty($data)): ?>
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Mã Đơn</th>
                                        <th>Thông tin Tour</th>
                                        <th>Khách Hàng</th>
                                        <th>Khởi Hành</th>
                                        <th class="text-center">Số lượng</th>
                                        <th>Trạng Thái</th>
                                        <th class="text-end pe-4">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data as $row): ?>
                                        <?php
                                            // Xử lý dữ liệu hiển thị an toàn
                                            $id_don     = $row['dat_tour_id'] ?? 'N/A';
                                            $tour_id    = $row['tour_id'] ?? 'N/A';
                                            $ten_tour   = !empty($row['ten_tour']) ? htmlspecialchars($row['ten_tour']) : 'Chưa chọn Tour';
                                            
                                            $ten_khach  = htmlspecialchars($row['ten_khach_hang'] ?? 'Khách lẻ');
                                            $avatar_char = strtoupper(substr($ten_khach, 0, 1));
                                            $cccd       = htmlspecialchars($row['cccd'] ?? '');
                                            
                                            $ngay_kh = !empty($row['ngay_bat_dau']) 
                                                ? date('d/m/Y', strtotime($row['ngay_bat_dau'])) 
                                                : '<span class="text-muted small fst-italic">Chưa lịch</span>';
                                            
                                            // Xử lý Badge Trạng thái
                                            $trang_thai = strtolower($row['trang_thai_dat_tour'] ?? '');
                                            $badge_class = match ($trang_thai) {
                                                'chờ xác nhận', 'pending' => 'badge-soft-warning',
                                                'đã đặt cọc', 'confirmed', 'đã xác nhận' => 'badge-soft-info',
                                                'hoàn tất', 'completed', 'success', 'paid' => 'badge-soft-success',
                                                'hủy', 'cancelled', 'đã hủy' => 'badge-soft-danger',
                                                default => 'badge-soft-secondary',
                                            };
                                        ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-primary">#<?= $id_don ?></td>
                                            
                                            <td>
                                                <div class="fw-semibold text-dark text-wrap" style="max-width: 250px;"><?= $ten_tour ?></div>
                                                <small class="text-muted"><i class="bi bi-upc-scan"></i> ID Tour: <?= $tour_id ?></small>
                                            </td>
                                            
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-3 flex-shrink-0"><?= $avatar_char ?></div>
                                                    <div>
                                                        <div class="fw-medium text-dark"><?= $ten_khach ?></div>
                                                        <?php if($cccd): ?>
                                                            <div class="small text-muted" style="font-size: 0.8rem;">
                                                                <i class="bi bi-person-vcard"></i> <?= $cccd ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <div class="d-flex align-items-center text-secondary">
                                                    <i class="bi bi-calendar4-week me-2"></i> <?= $ngay_kh ?>
                                                </div>
                                            </td>
                                            
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border">
                                                    <i class="bi bi-people-fill me-1"></i> <?= $row['so_nguoi'] ?? 0 ?>
                                                </span>
                                            </td>
                                            
                                            <td>
                                                <span class="badge <?= $badge_class ?> badge-soft">
                                                    <?= ucfirst($row['trang_thai_dat_tour'] ?? 'N/A') ?>
                                                </span>
                                            </td>
                                            
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="<?= BASEURL ?>?act=dat_tour_detail&dat_tour_id=<?= $id_don ?>" 
                                                       class="btn-icon view" title="Xem chi tiết">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    
                                                    <a href="<?= BASEURL ?>?act=dat_tour_edit&dat_tour_id=<?= $id_don ?>" 
                                                       class="btn-icon edit" title="Chỉnh sửa">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    
                                                    <a href="<?= BASEURL ?>?act=dat_tour_delete&dat_tour_id=<?= $id_don ?>" 
                                                       onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')" 
                                                       class="btn-icon delete" title="Xóa">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-search display-1 text-muted opacity-25"></i>
                            <h5 class="text-muted mt-3">Không tìm thấy kết quả</h5>
                            <p class="text-muted mb-4">Thử thay đổi từ khóa hoặc bộ lọc trạng thái.</p>
                            
                            <a href="<?= BASEURL ?>?act=dattourlist" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-arrow-counterclockwise"></i> Xóa bộ lọc
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>
</html>