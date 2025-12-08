<div class="max-w-4xl mx-auto p-4 lg:p-8">
    <!-- Page Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Viết Nhật ký Tour</h1>
        <a href="?act=admin&module=journals" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 font-semibold rounded-xl hover:bg-primary-100 transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="p-4 lg:p-6">
            <form action="?act=admin&module=journals&action=store" method="POST" enctype="multipart/form-data" class="space-y-4 lg:space-y-6">
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Chọn Tour đã hoàn thành <span class="text-danger">*</span></label>
                    <select name="schedule_id" required
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                        <option value="">-- Chọn Tour --</option>
                        <?php if (isset($schedules['data'])): ?>
                            <?php foreach ($schedules['data'] as $schedule): ?>
                                <option value="<?= $schedule['id'] ?>">
                                    <?= htmlspecialchars($schedule['tour_name']) ?> (KH:
                                    <?= date('d/m/Y', strtotime($schedule['start_date'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tiêu đề <span class="text-danger">*</span></label>
                    <input type="text" name="title" required
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="Nhập tiêu đề nhật ký...">
                </div>

                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Nội dung</label>
                    <textarea name="content" rows="10"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="Chia sẻ câu chuyện về chuyến đi..."></textarea>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-primary-100">
                    <a href="?act=admin&module=journals" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 font-semibold rounded-xl hover:bg-primary-100 transition-colors text-sm lg:text-base text-center">
                        Hủy
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        Đăng nhật ký
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>