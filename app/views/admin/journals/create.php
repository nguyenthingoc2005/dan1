<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Viết Nhật ký Tour</h1>
        <a href="?act=admin&module=journals" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="?act=admin&module=journals&action=store" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Chọn Tour đã hoàn thành <span class="text-danger">*</span></label>
                            <select name="schedule_id" class="form-select" required>
                                <option value="">-- Chọn Tour --</option>
                                <?php if (isset($schedules['data'])): ?>
                                    <?php foreach ($schedules['data'] as $schedule): ?>
                                        <option value="<?= $schedule['id'] ?>">
                                            <?= htmlspecialchars($schedule['tour_name']) ?> (KH:
                                            <?= date('d/m/Y', strtotime($schedule['start_date'])) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Nhập tiêu đề nhật ký..."
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nội dung</label>
                            <textarea name="content" class="form-control" rows="10"
                                placeholder="Chia sẻ câu chuyện về chuyến đi..."></textarea>
                        </div>

                        <!-- <div class="mb-3">
                            <label class="form-label">Hình ảnh</label>
                            <input type="file" name="images[]" class="form-control" multiple>
                        </div> -->

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Đăng nhật ký
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>