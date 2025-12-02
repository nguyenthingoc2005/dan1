<?php
/**
 * PROFILE - SỬA THÔNG TIN CÁ NHÂN (All roles)
 * Variables: $user
 */

require_login();
?>

<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-primary mb-6">Sửa thông tin cá nhân</h1>

    <form method="POST" action="?act=profile/update" enctype="multipart/form-data" class="bg-white p-6 rounded">

        <!-- Full Name -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên <span
                    class="text-red-500">*</span></label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
        </div>

        <!-- Phone -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
        </div>

        <!-- Date of Birth -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
            <input type="date" name="date_of_birth" value="<?= $user['date_of_birth'] ?? '' ?>"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
        </div>

        <!-- Gender -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
            <select name="gender"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                <option value="male" <?= ($user['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Nam</option>
                <option value="female" <?= ($user['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Nữ</option>
                <option value="other" <?= ($user['gender'] ?? '') == 'other' ? 'selected' : '' ?>>Khác</option>
            </select>
        </div>

        <!-- Address -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
            <textarea name="address" rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
        </div>

        <!-- Current Avatar -->
        <?php if ($user['avatar']): ?>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh hiện tại</label>
                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar"
                    class="w-24 h-24 rounded-full object-cover">
            </div>
        <?php endif; ?>

        <!-- New Avatar -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh đại diện mới</label>
            <input type="file" name="avatar" accept="image/*"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
        </div>

        <!-- Info Note -->
        <div class="mb-4 p-4 bg-blue-50 rounded">
            <p class="text-sm text-gray-600">
                <strong>Lưu ý:</strong> Bạn không thể thay đổi email và vai trò.
                Nếu cần thay đổi, vui lòng liên hệ quản trị viên.
            </p>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 mt-6">
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                Cập nhật
            </button>
            <a href="?act=profile" class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Hủy
            </a>
        </div>
    </form>
</div>