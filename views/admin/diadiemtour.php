<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Địa điểm của Tour</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container-fluid mt-4">
  <div class="row">
    <!-- Sidebar trái -->
    <div class="col-md-3 mb-4">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white text-center">
          <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Danh mục quản lý</h6>
        </div>
        <div class="card-body px-3 py-4">
          <div class="d-grid gap-3">
            <a href="<?= BASEURL ?>?act=ncc_list" class="btn btn-outline-primary">
              <i class="bi bi-building"></i> Nhà cung cấp
            </a>
            <a href="<?= BASEURL ?>?act=hdv" class="btn btn-outline-primary">
              <i class="bi bi-person-badge"></i> Hướng dẫn viên
            </a>
            <a href="<?= BASEURL ?>?act=admin" class="btn btn-outline-primary">
              <i class="bi bi-map"></i> Tour
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Nội dung chính -->
    <div class="col-md-9">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">📍 Địa điểm của Tour #<?= htmlspecialchars($tour_id) ?></h3>
        <a href="<?= BASEURL ?>?act=gan_diadiem&tour_id=<?= $tour_id ?>" class="btn btn-primary">
          <i class="bi bi-plus-circle"></i> Gắn địa điểm
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
                  <p class="card-text"><strong>🌍 Quốc gia:</strong> <?= $dd['quoc_gia'] ?? 'Không rõ' ?></p>
                  <p class="card-text"><?= $dd['mo_ta'] ?? 'Chưa có mô tả' ?></p>
                  <div class="d-flex justify-content-between mt-3">
                    <a href="<?= BASEURL ?>?act=sua_diadiemtour&dia_diem_tour_id=<?= $dd['dia_diem_tour_id'] ?>&tour_id=<?= $tour_id ?>" class="btn btn-warning btn-sm">
                      <i class="bi bi-pencil-square"></i> Sửa
                    </a>
                    <a href="<?= BASEURL ?>?act=xoa_diadiem&dia_diem_tour_id=<?= $dd['dia_diem_tour_id'] ?>&tour_id=<?= $tour_id ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa địa điểm này?')" class="btn btn-danger btn-sm">
                      <i class="bi bi-trash"></i> Xóa
                    </a>
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
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>