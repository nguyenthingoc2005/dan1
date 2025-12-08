<?php
/**
 * STAFF - LỊCH TOUR KHỞI HÀNH (READ ONLY - ĐỂ TƯ VẤN KHÁCH HÀNG)
 * Variables: $schedules, $tours, $categories, $stats, $total, $total_pages, $current_page, $view_filters
 */
?>
<div class="max-w-8xl mx-auto">
    <!-- HEADER - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700 flex items-center gap-2">
                <i data-lucide="calendar" class="w-5 h-5 lg:w-6 lg:h-6"></i>
                Lịch Tour Khởi Hành
            </h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Xem lịch tour để tư vấn khách hàng</p>
        </div>
        <a href="?act=staff-bookings&action=create"
            class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tạo Booking
        </a>
    </div>

    <!-- SUMMARY STATS -->
    <div class="mt-4 lg:mt-6 grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
        <div class="bg-success-bg border border-success rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-success-text mb-1 font-semibold">Đang mở bán</div>
            <div class="text-xl lg:text-2xl font-bold text-success-text">
                <?= $stats['open'] ?? 0 ?>
            </div>
        </div>
        <div class="bg-warning-bg border border-warning rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-warning-text mb-1 font-semibold">Sắp hết chỗ</div>
            <div class="text-xl lg:text-2xl font-bold text-warning-text">
                <?= $stats['almost_full'] ?? 0 ?>
            </div>
        </div>
        <div class="bg-danger-bg border border-danger rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-danger-text mb-1 font-semibold">Đã đầy</div>
            <div class="text-xl lg:text-2xl font-bold text-danger-text">
                <?= $stats['full'] ?? 0 ?>
            </div>
        </div>
        <div class="bg-primary-50 border border-primary-100 rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-primary-500 mb-1 font-semibold">Tổng lịch</div>
            <div class="text-xl lg:text-2xl font-bold text-primary-700"><?= $total ?? 0 ?></div>
        </div>
    </div>

    <!-- FILTER -->
    <div class="bg-panel p-4 lg:p-5 rounded-2xl shadow-sm border border-primary-100 mb-4 lg:mb-6">
        <form method="GET" class="grid grid-cols-1 lg:grid-cols-5 gap-3 lg:gap-4">
            <input type="hidden" name="act" value="staff-schedules">

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tour</label>
                <select name="tour_id" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Tất cả Tour --</option>
                    <?php foreach ($tours as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($view_filters['tour_id'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['tour_code'] . ' - ' . $t['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Từ ngày</label>
                <input type="date" name="start_date" value="<?= $view_filters['start_date'] ?? '' ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
            </div>

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Đến ngày</label>
                <input type="date" name="end_date" value="<?= $view_filters['end_date'] ?? '' ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
            </div>

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
                <select name="status" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Tất cả --</option>
                    <option value="open" <?= ($view_filters['status'] ?? '') == 'open' ? 'selected' : '' ?>>Đang mở bán</option>
                    <option value="closed" <?= ($view_filters['status'] ?? '') == 'closed' ? 'selected' : '' ?>>Đóng bán</option>
                    <option value="completed" <?= ($view_filters['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                </select>
            </div>

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Danh mục</label>
                <select name="category_id" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Tất cả --</option>
                    <?php foreach ($categories as $cat_id => $cat_name): ?>
                        <option value="<?= $cat_id ?>" <?= ($view_filters['category_id'] ?? '') == $cat_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat_name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="lg:col-span-5 flex flex-col sm:flex-row items-start sm:items-center gap-3 lg:gap-4">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="only_available" value="1" id="only_available"
                        <?= !empty($_GET['only_available']) ? 'checked' : '' ?>
                        class="rounded-xl border-primary-200 text-accent focus:ring-accent w-4 h-4">
                    <label for="only_available" class="text-xs lg:text-sm text-primary-700">Chỉ hiển thị còn chỗ</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="almost_full" value="1" id="almost_full"
                        <?= !empty($_GET['almost_full']) ? 'checked' : '' ?>
                        class="rounded-xl border-primary-200 text-accent focus:ring-accent w-4 h-4">
                    <label for="almost_full" class="text-xs lg:text-sm text-primary-700">Sắp hết chỗ (< 10%)</label>
                </div>
                <button type="submit" class="w-full sm:w-auto sm:ml-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-600 hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Lọc
                </button>
            </div>
        </form>
    </div>

    <!-- TABLE -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead class="bg-primary-50 text-primary-700 uppercase text-xs font-bold">
                <tr>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Mã Tour</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Tên Tour</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Danh mục</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Khởi hành</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Kết thúc</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-center">Chỗ</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-center">Đã đặt</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-center">Còn lại</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-right">Giá (NL)</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-right">Giá (TE)</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">HDV</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-center">Trạng thái</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary-100">
                <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="13" class="px-3 lg:px-4 py-6 lg:py-8 text-center text-primary-500 text-sm">
                            Không tìm thấy lịch tour nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($schedules as $s):
                        $available = max(0, ($s['quota'] ?? 0) - ($s['booked'] ?? 0));
                        $fill_rate = ($s['quota'] ?? 0) > 0 ? round((($s['booked'] ?? 0) / ($s['quota'] ?? 1)) * 100, 1) : 0;
                        $is_almost_full = $fill_rate >= 90 && $available > 0;
                        ?>
                        <tr class="hover:bg-primary-50 transition-colors <?= $available <= 0 ? 'bg-danger-bg/30' : ($is_almost_full ? 'bg-warning-bg/30' : '') ?>">
                            <td class="px-3 lg:px-4 py-2 lg:py-3 font-mono text-sm text-accent">
                                <?= htmlspecialchars($s['tour_code'] ?? '') ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">
                                <a href="?act=staff-schedules&action=show&id=<?= $s['id'] ?>"
                                    class="text-primary-700 hover:text-accent hover:underline text-sm">
                                    <?= htmlspecialchars($s['tour_name'] ?? '') ?>
                                </a>
                                <div class="text-xs text-primary-500 mt-1">
                                    <?= ($s['duration_days'] ?? 0) ?> ngày 
                                    <?= ($s['duration_nights'] ?? 0) > 0 ? ($s['duration_nights'] ?? 0) . ' đêm' : '' ?>
                                </div>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-600">
                                <?= htmlspecialchars($s['category_name'] ?? 'Chưa phân loại') ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3">
                                <span class="text-success-text font-bold text-sm"><?= date('d/m/Y', strtotime($s['start_date'])) ?></span>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 text-sm">
                                <?= date('d/m/Y', strtotime($s['end_date'])) ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-center font-semibold text-sm"><?= $s['quota'] ?? 0 ?></td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                <span class="font-bold text-sm <?= $fill_rate >= 80 ? 'text-warning-text' : 'text-accent' ?>">
                                    <?= $s['booked'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                <span class="font-bold text-sm <?= $available <= 0 ? 'text-danger-text' : ($available <= 5 ? 'text-warning-text' : 'text-success-text') ?>">
                                    <?= $available ?>
                                </span>
                                <?php if ($fill_rate > 0): ?>
                                    <div class="text-xs text-primary-400"><?= $fill_rate ?>%</div>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-right font-semibold text-sm">
                                <?= number_format($s['adult_price'] ?? 0, 0, ',', '.') ?> đ
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-right text-sm text-primary-600">
                                <?= number_format($s['child_price'] ?? 0, 0, ',', '.') ?> đ
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3">
                                <?php if (!empty($s['guide_name'])): ?>
                                    <span class="text-xs lg:text-sm text-info-text flex items-center gap-1" title="<?= htmlspecialchars($s['guide_phone'] ?? '') ?>">
                                        <i data-lucide="user" class="w-3 h-3"></i>
                                        <?= htmlspecialchars($s['guide_name']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-xs text-primary-400">Chưa gán</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase
                                    <?= $s['status'] == 'open' ? 'bg-success-bg text-success-text' :
                                        ($s['status'] == 'closed' ? 'bg-danger-bg text-danger-text' :
                                            ($s['status'] == 'completed' ? 'bg-info-bg text-info-text' : 'bg-primary-100 text-primary-500')) ?>">
                                    <?php
                                    $status_names = [
                                        'open' => 'Mở bán',
                                        'closed' => 'Đóng',
                                        'completed' => 'Hoàn thành',
                                        'cancelled' => 'Hủy'
                                    ];
                                    echo $status_names[$s['status']] ?? $s['status'];
                                    ?>
                                </span>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-right">
                                <div class="flex items-center justify-end gap-1 lg:gap-2">
                                    <a href="?act=staff-schedules&action=show&id=<?= $s['id'] ?>"
                                        class="text-accent hover:text-accent-hover p-1.5 rounded-xl hover:bg-primary-50 transition-all" title="Xem chi tiết">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <?php if ($s['status'] == 'open' && $available > 0): ?>
                                        <a href="?act=staff-bookings&action=create&tour_id=<?= $s['tour_id'] ?>&start_date=<?= $s['start_date'] ?>"
                                            class="text-success-text hover:text-success-text p-1.5 rounded-xl hover:bg-success-bg transition-all font-semibold" title="Tạo booking">
                                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if (($total_pages ?? 1) > 1): ?>
        <div class="mt-4 flex justify-center gap-2 flex-wrap">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?act=staff-schedules&page=<?= $i ?>&tour_id=<?= $view_filters['tour_id'] ?? '' ?>&start_date=<?= $view_filters['start_date'] ?? '' ?>&end_date=<?= $view_filters['end_date'] ?? '' ?>&status=<?= $view_filters['status'] ?? '' ?>&category_id=<?= $view_filters['category_id'] ?? '' ?>"
                    class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-sm font-semibold transition-all <?= $i == $current_page ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white shadow-sm' : 'bg-panel border border-primary-100 text-primary-700 hover:bg-primary-50' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

