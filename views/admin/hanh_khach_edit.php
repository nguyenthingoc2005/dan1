<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập Nhật Hành Khách</title>
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

        /* ACCORDION STYLE */
        .accordion-item {
            border: 1px solid #e9ecef;
            border-radius: 8px !important;
            margin-bottom: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .accordion-button {
            background-color: #fff;
            color: #495057;
            font-weight: 600;
            padding: 15px 20px;
        }
        .accordion-button:not(.collapsed) {
            background-color: #e7f1ff;
            color: #0d6efd;
            box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
        }
        
        /* STT Badge */
        .stt-badge {
            width: 28px; height: 28px;
            background-color: #e9ecef; color: #495057;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.85rem; margin-right: 15px; font-weight: bold;
        }
        .accordion-button:not(.collapsed) .stt-badge {
            background-color: #0d6efd; color: #fff;
        }

        /* FORM ELEMENTS */
        .form-label { font-weight: 600; font-size: 0.9rem; color: #6c757d; margin-bottom: 5px; }
        .required::after { content: " *"; color: #dc3545; }
        
        .form-control, .form-select { 
            border-radius: 8px; padding: 10px 15px; border: 1px solid #dee2e6; transition: all 0.2s; 
        }
        .form-control:focus, .form-select:focus { 
            border-color: #0d6efd; box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15); 
        }
        
        .input-group-text { background-color: #f8f9fa; border: 1px solid #dee2e6; border-right: none; border-radius: 8px 0 0 8px; color: #6c757d; }
        .input-icon-control { border-left: none; border-radius: 0 8px 8px 0; }
        .input-icon-control:focus { border-color: #dee2e6; border-left-color: #dee2e6; }
    </style>
</head>
<body>

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Cập Nhật Danh Sách Khách</h3>
                    <p class="text-muted mb-0">
                        Đơn hàng #<?= htmlspecialchars($data['dat_tour_id']) ?> 
                        <span class="mx-2">|</span> 
                        Tổng số: <strong class="text-primary"><?= htmlspecialchars($data['so_nguoi']) ?></strong> người
                    </p>
                </div>
                
                <a href="<?= BASEURL ?>?act=dat_tour_edit&dat_tour_id=<?= htmlspecialchars($data['dat_tour_id']) ?>" class="btn btn-outline-secondary px-4 shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại Sửa Đơn
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card card-custom p-4">
                        
                        <form action="<?= BASEURL ?>?act=hanh_khach_update&dat_tour_id=<?= htmlspecialchars($data['dat_tour_id']) ?>" method="POST">
                            
                            <input type="hidden" name="dat_tour_id" value="<?= htmlspecialchars($data['dat_tour_id']) ?>">
                            
                            <input type="hidden" name="next_step" value="deposit"> 

                            <div class="accordion" id="accordionPassengers">
                                <?php 
                                $so_luong_nguoi = (int)($data['so_nguoi'] ?? 0);
                                
                                for ($i = 0; $i < $so_luong_nguoi; $i++): 
                                    $hk = $hanhKhachList[$i] ?? []; 
                                    $stt = $i + 1;
                                    $is_expanded = ($i === 0) ? 'true' : 'false'; 
                                    $show_class = ($i === 0) ? 'show' : '';
                                ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?= $i ?>">
                                        <button class="accordion-button <?= ($i === 0) ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $i ?>" aria-expanded="<?= $is_expanded ?>" aria-controls="collapse<?= $i ?>">
                                            <span class="stt-badge"><?= $stt ?></span>
                                            <span class="fw-bold name-display-<?= $i ?>">
                                                <?= !empty($hk['ho_ten']) ? htmlspecialchars($hk['ho_ten']) : 'Hành khách #' . $stt ?>
                                            </span>
                                            
                                            <?php if(!empty($hk['so_giay_to'])): ?>
                                                <span class="badge bg-light text-secondary ms-3 fw-normal border">
                                                    <i class="bi bi-card-heading"></i> <?= htmlspecialchars($hk['so_giay_to']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $i ?>" class="accordion-collapse collapse <?= $show_class ?>" aria-labelledby="heading<?= $i ?>" data-bs-parent="#accordionPassengers">
                                        <div class="accordion-body bg-white p-4">
                                            
                                            <input type="hidden" name="hanh_khach[<?= $i ?>][hanh_khach_id]" value="<?= htmlspecialchars($hk['hanh_khach_id'] ?? 0) ?>">

                                            <div class="row g-3">
                                                
                                                <div class="col-md-6">
                                                    <label class="form-label required">Họ và Tên</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                                        <input type="text" class="form-control input-icon-control name-input" 
                                                               data-index="<?= $i ?>"
                                                               name="hanh_khach[<?= $i ?>][ho_ten]" 
                                                               value="<?= htmlspecialchars($hk['ho_ten'] ?? '') ?>" 
                                                               required placeholder="Nhập họ tên đầy đủ">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Giới Tính</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-gender-ambiguous"></i></span>
                                                        <?php $gender = $hk['gioi_tinh'] ?? ''; ?>
                                                        <select class="form-select input-icon-control" name="hanh_khach[<?= $i ?>][gioi_tinh]">
                                                            <option value="" <?= empty($gender) ? 'selected' : '' ?>>-- Chọn --</option>
                                                            <option value="Nam" <?= $gender == 'Nam' ? 'selected' : '' ?>>Nam</option>
                                                            <option value="Nữ" <?= $gender == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                                                            <option value="Khác" <?= $gender == 'Khác' ? 'selected' : '' ?>>Khác</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Số Giấy Tờ (CCCD/Passport)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                                                        <input type="text" class="form-control input-icon-control" 
                                                               name="hanh_khach[<?= $i ?>][so_giay_to]" 
                                                               value="<?= htmlspecialchars($hk['so_giay_to'] ?? '') ?>" 
                                                               placeholder="Số giấy tờ tùy thân">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Ngày Sinh</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                                        <input type="date" class="form-control input-icon-control" 
                                                               name="hanh_khach[<?= $i ?>][ngay_sinh]" 
                                                               value="<?= htmlspecialchars($hk['ngay_sinh'] ?? '') ?>">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Liên Hệ (SĐT)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                                        <input type="text" class="form-control input-icon-control" 
                                                               name="hanh_khach[<?= $i ?>][lien_he]" 
                                                               value="<?= htmlspecialchars($hk['lien_he'] ?? '') ?>" 
                                                               placeholder="Nhập số điện thoại liên lạc">
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label">Yêu Cầu Cá Nhân / Ghi Chú</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-chat-left-text"></i></span>
                                                        <textarea class="form-control input-icon-control" rows="2"
                                                                  name="hanh_khach[<?= $i ?>][yeu_cau_ca_nhan]" 
                                                                  placeholder="Ví dụ: Ăn chay, dị ứng, cần hỗ trợ xe lăn..."><?= htmlspecialchars($hk['yeu_cau_ca_nhan'] ?? '') ?></textarea>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted fst-italic small">
                                    * Nhấn "Hoàn thành" để lưu tất cả và chuyển sang bước thanh toán.
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?= BASEURL ?>?act=dat_tour_detail&dat_tour_id=<?= htmlspecialchars($data['dat_tour_id']) ?>" 
                                       class="btn btn-light border px-4">
                                        <i class="bi bi-x-circle me-2"></i> Bỏ qua
                                    </a>
                                    
                                    <button type="submit" class="btn btn-success px-4 fw-bold shadow-sm">
                                        <i class="bi bi-check-circle-fill me-2"></i> Hoàn thành 
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
    
    <script>
        // Tự động cập nhật tên trên header accordion khi người dùng nhập
        document.addEventListener('DOMContentLoaded', function() {
            const nameInputs = document.querySelectorAll('.name-input');
            nameInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const index = this.getAttribute('data-index');
                    const displaySpan = document.querySelector('.name-display-' + index);
                    if (displaySpan) {
                        displaySpan.textContent = this.value.trim() || ('Hành khách #' + (parseInt(index) + 1));
                    }
                });
            });
        });
    </script>
</body>
</html>