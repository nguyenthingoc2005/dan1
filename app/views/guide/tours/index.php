<?php
/**
 * GUIDE - MY TOURS LIST
 */
?>

<div class="max-w-6xl mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Lịch Tour Của Tôi</h1>

        <!-- Filter Buttons - Responsive -->
        <div class="flex gap-2 w-full sm:w-auto">
            <a href="?act=guide-tours&filter=all"
                class="flex-1 sm:flex-none px-3 lg:px-4 py-2 rounded-xl border font-semibold text-xs lg:text-sm transition-colors <?= (!isset($_GET['filter']) || $_GET['filter'] === 'all') ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white border-transparent' : 'bg-panel text-primary-700 border-primary-100 hover:bg-primary-50' ?>">
                Tất cả
            </a>
            <a href="?act=guide-tours&filter=upcoming"
                class="flex-1 sm:flex-none px-3 lg:px-4 py-2 rounded-xl border font-semibold text-xs lg:text-sm transition-colors <?= (isset($_GET['filter']) && $_GET['filter'] === 'upcoming') ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white border-transparent' : 'bg-panel text-primary-700 border-primary-100 hover:bg-primary-50' ?>">
                Sắp tới
            </a>
            <a href="?act=guide-tours&filter=history"
                class="flex-1 sm:flex-none px-3 lg:px-4 py-2 rounded-xl border font-semibold text-xs lg:text-sm transition-colors <?= (isset($_GET['filter']) && $_GET['filter'] === 'history') ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white border-transparent' : 'bg-panel text-primary-700 border-primary-100 hover:bg-primary-50' ?>">
                Đã qua
            </a>
        </div>
    </div>

    <!-- Table - Responsive -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr class="bg-primary-50">
                        <th
                            class="px-3 lg:px-6 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Mã Tour</th>
                        <th
                            class="px-3 lg:px-6 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Tên Tour</th>
                        <th
                            class="px-3 lg:px-6 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Khởi hành</th>
                        <th
                            class="px-3 lg:px-6 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Kết thúc</th>
                        <th
                            class="px-3 lg:px-6 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Khách</th>
                        <th
                            class="px-3 lg:px-6 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider text-right">
                            Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-100">
                    <?php if (empty($schedules)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-primary-500">
                                Không tìm thấy lịch tour nào.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($schedules as $s): ?>
                            <tr class="hover:bg-primary-50 transition-colors">
                                <td class="px-3 lg:px-6 py-3 lg:py-4">
                                    <span
                                        class="font-mono text-accent font-semibold text-sm"><?= htmlspecialchars($s['tour_code']) ?></span>
                                </td>
                                <td class="px-3 lg:px-6 py-3 lg:py-4">
                                    <div class="font-semibold text-primary-700 text-sm"><?= htmlspecialchars($s['tour_name']) ?>
                                    </div>
                                    <?php if (!empty($s['guide_notes'])): ?>
                                        <div class="text-xs text-warning-text mt-1 flex items-center gap-1">
                                            <i data-lucide="sticky-note" class="w-3 h-3"></i>
                                            <?= htmlspecialchars($s['guide_notes']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 lg:px-6 py-3 lg:py-4 text-primary-700 text-sm">
                                    <?= date('d/m/Y', strtotime($s['start_date'])) ?>
                                </td>
                                <td class="px-3 lg:px-6 py-3 lg:py-4 text-primary-700 text-sm">
                                    <?= date('d/m/Y', strtotime($s['end_date'])) ?>
                                </td>
                                <td class="px-3 lg:px-6 py-3 lg:py-4">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-success-bg text-success-text">
                                        <?= $s['booked'] ?> / <?= $s['quota'] ?>
                                    </span>
                                </td>
                                <td class="px-3 lg:px-6 py-3 lg:py-4 text-right">
                                    <a href="?act=guide-tours&action=show&id=<?= $s['id'] ?>"
                                        class="inline-block px-3 lg:px-4 py-1.5 lg:py-2 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all text-xs lg:text-sm">
                                        Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination - Responsive -->
        <?php if ($total_pages > 1): ?>
            <div class="px-4 lg:px-6 py-3 lg:py-4 border-t border-primary-100 flex justify-center">
                <div class="flex gap-2 flex-wrap">
                    <?php
                    $filter_param = isset($_GET['filter']) ? '&filter=' . htmlspecialchars($_GET['filter']) : '';
                    for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?act=guide-tours&page=<?= $i ?><?= $filter_param ?>"
                            class="px-3 py-1.5 rounded-xl text-sm font-semibold transition-colors <?= $i == $current_page ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white' : 'bg-panel border border-primary-100 text-primary-700 hover:bg-primary-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>