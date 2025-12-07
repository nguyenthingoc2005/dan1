<?php
/**
 * Destination Image Gallery Component
 * Hiển thị và quản lý ảnh cho destination
 */
$destination_id = $destination['id'] ?? null;
$images = $destination_images ?? [];

if (!$destination_id) {
    return;
}
?>

<div class="space-y-4">
    <!-- Upload Section -->
    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
        <form id="uploadImageForm" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="destination_id" value="<?= $destination_id ?>">
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
                <input type="file" id="destinationImages" name="images[]" multiple accept="image/jpeg,image/png,image/webp" 
                    class="hidden" onchange="handleImageUpload()">
            </div>
            <button type="button" onclick="document.getElementById('destinationImages').click()" 
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                Chọn ảnh
            </button>
        </form>
    </div>

    <!-- Gallery -->
    <div id="imageGallery" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php if (empty($images)): ?>
            <div class="col-span-full text-center py-8 text-gray-500">
                <p>Chưa có ảnh nào. Hãy upload ảnh để bắt đầu.</p>
            </div>
        <?php else: ?>
            <?php foreach ($images as $index => $image): ?>
                <div class="image-item relative group border rounded-lg overflow-hidden" data-image-id="<?= $image['id'] ?>">
                    <img src="<?= htmlspecialchars($image['image_url']) ?>" 
                         alt="Destination Image" 
                         class="w-full h-32 object-cover">
                    
                    <!-- Primary Badge -->
                    <?php if ($image['is_primary']): ?>
                        <div class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded">
                            ⭐ Chính
                        </div>
                    <?php endif; ?>

                    <!-- Overlay Actions -->
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100">
                        <button onclick="setPrimaryImage(<?= $image['id'] ?>, <?= $destination_id ?>)" 
                                class="px-2 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600"
                                title="Đặt làm ảnh chính">
                            ⭐
                        </button>
                        <button onclick="editImageCaption(<?= $image['id'] ?>, '<?= htmlspecialchars(addslashes($image['caption'] ?? '')) ?>')" 
                                class="px-2 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600"
                                title="Sửa caption">
                            ✏️
                        </button>
                        <button onclick="deleteImage(<?= $image['id'] ?>, <?= $destination_id ?>)" 
                                class="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600"
                                title="Xóa">
                            🗑️
                        </button>
                    </div>

                    <!-- Caption -->
                    <?php if (!empty($image['caption'])): ?>
                        <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-70 text-white text-xs p-2">
                            <?= htmlspecialchars($image['caption']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function handleImageUpload() {
    const form = document.getElementById('uploadImageForm');
    const formData = new FormData(form);
    const fileInput = document.getElementById('destinationImages');
    
    if (!fileInput.files || fileInput.files.length === 0) {
        showToast('Vui lòng chọn ít nhất một ảnh', 'error');
        return;
    }

    // Add files to formData
    for (let i = 0; i < fileInput.files.length; i++) {
        formData.append('images[]', fileInput.files[i]);
    }

    // Show loading
    const gallery = document.getElementById('imageGallery');
    const loadingHtml = '<div class="col-span-full text-center py-4"><span class="text-blue-600">Đang upload...</span></div>';
    gallery.innerHTML = loadingHtml;

    $.ajax({
        url: '?act=admin&module=location-services&action=upload-destination-image',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                // Reload gallery
                loadImageGallery(<?= $destination_id ?>);
                // Reset file input
                fileInput.value = '';
            } else {
                showToast(response.message || 'Có lỗi xảy ra', 'error');
                loadImageGallery(<?= $destination_id ?>);
            }
        },
        error: function() {
            showToast('Lỗi khi upload ảnh', 'error');
            loadImageGallery(<?= $destination_id ?>);
        }
    });
}

function loadImageGallery(destinationId) {
    // Reload page để cập nhật gallery
    // Hoặc có thể dùng AJAX để reload chỉ phần gallery
    window.location.reload();
}

function deleteImage(imageId, destinationId) {
    if (!confirm('Bạn có chắc muốn xóa ảnh này?')) return;

    $.ajax({
        url: '?act=admin&module=location-services&action=delete-destination-image',
        method: 'POST',
        data: { image_id: imageId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                // Remove image from DOM
                $(`.image-item[data-image-id="${imageId}"]`).fadeOut(300, function() {
                    $(this).remove();
                    // Check if gallery is empty
                    if ($('#imageGallery .image-item').length === 0) {
                        $('#imageGallery').html('<div class="col-span-full text-center py-8 text-gray-500"><p>Chưa có ảnh nào. Hãy upload ảnh để bắt đầu.</p></div>');
                    }
                });
            } else {
                showToast(response.message || 'Có lỗi xảy ra', 'error');
            }
        },
        error: function() {
            showToast('Lỗi khi xóa ảnh', 'error');
        }
    });
}

function setPrimaryImage(imageId, destinationId) {
    $.ajax({
        url: '?act=admin&module=location-services&action=set-primary-destination-image',
        method: 'POST',
        data: { 
            image_id: imageId,
            destination_id: destinationId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                // Reload để cập nhật primary badge
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showToast(response.message || 'Có lỗi xảy ra', 'error');
            }
        },
        error: function() {
            showToast('Lỗi khi đặt ảnh chính', 'error');
        }
    });
}

function editImageCaption(imageId, currentCaption) {
    const newCaption = prompt('Nhập caption cho ảnh:', currentCaption || '');
    if (newCaption === null) return; // User cancelled

    $.ajax({
        url: '?act=admin&module=location-services&action=update-destination-image-caption',
        method: 'POST',
        data: { 
            image_id: imageId,
            caption: newCaption
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                // Reload để cập nhật caption
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                showToast(response.message || 'Có lỗi xảy ra', 'error');
            }
        },
        error: function() {
            showToast('Lỗi khi cập nhật caption', 'error');
        }
    });
}
</script>

