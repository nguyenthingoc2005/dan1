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

<form method="POST" action="<?= $action_url ?>" class="space-y-6">
    <input type="hidden" name="service_provider_id" value="<?= $current_provider['id'] ?? '' ?>">
    <?php if ($is_edit): ?>
        <input type="hidden" name="id" value="<?= $service['id'] ?>">
    <?php endif; ?>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Loại dịch vụ *</label>
        <select name="service_type_id"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <option value="">-- Chọn loại dịch vụ --</option>
            <?php foreach ($service_types ?? [] as $st): ?>
                <option value="<?= $st['id'] ?>" <?= ($service['service_type_id'] ?? '') == $st['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($st['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Tên dịch vụ *</label>
        <input type="text" name="name" value="<?= htmlspecialchars($service['name'] ?? '') ?>"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="VD: Phòng Deluxe, Buffet sáng" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Mô tả</label>
        <textarea name="description"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3"
            placeholder="Mô tả về dịch vụ..."><?= htmlspecialchars($service['description'] ?? '') ?></textarea>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Đơn vị</label>
        <input type="text" name="unit" value="<?= htmlspecialchars($service['unit'] ?? '') ?>"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="VD: phòng, người, suất, vé">
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Ghi chú</label>
        <textarea name="notes"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2"
            placeholder="Ghi chú..."><?= htmlspecialchars($service['notes'] ?? '') ?></textarea>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Trạng thái *</label>
        <select name="status"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <option value="active" <?= ($service['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động
            </option>
            <option value="inactive" <?= ($service['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động
            </option>
        </select>
    </div>

    <div class="flex justify-end gap-2 pt-4 border-t border-gray-200">
        <?php if ($is_edit && $current_provider): ?>
            <a href="?act=admin&module=location-services&country_id=<?= $current_country['id'] ?? '' ?>&province_id=<?= $current_province['id'] ?? '' ?>&service_provider_id=<?= $current_provider['id'] ?>"
                class="px-6 py-2 bg-gray-300 text-gray-700 font-medium hover:bg-gray-400 transition-colors">
                Hủy
            </a>
        <?php else: ?>
            <a href="?act=admin&module=location-services"
                class="px-6 py-2 bg-gray-300 text-gray-700 font-medium hover:bg-gray-400 transition-colors">
                Hủy
            </a>
        <?php endif; ?>
        <button type="submit" class="px-6 py-2 bg-accent text-white font-medium hover:bg-blue-600 transition-colors">
            <?= $is_edit ? 'Cập nhật' : 'Tạo mới' ?>
        </button>
    </div>
</form>
