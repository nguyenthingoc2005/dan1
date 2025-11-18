<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="container py-5">
    <div class="row justify-content-center">
        
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Sửa địa điểm tour</h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASEURL ?>?act=capnhat_diadiem_tour&dia_diem_tour_id=<?= $info['dia_diem_tour_id'] ?>" method="POST">
                        <input type="hidden" name="dia_diem_tour_id" value="<?= $info['dia_diem_tour_id'] ?>">
                        <input type="hidden" name="tour_id" value="<?= $info['tour_id'] ?>">

                        <select class="form-select" name="dia_diem_id" id="dia_diem_id" required>
    <option value="" disabled>-- Chọn địa điểm --</option>
    <?php foreach($data as $dd): ?>
        <option value="<?= $dd['dia_diem_id'] ?>"
            <?= ($dd['dia_diem_id'] == $info['dia_diem_id']) ? 'selected' : '' ?>>
            <?= $dd['ten'] ?> (<?= $dd['quoc_gia'] ?>)
        </option>
    <?php endforeach; ?>
</select>

                        <div class="mb-3">
                            <label for="ghi_chu" class="form-label">Ghi chú</label>
                            <textarea class="form-control" name="ghi_chu" id="ghi_chu" rows="3"><?= htmlspecialchars($info['ghi_chu']) ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">
                            <i class="bi bi-save me-1"></i> Cập nhật địa điểm
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>