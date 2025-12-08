<?php
/**
 * GUIDE - DANH SÁCH TOUR SCHEDULES CẦN CHECK-IN
 * Variables: $schedules, $total_pages, $current_page, $filter_type
 */
?>

<div class="max-w-6xl mx-auto p-4 lg:p-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Check-in theo Hoạt động</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Quản lý check-in chi tiết theo từng checkpoint</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-panel rounded-2xl p-4 lg:p-5 mb-4 lg:mb-6 border border-primary-100">
        <div class="flex flex-wrap gap-2">
            <a href="?act=guide-activity-checkin&filter=upcoming" 
                class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-semibold transition-all <?= ($filter_type ?? 'upcoming') == 'upcoming' ? 'bg-accent text-white' : 'bg-primary-50 text-primary-700 hover:bg-primary-100' ?>">
                Sắp tới
            </a>
            <a href="?act=guide-activity-checkin&filter=all" 
                class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-semibold transition-all <?= ($filter_type ?? '') == 'all' ? 'bg-accent text-white' : 'bg-primary-50 text-primary-700 hover:bg-primary-100' ?>">
                Tất cả
            </a>
            <a href="?act=guide-activity-checkin&filter=history" 
                class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-semibold transition-all <?= ($filter_type ?? '') == 'history' ? 'bg-accent text-white' : 'bg-primary-50 text-primary-700 hover:bg-primary-100' ?>">
                Đã qua
            </a>
        </div>
    </div>

    <?php if (empty($schedules)): ?>
        <div class="bg-panel rounded-2xl p-8 lg:p-12 text-center border border-primary-100">
            <i data-lucide="calendar-x" class="w-12 h-12 lg:w-16 lg:h-16 text-primary-300 mx-auto mb-4"></i>
            <h3 class="text-lg lg:text-xl font-semibold text-primary-700 mb-2">Chưa có tour nào</h3>
            <p class="text-sm lg:text-base text-primary-500">Bạn chưa được phân công tour nào</p>
        </div>
    <?php else: ?>
        <div class="space-y-3 lg:space-y-4">
            <?php foreach ($schedules as $schedule): ?>
                <div class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="p-4 lg:p-6">
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                            <div class="flex-1">
                                <h3 class="text-base lg:text-lg font-bold text-primary-700 mb-2">
                                    <?= htmlspecialchars($schedule['tour_code'] ?? 'N/A') ?> - <?= htmlspecialchars($schedule['tour_name'] ?? '') ?>
                                </h3>
                                <div class="flex flex-wrap items-center gap-3 lg:gap-4 text-xs lg:text-sm text-primary-500">
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="calendar" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                        <?= date('d/m/Y', strtotime($schedule['start_date'])) ?> - <?= date('d/m/Y', strtotime($schedule['end_date'])) ?>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="users" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                        <?= $schedule['booked'] ?> / <?= $schedule['quota'] ?> khách
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="map-pin" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                        <?= $schedule['checkpoint_count'] ?? 0 ?> checkpoint(s)
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                                <a href="?act=guide-checkpoints&action=index&schedule_id=<?= $schedule['id'] ?>" 
                                    class="w-full sm:w-auto px-3 lg:px-4 py-1.5 lg:py-2 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 text-xs lg:text-sm font-semibold transition-colors flex items-center justify-center gap-1">
                                    <i data-lucide="settings" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                    Quản lý Checkpoints
                                </a>
                                <a href="?act=guide-activity-checkin&action=checkpoints&schedule_id=<?= $schedule['id'] ?>" 
                                    class="w-full sm:w-auto px-3 lg:px-4 py-1.5 lg:py-2 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl text-xs lg:text-sm font-semibold transition-all flex items-center justify-center gap-1">
                                    <i data-lucide="check-circle" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                    Check-in
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-4 lg:mt-6 flex justify-center">
                <div class="flex gap-2">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?act=guide-activity-checkin&page=<?= $i ?>&filter=<?= $filter_type ?? 'upcoming' ?>" 
                            class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-semibold transition-all <?= $i == $current_page ? 'bg-accent text-white' : 'bg-primary-50 text-primary-700 hover:bg-primary-100' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

