<?php
/**
 * GUIDE - DANH SÁCH TOUR CẦN CHECK-IN
 * Variables: $schedules, $total_pages, $current_page
 */
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Check-in Hành khách</h1>
        
        <!-- Filter Buttons -->
        <div class="flex gap-2">
            <a href="?act=guide-checkin&filter=all" 
               class="px-4 py-2 rounded border <?= (!isset($_GET['filter']) || $_GET['filter'] === 'all') ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">
                Tất cả
            </a>
            <a href="?act=guide-checkin&filter=upcoming" 
               class="px-4 py-2 rounded border <?= (isset($_GET['filter']) && $_GET['filter'] === 'upcoming') || !isset($_GET['filter']) ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">
                Sắp tới
            </a>
            <a href="?act=guide-checkin&filter=history" 
               class="px-4 py-2 rounded border <?= (isset($_GET['filter']) && $_GET['filter'] === 'history') ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">
                Đã qua
            </a>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
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
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="text-blue-600 text-sm font-medium mb-1">Tổng số tour</div>
            <div class="text-2xl font-bold text-blue-900"><?= $total_tours ?></div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="text-green-600 text-sm font-medium mb-1">Tổng hành khách</div>
            <div class="text-2xl font-bold text-green-900"><?= $total_passengers ?></div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="text-yellow-600 text-sm font-medium mb-1">Đã check-in</div>
            <div class="text-2xl font-bold text-yellow-900"><?= $total_checked ?></div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="text-red-600 text-sm font-medium mb-1">Chưa check-in</div>
            <div class="text-2xl font-bold text-red-900"><?= $total_passengers - $total_checked ?></div>
        </div>
    </div>

    <!-- Tours List -->
    <div class="bg-panel rounded overflow-hidden border border-slate-200">
        <?php if (empty($schedules)): ?>
            <div class="p-12 text-center">
                <div class="text-gray-300 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <p class="text-gray-500 font-medium">Không có tour nào cần check-in</p>
            </div>
        <?php else: ?>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">Tour</th>
                        <th class="px-6 py-4 font-medium">Ngày khởi hành</th>
                        <th class="px-6 py-4 font-medium">Ngày kết thúc</th>
                        <th class="px-6 py-4 font-medium">Thời lượng</th>
                        <th class="px-6 py-4 font-medium">Hành khách</th>
                        <th class="px-6 py-4 font-medium">Check-in</th>
                        <th class="px-6 py-4 font-medium text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($schedules as $schedule): ?>
                        <?php
                        $stats = $schedule['checkin_stats'] ?? ['total' => 0, 'checked_in' => 0, 'present' => 0, 'absent' => 0, 'late' => 0];
                        $progress = $stats['total'] > 0 ? ($stats['checked_in'] / $stats['total']) * 100 : 0;
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900"><?= htmlspecialchars($schedule['tour_name']) ?></div>
                                <div class="text-xs text-gray-500 font-mono"><?= htmlspecialchars($schedule['tour_code']) ?></div>
                                <?php if (!empty($schedule['departure_location'])): ?>
                                    <div class="text-xs text-gray-500 mt-1">📍 <?= htmlspecialchars($schedule['departure_location']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?= date('d/m/Y', strtotime($schedule['start_date'])) ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?= date('d/m/Y', strtotime($schedule['end_date'])) ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-sm">
                                <?= $schedule['duration_days'] ?? 'N/A' ?> ngày <?= $schedule['duration_nights'] ?? 'N/A' ?> đêm
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <span class="font-medium"><?= $stats['total'] ?></span> khách
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 min-w-[100px]">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: <?= $progress ?>%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600 font-medium">
                                        <?= $stats['checked_in'] ?>/<?= $stats['total'] ?>
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    ✅ <?= $stats['present'] ?> • ❌ <?= $stats['absent'] ?> • ⏰ <?= $stats['late'] ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex gap-2 justify-end">
                                    <a href="?act=guide-checkin&action=show&schedule_id=<?= $schedule['id'] ?>"
                                        class="px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
                                        Check-in
                                    </a>
                                    <a href="?act=guide-checkin&action=printManifest&schedule_id=<?= $schedule['id'] ?>"
                                        target="_blank"
                                        class="px-3 py-1.5 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm font-medium">
                                        📄 In danh sách
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="px-6 py-4 border-t border-gray-100 flex justify-center">
                    <div class="flex gap-2">
                    <?php 
                    $filter_param = isset($_GET['filter']) ? '&filter=' . htmlspecialchars($_GET['filter']) : '';
                    for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?act=guide-checkin&page=<?= $i ?><?= $filter_param ?>"
                            class="px-3 py-1 rounded border <?= $i == $current_page ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

