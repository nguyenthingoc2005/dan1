<?php
/**
 * GUIDE - CHI TIẾT CHI PHÍ PHÁT SINH
 * Variables: $schedule, $tour, $bookings, $expenses, $expense_total
 */
?>

<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Chi phí phát sinh</h1>
            <p class="text-slate-500 text-sm mt-1">
                <?= htmlspecialchars($tour['tour_code']) ?> - <?= htmlspecialchars($tour['name']) ?>
                <span class="mx-2">•</span>
                <?= date('d/m/Y', strtotime($schedule['start_date'])) ?> - <?= date('d/m/Y', strtotime($schedule['end_date'])) ?>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="?act=guide-expenses&action=create&schedule_id=<?= $schedule['id'] ?>" 
               class="px-4 py-2 bg-accent text-white rounded hover:bg-accent/90 font-medium">
                + Ghi chi phí mới
            </a>
            <a href="?act=guide-expenses" 
               class="px-4 py-2 bg-panel border border-slate-300 text-slate-700 rounded hover:bg-slate-50">
                ← Quay lại
            </a>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="bg-panel rounded p-6 mb-6 border border-slate-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Tổng chi phí</div>
                <div class="text-2xl font-bold text-slate-900"><?= number_format($expense_total) ?> VNĐ</div>
            </div>
            <div>
                <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Số lượng ghi nhận</div>
                <div class="text-2xl font-bold text-slate-900"><?= count($expenses) ?></div>
            </div>
            <div>
                <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Tour</div>
                <div class="font-semibold text-slate-900"><?= htmlspecialchars($tour['tour_code']) ?></div>
            </div>
        </div>
    </div>

    <!-- Expenses List -->
    <div class="bg-panel rounded overflow-hidden border border-slate-200">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-800">Danh sách chi phí</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Ngày</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Booking</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Loại</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Mô tả</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Số tiền</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($expenses)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Chưa có chi phí nào được ghi nhận.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($expenses as $expense): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-slate-600 text-sm">
                                    <?= date('d/m/Y', strtotime($expense['expense_date'])) ?>
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-xs font-mono">
                                    <?= htmlspecialchars($expense['booking_code']) ?>
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-sm">
                                    <?= htmlspecialchars($expense['category'] ?? '-') ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-slate-700 text-sm"><?= htmlspecialchars($expense['description']) ?></div>
                                    <?php if (!empty($expense['notes'])): ?>
                                        <div class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($expense['notes']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900"><?= number_format($expense['amount']) ?> VNĐ</div>
                                </td>
                                <td class="px-4 py-3">
                                    <?php
                                    $status_colors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800'
                                    ];
                                    $status_labels = [
                                        'pending' => 'Chờ duyệt',
                                        'approved' => 'Đã duyệt',
                                        'rejected' => 'Từ chối'
                                    ];
                                    $status = $expense['approval_status'];
                                    ?>
                                    <span class="px-2 py-1 rounded text-xs font-medium <?= $status_colors[$status] ?? 'bg-slate-100 text-slate-800' ?>">
                                        <?= $status_labels[$status] ?? $status ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <?php if (!empty($expense['receipt_file'])): ?>
                                            <a href="<?= BASE_URL ?>/public/<?= $expense['receipt_file'] ?>" 
                                               target="_blank"
                                               class="text-accent hover:text-accent/80 text-sm">
                                                Xem hóa đơn
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($expense['approval_status'] === 'pending' && $expense['reported_by'] == get_user_id()): ?>
                                            <a href="?act=guide-expenses&action=delete&id=<?= $expense['id'] ?>&schedule_id=<?= $schedule['id'] ?>" 
                                               onclick="return confirm('Bạn có chắc muốn xóa chi phí này?')"
                                               class="text-red-600 hover:text-red-800 text-sm">
                                                Xóa
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

