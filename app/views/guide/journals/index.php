<?php
/**
 * GUIDE - DANH SÁCH NHẬT KÝ TOUR
 * Variables: $journals, $total_pages, $current_page
 */
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Nhật ký Tour của tôi</h1>
        <a href="?act=guide-journals&action=create"
            class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-700 font-medium transition-colors">
            + Viết nhật ký mới
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-panel rounded p-4 mb-6 border border-slate-200">
        <form method="GET" class="flex gap-4 items-end">
            <input type="hidden" name="act" value="guide-journals">
            <?php if (!empty($_GET['schedule_id'])): ?>
                <input type="hidden" name="schedule_id" value="<?= (int) $_GET['schedule_id'] ?>">
            <?php endif; ?>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ngày viết</label>
                <input type="date" name="journal_date" value="<?= htmlspecialchars($_GET['journal_date'] ?? '') ?>"
                    class="w-full px-3 py-2 border rounded focus:border-blue-500 focus:outline-none">
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                    Lọc
                </button>
            </div>
            <?php if (!empty($_GET['schedule_id']) || !empty($_GET['journal_date'])): ?>
                <div>
                    <a href="?act=guide-journals" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        Xóa bộ lọc
                    </a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Journals List -->
    <div class="bg-panel rounded overflow-hidden border border-slate-200">
        <?php if (empty($journals)): ?>
            <div class="p-12 text-center">
                <div class="text-gray-300 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="text-gray-500 font-medium mb-2">Chưa có nhật ký nào</p>
                <p class="text-gray-400 text-sm mb-4">Bắt đầu viết nhật ký cho tour của bạn</p>
                <a href="?act=guide-journals&action=create"
                    class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Viết nhật ký đầu tiên
                </a>
            </div>
        <?php else: ?>
            <div class="divide-y divide-gray-100">
                <?php foreach ($journals as $journal): ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-bold text-gray-900">
                                        <a href="?act=guide-journals&action=show&id=<?= $journal['id'] ?>"
                                            class="hover:text-blue-600">
                                            <?= htmlspecialchars($journal['title']) ?>
                                        </a>
                                    </h3>
                                </div>
                                <div class="text-sm text-gray-500 mb-3">
                                    <span class="font-medium text-gray-700"><?= htmlspecialchars($journal['tour_name']) ?></span>
                                    <span class="mx-2">•</span>
                                    <span class="font-mono text-xs"><?= htmlspecialchars($journal['tour_code']) ?></span>
                                    <?php if (!empty($journal['schedule_start_date'])): ?>
                                        <span class="mx-2">•</span>
                                        <span>KH: <?= date('d/m/Y', strtotime($journal['schedule_start_date'])) ?></span>
                                        <?php if (!empty($journal['duration_days'])): ?>
                                            <span class="mx-2">•</span>
                                            <span><?= $journal['duration_days'] ?>N<?= $journal['duration_nights'] ?>Đ</span>
                                        <?php endif; ?>
                                    <?php elseif (!empty($journal['booking_start_date'])): ?>
                                        <span class="mx-2">•</span>
                                        <span>KH: <?= date('d/m/Y', strtotime($journal['booking_start_date'])) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($journal['departure_location'])): ?>
                                        <span class="mx-2">•</span>
                                        <span>📍 <?= htmlspecialchars($journal['departure_location']) ?></span>
                                    <?php endif; ?>
                                    <span class="mx-2">•</span>
                                    <span>Ngày viết: <?= date('d/m/Y', strtotime($journal['journal_date'])) ?></span>
                                    <?php if ($journal['day_number']): ?>
                                        <span class="mx-2">•</span>
                                        <span>Ngày <?= $journal['day_number'] ?></span>
                                    <?php endif; ?>
                                    <span class="mx-2">•</span>
                                    <span><?= date('d/m/Y H:i', strtotime($journal['created_at'])) ?></span>
                                </div>
                                <p class="text-gray-600 line-clamp-2 mb-3">
                                    <?= htmlspecialchars(strip_tags(substr($journal['content'], 0, 200))) ?>
                                    <?= strlen($journal['content']) > 200 ? '...' : '' ?>
                                </p>
                                <?php if (!empty($journal['weather'])): ?>
                                    <div class="text-sm text-gray-500 mb-2">
                                        <span class="font-medium">Thời tiết:</span> <?= htmlspecialchars($journal['weather']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($journal['images'])): ?>
                                    <div class="flex gap-2 mb-3">
                                        <?php foreach (array_slice($journal['images'], 0, 3) as $img): ?>
                                            <img src="<?= BASE_URL . '/' . htmlspecialchars($img['image_url']) ?>"
                                                alt="Journal image" class="w-16 h-16 object-cover rounded border">
                                        <?php endforeach; ?>
                                        <?php if (count($journal['images']) > 3): ?>
                                            <div
                                                class="w-16 h-16 bg-gray-100 rounded border flex items-center justify-center text-xs text-gray-500">
                                                +<?= count($journal['images']) - 3 ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex gap-2">
                                <a href="?act=guide-journals&action=show&id=<?= $journal['id'] ?>"
                                    class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 text-sm font-medium">
                                    Xem
                                </a>
                                <a href="?act=guide-journals&action=edit&id=<?= $journal['id'] ?>"
                                    class="px-3 py-1.5 bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-100 text-sm font-medium">
                                    Sửa
                                </a>
                                <a href="?act=guide-journals&action=delete&id=<?= $journal['id'] ?>"
                                    onclick="return confirm('Bạn có chắc muốn xóa nhật ký này?')"
                                    class="px-3 py-1.5 bg-red-50 text-red-600 rounded hover:bg-red-100 text-sm font-medium">
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
            <div class="px-6 py-4 border-t border-gray-100 flex justify-center">
                <div class="flex gap-2">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?act=guide-journals&page=<?= $i ?><?= !empty($_GET['journal_date']) ? '&journal_date=' . htmlspecialchars($_GET['journal_date']) : '' ?><?= !empty($_GET['schedule_id']) ? '&schedule_id=' . (int) $_GET['schedule_id'] : '' ?>"
                            class="px-3 py-1 rounded border <?= $i == $current_page ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
