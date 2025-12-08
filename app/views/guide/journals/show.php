<?php
/**
 * GUIDE - CHI TIẾT NHẬT KÝ TOUR
 * Variables: $journal, $images
 */
?>

<div class="max-w-7xl mx-auto p-4 lg:p-8">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700"><?= htmlspecialchars($journal['title']) ?></h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1 flex flex-wrap gap-1 lg:gap-2">
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
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <a href="?act=guide-journals&action=edit&id=<?= $journal['id'] ?>"
                class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-warning-bg text-warning-text rounded-xl hover:opacity-90 font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="pencil" class="w-4 h-4"></i>
                Sửa
            </a>
            <a href="?act=guide-journals&action=delete&id=<?= $journal['id'] ?>"
                onclick="return confirm('Bạn có chắc muốn xóa nhật ký này?')"
                class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-danger-bg text-danger-text rounded-xl hover:opacity-90 font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                Xóa
            </a>
            <?php if (!empty($journal['tour_schedule_id'])): ?>
                <a href="?act=guide-tours&action=show&id=<?= $journal['tour_schedule_id'] ?>&tab=journals" 
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

    <div class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm">
        <div class="p-4 lg:p-6">
            <!-- Tour Info -->
            <div class="bg-primary-50 p-4 lg:p-5 rounded-2xl mb-4 lg:mb-6 border border-primary-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 text-xs lg:text-sm">
                    <div>
                        <span class="text-primary-500 block mb-1">Tour:</span>
                        <div class="font-semibold text-primary-700"><?= htmlspecialchars($journal['tour_name']) ?></div>
                        <div class="text-xs text-primary-500 font-mono mt-1"><?= htmlspecialchars($journal['tour_code']) ?></div>
                    </div>
                    <?php if (!empty($journal['schedule_start_date'])): ?>
                        <div>
                            <span class="text-primary-500 block mb-1">Ngày khởi hành:</span>
                            <div class="font-semibold text-primary-700"><?= date('d/m/Y', strtotime($journal['schedule_start_date'])) ?></div>
                            <?php if (!empty($journal['schedule_end_date'])): ?>
                                <div class="text-xs text-primary-500 mt-1">Kết thúc: <?= date('d/m/Y', strtotime($journal['schedule_end_date'])) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($journal['duration_days'])): ?>
                        <div>
                            <span class="text-primary-500 block mb-1">Thời lượng:</span>
                            <div class="font-semibold text-primary-700"><?= $journal['duration_days'] ?> ngày <?= $journal['duration_nights'] ?> đêm</div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($journal['departure_location'])): ?>
                        <div>
                            <span class="text-primary-500 block mb-1">Điểm khởi hành:</span>
                            <div class="font-semibold text-primary-700"><?= htmlspecialchars($journal['departure_location']) ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($journal['guide_name'])): ?>
                        <div>
                            <span class="text-primary-500 block mb-1">HDV:</span>
                            <div class="font-semibold text-primary-700"><?= htmlspecialchars($journal['guide_name']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Journal Date Info -->
            <div class="bg-info-bg p-4 lg:p-5 rounded-2xl mb-4 lg:mb-6 border border-info">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-4 text-xs lg:text-sm">
                    <div>
                        <span class="text-info-text block mb-1">Ngày viết nhật ký:</span>
                        <div class="font-semibold text-info-dark"><?= date('d/m/Y', strtotime($journal['journal_date'])) ?></div>
                    </div>
                    <?php if ($journal['day_number']): ?>
                        <div>
                            <span class="text-info-text block mb-1">Ngày trong tour:</span>
                            <div class="font-semibold text-info-dark">Ngày <?= $journal['day_number'] ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Content -->
            <div class="mb-4 lg:mb-6">
                <div class="text-sm lg:text-base text-primary-700 whitespace-pre-wrap leading-relaxed"><?= nl2br(htmlspecialchars($journal['content'])) ?></div>
            </div>

            <!-- Weather -->
            <?php if (!empty($journal['weather'])): ?>
                <div class="bg-info-bg p-4 lg:p-5 rounded-2xl mb-4 border border-info">
                    <div class="text-xs lg:text-sm">
                        <span class="font-semibold text-info-dark">Thời tiết:</span>
                        <span class="text-info-text ml-2"><?= htmlspecialchars($journal['weather']) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Highlights -->
            <?php if (!empty($journal['highlights'])): ?>
                <div class="bg-success-bg p-4 lg:p-5 rounded-2xl mb-4 border border-success">
                    <div class="text-xs lg:text-sm font-semibold text-success-dark mb-2">Điểm nổi bật:</div>
                    <div class="text-success-text whitespace-pre-wrap"><?= nl2br(htmlspecialchars($journal['highlights'])) ?></div>
                </div>
            <?php endif; ?>

            <!-- Issues -->
            <?php if (!empty($journal['issues'])): ?>
                <div class="bg-warning-bg p-4 lg:p-5 rounded-2xl mb-4 border border-warning">
                    <div class="text-xs lg:text-sm font-semibold text-warning-dark mb-2">Vấn đề phát sinh:</div>
                    <div class="text-warning-text whitespace-pre-wrap"><?= nl2br(htmlspecialchars($journal['issues'])) ?></div>
                </div>
            <?php endif; ?>

            <!-- Images -->
            <?php if (!empty($images)): ?>
                <div class="border-t border-primary-100 pt-4 lg:pt-6 mt-4 lg:mt-6">
                    <h3 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4">Hình ảnh</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 lg:gap-4">
                        <?php foreach ($images as $img): ?>
                            <div class="relative group">
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($img['image_url']) ?>" 
                                    alt="<?= htmlspecialchars($img['caption'] ?? 'Journal image') ?>" 
                                    class="w-full h-40 lg:h-48 object-cover rounded-xl border border-primary-100 cursor-pointer hover:opacity-90 transition-opacity"
                                    onclick="openImageModal('<?= BASE_URL . '/' . htmlspecialchars($img['image_url']) ?>', '<?= htmlspecialchars($img['caption'] ?? '') ?>')">
                                <?php if (!empty($img['caption'])): ?>
                                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-2 rounded-b-xl">
                                        <?= htmlspecialchars($img['caption']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Timestamps -->
            <div class="border-t border-primary-100 pt-4 mt-4 lg:mt-6 text-xs text-primary-500">
                <div>Tạo lúc: <?= date('d/m/Y H:i:s', strtotime($journal['created_at'])) ?></div>
                <?php if ($journal['updated_at'] != $journal['created_at']): ?>
                    <div class="mt-1">Cập nhật lúc: <?= date('d/m/Y H:i:s', strtotime($journal['updated_at'])) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4"
    onclick="closeImageModal()">
    <div class="max-w-4xl w-full relative">
        <img id="modalImage" src="" alt="Journal image" class="w-full h-auto rounded-2xl">
        <div id="modalCaption" class="text-white text-center mt-2 text-sm"></div>
        <button onclick="closeImageModal()" 
            class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-75 transition-all">
            <i data-lucide="x" class="w-5 h-5"></i>
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
