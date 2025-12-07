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
    echo '<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">';
    echo '<p class="text-red-700 font-medium">Lỗi: Thiếu thông tin quốc gia hoặc tỉnh thành.</p>';
    echo '<p class="text-red-600 text-sm mt-1">Vui lòng quay lại và chọn quốc gia/tỉnh thành trước khi thêm địa điểm.</p>';
    echo '</div>';
    return;
}

$is_edit = !empty($destination);
$action_url = $is_edit
    ? '?act=admin&module=location-services&action=update-destination&id=' . $destination['id'] . '&country_id=' . $current_country_id . '&province_id=' . $current_province_id
    : '?act=admin&module=location-services&action=store-destination&country_id=' . $current_country_id . '&province_id=' . $current_province_id;
?>

<form method="POST" action="<?= $action_url ?>" class="space-y-6" enctype="multipart/form-data">
    <input type="hidden" name="province_id" value="<?= (int) $current_province_id ?>">
    <input type="hidden" name="country_id" value="<?= (int) $current_country_id ?>">
    <?php if ($is_edit): ?>
        <input type="hidden" name="id" value="<?= $destination['id'] ?>">
    <?php endif; ?>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Tên địa điểm *</label>
        <input type="text" name="name" value="<?= htmlspecialchars($destination['name'] ?? '') ?>"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="VD: Hồ Xuân Hương, Chợ Đà Lạt" required>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Tỉnh/Thành phố *</label>
        <input type="text" value="<?= htmlspecialchars($current_province['name'] ?? '') ?>"
            class="w-full px-3 py-2 border rounded bg-gray-100" readonly>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Quốc gia *</label>
        <input type="text" value="<?= htmlspecialchars($current_country['name'] ?? '') ?>"
            class="w-full px-3 py-2 border rounded bg-gray-100" readonly>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Mô tả</label>
        <textarea name="description"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4"
            placeholder="Mô tả về địa điểm..."><?= htmlspecialchars($destination['description'] ?? '') ?></textarea>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Vị trí cụ thể</label>
        <textarea name="locations"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2"
            placeholder="Số nhà, đường, phường/xã..."><?= htmlspecialchars($destination['locations'] ?? '') ?></textarea>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Trạng thái *</label>
        <select name="status"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <option value="active" <?= ($destination['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động
            </option>
            <option value="inactive" <?= ($destination['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động
            </option>
        </select>
    </div>

    <!-- Section: Quản lý ảnh -->
    <div class="mb-6 pt-6 border-t">
        <h4 class="text-lg font-semibold mb-3">Quản lý ảnh</h4>
        
        <?php if ($is_edit && !empty($destination['id']) && !empty($destination_images)): ?>
            <!-- Edit mode: Hiển thị ảnh cũ với checkbox để xóa -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Ảnh hiện tại</label>
                <div id="existingImages" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <?php foreach ($destination_images as $image): ?>
                        <div class="relative border border-gray-200 rounded overflow-hidden group" data-image-id="<?= $image['id'] ?>">
                            <img src="<?= htmlspecialchars($image['image_url']) ?>" 
                                 alt="Destination Image" 
                                 class="w-full h-32 object-cover">
                            
                            <?php if ($image['is_primary']): ?>
                                <div class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 font-medium rounded">
                                    ⭐ Chính
                                </div>
                            <?php endif; ?>
                            
                            <div class="absolute inset-0 bg-gray-900 bg-opacity-0 group-hover:bg-opacity-60 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <label class="cursor-pointer px-3 py-1.5 bg-red-500 text-white text-xs font-medium hover:bg-red-600 transition-colors rounded">
                                    <input type="checkbox" name="delete_images[]" value="<?= $image['id'] ?>" class="sr-only" onchange="toggleDeleteImage(this)">
                                    <span class="delete-label">Xóa</span>
                                </label>
                            </div>
                            
                            <?php if (!empty($image['caption'])): ?>
                                <div class="absolute bottom-0 left-0 right-0 bg-gray-900 bg-opacity-75 text-white text-xs p-2">
                                    <?= htmlspecialchars($image['caption']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Upload ảnh mới -->
        <div class="border-2 border-dashed border-gray-300 p-6 text-center bg-gray-50 rounded">
            <div class="space-y-4">
                <div>
                    <label for="destinationImages" class="cursor-pointer">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Chọn ảnh để upload</span>
                            <span class="text-xs text-gray-500 mt-1">JPG, PNG, WEBP (tối đa 5MB mỗi ảnh)</span>
                        </div>
                    </label>
                    <input type="file" id="destinationImages" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
                </div>
                <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4 hidden">
                    <!-- Preview images will be shown here -->
                </div>
                <p class="text-xs text-gray-500 mt-2">Ảnh sẽ được upload khi lưu form.</p>
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
                            <img src="${e.target.result}" class="w-full h-32 object-cover" alt="Preview ${index + 1}">
                            <button type="button" onclick="removePreviewImage(${index})" 
                                class="absolute top-1 right-1 bg-red-500 text-white text-xs px-2 py-1 rounded hover:bg-red-600">
                                ✕
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

    <div class="flex justify-end gap-2 pt-4 border-t border-gray-200">
        <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?>&province_id=<?= $current_province_id ?>&tab=destinations"
            class="px-6 py-2 bg-gray-300 text-gray-700 font-medium hover:bg-gray-400 transition-colors">
            Hủy
        </a>
        <button type="submit" class="px-6 py-2 bg-accent text-white font-medium hover:bg-blue-600 transition-colors">
            <?= $is_edit ? 'Cập nhật' : 'Tạo mới' ?>
        </button>
    </div>
</form>