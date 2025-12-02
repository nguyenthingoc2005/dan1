<?php
/**
 * ADMIN - FORM SỬA DESTINATION
 * Variables: $destination, $images, $categories
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-primary mb-6">Sửa địa điểm</h1>

    <form method="POST" action="?act=admin&module=destinations&action=update" enctype="multipart/form-data"
        class="bg-white p-6 rounded shadow-sm">
        <input type="hidden" name="id" value="<?= $destination['id'] ?>">

        <!-- Basic Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tên địa điểm <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="<?= htmlspecialchars($destination['name']) ?>" required
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                <select name="category_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($categories as $id => $name): ?>
                        <option value="<?= $id ?>" <?= $destination['category_id'] == $id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả chi tiết</label>
            <textarea name="description" rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"><?= htmlspecialchars($destination['description'] ?? '') ?></textarea>
        </div>

        <!-- Locations -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ / Vị trí</label>
            <input type="text" name="locations" value="<?= htmlspecialchars($destination['locations'] ?? '') ?>"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
        </div>

        <!-- Current Images -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh hiện tại</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php foreach ($images as $img): ?>
                    <div
                        class="relative group aspect-video bg-gray-100 rounded overflow-hidden border <?= $img['is_primary'] ? 'border-2 border-accent' : 'border-gray-200' ?>">
                        <img src="<?= htmlspecialchars($img['image_url']) ?>" class="w-full h-full object-cover">

                        <!-- Overlay Actions -->
                        <div
                            class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                            <?php if (!$img['is_primary']): ?>
                                <button type="button" class="p-1 bg-white rounded-full text-green-600 hover:bg-green-50"
                                    title="Đặt làm ảnh đại diện">
                                    ⭐
                                </button>
                                <button type="button" class="p-1 bg-white rounded-full text-red-600 hover:bg-red-50"
                                    title="Xóa ảnh">
                                    🗑️
                                </button>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-accent text-white text-xs rounded">Primary</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Upload New Images -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Thêm ảnh mới</label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition cursor-pointer"
                onclick="document.getElementById('images').click()">
                <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden"
                    onchange="previewImages(this)">
                <div class="text-4xl mb-2">📷</div>
                <p class="text-sm text-gray-500">Click để chọn ảnh hoặc kéo thả vào đây</p>
            </div>
            <div id="image-preview" class="grid grid-cols-4 gap-4 mt-4"></div>
        </div>

        <!-- Status -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
            <select name="status"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                <option value="active" <?= $destination['status'] == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                <option value="inactive" <?= $destination['status'] == 'inactive' ? 'selected' : '' ?>>Vô hiệu</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 border-t pt-6">
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                Cập nhật
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