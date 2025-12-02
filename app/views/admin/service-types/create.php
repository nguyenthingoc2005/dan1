<?php
/**
 * ADMIN - FORM TẠO SERVICE TYPE
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <h1 class="text-2xl font-bold text-primary mb-6">Thêm loại dịch vụ mới</h1>

    <form method="POST" action="?act=admin&module=service-types&action=store" class="bg-white p-6 rounded">

        <!-- Tên loại dịch vụ -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tên loại dịch vụ <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" required
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                placeholder="VD: Khách sạn, Nhà hàng">
        </div>

        <!-- Mã Code -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Mã Code <span class="text-red-500">*</span>
            </label>
            <input type="text" name="code" required
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent font-mono uppercase"
                placeholder="VD: HOTEL, RESTAURANT" pattern="[A-Z0-9_]+" title="Chỉ chữ in hoa, số và dấu gạch dưới"
                oninput="this.value = this.value.toUpperCase()">
            <small class="text-gray-500">Chỉ chữ in hoa, số và dấu gạch dưới (_). VD: HOTEL, RESTAURANT,
                TOUR_GUIDE</small>
        </div>

        <!-- Mô tả -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
            <textarea name="description" rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                placeholder="Mô tả ngắn về loại dịch vụ"></textarea>
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
                Tạo loại dịch vụ
            </button>
            <a href="?act=admin&module=service-types"
                class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Hủy
            </a>
        </div>
    </form>
</div>