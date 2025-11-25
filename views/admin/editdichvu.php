<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa Dịch Vụ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">

    <div class="container mt-4">

        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark text-center">
                <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Sửa Dịch Vụ</h4>
            </div>

            <div class="card-body p-4">

                <form action="<?= BASEURL ?>?act=capnhat_dich_vu&id=<?= $dichVu['dich_vu_id'] ?>" method="POST">

                    <!-- Hidden ID -->
                    <input type="hidden" name="dich_vu_id" value="<?= $dichVu['dich_vu_id'] ?>">

                    <div class="mb-3">
                        <label class="form-label">Loại dịch vụ</label>
                        <input type="text" class="form-control" name="loai_dich_vu"
                            value="<?= $dichVu['loai_dich_vu'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mã dịch vụ</label>
                        <input type="text" class="form-control" name="ma"
                            value="<?= $dichVu['ma'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="mo_ta" rows="3"><?= $dichVu['mo_ta'] ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giá mặc định</label>
                            <input type="number" class="form-control" name="gia_mac_dinh"
                                value="<?= $dichVu['gia_mac_dinh'] ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Đơn vị</label>
                            <input type="text" class="form-control" name="don_vi"
                                value="<?= $dichVu['don_vi'] ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nhà cung cấp</label>
                        <select name="ncc_id" class="form-select" required>
                            <option value="">-- Chọn nhà cung cấp --</option>

                            <?php foreach ($nccList as $ncc): ?>
                                <option value="<?= $ncc['ncc_id'] ?>"
                                    <?= $ncc['ncc_id'] == $dichVu['ncc_id'] ? 'selected' : '' ?>>
                                    <?= $ncc['ten'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= BASEURL ?>?act=lay_dich_vu" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>

                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save"></i> Cập nhật dịch vụ
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>

</body>

</html>