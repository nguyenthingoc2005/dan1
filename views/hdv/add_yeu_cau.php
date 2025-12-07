<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Yêu Cầu Đặc Biệt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Thêm Yêu Cầu Phục Vụ Mới</h3>
            </div>
            <div class="card-body">
                <form action="<?= BASEURL ?>?act=luu_yeu_cau" method="POST">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ID Đặt Tour:</label>
                            <input type="number" class="form-control" name="dat_tour_id" required placeholder="Nhập ID tour...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ID Hành Khách:</label>
                            <input type="number" class="form-control" name="hanh_khach_id" required placeholder="Nhập ID khách...">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nội dung yêu cầu:</label>
                        <textarea class="form-control" name="noi_dung" rows="3" required placeholder="Ví dụ: Khách ăn chay, cần xe lăn..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mức độ ưu tiên:</label>
                        <select class="form-select" name="muc_do_uu_tien">
                            <option value="Thấp">Thấp</option>
                            <option value="Trung bình" selected>Trung bình</option>
                            <option value="Cao">Cao</option>
                            <option value="Khẩn cấp">Khẩn cấp</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ghi chú thêm:</label>
                        <textarea class="form-control" name="ghi_chu" rows="2"></textarea>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="da_chuan_bi" name="da_chuan_bi" value="1">
                        <label class="form-check-label fw-bold text-success" for="da_chuan_bi">
                            Đã chuẩn bị xong? (Check nếu đã hoàn thành)
                        </label>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= BASEURL ?>?act=chitiet_khach_hang" class="btn btn-secondary">Quay lại danh sách</a>
                        <button type="submit" class="btn btn-primary">Lưu Yêu Cầu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>