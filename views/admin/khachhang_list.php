<?php
if(session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Khách Hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    
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

        /* SEARCH BOX */
        .search-box .form-control { border-radius: 20px; padding-left: 40px; border: 1px solid #e9ecef; background-color: #f8f9fa; }
        .search-box .bi-search { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #adb5bd; }

        /* CARD & TABLE STYLE */
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); background: #fff; overflow: hidden; }
        .table-custom thead th { background-color: #f8f9fa; color: #6c757d; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; border-bottom: 2px solid #e9ecef; padding: 15px; white-space: nowrap; }
        .table-custom tbody td { vertical-align: middle; padding: 15px; border-bottom: 1px solid #f1f1f1; font-size: 0.9rem; }
        .table-custom tbody tr:hover { background-color: #fcfcfc; }

        /* AVATAR & BADGE */
        /* Màu xanh lá đặc trưng cho Khách hàng để phân biệt với HDV (Xanh dương/Vàng) */
        .avatar-circle { width: 40px; height: 40px; background-color: #d1e7dd; color: #0f5132; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; margin-right: 15px; text-transform: uppercase; }
        
        .badge-soft { padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 0.8rem; }
        .badge-soft-success { background-color: #d1e7dd; color: #0f5132; }
        .badge-soft-danger { background-color: #f8d7da; color: #842029; }
        .badge-soft-info { background-color: #cff4fc; color: #055160; }

        /* ACTION BUTTONS */
        .btn-icon { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s; color: #6c757d; background: #f8f9fa; border: 1px solid transparent; }
        .btn-icon:hover { transform: translateY(-2px); }
        .btn-icon.edit:hover { color: #ffc107; background: #fff3cd; }
        .btn-icon.delete:hover { color: #dc3545; background: #f8d7da; }
    </style>
</head>
<body>

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Danh Sách Khách Hàng</h3>
                    <p class="text-muted mb-0">Quản lý thông tin và lịch sử khách hàng.</p>
                </div>
                <a href="<?= BASEURL ?>?act=user_create" class="btn btn-success d-flex align-items-center px-4 shadow-sm">
                    <i class="bi bi-person-plus-fill me-2"></i> Thêm Khách Mới
                </a>
            </div>

            <div class="card card-custom mb-4">
                <div class="card-body py-3">
                    <form action="" method="GET" class="row g-3 align-items-center" onsubmit="return validateSearch()">
                        <input type="hidden" name="act" value="khach_hang_list">
                        <div class="col-md-6">
                            <div class="position-relative search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" id="keyword" name="keyword" class="form-control" 
                                       placeholder="Tìm theo tên, email, số điện thoại..."
                                       value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-light w-100 border fw-medium text-secondary">
                                <i class="bi bi-funnel"></i> Tìm kiếm
                            </button>
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
                                        <th class="ps-4">Thông tin Khách hàng</th>
                                        <th>Liên hệ</th>
                                        <th>Thông tin định danh</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th class="text-end pe-4">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data as $kh): ?>
                                        <?php 
                                            // Lấy chữ cái đầu làm Avatar
                                            $char = strtoupper(substr($kh['ho_ten'], 0, 1)); 
                                            $status = $kh['trang_thai_tai_khoan'];
                                        ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle flex-shrink-0"><?= $char ?></div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= htmlspecialchars($kh['ho_ten']) ?></div>
                                                        <div class="small text-muted">Mã KH: #<?= $kh['khach_hang_id'] ?></div>
                                                        <div class="small text-muted">User ID: #<?= $kh['nguoi_dung_id'] ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="mb-1"><i class="bi bi-envelope me-1 text-secondary"></i> <?= htmlspecialchars($kh['email']) ?></span>
                                                    <span class="text-muted small"><i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($kh['so_dien_thoai']) ?></span>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="mb-1 text-dark"><i class="bi bi-postcard me-1"></i> <?= htmlspecialchars($kh['cccd']) ?></span>
                                                    <span class="text-muted small text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($kh['dia_chi']) ?>">
                                                        <i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($kh['dia_chi']) ?>
                                                    </span>
                                                </div>
                                            </td>

                                            <td>
                                                <?php if($status == 'active'): ?>
                                                    <span class="badge badge-soft badge-soft-success">Hoạt động</span>
                                                <?php else: ?>
                                                    <span class="badge badge-soft badge-soft-danger">Đã khóa</span>
                                                <?php endif; ?>
                                            </td>

                                            <td><?= date('d/m/Y', strtotime($kh['ngay_tao_khach_hang'])) ?></td>
                                            
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="<?= BASEURL ?>?act=user_edit&id=<?= $kh['nguoi_dung_id'] ?>" 
                                                       class="btn-icon edit" title="Sửa thông tin">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    
                                                    <a href="<?= BASEURL ?>?act=user_delete&id=<?= $kh['nguoi_dung_id'] ?>" 
                                                       class="btn-icon delete" 
                                                       onclick="return confirm('Xóa tài khoản này sẽ xóa cả thông tin khách hàng. Bạn có chắc chắn?')" 
                                                       title="Xóa khách hàng">
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
                            <i class="bi bi-people display-1 text-muted opacity-25"></i>
                            <h5 class="text-muted mt-3">Chưa có dữ liệu</h5>
                            <p class="text-muted mb-4">Hiện tại chưa có khách hàng nào trong hệ thống.</p>
                            <?php if(isset($_GET['keyword']) && $_GET['keyword'] != ''): ?>
                                <a href="<?= BASEURL ?>?act=khach_hang_list" class="btn btn-outline-secondary">Xóa bộ lọc</a>
                            <?php else: ?>
                                <a href="<?= BASEURL ?>?act=user_create" class="btn btn-success px-4">Thêm mới ngay</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <script src="./assets/js/sidebar.js"></script>
    <script>
        function validateSearch() {
            var keyword = document.getElementById('keyword').value.trim();
            if (keyword === "") {
                window.location.href = "<?= BASEURL ?>?act=khach_hang_list";
                return false;
            }
            return true;
        }
    </script>
</body>
</html>