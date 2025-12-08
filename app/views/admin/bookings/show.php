<?php
/**
 * ADMIN - CHI TIẾT BOOKING
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

// Status Colors (chỉ dùng payment_status) - Horizon UI
$statusColors = [
    'unpaid' => 'bg-warning-bg text-warning-text',
    'partial' => 'bg-warning-bg text-warning-text',
    'paid' => 'bg-success-bg text-success-text',
    'rejected' => 'bg-primary-100 text-primary-500',
    'cancelled' => 'bg-danger-bg text-danger-text',
    'refunded' => 'bg-info-bg text-info-text'
];

// Status Text (tiếng Việt)
$statusTexts = [
    'unpaid' => 'Chờ thanh toán',
    'partial' => 'Đã cọc',
    'paid' => 'Đã thanh toán',
    'rejected' => 'Từ chối',
    'cancelled' => 'Đã hủy',
    'refunded' => 'Đã hoàn tiền'
];
?>

<div class="max-w-[95%] mx-auto">
    <!-- HEADER - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <div class="flex flex-wrap items-center gap-2 lg:gap-3 mb-2">
                <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Booking: <?= $booking['booking_code'] ?></h1>
                <span
                    class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $statusColors[$booking['payment_status']] ?? 'bg-primary-100 text-primary-500' ?>">
                    <?= $statusTexts[$booking['payment_status']] ?? $booking['payment_status'] ?>
                </span>
            </div>
            <p class="text-xs lg:text-sm text-primary-500">Tạo ngày <?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?> bởi
                <?= $booking['creator_name'] ?? 'N/A' ?>
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <a href="?act=admin&module=bookings" class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>

            <?php if ($booking['payment_status'] == 'unpaid'): ?>
                <!-- Bỏ button "Duyệt" vì đã bỏ tính năng duyệt thủ công -->
                <button onclick="openModal('rejectModal')"
                    class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-500 hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                    Từ chối
                </button>
            <?php endif; ?>

            <?php if (in_array($booking['payment_status'], ['unpaid', 'partial', 'paid'])): ?>
                <button onclick="openModal('cancelModal')"
                    class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-danger hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    Hủy Booking
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">

        <!-- LEFT COLUMN: INFO -->
        <div class="lg:col-span-2 space-y-4 lg:space-y-6">

            <!-- 1. TOUR INFO -->
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100">
                <h2 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-4 lg:mb-5">Thông tin Tour</h2>
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
                        <p class="font-semibold text-primary-700 text-sm lg:text-base"><?= date('d/m/Y', strtotime($booking['start_date'])) ?></p>
                    </div>
                    <div>
                        <p class="text-xs lg:text-sm text-primary-500 font-semibold mb-1">Kết thúc</p>
                        <p class="font-semibold text-primary-700 text-sm lg:text-base"><?= date('d/m/Y', strtotime($booking['end_date'])) ?></p>
                    </div>
                    <div>
                        <p class="text-xs lg:text-sm text-primary-500 font-semibold mb-1">Thời lượng</p>
                        <p class="font-semibold text-primary-700 text-sm lg:text-base"><?= $booking['duration_days'] ?>N <?= $booking['duration_nights'] ?>Đ</p>
                    </div>
                </div>
            </div>

            <!-- 2. CUSTOMER & PASSENGERS -->
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100">
                <h2 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-4 lg:mb-5">Khách hàng & Hành khách</h2>

                <div class="mb-4 bg-info-bg p-4 lg:p-5 rounded-2xl border border-info">
                    <p class="font-bold text-info-text text-sm lg:text-base"><?= $booking['customer_name'] ?></p>
                    <p class="text-xs lg:text-sm text-info-text mt-1"><?= $booking['customer_phone'] ?> |
                        <?= $booking['customer_email'] ?>
                    </p>
                    <p class="text-xs lg:text-sm text-primary-500 mt-1"><?= $booking['customer_address'] ?></p>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-2 lg:mb-3">
                    <h3 class="font-bold text-xs lg:text-sm text-primary-700">Danh sách đoàn
                        (<?= count($booking['passengers'] ?? []) ?> người)</h3>
                    <?php
                    // Chỉ cho phép thêm khách nếu:
                    // 1. Booking chưa bị hủy
                    // 2. Còn >= 1 ngày đến ngày khởi hành
                    $canAddPassenger = false;
                    if (!in_array($booking['payment_status'], ['cancelled', 'rejected', 'refunded'])) {
                        $today = date('Y-m-d');
                        $start_date = $booking['start_date'] ?? null;
                        if ($start_date) {
                            $daysUntilStart = (strtotime($start_date) - strtotime($today)) / (60 * 60 * 24);
                            $canAddPassenger = $daysUntilStart >= 1;
                        }
                    }
                    ?>
                    <?php if ($canAddPassenger): ?>
                        <button onclick="openModal('addPassengerModal')"
                            class="text-xs lg:text-sm px-3 lg:px-4 py-1.5 lg:py-2 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all flex items-center gap-1">
                            <i data-lucide="plus" class="w-3 h-3"></i>
                            Thêm khách
                        </button>
                    <?php elseif (!in_array($booking['payment_status'], ['cancelled', 'rejected', 'refunded'])): ?>
                        <span class="text-xs text-primary-500 italic flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3 h-3"></i>
                            Không thể thêm khách (còn < 1 ngày đến ngày khởi hành)
                        </span>
                    <?php endif; ?>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse min-w-[600px]">
                        <thead class="bg-primary-50">
                        <tr>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">Họ tên</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">Loại</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">Vai trò</th>
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
                                        <span class="text-xs bg-info-bg text-info-text px-3 py-1 rounded-full font-bold">Người đặt</span>
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

            <!-- 3. BOOKING SERVICES -->
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4">
                    <h2 class="text-base lg:text-lg font-bold text-primary-700">Dịch vụ bổ sung</h2>
                    <?php
                    // Chỉ cho phép thêm dịch vụ nếu:
                    // 1. Booking chưa bị hủy
                    // 2. Còn >= 1 ngày đến ngày khởi hành
                    $canAddService = false;
                    if (!in_array($booking['payment_status'], ['cancelled', 'rejected', 'refunded'])) {
                        $today = date('Y-m-d');
                        $start_date = $booking['start_date'] ?? null;
                        if ($start_date) {
                            $daysUntilStart = (strtotime($start_date) - strtotime($today)) / (60 * 60 * 24);
                            $canAddService = $daysUntilStart >= 1;
                        }
                    }
                    ?>
                    <?php if ($canAddService): ?>
                        <button onclick="openModal('addServiceModal')"
                            class="text-xs lg:text-sm px-3 lg:px-4 py-1.5 lg:py-2 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all flex items-center gap-1">
                            <i data-lucide="plus" class="w-3 h-3"></i>
                            Thêm dịch vụ
                        </button>
                    <?php elseif (!in_array($booking['payment_status'], ['cancelled', 'rejected', 'refunded'])): ?>
                        <span class="text-xs text-primary-500 italic flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3 h-3"></i>
                            Không thể thêm dịch vụ (còn < 1 ngày đến ngày khởi hành)
                        </span>
                    <?php endif; ?>
                </div>

                <?php if (empty($bookingServices)): ?>
                    <p class="text-sm text-gray-500 italic">Chưa có dịch vụ nào được thêm vào booking này.</p>
                <?php else: ?>
                    <table class="w-full text-sm text-left text-gray-500 border rounded">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2">Tên dịch vụ</th>
                                <th class="px-3 py-2">Nhà cung cấp</th>
                                <th class="px-3 py-2 text-right">Số lượng</th>
                                <th class="px-3 py-2 text-right">Đơn giá</th>
                                <th class="px-3 py-2 text-right">Thành tiền</th>
                                <th class="px-3 py-2 text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookingServices as $service): ?>
                                <?php
                                // Phân biệt dịch vụ gốc (từ tour) vs dịch vụ thêm vào sau
                                $isFromTour = !empty($service['notes']) && strpos($service['notes'], '[TOUR_ORIGINAL]') !== false;
                                $serviceNotes = $service['notes'] ?? '';
                                // Remove prefix nếu là dịch vụ gốc
                                if ($isFromTour) {
                                    $serviceNotes = str_replace('[TOUR_ORIGINAL] Auto-copied from tour template', '', $serviceNotes);
                                    $serviceNotes = trim($serviceNotes);
                                }
                                ?>
                                <tr class="border-b">
                                    <td class="px-3 py-2">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-medium"><?= htmlspecialchars($service['service_name'] ?? $service['service_name_original'] ?? 'N/A') ?></span>
                                            <?php if ($isFromTour): ?>
                                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded whitespace-nowrap">Dịch vụ gốc</span>
                                            <?php else: ?>
                                                <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded whitespace-nowrap">Dịch vụ thêm</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($serviceNotes)): ?>
                                            <div class="text-xs text-gray-400 italic mt-1"><?= htmlspecialchars($serviceNotes) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-3 py-2">
                                        <?= htmlspecialchars($service['supplier_name'] ?? $service['service_provider_name'] ?? 'N/A') ?>
                                    </td>
                                    <td class="px-3 py-2 text-right"><?= $service['quantity'] ?>
                                        <?= htmlspecialchars($service['unit'] ?? '') ?></td>
                                    <td class="px-3 py-2 text-right"><?= format_currency($service['unit_price']) ?></td>
                                    <td class="px-3 py-2 text-right font-medium"><?= format_currency($service['total_price']) ?>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <?php if ($service['paid_amount'] == 0 && !in_array($booking['payment_status'], ['cancelled', 'rejected', 'refunded'])): ?>
                                            <?php if ($isFromTour): ?>
                                                <span class="text-xs text-gray-400 italic">Không thể xóa</span>
                                            <?php else: ?>
                                                <form action="?act=admin&module=bookings&action=deleteBookingService" method="POST"
                                                    class="inline" onsubmit="return confirm('Xóa dịch vụ này?')">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="id" value="<?= $service['id'] ?>">
                                                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs">✕ Xóa</button>
                                                </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400">Đã thanh toán</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-3 py-2 text-right font-bold">Tổng tiền dịch vụ:</td>
                                <td class="px-3 py-2 text-right font-bold text-blue-600">
                                    <?= format_currency($serviceTotals['total_cost'] ?? 0) ?>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php endif; ?>
            </div>

            <!-- 4. HISTORY LOG -->
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100">
                <h2 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-4 lg:mb-5">Lịch sử hoạt động</h2>
                <div class="relative border-l-2 border-gray-200 ml-3 space-y-6">
                    <?php foreach ($history as $log): ?>
                        <div class="mb-4 ml-6 relative">
                            <span
                                class="absolute -left-[33px] flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full ring-4 ring-white">
                                <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                            </span>
                            <h3 class="flex items-center mb-1 text-sm font-semibold text-gray-900">
                                <?= $log['user_name'] ?? 'Hệ thống' ?>
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded ml-2">
                                    <?= $log['new_status'] ?>
                                </span>
                            </h3>
                            <time class="block mb-2 text-xs font-normal leading-none text-gray-400">
                                <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                            </time>
                            <p class="text-sm font-normal text-gray-500">
                                <?= $log['notes'] ?? $log['reason'] ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: FINANCIALS -->
        <div class="lg:col-span-1 space-y-6">

            <!-- KHUYẾN MÃI -->
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border-t-4 border-success">
                <h2 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-4 lg:mb-5">Khuyến mãi</h2>
                
                <?php if (empty($booking['discount_code']) && $booking['discount_amount'] == 0): ?>
                    <div class="space-y-3">
                        <p class="text-xs lg:text-sm text-primary-500">Chưa áp dụng mã giảm giá</p>
                        <?php if (!in_array($booking['payment_status'], ['cancelled', 'rejected', 'refunded'])): ?>
                            <button onclick="openModal('discountModal')" 
                                class="w-full px-4 lg:px-5 py-2 lg:py-2.5 bg-success hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                                <i data-lucide="sparkles" class="w-4 h-4"></i>
                                Áp dụng mã giảm giá
                            </button>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs lg:text-sm text-primary-500">Mã giảm giá:</span>
                            <span class="font-bold text-success-text text-sm lg:text-base"><?= htmlspecialchars($booking['discount_code'] ?: 'Giảm trực tiếp') ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs lg:text-sm text-primary-500">Số tiền giảm:</span>
                            <span class="font-bold text-success-text text-sm lg:text-base">-<?= format_currency($booking['discount_amount']) ?></span>
                        </div>
                        <?php if (!in_array($booking['payment_status'], ['cancelled', 'rejected', 'refunded'])): ?>
                            <button onclick="openModal('discountModal')" 
                                class="w-full text-xs lg:text-sm text-accent hover:text-accent-hover hover:underline font-semibold flex items-center justify-center gap-1">
                                <i data-lucide="pencil" class="w-3 h-3"></i>
                                Sửa mã giảm giá
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- FINANCIAL SUMMARY -->
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border-t-4 border-accent">
                <h2 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-4 lg:mb-5">Thanh toán</h2>

                <div class="space-y-3 mb-4 lg:mb-6">
                    <div class="flex justify-between">
                        <span class="text-xs lg:text-sm text-primary-500">Tổng tiền tour</span>
                        <span class="font-semibold text-primary-700 text-sm lg:text-base"><?= format_currency($booking['total_amount']) ?></span>
                    </div>
                    <div class="flex justify-between text-success-text">
                        <span class="text-xs lg:text-sm text-primary-500">Giảm giá</span>
                        <span class="font-semibold text-sm lg:text-base">-<?= format_currency($booking['discount_amount']) ?></span>
                    </div>
                    <div class="flex justify-between text-lg lg:text-xl font-bold text-primary-700 border-t border-primary-100 pt-2">
                        <span>Thành tiền</span>
                        <span><?= format_currency($booking['final_amount']) ?></span>
                    </div>

                    <div class="bg-primary-50 p-3 lg:p-4 rounded-2xl border border-primary-100">
                        <div class="flex justify-between mb-1">
                            <span class="text-xs lg:text-sm text-primary-500">Đã thanh toán</span>
                            <span
                                class="font-bold text-success-text text-sm lg:text-base"><?= format_currency($booking['paid_amount']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs lg:text-sm text-primary-500">Còn lại</span>
                            <span
                                class="font-bold text-danger-text text-sm lg:text-base"><?= format_currency($booking['remaining_amount']) ?></span>
                        </div>
                        <!-- Progress Bar -->
                        <?php
                        $percent = $booking['final_amount'] > 0 ? ($booking['paid_amount'] / $booking['final_amount']) * 100 : 0;
                        ?>
                        <div class="w-full bg-primary-100 rounded-full h-2.5 mt-2">
                            <div class="bg-success h-2.5 rounded-full transition-all" style="width: <?= $percent ?>%"></div>
                        </div>
                    </div>
                </div>

                <?php if ($booking['remaining_amount'] > 0 && !in_array($booking['payment_status'], ['cancelled', 'rejected', 'refunded'])): ?>
                    <button onclick="openModal('paymentModal')"
                        class="w-full py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white font-bold rounded-xl shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        Thêm thanh toán
                    </button>
                <?php endif; ?>
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
                                        class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded capitalize"><?= $pay['payment_method'] ?></span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span><?= date('d/m/Y', strtotime($pay['payment_date'])) ?></span>
                                    <span><?= $pay['creator_name'] ?></span>
                                </div>
                                <?php if ($pay['notes']): ?>
                                    <p class="text-xs text-gray-400 mt-1 italic"><?= $pay['notes'] ?></p>
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
        <h3 class="text-lg font-bold mb-4">Thêm thanh toán mới</h3>
        <form action="?act=admin&module=bookings&action=storePayment" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Số tiền (VNĐ)</label>
                    <input type="number" name="amount" value="<?= $booking['remaining_amount'] ?>"
                        max="<?= $booking['remaining_amount'] ?>" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Phương thức</label>
                    <select name="payment_method" class="w-full border rounded px-3 py-2">
                        <option value="cash">Tiền mặt</option>
                        <option value="bank_transfer">Chuyển khoản</option>
                        <option value="credit_card">Thẻ tín dụng</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Loại thanh toán</label>
                    <select name="payment_type" class="w-full border rounded px-3 py-2">
                        <option value="deposit">Đặt cọc</option>
                        <option value="installment">Thanh toán đợt</option>
                        <option value="full">Thanh toán hết</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Ghi chú</label>
                    <textarea name="notes" class="w-full border rounded px-3 py-2"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal('paymentModal')"
                    class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Lưu thanh
                    toán</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Discount Modal -->
<div id="discountModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-lg font-bold mb-4">Áp dụng mã giảm giá</h3>
        <form action="?act=admin&module=bookings&action=applyDiscount" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Mã giảm giá (Tùy chọn)</label>
                    <input type="text" name="discount_code" 
                        value="<?= htmlspecialchars($booking['discount_code'] ?? '') ?>"
                        placeholder="Nhập mã giảm giá (nếu có)"
                        class="w-full px-3 py-2 border rounded">
                    <p class="text-xs text-gray-500 mt-1">Có thể để trống nếu chỉ giảm trực tiếp</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Số tiền giảm (VNĐ) <span class="text-red-500">*</span></label>
                    <input type="number" name="discount_amount" 
                        value="<?= $booking['discount_amount'] ?? 0 ?>"
                        min="0" max="<?= $booking['total_amount'] ?>"
                        step="1000"
                        class="w-full px-3 py-2 border rounded" required>
                    <p class="text-xs text-gray-500 mt-1">
                        Tối đa: <?= format_currency($booking['total_amount']) ?>
                    </p>
                </div>
            </div>
            
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal('discountModal')"
                    class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Áp dụng
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Cancel Modal -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-lg font-bold mb-4 text-red-600">Xác nhận Hủy Booking</h3>
        <div class="bg-yellow-50 p-3 rounded mb-4 text-sm text-yellow-800">
            Hệ thống sẽ tự động tính phí hủy dựa trên ngày khởi hành
            (<?= date('d/m/Y', strtotime($booking['start_date'])) ?>).
        </div>
        <form action="?act=admin&module=bookings&action=changeStatus" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $booking['id'] ?>">
            <input type="hidden" name="action" value="cancel">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Lý do hủy <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" class="w-full border rounded px-3 py-2" required
                        placeholder="Khách bận, Sai thông tin..."></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal('cancelModal')"
                    class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Không hủy</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Xác nhận
                    Hủy</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-lg font-bold mb-4">Từ chối Booking</h3>
        <form action="?act=admin&module=bookings&action=changeStatus" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $booking['id'] ?>">
            <input type="hidden" name="action" value="reject">

            <div>
                <label class="block text-sm font-medium mb-1">Lý do từ chối</label>
                <textarea name="reason" rows="3" class="w-full border rounded px-3 py-2" required></textarea>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal('rejectModal')"
                    class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Từ
                    chối</button>
            </div>
        </form>
    </div>
</div>

<!-- 4. Add Passenger Modal -->
<div id="addPassengerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-lg font-bold mb-4">Thêm khách hàng vào Booking</h3>
        <form action="?act=admin&module=bookings&action=addPassengerToBooking" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Chọn khách hàng <span
                            class="text-red-500">*</span></label>
                    <select name="customer_id" required class="w-full border rounded px-3 py-2">
                        <option value="">-- Chọn khách hàng --</option>
                        <?php foreach ($availableCustomers as $customer): ?>
                            <?php
                            // Skip if customer already in booking
                            $alreadyInBooking = false;
                            foreach ($booking['passengers'] ?? [] as $p) {
                                if ($p['customer_id'] == $customer['id']) {
                                    $alreadyInBooking = true;
                                    break;
                                }
                            }
                            if ($alreadyInBooking)
                                continue;
                            ?>
                            <option value="<?= $customer['id'] ?>">
                                <?= htmlspecialchars($customer['full_name']) ?> -
                                <?= htmlspecialchars($customer['phone'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Loại <span class="text-red-500">*</span></label>
                    <select name="age_type" required class="w-full border rounded px-3 py-2">
                        <option value="adult">Người lớn</option>
                        <option value="child">Trẻ em</option>
                        <option value="infant">Em bé</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_primary" value="1" class="mr-2">
                        <span class="text-sm">Đánh dấu là khách chính (người đặt tour)</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1">Lưu ý: Booking chỉ có thể có 1 khách chính</p>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal('addPassengerModal')"
                    class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Thêm
                    khách</button>
            </div>
        </form>
    </div>
</div>

<!-- 5. Add Service Modal -->
<div id="addServiceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold mb-4">Thêm dịch vụ vào Booking</h3>
        <form action="?act=admin&module=bookings&action=storeBookingService" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Dịch vụ <span
                                class="text-red-500">*</span></label>
                        <select name="service_id" id="add_service_id" required
                            onchange="updateServiceProvider(this.value)" class="w-full border rounded px-3 py-2">
                            <option value="">-- Chọn dịch vụ --</option>
                            <?php foreach ($availableServicesList as $svc): ?>
                                <option value="<?= $svc['id'] ?>" data-provider="<?= $svc['service_provider_id'] ?? '' ?>"
                                    data-unit="<?= htmlspecialchars($svc['unit'] ?? '') ?>" data-price="0">
                                    <?= htmlspecialchars($svc['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nhà cung cấp</label>
                        <select name="service_provider_id" id="add_service_provider_id"
                            class="w-full border rounded px-3 py-2">
                            <option value="">-- Tự động (từ dịch vụ) --</option>
                            <?php foreach ($serviceProviders as $provider): ?>
                                <option value="<?= $provider['id'] ?>">
                                    <?= htmlspecialchars($provider['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Số lượng <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="quantity" id="add_service_quantity" value="1" min="1" required
                            onchange="calculateServiceTotal()" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Đơn giá (VNĐ) <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="unit_price" id="add_service_unit_price" value="0" min="0" step="1000"
                            required onchange="calculateServiceTotal()"
                            class="w-full border rounded px-3 py-2 text-right">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Thành tiền</label>
                        <input type="text" id="add_service_total_display" value="0 đ" readonly
                            class="w-full border rounded px-3 py-2 text-right font-bold bg-gray-50">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Ghi chú</label>
                    <textarea name="notes" rows="2" class="w-full border rounded px-3 py-2"
                        placeholder="Ghi chú về dịch vụ..."></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal('addServiceModal')"
                    class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Thêm dịch
                    vụ</button>
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

    function updateServiceProvider(serviceId) {
        const serviceSelect = document.getElementById('add_service_id');
        const providerSelect = document.getElementById('add_service_provider_id');
        const unitPriceInput = document.getElementById('add_service_unit_price');

        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            // Auto-fill service provider
            const providerId = selectedOption.dataset.provider || '';
            if (providerId) {
                providerSelect.value = providerId;
            }

            // Auto-fill unit price if available
            const price = selectedOption.dataset.price || 0;
            if (price > 0) {
                unitPriceInput.value = price;
                calculateServiceTotal();
            }
        } else {
            providerSelect.value = '';
            unitPriceInput.value = 0;
            calculateServiceTotal();
        }
    }

    function calculateServiceTotal() {
        const quantity = parseFloat(document.getElementById('add_service_quantity').value) || 0;
        const unitPrice = parseFloat(document.getElementById('add_service_unit_price').value) || 0;
        const total = quantity * unitPrice;

        document.getElementById('add_service_total_display').value =
            new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(total);
    }
</script>