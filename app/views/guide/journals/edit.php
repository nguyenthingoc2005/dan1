<?php
/**
 * GUIDE - SỬA NHẬT KÝ TOUR
 * Variables: $journal, $schedule, $images
 */
?>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Sửa Nhật ký</h1>
        <a href="?act=guide-journals&action=show&id=<?= $journal['id'] ?>" class="text-gray-500 hover:text-gray-700">
            ← Quay lại
        </a>
    </div>

    <form method="POST" action="?act=guide-journals&action=update" enctype="multipart/form-data"
        class="bg-white rounded-lg shadow-sm overflow-hidden">
        <input type="hidden" name="id" value="<?= $journal['id'] ?>">
        
        <div class="p-6 space-y-6">
            <!-- Tour Info (Read-only) -->
            <div class="bg-gray-50 p-4 rounded border">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tour</label>
                <p class="text-gray-900 font-medium">
                    <?= htmlspecialchars($journal['tour_name']) ?> 
                    (<?= date('d/m/Y', strtotime($schedule['start_date'])) ?>)
                </p>
            </div>

            <!-- Title -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tiêu đề <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" required
                    value="<?= htmlspecialchars($journal['title']) ?>"
                    class="w-full px-3 py-2 border rounded focus:border-blue-500 focus:outline-none">
            </div>

            <!-- Content -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nội dung <span class="text-red-500">*</span>
                </label>
                <textarea name="content" id="content" rows="12" required
                    class="w-full px-3 py-2 border rounded focus:border-blue-500 focus:outline-none"><?= htmlspecialchars($journal['content']) ?></textarea>
            </div>

            <!-- Existing Images -->
            <?php if (!empty($images)): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Hình ảnh hiện tại
                    </label>
                    <div class="grid grid-cols-4 gap-3">
                        <?php foreach ($images as $img): ?>
                            <div class="relative group">
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($img) ?>" 
                                    alt="Journal image" class="w-full h-32 object-cover rounded border">
                                <input type="hidden" name="existing_images[]" value="<?= htmlspecialchars($img) ?>">
                                <button type="button" onclick="removeExistingImage(this)" 
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                    ×
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- New Images -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Thêm hình ảnh mới (Tùy chọn)
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:bg-gray-50 transition-colors"
                    onclick="document.getElementById('images').click()">
                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden"
                        onchange="previewImages(this)">
                    <div class="text-gray-400 text-4xl mb-2">📷</div>
                    <p class="text-gray-500 text-sm">Click để chọn ảnh (tối đa 10 ảnh, mỗi ảnh tối đa 5MB)</p>
                </div>
                <div id="image-preview" class="grid grid-cols-4 gap-3 mt-4"></div>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Trạng thái
                </label>
                <select name="status" class="w-full px-3 py-2 border rounded focus:border-blue-500 focus:outline-none">
                    <option value="draft" <?= $journal['status'] == 'draft' ? 'selected' : '' ?>>Nháp</option>
                    <option value="published" <?= $journal['status'] == 'published' ? 'selected' : '' ?>>Đăng</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="?act=guide-journals&action=show&id=<?= $journal['id'] ?>" 
                    class="px-6 py-2 border rounded hover:bg-gray-50">
                    Hủy
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">
                    Cập nhật
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function previewImages(input) {
        const container = document.getElementById('image-preview');
        if (!container) return;

        // Clear existing previews (only new ones)
        const existingPreviews = container.querySelectorAll('.new-image-preview');
        existingPreviews.forEach(el => el.remove());

        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach((file, index) => {
                if (index >= 10) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group new-image-preview';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-32 object-cover rounded border">
                        <button type="button" onclick="removeImagePreview(this)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                            ×
                        </button>
                    `;
                    container.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    }

    function removeImagePreview(btn) {
        btn.closest('div').remove();
    }

    function removeExistingImage(btn) {
        const container = btn.closest('div');
        container.remove();
    }
</script>

