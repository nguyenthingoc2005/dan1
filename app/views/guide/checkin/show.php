<?php
/**
 * GUIDE - CHECK-IN HÀNH KHÁCH
 * Variables: $schedule, $tour, $passengers, $stats
 */
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Check-in Hành khách</h1>
            <p class="text-gray-500 text-sm mt-1">
                <?= htmlspecialchars($tour['tour_code']) ?> - <?= htmlspecialchars($tour['name']) ?>
                <span class="mx-2">•</span>
                <?= date('d/m/Y', strtotime($schedule['start_date'])) ?>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="?act=guide-checkin&action=printManifest&schedule_id=<?= $schedule['id'] ?>" target="_blank"
                class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 font-medium">
                📄 In danh sách
            </a>
            <a href="?act=guide-checkin" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                ← Quay lại
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
            <div class="text-blue-600 text-sm font-medium mb-1">Tổng</div>
            <div class="text-2xl font-bold text-blue-900"><?= $stats['total'] ?? 0 ?></div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
            <div class="text-green-600 text-sm font-medium mb-1">Có mặt</div>
            <div class="text-2xl font-bold text-green-900"><?= $stats['present'] ?? 0 ?></div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
            <div class="text-red-600 text-sm font-medium mb-1">Vắng mặt</div>
            <div class="text-2xl font-bold text-red-900"><?= $stats['absent'] ?? 0 ?></div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
            <div class="text-yellow-600 text-sm font-medium mb-1">Đến muộn</div>
            <div class="text-2xl font-bold text-yellow-900"><?= $stats['late'] ?? 0 ?></div>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
            <div class="text-gray-600 text-sm font-medium mb-1">Chưa check-in</div>
            <div class="text-2xl font-bold text-gray-900"><?= $stats['not_checked'] ?? 0 ?></div>
        </div>
    </div>

    <!-- Check-in Form -->
    <form method="POST" action="?act=guide-checkin&action=store" class="bg-white rounded-lg shadow-sm overflow-hidden">
        <?= csrf_field() ?>
        <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">
        
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800">Danh sách hành khách</h2>
                <div class="flex gap-2">
                    <button type="button" onclick="checkAll('present')" 
                        class="px-3 py-1.5 bg-green-100 text-green-700 rounded hover:bg-green-200 text-sm font-medium">
                        ✅ Đánh dấu tất cả "Có mặt"
                    </button>
                    <button type="button" onclick="checkAll('absent')" 
                        class="px-3 py-1.5 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm font-medium">
                        ❌ Đánh dấu tất cả "Vắng mặt"
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                            <th class="px-4 py-3 font-medium w-12">#</th>
                            <th class="px-4 py-3 font-medium">Họ tên</th>
                            <th class="px-4 py-3 font-medium">SĐT</th>
                            <th class="px-4 py-3 font-medium">Booking</th>
                            <th class="px-4 py-3 font-medium text-center">Trạng thái</th>
                            <th class="px-4 py-3 font-medium">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($passengers)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">
                                    Chưa có hành khách nào.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($passengers as $index => $p): ?>
                                <?php
                                $current_status = $p['checkin_status'] ?? '';
                                $checkin_time = $p['checkin_time'] ? date('H:i', strtotime($p['checkin_time'])) : '';
                                ?>
                                <tr class="hover:bg-gray-50 <?= $current_status == 'present' ? 'bg-green-50' : ($current_status == 'absent' ? 'bg-red-50' : ($current_status == 'late' ? 'bg-yellow-50' : '')) ?>">
                                    <td class="px-4 py-3 text-gray-400"><?= $index + 1 ?></td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900"><?= htmlspecialchars($p['full_name']) ?></div>
                                        <?php if ($p['is_primary']): ?>
                                            <span class="text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">Trưởng đoàn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 font-mono text-sm">
                                        <?= htmlspecialchars($p['phone']) ?>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 text-xs font-mono">
                                        <?= htmlspecialchars($p['booking_code']) ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="hidden" name="checkins[<?= $index ?>][booking_id]" value="<?= $p['booking_id'] ?>">
                                        <input type="hidden" name="checkins[<?= $index ?>][customer_id]" value="<?= $p['id'] ?>">
                                        <div class="flex flex-col gap-2">
                                            <select name="checkins[<?= $index ?>][status]" 
                                                class="w-full px-2 py-1.5 border rounded text-sm checkin-status"
                                                onchange="updateRowColor(this)">
                                                <option value="">-- Chọn --</option>
                                                <option value="present" <?= $current_status == 'present' ? 'selected' : '' ?>>✅ Có mặt</option>
                                                <option value="absent" <?= $current_status == 'absent' ? 'selected' : '' ?>>❌ Vắng mặt</option>
                                                <option value="late" <?= $current_status == 'late' ? 'selected' : '' ?>>⏰ Đến muộn</option>
                                            </select>
                                            <?php if ($checkin_time): ?>
                                                <span class="text-xs text-gray-500">Check-in: <?= $checkin_time ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="checkins[<?= $index ?>][notes]" 
                                            value="<?= htmlspecialchars($p['checkin_notes'] ?? '') ?>"
                                            placeholder="Ghi chú..."
                                            class="w-full px-2 py-1.5 border rounded text-sm">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($passengers)): ?>
                <div class="mt-6 flex justify-end gap-3 pt-4 border-t">
                    <a href="?act=guide-checkin" class="px-6 py-2 border rounded hover:bg-gray-50">
                        Hủy
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">
                        💾 Lưu check-in
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
        row.classList.remove('bg-green-50', 'bg-red-50', 'bg-yellow-50');
        
        // Add appropriate class
        if (status == 'present') {
            row.classList.add('bg-green-50');
        } else if (status == 'absent') {
            row.classList.add('bg-red-50');
        } else if (status == 'late') {
            row.classList.add('bg-yellow-50');
        }
    }

    function checkAll(status) {
        document.querySelectorAll('.checkin-status').forEach(select => {
            select.value = status;
            updateRowColor(select);
        });
    }
</script>

