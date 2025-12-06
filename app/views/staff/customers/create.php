<?php
/**
 * View: Thêm Khách Hàng Mới (Staff)
 */
?>

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-slate-800">Thêm Khách Hàng Mới</h2>
            <a href="?act=staff-customers" class="text-slate-500 hover:text-slate-700 font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>

        <form action="?act=staff-customers&action=store" method="POST" class="p-6">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Full Name -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Họ và Tên <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="full_name" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all"
                        placeholder="Nhập họ tên khách hàng"
                        value="<?php echo htmlspecialchars($_SESSION['old']['full_name'] ?? ''); ?>">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Số Điện Thoại <span
                            class="text-red-500">*</span></label>
                    <input type="tel" name="phone" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all"
                        placeholder="0912345678"
                        value="<?php echo htmlspecialchars($_SESSION['old']['phone'] ?? ''); ?>">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                    <input type="email" name="email"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all"
                        placeholder="example@email.com"
                        value="<?php echo htmlspecialchars($_SESSION['old']['email'] ?? ''); ?>">
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Giới Tính</label>
                    <select name="gender"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                        <option value="male" <?php echo (($_SESSION['old']['gender'] ?? '') == 'male') ? 'selected' : ''; ?>>Nam</option>
                        <option value="female" <?php echo (($_SESSION['old']['gender'] ?? '') == 'female') ? 'selected' : ''; ?>>Nữ</option>
                        <option value="other" <?php echo (($_SESSION['old']['gender'] ?? '') == 'other') ? 'selected' : ''; ?>>Khác</option>
                    </select>
                </div>

                <!-- Date of Birth -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Ngày Sinh</label>
                    <input type="date" name="date_of_birth"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all"
                        value="<?php echo htmlspecialchars($_SESSION['old']['date_of_birth'] ?? ''); ?>">
                </div>

                <!-- Address -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Địa Chỉ</label>
                    <input type="text" name="address"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all"
                        placeholder="Số nhà, đường, phường/xã..."
                        value="<?php echo htmlspecialchars($_SESSION['old']['address'] ?? ''); ?>">
                </div>

                <!-- Special Requirements -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Yêu Cầu Đặc Biệt</label>
                    <textarea name="special_requirements" rows="3"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all"
                        placeholder="Nhập yêu cầu đặc biệt của khách hàng..."><?php echo htmlspecialchars($_SESSION['old']['special_requirements'] ?? ''); ?></textarea>
                </div>

                <!-- Notes -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Ghi Chú</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all"
                        placeholder="Ghi chú thêm về khách hàng..."><?php echo htmlspecialchars($_SESSION['old']['notes'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="?act=staff-customers"
                    class="px-6 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 font-medium transition-colors">Hủy</a>
                <button type="submit"
                    class="px-6 py-2 bg-accent hover:bg-blue-600 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all">
                    <i class="fas fa-save mr-2"></i> Lưu Khách Hàng
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