<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Danh Sách Lịch Trình Tour</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <h2 class="mb-0">📋 Danh Sách Lịch Trình Tour</h2>
        <a href="<?= BASEURL ?>?act=addlichtrinh&tour_id=<?= $_GET['tour_id'] ?>" class="btn btn-success">
          <i class="bi bi-plus-circle"></i> Thêm lịch tour
        </a>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white shadow-sm">
          <thead class="table-primary text-center">
            <tr>
              <th>#</th>
              <th>Tên Tour</th>
              <th>Ngày Thứ</th>
              <th>Tiêu Đề</th>
              <th>Nội Dung</th>
              <th>Hành Động</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($data)): ?>
              <?php foreach ($data as $index => $lichTrinh): ?>
                <tr>
                  <td><?= $index + 1 ?></td>
                  <td><?= htmlspecialchars($lichTrinh['ten_tour']) ?></td>
                  <td>Ngày <?= htmlspecialchars($lichTrinh['ngay_thu']) ?></td>
                  <td><?= htmlspecialchars($lichTrinh['tieu_de']) ?></td>
                  <td><?= htmlspecialchars($lichTrinh['noi_dung']) ?></td>
                  <td class="text-center">
                    <div class="d-grid gap-2">
                      <a href="<?= BASEURL ?>?act=editlichtrinh&lich_trinh_id=<?= $lichTrinh['lich_trinh_id'] ?>&tour_id=<?= $lichTrinh['tour_id'] ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil-square"></i> Sửa
                      </a>
                      <a href="<?= BASEURL ?>?act=deletelichtrinh&lich_trinh_id=<?= $lichTrinh['lich_trinh_id'] ?>&tour_id=<?= $lichTrinh['tour_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xoá lịch trình này?')">
                        <i class="bi bi-trash"></i> Xoá
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center text-muted">Chưa có lịch trình tour nào</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>