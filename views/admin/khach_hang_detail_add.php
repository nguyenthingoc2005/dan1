<div class="main-content">
    <h3 class="title-page">Bổ sung thông tin khách hàng: <?= $khachhang['ho_ten'] ?? 'Mới' ?></h3>

    <form action="<?= BASEURL ?>?act=store_khach_hang_detail" method="POST">
        <input type="hidden" name="khach_hang_id" value="<?= $_GET['khach_hang_id'] ?>">

        <div class="form-group mb-3">
            <label>Ngày sinh</label>
            <input type="date" class="form-control" name="ngay_sinh">
        </div>

        <div class="form-group mb-3">
            <label>Giới tính</label>
            <select class="form-select" name="gioi_tinh">
                <option value="Nam">Nam</option>
                <option value="Nữ">Nữ</option>
                <option value="Khác">Khác</option>
            </select>
        </div>

        <div class="form-group mb-3">
            <label>CCCD / CMND</label>
            <input type="text" class="form-control" name="cccd" placeholder="Nhập số CCCD">
        </div>

        <div class="form-group mb-3">
            <label>Địa chỉ</label>
            <input type="text" class="form-control" name="dia_chi" placeholder="Nhập địa chỉ">
        </div>

        <div class="mt-4">
            <button type="submit" name="btn_save" class="btn btn-primary">Lưu thông tin</button>

            <button type="submit" name="btn_skip" class="btn btn-secondary" formnovalidate>Bỏ qua</button>
        </div>
    </form>
</div>