<?php
// PHP BLOCK: Khởi tạo dữ liệu
$tour_id_safe = htmlspecialchars($tour_id ?? 2);
$assigned_ids = array_column($dichVuDaGan ?? [], 'dich_vu_id'); 

// Hàm helper để chọn icon dựa trên loại dịch vụ (Giả lập logic)
function getServiceIcon($type) {
    $t = strtolower($type);
    if (strpos($t, 'khách sạn') !== false || strpos($t, 'hotel') !== false) return 'bi-building';
    if (strpos($t, 'xe') !== false || strpos($t, 'transport') !== false) return 'bi-bus-front';
    if (strpos($t, 'ăn') !== false || strpos($t, 'food') !== false) return 'bi-cup-hot';
    if (strpos($t, 'vé') !== false || strpos($t, 'ticket') !== false) return 'bi-ticket-perforated';
    return 'bi-box-seam'; // Mặc định
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Dịch Vụ Tour</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        /* GLOBAL STYLE */
        body { background-color: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .main-content { padding: 30px; margin-top: 70px; min-height: 100vh; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.04); }

        /* NAVIGATION BUTTONS (Đẹp, Gọn) */
        .btn-nav {
            padding: 8px 16px;
            font-weight: 600;
            border-radius: 8px;
            display: inline-flex; align-items: center; gap: 6px;
            transition: all 0.2s;
        }
        .btn-nav-back { background: #fff; border: 1px solid #dee2e6; color: #6c757d; }
        .btn-nav-back:hover { background: #f8f9fa; color: #000; border-color: #c1c9d0; }
        
        .btn-nav-view { background: #e0faff; color: #0dcaf0; border: 1px solid transparent; }
        .btn-nav-view:hover { background: #0dcaf0; color: #fff; }

        .btn-nav-finish { background: #198754; color: #fff; border: 1px solid transparent; }
        .btn-nav-finish:hover { background: #146c43; color: #fff; }

        /* TABLE STYLE */
        .table-custom th { background-color: #f8f9fa; color: #8898aa; font-size: 0.8rem; text-transform: uppercase; font-weight: 700; padding: 12px 15px; border-bottom: 1px solid #eee; }
        .table-custom td { padding: 12px 15px; vertical-align: middle; border-bottom: 1px solid #f1f1f1; }
        .table-custom tr:last-child td { border-bottom: none; }
        
        /* SERVICE CARD (THẺ DỊCH VỤ) */
        .service-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            height: 100%;
        }
        .service-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-color: #a0c3ff;
        }
        /* Khi được chọn */
        .service-card.selected {
            border-color: #0d6efd;
            background-color: #f0f7ff;
        }
        .service-card.selected .icon-box {
            background-color: #0d6efd !important;
            color: #fff !important;
        }

        /* Icon trong thẻ */
        .icon-box {
            width: 45px; height: 45px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            transition: all 0.2s;
        }
        .service-checkbox {
            position: absolute; top: 15px; right: 15px;
            transform: scale(1.3); accent-color: #0d6efd;
        }

        /* Action Icon Small */
        .btn-icon-del {
            width: 32px; height: 32px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 6px; background: #fee2e2; color: #dc3545;
            transition: 0.2s;
        }
        .btn-icon-del:hover { background: #dc3545; color: #fff; }
    </style>
</head>
<body class="bg-light">

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-1 fw-bold text-dark">Quản Lý Dịch Vụ Tour</h3>
                    <p class="text-muted mb-0">Cấu hình dịch vụ đi kèm cho Tour #<?= $tour_id_safe ?></p>
                </div>
                <div>
                    </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-check-circle-fill me-2"></i>Dịch vụ đang sử dụng</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($dichVuDaGan)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-box-seam display-6 opacity-25"></i>
                            <p class="mt-2 mb-0">Chưa có dịch vụ nào. Hãy chọn bên dưới.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Tên Dịch Vụ</th>
                                        <th style="width: 150px;">Đơn Giá</th>
                                        <th>Nhà Cung Cấp</th>
                                        <th>Ghi Chú Riêng</th>
                                        <th style="width: 80px;" class="text-center">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dichVuDaGan as $index => $dv): ?>
                                        <tr>
                                            <td class="fw-bold text-secondary"><?= $index + 1 ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-box me-3 bg-light text-secondary" style="width: 35px; height: 35px; font-size: 1rem;">
                                                        <i class="bi <?= getServiceIcon($dv['loai_dich_vu'] ?? '') ?>"></i>
                                                    </div>
                                                    <span class="fw-bold text-dark"><?= htmlspecialchars($dv['ten_dich_vu']) ?></span>
                                                </div>
                                            </td>
                                            <td class="text-success fw-bold"><?= number_format($dv['gia_mac_dinh']) ?> ₫</td>
                                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($dv['nha_cung_cap'] ?? 'N/A') ?></span></td>
                                            <td class="text-muted small fst-italic"><?= htmlspecialchars($dv['ghi_chu'] ?? '-') ?></td>
                                            <td class="text-center">
                                                <a href="<?= BASEURL ?>?act=XoaGanDichVuTour&dich_vu_id=<?= $dv['gia_dv_id'] ?>&tour_id=<?= $tour_id_safe ?>"
                                                   onclick="return confirm('Gỡ dịch vụ này khỏi tour?')"
                                                   class="btn-icon-del" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-success"><i class="bi bi-plus-circle-fill me-2"></i>Thêm Dịch Vụ Mới</h6>
                    <small class="text-muted">Chọn các dịch vụ bên dưới để thêm vào tour</small>
                </div>
                <div class="card-body bg-light">
                    
                    <form action="<?= BASEURL ?>?act=luuGanDichVuTour&tour_id=<?= $tour_id_safe ?>" method="POST">
                        <input type="hidden" name="tour_id" value="<?= $tour_id_safe ?>">

                        <div class="row g-3">
                            <?php 
                            $has_services = false;
                            if (!empty($data)) {
                                foreach ($data as $dv) {
                                    // Bỏ qua dịch vụ đã gán
                                    if (in_array($dv['dich_vu_id'], $assigned_ids)) continue;
                                    $has_services = true;
                                    
                                    // Màu icon ngẫu nhiên cho đẹp
                                    $bg_colors = ['#e7f1ff', '#d1e7dd', '#fff3cd', '#f8d7da', '#e2e3e5'];
                                    $text_colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#41464b'];
                                    $rand = array_rand($bg_colors);
                                    $bg = $bg_colors[$rand];
                                    $txt = $text_colors[$rand];
                            ?>
                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <label class="service-card shadow-sm h-100 d-flex flex-column" for="dv_<?= $dv['dich_vu_id'] ?>">
                                        <input class="form-check-input service-checkbox" type="checkbox" 
                                               name="dich_vu_id[]" value="<?= $dv['dich_vu_id'] ?>" id="dv_<?= $dv['dich_vu_id'] ?>">

                                        <div class="d-flex align-items-start mb-3">
                                            <div class="icon-box me-3" style="background-color: <?= $bg ?>; color: <?= $txt ?>;">
                                                <i class="bi <?= getServiceIcon($dv['loai_dich_vu']) ?>"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($dv['ten_dich_vu']) ?></h6>
                                                <div class="text-success fw-bold small"><?= number_format($dv['gia_mac_dinh']) ?> ₫</div>
                                            </div>
                                        </div>
                                        
                                        <div class="small text-muted mb-3 flex-grow-1">
                                            <i class="bi bi-shop me-1"></i> <?= htmlspecialchars($dv['ten_ncc'] ?? 'NCC không tên') ?>
                                        </div>

                                        <input type="text" name="ghi_chu[<?= $dv['dich_vu_id'] ?>]" 
                                               class="form-control form-control-sm bg-light border-0" 
                                               placeholder="Ghi chú (VD: Cần đặt trước)..."
                                               onclick="event.stopPropagation()"> </label>
                                </div>
                            <?php 
                                } 
                            }
                            ?>
                            
                            <?php if (!$has_services): ?>
                                <div class="col-12 text-center py-5">
                                    <p class="text-success fw-bold mb-0"><i class="bi bi-check-all display-4"></i></p>
                                    <p class="text-muted">Tất cả dịch vụ khả dụng đã được gán cho tour này!</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($has_services): ?>
                            <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                                <button type="submit" class="btn btn-success px-5 py-2 fw-bold shadow-sm">
                                    <i class="bi bi-plus-lg me-2"></i> Lưu Các Dịch Vụ Đã Chọn
                                </button>
                            </div>
                        <?php endif; ?>

                    </form>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                <a href="<?= BASEURL ?>?act=chinhsach&tour_id=<?= $tour_id_safe ?>" class="btn btn-nav btn-nav-back">
                    <i class="bi bi-chevron-left"></i> Quay lại Chính sách
                </a>

                <div class="d-flex gap-2">
                    <a href="<?= BASEURL ?>?act=chitiettour&tour_id=<?= $tour_id_safe ?>" class="btn btn-nav btn-nav-view">
                        <i class="bi bi-eye"></i> Xem Chi Tiết
                    </a>
                    
                    <a href="<?= BASEURL ?>?act=tour_list" class="btn btn-nav btn-nav-finish shadow-sm">
                        <i class="bi bi-check-circle-fill"></i> HOÀN TẤT
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
    <script>
        // Script đổi màu Card khi chọn Checkbox
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.service-card');
            
            cards.forEach(card => {
                const checkbox = card.querySelector('input[type="checkbox"]');
                
                // Hàm cập nhật trạng thái
                const updateState = () => {
                    if(checkbox.checked) card.classList.add('selected');
                    else card.classList.remove('selected');
                };

                // Lắng nghe sự kiện change của checkbox
                checkbox.addEventListener('change', updateState);

                // Click vào card thì toggle checkbox (trừ khi click vào input ghi chú)
                card.addEventListener('click', (e) => {
                    if (e.target.tagName !== 'INPUT') {
                        checkbox.checked = !checkbox.checked;
                        updateState(); // Gọi cập nhật thủ công vì thay đổi qua JS không kích hoạt event 'change'
                    }
                });
            });
        });
    </script>
</body>
</html>