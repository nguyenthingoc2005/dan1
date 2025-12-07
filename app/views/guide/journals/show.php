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
                <?= htmlspecialchars($journal['tour_name']) ?> 
                (<?= htmlspecialchars($journal['tour_code']) ?>) 
                <?php if (!empty($journal['schedule_start_date'])): ?>
                    • KH: <?= date('d/m/Y', strtotime($journal['schedule_start_date'])) ?>
                <?php elseif (!empty($journal['booking_start_date'])): ?>
                    • KH: <?= date('d/m/Y', strtotime($journal['booking_start_date'])) ?>
                <?php endif; ?>
                • Ngày viết: <?= date('d/m/Y', strtotime($journal['journal_date'])) ?>
                <?php if ($journal['day_number']): ?>
                    • Ngày <?= $journal['day_number'] ?>
                <?php endif; ?>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="?act=guide-journals&action=edit&id=<?= $journal['id'] ?>"
                class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 font-medium">
                Sửa
            </a>
            <a href="?act=guide-journals&action=delete&id=<?= $journal['id'] ?>"
                onclick="return confirm('Bạn có chắc muốn xóa nhật ký này?')"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 font-medium">
                Xóa
            </a>
            <a href="?act=guide-journals" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                ← Quay lại
            </a>
        </div>
    </div>

    <div class="bg-panel rounded overflow-hidden border border-slate-200">
        <div class="p-6">
            <!-- Tour Info -->
            <div class="bg-gray-50 p-4 rounded mb-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Tour:</span>
                        <div class="font-medium"><?= htmlspecialchars($journal['tour_name']) ?></div>
                        <div class="text-xs text-gray-500 font-mono"><?= htmlspecialchars($journal['tour_code']) ?></div>
                    </div>
                    <?php if (!empty($journal['schedule_start_date'])): ?>
                        <div>
                            <span class="text-gray-500">Ngày khởi hành:</span>
                            <div class="font-medium"><?= date('d/m/Y', strtotime($journal['schedule_start_date'])) ?></div>
                            <?php if (!empty($journal['schedule_end_date'])): ?>
                                <div class="text-xs text-gray-500">Kết thúc: <?= date('d/m/Y', strtotime($journal['schedule_end_date'])) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($journal['duration_days'])): ?>
                        <div>
                            <span class="text-gray-500">Thời lượng:</span>
                            <div class="font-medium"><?= $journal['duration_days'] ?> ngày <?= $journal['duration_nights'] ?> đêm</div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($journal['departure_location'])): ?>
                        <div>
                            <span class="text-gray-500">Điểm khởi hành:</span>
                            <div class="font-medium"><?= htmlspecialchars($journal['departure_location']) ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($journal['guide_name'])): ?>
                        <div>
                            <span class="text-gray-500">HDV:</span>
                            <div class="font-medium"><?= htmlspecialchars($journal['guide_name']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Journal Date Info -->
            <div class="bg-blue-50 p-4 rounded mb-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Ngày viết nhật ký:</span>
                        <div class="font-medium"><?= date('d/m/Y', strtotime($journal['journal_date'])) ?></div>
                    </div>
                    <?php if ($journal['day_number']): ?>
                        <div>
                            <span class="text-gray-500">Ngày trong tour:</span>
                            <div class="font-medium">Ngày <?= $journal['day_number'] ?></div>
                        </div>
                    <?php endif; ?>
                        <span class="font-medium text-gray-900 ml-2"><?= htmlspecialchars($journal['tour_name']) ?></span>
                        <span class="text-gray-500 ml-1">(<?= htmlspecialchars($journal['tour_code']) ?>)</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Ngày khởi hành:</span>
                        <span class="text-gray-900 ml-2"><?= date('d/m/Y', strtotime($journal['booking_start_date'])) ?></span>
                    </div>
                    <div>
                        <span class="text-gray-500">Ngày viết nhật ký:</span>
                        <span class="text-gray-900 ml-2"><?= date('d/m/Y', strtotime($journal['journal_date'])) ?></span>
                    </div>
                    <?php if ($journal['day_number']): ?>
                        <div>
                            <span class="text-gray-500">Ngày trong tour:</span>
                            <span class="text-gray-900 ml-2">Ngày <?= $journal['day_number'] ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Content -->
            <div class="prose max-w-none mb-6">
                <div class="text-gray-700 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($journal['content'])) ?></div>
            </div>

            <!-- Weather -->
            <?php if (!empty($journal['weather'])): ?>
                <div class="bg-blue-50 p-4 rounded mb-4">
                    <div class="text-sm">
                        <span class="font-medium text-gray-700">Thời tiết:</span>
                        <span class="text-gray-900 ml-2"><?= htmlspecialchars($journal['weather']) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Highlights -->
            <?php if (!empty($journal['highlights'])): ?>
                <div class="bg-green-50 p-4 rounded mb-4">
                    <div class="text-sm font-medium text-gray-700 mb-2">Điểm nổi bật:</div>
                    <div class="text-gray-900 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($journal['highlights'])) ?></div>
                </div>
            <?php endif; ?>

            <!-- Issues -->
            <?php if (!empty($journal['issues'])): ?>
                <div class="bg-yellow-50 p-4 rounded mb-4">
                    <div class="text-sm font-medium text-gray-700 mb-2">Vấn đề phát sinh:</div>
                    <div class="text-gray-900 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($journal['issues'])) ?></div>
                </div>
            <?php endif; ?>

            <!-- Images -->
            <?php if (!empty($images)): ?>
                <div class="border-t pt-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Hình ảnh</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php foreach ($images as $img): ?>
                            <div class="relative group">
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($img['image_url']) ?>" 
                                    alt="<?= htmlspecialchars($img['caption'] ?? 'Journal image') ?>" 
                                    class="w-full h-48 object-cover rounded border cursor-pointer hover:opacity-90 transition-opacity"
                                    onclick="openImageModal('<?= BASE_URL . '/' . htmlspecialchars($img['image_url']) ?>', '<?= htmlspecialchars($img['caption'] ?? '') ?>')">
                                <?php if (!empty($img['caption'])): ?>
                                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-2 rounded-b-lg">
                                        <?= htmlspecialchars($img['caption']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Timestamps -->
            <div class="border-t pt-4 mt-6 text-xs text-gray-500">
                <div>Tạo lúc: <?= date('d/m/Y H:i:s', strtotime($journal['created_at'])) ?></div>
                <?php if ($journal['updated_at'] != $journal['created_at']): ?>
                    <div>Cập nhật lúc: <?= date('d/m/Y H:i:s', strtotime($journal['updated_at'])) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4"
    onclick="closeImageModal()">
    <div class="max-w-4xl w-full">
        <img id="modalImage" src="" alt="Journal image" class="w-full h-auto rounded">
        <div id="modalCaption" class="text-white text-center mt-2"></div>
        <button onclick="closeImageModal()" 
            class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-75">
            ×
        </button>
    </div>
</div>

<script>
    function openImageModal(src, caption) {
        document.getElementById('modalImage').src = src;
        document.getElementById('modalCaption').textContent = caption || '';
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }
</script>
