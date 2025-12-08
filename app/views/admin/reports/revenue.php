<div class=" mx-auto">
    <!-- Page Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Báo cáo Doanh thu</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Tổng hợp doanh thu và hiệu quả kinh doanh</p>
        </div>
        <div class="flex bg-primary-100 p-1 rounded-xl">
            <a href="?act=admin&module=reports&action=revenue"
                class="px-3 lg:px-4 py-1.5 lg:py-2 text-xs lg:text-sm font-semibold rounded-xl shadow-sm bg-panel text-accent">
                Doanh thu
            </a>
            <a href="?act=admin&module=reports&action=bookings"
                class="px-3 lg:px-4 py-1.5 lg:py-2 text-xs lg:text-sm font-semibold rounded-xl text-primary-500 hover:text-primary-700 hover:bg-primary-50 transition-colors">
                Đặt tour
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-panel rounded-2xl border border-primary-100 p-4 lg:p-5 mb-4 lg:mb-6 shadow-sm">
        <form method="GET" action="" class="grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-4">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="reports">
            <input type="hidden" name="action" value="revenue">

            <div class="lg:col-span-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Từ ngày</label>
                <input type="date" name="start_date"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-sm lg:text-base text-primary-700"
                    value="<?= $start_date ?>">
            </div>
            <div class="lg:col-span-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Đến ngày</label>
                <input type="date" name="end_date"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-sm lg:text-base text-primary-700"
                    value="<?= $end_date ?>">
            </div>
            <div class="lg:col-span-2 flex items-end">
                <button type="submit"
                    class="w-full px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-600 hover:opacity-90 text-white font-semibold rounded-xl transition-all text-sm lg:text-base flex items-center justify-center gap-2 h-[38px] shadow-sm">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Xem báo cáo
                </button>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 mb-4 lg:mb-6">
        <div class="bg-panel rounded-2xl border border-success p-4 lg:p-6 shadow-sm relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1 bg-success"></div>
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-xs font-bold text-success-text uppercase tracking-wide mb-1 lg:mb-2">Tổng doanh thu
                        (Thực
                        thu)</div>
                    <div class="text-xl lg:text-2xl font-bold text-primary-700"><?= format_currency($total_revenue) ?>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-full bg-success-bg flex items-center justify-center">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-success-text"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Placeholder -->
    <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm">
        <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100">
            <h6 class="font-semibold text-primary-700 text-sm lg:text-base">Biểu đồ doanh thu theo tháng</h6>
        </div>
        <div class="p-4 lg:p-6">
            <div
                class="flex flex-col items-center justify-center py-8 lg:py-12 text-primary-400 bg-primary-50 rounded-xl border border-dashed border-primary-200">
                <i data-lucide="bar-chart-3" class="w-12 h-12 mb-3 text-primary-300"></i>
                <p class="text-sm text-primary-500">Biểu đồ đang được cập nhật...</p>
            </div>
        </div>
    </div>
</div>