<?php
// =================================================================================
// PHẦN 1: HÀM LỌC DỮ LIỆU (SEARCH)
// =================================================================================

function filterNCCData($data) {
    // Lấy từ khóa từ URL
    $keyword = isset($_GET['keyword']) ? mb_strtolower(trim($_GET['keyword']), 'UTF-8') : '';

    // Nếu không nhập gì -> Trả về data gốc
    if (empty($keyword)) {
        return $data;
    }

    $filtered_list = [];

    foreach ($data as $row) {
        // Chuẩn hóa dữ liệu về chữ thường
        $id         = mb_strtolower((string)($row['ncc_id'] ?? ''), 'UTF-8');
        $ten        = mb_strtolower((string)($row['ten'] ?? ''), 'UTF-8');
        $lien_he    = mb_strtolower((string)($row['lien_he'] ?? ''), 'UTF-8');
        $dia_chi    = mb_strtolower((string)($row['dia_chi'] ?? ''), 'UTF-8');
        $mst        = mb_strtolower((string)($row['ma_so_thue'] ?? ''), 'UTF-8');

        // Kiểm tra từ khóa xuất hiện
        if (strpos($id, $keyword) !== false || 
            strpos($ten, $keyword) !== false || 
            strpos($lien_he, $keyword) !== false || 
            strpos($dia_chi, $keyword) !== false || 
            strpos($mst, $keyword) !== false) {
            
            $filtered_list[] = $row;
        }
    }
    
    return $filtered_list;
}

// Gọi hàm lọc nếu có dữ liệu
if (!empty($data)) {
    $data = filterNCCData($data);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách Nhà Cung Cấp</title>
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
        
        /* CARD & SEARCH STYLE */
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            background: #fff;
            overflow: hidden;
        }

        .search-box .form-control { 
            border-radius: 20px; 
            padding-left: 40px; 
            border: 1px solid #e9ecef; 
            background-color: #f8f9fa; 
        }
        .search-box .bi-search { 
            position: absolute; 
            left: 15px; top: 50%; 
            transform: translateY(-50%); 
            color: #adb5bd; 
        }

        /* TABLE STYLE */
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
        
        .table-custom tbody td { 
            vertical-align: middle; 
            padding: 15px; 
            border-bottom: 1px solid #f1f1f1; 
            font-size: 0.9rem; 
        }
        .table-custom tbody tr:hover { background-color: #fcfcfc; }

        /* AVATAR ICON */
        .ncc-icon {
            width: 40px; height: 40px;
            background-color: #fff3cd; color: #ffc107;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; margin-right: 15px;
        }

        /* ACTION BUTTONS */
        .btn-icon {
            width: 32px; height: 32px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; transition: all 0.2s;
            color: #6c757d; background: #f8f9fa; border: 1px solid transparent;
        }
        .btn-icon:hover { transform: translateY(-2px); }
        .btn-icon.service:hover { color: #0dcaf0; background: #cff4fc; } /* Màu nút Dịch vụ */
        .btn-icon.edit:hover { color: #ffc107; background: #fff3cd; }
        .btn-icon.delete:hover { color: #dc3545; background: #f8d7da; }
    </style>
</head>
<body>

    <?php include_once './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Quản Lý Nhà Cung Cấp</h3>
                    <p class="text-muted mb-0">Danh sách đối tác cung cấp dịch vụ (Khách sạn, Xe, Nhà hàng...).</p>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="<?= BASEURL ?>?act=lay_dich_vu" class="btn btn-outline-primary d-flex align-items-center px-3 shadow-sm">
                        <i class="bi bi-list-ul me-2"></i> DS Dịch Vụ
                    </a>
                    
                    <a href="<?= BASEURL ?>?act=ncc_add" class="btn btn-success d-flex align-items-center px-3 shadow-sm">
                        <i class="bi bi-plus-lg me-2"></i> Thêm NCC Mới
                    </a>
                </div>
            </div>

            <div class="card card-custom mb-4">
                <div class="card-body py-3">
                    <form action="" method="GET">
                        <input type="hidden" name="act" value="ncc_list">

                        <div class="row g-3 align-items-center">
                            <div class="col-md-10">
                                <div class="position-relative search-box">
                                    <i class="bi bi-search"></i>
                                    <input type="text" name="keyword" class="form-control" 
                                           placeholder="Tìm kiếm theo tên công ty, số điện thoại, mã số thuế..."
                                           value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-2 text-end">
                                <button type="submit" class="btn btn-primary w-100 fw-medium">
                                    <i class="bi bi-funnel"></i> Tìm kiếm
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
                                        <th class="ps-4">Thông tin NCC</th>
                                        <th>Liên hệ</th>
                                        <th>Địa chỉ</th>
                                        <th>Ngày tạo</th>
                                        <th class="text-end pe-4">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data as $row): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="ncc-icon flex-shrink-0">
                                                        <i class="bi bi-building"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['ten']) ?></div>
                                                        <div class="small text-muted mt-1">
                                                            <span class="badge bg-light text-secondary border">MST: <?= htmlspecialchars($row['ma_so_thue']) ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-medium">
                                                        <i class="bi bi-telephone me-1 text-secondary"></i> <?= htmlspecialchars($row['lien_he']) ?>
                                                    </span>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <div class="text-muted small text-wrap" style="max-width: 250px;">
                                                    <i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($row['dia_chi']) ?>
                                                </div>
                                            </td>
                                            
                                            <td class="text-secondary small">
                                                <?= date('d/m/Y', strtotime($row['ngay_tao'])) ?>
                                            </td>
                                            
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="<?= BASEURL ?>?act=lay_dich_vu_ncc&ncc_id=<?= $row['ncc_id'] ?>" 
                                                       class="btn-icon service" title="Quản lý dịch vụ của NCC này">
                                                        <i class="bi bi-box-seam"></i>
                                                    </a>

                                                    <a href="<?= BASEURL ?>?act=ncc_update&id=<?= $row['ncc_id'] ?>" 
                                                       class="btn-icon edit" title="Chỉnh sửa thông tin">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    
                                                    <a href="<?= BASEURL ?>?act=ncc_delete&id=<?= $row['ncc_id'] ?>" 
                                                       class="btn-icon delete" 
                                                       onclick="return confirm('Bạn có chắc chắn muốn xóa nhà cung cấp này? Các dịch vụ liên quan có thể bị ảnh hưởng.')" 
                                                       title="Xóa nhà cung cấp">
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
                            <i class="bi bi-building-slash display-1 text-muted opacity-25"></i>
                            <h5 class="text-muted mt-3">Không tìm thấy kết quả</h5>
                            <p class="text-muted mb-4">Thử thay đổi từ khóa tìm kiếm hoặc thêm nhà cung cấp mới.</p>
                            
                            <?php if(isset($_GET['keyword'])): ?>
                                <a href="<?= BASEURL ?>?act=ncc_list" class="btn btn-outline-secondary px-4">
                                    Xóa bộ lọc
                                </a>
                            <?php else: ?>
                                <a href="<?= BASEURL ?>?act=ncc_add" class="btn btn-success px-4">
                                    <i class="bi bi-plus-lg me-2"></i> Thêm NCC Mới
                                </a>
                            <?php endif; ?>
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