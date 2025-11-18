<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <title>Danh Sách Lịch Trình Tour</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<a href="<?= BASEURL ?>?act=addlichtrinh&tour_id=<?= $_GET['tour_id'] ?>" class="btn btn-success mt-3">Thêm lịch tour</a>


<body>
  <div class="container mt-5">
    <h2 class="mb-4">📋 Danh Sách Lịch Trình Tour</h2>

    <table class="table table-bordered table-hover">
      <thead class="table-primary">
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
              <td><?= htmlspecialchars($lichTrinh['ngay_thu']) ?></td>
              <td><?= htmlspecialchars($lichTrinh['tieu_de']) ?></td>
              <td><?= htmlspecialchars($lichTrinh['noi_dung']) ?></td>
              <td>

                <!-- Nút sửa -->
                <a
                  href="<?= BASEURL ?>?act=editlichtrinh&lich_trinh_id=<?= $lichTrinh['lich_trinh_id'] ?>&tour_id=<?= $lichTrinh['tour_id'] ?>"
                  class="btn btn-sm btn-warning">
                  Sửa
                </a>

                <!-- Nút xóa -->
                <a
                  href="<?= BASEURL ?>?act=deletelichtrinh&lich_trinh_id=<?= $lichTrinh['lich_trinh_id'] ?>&tour_id=<?= $lichTrinh['tour_id'] ?>"
                  class="btn btn-sm btn-danger"
                  onclick="return confirm('Bạn có chắc muốn xoá lịch trình này?')">
                  Xoá
                </a>

              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center">Chưa có lịch trình tour nào</td>
          </tr>
        <?php endif; ?>

      </tbody>
    </table>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>