<?php
/**
 * ==============================================================================
 * ADMIN DASHBOARD VIEW
 * ==============================================================================
 */
?>

<!-- Stats Grid - Responsive -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8">
    <!-- Total Bookings -->
    <div class="bg-panel rounded-2xl p-4 lg:p-6 shadow-sm border border-primary-100">
        <div class="flex items-center justify-between mb-3 lg:mb-4">
            <h3 class="text-xs lg:text-sm text-primary-500 font-medium">Tổng Booking</h3>
            <i data-lucide="calendar-check" class="w-6 h-6 lg:w-8 lg:h-8 text-accent"></i>
        </div>
        <div class="text-2xl lg:text-3xl font-bold text-primary-700">
            <?php echo number_format($stats['total_bookings'] ?? 0); ?>
        </div>
        <p class="text-xs lg:text-sm text-primary-500 mt-2">
            Đã duyệt: <span
                class="font-semibold text-success-text"><?php echo number_format($stats['approved_bookings'] ?? 0); ?></span>
        </p>
    </div>

    <!-- Pending Bookings -->
    <div class="bg-panel rounded-2xl p-4 lg:p-6 shadow-sm border border-primary-100">
        <div class="flex items-center justify-between mb-3 lg:mb-4">
            <h3 class="text-xs lg:text-sm text-primary-500 font-medium">Chờ Duyệt</h3>
            <i data-lucide="clock" class="w-6 h-6 lg:w-8 lg:h-8 text-warning"></i>
        </div>
        <div class="text-2xl lg:text-3xl font-bold text-warning-text">
            <?php echo number_format($stats['pending_bookings'] ?? 0); ?>
        </div>
        <p class="text-xs lg:text-sm text-primary-500 mt-2">Cần xử lý ngay</p>
    </div>

    <!-- Total Revenue -->
    <div class="bg-panel rounded-2xl p-4 lg:p-6 shadow-sm border border-primary-100">
        <div class="flex items-center justify-between mb-3 lg:mb-4">
            <h3 class="text-xs lg:text-sm text-primary-500 font-medium">Tổng Doanh Thu</h3>
            <i data-lucide="dollar-sign" class="w-6 h-6 lg:w-8 lg:h-8 text-success-text"></i>
        </div>
        <div class="text-2xl lg:text-3xl font-bold text-success-text">
            <?php echo format_currency($stats['total_revenue'] ?? 0); ?>
        </div>
        <p class="text-xs lg:text-sm text-primary-500 mt-2">Đã thanh toán & Thanh toán một phần</p>
    </div>

    <!-- Active Tours -->
    <div class="bg-panel rounded-2xl p-4 lg:p-6 shadow-sm border border-primary-100">
        <div class="flex items-center justify-between mb-3 lg:mb-4">
            <h3 class="text-xs lg:text-sm text-primary-500 font-medium">Tours Hoạt động</h3>
            <i data-lucide="map-pin" class="w-6 h-6 lg:w-8 lg:h-8 text-accent"></i>
        </div>
        <div class="text-2xl lg:text-3xl font-bold text-accent">
            <?php echo number_format($stats['active_tours'] ?? 0); ?>
        </div>
        <p class="text-xs lg:text-sm text-primary-500 mt-2">Sẵn sàng nhận booking</p>
    </div>

    <!-- Pending Tours -->
    <div class="bg-panel rounded-2xl p-4 lg:p-6 shadow-sm border border-primary-100">
        <div class="flex items-center justify-between mb-3 lg:mb-4">
            <h3 class="text-xs lg:text-sm text-primary-500 font-medium">Tours Chờ Duyệt</h3>
            <i data-lucide="file-text" class="w-6 h-6 lg:w-8 lg:h-8 text-warning"></i>
        </div>
        <div class="text-2xl lg:text-3xl font-bold text-warning-text">
            <?php echo number_format($stats['pending_tours'] ?? 0); ?>
        </div>
        <a href="<?php echo BASE_URL; ?>/?act=admin&module=tours&status=pending"
            class="text-xs lg:text-sm text-accent hover:text-accent-hover font-semibold mt-2 inline-block">
            → Xem chi tiết
        </a>
    </div>
</div>

