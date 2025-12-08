<?php
/**
 * PROFILE - XEM THÔNG TIN CÁ NHÂN (All roles)
 * Variables: $user
 */

require_login();
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-xl lg:text-2xl font-bold text-primary-700 mb-4 lg:mb-6">Thông tin cá nhân</h1>

    <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100">
        <!-- Avatar & Basic Info -->
        <div class="text-center mb-6 lg:mb-8">
            <?php if ($user['avatar']): ?>
                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar"
                    class="w-24 lg:w-32 h-24 lg:h-32 rounded-2xl object-cover mx-auto mb-4 border-4 border-primary-100 shadow-sm">
            <?php else: ?>
                <div
                    class="w-24 lg:w-32 h-24 lg:h-32 rounded-2xl bg-info-bg flex items-center justify-center text-2xl lg:text-3xl font-bold text-info-text mx-auto mb-4 border-4 border-primary-100 shadow-sm">
                    <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                </div>
            <?php endif; ?>

            <h2 class="text-lg lg:text-xl font-bold text-primary-700"><?= htmlspecialchars($user['full_name']) ?></h2>
            <p class="text-sm lg:text-base text-primary-500 mt-1"><?= htmlspecialchars($user['role_display']) ?></p>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-500 mb-1 lg:mb-2">Email</label>
                <p class="text-sm lg:text-base text-primary-700 font-semibold"><?= htmlspecialchars($user['email']) ?></p>
            </div>
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-500 mb-1 lg:mb-2">Số điện thoại</label>
                <p class="text-sm lg:text-base text-primary-700 font-semibold"><?= htmlspecialchars($user['phone'] ?? '-') ?></p>
            </div>
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-500 mb-1 lg:mb-2">Ngày sinh</label>
                <p class="text-sm lg:text-base text-primary-700 font-semibold"><?= !empty($user['date_of_birth']) ? format_date($user['date_of_birth']) : '-' ?></p>
            </div>
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-500 mb-1 lg:mb-2">Giới tính</label>
                <p class="text-sm lg:text-base text-primary-700 font-semibold">
                    <?php
                    $genders = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                    echo $genders[$user['gender'] ?? ''] ?? '-';
                    ?>
                </p>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs lg:text-sm font-semibold text-primary-500 mb-1 lg:mb-2">Địa chỉ</label>
                <p class="text-sm lg:text-base text-primary-700 font-semibold"><?= htmlspecialchars($user['address'] ?? '-') ?></p>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3 mt-6 lg:mt-8 pt-4 lg:pt-6 border-t border-primary-100">
            <a href="?act=profile/edit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="pencil" class="w-4 h-4"></i>
                Sửa thông tin
            </a>
            <a href="?act=profile/change-password"
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="key" class="w-4 h-4"></i>
                Đổi mật khẩu
            </a>
        </div>
    </div>
</div>
