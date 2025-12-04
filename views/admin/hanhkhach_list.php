<!-- Bootstrap 5 CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">

    <h2 class="mb-4 text-primary fw-bold">Danh sách hành khách</h2>

    <!-- Thanh tìm kiếm -->
    <form method="GET" action="" class="row mb-4">
        <input type="hidden" name="act" value="hk_list">

        <div class="col-md-4">
            <input type="text" name="keyword"
                   class="form-control"
                   placeholder="Tìm theo tên hoặc CCCD..."
                   value="<?= $_GET['keyword'] ?? '' ?>">
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Tìm kiếm
            </button>
        </div>
    </form>

    <!-- Bảng danh sách -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Đặt tour ID</th>
                    <th>Họ tên</th>
                    <th>CCCD</th>
                    <th>Ngày sinh</th>
                    <th>Số ghế</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($list as $hk): ?>
                <tr>
                    <td><?= $hk['hanh_khach_id'] ?></td>
                    <td><?= $hk['dat_tour_id'] ?></td>
                    <td><?= $hk['ho_ten'] ?></td>
                    <td><?= $hk['cccd'] ?></td>
                    <td><?= $hk['ngay_sinh'] ?></td>
                    <td><?= $hk['so_ghe'] ?></td>
                    <td><?= $hk['ghi_chu'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
