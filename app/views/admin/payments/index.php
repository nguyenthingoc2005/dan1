<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Quản lý Thanh toán</h1>
            <p class="text-sm text-gray-500 mt-1">Theo dõi lịch sử giao dịch và doanh thu</p>
        </div>
        <!-- <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2 shadow-sm">
            <i class="fas fa-download"></i> <span>Xuất báo cáo</span>
        </button> -->
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6 shadow-sm">
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="payments">

            <div class="md:col-span-3">
                <label class="block text-xs font-medium text-gray-700 mb-1">Từ ngày</label>
                <input type="date" name="start_date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm"
                    value="<?= $filters['start_date'] ?? '' ?>">
            </div>
            <div class="md:col-span-3">
                <label class="block text-xs font-medium text-gray-700 mb-1">Đến ngày</label>
                <input type="date" name="end_date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm"
                    value="<?= $filters['end_date'] ?? '' ?>">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Phương thức</label>
                <select name="method"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-white">
                    <option value="">Tất cả</option>
                    <option value="cash" <?= ($filters['payment_method'] ?? '') == 'cash' ? 'selected' : '' ?>>Tiền mặt
                    </option>
                    <option value="bank_transfer" <?= ($filters['payment_method'] ?? '') == 'bank_transfer' ? 'selected' : '' ?>>Chuyển khoản</option>
                    <option value="credit_card" <?= ($filters['payment_method'] ?? '') == 'credit_card' ? 'selected' : '' ?>>Thẻ tín dụng</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Loại</label>
                <select name="type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-white">
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
            <div class="md:col-span-2 flex items-end">
                <button type="submit"
                    class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium py-2 px-4 rounded-lg transition-colors text-sm h-[38px] shadow-sm">
                    <i class="fas fa-filter mr-1"></i> Lọc
                </button>
            </div>
        </form>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                        <th class="px-6 py-4">Mã GD</th>
                        <th class="px-6 py-4">Ngày TT</th>
                        <th class="px-6 py-4">Booking</th>
                        <th class="px-6 py-4">Khách hàng</th>
                        <th class="px-6 py-4">Số tiền</th>
                        <th class="px-6 py-4">Loại</th>
                        <th class="px-6 py-4">Phương thức</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($payments)): ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="?act=admin&module=payments&action=show&id=<?= $payment['id'] ?>"
                                        class="font-mono text-blue-600 font-medium hover:underline">
                                        <?= htmlspecialchars($payment['receipt_number'] ?? 'GD' . $payment['id']) ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?= date('d/m/Y', strtotime($payment['payment_date'])) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="?act=admin&module=bookings&action=show&id=<?= $payment['booking_id'] ?>"
                                        class="text-sm text-slate-700 hover:text-blue-600 font-medium">
                                        <?= htmlspecialchars($payment['booking_code']) ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800">
                                    <?= htmlspecialchars($payment['customer_name']) ?>
                                </td>
                                <td
                                    class="px-6 py-4 font-medium <?= $payment['payment_type'] == 'refund' ? 'text-red-600' : 'text-emerald-600' ?>">
                                    <?= $payment['payment_type'] == 'refund' ? '-' : '+' ?>
                                    <?= format_currency($payment['amount']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $typeMap = [
                                        'deposit' => ['class' => 'bg-amber-100 text-amber-700', 'text' => 'Đặt cọc'],
                                        'full' => ['class' => 'bg-emerald-100 text-emerald-700', 'text' => 'TT Hết'],
                                        'installment' => ['class' => 'bg-blue-100 text-blue-700', 'text' => 'Trả góp'],
                                        'refund' => ['class' => 'bg-red-100 text-red-700', 'text' => 'Hoàn tiền'],
                                    ];
                                    $type = $typeMap[$payment['payment_type']] ?? ['class' => 'bg-gray-100 text-gray-600', 'text' => $payment['payment_type']];
                                    ?>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $type['class'] ?>">
                                        <?= $type['text'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?php
                                    $methodMap = [
                                        'cash' => '<i class="fas fa-money-bill-wave mr-1 text-emerald-500"></i> Tiền mặt',
                                        'bank_transfer' => '<i class="fas fa-university mr-1 text-blue-500"></i> CK',
                                        'credit_card' => '<i class="fas fa-credit-card mr-1 text-purple-500"></i> Thẻ',
                                        'other' => 'Khác'
                                    ];
                                    echo $methodMap[$payment['payment_method']] ?? $payment['payment_method'];
                                    ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($payment['status'] == 'completed'): ?>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Thành công
                                        </span>
                                    <?php elseif ($payment['status'] == 'failed'): ?>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                            Thất bại
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                            Chờ xử lý
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <a href="?act=admin&module=payments&action=show&id=<?= $payment['id'] ?>"
                                        class="text-blue-600 hover:text-blue-900 p-2 rounded-full hover:bg-blue-50 transition-colors"
                                        title="In phiếu">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-receipt text-4xl text-gray-300 mb-3"></i>
                                    <p>Chưa có giao dịch nào.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-center">
                <nav class="flex gap-1">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?act=admin&module=payments&page=<?= $i ?>&start_date=<?= $filters['start_date'] ?? '' ?>&end_date=<?= $filters['end_date'] ?? '' ?>&method=<?= $filters['payment_method'] ?? '' ?>&type=<?= $filters['payment_type'] ?? '' ?>"
                            class="px-3 py-1 rounded-md text-sm font-medium transition-colors <?= $i == $current_page
                                ? 'bg-blue-600 text-white shadow-sm'
                                : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>