<?php
/**
 * GUIDE - DANH SÁCH TOUR ĐỂ GHI CHI PHÍ
 * Variables: $schedules, $total_pages, $current_page
 */
?>

<div class="max-w-8xl mx-auto p-4 lg:p-8">
    <!-- Header - Responsive -->
    <div class="mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Chi phí phát sinh</h1>
        <p class="text-xs lg:text-sm text-primary-500 mt-1">Quản lý chi phí phát sinh trong tour</p>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-panel rounded-2xl p-3 lg:p-4 mb-4 lg:mb-6 border border-primary-100 shadow-sm">
        <div class="flex flex-wrap gap-2">
            <a href="?act=guide-expenses&filter=upcoming" 
               class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-semibold transition-all <?= ($_GET['filter'] ?? 'upcoming') === 'upcoming' ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white shadow-sm' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' ?>">
                Sắp tới
            </a>
            <a href="?act=guide-expenses&filter=all" 
               class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-semibold transition-all <?= ($_GET['filter'] ?? '') === 'all' ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white shadow-sm' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' ?>">
                Tất cả
            </a>
            <a href="?act=guide-expenses&filter=history" 
               class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-semibold transition-all <?= ($_GET['filter'] ?? '') === 'history' ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white shadow-sm' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' ?>">
                Đã qua
            </a>
        </div>
    </div>

    <!-- Tours List -->
    <div class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[600px]">
                <thead>
                    <tr class="bg-primary-50">
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase tracking-wider">Tour</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase tracking-wider">Ngày khởi hành</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase tracking-wider">Tổng chi phí</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-100">
                    <?php if (empty($schedules)): ?>
                        <tr>
                            <td colspan="4" class="px-3 lg:px-4 py-8 lg:py-12 text-center text-primary-500">
                                Chưa có tour nào.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr class="hover:bg-primary-50 transition-colors">
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <div class="font-semibold text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($schedule['tour_name']) ?></div>
                                    <div class="text-xs text-primary-500 font-mono mt-1"><?= htmlspecialchars($schedule['tour_code']) ?></div>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 text-xs lg:text-sm">
                                    <?= date('d/m/Y', strtotime($schedule['start_date'])) ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <div class="font-bold text-primary-700 text-sm lg:text-base">
                                        <?= number_format($schedule['expense_total'] ?? 0) ?> VNĐ
                                    </div>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <a href="?act=guide-expenses&action=show&schedule_id=<?= $schedule['id'] ?>" 
                                       class="text-accent hover:text-accent-dark text-xs lg:text-sm font-semibold flex items-center gap-1">
                                        Xem chi tiết
                                        <i data-lucide="chevron-right" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="px-4 lg:px-6 py-3 lg:py-4 border-t border-primary-100 bg-primary-50 flex flex-col sm:flex-row justify-between items-center gap-3">
                <div class="text-xs lg:text-sm text-primary-600">
                    Trang <?= $current_page ?> / <?= $total_pages ?>
                </div>
                <div class="flex gap-2">
                    <?php if ($current_page > 1): ?>
                        <a href="?act=guide-expenses&page=<?= $current_page - 1 ?>&filter=<?= $_GET['filter'] ?? 'upcoming' ?>" 
                           class="px-3 lg:px-4 py-1.5 lg:py-2 border border-primary-100 rounded-xl hover:bg-primary-100 text-xs lg:text-sm font-semibold text-primary-700 transition-all flex items-center gap-1">
                            <i data-lucide="chevron-left" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                            Trước
                        </a>
                    <?php endif; ?>
                    <?php if ($current_page < $total_pages): ?>
                        <a href="?act=guide-expenses&page=<?= $current_page + 1 ?>&filter=<?= $_GET['filter'] ?? 'upcoming' ?>" 
                           class="px-3 lg:px-4 py-1.5 lg:py-2 border border-primary-100 rounded-xl hover:bg-primary-100 text-xs lg:text-sm font-semibold text-primary-700 transition-all flex items-center gap-1">
                            Sau
                            <i data-lucide="chevron-right" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

