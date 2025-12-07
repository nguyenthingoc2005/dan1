<?php
/**
 * Create Country Page
 */
?>

<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="?act=admin&module=location-services" class="hover:text-blue-600">Địa điểm & Dịch vụ</a>
        <span>/</span>
        <span class="text-gray-700">Thêm Quốc gia</span>
    </div>
    <h1 class="text-2xl font-bold text-primary">Thêm Quốc gia</h1>
</div>

<div class="bg-white p-6 border-l-4 border-accent">
    <form method="POST" action="?act=admin&module=location-services&action=store-country" class="space-y-6">
        <!-- Section 1: Thông tin cơ bản -->
        <div>
            <h4 class="text-lg font-semibold mb-3 pb-2 border-b">Thông tin cơ bản</h4>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Mã quốc gia * <span class="text-gray-500 text-xs">(VD: VN,
                        US, TH)</span></label>
                <input type="text" name="code" value="<?= htmlspecialchars($_POST['code'] ?? '') ?>"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase"
                    placeholder="VN" maxlength="10" required>
                <p class="text-xs text-gray-500 mt-1">Mã quốc gia sẽ được tự động chuyển thành chữ hoa</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Tên quốc gia *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Việt Nam" maxlength="100" required>
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
                    <option value="active" <?= ($_POST['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động
                    </option>
                    <option value="inactive" <?= ($_POST['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt
                        động</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-4 border-t border-gray-200">
            <a href="?act=admin&module=location-services"
                class="px-6 py-2 bg-gray-300 text-gray-700 font-medium hover:bg-gray-400 transition-colors">
                Hủy
            </a>
            <button type="submit"
                class="px-6 py-2 bg-accent text-white font-medium hover:bg-blue-600 transition-colors">
                Tạo mới
            </button>
        </div>
    </form>
</div>