<?php
/**
 * ADMIN - FORM SỬA DESTINATION
 * Variables: $destination, $images, $categories
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-4xl mx-auto">
    <!-- Header - Responsive -->
    <div class="mb-4 lg:mb-6">
        <div class="flex items-center gap-2 mb-2">
            <a href="?act=admin&module=destinations" class="text-primary-500 hover:text-primary-700 font-semibold text-sm lg:text-base flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Sửa địa điểm</h1>
    </div>

    <form method="POST" action="?act=admin&module=destinations&action=update" enctype="multipart/form-data"
        class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100 space-y-4 lg:space-y-6">
        <input type="hidden" name="id" value="<?= $destination['id'] ?>">

        <!-- Basic Info -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Tên địa điểm <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" value="<?= htmlspecialchars($destination['name']) ?>" required
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
            </div>

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Danh mục</label>
                <select name="category_id"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($categories as $id => $name): ?>
                        <option value="<?= $id ?>" <?= $destination['category_id'] == $id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả chi tiết</label>
            <textarea name="description" rows="4"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($destination['description'] ?? '') ?></textarea>
        </div>

        <!-- Locations -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Địa chỉ / Vị trí</label>
            <input type="text" name="locations" value="<?= htmlspecialchars($destination['locations'] ?? '') ?>"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
        </div>

        <!-- Current Images -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-2">Hình ảnh hiện tại</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 lg:gap-4">
                <?php foreach ($images as $img): ?>
                    <div
                        class="relative group aspect-video bg-primary-100 rounded-xl overflow-hidden border-2 <?= $img['is_primary'] ? 'border-accent' : 'border-primary-100' ?>">
                        <img src="<?= htmlspecialchars($img['image_url']) ?>" class="w-full h-full object-cover">

                        <!-- Overlay Actions -->
                        <div
                            class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                            <?php if (!$img['is_primary']): ?>
                                <button type="button" class="p-1.5 bg-white rounded-xl text-success-text hover:bg-success-bg transition-all"
                                    title="Đặt làm ảnh đại diện">
                                    <i data-lucide="star" class="w-4 h-4"></i>
                                </button>
                                <button type="button" class="p-1.5 bg-white rounded-xl text-danger-text hover:bg-danger-bg transition-all"
                                    title="Xóa ảnh">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            <?php else: ?>
                                <span class="px-2 lg:px-3 py-1 bg-accent text-white text-xs rounded-xl font-semibold">Primary</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Upload New Images -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-2">Thêm ảnh mới</label>
            <div class="border-2 border-dashed border-primary-200 rounded-2xl p-4 lg:p-6 text-center hover:bg-primary-50 transition cursor-pointer"
                onclick="document.getElementById('images').click()">
                <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden"
                    onchange="previewImages(this)">
                <div class="mb-2 flex justify-center">
                    <i data-lucide="image" class="w-12 h-12 text-primary-400"></i>
                </div>
                <p class="text-xs lg:text-sm text-primary-600">Click để chọn ảnh hoặc kéo thả vào đây</p>
            </div>
            <div id="image-preview" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 lg:gap-4 mt-4"></div>
        </div>

        <!-- Status -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
            <select name="status"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                <option value="active" <?= $destination['status'] == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                <option value="inactive" <?= $destination['status'] == 'inactive' ? 'selected' : '' ?>>Vô hiệu</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-primary-100">
            <a href="?act=admin&module=destinations"
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

<script>
    function previewImages(input) {
        const container = document.getElementById('image-preview');
        container.innerHTML = '';

        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const div = document.createElement('div');
                    div.className = 'relative aspect-video bg-primary-100 rounded-xl overflow-hidden border border-primary-100';
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