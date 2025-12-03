<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết Nhật ký</h1>
        <a href="?act=admin&module=journals" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                    <div>
                        <h4 class="m-0 font-weight-bold text-primary"><?= htmlspecialchars($journal['title']) ?></h4>
                        <p class="text-muted mb-0 mt-1">
                            <i class="fas fa-user me-1"></i> <?= htmlspecialchars($journal['author_name']) ?>
                            &nbsp;|&nbsp;
                            <i class="fas fa-clock me-1"></i>
                            <?= date('d/m/Y H:i', strtotime($journal['created_at'])) ?>
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-info p-2">
                            <i class="fas fa-map-marker-alt me-1"></i> <?= htmlspecialchars($journal['tour_name']) ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="journal-content">
                        <?= nl2br($journal['content']) ?> <!-- Note: Should use HTML purifier if allowing HTML -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>