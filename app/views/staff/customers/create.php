<?php
/**
 * View: Thêm Khách Hàng Mới (Staff)
 */
?>

<div class="max-w-3xl mx-auto">
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="p-4 lg:p-6 border-b border-primary-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="text-lg lg:text-xl font-bold text-primary-700">Thêm Khách Hàng Mới</h2>
            <a href="?act=staff-customers" class="text-primary-500 hover:text-primary-700 font-semibold transition-colors text-sm lg:text-base flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>

        <form action="?act=staff-customers&action=store" method="POST" class="p-4 lg:p-6">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-4 lg:mb-6">
                <!-- Full Name -->
                <div class="lg:col-span-2">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Họ và Tên <span
                            class="text-danger">*</span></label>
                    <input type="text" name="full_name" required
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="Nhập họ tên khách hàng"
                        value="<?php echo htmlspecialchars($_SESSION['old']['full_name'] ?? ''); ?>">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số Điện Thoại <span
                            class="text-danger">*</span></label>
                    <input type="tel" name="phone" required
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="0912345678"
                        value="<?php echo htmlspecialchars($_SESSION['old']['phone'] ?? ''); ?>">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Email</label>
                    <input type="email" name="email"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="example@email.com"
                        value="<?php echo htmlspecialchars($_SESSION['old']['email'] ?? ''); ?>">
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Giới Tính</label>
                    <select name="gender"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                        <option value="male" <?php echo (($_SESSION['old']['gender'] ?? '') == 'male') ? 'selected' : ''; ?>>Nam</option>
                        <option value="female" <?php echo (($_SESSION['old']['gender'] ?? '') == 'female') ? 'selected' : ''; ?>>Nữ</option>
                        <option value="other" <?php echo (($_SESSION['old']['gender'] ?? '') == 'other') ? 'selected' : ''; ?>>Khác</option>
                    </select>
                </div>

                <!-- Date of Birth -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ngày Sinh</label>
                    <input type="date" name="date_of_birth"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base"
                        value="<?php echo htmlspecialchars($_SESSION['old']['date_of_birth'] ?? ''); ?>">
                </div>

                <!-- Address -->
                <div class="lg:col-span-2">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Địa Chỉ</label>
                    <input type="text" name="address"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="Số nhà, đường, phường/xã..."
                        value="<?php echo htmlspecialchars($_SESSION['old']['address'] ?? ''); ?>">
                </div>

                <!-- Special Requirements -->
                <div class="lg:col-span-2">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Yêu Cầu Đặc Biệt</label>
                    <textarea name="special_requirements" rows="3"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="Nhập yêu cầu đặc biệt của khách hàng..."><?php echo htmlspecialchars($_SESSION['old']['special_requirements'] ?? ''); ?></textarea>
                </div>

                <!-- Notes -->
                <div class="lg:col-span-2">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ghi Chú</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="Ghi chú thêm về khách hàng..."><?php echo htmlspecialchars($_SESSION['old']['notes'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 lg:pt-6 border-t border-primary-100">
                <a href="?act=staff-customers"
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center">Hủy</a>
                <button type="submit"
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Lưu Khách Hàng
                </button>
            </div>
        </form>
    </div>
</div>

<?php
// Clear old input
if (isset($_SESSION['old']))
    unset($_SESSION['old']);
?>