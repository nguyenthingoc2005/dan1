<?php
/**
 * GUIDE - CHI TIẾT NHẬT KÝ TOUR
 * Variables: $journal, $images
 */
?>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($journal['title']) ?></h1>
            <p class="text-gray-500 text-sm mt-1">
                <?= htmlspecialchars($journal['tour_name']) ?> • 
                <?= date('d/m/Y', strtotime($journal['start_date'])) ?> • 
                <?= date('d/m/Y H:i', strtotime($journal['created_at'])) ?>
            </p>
        </div>
        <div class="flex gap-2">
            <?php if ($journal['status'] == 'draft'): ?>
                <a href="?act=guide-journals&action=edit&id=<?= $journal['id'] ?>"
                    class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 font-medium">
                    Sửa
                </a>
                <a href="?act=guide-journals&action=delete&id=<?= $journal['id'] ?>"
                    onclick="return confirm('Bạn có chắc muốn xóa nhật ký này?')"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 font-medium">
                    Xóa
                </a>
            <?php endif; ?>
            <a href="?act=guide-journals" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                ← Quay lại
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <!-- Status Badge -->
            <div class="mb-4">
                <span class="px-3 py-1 text-sm font-medium rounded <?= $journal['status'] == 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                    <?= $journal['status'] == 'published' ? 'Đã đăng' : 'Nháp' ?>
                </span>
            </div>

            <!-- Content -->
            <div class="prose max-w-none mb-6">
                <div class="text-gray-700 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($journal['content'])) ?></div>
            </div>

            <!-- Images -->
            <?php if (!empty($images)): ?>
                <div class="border-t pt-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Hình ảnh</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php foreach ($images as $img): ?>
                            <div class="relative group">
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($img) ?>" 
                                    alt="Journal image" 
                                    class="w-full h-48 object-cover rounded-lg border cursor-pointer hover:opacity-90 transition-opacity"
                                    onclick="openImageModal('<?= BASE_URL . '/' . htmlspecialchars($img) ?>')">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4"
    onclick="closeImageModal()">
    <div class="max-w-4xl w-full">
        <img id="modalImage" src="" alt="Journal image" class="w-full h-auto rounded-lg">
        <button onclick="closeImageModal()" 
            class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-75">
            ×
        </button>
    </div>
</div>

<script>
    function openImageModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }
</script>

