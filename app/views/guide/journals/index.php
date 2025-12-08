<?php
/**
 * GUIDE - DANH SÁCH NHẬT KÝ TOUR
 * Variables: $journals, $total_pages, $current_page
 */
?>

<div class="max-w-6xl mx-auto p-4 lg:p-8">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Nhật ký Tour của tôi</h1>
        <a href="?act=guide-journals&action=create"
            class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Viết nhật ký mới
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-panel rounded-2xl p-4 lg:p-5 mb-4 lg:mb-6 border border-primary-100 shadow-sm">
        <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:gap-4 items-end">
            <input type="hidden" name="act" value="guide-journals">
            <?php if (!empty($_GET['schedule_id'])): ?>
                <input type="hidden" name="schedule_id" value="<?= (int) $_GET['schedule_id'] ?>">
            <?php endif; ?>
            <div class="flex-1 w-full lg:w-auto">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ngày viết</label>
                <input type="date" name="journal_date" value="<?= htmlspecialchars($_GET['journal_date'] ?? '') ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:border-accent focus:outline-none transition-all text-primary-700 text-sm lg:text-base">
            </div>
            <div class="w-full lg:w-auto">
                <button type="submit" class="w-full lg:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-600 hover:opacity-90 text-white font-semibold rounded-xl transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Lọc
                </button>
            </div>
            <?php if (!empty($_GET['schedule_id']) || !empty($_GET['journal_date'])): ?>
                <div class="w-full lg:w-auto">
                    <a href="?act=guide-journals" class="w-full lg:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-50 text-primary-700 font-semibold rounded-xl hover:bg-primary-100 transition-colors text-sm lg:text-base text-center">
                        Xóa bộ lọc
                    </a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Journals List -->
    <div class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm">
        <?php if (empty($journals)): ?>
            <div class="p-8 lg:p-12 text-center">
                <div class="text-primary-300 mb-4 flex justify-center">
                    <i data-lucide="file-text" class="w-16 h-16"></i>
                </div>
                <p class="text-primary-600 font-semibold mb-2 text-sm lg:text-base">Chưa có nhật ký nào</p>
                <p class="text-primary-500 text-xs lg:text-sm mb-4">Bắt đầu viết nhật ký cho tour của bạn</p>
                <a href="?act=guide-journals&action=create"
                    class="inline-block px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base">
                    Viết nhật ký đầu tiên
                </a>
            </div>
        <?php else: ?>
            <div class="divide-y divide-primary-100">
                <?php foreach ($journals as $journal): ?>
                    <div class="p-4 lg:p-6 hover:bg-primary-50 transition-colors">
                        <div class="flex flex-col lg:flex-row items-start justify-between gap-4">
                            <div class="flex-1 w-full">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-base lg:text-lg font-bold text-primary-700">
                                        <a href="?act=guide-journals&action=show&id=<?= $journal['id'] ?>"
                                            class="hover:text-accent transition-colors">
                                            <?= htmlspecialchars($journal['title']) ?>
                                        </a>
                                    </h3>
                                </div>
                                <div class="text-xs lg:text-sm text-primary-500 mb-3 flex flex-wrap gap-1 lg:gap-2">
                                    <span class="font-semibold text-primary-700"><?= htmlspecialchars($journal['tour_name']) ?></span>
                                    <span>•</span>
                                    <span class="font-mono text-xs"><?= htmlspecialchars($journal['tour_code']) ?></span>
                                    <?php if (!empty($journal['schedule_start_date'])): ?>
                                        <span>•</span>
                                        <span>KH: <?= date('d/m/Y', strtotime($journal['schedule_start_date'])) ?></span>
                                        <?php if (!empty($journal['duration_days'])): ?>
                                            <span>•</span>
                                            <span><?= $journal['duration_days'] ?>N<?= $journal['duration_nights'] ?>Đ</span>
                                        <?php endif; ?>
                                    <?php elseif (!empty($journal['booking_start_date'])): ?>
                                        <span>•</span>
                                        <span>KH: <?= date('d/m/Y', strtotime($journal['booking_start_date'])) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($journal['departure_location'])): ?>
                                        <span>•</span>
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="map-pin" class="w-3 h-3"></i>
                                            <?= htmlspecialchars($journal['departure_location']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <span>•</span>
                                    <span>Ngày viết: <?= date('d/m/Y', strtotime($journal['journal_date'])) ?></span>
                                    <?php if ($journal['day_number']): ?>
                                        <span>•</span>
                                        <span>Ngày <?= $journal['day_number'] ?></span>
                                    <?php endif; ?>
                                    <span>•</span>
                                    <span><?= date('d/m/Y H:i', strtotime($journal['created_at'])) ?></span>
                                </div>
                                <p class="text-xs lg:text-sm text-primary-600 line-clamp-2 mb-3">
                                    <?= htmlspecialchars(strip_tags(substr($journal['content'], 0, 200))) ?>
                                    <?= strlen($journal['content']) > 200 ? '...' : '' ?>
                                </p>
                                <?php if (!empty($journal['weather'])): ?>
                                    <div class="text-xs lg:text-sm text-primary-500 mb-2">
                                        <span class="font-semibold">Thời tiết:</span> <?= htmlspecialchars($journal['weather']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($journal['images'])): ?>
                                    <div class="flex gap-2 mb-3">
                                        <?php foreach (array_slice($journal['images'], 0, 3) as $img): ?>
                                            <img src="<?= BASE_URL . '/' . htmlspecialchars($img['image_url']) ?>"
                                                alt="Journal image" class="w-16 h-16 object-cover rounded-xl border border-primary-100">
                                        <?php endforeach; ?>
                                        <?php if (count($journal['images']) > 3): ?>
                                            <div
                                                class="w-16 h-16 bg-primary-100 rounded-xl border border-primary-100 flex items-center justify-center text-xs text-primary-500">
                                                +<?= count($journal['images']) - 3 ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                                <a href="?act=guide-journals&action=show&id=<?= $journal['id'] ?>"
                                    class="px-3 lg:px-4 py-1.5 lg:py-2 bg-info-bg text-info-text rounded-xl hover:bg-info-bg/80 text-xs lg:text-sm font-semibold transition-all flex items-center justify-center gap-1">
                                    <i data-lucide="eye" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                    Xem
                                </a>
                                <a href="?act=guide-journals&action=edit&id=<?= $journal['id'] ?>"
                                    class="px-3 lg:px-4 py-1.5 lg:py-2 bg-warning-bg text-warning-text rounded-xl hover:bg-warning-bg/80 text-xs lg:text-sm font-semibold transition-all flex items-center justify-center gap-1">
                                    <i data-lucide="pencil" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                    Sửa
                                </a>
                                <a href="?act=guide-journals&action=delete&id=<?= $journal['id'] ?>"
                                    onclick="return confirm('Bạn có chắc muốn xóa nhật ký này?')"
                                    class="px-3 lg:px-4 py-1.5 lg:py-2 bg-danger-bg text-danger-text rounded-xl hover:bg-danger-bg/80 text-xs lg:text-sm font-semibold transition-all flex items-center justify-center gap-1">
                                    <i data-lucide="trash-2" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                    Xóa
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if (isset($total_pages) && $total_pages > 1): ?>
            <div class="px-4 lg:px-6 py-3 lg:py-4 border-t border-primary-100 bg-primary-50 flex justify-center">
                <div class="flex gap-1 lg:gap-2">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?act=guide-journals&page=<?= $i ?><?= !empty($_GET['journal_date']) ? '&journal_date=' . htmlspecialchars($_GET['journal_date']) : '' ?><?= !empty($_GET['schedule_id']) ? '&schedule_id=' . (int) $_GET['schedule_id'] : '' ?>"
                            class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-semibold transition-all <?= $i == $current_page ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white shadow-sm' : 'bg-panel border border-primary-100 text-primary-700 hover:bg-primary-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
