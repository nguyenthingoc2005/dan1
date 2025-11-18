<!-- BOOTSTRAP CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4" style="max-width: 700px;">
    
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Tạo Tour</h4>
        </div>

        <div class="card-body">
            <form action="<?= BASEURL ?>?act=createtour" method="POST">
                
                <div class="mb-3">
                    <label class="form-label">Tên tour</label>
                    <input type="text" class="form-control" name="ten" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Danh mục tour</label>
                    <select class="form-select" name="danh_muc_id" required>
                        <?php foreach($data as $cat): ?>
                            <option value="<?= $cat['danh_muc_id'] ?>"><?= $cat['ten'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả ngắn</label>
                    <input type="text" class="form-control" name="mo_ta_ngan">
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea class="form-control" name="mo_ta" rows="4"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Giá cơ bản</label>
                    <input type="number" step="0.01" class="form-control" name="gia_co_ban">
                </div>

                <div class="mb-3">
                    <label class="form-label">Thời lượng mặc định (ngày)</label>
                    <input type="number" class="form-control" name="thoi_luong_mac_dinh">
                </div>

                <div class="mb-3">
                    <label class="form-label">Điểm khởi hành</label>
                    <input type="text" class="form-control" name="diem_khoi_hanh">
                </div>

                <div class="mb-3">
                    <label class="form-label">Hoạt động</label>
                    <select class="form-select" name="hoat_dong">
                        <option value="1">Đang hoạt động</option>
                        <option value="0">Tạm dừng</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100">Tạo tour</button>
            </form>
        </div>
    </div>
</div>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
