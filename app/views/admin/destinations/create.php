<?php
/**
 * ADMIN - FORM TẠO DESTINATION
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-primary mb-6">Thêm địa điểm mới</h1>

    <form method="POST" action="?act=admin&module=destinations&action=store" enctype="multipart/form-data"
        class="bg-white p-6 rounded shadow-sm">

        <!-- Basic Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tên địa điểm <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" required
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                    placeholder="VD: Vịnh Hạ Long">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                <select name="category_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($categories as $id => $name): ?>
                        <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả chi tiết</label>
            <textarea name="description" rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                placeholder="Giới thiệu về địa điểm..."></textarea>
        </div>

        <!-- Locations -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ / Vị trí</label>
            <input type="text" name="locations"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                placeholder="VD: Quảng Ninh, Việt Nam">
        </div>

        <!-- Images Upload -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh (Chọn nhiều)</label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition cursor-pointer"
                onclick="document.getElementById('images').click()">
                <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden"
                    onchange="previewImages(this)">
                <div class="text-4xl mb-2">📷</div>
                <p class="text-sm text-gray-500">Click để chọn ảnh hoặc kéo thả vào đây</p>
                <p class="text-xs text-gray-400 mt-1">Hỗ trợ JPG, PNG, WEBP (Max 5MB)</p>
            </div>

            <!-- Preview Container -->
            <div id="image-preview" class="grid grid-cols-4 gap-4 mt-4"></div>
        </div>

        <!-- Status -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
            <select name="status"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                <option value="active">Hoạt động</option>
                <option value="inactive">Vô hiệu</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 border-t pt-6">
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                Tạo địa điểm
            </button>
            <a href="?act=admin&module=destinations"
                class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Hủy
            </a>
        </div>
    </form>
</div>

<script>
    function previewImages(input) {
        const container = document.getElementById('image-preview');
        container.innerHTML = '';

        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const div = document.createElement('div');
                    div.className = 'relative aspect-video bg-gray-100 rounded overflow-hidden';
                    div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                `;
                    container.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        }
    }
</script>