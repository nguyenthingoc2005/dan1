<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Danh sách nhà cung cấp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container-fluid py-4">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 mb-4">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white text-center">
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

    <!-- Main content -->
    <div class="col-md-9">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Danh sách nhà cung cấp</h2>
        <a href="<?= BASEURL ?>?act=ncc_add" class="btn btn-success">
          <i class="bi bi-plus-circle"></i> Thêm mới
        </a>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white shadow-sm">
          <thead class="table-primary">
            <tr>
              <th>ID</th>
              <th>Tên</th>
              <th>Liên hệ</th>
              <th>Địa chỉ</th>
              <th>Mã số thuế</th>
              <th>Ngày tạo</th>
              <th width="150">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data as $row): ?>
              <tr>
                <td><?= $row['ncc_id'] ?></td>
                <td><?= $row['ten'] ?></td>
                <td><?= $row['lien_he'] ?></td>
                <td><?= $row['dia_chi'] ?></td>
                <td><?= $row['ma_so_thue'] ?></td>
                <td><?= $row['ngay_tao'] ?></td>
                <td>
                  <a href="<?= BASEURL ?>?act=ncc_update&id=<?= $row['ncc_id'] ?>" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil-square"></i> Sửa
                  </a>
                  <a onclick="return confirm('Xóa nhà cung cấp?')" href="<?= BASEURL ?>?act=ncc_delete&id=<?= $row['ncc_id'] ?>" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash"></i> Xóa
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</body>
</html>