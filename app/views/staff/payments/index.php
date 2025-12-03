<?php
/**
 * View: Lịch Sử Thanh Toán (Staff)
 */
?>

<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-slate-200 flex justify-between items-center">
        <h2 class="text-xl font-bold text-slate-800">Lịch Sử Thanh Toán</h2>
        <div class="text-sm text-slate-500">
            Hiển thị các giao dịch từ bookings của bạn
        </div>
    </div>

    <div class="p-6">
        <!-- Filter -->
        <form action="" method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="act" value="staff-payments">

            <!-- Search -->
            <div class="md:col-span-1">
                <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                    placeholder="Mã booking, tên khách..."
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
            </div>

            <!-- Payment Method -->
            <div>
                <select name="payment_method"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                    <option value="">-- Phương thức --</option>
                    <option value="cash" <?php echo ($_GET['payment_method'] ?? '') == 'cash' ? 'selected' : ''; ?>>Tiền
                        mặt</option>
                    <option value="transfer" <?php echo ($_GET['payment_method'] ?? '') == 'transfer' ? 'selected' : ''; ?>>Chuyển khoản</option>
                    <option value="credit_card" <?php echo ($_GET['payment_method'] ?? '') == 'credit_card' ? 'selected' : ''; ?>>Thẻ tín dụng</option>
                </select>
            </div>

            <!-- Date Range -->
            <div class="flex gap-2">
                <input type="date" name="start_date" value="<?php echo $_GET['start_date'] ?? ''; ?>"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
            </div>

            <button type="submit"
                class="bg-slate-800 text-white px-6 py-2 rounded-lg hover:bg-slate-700 transition-colors font-medium">
                <i class="fas fa-filter mr-2"></i> Lọc
            </button>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 text-sm uppercase tracking-wider">
                        <th class="p-4 font-semibold border-b border-slate-200">ID</th>
                        <th class="p-4 font-semibold border-b border-slate-200">Booking</th>
                        <th class="p-4 font-semibold border-b border-slate-200">Khách Hàng</th>
                        <th class="p-4 font-semibold border-b border-slate-200">Số Tiền</th>
                        <th class="p-4 font-semibold border-b border-slate-200">Phương Thức</th>
                        <th class="p-4 font-semibold border-b border-slate-200">Ngày TT</th>
                        <th class="p-4 font-semibold border-b border-slate-200 text-right">Hành Động</th>
                    </tr>
                </thead>
                <tbody class="text-slate-700 text-sm">
                    <?php if (!empty($payments)): ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                <td class="p-4 font-medium text-slate-500">#<?php echo $payment['id']; ?></td>
                                <td class="p-4">
                                    <a href="?act=staff-bookings&action=show&id=<?php echo $payment['booking_id']; ?>"
                                        class="font-bold text-accent hover:underline">
                                        <?php echo $payment['booking_code']; ?>
                                    </a>
                                    <div class="text-xs text-slate-500 uppercase"><?php echo $payment['payment_type']; ?></div>
                                </td>
                                <td class="p-4 font-medium">
                                    <?php echo htmlspecialchars($payment['customer_name']); ?>
                                </td>
                                <td class="p-4 font-bold text-green-600">
                                    <?php echo number_format($payment['amount']); ?> đ
                                </td>
                                <td class="p-4">
                                    <?php
                                    $methodMap = [
                                        'cash' => 'Tiền mặt',
                                        'transfer' => 'Chuyển khoản',
                                        'credit_card' => 'Thẻ tín dụng'
                                    ];
                                    echo $methodMap[$payment['payment_method']] ?? $payment['payment_method'];
                                    ?>
                                </td>
                                <td class="p-4 text-slate-500">
                                    <?php echo date('d/m/Y', strtotime($payment['payment_date'])); ?>
                                </td>
                                <td class="p-4 text-right">
                                    <a href="?act=staff-payments&action=show&id=<?php echo $payment['id']; ?>"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">
                                <div class="flex flex-col items-center gap-3">
                                    <i class="fas fa-receipt text-4xl text-slate-300"></i>
                                    <p>Chưa có giao dịch thanh toán nào.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-6 flex justify-center gap-2">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?act=staff-payments&page=<?php echo $i; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>"
                        class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors <?php echo $i == $current_page ? 'bg-accent text-white shadow-md' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>