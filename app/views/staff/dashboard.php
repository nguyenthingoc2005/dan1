<?php
/**
 * ==============================================================================
 * STAFF DASHBOARD VIEW
 * ==============================================================================
 */
?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- My Tours -->
    <div class="bg-panel rounded-lg p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-slate-600 font-medium">Tours Của Tôi</h3>
            <span class="text-2xl">✈️</span>
        </div>
        <div class="text-3xl font-bold text-accent">
            <?php echo number_format($stats['my_tours'] ?? 0); ?>
        </div>
        <p class="text-sm text-slate-500 mt-2">Đã tạo</p>
    </div>

    <!-- My Bookings -->
    <div class="bg-panel rounded-lg p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-slate-600 font-medium">Booking Của Tôi</h3>
            <span class="text-2xl">📅</span>
        </div>
        <div class="text-3xl font-bold text-green-600">
            <?php echo number_format($stats['my_bookings'] ?? 0); ?>
        </div>
        <p class="text-sm text-slate-500 mt-2">Đã tạo</p>
    </div>

    <!-- My Customers -->
    <div class="bg-panel rounded-lg p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-slate-600 font-medium">Khách Hàng</h3>
            <span class="text-2xl">👥</span>
        </div>
        <div class="text-3xl font-bold text-blue-600">
            <?php echo number_format($stats['my_customers'] ?? 0); ?>
        </div>
        <p class="text-sm text-slate-500 mt-2">Trong hệ thống</p>
    </div>
</div>

<!-- Action Buttons -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <a href="<?php echo BASE_URL; ?>/staff/tours/create"
        class="bg-accent text-white p-4 rounded-lg hover:bg-blue-600 transition-colors text-center">
        <div class="text-2xl mb-2">➕</div>
        <div class="font-bold">Tạo Tour Mới</div>
        <div class="text-sm text-blue-100">Khách hàng chưa có tour?</div>
    </a>

    <a href="<?php echo BASE_URL; ?>/staff/bookings/create"
        class="bg-green-600 text-white p-4 rounded-lg hover:bg-green-700 transition-colors text-center">
        <div class="text-2xl mb-2">📝</div>
        <div class="font-bold">Tạo Booking</div>
        <div class="text-sm text-green-100">Ghi nhận đặt tour</div>
    </a>

    <a href="<?php echo BASE_URL; ?>/staff/customers/import"
        class="bg-purple-600 text-white p-4 rounded-lg hover:bg-purple-700 transition-colors text-center">
        <div class="text-2xl mb-2">📥</div>
        <div class="font-bold">Import Khách Hàng</div>
        <div class="text-sm text-purple-100">Thêm hàng loạt từ Excel</div>
    </a>
</div>

<!-- My Recent Tours -->
<div class="bg-panel rounded-lg border border-slate-200 mb-8">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
        <h2 class="text-lg font-bold text-primary">Tours Gần Đây Của Tôi</h2>
        <a href="<?php echo BASE_URL; ?>/staff/tours" class="text-accent hover:text-blue-700 text-sm">
            Xem tất cả →
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50">
                    <th class="px-6 py-3 text-left text-slate-700 font-semibold">Tên Tour</th>
                    <th class="px-6 py-3 text-left text-slate-700 font-semibold">Số Ngày</th>
                    <th class="px-6 py-3 text-right text-slate-700 font-semibold">Giá</th>
                    <th class="px-6 py-3 text-left text-slate-700 font-semibold">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-slate-700 font-semibold">Ngày Tạo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($my_tours ?? []) as $tour): ?>
                    <tr class="border-b border-slate-200 hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-slate-700 font-medium">
                            <?php echo sanitize(substr($tour['name'], 0, 40)); ?>
                        </td>
                        <td class="px-6 py-4 text-slate-700">
                            <?php echo $tour['duration_days']; ?> ngày
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-slate-900">
                            <?php echo format_currency($tour['adult_price']); ?>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium <?php echo get_status_color($tour['approval_status']); ?>">
                                <?php echo approval_status_text($tour['approval_status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-700 text-sm">
                            <?php echo format_date($tour['created_at'], 'd/m/Y'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($my_tours)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                            Bạn chưa tạo tour nào. <a href="<?php echo BASE_URL; ?>/staff/tours/create"
                                class="text-accent hover:underline">Tạo tour ngay →</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>