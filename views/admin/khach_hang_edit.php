<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Cập Nhật Khách Hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/css/sidebar.css">

    <style>
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', sans-serif;
        }

        .main-content {
            padding: 30px;
            margin-top: 70px;
            margin-left: 0;
            min-height: 100vh;
        }

        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            background: #fff;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
            margin-bottom: 6px;
        }

        .required::after {
            content: " *";
            color: #dc3545;
        }

        /* Edit Mode Styles */
        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-radius: 8px 0 0 8px;
            color: #6c757d;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0d6efd;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            margin-bottom: 20px;
            margin-top: 10px;
        }

        /* Avatar Placeholder */
        .img-preview-container {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 0 auto 15px;
            background: #e7f1ff;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            color: #0d6efd;
            font-size: 3rem;
            font-weight: bold;
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
                    <h3 class="fw-bold text-dark mb-1">Cập Nhật Hồ Sơ Khách Hàng</h3>
                    <p class="text-muted mb-0">Chỉnh sửa thông tin chi tiết cho ID: #<?= htmlspecialchars($info['khach_hang_id']) ?></p>
                </div>
                <a href="index.php?act=khachhang_list" class="btn btn-outline-secondary px-4 shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card card-custom p-4">

                        <form action="index.php?act=update_khach_hang_submit" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="khach_hang_id" value="<?= $info['khach_hang_id'] ?>">
                            <input type="hidden" name="nguoi_dung_id" value="<?= $info['nguoi_dung_id'] ?>">

                            <div class="row">
                                <div class="col-md-4 text-center mb-4">
                                    <div class="section-title text-start"><i class="bi bi-image me-2"></i>Ảnh & Trạng Thái</div>

                                    <?php
                                    $char = strtoupper(substr($info['ho_ten'] ?? 'U', 0, 1));
                                    ?>
                                    <div class="img-preview-container">
                                        <?= $char ?>
                                    </div>

                                    <div class="mb-4 text-start text-center">
                                        <small class="text-muted">Ảnh đại diện mặc định theo tên.</small>
                                    </div>

                                    <div class="text-start">
                                        <label class="form-label">Trạng thái tài khoản</label>
                                        <div class="p-3 border rounded bg-light">
                                            <?php
                                            $status = $info['trang_thai'] ?? 'active';
                                            if ($status == 'active' || $status == 1) {
                                                echo '<span class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Đang hoạt động</span>';
                                            } else {
                                                echo '<span class="text-danger fw-bold"><i class="bi bi-lock-fill"></i> Đã khóa</span>';
                                            }
                                            ?>
                                            <div class="mt-2 small text-muted">
                                                Để thay đổi trạng thái, vui lòng truy cập <a href="index.php?act=user_list">Quản lý User</a>.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-8">

                                    <div class="section-title"><i class="bi bi-person-badge me-2"></i>Thông Tin Tài Khoản</div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label required">Họ và tên</label>
                                            <input type="text" class="form-control" name="ho_ten"
                                                value="<?= htmlspecialchars($info['ho_ten'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required">Email (Đăng nhập)</label>
                                            <input type="email" class="form-control" name="email"
                                                value="<?= htmlspecialchars($info['email'] ?? '') ?>" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label required">Số điện thoại</label>
                                        <input type="text" class="form-control" name="so_dien_thoai"
                                            value="<?= htmlspecialchars($info['so_dien_thoai'] ?? '') ?>" required>
                                    </div>

                                    <div class="section-title mt-4"><i class="bi bi-person-vcard me-2"></i>Thông Tin Cá Nhân</div>



                                    <div class="mb-3">
                                        <label class="form-label">Số CCCD / CMND</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                                            <input type="text" class="form-control" name="cccd"
                                                value="<?= htmlspecialchars($info['cccd'] ?? '') ?>" placeholder="Nhập số giấy tờ tùy thân">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Địa chỉ liên hệ</label>
                                        <textarea class="form-control" name="dia_chi" rows="3"><?= htmlspecialchars($info['dia_chi'] ?? '') ?></textarea>
                                    </div>

                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-3">
                                <a href="index.php?act=khachhang_list" class="btn btn-light border px-4">Hủy bỏ</a>
                                <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                                    <i class="bi bi-check-circle-fill me-2"></i> Lưu Thay Đổi
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="./assets/js/sidebar.js"></script>
    <script>
        // Validate form Bootstrap
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
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