<?php
/**
 * ADMIN - FORM TẠO MÃ GIẢM GIÁ
 */

if (!is_admin())
    redirect('?act=access-denied');
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>

<div class="max-w-4xl mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Thêm mã giảm giá mới</h1>
        <a href="?act=admin&module=discount-codes" class="text-primary-500 hover:text-primary-700 font-semibold text-sm lg:text-base flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    <form method="POST" action="?act=admin&module=discount-codes&action=store"
        class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6 space-y-4 lg:space-y-6">
        <?= csrf_field() ?>

        <!-- Mã giảm giá -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                Mã giảm giá <span class="text-danger">*</span>
            </label>
            <input type="text" name="code" required value="<?= htmlspecialchars($old['code'] ?? '') ?>"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base uppercase"
                placeholder="VD: WELCOME10, SUMMER2025" maxlength="50"
                oninput="this.value = this.value.toUpperCase()">
            <small class="text-xs text-primary-500 mt-1 block">Mã sẽ được tự động chuyển thành chữ hoa</small>
        </div>

        <!-- Tên mã -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                Tên mã giảm giá
            </label>
            <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                placeholder="VD: Giảm 10% cho khách hàng mới">
            <small class="text-xs text-primary-500 mt-1 block">Mô tả ngắn về mã giảm giá (tùy chọn)</small>
        </div>

        <!-- Loại giảm giá -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Loại giảm giá <span class="text-danger">*</span>
                </label>
                <select name="discount_type" required id="discount_type"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base"
                    onchange="updateDiscountValueLabel()">
                    <option value="">-- Chọn loại --</option>
                    <option value="percentage" <?= ($old['discount_type'] ?? '') == 'percentage' ? 'selected' : '' ?>>Phần trăm (%)</option>
                    <option value="fixed" <?= ($old['discount_type'] ?? '') == 'fixed' ? 'selected' : '' ?>>Số tiền cố định (VNĐ)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Giá trị giảm <span class="text-danger">*</span>
                </label>
                <div class="relative">
                    <input type="number" name="discount_value" required value="<?= htmlspecialchars($old['discount_value'] ?? '') ?>"
                        id="discount_value"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base"
                        placeholder="0" min="0" step="0.01">
                    <span id="discount_unit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-primary-500 text-sm">%</span>
                </div>
                <small class="text-xs text-primary-500 mt-1 block" id="discount_hint">
                    Nhập phần trăm (0-100)
                </small>
            </div>
        </div>

        <!-- Giá trị đơn hàng tối thiểu -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                Giá trị đơn hàng tối thiểu (VNĐ)
            </label>
            <input type="number" name="min_purchase" value="<?= htmlspecialchars($old['min_purchase'] ?? '0') ?>"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                placeholder="0" min="0" step="1000">
            <small class="text-xs text-primary-500 mt-1 block">Để trống hoặc 0 = không giới hạn</small>
        </div>

        <!-- Thời gian hiệu lực -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Ngày bắt đầu
                </label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($old['start_date'] ?? '') ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                <small class="text-xs text-primary-500 mt-1 block">Để trống = có hiệu lực ngay</small>
            </div>

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Ngày kết thúc
                </label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($old['end_date'] ?? '') ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                <small class="text-xs text-primary-500 mt-1 block">Để trống = không giới hạn</small>
            </div>
        </div>

        <!-- Giới hạn số lần sử dụng -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                Giới hạn số lần sử dụng
            </label>
            <input type="number" name="usage_limit" value="<?= htmlspecialchars($old['usage_limit'] ?? '0') ?>"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                placeholder="0" min="0" step="1">
            <small class="text-xs text-primary-500 mt-1 block">0 = không giới hạn</small>
        </div>

        <!-- Trạng thái -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
            <select name="status"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                <option value="active" selected>Hoạt động</option>
                <option value="inactive" <?= ($old['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Vô hiệu</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-primary-100">
            <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                Tạo mã giảm giá
            </button>
            <a href="?act=admin&module=discount-codes" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center">
                Hủy
            </a>
        </div>
    </form>
</div>

<script>
function updateDiscountValueLabel() {
    const type = document.getElementById('discount_type').value;
    const unit = document.getElementById('discount_unit');
    const hint = document.getElementById('discount_hint');
    const input = document.getElementById('discount_value');

    if (type === 'percentage') {
        unit.textContent = '%';
        hint.textContent = 'Nhập phần trăm (0-100)';
        input.max = 100;
        input.step = '0.01';
    } else if (type === 'fixed') {
        unit.textContent = 'VNĐ';
        hint.textContent = 'Nhập số tiền giảm (VNĐ)';
        input.max = null;
        input.step = '1000';
    } else {
        unit.textContent = '';
        hint.textContent = 'Chọn loại giảm giá trước';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateDiscountValueLabel();
});
</script>

