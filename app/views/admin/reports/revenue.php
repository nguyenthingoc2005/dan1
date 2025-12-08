<div class="mx-auto">
    <!-- Page Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Báo cáo Doanh thu</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Tổng hợp doanh thu và hiệu quả kinh doanh</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Export Excel Button -->
            <a href="?act=admin&module=reports&action=revenue&export=excel&<?= http_build_query($_GET) ?>"
                class="px-4 py-2 bg-success hover:bg-success-hover text-white font-semibold rounded-xl transition-all text-sm flex items-center gap-2 shadow-sm">
                <i data-lucide="download" class="w-4 h-4"></i>
                Xuất Excel
            </a>
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
    </div>

    <!-- Advanced Filters -->
    <div class="bg-panel rounded-2xl border border-primary-100 p-4 lg:p-5 mb-4 lg:mb-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm lg:text-base font-semibold text-primary-700 flex items-center gap-2">
                <i data-lucide="filter" class="w-4 h-4"></i>
                Bộ lọc nâng cao
            </h3>
            <button type="button" onclick="toggleAdvancedFilters()" 
                class="text-xs text-accent hover:text-accent-hover font-semibold">
                <span id="filterToggleText">Hiện thêm</span>
            </button>
        </div>
        <form method="GET" action="" class="grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-4">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="reports">
            <input type="hidden" name="action" value="revenue">

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

            <!-- Customer Filter -->
            <div class="lg:col-span-3" id="advancedFilters2" style="display: none;">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Khách hàng</label>
                <select name="customer_id"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-sm lg:text-base text-primary-700">
                    <option value="">Tất cả khách hàng</option>
                    <?php foreach ($customers ?? [] as $customer): ?>
                        <option value="<?= $customer['id'] ?>" <?= (isset($_GET['customer_id']) && $_GET['customer_id'] == $customer['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($customer['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Payment Method Filter -->
            <div class="lg:col-span-3" id="advancedFilters3" style="display: none;">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Phương thức thanh toán</label>
                <select name="payment_method"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-sm lg:text-base text-primary-700">
                    <option value="">Tất cả</option>
                    <option value="cash" <?= (isset($_GET['payment_method']) && $_GET['payment_method'] == 'cash') ? 'selected' : '' ?>>Tiền mặt</option>
                    <option value="bank_transfer" <?= (isset($_GET['payment_method']) && $_GET['payment_method'] == 'bank_transfer') ? 'selected' : '' ?>>Chuyển khoản</option>
                    <option value="credit_card" <?= (isset($_GET['payment_method']) && $_GET['payment_method'] == 'credit_card') ? 'selected' : '' ?>>Thẻ tín dụng</option>
                    <option value="other" <?= (isset($_GET['payment_method']) && $_GET['payment_method'] == 'other') ? 'selected' : '' ?>>Khác</option>
                </select>
            </div>

            <!-- Payment Status Filter -->
            <div class="lg:col-span-3" id="advancedFilters4" style="display: none;">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái booking</label>
                <select name="payment_status"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-sm lg:text-base text-primary-700">
                    <option value="">Tất cả</option>
                    <option value="paid" <?= (isset($_GET['payment_status']) && $_GET['payment_status'] == 'paid') ? 'selected' : '' ?>>Đã thanh toán</option>
                    <option value="partial" <?= (isset($_GET['payment_status']) && $_GET['payment_status'] == 'partial') ? 'selected' : '' ?>>Thanh toán một phần</option>
                    <option value="unpaid" <?= (isset($_GET['payment_status']) && $_GET['payment_status'] == 'unpaid') ? 'selected' : '' ?>>Chưa thanh toán</option>
                </select>
            </div>

            <!-- Source Filter -->
            <div class="lg:col-span-3" id="advancedFilters5" style="display: none;">
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
                <a href="?act=admin&module=reports&action=revenue"
                    class="px-4 py-2 lg:py-2.5 bg-primary-100 hover:bg-primary-200 text-primary-700 font-semibold rounded-xl transition-all text-sm lg:text-base h-[38px] flex items-center justify-center">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-4 lg:mb-6">
        <!-- Total Revenue -->
        <div class="bg-panel rounded-2xl border border-success p-4 lg:p-6 shadow-sm relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1 bg-success"></div>
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-xs font-bold text-success-text uppercase tracking-wide mb-1 lg:mb-2">Tổng doanh thu</div>
                    <div class="text-xl lg:text-2xl font-bold text-primary-700"><?= format_currency($total_revenue ?? 0) ?></div>
                </div>
                <div class="w-12 h-12 rounded-full bg-success-bg flex items-center justify-center">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-success-text"></i>
                </div>
            </div>
        </div>

        <!-- Total Costs -->
        <div class="bg-panel rounded-2xl border border-warning p-4 lg:p-6 shadow-sm relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1 bg-warning"></div>
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-xs font-bold text-warning-text uppercase tracking-wide mb-1 lg:mb-2">Tổng chi phí</div>
                    <div class="text-xl lg:text-2xl font-bold text-primary-700"><?= format_currency($total_costs ?? 0) ?></div>
                    <div class="text-xs text-primary-500 mt-1">
                        Dịch vụ: <?= format_currency($total_service_costs ?? 0) ?> | 
                        Hoàn tiền: <?= format_currency($total_refunds ?? 0) ?>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-full bg-warning-bg flex items-center justify-center">
                    <i data-lucide="trending-down" class="w-6 h-6 text-warning-text"></i>
                </div>
            </div>
        </div>

        <!-- Profit -->
        <div class="bg-panel rounded-2xl border border-info p-4 lg:p-6 shadow-sm relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1 bg-info"></div>
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-xs font-bold text-info-text uppercase tracking-wide mb-1 lg:mb-2">Lợi nhuận</div>
                    <div class="text-xl lg:text-2xl font-bold <?= ($profit ?? 0) >= 0 ? 'text-success-text' : 'text-danger-text' ?>">
                        <?= format_currency($profit ?? 0) ?>
                    </div>
                    <?php if (($profit ?? 0) > 0): ?>
                        <div class="text-xs text-success-text mt-1">
                            Tỷ suất: <?= number_format((($profit ?? 0) / max($total_revenue, 1)) * 100, 2) ?>%
                        </div>
                    <?php endif; ?>
                </div>
                <div class="w-12 h-12 rounded-full bg-info-bg flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-6 h-6 text-info-text"></i>
                </div>
            </div>
        </div>

        <!-- Total Bookings -->
        <div class="bg-panel rounded-2xl border border-accent p-4 lg:p-6 shadow-sm relative overflow-hidden">
            <div class="absolute right-0 top-0 h-full w-1 bg-accent"></div>
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-xs font-bold text-accent uppercase tracking-wide mb-1 lg:mb-2">Tổng booking</div>
                    <div class="text-xl lg:text-2xl font-bold text-primary-700"><?= number_format($total_bookings ?? 0) ?></div>
                    <div class="text-xs text-primary-500 mt-1"><?= number_format($total_customers ?? 0) ?> khách hàng</div>
                </div>
                <div class="w-12 h-12 rounded-full bg-primary-50 flex items-center justify-center">
                    <i data-lucide="calendar-check" class="w-6 h-6 text-accent"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Chi phí chi tiết -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-4 lg:mb-6">
        <div class="bg-panel rounded-2xl border border-primary-100 p-4 lg:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="text-xs font-semibold text-primary-600 uppercase">Tiền trả dịch vụ</div>
                <i data-lucide="truck" class="w-5 h-5 text-primary-500"></i>
            </div>
            <div class="text-lg lg:text-xl font-bold text-primary-700"><?= format_currency($total_service_costs ?? 0) ?></div>
        </div>
        <div class="bg-panel rounded-2xl border border-danger p-4 lg:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="text-xs font-semibold text-danger-text uppercase">Hoàn tiền</div>
                <i data-lucide="rotate-ccw" class="w-5 h-5 text-danger-text"></i>
            </div>
            <div class="text-lg lg:text-xl font-bold text-danger-text"><?= format_currency($total_refunds ?? 0) ?></div>
        </div>
        <div class="bg-panel rounded-2xl border border-primary-100 p-4 lg:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="text-xs font-semibold text-primary-600 uppercase">Lương HDV</div>
                <i data-lucide="user-check" class="w-5 h-5 text-primary-500"></i>
            </div>
            <div class="text-lg lg:text-xl font-bold text-primary-700"><?= format_currency($total_guide_salary ?? 0) ?></div>
        </div>
        <div class="bg-panel rounded-2xl border border-primary-100 p-4 lg:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <div class="text-xs font-semibold text-primary-600 uppercase">Lương tài xế</div>
                <i data-lucide="steering-wheel" class="w-5 h-5 text-primary-500"></i>
            </div>
            <div class="text-lg lg:text-xl font-bold text-primary-700"><?= format_currency($total_driver_salary ?? 0) ?></div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-4 lg:mb-6">
        <!-- Revenue by Month Chart -->
        <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm">
            <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100">
                <h6 class="font-semibold text-primary-700 text-sm lg:text-base">Doanh thu theo tháng</h6>
            </div>
            <div class="p-4 lg:p-6">
                <canvas id="revenueByMonthChart" height="200"></canvas>
            </div>
        </div>

        <!-- Revenue by Payment Method -->
        <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm">
            <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100">
                <h6 class="font-semibold text-primary-700 text-sm lg:text-base">Doanh thu theo phương thức thanh toán</h6>
            </div>
            <div class="p-4 lg:p-6">
                <canvas id="revenueByPaymentMethodChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Revenue by Tour Table -->
    <?php if (!empty($revenue_by_tour)): ?>
    <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm mb-4 lg:mb-6">
        <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100">
            <h6 class="font-semibold text-primary-700 text-sm lg:text-base">Top 20 Tours có doanh thu cao nhất</h6>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-primary-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">STT</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">Mã tour</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">Tên tour</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase">Số booking</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase">Doanh thu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-100">
                    <?php foreach ($revenue_by_tour as $index => $tour): ?>
                    <tr class="hover:bg-primary-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-primary-600"><?= $index + 1 ?></td>
                        <td class="px-4 py-3 text-sm font-semibold text-primary-700"><?= htmlspecialchars($tour['tour_code'] ?? '') ?></td>
                        <td class="px-4 py-3 text-sm text-primary-600"><?= htmlspecialchars($tour['tour_name'] ?? '') ?></td>
                        <td class="px-4 py-3 text-sm text-primary-600 text-right"><?= number_format($tour['booking_count'] ?? 0) ?></td>
                        <td class="px-4 py-3 text-sm font-bold text-success-text text-right"><?= format_currency($tour['revenue'] ?? 0) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Revenue by Customer Table -->
    <?php if (!empty($revenue_by_customer)): ?>
    <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm mb-4 lg:mb-6">
        <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100">
            <h6 class="font-semibold text-primary-700 text-sm lg:text-base">Top 10 Khách hàng chi nhiều tiền nhất</h6>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-primary-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">STT</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">Mã KH</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">Tên khách hàng</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase">Số booking</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase">Tổng chi tiêu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-100">
                    <?php foreach ($revenue_by_customer as $index => $customer): ?>
                    <tr class="hover:bg-primary-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-primary-600"><?= $index + 1 ?></td>
                        <td class="px-4 py-3 text-sm font-semibold text-primary-700"><?= htmlspecialchars($customer['customer_code'] ?? '') ?></td>
                        <td class="px-4 py-3 text-sm text-primary-600"><?= htmlspecialchars($customer['full_name'] ?? '') ?></td>
                        <td class="px-4 py-3 text-sm text-primary-600 text-right"><?= number_format($customer['booking_count'] ?? 0) ?></td>
                        <td class="px-4 py-3 text-sm font-bold text-success-text text-right"><?= format_currency($customer['revenue'] ?? 0) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Chi tiết Hoàn tiền -->
    <?php if (!empty($refund_details)): ?>
    <div class="bg-panel rounded-2xl border border-danger shadow-sm mb-4 lg:mb-6">
        <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100">
            <h6 class="font-semibold text-primary-700 text-sm lg:text-base flex items-center gap-2">
                <i data-lucide="rotate-ccw" class="w-4 h-4 text-danger-text"></i>
                Chi tiết Hoàn tiền (<?= count($refund_details) ?> giao dịch)
            </h6>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-primary-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">Ngày</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">Mã booking</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">Tour</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">Khách hàng</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase">Số tiền hoàn</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">Phương thức</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-100">
                    <?php foreach ($refund_details as $refund): ?>
                    <tr class="hover:bg-primary-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-primary-600"><?= date('d/m/Y', strtotime($refund['payment_date'])) ?></td>
                        <td class="px-4 py-3 text-sm font-semibold text-primary-700"><?= htmlspecialchars($refund['booking_code'] ?? '') ?></td>
                        <td class="px-4 py-3 text-sm text-primary-600"><?= htmlspecialchars($refund['tour_name'] ?? '') ?></td>
                        <td class="px-4 py-3 text-sm text-primary-600"><?= htmlspecialchars($refund['customer_name'] ?? '') ?></td>
                        <td class="px-4 py-3 text-sm font-bold text-danger-text text-right"><?= format_currency($refund['amount'] ?? 0) ?></td>
                        <td class="px-4 py-3 text-sm text-primary-600">
                            <?php
                            $methods = ['cash' => 'Tiền mặt', 'bank_transfer' => 'Chuyển khoản', 'credit_card' => 'Thẻ', 'other' => 'Khác'];
                            echo $methods[$refund['payment_method']] ?? $refund['payment_method'];
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Chi tiết Tiền trả dịch vụ theo nhà cung cấp -->
    <?php if (!empty($service_cost_by_provider)): ?>
    <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm mb-4 lg:mb-6">
        <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100">
            <h6 class="font-semibold text-primary-700 text-sm lg:text-base flex items-center gap-2">
                <i data-lucide="truck" class="w-4 h-4 text-primary-500"></i>
                Chi tiết Tiền trả cho Nhà cung cấp dịch vụ
            </h6>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-primary-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">STT</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">Nhà cung cấp</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase">Số lần thanh toán</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase">Tổng tiền đã trả</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-100">
                    <?php foreach ($service_cost_by_provider as $index => $provider): ?>
                    <tr class="hover:bg-primary-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-primary-600"><?= $index + 1 ?></td>
                        <td class="px-4 py-3 text-sm font-semibold text-primary-700"><?= htmlspecialchars($provider['provider_name'] ?? '') ?></td>
                        <td class="px-4 py-3 text-sm text-primary-600 text-right"><?= number_format($provider['payment_count'] ?? 0) ?></td>
                        <td class="px-4 py-3 text-sm font-bold text-warning-text text-right"><?= format_currency($provider['total_amount'] ?? 0) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Chi tiết Lương HDV -->
    <?php if (!empty($guide_salary_details)): ?>
    <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm mb-4 lg:mb-6">
        <div class="px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100">
            <h6 class="font-semibold text-primary-700 text-sm lg:text-base flex items-center gap-2">
                <i data-lucide="user-check" class="w-4 h-4 text-primary-500"></i>
                Chi tiết Lương Hướng dẫn viên (<?= count($guide_salary_details) ?> giao dịch)
            </h6>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-primary-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">Ngày trả</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">HDV</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">Tour</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-primary-700 uppercase">Thời gian tour</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-primary-700 uppercase">Lương</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-100">
                    <?php foreach ($guide_salary_details as $salary): ?>
                    <tr class="hover:bg-primary-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-primary-600">
                            <?php 
                            $display_date = $salary['paid_date'] ?? $salary['end_date'] ?? $salary['assignment_date'] ?? '';
                            echo $display_date ? date('d/m/Y', strtotime($display_date)) : '-';
                            ?>
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-primary-700"><?= htmlspecialchars($salary['guide_name'] ?? '') ?></td>
                        <td class="px-4 py-3 text-sm text-primary-600"><?= htmlspecialchars($salary['tour_name'] ?? '') ?></td>
                        <td class="px-4 py-3 text-sm text-primary-600">
                            <?php if ($salary['start_date'] && $salary['end_date']): ?>
                                <?= date('d/m/Y', strtotime($salary['start_date'])) ?> - <?= date('d/m/Y', strtotime($salary['end_date'])) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm font-bold text-success-text text-right"><?= format_currency($salary['salary_amount'] ?? 0) ?></td>
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
    const filters = document.querySelectorAll('#advancedFilters, #advancedFilters2, #advancedFilters3, #advancedFilters4, #advancedFilters5');
    filters.forEach(filter => {
        filter.style.display = filtersVisible ? 'block' : 'none';
    });
    document.getElementById('filterToggleText').textContent = filtersVisible ? 'Ẩn bớt' : 'Hiện thêm';
}

// Revenue by Month Chart
<?php if (!empty($revenue_by_month)): ?>
const ctxMonth = document.getElementById('revenueByMonthChart').getContext('2d');
new Chart(ctxMonth, {
    type: 'line',
    data: {
        labels: [<?= implode(',', array_map(function($item) { return "'" . date('m/Y', strtotime($item['month'] . '-01')) . "'"; }, $revenue_by_month ?? [])) ?>],
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: [<?= implode(',', array_map(function($item) { return $item['revenue']; }, $revenue_by_month ?? [])) ?>],
            borderColor: '#4318FF',
            backgroundColor: 'rgba(67, 24, 255, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed.y);
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', notation: 'compact' }).format(value);
                    }
                }
            }
        }
    }
});
<?php endif; ?>

// Revenue by Payment Method Chart
<?php if (!empty($revenue_by_payment_method)): ?>
const ctxPayment = document.getElementById('revenueByPaymentMethodChart').getContext('2d');
const paymentMethodLabels = {
    'cash': 'Tiền mặt',
    'bank_transfer': 'Chuyển khoản',
    'credit_card': 'Thẻ tín dụng',
    'other': 'Khác'
};
new Chart(ctxPayment, {
    type: 'doughnut',
    data: {
        labels: [<?= implode(',', array_map(function($item) { return "'" . ($paymentMethodLabels[$item['payment_method']] ?? $item['payment_method']) . "'"; }, $revenue_by_payment_method ?? [])) ?>],
        datasets: [{
            data: [<?= implode(',', array_map(function($item) { return $item['revenue']; }, $revenue_by_payment_method ?? [])) ?>],
            backgroundColor: ['#4318FF', '#10B981', '#F59E0B', '#EF4444']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed);
                        return label + ': ' + value;
                    }
                }
            }
        }
    }
});
<?php endif; ?>

// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
