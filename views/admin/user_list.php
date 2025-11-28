<?php
if(session_status() === PHP_SESSION_NONE) session_start();

// HÀM LỌC DỮ LIỆU (Search + Filter)
function filterUserData($users) {
    // Lấy từ khóa và trạng thái từ URL
    $keyword = isset($_GET['keyword']) ? mb_strtolower(trim($_GET['keyword']), 'UTF-8') : '';
    $status  = isset($_GET['status']) ? $_GET['status'] : '';

    if (empty($keyword) && empty($status)) {
        return $users;
    }

    $filtered_list = [];
    foreach ($users as $user) {
        $id     = mb_strtolower((string)$user['nguoi_dung_id'], 'UTF-8');
        $name   = mb_strtolower((string)$user['ho_ten'], 'UTF-8');
        $email  = mb_strtolower((string)$user['email'], 'UTF-8');
        $phone  = mb_strtolower((string)$user['so_dien_thoai'], 'UTF-8');
        $role   = mb_strtolower((string)$user['ten_vai_tro'], 'UTF-8');
        $stt    = (string)$user['trang_thai']; // active / inactive

        // 1. Check Keyword
        $match_key = true;
        if (!empty($keyword)) {
            $match_key = (strpos($id, $keyword) !== false || 
                          strpos($name, $keyword) !== false || 
                          strpos($email, $keyword) !== false || 
                          strpos($phone, $keyword) !== false ||
                          strpos($role, $keyword) !== false);
        }

        // 2. Check Status
        $match_status = true;
        if (!empty($status)) {
            $match_status = ($stt === $status);
        }

        if ($match_key && $match_status) {
            $filtered_list[] = $user;
        }
    }
    return $filtered_list;
}

// Gọi hàm lọc
if (!empty($users)) {
    $users = filterUserData($users);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Người Dùng</title>
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

        /* CARD & TABLE */
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

        /* AVATAR CIRCLE */
        .avatar-circle {
            width: 40px; height: 40px;
            background-color: #e7f1ff; color: #0d6efd;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 1.2rem; margin-right: 15px;
            text-transform: uppercase;
        }

        /* SOFT BADGES */
        .badge-soft { padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 0.8rem; }
        .badge-soft-success { background-color: #d1e7dd; color: #0f5132; }
        .badge-soft-secondary { background-color: #e2e3e5; color: #41464b; }
        .badge-soft-primary { background-color: #cff4fc; color: #055160; }
        .badge-soft-warning { background-color: #fff3cd; color: #664d03; }

        /* ACTION BUTTONS */
        .btn-icon {
            width: 32px; height: 32px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; transition: all 0.2s;
            color: #6c757d; background: #f8f9fa; border: 1px solid transparent;
        }
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
                    <h3 class="fw-bold text-dark mb-1">Quản Lý Người Dùng</h3>
                    <p class="text-muted mb-0">Danh sách tài khoản quản trị viên, nhân viên và khách hàng.</p>
                </div>
                <a href="index.php?act=user_create" class="btn btn-primary d-flex align-items-center px-4 shadow-sm">
                    <i class="bi bi-person-plus-fill me-2"></i> Thêm Tài Khoản
                </a>
            </div>

            <div class="card card-custom mb-4">
                <div class="card-body py-3">
                    <form action="" method="GET">
                        <input type="hidden" name="act" value="user_list"> <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <div class="position-relative search-box">
                                    <i class="bi bi-search"></i>
                                    <input type="text" name="keyword" class="form-control" 
                                           placeholder="Tìm theo tên, email, số điện thoại..."
                                           value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select name="status" class="form-select border-light bg-light text-muted">
                                    <option value="">-- Tất cả trạng thái --</option>
                                    <option value="active" <?= (isset($_GET['status']) && $_GET['status'] == 'active') ? 'selected' : '' ?>>Hoạt động</option>
                                    <option value="inactive" <?= (isset($_GET['status']) && $_GET['status'] == 'inactive') ? 'selected' : '' ?>>Đã khóa</option>
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
                    <?php if (!empty($users)): ?>
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Thông tin Người dùng</th>
                                        <th>Liên hệ</th>
                                        <th>Vai trò</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-end pe-4">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <?php 
                                            // Lấy chữ cái đầu làm Avatar
                                            $char = strtoupper(substr($user['ho_ten'], 0, 1));
                                            
                                            // Badge Vai trò
                                            $role_class = 'badge-soft-secondary';
                                            if (stripos($user['ten_vai_tro'], 'admin') !== false) $role_class = 'badge-soft-primary';
                                            if (stripos($user['ten_vai_tro'], 'nhân viên') !== false) $role_class = 'badge-soft-warning';
                                        ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle flex-shrink-0">
                                                        <?= $char ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= htmlspecialchars($user['ho_ten']) ?></div>
                                                        <div class="small text-muted">ID: #<?= $user['nguoi_dung_id'] ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="mb-1"><i class="bi bi-envelope me-1 text-secondary"></i> <?= htmlspecialchars($user['email']) ?></span>
                                                    <span class="text-muted small"><i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($user['so_dien_thoai']) ?></span>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <span class="badge badge-soft <?= $role_class ?>">
                                                    <?= htmlspecialchars($user['ten_vai_tro']) ?>
                                                </span>
                                            </td>
                                            
                                            <td class="text-center">
                                                <?php if($user['trang_thai'] === 'active'): ?>
                                                    <span class="badge badge-soft badge-soft-success">Hoạt động</span>
                                                <?php else: ?>
                                                    <span class="badge badge-soft badge-soft-secondary">Đã khóa</span>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="index.php?act=user_edit&id=<?= $user['nguoi_dung_id'] ?>" 
                                                       class="btn-icon edit" title="Sửa thông tin">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    
                                                    <a href="index.php?act=user_delete&id=<?= $user['nguoi_dung_id'] ?>" 
                                                       class="btn-icon delete" 
                                                       onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?')" 
                                                       title="Xóa tài khoản">
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
                            <h5 class="text-muted mt-3">Không tìm thấy người dùng</h5>
                            <p class="text-muted mb-4">Thử thay đổi từ khóa tìm kiếm hoặc thêm người dùng mới.</p>
                            
                            <?php if(isset($_GET['keyword'])): ?>
                                <a href="index.php?act=user_list" class="btn btn-outline-secondary px-4">Xóa bộ lọc</a>
                            <?php else: ?>
                                <a href="index.php?act=user_create" class="btn btn-success px-4">Thêm mới</a>
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