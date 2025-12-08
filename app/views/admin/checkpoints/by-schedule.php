<?php
/**
 * ADMIN - CHECKPOINTS THEO SCHEDULE (CHỈ XEM)
 * Variables: $schedule, $tour, $checkpoints
 */
?>

<div class="max-w-6xl mx-auto p-4 lg:p-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Checkpoints: <?= htmlspecialchars($tour['tour_code']) ?></h1>
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

    <?php if (empty($checkpoints)): ?>
        <div class="bg-panel rounded-2xl p-8 lg:p-12 text-center border border-primary-100">
            <i data-lucide="map-pin" class="w-12 h-12 lg:w-16 lg:h-16 text-primary-300 mx-auto mb-4"></i>
            <h3 class="text-lg lg:text-xl font-semibold text-primary-700 mb-2">Chưa có checkpoint nào</h3>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
            <?php foreach ($checkpoints as $cp): ?>
                <div class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm">
                    <div class="p-4 lg:p-5">
                        <div class="flex items-start gap-3 mb-3">
                            <?php
                            $icon = 'map-pin';
                            $color = 'primary';
                            switch ($cp['checkpoint_type']) {
                                case 'boarding': $icon = 'bus'; $color = 'info'; break;
                                case 'meal': $icon = 'utensils'; $color = 'success'; break;
                                case 'accommodation': $icon = 'bed'; $color = 'accent'; break;
                                case 'transfer': $icon = 'car'; $color = 'warning'; break;
                                case 'activity': $icon = 'activity'; $color = 'primary'; break;
                            }
                            ?>
                            <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-xl bg-<?= $color ?>-100 flex items-center justify-center flex-shrink-0">
                                <i data-lucide="<?= $icon ?>" class="w-6 h-6 lg:w-7 lg:h-7 text-<?= $color ?>-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base lg:text-lg font-bold text-primary-700 mb-1">
                                    <?= htmlspecialchars($cp['checkpoint_name']) ?>
                                </h3>
                                <div class="flex flex-wrap items-center gap-2 text-xs lg:text-sm text-primary-500">
                                    <span class="bg-<?= $color ?>-100 text-<?= $color ?>-700 px-2 py-0.5 rounded-full font-semibold">
                                        <?= ucfirst($cp['checkpoint_type']) ?>
                                    </span>
                                    <span><?= date('d/m/Y', strtotime($cp['scheduled_date'])) ?></span>
                                    <?php if ($cp['scheduled_time']): ?>
                                        <span><?= date('H:i', strtotime($cp['scheduled_time'])) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php if ($cp['stats']): ?>
                            <div class="grid grid-cols-3 gap-2 mb-3 pt-3 border-t border-primary-100">
                                <div class="text-center">
                                    <div class="text-xs text-primary-500">Có mặt</div>
                                    <div class="text-sm font-bold text-success-600"><?= $cp['stats']['present'] ?? 0 ?></div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xs text-primary-500">Vắng</div>
                                    <div class="text-sm font-bold text-danger-600"><?= $cp['stats']['absent'] ?? 0 ?></div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xs text-primary-500">Tổng</div>
                                    <div class="text-sm font-bold text-primary-700"><?= $cp['stats']['total'] ?? 0 ?></div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <a href="?act=admin&module=checkpoints&action=show&id=<?= $cp['id'] ?>" 
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 text-xs lg:text-sm font-semibold transition-colors flex items-center justify-center gap-2">
                            <i data-lucide="eye" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

