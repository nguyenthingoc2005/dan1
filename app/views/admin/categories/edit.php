<?php
/**
 * ADMIN - FORM SỬA CATEGORY
 * Variables: $category
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-3xl">
    <h1 class="text-2xl font-bold text-primary mb-6">Sửa danh mục</h1>

    <form method="POST" action="?act=admin&module=categories&action=update" class="bg-white p-6 rounded">
        <input type="hidden" name="id" value="<?= $category['id'] ?>">

        <!-- Tên danh mục -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tên danh mục <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
        </div>

        <!-- Mô tả -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
            <textarea name="description" rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
        </div>

        <!-- Thứ tự hiển thị -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự hiển thị</label>
            <input type="number" name="display_order" value="<?= $category['display_order'] ?? 0 ?>" min="0"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
            <small class="text-gray-500">Số nhỏ hơn hiển thị trước</small>
        </div>

        <!-- Trạng thái -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
            <select name="status"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                <option value="active" <?= ($category['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động
                </option>
                <option value="inactive" <?= ($category['status'] ?? 'active') == 'inactive' ? 'selected' : '' ?>>Vô hiệu
                </option>
            </select>
        </div>

        <!-- Info -->
        <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded">
            <p class="text-sm text-gray-600">
                <strong>ID:</strong> <?= $category['id'] ?> |
                <strong>Tạo lúc:</strong> <?= date('d/m/Y H:i', strtotime($category['created_at'])) ?>
            </p>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 mt-6">
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                Cập nhật
            </button>
            <a href="?act=admin&module=categories"
                class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Hủy
            </a>
        </div>
    </form>
</div>