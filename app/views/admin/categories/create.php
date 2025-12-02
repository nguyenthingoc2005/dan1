<?php
/**
 * ADMIN - FORM TẠO CATEGORY
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-3xl">
    <h1 class="text-2xl font-bold text-primary mb-6">Thêm danh mục mới</h1>

    <form method="POST" action="?act=admin&module=categories&action=store" class="bg-white p-6 rounded">

        <!-- Tên danh mục -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tên danh mục <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" required
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                placeholder="VD: Trong nước, Ngoài nước">
        </div>

        <!-- Mô tả -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
            <textarea name="description" rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                placeholder="Mô tả ngắn về danh mục"></textarea>
        </div>

        <!-- Thứ tự hiển thị -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự hiển thị</label>
            <input type="number" name="display_order" value="0" min="0"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
            <small class="text-gray-500">Số nhỏ hơn hiển thị trước</small>
        </div>

        <!-- Trạng thái -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
            <select name="status"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                <option value="active">Hoạt động</option>
                <option value="inactive">Vô hiệu</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 mt-6">
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                Tạo danh mục
            </button>
            <a href="?act=admin&module=categories"
                class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Hủy
            </a>
        </div>
    </form>
</div>