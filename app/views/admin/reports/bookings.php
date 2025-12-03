<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Báo cáo Đặt tour</h1>
            <p class="text-sm text-gray-500 mt-1">Thống kê số lượng và xu hướng đặt tour</p>
        </div>
        <div class="flex bg-gray-100 p-1 rounded-lg">
            <a href="?act=admin&module=reports&action=revenue"
                class="px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-200 transition-colors">
                Doanh thu
            </a>
            <a href="?act=admin&module=reports&action=bookings"
                class="px-4 py-2 text-sm font-medium rounded-md shadow-sm bg-white text-blue-600">
                Đặt tour
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6 shadow-sm">
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="reports">
            <input type="hidden" name="action" value="bookings">

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

    <!-- Content Placeholder -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
        <div class="flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-tools text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">Tính năng đang phát triển</h3>
            <p class="text-gray-500">Báo cáo chi tiết về đặt tour sẽ sớm được cập nhật.</p>
        </div>
    </div>
</div>