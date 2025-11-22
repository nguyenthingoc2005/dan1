<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm dịch vụ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">

    <div class="container mt-4">

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Thêm Dịch Vụ</h4>
            </div>

            <div class="card-body p-4">

                <form action="<?= BASEURL ?>?act=them_dich_vu" method="POST">

                    <div class="mb-3">
                        <label class="form-label">Loại dịch vụ</label>
                        <input type="text" class="form-control" name="loai_dich_vu" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mã dịch vụ</label>
                        <input type="text" class="form-control" name="ma" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="mo_ta" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giá mặc định</label>
                            <input type="number" class="form-control" name="gia_mac_dinh" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Đơn vị</label>
                            <input type="text" class="form-control" name="don_vi" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nhà cung cấp</label>
                        <select name="ncc_id" class="form-select" required>

                            <?php foreach ($nccList as $ncc): ?>
                                <option value="<?= $ncc['ncc_id'] ?>">
                                    <?= $ncc['ten'] ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= BASEURL ?>?act=lay_dich_vu" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>

                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Lưu dịch vụ
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>

</body>

</html>