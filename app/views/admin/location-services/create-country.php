<?php
/**
 * Create Country Page
 */
?>

<div class="mb-4 lg:mb-6">
    <div class="flex flex-wrap items-center gap-2 text-xs lg:text-sm text-primary-500 mb-2 lg:mb-3">
        <a href="?act=admin&module=location-services" class="hover:text-accent font-semibold flex items-center gap-1">
            <i data-lucide="map-pin" class="w-3 h-3 lg:w-4 lg:h-4"></i>
            Địa điểm & Dịch vụ
        </a>
        <span>/</span>
        <span class="text-primary-700 font-semibold">Thêm Quốc gia</span>
    </div>
    <h1 class="text-xl lg:text-2xl font-bold text-primary-700 flex items-center gap-2">
        <i data-lucide="globe" class="w-5 h-5 lg:w-6 lg:h-6 text-accent"></i>
        Thêm Quốc gia
    </h1>
</div>

<div class="bg-panel p-4 lg:p-6 rounded-2xl border-l-4 border-accent shadow-sm">
    <form method="POST" action="?act=admin&module=location-services&action=store-country" class="space-y-4 lg:space-y-6">
        <!-- Section 1: Thông tin cơ bản -->
        <div>
            <h4 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4 pb-2 lg:pb-3 border-b border-primary-100">Thông tin cơ bản</h4>

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mã quốc gia <span class="text-danger">*</span> <span class="text-primary-500 text-xs">(VD: VN, US, TH)</span></label>
                <input type="text" name="code" value="<?= htmlspecialchars($_POST['code'] ?? '') ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base uppercase"
                    placeholder="VN" maxlength="10" required>
                <p class="text-xs text-primary-500 mt-1">Mã quốc gia sẽ được tự động chuyển thành chữ hoa</p>
            </div>

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tên quốc gia <span class="text-danger">*</span></label>
                <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                    placeholder="Việt Nam" maxlength="100" required>
            </div>
        </div>

        <!-- Section 2: Trạng thái -->
        <div>
            <h4 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4 pb-2 lg:pb-3 border-b border-primary-100">Trạng thái</h4>

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái <span class="text-danger">*</span></label>
                <select name="status"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                    required>
                    <option value="active" <?= ($_POST['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động
                    </option>
                    <option value="inactive" <?= ($_POST['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động</option>
                </select>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-end gap-2 lg:gap-3 pt-4 border-t border-primary-100">
            <a href="?act=admin&module=location-services"
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl font-semibold hover:bg-primary-100 transition-colors text-sm lg:text-base text-center">
                Hủy
            </a>
            <button type="submit"
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base">
                Tạo mới
            </button>
        </div>
    </form>
</div>