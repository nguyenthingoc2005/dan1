<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sửa Khách hàng</h1>
        <a href="?act=admin&module=customers" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin khách hàng:
                        <?= htmlspecialchars($customer['full_name']) ?></h6>
                </div>
                <div class="card-body">
                    <form action="?act=admin&module=customers&action=update" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="id" value="<?= $customer['id'] ?>">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control"
                                    value="<?= htmlspecialchars($customer['full_name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control"
                                    value="<?= htmlspecialchars($customer['phone']) ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="<?= htmlspecialchars($customer['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ngày sinh</label>
                                <input type="date" name="date_of_birth" class="form-control"
                                    value="<?= $customer['date_of_birth'] ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Giới tính</label>
                                <select name="gender" class="form-select">
                                    <option value="male" <?= $customer['gender'] == 'male' ? 'selected' : '' ?>>Nam
                                    </option>
                                    <option value="female" <?= $customer['gender'] == 'female' ? 'selected' : '' ?>>Nữ
                                    </option>
                                    <option value="other" <?= $customer['gender'] == 'other' ? 'selected' : '' ?>>Khác
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">CMND/CCCD</label>
                                <input type="text" name="id_card" class="form-control"
                                    value="<?= htmlspecialchars($customer['id_card'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Hộ chiếu (Passport)</label>
                                <input type="text" name="passport" class="form-control"
                                    value="<?= htmlspecialchars($customer['passport'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" name="address" class="form-control"
                                value="<?= htmlspecialchars($customer['address'] ?? '') ?>">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Quốc tịch</label>
                                <input type="text" name="nationality" class="form-control"
                                    value="<?= htmlspecialchars($customer['nationality'] ?? 'Vietnam') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Loại khách hàng</label>
                                <select name="customer_type" class="form-select">
                                    <option value="individual" <?= $customer['customer_type'] == 'individual' ? 'selected' : '' ?>>Cá nhân</option>
                                    <option value="group" <?= $customer['customer_type'] == 'group' ? 'selected' : '' ?>>
                                        Nhóm</option>
                                    <option value="corporate" <?= $customer['customer_type'] == 'corporate' ? 'selected' : '' ?>>Doanh nghiệp</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nguồn khách</label>
                                <select name="source" class="form-select">
                                    <option value="other" <?= $customer['source'] == 'other' ? 'selected' : '' ?>>Khác
                                    </option>
                                    <option value="phone" <?= $customer['source'] == 'phone' ? 'selected' : '' ?>>Điện
                                        thoại</option>
                                    <option value="email" <?= $customer['source'] == 'email' ? 'selected' : '' ?>>Email
                                    </option>
                                    <option value="facebook" <?= $customer['source'] == 'facebook' ? 'selected' : '' ?>>
                                        Facebook</option>
                                    <option value="zalo" <?= $customer['source'] == 'zalo' ? 'selected' : '' ?>>Zalo
                                    </option>
                                    <option value="walk_in" <?= $customer['source'] == 'walk_in' ? 'selected' : '' ?>>Trực
                                        tiếp</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="active" <?= $customer['status'] == 'active' ? 'selected' : '' ?>>Hoạt
                                        động</option>
                                    <option value="inactive" <?= $customer['status'] == 'inactive' ? 'selected' : '' ?>>
                                        Ngừng hoạt động</option>
                                    <option value="blacklist" <?= $customer['status'] == 'blacklist' ? 'selected' : '' ?>>
                                        Blacklist</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Yêu cầu đặc biệt</label>
                            <textarea name="special_requirements" class="form-control"
                                rows="3" placeholder="Nhập yêu cầu đặc biệt của khách hàng..."><?= htmlspecialchars($customer['special_requirements'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="notes" class="form-control"
                                rows="3"><?= htmlspecialchars($customer['notes'] ?? '') ?></textarea>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>