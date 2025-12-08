<?php
/**
 * GUIDE - VIẾT NHẬT KÝ TOUR
 * Variables: $schedules, $selected_schedule
 */
?>

<div class="max-w-4xl mx-auto p-4 lg:p-8">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Viết Nhật ký Tour</h1>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <?php if ($selected_schedule): ?>
                <a href="?act=guide-tours&action=show&id=<?= $selected_schedule['id'] ?>&tab=journals" 
                   class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="home" class="w-4 h-4"></i>
                    Quay về Tour
                </a>
            <?php endif; ?>
            <a href="?act=guide-journals" class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
    </div>

    <form method="POST" action="?act=guide-journals&action=store" enctype="multipart/form-data"
        class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm">
        <?= csrf_field() ?>
        <div class="p-4 lg:p-6 space-y-4 lg:space-y-6">
            <!-- Tour Selection -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Chọn Tour <span class="text-danger">*</span>
                </label>
                <select name="tour_schedule_id" id="tour_schedule_id" required
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Chọn Tour --</option>
                    <?php foreach ($schedules as $schedule): ?>
                        <option value="<?= $schedule['id'] ?>" <?= ($selected_schedule && $selected_schedule['id'] == $schedule['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($schedule['tour_name']) ?> 
                            (<?= htmlspecialchars($schedule['tour_code']) ?>) - 
                            <?= date('d/m/Y', strtotime($schedule['start_date'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-primary-500 mt-1">Chỉ hiển thị tour bạn được phân công và đã bắt đầu</p>
            </div>

            <!-- Journal Date -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Ngày viết nhật ký <span class="text-danger">*</span>
                </label>
                <input type="date" name="journal_date" id="journal_date" required
                    value="<?= date('Y-m-d') ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
            </div>

            <!-- Day Number -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Số ngày trong tour (Tùy chọn)
                </label>
                <input type="number" name="day_number" id="day_number" min="1"
                    placeholder="VD: 1, 2, 3..."
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
            </div>

            <!-- Title -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Tiêu đề <span class="text-danger">*</span>
                </label>
                <input type="text" name="title" id="title" required
                    placeholder="VD: Nhật ký ngày đầu tiên - Tour Đà Lạt"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
            </div>

            <!-- Content -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Nội dung <span class="text-danger">*</span>
                </label>
                <textarea name="content" id="content" rows="12" required
                    placeholder="Chia sẻ câu chuyện về chuyến đi, những trải nghiệm thú vị, cảm nhận của bạn..."
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"></textarea>
                <p class="text-xs text-primary-500 mt-1">Bạn có thể sử dụng HTML để format text</p>
            </div>

            <!-- Weather -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Thời tiết (Tùy chọn)
                </label>
                <input type="text" name="weather" id="weather"
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
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"></textarea>
            </div>

            <!-- Issues -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Vấn đề phát sinh (Tùy chọn)
                </label>
                <textarea name="issues" id="issues" rows="4"
                    placeholder="Các vấn đề, sự cố phát sinh (nếu có)..."
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"></textarea>
            </div>

            <!-- Images -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Hình ảnh (Tùy chọn)
                </label>
                <div class="border-2 border-dashed border-primary-200 rounded-2xl p-4 lg:p-6 text-center cursor-pointer hover:bg-primary-50 transition-colors"
                    onclick="document.getElementById('images').click()">
                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden"
                        onchange="previewImages(this)">
                    <div class="mb-2 flex justify-center">
                        <i data-lucide="image" class="w-12 h-12 text-primary-400"></i>
                    </div>
                    <p class="text-xs lg:text-sm text-primary-600 mb-1">Click để chọn ảnh (tối đa 10 ảnh, mỗi ảnh tối đa 5MB)</p>
                    <p class="text-xs text-primary-400">JPG, PNG, GIF, WebP</p>
                </div>
                <div id="image-preview" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 lg:gap-4 mt-4"></div>
            </div>

            <!-- Submit -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-primary-100">
                <?php if ($selected_schedule): ?>
                    <a href="?act=guide-tours&action=show&id=<?= $selected_schedule['id'] ?>&tab=journals" 
                       class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base text-center">
                        Hủy
                    </a>
                <?php else: ?>
                    <a href="?act=guide-journals" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base text-center">
                        Hủy
                    </a>
                <?php endif; ?>
                <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Lưu nhật ký
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function previewImages(input) {
        const container = document.getElementById('image-preview');
        container.innerHTML = '';

        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach((file, index) => {
                if (index >= 10) return; // Max 10 images

                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
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
        // Note: This only removes preview, actual file input still has the file
        // For full removal, we'd need to use FileList manipulation (complex)
    }
</script>
