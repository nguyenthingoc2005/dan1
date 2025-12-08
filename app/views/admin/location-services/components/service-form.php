<?php
/**
 * Service Form Component
 * Used for both create and edit
 */
// Ensure variables are set
$current_provider = $current_provider ?? null;
$service = $service ?? null;

$is_edit = !empty($service);
$action_url = $is_edit
    ? '?act=admin&module=location-services&action=update-service&id=' . $service['id']
    : '?act=admin&module=location-services&action=store-service&service_provider_id=' . ($current_provider['id'] ?? '');
?>

<form method="POST" action="<?= $action_url ?>" class="space-y-4 lg:space-y-6">
    <input type="hidden" name="service_provider_id" value="<?= $current_provider['id'] ?? '' ?>">
    <?php if ($is_edit): ?>
        <input type="hidden" name="id" value="<?= $service['id'] ?>">
    <?php endif; ?>

    <div class="mb-3 lg:mb-4">
        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Loại dịch vụ <span class="text-danger">*</span></label>
        <select name="service_type_id"
            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base" required>
            <option value="">-- Chọn loại dịch vụ --</option>
            <?php foreach ($service_types ?? [] as $st): ?>
                <option value="<?= $st['id'] ?>" <?= ($service['service_type_id'] ?? '') == $st['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($st['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3 lg:mb-4">
        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tên dịch vụ <span class="text-danger">*</span></label>
        <input type="text" name="name" value="<?= htmlspecialchars($service['name'] ?? '') ?>"
            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
            placeholder="VD: Phòng Deluxe, Buffet sáng" required>
    </div>

    <div class="mb-3 lg:mb-4">
        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả</label>
        <textarea name="description"
            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base" rows="3"
            placeholder="Mô tả về dịch vụ..."><?= htmlspecialchars($service['description'] ?? '') ?></textarea>
    </div>

    <div class="mb-3 lg:mb-4">
        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Đơn vị</label>
        <input type="text" name="unit" value="<?= htmlspecialchars($service['unit'] ?? '') ?>"
            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
            placeholder="VD: phòng, người, suất, vé">
    </div>

    <div class="mb-3 lg:mb-4">
        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ghi chú</label>
        <textarea name="notes"
            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base" rows="2"
            placeholder="Ghi chú..."><?= htmlspecialchars($service['notes'] ?? '') ?></textarea>
    </div>

    <div class="mb-3 lg:mb-4">
        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái <span class="text-danger">*</span></label>
        <select name="status"
            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base" required>
            <option value="active" <?= ($service['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động
            </option>
            <option value="inactive" <?= ($service['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động
            </option>
        </select>
    </div>

    <div class="flex flex-col sm:flex-row justify-end gap-2 lg:gap-3 pt-4 border-t border-primary-100">
        <?php if ($is_edit && $current_provider): ?>
            <a href="?act=admin&module=location-services&country_id=<?= $current_country['id'] ?? '' ?>&province_id=<?= $current_province['id'] ?? '' ?>&service_provider_id=<?= $current_provider['id'] ?>"
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl font-semibold hover:bg-primary-100 transition-colors text-sm lg:text-base text-center">
                Hủy
            </a>
        <?php else: ?>
            <a href="?act=admin&module=location-services"
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl font-semibold hover:bg-primary-100 transition-colors text-sm lg:text-base text-center">
                Hủy
            </a>
        <?php endif; ?>
        <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base">
            <?= $is_edit ? 'Cập nhật' : 'Tạo mới' ?>
        </button>
    </div>
</form>
