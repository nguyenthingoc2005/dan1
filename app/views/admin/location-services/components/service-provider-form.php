<?php
/**
 * Service Provider Form Component
 * Used for both create and edit
 */
// Ensure variables are set
$current_country_id = $current_country_id ?? null;
$current_province_id = $current_province_id ?? null;

$is_edit = !empty($provider);
$action_url = $is_edit
    ? '?act=admin&module=location-services&action=update-provider&id=' . $provider['id'] . '&country_id=' . $current_country_id . '&province_id=' . $current_province_id
    : '?act=admin&module=location-services&action=store-provider&country_id=' . $current_country_id . '&province_id=' . $current_province_id;
?>

<form method="POST" action="<?= $action_url ?>" class="space-y-6">
    <input type="hidden" name="province_id" value="<?= $current_province_id ?>">
    <input type="hidden" name="country_id" value="<?= $current_country_id ?>">
    <?php if ($is_edit): ?>
        <input type="hidden" name="id" value="<?= $provider['id'] ?>">
    <?php endif; ?>

    <!-- Section 1: Thông tin cơ bản -->
    <div>
        <h4 class="text-lg font-semibold mb-3 pb-2 border-b">Thông tin cơ bản</h4>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Tên nhà dịch vụ *</label>
            <input type="text" name="name" value="<?= htmlspecialchars($provider['name'] ?? '') ?>"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="VD: Khách sạn ABC, Nhà hàng XYZ" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Loại dịch vụ (Service Type)</label>
            <select name="service_type_id"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Chọn loại dịch vụ (tùy chọn) --</option>
                <?php foreach ($service_types ?? [] as $st): ?>
                    <option value="<?= $st['id'] ?>" <?= ($provider['service_type_id'] ?? '') == $st['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($st['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ($is_edit && !empty($provider['service_code'])): ?>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Mã nhà dịch vụ</label>
                <input type="text" value="<?= htmlspecialchars($provider['service_code']) ?>"
                    class="w-full px-3 py-2 border rounded bg-gray-50" readonly>
            </div>
        <?php endif; ?>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Mô tả</label>
            <textarea name="description"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3"
                placeholder="Mô tả về nhà dịch vụ..."><?= htmlspecialchars($provider['description'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Section 2: Địa điểm -->
    <div>
        <h4 class="text-lg font-semibold mb-3 pb-2 border-b">Địa điểm</h4>

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
            <label class="block text-sm font-medium mb-1">Địa chỉ chi tiết</label>
            <textarea name="address"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2"
                placeholder="Số nhà, đường, phường/xã..."><?= htmlspecialchars($provider['address'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Section 3: Thông tin liên hệ -->
    <div>
        <h4 class="text-lg font-semibold mb-3 pb-2 border-b">Thông tin liên hệ</h4>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Người liên hệ</label>
                <input type="text" name="contact_person"
                    value="<?= htmlspecialchars($provider['contact_person'] ?? '') ?>"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Tên người liên hệ">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Điện thoại</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($provider['phone'] ?? '') ?>"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="0901234567">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($provider['email'] ?? '') ?>"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="contact@example.com">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Website</label>
            <input type="url" name="website" value="<?= htmlspecialchars($provider['website'] ?? '') ?>"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="https://example.com">
        </div>
    </div>

    <!-- Section 4: Trạng thái -->
    <div>
        <h4 class="text-lg font-semibold mb-3 pb-2 border-b">Trạng thái</h4>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Trạng thái *</label>
            <select name="status"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <option value="active" <?= ($provider['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động
                </option>
                <option value="inactive" <?= ($provider['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động
                </option>
            </select>
        </div>
    </div>

    <div class="flex justify-end gap-2 pt-4 border-t">
        <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?>&province_id=<?= $current_province_id ?>"
            class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
            Hủy
        </a>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            <?= $is_edit ? 'Cập nhật' : 'Tạo mới' ?>
        </button>
    </div>
</form>