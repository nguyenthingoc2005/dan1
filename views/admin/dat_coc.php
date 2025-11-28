<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Đặt Cọc</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        /* MAIN LAYOUT */
        body { background-color: #f5f7fb; font-family: 'Segoe UI', sans-serif; }
        .main-content { padding: 30px; margin-top: 70px; margin-left: 0; min-height: 100vh; }
        
        /* CARD STYLE */
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            background: #fff;
        }

        /* INPUT STYLING */
        .form-label { font-weight: 600; color: #495057; font-size: 0.9rem; margin-bottom: 6px; }
        .form-control, .form-select {
            border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px; font-size: 0.95rem;
            transition: border-color 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
        
        /* INPUT GROUPS */
        .input-group-text {
            background-color: #f8f9fa; border: 1px solid #dee2e6; color: #6c757d; border-radius: 8px 0 0 8px;
        }
        
        /* INFO BOX (TỔNG TIỀN) */
        .info-box {
            background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
        }
        .info-label { font-size: 0.85rem; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-size: 1.75rem; font-weight: 700; }
    </style>
</head>
<body>

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Ghi Nhận Đặt Cọc</h3>
                    <p class="text-muted mb-0">Bước cuối: Xác nhận thanh toán để hoàn tất đơn hàng.</p>
                </div>
                <a href="<?= BASEURL ?>?act=dattourlist" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i> Về Danh sách
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    
                    <div class="info-box d-flex justify-content-between align-items-center">
                        <div>
                            <div class="info-label"><i class="bi bi-receipt me-1"></i> Mã Đơn Hàng</div>
                            <div class="fs-4 fw-bold">#<?= htmlspecialchars($data['dat_tour_id'] ?? '---') ?></div>
                        </div>
                        <div class="text-end border-start border-white border-opacity-25 ps-4">
                            <div class="info-label text-warning"><i class="bi bi-coin me-1"></i> Tổng Giá Trị Đơn (Dự kiến)</div>
                            <div class="info-value text-warning">
                                <?= isset($data['tong_tien']) ? number_format($data['tong_tien']) : '0' ?> 
                                <span class="fs-6 fw-normal">VNĐ</span>
                            </div>
                        </div>
                    </div>

                    <div class="card card-custom p-4">
                        <h5 class="mb-4 text-primary fw-bold border-bottom pb-2">
                            <i class="bi bi-credit-card-2-front me-2"></i> Thông tin giao dịch
                        </h5>

                        <form action="<?= BASEURL ?>?act=dat_coc_save&dat_tour_id=<?= htmlspecialchars($_GET['dat_tour_id'] ?? '') ?>" method="POST">
                            
                            <input type="hidden" name="dat_tour_id" value="<?= htmlspecialchars($_GET['dat_tour_id'] ?? '') ?>">
                            
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label">Số Tiền Đặt Cọc <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-white text-success border-end-0"><i class="bi bi-cash-stack fs-4"></i></span>
                                        <input type="number" step="1000" min="0" class="form-control border-start-0 fw-bold text-success fs-4" 
                                               name="so_tien" id="so_tien" required placeholder="Nhập số tiền...">
                                        <span class="input-group-text bg-light fw-bold">VNĐ</span>
                                    </div>
                                    <div class="form-text">Nhập số tiền khách hàng thực tế thanh toán/chuyển khoản.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Hình Thức Thanh Toán <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-bank"></i></span>
                                        <select class="form-select" name="hinh_thuc" id="hinh_thuc" required>
                                            <option value="" disabled selected>-- Chọn hình thức --</option>
                                            <option value="Chuyen Khoan">Chuyển Khoản Ngân Hàng</option>
                                            <option value="Tien Mat">Tiền Mặt</option>
                                            <option value="The">Thẻ (Visa/Master)</option>
                                            <option value="Vi Dien Tu">Ví Điện Tử (Momo/ZaloPay)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Ngày Giao Dịch <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                        <input type="datetime-local" class="form-control" name="ngay_dat" id="ngay_dat" value="<?= date('Y-m-d\TH:i') ?>" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Trạng Thái Giao Dịch</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lightning-charge"></i></span>
                                        <select class="form-select" name="trang_thai" id="trang_thai" required>
                                            <option value="pending">Chờ xác nhận (Pending)</option>
                                            <option value="confirmed" selected>Đã xác nhận (Thành công)</option>
                                            <option value="failed">Thất bại (Failed)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Ghi Chú Giao Dịch</label>
                                    <textarea class="form-control" name="ghi_chu" id="ghi_chu" rows="3" placeholder="Nhập mã giao dịch ngân hàng, nội dung chuyển khoản..."></textarea>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end align-items-center gap-3">
                                <a href="<?= BASEURL ?>?act=dat_tour_detail&dat_tour_id=<?= htmlspecialchars($_GET['dat_tour_id'] ?? '') ?>" class="btn btn-light border px-4 py-2">
                                    Bỏ qua bước này
                                </a>
                                
                                <button type="submit" class="btn btn-success btn-lg px-5 fw-bold shadow-sm">
                                    <i class="bi bi-check-circle-fill me-2"></i> Hoàn Tất Đơn Hàng
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>
</html>