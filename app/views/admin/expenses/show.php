<?php
/**
 * ADMIN - CHI TIẾT CHI PHÍ PHÁT SINH
 * Variables: $schedule, $tour, $bookings, $expenses, $expense_total
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-8">
    <!-- Page Header -->
    <div class="mb-4 lg:mb-6">
        <div class="flex items-center gap-2 text-xs lg:text-sm text-primary-500 mb-2">
            <a href="?act=admin&module=expenses" class="hover:text-accent">Chi phí phát sinh</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span>Chi tiết</span>
        </div>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Chi phí phát sinh</h1>
                <p class="text-xs lg:text-sm text-primary-500 mt-1 flex flex-wrap gap-1 lg:gap-2">
                    <?= htmlspecialchars($tour['tour_code']) ?> - <?= htmlspecialchars($tour['name']) ?>
                    <span>•</span>
                    <?= date('d/m/Y', strtotime($schedule['start_date'])) ?> - <?= date('d/m/Y', strtotime($schedule['end_date'])) ?>
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <a href="?act=admin&module=expenses&action=create&schedule_id=<?= $schedule['id'] ?>" 
                   class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Thêm chi phí
                </a>
                <a href="?act=admin&module=schedules&action=show&id=<?= $schedule['id'] ?>" 
                   class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Quay lại
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="bg-panel rounded-2xl p-4 lg:p-6 mb-4 lg:mb-6 border border-primary-100 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6">
            <div>
                <div class="text-xs text-primary-500 uppercase tracking-wide mb-1 lg:mb-2">Tổng chi phí (đã duyệt)</div>
                <div class="text-xl lg:text-2xl font-bold text-primary-700"><?= number_format($expense_total, 0, ',', '.') ?> VNĐ</div>
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
            <table class="w-full text-left min-w-[1000px]">
                <thead>
                    <tr class="bg-primary-50">
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase">Ngày</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase">Booking</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase">Loại</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase">Mô tả</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase">Số tiền</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase">Người ghi</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase">Trạng thái</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-xs font-semibold text-primary-600 uppercase text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-100">
                    <?php if (empty($expenses)): ?>
                        <tr>
                            <td colspan="8" class="px-3 lg:px-4 py-8 lg:py-12 text-center text-primary-500">
                                Chưa có chi phí nào được ghi nhận.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($expenses as $expense): ?>
                            <tr class="hover:bg-primary-50 transition-colors">
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 text-xs lg:text-sm">
                                    <?= date('d/m/Y', strtotime($expense['expense_date'])) ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 text-xs font-mono">
                                    <?= htmlspecialchars($expense['booking_code'] ?? '-') ?>
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
                                    <div class="font-bold text-primary-700 text-sm lg:text-base"><?= number_format($expense['amount'], 0, ',', '.') ?> VNĐ</div>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 text-xs">
                                    <?= htmlspecialchars($expense['reported_by_name'] ?? '-') ?>
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
                                    $status = $expense['approval_status'] ?? 'pending';
                                    ?>
                                    <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase <?= $status_colors[$status] ?? 'bg-primary-100 text-primary-700' ?>">
                                        <?= $status_labels[$status] ?? $status ?>
                                    </span>
                                    <?php if ($status === 'approved' && !empty($expense['approved_by_name'])): ?>
                                        <div class="text-xs text-primary-500 mt-1">Bởi: <?= htmlspecialchars($expense['approved_by_name']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <div class="flex flex-col gap-2">
                                        <?php if (!empty($expense['receipt_file'])): ?>
                                            <a href="<?= BASE_URL ?>/public/<?= $expense['receipt_file'] ?>" 
                                               target="_blank"
                                               class="text-accent hover:text-accent-dark text-xs font-semibold flex items-center gap-1">
                                                <i data-lucide="file-text" class="w-3 h-3"></i>
                                                Hóa đơn
                                            </a>
                                        <?php endif; ?>
                                        <div class="flex gap-2">
                                            <a href="?act=admin&module=expenses&action=edit&id=<?= $expense['id'] ?>" 
                                               class="text-info-text hover:text-info-dark text-xs font-semibold">
                                                Sửa
                                            </a>
                                            <?php if ($status === 'pending'): ?>
                                                <a href="?act=admin&module=expenses&action=approve&id=<?= $expense['id'] ?>&schedule_id=<?= $schedule['id'] ?>&token=<?= csrf_token() ?>" 
                                                   onclick="return confirm('Bạn có chắc muốn duyệt chi phí này?')"
                                                   class="text-success-text hover:text-success-dark text-xs font-semibold">
                                                    Duyệt
                                                </a>
                                                <a href="#" 
                                                   onclick="showRejectModal(<?= $expense['id'] ?>); return false;"
                                                   class="text-danger-text hover:text-danger-dark text-xs font-semibold">
                                                    Từ chối
                                                </a>
                                            <?php endif; ?>
                                            <a href="?act=admin&module=expenses&action=delete&id=<?= $expense['id'] ?>&schedule_id=<?= $schedule['id'] ?>" 
                                               onclick="return confirm('Bạn có chắc muốn xóa chi phí này?')"
                                               class="text-danger-text hover:text-danger-dark text-xs font-semibold">
                                                Xóa
                                            </a>
                                        </div>
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

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-panel rounded-2xl p-6 max-w-md w-full border border-primary-100 shadow-lg">
        <h3 class="text-lg font-bold text-primary-700 mb-4">Từ chối chi phí</h3>
        <form id="rejectForm" method="POST" action="">
            <?= csrf_field() ?>
            <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-primary-700 mb-2">Lý do từ chối</label>
                <textarea name="rejection_reason" rows="3" 
                          class="w-full px-3 py-2 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none text-primary-700 text-sm"
                          placeholder="Nhập lý do từ chối..."></textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeRejectModal()" 
                        class="px-4 py-2 bg-primary-100 text-primary-700 rounded-xl hover:bg-primary-200 font-semibold transition-colors text-sm">
                    Hủy
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-danger-bg text-danger-text rounded-xl hover:opacity-90 font-semibold transition-colors text-sm">
                    Từ chối
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal(expenseId) {
    const form = document.getElementById('rejectForm');
    form.setAttribute('action', '?act=admin&module=expenses&action=reject&id=' + expenseId + '&schedule_id=<?= $schedule['id'] ?>&token=<?= csrf_token() ?>');
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>

