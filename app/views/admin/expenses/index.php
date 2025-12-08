<?php
/**
 * ADMIN - DANH SÁCH CHI PHÍ PHÁT SINH
 * Variables: $schedules, $total_pages, $current_page
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-8">
    <!-- Page Header -->
    <div class="mb-4 lg:mb-6">
        <div class="flex items-center gap-2 text-xs lg:text-sm text-primary-500 mb-2">
            <a href="?act=admin" class="hover:text-accent">Trang chủ</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span>Chi phí phát sinh</span>
        </div>
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý Chi phí phát sinh</h1>
    </div>

    <!-- Schedules List -->
    <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm overflow-hidden">
        <div class="p-4 lg:p-6 border-b border-primary-100">
            <h2 class="text-base lg:text-lg font-bold text-primary-700">Danh sách Lịch Tour</h2>
        </div>

        <?php if (empty($schedules)): ?>
            <div class="p-8 text-center text-primary-500">
                <p>Chưa có lịch tour nào.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[800px]">
                    <thead>
                        <tr class="bg-primary-50">
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase">Tour</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase">Ngày khởi hành</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase">Số lượng chi phí</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase text-right">Tổng chi phí</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary-100">
                        <?php foreach ($schedules as $schedule): ?>
                            <tr class="hover:bg-primary-50 transition-colors">
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <div class="font-semibold text-primary-700 text-sm"><?= htmlspecialchars($schedule['tour_name'] ?? 'N/A') ?></div>
                                    <div class="text-xs text-primary-500 font-mono"><?= htmlspecialchars($schedule['tour_code'] ?? '') ?></div>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 text-sm">
                                    <?= date('d/m/Y', strtotime($schedule['start_date'])) ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 text-sm">
                                    <?= $schedule['expense_count'] ?? 0 ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-right">
                                    <div class="font-bold text-primary-700 text-sm"><?= number_format($schedule['expense_total'] ?? 0, 0, ',', '.') ?> VNĐ</div>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                    <a href="?act=admin&module=expenses&action=show&schedule_id=<?= $schedule['id'] ?>" 
                                       class="text-accent hover:text-accent-hover text-xs lg:text-sm font-semibold">
                                        Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="p-4 lg:p-6 border-t border-primary-100 flex justify-center gap-2">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?act=admin&module=expenses&page=<?= $i ?>" 
                           class="px-3 py-1 rounded-lg text-sm font-semibold transition-colors <?= $i == $current_page ? 'bg-accent text-white' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

