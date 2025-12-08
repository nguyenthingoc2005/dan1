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

<div class="space-y-4 lg:space-y-6">
    <!-- Upload Section -->
    <div class="border-2 border-dashed border-primary-200 p-4 lg:p-6 text-center bg-primary-50 rounded-2xl">
        <form id="uploadImageForm" enctype="multipart/form-data" class="space-y-3 lg:space-y-4">
            <input type="hidden" name="destination_id" value="<?= $destination_id ?>">
            <div>
                <label for="galleryDestinationImages" class="cursor-pointer">
                    <div class="flex flex-col items-center">
                        <div class="mb-2 lg:mb-3">
                            <i data-lucide="upload" class="w-12 h-12 lg:w-16 lg:h-16 text-primary-300"></i>
                        </div>
                        <span class="text-xs lg:text-sm font-semibold text-primary-700">Chọn ảnh để upload</span>
                        <span class="text-xs text-primary-500 mt-1">JPG, PNG, WEBP (tối đa 5MB mỗi ảnh)</span>
                    </div>
                </label>
                <input type="file" id="galleryDestinationImages" name="gallery_images[]" multiple
                    accept="image/jpeg,image/png,image/webp" class="hidden" onchange="handleImageUpload()">
            </div>
            <button type="button" onclick="document.getElementById('galleryDestinationImages').click()"
                class="px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-xs lg:text-sm">
                Chọn ảnh
            </button>
        </form>
    </div>

    <!-- Gallery -->
    <div id="imageGallery" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 lg:gap-4">
        <?php if (empty($images)): ?>
            <div class="col-span-full text-center py-8 lg:py-12 text-primary-500">
                <i data-lucide="image" class="w-12 h-12 lg:w-16 lg:h-16 mx-auto mb-3 text-primary-300"></i>
                <p class="text-sm lg:text-base">Chưa có ảnh nào. Hãy upload ảnh để bắt đầu.</p>
            </div>
        <?php else: ?>
            <?php foreach ($images as $index => $image): ?>
                <div class="image-item relative group border-l-4 border-primary-200 bg-panel overflow-hidden transition-all hover:border-accent rounded-xl shadow-sm hover:shadow-md"
                    data-image-id="<?= $image['id'] ?>">
                    <img src="<?= htmlspecialchars($image['image_url']) ?>" alt="Destination Image"
                        class="w-full h-32 lg:h-40 object-cover rounded-r-xl">

                    <!-- Primary Badge -->
                    <?php if ($image['is_primary']): ?>
                        <div class="absolute top-2 left-2 bg-warning text-white text-xs px-2 py-1 font-bold rounded-xl flex items-center gap-1">
                            <i data-lucide="star" class="w-3 h-3"></i>
                            Chính
                        </div>
                    <?php endif; ?>

                    <!-- Overlay Actions -->
                    <div
                        class="absolute inset-0 bg-primary-900 bg-opacity-0 group-hover:bg-opacity-60 transition-all flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 rounded-xl">
                        <button onclick="setPrimaryImage(<?= $image['id'] ?>, <?= $destination_id ?>)"
                            class="px-2.5 py-1.5 bg-warning hover:opacity-90 text-white text-xs font-semibold rounded-xl transition-all flex items-center gap-1"
                            title="Đặt làm ảnh chính">
                            <i data-lucide="star" class="w-3 h-3"></i>
                        </button>
                        <button
                            onclick="editImageCaption(<?= $image['id'] ?>, '<?= htmlspecialchars(addslashes($image['caption'] ?? '')) ?>')"
                            class="px-2.5 py-1.5 bg-accent hover:opacity-90 text-white text-xs font-semibold rounded-xl transition-all flex items-center gap-1"
                            title="Sửa caption">
                            <i data-lucide="edit" class="w-3 h-3"></i>
                        </button>
                        <button onclick="deleteImage(<?= $image['id'] ?>, <?= $destination_id ?>)"
                            class="px-2.5 py-1.5 bg-danger hover:opacity-90 text-white text-xs font-semibold rounded-xl transition-all flex items-center gap-1"
                            title="Xóa">
                            <i data-lucide="trash-2" class="w-3 h-3"></i>
                        </button>
                    </div>

                    <!-- Caption -->
                    <?php if (!empty($image['caption'])): ?>
                        <div class="absolute bottom-0 left-0 right-0 bg-primary-900 bg-opacity-75 text-white text-xs p-2 font-semibold rounded-br-xl">
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
        const fileInput = document.getElementById('galleryDestinationImages');

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
        const loadingHtml = '<div class="col-span-full text-center py-4"><span class="text-accent flex items-center justify-center gap-2"><i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>Đang upload...</span></div>';
        gallery.innerHTML = loadingHtml;

        $.ajax({
            url: '?act=admin&module=location-services&action=upload-destination-image',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
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
            error: function () {
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
            success: function (response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    // Remove image from DOM
                    $(`.image-item[data-image-id="${imageId}"]`).fadeOut(300, function () {
                        $(this).remove();
                        // Check if gallery is empty
                        if ($('#imageGallery .image-item').length === 0) {
                            $('#imageGallery').html('<div class="col-span-full text-center py-8 lg:py-12 text-primary-500"><i data-lucide="image" class="w-12 h-12 lg:w-16 lg:h-16 mx-auto mb-3 text-primary-300"></i><p class="text-sm lg:text-base">Chưa có ảnh nào. Hãy upload ảnh để bắt đầu.</p></div>');
                        }
                    });
                } else {
                    showToast(response.message || 'Có lỗi xảy ra', 'error');
                }
            },
            error: function () {
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
            success: function (response) {
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
            error: function () {
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
            success: function (response) {
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
            error: function () {
                showToast('Lỗi khi cập nhật caption', 'error');
            }
        });
    }
</script>