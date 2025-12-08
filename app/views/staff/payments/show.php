<?php
/**
 * View: Chi tiết Thanh Toán (Staff)
 */
?>

<div class="max-w-3xl mx-auto p-4 lg:p-8">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h2 class="text-xl lg:text-2xl font-bold text-primary-700">Chi Tiết Thanh Toán #<?php echo $payment['id']; ?></h2>
        <a href="?act=staff-payments" class="text-primary-500 hover:text-primary-700 font-semibold text-sm lg:text-base flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="p-4 lg:p-6">
            <!-- Status Badge -->
            <div class="flex justify-center mb-6 lg:mb-8">
                <div class="flex flex-col items-center gap-2">
                    <div
                        class="w-16 h-16 rounded-full bg-success-bg flex items-center justify-center text-success-text text-2xl">
                        <i data-lucide="check-circle" class="w-8 h-8"></i>
                    </div>
                    <span class="text-success-text font-bold text-base lg:text-lg">Giao dịch thành công</span>
                    <span
                        class="text-primary-500 text-xs lg:text-sm"><?php echo date('H:i d/m/Y', strtotime($payment['created_at'])); ?></span>
                </div>
            </div>

            <!-- Amount -->
            <div class="text-center mb-6 lg:mb-8 pb-6 lg:pb-8 border-b border-primary-100">
                <div class="text-primary-500 text-xs lg:text-sm uppercase tracking-wider mb-2">Số tiền thanh toán</div>
                <div class="text-3xl lg:text-4xl font-bold text-primary-700"><?php echo number_format($payment['amount']); ?> đ</div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                <div>
                    <label class="block text-xs text-primary-400 uppercase font-semibold mb-1 lg:mb-2">Mã Booking</label>
                    <a href="?act=staff-bookings&action=show&id=<?php echo $payment['booking_id']; ?>"
                        class="text-accent font-bold hover:text-accent-dark text-sm lg:text-base">
                        <?php echo $payment['booking_code']; ?>
                    </a>
                </div>

                <div>
                    <label class="block text-xs text-primary-400 uppercase font-semibold mb-1 lg:mb-2">Khách Hàng</label>
                    <div class="font-semibold text-primary-700 text-sm lg:text-base"><?php echo htmlspecialchars($payment['customer_name']); ?>
                    </div>
                    <div class="text-xs text-primary-500 mt-1"><?php echo htmlspecialchars($payment['phone']); ?></div>
                </div>

                <div>
                    <label class="block text-xs text-primary-400 uppercase font-semibold mb-1 lg:mb-2">Phương Thức</label>
                    <div class="font-semibold text-primary-700 text-sm lg:text-base">
                        <?php
                        $methodMap = [
                            'cash' => 'Tiền mặt',
                            'transfer' => 'Chuyển khoản',
                            'credit_card' => 'Thẻ tín dụng'
                        ];
                        echo $methodMap[$payment['payment_method']] ?? $payment['payment_method'];
                        ?>
                    </div>
                </div>

                <div>
                    <label class="block text-xs text-primary-400 uppercase font-semibold mb-1 lg:mb-2">Loại Thanh Toán</label>
                    <div class="font-semibold text-primary-700 text-sm lg:text-base uppercase"><?php echo $payment['payment_type']; ?></div>
                </div>

                <?php if (!empty($payment['transaction_id'])): ?>
                    <div>
                        <label class="block text-xs text-primary-400 uppercase font-semibold mb-1 lg:mb-2">Mã Giao Dịch</label>
                        <div class="font-semibold text-primary-700 text-sm lg:text-base font-mono"><?php echo htmlspecialchars($payment['transaction_id']); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($payment['receipt_number'])): ?>
                    <div>
                        <label class="block text-xs text-primary-400 uppercase font-semibold mb-1 lg:mb-2">Số Phiếu Thu</label>
                        <div class="font-semibold text-primary-700 text-sm lg:text-base font-mono"><?php echo htmlspecialchars($payment['receipt_number']); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($payment['notes'])): ?>
                    <div class="lg:col-span-2">
                        <label class="block text-xs text-primary-400 uppercase font-semibold mb-1 lg:mb-2">Ghi Chú</label>
                        <div class="bg-primary-50 p-3 lg:p-4 rounded-xl text-primary-600 text-sm lg:text-base border border-primary-100">
                            <?php echo nl2br(htmlspecialchars($payment['notes'])); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-primary-50 p-4 lg:p-6 border-t border-primary-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="text-xs lg:text-sm text-primary-500">
                Người tạo: <span
                    class="font-semibold text-primary-700"><?php echo htmlspecialchars($payment['creator_name'] ?? 'N/A'); ?></span>
            </div>
            <button onclick="window.print()"
                class="text-primary-600 hover:text-primary-800 font-semibold text-sm lg:text-base flex items-center gap-2">
                <i data-lucide="printer" class="w-4 h-4"></i>
                In phiếu thu
            </button>
        </div>
    </div>
</div>