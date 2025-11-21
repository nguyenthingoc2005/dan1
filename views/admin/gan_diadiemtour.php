<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
         
<div class="card shadow border-0 mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="bi bi-pin-map-fill me-2"></i>
            Địa điểm đã gán cho Tour #<?= htmlspecialchars($tour_id) ?>
        </h5>
    </div>
    <div class="card-body">
        <?php 
        // Giả định biến $diaDiemDaGan chứa kết quả từ hàm getDiaDiemByTourId()
        if (empty($diaDiemDaGan)): 
        ?>
            <div class="alert alert-warning text-center">
                Tour này chưa có địa điểm nào được gán.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle bg-white shadow-sm">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 10%;">Ảnh</th>
                            <th style="width: 30%;">Địa điểm</th>
                            <th style="width: 20%;">Quốc gia</th>
                            <th style="width: 25%;">Mô tả ngắn</th>
                            <th style="width: 10%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($diaDiemDaGan as $index => $dd): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <?php if ($dd['hinh_anh']): ?>
                                    <img src="<?= htmlspecialchars($dd['hinh_anh']) ?>" 
                                         alt="<?= htmlspecialchars($dd['ten_diadiem']) ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                <?php else: ?>
                                    <i class="bi bi-image-fill text-muted" title="Không có ảnh"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($dd['ten_diadiem']) ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= htmlspecialchars($dd['quoc_gia']) ?></span>
                            </td>
                            <td><?= nl2br(substr(htmlspecialchars($dd['mo_ta']), 0, 100)) . (strlen($dd['mo_ta']) > 100 ? '...' : '') ?></td>
                            <td class="text-center">
                                 <a href="<?= BASEURL ?>?act=xoa_dia_diem&dia_diem_tour_id=<?= $dd['dia_diem_tour_id'] ?>&tour_id=<?= $tour_id ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa địa điểm này?')" class="btn btn-danger btn-sm">
                      <i class="bi bi-trash"></i> Xóa
                    </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>



            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Gắn địa điểm mới cho Tour #<?= htmlspecialchars($tour_id) ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASEURL ?>?act=luu_gan_diadiem&tour_id=<?= $tour_id ?>" method="POST">

                        <input type="hidden" name="tour_id" value="<?= $tour_id ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn địa điểm (nhiều)</label>

                            <div class="border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                                <?php if (empty($data)): ?>
                                    <p class="text-danger">Không có địa điểm nào để gán.</p>
                                <?php else: ?>
                                    <?php foreach($data as $dd): ?>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input"
                                                type="checkbox"
                                                name="dia_diem_id[]"
                                                value="<?= $dd['dia_diem_id'] ?>"
                                                id="dd_<?= $dd['dia_diem_id'] ?>">
                                            
                                            <label class="form-check-label" for="dd_<?= $dd['dia_diem_id'] ?>">
                                                <strong><?= $dd['ten'] ?></strong>
                                                <input type="number" name="thu_tu[<?= $dd['dia_diem_id'] ?>]" placeholder="Thứ tự" class="form-control-sm" style="width: 70px; margin-left: 15px;">
                                                <span class="text-muted small">(<?= $dd['quoc_gia'] ?>)</span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <small class="text-muted">
                                * Địa điểm đã gán sẽ không hiển thị lại trong danh sách này (Logic lọc phải nằm ở Controller/Model).
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="ghi_chu" class="form-label">Ghi chú chung (tuỳ chọn)</label>
                            <textarea class="form-control" name="ghi_chu" id="ghi_chu"
                                      rows="2" placeholder="Áp dụng ghi chú này cho tất cả địa điểm vừa chọn..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-plus-circle me-1"></i> Gắn địa điểm đã chọn
                        </button>
                        
                    </form>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-3 mt-4">
                <a href="<?= BASEURL ?>?act=chinhsach&tour_id=<?= htmlspecialchars($tour_id) ?>" 
                   class="btn btn-secondary btn-lg">
                    <i class="bi bi-list-ul me-2"></i> Tiếp theo (gắn chính sách tour)
                </a>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>