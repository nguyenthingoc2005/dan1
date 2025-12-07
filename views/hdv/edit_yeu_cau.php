<style>
    .form-title {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-custom {
        border-radius: 12px;
        border: 1px solid #e4e4e4;
    }

    .btn-success {
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 500;
    }

    .btn-secondary {
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 500;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 10px 12px;
    }

    label {
        font-weight: 500;
    }
</style>

<div class="container mt-4">

    <h3 class="mb-4 form-title">
        ✏️ Sửa yêu cầu phục vụ
    </h3>

    <form action="<?= BASEURL ?>?act=capnhat_yeu_cau" method="post" class="card card-custom p-4 shadow-sm">

        <input type="hidden" name="yeu_cau_id" value="<?= $row['yeu_cau_id'] ?>">

        <div class="mb-3">
            <label class="form-label">Đặt Tour (ID)</label>
            <input type="number" name="dat_tour_id" class="form-control"
                value="<?= $row['dat_tour_id'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Hành khách (ID)</label>
            <input type="number" name="hanh_khach_id" class="form-control"
                value="<?= $row['hanh_khach_id'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nội dung yêu cầu</label>
            <textarea name="noi_dung" class="form-control" rows="3" required><?= $row['noi_dung'] ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Mức độ ưu tiên</label>
            <select name="muc_do_uu_tien" class="form-select">
                <option <?= $row['muc_do_uu_tien'] == 'Thấp' ? 'selected' : '' ?>>Thấp</option>
                <option <?= $row['muc_do_uu_tien'] == 'Trung bình' ? 'selected' : '' ?>>Trung bình</option>
                <option <?= $row['muc_do_uu_tien'] == 'Cao' ? 'selected' : '' ?>>Cao</option>
                <option <?= $row['muc_do_uu_tien'] == 'Khẩn cấp' ? 'selected' : '' ?>>Khẩn cấp</option>
            </select>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="da_chuan_bi"
                value="1" <?= $row['da_chuan_bi'] ? 'checked' : '' ?>>
            <label class="form-check-label">Đã chuẩn bị</label>
        </div>

        <div class="mb-3">
            <label class="form-label">Ghi chú</label>
            <textarea name="ghi_chu" class="form-control" rows="2"><?= $row['ghi_chu'] ?></textarea>
        </div>

        <div class="d-flex align-items-center mt-3">
            <button type="submit" class="btn btn-success">
                <a href="<?= BASEURL ?>?act=chitiet_khach_hang"> 💾 Cập nhật</a>
            </button>

            <a href="<?= BASEURL ?>?act=chitiet_khach_hang" class="btn btn-secondary ms-3">
                ⬅️ Quay lại
            </a>
        </div>

    </form>
</div>