<?php
/**
 * ADMIN - ADD SERVICE TO SUPPLIER
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-4xl mx-auto p-4 lg:p-8">
    <!-- Header - Responsive -->
    <div class="mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Thêm dịch vụ</h1>
        <p class="text-xs lg:text-sm text-primary-500 mt-1">
            Nhà cung cấp: <strong class="text-primary-700"><?= htmlspecialchars($supplier['company_name']) ?></strong>
        </p>
    </div>

    <form method="POST" action="?act=admin&module=supplier-services&action=store-service"
        class="bg-panel p-4 lg:p-6 rounded-2xl border border-primary-100 shadow-sm">
        <input type="hidden" name="supplier_id" value="<?= $supplier['id'] ?>">

        <div class="space-y-4 lg:space-y-6">
            <!-- Service Type -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Loại dịch vụ <span class="text-danger">*</span>
                </label>
                <select name="service_type_id" required
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Chọn loại dịch vụ --</option>
                    <?php foreach ($service_types as $type): ?>
                        <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Service Name -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Tên dịch vụ <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" required
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                    placeholder="VD: Phòng Deluxe">
            </div>

            <!-- Unit -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Đơn vị tính</label>
                <input type="text" name="unit"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                    placeholder="VD: phòng/đêm, suất, xe/ngày">
            </div>

            <!-- Price -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Giá dự kiến (VNĐ)</label>
                <input type="number" name="estimated_price" min="0" step="1000"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base" placeholder="0">
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ghi chú</label>
                <textarea name="notes" rows="3"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                    placeholder="Ghi chú về dịch vụ"></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-primary-100">
                <a href="?act=admin&module=supplier-services"
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base text-center">
                    Hủy
                </a>
                <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Thêm dịch vụ
                </button>
            </div>
        </div>
    </form>
</div>