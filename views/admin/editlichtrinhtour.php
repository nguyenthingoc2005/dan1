<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Sửa lịch trình tour</h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASEURL ?>?act=updatelichtrinh&lich_trinh_id=<?= $info['lich_trinh_id'] ?>" method="POST">
                        <input type="hidden" name="lich_trinh_id" value="<?= $info['lich_trinh_id'] ?>">
                        <input type="hidden" name="tour_id" value="<?= $info['tour_id'] ?>">

                        <div class="mb-3">
                            <label for="ngay_thu" class="form-label">Ngày thứ</label>
                            <input type="number" name="ngay_thu" id="ngay_thu" class="form-control" value="<?= $info['ngay_thu'] ?>" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label for="tieu_de" class="form-label">Tiêu đề</label>
                            <input type="text" name="tieu_de" id="tieu_de" class="form-control" value="<?= htmlspecialchars($info['tieu_de']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="noi_dung" class="form-label">Nội dung</label>
                            <textarea name="noi_dung" id="noi_dung" class="form-control" rows="5" required><?= htmlspecialchars($info['noi_dung']) ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">
                            <i class="bi bi-save me-1"></i> Cập nhật lịch trình
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>