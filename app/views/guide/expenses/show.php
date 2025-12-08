<?php
/**
 * GUIDE - CHI TIẾT CHI PHÍ PHÁT SINH
 * Variables: $schedule, $tour, $bookings, $expenses, $expense_total
 */
?>

<div class="max-w-8xl mx-auto p-4 lg:p-8">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Chi phí phát sinh</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1 flex flex-wrap gap-1 lg:gap-2">
                <?= htmlspecialchars($tour['tour_code']) ?> - <?= htmlspecialchars($tour['name']) ?>
                <span>•</span>
                <?= date('d/m/Y', strtotime($schedule['start_date'])) ?> - <?= date('d/m/Y', strtotime($schedule['end_date'])) ?>
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <a href="?act=guide-expenses&action=create&schedule_id=<?= $schedule['id'] ?>" 
               class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Ghi chi phí mới
            </a>
            <a href="?act=guide-tours&action=show&id=<?= $schedule['id'] ?>&tab=expenses" 
               class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="home" class="w-4 h-4"></i>
                Quay về Tour
            </a>
            <a href="?act=guide-expenses" 
               class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="bg-panel rounded-2xl p-4 lg:p-6 mb-4 lg:mb-6 border border-primary-100 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6">
            <div>
                <div class="text-xs text-primary-500 uppercase tracking-wide mb-1 lg:mb-2">Tổng chi phí</div>
                <div class="text-xl lg:text-2xl font-bold text-primary-700"><?= number_format($expense_total) ?> VNĐ</div>
            </div>
            <div>
                <div class="text-xs text-primary-500 uppercase tracking-wide mb-1 lg:mb-2">Số lượng ghi nhận</div>
                <div class="text-xl lg:text-2xl font-bold text-primary-700"><?= count($expenses) ?></div>
            </div>
            <div>
                <div class="text-xs text-primary-500 uppercase tracking-wide mb-1 lg:mb-2">Tour</div>
                <div class="font-semibold text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($tour['tour_code']) ?></div>
            </div>
        </div>
    </div>

    <!-- Expenses List -->
    <div class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm">
        <div class="p-4 lg:p-6 border-b border-primary-100">
            <h2 class="text-base lg:text-lg font-bold text-primary-700">Danh sách chi phí</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[800px]">
                <thead>
                    <tr class="bg-primary-50">
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase tracking-wider">Ngày</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase tracking-wider">Booking</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase tracking-wider">Loại</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase tracking-wider">Mô tả</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase tracking-wider">Số tiền</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-100">
                    <?php if (empty($expenses)): ?>
                        <tr>
                            <td colspan="7" class="px-3 lg:px-4 py-8 lg:py-12 text-center text-primary-500">
                                Chưa có chi phí nào được ghi nhận.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($expenses as $expense): ?>
                            <tr class="hover:bg-primary-50 transition-colors">
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 text-xs lg:text-sm">
                                    <?= date('d/m/Y', strtotime($expense['expense_date'])) ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 text-xs">
                                    <?php if (!empty($expense['booking_code'])): ?>
                                        <span class="font-mono"><?= htmlspecialchars($expense['booking_code']) ?></span>
                                    <?php else: ?>
                                        <span class="text-primary-500 italic">Chi phí chung của tour</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 text-xs lg:text-sm">
                                    <?= htmlspecialchars($expense['category'] ?? '-') ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <div class="text-primary-700 text-xs lg:text-sm"><?= htmlspecialchars($expense['description']) ?></div>
                                    <?php if (!empty($expense['notes'])): ?>
                                        <div class="text-xs text-primary-500 mt-1"><?= htmlspecialchars($expense['notes']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <div class="font-bold text-primary-700 text-sm lg:text-base"><?= number_format($expense['amount']) ?> VNĐ</div>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <?php
                                    $status_colors = [
                                        'pending' => 'bg-warning-bg text-warning-text',
                                        'approved' => 'bg-success-bg text-success-text',
                                        'rejected' => 'bg-danger-bg text-danger-text'
                                    ];
                                    $status_labels = [
                                        'pending' => 'Chờ duyệt',
                                        'approved' => 'Đã duyệt',
                                        'rejected' => 'Từ chối'
                                    ];
                                    $status = $expense['approval_status'];
                                    ?>
                                    <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase <?= $status_colors[$status] ?? 'bg-primary-100 text-primary-700' ?>">
                                        <?= $status_labels[$status] ?? $status ?>
                                    </span>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <?php if (!empty($expense['receipt_file'])): ?>
                                            <a href="<?= BASE_URL ?>/public/<?= $expense['receipt_file'] ?>" 
                                               target="_blank"
                                               class="text-accent hover:text-accent-dark text-xs lg:text-sm font-semibold flex items-center gap-1">
                                                <i data-lucide="file-text" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                                Xem hóa đơn
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($expense['approval_status'] === 'pending' && $expense['reported_by'] == get_user_id()): ?>
                                            <a href="?act=guide-expenses&action=delete&id=<?= $expense['id'] ?>&schedule_id=<?= $schedule['id'] ?>" 
                                               onclick="return confirm('Bạn có chắc muốn xóa chi phí này?')"
                                               class="text-danger-text hover:text-danger-dark text-xs lg:text-sm font-semibold flex items-center gap-1">
                                                <i data-lucide="trash-2" class="w-3 h-3 lg:w-4 lg:h-4"></i>
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

