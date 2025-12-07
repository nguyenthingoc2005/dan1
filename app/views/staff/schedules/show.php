<?php
/**
 * STAFF - CHI TIẾT LỊCH TOUR (READ ONLY)
 * Variables: $schedule, $tour, $bookings
 */
?>
<div class="max-w-6xl mx-auto">
    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <nav class="text-sm text-gray-500 mb-2">
                <a href="?act=staff-schedules" class="hover:text-blue-600">Lịch tour</a>
                <span class="mx-2">/</span>
                <span class="text-gray-800">Chi tiết</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-800"><?= htmlspecialchars($schedule['tour_name']) ?></h1>
            <p class="text-sm text-gray-500 mt-1">Mã tour: <span class="font-mono text-blue-600"><?= htmlspecialchars($schedule['tour_code']) ?></span></p>
        </div>
        <div class="flex gap-2">
            <?php if ($schedule['status'] == 'open' && (($schedule['quota'] ?? 0) - ($schedule['booked'] ?? 0)) > 0): ?>
                <a href="?act=staff-bookings&action=create&tour_id=<?= $schedule['tour_id'] ?>&start_date=<?= $schedule['start_date'] ?>" 
                   class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 shadow">
                    ➕ Tạo Booking
                </a>
            <?php endif; ?>
            <a href="?act=staff-schedules" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                ← Quay lại
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- STATS CARDS -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="text-sm text-blue-600 mb-1">Tổng chỗ</div>
            <div class="text-2xl font-bold text-blue-700"><?= $schedule['quota'] ?? 0 ?></div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="text-sm text-green-600 mb-1">Đã đặt</div>
            <div class="text-2xl font-bold text-green-700"><?= $schedule['booked'] ?? 0 ?></div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="text-sm text-yellow-600 mb-1">Còn lại</div>
            <div class="text-2xl font-bold text-yellow-700">
                <?= max(0, ($schedule['quota'] ?? 0) - ($schedule['booked'] ?? 0)) ?>
            </div>
            <div class="text-xs text-gray-500 mt-1">
                <?= ($schedule['quota'] ?? 0) > 0 ? round((($schedule['booked'] ?? 0) / ($schedule['quota'] ?? 1)) * 100, 1) : 0 ?>% đầy
            </div>
        </div>
    </div>

    <!-- SCHEDULE INFO -->
    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
        <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Thông tin lịch khởi hành</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <span class="block text-sm text-gray-500 mb-1">Ngày khởi hành</span>
                <span class="font-bold text-green-700"><?= date('d/m/Y', strtotime($schedule['start_date'])) ?></span>
            </div>
            <div>
                <span class="block text-sm text-gray-500 mb-1">Ngày kết thúc</span>
                <span class="font-medium"><?= date('d/m/Y', strtotime($schedule['end_date'])) ?></span>
            </div>
            <div>
                <span class="block text-sm text-gray-500 mb-1">Thời gian</span>
                <span class="font-medium">
                    <?= $schedule['duration_days'] ?? 0 ?> ngày 
                    <?= ($schedule['duration_nights'] ?? 0) > 0 ? ($schedule['duration_nights'] ?? 0) . ' đêm' : '' ?>
                </span>
            </div>
            <div>
                <span class="block text-sm text-gray-500 mb-1">Trạng thái</span>
                <span class="px-2 py-1 text-xs rounded-full font-medium
                    <?= $schedule['status'] == 'open' ? 'bg-green-100 text-green-800' :
                        ($schedule['status'] == 'closed' ? 'bg-red-100 text-red-800' :
                            ($schedule['status'] == 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) ?>">
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
                <span class="block text-sm text-gray-500 mb-1">Điểm khởi hành</span>
                <span class="font-medium"><?= htmlspecialchars($tour['departure_location'] ?? 'Chưa xác định') ?></span>
            </div>
        </div>
    </div>

    <!-- PRICING -->
    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
        <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Giá tour</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <span class="block text-sm text-gray-500 mb-1">Người lớn</span>
                <span class="font-bold text-blue-700 text-lg"><?= number_format($schedule['adult_price'] ?? 0, 0, ',', '.') ?> đ</span>
            </div>
            <div>
                <span class="block text-sm text-gray-500 mb-1">Trẻ em</span>
                <span class="font-medium"><?= number_format($schedule['child_price'] ?? 0, 0, ',', '.') ?> đ</span>
            </div>
            <div>
                <span class="block text-sm text-gray-500 mb-1">Em bé</span>
                <span class="font-medium"><?= number_format($schedule['infant_price'] ?? 0, 0, ',', '.') ?> đ</span>
            </div>
        </div>
    </div>

    <!-- GUIDE INFO -->
    <?php if (!empty($schedule['guide_name'])): ?>
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Hướng dẫn viên</h2>
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <div class="font-bold text-gray-800"><?= htmlspecialchars($schedule['guide_name']) ?></div>
                    <?php if (!empty($schedule['guide_phone'])): ?>
                        <div class="text-sm text-gray-600">📞 <?= htmlspecialchars($schedule['guide_phone']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($schedule['guide_email'])): ?>
                        <div class="text-sm text-gray-600">✉️ <?= htmlspecialchars($schedule['guide_email']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!empty($schedule['guide_notes'])): ?>
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                    <div class="text-sm font-medium text-yellow-800 mb-1">Ghi chú:</div>
                    <div class="text-sm text-yellow-900"><?= nl2br(htmlspecialchars($schedule['guide_notes'])) ?></div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- BOOKINGS LIST -->
    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
        <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">
            Danh sách đặt tour (<?= count($bookings ?? []) ?>)
        </h2>
        <?php if (empty($bookings)): ?>
            <p class="text-gray-500 text-center py-8">Chưa có booking nào cho lịch này.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-4 py-3 border-b">Mã Booking</th>
                            <th class="px-4 py-3 border-b">Khách hàng</th>
                            <th class="px-4 py-3 border-b text-center">Số người</th>
                            <th class="px-4 py-3 border-b text-right">Tổng tiền</th>
                            <th class="px-4 py-3 border-b text-right">Đã thanh toán</th>
                            <th class="px-4 py-3 border-b text-center">Trạng thái</th>
                            <th class="px-4 py-3 border-b text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($bookings as $b): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-sm text-blue-600">
                                    <?= htmlspecialchars($b['booking_code'] ?? '') ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium"><?= htmlspecialchars($b['customer_name'] ?? '') ?></div>
                                    <div class="text-xs text-gray-500"><?= htmlspecialchars($b['customer_phone'] ?? '') ?></div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?= ($b['adult_count'] ?? 0) + ($b['child_count'] ?? 0) + ($b['infant_count'] ?? 0) ?> người
                                    <div class="text-xs text-gray-500">
                                        (<?= $b['adult_count'] ?? 0 ?>NL, <?= $b['child_count'] ?? 0 ?>TE, <?= $b['infant_count'] ?? 0 ?>EB)
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right font-medium">
                                    <?= number_format($b['final_amount'] ?? 0, 0, ',', '.') ?> đ
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="font-medium <?= ($b['payment_status'] ?? '') == 'paid' ? 'text-green-600' : (($b['payment_status'] ?? '') == 'partial' ? 'text-yellow-600' : 'text-red-600') ?>">
                                        <?= number_format($b['paid_amount'] ?? 0, 0, ',', '.') ?> đ
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full font-medium <?= get_payment_status_color($b['payment_status'] ?? 'unpaid') ?>">
                                        <?= payment_status_text($b['payment_status'] ?? 'unpaid') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="?act=staff-bookings&action=show&id=<?= $b['id'] ?>"
                                        class="text-blue-600 hover:text-blue-800" title="Xem chi tiết">
                                        👁️
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

