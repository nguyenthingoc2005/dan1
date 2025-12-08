<div class=" mx-auto">
    <!-- Page Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Báo cáo Đặt tour</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Thống kê số lượng và xu hướng đặt tour</p>
        </div>
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

    <!-- Filters -->
    <div class="bg-panel rounded-2xl border border-primary-100 p-4 lg:p-5 mb-4 lg:mb-6 shadow-sm">
        <form method="GET" action="" class="grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-4">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="reports">
            <input type="hidden" name="action" value="bookings">

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

    <!-- Content Placeholder -->
    <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm p-8 lg:p-12 text-center">
        <div class="flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="wrench" class="w-8 h-8 text-primary-400"></i>
            </div>
            <h3 class="text-base lg:text-lg font-semibold text-primary-700 mb-1">Tính năng đang phát triển</h3>
            <p class="text-sm text-primary-500">Báo cáo chi tiết về đặt tour sẽ sớm được cập nhật.</p>
        </div>
    </div>
</div>