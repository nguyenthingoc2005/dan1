<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tạo Lịch Trình Tour</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">

    <link rel="stylesheet" href="./assets/css/sidebar.css">

    <style>
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', sans-serif;
        }

        .main-content {
            padding: 30px;
            margin-top: 70px;
            transition: margin-left .32s ease;
            min-height: 100vh;
        }

        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            background: #fff;
        }

        label {
            font-weight: 600;
            color: #495057;
        }
    </style>
</head>

<body>

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">

            <!-- HEADER -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Tạo Lịch Trình Tour</h3>
                    <p class="text-muted mb-0">Thêm mới lịch khởi hành, giá và thời gian hiệu lực.</p>
                </div>
                <a href="index.php?act=dattourlist" class="btn btn-secondary px-4 shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại danh sách
                </a>
            </div>

            <!-- FORM CARD -->
            <div class="card card-custom">
                <div class="card-body p-4">
                    
                    <form action="<?= BASE_URL ?>?act=createschedule" method="POST" class="row g-4">
                        <input type="hidden" name="tour_id" value="<?= htmlspecialchars($tour_id) ?>">
                        <!-- Trạng thái -->
                        <div class="col-md-6">
                            <label>Trạng thái</label>
                            <select name="trang_thai" class="form-select">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>

                        <!-- Ngày bắt đầu -->
                        <div class="col-md-6">
                            <label>Ngày bắt đầu</label>
                            <input type="date" name="ngay_bat_dau" class="form-control" required>
                        </div>

                        <!-- Ngày kết thúc -->
                        <div class="col-md-6">
                            <label>Ngày kết thúc</label>
                            <input type="date" name="ngay_ket_thuc" class="form-control" required>
                        </div>
                        <!-- Hiệu lực từ -->
                        <div class="col-md-6">
                            <label>Hiệu lực từ</label>
                            <input type="date" name="hieu_luc_tu" class="form-control"  required>
                        </div>

                        <!-- Hiệu lực đến -->
                        <div class="col-md-6">
                            <label>Hiệu lực đến</label>
                            <input type="date" name="hieu_luc_den" class="form-control" required>
                        </div>

                        <!-- Ghi chú -->
                        <div class="col-12">
                            <label>Ghi chú</label>
                            <textarea name="ghi_chu" rows="3" class="form-control"></textarea>
                        </div>

                        <!-- BUTTON -->
                        <div class="col-12 mt-3">
                            <button class="btn btn-primary px-4 me-2">
                                <i class="bi bi-save2 me-2"></i> Lưu lịch trình
                            </button>
                            <a href="" class="btn btn-outline-secondary px-4">
                                Hủy
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>

</body>

</html>