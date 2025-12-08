<?php
/**
 * GUIDE - CHECK-IN CHO CHECKPOINT
 * Variables: $checkpoint, $schedule, $tour, $passengers, $stats, $summary, $can_checkin
 */
?>

<div class="max-w-6xl mx-auto p-4 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Check-in: <?= htmlspecialchars($checkpoint['checkpoint_name']) ?></h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1 flex flex-wrap gap-1 lg:gap-2">
                <?= htmlspecialchars($tour['tour_code']) ?> - <?= htmlspecialchars($tour['name']) ?>
                <span>•</span>
                <?= date('d/m/Y', strtotime($checkpoint['scheduled_date'])) ?>
                <?php if ($checkpoint['scheduled_time']): ?>
                    <span>•</span>
                    <?= date('H:i', strtotime($checkpoint['scheduled_time'])) ?>
                <?php endif; ?>
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

    <?php if (!$can_checkin): ?>
        <div class="bg-warning-bg border-l-4 border-warning rounded-xl p-4 lg:p-5 mb-4 lg:mb-6">
            <div class="flex items-start gap-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 lg:w-6 lg:h-6 text-warning-text flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="font-semibold text-warning-dark text-sm lg:text-base">Chưa đến ngày checkpoint</p>
                    <p class="text-xs lg:text-sm text-warning-text mt-1">
                        Chỉ có thể check-in từ ngày <strong><?= date('d/m/Y', strtotime($checkpoint['scheduled_date'])) ?></strong> trở đi.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Check-in Form -->
    <form method="POST" action="?act=guide-activity-checkin&action=store" class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm <?= !$can_checkin ? 'opacity-50 pointer-events-none' : '' ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="checkpoint_id" value="<?= $checkpoint['id'] ?>">
        
        <div class="p-4 lg:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <h2 class="text-base lg:text-lg font-bold text-primary-700">Danh sách hành khách</h2>
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <button type="button" onclick="checkAll('present')" 
                        class="w-full sm:w-auto px-3 lg:px-4 py-1.5 lg:py-2 bg-success-bg text-success-text rounded-xl hover:opacity-90 text-xs lg:text-sm font-semibold transition-all flex items-center justify-center gap-1 <?= !$can_checkin ? 'opacity-50 cursor-not-allowed' : '' ?>"
                        <?= !$can_checkin ? 'disabled' : '' ?>>
                        <i data-lucide="check-circle" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                        Tất cả "Có mặt"
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="bg-primary-50 text-primary-600 text-xs uppercase tracking-wider">
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold w-12">#</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Họ tên</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">SĐT</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Booking</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold text-center">Trạng thái</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Thời gian</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary-100">
                        <?php if (empty($passengers)): ?>
                            <tr>
                                <td colspan="7" class="px-3 lg:px-4 py-8 lg:py-12 text-center text-primary-500 italic">
                                    Chưa có hành khách nào.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($passengers as $index => $p): ?>
                                <?php
                                $current_status = $p['checkin_status'] ?? '';
                                $checkin_time = $p['checkin_time'] ? date('H:i', strtotime($p['checkin_time'])) : '';
                                ?>
                                <tr class="hover:bg-primary-50 transition-colors <?= $current_status == 'present' ? 'bg-success-bg/30' : ($current_status == 'absent' ? 'bg-danger-bg/30' : ($current_status == 'late' ? 'bg-warning-bg/30' : '')) ?>">
                                    <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-400"><?= $index + 1 ?></td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3">
                                        <div class="font-semibold text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($p['full_name']) ?></div>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 font-mono text-xs lg:text-sm">
                                        <?= htmlspecialchars($p['phone']) ?>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-500 text-xs font-mono">
                                        <?= htmlspecialchars($p['booking_code']) ?>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3">
                                        <input type="hidden" name="checkins[<?= $index ?>][booking_customer_id]" value="<?= $p['id'] ?>">
                                        <select name="checkins[<?= $index ?>][status]" 
                                            class="w-full px-2 lg:px-3 py-1.5 lg:py-2 bg-primary-50 border border-primary-100 rounded-xl text-xs lg:text-sm checkin-status text-primary-700 focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all <?= !$can_checkin ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                            onchange="updateRowColor(this)"
                                            <?= !$can_checkin ? 'disabled' : '' ?>>
                                            <option value="">-- Chọn --</option>
                                            <option value="present" <?= $current_status == 'present' ? 'selected' : '' ?>>Có mặt</option>
                                            <option value="absent" <?= $current_status == 'absent' ? 'selected' : '' ?>>Vắng mặt</option>
                                            <option value="late" <?= $current_status == 'late' ? 'selected' : '' ?>>Đến muộn</option>
                                            <option value="early" <?= $current_status == 'early' ? 'selected' : '' ?>>Đến sớm</option>
                                            <option value="excused" <?= $current_status == 'excused' ? 'selected' : '' ?>>Được miễn</option>
                                        </select>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3">
                                        <input type="time" name="checkins[<?= $index ?>][actual_time]" 
                                            value="<?= $checkin_time ? date('H:i', strtotime($checkin_time)) : date('H:i') ?>"
                                            class="w-full px-2 lg:px-3 py-1.5 lg:py-2 bg-primary-50 border border-primary-100 rounded-xl text-xs lg:text-sm text-primary-700 focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all <?= !$can_checkin ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                            <?= !$can_checkin ? 'disabled' : '' ?>>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3">
                                        <input type="text" name="checkins[<?= $index ?>][notes]" 
                                            value="<?= htmlspecialchars($p['checkin_notes'] ?? '') ?>"
                                            placeholder="Ghi chú..."
                                            class="w-full px-2 lg:px-3 py-1.5 lg:py-2 bg-primary-50 border border-primary-100 rounded-xl text-xs lg:text-sm text-primary-700 focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 <?= !$can_checkin ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                            <?= !$can_checkin ? 'disabled' : '' ?>>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($passengers)): ?>
                <div class="mt-4 lg:mt-6 flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-primary-100">
                    <a href="?act=guide-tours&action=show&id=<?= $checkpoint['tour_schedule_id'] ?>&tab=checkin" 
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base text-center">
                        Hủy
                    </a>
                    <button type="submit" 
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2 <?= !$can_checkin ? 'opacity-50 cursor-not-allowed' : '' ?>"
                        <?= !$can_checkin ? 'disabled' : '' ?>>
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Lưu check-in
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
function updateRowColor(select) {
    const row = select.closest('tr');
    const status = select.value;
    
    row.classList.remove('bg-success-bg/30', 'bg-danger-bg/30', 'bg-warning-bg/30');
    
    if (status == 'present') {
        row.classList.add('bg-success-bg/30');
    } else if (status == 'absent') {
        row.classList.add('bg-danger-bg/30');
    } else if (status == 'late') {
        row.classList.add('bg-warning-bg/30');
    }
}

function checkAll(status) {
    document.querySelectorAll('.checkin-status').forEach(select => {
        select.value = status;
        updateRowColor(select);
    });
}
</script>

