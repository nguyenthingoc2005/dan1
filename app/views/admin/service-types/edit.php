<?php
/**
 * ADMIN - FORM SỬA SERVICE TYPE
 * Variables: $service_type
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <h1 class="text-2xl font-bold text-primary mb-6">Sửa loại dịch vụ</h1>

    <form method="POST" action="?act=admin&module=service-types&action=update" class="bg-white p-6 rounded">
        <input type="hidden" name="id" value="<?= $service_type['id'] ?>">

        <!-- Tên loại dịch vụ -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tên loại dịch vụ <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" value="<?= htmlspecialchars($service_type['name']) ?>" required
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
        </div>

        <!-- Mã Code - READ ONLY -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mã Code</label>
            <input type="text" value="<?= htmlspecialchars($service_type['code']) ?>" disabled
                class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 font-mono">
            <small class="text-gray-500">Mã code không thể thay đổi</small>
        </div>

        <!-- Mô tả -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
            <textarea name="description" rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"><?= htmlspecialchars($service_type['description'] ?? '') ?></textarea>
        </div>

        <!-- Trạng thái -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
            <select name="status"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                <option value="active" <?= ($service_type['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động
                </option>
                <option value="inactive" <?= ($service_type['status'] ?? 'active') == 'inactive' ? 'selected' : '' ?>>Vô
                    hiệu</option>
            </select>
        </div>

        <!-- Info -->
        <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded">
            <p class="text-sm text-gray-600">
                <strong>ID:</strong> <?= $service_type['id'] ?> |
                <strong>Tạo lúc:</strong> <?= date('d/m/Y H:i', strtotime($service_type['created_at'])) ?>
            </p>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 mt-6">
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                Cập nhật
            </button>
            <a href="?act=admin&module=service-types"
                class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Hủy
            </a>
        </div>
    </form>
</div>