<?php
/**
 * ADMIN - CHI TIẾT LỊCH KHỞI HÀNH
 * Variables: $schedule, $bookings
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="?act=admin&module=schedules" class="hover:text-blue-600">Lịch khởi hành</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span>Chi tiết</span>
        </div>
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-slate-800"><?= htmlspecialchars($schedule['tour_name']) ?></h1>
                <p class="text-sm text-gray-500 mt-1">Mã tour: <span class="font-mono text-blue-600"><?= htmlspecialchars($schedule['tour_code']) ?></span></p>
            </div>
            <div class="flex gap-2">
                <a href="?act=admin&module=schedules&action=edit&id=<?= $schedule['id'] ?>" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    ✏️ Sửa
                </a>
                <a href="?act=admin&module=schedules" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    ← Quay lại
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="text-sm text-blue-600 mb-1">Số chỗ mở bán</div>
            <div class="text-2xl font-bold text-blue-700"><?= $schedule['quota'] ?></div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="text-sm text-green-600 mb-1">Đã đặt</div>
            <div class="text-2xl font-bold text-green-700"><?= $schedule['booked'] ?? 0 ?></div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="text-sm text-purple-600 mb-1">Còn lại</div>
            <div class="text-2xl font-bold text-purple-700">
                <?= max(0, ($schedule['quota'] - ($schedule['booked'] ?? 0))) ?>
            </div>
        </div>
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
            <div class="text-sm text-orange-600 mb-1">Tỷ lệ lấp đầy</div>
            <div class="text-2xl font-bold text-orange-700">
                <?= $schedule['quota'] > 0 ? round((($schedule['booked'] ?? 0) / $schedule['quota']) * 100, 1) : 0 ?>%
            </div>
        </div>
    </div>

    <!-- Main Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Schedule Info -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Thông tin Lịch Khởi Hành</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày khởi hành:</span>
                    <span class="font-bold text-green-700"><?= date('d/m/Y', strtotime($schedule['start_date'])) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ngày kết thúc:</span>
                    <span class="font-medium"><?= date('d/m/Y', strtotime($schedule['end_date'])) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Thời gian:</span>
                    <span class="font-medium">
                        <?= $schedule['duration_days'] ?? 0 ?> ngày 
                        <?= ($schedule['duration_nights'] ?? 0) > 0 ? $schedule['duration_nights'] . ' đêm' : '' ?>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Trạng thái:</span>
                    <span class="px-2 py-1 text-xs rounded-full font-medium
                        <?= $schedule['status'] == 'open' ? 'bg-green-100 text-green-800' :
                            ($schedule['status'] == 'closed' ? 'bg-red-100 text-red-800' :
                            ($schedule['status'] == 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) ?>">
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
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Giá bán</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Người lớn:</span>
                    <span class="font-bold text-blue-700"><?= number_format($schedule['adult_price'], 0, ',', '.') ?> đ</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Trẻ em:</span>
                    <span class="font-medium"><?= number_format($schedule['child_price'], 0, ',', '.') ?> đ</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Em bé:</span>
                    <span class="font-medium"><?= number_format($schedule['infant_price'], 0, ',', '.') ?> đ</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Guide Info -->
    <?php if (!empty($schedule['guide_name'])): ?>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Hướng dẫn viên</h2>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-blue-600 text-xl"></i>
                </div>
                <div>
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
                <div class="mt-4 p-3 bg-gray-50 rounded border border-gray-200">
                    <div class="text-sm font-medium text-gray-700 mb-1">Ghi chú:</div>
                    <div class="text-sm text-gray-600"><?= nl2br(htmlspecialchars($schedule['guide_notes'])) ?></div>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-6">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                <span class="text-yellow-800 font-medium">Chưa gán hướng dẫn viên cho lịch này</span>
            </div>
            <a href="?act=admin&module=schedules&action=edit&id=<?= $schedule['id'] ?>" 
               class="mt-2 inline-block text-sm text-blue-600 hover:underline">
                Gán HDV ngay →
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
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">
            Danh sách khách hàng đã đặt tour 
            <span class="text-blue-600">(<?= count($bookings ?? []) ?> đặt chỗ)</span>
        </h2>
        <p class="text-sm text-gray-500 mb-4">
            Danh sách tất cả khách hàng đã đặt tour cho lịch khởi hành này (ngày <?= date('d/m/Y', strtotime($schedule['start_date'])) ?>)
        </p>
        <?php if (empty($bookings)): ?>
            <div class="text-center py-8 text-gray-500">
                Chưa có đặt tour nào cho lịch này
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-4 py-3 border-b">Mã đặt</th>
                            <th class="px-4 py-3 border-b">Khách hàng</th>
                            <th class="px-4 py-3 border-b text-center">Số khách</th>
                            <th class="px-4 py-3 border-b text-right">Tổng tiền</th>
                            <th class="px-4 py-3 border-b text-center">Trạng thái</th>
                            <th class="px-4 py-3 border-b text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($bookings as $b): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-sm text-blue-600">
                                    <?= htmlspecialchars($b['booking_code'] ?? 'N/A') ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium"><?= htmlspecialchars($b['customer_name'] ?? 'N/A') ?></div>
                                    <div class="text-xs text-gray-500"><?= htmlspecialchars($b['customer_phone'] ?? '') ?></div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-medium">
                                        <?= ($b['adult_count'] ?? 0) + ($b['child_count'] ?? 0) + ($b['infant_count'] ?? 0) ?>
                                    </span>
                                    <div class="text-xs text-gray-500">
                                        NL: <?= $b['adult_count'] ?? 0 ?>, 
                                        TE: <?= $b['child_count'] ?? 0 ?>, 
                                        EB: <?= $b['infant_count'] ?? 0 ?>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right font-medium">
                                    <?= number_format($b['total_amount'] ?? 0, 0, ',', '.') ?> đ
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full font-medium
                                        <?= ($b['status'] ?? '') == 'confirmed' ? 'bg-green-100 text-green-800' :
                                            (($b['status'] ?? '') == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                            (($b['status'] ?? '') == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) ?>">
                                        <?= ucfirst($b['status'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="?act=admin&module=bookings&action=show&id=<?= $b['id'] ?>" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        Xem →
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

