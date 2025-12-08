<?php
/**
 * ==============================================================================
 * ADMIN DASHBOARD VIEW - Comprehensive Overview
 * ==============================================================================
 */
?>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- Overview Stats Grid -->
<div class="mb-6 lg:mb-8">
    <h1 class="text-2xl lg:text-3xl font-bold text-primary-700 mb-4 lg:mb-6">Tổng Quan Hệ Thống</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Total Revenue -->
        <div class="bg-panel rounded-2xl p-4 lg:p-6 shadow-sm border border-primary-100">
            <div class="flex items-center justify-between mb-3 lg:mb-4">
                <h3 class="text-xs lg:text-sm text-primary-500 font-medium">Tổng Doanh Thu</h3>
                VND
            </div>
            <div class="text-2xl lg:text-3xl font-bold text-success-text">
                <?php echo format_currency($stats['total_revenue'] ?? 0); ?>
            </div>
            <p class="text-xs lg:text-sm text-primary-500 mt-2">
                Tháng này: <span
                    class="font-semibold text-success-text"><?php echo format_currency($stats['month_revenue'] ?? 0); ?></span>
            </p>
        </div>

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
                | Chờ: <span
                    class="font-semibold text-warning-text"><?php echo number_format($stats['pending_bookings'] ?? 0); ?></span>
            </p>
        </div>

        <!-- Total Customers -->
        <div class="bg-panel rounded-2xl p-4 lg:p-6 shadow-sm border border-primary-100">
            <div class="flex items-center justify-between mb-3 lg:mb-4">
                <h3 class="text-xs lg:text-sm text-primary-500 font-medium">Tổng Khách Hàng</h3>
                <i data-lucide="users" class="w-6 h-6 lg:w-8 lg:h-8 text-info-DEFAULT"></i>
            </div>
            <div class="text-2xl lg:text-3xl font-bold text-primary-700">
                <?php echo number_format($stats['total_customers'] ?? 0); ?>
            </div>
            <p class="text-xs lg:text-sm text-primary-500 mt-2">Khách hàng trong hệ thống</p>
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
            <p class="text-xs lg:text-sm text-primary-500 mt-2">
                Chờ duyệt: <span
                    class="font-semibold text-warning-text"><?php echo number_format($stats['pending_tours'] ?? 0); ?></span>
            </p>
        </div>
    </div>
</div>


