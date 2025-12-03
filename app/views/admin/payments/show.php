<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết Giao dịch</h1>
        <div>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> In phiếu
            </button>
            <a href="?act=admin&module=payments" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-body p-5">
                    <!-- Receipt Header -->
                    <div class="text-center mb-5">
                        <h2 class="font-weight-bold text-uppercase mb-2">Phiếu Thu / Receipt</h2>
                        <p class="text-muted">Mã phiếu:
                            <strong><?= htmlspecialchars($payment['receipt_number']) ?></strong></p>
                        <p class="text-muted">Ngày: <?= date('d/m/Y H:i', strtotime($payment['created_at'])) ?></p>
                    </div>

                    <!-- Receipt Info -->
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="font-weight-bold text-uppercase text-gray-700">Đơn vị thu:</h6>
                            <h5>TOUR MANAGEMENT SYSTEM</h5>
                            <ul class="list-unstyled text-muted">
                                <li>123 Đường ABC, Quận XYZ</li>
                                <li>TP. Hồ Chí Minh, Việt Nam</li>
                                <li>Hotline: 1900 1234</li>
                            </ul>
                        </div>
                        <div class="col-sm-6 text-end">
                            <h6 class="font-weight-bold text-uppercase text-gray-700">Khách hàng:</h6>
                            <h5><?= htmlspecialchars($payment['customer_name']) ?></h5>
                            <ul class="list-unstyled text-muted">
                                <li><?= htmlspecialchars($payment['phone']) ?></li>
                                <li><?= htmlspecialchars($payment['email'] ?? '') ?></li>
                                <li><?= htmlspecialchars($payment['address'] ?? '') ?></li>
                            </ul>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Payment Details -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td class="fw-bold" style="width: 200px;">Nội dung thu:</td>
                                        <td>Thanh toán cho Booking
                                            <strong><?= htmlspecialchars($payment['booking_code']) ?></strong> - Tour:
                                            <?= htmlspecialchars($payment['tour_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Loại thanh toán:</td>
                                        <td>
                                            <?php
                                            $typeMap = [
                                                'deposit' => 'Đặt cọc',
                                                'full' => 'Thanh toán hết',
                                                'installment' => 'Trả góp',
                                                'refund' => 'Hoàn tiền',
                                            ];
                                            echo $typeMap[$payment['payment_type']] ?? $payment['payment_type'];
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Phương thức:</td>
                                        <td>
                                            <?php
                                            $methodMap = [
                                                'cash' => 'Tiền mặt',
                                                'bank_transfer' => 'Chuyển khoản',
                                                'credit_card' => 'Thẻ tín dụng',
                                                'other' => 'Khác'
                                            ];
                                            echo $methodMap[$payment['payment_method']] ?? $payment['payment_method'];
                                            ?>
                                        </td>
                                    </tr>
                                    <?php if (!empty($payment['transaction_id'])): ?>
                                        <tr>
                                            <td class="fw-bold">Mã giao dịch (Ref):</td>
                                            <td><?= htmlspecialchars($payment['transaction_id']) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($payment['notes'])): ?>
                                        <tr>
                                            <td class="fw-bold">Ghi chú:</td>
                                            <td><?= nl2br(htmlspecialchars($payment['notes'])) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row justify-content-end">
                        <div class="col-lg-6">
                            <div class="bg-light p-3 rounded">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="font-weight-bold">Số tiền:</span>
                                    <span
                                        class="h4 mb-0 font-weight-bold text-primary"><?= format_currency($payment['amount']) ?></span>
                                </div>
                                <div class="text-end text-muted small fst-italic">
                                    (Đã bao gồm VAT nếu có)
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-5">

                    <!-- Signatures -->
                    <div class="row text-center mt-5">
                        <div class="col-6">
                            <p class="font-weight-bold">Người nộp tiền</p>
                            <p class="text-muted small fst-italic">(Ký, họ tên)</p>
                            <div style="height: 100px;"></div>
                        </div>
                        <div class="col-6">
                            <p class="font-weight-bold">Người thu tiền</p>
                            <p class="text-muted small fst-italic">(Ký, họ tên)</p>
                            <div style="height: 100px;"></div>
                        </div>
                    </div>
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