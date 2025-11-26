<h2 class="mb-4">Thêm Nhà Cung Cấp</h2>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


<form action="<?= BASEURL ?>?act=ncc_create" method="POST" class="card p-4 shadow-sm bg-white">

    <div class="mb-3">
        <label for="ten" class="form-label">Tên</label>
        <input type="text" id="ten" name="ten" class="form-control" placeholder="Nhập tên nhà cung cấp" required>
    </div>

    <div class="mb-3">
        <label for="lien_he" class="form-label">Liên hệ</label>
        <input type="text" id="lien_he" name="lien_he" class="form-control" placeholder="Số điện thoại / Email">
    </div>

    <div class="mb-3">
        <label for="dia_chi" class="form-label">Địa chỉ</label>
        <input type="text" id="dia_chi" name="dia_chi" class="form-control" placeholder="Nhập địa chỉ">
    </div>

    <div class="mb-3">
        <label for="ma_so_thue" class="form-label">Mã số thuế</label>
        <input type="text" id="ma_so_thue" name="ma_so_thue" class="form-control" placeholder="Nhập mã số thuế">
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm
        </button>
                <a href="index.php?act=ncc_list" class="btn btn-secondary">← Quay lại</a>

    </div>
</form>
