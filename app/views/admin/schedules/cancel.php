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

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-8">
    <!-- Page Header - Responsive -->
    <div class="mb-4 lg:mb-8">
        <div class="flex items-center gap-2 text-xs lg:text-sm text-primary-500 mb-2">
            <a href="?act=admin&module=schedules" class="hover:text-accent">Lịch khởi hành</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <a href="?act=admin&module=schedules&action=show&id=<?= $schedule['id'] ?>" class="hover:text-accent">Chi
                tiết</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span>Hủy lịch trình</span>
        </div>
        <h1 class="text-xl lg:text-2xl font-bold text-danger flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-5 h-5 lg:w-6 lg:h-6"></i>
            Hủy Lịch Trình Tour
        </h1>
        <p class="text-xs lg:text-sm text-primary-500 mt-1">Xác nhận và xử lý hủy lịch trình tour này</p>
    </div>

    <!-- Warning Alert -->
    <div class="bg-danger-bg border-l-4 border-danger p-4 lg:p-5 mb-4 lg:mb-6 rounded-2xl">
        <div class="flex">
            <div class="flex-shrink-0">
                <i data-lucide="alert-triangle" class="w-5 h-5 lg:w-6 lg:h-6 text-danger"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-xs lg:text-sm font-bold text-danger-text">Cảnh báo: Hành động này không thể hoàn tác</h3>
                <div class="mt-2 text-xs lg:text-sm text-danger-text">
                    <p>Bạn đang chuẩn bị hủy lịch trình tour này. Hành động này sẽ:</p>
                    <ul class="list-disc list-inside mt-1 space-y-1">
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
    <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm p-4 lg:p-6 mb-4 lg:mb-6">
        <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4">Thông tin Lịch trình</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <span class="text-xs lg:text-sm text-primary-500">Tour:</span>
                <p class="font-bold text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($schedule['tour_name']) ?></p>
                <p class="text-xs text-primary-500"><?= htmlspecialchars($schedule['tour_code']) ?></p>
            </div>
            <div>
                <span class="text-xs lg:text-sm text-primary-500">Ngày khởi hành:</span>
                <p class="font-bold text-success-text text-sm lg:text-base"><?= date('d/m/Y', strtotime($schedule['start_date'])) ?></p>
                <p class="text-xs text-primary-500">Kết thúc: <?= date('d/m/Y', strtotime($schedule['end_date'])) ?></p>
            </div>
            <div>
                <span class="text-xs lg:text-sm text-primary-500">Trạng thái:</span>
                <p class="font-bold">
                    <span
                        class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase
                        <?= $schedule['status'] == 'open' ? 'bg-success-bg text-success-text' : ($schedule['status'] == 'closed' ? 'bg-warning-bg text-warning-text' : 'bg-info-bg text-info-text') ?>">
                        <?= ucfirst($schedule['status']) ?>
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Bookings Info (if exists) -->
    <?php if ($booking_count > 0): ?>
        <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm p-4 lg:p-6 mb-4 lg:mb-6">
            <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4">
                Danh sách Bookings (<?= $booking_count ?> booking, <?= $total_participants ?> khách)
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-primary-100">
                    <thead class="bg-primary-50">
                        <tr>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-700 uppercase">Mã Booking</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-700 uppercase">Khách hàng</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-700 uppercase">Số người</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-700 uppercase">Tổng tiền</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-700 uppercase">Đã thanh toán</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-700 uppercase">Còn lại</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-700 uppercase">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="bg-panel divide-y divide-primary-100">
                        <?php foreach ($active_bookings as $booking): ?>
                            <?php
                            $participants = ($booking['adult_count'] ?? 0) + ($booking['child_count'] ?? 0) + ($booking['infant_count'] ?? 0);
                            $paid = (float) ($booking['paid_amount'] ?? 0);
                            $remaining = (float) ($booking['remaining_amount'] ?? 0);
                            ?>
                            <tr class="hover:bg-primary-50 transition-colors">
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm font-mono text-accent">
                                    <?= htmlspecialchars($booking['booking_code'] ?? 'N/A') ?></td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-700"><?= htmlspecialchars($booking['customer_name'] ?? 'N/A') ?></td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-700"><?= $participants ?> người</td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-700"><?= number_format($booking['final_amount'] ?? 0) ?> đ</td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm font-bold text-success-text"><?= number_format($paid) ?> đ</td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-danger-text"><?= number_format($remaining) ?> đ</td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm">
                                    <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase bg-info-bg text-info-text">
                                        <?= payment_status_text($booking['payment_status'] ?? 'unpaid') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-primary-50">
                        <tr>
                            <td colspan="2" class="px-3 lg:px-4 py-2 lg:py-3 text-sm font-bold text-primary-700">Tổng cộng:</td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm font-bold text-primary-700"><?= $total_participants ?> người</td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm font-bold text-primary-700">-</td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm font-bold text-success-text"><?= number_format($total_paid) ?> đ</td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm font-bold text-danger-text">-</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-success-bg border border-success rounded-2xl p-4 lg:p-5 mb-4 lg:mb-6">
            <p class="text-success-text flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                Lịch trình này chưa có booking nào. Có thể hủy an toàn.
            </p>
        </div>
    <?php endif; ?>

    <!-- Cancel Form -->
    <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm p-4 lg:p-6">
        <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4">Xác nhận Hủy Lịch trình</h2>

        <form action="?act=admin&module=schedules&action=cancel" method="POST" id="cancelForm">
            <input type="hidden" name="id" value="<?= $schedule['id'] ?>">

            <div class="space-y-4 lg:space-y-6">
                <!-- Cancellation Reason -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Lý do hủy</label>
                    <textarea name="cancellation_reason" id="cancellation_reason" rows="3"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all bg-primary-50 placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="Nhập lý do hủy lịch trình (tùy chọn)..."></textarea>
                </div>

                <!-- Action Type Selection (only if has bookings) -->
                <?php if ($booking_count > 0): ?>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-3">Chọn cách xử lý bookings:</label>
                        <div class="space-y-3">
                            <!-- Option 1: Cancel All & Refund 100% -->
                            <div class="border border-primary-100 rounded-2xl p-4 hover:border-accent transition">
                                <label class="flex items-start cursor-pointer">
                                    <input type="radio" name="action_type" value="cancel_all" checked
                                        class="mt-1 mr-3 text-accent focus:ring-accent w-4 h-4">
                                    <div class="flex-1">
                                        <div class="font-bold text-primary-700 text-sm lg:text-base">Option 1: Tự động hủy bookings & Hoàn tiền 100%
                                        </div>
                                        <div class="text-xs lg:text-sm text-primary-600 mt-1 space-y-1">
                                            <div>- Tự động hủy tất cả <?= $booking_count ?> booking</div>
                                            <div>- Hoàn lại 100% số tiền đã thanh toán (<?= number_format($total_paid) ?> đ)</div>
                                            <div>- Không tính phí hủy (vì lỗi từ công ty)</div>
                                            <div>- Tự động tạo yêu cầu hoàn tiền (refunds)</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Option 2: Transfer to another schedule -->
                            <?php if (!empty($other_schedules)): ?>
                                <div class="border border-primary-100 rounded-2xl p-4 hover:border-accent transition">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="radio" name="action_type" value="transfer"
                                            class="mt-1 mr-3 text-accent focus:ring-accent w-4 h-4"
                                            onchange="toggleScheduleSelect(this.checked)">
                                        <div class="flex-1">
                                            <div class="font-bold text-primary-700 text-sm lg:text-base">Option 2: Chuyển bookings sang lịch trình khác
                                            </div>
                                            <div class="text-xs lg:text-sm text-primary-600 mt-1 space-y-1">
                                                <div>- Chuyển tất cả bookings sang lịch trình khác (cùng tour)</div>
                                                <div>- Không hoàn tiền (vì chuyển schedule thành công)</div>
                                                <div>- Cập nhật quota của schedule mới</div>
                                            </div>
                                            <div id="schedule-select" class="mt-3 hidden">
                                                <select name="new_schedule_id" id="new_schedule_id"
                                                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all bg-primary-50 text-primary-700 text-sm lg:text-base">
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
                                                <p class="text-xs text-danger-text mt-1 hidden" id="schedule-warning">
                                                    ⚠️ Lịch trình được chọn không đủ chỗ cho <?= $total_participants ?> khách
                                                </p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            <?php endif; ?>

                            <!-- Option 3: Cancel with Policy -->
                            <div class="border border-primary-100 rounded-2xl p-4 hover:border-accent transition">
                                <label class="flex items-start cursor-pointer">
                                    <input type="radio" name="action_type" value="cancel_with_policy"
                                        class="mt-1 mr-3 text-accent focus:ring-accent w-4 h-4">
                                    <div class="flex-1">
                                        <div class="font-bold text-primary-700 text-sm lg:text-base">Option 3: Hủy bookings & Hoàn tiền theo chính
                                            sách hủy</div>
                                        <div class="text-xs lg:text-sm text-primary-600 mt-1 space-y-1">
                                            <div>- Áp dụng chính sách hủy tour</div>
                                            <div>- Tính phí hủy theo % trong chính sách</div>
                                            <div>- Hoàn tiền = Số tiền đã trả - Phí hủy</div>
                                            <div>- Tự động tạo yêu cầu hoàn tiền</div>
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
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-primary-100">
                    <a href="?act=admin&module=schedules&action=show&id=<?= $schedule['id'] ?>"
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center">
                        Hủy
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-danger hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2"
                        onclick="return confirm('Bạn có chắc chắn muốn hủy lịch trình này? Hành động này không thể hoàn tác!');">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        Xác nhận Hủy Lịch trình
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