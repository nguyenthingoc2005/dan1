<?php
/**
 * ADMIN - DANH SÁCH CATEGORIES
 * Variables: $categories
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-6xl">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Quản lý danh mục</h1>
        <a href="?act=admin&module=categories&action=create"
            class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600">
            + Thêm mới
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">ID</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Tên danh mục</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Mô tả</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Thứ tự</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Trạng thái</th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-slate-700">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                            Chưa có danh mục nào.
                            <a href="?act=admin&module=categories&action=create" class="text-accent hover:underline">Thêm
                                mới</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-sm"><?= $cat['id'] ?></td>
                            <td class="px-4 py-3 text-sm font-medium"><?= htmlspecialchars($cat['name']) ?></td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                <?= htmlspecialchars(substr($cat['description'] ?? '', 0, 50)) ?>
                                <?= strlen($cat['description'] ?? '') > 50 ? '...' : '' ?>
                            </td>
                            <td class="px-4 py-3 text-sm"><?= $cat['display_order'] ?></td>
                            <td class="px-4 py-3 text-sm">
                                <?php if ($cat['status'] == 'active'): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Hoạt động</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">Vô hiệu</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <a href="?act=admin&module=categories&action=edit&id=<?= $cat['id'] ?>"
                                    class="text-blue-600 hover:text-blue-800 mr-3">
                                    ✏️ Sửa
                                </a>
                                <a href="?act=admin&module=categories&action=delete&id=<?= $cat['id'] ?>"
                                    onclick="return confirm('Xác nhận vô hiệu hóa danh mục này?')"
                                    class="text-red-600 hover:text-red-800">
                                    🗑️ Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Note -->
    <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r">
        <p class="text-sm text-blue-700">
            💡 <strong>Lưu ý:</strong> Hệ thống có 3 danh mục chính: Trong nước, Ngoài nước, Custom Tour
        </p>
    </div>
</div>