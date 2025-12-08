<?php
/**
 * ADMIN - DANH SÁCH CATEGORIES
 * Variables: $categories
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý danh mục</h1>
        <a href="?act=admin&module=categories&action=create"
            class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Thêm mới
        </a>
    </div>

    <!-- Table -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse min-w-[600px]">
                <thead class="bg-primary-50 border-b border-primary-100">
                <tr>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">ID</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Tên danh mục</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Mô tả</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Thứ tự</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Trạng thái</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-right text-xs uppercase font-semibold text-primary-700 tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary-100">
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="6" class="px-3 lg:px-4 py-6 lg:py-8 text-center text-primary-500 text-sm">
                            Chưa có danh mục nào.
                            <a href="?act=admin&module=categories&action=create" class="text-accent hover:text-accent-hover font-semibold ml-2">Thêm
                                mới</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr class="hover:bg-primary-50 transition-colors">
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-700"><?= $cat['id'] ?></td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm font-semibold text-primary-700"><?= htmlspecialchars($cat['name']) ?></td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-600">
                                <?= htmlspecialchars(substr($cat['description'] ?? '', 0, 50)) ?>
                                <?= strlen($cat['description'] ?? '') > 50 ? '...' : '' ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-700"><?= $cat['display_order'] ?></td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm">
                                <?php if ($cat['status'] == 'active'): ?>
                                    <span class="px-2 lg:px-3 py-1 bg-success-bg text-success-text rounded-full text-xs font-bold uppercase">Hoạt động</span>
                                <?php else: ?>
                                    <span class="px-2 lg:px-3 py-1 bg-primary-100 text-primary-500 rounded-full text-xs font-bold uppercase">Vô hiệu</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-right">
                                <div class="flex items-center justify-end gap-1 lg:gap-2">
                                    <a href="?act=admin&module=categories&action=edit&id=<?= $cat['id'] ?>"
                                        class="text-warning-text hover:text-warning-text p-1.5 rounded-xl hover:bg-warning-bg transition-all" title="Sửa">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <a href="?act=admin&module=categories&action=delete&id=<?= $cat['id'] ?>"
                                        onclick="return confirm('Xác nhận vô hiệu hóa danh mục này?')"
                                        class="text-danger-text hover:text-danger-text p-1.5 rounded-xl hover:bg-danger-bg transition-all" title="Xóa">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>

    <!-- Note -->
    <div class="mt-4 p-4 lg:p-5 bg-info-bg border-l-4 border-info rounded-r-xl">
        <p class="text-xs lg:text-sm text-info-text flex items-start gap-2">
            <i data-lucide="lightbulb" class="w-4 h-4 flex-shrink-0 mt-0.5"></i>
            <span><strong>Lưu ý:</strong> Hệ thống có 3 danh mục chính: Trong nước, Ngoài nước, Custom Tour</span>
        </p>
    </div>
</div>