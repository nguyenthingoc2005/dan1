<?php
/**
 * ==============================================================================
 * STAFF DASHBOARD VIEW
 * ==============================================================================
 */
?>

<!-- Stats Grid - Responsive -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8">
    <!-- My Tours -->
    <div class="bg-panel rounded-2xl p-4 lg:p-6 shadow-sm border border-primary-100">
        <div class="flex items-center justify-between mb-3 lg:mb-4">
            <h3 class="text-xs lg:text-sm text-primary-500 font-medium">Tours Của Tôi</h3>
            <i data-lucide="map-pin" class="w-6 h-6 lg:w-8 lg:h-8 text-accent"></i>
        </div>
        <div class="text-2xl lg:text-3xl font-bold text-accent">
            <?php echo number_format($stats['my_tours'] ?? 0); ?>
        </div>
        <p class="text-xs lg:text-sm text-primary-500 mt-2">Đã tạo</p>
    </div>

    <!-- My Bookings -->
    <div class="bg-panel rounded-2xl p-4 lg:p-6 shadow-sm border border-primary-100">
        <div class="flex items-center justify-between mb-3 lg:mb-4">
            <h3 class="text-xs lg:text-sm text-primary-500 font-medium">Booking Của Tôi</h3>
            <i data-lucide="calendar-check" class="w-6 h-6 lg:w-8 lg:h-8 text-success-text"></i>
        </div>
        <div class="text-2xl lg:text-3xl font-bold text-success-text">
            <?php echo number_format($stats['my_bookings'] ?? 0); ?>
        </div>
        <p class="text-xs lg:text-sm text-primary-500 mt-2">Đã tạo</p>
    </div>

    <!-- My Customers -->
    <div class="bg-panel rounded-2xl p-4 lg:p-6 shadow-sm border border-primary-100">
        <div class="flex items-center justify-between mb-3 lg:mb-4">
            <h3 class="text-xs lg:text-sm text-primary-500 font-medium">Khách Hàng</h3>
            <i data-lucide="users" class="w-6 h-6 lg:w-8 lg:h-8 text-info"></i>
        </div>
        <div class="text-2xl lg:text-3xl font-bold text-info-text">
            <?php echo number_format($stats['my_customers'] ?? 0); ?>
        </div>
        <p class="text-xs lg:text-sm text-primary-500 mt-2">Trong hệ thống</p>
    </div>
</div>

<!-- Action Buttons - Responsive -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6 lg:mb-8">
    <a href="<?php echo BASE_URL; ?>/staff/tours/create"
        class="bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white p-4 lg:p-6 rounded-2xl transition-all text-center shadow-sm">
        <i data-lucide="plus-circle" class="w-8 h-8 lg:w-10 lg:h-10 mx-auto mb-2"></i>
        <div class="font-bold text-sm lg:text-base">Tạo Tour Mới</div>
        <div class="text-xs lg:text-sm text-white/80 mt-1">Khách hàng chưa có tour?</div>
    </a>

    <a href="<?php echo BASE_URL; ?>/staff/bookings/create"
        class="bg-success hover:opacity-90 text-white p-4 lg:p-6 rounded-2xl transition-all text-center shadow-sm">
        <i data-lucide="calendar-check" class="w-8 h-8 lg:w-10 lg:h-10 mx-auto mb-2"></i>
        <div class="font-bold text-sm lg:text-base">Tạo Booking</div>
        <div class="text-xs lg:text-sm text-white/80 mt-1">Ghi nhận đặt tour</div>
    </a>

    <a href="<?php echo BASE_URL; ?>/staff/customers/import"
        class="bg-accent-light hover:opacity-90 text-white p-4 lg:p-6 rounded-2xl transition-all text-center shadow-sm">
        <i data-lucide="download" class="w-8 h-8 lg:w-10 lg:h-10 mx-auto mb-2"></i>
        <div class="font-bold text-sm lg:text-base">Import Khách Hàng</div>
        <div class="text-xs lg:text-sm text-white/80 mt-1">Thêm hàng loạt từ Excel</div>
    </a>
</div>

<!-- My Recent Tours - Responsive -->
<div class="bg-panel rounded-2xl shadow-sm border border-primary-100 mb-6 lg:mb-8 overflow-hidden">
    <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100 flex items-center justify-between">
        <h2 class="text-base lg:text-lg font-bold text-primary-700">Tours Gần Đây Của Tôi</h2>
        <a href="<?php echo BASE_URL; ?>/staff/tours"
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
                        Số Ngày</th>
                    <th
                        class="px-3 lg:px-6 py-3 lg:py-4 text-right text-primary-700 font-semibold text-xs uppercase tracking-wider">
                        Giá</th>
                    <th
                        class="px-3 lg:px-6 py-3 lg:py-4 text-left text-primary-700 font-semibold text-xs uppercase tracking-wider">
                        Trạng thái</th>
                    <th
                        class="px-3 lg:px-6 py-3 lg:py-4 text-left text-primary-700 font-semibold text-xs uppercase tracking-wider">
                        Ngày Tạo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($my_tours ?? []) as $tour): ?>
                    <tr class="border-b border-primary-100 hover:bg-primary-50 transition-colors">
                        <td class="px-3 lg:px-6 py-3 lg:py-4 text-primary-700 font-semibold text-sm">
                            <?php echo sanitize(substr($tour['name'], 0, 40)); ?>
                        </td>
                        <td class="px-3 lg:px-6 py-3 lg:py-4 text-primary-700 text-sm">
                            <?php echo $tour['duration_days']; ?> ngày
                        </td>
                        <td class="px-3 lg:px-6 py-3 lg:py-4 text-right font-semibold text-primary-700 text-sm">
                            <?php echo format_currency($tour['adult_price']); ?>
                        </td>
                        <td class="px-3 lg:px-6 py-3 lg:py-4">
                            <?php
                            $status = $tour['status'] ?? 'draft';
                            $statusColors = [
                                'pending' => 'bg-warning-bg text-warning-text',
                                'active' => 'bg-success-bg text-success-text',
                                'rejected' => 'bg-danger-bg text-danger-text',
                                'draft' => 'bg-primary-100 text-primary-500',
                                'inactive' => 'bg-primary-300 text-primary-700'
                            ];
                            $statusTexts = [
                                'pending' => 'Chờ duyệt',
                                'active' => 'Hoạt động',
                                'rejected' => 'Từ chối',
                                'draft' => 'Nháp',
                                'inactive' => 'Đã ẩn'
                            ];
                            ?>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold <?= $statusColors[$status] ?? 'bg-primary-100 text-primary-500' ?>">
                                <?= $statusTexts[$status] ?? $status ?>
                            </span>
                        </td>
                        <td class="px-3 lg:px-6 py-3 lg:py-4 text-primary-700 text-xs lg:text-sm">
                            <?php echo format_date($tour['created_at'], 'd/m/Y'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($my_tours)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-primary-500">
                            Bạn chưa tạo tour nào. <a href="<?php echo BASE_URL; ?>/staff/tours/create"
                                class="text-accent hover:text-accent-hover font-semibold">Tạo tour ngay →</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>