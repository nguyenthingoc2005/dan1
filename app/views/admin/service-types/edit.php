<?php
/**
 * ADMIN - FORM SỬA SERVICE TYPE
 * Variables: $service_type
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-4xl mx-auto p-4 lg:p-8">
    <!-- Header - Responsive -->
    <div class="mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Sửa loại dịch vụ</h1>
    </div>

    <form method="POST" action="?act=admin&module=service-types&action=update" class="bg-panel p-4 lg:p-6 rounded-2xl border border-primary-100 shadow-sm">
        <input type="hidden" name="id" value="<?= $service_type['id'] ?>">

        <div class="space-y-4 lg:space-y-6">
            <!-- Tên loại dịch vụ -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Tên loại dịch vụ <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" value="<?= htmlspecialchars($service_type['name']) ?>" required
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
            </div>

            <!-- Mô tả -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả</label>
                <textarea name="description" rows="3"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($service_type['description'] ?? '') ?></textarea>
            </div>

            <!-- Trạng thái -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
                <select name="status"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    <option value="active" <?= ($service_type['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="inactive" <?= ($service_type['status'] ?? 'active') == 'inactive' ? 'selected' : '' ?>>Vô hiệu</option>
                </select>
            </div>

            <!-- Info -->
            <div class="p-3 lg:p-4 bg-primary-50 border border-primary-100 rounded-xl">
                <p class="text-xs lg:text-sm text-primary-600">
                    <strong>ID:</strong> <?= $service_type['id'] ?> |
                    <strong>Tạo lúc:</strong> <?= date('d/m/Y H:i', strtotime($service_type['created_at'])) ?>
                    <?php if (!empty($service_type['creator_name'])): ?>
                        | <strong>Người tạo:</strong> <?= htmlspecialchars($service_type['creator_name']) ?>
                    <?php elseif (!empty($service_type['created_by'])): ?>
                        | <strong>Người tạo:</strong> ID <?= $service_type['created_by'] ?>
                    <?php endif; ?>
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-primary-100">
                <a href="?act=admin&module=service-types"
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base text-center">
                    Hủy
                </a>
                <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Cập nhật
                </button>
            </div>
        </div>
    </form>
</div>