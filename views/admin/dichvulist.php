<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách Dịch Vụ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        /* MAIN LAYOUT */
        body { background-color: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .main-content { padding: 30px; margin-top: 70px; margin-left: 0; min-height: 100vh; }
        
        /* CARD & TABLE STYLE */
        .card-custom {
            border: none; border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            background: #fff; overflow: hidden;
        }
        
        .table-custom thead th {
            background-color: #f8f9fa; color: #6c757d; font-weight: 600;
            text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px;
            border-bottom: 2px solid #e9ecef; padding: 15px; white-space: nowrap;
        }
        
        .table-custom tbody td { vertical-align: middle; padding: 15px; border-bottom: 1px solid #f1f1f1; font-size: 0.9rem; }
        .table-custom tbody tr:hover { background-color: #fcfcfc; }

        /* SERVICE ICONS & BADGES */
        .service-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; margin-right: 15px;
        }
        
        /* Soft Badges */
        .badge-soft { padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 0.8rem; }
        .badge-soft-primary { background-color: #cff4fc; color: #055160; } /* Hotel */
        .badge-soft-success { background-color: #d1e7dd; color: #0f5132; } /* Food */
        .badge-soft-warning { background-color: #fff3cd; color: #664d03; } /* Transport */
        .badge-soft-secondary { background-color: #e2e3e5; color: #41464b; } /* Other */

        /* PRICE TAG */
        .price-tag { font-weight: 700; color: #dc3545; font-size: 0.95rem; }
        
        /* ACTION BUTTONS */
        .btn-icon {
            width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; transition: all 0.2s; color: #6c757d; background: #f8f9fa; border: 1px solid transparent;
        }
        .btn-icon:hover { transform: translateY(-2px); }
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
                    <h3 class="fw-bold text-dark mb-1">Quản Lý Dịch Vụ</h3>
                    <p class="text-muted mb-0">Danh sách các dịch vụ bổ sung (Khách sạn, Xe, Ăn uống...).</p>
                </div>
                <a href="<?= BASEURL ?>?act=them_dich_vu" class="btn btn-success d-flex align-items-center px-4 shadow-sm">
                    <i class="bi bi-plus-lg me-2"></i> Thêm Dịch Vụ
                </a>
            </div>

            <div class="card card-custom">
                <div class="card-body p-0">
                    <?php if (!empty($dichVuList)): ?>
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Mã & Tên Dịch Vụ</th>
                                        <th>Loại Dịch Vụ</th>
                                        <th>Giá Mặc Định</th>
                                        <th>Nhà Cung Cấp</th>
                                        <th>Mô Tả</th>
                                        <th class="text-end pe-4">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dichVuList as $dv): ?>
                                        <?php 
                                            // Xử lý Icon & Badge theo loại dịch vụ
                                            $loai = strtolower($dv['loai_dich_vu']);
                                            $icon = 'bi-box-seam';
                                            $badge_class = 'badge-soft-secondary';
                                            $bg_icon = '#e9ecef'; $color_icon = '#495057';

                                            if (strpos($loai, 'hotel') !== false || strpos($loai, 'khách sạn') !== false) {
                                                $icon = 'bi-building'; $badge_class = 'badge-soft-primary';
                                                $bg_icon = '#e7f1ff'; $color_icon = '#0d6efd';
                                            } elseif (strpos($loai, 'transport') !== false || strpos($loai, 'xe') !== false) {
                                                $icon = 'bi-car-front'; $badge_class = 'badge-soft-warning';
                                                $bg_icon = '#fff3cd'; $color_icon = '#ffc107';
                                            } elseif (strpos($loai, 'food') !== false || strpos($loai, 'ăn') !== false) {
                                                $icon = 'bi-cup-hot'; $badge_class = 'badge-soft-success';
                                                $bg_icon = '#d1e7dd'; $color_icon = '#198754';
                                            }
                                        ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="service-icon flex-shrink-0" style="background-color: <?= $bg_icon ?>; color: <?= $color_icon ?>;">
                                                        <i class="bi <?= $icon ?>"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= htmlspecialchars($dv['ten_dich_vu']) ?></div>
                                                        <div class="small text-muted">Mã: <?= htmlspecialchars($dv['ma']) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <span class="badge badge-soft <?= $badge_class ?>">
                                                    <?= htmlspecialchars($dv['loai_dich_vu']) ?>
                                                </span>
                                            </td>
                                            
                                            <td>
                                                <div class="price-tag">
                                                    <?= number_format($dv['gia_mac_dinh']) ?> <small>đ</small>
                                                    <span class="text-muted fw-normal small">/ <?= htmlspecialchars($dv['don_vi']) ?></span>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <div class="text-dark fw-medium">
                                                    <i class="bi bi-shop me-1 text-secondary"></i> <?= htmlspecialchars($dv['ten_ncc']) ?>
                                                </div>
                                            </td>
                                            
                                            <td class="text-muted small text-truncate" style="max-width: 200px;">
                                                <?= htmlspecialchars($dv['mo_ta']) ?>
                                            </td>
                                            
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="<?= BASEURL ?>?act=capnhat_dich_vu&id=<?= $dv['dich_vu_id'] ?>" 
                                                       class="btn-icon edit" title="Sửa">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    
                                                    <a href="<?= BASEURL ?>?act=xoa_dich_vu&id=<?= $dv['dich_vu_id'] ?>" 
                                                       class="btn-icon delete" 
                                                       onclick="return confirm('Bạn có chắc chắn muốn xóa dịch vụ này không?')" 
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
                            <i class="bi bi-inbox display-1 text-muted opacity-25"></i>
                            <h5 class="text-muted mt-3">Chưa có dịch vụ nào</h5>
                            <p class="text-muted mb-4">Hãy thêm dịch vụ đầu tiên để bắt đầu sử dụng.</p>
                            <a href="<?= BASEURL ?>?act=them_dich_vu" class="btn btn-success px-4">Thêm Dịch Vụ</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <script src="./assets/js/sidebar.js"></script>
</body>
</html>