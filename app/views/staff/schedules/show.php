<?php
/**
 * STAFF - CHI TIẾT LỊCH TOUR (READ ONLY)
 * Variables: $schedule, $tour, $bookings
 */
?>
<div class="max-w-6xl mx-auto">
    <!-- HEADER - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <nav class="text-xs lg:text-sm text-primary-500 mb-2 flex items-center gap-2">
                <a href="?act=staff-schedules" class="hover:text-accent">Lịch tour</a>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                <span class="text-primary-700">Chi tiết</span>
            </nav>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700"><?= htmlspecialchars($schedule['tour_name']) ?></h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Mã tour: <span class="font-mono text-accent"><?= htmlspecialchars($schedule['tour_code']) ?></span></p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <?php if ($schedule['status'] == 'open' && (($schedule['quota'] ?? 0) - ($schedule['booked'] ?? 0)) > 0): ?>
                <a href="?act=staff-bookings&action=create&tour_id=<?= $schedule['tour_id'] ?>&start_date=<?= $schedule['start_date'] ?>" 
                   class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-success to-success-text hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    Tạo Booking
                </a>
            <?php endif; ?>
            <a href="?act=staff-schedules" 
               class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 lg:gap-4 mb-4 lg:mb-6">
        <!-- STATS CARDS -->
        <div class="bg-info-bg border border-info rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-info-text mb-1 font-semibold">Tổng chỗ</div>
            <div class="text-xl lg:text-2xl font-bold text-info-text"><?= $schedule['quota'] ?? 0 ?></div>
        </div>
        <div class="bg-success-bg border border-success rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-success-text mb-1 font-semibold">Đã đặt</div>
            <div class="text-xl lg:text-2xl font-bold text-success-text"><?= $schedule['booked'] ?? 0 ?></div>
        </div>
        <div class="bg-warning-bg border border-warning rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-warning-text mb-1 font-semibold">Còn lại</div>
            <div class="text-xl lg:text-2xl font-bold text-warning-text">
                <?= max(0, ($schedule['quota'] ?? 0) - ($schedule['booked'] ?? 0)) ?>
            </div>
            <div class="text-xs text-primary-500 mt-1">
                <?= ($schedule['quota'] ?? 0) > 0 ? round((($schedule['booked'] ?? 0) / ($schedule['quota'] ?? 1)) * 100, 1) : 0 ?>% đầy
            </div>
        </div>
    </div>

    <!-- SCHEDULE INFO -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6 mb-4 lg:mb-6">
        <h2 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-3 lg:mb-4">Thông tin lịch khởi hành</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
            <div>
                <span class="block text-xs lg:text-sm text-primary-500 mb-1 lg:mb-2">Ngày khởi hành</span>
                <span class="font-bold text-success-text text-sm lg:text-base"><?= date('d/m/Y', strtotime($schedule['start_date'])) ?></span>
            </div>
            <div>
                <span class="block text-xs lg:text-sm text-primary-500 mb-1 lg:mb-2">Ngày kết thúc</span>
                <span class="font-semibold text-primary-700 text-sm lg:text-base"><?= date('d/m/Y', strtotime($schedule['end_date'])) ?></span>
            </div>
            <div>
                <span class="block text-xs lg:text-sm text-primary-500 mb-1 lg:mb-2">Thời gian</span>
                <span class="font-semibold text-primary-700 text-sm lg:text-base">
                    <?= $schedule['duration_days'] ?? 0 ?> ngày 
                    <?= ($schedule['duration_nights'] ?? 0) > 0 ? ($schedule['duration_nights'] ?? 0) . ' đêm' : '' ?>
                </span>
            </div>
            <div>
                <span class="block text-xs lg:text-sm text-primary-500 mb-1 lg:mb-2">Trạng thái</span>
                <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase
                    <?= $schedule['status'] == 'open' ? 'bg-success-bg text-success-text' :
                        ($schedule['status'] == 'closed' ? 'bg-danger-bg text-danger-text' :
                            ($schedule['status'] == 'completed' ? 'bg-info-bg text-info-text' : 'bg-primary-100 text-primary-500')) ?>">
                    <?php
                    $status_names = [
                        'open' => 'Mở bán',
                        'closed' => 'Đóng',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Hủy'
                    ];
                    echo $status_names[$schedule['status']] ?? $schedule['status'];
                    ?>
                </span>
            </div>
            <div>
                <span class="block text-xs lg:text-sm text-primary-500 mb-1 lg:mb-2">Điểm khởi hành</span>
                <span class="font-semibold text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($tour['departure_location'] ?? 'Chưa xác định') ?></span>
            </div>
        </div>
    </div>

    <!-- PRICING -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6 mb-4 lg:mb-6">
        <h2 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-3 lg:mb-4">Giá tour</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <span class="block text-xs lg:text-sm text-primary-500 mb-1 lg:mb-2">Người lớn</span>
                <span class="font-bold text-accent text-base lg:text-lg"><?= number_format($schedule['adult_price'] ?? 0, 0, ',', '.') ?> đ</span>
            </div>
            <div>
                <span class="block text-xs lg:text-sm text-primary-500 mb-1 lg:mb-2">Trẻ em</span>
                <span class="font-semibold text-primary-700 text-sm lg:text-base"><?= number_format($schedule['child_price'] ?? 0, 0, ',', '.') ?> đ</span>
            </div>
            <div>
                <span class="block text-xs lg:text-sm text-primary-500 mb-1 lg:mb-2">Em bé</span>
                <span class="font-semibold text-primary-700 text-sm lg:text-base"><?= number_format($schedule['infant_price'] ?? 0, 0, ',', '.') ?> đ</span>
            </div>
        </div>
    </div>

    <!-- GUIDE INFO -->
    <?php if (!empty($schedule['guide_name'])): ?>
        <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6 mb-4 lg:mb-6">
            <h2 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-3 lg:mb-4">Hướng dẫn viên</h2>
            <div class="flex items-center gap-3 lg:gap-4">
                <div class="flex-1">
                    <div class="font-bold text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($schedule['guide_name']) ?></div>
                    <?php if (!empty($schedule['guide_phone'])): ?>
                        <div class="text-xs lg:text-sm text-primary-600 flex items-center gap-1 mt-1">
                            <i data-lucide="phone" class="w-3 h-3"></i>
                            <?= htmlspecialchars($schedule['guide_phone']) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($schedule['guide_email'])): ?>
                        <div class="text-xs lg:text-sm text-primary-600 flex items-center gap-1 mt-1">
                            <i data-lucide="mail" class="w-3 h-3"></i>
                            <?= htmlspecialchars($schedule['guide_email']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!empty($schedule['guide_notes'])): ?>
                <div class="mt-4 p-3 lg:p-4 bg-warning-bg border border-warning rounded-2xl">
                    <div class="text-xs lg:text-sm font-semibold text-warning-text mb-1">Ghi chú:</div>
                    <div class="text-xs lg:text-sm text-warning-text"><?= nl2br(htmlspecialchars($schedule['guide_notes'])) ?></div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- BOOKINGS LIST -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
        <h2 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-3 lg:mb-4">
            Danh sách đặt tour (<?= count($bookings ?? []) ?>)
        </h2>
        <?php if (empty($bookings)): ?>
            <p class="text-primary-500 text-center py-6 lg:py-8 text-sm">Chưa có booking nào cho lịch này.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead class="bg-primary-50 text-primary-700 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Mã Booking</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Khách hàng</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-center">Số người</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-right">Tổng tiền</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-right">Đã thanh toán</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-center">Trạng thái</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary-100">
                        <?php foreach ($bookings as $b): ?>
                            <tr class="hover:bg-primary-50 transition-colors">
                                <td class="px-3 lg:px-4 py-2 lg:py-3 font-mono text-sm text-accent">
                                    <?= htmlspecialchars($b['booking_code'] ?? '') ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <div class="font-semibold text-primary-700 text-sm"><?= htmlspecialchars($b['customer_name'] ?? '') ?></div>
                                    <div class="text-xs text-primary-500"><?= htmlspecialchars($b['customer_phone'] ?? '') ?></div>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-center text-sm">
                                    <?= ($b['adult_count'] ?? 0) + ($b['child_count'] ?? 0) + ($b['infant_count'] ?? 0) ?> người
                                    <div class="text-xs text-primary-500">
                                        (<?= $b['adult_count'] ?? 0 ?>NL, <?= $b['child_count'] ?? 0 ?>TE, <?= $b['infant_count'] ?? 0 ?>EB)
                                    </div>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-right font-semibold text-sm">
                                    <?= number_format($b['final_amount'] ?? 0, 0, ',', '.') ?> đ
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-right">
                                    <span class="font-semibold text-sm <?= ($b['payment_status'] ?? '') == 'paid' ? 'text-success-text' : (($b['payment_status'] ?? '') == 'partial' ? 'text-warning-text' : 'text-danger-text') ?>">
                                        <?= number_format($b['paid_amount'] ?? 0, 0, ',', '.') ?> đ
                                    </span>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                    <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase <?= get_payment_status_color($b['payment_status'] ?? 'unpaid') ?>">
                                        <?= payment_status_text($b['payment_status'] ?? 'unpaid') ?>
                                    </span>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-right">
                                    <a href="?act=staff-bookings&action=show&id=<?= $b['id'] ?>"
                                        class="text-accent hover:text-accent-hover p-1.5 rounded-xl hover:bg-primary-50 transition-all" title="Xem chi tiết">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