<!-- Recent Bookings Section - Responsive -->
<div class="bg-panel rounded-2xl shadow-sm border border-primary-100 mb-6 lg:mb-8 overflow-hidden">
    <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100 flex items-center justify-between">
        <h2 class="text-base lg:text-lg font-bold text-primary-700">Booking Gần Đây</h2>
        <a href="<?php echo BASE_URL; ?>/?act=admin&module=bookings"
            class="text-xs lg:text-sm text-accent hover:text-accent-hover font-semibold">
            Xem tất cả →
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[600px]">
            <thead>
                <tr class="border-b border-primary-100 bg-primary-50">
                    <th
                        class="px-3 lg:px-6 py-3 lg:py-4 text-left text-primary-700 font-semibold text-xs uppercase tracking-wider">
                        Mã Booking</th>
                    <th
                        class="px-3 lg:px-6 py-3 lg:py-4 text-left text-primary-700 font-semibold text-xs uppercase tracking-wider">
                        Tour</th>
                    <th
                        class="px-3 lg:px-6 py-3 lg:py-4 text-left text-primary-700 font-semibold text-xs uppercase tracking-wider">
                        Khách</th>
                    <th
                        class="px-3 lg:px-6 py-3 lg:py-4 text-left text-primary-700 font-semibold text-xs uppercase tracking-wider">
                        Ngày Đi</th>
                    <th
                        class="px-3 lg:px-6 py-3 lg:py-4 text-right text-primary-700 font-semibold text-xs uppercase tracking-wider">
                        Số Tiền</th>
                    <th
                        class="px-3 lg:px-6 py-3 lg:py-4 text-left text-primary-700 font-semibold text-xs uppercase tracking-wider">
                        Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($recent_bookings ?? []) as $booking): ?>
                    <tr class="border-b border-primary-100 hover:bg-primary-50 transition-colors">
                        <td class="px-3 lg:px-6 py-3 lg:py-4">
                            <span class="font-mono text-accent font-semibold text-sm">
                                <?php echo sanitize($booking['booking_code']); ?>
                            </span>
                        </td>
                        <td class="px-3 lg:px-6 py-3 lg:py-4 text-primary-700 font-semibold text-sm">
                            <?php echo sanitize(substr($booking['tour_name'], 0, 30)); ?>
                        </td>
                        <td class="px-3 lg:px-6 py-3 lg:py-4 text-primary-700 text-sm">
                            <?php echo sanitize($booking['customer_name']); ?>
                        </td>
                        <td class="px-3 lg:px-6 py-3 lg:py-4 text-primary-700 text-sm">
                            <?php echo format_date($booking['start_date'], 'd/m/Y'); ?>
                        </td>
                        <td class="px-3 lg:px-6 py-3 lg:py-4 text-right font-semibold text-primary-700 text-sm">
                            <?php echo format_currency($booking['final_amount']); ?>
                        </td>
                        <td class="px-3 lg:px-6 py-3 lg:py-4">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold <?php echo get_payment_status_color($booking['payment_status']); ?>">
                                <?php echo payment_status_text($booking['payment_status']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($recent_bookings)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-primary-500">
                            Chưa có booking nào
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pending Tours Section - Responsive -->
<div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
    <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100 flex items-center justify-between">
        <h2 class="text-base lg:text-lg font-bold text-primary-700">Tours Chờ Duyệt</h2>
        <a href="<?php echo BASE_URL; ?>/?act=admin&module=tours&status=pending"
            class="text-xs lg:text-sm text-accent hover:text-accent-hover font-semibold">
            Xem tất cả →
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[600px]">
            <thead>
                <tr class="border-b border-primary-100 bg-primary-50">
                    <th
                        class="px-3 lg:px-6 py-3 lg:py-4 text-left text-primary-700 font-semibold text-xs uppercase tracking-wider">
                        Tên Tour</th>
                    <th
                        class="px-3 lg:px-6 py-3 lg:py-4 text-left text-primary-700 font-semibold text-xs uppercase tracking-wider">
                        Người Tạo</th>
                    <th
                        class="px-3 lg:px-6 py-3 lg:py-4 text-left text-primary-700 font-semibold text-xs uppercase tracking-wider">
                        Số Ngày</th>
                    <th
                        class="px-3 lg:px-6 py-3 lg:py-4 text-right text-primary-700 font-semibold text-xs uppercase tracking-wider">
                        Giá</th>
                    <th
                        class="px-3 lg:px-6 py-3 lg:py-4 text-left text-primary-700 font-semibold text-xs uppercase tracking-wider">
                        Ngày Tạo</th>
                    <th
                        class="px-3 lg:px-6 py-3 lg:py-4 text-left text-primary-700 font-semibold text-xs uppercase tracking-wider">
                        Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($pending_tours ?? []) as $tour): ?>
                    <tr class="border-b border-primary-100 hover:bg-primary-50 transition-colors">
                        <td class="px-3 lg:px-6 py-3 lg:py-4 text-primary-700 font-semibold text-sm">
                            <?php echo sanitize(substr($tour['name'], 0, 30)); ?>
                        </td>
                        <td class="px-3 lg:px-6 py-3 lg:py-4 text-primary-700 text-sm">
                            <?php echo sanitize($tour['staff_name'] ?? 'N/A'); ?>
                        </td>
                        <td class="px-3 lg:px-6 py-3 lg:py-4 text-primary-700 text-sm">
                            <?php echo $tour['duration_days']; ?> ngày
                        </td>
                        <td class="px-3 lg:px-6 py-3 lg:py-4 text-right font-semibold text-primary-700 text-sm">
                            <?php echo format_currency($tour['adult_price']); ?>
                        </td>
                        <td class="px-3 lg:px-6 py-3 lg:py-4 text-primary-700 text-xs lg:text-sm">
                            <?php echo format_date($tour['created_at'], 'd/m/Y'); ?>
                        </td>
                        <td class="px-3 lg:px-6 py-3 lg:py-4">
                            <button
                                class="px-3 py-1.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl text-xs font-semibold transition-all">
                                Duyệt
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($pending_tours)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-primary-500">
                            Không có tour nào chờ duyệt
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>