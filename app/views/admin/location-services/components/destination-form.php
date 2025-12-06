<?php
/**
 * Destination Form Component
 * Used for both create and edit
 */
// Ensure variables are set
$current_country_id = $current_country_id ?? null;
$current_province_id = $current_province_id ?? null;

$is_edit = !empty($destination);
$action_url = $is_edit
    ? '?act=admin&module=location-services&action=update-destination&id=' . $destination['id'] . '&country_id=' . $current_country_id . '&province_id=' . $current_province_id
    : '?act=admin&module=location-services&action=store-destination&country_id=' . $current_country_id . '&province_id=' . $current_province_id;
?>

<form method="POST" action="<?= $action_url ?>" class="space-y-6">
    <input type="hidden" name="province_id" value="<?= $current_province_id ?>">
    <input type="hidden" name="country_id" value="<?= $current_country_id ?>">
    <?php if ($is_edit): ?>
        <input type="hidden" name="id" value="<?= $destination['id'] ?>">
    <?php endif; ?>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Tên địa điểm *</label>
        <input type="text" name="name" value="<?= htmlspecialchars($destination['name'] ?? '') ?>"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="VD: Hồ Xuân Hương, Chợ Đà Lạt" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Tỉnh/Thành phố *</label>
        <input type="text" value="<?= htmlspecialchars($current_province['name'] ?? '') ?>"
            class="w-full px-3 py-2 border rounded bg-gray-100" readonly>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Quốc gia *</label>
        <input type="text" value="<?= htmlspecialchars($current_country['name'] ?? '') ?>"
            class="w-full px-3 py-2 border rounded bg-gray-100" readonly>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Mô tả</label>
        <textarea name="description"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4"
            placeholder="Mô tả về địa điểm..."><?= htmlspecialchars($destination['description'] ?? '') ?></textarea>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Vị trí cụ thể</label>
        <textarea name="locations"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2"
            placeholder="Số nhà, đường, phường/xã..."><?= htmlspecialchars($destination['locations'] ?? '') ?></textarea>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Trạng thái *</label>
        <select name="status"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <option value="active" <?= ($destination['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động
            </option>
            <option value="inactive" <?= ($destination['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động
            </option>
        </select>
    </div>

    <div class="flex justify-end gap-2 pt-4 border-t">
        <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?>&province_id=<?= $current_province_id ?>&tab=destinations"
            class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
            Hủy
        </a>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            <?= $is_edit ? 'Cập nhật' : 'Tạo mới' ?>
        </button>
    </div>
</form>