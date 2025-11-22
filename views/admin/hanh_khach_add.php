<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Hành Khách cho Đơn #<?= htmlspecialchars($data['dat_tour_id']) ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        .container { 
            max-width: 900px; 
            margin-top: 30px; /* Tăng khoảng cách trên */
        }
        .passenger-card { 
            border-left: 5px solid #0d6efd; /* Màu xanh Primary */
            background-color: #f8f9fa; /* Nền xám nhạt cho từng hành khách */
        }
        .form-control-label {
            font-weight: 600; /* Nhấn mạnh label */
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-success text-white py-3">
            <h4 class="mb-0"><i class="bi bi-person-lines-fill"></i> Thêm Danh Sách Hành Khách</h4>
            <div class="small">
                Đơn ID: **#<?= htmlspecialchars($data['dat_tour_id']) ?>** | Tổng Số Người: **<?= htmlspecialchars($data['so_nguoi']) ?>**
            </div>
        </div>
        
        <div class="card-body p-4">
            
            <form action="<?= BASEURL ?>?act=hanh_khach_save&dat_tour_id=<?= $_GET['dat_tour_id'] ?>" method="POST">
                <input type="hidden" name="dat_tour_id" value="<?= htmlspecialchars($data['dat_tour_id']) ?>">
                
                <?php 
                // Vòng lặp tạo form dựa trên $data['so_nguoi']
                for ($i = 1; $i <= $data['so_nguoi']; $i++): 
                ?>
                    <div class="card passenger-card mb-4 border-radius-lg">
                        <div class="card-body p-3">
                            <h5 class="card-title text-primary mb-3">Hành Khách Số <?= $i ?></h5>
                            
                            <div class="row g-3">
                                
                                <div class="col-md-6">
                                    <label for="ho_ten_<?= $i ?>" class="form-label form-control-label required">Họ và Tên</label>
                                    <input type="text" class="form-control" name="hanh_khach[<?= $i ?>][ho_ten]" id="ho_ten_<?= $i ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="cccd_<?= $i ?>" class="form-label form-control-label">CCCD/Hộ Chiếu</label>
                                    <input type="text" class="form-control" name="hanh_khach[<?= $i ?>][cccd]" id="cccd_<?= $i ?>">
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="ngay_sinh_<?= $i ?>" class="form-label form-control-label">Ngày Sinh</label>
                                    <input type="date" class="form-control" name="hanh_khach[<?= $i ?>][ngay_sinh]" id="ngay_sinh_<?= $i ?>">
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="so_ghe_<?= $i ?>" class="form-label form-control-label">Số Ghế (Tùy chọn)</label>
                                    <input type="text" class="form-control" name="hanh_khach[<?= $i ?>][so_ghe]" id="so_ghe_<?= $i ?>">
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="ghi_chu_<?= $i ?>" class="form-label form-control-label">Ghi Chú</label>
                                    <input type="text" class="form-control" name="hanh_khach[<?= $i ?>][ghi_chu]" id="ghi_chu_<?= $i ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
                
                <div class="d-flex justify-content-end pt-3">
                    <a href="<?= BASEURL ?>?act=dat_tour_detail&dat_tour_id=<?= htmlspecialchars($data['dat_tour_id']) ?>" class="btn btn-secondary me-3">
                        <i class="bi bi-x-circle"></i> Bỏ qua (Hoàn thành sau)
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Lưu & Chuyển sang Đặt Cọc
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>