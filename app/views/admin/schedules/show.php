<?php
/**
 * ADMIN - CHI TIẾT LỊCH KHỞI HÀNH
 * Variables: $schedule, $bookings
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-8">
    <!-- Page Header - Responsive -->
    <div class="mb-4 lg:mb-8">
        <div class="flex items-center gap-2 text-xs lg:text-sm text-primary-500 mb-2">
            <a href="?act=admin&module=schedules" class="hover:text-accent">Lịch khởi hành</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span>Chi tiết</span>
        </div>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-primary-700"><?= htmlspecialchars($schedule['tour_name']) ?></h1>
                <p class="text-xs lg:text-sm text-primary-500 mt-1">Mã tour: <span class="font-mono text-accent"><?= htmlspecialchars($schedule['tour_code']) ?></span></p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <a href="?act=admin&module=schedules&action=edit&id=<?= $schedule['id'] ?>" 
                   class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-warning hover:opacity-90 text-white rounded-xl font-semibold transition-all shadow-sm text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="pencil" class="w-4 h-4"></i>
                    Sửa
                </a>
                <a href="?act=admin&module=schedules" 
                   class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Quay lại
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-4 lg:mb-6">
        <div class="bg-info-bg border border-info rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-info-text mb-1 font-semibold">Số chỗ mở bán</div>
            <div class="text-xl lg:text-2xl font-bold text-info-text"><?= $schedule['quota'] ?></div>
        </div>
        <div class="bg-success-bg border border-success rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-success-text mb-1 font-semibold">Đã đặt (người)</div>
            <div class="text-xl lg:text-2xl font-bold text-success-text">
                <?= isset($actualBookedCount) ? $actualBookedCount : ($schedule['booked'] ?? 0) ?>
            </div>
            <?php if (isset($approvedBookingsCount)): ?>
                <div class="text-xs text-primary-500 mt-1"><?= $approvedBookingsCount ?> booking<?= $approvedBookingsCount > 1 ? 's' : '' ?></div>
            <?php endif; ?>
        </div>
        <div class="bg-accent-bg border border-accent rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-accent-text mb-1 font-semibold">Còn lại</div>
            <div class="text-xl lg:text-2xl font-bold text-accent-text">
                <?= max(0, ($schedule['quota'] - ($schedule['booked'] ?? 0))) ?>
            </div>
        </div>
        <div class="bg-warning-bg border border-warning rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-warning-text mb-1 font-semibold">Tỷ lệ lấp đầy</div>
            <div class="text-xl lg:text-2xl font-bold text-warning-text">
                <?php 
                $bookedCount = isset($actualBookedCount) ? $actualBookedCount : ($schedule['booked'] ?? 0);
                echo $schedule['quota'] > 0 ? round(($bookedCount / $schedule['quota']) * 100, 1) : 0;
                ?>%
            </div>
        </div>
    </div>

    <!-- Main Info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-4 lg:mb-6">
        <!-- Schedule Info -->
        <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm p-4 lg:p-6">
            <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4">Thông tin Lịch Khởi Hành</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs lg:text-sm text-primary-500">Ngày khởi hành:</span>
                    <span class="font-bold text-success-text text-sm lg:text-base"><?= date('d/m/Y', strtotime($schedule['start_date'])) ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs lg:text-sm text-primary-500">Ngày kết thúc:</span>
                    <span class="font-semibold text-primary-700 text-sm lg:text-base"><?= date('d/m/Y', strtotime($schedule['end_date'])) ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs lg:text-sm text-primary-500">Thời gian:</span>
                    <span class="font-semibold text-primary-700 text-sm lg:text-base">
                        <?= $schedule['duration_days'] ?? 0 ?> ngày 
                        <?= ($schedule['duration_nights'] ?? 0) > 0 ? $schedule['duration_nights'] . ' đêm' : '' ?>
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs lg:text-sm text-primary-500">Trạng thái:</span>
                    <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase
                        <?= $schedule['status'] == 'open' ? 'bg-success-bg text-success-text' :
                            ($schedule['status'] == 'closed' ? 'bg-danger-bg text-danger-text' :
                            ($schedule['status'] == 'completed' ? 'bg-info-bg text-info-text' : 'bg-primary-100 text-primary-500')) ?>">
                        <?php
                        $status_names = [
                            'open' => 'Đang mở bán',
                            'closed' => 'Đóng bán',
                            'completed' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy'
                        ];
                        echo $status_names[$schedule['status']] ?? $schedule['status'];
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm p-4 lg:p-6">
            <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4">Giá bán</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs lg:text-sm text-primary-500">Người lớn:</span>
                    <span class="font-bold text-accent text-sm lg:text-base"><?= number_format($schedule['adult_price'], 0, ',', '.') ?> đ</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs lg:text-sm text-primary-500">Trẻ em:</span>
                    <span class="font-semibold text-primary-700 text-sm lg:text-base"><?= number_format($schedule['child_price'], 0, ',', '.') ?> đ</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs lg:text-sm text-primary-500">Em bé:</span>
                    <span class="font-semibold text-primary-700 text-sm lg:text-base"><?= number_format($schedule['infant_price'], 0, ',', '.') ?> đ</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Guide Info -->
    <?php if (!empty($schedule['guide_name'])): ?>
        <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm p-4 lg:p-6 mb-4 lg:mb-6">
            <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4">Hướng dẫn viên</h2>
            <div class="flex items-center gap-3 lg:gap-4">
                <div class="w-12 h-12 lg:w-14 lg:h-14 bg-info-bg rounded-2xl flex items-center justify-center">
                    <i data-lucide="user" class="w-6 h-6 lg:w-7 lg:h-7 text-info-text"></i>
                </div>
                <div>
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
                <div class="mt-4 p-3 lg:p-4 bg-primary-50 rounded-2xl border border-primary-100">
                    <div class="text-xs lg:text-sm font-semibold text-primary-700 mb-1">Ghi chú:</div>
                    <div class="text-xs lg:text-sm text-primary-600"><?= nl2br(htmlspecialchars($schedule['guide_notes'])) ?></div>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="bg-warning-bg border border-warning rounded-2xl p-4 lg:p-6 mb-4 lg:mb-6">
            <div class="flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4 lg:w-5 lg:h-5 text-warning-text"></i>
                <span class="text-warning-text font-semibold text-sm lg:text-base">Chưa gán hướng dẫn viên cho lịch này</span>
            </div>
            <a href="?act=admin&module=schedules&action=edit&id=<?= $schedule['id'] ?>" 
               class="mt-2 inline-block text-xs lg:text-sm text-accent hover:text-accent-hover font-semibold flex items-center gap-1">
                Gán HDV ngay
                <i data-lucide="arrow-right" class="w-3 h-3"></i>
            </a>
        </div>
    <?php endif; ?>

    <!-- Guide Change History -->
    <?php if (!empty($guide_history)): ?>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-history text-blue-500"></i> Lịch sử thay đổi HDV
            </h2>
            <div class="space-y-3">
                <?php foreach ($guide_history as $h): ?>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="font-medium text-gray-800 mb-2">
                                    <?php if ($h['old_guide_name'] && $h['new_guide_name']): ?>
                                        <span class="text-red-600"><?= htmlspecialchars($h['old_guide_name']) ?></span>
                                        <span class="mx-2 text-gray-400">→</span>
                                        <span class="text-green-600"><?= htmlspecialchars($h['new_guide_name']) ?></span>
                                    <?php elseif ($h['new_guide_name']): ?>
                                        <span class="text-green-600">Gán: <?= htmlspecialchars($h['new_guide_name']) ?></span>
                                    <?php else: ?>
                                        <span class="text-red-600">Hủy gán: <?= htmlspecialchars($h['old_guide_name']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($h['reason']): ?>
                                    <div class="text-sm text-gray-600 mb-1">
                                        <span class="font-medium">Lý do:</span> <?= htmlspecialchars($h['reason']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($h['notes']): ?>
                                    <div class="text-sm text-gray-500 mb-1">
                                        <span class="font-medium">Ghi chú:</span> <?= htmlspecialchars($h['notes']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="text-sm text-gray-500 text-right ml-4">
                                <div class="font-medium"><?= date('d/m/Y', strtotime($h['created_at'])) ?></div>
                                <div class="text-xs text-gray-400"><?= date('H:i', strtotime($h['created_at'])) ?></div>
                                <?php if ($h['changed_by_name']): ?>
                                    <div class="text-xs text-gray-400 mt-1">bởi <?= htmlspecialchars($h['changed_by_name']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Bookings List -->
    <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm p-4 lg:p-6">
        <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4">
            Danh sách khách hàng đã đặt tour 
            <span class="text-accent">
                (<?= isset($totalBookingsCount) ? $totalBookingsCount : count($bookings ?? []) ?> booking<?= (isset($totalBookingsCount) ? $totalBookingsCount : count($bookings ?? [])) > 1 ? 's' : '' ?><?= isset($actualBookedCount) && $actualBookedCount > 0 ? ' - ' . $actualBookedCount . ' người' : '' ?>)
            </span>
            <?php if (isset($totalBookingsCount) && $totalBookingsCount > $approvedBookingsCount): ?>
                <span class="text-xs text-primary-500 ml-2">
                    (<?= $approvedBookingsCount ?> đã duyệt/chờ duyệt, <?= $totalBookingsCount - $approvedBookingsCount ?> đã hủy/từ chối)
                </span>
            <?php endif; ?>
        </h2>
        <p class="text-xs lg:text-sm text-primary-500 mb-3 lg:mb-4">
            Danh sách tất cả khách hàng đã đặt tour cho lịch khởi hành này (ngày <?= date('d/m/Y', strtotime($schedule['start_date'])) ?>)
        </p>
        <?php if (empty($bookings)): ?>
            <div class="text-center py-6 lg:py-8 text-primary-500 text-sm">
                <p>Chưa có đặt tour nào cho lịch này</p>
                <?php if (isset($schedule['booked']) && $schedule['booked'] > 0): ?>
                    <div class="mt-4 p-4 bg-warning-bg border border-warning rounded-2xl text-left max-w-2xl mx-auto">
                        <p class="text-xs lg:text-sm text-warning-text font-semibold mb-2 flex items-start gap-2">
                            <i data-lucide="alert-triangle" class="w-4 h-4 flex-shrink-0 mt-0.5"></i>
                            <span>Lưu ý: Schedule có <strong>booked = <?= $schedule['booked'] ?></strong> trong database nhưng không tìm thấy bookings.</span>
                        </p>
                        <p class="text-xs text-warning-text mb-2">
                            Có thể:
                        </p>
                        <ul class="text-xs text-warning-text list-disc list-inside mb-2 space-y-1">
                            <li>Bookings đã bị xóa hoặc tour_id/start_date không khớp</li>
                            <li>Bookings có tour_schedule_id = NULL hoặc khác schedule ID này</li>
                            <li>Bookings có payment_status = 'cancelled', 'rejected' hoặc 'refunded' (không được tính)</li>
                        </ul>
                        <p class="text-xs text-warning-text">
                            Vui lòng chạy các query trong file <code class="bg-warning-bg/50 px-2 py-1 rounded-xl">test_schedule_bookings.sql</code> để kiểm tra chi tiết.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead class="bg-primary-50 text-primary-700 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Mã đặt</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Khách hàng</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-center">Số khách</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-right">Tổng tiền</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-center">Trạng thái</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary-100">
                        <?php foreach ($bookings as $b): ?>
                            <tr class="hover:bg-primary-50 transition-colors">
                                <td class="px-3 lg:px-4 py-2 lg:py-3 font-mono text-sm text-accent">
                                    <?= htmlspecialchars($b['booking_code'] ?? 'N/A') ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <div class="font-semibold text-primary-700 text-sm"><?= htmlspecialchars($b['customer_name'] ?? 'N/A') ?></div>
                                    <div class="text-xs text-primary-500"><?= htmlspecialchars($b['customer_phone'] ?? '') ?></div>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                    <span class="font-semibold text-sm">
                                        <?= ($b['adult_count'] ?? 0) + ($b['child_count'] ?? 0) + ($b['infant_count'] ?? 0) ?>
                                    </span>
                                    <div class="text-xs text-primary-500">
                                        NL: <?= $b['adult_count'] ?? 0 ?>, 
                                        TE: <?= $b['child_count'] ?? 0 ?>, 
                                        EB: <?= $b['infant_count'] ?? 0 ?>
                                    </div>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-right font-semibold text-sm">
                                    <?= number_format($b['total_amount'] ?? 0, 0, ',', '.') ?> đ
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                    <?php
                                    $paymentStatus = $b['payment_status'] ?? '';
                                    $statusClass = get_payment_status_color($paymentStatus);
                                    $statusText = payment_status_text($paymentStatus);
                                    ?>
                                    <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase <?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                    <?php if ($paymentStatus): ?>
                                        <div class="text-xs text-primary-500 mt-1">
                                            <?php
                                            $paymentText = [
                                                'paid' => 'Đã thanh toán',
                                                'partial' => 'Đã đặt cọc',
                                                'unpaid' => 'Chưa thanh toán',
                                                'refunded' => 'Đã hoàn tiền'
                                            ];
                                            echo $paymentText[$paymentStatus] ?? ucfirst($paymentStatus);
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-right">
                                    <a href="?act=admin&module=bookings&action=show&id=<?= $b['id'] ?>" 
                                       class="text-accent hover:text-accent-hover text-xs lg:text-sm font-semibold flex items-center justify-end gap-1">
                                        Xem
                                        <i data-lucide="arrow-right" class="w-3 h-3"></i>
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

