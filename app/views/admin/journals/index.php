<div class="max-w-8xl mx-auto p-4 lg:p-8">
    <!-- Page Header - Responsive -->
    <div class="mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Nhật ký Tour</h1>
        <p class="text-xs lg:text-sm text-primary-500 mt-1">Xem nhật ký của các tour đã hoàn thành hoặc đang diễn ra</p>
    </div>

    <!-- Journals Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <?php if (!empty($journals)): ?>
            <?php foreach ($journals as $journal): ?>
                <div
                    class="bg-panel rounded-2xl border border-primary-100 overflow-hidden hover:shadow-md transition-all duration-200 flex flex-col h-full">
                    <div class="p-4 lg:p-5 flex-1">
                        <h5 class="text-base lg:text-lg font-bold text-primary-700 mb-2 line-clamp-2">
                            <a href="?act=admin&module=journals&action=show&id=<?= $journal['id'] ?>"
                                class="hover:text-accent transition-colors">
                                <?= htmlspecialchars($journal['title']) ?>
                            </a>
                        </h5>

                        <div class="flex flex-wrap items-center gap-2 text-xs text-primary-500 mb-3">
                            <span class="flex items-center gap-1 bg-primary-100 px-2 lg:px-3 py-1 rounded-full">
                                <i data-lucide="map-pin" class="w-3 h-3"></i>
                                <span class="truncate max-w-[120px] lg:max-w-[150px]"><?= htmlspecialchars($journal['tour_name']) ?></span>
                            </span>
                            <span class="flex items-center gap-1 bg-primary-100 px-2 lg:px-3 py-1 rounded-full">
                                <i data-lucide="calendar" class="w-3 h-3"></i>
                                <?= date('d/m/Y', strtotime($journal['start_date'])) ?>
                            </span>
                        </div>

                        <p class="text-xs lg:text-sm text-primary-600 line-clamp-3 mb-4">
                            <?= substr(strip_tags($journal['content']), 0, 150) ?>...
                        </p>
                    </div>

                    <div
                        class="px-4 lg:px-5 py-3 bg-primary-50 border-t border-primary-100 flex justify-between items-center text-xs text-primary-500">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-6 h-6 rounded-full bg-accent-100 text-accent-600 flex items-center justify-center font-bold text-xs">
                                <?= strtoupper(substr($journal['author_name'], 0, 1)) ?>
                            </div>
                            <span class="text-primary-600"><?= htmlspecialchars($journal['author_name']) ?></span>
                        </div>
                        <span title="<?= date('H:i d/m/Y', strtotime($journal['created_at'])) ?>" class="text-primary-500">
                            <?= date('d/m/Y', strtotime($journal['created_at'])) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-8 lg:py-12 text-center bg-panel rounded-2xl border border-primary-100 border-dashed">
                <div class="flex flex-col items-center justify-center">
                    <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="book-open" class="w-8 h-8 text-primary-400"></i>
                    </div>
                    <h3 class="text-base lg:text-lg font-semibold text-primary-700 mb-1">Chưa có nhật ký nào</h3>
                    <p class="text-sm text-primary-500">Chỉ hiển thị nhật ký của các tour đã hoàn thành hoặc đang diễn ra.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>