<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Bổ Sung Thông Tin Khách Hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/css/sidebar.css">

    <style>
        /* MAIN LAYOUT */
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

        /* CARD STYLE */
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            background: #fff;
        }

        /* FORM ELEMENTS */
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

        .form-control {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 10px 15px;
            font-size: 0.95rem;
        }

        /* Màu xanh lá cho Khách Hàng */
        .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #6c757d;
            border-radius: 8px 0 0 8px;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 700;
            color: #198754;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            margin-bottom: 20px;
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
                    <h3 class="fw-bold text-dark mb-1">Hoàn Tất Hồ Sơ Khách Hàng</h3>
                    <p class="text-muted mb-0">Bước 2: Bổ sung thông tin cá nhân cho Khách hàng.</p>
                </div>
                <a href="index.php?act=user_create" class="btn btn-outline-secondary px-4 shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại thêm TK
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card card-custom p-4">

                        <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                            <div>
                                Tài khoản <strong><?= htmlspecialchars($user['ho_ten'] ?? 'Khách hàng') ?></strong> đã được tạo thành công.<br>
                                <small>Vui lòng cập nhật số CCCD và địa chỉ để hoàn tất hồ sơ.</small>
                            </div>
                        </div>

                        <form action="index.php?act=store_khachhang_detail" method="POST" class="needs-validation" novalidate>

                            <input type="hidden" name="nguoi_dung_id" value="<?= $user['nguoi_dung_id'] ?>">

                            <div class="section-title"><i class="bi bi-person-vcard me-2"></i>Thông tin Định danh</div>

                            <div class="mb-4">
                                <label for="cccd" class="form-label required">Căn Cước Công Dân (CCCD)</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text"><i class="bi bi-postcard"></i></span>
                                    <input type="text" class="form-control" id="cccd" name="cccd" required
                                        placeholder="Nhập số CCCD/CMND">
                                    <div class="invalid-feedback">Vui lòng nhập số CCCD.</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="dia_chi" class="form-label required">Địa Chỉ Liên Hệ</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" class="form-control" id="dia_chi" name="dia_chi" required
                                        placeholder="Số nhà, đường, phường/xã, quận/huyện...">
                                    <div class="invalid-feedback">Vui lòng nhập địa chỉ liên hệ.</div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-3">
                                <a href="index.php?act=user_list" class="btn btn-light border px-4">Bỏ qua & Về danh sách</a>

                                <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm">
                                    <i class="bi bi-check-lg me-2"></i> Lưu & Hoàn Tất
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
        // Script validate form bootstrap
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