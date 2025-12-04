
<?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập Nhật HDV</title>
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
        
        /* Edit Mode Styles */
        .form-control:focus, .form-select:focus { border-color: #ffc107; box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25); }
        .input-group-text { background-color: #f8f9fa; border-radius: 8px 0 0 8px; color: #6c757d; }
        .section-title { font-size: 1.1rem; font-weight: 700; color: #d63384; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 20px; margin-top: 10px; }

        /* Image Preview */

        .img-preview-container { width: 150px; height: 150px; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); overflow: hidden; margin: 0 auto 15px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; position: relative; }
        .img-preview-container img { width: 100%; height: 100%; object-fit: cover; }
        .current-status { position: absolute; top: 10px; right: 10px; }
    </style>
</head>
<body>

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Cập Nhật Hồ Sơ HDV</h3>
                    <p class="text-muted mb-0">Chỉnh sửa thông tin chi tiết cho ID: #<?= htmlspecialchars($hdv['hdv_id']) ?></p>
                </div>
                <a href="index.php?act=hdv" class="btn btn-outline-secondary px-4 shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card card-custom p-4">
                        
                        <form action="index.php?act=update_hdv_submit" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            
                            <input type="hidden" name="hdv_id" value="<?= $hdv['hdv_id'] ?>">
                            <input type="hidden" name="old_img" value="<?= $hdv['anh_dai_dien'] ?? '' ?>">

                            <div class="row">
                                <div class="col-md-4 text-center mb-4">
                                    <div class="section-title text-start"><i class="bi bi-image me-2"></i>Ảnh & Trạng Thái</div>
                                    
                                    <?php 
                                        $imgUrl = !empty($hdv['anh_dai_dien']) ? './assets/uploads/hdv/' . $hdv['anh_dai_dien'] : './assets/images/default-avatar.png';
                                    ?>
                                    <div class="img-preview-container" id="avatarPreview">
                                        <?php if(!empty($hdv['anh_dai_dien'])): ?>
                                            <img src="<?= $imgUrl ?>" alt="Avatar" onerror="this.src='https://via.placeholder.com/150'">
                                        <?php else: ?>
                                            <i class="bi bi-person fs-1 text-secondary"></i>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mb-4 text-start">
                                        <label for="anh_dai_dien" class="form-label">Đổi ảnh đại diện</label>
                                        <input type="file" class="form-control" id="anh_dai_dien" name="anh_dai_dien" accept="image/*" onchange="previewImage(this)">
                                        <small class="text-muted">Chỉ chọn nếu muốn thay đổi ảnh cũ.</small>
                                    </div>

                                    <div class="text-start">
                                        <label for="tinh_trang_hoat_dong" class="form-label required">Trạng thái hoạt động</label>
                                        <select class="form-select border-warning" name="tinh_trang_hoat_dong" required>
                                            <?php 
                                                $status = $hdv['tinh_trang_hoat_dong'] ?? 'Sẵn sàng';
                                                $options = ['Sẵn sàng', 'Bận', 'Đang dẫn tour', 'Tạm ngưng'];
                                                foreach($options as $opt) {
                                                    $selected = ($status == $opt) ? 'selected' : '';
                                                    echo "<option value='$opt' $selected>$opt</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="section-title"><i class="bi bi-person-vcard me-2"></i>Thông Tin Cá Nhân</div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Họ tên (User)</label>
                                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($hdv['ho_ten'] ?? '') ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email (User)</label>
                                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($hdv['email'] ?? '') ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="ngay_sinh" class="form-label required">Ngày sinh</label>
                                            <input type="date" class="form-control" id="ngay_sinh" name="ngay_sinh" 
                                                   value="<?= $hdv['ngay_sinh'] ?? '' ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="gioi_tinh" class="form-label required">Giới tính</label>
                                            <select class="form-select" name="gioi_tinh" required>
                                                <option value="Nam" <?= ($hdv['gioi_tinh'] ?? '') == 'Nam' ? 'selected' : '' ?>>Nam</option>
                                                <option value="Nữ" <?= ($hdv['gioi_tinh'] ?? '') == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="dia_chi_lien_he" class="form-label required">Địa chỉ liên hệ</label>
                                        <input type="text" class="form-control" name="dia_chi_lien_he" 
                                               value="<?= htmlspecialchars($hdv['dia_chi_lien_he'] ?? '') ?>" required>
                                    </div>

                                    <div class="section-title mt-4"><i class="bi bi-briefcase me-2"></i>Năng Lực & Chuyên Môn</div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="ngon_ngu_su_dung" class="form-label required">Ngôn ngữ</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-translate"></i></span>
                                                <input type="text" class="form-control" name="ngon_ngu_su_dung" 
                                                       value="<?= htmlspecialchars($hdv['ngon_ngu_su_dung'] ?? '') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="tinh_trang_suc_khoe" class="form-label">Sức khỏe</label>
                                            <input type="text" class="form-control" name="tinh_trang_suc_khoe" 
                                                   value="<?= htmlspecialchars($hdv['tinh_trang_suc_khoe'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="kinh_nghiem_lam_viec" class="form-label required">Kinh nghiệm làm việc</label>
                                        <textarea class="form-control" name="kinh_nghiem_lam_viec" rows="3" required><?= htmlspecialchars($hdv['kinh_nghiem_lam_viec'] ?? '') ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="chung_chi_chuyen_mon" class="form-label">Chứng chỉ & Bằng cấp</label>
                                        <textarea class="form-control" name="chung_chi_chuyen_mon" rows="2"><?= htmlspecialchars($hdv['chung_chi_chuyen_mon'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-3">
                                <a href="index.php?act=hdv" class="btn btn-light border px-4">Hủy bỏ</a>
                                <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm text-dark">
                                    <i class="bi bi-check-circle-fill me-2"></i> Cập Nhật Thông Tin
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
        // Preview ảnh khi chọn file
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

        // Validate form Bootstrap
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
