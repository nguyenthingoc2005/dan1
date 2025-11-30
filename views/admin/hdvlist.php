
<?php
if(session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Hướng Dẫn Viên</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        body { background-color: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .main-content { padding: 30px; margin-top: 70px; margin-left: 0; min-height: 100vh; }
        
        /* SEARCH BOX */
        .search-box .form-control { border-radius: 20px; padding-left: 40px; border: 1px solid #e9ecef; background-color: #f8f9fa; }
        .search-box .bi-search { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #adb5bd; }

        .card-custom { border: none; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); background: #fff; overflow: hidden; }
        .table-custom thead th { background-color: #f8f9fa; color: #6c757d; font-weight: 600; font-size: 0.75rem; border-bottom: 2px solid #e9ecef; padding: 15px; text-transform: uppercase; white-space: nowrap; }
        .table-custom tbody td { vertical-align: middle; padding: 15px; border-bottom: 1px solid #f1f1f1; font-size: 0.9rem; }
        
        /* Avatar Styles */
        .avatar-wrapper { width: 45px; height: 45px; margin-right: 15px; flex-shrink: 0; }
        .avatar-img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .avatar-circle { width: 100%; height: 100%; background-color: #e7f1ff; color: #0d6efd; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem; }
        
        /* Badges */
        .badge-soft { padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
        .badge-soft-primary { background-color: #cff4fc; color: #055160; }
        .badge-soft-info { background-color: #e0cffc; color: #4a0560; }
        .badge-soft-success { background-color: #d1e7dd; color: #0f5132; }
        .badge-soft-warning { background-color: #fff3cd; color: #664d03; }
        
        .btn-icon { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #6c757d; background: #f8f9fa; transition: all 0.2s; }
        .btn-icon:hover { transform: translateY(-2px); }
        .btn-icon.edit:hover { color: #ffc107; background: #fff3cd; }
        .btn-icon.delete:hover { color: #dc3545; background: #f8d7da; }
        
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            max-width: 250px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Danh Sách Hướng Dẫn Viên</h3>
                    <p class="text-muted mb-0">Quản lý hồ sơ, năng lực và trạng thái HDV.</p>
                </div>
                <a href="index.php?act=user_create" class="btn btn-primary shadow-sm px-4">
                    <i class="bi bi-person-plus-fill me-2"></i> Thêm HDV Mới
                </a>
            </div>

            <div class="card card-custom mb-4">
                <div class="card-body py-3">
                    <form action="" method="GET" class="row g-3 align-items-center" onsubmit="return validateSearch()">
                        <input type="hidden" name="act" value="hdv">
                        <div class="col-md-6">
                            <div class="position-relative search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" id="keyword" name="keyword" class="form-control" 
                                       placeholder="Tìm theo tên, email, kỹ năng..."
                                       value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-light w-100 border text-secondary fw-medium">
                                <i class="bi bi-funnel"></i> Lọc
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-custom">
                <div class="card-body p-0">
                    <?php if (!empty($hdvList)): ?>
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Thông tin HDV</th>
                                        <th>Liên hệ</th>
                                        <th>Năng lực & Chuyên môn</th>
                                        <th>Trạng thái</th>
                                        <th class="text-end pe-4">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($hdvList as $hdv): ?>
                                        <?php 
                                            $char = strtoupper(substr($hdv['ho_ten'] ?? 'U', 0, 1)); 
                                            $avatarPath = './assets/uploads/hdv/' . ($hdv['anh_dai_dien'] ?? '');
                                            $hasAvatar = !empty($hdv['anh_dai_dien']) && file_exists($avatarPath);
                                        ?>
                                        
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-wrapper">
                                                        <?php if($hasAvatar): ?>
                                                            <img src="<?= $avatarPath ?>" alt="AVT" class="avatar-img">
                                                        <?php else: ?>
                                                            <div class="avatar-circle"><?= $char ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= htmlspecialchars($hdv['ho_ten'] ?? 'Chưa cập nhật') ?></div>
                                                        <div class="small text-muted">
                                                            <?php if(isset($hdv['gioi_tinh'])): ?>
                                                                <span class="me-2"><i class="bi bi-gender-ambiguous"></i> <?= $hdv['gioi_tinh'] ?></span>
                                                            <?php endif; ?>
                                                            <?php if(isset($hdv['ngay_sinh'])): ?>
                                                                <span><i class="bi bi-cake2"></i> <?= date('d/m/Y', strtotime($hdv['ngay_sinh'])) ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="text-dark small fw-medium"><i class="bi bi-telephone me-2 text-primary"></i> <?= htmlspecialchars($hdv['so_dien_thoai'] ?? '---') ?></span>
                                                    <span class="text-muted small"><i class="bi bi-envelope me-2"></i> <?= htmlspecialchars($hdv['email'] ?? '---') ?></span>
                                                    <span class="text-muted small text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($hdv['dia_chi_lien_he'] ?? '') ?>">
                                                        <i class="bi bi-geo-alt me-2"></i> <?= htmlspecialchars($hdv['dia_chi_lien_he'] ?? 'Chưa có địa chỉ') ?>
                                                    </span>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="mb-1">
                                                    <span class="badge badge-soft badge-soft-primary"><i class="bi bi-translate me-1"></i> <?= htmlspecialchars($hdv['ngon_ngu_su_dung'] ?? 'Chưa rõ') ?></span>
                                                </div>
                                                <div class="text-muted text-truncate-2 small" title="<?= htmlspecialchars($hdv['kinh_nghiem_lam_viec'] ?? '') ?>">
                                                    <?= htmlspecialchars($hdv['kinh_nghiem_lam_viec'] ?? 'Chưa cập nhật kinh nghiệm') ?>
                                                </div>
                                            </td>

                                            <td>
                                                <?php 
                                                    $status = $hdv['tinh_trang_hoat_dong'] ?? 'Sẵn sàng';
                                                    $statusClass = 'badge-soft-success'; // Mặc định xanh
                                                    if ($status == 'Bận' || $status == 'Đang dẫn tour') $statusClass = 'badge-soft-warning';
                                                    if ($status == 'Tạm ngưng') $statusClass = 'badge-soft-danger'; // Đỏ (nếu bạn định nghĩa css này)
                                                ?>
                                                <span class="badge badge-soft <?= $statusClass ?>">
                                                    <?= htmlspecialchars($status) ?>
                                                </span>
                                            </td>

                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="index.php?act=edithdv&id=<?= $hdv['hdv_id'] ?>" class="btn-icon edit" title="Sửa thông tin">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    
                                                    <a href="index.php?act=deletehdv&id=<?= $hdv['hdv_id'] ?>" class="btn-icon delete" 
                                                       onclick="return confirm('Bạn có chắc chắn muốn xóa HDV: <?= htmlspecialchars($hdv['ho_ten'] ?? '') ?>?')" 
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
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-1 opacity-25"></i>
                            <p class="mt-3">Chưa có dữ liệu hướng dẫn viên nào.</p>
                            <a href="index.php?act=user_create" class="btn btn-sm btn-outline-primary mt-2">Thêm ngay</a>
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
                // Nếu rỗng thì load lại trang danh sách gốc (bỏ query search)
                window.location.href = "index.php?act=hdv";
                return false;
            }
            return true;
        }
    </script>

</body>
</html>