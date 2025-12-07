<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Nhật ký Tour</h1>
            <p class="text-sm text-gray-500 mt-1">Xem nhật ký của các tour đã hoàn thành hoặc đang diễn ra</p>
        </div>
    </div>

    <!-- Journals Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (!empty($journals)): ?>
            <?php foreach ($journals as $journal): ?>
                <div
                    class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col h-full">
                    <!-- <img src="..." class="w-full h-48 object-cover" alt="..."> -->
                    <div class="p-5 flex-1">
                        <h5 class="text-lg font-bold text-slate-800 mb-2 line-clamp-2">
                            <a href="?act=admin&module=journals&action=show&id=<?= $journal['id'] ?>"
                                class="hover:text-blue-600 transition-colors">
                                <?= htmlspecialchars($journal['title']) ?>
                            </a>
                        </h5>

                        <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                            <span class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                                <span class="truncate max-w-[150px]"><?= htmlspecialchars($journal['tour_name']) ?></span>
                            </span>
                            <span class="flex items-center gap-1 bg-gray-100 px-2 py-1 rounded">
                                <i class="fas fa-calendar-alt text-gray-400"></i>
                                <?= date('d/m/Y', strtotime($journal['start_date'])) ?>
                            </span>
                        </div>

                        <p class="text-sm text-gray-600 line-clamp-3 mb-4">
                            <?= substr(strip_tags($journal['content']), 0, 150) ?>...
                        </p>
                    </div>

                    <div
                        class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                                <?= strtoupper(substr($journal['author_name'], 0, 1)) ?>
                            </div>
                            <span><?= htmlspecialchars($journal['author_name']) ?></span>
                        </div>
                        <span title="<?= date('H:i d/m/Y', strtotime($journal['created_at'])) ?>">
                            <?= date('d/m/Y', strtotime($journal['created_at'])) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full py-12 text-center bg-white rounded-xl border border-gray-200 border-dashed">
                <div class="flex flex-col items-center justify-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-book-open text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Chưa có nhật ký nào</h3>
                    <p class="text-gray-500 mb-4">Chỉ hiển thị nhật ký của các tour đã hoàn thành hoặc đang diễn ra.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>