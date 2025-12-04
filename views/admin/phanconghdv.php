<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Phân Công Hướng Dẫn Viên</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="./assets/css/sidebar.css">

    <style>
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', sans-serif;
        }

        .main-content {
            padding: 30px;
            margin-top: 70px;
            min-height: 100vh;
            transition: margin-left .32s ease;
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
                    <h3 class="fw-bold text-dark mb-1">Phân Công Hướng Dẫn Viên</h3>
                    <p class="text-muted mb-0">Gán hướng dẫn viên cho lịch khởi hành của tour.</p>
                </div>
                <a href="index.php?act=phanconghdvlist" class="btn btn-secondary px-4 shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại danh sách
                </a>
            </div>

            <!-- FORM -->
            <div class="card card-custom">
                <div class="card-body p-4">

                    <form action="<?= BASE_URL ?>?act=storephancong" method="POST" class="row g-4">
                        <input type="hidden" name="lich_id" value="<?= $lich['lich_id'] ?? '' ?>">
                        <!-- SELECT HDV -->
                        <div class="col-md-6">
                            <label>Hướng dẫn viên</label>
                            <select name="hdv_id" class="form-select" required>
                                <option value="">-- Chọn hướng dẫn viên --</option>
                                <?php foreach ($hdvList as $hdv): ?>
                                    <option value="<?= $hdv['hdv_id'] ?>">
                                        <?= $hdv['ho_ten'] ?> (ID: <?= $hdv['hdv_id'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- SELECT LỊCH KHỞI HÀNH -->
                        <div class="col-md-6">
                            <lab>Lịch khởi hành</lab>
                            <input type="text" class="form-control"
                                value=" Ngày khởi hành: <?= $lich['ngay_bat_dau'] ?> || Ngày kết thúc : <?= $lich['ngay_ket_thuc'] ?>  "
                                disabled>

                        </div>

                        <!-- NGÀY PHÂN CÔNG -->
                        <!-- tự động nhập ngày hiện tại -->
                        <div class="col-md-6">
                            <label>Ngày phân công</label>
                            <input type="date" name="ngay_phan_cong" class="form-control"
                                value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <!-- TRẠNG THÁI -->
                        <div class="col-md-6">
                            <label>Tình trạng hoạt động</label>
                            <select name="trang_thai" class="form-select">
                                <option value="1">Sẵn sàng</option>
                                <option value="0">Bận</option>
                                <option value="2">Nghỉ phép</option>
                            </select>
                        </div>

                        <!-- BUTTON -->
                        <div class="col-12 mt-3">
                            <button class="btn btn-primary px-4 me-2">
                                <i class="bi bi-save2 me-2"></i> Lưu phân công
                            </button>
                            <a href="" class="btn btn-outline-secondary px-4">Hủy</a>
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