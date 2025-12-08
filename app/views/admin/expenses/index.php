<?php
/**
 * ADMIN - DANH SÁCH CHI PHÍ PHÁT SINH
 * Variables: $schedules, $total_pages, $current_page
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<script>
    // Định nghĩa các hàm ngay từ đầu để tránh lỗi "not defined"
    window.toggleExpenses = function (scheduleId) {
        try {
            const element = document.getElementById('expenses-' + scheduleId);
            const icon = document.getElementById('icon-' + scheduleId);

            if (!element) {
                console.error('Element expenses-' + scheduleId + ' not found');
                return false;
            }

            if (!icon) {
                console.error('Icon icon-' + scheduleId + ' not found');
                return false;
            }

            if (element.classList.contains('hidden')) {
                element.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
                icon.style.transition = 'transform 0.3s ease';
            } else {
                element.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
                icon.style.transition = 'transform 0.3s ease';
            }
            return true;
        } catch (e) {
            console.error('Error in toggleExpenses:', e);
            return false;
        }
    };

    window.showRejectModal = function (expenseId, scheduleId) {
        try {
            const form = document.getElementById('rejectForm');
            const expenseIdInput = document.getElementById('rejectExpenseId');

            if (!form) {
                console.error('Reject form not found');
                return false;
            }

            // Tạo input hidden cho expense_id nếu chưa có
            if (!expenseIdInput) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'expense_id';
                hiddenInput.id = 'rejectExpenseId';
                hiddenInput.value = expenseId;
                form.appendChild(hiddenInput);
            } else {
                expenseIdInput.value = expenseId;
            }

            form.setAttribute('action', '?act=admin&module=expenses&action=reject&id=' + expenseId + '&token=<?= generate_csrf_token() ?>');
            const modal = document.getElementById('rejectModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
            return true;
        } catch (e) {
            console.error('Error in showRejectModal:', e);
            return false;
        }
    };

    window.closeRejectModal = function () {
        try {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');

            if (modal) {
                modal.classList.add('hidden');
            }
            if (form) {
                form.reset();
            }
            return true;
        } catch (e) {
            console.error('Error in closeRejectModal:', e);
            return false;
        }
    };

    window.showRejectAllModal = function (scheduleId, count) {
        try {
            const scheduleIdInput = document.getElementById('rejectAllScheduleId');
            const countSpan = document.getElementById('rejectAllCount');
            const form = document.getElementById('rejectAllForm');

            if (!scheduleIdInput || !countSpan || !form) {
                console.error('Reject all modal elements not found');
                return false;
            }

            scheduleIdInput.value = scheduleId;
            countSpan.textContent = count;
            form.setAttribute('action', '?act=admin&module=expenses&action=reject-all&schedule_id=' + scheduleId + '&token=<?= generate_csrf_token() ?>');
            const modal = document.getElementById('rejectAllModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
            return true;
        } catch (e) {
            console.error('Error in showRejectAllModal:', e);
            return false;
        }
    };

    window.closeRejectAllModal = function () {
        try {
            const modal = document.getElementById('rejectAllModal');
            const form = document.getElementById('rejectAllForm');

            if (modal) {
                modal.classList.add('hidden');
            }
            if (form) {
                form.reset();
            }
            return true;
        } catch (e) {
            console.error('Error in closeRejectAllModal:', e);
            return false;
        }
    };
</script>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-8">
    <!-- Page Header -->
    <div class="mb-4 lg:mb-6">
        <div class="flex items-center gap-2 text-xs lg:text-sm text-primary-500 mb-2">
            <a href="?act=admin" class="hover:text-accent">Trang chủ</a>
            <span>›</span>
            <span>Chi phí phát sinh</span>
        </div>
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý Chi phí phát sinh</h1>
    </div>

    <!-- Statistics Cards -->
    <?php if (isset($expense_stats)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Expenses -->
            <div class="bg-panel rounded-xl border border-primary-100 shadow-sm p-4 lg:p-5">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs lg:text-sm font-semibold text-primary-600 uppercase">Tổng chi phí</h3>
                    <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <div class="text-xl lg:text-2xl font-bold text-primary-700 mb-1">
                    <?= number_format($expense_stats['total']['count'], 0, ',', '.') ?>
                </div>
                <div class="text-xs lg:text-sm text-primary-500">
                    <?= number_format($expense_stats['total']['total'], 0, ',', '.') ?> VNĐ
                </div>
            </div>

            <!-- Approved Expenses -->
            <div class="bg-panel rounded-xl border border-green-200 shadow-sm p-4 lg:p-5">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs lg:text-sm font-semibold text-green-700 uppercase">Đã duyệt</h3>
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-xl lg:text-2xl font-bold text-green-700 mb-1">
                    <?= number_format($expense_stats['approved']['count'], 0, ',', '.') ?>
                </div>
                <div class="text-xs lg:text-sm text-green-600">
                    <?= number_format($expense_stats['approved']['total'], 0, ',', '.') ?> VNĐ
                </div>
            </div>

            <!-- Pending Expenses -->
            <div class="bg-panel rounded-xl border border-yellow-200 shadow-sm p-4 lg:p-5">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs lg:text-sm font-semibold text-yellow-700 uppercase">Chờ duyệt</h3>
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-xl lg:text-2xl font-bold text-yellow-700 mb-1">
                    <?= number_format($expense_stats['pending']['count'], 0, ',', '.') ?>
                </div>
                <div class="text-xs lg:text-sm text-yellow-600">
                    <?= number_format($expense_stats['pending']['total'], 0, ',', '.') ?> VNĐ
                </div>
            </div>

            <!-- Rejected Expenses -->
            <div class="bg-panel rounded-xl border border-red-200 shadow-sm p-4 lg:p-5">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs lg:text-sm font-semibold text-red-700 uppercase">Đã từ chối</h3>
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-xl lg:text-2xl font-bold text-red-700 mb-1">
                    <?= number_format($expense_stats['rejected']['count'], 0, ',', '.') ?>
                </div>
                <div class="text-xs lg:text-sm text-red-600">
                    <?= number_format($expense_stats['rejected']['total'], 0, ',', '.') ?> VNĐ
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Schedules List -->
    <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm overflow-hidden">
        <div class="p-4 lg:p-6 border-b border-primary-100">
            <h2 class="text-base lg:text-lg font-bold text-primary-700">Danh sách Lịch Tour & Chi phí</h2>
        </div>

        <?php if (empty($schedules)):  ?>
            <div class="p-8 text-center text-primary-500">
                <p>Chưa có lịch tour nào.</p>
            </div>
        <?php else: ?>
          
            <div class="overflow-x-auto">
                <?php foreach ($schedules as $schedule): ?>
                    <?php $expenses = $schedule['expenses'] ?? []; ?>
                    <div class="border-b border-primary-100 last:border-b-0">
                        <!-- Schedule Header Row -->
                        <div class="p-4 hover:bg-primary-50 transition-colors">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                                <!-- Tour Info -->
                                <div class="md:col-span-3">
                                    <div class="font-semibold text-primary-700 text-sm mb-1">
                                        <?= htmlspecialchars($schedule['tour_name'] ?? 'N/A') ?>
                                    </div>
                                    <div class="text-xs text-primary-500 font-mono mb-1">
                                        <?= htmlspecialchars($schedule['tour_code'] ?? '') ?>
                                    </div>
                                    <?php if (!empty($schedule['guide_name'])): ?>
                                        <div class="text-xs text-primary-600 mt-1">
                                            <span class="font-semibold">HDV:</span> <?= htmlspecialchars($schedule['guide_name']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Date & Status -->
                                <div class="md:col-span-2">
                                    <div class="text-primary-600 text-sm mb-1">
                                        <span class="font-semibold">Ngày đi:</span><br>
                                        <?= date('d/m/Y', strtotime($schedule['start_date'])) ?>
                                    </div>
                                    <?php if (!empty($schedule['end_date'])): ?>
                                        <div class="text-primary-600 text-xs">
                                            <span class="font-semibold">Ngày về:</span><br>
                                            <?= date('d/m/Y', strtotime($schedule['end_date'])) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php
                                    $schedule_status = $schedule['status'] ?? 'open';
                                    $status_colors = [
                                        'open' => 'bg-blue-100 text-blue-700',
                                        'closed' => 'bg-gray-100 text-gray-700',
                                        'completed' => 'bg-green-100 text-green-700',
                                        'cancelled' => 'bg-red-100 text-red-700'
                                    ];
                                    $status_labels = [
                                        'open' => 'Mở',
                                        'closed' => 'Đóng',
                                        'completed' => 'Hoàn thành',
                                        'cancelled' => 'Hủy'
                                    ];
                                    ?>
                                    <div class="mt-1">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $status_colors[$schedule_status] ?? 'bg-primary-100 text-primary-700' ?>">
                                            <?= $status_labels[$schedule_status] ?? $schedule_status ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Expense Summary -->
                                <div class="md:col-span-2">
                                    <div class="text-primary-600 text-sm mb-1">
                                        <span class="font-semibold"><?= $schedule['expense_count'] ?? 0 ?></span> chi phí
                                    </div>
                                    <div class="text-xs text-primary-500">
                                        <?php
                                        $approved_count = $schedule['expense_approved_count'] ?? 0;
                                        $pending_count = $schedule['expense_pending_count'] ?? 0;
                                        $rejected_count = $schedule['expense_rejected_count'] ?? 0;
                                        ?>
                                        <div>✓ Đã duyệt: <span class="font-semibold text-green-600"><?= $approved_count ?></span></div>
                                        <div>⏳ Chờ: <span class="font-semibold text-yellow-600"><?= $pending_count ?></span></div>
                                        <?php if ($rejected_count > 0): ?>
                                            <div>✗ Từ chối: <span class="font-semibold text-red-600"><?= $rejected_count ?></span></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Total Amount -->
                                <div class="md:col-span-2">
                                    <div class="text-right">
                                        <div class="text-xs text-primary-500 mb-1">Tổng chi phí</div>
                                        <div class="font-bold text-primary-700 text-sm">
                                            <?= number_format($schedule['expense_total'] ?? 0, 0, ',', '.') ?> VNĐ
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="md:col-span-3 flex items-center justify-end gap-2">
                                    <?php
                                    $pending_expenses = $schedule['pending_expenses'] ?? [];
                                    $has_pending = count($pending_expenses) > 0;
                                    ?>
                                    <?php if ($has_pending): ?>
                                        <a href="?act=admin&module=expenses&action=approve-all&schedule_id=<?= $schedule['id'] ?>&token=<?= generate_csrf_token() ?>"
                                            onclick="return confirm('Bạn có chắc muốn duyệt tất cả <?= count($pending_expenses) ?> chi phí đang chờ duyệt?')"
                                            class="p-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors"
                                            title="Duyệt tất cả (<?= count($pending_expenses) ?>)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </a>
                                        <button onclick="showRejectAllModal(<?= $schedule['id'] ?>, <?= count($pending_expenses) ?>)"
                                            class="p-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors"
                                            title="Từ chối tất cả">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Expenses List (Always Visible) -->
                        <div id="expenses-<?= $schedule['id'] ?>" class="bg-primary-50">
                            <?php if (empty($expenses)): ?>
                                <div class="p-4 text-center text-primary-500 text-sm">
                                    Chưa có chi phí nào được ghi nhận.
                                </div>
                            <?php else: ?>
                                <div class="p-4">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="bg-primary-100 border-b border-primary-200">
                                                <th class="px-3 py-2 text-left text-xs font-semibold text-primary-600 uppercase">
                                                    Ngày</th>
                                                <th class="px-3 py-2 text-left text-xs font-semibold text-primary-600 uppercase">
                                                    Booking</th>
                                                <th class="px-3 py-2 text-left text-xs font-semibold text-primary-600 uppercase">
                                                    Loại</th>
                                                <th class="px-3 py-2 text-left text-xs font-semibold text-primary-600 uppercase">Mô
                                                    tả</th>
                                                <th class="px-3 py-2 text-right text-xs font-semibold text-primary-600 uppercase">Số
                                                    tiền</th>
                                                <th class="px-3 py-2 text-left text-xs font-semibold text-primary-600 uppercase">
                                                    Người ghi</th>
                                                <th class="px-3 py-2 text-center text-xs font-semibold text-primary-600 uppercase">
                                                    Trạng thái</th>
                                                <th class="px-3 py-2 text-center text-xs font-semibold text-primary-600 uppercase">
                                                    Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-primary-200">
                                            <?php foreach ($expenses as $expense): ?>
                                                <?php
                                                $status = $expense['approval_status'] ?? 'pending';
                                                $status_colors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                                    'approved' => 'bg-green-100 text-green-700',
                                                    'rejected' => 'bg-red-100 text-red-700'
                                                ];
                                                $status_labels = [
                                                    'pending' => 'Chờ duyệt',
                                                    'approved' => 'Đã duyệt',
                                                    'rejected' => 'Từ chối'
                                                ];
                                                ?>
                                                <tr class="hover:bg-white transition-colors">
                                                    <td class="px-3 py-2 text-primary-600 text-xs">
                                                        <?= date('d/m/Y', strtotime($expense['expense_date'])) ?>
                                                    </td>
                                                    <td class="px-3 py-2 text-primary-600 text-xs font-mono">
                                                        <?= htmlspecialchars($expense['booking_code'] ?? '-') ?>
                                                    </td>
                                                    <td class="px-3 py-2 text-primary-600 text-xs">
                                                        <?= htmlspecialchars($expense['category'] ?? '-') ?>
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <div class="text-primary-700 text-xs">
                                                            <?= htmlspecialchars($expense['description']) ?></div>
                                                        <?php if (!empty($expense['notes'])): ?>
                                                            <div class="text-xs text-primary-500 mt-1">
                                                                <?= htmlspecialchars($expense['notes']) ?></div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="px-3 py-2 text-right">
                                                        <div class="font-bold text-primary-700 text-xs">
                                                            <?= number_format($expense['amount'], 0, ',', '.') ?> VNĐ</div>
                                                    </td>
                                                    <td class="px-3 py-2 text-primary-600 text-xs">
                                                        <?= htmlspecialchars($expense['reported_by_name'] ?? '-') ?>
                                                    </td>
                                                    <td class="px-3 py-2 text-center">
                                                        <span
                                                            class="px-2 py-1 rounded-full text-xs font-semibold <?= $status_colors[$status] ?? 'bg-primary-100 text-primary-700' ?>">
                                                            <?= $status_labels[$status] ?? $status ?>
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <div class="flex items-center justify-center gap-1">
                                                            <?php if (!empty($expense['receipt_file'])): ?>
                                                                <a href="<?= (defined('BASE_URL') ? BASE_URL : '') ?>/public/<?= $expense['receipt_file'] ?>"
                                                                    target="_blank"
                                                                    class="p-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded transition-colors"
                                                                    title="Xem hóa đơn">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                                        </path>
                                                                    </svg>
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if ($status === 'pending'): ?>
                                                                <a href="?act=admin&module=expenses&action=approve&id=<?= $expense['id'] ?>&schedule_id=<?= $schedule['id'] ?>&token=<?= generate_csrf_token() ?>"
                                                                    onclick="return confirm('Bạn có chắc muốn duyệt chi phí này?')"
                                                                    class="p-1.5 bg-green-500 hover:bg-green-600 text-white rounded transition-colors"
                                                                    title="Duyệt">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                    </svg>
                                                                </a>
                                                                <button
                                                                    onclick="showRejectModal(<?= $expense['id'] ?>, <?= $schedule['id'] ?>)"
                                                                    class="p-1.5 bg-red-500 hover:bg-red-600 text-white rounded transition-colors"
                                                                    title="Từ chối">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="p-4 lg:p-6 border-t border-primary-100 flex justify-center gap-2">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?act=admin&module=expenses&page=<?= $i ?>"
                            class="px-3 py-1 rounded-lg text-sm font-semibold transition-colors <?= $i == $current_page ? 'bg-accent text-white' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Reject Single Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-panel rounded-2xl p-6 max-w-md w-full border border-primary-100 shadow-lg">
        <h3 class="text-lg font-bold text-primary-700 mb-4">Từ chối chi phí</h3>
        <form id="rejectForm" method="POST" action="">
            <?= csrf_field() ?>
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
                    class="px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 font-semibold transition-colors text-sm">
                    Từ chối
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject All Modal -->
<div id="rejectAllModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-panel rounded-2xl p-6 max-w-md w-full border border-primary-100 shadow-lg">
        <h3 class="text-lg font-bold text-primary-700 mb-4">Từ chối tất cả chi phí chờ duyệt</h3>
        <form id="rejectAllForm" method="POST" action="">
            <?= csrf_field() ?>
            <input type="hidden" name="schedule_id" id="rejectAllScheduleId">
            <div class="mb-4">
                <p class="text-sm text-primary-600 mb-3">
                    Bạn có chắc muốn từ chối tất cả <span id="rejectAllCount" class="font-bold"></span> chi phí đang chờ
                    duyệt?
                </p>
                <label class="block text-sm font-semibold text-primary-700 mb-2">Lý do từ chối</label>
                <textarea name="rejection_reason" rows="3"
                    class="w-full px-3 py-2 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none text-primary-700 text-sm"
                    placeholder="Nhập lý do từ chối..."></textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeRejectAllModal()"
                    class="px-4 py-2 bg-primary-100 text-primary-700 rounded-xl hover:bg-primary-200 font-semibold transition-colors text-sm">
                    Hủy
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 font-semibold transition-colors text-sm">
                    Từ chối tất cả
                </button>
            </div>
        </form>
    </div>
</div>