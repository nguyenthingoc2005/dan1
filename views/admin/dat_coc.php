<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Đặt Cọc cho Đơn #<?= htmlspecialchars($data['dat_tour_id'] ?? 'MỚI') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .container { max-width: 700px; margin-top: 30px; }
        .required::after { content: " *"; color: red; }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="card shadow-lg border-0">
        
        <div class="card-header bg-primary text-white py-3">
            <h4 class="mb-0"><i class="bi bi-wallet2"></i> Thêm Thông Tin Đặt Cọc</h4>
            <div class="small">
                Đơn ID: **#<?= htmlspecialchars($data['dat_tour_id'] ?? 'N/A') ?>** <?php if (isset($data['tong_tien'])): ?>
                    | Tổng Tiền Đơn: **<?= number_format($data['tong_tien']) ?> <?= htmlspecialchars($data['tien_te'] ?? 'VND') ?>**
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card-body p-4">
            
            <form action="<?= BASEURL ?>?act=dat_coc_save&dat_tour_id=<?= $_GET['dat_tour_id'] ?? '' ?>" method="POST">
                
                <input type="hidden" name="dat_tour_id" value="<?= htmlspecialchars($_GET['dat_tour_id'] ?? '') ?>">
                
                <div class="row g-3">
                    
                    <div class="col-md-6">
                        <label for="so_tien" class="form-label required">Số Tiền Đặt Cọc</label>
                        <input type="number" step="0.01" min="1" class="form-control" name="so_tien" id="so_tien" required placeholder="Ví dụ: 500000">
                    </div>

                    <div class="col-md-6">
                        <label for="tien_te" class="form-label required">Tiền Tệ</label>
                        <select class="form-select" name="tien_te" id="tien_te" required>
                            <option value="VND" selected>VND (Việt Nam Đồng)</option>
                            <option value="USD">USD (Đô la Mỹ)</option>
                            <option value="EUR">EUR (Euro)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="hinh_thuc" class="form-label required">Hình Thức Thanh Toán</label>
                        <select class="form-select" name="hinh_thuc" id="hinh_thuc" required>
                            <option value="" disabled selected>-- Chọn hình thức --</option>
                            <option value="Chuyen Khoan">Chuyển Khoản Ngân Hàng</option>
                            <option value="Tien Mat">Tiền Mặt</option>
                            <option value="The">Thẻ (Visa/Master)</option>
                            <option value="Vi Dien Tu">Ví Điện Tử (Momo/ZaloPay)</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="trang_thai" class="form-label required">Trạng Thái Đặt Cọc</label>
                        <select class="form-select" name="trang_thai" id="trang_thai" required>
                            <option value="pending" selected>Chờ xác nhận</option>
                            <option value="confirmed">Đã xác nhận</option>
                            <option value="failed">Thất bại</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="ngay_dat" class="form-label">Ngày Đặt Cọc</label>
                        <input type="datetime-local" class="form-control" name="ngay_dat" id="ngay_dat" value="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                    
                    <div class="col-12">
                        <label for="ghi_chu" class="form-label">Ghi Chú Chi Tiết</label>
                        <textarea class="form-control" name="ghi_chu" id="ghi_chu" rows="3" placeholder="Nhập mã giao dịch, tên người chuyển, hoặc bất kỳ thông tin cần thiết nào..."></textarea>
                    </div>
                    
                </div>
                
                <div class="d-flex justify-content-end pt-4">
                    <a href="<?= BASEURL ?>?act=dat_tour_detail&dat_tour_id=<?= htmlspecialchars($data['dat_tour_id'] ?? '') ?>" class="btn btn-secondary me-3">
                        <i class="bi bi-arrow-left-circle"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Lưu Đặt Cọc
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>