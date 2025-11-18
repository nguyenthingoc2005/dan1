<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm Lịch Trình Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h2 class="mb-4">➕ Thêm Lịch Trình Tour</h2>

        <form action="<?= BASEURL ?>?act=createlichtrinh&tour_id=<?= $tour['tour_id'] ?>" method="POST">

            <!-- ID tour -->
            <input type="hidden" name="tour_id" value="<?= htmlspecialchars($tour['tour_id']) ?>">

            <div class="mb-3">
                <label class="form-label">Tên Tour</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($tour['ten']) ?>" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Ngày Thứ</label>
                <input type="number" name="ngay_thu" class="form-control" placeholder="VD: 1, 2, 3..." required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tiêu Đề</label>
                <input type="text" name="tieu_de" class="form-control" placeholder="Nhập tiêu đề lịch trình" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nội Dung</label>
                <textarea name="noi_dung" class="form-control" rows="5" placeholder="Nhập nội dung lịch trình" required></textarea>
            </div>

            <div class="mt-4">
                <button class="btn btn-primary">Lưu Lịch Trình</button>
                <a href="<?= BASEURL ?>?act=listlichtrinhtour&tour_id=<?= $tour['tour_id'] ?>" class="btn btn-secondary">Quay lại</a>
            </div>

        </form>
    </div>

</body>

</html>