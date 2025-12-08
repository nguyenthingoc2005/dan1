<?php
/**
 * GUIDE - SUMMARY CHECKPOINT
 * Variables: $checkpoint, $schedule, $tour, $summary, $checkins, $stats
 */
?>

<div class="max-w-6xl mx-auto p-4 lg:p-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Summary: <?= htmlspecialchars($checkpoint['checkpoint_name']) ?></h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">
                <?= htmlspecialchars($tour['tour_code']) ?> - <?= date('d/m/Y', strtotime($checkpoint['scheduled_date'])) ?>
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <a href="?act=guide-tours&action=show&id=<?= $checkpoint['tour_schedule_id'] ?>&tab=checkin" 
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="home" class="w-4 h-4"></i>
                Quay về Tour
            </a>
            <a href="?act=guide-activity-checkin&action=checkpoints&schedule_id=<?= $checkpoint['tour_schedule_id'] ?>" 
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-3 lg:gap-4 mb-4 lg:mb-6">
        <div class="bg-info-bg border border-info rounded-2xl p-3 lg:p-4 text-center">
            <div class="text-info-text text-xs lg:text-sm font-semibold mb-1 lg:mb-2">Tổng</div>
            <div class="text-xl lg:text-2xl font-bold text-info-dark"><?= $stats['total'] ?? 0 ?></div>
        </div>
        <div class="bg-success-bg border border-success rounded-2xl p-3 lg:p-4 text-center">
            <div class="text-success-text text-xs lg:text-sm font-semibold mb-1 lg:mb-2">Có mặt</div>
            <div class="text-xl lg:text-2xl font-bold text-success-dark"><?= $stats['present'] ?? 0 ?></div>
        </div>
        <div class="bg-danger-bg border border-danger rounded-2xl p-3 lg:p-4 text-center">
            <div class="text-danger-text text-xs lg:text-sm font-semibold mb-1 lg:mb-2">Vắng mặt</div>
            <div class="text-xl lg:text-2xl font-bold text-danger-dark"><?= $stats['absent'] ?? 0 ?></div>
        </div>
        <div class="bg-warning-bg border border-warning rounded-2xl p-3 lg:p-4 text-center">
            <div class="text-warning-text text-xs lg:text-sm font-semibold mb-1 lg:mb-2">Muộn</div>
            <div class="text-xl lg:text-2xl font-bold text-warning-dark"><?= $stats['late'] ?? 0 ?></div>
        </div>
        <div class="bg-primary-50 border border-primary-100 rounded-2xl p-3 lg:p-4 text-center">
            <div class="text-primary-600 text-xs lg:text-sm font-semibold mb-1 lg:mb-2">Sớm</div>
            <div class="text-xl lg:text-2xl font-bold text-primary-700"><?= $stats['early'] ?? 0 ?></div>
        </div>
        <div class="bg-accent-50 border border-accent-100 rounded-2xl p-3 lg:p-4 text-center">
            <div class="text-accent-600 text-xs lg:text-sm font-semibold mb-1 lg:mb-2">Miễn</div>
            <div class="text-xl lg:text-2xl font-bold text-accent-700"><?= $stats['excused'] ?? 0 ?></div>
        </div>
    </div>

    <!-- Summary Info -->
    <?php if ($summary): ?>
        <div class="bg-panel rounded-2xl p-4 lg:p-6 mb-4 lg:mb-6 border border-primary-100">
            <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-4">Thông tin Checkpoint</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-primary-500 block mb-1">Thời gian dự kiến:</span>
                    <div class="font-semibold text-primary-700">
                        <?= $summary['scheduled_start_time'] ? date('H:i', strtotime($summary['scheduled_start_time'])) : 'N/A' ?>
                    </div>
                </div>
                <div>
                    <span class="text-primary-500 block mb-1">Thời gian bắt đầu thực tế:</span>
                    <div class="font-semibold text-primary-700">
                        <?= $summary['actual_start_time'] ? date('H:i', strtotime($summary['actual_start_time'])) : 'Chưa bắt đầu' ?>
                    </div>
                </div>
                <div>
                    <span class="text-primary-500 block mb-1">Thời gian kết thúc:</span>
                    <div class="font-semibold text-primary-700">
                        <?= $summary['actual_end_time'] ? date('H:i', strtotime($summary['actual_end_time'])) : 'Chưa kết thúc' ?>
                    </div>
                </div>
                <div>
                    <span class="text-primary-500 block mb-1">Trạng thái:</span>
                    <div class="font-semibold text-primary-700">
                        <?php
                        $statusText = [
                            'pending' => 'Chờ',
                            'in_progress' => 'Đang diễn ra',
                            'completed' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy'
                        ];
                        echo $statusText[$summary['status']] ?? $summary['status'];
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Check-ins List -->
    <div class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm">
        <div class="p-4 lg:p-6">
            <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-4">Danh sách Check-in</h2>
            
            <?php if (empty($checkins)): ?>
                <p class="text-center text-primary-500 py-8">Chưa có check-in nào.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[600px]">
                        <thead>
                            <tr class="bg-primary-50 text-primary-600 text-xs uppercase tracking-wider">
                                <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Họ tên</th>
                                <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Booking</th>
                                <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Thời gian</th>
                                <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Trạng thái</th>
                                <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary-100">
                            <?php foreach ($checkins as $checkin): ?>
                                <tr class="hover:bg-primary-50 transition-colors">
                                    <td class="px-3 lg:px-4 py-2 lg:py-3">
                                        <div class="font-semibold text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($checkin['full_name']) ?></div>
                                        <div class="text-xs text-primary-500"><?= htmlspecialchars($checkin['phone']) ?></div>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-500 text-xs font-mono">
                                        <?= htmlspecialchars($checkin['booking_code']) ?>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm">
                                        <?php if ($checkin['actual_time']): ?>
                                            <div><?= date('H:i', strtotime($checkin['actual_time'])) ?></div>
                                            <?php if ($checkin['minutes_late'] > 0): ?>
                                                <div class="text-xs text-warning-600">Muộn <?= $checkin['minutes_late'] ?> phút</div>
                                            <?php elseif ($checkin['minutes_early'] > 0): ?>
                                                <div class="text-xs text-info-600">Sớm <?= $checkin['minutes_early'] ?> phút</div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-primary-400">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3">
                                        <?php
                                        $statusColors = [
                                            'present' => 'success',
                                            'absent' => 'danger',
                                            'late' => 'warning',
                                            'early' => 'info',
                                            'excused' => 'accent'
                                        ];
                                        $statusTexts = [
                                            'present' => 'Có mặt',
                                            'absent' => 'Vắng mặt',
                                            'late' => 'Muộn',
                                            'early' => 'Sớm',
                                            'excused' => 'Miễn'
                                        ];
                                        $color = $statusColors[$checkin['status']] ?? 'primary';
                                        $text = $statusTexts[$checkin['status']] ?? $checkin['status'];
                                        ?>
                                        <span class="bg-<?= $color ?>-100 text-<?= $color ?>-700 px-2 py-0.5 rounded-full text-xs font-semibold">
                                            <?= $text ?>
                                        </span>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-600">
                                        <?= htmlspecialchars($checkin['notes'] ?? '') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

