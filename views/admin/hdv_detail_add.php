<?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bổ Sung Thông Tin HDV</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        /* MAIN LAYOUT */
        body { background-color: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .main-content { padding: 30px; margin-top: 70px; margin-left: 0; min-height: 100vh; }
        
        /* CARD STYLE */
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); background: #fff; }
        
        /* FORM ELEMENTS */
        .form-label { font-weight: 600; color: #495057; font-size: 0.9rem; margin-bottom: 6px; }
        .required::after { content: " *"; color: #dc3545; }
        
        .form-control, .form-select { border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px; font-size: 0.95rem; }
        .form-control:focus, .form-select:focus { border-color: #ffc107; box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25); } 
        
        .input-group-text { background-color: #f8f9fa; border: 1px solid #dee2e6; color: #6c757d; border-radius: 8px 0 0 8px; }
        
        .section-title { font-size: 1.1rem; font-weight: 700; color: #d63384; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 20px; margin-top: 10px; }
        
        /* IMAGE PREVIEW */
        .img-preview-container { width: 150px; height: 150px; border-radius: 50%; border: 3px solid #dee2e6; overflow: hidden; margin: 0 auto 15px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; }
        .img-preview-container img { width: 100%; height: 100%; object-fit: cover; }
    </style>
</head>
<body>

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Hoàn Tất Hồ Sơ HDV</h3>
                    <p class="text-muted mb-0">Cập nhật đầy đủ thông tin để kích hoạt tài khoản Hướng dẫn viên.</p>
                </div>
                <a href="index.php?act=user_create" class="btn btn-outline-secondary px-4 shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card card-custom p-4">
                        
                        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                            <div>
                                Đang cập nhật thông tin cho: <strong><?= htmlspecialchars($user['ho_ten'] ?? 'Người dùng') ?></strong><br>
                                <small>Các trường có dấu sao (<span class="text-danger">*</span>) là bắt buộc.</small>
                            </div>
                        </div>

                        <form action="<?=BASE_URL ?>?act=store_hdv_detail" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            
                            <input type="hidden" name="nguoi_dung_id" value="<?= $user['nguoi_dung_id'] ?>">
                            <input type="hidden" name="ho_ten" value="<?= htmlspecialchars($user['ho_ten'] ?? '') ?>">
                            <input type="hidden" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">

                            <div class="row">
                                <div class="col-md-4 text-center mb-4">
                                    <div class="section-title text-start"><i class="bi bi-image me-2"></i>Ảnh Đại Diện</div>
                                    
                                    <div class="img-preview-container" id="avatarPreview">
                                        <i class="bi bi-person fs-1 text-secondary"></i>
                                    </div>
                                    
                                    <div class="mb-3 text-start">
                                        <label for="anh_dai_dien" class="form-label required">Chọn ảnh chân dung</label>
                                        <input type="file" class="form-control" id="anh_dai_dien" name="anh_dai_dien" accept="image/*" onchange="previewImage(this)" required>
                                        <div class="invalid-feedback">Vui lòng chọn ảnh đại diện.</div>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="section-title"><i class="bi bi-person-lines-fill me-2"></i>Thông Tin Cơ Bản</div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="ngay_sinh" class="form-label required">Ngày sinh</label>
                                            <input type="date" class="form-control" id="ngay_sinh" name="ngay_sinh" required>
                                            <div class="invalid-feedback">Vui lòng chọn ngày sinh.</div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="gioi_tinh" class="form-label required">Giới tính</label>
                                            <select class="form-select" id="gioi_tinh" name="gioi_tinh" required>
                                                <option value="" selected disabled>-- Chọn --</option>
                                                <option value="Nam">Nam</option>
                                                <option value="Nữ">Nữ</option>
                                            </select>
                                            <div class="invalid-feedback">Vui lòng chọn giới tính.</div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="dia_chi_lien_he" class="form-label required">Địa chỉ liên hệ</label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                            <input type="text" class="form-control" id="dia_chi_lien_he" name="dia_chi_lien_he" required placeholder="Số nhà, đường, phường/xã...">
                                            <div class="invalid-feedback">Vui lòng nhập địa chỉ.</div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Số điện thoại</label>
                                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['so_dien_thoai'] ?? '') ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="section-title"><i class="bi bi-briefcase me-2"></i>Năng Lực & Chuyên Môn</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="ngon_ngu_su_dung" class="form-label required">Ngôn ngữ thành thạo</label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text"><i class="bi bi-translate"></i></span>
                                        <input type="text" class="form-control" id="ngon_ngu_su_dung" name="ngon_ngu_su_dung" required placeholder="VD: Anh, Trung, Pháp...">
                                        <div class="invalid-feedback">Nhập ít nhất 1 ngôn ngữ.</div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="tinh_trang_suc_khoe" class="form-label">Tình trạng sức khỏe</label>
                                    <input type="text" class="form-control" id="tinh_trang_suc_khoe" name="tinh_trang_suc_khoe" placeholder="VD: Tốt, không say xe...">
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="kinh_nghiem_lam_viec" class="form-label required">Kinh nghiệm làm việc</label>
                                    <textarea class="form-control" id="kinh_nghiem_lam_viec" name="kinh_nghiem_lam_viec" rows="3" required placeholder="Mô tả số năm kinh nghiệm, các loại tour sở trường..."></textarea>
                                    <div class="invalid-feedback">Vui lòng nhập kinh nghiệm.</div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="chung_chi_chuyen_mon" class="form-label">Chứng chỉ & Bằng cấp</label>
                                    <textarea class="form-control" id="chung_chi_chuyen_mon" name="chung_chi_chuyen_mon" rows="2" placeholder="VD: Thẻ HDV Quốc tế số..., Chứng nhận sơ cấp cứu..."></textarea>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-3">
                                <a href="index.php?act=user_list" class="btn btn-light border px-4">Bỏ qua</a>
                                <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm text-dark">
                                    <i class="bi bi-check-circle-fill me-2"></i> Lưu & Hoàn Tất
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
        // Script preview ảnh
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var container = document.getElementById('avatarPreview');
                    container.innerHTML = '<img src="' + e.target.result + '" alt="Avatar Preview">';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Script validate form bootstrap
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
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