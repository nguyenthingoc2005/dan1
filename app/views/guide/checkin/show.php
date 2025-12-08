<?php
/**
 * GUIDE - CHECK-IN HÀNH KHÁCH
 * Variables: $schedule, $tour, $passengers, $stats
 */
?>

<div class="max-w-6xl mx-auto p-4 lg:p-8">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Check-in Hành khách</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1 flex flex-wrap gap-1 lg:gap-2">
                <?= htmlspecialchars($tour['tour_code']) ?> - <?= htmlspecialchars($tour['name']) ?>
                <span>•</span>
                <?= date('d/m/Y', strtotime($schedule['start_date'])) ?> - <?= date('d/m/Y', strtotime($schedule['end_date'])) ?>
                <?php if (!empty($tour['duration_days'])): ?>
                    <span>•</span>
                    <?= $tour['duration_days'] ?> ngày <?= $tour['duration_nights'] ?> đêm
                <?php endif; ?>
            </p>
            <?php if (!empty($tour['departure_location'])): ?>
                <p class="text-xs lg:text-sm text-primary-500 mt-1 flex items-center gap-1">
                    <i data-lucide="map-pin" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                    Điểm khởi hành: <?= htmlspecialchars($tour['departure_location']) ?>
                </p>
            <?php endif; ?>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <a href="?act=guide-checkin&action=printManifest&schedule_id=<?= $schedule['id'] ?>" target="_blank"
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-600 hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="printer" class="w-4 h-4"></i>
                In danh sách
            </a>
            <a href="?act=guide-checkin" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Tour Info Card -->
    <div class="bg-primary-50 rounded-2xl p-4 lg:p-5 mb-4 lg:mb-6 border border-primary-100">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 text-xs lg:text-sm">
            <div>
                <span class="text-primary-500 block mb-1">Thời gian tour:</span>
                <div class="font-semibold text-primary-700"><?= date('d/m/Y', strtotime($schedule['start_date'])) ?> - <?= date('d/m/Y', strtotime($schedule['end_date'])) ?></div>
            </div>
            <?php if (!empty($tour['duration_days'])): ?>
                <div>
                    <span class="text-primary-500 block mb-1">Thời lượng:</span>
                    <div class="font-semibold text-primary-700"><?= $tour['duration_days'] ?> ngày <?= $tour['duration_nights'] ?> đêm</div>
                </div>
            <?php endif; ?>
            <div>
                <span class="text-primary-500 block mb-1">Số khách:</span>
                <div class="font-semibold text-primary-700"><?= $schedule['booked'] ?> / <?= $schedule['quota'] ?></div>
            </div>
            <?php if (!empty($schedule['guide_name'])): ?>
                <div>
                    <span class="text-primary-500 block mb-1">HDV:</span>
                    <div class="font-semibold text-primary-700"><?= htmlspecialchars($schedule['guide_name']) ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 lg:gap-4 mb-4 lg:mb-6">
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
            <div class="text-warning-text text-xs lg:text-sm font-semibold mb-1 lg:mb-2">Đến muộn</div>
            <div class="text-xl lg:text-2xl font-bold text-warning-dark"><?= $stats['late'] ?? 0 ?></div>
        </div>
        <div class="bg-primary-50 border border-primary-100 rounded-2xl p-3 lg:p-4 text-center">
            <div class="text-primary-600 text-xs lg:text-sm font-semibold mb-1 lg:mb-2">Chưa check-in</div>
            <div class="text-xl lg:text-2xl font-bold text-primary-700"><?= $stats['not_checked'] ?? 0 ?></div>
        </div>
    </div>

    <!-- Check-in Form -->
    <?php 
    $today = date('Y-m-d');
    $can_checkin = ($schedule['start_date'] <= $today);
    ?>
    
    <?php if (!$can_checkin): ?>
        <div class="bg-warning-bg border-l-4 border-warning rounded-xl p-4 lg:p-5 mb-4 lg:mb-6">
            <div class="flex items-start gap-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 lg:w-6 lg:h-6 text-warning-text flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="font-semibold text-warning-dark text-sm lg:text-base">Chưa đến ngày bắt đầu tour</p>
                    <p class="text-xs lg:text-sm text-warning-text mt-1">
                        Chỉ có thể check-in từ ngày <strong><?= date('d/m/Y', strtotime($schedule['start_date'])) ?></strong> trở đi.
                        Hôm nay là: <strong><?= date('d/m/Y') ?></strong>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="?act=guide-checkin&action=store" class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm <?= !$can_checkin ? 'opacity-50 pointer-events-none' : '' ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">
        
        <div class="p-4 lg:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <h2 class="text-base lg:text-lg font-bold text-primary-700">Danh sách hành khách</h2>
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <button type="button" onclick="checkAll('present')" 
                        class="w-full sm:w-auto px-3 lg:px-4 py-1.5 lg:py-2 bg-success-bg text-success-text rounded-xl hover:opacity-90 text-xs lg:text-sm font-semibold transition-all flex items-center justify-center gap-1 <?= !$can_checkin ? 'opacity-50 cursor-not-allowed' : '' ?>"
                        <?= !$can_checkin ? 'disabled' : '' ?>>
                        <i data-lucide="check-circle" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                        Đánh dấu tất cả "Có mặt"
                    </button>
                    <button type="button" onclick="checkAll('absent')" 
                        class="w-full sm:w-auto px-3 lg:px-4 py-1.5 lg:py-2 bg-danger-bg text-danger-text rounded-xl hover:opacity-90 text-xs lg:text-sm font-semibold transition-all flex items-center justify-center gap-1 <?= !$can_checkin ? 'opacity-50 cursor-not-allowed' : '' ?>"
                        <?= !$can_checkin ? 'disabled' : '' ?>>
                        <i data-lucide="x-circle" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                        Đánh dấu tất cả "Vắng mặt"
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
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary-100">
                        <?php if (empty($passengers)): ?>
                            <tr>
                                <td colspan="6" class="px-3 lg:px-4 py-8 lg:py-12 text-center text-primary-500 italic">
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
                                        <?php if ($p['is_primary']): ?>
                                            <span class="text-xs bg-accent-100 text-accent-700 px-1.5 lg:px-2 py-0.5 rounded-full font-semibold mt-1 inline-block">Trưởng đoàn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 font-mono text-xs lg:text-sm">
                                        <?= htmlspecialchars($p['phone']) ?>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-500 text-xs font-mono">
                                        <?= htmlspecialchars($p['booking_code']) ?>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3">
                                        <input type="hidden" name="checkins[<?= $index ?>][booking_id]" value="<?= $p['booking_id'] ?>">
                                        <input type="hidden" name="checkins[<?= $index ?>][customer_id]" value="<?= $p['id'] ?>">
                                        <div class="flex flex-col gap-2">
                                            <select name="checkins[<?= $index ?>][status]" 
                                                class="w-full px-2 lg:px-3 py-1.5 lg:py-2 bg-primary-50 border border-primary-100 rounded-xl text-xs lg:text-sm checkin-status text-primary-700 focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all <?= !$can_checkin ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                                onchange="updateRowColor(this)"
                                                <?= !$can_checkin ? 'disabled' : '' ?>>
                                                <option value="">-- Chọn --</option>
                                                <option value="present" <?= $current_status == 'present' ? 'selected' : '' ?>>Có mặt</option>
                                                <option value="absent" <?= $current_status == 'absent' ? 'selected' : '' ?>>Vắng mặt</option>
                                                <option value="late" <?= $current_status == 'late' ? 'selected' : '' ?>>Đến muộn</option>
                                            </select>
                                            <?php if ($checkin_time): ?>
                                                <span class="text-xs text-primary-500">Check-in: <?= $checkin_time ?></span>
                                            <?php endif; ?>
                                        </div>
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
                    <a href="?act=guide-checkin" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base text-center">
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
        
        // Remove all status classes
        row.classList.remove('bg-success-bg/30', 'bg-danger-bg/30', 'bg-warning-bg/30');
        
        // Add appropriate class
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

