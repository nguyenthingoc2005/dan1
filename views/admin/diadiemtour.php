<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">
    <h3 class="mb-4">Địa điểm của Tour #<?= htmlspecialchars($tour_id) ?></h3>

    <div class="mb-3">
        <a href="<?= BASEURL ?>?act=gan_diadiem&tour_id=<?= $tour_id ?>" class="btn btn-primary">
            Gắn địa điểm cho tour <?= $tour_id ?>
        </a>
    </div>

    <?php if (!empty($diadiemList)): ?>
        <div class="row">
            <?php foreach($diadiemList as $dd): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= $dd['hinh_anh'] ?? 'default.jpg' ?>" class="card-img-top" alt="<?= $dd['ten_diadiem'] ?? 'Địa điểm' ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= $dd['ten_diadiem'] ?? 'Không rõ tên' ?></h5>
                            <p class="card-text"><strong>Quốc gia:</strong> <?= $dd['quoc_gia'] ?? 'Không rõ' ?></p>
                            <p class="card-text"><?= $dd['mo_ta'] ?? 'Chưa có mô tả' ?></p>
                            <div class="d-flex justify-content-between mt-3">
                                <a href="<?= BASEURL ?>?act=sua_diadiemtour&dia_diem_tour_id=<?= $dd['dia_diem_tour_id'] ?>&tour_id=<?= $tour_id ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <a href="<?= BASEURL ?>?act=xoa_diadiem&dia_diem_tour_id=<?= $dd['dia_diem_tour_id'] ?>&tour_id=<?= $tour_id ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa địa điểm này?')" class="btn btn-danger btn-sm">Xóa</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Tour này chưa có địa điểm nào.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>