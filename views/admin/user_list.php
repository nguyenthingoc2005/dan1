<?php
if(session_status() === PHP_SESSION_NONE) session_start();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách người dùng</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>

<!-- Sidebar -->
<?php include './views/parts/sidebar.php'; ?>

<!-- Overlay for sidebar -->
<div class="overlay"></div>

<!-- Main Content -->
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Danh sách người dùng</h2>
        <a href="index.php?act=user_create" class="btn btn-primary">+ Thêm tài khoản</a>
    </div>

    <div class="table-responsive shadow-sm rounded bg-white p-3">
        <table class="table table-hover table-bordered align-middle mb-0">
            <thead class="table-dark text-center">
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Họ tên</th>
                    <th>Điện thoại</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php if(!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="text-center"><?= $user['nguoi_dung_id'] ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['ho_ten']) ?></td>
                        <td><?= htmlspecialchars($user['so_dien_thoai']) ?></td>
                        <td><?= htmlspecialchars($user['ten_vai_tro']) ?></td>
                        <td class="text-center">
                            <?php if($user['trang_thai'] === 'hoat_dong'): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Khóa</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="index.php?act=user_edit&id=<?= $user['nguoi_dung_id'] ?>" class="btn btn-sm btn-warning me-1">
                                <i class='bx bx-edit-alt'></i> Sửa
                            </a>
                            <a href="index.php?act=user_delete&id=<?= $user['nguoi_dung_id'] ?>" onclick="return confirm('Xóa user này?')" class="btn btn-sm btn-danger">
                                <i class='bx bx-trash'></i> Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Chưa có người dùng nào.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap JS + Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="./assets/js/sidebar.js"></script>

</body>
</html>
