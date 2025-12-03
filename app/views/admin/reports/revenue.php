<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Báo cáo Doanh thu</h1>
            <p class="text-sm text-gray-500 mt-1">Tổng hợp doanh thu và hiệu quả kinh doanh</p>
        </div>
        <div class="flex bg-gray-100 p-1 rounded-lg">
            <a href="?act=admin&module=reports&action=revenue"
                class="px-4 py-2 text-sm font-medium rounded-md shadow-sm bg-white text-blue-600">
                Doanh thu
            </a>
            <a href="?act=admin&module=reports&action=bookings"
                class="px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-200 transition-colors">
                Đặt tour
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6 shadow-sm">
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="reports">
            <input type="hidden" name="action" value="revenue">

            <div class="md:col-span-4">
                <label class="block text-xs font-medium text-gray-700 mb-1">Từ ngày</label>
                <input type="date" name="start_date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm"
                    value="<?= $start_date ?>">
            </div>
            <div class="md:col-span-4">
                <label class="block text-xs font-medium text-gray-700 mb-1">Đến ngày</label>
                <input type="date" name="end_date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm"
                    value="<?= $end_date ?>">
            </div>
            <div class="md:col-span-2 flex items-end">
                <button type="submit"
                    class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium py-2 px-4 rounded-lg transition-colors text-sm h-[38px] shadow-sm">
                    <i class="fas fa-filter mr-1"></i> Xem báo cáo
                </button>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-emerald-100 p-6 shadow-sm relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1 bg-emerald-500"></div>
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-xs font-bold text-emerald-600 uppercase tracking-wide mb-1">Tổng doanh thu (Thực
                        thu)</div>
                    <div class="text-2xl font-bold text-slate-800"><?= format_currency($total_revenue) ?></div>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-xl text-emerald-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Placeholder -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h6 class="font-semibold text-slate-700">Biểu đồ doanh thu theo tháng</h6>
        </div>
        <div class="p-6">
            <div
                class="flex flex-col items-center justify-center py-12 text-gray-400 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                <i class="fas fa-chart-bar text-4xl mb-3 text-gray-300"></i>
                <p class="text-sm">Biểu đồ đang được cập nhật...</p>
            </div>
        </div>
    </div>
</div>