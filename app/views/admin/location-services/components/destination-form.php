<?php
/**
 * Destination Form Component
 * Used for both create and edit
 */
// Ensure variables are set - ưu tiên lấy từ destination nếu đang edit
if (!empty($destination)) {
    $current_country_id = $current_country_id ?? $destination['country_id'] ?? null;
    $current_province_id = $current_province_id ?? $destination['province_id'] ?? null;
} else {
    $current_country_id = $current_country_id ?? null;
    $current_province_id = $current_province_id ?? null;
}

// Validate required fields
if (empty($current_country_id) || empty($current_province_id)) {
    echo '<div class="bg-danger-bg border-l-4 border-danger rounded-xl p-4 mb-4">';
    echo '<p class="text-danger-dark font-bold flex items-center gap-2"><i data-lucide="alert-circle" class="w-4 h-4"></i>Lỗi: Thiếu thông tin quốc gia hoặc tỉnh thành.</p>';
    echo '<p class="text-danger-text text-xs lg:text-sm mt-1">Vui lòng quay lại và chọn quốc gia/tỉnh thành trước khi thêm địa điểm.</p>';
    echo '</div>';
    return;
}

$is_edit = !empty($destination);
$action_url = $is_edit
    ? '?act=admin&module=location-services&action=update-destination&id=' . $destination['id'] . '&country_id=' . $current_country_id . '&province_id=' . $current_province_id
    : '?act=admin&module=location-services&action=store-destination&country_id=' . $current_country_id . '&province_id=' . $current_province_id;
?>

