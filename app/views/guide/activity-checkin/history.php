<?php
/**
 * GUIDE - LỊCH SỬ CHECK-IN CỦA KHÁCH
 * Variables: $schedule, $tour, $customer, $history
 */
?>

<div class="max-w-6xl mx-auto p-4 lg:p-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Lịch sử Check-in</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">
                <?= htmlspecialchars($customer['full_name']) ?> - <?= htmlspecialchars($tour['tour_code']) ?>
            </p>
        </div>
        <a href="?act=guide-activity-checkin&action=checkpoints&schedule_id=<?= $schedule['id'] ?>" 
            class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    <!-- Customer Info -->
    <div class="bg-panel rounded-2xl p-4 lg:p-6 mb-4 lg:mb-6 border border-primary-100">
        <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-4">Thông tin Khách hàng</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="text-primary-500 block mb-1">Họ tên:</span>
                <div class="font-semibold text-primary-700"><?= htmlspecialchars($customer['full_name']) ?></div>
            </div>
            <div>
                <span class="text-primary-500 block mb-1">SĐT:</span>
                <div class="font-semibold text-primary-700"><?= htmlspecialchars($customer['phone']) ?></div>
            </div>
            <div>
                <span class="text-primary-500 block mb-1">Email:</span>
                <div class="font-semibold text-primary-700"><?= htmlspecialchars($customer['email'] ?? 'N/A') ?></div>
            </div>
            <div>
                <span class="text-primary-500 block mb-1">Tour:</span>
                <div class="font-semibold text-primary-700"><?= htmlspecialchars($tour['tour_code']) ?></div>
            </div>
        </div>
    </div>

    <!-- History -->
    <div class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm">
        <div class="p-4 lg:p-6">
            <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-4">Lịch sử Check-in</h2>
            
            <?php if (empty($history)): ?>
                <p class="text-center text-primary-500 py-8">Chưa có check-in nào.</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($history as $item): ?>
                        <div class="bg-primary-50 rounded-xl p-4 lg:p-5 border border-primary-100">
                            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3">
                                <div class="flex-1">
                                    <h3 class="text-sm lg:text-base font-bold text-primary-700 mb-1">
                                        <?= htmlspecialchars($item['checkpoint_name']) ?>
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-2 text-xs lg:text-sm text-primary-500">
                                        <span><?= date('d/m/Y', strtotime($item['checkpoint_date'])) ?></span>
                                        <?php if ($item['scheduled_time']): ?>
                                            <span>•</span>
                                            <span>Dự kiến: <?= date('H:i', strtotime($item['scheduled_time'])) ?></span>
                                        <?php endif; ?>
                                        <?php if ($item['checkin_datetime']): ?>
                                            <span>•</span>
                                            <span>Thực tế: <?= date('H:i', strtotime($item['checkin_datetime'])) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
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
                                    $color = $statusColors[$item['status']] ?? 'primary';
                                    $text = $statusTexts[$item['status']] ?? $item['status'];
                                    ?>
                                    <span class="bg-<?= $color ?>-100 text-<?= $color ?>-700 px-3 py-1 rounded-full text-xs font-semibold">
                                        <?= $text ?>
                                    </span>
                                </div>
                            </div>
                            <?php if ($item['notes']): ?>
                                <div class="mt-2 text-xs lg:text-sm text-primary-600">
                                    <i data-lucide="message-square" class="w-3 h-3 inline mr-1"></i>
                                    <?= htmlspecialchars($item['notes']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

