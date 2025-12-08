<?php
/**
 * GUIDE - QUẢN LÝ CHECKPOINTS
 * Variables: $schedule, $tour, $checkpoints, $checkpointsByDate
 */
?>

<div class="max-w-6xl mx-auto p-4 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý Checkpoints</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1 flex flex-wrap gap-1 lg:gap-2">
                <?= htmlspecialchars($tour['tour_code']) ?> - <?= htmlspecialchars($tour['name']) ?>
                <span>•</span>
                <?= date('d/m/Y', strtotime($schedule['start_date'])) ?> - <?= date('d/m/Y', strtotime($schedule['end_date'])) ?>
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <a href="?act=guide-checkpoints&action=create&schedule_id=<?= $schedule['id'] ?>" 
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tạo Checkpoint
            </a>
            <a href="?act=guide-tours&action=show&id=<?= $schedule['id'] ?>" 
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
    </div>

    <?php if (empty($checkpoints)): ?>
        <div class="bg-panel rounded-2xl p-8 lg:p-12 text-center border border-primary-100">
            <i data-lucide="map-pin" class="w-12 h-12 lg:w-16 lg:h-16 text-primary-300 mx-auto mb-4"></i>
            <h3 class="text-lg lg:text-xl font-semibold text-primary-700 mb-2">Chưa có checkpoint nào</h3>
            <p class="text-sm lg:text-base text-primary-500 mb-6">Tạo checkpoint đầu tiên để bắt đầu check-in theo hoạt động</p>
            <a href="?act=guide-checkpoints&action=create&schedule_id=<?= $schedule['id'] ?>" 
                class="inline-flex items-center gap-2 px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tạo Checkpoint
            </a>
        </div>
    <?php else: ?>
        <!-- Group by Date -->
        <?php foreach ($checkpointsByDate as $date => $dateCheckpoints): ?>
            <div class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm mb-4 lg:mb-6">
                <div class="bg-primary-50 px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100">
                    <h2 class="text-base lg:text-lg font-bold text-primary-700 flex items-center gap-2">
                        <i data-lucide="calendar" class="w-4 h-4 lg:w-5 lg:h-5"></i>
                        <?= date('d/m/Y', strtotime($date)) ?> (<?= date('l', strtotime($date)) ?>)
                    </h2>
                </div>
                <div class="p-4 lg:p-6">
                    <div class="space-y-3 lg:space-y-4">
                        <?php foreach ($dateCheckpoints as $cp): ?>
                            <div class="bg-primary-50 rounded-xl p-4 lg:p-5 border border-primary-100 hover:border-primary-200 transition-colors">
                                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 lg:gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <?php
                                                $icon = 'map-pin';
                                                $color = 'primary';
                                                switch ($cp['checkpoint_type']) {
                                                    case 'boarding':
                                                        $icon = 'bus';
                                                        $color = 'info';
                                                        break;
                                                    case 'meal':
                                                        $icon = 'utensils';
                                                        $color = 'success';
                                                        break;
                                                    case 'accommodation':
                                                        $icon = 'bed';
                                                        $color = 'accent';
                                                        break;
                                                    case 'transfer':
                                                        $icon = 'car';
                                                        $color = 'warning';
                                                        break;
                                                    case 'activity':
                                                        $icon = 'activity';
                                                        $color = 'primary';
                                                        break;
                                                }
                                                ?>
                                                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-xl bg-<?= $color ?>-100 flex items-center justify-center">
                                                    <i data-lucide="<?= $icon ?>" class="w-5 h-5 lg:w-6 lg:h-6 text-<?= $color ?>-600"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h3 class="text-sm lg:text-base font-bold text-primary-700 mb-1">
                                                    <?= htmlspecialchars($cp['checkpoint_name']) ?>
                                                </h3>
                                                <div class="flex flex-wrap items-center gap-2 text-xs lg:text-sm text-primary-500">
                                                    <span class="bg-<?= $color ?>-100 text-<?= $color ?>-700 px-2 py-0.5 rounded-full font-semibold">
                                                        <?= ucfirst($cp['checkpoint_type']) ?>
                                                    </span>
                                                    <?php if ($cp['scheduled_time']): ?>
                                                        <span class="flex items-center gap-1">
                                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                                            <?= date('H:i', strtotime($cp['scheduled_time'])) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($cp['location_name']): ?>
                                                        <span class="flex items-center gap-1">
                                                            <i data-lucide="map-pin" class="w-3 h-3"></i>
                                                            <?= htmlspecialchars($cp['location_name']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($cp['checkin_count'] > 0): ?>
                                                        <span class="flex items-center gap-1 text-success-600">
                                                            <i data-lucide="check-circle" class="w-3 h-3"></i>
                                                            <?= $cp['checkin_count'] ?> check-in
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                                        <a href="?act=guide-activity-checkin&action=show&checkpoint_id=<?= $cp['id'] ?>" 
                                            class="w-full sm:w-auto px-3 lg:px-4 py-1.5 lg:py-2 bg-success-bg text-success-text rounded-xl hover:opacity-90 text-xs lg:text-sm font-semibold transition-all flex items-center justify-center gap-1">
                                            <i data-lucide="check-circle" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                            Check-in
                                        </a>
                                        <a href="?act=guide-checkpoints&action=edit&id=<?= $cp['id'] ?>" 
                                            class="w-full sm:w-auto px-3 lg:px-4 py-1.5 lg:py-2 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 text-xs lg:text-sm font-semibold transition-colors flex items-center justify-center gap-1">
                                            <i data-lucide="edit" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                            Sửa
                                        </a>
                                        <form method="POST" action="?act=guide-checkpoints&action=delete" 
                                            onsubmit="return confirm('Bạn có chắc muốn xóa checkpoint này?');" 
                                            class="w-full sm:w-auto">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $cp['id'] ?>">
                                            <button type="submit" 
                                                class="w-full sm:w-auto px-3 lg:px-4 py-1.5 lg:py-2 bg-danger-bg text-danger-text rounded-xl hover:opacity-90 text-xs lg:text-sm font-semibold transition-all flex items-center justify-center gap-1">
                                                <i data-lucide="trash-2" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

