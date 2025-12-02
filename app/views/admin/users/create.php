<?php
/**
 * ADMIN - FORM TẠO NHÂN VIÊN MỚI
 * Variables: $roles
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-3xl">
    <h1 class="text-2xl font-bold text-primary mb-6">Thêm nhân viên mới</h1>

    <form method="POST" action="?act=admin&module=users&action=store" enctype="multipart/form-data"
        class="bg-white p-6 rounded">

        <!-- Email -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span
                    class="text-red-500">*</span></label>
            <input type="email" name="email" required
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu <span
                    class="text-red-500">*</span></label>
            <input type="password" name="password" required
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
            <small class="text-gray-500">Tối thiểu 8 ký tự, có chữ hoa, chữ thường và số</small>
        </div>

        <!-- Full Name -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên <span
                    class="text-red-500">*</span></label>
            <input type="text" name="full_name" required
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
        </div>

        <!-- Role -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò <span
                    class="text-red-500">*</span></label>
            <select name="role_id" required
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                <option value="">-- Chọn vai trò --</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['display_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Phone -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
            <input type="tel" name="phone"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
        </div>

        <!-- Date of Birth -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
            <input type="date" name="date_of_birth"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
        </div>

        <!-- Gender -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
            <select name="gender"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                <option value="male">Nam</option>
                <option value="female">Nữ</option>
                <option value="other">Khác</option>
            </select>
        </div>

        <!-- Address -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
            <textarea name="address" rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"></textarea>
        </div>

        <!-- Avatar -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh đại diện</label>
            <input type="file" name="avatar" accept="image/*"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
            <small class="text-gray-500">Max 2MB, JPG/PNG</small>
        </div>

        <!-- Status -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
            <select name="status"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                <option value="active">Hoạt động</option>
                <option value="inactive">Vô hiệu</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 mt-6">
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                Tạo nhân viên
            </button>
            <a href="?act=admin&module=users" class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Hủy
            </a>
        </div>
    </form>
</div>