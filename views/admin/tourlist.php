<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">
    <h2 class="mb-4">Danh sách Tour</h2>

    <?php if (!empty($data1)): ?>
        <table class="table table-bordered table-striped table-hover align-middle text-center">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Tên tour</th>
                    <th>Danh mục</th>
                    <th>Giá cơ bản</th>
                    <th>Mô tả ngắn</th>
                    <th>Mô tả</th>
                    <th>Thời lượng</th>
                    <th>Điểm khởi hành</th>
                    <th>Hoạt động</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data1 as $tour): ?>
                    <tr>
                        <td><?= $tour['tour_id'] ?></td>
                        <td><?= $tour['ten'] ?></td>
                        <td><?= $tour['ten_danh_muc'] ?></td>
                        <td><?= number_format($tour['gia_co_ban']) ?> VND</td>
                        <td><?= $tour['mo_ta_ngan'] ?></td>
                        <td><?= $tour['mo_ta'] ?></td>
                        <td><?= $tour['thoi_luong_mac_dinh'] ?> ngày</td>
                        <td><?= $tour['diem_khoi_hanh'] ?></td>
                        <td><?= $tour['hoat_dong'] ? 'Có' : 'Không' ?></td>
                        <td><?= $tour['ngay_tao'] ?></td>
                        <td>
                            <a href="<?= BASEURL ?>?act=uppdatetour&tour_id=<?= $tour['tour_id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
<a href="<?= BASEURL ?>?act=deletetour&tour_id=<?= $tour['tour_id'] ?>" 
   onclick="return confirm('Bạn có chắc chắn muốn xóa tour này không?')" 
   class="btn btn-sm btn-danger">Xóa</a>                        </td>
                    <td><div class="d-flex justify-content-between mt-3">
    <a href="<?= BASEURL ?>?act=diadiem&tour_id=<?= $tour['tour_id'] ?>" class="btn btn-info">
        Địa điểm tour
    </a>
    <a href="<?= BASEURL ?>?act=chinhsach&tour_id=<?= $tour['tour_id'] ?>" class="btn btn-secondary">
        Chính sách tour
    </a>
</div>
</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
                
        <a href="<?= BASEURL ?>?act=addtour" class="btn btn-success mt-3">Thêm tour mới</a>
    <?php else: ?>
        <div class="alert alert-info">Không có tour nào.</div>
        <a href="<?= BASEURL ?>?act=addtour" class="btn btn-success">Thêm tour đầu tiên</a>
    <?php endif; ?>
</div>