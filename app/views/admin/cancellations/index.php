<?php
/**
 * ADMIN - DANH SÁCH BOOKING ĐÃ HỦY
 */
if (!is_admin())
    redirect('?act=access-denied');

// Helper function
if (!function_exists('format_currency')) {
    function format_currency($amount)
    {
        return number_format($amount, 0, ',', '.') . ' đ';
    }
}

$statusColors = [
    'cancelled' => 'bg-danger-bg text-danger-text',
    'refunded' => 'bg-info-bg text-info-text'
];

$statusTexts = [
    'cancelled' => 'Đã hủy',
    'refunded' => 'Đã hoàn tiền'
];
?>

<div class="max-w-8xl mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý Hủy Booking</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Danh sách các booking đã được hủy</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <a href="?act=admin&module=cancellations&action=statistics"
                class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-500 hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="bar-chart" class="w-4 h-4"></i>
                Thống kê
            </a>
            <a href="?act=admin&module=bookings"
                class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại Bookings
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <?php if (!empty($stats)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4 lg:mb-6">
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm text-primary-500 font-semibold mb-1">Tổng số hủy</p>
                        <p class="text-xl lg:text-2xl font-bold text-primary-700">
                            <?= number_format($stats['total_cancelled'] ?? 0) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-danger-bg rounded-xl flex items-center justify-center">
                        <i data-lucide="x-circle" class="w-6 h-6 text-danger-text"></i>
                    </div>
                </div>
            </div>
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm text-primary-500 font-semibold mb-1">Tổng phí hủy</p>
                        <p class="text-xl lg:text-2xl font-bold text-primary-700">
                            <?= format_currency($stats['total_fee'] ?? 0) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-warning-bg rounded-xl flex items-center justify-center">
                        <i data-lucide="dollar-sign" class="w-6 h-6 text-warning-text"></i>
                    </div>
                </div>
            </div>
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm text-primary-500 font-semibold mb-1">Tổng hoàn lại</p>
                        <p class="text-xl lg:text-2xl font-bold text-primary-700">
                            <?= format_currency($stats['total_refund'] ?? 0) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-info-bg rounded-xl flex items-center justify-center">
                        <i data-lucide="arrow-left" class="w-6 h-6 text-info-text"></i>
                    </div>
                </div>
            </div>
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm text-primary-500 font-semibold mb-1">Đã hoàn tiền</p>
                        <p class="text-xl lg:text-2xl font-bold text-primary-700">
                            <?= number_format($stats['total_refunded_count'] ?? 0) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-success-bg rounded-xl flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-6 h-6 text-success-text"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Search & Filter - Responsive -->
    <form method="GET" class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100 mb-4 lg:mb-6">
        <input type="hidden" name="act" value="admin">
        <input type="hidden" name="module" value="cancellations">

        <div class="grid grid-cols-1 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-1">
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    placeholder="Mã booking, tên khách, SĐT..."
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
            </div>
            <div class="lg:col-span-1">
                <select name="status"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="cancelled" <?= ($_GET['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Đã hủy
                    </option>
                    <option value="refunded" <?= ($_GET['status'] ?? '') == 'refunded' ? 'selected' : '' ?>>Đã hoàn tiền
                    </option>
                </select>
            </div>
            <div class="lg:col-span-1">
                <select name="tour_id"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Tất cả Tour --</option>
                    <?php foreach ($tours as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($_GET['tour_id'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['tour_code'] . ' - ' . $t['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="lg:col-span-1">
                <select name="has_refund"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Tất cả --</option>
                    <option value="yes" <?= ($_GET['has_refund'] ?? '') == 'yes' ? 'selected' : '' ?>>Có hoàn tiền</option>
                    <option value="no" <?= ($_GET['has_refund'] ?? '') == 'no' ? 'selected' : '' ?>>Không hoàn tiền</option>
                </select>
            </div>
            <div class="lg:col-span-1">
                <select name="days_before"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Tất cả --</option>
                    <option value="0" <?= ($_GET['days_before'] ?? '') == '0' ? 'selected' : '' ?>>Hủy trong ngày (0 ngày)</option>
                    <option value="1" <?= ($_GET['days_before'] ?? '') == '1' ? 'selected' : '' ?>>Hủy trước 1 ngày</option>
                    <option value="2" <?= ($_GET['days_before'] ?? '') == '2' ? 'selected' : '' ?>>Hủy trước 2 ngày</option>
                    <option value="3" <?= ($_GET['days_before'] ?? '') == '3' ? 'selected' : '' ?>>Hủy trước 3 ngày</option>
                    <option value="7" <?= ($_GET['days_before'] ?? '') == '7' ? 'selected' : '' ?>>Hủy trước 7 ngày</option>
                    <option value="14" <?= ($_GET['days_before'] ?? '') == '14' ? 'selected' : '' ?>>Hủy trước 14 ngày</option>
                    <option value="30" <?= ($_GET['days_before'] ?? '') == '30' ? 'selected' : '' ?>>Hủy trước 30 ngày</option>
                </select>
            </div>
            <div>
                <button type="submit"
                    class="w-full px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base">
                    <i data-lucide="search" class="w-4 h-4 inline mr-2"></i>
                    Lọc
                </button>
            </div>
        </div>
    </form>

    <!-- Table - Responsive -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead>
                    <tr class="bg-primary-50">
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Mã Booking</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Khách hàng</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Tour</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Ngày khởi hành</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Ngày hủy</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Lý do hủy</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider text-right">
                            Phí hủy</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider text-right">
                            Hoàn lại</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider text-center">
                            Trạng thái</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider text-center">
                            Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-primary-500">
                                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 opacity-50"></i>
                                <p>Chưa có booking nào được hủy</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $b): ?>
                            <tr class="border-b border-primary-100 hover:bg-primary-50 transition-colors">
                                <td class="px-3 lg:px-4 py-3 lg:py-4">
                                    <div class="font-semibold text-primary-700 text-sm">
                                        <?= htmlspecialchars($b['booking_code']) ?></div>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4">
                                    <div class="font-semibold text-primary-700 text-sm">
                                        <?= htmlspecialchars($b['customer_name']) ?></div>
                                    <div class="text-xs text-primary-500"><?= htmlspecialchars($b['customer_phone']) ?></div>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4">
                                    <div class="font-semibold text-primary-700 text-sm"><?= htmlspecialchars($b['tour_name']) ?>
                                    </div>
                                    <div class="text-xs text-primary-500"><?= htmlspecialchars($b['tour_code']) ?></div>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4 text-sm text-primary-700">
                                    <?= date('d/m/Y', strtotime($b['start_date'])) ?>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4 text-sm text-primary-700">
                                    <?= $b['cancellation_date'] ? date('d/m/Y', strtotime($b['cancellation_date'])) : '-' ?>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4">
                                    <div class="text-sm text-primary-700 max-w-xs truncate"
                                        title="<?= htmlspecialchars($b['cancellation_reason'] ?? '') ?>">
                                        <?= htmlspecialchars($b['cancellation_reason'] ?? '-') ?>
                                    </div>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4 text-right text-sm font-semibold text-danger-text">
                                    <?= format_currency($b['cancellation_fee'] ?? 0) ?>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4 text-right text-sm font-semibold text-info-text">
                                    <?= format_currency($b['refund_amount'] ?? 0) ?>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4 text-center">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $statusColors[$b['payment_status']] ?? 'bg-primary-100 text-primary-500' ?>">
                                        <?= $statusTexts[$b['payment_status']] ?? $b['payment_status'] ?>
                                    </span>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4 text-center">
                                    <a href="?act=admin&module=cancellations&action=show&id=<?= $b['id'] ?>"
                                        class="text-accent hover:text-accent-hover font-semibold text-sm flex items-center justify-center gap-1">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                        Chi tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div
                class="px-4 lg:px-6 py-4 border-t border-primary-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-primary-500">
                    Hiển thị <?= count($bookings) ?> / <?= $total_records ?> kết quả
                </div>
                <div class="flex gap-2">
                    <?php if ($page > 1): ?>
                        <a href="?act=admin&module=cancellations&page=<?= $page - 1 ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['status']) ? '&status=' . $_GET['status'] : '' ?>"
                            class="px-4 py-2 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold text-sm">
                            Trước
                        </a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?act=admin&module=cancellations&page=<?= $i ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['status']) ? '&status=' . $_GET['status'] : '' ?>"
                            class="px-4 py-2 <?= $i == $page ? 'bg-accent text-white' : 'bg-panel border border-primary-100 text-primary-700 hover:bg-primary-50' ?> rounded-xl font-semibold text-sm">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?act=admin&module=cancellations&page=<?= $page + 1 ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['status']) ? '&status=' . $_GET['status'] : '' ?>"
                            class="px-4 py-2 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold text-sm">
                            Sau
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>