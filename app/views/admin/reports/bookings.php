<div class="mx-auto">
    <!-- Page Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Báo cáo Đặt tour</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Thống kê số lượng và xu hướng đặt tour</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Export Excel Button -->
            <a href="?act=admin&module=reports&action=bookings&export=excel&<?= http_build_query($_GET) ?>"
                class="px-4 py-2 bg-success hover:bg-success-hover text-white font-semibold rounded-xl transition-all text-sm flex items-center gap-2 shadow-sm">
                <i data-lucide="download" class="w-4 h-4"></i>
                Xuất Excel
            </a>
            <div class="flex bg-primary-100 p-1 rounded-xl">
                <a href="?act=admin&module=reports&action=revenue"
                    class="px-3 lg:px-4 py-1.5 lg:py-2 text-xs lg:text-sm font-semibold rounded-xl text-primary-500 hover:text-primary-700 hover:bg-primary-50 transition-colors">
                    Doanh thu
                </a>
                <a href="?act=admin&module=reports&action=bookings"
                    class="px-3 lg:px-4 py-1.5 lg:py-2 text-xs lg:text-sm font-semibold rounded-xl shadow-sm bg-panel text-accent">
                    Đặt tour
                </a>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="bg-panel rounded-2xl border border-primary-100 p-4 lg:p-5 mb-4 lg:mb-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm lg:text-base font-semibold text-primary-700 flex items-center gap-2">
                <i data-lucide="filter" class="w-4 h-4"></i>
                Bộ lọc
            </h3>
            <button type="button" onclick="toggleAdvancedFilters()" 
                class="text-xs text-accent hover:text-accent-hover font-semibold">
                <span id="filterToggleText">Hiện thêm</span>
            </button>
        </div>
        <form method="GET" action="" class="grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-4">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="reports">
            <input type="hidden" name="action" value="bookings">

            <!-- Date Range -->
            <div class="lg:col-span-3">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Từ ngày</label>
                <input type="date" name="start_date"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-sm lg:text-base text-primary-700"
                    value="<?= $start_date ?? date('Y-m-01') ?>">
            </div>
            <div class="lg:col-span-3">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Đến ngày</label>
                <input type="date" name="end_date"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-sm lg:text-base text-primary-700"
                    value="<?= $end_date ?? date('Y-m-t') ?>">
            </div>

            <!-- Tour Filter -->
            <div class="lg:col-span-3" id="advancedFilters" style="display: none;">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tour</label>
                <select name="tour_id"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-sm lg:text-base text-primary-700">
                    <option value="">Tất cả tours</option>
                    <?php foreach ($tours ?? [] as $tour): ?>
                        <option value="<?= $tour['id'] ?>" <?= (isset($_GET['tour_id']) && $_GET['tour_id'] == $tour['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tour['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Payment Status Filter -->
            <div class="lg:col-span-3" id="advancedFilters2" style="display: none;">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
                <select name="payment_status"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-sm lg:text-base text-primary-700">
                    <option value="">Tất cả</option>
                    <option value="paid" <?= (isset($_GET['payment_status']) && $_GET['payment_status'] == 'paid') ? 'selected' : '' ?>>Đã thanh toán</option>
                    <option value="partial" <?= (isset($_GET['payment_status']) && $_GET['payment_status'] == 'partial') ? 'selected' : '' ?>>Thanh toán một phần</option>
                    <option value="unpaid" <?= (isset($_GET['payment_status']) && $_GET['payment_status'] == 'unpaid') ? 'selected' : '' ?>>Chưa thanh toán</option>
                    <option value="cancelled" <?= (isset($_GET['payment_status']) && $_GET['payment_status'] == 'cancelled') ? 'selected' : '' ?>>Đã hủy</option>
                    <option value="refunded" <?= (isset($_GET['payment_status']) && $_GET['payment_status'] == 'refunded') ? 'selected' : '' ?>>Đã hoàn tiền</option>
                </select>
            </div>

            <!-- Source Filter -->
            <div class="lg:col-span-3" id="advancedFilters3" style="display: none;">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Nguồn booking</label>
                <select name="source"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-sm lg:text-base text-primary-700">
                    <option value="">Tất cả</option>
                    <option value="phone" <?= (isset($_GET['source']) && $_GET['source'] == 'phone') ? 'selected' : '' ?>>Điện thoại</option>
                    <option value="email" <?= (isset($_GET['source']) && $_GET['source'] == 'email') ? 'selected' : '' ?>>Email</option>
                    <option value="facebook" <?= (isset($_GET['source']) && $_GET['source'] == 'facebook') ? 'selected' : '' ?>>Facebook</option>
                    <option value="zalo" <?= (isset($_GET['source']) && $_GET['source'] == 'zalo') ? 'selected' : '' ?>>Zalo</option>
                    <option value="walk_in" <?= (isset($_GET['source']) && $_GET['source'] == 'walk_in') ? 'selected' : '' ?>>Tại quầy</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="lg:col-span-3 flex items-end gap-2">
                <button type="submit"
                    class="flex-1 px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-600 hover:opacity-90 text-white font-semibold rounded-xl transition-all text-sm lg:text-base flex items-center justify-center gap-2 h-[38px] shadow-sm">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Xem báo cáo
                </button>
                <a href="?act=admin&module=reports&action=bookings"
                    class="px-4 py-2 lg:py-2.5 bg-primary-100 hover:bg-primary-200 text-primary-700 font-semibold rounded-xl transition-all text-sm lg:text-base h-[38px] flex items-center justify-center">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-4 lg:mb-6">
        <!-- Total Bookings -->
        <div class="bg-panel rounded-2xl border border-accent p-4 lg:p-6 shadow-sm relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1 bg-accent"></div>
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-xs font-bold text-accent uppercase tracking-wide mb-1 lg:mb-2">Tổng số booking</div>
                    <div class="text-xl lg:text-2xl font-bold text-primary-700"><?= number_format($total_bookings ?? 0) ?></div>
                </div>
                <div class="w-12 h-12 rounded-full bg-primary-50 flex items-center justify-center">
                    <i data-lucide="calendar-check" class="w-6 h-6 text-accent"></i>
                </div>
            </div>
        </div>

        <!-- Total Passengers -->
        <div class="bg-panel rounded-2xl border border-info p-4 lg:p-6 shadow-sm relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1 bg-info"></div>
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-xs font-bold text-info-text uppercase tracking-wide mb-1 lg:mb-2">Tổng số khách</div>
                    <div class="text-xl lg:text-2xl font-bold text-primary-700"><?= number_format($passengers_stats['total_passengers'] ?? 0) ?></div>
                    <div class="text-xs text-primary-500 mt-1">
                        <?= number_format($passengers_stats['total_adults'] ?? 0) ?> NL, 
                        <?= number_format($passengers_stats['total_children'] ?? 0) ?> TE, 
                        <?= number_format($passengers_stats['total_infants'] ?? 0) ?> EB
                    </div>
                </div>
                <div class="w-12 h-12 rounded-full bg-info-bg flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-info-text"></i>
                </div>
            </div>
        </div>

        <!-- Cancellation Rate -->
        <div class="bg-panel rounded-2xl border border-warning p-4 lg:p-6 shadow-sm relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1 bg-warning"></div>
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-xs font-bold text-warning-text uppercase tracking-wide mb-1 lg:mb-2">Tỷ lệ hủy</div>
                    <div class="text-xl lg:text-2xl font-bold <?= ($cancellation_rate ?? 0) > 20 ? 'text-danger-text' : 'text-warning-text' ?>">
                        <?= number_format($cancellation_rate ?? 0, 2) ?>%
                    </div>
                    <div class="text-xs text-primary-500 mt-1"><?= number_format($cancelled_count ?? 0) ?> booking</div>
                </div>
                <div class="w-12 h-12 rounded-full bg-warning-bg flex items-center justify-center">
                    <i data-lucide="alert-circle" class="w-6 h-6 text-warning-text"></i>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-panel rounded-2xl border border-success p-4 lg:p-6 shadow-sm relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1 bg-success"></div>
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-xs font-bold text-success-text uppercase tracking-wide mb-1 lg:mb-2">Tổng giá trị</div>
                    <div class="text-xl lg:text-2xl font-bold text-primary-700"><?= format_currency($revenue_stats['total_revenue'] ?? 0) ?></div>
                    <div class="text-xs text-primary-500 mt-1">
                        Đã thu: <?= format_currency($revenue_stats['total_paid'] ?? 0) ?>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-full bg-success-bg flex items-center justify-center">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-success-text"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-4 lg:mb-6">
        <!-- Bookings by Month Chart -->
        <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm">
            <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100">
                <h6 class="font-semibold text-primary-700 text-sm lg:text-base">Số lượng booking theo tháng</h6>
            </div>
            <div class="p-4 lg:p-6">
                <canvas id="bookingsByMonthChart" height="200"></canvas>
            </div>
        </div>

        <!-- Bookings by Status Chart -->
        <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm">
            <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100">
                <h6 class="font-semibold text-primary-700 text-sm lg:text-base">Booking theo trạng thái</h6>
            </div>
            <div class="p-4 lg:p-6">
                <canvas id="bookingsByStatusChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Bookings by Tour Table -->
    <?php if (!empty($bookings_by_tour)): ?>
    <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm mb-4 lg:mb-6">
        <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100">
            <h6 class="font-semibold text-primary-700 text-sm lg:text-base">Top 20 Tours được đặt nhiều nhất</h6>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-primary-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">STT</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">Mã tour</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">Tên tour</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase">Số booking</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase">Tổng khách</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase">Giá trị</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-100">
                    <?php foreach ($bookings_by_tour as $index => $tour): ?>
                    <tr class="hover:bg-primary-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-primary-600"><?= $index + 1 ?></td>
                        <td class="px-4 py-3 text-sm font-semibold text-primary-700"><?= htmlspecialchars($tour['tour_code'] ?? '') ?></td>
                        <td class="px-4 py-3 text-sm text-primary-600"><?= htmlspecialchars($tour['tour_name'] ?? '') ?></td>
                        <td class="px-4 py-3 text-sm text-primary-600 text-right"><?= number_format($tour['booking_count'] ?? 0) ?></td>
                        <td class="px-4 py-3 text-sm text-primary-600 text-right"><?= number_format($tour['total_passengers'] ?? 0) ?></td>
                        <td class="px-4 py-3 text-sm font-bold text-success-text text-right"><?= format_currency($tour['total_amount'] ?? 0) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Bookings by Source Table -->
    <?php if (!empty($bookings_by_source)): ?>
    <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm mb-4 lg:mb-6">
        <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100">
            <h6 class="font-semibold text-primary-700 text-sm lg:text-base">Booking theo nguồn</h6>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-primary-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">Nguồn</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase">Số booking</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase">Tỷ lệ</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase">Giá trị</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-100">
                    <?php 
                    $source_names = [
                        'phone' => 'Điện thoại',
                        'email' => 'Email',
                        'facebook' => 'Facebook',
                        'zalo' => 'Zalo',
                        'walk_in' => 'Tại quầy',
                        'other' => 'Khác'
                    ];
                    foreach ($bookings_by_source as $source): 
                        $percentage = $total_bookings > 0 ? ($source['count'] / $total_bookings) * 100 : 0;
                    ?>
                    <tr class="hover:bg-primary-50 transition-colors">
                        <td class="px-4 py-3 text-sm font-semibold text-primary-700">
                            <?= $source_names[$source['source']] ?? $source['source'] ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-primary-600 text-right"><?= number_format($source['count'] ?? 0) ?></td>
                        <td class="px-4 py-3 text-sm text-primary-600 text-right"><?= number_format($percentage, 2) ?>%</td>
                        <td class="px-4 py-3 text-sm font-bold text-success-text text-right"><?= format_currency($source['total_amount'] ?? 0) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Toggle Advanced Filters
let filtersVisible = false;
function toggleAdvancedFilters() {
    filtersVisible = !filtersVisible;
    const filters = document.querySelectorAll('#advancedFilters, #advancedFilters2, #advancedFilters3');
    filters.forEach(filter => {
        filter.style.display = filtersVisible ? 'block' : 'none';
    });
    document.getElementById('filterToggleText').textContent = filtersVisible ? 'Ẩn bớt' : 'Hiện thêm';
}

// Bookings by Month Chart
<?php if (!empty($bookings_by_month)): ?>
const ctxMonth = document.getElementById('bookingsByMonthChart').getContext('2d');
new Chart(ctxMonth, {
    type: 'bar',
    data: {
        labels: [<?= implode(',', array_map(function($item) { return "'" . date('m/Y', strtotime($item['month'] . '-01')) . "'"; }, $bookings_by_month ?? [])) ?>],
        datasets: [{
            label: 'Số booking',
            data: [<?= implode(',', array_map(function($item) { return $item['count']; }, $bookings_by_month ?? [])) ?>],
            backgroundColor: '#4318FF',
            borderColor: '#4318FF',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
<?php endif; ?>

// Bookings by Status Chart
<?php if (!empty($bookings_by_status)): ?>
const ctxStatus = document.getElementById('bookingsByStatusChart').getContext('2d');
const statusLabels = {
    'paid': 'Đã thanh toán',
    'partial': 'Thanh toán một phần',
    'unpaid': 'Chưa thanh toán',
    'cancelled': 'Đã hủy',
    'refunded': 'Đã hoàn tiền',
    'rejected': 'Từ chối'
};
const statusColors = {
    'paid': '#10B981',
    'partial': '#F59E0B',
    'unpaid': '#EF4444',
    'cancelled': '#6B7280',
    'refunded': '#8B5CF6',
    'rejected': '#DC2626'
};
new Chart(ctxStatus, {
    type: 'doughnut',
    data: {
        labels: [<?= implode(',', array_map(function($item) { return "'" . ($statusLabels[$item['payment_status']] ?? $item['payment_status']) . "'"; }, $bookings_by_status ?? [])) ?>],
        datasets: [{
            data: [<?= implode(',', array_map(function($item) { return $item['count']; }, $bookings_by_status ?? [])) ?>],
            backgroundColor: [<?= implode(',', array_map(function($item) { return "'" . ($statusColors[$item['payment_status']] ?? '#6B7280') . "'"; }, $bookings_by_status ?? [])) ?>]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
<?php endif; ?>

// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
