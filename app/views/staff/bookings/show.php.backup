<?php
/**
 * STAFF - CHI TIẾT BOOKING
 */
require_staff_or_admin();

// Helper to format currency
if (!function_exists('format_currency')) {
    function format_currency($amount)
    {
        return number_format($amount, 0, ',', '.') . ' đ';
    }
}

// Status Colors
$statusColors = [
    'pending' => 'bg-yellow-100 text-yellow-800',
    'approved' => 'bg-blue-100 text-blue-800',
    'rejected' => 'bg-slate-100 text-slate-800',
    'cancelled' => 'bg-red-100 text-red-800',
    'unpaid' => 'bg-red-100 text-red-800',
    'partial' => 'bg-yellow-100 text-yellow-800',
    'paid' => 'bg-green-100 text-green-800',
    'refunded' => 'bg-purple-100 text-purple-800'
];
?>

<div class="max-w-[95%] mx-auto">
    <!-- HEADER -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h1 class="text-2xl font-bold text-primary">Booking: <?= htmlspecialchars($booking['booking_code']) ?>
                </h1>
                <span
                    class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $statusColors[$booking['approval_status']] ?? 'bg-slate-100' ?>">
                    <?= $booking['approval_status'] ?>
                </span>
                <span
                    class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $statusColors[$booking['payment_status']] ?? 'bg-slate-100' ?>">
                    <?= $booking['payment_status'] ?>
                </span>
            </div>
            <p class="text-slate-500 text-sm">Tạo ngày <?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?> bởi
                <?= htmlspecialchars($booking['creator_name'] ?? 'N/A') ?>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="?act=staff-bookings"
                class="px-4 py-2 bg-slate-200 text-slate-700 rounded hover:bg-slate-300 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
            <!-- Staff cannot change status, only view -->
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT COLUMN: INFO -->
        <div class="lg:col-span-2 space-y-6">

            <!-- 1. TOUR INFO -->
            <div class="bg-white p-6 rounded shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">Thông tin Tour</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-slate-500">Tour Code</p>
                        <p class="font-medium"><?= htmlspecialchars($booking['tour_code']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Tên Tour</p>
                        <p class="font-medium text-blue-600"><?= htmlspecialchars($booking['tour_name']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Khởi hành</p>
                        <p class="font-medium"><?= date('d/m/Y', strtotime($booking['start_date'])) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Kết thúc</p>
                        <p class="font-medium"><?= date('d/m/Y', strtotime($booking['end_date'])) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Thời lượng</p>
                        <p class="font-medium"><?= $booking['duration_days'] ?>N <?= $booking['duration_nights'] ?>Đ</p>
                    </div>
                </div>
            </div>

            <!-- 2. CUSTOMER & PASSENGERS -->
            <div class="bg-white p-6 rounded shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">Khách hàng & Hành khách</h2>

                <div class="mb-4 bg-blue-50 p-4 rounded border border-blue-100">
                    <p class="font-bold text-blue-800"><?= htmlspecialchars($booking['customer_name']) ?></p>
                    <p class="text-sm text-blue-600"><?= htmlspecialchars($booking['customer_phone']) ?> |
                        <?= htmlspecialchars($booking['customer_email']) ?>
                    </p>
                    <p class="text-sm text-slate-600"><?= htmlspecialchars($booking['customer_address']) ?></p>
                </div>

                <h3 class="font-bold text-sm text-slate-700 mb-2">Danh sách đoàn
                    (<?= count($booking['passengers'] ?? []) ?> người)</h3>
                <table class="w-full text-sm text-left text-slate-500 border border-slate-200 rounded overflow-hidden">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2">Họ tên</th>
                            <th class="px-3 py-2">Loại</th>
                            <th class="px-3 py-2">Vai trò</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($booking['passengers'])): ?>
                            <?php foreach ($booking['passengers'] as $p): ?>
                                <tr class="border-b last:border-0 border-slate-100">
                                    <td class="px-3 py-2 font-medium text-slate-900">
                                        <?= htmlspecialchars($p['full_name'] ?? 'Khách hàng #' . $p['customer_id']) ?>
                                    </td>
                                    <td class="px-3 py-2 capitalize"><?= $p['age_type'] ?></td>
                                    <td class="px-3 py-2">
                                        <?php if ($p['is_primary']): ?>
                                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Người đặt</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="px-3 py-2 text-center italic">Không có thông tin hành khách</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- 3. HISTORY LOG -->
            <div class="bg-white p-6 rounded shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">Lịch sử hoạt động</h2>
                <div class="relative border-l-2 border-slate-200 ml-3 space-y-6">
                    <?php if (!empty($history)): ?>
                        <?php foreach ($history as $log): ?>
                            <div class="mb-4 ml-6 relative">
                                <span
                                    class="absolute -left-[33px] flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full ring-4 ring-white">
                                    <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                </span>
                                <h3 class="flex items-center mb-1 text-sm font-semibold text-slate-900">
                                    <?= htmlspecialchars($log['user_name'] ?? 'Hệ thống') ?>
                                    <span
                                        class="bg-slate-100 text-slate-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded ml-2">
                                        <?= $log['new_status'] ?>
                                    </span>
                                </h3>
                                <time class="block mb-2 text-xs font-normal leading-none text-slate-400">
                                    <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                                </time>
                                <p class="text-sm font-normal text-slate-500">
                                    <?= htmlspecialchars($log['notes'] ?? $log['reason'] ?? '') ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="ml-6 text-sm text-slate-500 italic">Chưa có lịch sử hoạt động.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: FINANCIALS -->
        <div class="lg:col-span-1 space-y-6">

            <!-- FINANCIAL SUMMARY -->
            <div class="bg-white p-6 rounded shadow-sm border-t-4 border-accent">
                <h2 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">Thanh toán</h2>

                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Tổng tiền tour</span>
                        <span class="font-medium"><?= format_currency($booking['total_amount']) ?></span>
                    </div>
                    <div class="flex justify-between text-green-600">
                        <span class="text-slate-600">Giảm giá</span>
                        <span>-<?= format_currency($booking['discount_amount']) ?></span>
                    </div>
                    <div class="flex justify-between text-xl font-bold text-slate-800 border-t pt-2">
                        <span>Thành tiền</span>
                        <span><?= format_currency($booking['final_amount']) ?></span>
                    </div>

                    <div class="bg-slate-50 p-3 rounded border border-slate-200">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-slate-600">Đã thanh toán</span>
                            <span
                                class="font-bold text-green-600"><?= format_currency($booking['paid_amount']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-slate-600">Còn lại</span>
                            <span
                                class="font-bold text-red-600"><?= format_currency($booking['remaining_amount']) ?></span>
                        </div>
                        <!-- Progress Bar -->
                        <?php
                        $percent = $booking['final_amount'] > 0 ? ($booking['paid_amount'] / $booking['final_amount']) * 100 : 0;
                        ?>
                        <div class="w-full bg-slate-200 rounded-full h-2.5 mt-2">
                            <div class="bg-green-600 h-2.5 rounded-full" style="width: <?= $percent ?>%"></div>
                        </div>
                    </div>
                </div>

                <?php if ($booking['remaining_amount'] > 0 && $booking['approval_status'] != 'cancelled'): ?>
                    <button onclick="openModal('paymentModal')"
                        class="w-full py-2 bg-accent text-white font-bold rounded hover:bg-blue-600 shadow transition-colors">
                        + Thêm thanh toán
                    </button>
                <?php endif; ?>
            </div>

            <!-- PAYMENT LIST -->
            <div class="bg-white p-6 rounded shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">Lịch sử giao dịch</h2>
                <?php if (empty($payments)): ?>
                    <p class="text-sm text-slate-500 italic">Chưa có giao dịch nào.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($payments as $pay): ?>
                            <div class="border-b border-slate-100 pb-2 last:border-0">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-slate-800"><?= format_currency($pay['amount']) ?></span>
                                    <span
                                        class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded capitalize"><?= $pay['payment_method'] ?></span>
                                </div>
                                <div class="flex justify-between text-xs text-slate-500 mt-1">
                                    <span><?= date('d/m/Y', strtotime($pay['payment_date'])) ?></span>
                                    <span><?= htmlspecialchars($pay['creator_name'] ?? 'N/A') ?></span>
                                </div>
                                <?php if ($pay['notes']): ?>
                                    <p class="text-xs text-slate-400 mt-1 italic"><?= htmlspecialchars($pay['notes']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- MODALS -->

<!-- 1. Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-lg font-bold mb-4 text-slate-800">Thêm thanh toán mới</h3>
        <form action="?act=staff-bookings&action=storePayment" method="POST">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1 text-slate-700">Số tiền (VNĐ)</label>
                    <input type="number" name="amount" value="<?= $booking['remaining_amount'] ?>"
                        max="<?= $booking['remaining_amount'] ?>"
                        class="w-full border border-slate-300 rounded px-3 py-2 focus:outline-none focus:border-accent"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-slate-700">Phương thức</label>
                    <select name="payment_method"
                        class="w-full border border-slate-300 rounded px-3 py-2 focus:outline-none focus:border-accent">
                        <option value="cash">Tiền mặt</option>
                        <option value="bank_transfer">Chuyển khoản</option>
                        <option value="credit_card">Thẻ tín dụng</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-slate-700">Loại thanh toán</label>
                    <select name="payment_type"
                        class="w-full border border-slate-300 rounded px-3 py-2 focus:outline-none focus:border-accent">
                        <option value="deposit">Đặt cọc</option>
                        <option value="installment">Thanh toán đợt</option>
                        <option value="full">Thanh toán hết</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-slate-700">Ghi chú</label>
                    <textarea name="notes"
                        class="w-full border border-slate-300 rounded px-3 py-2 focus:outline-none focus:border-accent"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal('paymentModal')"
                    class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded transition-colors">Hủy</button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">Lưu thanh
                    toán</button>
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