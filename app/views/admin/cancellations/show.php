<?php
/**
 * ADMIN - CHI TIẾT BOOKING ĐÃ HỦY
 */
if (!is_admin())
    redirect('?act=access-denied');

// Helper to format currency
if (!function_exists('format_currency')) {
    function format_currency($amount)
    {
        return number_format($amount, 0, ',', '.') . ' đ';
    }
}

// Status Colors
$statusColors = [
    'cancelled' => 'bg-danger-bg text-danger-text',
    'refunded' => 'bg-info-bg text-info-text'
];

// Status Text
$statusTexts = [
    'cancelled' => 'Đã hủy',
    'refunded' => 'Đã hoàn tiền'
];
?>

<div class="max-w-[95%] mx-auto">
    <!-- HEADER - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <div class="flex flex-wrap items-center gap-2 lg:gap-3 mb-2">
                <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Booking Hủy: <?= $booking['booking_code'] ?>
                </h1>
                <span
                    class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $statusColors[$booking['payment_status']] ?? 'bg-primary-100 text-primary-500' ?>">
                    <?= $statusTexts[$booking['payment_status']] ?? $booking['payment_status'] ?>
                </span>
            </div>
            <p class="text-xs lg:text-sm text-primary-500">
                Hủy ngày
                <?= $booking['cancellation_date'] ? date('d/m/Y H:i', strtotime($booking['cancellation_date'])) : 'N/A' ?>
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <a href="?act=admin&module=cancellations"
                class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
            <a href="?act=admin&module=bookings&action=show&id=<?= $booking['id'] ?>"
                class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-500 hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base text-center flex items-center justify-center gap-2">
                <i data-lucide="eye" class="w-4 h-4"></i>
                Xem Booking gốc
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- LEFT COLUMN: INFO -->
        <div class="lg:col-span-2 space-y-4 lg:space-y-6">
            <!-- 1. TOUR INFO -->
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100">
                <h2
                    class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-4 lg:mb-5">
                    Thông tin Tour</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs lg:text-sm text-primary-500 font-semibold mb-1">Tour Code</p>
                        <p class="font-semibold text-primary-700 text-sm lg:text-base"><?= $booking['tour_code'] ?></p>
                    </div>
                    <div>
                        <p class="text-xs lg:text-sm text-primary-500 font-semibold mb-1">Tên Tour</p>
                        <p class="font-semibold text-accent text-sm lg:text-base"><?= $booking['tour_name'] ?></p>
                    </div>
                    <div>
                        <p class="text-xs lg:text-sm text-primary-500 font-semibold mb-1">Khởi hành</p>
                        <p class="font-semibold text-primary-700 text-sm lg:text-base">
                            <?= date('d/m/Y', strtotime($booking['start_date'])) ?></p>
                    </div>
                    <div>
                        <p class="text-xs lg:text-sm text-primary-500 font-semibold mb-1">Kết thúc</p>
                        <p class="font-semibold text-primary-700 text-sm lg:text-base">
                            <?= date('d/m/Y', strtotime($booking['end_date'])) ?></p>
                    </div>
                    <div>
                        <p class="text-xs lg:text-sm text-primary-500 font-semibold mb-1">Thời lượng</p>
                        <p class="font-semibold text-primary-700 text-sm lg:text-base"><?= $booking['duration_days'] ?>N
                            <?= $booking['duration_nights'] ?>Đ</p>
                    </div>
                </div>
            </div>

            <!-- 2. CANCELLATION INFO -->
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border-t-4 border-danger">
                <h2
                    class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-4 lg:mb-5">
                    Thông tin Hủy</h2>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs lg:text-sm text-primary-500 font-semibold mb-1">Ngày hủy</p>
                            <p class="font-semibold text-primary-700 text-sm lg:text-base">
                                <?= $booking['cancellation_date'] ? date('d/m/Y H:i', strtotime($booking['cancellation_date'])) : 'N/A' ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs lg:text-sm text-primary-500 font-semibold mb-1">Số ngày trước khởi hành
                            </p>
                            <p class="font-semibold text-primary-700 text-sm lg:text-base">
                                <?= $daysBeforeDeparture !== null ? $daysBeforeDeparture . ' ngày' : 'N/A' ?>
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs lg:text-sm text-primary-500 font-semibold mb-1">Lý do hủy</p>
                        <div class="bg-primary-50 p-3 lg:p-4 rounded-xl border border-primary-100">
                            <p class="text-sm lg:text-base text-primary-700">
                                <?= nl2br(htmlspecialchars($booking['cancellation_reason'] ?? 'Không có')) ?></p>
                        </div>
                    </div>

                    <?php if ($policy): ?>
                        <div class="bg-info-bg p-3 lg:p-4 rounded-xl border border-info">
                            <p class="text-xs lg:text-sm text-info-text font-semibold mb-1">Policy áp dụng</p>
                            <p class="font-bold text-info-text text-sm lg:text-base">
                                <?= htmlspecialchars($policy['name'] ?? 'N/A') ?></p>
                            <?php if (!empty($policy['description'])): ?>
                                <p class="text-xs text-info-text mt-1"><?= htmlspecialchars($policy['description']) ?></p>
                            <?php endif; ?>
                            <p class="text-xs text-info-text mt-1">Phí hủy:
                                <?= number_format($policy['fee_percentage'], 2) ?>%</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 3. CUSTOMER & PASSENGERS -->
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100">
                <h2
                    class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-4 lg:mb-5">
                    Khách hàng & Hành khách</h2>

                <div class="mb-4 bg-info-bg p-4 lg:p-5 rounded-2xl border border-info">
                    <p class="font-bold text-info-text text-sm lg:text-base"><?= $booking['customer_name'] ?></p>
                    <p class="text-xs lg:text-sm text-info-text mt-1"><?= $booking['customer_phone'] ?> |
                        <?= $booking['customer_email'] ?></p>
                    <p class="text-xs lg:text-sm text-primary-500 mt-1"><?= $booking['customer_address'] ?></p>
                </div>

                <div class="mb-2 lg:mb-3">
                    <h3 class="font-bold text-xs lg:text-sm text-primary-700">Danh sách đoàn
                        (<?= count($booking['passengers'] ?? []) ?> người)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse min-w-[600px]">
                        <thead class="bg-primary-50">
                            <tr>
                                <th
                                    class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                                    Họ tên</th>
                                <th
                                    class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                                    Loại</th>
                                <th
                                    class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                                    Vai trò</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($booking['passengers'] ?? [] as $p): ?>
                                <tr class="border-b border-primary-100 hover:bg-primary-50 transition-colors">
                                    <td class="px-3 lg:px-4 py-2 lg:py-3">
                                        <div class="font-semibold text-primary-700 text-sm">
                                            <?= htmlspecialchars($p['full_name'] ?? 'Khách hàng #' . $p['customer_id']) ?>
                                        </div>
                                        <?php if (!empty($p['phone'])): ?>
                                            <div class="text-xs text-primary-500"><?= htmlspecialchars($p['phone']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3 capitalize text-primary-700 text-sm">
                                        <?php
                                        $ageTypeLabels = ['adult' => 'Người lớn', 'child' => 'Trẻ em', 'infant' => 'Em bé'];
                                        echo $ageTypeLabels[$p['age_type']] ?? $p['age_type'];
                                        ?>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3">
                                        <?php if ($p['is_primary']): ?>
                                            <span
                                                class="text-xs bg-info-bg text-info-text px-3 py-1 rounded-full font-bold">Người
                                                đặt</span>
                                        <?php else: ?>
                                            <span class="text-xs text-primary-500">Hành khách</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: FINANCIALS -->
        <div class="lg:col-span-1 space-y-6">
            <!-- FINANCIAL SUMMARY -->
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border-t-4 border-accent">
                <h2
                    class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-4 lg:mb-5">
                    Thanh toán</h2>

                <div class="space-y-3 mb-4 lg:mb-6">
                    <div class="flex justify-between">
                        <span class="text-xs lg:text-sm text-primary-500">Tổng tiền tour</span>
                        <span
                            class="font-semibold text-primary-700 text-sm lg:text-base"><?= format_currency($booking['total_amount']) ?></span>
                    </div>
                    <div class="flex justify-between text-success-text">
                        <span class="text-xs lg:text-sm text-primary-500">Giảm giá</span>
                        <span
                            class="font-semibold text-sm lg:text-base">-<?= format_currency($booking['discount_amount']) ?></span>
                    </div>
                    <div
                        class="flex justify-between text-lg lg:text-xl font-bold text-primary-700 border-t border-primary-100 pt-2">
                        <span>Thành tiền</span>
                        <span><?= format_currency($booking['final_amount']) ?></span>
                    </div>

                    <div class="bg-primary-50 p-3 lg:p-4 rounded-2xl border border-primary-100">
                        <div class="flex justify-between mb-1">
                            <span class="text-xs lg:text-sm text-primary-500">Đã thanh toán</span>
                            <span
                                class="font-bold text-success-text text-sm lg:text-base"><?= format_currency($booking['paid_amount']) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CANCELLATION FEES -->
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border-t-4 border-danger">
                <h2
                    class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-4 lg:mb-5">
                    Phí Hủy & Hoàn Tiền</h2>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-xs lg:text-sm text-primary-500">Phí hủy</span>
                        <span
                            class="font-bold text-danger-text text-sm lg:text-base"><?= format_currency($booking['cancellation_fee'] ?? 0) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-xs lg:text-sm text-primary-500">Số tiền hoàn lại</span>
                        <span
                            class="font-bold text-info-text text-sm lg:text-base"><?= format_currency($booking['refund_amount'] ?? 0) ?></span>
                    </div>
                    <div class="flex justify-between border-t border-primary-100 pt-2">
                        <span class="text-xs lg:text-sm text-primary-500">Còn lại sau hủy</span>
                        <span class="font-bold text-primary-700 text-sm lg:text-base">
                            <?= format_currency(max(0, $booking['paid_amount'] - ($booking['cancellation_fee'] ?? 0))) ?>
                        </span>
                    </div>

                    <?php if ($booking['refund_amount'] > 0 && !$hasRefundProcessed): ?>
                        <div class="mt-4 pt-4 border-t border-primary-100">
                            <button onclick="openModal('refundModal')"
                                class="w-full py-2 lg:py-2.5 bg-gradient-to-r from-info-gradient-from to-info-gradient-to hover:opacity-90 text-white font-bold rounded-xl shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                                <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                                Xử lý hoàn tiền
                            </button>
                        </div>
                    <?php elseif ($hasRefundProcessed): ?>
                        <div class="mt-4 pt-4 border-t border-primary-100">
                            <div class="bg-success-bg p-3 rounded-xl border border-success">
                                <p class="text-xs lg:text-sm text-success-text font-semibold flex items-center gap-2">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    Đã xử lý hoàn tiền
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- PAYMENT LIST -->
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Lịch sử giao dịch</h2>
                <?php if (empty($payments)): ?>
                    <p class="text-sm text-gray-500 italic">Chưa có giao dịch nào.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($payments as $pay): ?>
                            <div class="border-b pb-2 last:border-0">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-gray-800"><?= format_currency($pay['amount']) ?></span>
                                    <span
                                        class="text-xs bg-<?= $pay['payment_type'] === 'refund' ? 'info' : 'success' ?>-bg text-<?= $pay['payment_type'] === 'refund' ? 'info' : 'success' ?>-text px-2 py-1 rounded capitalize">
                                        <?= $pay['payment_type'] === 'refund' ? 'Hoàn tiền' : $pay['payment_method'] ?>
                                    </span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span><?= date('d/m/Y', strtotime($pay['payment_date'])) ?></span>
                                    <span><?= $pay['creator_name'] ?? 'N/A' ?></span>
                                </div>
                                <?php if ($pay['notes']): ?>
                                    <p class="text-xs text-gray-400 mt-1 italic"><?= htmlspecialchars($pay['notes']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- REFUND MODAL -->
<?php if ($booking['refund_amount'] > 0 && !$hasRefundProcessed): ?>
    <div id="refundModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-md p-6">
            <h3 class="text-lg font-bold mb-4 text-info-text">Xử lý Hoàn Tiền</h3>
            <form action="?act=admin&module=cancellations&action=processRefund" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">

                <div class="space-y-4">
                    <div class="bg-info-bg p-3 rounded border border-info">
                        <p class="text-xs text-info-text mb-1">Số tiền hoàn lại tính toán:</p>
                        <p class="font-bold text-info-text text-lg"><?= format_currency($booking['refund_amount']) ?></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Số tiền hoàn lại (VNĐ) <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="amount" value="<?= $booking['refund_amount'] ?>"
                            max="<?= $booking['refund_amount'] ?>" min="0" step="1000"
                            class="w-full border rounded px-3 py-2" required>
                        <p class="text-xs text-gray-500 mt-1">Tối đa: <?= format_currency($booking['refund_amount']) ?></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Phương thức hoàn tiền</label>
                        <select name="payment_method" class="w-full border rounded px-3 py-2">
                            <option value="bank_transfer">Chuyển khoản</option>
                            <option value="cash">Tiền mặt</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Ngày hoàn tiền</label>
                        <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>"
                            class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Ghi chú</label>
                        <textarea name="notes" rows="3" class="w-full border rounded px-3 py-2"
                            placeholder="Ghi chú về việc hoàn tiền...">Hoàn tiền hủy booking</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeModal('refundModal')"
                        class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-info text-white rounded hover:bg-info-hover">
                        Xác nhận hoàn tiền
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
    </script>
<?php endif; ?>