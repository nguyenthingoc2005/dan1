<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Danh sách Dịch vụ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">

    <div class="container-fluid mt-4">
        <div class="row">

            <!-- Sidebar trái -->
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white text-center">
                        <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Danh mục quản lý</h6>
                    </div>
                    <div class="card-body px-3 py-4">
                        <div class="d-grid gap-3">
                            <a href="<?= BASEURL ?>?act=ncc_list" class="btn btn-outline-primary">
                                <i class="bi bi-building"></i> Nhà cung cấp
                            </a>
                            <a href="<?= BASEURL ?>?act=dichvulist" class="btn btn-outline-primary">
                                <i class="bi bi-gear"></i> Dịch vụ
                            </a>
                            <a href="<?= BASEURL ?>?act=admin" class="btn btn-outline-primary">
                                <i class="bi bi-map"></i> Tour
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nội dung chính -->
            <div class="col-md-9">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="mb-0">📋 Danh sách Dịch vụ</h2>
                    <a href="<?= BASEURL ?>?act=them_dich_vu" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Thêm dịch vụ
                    </a>
                </div>

                <?php if (!empty($dichVuList)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle text-center bg-white shadow-sm">
                            <thead class="table-primary">
                                <tr>
                                    <th>ID</th>
                                    <th>Loại dịch vụ</th>
                                    <th>Mã</th>
                                    <th>Mô tả</th>
                                    <th>Giá mặc định</th>
                                    <th>Đơn vị</th>
                                    <th>Nhà cung cấp</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dichVuList as $dv): ?>
                                    <tr>
                                        <td><?= $dv['dich_vu_id'] ?></td>
                                        <td><?= $dv['loai_dich_vu'] ?></td>
                                        <td><?= $dv['ma'] ?></td>
                                        <td><?= $dv['mo_ta'] ?></td>
                                        <td><?= number_format($dv['gia_mac_dinh']) ?> VND</td>
                                        <td><?= $dv['don_vi'] ?></td>
                                        <td><?= $dv['ten_ncc'] ?></td>

                                        <td>
                                            <div class="d-grid gap-2">
                                                <a href="<?= BASEURL ?>?act=capnhat_dich_vu&id=<?= $dv['dich_vu_id'] ?>"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="bi bi-pencil-square"></i> Sửa
                                                </a>

                                                <a href="<?= BASEURL ?>?act=xoa_dich_vu&id=<?= $dv['dich_vu_id'] ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa dịch vụ này không?')">
                                                    <i class="bi bi-trash"></i> Xóa
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <?php else: ?>
                    <div class="alert alert-info">Không có dịch vụ nào.</div>
                    <a href="<?= BASEURL ?>?act=adddichvu" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Thêm dịch vụ đầu tiên
                    </a>
                <?php endif; ?>

            </div>
        </div>
    </div>

</body>

</html>