<!-- Booking Status Chart -->
<div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6 mb-6 lg:mb-8">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg lg:text-xl font-bold text-primary-700">Phân Bổ Trạng Thái Booking</h2>
        <i data-lucide="pie-chart" class="w-5 h-5 text-accent"></i>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
            <canvas id="bookingStatusChart" height="300"></canvas>
        </div>
        <div class="flex flex-col justify-center">
            <?php
            $status_labels = [
                'unpaid' => 'Chưa thanh toán',
                'partial' => 'Thanh toán một phần',
                'paid' => 'Đã thanh toán',
                'cancelled' => 'Đã hủy',
                'refunded' => 'Đã hoàn tiền',
                'rejected' => 'Từ chối'
            ];
            $status_colors = [
                'unpaid' => '#FFB547',
                'partial' => '#6AD2FF',
                'paid' => '#01B574',
                'cancelled' => '#EE5D50',
                'refunded' => '#A3AED0',
                'rejected' => '#E31A1A'
            ];
            ?>
            <div class="space-y-3">
                <?php foreach (($booking_status ?? []) as $status): ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded-full mr-3"
                                style="background-color: <?php echo $status_colors[$status['payment_status']] ?? '#A3AED0'; ?>">
                            </div>
                            <span
                                class="text-sm text-primary-700"><?php echo $status_labels[$status['payment_status']] ?? $status['payment_status']; ?></span>
                        </div>
                        <span
                            class="text-sm font-bold text-primary-700"><?php echo number_format($status['count']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Sections Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 mb-6 lg:mb-8">
    <!-- KHÁCH HÀNG & BOOKING Section -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100 bg-gradient-to-r from-info-bg to-primary-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i data-lucide="users" class="w-5 h-5 text-info-DEFAULT mr-2"></i>
                    <h2 class="text-lg lg:text-xl font-bold text-primary-700">Khách Hàng & Booking</h2>
                </div>
                <a href="<?php echo BASE_URL; ?>/?act=admin&module=bookings"
                    class="text-xs lg:text-sm text-accent hover:text-accent-hover font-semibold">
                    Xem tất cả →
                </a>
            </div>
        </div>
        <div class="p-4 lg:p-6">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-primary-50 rounded-xl p-4">
                    <div class="text-xs text-primary-500 mb-1">Tổng Khách Hàng</div>
                    <div class="text-2xl font-bold text-primary-700">
                        <?php echo number_format($stats['total_customers'] ?? 0); ?>
                    </div>
                </div>
                <div class="bg-primary-50 rounded-xl p-4">
                    <div class="text-xs text-primary-500 mb-1">Booking Chờ</div>
                    <div class="text-2xl font-bold text-warning-text">
                        <?php echo number_format($stats['pending_bookings'] ?? 0); ?>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-primary-100 bg-primary-50">
                            <th class="px-3 py-2 text-left text-primary-700 font-semibold text-xs">Mã Booking</th>
                            <th class="px-3 py-2 text-left text-primary-700 font-semibold text-xs">Khách</th>
                            <th class="px-3 py-2 text-right text-primary-700 font-semibold text-xs">Số Tiền</th>
                            <th class="px-3 py-2 text-left text-primary-700 font-semibold text-xs">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recent_bookings)): ?>
                            <?php foreach (array_slice($recent_bookings, 0, 5) as $booking): ?>
                                <?php if (!empty($booking['booking_code'])): ?>
                                    <tr class="border-b border-primary-100 hover:bg-primary-50">
                                        <td class="px-3 py-2">
                                            <span
                                                class="font-mono text-accent font-semibold text-xs"><?php echo sanitize($booking['booking_code'] ?? ''); ?></span>
                                        </td>
                                        <td class="px-3 py-2 text-primary-700 text-xs">
                                            <?php echo sanitize(substr($booking['customer_name'] ?? 'N/A', 0, 20)); ?>
                                        </td>
                                        <td class="px-3 py-2 text-right font-semibold text-primary-700 text-xs">
                                            <?php echo format_currency($booking['final_amount'] ?? 0); ?>
                                        </td>
                                        <td class="px-3 py-2">
                                            <span
                                                class="px-2 py-1 rounded-full text-xs font-bold <?php echo get_payment_status_color($booking['payment_status'] ?? 'unpaid'); ?>">
                                                <?php echo payment_status_text($booking['payment_status'] ?? 'unpaid'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-primary-500 text-xs">Chưa có booking nào
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TOURS Section -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div
            class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100 bg-gradient-to-r from-accent-gradient-from/10 to-accent-gradient-to/10">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i data-lucide="map-pin" class="w-5 h-5 text-accent mr-2"></i>
                    <h2 class="text-lg lg:text-xl font-bold text-primary-700">Tours</h2>
                </div>
                <a href="<?php echo BASE_URL; ?>/?act=admin&module=tours"
                    class="text-xs lg:text-sm text-accent hover:text-accent-hover font-semibold">
                    Xem tất cả →
                </a>
            </div>
        </div>
        <div class="p-4 lg:p-6">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-primary-50 rounded-xl p-4">
                    <div class="text-xs text-primary-500 mb-1">Tours Hoạt động</div>
                    <div class="text-2xl font-bold text-accent">
                        <?php echo number_format($stats['active_tours'] ?? 0); ?>
                    </div>
                </div>
                <div class="bg-primary-50 rounded-xl p-4">
                    <div class="text-xs text-primary-500 mb-1">Chờ Duyệt</div>
                    <div class="text-2xl font-bold text-warning-text">
                        <?php echo number_format($stats['pending_tours'] ?? 0); ?>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-primary-700 mb-2">Top Tours Doanh Thu</h3>
                <div class="space-y-2">
                    <?php if (!empty($top_tours)): ?>
                        <?php foreach ($top_tours as $tour): ?>
                            <?php if (!empty($tour['name'])): ?>
                                <div class="flex items-center justify-between bg-primary-50 rounded-xl p-3">
                                    <div class="flex-1">
                                        <div class="text-sm font-semibold text-primary-700">
                                            <?php echo sanitize(substr($tour['name'] ?? 'N/A', 0, 30)); ?>
                                        </div>
                                        <div class="text-xs text-primary-500">
                                            <?php echo number_format($tour['booking_count'] ?? 0); ?> booking
                                        </div>
                                    </div>
                                    <div class="text-sm font-bold text-success-text">
                                        <?php echo format_currency($tour['revenue'] ?? 0); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-xs text-primary-500 text-center py-4">Chưa có dữ liệu</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- NHÂN VIÊN & HƯỚNG DẪN VIÊN Section -->
<div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden mb-6 lg:mb-8">
    <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100 bg-gradient-to-r from-success-bg to-primary-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i data-lucide="user-check" class="w-5 h-5 text-success-text mr-2"></i>
                <h2 class="text-lg lg:text-xl font-bold text-primary-700">Nhân Viên & Hướng Dẫn Viên</h2>
            </div>
            <a href="<?php echo BASE_URL; ?>/?act=admin&module=users"
                class="text-xs lg:text-sm text-accent hover:text-accent-hover font-semibold">
                Xem tất cả →
            </a>
        </div>
    </div>
    <div class="p-4 lg:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-primary-50 rounded-xl p-4">
                <div class="text-xs text-primary-500 mb-1">Tổng Nhân Viên</div>
                <div class="text-2xl font-bold text-primary-700">
                    <?php echo number_format($stats['total_staff'] ?? 0); ?>
                </div>
            </div>
            <div class="bg-primary-50 rounded-xl p-4">
                <div class="text-xs text-primary-500 mb-1">Tổng HDV</div>
                <div class="text-2xl font-bold text-primary-700">
                    <?php echo number_format($stats['total_guides'] ?? 0); ?>
                </div>
            </div>
            <div class="bg-primary-50 rounded-xl p-4">
                <div class="text-xs text-primary-500 mb-1">Tổng Tài Xế</div>
                <div class="text-2xl font-bold text-primary-700">
                    <?php echo number_format($stats['total_drivers'] ?? 0); ?>
                </div>
            </div>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-primary-700 mb-3">Top Nhân Viên Theo Booking</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-primary-100 bg-primary-50">
                            <th class="px-3 py-2 text-left text-primary-700 font-semibold text-xs">Nhân Viên</th>
                            <th class="px-3 py-2 text-center text-primary-700 font-semibold text-xs">Số Booking</th>
                            <th class="px-3 py-2 text-right text-primary-700 font-semibold text-xs">Tổng Giá Trị</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($top_staff)): ?>
                            <?php foreach ($top_staff as $staff): ?>
                                <?php if (!empty($staff['full_name'])): ?>
                                    <tr class="border-b border-primary-100 hover:bg-primary-50">
                                        <td class="px-3 py-2 text-primary-700 text-xs">
                                            <?php echo sanitize($staff['full_name'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="px-3 py-2 text-center font-semibold text-primary-700 text-xs">
                                            <?php echo number_format($staff['booking_count'] ?? 0); ?>
                                        </td>
                                        <td class="px-3 py-2 text-right font-semibold text-primary-700 text-xs">
                                            <?php echo format_currency($staff['total_value'] ?? 0); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="px-3 py-4 text-center text-primary-500 text-xs">Chưa có dữ liệu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- VẬN HÀNH & LỊCH TRÌNH Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 mb-6 lg:mb-8">
    <!-- Lịch Trình -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div
            class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100 bg-gradient-to-r from-warning-bg to-primary-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i data-lucide="calendar" class="w-5 h-5 text-warning-text mr-2"></i>
                    <h2 class="text-lg lg:text-xl font-bold text-primary-700">Lịch Trình Tour</h2>
                </div>
                <a href="<?php echo BASE_URL; ?>/?act=admin&module=schedules"
                    class="text-xs lg:text-sm text-accent hover:text-accent-hover font-semibold">
                    Xem tất cả →
                </a>
            </div>
        </div>
        <div class="p-4 lg:p-6">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-primary-50 rounded-xl p-4">
                    <div class="text-xs text-primary-500 mb-1">Đang Diễn Ra</div>
                    <div class="text-2xl font-bold text-success-text">
                        <?php echo number_format($stats['active_schedules'] ?? 0); ?>
                    </div>
                </div>
                <div class="bg-primary-50 rounded-xl p-4">
                    <div class="text-xs text-primary-500 mb-1">Sắp Tới (7 ngày)</div>
                    <div class="text-2xl font-bold text-warning-text">
                        <?php echo number_format($stats['upcoming_schedules'] ?? 0); ?>
                    </div>
                </div>
            </div>
            <div class="space-y-2">
                <?php if (!empty($upcoming_schedules_list)): ?>
                    <?php foreach ($upcoming_schedules_list as $schedule): ?>
                        <?php if (!empty($schedule['tour_name']) || !empty($schedule['start_date'])): ?>
                            <div class="bg-primary-50 rounded-xl p-3">
                                <div class="text-sm font-semibold text-primary-700 mb-1">
                                    <?php echo sanitize($schedule['tour_name'] ?? 'N/A'); ?>
                                </div>
                                <div class="flex items-center justify-between text-xs text-primary-500">
                                    <span>
                                        <?php if (!empty($schedule['start_date'])): ?>
                                            <?php echo format_date($schedule['start_date'], 'd/m/Y'); ?>
                                        <?php endif; ?>
                                        <?php if (!empty($schedule['end_date'])): ?>
                                            - <?php echo format_date($schedule['end_date'], 'd/m/Y'); ?>
                                        <?php endif; ?>
                                    </span>
                                    <span
                                        class="font-semibold text-primary-700"><?php echo number_format($schedule['booking_count'] ?? 0); ?>
                                        booking</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-xs text-primary-500 text-center py-4">Chưa có lịch trình sắp tới</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Xe & Tài Xế -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100 bg-gradient-to-r from-info-bg to-primary-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i data-lucide="truck" class="w-5 h-5 text-info-DEFAULT mr-2"></i>
                    <h2 class="text-lg lg:text-xl font-bold text-primary-700">Xe & Tài Xế</h2>
                </div>
                <a href="<?php echo BASE_URL; ?>/?act=admin&module=vehicles"
                    class="text-xs lg:text-sm text-accent hover:text-accent-hover font-semibold">
                    Xem tất cả →
                </a>
            </div>
        </div>
        <div class="p-4 lg:p-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-primary-50 rounded-xl p-4">
                    <div class="text-xs text-primary-500 mb-1">Tổng Xe</div>
                    <div class="text-2xl font-bold text-primary-700">
                        <?php echo number_format($stats['total_vehicles'] ?? 0); ?>
                    </div>
                </div>
                <div class="bg-primary-50 rounded-xl p-4">
                    <div class="text-xs text-primary-500 mb-1">Tổng Tài Xế</div>
                    <div class="text-2xl font-bold text-primary-700">
                        <?php echo number_format($stats['total_drivers'] ?? 0); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tours Chờ Duyệt Section -->
<?php if (!empty($pending_tours)): ?>
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
                        <th class="px-3 lg:px-6 py-3 lg:py-4 text-left text-primary-700 font-semibold text-xs uppercase">Tên
                            Tour</th>
                        <th class="px-3 lg:px-6 py-3 lg:py-4 text-left text-primary-700 font-semibold text-xs uppercase">
                            Người Tạo</th>
                        <th class="px-3 lg:px-6 py-3 lg:py-4 text-left text-primary-700 font-semibold text-xs uppercase">Số
                            Ngày</th>
                        <th class="px-3 lg:px-6 py-3 lg:py-4 text-right text-primary-700 font-semibold text-xs uppercase">
                            Giá</th>
                        <th class="px-3 lg:px-6 py-3 lg:py-4 text-left text-primary-700 font-semibold text-xs uppercase">
                            Ngày Tạo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_tours as $tour): ?>
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
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Booking Status Chart
        const statusCtx = document.getElementById('bookingStatusChart');
        if (statusCtx) {
            const statusData = <?php echo json_encode($booking_status ?? [], JSON_UNESCAPED_UNICODE); ?>;
            if (statusData && statusData.length > 0) {
                const statusLabels = statusData.map(item => {
                    const labels = {
                        'unpaid': 'Chưa thanh toán',
                        'partial': 'Thanh toán một phần',
                        'paid': 'Đã thanh toán',
                        'cancelled': 'Đã hủy',
                        'refunded': 'Đã hoàn tiền',
                        'rejected': 'Từ chối'
                    };
                    return labels[item.payment_status] || item.payment_status;
                });
                const statusValues = statusData.map(item => parseInt(item.count) || 0);
                const statusColors = statusData.map(item => {
                    const colors = {
                        'unpaid': '#FFB547',
                        'partial': '#6AD2FF',
                        'paid': '#01B574',
                        'cancelled': '#EE5D50',
                        'refunded': '#A3AED0',
                        'rejected': '#E31A1A'
                    };
                    return colors[item.payment_status] || '#A3AED0';
                });

                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: statusValues,
                            backgroundColor: statusColors,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        }
    });
</script>