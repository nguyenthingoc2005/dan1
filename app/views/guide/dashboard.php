<?php
/**
 * GUIDE DASHBOARD - Thống kê cá nhân
 * Variables: $stats, $my_schedules, $completed_tours
 * Flat Design - Không shadow, không gradient, dùng spacing thay border
 */
?>

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 mb-2">Dashboard</h1>
        <p class="text-slate-500 text-sm">Tổng quan hoạt động của bạn</p>
    </div>

    <!-- Thống kê cá nhân -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-slate-700 mb-6">Thống kê cá nhân</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <!-- Tour sắp tới -->
            <div class="bg-panel rounded p-5 border border-slate-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs text-slate-500 uppercase tracking-wide mb-2">Tour Sắp Tới</p>
                        <h3 class="text-3xl font-bold text-slate-800"><?= $stats['upcoming_tours'] ?? 0 ?></h3>
                    </div>
                    <div class="text-2xl opacity-60">📅</div>
                </div>
            </div>

            <!-- Tour đã hoàn thành -->
            <div class="bg-panel rounded p-5 border border-slate-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs text-slate-500 uppercase tracking-wide mb-2">Tour Đã Hoàn Thành</p>
                        <h3 class="text-3xl font-bold text-slate-800"><?= $stats['completed_tours'] ?? 0 ?></h3>
                    </div>
                    <div class="text-2xl opacity-60">✅</div>
                </div>
            </div>

            <!-- Tổng số ngày làm việc -->
            <div class="bg-panel rounded p-5 border border-slate-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs text-slate-500 uppercase tracking-wide mb-2">Tổng Ngày Làm Việc</p>
                        <h3 class="text-3xl font-bold text-slate-800"><?= $stats['total_working_days'] ?? 0 ?></h3>
                        <p class="text-xs text-slate-400 mt-1">ngày</p>
                    </div>
                    <div class="text-2xl opacity-60">📊</div>
                </div>
            </div>

            <!-- Khách hàng đã phục vụ -->
            <div class="bg-panel rounded p-5 border border-slate-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs text-slate-500 uppercase tracking-wide mb-2">Khách Đã Phục Vụ</p>
                        <h3 class="text-3xl font-bold text-slate-800"><?= $stats['customers_served'] ?? 0 ?></h3>
                        <p class="text-xs text-slate-400 mt-1">người</p>
                    </div>
                    <div class="text-2xl opacity-60">👥</div>
                </div>
            </div>

            <!-- Tổng tour được phân công -->
            <div class="bg-panel rounded p-5 border border-slate-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs text-slate-500 uppercase tracking-wide mb-2">Tổng Tour Được Phân Công</p>
                        <h3 class="text-3xl font-bold text-slate-800"><?= $stats['total_tours_assigned'] ?? 0 ?></h3>
                    </div>
                    <div class="text-2xl opacity-60">✈️</div>
                </div>
            </div>

            <!-- Tổng chi phí phát sinh -->
            <div class="bg-panel rounded p-5 border border-slate-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs text-slate-500 uppercase tracking-wide mb-2">Tổng Chi Phí Phát Sinh</p>
                        <h3 class="text-2xl font-bold text-slate-800"><?= number_format($stats['total_expenses'] ?? 0) ?></h3>
                        <p class="text-xs text-slate-400 mt-1">VNĐ</p>
                    </div>
                    <div class="text-2xl opacity-60">💰</div>
                </div>
            </div>

            <!-- Số nhật ký -->
            <div class="bg-panel rounded p-5 border border-slate-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs text-slate-500 uppercase tracking-wide mb-2">Nhật Ký Đã Viết</p>
                        <h3 class="text-3xl font-bold text-slate-800"><?= $stats['journals_count'] ?? 0 ?></h3>
                    </div>
                    <div class="text-2xl opacity-60">📔</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tour sắp tới và đã hoàn thành -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Tours List -->
        <div class="bg-panel rounded border border-slate-200">
            <div class="p-5 border-b border-slate-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-slate-800">Tour Sắp Tới</h2>
                <a href="?act=guide-tours&filter=upcoming" class="text-sm text-accent hover:text-accent/80 font-medium">Xem tất cả →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Mã Tour</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Tên Tour</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Khởi hành</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($my_schedules)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    Bạn chưa có lịch tour nào sắp tới.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($my_schedules as $s): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="font-mono text-accent font-medium text-sm"><?= htmlspecialchars($s['tour_code']) ?></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-slate-900"><?= htmlspecialchars($s['tour_name']) ?></div>
                                        <div class="text-xs text-slate-500 mt-0.5"><?= $s['duration_days'] ?>N<?= $s['duration_nights'] ?>Đ</div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 text-sm">
                                        <?= date('d/m/Y', strtotime($s['start_date'])) ?>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="?act=guide-tours&action=show&id=<?= $s['id'] ?>"
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
        </div>

        <!-- Completed Tours List -->
        <div class="bg-panel rounded border border-slate-200">
            <div class="p-5 border-b border-slate-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-slate-800">Tour Đã Hoàn Thành</h2>
                <a href="?act=guide-tours&filter=history" class="text-sm text-accent hover:text-accent/80 font-medium">Xem tất cả →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Mã Tour</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Tên Tour</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Kết thúc</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($completed_tours)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    Chưa có tour nào đã hoàn thành.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($completed_tours as $s): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="font-mono text-accent font-medium text-sm"><?= htmlspecialchars($s['tour_code']) ?></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-slate-900"><?= htmlspecialchars($s['tour_name']) ?></div>
                                        <div class="text-xs text-slate-500 mt-0.5"><?= $s['duration_days'] ?>N<?= $s['duration_nights'] ?>Đ</div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 text-sm">
                                        <?= date('d/m/Y', strtotime($s['end_date'])) ?>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="?act=guide-tours&action=show&id=<?= $s['id'] ?>"
                                            class="text-accent hover:text-accent/80 text-sm font-medium">
                                            Xem lại →
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>