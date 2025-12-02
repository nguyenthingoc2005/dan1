<?php
/**
 * ADMIN - FORM SỬA NHÂN VIÊN
 * Variables: $user, $roles
 */

if (!is_admin()) redirect('?act=access-denied');
?>

<div class="max-w-3xl">
    <h1 class="text-2xl font-bold text-primary mb-6">Sửa thông tin nhân viên</h1>

    <form method="POST" action="?act=admin/users/update" enctype="multipart/form-data" class="bg-white p-6 rounded">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">

        <!-- Email (Read-only in edit) -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled 
                   class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100">
            <small class="text-gray-500">Email không thể thay đổi</small>
        </div>

        <!-- Password (Optional) -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới</label>
            <input type="password" name="password" 
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
            <small class="text-gray-500">Để trống nếu không muốn đổi mật khẩu</small>
        </div>

        <!-- Full Name -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required 
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
        </div>

        <!-- Role -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò <span class="text-red-500">*</span></label>
            <select name="role_id" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>" <?= ($user['role_id'] ?? 0) == $role['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['display_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
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
        <?php if (!empty($user['avatar'])): ?>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh hiện tại</label>
                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="w-24 h-24 rounded-full object-cover">
            </div>
        <?php endif; ?>

        <!-- New Avatar -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh đại diện mới</label>
            <input type="file" name="avatar" accept="image/*" 
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
        </div>

        <!-- Status -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
            <select name="status" 
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                <option value="active" <?= ($user['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                <option value="inactive" <?= ($user['status'] ?? 'active') == 'inactive' ? 'selected' : '' ?>>Vô hiệu</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 mt-6">
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                Cập nhật
            </button>
            <a href="?act=admin/users" class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Hủy
            </a>
        </div>
    </form>
</div>