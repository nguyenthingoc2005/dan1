<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i>Gắn địa điểm cho Tour #<?= htmlspecialchars($tour_id) ?></h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASEURL ?>?act=luu_gan_diadiem&tour_id=<?= $tour_id ?>" method="POST">
                        <input type="hidden" name="tour_id" value="<?= $tour_id ?>">

                        <div class="mb-3">
                            <label for="dia_diem_id" class="form-label">Chọn địa điểm</label>
                            <select class="form-select" name="dia_diem_id" id="dia_diem_id" required>
                                <option value="" disabled selected>-- Chọn địa điểm --</option>
                                <?php foreach($data as $dd): ?>
                                    <option value="<?= $dd['dia_diem_id'] ?>">
                                        <?= $dd['ten'] ?> (<?= $dd['quoc_gia'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="ghi_chu" class="form-label">Ghi chú (tuỳ chọn)</label>
                            <textarea class="form-control" name="ghi_chu" id="ghi_chu" rows="2" placeholder="Ví dụ: điểm chính, điểm phụ..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-plus-circle me-1"></i> Gắn địa điểm
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>