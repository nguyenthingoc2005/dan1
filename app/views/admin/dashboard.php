<?php
/**
 * ==============================================================================
 * ADMIN DASHBOARD VIEW
 * ==============================================================================
 */
?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Total Bookings -->
    <div class="bg-panel rounded-lg p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-slate-600 font-medium">Tổng Booking</h3>
            <span class="text-2xl">📅</span>
        </div>
        <div class="text-3xl font-bold text-primary">
            <?php echo number_format($stats['total_bookings'] ?? 0); ?>
        </div>
        <p class="text-sm text-slate-500 mt-2">
            Đã duyệt: <span
                class="font-bold text-green-600"><?php echo number_format($stats['approved_bookings'] ?? 0); ?></span>
        </p>
    </div>

    <!-- Pending Bookings -->
    <div class="bg-panel rounded-lg p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-slate-600 font-medium">Chờ Duyệt</h3>
            <span class="text-2xl">⏳</span>
        </div>
        <div class="text-3xl font-bold text-yellow-600">
            <?php echo number_format($stats['pending_bookings'] ?? 0); ?>
        </div>
        <p class="text-sm text-slate-500 mt-2">Cần xử lý ngay</p>
    </div>

    <!-- Total Revenue -->
    <div class="bg-panel rounded-lg p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-slate-600 font-medium">Tổng Doanh Thu</h3>
            <span class="text-2xl">💰</span>
        </div>
        <div class="text-3xl font-bold text-green-600">
            <?php echo format_currency($stats['total_revenue'] ?? 0); ?>
        </div>
        <p class="text-sm text-slate-500 mt-2">Đã thanh toán & Thanh toán một phần</p>
    </div>

    <!-- Active Tours -->
    <div class="bg-panel rounded-lg p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-slate-600 font-medium">Tours Hoạt động</h3>
            <span class="text-2xl">✈️</span>
        </div>
        <div class="text-3xl font-bold text-accent">
            <?php echo number_format($stats['active_tours'] ?? 0); ?>
        </div>
        <p class="text-sm text-slate-500 mt-2">Sẵn sàng nhận booking</p>
    </div>

    <!-- Pending Tours -->
    <div class="bg-panel rounded-lg p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-slate-600 font-medium">Tours Chờ Duyệt</h3>
            <span class="text-2xl">📝</span>
        </div>
        <div class="text-3xl font-bold text-yellow-600">
            <?php echo number_format($stats['pending_tours'] ?? 0); ?>
        </div>
        <a href="<?php echo BASE_URL; ?>/?act=admin&module=tours&approval_status=pending"
            class="text-sm text-accent hover:underline mt-2 inline-block">
            → Xem chi tiết
        </a>
    </div>
</div>

<!-- Recent Bookings Section -->
<div class="bg-panel rounded-lg border border-slate-200 mb-8">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
        <h2 class="text-lg font-bold text-primary">Booking Gần Đây</h2>
        <a href="<?php echo BASE_URL; ?>/?act=admin&module=bookings" class="text-accent hover:text-blue-700 text-sm">
            Xem tất cả →
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50">
                    <th class="px-6 py-3 text-left text-slate-700 font-semibold">Mã Booking</th>
                    <th class="px-6 py-3 text-left text-slate-700 font-semibold">Tour</th>
                    <th class="px-6 py-3 text-left text-slate-700 font-semibold">Khách</th>
                    <th class="px-6 py-3 text-left text-slate-700 font-semibold">Ngày Đi</th>
                    <th class="px-6 py-3 text-right text-slate-700 font-semibold">Số Tiền</th>
                    <th class="px-6 py-3 text-left text-slate-700 font-semibold">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($recent_bookings ?? []) as $booking): ?>
                    <tr class="border-b border-slate-200 hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-mono text-slate-700">
                                <?php echo sanitize($booking['booking_code']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-700">
                            <?php echo sanitize(substr($booking['tour_name'], 0, 30)); ?>
                        </td>
                        <td class="px-6 py-4 text-slate-700">
                            <?php echo sanitize($booking['customer_name']); ?>
                        </td>
                        <td class="px-6 py-4 text-slate-700">
                            <?php echo format_date($booking['start_date'], 'd/m/Y'); ?>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-slate-900">
                            <?php echo format_currency($booking['final_amount']); ?>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium <?php echo get_status_color($booking['approval_status']); ?>">
                                <?php echo approval_status_text($booking['approval_status']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($recent_bookings)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                            Chưa có booking nào
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pending Tours Section -->
<div class="bg-panel rounded-lg border border-slate-200">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
        <h2 class="text-lg font-bold text-primary">Tours Chờ Duyệt</h2>
        <a href="<?php echo BASE_URL; ?>/?act=admin&module=tours&approval_status=pending" class="text-accent hover:text-blue-700 text-sm">
            Xem tất cả →
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50">
                    <th class="px-6 py-3 text-left text-slate-700 font-semibold">Tên Tour</th>
                    <th class="px-6 py-3 text-left text-slate-700 font-semibold">Người Tạo</th>
                    <th class="px-6 py-3 text-left text-slate-700 font-semibold">Số Ngày</th>
                    <th class="px-6 py-3 text-right text-slate-700 font-semibold">Giá</th>
                    <th class="px-6 py-3 text-left text-slate-700 font-semibold">Ngày Tạo</th>
                    <th class="px-6 py-3 text-left text-slate-700 font-semibold">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($pending_tours ?? []) as $tour): ?>
                    <tr class="border-b border-slate-200 hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-slate-700 font-medium">
                            <?php echo sanitize(substr($tour['name'], 0, 30)); ?>
                        </td>
                        <td class="px-6 py-4 text-slate-700">
                            <?php echo sanitize($tour['staff_name'] ?? 'N/A'); ?>
                        </td>
                        <td class="px-6 py-4 text-slate-700">
                            <?php echo $tour['duration_days']; ?> ngày
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-slate-900">
                            <?php echo format_currency($tour['adult_price']); ?>
                        </td>
                        <td class="px-6 py-4 text-slate-700 text-sm">
                            <?php echo format_date($tour['created_at'], 'd/m/Y'); ?>
                        </td>
                        <td class="px-6 py-4">
                            <button
                                class="px-3 py-1 bg-accent text-white rounded text-xs hover:bg-blue-600 transition-colors">
                                Duyệt
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($pending_tours)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                            Không có tour nào chờ duyệt
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>