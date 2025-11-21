
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Danh sách Hướng dẫn viên</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
<link rel="stylesheet" href="./assets/css/sidebar.css">

  </head>
<body class="bg-light">

<div class="container-fluid mt-4">
  <?php include './views/parts/sidebar.php'; ?>

  <!-- Overlay -->
  <div class="overlay"></div>

  <div class="row">
    <!-- Nội dung chính toàn màn hình -->
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">📋 Danh sách Hướng dẫn viên</h2>
        <a href="<?= BASEURL ?>?act=addhdv" class="btn btn-success">
          <i class="bi bi-plus-circle"></i> Thêm HDV
        </a>
      </div>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">📋 Danh sách Hướng dẫn viên</h2>
        <a href="<?= BASEURL ?>?act=addhdv" class="btn btn-success">
          <i class="bi bi-plus-circle"></i> Thêm HDV
        </a>
      </div>

      <?php if (!empty($hdvList)): ?>
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle text-center bg-white shadow-sm">
            <thead class="table-primary">
              <tr>
                <th>ID</th>
                <th>Tên HDV</th>
                <th>Số điện thoại</th>
                <th>Email</th>
                <th>Kinh nghiệm</th>
                <th>Ngôn ngữ</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($hdvList as $hdv): ?>
                <tr>
                  <td><?= $hdv['hdv_id'] ?></td>
                  <td><?= $hdv['ho_ten'] ?></td>
                  <td><?= $hdv['so_dien_thoai'] ?></td>
                  <td><?= $hdv['email'] ?></td>
                  <td><?= $hdv['kinh_nghiem'] ?></td>
                  <td><?= $hdv['ngon_ngu'] ?></td>
                  <td><?= $hdv['ngay_tao'] ?></td>
                  <td>
                    <div class="d-grid gap-2">
                      <a href="<?= BASEURL ?>?act=edithdv&id=<?= $hdv['hdv_id'] ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil-square"></i> Sửa
                      </a>
                      <a href="<?= BASEURL ?>?act=deletehdv&id=<?= $hdv['hdv_id'] ?>" class="btn btn-danger btn-sm"
                         onclick="return confirm('Bạn có chắc chắn muốn xóa HDV này không?')">
                        <i class="bi bi-trash"></i> Xóa
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-info">Không có hướng dẫn viên nào.</div>
        <a href="<?= BASEURL ?>?act=addhdv" class="btn btn-success">
          <i class="bi bi-plus-circle"></i> Thêm HDV đầu tiên
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>
<script src="./assets/js/sidebar.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>