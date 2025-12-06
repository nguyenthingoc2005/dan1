<?php
/**
 * PROFILE - XEM THÔNG TIN CÁ NHÂN (All roles)
 * Variables: $user
 */

require_login();
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-primary mb-6">Thông tin cá nhân</h1>

    <div class="bg-white p-6 rounded">
        <!-- Avatar & Basic Info -->
        <div class="text-center mb-8">
            <?php if ($user['avatar']): ?>
                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar"
                    class="w-32 h-32 rounded-full object-cover mx-auto mb-4">
            <?php else: ?>
                <div
                    class="w-32 h-32 rounded-full bg-gray-300 flex items-center justify-center text-3xl font-bold text-gray-600 mx-auto mb-4">
                    <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                </div>
            <?php endif; ?>

            <h2 class="text-xl font-bold"><?= htmlspecialchars($user['full_name']) ?></h2>
            <p class="text-gray-600"><?= htmlspecialchars($user['role_display']) ?></p>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                <p class="text-gray-900"><?= htmlspecialchars($user['email']) ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Số điện thoại</label>
                <p class="text-gray-900"><?= htmlspecialchars($user['phone'] ?? '-') ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Ngày sinh</label>
                <p class="text-gray-900"><?= !empty($user['date_of_birth']) ? format_date($user['date_of_birth']) : '-' ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Giới tính</label>
                <p class="text-gray-900">
                    <?php
                    $genders = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                    echo $genders[$user['gender'] ?? ''] ?? '-';
                    ?>
                </p>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Địa chỉ</label>
                <p class="text-gray-900"><?= htmlspecialchars($user['address'] ?? '-') ?></p>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4 mt-8 pt-6 border-t">
            <a href="?act=profile/edit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                Sửa thông tin
            </a>
            <a href="?act=profile/change-password"
                class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Đổi mật khẩu
            </a>
        </div>
    </div>
</div>