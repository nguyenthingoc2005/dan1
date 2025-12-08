<div class="max-w-4xl mx-auto p-4 lg:p-8">
    <!-- Page Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Chi tiết Nhật ký</h1>
        <a href="?act=admin&module=journals" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 font-semibold rounded-xl hover:bg-primary-100 transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="p-4 lg:p-6 border-b border-primary-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h4 class="text-base lg:text-lg font-bold text-primary-700 mb-1 lg:mb-2"><?= htmlspecialchars($journal['title']) ?></h4>
                <p class="text-xs lg:text-sm text-primary-500 flex flex-wrap items-center gap-2">
                    <span class="flex items-center gap-1">
                        <i data-lucide="user" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                        <?= htmlspecialchars($journal['author_name']) ?>
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1">
                        <i data-lucide="clock" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                        <?= date('d/m/Y H:i', strtotime($journal['created_at'])) ?>
                    </span>
                </p>
            </div>
            <div>
                <span class="px-3 lg:px-4 py-1.5 lg:py-2 bg-info-bg text-info-text rounded-full text-xs lg:text-sm font-semibold flex items-center gap-1">
                    <i data-lucide="map-pin" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                    <?= htmlspecialchars($journal['tour_name']) ?>
                </span>
            </div>
        </div>
        <div class="p-4 lg:p-6">
            <div class="journal-content text-sm lg:text-base text-primary-700 leading-relaxed whitespace-pre-wrap">
                <?= nl2br(htmlspecialchars($journal['content'])) ?>
            </div>
        </div>
    </div>
</div>