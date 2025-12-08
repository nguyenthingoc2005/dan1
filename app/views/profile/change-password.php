<?php
/**
 * PROFILE - ĐỔI MẬT KHẨU (All roles)
 */

require_login();
?>

<div class="max-w-2xl mx-auto">
    <h1 class="text-xl lg:text-2xl font-bold text-primary-700 mb-4 lg:mb-6">Đổi mật khẩu</h1>

    <form method="POST" action="?act=profile/update-password" class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100">

        <!-- Old Password -->
        <div class="mb-4 lg:mb-5">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mật khẩu hiện tại <span
                    class="text-danger">*</span></label>
            <input type="password" name="old_password" required
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
        </div>

        <!-- New Password -->
        <div class="mb-4 lg:mb-5">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mật khẩu mới <span
                    class="text-danger">*</span></label>
            <input type="password" name="new_password" required
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
            <small class="text-xs text-primary-500 mt-1 block">Tối thiểu 8 ký tự, có chữ hoa, chữ thường và số</small>
        </div>

        <!-- Confirm Password -->
        <div class="mb-4 lg:mb-5">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Xác nhận mật khẩu mới <span
                    class="text-danger">*</span></label>
            <input type="password" name="confirm_password" required
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
        </div>

        <!-- Security Tips -->
        <div class="mb-4 lg:mb-6 p-4 bg-warning-bg rounded-2xl border border-warning">
            <p class="text-xs lg:text-sm text-warning-text font-semibold mb-2 flex items-center gap-2">
                <i data-lucide="lightbulb" class="w-4 h-4"></i>
                Mẹo bảo mật:
            </p>
            <ul class="text-xs lg:text-sm text-warning-text list-disc list-inside space-y-1">
                <li>Sử dụng mật khẩu mạnh và khác biệt</li>
                <li>Không chia sẻ mật khẩu cho bất kỳ ai</li>
                <li>Đổi mật khẩu định kỳ mỗi 3-6 tháng</li>
            </ul>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-primary-100">
            <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="key" class="w-4 h-4"></i>
                Đổi mật khẩu
            </button>
            <a href="?act=profile" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center">
                Hủy
            </a>
        </div>
    </form>
</div>
