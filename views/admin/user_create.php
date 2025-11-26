<?php
if(session_status() === PHP_SESSION_NONE) session_start();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm người dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<div class="container my-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Thêm người dùng</h2>
        <a href="index.php?act=user_list" class="btn btn-secondary">← Quay lại</a>
    </div>

    <form action="index.php?act=user_store" method="POST" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="email" name="email" required>
            <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
        </div>

        <div class="mb-3">
            <label for="mat_khau" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
            <input type="password" class="form-control" id="mat_khau" name="mat_khau" required minlength="6">
            <div class="invalid-feedback">Mật khẩu ít nhất 6 ký tự.</div>
        </div>

        <div class="mb-3">
            <label for="ho_ten" class="form-label">Họ tên <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="ho_ten" name="ho_ten" required>
            <div class="invalid-feedback">Vui lòng nhập họ tên.</div>
        </div>

        <div class="mb-3">
            <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
            <input type="text" class="form-control" id="so_dien_thoai" name="so_dien_thoai">
        </div>

        <div class="mb-3">
            <label for="vai_tro_id" class="form-label">Vai trò <span class="text-danger">*</span></label>
            <select class="form-select" id="vai_tro_id" name="vai_tro_id" required>
                <option value="">-- Chọn vai trò --</option>
                <?php foreach($roles as $role): ?>
                    <option value="<?= $role['vai_tro_id'] ?>"><?= htmlspecialchars($role['ten']) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Vui lòng chọn vai trò.</div>
        </div>

        <div class="mb-3">
            <label for="trang_thai" class="form-label">Trạng thái</label>
            <select class="form-select" id="trang_thai" name="trang_thai">
                <option value="hoat_dong" selected>Hoạt động</option>
                <option value="khoa">Khóa</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Lưu</button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Bootstrap validation
(() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if(!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

</body>
</html>
