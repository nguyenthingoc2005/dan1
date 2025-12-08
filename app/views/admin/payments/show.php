<div class="max-w-4xl mx-auto p-4 lg:p-8">
    <!-- Page Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Chi tiết Giao dịch</h1>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <button onclick="window.print()" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white font-semibold rounded-xl shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="printer" class="w-4 h-4"></i>
                In phiếu
            </button>
            <a href="?act=admin&module=payments" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 font-semibold rounded-xl hover:bg-primary-100 transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
    </div>

    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="p-4 lg:p-8">
            <!-- Receipt Header -->
            <div class="text-center mb-6 lg:mb-8">
                <h2 class="text-xl lg:text-2xl font-bold text-primary-700 mb-2 uppercase">Phiếu Thu / Receipt</h2>
                <p class="text-sm lg:text-base text-primary-500">Mã phiếu:
                    <strong class="text-primary-700"><?= htmlspecialchars($payment['receipt_number']) ?></strong></p>
                <p class="text-sm lg:text-base text-primary-500">Ngày: <?= date('d/m/Y H:i', strtotime($payment['created_at'])) ?></p>
            </div>

            <!-- Receipt Info -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-6 lg:mb-8">
                <div>
                    <h6 class="text-xs lg:text-sm font-bold text-uppercase text-primary-700 mb-2">Đơn vị thu:</h6>
                    <h5 class="text-base lg:text-lg font-bold text-primary-700 mb-2">TOUR MANAGEMENT SYSTEM</h5>
                    <ul class="list-none text-sm text-primary-600 space-y-1">
                        <li>123 Đường ABC, Quận XYZ</li>
                        <li>TP. Hồ Chí Minh, Việt Nam</li>
                        <li>Hotline: 1900 1234</li>
                    </ul>
                </div>
                <div class="text-left lg:text-right">
                    <h6 class="text-xs lg:text-sm font-bold text-uppercase text-primary-700 mb-2">Khách hàng:</h6>
                    <h5 class="text-base lg:text-lg font-bold text-primary-700 mb-2"><?= htmlspecialchars($payment['customer_name']) ?></h5>
                    <ul class="list-none text-sm text-primary-600 space-y-1">
                        <li><?= htmlspecialchars($payment['phone']) ?></li>
                        <li><?= htmlspecialchars($payment['email'] ?? '') ?></li>
                        <li><?= htmlspecialchars($payment['address'] ?? '') ?></li>
                    </ul>
                </div>
            </div>

            <hr class="my-4 lg:my-6 border-primary-100">

            <!-- Payment Details -->
            <div class="mb-6 lg:mb-8">
                <div class="space-y-3 lg:space-y-4">
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                        <div class="w-full sm:w-48 font-semibold text-primary-700 text-sm lg:text-base">Nội dung thu:</div>
                        <div class="flex-1 text-primary-600 text-sm lg:text-base">Thanh toán cho Booking
                            <strong class="text-primary-700"><?= htmlspecialchars($payment['booking_code']) ?></strong> - Tour:
                            <?= htmlspecialchars($payment['tour_name']) ?></div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                        <div class="w-full sm:w-48 font-semibold text-primary-700 text-sm lg:text-base">Loại thanh toán:</div>
                        <div class="flex-1 text-primary-600 text-sm lg:text-base">
                            <?php
                            $typeMap = [
                                'deposit' => 'Đặt cọc',
                                'full' => 'Thanh toán hết',
                                'installment' => 'Trả góp',
                                'refund' => 'Hoàn tiền',
                            ];
                            echo $typeMap[$payment['payment_type']] ?? $payment['payment_type'];
                            ?>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                        <div class="w-full sm:w-48 font-semibold text-primary-700 text-sm lg:text-base">Phương thức:</div>
                        <div class="flex-1 text-primary-600 text-sm lg:text-base">
                            <?php
                            $methodMap = [
                                'cash' => 'Tiền mặt',
                                'bank_transfer' => 'Chuyển khoản',
                                'credit_card' => 'Thẻ tín dụng',
                                'other' => 'Khác'
                            ];
                            echo $methodMap[$payment['payment_method']] ?? $payment['payment_method'];
                            ?>
                        </div>
                    </div>
                    <?php if (!empty($payment['transaction_id'])): ?>
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                            <div class="w-full sm:w-48 font-semibold text-primary-700 text-sm lg:text-base">Mã giao dịch (Ref):</div>
                            <div class="flex-1 text-primary-600 text-sm lg:text-base font-mono"><?= htmlspecialchars($payment['transaction_id']) ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($payment['notes'])): ?>
                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                            <div class="w-full sm:w-48 font-semibold text-primary-700 text-sm lg:text-base">Ghi chú:</div>
                            <div class="flex-1 text-primary-600 text-sm lg:text-base"><?= nl2br(htmlspecialchars($payment['notes'])) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex justify-end mb-6 lg:mb-8">
                <div class="w-full sm:w-96">
                    <div class="bg-primary-50 p-4 lg:p-5 rounded-2xl border border-primary-100">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold text-primary-700 text-sm lg:text-base">Số tiền:</span>
                            <span class="text-xl lg:text-2xl font-bold text-accent"><?= format_currency($payment['amount']) ?></span>
                        </div>
                        <div class="text-right text-xs text-primary-500 italic mt-2">
                            (Đã bao gồm VAT nếu có)
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4 lg:my-6 border-primary-100">

            <!-- Signatures -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6 text-center mt-6 lg:mt-8">
                <div>
                    <p class="font-bold text-primary-700 text-sm lg:text-base mb-2">Người nộp tiền</p>
                    <p class="text-xs text-primary-500 italic mb-4">(Ký, họ tên)</p>
                    <div class="h-24 border-b border-primary-200"></div>
                </div>
                <div>
                    <p class="font-bold text-primary-700 text-sm lg:text-base mb-2">Người thu tiền</p>
                    <p class="text-xs text-primary-500 italic mb-4">(Ký, họ tên)</p>
                    <div class="h-24 border-b border-primary-200"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }

        .card,
        .card * {
            visibility: visible;
        }

        .card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none !important;
            border: none !important;
        }

        .btn,
        .d-flex.justify-content-between {
            display: none !important;
        }
    }
</style>