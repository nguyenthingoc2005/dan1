<?php
/**
 * ADMIN - SUMMARY CHECK-IN CỦA SCHEDULE (CHỈ XEM)
 * Variables: $schedule, $tour, $summaries, $checkpoints
 */
?>

<div class="max-w-6xl mx-auto p-4 lg:p-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Summary Check-in: <?= htmlspecialchars($tour['tour_code']) ?></h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">
                <?= date('d/m/Y', strtotime($schedule['start_date'])) ?> - <?= date('d/m/Y', strtotime($schedule['end_date'])) ?>
            </p>
        </div>
        <a href="?act=admin&module=checkpoints" 
            class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    <?php if (empty($summaries)): ?>
        <div class="bg-panel rounded-2xl p-8 lg:p-12 text-center border border-primary-100">
            <i data-lucide="bar-chart" class="w-12 h-12 lg:w-16 lg:h-16 text-primary-300 mx-auto mb-4"></i>
            <h3 class="text-lg lg:text-xl font-semibold text-primary-700 mb-2">Chưa có summary nào</h3>
        </div>
    <?php else: ?>
        <div class="space-y-4 lg:space-y-6">
            <?php foreach ($summaries as $summary): ?>
                <div class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm">
                    <div class="bg-primary-50 px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100">
                        <h3 class="text-base lg:text-lg font-bold text-primary-700">
                            <?= htmlspecialchars($summary['checkpoint_name']) ?>
                        </h3>
                        <p class="text-xs lg:text-sm text-primary-500 mt-1">
                            <?= date('d/m/Y', strtotime($summary['checkpoint_date'])) ?>
                            <?php if ($summary['scheduled_time']): ?>
                                - <?= date('H:i', strtotime($summary['scheduled_time'])) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="p-4 lg:p-6">
                        <div class="grid grid-cols-2 lg:grid-cols-6 gap-3 lg:gap-4 mb-4">
                            <div class="bg-info-bg border border-info rounded-xl p-3 text-center">
                                <div class="text-info-text text-xs font-semibold mb-1">Tổng</div>
                                <div class="text-lg font-bold text-info-dark"><?= $summary['total_customers'] ?? 0 ?></div>
                            </div>
                            <div class="bg-success-bg border border-success rounded-xl p-3 text-center">
                                <div class="text-success-text text-xs font-semibold mb-1">Có mặt</div>
                                <div class="text-lg font-bold text-success-dark"><?= $summary['present_count'] ?? 0 ?></div>
                            </div>
                            <div class="bg-danger-bg border border-danger rounded-xl p-3 text-center">
                                <div class="text-danger-text text-xs font-semibold mb-1">Vắng</div>
                                <div class="text-lg font-bold text-danger-dark"><?= $summary['absent_count'] ?? 0 ?></div>
                            </div>
                            <div class="bg-warning-bg border border-warning rounded-xl p-3 text-center">
                                <div class="text-warning-text text-xs font-semibold mb-1">Muộn</div>
                                <div class="text-lg font-bold text-warning-dark"><?= $summary['late_count'] ?? 0 ?></div>
                            </div>
                            <div class="bg-primary-50 border border-primary-100 rounded-xl p-3 text-center">
                                <div class="text-primary-600 text-xs font-semibold mb-1">Sớm</div>
                                <div class="text-lg font-bold text-primary-700"><?= $summary['early_count'] ?? 0 ?></div>
                            </div>
                            <div class="bg-accent-50 border border-accent-100 rounded-xl p-3 text-center">
                                <div class="text-accent-600 text-xs font-semibold mb-1">Miễn</div>
                                <div class="text-lg font-bold text-accent-700"><?= $summary['excused_count'] ?? 0 ?></div>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <a href="?act=admin&module=checkpoints&action=show&id=<?= $summary['activity_checkpoint_id'] ?>" 
                                class="px-3 py-1.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 text-xs font-semibold transition-colors flex items-center gap-1">
                                <i data-lucide="eye" class="w-3 h-3"></i>
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

