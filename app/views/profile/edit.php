<?php
/**
 * PROFILE - SỬA THÔNG TIN CÁ NHÂN (All roles)
 * Variables: $user
 */

require_login();
?>

<div class="max-w-3xl mx-auto">
    <h1 class="text-xl lg:text-2xl font-bold text-primary-700 mb-4 lg:mb-6">Sửa thông tin cá nhân</h1>

    <form method="POST" action="?act=profile/update" enctype="multipart/form-data" class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100">

        <!-- Full Name -->
        <div class="mb-4 lg:mb-5">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Họ và tên <span
                    class="text-danger">*</span></label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
        </div>

        <!-- Phone -->
        <div class="mb-4 lg:mb-5">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số điện thoại</label>
            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
        </div>

        <!-- Date of Birth -->
        <div class="mb-4 lg:mb-5">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ngày sinh</label>
            <input type="date" name="date_of_birth" value="<?= $user['date_of_birth'] ?? '' ?>"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
        </div>

        <!-- Gender -->
        <div class="mb-4 lg:mb-5">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Giới tính</label>
            <select name="gender"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                <option value="male" <?= ($user['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Nam</option>
                <option value="female" <?= ($user['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Nữ</option>
                <option value="other" <?= ($user['gender'] ?? '') == 'other' ? 'selected' : '' ?>>Khác</option>
            </select>
        </div>

        <!-- Address -->
        <div class="mb-4 lg:mb-5">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Địa chỉ</label>
            <textarea name="address" rows="3"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
        </div>

        <!-- Current Avatar -->
        <?php if ($user['avatar']): ?>
            <div class="mb-4 lg:mb-5">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ảnh hiện tại</label>
                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar"
                    class="w-20 lg:w-24 h-20 lg:h-24 rounded-2xl object-cover border border-primary-100">
            </div>
        <?php endif; ?>

        <!-- New Avatar -->
        <div class="mb-4 lg:mb-5">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ảnh đại diện mới</label>
            <input type="file" name="avatar" accept="image/*"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
        </div>

        <!-- Info Note -->
        <div class="mb-4 lg:mb-5 p-4 bg-info-bg rounded-2xl border border-info">
            <p class="text-xs lg:text-sm text-info-text flex items-start gap-2">
                <i data-lucide="info" class="w-4 h-4 flex-shrink-0 mt-0.5"></i>
                <span><strong>Lưu ý:</strong> Bạn không thể thay đổi email và vai trò. Nếu cần thay đổi, vui lòng liên hệ quản trị viên.</span>
            </p>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-4 border-t border-primary-100">
            <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                Cập nhật
            </button>
            <a href="?act=profile" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center">
                Hủy
            </a>
        </div>
    </form>
</div>
