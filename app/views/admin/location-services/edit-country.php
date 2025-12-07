<?php
/**
 * Edit Country Page
 */
if (empty($country)) {
    redirect('?act=admin&module=location-services');
}
?>

<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="?act=admin&module=location-services" class="hover:text-blue-600">Địa điểm & Dịch vụ</a>
        <span>/</span>
        <a href="?act=admin&module=location-services&country_id=<?= $country['id'] ?>" class="hover:text-blue-600">
            <?= htmlspecialchars($country['name']) ?>
        </a>
        <span>/</span>
        <span class="text-gray-700">Sửa</span>
    </div>
    <h1 class="text-2xl font-bold text-primary">Sửa Quốc gia</h1>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <form method="POST" action="?act=admin&module=location-services&action=update-country" class="space-y-6">
        <input type="hidden" name="id" value="<?= $country['id'] ?>">

        <!-- Section 1: Thông tin cơ bản -->
        <div>
            <h4 class="text-lg font-semibold mb-3 pb-2 border-b">Thông tin cơ bản</h4>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Mã quốc gia</label>
                <input type="text" value="<?= htmlspecialchars($country['code']) ?>"
                    class="w-full px-3 py-2 border rounded bg-gray-50" readonly>
                <p class="text-xs text-gray-500 mt-1">Mã quốc gia không thể thay đổi sau khi tạo</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Tên quốc gia (Tiếng Việt) *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($country['name']) ?>"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Việt Nam" maxlength="100" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Tên quốc gia (Tiếng Anh) <span
                        class="text-gray-500 text-xs">(Tùy chọn)</span></label>
                <input type="text" name="name_en" value="<?= htmlspecialchars($country['name_en'] ?? '') ?>"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Vietnam" maxlength="100">
            </div>
        </div>

        <!-- Section 2: Sắp xếp và trạng thái -->
        <div>
            <h4 class="text-lg font-semibold mb-3 pb-2 border-b">Sắp xếp và trạng thái</h4>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Thứ tự hiển thị</label>
                    <input type="number" name="display_order"
                        value="<?= htmlspecialchars($country['display_order'] ?? 0) ?>"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        min="0">
                    <p class="text-xs text-gray-500 mt-1">Số nhỏ hơn sẽ hiển thị trước</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Trạng thái *</label>
                    <select name="status"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="active" <?= $country['status'] == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="inactive" <?= $country['status'] == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-4 border-t">
            <a href="?act=admin&module=location-services&country_id=<?= $country['id'] ?>"
                class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                Hủy
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Cập nhật
            </button>
        </div>
    </form>
</div>