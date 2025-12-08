<?php
/**
 * GUIDE - DANH SÁCH TOUR CẦN CHECK-IN
 * Variables: $schedules, $total_pages, $current_page
 */
?>

<div class="max-w-8xl mx-auto p-4 lg:p-8">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Check-in Hành khách</h1>
        
        <!-- Filter Buttons -->
        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
            <a href="?act=guide-checkin&filter=all" 
               class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-semibold transition-all border <?= (!isset($_GET['filter']) || $_GET['filter'] === 'all') ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white border-transparent shadow-sm' : 'bg-panel border-primary-100 text-primary-700 hover:bg-primary-50' ?>">
                Tất cả
            </a>
            <a href="?act=guide-checkin&filter=upcoming" 
               class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-semibold transition-all border <?= (isset($_GET['filter']) && $_GET['filter'] === 'upcoming') || !isset($_GET['filter']) ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white border-transparent shadow-sm' : 'bg-panel border-primary-100 text-primary-700 hover:bg-primary-50' ?>">
                Sắp tới
            </a>
            <a href="?act=guide-checkin&filter=history" 
               class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-semibold transition-all border <?= (isset($_GET['filter']) && $_GET['filter'] === 'history') ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white border-transparent shadow-sm' : 'bg-panel border-primary-100 text-primary-700 hover:bg-primary-50' ?>">
                Đã qua
            </a>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-4 lg:mb-6">
        <?php
        $total_tours = count($schedules);
        $total_passengers = 0;
        $total_checked = 0;
        foreach ($schedules as $s) {
            if (isset($s['checkin_stats'])) {
                $total_passengers += $s['checkin_stats']['total'] ?? 0;
                $total_checked += $s['checkin_stats']['checked_in'] ?? 0;
            }
        }
        ?>
        <div class="bg-info-bg border border-info rounded-2xl p-3 lg:p-4">
            <div class="text-info-text text-xs lg:text-sm font-semibold mb-1 lg:mb-2">Tổng số tour</div>
            <div class="text-xl lg:text-2xl font-bold text-info-dark"><?= $total_tours ?></div>
        </div>
        <div class="bg-success-bg border border-success rounded-2xl p-3 lg:p-4">
            <div class="text-success-text text-xs lg:text-sm font-semibold mb-1 lg:mb-2">Tổng hành khách</div>
            <div class="text-xl lg:text-2xl font-bold text-success-dark"><?= $total_passengers ?></div>
        </div>
        <div class="bg-warning-bg border border-warning rounded-2xl p-3 lg:p-4">
            <div class="text-warning-text text-xs lg:text-sm font-semibold mb-1 lg:mb-2">Đã check-in</div>
            <div class="text-xl lg:text-2xl font-bold text-warning-dark"><?= $total_checked ?></div>
        </div>
        <div class="bg-danger-bg border border-danger rounded-2xl p-3 lg:p-4">
            <div class="text-danger-text text-xs lg:text-sm font-semibold mb-1 lg:mb-2">Chưa check-in</div>
            <div class="text-xl lg:text-2xl font-bold text-danger-dark"><?= $total_passengers - $total_checked ?></div>
        </div>
    </div>

    <!-- Tours List -->
    <div class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm">
        <?php if (empty($schedules)): ?>
            <div class="p-8 lg:p-12 text-center">
                <div class="text-primary-300 mb-4 flex justify-center">
                    <i data-lucide="clipboard-check" class="w-16 h-16"></i>
                </div>
                <p class="text-primary-600 font-semibold text-sm lg:text-base">Không có tour nào cần check-in</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[900px]">
                    <thead>
                        <tr class="bg-primary-50 text-primary-600 text-xs uppercase tracking-wider">
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Tour</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Ngày khởi hành</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Ngày kết thúc</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Thời lượng</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Hành khách</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Check-in</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary-100">
                        <?php foreach ($schedules as $schedule): ?>
                            <?php
                            $stats = $schedule['checkin_stats'] ?? ['total' => 0, 'checked_in' => 0, 'present' => 0, 'absent' => 0, 'late' => 0];
                            $progress = $stats['total'] > 0 ? ($stats['checked_in'] / $stats['total']) * 100 : 0;
                            ?>
                            <tr class="hover:bg-primary-50 transition-colors">
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <div class="font-semibold text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($schedule['tour_name']) ?></div>
                                    <div class="text-xs text-primary-500 font-mono mt-1"><?= htmlspecialchars($schedule['tour_code']) ?></div>
                                    <?php if (!empty($schedule['departure_location'])): ?>
                                        <div class="text-xs text-primary-500 mt-1 flex items-center gap-1">
                                            <i data-lucide="map-pin" class="w-3 h-3"></i>
                                            <?= htmlspecialchars($schedule['departure_location']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 text-xs lg:text-sm">
                                    <?= date('d/m/Y', strtotime($schedule['start_date'])) ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 text-xs lg:text-sm">
                                    <?= date('d/m/Y', strtotime($schedule['end_date'])) ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 text-xs lg:text-sm">
                                    <?= $schedule['duration_days'] ?? 'N/A' ?> ngày <?= $schedule['duration_nights'] ?? 'N/A' ?> đêm
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <div class="text-xs lg:text-sm">
                                        <span class="font-semibold text-primary-700"><?= $stats['total'] ?></span> khách
                                    </div>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-primary-100 rounded-full h-2 min-w-[80px] lg:min-w-[100px]">
                                            <div class="bg-success h-2 rounded-full transition-all" style="width: <?= $progress ?>%"></div>
                                        </div>
                                        <span class="text-xs text-primary-600 font-semibold">
                                            <?= $stats['checked_in'] ?>/<?= $stats['total'] ?>
                                        </span>
                                    </div>
                                    <div class="text-xs text-primary-500 mt-1 flex items-center gap-1 flex-wrap">
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="check-circle" class="w-3 h-3 text-success-text"></i>
                                            <?= $stats['present'] ?>
                                        </span>
                                        <span>•</span>
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="x-circle" class="w-3 h-3 text-danger-text"></i>
                                            <?= $stats['absent'] ?>
                                        </span>
                                        <span>•</span>
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="clock" class="w-3 h-3 text-warning-text"></i>
                                            <?= $stats['late'] ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-right">
                                    <div class="flex flex-col sm:flex-row gap-2 justify-end">
                                        <a href="?act=guide-checkin&action=show&schedule_id=<?= $schedule['id'] ?>"
                                            class="px-3 lg:px-4 py-1.5 lg:py-2 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl text-xs lg:text-sm font-semibold shadow-sm transition-all flex items-center justify-center gap-1">
                                            <i data-lucide="check-square" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                            Check-in
                                        </a>
                                        <a href="?act=guide-checkin&action=printManifest&schedule_id=<?= $schedule['id'] ?>"
                                            target="_blank"
                                            class="px-3 lg:px-4 py-1.5 lg:py-2 bg-primary-600 hover:opacity-90 text-white rounded-xl text-xs lg:text-sm font-semibold shadow-sm transition-all flex items-center justify-center gap-1">
                                            <i data-lucide="printer" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                            In danh sách
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="px-4 lg:px-6 py-3 lg:py-4 border-t border-primary-100 bg-primary-50 flex justify-center">
                    <div class="flex gap-1 lg:gap-2">
                    <?php 
                    $filter_param = isset($_GET['filter']) ? '&filter=' . htmlspecialchars($_GET['filter']) : '';
                    for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?act=guide-checkin&page=<?= $i ?><?= $filter_param ?>"
                            class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-semibold transition-all <?= $i == $current_page ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white shadow-sm' : 'bg-panel border border-primary-100 text-primary-700 hover:bg-primary-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

