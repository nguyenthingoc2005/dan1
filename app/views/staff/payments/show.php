<?php
/**
 * View: Chi tiết Thanh Toán (Staff)
 */
?>

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-slate-800">Chi Tiết Thanh Toán #<?php echo $payment['id']; ?></h2>
            <a href="?act=staff-payments" class="text-slate-500 hover:text-slate-700 font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>

        <div class="p-6">
            <!-- Status Badge -->
            <div class="flex justify-center mb-8">
                <div class="flex flex-col items-center gap-2">
                    <div
                        class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-2xl">
                        <i class="fas fa-check"></i>
                    </div>
                    <span class="text-green-600 font-bold text-lg">Giao dịch thành công</span>
                    <span
                        class="text-slate-500 text-sm"><?php echo date('H:i d/m/Y', strtotime($payment['created_at'])); ?></span>
                </div>
            </div>

            <!-- Amount -->
            <div class="text-center mb-8 pb-8 border-b border-slate-100">
                <div class="text-slate-500 text-sm uppercase tracking-wider mb-1">Số tiền thanh toán</div>
                <div class="text-4xl font-bold text-slate-800"><?php echo number_format($payment['amount']); ?> đ</div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs text-slate-400 uppercase font-medium mb-1">Mã Booking</label>
                    <a href="?act=staff-bookings&action=show&id=<?php echo $payment['booking_id']; ?>"
                        class="text-accent font-bold hover:underline">
                        <?php echo $payment['booking_code']; ?>
                    </a>
                </div>

                <div>
                    <label class="block text-xs text-slate-400 uppercase font-medium mb-1">Khách Hàng</label>
                    <div class="font-medium text-slate-800"><?php echo htmlspecialchars($payment['customer_name']); ?>
                    </div>
                    <div class="text-xs text-slate-500"><?php echo htmlspecialchars($payment['phone']); ?></div>
                </div>

                <div>
                    <label class="block text-xs text-slate-400 uppercase font-medium mb-1">Phương Thức</label>
                    <div class="font-medium text-slate-800">
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
                    <label class="block text-xs text-slate-400 uppercase font-medium mb-1">Loại Thanh Toán</label>
                    <div class="font-medium text-slate-800 uppercase"><?php echo $payment['payment_type']; ?></div>
                </div>

                <?php if (!empty($payment['transaction_id'])): ?>
                    <div>
                        <label class="block text-xs text-slate-400 uppercase font-medium mb-1">Mã Giao Dịch</label>
                        <div class="font-medium text-slate-800"><?php echo htmlspecialchars($payment['transaction_id']); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($payment['receipt_number'])): ?>
                    <div>
                        <label class="block text-xs text-slate-400 uppercase font-medium mb-1">Số Phiếu Thu</label>
                        <div class="font-medium text-slate-800"><?php echo htmlspecialchars($payment['receipt_number']); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($payment['notes'])): ?>
                    <div class="col-span-2">
                        <label class="block text-xs text-slate-400 uppercase font-medium mb-1">Ghi Chú</label>
                        <div class="bg-slate-50 p-3 rounded-lg text-slate-600 text-sm">
                            <?php echo nl2br(htmlspecialchars($payment['notes'])); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-slate-50 p-6 border-t border-slate-200 flex justify-between items-center">
            <div class="text-xs text-slate-500">
                Người tạo: <span
                    class="font-medium text-slate-700"><?php echo htmlspecialchars($payment['creator_name'] ?? 'N/A'); ?></span>
            </div>
            <button onclick="window.print()"
                class="text-slate-600 hover:text-slate-800 font-medium text-sm flex items-center gap-2">
                <i class="fas fa-print"></i> In phiếu thu
            </button>
        </div>
    </div>
</div>