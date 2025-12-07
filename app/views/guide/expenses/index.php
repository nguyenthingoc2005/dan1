<?php
/**
 * GUIDE - DANH SÁCH TOUR ĐỂ GHI CHI PHÍ
 * Variables: $schedules, $total_pages, $current_page
 */
?>

<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Chi phí phát sinh</h1>
            <p class="text-slate-500 text-sm mt-1">Quản lý chi phí phát sinh trong tour</p>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-panel rounded p-4 mb-6 border border-slate-200">
        <div class="flex gap-2">
            <a href="?act=guide-expenses&filter=upcoming" 
               class="px-4 py-2 rounded <?= ($_GET['filter'] ?? 'upcoming') === 'upcoming' ? 'bg-accent text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                Sắp tới
            </a>
            <a href="?act=guide-expenses&filter=all" 
               class="px-4 py-2 rounded <?= ($_GET['filter'] ?? '') === 'all' ? 'bg-accent text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                Tất cả
            </a>
            <a href="?act=guide-expenses&filter=history" 
               class="px-4 py-2 rounded <?= ($_GET['filter'] ?? '') === 'history' ? 'bg-accent text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                Đã qua
            </a>
        </div>
    </div>

    <!-- Tours List -->
    <div class="bg-panel rounded overflow-hidden border border-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Tour</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Ngày khởi hành</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Tổng chi phí</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($schedules)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                Chưa có tour nào.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-slate-900"><?= htmlspecialchars($schedule['tour_name']) ?></div>
                                    <div class="text-xs text-slate-500 font-mono"><?= htmlspecialchars($schedule['tour_code']) ?></div>
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-sm">
                                    <?= date('d/m/Y', strtotime($schedule['start_date'])) ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900">
                                        <?= number_format($schedule['expense_total'] ?? 0) ?> VNĐ
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="?act=guide-expenses&action=show&schedule_id=<?= $schedule['id'] ?>" 
                                       class="text-accent hover:text-accent/80 text-sm font-medium">
                                        Xem chi tiết →
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
            <div class="px-6 py-4 border-t border-slate-200 flex justify-between items-center">
                <div class="text-sm text-slate-600">
                    Trang <?= $current_page ?> / <?= $total_pages ?>
                </div>
                <div class="flex gap-2">
                    <?php if ($current_page > 1): ?>
                        <a href="?act=guide-expenses&page=<?= $current_page - 1 ?>&filter=<?= $_GET['filter'] ?? 'upcoming' ?>" 
                           class="px-3 py-1 border border-slate-300 rounded hover:bg-slate-50">
                            ← Trước
                        </a>
                    <?php endif; ?>
                    <?php if ($current_page < $total_pages): ?>
                        <a href="?act=guide-expenses&page=<?= $current_page + 1 ?>&filter=<?= $_GET['filter'] ?? 'upcoming' ?>" 
                           class="px-3 py-1 border border-slate-300 rounded hover:bg-slate-50">
                            Sau →
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

