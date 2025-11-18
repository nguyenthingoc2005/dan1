<!-- BOOTSTRAP CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4" style="max-width: 700px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Cập Nhật Hướng Dẫn Viên</h4>
        </div>

        <div class="card-body">
            <form action="<?= BASEURL ?>?act=updatehdv&id=<?= $hdv['hdv_id']?> ?>" method="POST">

               

                <!-- ID user nếu cần -->
                <input type="hidden" name="nguoi_dung_id" value="<?= htmlspecialchars($hdv['nguoi_dung_id']) ?>">

                <div class="mb-3">
                    <label class="form-label">Tên HDV</label>
                    <input type="text" class="form-control" name="ho_ten" 
                           value="<?= htmlspecialchars($hdv['ho_ten']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" name="so_dien_thoai" 
                           value="<?= htmlspecialchars($hdv['so_dien_thoai']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" 
                           value="<?= htmlspecialchars($hdv['email']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kinh nghiệm</label>
                    <input type="text" class="form-control" name="kinh_nghiem" 
                           value="<?= htmlspecialchars($hdv['kinh_nghiem']) ?>" placeholder="Số năm hoặc mô tả">
                </div>

                <div class="mb-3">
                    <label class="form-label">Ngôn ngữ</label>
                    <input type="text" class="form-control" name="ngon_ngu" 
                           value="<?= htmlspecialchars($hdv['ngon_ngu']) ?>" placeholder="Ví dụ: Việt, Anh">
                </div>

                <button type="submit" class="btn btn-primary w-100">Cập Nhật HDV</button>
                <a href="<?= BASEURL ?>?act=hdv" class="btn btn-secondary w-100 mt-2">Quay lại</a>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>