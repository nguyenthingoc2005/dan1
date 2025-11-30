<?php
if(session_status() === PHP_SESSION_NONE) session_start();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Người Dùng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        /* MAIN LAYOUT */
        body { background-color: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .main-content { padding: 30px; margin-top: 70px; margin-left: 0; min-height: 100vh; }
        
        /* CARD & FORM STYLE */
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            background: #fff;
        }
        
        .form-label { font-weight: 600; color: #495057; font-size: 0.9rem; margin-bottom: 6px; }
        .required::after { content: " *"; color: #dc3545; }
        
        .form-control, .form-select {
            border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px; font-size: 0.95rem;
            transition: border-color 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #198754; box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.15); /* Màu xanh lá */
        }

        /* INPUT GROUP ICONS */
        .input-group-text {
            background-color: #f8f9fa; border: 1px solid #dee2e6; color: #6c757d; border-radius: 8px 0 0 8px;
        }
        
        /* SECTION TITLE */
        .section-title {
            font-size: 1rem; font-weight: 700; color: #198754;
            border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 20px; margin-top: 10px;
        }
    </style>
</head>
<body>

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Thêm Tài Khoản Mới</h3>
                    <p class="text-muted mb-0">Tạo tài khoản cho quản trị viên, nhân viên hoặc khách hàng.</p>
                </div>
                <a href="index.php?act=user_list" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card card-custom p-4">
                        <form action="index.php?act=user_store" method="POST">
                            
                            <div class="row g-4">
                                
                                <div class="col-md-6">
                                    <div class="section-title"><i class="bi bi-shield-lock me-2"></i>Thông tin Đăng nhập</div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label required">Email Đăng Nhập</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                            <input type="email" class="form-control" id="email" name="email" required placeholder="example@gmail.com">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="mat_khau" class="form-label required">Mật Khẩu</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                                            <input type="password" class="form-control" id="mat_khau" name="mat_khau" required minlength="6" placeholder="Tối thiểu 6 ký tự">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="vai_tro_id" class="form-label required">Vai Trò (Role)</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                            <select class="form-select" id="vai_tro_id" name="vai_tro_id" required>
                                                <option value="" selected disabled>-- Chọn vai trò --</option>
                                                <?php if(!empty($roles)): foreach($roles as $role): ?>
                                                    <option value="<?= $role['vai_tro_id'] ?>"><?= htmlspecialchars($role['ten']) ?></option>
                                                <?php endforeach; endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="section-title"><i class="bi bi-person-vcard me-2"></i>Thông tin Cá nhân</div>

                                    <div class="mb-3">
                                        <label for="ho_ten" class="form-label required">Họ và Tên</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control fw-bold text-dark" id="ho_ten" name="ho_ten" required placeholder="Nhập họ tên đầy đủ">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="so_dien_thoai" class="form-label">Số Điện Thoại</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                            <input type="text" class="form-control" id="so_dien_thoai" name="so_dien_thoai" placeholder="Nhập SĐT liên hệ">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="trang_thai" class="form-label">Trạng Thái</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-toggle-on"></i></span>
                                            <select class="form-select" id="trang_thai" name="trang_thai">
                                                <option value="active" selected>Hoạt động (Active)</option>
                                                <option value="inactive">Khóa (Inactive)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-3">
                                <a href="index.php?act=user_list" class="btn btn-light border px-4">Hủy bỏ</a>
                                <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm">
                                    <i class="bi bi-check-lg me-2"></i> Lưu Người Dùng
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="./assets/js/sidebar.js"></script>
</body>
</html>