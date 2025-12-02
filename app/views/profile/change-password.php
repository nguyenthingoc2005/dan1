<?php
/**
 * PROFILE - ĐỔI MẬT KHẨU (All roles)
 */

require_login();
?>

<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-primary mb-6">Đổi mật khẩu</h1>

    <form method="POST" action="?act=profile/update-password" class="bg-white p-6 rounded">

        <!-- Old Password -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu hiện tại <span
                    class="text-red-500">*</span></label>
            <input type="password" name="old_password" required
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
        </div>

        <!-- New Password -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới <span
                    class="text-red-500">*</span></label>
            <input type="password" name="new_password" required
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
            <small class="text-gray-500">Tối thiểu 8 ký tự, có chữ hoa, chữ thường và số</small>
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu mới <span
                    class="text-red-500">*</span></label>
            <input type="password" name="confirm_password" required
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
        </div>

        <!-- Security Tips -->
        <div class="mb-6 p-4 bg-yellow-50 rounded">
            <p class="text-sm text-gray-700 font-medium mb-2">💡 Mẹo bảo mật:</p>
            <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                <li>Sử dụng mật khẩu mạnh và khác biệt</li>
                <li>Không chia sẻ mật khẩu cho bất kỳ ai</li>
                <li>Đổi mật khẩu định kỳ mỗi 3-6 tháng</li>
            </ul>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4">
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                Đổi mật khẩu
            </button>
            <a href="?act=profile" class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Hủy
            </a>
        </div>
    </form>
</div>