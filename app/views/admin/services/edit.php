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

<div class="max-w-6xl mx-auto">
    <!-- Header - Responsive -->
    <div class="mb-4 lg:mb-6">
        <div class="flex items-center gap-2 mb-2">
            <a href="?act=admin&module=services" class="text-primary-500 hover:text-primary-700 font-semibold text-sm lg:text-base flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Sửa dịch vụ</h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="bg-danger-bg border-l-4 border-danger rounded-r-xl p-4 lg:p-5 mb-4">
            <ul class="list-disc list-inside text-sm text-danger-text">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="?act=admin&module=services&action=update" class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100 space-y-4 lg:space-y-6">
        <input type="hidden" name="id" value="<?= $service['id'] ?>">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
            <!-- COLUMN 1: THÔNG TIN CƠ BẢN -->
            <div class="space-y-4 lg:space-y-6">
                <h3 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2">Thông tin cơ bản</h3>

                <!-- Service Provider -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                        Nhà cung cấp dịch vụ <span class="text-danger">*</span>
                    </label>
                    <select name="service_provider_id" required
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
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
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                        Loại dịch vụ
                    </label>
                    <select name="service_type_id"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                        <option value="">-- Chọn loại dịch vụ (tùy chọn) --</option>
                        <?php foreach ($service_types as $type): ?>
                            <option value="<?= $type['id'] ?>" <?= ($service['service_type_id'] ?? null) == $type['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-xs text-primary-500 mt-1">Có thể để trống</small>
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                        Tên dịch vụ <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" value="<?= htmlspecialchars($service['name']) ?>" required
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả</label>
                    <textarea name="description" rows="4"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($service['description'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- COLUMN 2: CHI TIẾT -->
            <div class="space-y-4 lg:space-y-6">
                <h3 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2">Chi tiết</h3>

                <!-- Unit -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                        Đơn vị tính
                    </label>
                    <select name="unit"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
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
                    <small class="text-xs text-primary-500 mt-1">Có thể để trống</small>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
                    <select name="status"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                        <option value="active" <?= ($service['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt
                            động</option>
                        <option value="inactive" <?= ($service['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Vô hiệu
                        </option>
                    </select>
                </div>

                <!-- Info Box -->
                <div class="bg-info-bg border border-info rounded-2xl p-4 lg:p-5">
                    <h4 class="font-semibold text-info-text mb-2 flex items-center gap-2">
                        <i data-lucide="lightbulb" class="w-4 h-4"></i>
                        Lưu ý
                    </h4>
                    <ul class="text-xs lg:text-sm text-info-text space-y-1 list-disc list-inside">
                        <li>Nhà cung cấp dịch vụ là bắt buộc</li>
                        <li>Tên dịch vụ là bắt buộc</li>
                        <li>Loại dịch vụ và đơn vị tính là tùy chọn</li>
                        <li>Giá dịch vụ sẽ được quản lý trong Service Prices</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-primary-100">
            <a href="?act=admin&module=services" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center">
                Hủy
            </a>
            <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                Cập nhật
            </button>
        </div>
    </form>
</div>