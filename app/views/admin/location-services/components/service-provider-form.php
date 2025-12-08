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
    echo '<div class="bg-danger-bg border-l-4 border-danger rounded-xl p-4 mb-4">';
    echo '<p class="text-danger-dark font-bold flex items-center gap-2"><i data-lucide="alert-circle" class="w-4 h-4"></i>Lỗi: Thiếu thông tin quốc gia hoặc tỉnh thành.</p>';
    echo '<p class="text-danger-text text-xs lg:text-sm mt-1">Vui lòng quay lại và chọn quốc gia/tỉnh thành trước khi thêm nhà dịch vụ.</p>';
    echo '</div>';
    return;
}

$is_edit = !empty($provider);
$action_url = $is_edit
    ? '?act=admin&module=location-services&action=update-provider&id=' . $provider['id'] . '&country_id=' . $current_country_id . '&province_id=' . $current_province_id
    : '?act=admin&module=location-services&action=store-provider&country_id=' . $current_country_id . '&province_id=' . $current_province_id;
?>

<form method="POST" action="<?= $action_url ?>" class="space-y-4 lg:space-y-6">
    <input type="hidden" name="province_id" value="<?= (int) $current_province_id ?>">
    <input type="hidden" name="country_id" value="<?= (int) $current_country_id ?>">
    <?php if ($is_edit): ?>
        <input type="hidden" name="id" value="<?= $provider['id'] ?>">
    <?php endif; ?>

    <!-- Section 1: Thông tin cơ bản -->
    <div>
        <h4 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4 pb-2 lg:pb-3 border-b border-primary-100">Thông tin cơ bản</h4>

        <div class="mb-3 lg:mb-4">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tên nhà dịch vụ <span class="text-danger">*</span></label>
            <input type="text" name="name" value="<?= htmlspecialchars($provider['name'] ?? '') ?>"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                placeholder="VD: Khách sạn ABC, Nhà hàng XYZ" required>
            <p class="text-xs text-primary-500 mt-1">Lưu ý: Một nhà cung cấp có thể cung cấp nhiều loại dịch vụ. Loại dịch vụ sẽ được xác định ở từng dịch vụ cụ thể.</p>
        </div>

        <?php if ($is_edit && !empty($provider['service_code'])): ?>
            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mã nhà dịch vụ</label>
                <input type="text" value="<?= htmlspecialchars($provider['service_code']) ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-100 border border-primary-200 rounded-xl text-primary-600 text-sm lg:text-base" readonly>
            </div>
        <?php endif; ?>

        <div class="mb-3 lg:mb-4">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả</label>
            <textarea name="description"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base" rows="3"
                placeholder="Mô tả về nhà dịch vụ..."><?= htmlspecialchars($provider['description'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Section 2: Địa điểm -->
    <div>
        <h4 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4 pb-2 lg:pb-3 border-b border-primary-100">Địa điểm</h4>

        <div class="mb-3 lg:mb-4">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tỉnh/Thành phố <span class="text-danger">*</span></label>
            <input type="text" value="<?= htmlspecialchars($current_province['name'] ?? '') ?>"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-100 border border-primary-200 rounded-xl text-primary-600 text-sm lg:text-base" readonly>
        </div>

        <div class="mb-3 lg:mb-4">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Quốc gia <span class="text-danger">*</span></label>
            <input type="text" value="<?= htmlspecialchars($current_country['name'] ?? '') ?>"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-100 border border-primary-200 rounded-xl text-primary-600 text-sm lg:text-base" readonly>
        </div>

        <div class="mb-3 lg:mb-4">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Địa chỉ chi tiết</label>
            <textarea name="address"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base" rows="2"
                placeholder="Số nhà, đường, phường/xã..."><?= htmlspecialchars($provider['address'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Section 3: Thông tin liên hệ -->
    <div>
        <h4 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4 pb-2 lg:pb-3 border-b border-primary-100">Thông tin liên hệ</h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-4 mb-3 lg:mb-4">
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Người liên hệ</label>
                <input type="text" name="contact_person"
                    value="<?= htmlspecialchars($provider['contact_person'] ?? '') ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                    placeholder="Tên người liên hệ">
            </div>

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Điện thoại</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($provider['phone'] ?? '') ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                    placeholder="0901234567">
            </div>
        </div>

        <div class="mb-3 lg:mb-4">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($provider['email'] ?? '') ?>"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                placeholder="contact@example.com">
        </div>

        <div class="mb-3 lg:mb-4">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Website</label>
            <input type="url" name="website" value="<?= htmlspecialchars($provider['website'] ?? '') ?>"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                placeholder="https://example.com">
        </div>
    </div>

    <!-- Section 4: Trạng thái -->
    <div>
        <h4 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4 pb-2 lg:pb-3 border-b border-primary-100">Trạng thái</h4>

        <div class="mb-3 lg:mb-4">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái <span class="text-danger">*</span></label>
            <select name="status"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base" required>
                <option value="active" <?= ($provider['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động
                </option>
                <option value="inactive" <?= ($provider['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động
                </option>
            </select>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-end gap-2 lg:gap-3 pt-4 border-t border-primary-100">
        <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?>&province_id=<?= $current_province_id ?>"
            class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl font-semibold hover:bg-primary-100 transition-colors text-sm lg:text-base text-center">
            Hủy
        </a>
        <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base">
            <?= $is_edit ? 'Cập nhật' : 'Tạo mới' ?>
        </button>
    </div>
</form>