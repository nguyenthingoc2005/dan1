<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4" style="max-width: 700px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">Cập nhật Tour</h4>
        </div>

        <div class="card-body">
            <form action="<?= BASEURL ?>?act=uppdatetour1&tour_id=<?= $tour['tour_id'] ?>" method="POST">
                <input type="hidden" name="tour_id" value="<?= $tour['tour_id'] ?>">

                <div class="mb-3">
                    <label class="form-label">Tên tour</label>
                    <input type="text" class="form-control" name="ten" value="<?= $tour['ten'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Danh mục tour</label>
                    <select class="form-select" name="danh_muc_id" required>
                        <?php foreach($data as $cat): ?>
                            <option value="<?= $cat['danh_muc_id'] ?>" <?= $cat['danh_muc_id'] == $tour['danh_muc_id'] ? 'selected' : '' ?>>
                                <?= $cat['ten'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả ngắn</label>
                    <input type="text" class="form-control" name="mo_ta_ngan" value="<?= $tour['mo_ta_ngan'] ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea class="form-control" name="mo_ta" rows="4"><?= $tour['mo_ta'] ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Giá cơ bản</label>
                    <input type="number" step="0.01" class="form-control" name="gia_co_ban" value="<?= $tour['gia_co_ban'] ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Thời lượng mặc định (ngày)</label>
                    <input type="number" class="form-control" name="thoi_luong_mac_dinh" value="<?= $tour['thoi_luong_mac_dinh'] ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Điểm khởi hành</label>
                    <input type="text" class="form-control" name="diem_khoi_hanh" value="<?= $tour['diem_khoi_hanh'] ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Hoạt động</label>
                    <select class="form-select" name="hoat_dong">
                        <option value="1" <?= $tour['hoat_dong'] == 1 ? 'selected' : '' ?>>Đang hoạt động</option>
                        <option value="0" <?= $tour['hoat_dong'] == 0 ? 'selected' : '' ?>>Tạm dừng</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-warning w-100">Cập nhật tour</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>