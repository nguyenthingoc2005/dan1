<div class=" mx-auto">
    <!-- Page Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý Thanh toán</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Theo dõi lịch sử giao dịch và doanh thu</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-panel rounded-2xl border border-primary-100 p-4 lg:p-5 mb-4 lg:mb-6 shadow-sm">
        <form method="GET" action="" class="grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-4">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="payments">

            <div class="lg:col-span-3">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Từ ngày</label>
                <input type="date" name="start_date"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-sm lg:text-base text-primary-700"
                    value="<?= $filters['start_date'] ?? '' ?>">
            </div>
            <div class="lg:col-span-3">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Đến ngày</label>
                <input type="date" name="end_date"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-sm lg:text-base text-primary-700"
                    value="<?= $filters['end_date'] ?? '' ?>">
            </div>
            <div class="lg:col-span-2">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Phương thức</label>
                <select name="method"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-sm lg:text-base text-primary-700">
                    <option value="">Tất cả</option>
                    <option value="cash" <?= ($filters['payment_method'] ?? '') == 'cash' ? 'selected' : '' ?>>Tiền mặt
                    </option>
                    <option value="bank_transfer" <?= ($filters['payment_method'] ?? '') == 'bank_transfer' ? 'selected' : '' ?>>Chuyển khoản</option>
                    <option value="credit_card" <?= ($filters['payment_method'] ?? '') == 'credit_card' ? 'selected' : '' ?>>Thẻ tín dụng</option>
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Loại</label>
                <select name="type"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-sm lg:text-base text-primary-700">
                    <option value="">Tất cả</option>
                    <option value="deposit" <?= ($filters['payment_type'] ?? '') == 'deposit' ? 'selected' : '' ?>>Đặt cọc
                    </option>
                    <option value="full" <?= ($filters['payment_type'] ?? '') == 'full' ? 'selected' : '' ?>>Thanh toán hết
                    </option>
                    <option value="installment" <?= ($filters['payment_type'] ?? '') == 'installment' ? 'selected' : '' ?>>
                        Trả góp</option>
                    <option value="refund" <?= ($filters['payment_type'] ?? '') == 'refund' ? 'selected' : '' ?>>Hoàn tiền
                    </option>
                </select>
            </div>
            <div class="lg:col-span-2 flex items-end">
                <button type="submit"
                    class="w-full px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-600 hover:opacity-90 text-white font-semibold rounded-xl transition-all text-sm lg:text-base flex items-center justify-center gap-2 h-[38px] shadow-sm">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Lọc
                </button>
            </div>
        </form>
    </div>

    <!-- Payments Table -->
    <div class="bg-panel rounded-2xl border border-primary-100 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead>
                    <tr
                        class="bg-primary-50 border-b border-primary-100 text-xs uppercase text-primary-600 font-semibold tracking-wider">
                        <th class="px-3 lg:px-4 py-2 lg:py-3">Mã GD</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3">Ngày TT</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3">Booking</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3">Khách hàng</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3">Số tiền</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3">Loại</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3">Phương thức</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3">Trạng thái</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-100">
                    <?php if (!empty($payments)): ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr class="hover:bg-primary-50 transition-colors group">
                                <td class="px-3 lg:px-4 py-2 lg:py-3 whitespace-nowrap">
                                    <a href="?act=admin&module=payments&action=show&id=<?= $payment['id'] ?>"
                                        class="font-mono text-accent font-semibold hover:text-accent-dark">
                                        <?= htmlspecialchars($payment['receipt_number'] ?? 'GD' . $payment['id']) ?>
                                    </a>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-600">
                                    <?= date('d/m/Y', strtotime($payment['payment_date'])) ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <a href="?act=admin&module=bookings&action=show&id=<?= $payment['booking_id'] ?>"
                                        class="text-sm text-primary-700 hover:text-accent font-semibold">
                                        <?= htmlspecialchars($payment['booking_code']) ?>
                                    </a>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-700 font-medium">
                                    <?= htmlspecialchars($payment['customer_name']) ?>
                                </td>
                                <td
                                    class="px-3 lg:px-4 py-2 lg:py-3 font-semibold <?= $payment['payment_type'] == 'refund' ? 'text-danger-text' : 'text-success-text' ?>">
                                    <?= $payment['payment_type'] == 'refund' ? '-' : '+' ?>
                                    <?= format_currency($payment['amount']) ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <?php
                                    $typeMap = [
                                        'deposit' => ['class' => 'bg-warning-bg text-warning-text', 'text' => 'Đặt cọc'],
                                        'full' => ['class' => 'bg-success-bg text-success-text', 'text' => 'TT Hết'],
                                        'installment' => ['class' => 'bg-info-bg text-info-text', 'text' => 'Trả góp'],
                                        'refund' => ['class' => 'bg-danger-bg text-danger-text', 'text' => 'Hoàn tiền'],
                                    ];
                                    $type = $typeMap[$payment['payment_type']] ?? ['class' => 'bg-primary-100 text-primary-500', 'text' => $payment['payment_type']];
                                    ?>
                                    <span
                                        class="inline-flex items-center px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase <?= $type['class'] ?>">
                                        <?= $type['text'] ?>
                                    </span>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-600">
                                    <?php
                                    $methodMap = [
                                        'cash' => ['icon' => 'banknote', 'text' => 'Tiền mặt', 'color' => 'text-success-text'],
                                        'bank_transfer' => ['icon' => 'building-2', 'text' => 'CK', 'color' => 'text-info-text'],
                                        'credit_card' => ['icon' => 'credit-card', 'text' => 'Thẻ', 'color' => 'text-accent'],
                                        'other' => ['icon' => 'more-horizontal', 'text' => 'Khác', 'color' => 'text-primary-500']
                                    ];
                                    $method = $methodMap[$payment['payment_method']] ?? ['icon' => 'more-horizontal', 'text' => $payment['payment_method'], 'color' => 'text-primary-500'];
                                    ?>
                                    <span class="flex items-center gap-1 <?= $method['color'] ?>">
                                        <i data-lucide="<?= $method['icon'] ?>" class="w-4 h-4"></i>
                                        <?= $method['text'] ?>
                                    </span>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <?php if ($payment['status'] == 'completed'): ?>
                                        <span
                                            class="inline-flex items-center px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase bg-success-bg text-success-text">
                                            <span class="w-1.5 h-1.5 rounded-full bg-success-text mr-1.5"></span>
                                            Thành công
                                        </span>
                                    <?php elseif ($payment['status'] == 'failed'): ?>
                                        <span
                                            class="inline-flex items-center px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase bg-danger-bg text-danger-text">
                                            <span class="w-1.5 h-1.5 rounded-full bg-danger-text mr-1.5"></span>
                                            Thất bại
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase bg-warning-bg text-warning-text">
                                            <span class="w-1.5 h-1.5 rounded-full bg-warning-text mr-1.5"></span>
                                            Chờ xử lý
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-right text-sm font-medium">
                                    <a href="?act=admin&module=payments&action=show&id=<?= $payment['id'] ?>"
                                        class="text-accent hover:text-accent-dark p-1.5 rounded-xl hover:bg-accent-50 transition-all inline-flex items-center"
                                        title="In phiếu">
                                        <i data-lucide="printer" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="px-3 lg:px-4 py-8 lg:py-12 text-center text-primary-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-lucide="receipt" class="w-12 h-12 text-primary-300 mb-3"></i>
                                    <p class="text-sm">Chưa có giao dịch nào.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="px-4 lg:px-6 py-3 lg:py-4 border-t border-primary-100 bg-primary-50 flex justify-center">
                <nav class="flex gap-1 lg:gap-2">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?act=admin&module=payments&page=<?= $i ?>&start_date=<?= $filters['start_date'] ?? '' ?>&end_date=<?= $filters['end_date'] ?? '' ?>&method=<?= $filters['payment_method'] ?? '' ?>&type=<?= $filters['payment_type'] ?? '' ?>"
                            class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-sm font-semibold transition-all <?= $i == $current_page
                                ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white shadow-sm'
                                : 'bg-panel border border-primary-100 text-primary-700 hover:bg-primary-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>