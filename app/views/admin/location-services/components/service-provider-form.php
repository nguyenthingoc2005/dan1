<?php
/**
 * Service Provider Form Component
 * Used for both create and edit
 */
// Ensure variables are set - ưu tiên lấy từ provider nếu đang edit
if (!empty($provider)) {
    $current_country_id = $current_country_id ?? $provider['country_id'] ?? null;
    $current_province_id = $current_province_id ?? $provider['province_id'] ?? null;
} else {
    $current_country_id = $current_country_id ?? null;
    $current_province_id = $current_province_id ?? null;
}

// Validate required fields
if (empty($current_country_id) || empty($current_province_id)) {
    echo '<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">';
    echo '<p class="text-red-700 font-medium">Lỗi: Thiếu thông tin quốc gia hoặc tỉnh thành.</p>';
    echo '<p class="text-red-600 text-sm mt-1">Vui lòng quay lại và chọn quốc gia/tỉnh thành trước khi thêm nhà dịch vụ.</p>';
    echo '</div>';
    return;
}

$is_edit = !empty($provider);
$action_url = $is_edit
    ? '?act=admin&module=location-services&action=update-provider&id=' . $provider['id'] . '&country_id=' . $current_country_id . '&province_id=' . $current_province_id
    : '?act=admin&module=location-services&action=store-provider&country_id=' . $current_country_id . '&province_id=' . $current_province_id;
?>

<form method="POST" action="<?= $action_url ?>" class="space-y-6">
    <input type="hidden" name="province_id" value="<?= (int) $current_province_id ?>">
    <input type="hidden" name="country_id" value="<?= (int) $current_country_id ?>">
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
            <p class="text-xs text-gray-500 mt-1">Lưu ý: Một nhà cung cấp có thể cung cấp nhiều loại dịch vụ. Loại dịch
                vụ sẽ được xác định ở từng dịch vụ cụ thể.</p>
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

    <div class="flex justify-end gap-2 pt-4 border-t border-gray-200">
        <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?>&province_id=<?= $current_province_id ?>"
            class="px-6 py-2 bg-gray-300 text-gray-700 font-medium hover:bg-gray-400 transition-colors">
            Hủy
        </a>
        <button type="submit" class="px-6 py-2 bg-accent text-white font-medium hover:bg-blue-600 transition-colors">
            <?= $is_edit ? 'Cập nhật' : 'Tạo mới' ?>
        </button>
    </div>
</form>