<?php
/**
 * ADMIN - FORM SỬA CATEGORY
 * Variables: $category
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-3xl mx-auto">
    <!-- Header - Responsive -->
    <div class="mb-4 lg:mb-6">
        <div class="flex items-center gap-2 mb-2">
            <a href="?act=admin&module=categories" class="text-primary-500 hover:text-primary-700 font-semibold text-sm lg:text-base flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Sửa danh mục</h1>
    </div>

    <form method="POST" action="?act=admin&module=categories&action=update" class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100 space-y-4 lg:space-y-6">
        <input type="hidden" name="id" value="<?= $category['id'] ?>">

        <!-- Tên danh mục -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                Tên danh mục <span class="text-danger">*</span>
            </label>
            <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
        </div>

        <!-- Mô tả -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả</label>
            <textarea name="description" rows="3"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
        </div>

        <!-- Thứ tự hiển thị -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Thứ tự hiển thị</label>
            <input type="number" name="display_order" value="<?= $category['display_order'] ?? 0 ?>" min="0"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
            <small class="text-xs text-primary-500 mt-1">Số nhỏ hơn hiển thị trước</small>
        </div>

        <!-- Trạng thái -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
            <select name="status"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                <option value="active" <?= ($category['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động
                </option>
                <option value="inactive" <?= ($category['status'] ?? 'active') == 'inactive' ? 'selected' : '' ?>>Vô hiệu
                </option>
            </select>
        </div>

        <!-- Info -->
        <div class="p-3 lg:p-4 bg-primary-50 border border-primary-100 rounded-2xl">
            <p class="text-xs lg:text-sm text-primary-600">
                <strong>ID:</strong> <?= $category['id'] ?> |
                <strong>Tạo lúc:</strong> <?= date('d/m/Y H:i', strtotime($category['created_at'])) ?>
            </p>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-primary-100">
            <a href="?act=admin&module=categories"
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center">
                Hủy
            </a>
            <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                Cập nhật
            </button>
        </div>
    </form>
</div>