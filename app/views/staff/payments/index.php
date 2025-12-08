<?php
/**
 * View: Lịch Sử Thanh Toán (Staff)
 */
?>

<div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
    <div class="p-4 lg:p-6 border-b border-primary-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg lg:text-xl font-bold text-primary-700">Lịch Sử Thanh Toán</h2>
            <div class="text-xs lg:text-sm text-primary-500 mt-1">
                Hiển thị các giao dịch từ bookings của bạn
            </div>
        </div>
    </div>

    <div class="p-4 lg:p-6">
        <!-- Filter -->
        <form action="" method="GET" class="mb-4 lg:mb-6 grid grid-cols-1 lg:grid-cols-4 gap-3 lg:gap-4">
            <input type="hidden" name="act" value="staff-payments">

            <!-- Search -->
            <div class="lg:col-span-1">
                <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                    placeholder="Mã booking, tên khách..."
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
            </div>

            <!-- Payment Method -->
            <div>
                <select name="payment_method"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Phương thức --</option>
                    <option value="cash" <?php echo ($_GET['payment_method'] ?? '') == 'cash' ? 'selected' : ''; ?>>Tiền
                        mặt</option>
                    <option value="transfer" <?php echo ($_GET['payment_method'] ?? '') == 'transfer' ? 'selected' : ''; ?>>Chuyển khoản</option>
                    <option value="credit_card" <?php echo ($_GET['payment_method'] ?? '') == 'credit_card' ? 'selected' : ''; ?>>Thẻ tín dụng</option>
                </select>
            </div>

            <!-- Date Range -->
            <div>
                <input type="date" name="start_date" value="<?php echo $_GET['start_date'] ?? ''; ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
            </div>

            <button type="submit"
                class="w-full px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-600 hover:opacity-90 text-white font-semibold rounded-xl transition-all text-sm lg:text-base flex items-center justify-center gap-2 shadow-sm">
                <i data-lucide="filter" class="w-4 h-4"></i>
                Lọc
            </button>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-primary-50 text-primary-600 text-xs uppercase tracking-wider">
                        <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold border-b border-primary-100">ID</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold border-b border-primary-100">Booking</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold border-b border-primary-100">Khách Hàng</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold border-b border-primary-100">Số Tiền</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold border-b border-primary-100">Phương Thức</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold border-b border-primary-100">Ngày TT</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold border-b border-primary-100 text-right">Hành Động</th>
                    </tr>
                </thead>
                <tbody class="text-primary-700 text-sm">
                    <?php if (!empty($payments)): ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr class="border-b border-primary-100 hover:bg-primary-50 transition-colors">
                                <td class="px-3 lg:px-4 py-2 lg:py-3 font-medium text-primary-500">#<?php echo $payment['id']; ?></td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <a href="?act=staff-bookings&action=show&id=<?php echo $payment['booking_id']; ?>"
                                        class="font-bold text-accent hover:text-accent-dark">
                                        <?php echo $payment['booking_code']; ?>
                                    </a>
                                    <div class="text-xs text-primary-500 uppercase mt-1"><?php echo $payment['payment_type']; ?></div>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 font-medium text-primary-700">
                                    <?php echo htmlspecialchars($payment['customer_name']); ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 font-bold text-success-text">
                                    <?php echo number_format($payment['amount']); ?> đ
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600">
                                    <?php
                                    $methodMap = [
                                        'cash' => ['icon' => 'banknote', 'text' => 'Tiền mặt'],
                                        'transfer' => ['icon' => 'building-2', 'text' => 'Chuyển khoản'],
                                        'credit_card' => ['icon' => 'credit-card', 'text' => 'Thẻ tín dụng']
                                    ];
                                    $method = $methodMap[$payment['payment_method']] ?? ['icon' => 'more-horizontal', 'text' => $payment['payment_method']];
                                    ?>
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="<?= $method['icon'] ?>" class="w-4 h-4"></i>
                                        <?= $method['text'] ?>
                                    </span>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-500">
                                    <?php echo date('d/m/Y', strtotime($payment['payment_date'])); ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-right">
                                    <a href="?act=staff-payments&action=show&id=<?php echo $payment['id']; ?>"
                                        class="p-1.5 lg:p-2 text-accent hover:text-accent-dark hover:bg-accent-50 rounded-xl transition-all inline-flex items-center"
                                        title="Xem chi tiết">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-3 lg:px-4 py-8 lg:py-12 text-center text-primary-500">
                                <div class="flex flex-col items-center gap-3">
                                    <i data-lucide="receipt" class="w-12 h-12 text-primary-300"></i>
                                    <p class="text-sm">Chưa có giao dịch thanh toán nào.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-4 lg:mt-6 flex justify-center gap-2 flex-wrap">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?act=staff-payments&page=<?php echo $i; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>"
                        class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-semibold transition-all <?php echo $i == $current_page ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white shadow-sm' : 'bg-panel border border-primary-100 text-primary-700 hover:bg-primary-50'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>