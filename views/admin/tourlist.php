<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Danh sách Tour</title>
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
        <h2 class="mb-0">Danh sách Tour</h2>
        <a href="<?= BASEURL ?>?act=addtour" class="btn btn-success">
          <i class="bi bi-plus-circle"></i> Thêm tour mới
        </a>
      </div>

      <?php if (!empty($data1)): ?>
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle text-center bg-white shadow-sm">
            <thead class="table-primary">
              <tr>
                <th>ID</th>
                <th>Tên tour</th>
                <th>Danh mục</th>
                <th>Giá cơ bản</th>
                <th>Mô tả ngắn</th>
                <th>Mô tả</th>
                <th>Thời lượng</th>
                <th>Điểm khởi hành</th>
                <th>Hoạt động</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
                <th>Chi tiết</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($data1 as $tour): ?>
                <tr>
                  <td><?= $tour['tour_id'] ?></td>
                  <td><?= $tour['ten'] ?></td>
                  <td><?= $tour['ten_danh_muc'] ?></td>
                  <td><?= number_format($tour['gia_co_ban']) ?> VND</td>
                  <td><?= $tour['mo_ta_ngan'] ?></td>
                  <td><?= $tour['mo_ta'] ?></td>
                  <td><?= $tour['thoi_luong_mac_dinh'] ?> ngày</td>
                  <td><?= $tour['diem_khoi_hanh'] ?></td>
                  <td>
                    <span class="badge <?= $tour['hoat_dong'] ? 'bg-success' : 'bg-secondary' ?>">
                      <?= $tour['hoat_dong'] ? 'Có' : 'Không' ?>
                    </span>
                  </td>
                  <td><?= $tour['ngay_tao'] ?></td>
                  <td>
                    <div class="d-grid gap-2">
                      <a href="<?= BASEURL ?>?act=uppdatetour&tour_id=<?= $tour['tour_id'] ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil-square"></i> Sửa
                      </a>
                      <a href="<?= BASEURL ?>?act=deletetour&tour_id=<?= $tour['tour_id'] ?>"
                         onclick="return confirm('Bạn có chắc chắn muốn xóa tour này không?')"
                         class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i> Xóa
                      </a>
                    </div>
                  </td>
                  <td>
                    <div class="d-grid gap-2">
                      <a href="<?= BASEURL ?>?act=diadiem&tour_id=<?= $tour['tour_id'] ?>" class="btn btn-info btn-sm">
                        <i class="bi bi-geo-alt"></i> Địa điểm
                      </a>
                      <a href="<?= BASEURL ?>?act=chinhsach&tour_id=<?= $tour['tour_id'] ?>" class="btn btn-secondary btn-sm">
                        <i class="bi bi-file-earmark-text"></i> Chính sách
                      </a>
                      <a href="<?= BASEURL ?>?act=listlichtrinhtour&tour_id=<?= $tour['tour_id'] ?>" class="btn btn-sm" style="background-color: #ff69b4; color: white;">
                        <i class="bi bi-calendar-week"></i> Lịch trình
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-info">Không có tour nào.</div>
        <a href="<?= BASEURL ?>?act=addtour" class="btn btn-success">
          <i class="bi bi-plus-circle"></i> Thêm tour đầu tiên
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>

</body>
</html>