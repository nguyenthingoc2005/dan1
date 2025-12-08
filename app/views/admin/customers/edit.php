<div class="max-w-4xl mx-auto">
    <!-- HEADER - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Sửa Khách hàng</h1>
        <a href="?act=admin&module=customers" class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center flex items-center justify-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    <!-- FORM CARD -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="p-4 lg:p-6 border-b border-primary-100">
            <h2 class="text-base lg:text-lg font-bold text-primary-700">Thông tin khách hàng:
                <?= htmlspecialchars($customer['full_name']) ?></h2>
        </div>
        <div class="p-4 lg:p-6">
            <form action="?act=admin&module=customers&action=update" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?= $customer['id'] ?>">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-4 lg:mb-6">
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="full_name"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base"
                            value="<?= htmlspecialchars($customer['full_name']) ?>" required>
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" name="phone"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base"
                            value="<?= htmlspecialchars($customer['phone']) ?>" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-4 lg:mb-6">
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Email</label>
                        <input type="email" name="email"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                            value="<?= htmlspecialchars($customer['email'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ngày sinh</label>
                        <input type="date" name="date_of_birth"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base"
                            value="<?= $customer['date_of_birth'] ?>">
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 mb-4 lg:mb-6">
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Giới tính</label>
                        <select name="gender"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                            <option value="male" <?= $customer['gender'] == 'male' ? 'selected' : '' ?>>Nam</option>
                            <option value="female" <?= $customer['gender'] == 'female' ? 'selected' : '' ?>>Nữ</option>
                            <option value="other" <?= $customer['gender'] == 'other' ? 'selected' : '' ?>>Khác</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">CMND/CCCD</label>
                        <input type="text" name="id_card"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                            value="<?= htmlspecialchars($customer['id_card'] ?? '') ?>">
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Hộ chiếu (Passport)</label>
                        <input type="text" name="passport"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                            value="<?= htmlspecialchars($customer['passport'] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-4 lg:mb-6">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Địa chỉ</label>
                    <input type="text" name="address"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        value="<?= htmlspecialchars($customer['address'] ?? '') ?>">
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-4 lg:mb-6">
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Quốc tịch</label>
                        <input type="text" name="nationality"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base"
                            value="<?= htmlspecialchars($customer['nationality'] ?? 'Vietnam') ?>">
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Loại khách hàng</label>
                        <select name="customer_type"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                            <option value="individual" <?= $customer['customer_type'] == 'individual' ? 'selected' : '' ?>>Cá nhân</option>
                            <option value="group" <?= $customer['customer_type'] == 'group' ? 'selected' : '' ?>>Nhóm</option>
                            <option value="corporate" <?= $customer['customer_type'] == 'corporate' ? 'selected' : '' ?>>Doanh nghiệp</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-4 lg:mb-6">
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Nguồn khách</label>
                        <select name="source"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                            <option value="other" <?= $customer['source'] == 'other' ? 'selected' : '' ?>>Khác</option>
                            <option value="phone" <?= $customer['source'] == 'phone' ? 'selected' : '' ?>>Điện thoại</option>
                            <option value="email" <?= $customer['source'] == 'email' ? 'selected' : '' ?>>Email</option>
                            <option value="facebook" <?= $customer['source'] == 'facebook' ? 'selected' : '' ?>>Facebook</option>
                            <option value="zalo" <?= $customer['source'] == 'zalo' ? 'selected' : '' ?>>Zalo</option>
                            <option value="walk_in" <?= $customer['source'] == 'walk_in' ? 'selected' : '' ?>>Trực tiếp</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
                        <select name="status"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                            <option value="active" <?= $customer['status'] == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                            <option value="inactive" <?= $customer['status'] == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động</option>
                            <option value="blacklist" <?= $customer['status'] == 'blacklist' ? 'selected' : '' ?>>Blacklist</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4 lg:mb-6">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Yêu cầu đặc biệt</label>
                    <textarea name="special_requirements"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        rows="3" placeholder="Nhập yêu cầu đặc biệt của khách hàng..."><?= htmlspecialchars($customer['special_requirements'] ?? '') ?></textarea>
                </div>

                <div class="mb-4 lg:mb-6">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ghi chú</label>
                    <textarea name="notes"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        rows="3"><?= htmlspecialchars($customer['notes'] ?? '') ?></textarea>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-primary-100">
                    <a href="?act=admin&module=customers"
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center">
                        Hủy bỏ
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
