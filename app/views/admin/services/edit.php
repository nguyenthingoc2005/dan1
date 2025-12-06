<?php
/**
 * ADMIN - FORM SỬA SERVICE
 * Variables: $service, $service_types, $service_providers
 */

if (!is_admin())
    redirect('?act=access-denied');

$service_types = $service_types ?? [];
$service_providers = $service_providers ?? [];
?>

<div class="max-w-8xl mx-auto">
    <h1 class="text-2xl font-bold text-primary mb-6">Sửa dịch vụ</h1>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="?act=admin&module=services&action=update" class="bg-white p-6 rounded">
        <input type="hidden" name="id" value="<?= $service['id'] ?>">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- COLUMN 1: THÔNG TIN CƠ BẢN -->
            <div>
                <h3 class="text-lg font-semibold text-primary mb-4">Thông tin cơ bản</h3>

                <!-- Service Provider -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nhà cung cấp dịch vụ <span class="text-red-500">*</span>
                    </label>
                    <select name="service_provider_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                        <option value="">-- Chọn nhà cung cấp dịch vụ --</option>
                        <?php foreach ($service_providers as $provider): ?>
                            <option value="<?= $provider['id'] ?>" <?= ($service['service_provider_id'] ?? 0) == $provider['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($provider['name'] ?? '') ?>
                                <?php if (!empty($provider['service_code'])): ?>
                                    (<?= htmlspecialchars($provider['service_code']) ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Service Type -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Loại dịch vụ
                    </label>
                    <select name="service_type_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                        <option value="">-- Chọn loại dịch vụ (tùy chọn) --</option>
                        <?php foreach ($service_types as $type): ?>
                            <option value="<?= $type['id'] ?>" <?= ($service['service_type_id'] ?? null) == $type['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-gray-500">Có thể để trống</small>
                </div>

                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tên dịch vụ <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="<?= htmlspecialchars($service['name']) ?>" required
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea name="description" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"><?= htmlspecialchars($service['description'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- COLUMN 2: CHI TIẾT -->
            <div>
                <h3 class="text-lg font-semibold text-primary mb-4">Chi tiết</h3>

                <!-- Unit -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Đơn vị tính
                    </label>
                    <select name="unit"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                        <option value="">-- Chọn đơn vị (tùy chọn) --</option>
                        <?php
                        $currentUnit = $service['unit'] ?? '';
                        $units = get_service_units();
                        foreach ($units as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $currentUnit == $value ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach;
                        // Nếu unit hiện tại không có trong list, thêm vào
                        if ($currentUnit && !isset($units[$currentUnit])): ?>
                            <option value="<?= htmlspecialchars($currentUnit) ?>" selected>
                                <?= htmlspecialchars($currentUnit) ?>
                            </option>
                        <?php endif; ?>
                    </select>
                    <small class="text-gray-500">Có thể để trống</small>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                        <option value="active" <?= ($service['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt
                            động</option>
                        <option value="inactive" <?= ($service['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Vô hiệu
                        </option>
                    </select>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
                    <h4 class="font-semibold text-blue-900 mb-2">ℹ️ Lưu ý</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Nhà cung cấp dịch vụ là bắt buộc</li>
                        <li>• Tên dịch vụ là bắt buộc</li>
                        <li>• Loại dịch vụ và đơn vị tính là tùy chọn</li>
                        <li>• Giá dịch vụ sẽ được quản lý trong Service Prices</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 mt-6 border-t pt-6">
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                Cập nhật
            </button>
            <a href="?act=admin&module=services" class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Hủy
            </a>
        </div>
    </form>
</div>