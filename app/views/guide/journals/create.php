<?php
/**
 * GUIDE - VIẾT NHẬT KÝ TOUR
 * Variables: $schedules, $selected_schedule
 */
?>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Viết Nhật ký Tour</h1>
        <a href="?act=guide-journals" class="text-gray-500 hover:text-gray-700">
            ← Quay lại
        </a>
    </div>

    <form method="POST" action="?act=guide-journals&action=store" enctype="multipart/form-data"
        class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 space-y-6">
            <!-- Tour Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Chọn Tour <span class="text-red-500">*</span>
                </label>
                <select name="tour_schedule_id" id="tour_schedule_id" required
                    class="w-full px-3 py-2 border rounded focus:border-blue-500 focus:outline-none">
                    <option value="">-- Chọn Tour --</option>
                    <?php foreach ($schedules as $schedule): ?>
                        <option value="<?= $schedule['id'] ?>" <?= ($selected_schedule && $selected_schedule['id'] == $schedule['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($schedule['tour_name']) ?> 
                            (<?= date('d/m/Y', strtotime($schedule['start_date'])) ?> - <?= date('d/m/Y', strtotime($schedule['end_date'])) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">Chỉ hiển thị tour bạn được phân công và đã bắt đầu</p>
            </div>

            <!-- Title -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tiêu đề <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" required
                    placeholder="VD: Nhật ký ngày đầu tiên - Tour Đà Lạt"
                    class="w-full px-3 py-2 border rounded focus:border-blue-500 focus:outline-none">
            </div>

            <!-- Content -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nội dung <span class="text-red-500">*</span>
                </label>
                <textarea name="content" id="content" rows="12" required
                    placeholder="Chia sẻ câu chuyện về chuyến đi, những trải nghiệm thú vị, cảm nhận của bạn..."
                    class="w-full px-3 py-2 border rounded focus:border-blue-500 focus:outline-none"></textarea>
                <p class="text-xs text-gray-500 mt-1">Bạn có thể sử dụng HTML để format text</p>
            </div>

            <!-- Images -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Hình ảnh (Tùy chọn)
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:bg-gray-50 transition-colors"
                    onclick="document.getElementById('images').click()">
                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden"
                        onchange="previewImages(this)">
                    <div class="text-gray-400 text-4xl mb-2">📷</div>
                    <p class="text-gray-500 text-sm mb-1">Click để chọn ảnh (tối đa 10 ảnh, mỗi ảnh tối đa 5MB)</p>
                    <p class="text-gray-400 text-xs">JPG, PNG, GIF, WebP</p>
                </div>
                <div id="image-preview" class="grid grid-cols-4 gap-3 mt-4"></div>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Trạng thái
                </label>
                <select name="status" class="w-full px-3 py-2 border rounded focus:border-blue-500 focus:outline-none">
                    <option value="draft">Nháp (Lưu để chỉnh sửa sau)</option>
                    <option value="published">Đăng ngay</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="?act=guide-journals" class="px-6 py-2 border rounded hover:bg-gray-50">
                    Hủy
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">
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
        // Note: This only removes preview, actual file input still has the file
        // For full removal, we'd need to use FileList manipulation (complex)
    }
</script>

