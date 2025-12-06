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
        class="bg-panel rounded overflow-hidden border border-slate-200">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $journal['id'] ?>">
        
        <div class="p-6 space-y-6">
            <!-- Tour Info (Read-only) -->
            <div class="bg-gray-50 p-4 rounded border">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tour</label>
                <p class="text-gray-900 font-medium">
                    <?= htmlspecialchars($journal['tour_name']) ?> 
                    (<?= htmlspecialchars($journal['tour_code']) ?>)
                    <?php if ($schedule): ?>
                        - <?= date('d/m/Y', strtotime($schedule['start_date'])) ?>
                    <?php endif; ?>
                </p>
            </div>

            <!-- Journal Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Ngày viết nhật ký <span class="text-red-500">*</span>
                </label>
                <input type="date" name="journal_date" id="journal_date" required
                    value="<?= htmlspecialchars($journal['journal_date']) ?>"
                    class="w-full px-3 py-2 border rounded focus:border-blue-500 focus:outline-none">
            </div>

            <!-- Day Number -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Số ngày trong tour (Tùy chọn)
                </label>
                <input type="number" name="day_number" id="day_number" min="1"
                    value="<?= htmlspecialchars($journal['day_number'] ?? '') ?>"
                    placeholder="VD: 1, 2, 3..."
                    class="w-full px-3 py-2 border rounded focus:border-blue-500 focus:outline-none">
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

            <!-- Weather -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Thời tiết (Tùy chọn)
                </label>
                <input type="text" name="weather" id="weather"
                    value="<?= htmlspecialchars($journal['weather'] ?? '') ?>"
                    placeholder="VD: Nắng đẹp, 25°C"
                    class="w-full px-3 py-2 border rounded focus:border-blue-500 focus:outline-none">
            </div>

            <!-- Highlights -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Điểm nổi bật (Tùy chọn)
                </label>
                <textarea name="highlights" id="highlights" rows="4"
                    placeholder="Những điểm nổi bật, hoạt động thú vị trong ngày..."
                    class="w-full px-3 py-2 border rounded focus:border-blue-500 focus:outline-none"><?= htmlspecialchars($journal['highlights'] ?? '') ?></textarea>
            </div>

            <!-- Issues -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Vấn đề phát sinh (Tùy chọn)
                </label>
                <textarea name="issues" id="issues" rows="4"
                    placeholder="Các vấn đề, sự cố phát sinh (nếu có)..."
                    class="w-full px-3 py-2 border rounded focus:border-blue-500 focus:outline-none"><?= htmlspecialchars($journal['issues'] ?? '') ?></textarea>
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
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($img['image_url']) ?>" 
                                    alt="Journal image" class="w-full h-32 object-cover rounded border">
                                <input type="checkbox" name="keep_images[]" value="<?= $img['id'] ?>" checked
                                    class="absolute top-2 left-2 w-5 h-5">
                                <button type="button" onclick="toggleImageCheckbox(this)" 
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                    ×
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Bỏ chọn để xóa ảnh</p>
                </div>
            <?php endif; ?>

            <!-- New Images -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Thêm hình ảnh mới (Tùy chọn)
                </label>
                <div class="border border-dashed border-gray-300 rounded p-6 text-center cursor-pointer hover:bg-gray-50 transition-colors"
                    onclick="document.getElementById('images').click()">
                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden"
                        onchange="previewImages(this)">
                    <div class="text-gray-400 text-4xl mb-2">📷</div>
                    <p class="text-gray-500 text-sm">Click để chọn ảnh (tối đa 10 ảnh, mỗi ảnh tối đa 5MB)</p>
                </div>
                <div id="image-preview" class="grid grid-cols-4 gap-3 mt-4"></div>
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

    function toggleImageCheckbox(btn) {
        const checkbox = btn.parentElement.querySelector('input[type="checkbox"]');
        checkbox.checked = !checkbox.checked;
    }
</script>
