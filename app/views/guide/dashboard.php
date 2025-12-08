<?php
/**
 * GUIDE DASHBOARD - Dashboard hiện đại với metrics và quick actions
 * Variables: $stats, $my_schedules, $completed_tours, $ongoing_tours, $recent_journals, $user
 * Flat Design - Không shadow, không gradient, dùng spacing thay border
 */
$user = get_auth_user();
?>

<div class="max-w-7xl mx-auto">
    <!-- Welcome Header -->
    <div class="mb-6 lg:mb-8">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700 mb-2">
            Chào mừng trở lại, <?= htmlspecialchars($user['full_name']) ?>! 👋
        </h1>
        <p class="text-sm lg:text-base text-primary-500">Tổng quan hoạt động của bạn hôm nay</p>
    </div>

    <!-- Quick Actions -->
    <div class="mb-6 lg:mb-8">
        <div class="bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to rounded-2xl p-4 lg:p-6">
            <h2 class="text-base lg:text-lg font-semibold text-white mb-4">Thao tác nhanh</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 lg:gap-4">
                <a href="?act=guide-tours&filter=upcoming"
                    class="bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl p-3 lg:p-4 text-white text-center transition-all">
                    <i data-lucide="calendar" class="w-5 h-5 lg:w-6 lg:h-6 mx-auto mb-2"></i>
                    <div class="text-xs lg:text-sm font-semibold">Tour sắp tới</div>
                </a>
                <a href="?act=guide-tours"
                    class="bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl p-3 lg:p-4 text-white text-center transition-all">
                    <i data-lucide="map-pin" class="w-5 h-5 lg:w-6 lg:h-6 mx-auto mb-2"></i>
                    <div class="text-xs lg:text-sm font-semibold">Lịch Tour</div>
                </a>
                <a href="?act=guide-journals&action=create"
                    class="bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl p-3 lg:p-4 text-white text-center transition-all">
                    <i data-lucide="book-open" class="w-5 h-5 lg:w-6 lg:h-6 mx-auto mb-2"></i>
                    <div class="text-xs lg:text-sm font-semibold">Viết nhật ký</div>
                </a>
                <a href="?act=guide-expenses&action=create"
                    class="bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-xl p-3 lg:p-4 text-white text-center transition-all">
                    <i data-lucide="dollar-sign" class="w-5 h-5 lg:w-6 lg:h-6 mx-auto mb-2"></i>
                    <div class="text-xs lg:text-sm font-semibold">Thêm chi phí</div>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="mb-6 lg:mb-8">
        <h2 class="text-base lg:text-lg font-semibold text-primary-700 mb-4 lg:mb-6">Thống kê tổng quan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <!-- Tour đang diễn ra -->
            <div class="bg-panel rounded-2xl p-4 lg:p-5 shadow-sm border border-primary-100">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs text-primary-500 uppercase tracking-wide mb-2">Tour Đang Diễn Ra</p>
                        <h3 class="text-2xl lg:text-3xl font-bold text-primary-700"><?= $stats['ongoing_tours'] ?? 0 ?>
                        </h3>
                        <p class="text-xs text-primary-500 mt-1">tour hiện tại</p>
                    </div>
                    <div class="p-3 bg-accent/10 rounded-xl">
                        <i data-lucide="play-circle" class="w-6 h-6 lg:w-8 lg:h-8 text-accent"></i>
                    </div>
                </div>
            </div>

            <!-- Tour sắp tới -->
            <div class="bg-panel rounded-2xl p-4 lg:p-5 shadow-sm border border-primary-100">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs text-primary-500 uppercase tracking-wide mb-2">Tour Sắp Tới</p>
                        <h3 class="text-2xl lg:text-3xl font-bold text-primary-700"><?= $stats['upcoming_tours'] ?? 0 ?>
                        </h3>
                        <p class="text-xs text-primary-500 mt-1"><?= $stats['upcoming_7days'] ?? 0 ?> trong 7 ngày tới</p>
                    </div>
                    <div class="p-3 bg-info/10 rounded-xl">
                        <i data-lucide="calendar" class="w-6 h-6 lg:w-8 lg:h-8 text-info"></i>
                    </div>
                </div>
            </div>

            <!-- Hành khách sắp tới -->
            <div class="bg-panel rounded-2xl p-4 lg:p-5 shadow-sm border border-primary-100">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs text-primary-500 uppercase tracking-wide mb-2">Hành Khách Sắp Tới</p>
                        <h3 class="text-2xl lg:text-3xl font-bold text-primary-700">
                            <?= $stats['upcoming_passengers'] ?? 0 ?></h3>
                        <p class="text-xs text-primary-500 mt-1">người</p>
                    </div>
                    <div class="p-3 bg-success/10 rounded-xl">
                        <i data-lucide="users" class="w-6 h-6 lg:w-8 lg:h-8 text-success"></i>
                    </div>
                </div>
            </div>

            <!-- Check-in Rate -->
            <div class="bg-panel rounded-2xl p-4 lg:p-5 shadow-sm border border-primary-100">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs text-primary-500 uppercase tracking-wide mb-2">Tỷ lệ Check-in</p>
                        <h3 class="text-2xl lg:text-3xl font-bold text-primary-700">
                            <?= $stats['checkin_stats']['percentage'] ?? 0 ?>%</h3>
                        <p class="text-xs text-primary-500 mt-1">
                            <?= $stats['checkin_stats']['total_checked_in'] ?? 0 ?>/<?= $stats['checkin_stats']['total_passengers'] ?? 0 ?>
                            người
                        </p>
                    </div>
                    <div class="p-3 bg-warning/10 rounded-xl">
                        <i data-lucide="check-circle" class="w-6 h-6 lg:w-8 lg:h-8 text-warning-text"></i>
                    </div>
                </div>
            </div>

            <!-- Tour đã hoàn thành -->
            <div class="bg-panel rounded-2xl p-4 lg:p-5 shadow-sm border border-primary-100">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs text-primary-500 uppercase tracking-wide mb-2">Tour Đã Hoàn Thành</p>
                        <h3 class="text-2xl lg:text-3xl font-bold text-primary-700">
                            <?= $stats['completed_tours'] ?? 0 ?></h3>
                    </div>
                    <div class="p-3 bg-success/10 rounded-xl">
                        <i data-lucide="check-circle-2" class="w-6 h-6 lg:w-8 lg:h-8 text-success"></i>
                    </div>
                </div>
            </div>

            <!-- Tổng số ngày làm việc -->
            <div class="bg-panel rounded-2xl p-4 lg:p-5 shadow-sm border border-primary-100">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs text-primary-500 uppercase tracking-wide mb-2">Tổng Ngày Làm Việc</p>
                        <h3 class="text-2xl lg:text-3xl font-bold text-primary-700">
                            <?= $stats['total_working_days'] ?? 0 ?></h3>
                        <p class="text-xs text-primary-500 mt-1">ngày</p>
                    </div>
                    <div class="p-3 bg-info/10 rounded-xl">
                        <i data-lucide="calendar-check" class="w-6 h-6 lg:w-8 lg:h-8 text-info"></i>
                    </div>
                </div>
            </div>

            <!-- Khách hàng đã phục vụ -->
            <div class="bg-panel rounded-2xl p-4 lg:p-5 shadow-sm border border-primary-100">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs text-primary-500 uppercase tracking-wide mb-2">Khách Đã Phục Vụ</p>
                        <h3 class="text-2xl lg:text-3xl font-bold text-primary-700">
                            <?= $stats['customers_served'] ?? 0 ?></h3>
                        <p class="text-xs text-primary-500 mt-1">người</p>
                    </div>
                    <div class="p-3 bg-success/10 rounded-xl">
                        <i data-lucide="user-check" class="w-6 h-6 lg:w-8 lg:h-8 text-success"></i>
                    </div>
                </div>
            </div>

            <!-- Tổng tour được phân công -->
            <div class="bg-panel rounded-2xl p-4 lg:p-5 shadow-sm border border-primary-100">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs text-primary-500 uppercase tracking-wide mb-2">Tổng Tour Phân Công</p>
                        <h3 class="text-2xl lg:text-3xl font-bold text-primary-700">
                            <?= $stats['total_tours_assigned'] ?? 0 ?></h3>
                    </div>
                    <div class="p-3 bg-accent/10 rounded-xl">
                        <i data-lucide="map-pin" class="w-6 h-6 lg:w-8 lg:h-8 text-accent"></i>
                    </div>
                </div>
            </div>

            <!-- Chi phí phát sinh -->
            <div class="bg-panel rounded-2xl p-4 lg:p-5 shadow-sm border border-primary-100">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs text-primary-500 uppercase tracking-wide mb-2">Tổng Chi Phí</p>
                        <h3 class="text-xl lg:text-2xl font-bold text-primary-700">
                            <?= number_format($stats['total_expenses'] ?? 0, 0, ',', '.') ?></h3>
                        <p class="text-xs text-primary-500 mt-1">VNĐ</p>
                    </div>
                    <div class="p-3 bg-warning/10 rounded-xl">
                        <i data-lucide="dollar-sign" class="w-6 h-6 lg:w-8 lg:h-8 text-warning-text"></i>
                    </div>
                </div>
                <?php if ($stats['pending_expenses'] > 0): ?>
                    <div class="mt-2 pt-2 border-t border-primary-100">
                        <span class="text-xs text-warning-text font-semibold">
                            <i data-lucide="clock" class="w-3 h-3 inline"></i>
                            <?= $stats['pending_expenses'] ?> chờ duyệt
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Nhật ký đã viết -->
            <div class="bg-panel rounded-2xl p-4 lg:p-5 shadow-sm border border-primary-100">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-xs text-primary-500 uppercase tracking-wide mb-2">Nhật Ký Đã Viết</p>
                        <h3 class="text-2xl lg:text-3xl font-bold text-primary-700"><?= $stats['journals_count'] ?? 0 ?>
                        </h3>
                    </div>
                    <div class="p-3 bg-accent/10 rounded-xl">
                        <i data-lucide="book-open" class="w-6 h-6 lg:w-8 lg:h-8 text-accent"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tour đang diễn ra -->
    <?php if (!empty($ongoing_tours)): ?>
        <div class="mb-6 lg:mb-8">
            <div class="bg-gradient-to-r from-success/20 to-success/10 rounded-2xl p-4 lg:p-6 border border-success/30">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-base lg:text-lg font-semibold text-primary-700">Tour Đang Diễn Ra</h2>
                    <span class="px-3 py-1 bg-success text-white rounded-full text-xs font-semibold">Hoạt động</span>
                </div>
                <div class="space-y-3">
                    <?php foreach ($ongoing_tours as $tour): ?>
                        <a href="?act=guide-tours&action=show&id=<?= $tour['id'] ?>"
                            class="block bg-white rounded-xl p-4 hover:shadow-md transition-all">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="font-mono text-accent font-bold text-sm"><?= htmlspecialchars($tour['tour_code']) ?></span>
                                        <span class="text-xs px-2 py-0.5 bg-success/20 text-success rounded">Đang diễn ra</span>
                                    </div>
                                    <h3 class="font-semibold text-primary-700 text-sm lg:text-base mb-1">
                                        <?= htmlspecialchars($tour['tour_name']) ?></h3>
                                    <div class="flex gap-4 text-xs text-primary-500">
                                        <span><i data-lucide="calendar" class="w-3 h-3 inline"></i>
                                            <?= date('d/m/Y', strtotime($tour['start_date'])) ?> - <?= date('d/m/Y', strtotime($tour['end_date'])) ?>
                                        </span>
                                        <span><i data-lucide="clock" class="w-3 h-3 inline"></i>
                                            <?= $tour['duration_days'] ?>N<?= $tour['duration_nights'] ?>Đ</span>
                                    </div>
                                </div>
                                <i data-lucide="chevron-right" class="w-5 h-5 text-primary-500"></i>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Content Grid: Tours và Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <!-- Tour sắp tới -->
        <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
            <div class="p-4 lg:p-5 border-b border-primary-100 flex justify-between items-center">
                <h2 class="text-base lg:text-lg font-semibold text-primary-700">Tour Sắp Tới</h2>
                <a href="?act=guide-tours&filter=upcoming"
                    class="text-xs lg:text-sm text-accent hover:text-accent-hover font-semibold">Xem tất cả →</a>
            </div>
            <div class="overflow-x-auto">
                <?php if (empty($my_schedules)): ?>
                    <div class="p-6 lg:p-8 text-center text-primary-500">
                        <i data-lucide="calendar-x" class="w-12 h-12 mx-auto mb-3 text-primary-300"></i>
                        <p class="text-sm">Bạn chưa có lịch tour nào sắp tới.</p>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-primary-100">
                        <?php foreach ($my_schedules as $s): ?>
                            <a href="?act=guide-tours&action=show&id=<?= $s['id'] ?>"
                                class="block p-4 hover:bg-primary-50 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span
                                                class="font-mono text-accent font-bold text-sm"><?= htmlspecialchars($s['tour_code']) ?></span>
                                        </div>
                                        <h3 class="font-semibold text-primary-700 text-sm mb-2">
                                            <?= htmlspecialchars($s['tour_name']) ?></h3>
                                        <div class="flex flex-wrap gap-3 text-xs text-primary-500">
                                            <span><i data-lucide="calendar" class="w-3 h-3 inline"></i>
                                                <?= date('d/m/Y', strtotime($s['start_date'])) ?></span>
                                            <span><i data-lucide="clock" class="w-3 h-3 inline"></i>
                                                <?= $s['duration_days'] ?>N<?= $s['duration_nights'] ?>Đ</span>
                                            <span><i data-lucide="users" class="w-3 h-3 inline"></i>
                                                <?= $s['passengers_count'] ?? 0 ?> khách</span>
                                        </div>
                                    </div>
                                    <i data-lucide="chevron-right" class="w-5 h-5 text-primary-300 mt-1"></i>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tour đã hoàn thành -->
        <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
            <div class="p-4 lg:p-5 border-b border-primary-100 flex justify-between items-center">
                <h2 class="text-base lg:text-lg font-semibold text-primary-700">Tour Đã Hoàn Thành</h2>
                <a href="?act=guide-tours&filter=history"
                    class="text-xs lg:text-sm text-accent hover:text-accent-hover font-semibold">Xem tất cả →</a>
            </div>
            <div class="overflow-x-auto">
                <?php if (empty($completed_tours)): ?>
                    <div class="p-6 lg:p-8 text-center text-primary-500">
                        <i data-lucide="check-circle-2" class="w-12 h-12 mx-auto mb-3 text-primary-300"></i>
                        <p class="text-sm">Chưa có tour nào đã hoàn thành.</p>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-primary-100">
                        <?php foreach ($completed_tours as $s): ?>
                            <a href="?act=guide-tours&action=show&id=<?= $s['id'] ?>"
                                class="block p-4 hover:bg-primary-50 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span
                                                class="font-mono text-accent font-bold text-sm"><?= htmlspecialchars($s['tour_code']) ?></span>
                                        </div>
                                        <h3 class="font-semibold text-primary-700 text-sm mb-2">
                                            <?= htmlspecialchars($s['tour_name']) ?></h3>
                                        <div class="flex flex-wrap gap-3 text-xs text-primary-500">
                                            <span><i data-lucide="calendar" class="w-3 h-3 inline"></i>
                                                Kết thúc: <?= date('d/m/Y', strtotime($s['end_date'])) ?></span>
                                            <span><i data-lucide="clock" class="w-3 h-3 inline"></i>
                                                <?= $s['duration_days'] ?>N<?= $s['duration_nights'] ?>Đ</span>
                                        </div>
                                    </div>
                                    <i data-lucide="chevron-right" class="w-5 h-5 text-primary-300 mt-1"></i>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Nhật ký gần đây -->
    <?php if (!empty($recent_journals)): ?>
        <div class="mt-6 lg:mt-8">
            <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
                <div class="p-4 lg:p-5 border-b border-primary-100 flex justify-between items-center">
                    <h2 class="text-base lg:text-lg font-semibold text-primary-700">Nhật Ký Gần Đây</h2>
                    <a href="?act=guide-journals"
                        class="text-xs lg:text-sm text-accent hover:text-accent-hover font-semibold">Xem tất cả →</a>
                </div>
                <div class="p-4 lg:p-5 space-y-4">
                    <?php foreach ($recent_journals as $journal): ?>
                        <a href="?act=guide-journals&action=show&id=<?= $journal['id'] ?>"
                            class="block p-4 bg-primary-50 rounded-xl hover:bg-primary-100 transition-colors">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="font-semibold text-primary-700 text-sm">
                                    <?= htmlspecialchars($journal['title'] ?? 'Nhật ký ngày ' . date('d/m/Y', strtotime($journal['journal_date']))) ?>
                                </h3>
                                <span class="text-xs text-primary-500">
                                    <?= date('d/m/Y', strtotime($journal['journal_date'])) ?></span>
                            </div>
                            <?php if (!empty($journal['content'])): ?>
                                <p class="text-xs text-primary-600 line-clamp-2">
                                    <?= strip_tags(substr($journal['content'], 0, 150)) ?>...
                                </p>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Initialize Lucide icons after page load
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
