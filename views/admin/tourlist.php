<?php
// =================================================================================
// PHẦN 1: HÀM LỌC DỮ LIỆU TOUR (SEARCH + FILTER)
// =================================================================================

function filterTourData($data) {
    // Lấy từ khóa và trạng thái từ URL (GET)
    $keyword = isset($_GET['keyword']) ? mb_strtolower(trim($_GET['keyword']), 'UTF-8') : '';
    $status  = isset($_GET['status']) ? $_GET['status'] : '';

    // Nếu không nhập gì -> Trả về data gốc
    if (empty($keyword) && $status === '') {
        return $data;
    }

    $filtered_list = [];

    foreach ($data as $row) {
        // Chuẩn hóa dữ liệu dòng hiện tại về chữ thường
        $id             = mb_strtolower((string)($row['tour_id'] ?? ''), 'UTF-8');
        $ten_tour       = mb_strtolower((string)($row['ten'] ?? ''), 'UTF-8');
        $danh_muc       = mb_strtolower((string)($row['ten_danh_muc'] ?? ''), 'UTF-8');
        $khoi_hanh      = mb_strtolower((string)($row['diem_khoi_hanh'] ?? ''), 'UTF-8');
        $hoat_dong      = (int)($row['hoat_dong'] ?? 0); // 1: Active, 0: Inactive

        // 1. Kiểm tra Từ khóa (Nếu có nhập)
        $match_keyword = true; 
        if (!empty($keyword)) {
            $match_keyword = (
                strpos($id, $keyword) !== false || 
                strpos($ten_tour, $keyword) !== false || 
                strpos($danh_muc, $keyword) !== false || 
                strpos($khoi_hanh, $keyword) !== false
            );
        }

        // 2. Kiểm tra Trạng thái (Nếu có chọn)
        $match_status = true; 
        if ($status !== '') {
            if ($status == 'active') {
                $match_status = ($hoat_dong === 1);
            } 
            elseif ($status == 'inactive') {
                $match_status = ($hoat_dong === 0);
            }
        }

        // Thỏa mãn cả 2 điều kiện
        if ($match_keyword && $match_status) {
            $filtered_list[] = $row;
        }
    }
    
    return $filtered_list;
}

