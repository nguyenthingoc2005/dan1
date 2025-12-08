<?php
/**
 * GUIDE - SỬA NHẬT KÝ TOUR
 * Variables: $journal, $schedule, $images
 */
?>

<div class="max-w-4xl mx-auto p-4 lg:p-8">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Sửa Nhật ký</h1>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <?php 
            $schedule_id = !empty($journal['tour_schedule_id']) ? $journal['tour_schedule_id'] : ($schedule ? $schedule['id'] : null);
            if ($schedule_id): 
            ?>
                <a href="?act=guide-tours&action=show&id=<?= $schedule_id ?>&tab=journals" 
                   class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="home" class="w-4 h-4"></i>
                    Quay về Tour
                </a>
            <?php endif; ?>
            <a href="?act=guide-journals&action=show&id=<?= $journal['id'] ?>" class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
    </div>

    <form method="POST" action="?act=guide-journals&action=update" enctype="multipart/form-data"
        class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $journal['id'] ?>">
        
        <div class="p-4 lg:p-6 space-y-4 lg:space-y-6">
            <!-- Tour Info (Read-only) -->
            <div class="bg-primary-50 p-4 lg:p-5 rounded-2xl border border-primary-100">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tour</label>
                <p class="text-primary-700 font-semibold text-sm lg:text-base">
                    <?= htmlspecialchars($journal['tour_name']) ?> 
                    (<?= htmlspecialchars($journal['tour_code']) ?>) 
                    <?php if ($schedule): ?>
                        - <?= date('d/m/Y', strtotime($schedule['start_date'])) ?>
                    <?php endif; ?>
                </p>
            </div>

            <!-- Journal Date -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Ngày viết nhật ký <span class="text-danger">*</span>
                </label>
                <input type="date" name="journal_date" id="journal_date" required
                    value="<?= htmlspecialchars($journal['journal_date']) ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
            </div>

            <!-- Day Number -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Số ngày trong tour (Tùy chọn)
                </label>
                <input type="number" name="day_number" id="day_number" min="1"
                    value="<?= htmlspecialchars($journal['day_number'] ?? '') ?>"
                    placeholder="VD: 1, 2, 3..."
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
            </div>

            <!-- Title -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Tiêu đề <span class="text-danger">*</span>
                </label>
                <input type="text" name="title" id="title" required
                    value="<?= htmlspecialchars($journal['title']) ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
            </div>

            <!-- Content -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Nội dung <span class="text-danger">*</span>
                </label>
                <textarea name="content" id="content" rows="12" required
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($journal['content']) ?></textarea>
            </div>

            <!-- Weather -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Thời tiết (Tùy chọn)
                </label>
                <input type="text" name="weather" id="weather"
                    value="<?= htmlspecialchars($journal['weather'] ?? '') ?>"
                    placeholder="VD: Nắng đẹp, 25°C"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
            </div>

            <!-- Highlights -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Điểm nổi bật (Tùy chọn)
                </label>
                <textarea name="highlights" id="highlights" rows="4"
                    placeholder="Những điểm nổi bật, hoạt động thú vị trong ngày..."
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($journal['highlights'] ?? '') ?></textarea>
            </div>

            <!-- Issues -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Vấn đề phát sinh (Tùy chọn)
                </label>
                <textarea name="issues" id="issues" rows="4"
                    placeholder="Các vấn đề, sự cố phát sinh (nếu có)..."
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($journal['issues'] ?? '') ?></textarea>
            </div>

            <!-- Existing Images -->
            <?php if (!empty($images)): ?>
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                        Hình ảnh hiện tại
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 lg:gap-4">
                        <?php foreach ($images as $img): ?>
                            <div class="relative group">
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($img['image_url']) ?>" 
                                    alt="Journal image" class="w-full h-32 object-cover rounded-xl border border-primary-100">
                                <input type="checkbox" name="keep_images[]" value="<?= $img['id'] ?>" checked
                                    class="absolute top-2 left-2 w-5 h-5 rounded border-primary-300 text-accent focus:ring-accent">
                                <button type="button" onclick="toggleImageCheckbox(this)" 
                                    class="absolute top-1 right-1 bg-danger text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-xs text-primary-500 mt-2">Bỏ chọn để xóa ảnh</p>
                </div>
            <?php endif; ?>

            <!-- New Images -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Thêm hình ảnh mới (Tùy chọn)
                </label>
                <div class="border-2 border-dashed border-primary-200 rounded-2xl p-4 lg:p-6 text-center cursor-pointer hover:bg-primary-50 transition-colors"
                    onclick="document.getElementById('images').click()">
                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden"
                        onchange="previewImages(this)">
                    <div class="mb-2 flex justify-center">
                        <i data-lucide="image" class="w-12 h-12 text-primary-400"></i>
                    </div>
                    <p class="text-xs lg:text-sm text-primary-600">Click để chọn ảnh (tối đa 10 ảnh, mỗi ảnh tối đa 5MB)</p>
                </div>
                <div id="image-preview" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 lg:gap-4 mt-4"></div>
            </div>

            <!-- Submit -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-primary-100">
                <?php 
                $schedule_id = !empty($journal['tour_schedule_id']) ? $journal['tour_schedule_id'] : ($schedule ? $schedule['id'] : null);
                if ($schedule_id): 
                ?>
                    <a href="?act=guide-tours&action=show&id=<?= $schedule_id ?>&tab=journals" 
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base text-center">
                        Hủy
                    </a>
                <?php else: ?>
                    <a href="?act=guide-journals&action=show&id=<?= $journal['id'] ?>" 
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base text-center">
                        Hủy
                    </a>
                <?php endif; ?>
                <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
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
                        <img src="${e.target.result}" class="w-full h-32 object-cover rounded-xl border border-primary-100">
                        <button type="button" onclick="removeImagePreview(this)" class="absolute top-1 right-1 bg-danger text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                            <i data-lucide="x" class="w-4 h-4"></i>
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