<form method="POST" action="<?= $action_url ?>" class="space-y-4 lg:space-y-6" enctype="multipart/form-data">
    <input type="hidden" name="province_id" value="<?= (int) $current_province_id ?>">
    <input type="hidden" name="country_id" value="<?= (int) $current_country_id ?>">
    <?php if ($is_edit): ?>
        <input type="hidden" name="id" value="<?= $destination['id'] ?>">
    <?php endif; ?>

    <div class="mb-3 lg:mb-4">
        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tên địa điểm <span class="text-danger">*</span></label>
        <input type="text" name="name" value="<?= htmlspecialchars($destination['name'] ?? '') ?>"
            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
            placeholder="VD: Hồ Xuân Hương, Chợ Đà Lạt" required>
    </div>

    <div class="mb-3 lg:mb-4">
        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tỉnh/Thành phố <span class="text-danger">*</span></label>
        <input type="text" value="<?= htmlspecialchars($current_province['name'] ?? '') ?>"
            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-100 border border-primary-200 rounded-xl text-primary-600 text-sm lg:text-base" readonly>
    </div>

    <div class="mb-3 lg:mb-4">
        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Quốc gia <span class="text-danger">*</span></label>
        <input type="text" value="<?= htmlspecialchars($current_country['name'] ?? '') ?>"
            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-100 border border-primary-200 rounded-xl text-primary-600 text-sm lg:text-base" readonly>
    </div>

    <div class="mb-3 lg:mb-4">
        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả</label>
        <textarea name="description"
            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base" rows="4"
            placeholder="Mô tả về địa điểm..."><?= htmlspecialchars($destination['description'] ?? '') ?></textarea>
    </div>

    <div class="mb-3 lg:mb-4">
        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Vị trí cụ thể</label>
        <textarea name="locations"
            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base" rows="2"
            placeholder="Số nhà, đường, phường/xã..."><?= htmlspecialchars($destination['locations'] ?? '') ?></textarea>
    </div>

    <div class="mb-3 lg:mb-4">
        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái <span class="text-danger">*</span></label>
        <select name="status"
            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base" required>
            <option value="active" <?= ($destination['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động
            </option>
            <option value="inactive" <?= ($destination['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động
            </option>
        </select>
    </div>

    <!-- Section: Quản lý ảnh -->
    <div class="mb-4 lg:mb-6 pt-4 lg:pt-6 border-t border-primary-100">
        <h4 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4">Quản lý ảnh</h4>
        
        <?php if ($is_edit && !empty($destination['id']) && !empty($destination_images)): ?>
            <!-- Edit mode: Hiển thị ảnh cũ với checkbox để xóa -->
            <div class="mb-4 lg:mb-6">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-2 lg:mb-3">Ảnh hiện tại</label>
                <div id="existingImages" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 lg:gap-4">
                    <?php foreach ($destination_images as $image): ?>
                        <div class="relative border border-primary-100 rounded-xl overflow-hidden group shadow-sm transition-all hover:shadow-md" data-image-id="<?= $image['id'] ?>">
                            <img src="<?= htmlspecialchars($image['image_url']) ?>" 
                                 alt="Destination Image" 
                                 class="w-full h-32 lg:h-40 object-cover">
                            
                            <?php if ($image['is_primary']): ?>
                                <div class="absolute top-2 left-2 bg-warning text-white text-xs px-2 py-1 font-bold rounded-xl flex items-center gap-1">
                                    <i data-lucide="star" class="w-3 h-3"></i>
                                    Chính
                                </div>
                            <?php endif; ?>
                            
                            <div class="absolute inset-0 bg-primary-900 bg-opacity-0 group-hover:bg-opacity-60 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100 rounded-xl">
                                <label class="cursor-pointer px-3 py-1.5 bg-danger hover:opacity-90 text-white text-xs font-semibold rounded-xl transition-all">
                                    <input type="checkbox" name="delete_images[]" value="<?= $image['id'] ?>" class="sr-only" onchange="toggleDeleteImage(this)">
                                    <span class="delete-label flex items-center gap-1">
                                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                                        Xóa
                                    </span>
                                </label>
                            </div>
                            
                            <?php if (!empty($image['caption'])): ?>
                                <div class="absolute bottom-0 left-0 right-0 bg-primary-900 bg-opacity-75 text-white text-xs p-2">
                                    <?= htmlspecialchars($image['caption']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Upload ảnh mới -->
        <div class="border-2 border-dashed border-primary-200 p-4 lg:p-6 text-center bg-primary-50 rounded-2xl">
            <div class="space-y-3 lg:space-y-4">
                <div>
                    <label for="destinationImages" class="cursor-pointer">
                        <div class="flex flex-col items-center">
                            <div class="mb-2 lg:mb-3">
                                <i data-lucide="upload" class="w-12 h-12 lg:w-16 lg:h-16 text-primary-300"></i>
                            </div>
                            <span class="text-xs lg:text-sm font-semibold text-primary-700">Chọn ảnh để upload</span>
                            <span class="text-xs text-primary-500 mt-1">JPG, PNG, WEBP (tối đa 5MB mỗi ảnh)</span>
                        </div>
                    </label>
                    <input type="file" id="destinationImages" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
                </div>
                <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 lg:gap-4 mt-4 hidden">
                    <!-- Preview images will be shown here -->
                </div>
                <p class="text-xs text-primary-500 mt-2">Ảnh sẽ được upload khi lưu form.</p>
            </div>
        </div>
        
        <script>
        // Preview ảnh mới
        document.getElementById('destinationImages').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            preview.classList.remove('hidden');
            
            const files = Array.from(e.target.files);
            files.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative border border-gray-200 rounded overflow-hidden';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-32 lg:h-40 object-cover rounded-xl" alt="Preview ${index + 1}">
                            <button type="button" onclick="removePreviewImage(${index})" 
                                class="absolute top-2 right-2 bg-danger hover:opacity-90 text-white text-xs px-2 py-1 rounded-xl font-semibold transition-all flex items-center gap-1">
                                <i data-lucide="x" class="w-3 h-3"></i>
                            </button>
                        `;
                        div.dataset.index = index;
                        preview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
        
        function removePreviewImage(index) {
            const input = document.getElementById('destinationImages');
            const dt = new DataTransfer();
            const files = Array.from(input.files);
            files.forEach((file, i) => {
                if (i !== index) {
                    dt.items.add(file);
                }
            });
            input.files = dt.files;
            input.dispatchEvent(new Event('change'));
        }
        
        // Toggle xóa ảnh cũ
        function toggleDeleteImage(checkbox) {
            const imageDiv = checkbox.closest('[data-image-id]');
            if (checkbox.checked) {
                imageDiv.style.opacity = '0.5';
                imageDiv.style.borderColor = '#ef4444';
            } else {
                imageDiv.style.opacity = '1';
                imageDiv.style.borderColor = '#e5e7eb';
            }
        }
        </script>
    </div>

    <div class="flex flex-col sm:flex-row justify-end gap-2 lg:gap-3 pt-4 border-t border-primary-100">
        <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?>&province_id=<?= $current_province_id ?>&tab=destinations"
            class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl font-semibold hover:bg-primary-100 transition-colors text-sm lg:text-base text-center">
            Hủy
        </a>
        <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base">
            <?= $is_edit ? 'Cập nhật' : 'Tạo mới' ?>
        </button>
    </div>
</form>