<?php
/**
 * ADMIN - FORM SỬA NHÂN VIÊN
 * Variables: $user, $roles
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-3xl mx-auto">
    <!-- Header - Responsive -->
    <div class="mb-4 lg:mb-6">
        <div class="flex items-center gap-2 mb-2">
            <a href="?act=admin&module=users" class="text-primary-500 hover:text-primary-700 font-semibold text-sm lg:text-base flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Sửa thông tin nhân viên</h1>
    </div>

    <form method="POST" action="?act=admin&module=users&action=update" enctype="multipart/form-data"
        class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100 space-y-4 lg:space-y-6">
        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
            <!-- Email (Read-only in edit) -->
            <div class="lg:col-span-2">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Email</label>
                <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl text-primary-500 text-sm lg:text-base cursor-not-allowed">
                <small class="text-xs text-primary-500 mt-1">Email không thể thay đổi</small>
            </div>

            <!-- Password (Optional) -->
            <div class="lg:col-span-2">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mật khẩu mới</label>
                <input type="password" name="password"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
                <small class="text-xs text-primary-500 mt-1">Để trống nếu không muốn đổi mật khẩu</small>
            </div>

            <!-- Full Name -->
            <div class="lg:col-span-2">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Họ và tên <span
                        class="text-danger">*</span></label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
            </div>

            <!-- Role -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Vai trò <span
                        class="text-danger">*</span></label>
                <select name="role_id" required
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>" <?= ($user['role_id'] ?? 0) == $role['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($role['display_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số điện thoại</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
            </div>

            <!-- Date of Birth -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ngày sinh</label>
                <input type="date" name="date_of_birth" value="<?= $user['date_of_birth'] ?? '' ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
            </div>

            <!-- Gender -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Giới tính</label>
                <select name="gender"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="male" <?= ($user['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Nam</option>
                    <option value="female" <?= ($user['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Nữ</option>
                    <option value="other" <?= ($user['gender'] ?? '') == 'other' ? 'selected' : '' ?>>Khác</option>
                </select>
            </div>

            <!-- Address -->
            <div class="lg:col-span-2">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Địa chỉ</label>
                <textarea name="address" rows="3"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
            </div>

            <!-- Current Avatar -->
            <?php if (!empty($user['avatar'])): ?>
                <div class="lg:col-span-2">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ảnh hiện tại</label>
                    <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar"
                        class="w-24 h-24 rounded-full object-cover border border-primary-100 shadow-sm">
                </div>
            <?php endif; ?>

            <!-- New Avatar -->
            <div class="lg:col-span-2">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ảnh đại diện mới</label>
                <input type="file" name="avatar" accept="image/*"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-accent file:text-white hover:file:opacity-90">
            </div>

            <!-- Status -->
            <div class="lg:col-span-2">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
                <select name="status"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="active" <?= ($user['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động
                    </option>
                    <option value="inactive" <?= ($user['status'] ?? 'active') == 'inactive' ? 'selected' : '' ?>>Vô hiệu
                    </option>
                </select>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-primary-100">
            <a href="?act=admin&module=users" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center">
                Hủy
            </a>
            <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                Cập nhật
            </button>
        </div>
    </form>
</div>