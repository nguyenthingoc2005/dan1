<?php
/**
 * ADMIN - HỦY LỊCH TRÌNH TOUR (TOUR-013)
 * Variables: $schedule, $active_bookings, $other_schedules
 */
if (!is_admin())
    redirect('?act=access-denied');

$booking_count = count($active_bookings);
$total_participants = 0;
$total_paid = 0;
foreach ($active_bookings as $b) {
    $total_participants += ($b['adult_count'] ?? 0) + ($b['child_count'] ?? 0) + ($b['infant_count'] ?? 0);
    $total_paid += (float) ($b['paid_amount'] ?? 0);
}
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="?act=admin&module=schedules" class="hover:text-blue-600">Lịch khởi hành</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="?act=admin&module=schedules&action=show&id=<?= $schedule['id'] ?>" class="hover:text-blue-600">Chi
                tiết</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span>Hủy lịch trình</span>
        </div>
        <h1 class="text-2xl font-bold text-red-700">⚠️ Hủy Lịch Trình Tour</h1>
        <p class="text-sm text-gray-500 mt-1">Xác nhận và xử lý hủy lịch trình tour này</p>
    </div>

    <!-- Warning Alert -->
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-bold text-red-800">Cảnh báo: Hành động này không thể hoàn tác</h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>Bạn đang chuẩn bị hủy lịch trình tour này. Hành động này sẽ:</p>
                    <ul class="list-disc list-inside mt-1">
                        <li>Đánh dấu lịch trình là "Đã hủy"</li>
                        <?php if ($booking_count > 0): ?>
                            <li>Ảnh hưởng đến <strong><?= $booking_count ?></strong> booking với
                                <strong><?= $total_participants ?></strong> khách</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Info Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Thông tin Lịch trình</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <span class="text-sm text-gray-600">Tour:</span>
                <p class="font-bold text-gray-800"><?= htmlspecialchars($schedule['tour_name']) ?></p>
                <p class="text-xs text-gray-500"><?= htmlspecialchars($schedule['tour_code']) ?></p>
            </div>
            <div>
                <span class="text-sm text-gray-600">Ngày khởi hành:</span>
                <p class="font-bold text-green-700"><?= date('d/m/Y', strtotime($schedule['start_date'])) ?></p>
                <p class="text-xs text-gray-500">Kết thúc: <?= date('d/m/Y', strtotime($schedule['end_date'])) ?></p>
            </div>
            <div>
                <span class="text-sm text-gray-600">Trạng thái:</span>
                <p class="font-bold">
                    <span
                        class="px-2 py-1 rounded text-xs bg-<?= $schedule['status'] == 'open' ? 'green' : ($schedule['status'] == 'closed' ? 'yellow' : 'blue') ?>-100 text-<?= $schedule['status'] == 'open' ? 'green' : ($schedule['status'] == 'closed' ? 'yellow' : 'blue') ?>-700">
                        <?= ucfirst($schedule['status']) ?>
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Bookings Info (if exists) -->
    <?php if ($booking_count > 0): ?>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">
                Danh sách Bookings (<?= $booking_count ?> booking, <?= $total_participants ?> khách)
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã Booking</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Khách hàng</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số người</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tổng tiền</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Đã thanh toán</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Còn lại</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($active_bookings as $booking): ?>
                            <?php
                            $participants = ($booking['adult_count'] ?? 0) + ($booking['child_count'] ?? 0) + ($booking['infant_count'] ?? 0);
                            $paid = (float) ($booking['paid_amount'] ?? 0);
                            $remaining = (float) ($booking['remaining_amount'] ?? 0);
                            ?>
                            <tr>
                                <td class="px-4 py-3 text-sm font-mono text-blue-600">
                                    <?= htmlspecialchars($booking['booking_code'] ?? 'N/A') ?></td>
                                <td class="px-4 py-3 text-sm"><?= htmlspecialchars($booking['customer_name'] ?? 'N/A') ?></td>
                                <td class="px-4 py-3 text-sm"><?= $participants ?> người</td>
                                <td class="px-4 py-3 text-sm"><?= number_format($booking['final_amount'] ?? 0) ?> đ</td>
                                <td class="px-4 py-3 text-sm font-bold text-green-600"><?= number_format($paid) ?> đ</td>
                                <td class="px-4 py-3 text-sm text-red-600"><?= number_format($remaining) ?> đ</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-700">
                                        <?= payment_status_text($booking['payment_status'] ?? 'unpaid') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="2" class="px-4 py-3 text-sm font-bold">Tổng cộng:</td>
                            <td class="px-4 py-3 text-sm font-bold"><?= $total_participants ?> người</td>
                            <td class="px-4 py-3 text-sm font-bold">-</td>
                            <td class="px-4 py-3 text-sm font-bold text-green-600"><?= number_format($total_paid) ?> đ</td>
                            <td class="px-4 py-3 text-sm font-bold text-red-600">-</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-green-800">✅ Lịch trình này chưa có booking nào. Có thể hủy an toàn.</p>
        </div>
    <?php endif; ?>

    <!-- Cancel Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Xác nhận Hủy Lịch trình</h2>

        <form action="?act=admin&module=schedules&action=cancel" method="POST" id="cancelForm">
            <input type="hidden" name="id" value="<?= $schedule['id'] ?>">

            <div class="space-y-6">
                <!-- Cancellation Reason -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lý do hủy</label>
                    <textarea name="cancellation_reason" id="cancellation_reason" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        placeholder="Nhập lý do hủy lịch trình (tùy chọn)..."></textarea>
                </div>

                <!-- Action Type Selection (only if has bookings) -->
                <?php if ($booking_count > 0): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Chọn cách xử lý bookings:</label>
                        <div class="space-y-3">
                            <!-- Option 1: Cancel All & Refund 100% -->
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition">
                                <label class="flex items-start cursor-pointer">
                                    <input type="radio" name="action_type" value="cancel_all" checked
                                        class="mt-1 mr-3 text-blue-600 focus:ring-blue-500">
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-800">Option 1: Tự động hủy bookings & Hoàn tiền 100%
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            - Tự động hủy tất cả <?= $booking_count ?> booking<br>
                                            - Hoàn lại 100% số tiền đã thanh toán (<?= number_format($total_paid) ?> đ)<br>
                                            - Không tính phí hủy (vì lỗi từ công ty)<br>
                                            - Tự động tạo yêu cầu hoàn tiền (refunds)
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Option 2: Transfer to another schedule -->
                            <?php if (!empty($other_schedules)): ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="radio" name="action_type" value="transfer"
                                            class="mt-1 mr-3 text-blue-600 focus:ring-blue-500"
                                            onchange="toggleScheduleSelect(this.checked)">
                                        <div class="flex-1">
                                            <div class="font-bold text-gray-800">Option 2: Chuyển bookings sang lịch trình khác
                                            </div>
                                            <div class="text-sm text-gray-600 mt-1">
                                                - Chuyển tất cả bookings sang lịch trình khác (cùng tour)<br>
                                                - Không hoàn tiền (vì chuyển schedule thành công)<br>
                                                - Cập nhật quota của schedule mới
                                            </div>
                                            <div id="schedule-select" class="mt-3 hidden">
                                                <select name="new_schedule_id" id="new_schedule_id"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                                    <option value="">-- Chọn lịch trình mới --</option>
                                                    <?php foreach ($other_schedules as $os): ?>
                                                        <?php
                                                        $available = ($os['quota'] ?? 0) - ($os['booked'] ?? 0);
                                                        ?>
                                                        <option value="<?= $os['id'] ?>" data-available="<?= $available ?>"
                                                            <?= $available < $total_participants ? 'disabled' : '' ?>>
                                                            <?= date('d/m/Y', strtotime($os['start_date'])) ?>
                                                            - Còn <?= $available ?> chỗ
                                                            <?= $available < $total_participants ? ' (KHÔNG ĐỦ CHỖ)' : '' ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <p class="text-xs text-red-600 mt-1 hidden" id="schedule-warning">
                                                    ⚠️ Lịch trình được chọn không đủ chỗ cho <?= $total_participants ?> khách
                                                </p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            <?php endif; ?>

                            <!-- Option 3: Cancel with Policy -->
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 transition">
                                <label class="flex items-start cursor-pointer">
                                    <input type="radio" name="action_type" value="cancel_with_policy"
                                        class="mt-1 mr-3 text-blue-600 focus:ring-blue-500">
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-800">Option 3: Hủy bookings & Hoàn tiền theo chính
                                            sách hủy</div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            - Áp dụng chính sách hủy tour<br>
                                            - Tính phí hủy theo % trong chính sách<br>
                                            - Hoàn tiền = Số tiền đã trả - Phí hủy<br>
                                            - Tự động tạo yêu cầu hoàn tiền
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="action_type" value="cancel_all">
                <?php endif; ?>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="?act=admin&module=schedules&action=show&id=<?= $schedule['id'] ?>"
                        class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Hủy
                    </a>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow"
                        onclick="return confirm('Bạn có chắc chắn muốn hủy lịch trình này? Hành động này không thể hoàn tác!');">
                        ⚠️ Xác nhận Hủy Lịch trình
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleScheduleSelect(show) {
        const select = document.getElementById('schedule-select');
        const warning = document.getElementById('schedule-warning');
        if (show) {
            select.classList.remove('hidden');
            // Check if selected schedule has enough slots
            const scheduleSelect = document.getElementById('new_schedule_id');
            scheduleSelect.addEventListener('change', function () {
                const selected = scheduleSelect.options[scheduleSelect.selectedIndex];
                const available = parseInt(selected.dataset.available) || 0;
                const needed = <?= $total_participants ?>;
                if (available < needed) {
                    warning.classList.remove('hidden');
                } else {
                    warning.classList.add('hidden');
                }
            });
        } else {
            select.classList.add('hidden');
            warning.classList.add('hidden');
        }
    }

    // Validate form before submit
    document.getElementById('cancelForm').addEventListener('submit', function (e) {
        const actionType = document.querySelector('input[name="action_type"]:checked')?.value;

        if (actionType === 'transfer') {
            const newScheduleId = document.getElementById('new_schedule_id')?.value;
            if (!newScheduleId) {
                e.preventDefault();
                alert('Vui lòng chọn lịch trình mới để chuyển bookings.');
                return false;
            }

            const selected = document.getElementById('new_schedule_id').options[document.getElementById('new_schedule_id').selectedIndex];
            const available = parseInt(selected.dataset.available) || 0;
            const needed = <?= $total_participants ?>;

            if (available < needed) {
                e.preventDefault();
                alert('Lịch trình được chọn không đủ chỗ. Cần ' + needed + ' chỗ, chỉ còn ' + available + ' chỗ.');
                return false;
            }
        }
    });
</script>