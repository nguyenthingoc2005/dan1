
<?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Hướng Dẫn Viên</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        body { background-color: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .main-content { padding: 30px; margin-top: 70px; margin-left: 0; min-height: 100vh; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); background: #fff; }
        .form-label { font-weight: 600; color: #495057; font-size: 0.9rem; margin-bottom: 6px; }
        .required::after { content: " *"; color: #dc3545; }
        .form-control { border-radius: 8px; padding: 10px 15px; }
        .input-group-text { border-radius: 8px 0 0 8px; background-color: #f8f9fa; color: #6c757d; }
        .section-title { font-size: 1rem; font-weight: 700; color: #198754; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Tạo Hướng Dẫn Viên</h3>
                    <p class="text-muted mb-0">Thêm thông tin HDV mới vào hệ thống.</p>
                </div>
                <a href="<?= BASEURL ?>?act=hdv" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card card-custom p-4">
                        <form action="<?= BASEURL ?>?act=createhdv" method="POST" class="needs-validation" novalidate>
                            
                            <input type="hidden" name="nguoi_dung_id" value="<?= isset($user_id) ? $user_id : '' ?>">
                            
                            <div class="section-title"><i class="bi bi-info-circle me-2"></i>Thông tin chung</div>

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label required">Họ và Tên</label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control" name="ho_ten" required minlength="2" placeholder="Nhập tên HDV">
                                            <div class="invalid-feedback">Vui lòng nhập họ tên (tối thiểu 2 ký tự).</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Số Điện Thoại</label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                            <input type="tel" class="form-control" name="so_dien_thoai" required pattern="0[0-9]{9}" title="Số điện thoại gồm 10 chữ số bắt đầu bằng 0" placeholder="09xxxxxxxx">
                                            <div class="invalid-feedback">Vui lòng nhập đúng định dạng SĐT (10 số, bắt đầu bằng 0).</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Email Liên Hệ</label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                            <input type="email" class="form-control" name="email" required placeholder="email@example.com">
                                            <div class="invalid-feedback">Vui lòng nhập địa chỉ email hợp lệ.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section-title mt-4"><i class="bi bi-briefcase me-2"></i>Thông tin Nghề nghiệp</div>

                            <div class="mb-3">
                                <label class="form-label required">Kinh Nghiệm</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text"><i class="bi bi-star"></i></span>
                                    <input type="text" class="form-control" name="kinh_nghiem" required placeholder="Ví dụ: 5 năm, chuyên tour biển đảo...">
                                    <div class="invalid-feedback">Vui lòng nhập thông tin kinh nghiệm.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Ngôn Ngữ Thành Thạo</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text"><i class="bi bi-translate"></i></span>
                                    <input type="text" class="form-control" name="ngon_ngu" required placeholder="Ví dụ: Tiếng Việt, Tiếng Anh...">
                                    <div class="invalid-feedback">Vui lòng nhập ít nhất 1 ngôn ngữ.</div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-3">
                                <a href="<?= BASEURL ?>?act=hdv" class="btn btn-light border px-4">Hủy bỏ</a>
                                <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm">
                                    <i class="bi bi-check-lg me-2"></i> Lưu Hướng Dẫn Viên
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
        (function () {
            'use strict'
            // Lấy tất cả các form có class .needs-validation
            var forms = document.querySelectorAll('.needs-validation')

            // Duyệt qua từng form và chặn submit nếu không hợp lệ
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
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
