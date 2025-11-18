<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">
    <h2 class="mb-4">Danh sách HDV</h2>

    <?php if (!empty($hdvList)): ?>
        <table class="table table-bordered table-striped table-hover align-middle text-center">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Tên HDV</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Kinh nghiệm</th>
                    <th>Ngôn ngữ</th>
                    <th>Ngày tạo</th>
                    <th>Hoạt động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hdvList as $hdv): ?>
                    <tr>
                        <td><?= $hdv['hdv_id'] ?></td>
                        <td><?= $hdv['ho_ten'] ?></td>
                        <td><?= $hdv['so_dien_thoai'] ?></td>
                        <td><?= $hdv['email'] ?></td>
                        <td><?= $hdv['kinh_nghiem'] ?></td>
                        <td><?= $hdv['ngon_ngu'] ?> </td>
                        <td><?= $hdv['ngay_tao'] ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>?act=edithdv&id=<?= $hdv['hdv_id'] ?> ?>" class="btn btn-sm btn-warning">Sửa</a>
                            <a href="<?= BASEURL ?>?act=deletehdv&id=<?= $hdv['hdv_id'] ?>"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa tour này không?')"
                                class="btn btn-sm btn-danger">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="<?= BASEURL ?>?act=addhdv" class="btn btn-success mt-3">Thêm</a>
    <?php else: ?>
        <div class="alert alert-info">Không có HDV.</div>
        <a href="<?= BASEURL ?>?act=addtour" class="btn btn-success">Thêm HDV đầu tiên</a>
    <?php endif; ?>
</div>