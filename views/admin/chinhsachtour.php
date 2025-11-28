<?php
// PHP BLOCK
$tour_id_safe = htmlspecialchars($tour_id ?? 2);
$assigned_ids = array_column($chinhSachList ?? [], 'chinh_sach_id');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Chính Sách Tour</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        /* BASE STYLE */
        body { background-color: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .main-content { padding: 30px; margin-top: 70px; min-height: 100vh; }
        
        /* CARD STYLE */
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }

        /* POLICY CARD (CHECKBOX) */
        .policy-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .policy-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-color: #a0c3ff;
        }
        .policy-card.selected {
            border-color: #0d6efd;
            background-color: #f0f7ff;
        }
        
        .policy-checkbox {
            position: absolute; top: 15px; right: 15px;
            transform: scale(1.3); accent-color: #0d6efd;
        }

        /* Input Ghi chú riêng */
        .note-input {
            border: 1px solid #e0e0e0;
            background-color: #fff;
            font-size: 0.85rem;
            border-radius: 6px;
            padding: 6px 10px;
            margin-top: auto;
        }

        /* TABLE STYLE */
        .table-custom th { background-color: #f8f9fa; color: #6c757d; font-size: 0.8rem; text-transform: uppercase; padding: 12px 15px; border-bottom: 1px solid #eee; }
        .table-custom td { padding: 12px 15px; vertical-align: middle; border-bottom: 1px solid #f1f1f1; }

        /* NÚT BẤM (ĐƠN GIẢN, GỌN) */
        .btn-nav {
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex; align-items: center; gap: 8px;
        }
        
        /* Nút phụ (Trắng/Xám) */
        .btn-outline-custom { background: #fff; border: 1px solid #dee2e6; color: #495057; }
        .btn-outline-custom:hover { background: #f8f9fa; color: #000; }

        /* Nút Xem chi tiết (Xanh nhạt) */
        .btn-info-soft { background: #e0faff; color: #0dcaf0; border: none; }
        .btn-info-soft:hover { background: #0dcaf0; color: #fff; }

        /* Nút Chính (Xanh lá/Xanh dương) */
        .btn-primary-custom { background: #0d6efd; color: #fff; border: none; }
        .btn-primary-custom:hover { background: #0b5ed7; }
        
        .btn-success-custom { background: #198754; color: #fff; border: none; }
        .btn-success-custom:hover { background: #146c43; }

        .btn-action-icon { border: none; background: #fee2e2; color: #dc3545; padding: 5px 8px; border-radius: 6px; }
        .btn-action-icon:hover { background: #dc3545; color: white; }
    </style>
</head>
<body class="bg-light">

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Quản Lý Chính Sách</h3>
                    <p class="text-muted mb-0">Thiết lập các quy định và chính sách cho Tour #<?= $tour_id_safe ?></p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-check-circle-fill me-2"></i>Chính sách hiện tại</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($chinhSachList)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-clipboard-x display-6 opacity-25"></i>
                            <p class="mt-2 mb-0">Chưa có chính sách nào.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th style="width: 25%;">Tên chính sách</th>
                                        <th style="width: 15%;">Loại</th>
                                        <th>Ghi chú riêng</th>
                                        <th style="width: 80px;" class="text-center">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($chinhSachList as $index => $cs): ?>
                                    <tr>
                                        <td class="text-center fw-bold text-secondary"><?= $index + 1 ?></td>
                                        <td><strong class="text-dark"><?= htmlspecialchars($cs['ten']) ?></strong></td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($cs['loai']) ?></span></td>
                                        <td class="text-muted small fst-italic"><?= htmlspecialchars($cs['ghi_chu'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <a href="?act=xoa_chinh_sach_tour&id=<?= $cs['tour_chinh_sach_id'] ?>&tour_id=<?= $tour_id_safe ?>" 
                                               class="btn-action-icon" onclick="return confirm('Gỡ chính sách này?')">
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

            <div class="card mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-success"><i class="bi bi-plus-circle-fill me-2"></i>Thêm Chính Sách Mới</h6>
                </div>
                <div class="card-body bg-light">
                    <form action="<?= BASEURL ?>?act=luu_chinh_sach_tour&tour_id=<?= $tour_id_safe ?>" method="POST">
                        <input type="hidden" name="tour_id" value="<?= $tour_id_safe ?>">

                        <div class="row g-3">
                            <?php 
                            $has_policies = false;
                            if (!empty($danhsachchinhsach)) {
                                foreach($danhsachchinhsach as $cs) {
                                    if (in_array($cs['chinh_sach_id'], $assigned_ids)) continue;
                                    $has_policies = true;
                            ?>
                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <label class="policy-card shadow-sm h-100" for="cs_<?= $cs['chinh_sach_id'] ?>">
                                        
                                        <input class="policy-checkbox" type="checkbox" 
                                               name="chinh_sach_ids[]" 
                                               value="<?= $cs['chinh_sach_id'] ?>" 
                                               id="cs_<?= $cs['chinh_sach_id'] ?>">
                                    
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-2 text-primary fs-4"><i class="bi bi-file-earmark-text"></i></div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($cs['ten']) ?></div>
                                                <span class="badge bg-light text-secondary border small"><?= htmlspecialchars($cs['loai']) ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="text-muted small mb-3 text-truncate">
                                            <?= htmlspecialchars($cs['noi_dung'] ?? '') ?>
                                        </div>

                                        <input type="text" 
                                               name="ghi_chu[<?= $cs['chinh_sach_id'] ?>]" 
                                               class="form-control note-input" 
                                               placeholder="Ghi chú riêng (nếu có)..."
                                               onclick="event.stopPropagation()">
                                    </label>
                                </div>
                            <?php 
                                }
                            }
                            ?>

                            <?php if (!$has_policies): ?>
                                <div class="col-12 text-center py-3">
                                    <p class="text-success fw-bold mb-0">Tất cả chính sách đã được áp dụng!</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($has_policies): ?>
                            <div class="d-flex justify-content-end mt-4 border-top pt-3">
                                <button type="submit" class="btn btn-nav btn-success-custom shadow-sm">
                                    <i class="bi bi-plus-lg"></i> Lưu Các Chính Sách Đã Chọn
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-2 pb-5">
                <a href="<?= BASEURL ?>?act=addlichtrinh&tour_id=<?= $tour_id_safe ?>" class="btn btn-nav btn-outline-custom">
                    <i class="bi bi-arrow-left"></i> Quay lại Lịch Trình
                </a>

                <div class="d-flex gap-2">
                    <a href="<?= BASEURL ?>?act=chitiettour&tour_id=<?= $tour_id_safe ?>" class="btn btn-nav btn-info-soft">
                        <i class="bi bi-eye"></i> Xem Chi Tiết
                    </a>
                    
                    <a href="<?= BASEURL ?>?act=gandichvu&tour_id=<?= $tour_id_safe ?>" class="btn btn-nav btn-primary-custom shadow-sm">
                        Tiếp theo: Gắn Dịch Vụ <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.policy-card');
            cards.forEach(card => {
                const checkbox = card.querySelector('input[type="checkbox"]');
                
                // Hàm cập nhật style
                const updateState = () => {
                    if(checkbox.checked) card.classList.add('selected');
                    else card.classList.remove('selected');
                };
                checkbox.addEventListener('change', updateState);
                
                // Click card -> toggle checkbox
                card.addEventListener('click', (e) => {
                    // Nếu click vào input text thì không toggle checkbox
                    if (e.target.tagName !== 'INPUT') {
                        checkbox.checked = !checkbox.checked;
                        updateState();
                     }
                });
            });
        });
    </script>
</body>
</html>