// Gọi hàm lọc nếu có dữ liệu
if (!empty($data1)) {
    $data1 = filterTourData($data1);
}
?>

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
        /* MAIN LAYOUT */
        body { background-color: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        
        .main-content {
            padding: 30px;
            margin-top: 70px;
            margin-left: 0;   
            transition: margin-left .32s ease;
            min-height: 100vh;
        }
        
        /* SEARCH BOX STYLE */
        .search-box .form-control { border-radius: 20px; padding-left: 40px; border: 1px solid #e9ecef; background-color: #f8f9fa; }
        .search-box .bi-search { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #adb5bd; }

        /* CARD & TABLE STYLE (Modern) */
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            background: #fff;
            overflow: hidden;
        }
        
        .table-custom thead th {
            background-color: #f8f9fa;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e9ecef;
            padding: 15px;
            white-space: nowrap;
        }
        
        .table-custom tbody td { vertical-align: middle; padding: 15px; border-bottom: 1px solid #f1f1f1; font-size: 0.9rem; }
        .table-custom tbody tr:hover { background-color: #fcfcfc; }

        /* TOUR ICON AVATAR */
        .tour-icon {
            width: 45px; height: 45px;
            background-color: #e7f1ff; color: #0d6efd;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; margin-right: 15px;
        }

        /* SOFT BADGES */
        .badge-soft { padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 0.8rem; }
        .badge-soft-success { background-color: #d1e7dd; color: #0f5132; }
        .badge-soft-secondary { background-color: #e2e3e5; color: #41464b; }
        .badge-soft-info { background-color: #cff4fc; color: #055160; }

        /* ACTION BUTTONS */
        .btn-icon {
            width: 34px; height: 34px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; transition: all 0.2s;
            color: #6c757d; background: #f8f9fa; border: 1px solid transparent;
        }
        .btn-icon:hover { transform: translateY(-2px); }
        .btn-icon.view:hover { color: #0dcaf0; background: #cff4fc; }
        .btn-icon.edit:hover { color: #ffc107; background: #fff3cd; }
        .btn-icon.delete:hover { color: #dc3545; background: #f8d7da; }
        
        .price-tag { font-weight: 700; color: #dc3545; font-size: 0.95rem; }
    </style>
</head>
<body>

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Quản Lý Tour Du Lịch</h3>
                    <p class="text-muted mb-0">Danh sách tất cả các tour hiện có trong hệ thống.</p>
                </div>
                <a href="<?= BASEURL ?>?act=addtour" class="btn btn-primary d-flex align-items-center px-4 shadow-sm">
                    <i class="bi bi-plus-lg me-2"></i> Thêm Tour Mới
                </a>
            </div>

            <div class="card card-custom mb-4">
                <div class="card-body py-3">
                    <form action="" method="GET">
                        <input type="hidden" name="act" value="tour_list">

                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <div class="position-relative search-box">
                                    <i class="bi bi-search"></i>
                                    <input type="text" name="keyword" class="form-control" 
                                           placeholder="Tìm theo tên tour, mã ID, điểm đến..."
                                           value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <select name="status" class="form-select border-light bg-light text-muted">
                                    <option value="">-- Tất cả trạng thái --</option>
                                    <option value="active" <?= (isset($_GET['status']) && $_GET['status'] == 'active') ? 'selected' : '' ?>>Đang hoạt động</option>
                                    <option value="inactive" <?= (isset($_GET['status']) && $_GET['status'] == 'inactive') ? 'selected' : '' ?>>Tạm dừng</option>
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
                    <?php if (!empty($data1)): ?>
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">ID</th>
                                        <th>Thông tin Tour</th>
                                        <th>Danh mục</th>
                                        <th>Giá cơ bản</th>
                                        <th class="text-center">Thời lượng</th>
                                        <th>Khởi hành</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-end pe-4">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data1 as $tour): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-secondary">#<?= $tour['tour_id'] ?></td>
                                            
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="tour-icon flex-shrink-0">
                                                        <i class="bi bi-map"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark text-wrap" style="max-width: 280px;">
                                                            <?= htmlspecialchars($tour['ten']) ?>
                                                        </div>
                                                        <div class="small text-muted mt-1">
                                                            <i class="bi bi-calendar3"></i> Tạo: <?= date('d/m/Y', strtotime($tour['ngay_tao'])) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <span class="badge badge-soft-info">
                                                    <?= htmlspecialchars($tour['ten_danh_muc'] ?? 'Chưa phân loại') ?>
                                                </span>
                                            </td>
                                            
                                            <td>
                                                <div class="price-tag">
                                                    <?= number_format($tour['gia_co_ban']) ?> <small>đ</small>
                                                </div>
                                            </td>
                                            
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border">
                                                    <?= htmlspecialchars($tour['thoi_luong_mac_dinh']) ?> ngày
                                                </span>
                                            </td>
                                            
                                            <td>
                                                <div class="d-flex align-items-center text-secondary">
                                                    <i class="bi bi-geo-alt me-2"></i>
                                                    <?= htmlspecialchars($tour['diem_khoi_hanh']) ?>
                                                </div>
                                            </td>
                                            
                                            <td class="text-center">
                                                <?php if($tour['hoat_dong']): ?>
                                                    <span class="badge badge-soft badge-soft-success">Đang hoạt động</span>
                                                <?php else: ?>
                                                    <span class="badge badge-soft badge-soft-secondary">Tạm dừng</span>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="<?= BASEURL ?>?act=chitiettour&tour_id=<?= $tour['tour_id'] ?>" 
                                                       class="btn-icon view" title="Xem chi tiết">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    
                                                    <a href="<?= BASEURL ?>?act=uppdatetour&tour_id=<?= $tour['tour_id'] ?>" 
                                                       class="btn-icon edit" title="Chỉnh sửa">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    
                                                    <a href="<?= BASEURL ?>?act=deletetour&tour_id=<?= $tour['tour_id'] ?>" 
                                                       class="btn-icon delete" 
                                                       onclick="return confirm('Bạn có chắc chắn muốn xóa tour này không?')" 
                                                       title="Xóa">
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
                            <p class="text-muted mb-4">Thử thay đổi từ khóa tìm kiếm hoặc thêm tour mới.</p>
                            
                            <a href="<?= BASEURL ?>?act=tour_list" class="btn btn-outline-secondary px-4 me-2">
                                Xóa bộ lọc
                            </a>
                            <a href="<?= BASEURL ?>?act=addtour" class="btn btn-success px-4">
                                <i class="bi bi-plus-lg me-2"></i> Thêm Tour
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