<?php
// PHP BLOCK: KHỞI TẠO DỮ LIỆU

$tour_id_safe = htmlspecialchars($_GET['tour_id'] ?? '');

$assigned_ids = array_column($diaDiemDaGan ?? [], 'dia_diem_id'); 
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Gán Địa điểm</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        /* === MAIN LAYOUT (GIỐNG MẪU BẠN GỬI) === */
        body { background-color: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        
        .main-content {
            padding: 30px;
            margin-top: 70px;
            margin-left: 0;   
            transition: margin-left .32s ease;
            min-height: 100vh;
        }

        /* CARD STYLE */
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            background: #fff;
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        /* TABLE STYLE */
        .table-custom thead th {
            background-color: #f8f9fa;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e9ecef;
            padding: 15px;
            white-space: nowrap;
        }
        .table-custom tbody td { vertical-align: middle; padding: 15px; border-bottom: 1px solid #f1f1f1; font-size: 0.9rem; }
        .table-custom tbody tr:hover { background-color: #fcfcfc; }

        /* BUTTON ICONS */
        .btn-icon {
            width: 34px; height: 34px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; transition: all 0.2s;
            color: #6c757d; background: #f8f9fa; border: 1px solid transparent;
            text-decoration: none;
        }
        .btn-icon:hover { transform: translateY(-2px); }
        .btn-icon.delete:hover { color: #dc3545; background: #f8d7da; }

        /* === CÁC STYLE RIÊNG CHO TRANG NÀY === */
        
        /* Ảnh địa điểm */
        .img-location {
            width: 60px; height: 45px; object-fit: cover; 
            border-radius: 6px; border: 1px solid #dee2e6;
        }

        /* Khu vực danh sách checkbox */
        .checkbox-area {
            max-height: 500px;
            overflow-y: auto;
            padding: 15px;
            background: #fff;
        }
        .checkbox-item {
            border: 1px solid #f1f1f1;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            transition: 0.2s;
        }
        .checkbox-item:hover {
            border-color: #0d6efd;
            background-color: #f8fbff;
        }
        
        /* Ô nhập ghi chú */
        .input-note-custom {
            width: 100%;
            border: none;
            border-bottom: 1px dashed #ccc;
            background: transparent;
            font-size: 0.85rem;
            padding: 5px 0;
            margin-top: 5px;
            color: #555;
        }
        .input-note-custom:focus {
            outline: none;
            border-bottom: 1px solid #0d6efd;
            color: #000;
        }

        /* Nút Footer */
        .btn-nav-simple {
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            border: 1px solid #dee2e6;
            background: #fff;
            color: #555;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-nav-simple:hover { background: #f8f9fa; color: #000; }
        .btn-nav-primary { background: #0d6efd; color: #fff; border-color: #0d6efd; }
        .btn-nav-primary:hover { background: #0b5ed7; color: #fff; }
    </style>
</head>
<body>

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Gán Địa Điểm</h3>
                    <p class="text-muted mb-0">Tour: <strong>#<?= $tour_id_safe ?></strong></p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card card-custom h-100">
                        <div class="card-body p-0">
                            <div class="p-3 border-bottom bg-light">
                                <h6 class="mb-0 fw-bold text-secondary text-uppercase">
                                    <i class="bi bi-check2-circle me-1"></i> Đã gán (<?= count($diaDiemDaGan ?? []) ?>)
                                </h6>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Địa điểm</th>
                                            <th style="width: 35%;">Mô tả</th> <th>Ghi chú</th>
                                            <th class="text-center">Xóa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($diaDiemDaGan)): ?>
                                            <tr><td colspan="4" class="text-center py-5 text-muted">Chưa có địa điểm nào được gán.</td></tr>
                                        <?php else: ?>
                                            <?php foreach($diaDiemDaGan as $dd): ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($dd['hinh_anh']): ?>
                                                            <img src="<?= htmlspecialchars($dd['hinh_anh']) ?>" class="img-location me-3">
                                                        <?php else: ?>
                                                            <div class="img-location me-3 d-flex align-items-center justify-content-center bg-secondary text-white small">No IMG</div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <div class="fw-bold text-dark"><?= htmlspecialchars($dd['ten_diadiem']) ?></div>
                                                            <small class="text-muted"><?= htmlspecialchars($dd['quoc_gia']) ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                
                                                <td>
                                                    <small class="text-secondary text-wrap" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                        <?= strip_tags(htmlspecialchars_decode($dd['mo_ta'] ?? 'Không có mô tả')) ?>
                                                    </small>
                                                </td>

                                                <td>
                                                    <em class="text-info small"><?= htmlspecialchars($dd['ghi_chu'] ?? '') ?></em>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?= BASEURL ?>?act=xoa_dia_diem&dia_diem_tour_id=<?= $dd['dia_diem_tour_id'] ?>&tour_id=<?= $tour_id_safe ?>" 
                                                       onclick="return confirm('Xóa địa điểm này?')" 
                                                       class="btn-icon delete">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <form action="<?= BASEURL ?>?act=luu_gan_diadiem&tour_id=<?= $tour_id_safe ?>" method="POST" id="formGanDiaDiem">
                        <input type="hidden" name="tour_id" value="<?= $tour_id_safe ?>">

                        <div class="card card-custom h-100">
                            <div class="p-3 border-bottom bg-light">
                                <h6 class="mb-0 fw-bold text-primary text-uppercase">
                                    <i class="bi bi-plus-lg me-1"></i> Thêm mới
                                </h6>
                            </div>

                            <div class="checkbox-area">
                                <?php 
                                $has_data = false;
                                foreach($data as $dd) {
                                    if (!in_array($dd['dia_diem_id'], $assigned_ids)) {
                                        $has_data = true; break;
                                    }
                                }
                                ?>

                                <?php if (!$has_data): ?>
                                    <div class="text-center text-success py-4">
                                        <i class="bi bi-check-all display-4"></i>
                                        <p class="fw-bold mt-2">Đã thêm hết địa điểm!</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach($data as $dd): ?>
                                        <?php if (in_array($dd['dia_diem_id'], $assigned_ids)) continue; ?>
                                        
                                        <div class="checkbox-item">
                                            <label class="d-flex align-items-center w-100" style="cursor: pointer;" for="chk_<?= $dd['dia_diem_id'] ?>">
                                                <input class="form-check-input me-2 mt-0" type="checkbox" 
                                                       name="dia_diem_id[]" 
                                                       value="<?= $dd['dia_diem_id'] ?>" 
                                                       id="chk_<?= $dd['dia_diem_id'] ?>" 
                                                       style="width: 18px; height: 18px;">
                                                
                                                <div class="flex-grow-1">
                                                    <span class="fw-bold text-dark"><?= htmlspecialchars($dd['ten']) ?></span>
                                                    <br>
                                                    <span class="small text-muted"><?= htmlspecialchars($dd['quoc_gia']) ?></span>
                                                </div>
                                            </label>
                                            
                                            <input type="text" 
                                                   name="ghi_chu_rieng[<?= $dd['dia_diem_id'] ?>]" 
                                                   class="input-note-custom" 
                                                   placeholder="Ghi chú...">
                                        </div>

                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                            <div class="p-3 border-top bg-white">
                                <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                                    <i class="bi bi-floppy me-2"></i> LƯU ĐỊA ĐIỂM
                                </button>
                                <div id="error-msg" class="text-danger small mt-2 text-center" style="display:none;">
                                    <i class="bi bi-exclamation-circle"></i> Vui lòng chọn ít nhất 1 địa điểm!
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-3 mb-5">
                <a href="<?= BASEURL ?>?act=uppdatetour&tour_id=<?= $tour_id_safe ?>" class="btn-nav-simple">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>

                <div class="d-flex gap-2">
                    <a href="<?= BASEURL ?>?act=chitiettour&tour_id=<?= $tour_id_safe ?>" class="btn-nav-simple">
                        <i class="bi bi-eye"></i> Xem Tour
                    </a>
                    <a href="<?= BASEURL ?>?act=addlichtrinh&tour_id=<?= $tour_id_safe ?>" class="btn-nav-simple btn-nav-primary">
                        Tiếp theo: Lịch trình <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
    
    <script>
        document.getElementById('formGanDiaDiem').addEventListener('submit', function(e) {
            // Lấy tất cả checkbox trong form
            const checkboxes = document.querySelectorAll('input[name="dia_diem_id[]"]:checked');
            const errorMsg = document.getElementById('error-msg');
            
            // Nếu không có checkbox nào được chọn
            if (checkboxes.length === 0) {
                e.preventDefault(); // CHẶN CHUYỂN TRANG
                errorMsg.style.display = 'block'; // Hiện thông báo lỗi
                // Tự động ẩn thông báo sau 3s
                setTimeout(() => { errorMsg.style.display = 'none'; }, 3000);
            }
        });
    </script>
</body>
</html>