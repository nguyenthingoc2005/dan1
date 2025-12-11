<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Địa Điểm</title>
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

        /* PLACE BADGES */
        .badge-soft { padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 0.8rem; }
        .badge-soft-info { background-color: #cfe2ff; color: #084298; } /* Di tích/Lịch sử */
        .badge-soft-danger { background-color: #f8d7da; color: #842029; } /* Thiên nhiên/Cảnh quan */
        .badge-soft-primary { background-color: #e6f7ff; color: #007bff; } /* Văn hóa/Giải trí */

        /* ACTION BUTTONS */
        .btn-icon {
            width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; transition: all 0.2s; color: #6c757d; background: #f8f9fa; border: 1px solid transparent;
        }
        .btn-icon:hover { transform: translateY(-2px); }
        .btn-icon.edit:hover { color: #0d6efd; background: #e7f1ff; } /* Blue for edit */
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
                    <h3 class="fw-bold text-dark mb-1">Quản Lý Địa Điểm</h3>
                    <p class="text-muted mb-0">Danh sách các địa điểm sẽ được sử dụng trong các Tour.</p>
                </div>
                <a href="<?= BASEURL ?>?act=them_dia_diem" class="btn btn-primary d-flex align-items-center px-4 shadow-sm">
                    <i class="bi bi-geo-alt me-2"></i> Thêm Địa Điểm
                </a>
            </div>

            <div class="card card-custom">
                <div class="card-body p-0">
                    <?php 
                        // Giả lập dữ liệu Địa Điểm (thực tế sẽ được lấy từ Model)
                        $diaDiemList = [
                            [
                                'dia_diem_id' => 101, 'ten_dia_diem' => 'Vịnh Hạ Long', 'ma' => 'VHL01', 
                                'loai_dia_diem' => 'Thiên nhiên/Cảnh quan', 'tinh_thanh' => 'Quảng Ninh',
                                'mo_ta_ngan' => 'Di sản thiên nhiên thế giới, nổi tiếng với núi đá vôi.'
                            ],
                            [
                                'dia_diem_id' => 102, 'ten_dia_diem' => 'Khu Phố Cổ', 'ma' => 'KPC02', 
                                'loai_dia_diem' => 'Di tích/Lịch sử', 'tinh_thanh' => 'Hà Nội',
                                'mo_ta_ngan' => 'Khu vực lịch sử với kiến trúc truyền thống.'
                            ],
                            [
                                'dia_diem_id' => 103, 'ten_dia_diem' => 'Công Viên Văn Hóa', 'ma' => 'CPVH3', 
                                'loai_dia_diem' => 'Văn hóa/Giải trí', 'tinh_thanh' => 'TP.HCM',
                                'mo_ta_ngan' => 'Nơi tổ chức các sự kiện văn hóa và giải trí.'
                            ],
                        ];
                    ?>
                    
                    <?php if (!empty($diaDiemList)): ?>
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Mã & Tên Địa Điểm</th>
                                        <th>Loại Hình</th>
                                        <th>Tỉnh/Thành</th>
                                        <th>Mô Tả Ngắn</th>
                                        <th class="text-end pe-4">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($diaDiemList as $dd): ?>
                                        <?php 
                                            // Xử lý Badge theo loại địa điểm
                                            $loai = strtolower($dd['loai_dia_diem']);
                                            $badge_class = 'badge-soft-primary'; // Default
                                            
                                            if (strpos($loai, 'lịch sử') !== false || strpos($loai, 'di tích') !== false) {
                                                $badge_class = 'badge-soft-info';
                                            } elseif (strpos($loai, 'thiên nhiên') !== false || strpos($loai, 'cảnh quan') !== false) {
                                                $badge_class = 'badge-soft-danger';
                                            }
                                        ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-3">
                                                        <i class="bi bi-map text-primary fs-5"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= htmlspecialchars($dd['ten_dia_diem']) ?></div>
                                                        <div class="small text-muted">Mã: <?= htmlspecialchars($dd['ma']) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <span class="badge badge-soft <?= $badge_class ?>">
                                                    <?= htmlspecialchars($dd['loai_dia_diem']) ?>
                                                </span>
                                            </td>
                                            
                                            <td>
                                                <div class="text-dark fw-medium">
                                                    <i class="bi bi-pin-map me-1 text-secondary"></i> <?= htmlspecialchars($dd['tinh_thanh']) ?>
                                                </div>
                                            </td>
                                            
                                            <td class="text-muted small text-truncate" style="max-width: 300px;">
                                                <?= htmlspecialchars($dd['mo_ta_ngan']) ?>
                                            </td>
                                            
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="<?= BASEURL ?>?act=dich_vu_dia_diem&id=<?= $dd['dia_diem_id'] ?>" 
                                                        class="btn-icon" title="Dịch vụ liên quan">
                                                        <i class="bi bi-box-seam"></i>
                                                    </a>
                                                    
                                                    <a href="<?= BASEURL ?>?act=capnhat_dia_diem&id=<?= $dd['dia_diem_id'] ?>" 
                                                        class="btn-icon edit" title="Sửa">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    
                                                    <a href="<?= BASEURL ?>?act=xoa_dia_diem&id=<?= $dd['dia_diem_id'] ?>" 
                                                        class="btn-icon delete" 
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa địa điểm này không?')" 
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
                            <i class="bi bi-map display-1 text-muted opacity-25"></i>
                            <h5 class="text-muted mt-3">Chưa có địa điểm nào</h5>
                            <p class="text-muted mb-4">Hãy thêm địa điểm đầu tiên để sử dụng trong các tour.</p>
                            <a href="<?= BASEURL ?>?act=them_dia_diem" class="btn btn-primary px-4">Thêm Địa Điểm</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <script src="./assets/js/sidebar.js"></script>
</body>
</html>