<h2 class="mb-3">Danh sách nhà cung cấp</h2>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


<a href="<?= BASEURL ?>?act=ncc_add" class="btn btn-success mb-3">
    <i class="bi bi-plus-circle"></i> Thêm mới
</a>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle bg-white shadow-sm">

        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Liên hệ</th>
                <th>Địa chỉ</th>
                <th>Mã số thuế</th>
                <th>Ngày tạo</th>
                <th width="150">Hành động</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= $row['ncc_id'] ?></td>
                    <td><?= $row['ten'] ?></td>
                    <td><?= $row['lien_he'] ?></td>
                    <td><?= $row['dia_chi'] ?></td>
                    <td><?= $row['ma_so_thue'] ?></td>
                    <td><?= $row['ngay_tao'] ?></td>

                    <td>
                        <a href="<?= BASEURL ?>?act=ncc_update&id=<?= $row['ncc_id'] ?>" 
                           class="btn btn-warning btn-sm">
                           <i class="bi bi-pencil-square"></i> Sửa
                        </a>

                        <a onclick="return confirm('Xóa nhà cung cấp?')"
                           href="<?= BASEURL ?>?act=ncc_delete&id=<?= $row['ncc_id'] ?>"
                           class="btn btn-danger btn-sm">
                           <i class="bi bi-trash"></i> Xóa
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>
