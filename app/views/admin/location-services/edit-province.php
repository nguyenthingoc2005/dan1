<?php
/**
 * Edit Province Page
 */
if (empty($province)) {
    redirect('?act=admin&module=location-services');
}
?>

<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="?act=admin&module=location-services" class="hover:text-blue-600">Địa điểm & Dịch vụ</a>
        <span>/</span>
        <a href="?act=admin&module=location-services&country_id=<?= $province['country_id'] ?>"
            class="hover:text-blue-600">
            <?= htmlspecialchars($current_country['name'] ?? 'Quốc gia') ?>
        </a>
        <span>/</span>
        <a href="?act=admin&module=location-services&country_id=<?= $province['country_id'] ?>&province_id=<?= $province['id'] ?>"
            class="hover:text-blue-600">
            <?= htmlspecialchars($province['name']) ?>
        </a>
        <span>/</span>
        <span class="text-gray-700">Sửa</span>
    </div>
    <h1 class="text-2xl font-bold text-primary">Sửa Tỉnh thành</h1>
</div>

<div class="bg-white p-6 border-l-4 border-accent">
    <form method="POST" action="?act=admin&module=location-services&action=update-province" class="space-y-6">
        <input type="hidden" name="id" value="<?= $province['id'] ?>">
        <input type="hidden" name="country_id" value="<?= $province['country_id'] ?>">

        <!-- Section 1: Thông tin cơ bản -->
        <div>
            <h4 class="text-lg font-semibold mb-3 pb-2 border-b">Thông tin cơ bản</h4>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Quốc gia</label>
                <input type="text" value="<?= htmlspecialchars($current_country['name'] ?? '') ?>"
                    class="w-full px-3 py-2 border rounded bg-gray-100" readonly>
            </div>

            <?php if (!empty($province['code'])): ?>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Mã tỉnh thành</label>
                    <input type="text" value="<?= htmlspecialchars($province['code']) ?>"
                        class="w-full px-3 py-2 border rounded bg-gray-50" readonly>
                    <p class="text-xs text-gray-500 mt-1">Mã tỉnh thành không thể thay đổi sau khi tạo</p>
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Tên tỉnh thành *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($province['name']) ?>"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Hà Nội" maxlength="100" required>
            </div>
        </div>

        <!-- Section 2: Trạng thái -->
        <div>
            <h4 class="text-lg font-semibold mb-3 pb-2 border-b">Trạng thái</h4>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Trạng thái *</label>
                <select name="status"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                    <option value="active" <?= $province['status'] == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="inactive" <?= $province['status'] == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động
                    </option>
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-4 border-t border-gray-200">
            <a href="?act=admin&module=location-services&country_id=<?= $province['country_id'] ?>&province_id=<?= $province['id'] ?>"
                class="px-6 py-2 bg-gray-300 text-gray-700 font-medium hover:bg-gray-400 transition-colors">
                Hủy
            </a>
            <button type="submit"
                class="px-6 py-2 bg-accent text-white font-medium hover:bg-blue-600 transition-colors">
                Cập nhật
            </button>
        </div>
    </form>
</div>