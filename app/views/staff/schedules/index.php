<?php
/**
 * STAFF - LỊCH TOUR KHỞI HÀNH (READ ONLY - ĐỂ TƯ VẤN KHÁCH HÀNG)
 * Variables: $schedules, $tours, $categories, $stats, $total, $total_pages, $current_page, $view_filters
 */
?>
<div class="max-w-8xl mx-auto">
    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">📅 Lịch Tour Khởi Hành</h1>
            <p class="text-sm text-gray-500 mt-1">Xem lịch tour để tư vấn khách hàng</p>
        </div>
        <a href="?act=staff-bookings&action=create"
            class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600 shadow">
            + Tạo Booking
        </a>
    </div>

    <!-- SUMMARY STATS -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="text-sm text-green-600 mb-1">Đang mở bán</div>
            <div class="text-2xl font-bold text-green-700">
                <?= $stats['open'] ?? 0 ?>
            </div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="text-sm text-yellow-600 mb-1">Sắp hết chỗ</div>
            <div class="text-2xl font-bold text-yellow-700">
                <?= $stats['almost_full'] ?? 0 ?>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="text-sm text-red-600 mb-1">Đã đầy</div>
            <div class="text-2xl font-bold text-red-700">
                <?= $stats['full'] ?? 0 ?>
            </div>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <div class="text-sm text-gray-600 mb-1">Tổng lịch</div>
            <div class="text-2xl font-bold text-gray-700"><?= $total ?? 0 ?></div>
        </div>
    </div>

    <!-- FILTER -->
    <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="hidden" name="act" value="staff-schedules">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tour</label>
                <select name="tour_id" class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    <option value="">-- Tất cả Tour --</option>
                    <?php foreach ($tours as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($view_filters['tour_id'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['tour_code'] . ' - ' . $t['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                <input type="date" name="start_date" value="<?= $view_filters['start_date'] ?? '' ?>"
                    class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                <input type="date" name="end_date" value="<?= $view_filters['end_date'] ?? '' ?>"
                    class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select name="status" class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    <option value="">-- Tất cả --</option>
                    <option value="open" <?= ($view_filters['status'] ?? '') == 'open' ? 'selected' : '' ?>>Đang mở bán</option>
                    <option value="closed" <?= ($view_filters['status'] ?? '') == 'closed' ? 'selected' : '' ?>>Đóng bán</option>
                    <option value="completed" <?= ($view_filters['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                <select name="category_id" class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    <option value="">-- Tất cả --</option>
                    <?php foreach ($categories as $cat_id => $cat_name): ?>
                        <option value="<?= $cat_id ?>" <?= ($view_filters['category_id'] ?? '') == $cat_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat_name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="md:col-span-5 flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="only_available" value="1" id="only_available"
                        <?= !empty($_GET['only_available']) ? 'checked' : '' ?>
                        class="rounded border-gray-300 text-accent focus:ring-accent">
                    <label for="only_available" class="text-sm text-gray-700">Chỉ hiển thị còn chỗ</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="almost_full" value="1" id="almost_full"
                        <?= !empty($_GET['almost_full']) ? 'checked' : '' ?>
                        class="rounded border-gray-300 text-accent focus:ring-accent">
                    <label for="almost_full" class="text-sm text-gray-700">Sắp hết chỗ (< 10%)</label>
                </div>
                <button type="submit" class="ml-auto px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                    🔍 Lọc
                </button>
            </div>
        </form>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-bold">
                <tr>
                    <th class="px-4 py-3 border-b">Mã Tour</th>
                    <th class="px-4 py-3 border-b">Tên Tour</th>
                    <th class="px-4 py-3 border-b">Danh mục</th>
                    <th class="px-4 py-3 border-b">Khởi hành</th>
                    <th class="px-4 py-3 border-b">Kết thúc</th>
                    <th class="px-4 py-3 border-b text-center">Chỗ</th>
                    <th class="px-4 py-3 border-b text-center">Đã đặt</th>
                    <th class="px-4 py-3 border-b text-center">Còn lại</th>
                    <th class="px-4 py-3 border-b text-right">Giá (NL)</th>
                    <th class="px-4 py-3 border-b text-right">Giá (TE)</th>
                    <th class="px-4 py-3 border-b">HDV</th>
                    <th class="px-4 py-3 border-b text-center">Trạng thái</th>
                    <th class="px-4 py-3 border-b text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="13" class="px-6 py-8 text-center text-gray-500">
                            Không tìm thấy lịch tour nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($schedules as $s):
                        $available = max(0, ($s['quota'] ?? 0) - ($s['booked'] ?? 0));
                        $fill_rate = ($s['quota'] ?? 0) > 0 ? round((($s['booked'] ?? 0) / ($s['quota'] ?? 1)) * 100, 1) : 0;
                        $is_almost_full = $fill_rate >= 90 && $available > 0;
                        ?>
                        <tr class="hover:bg-gray-50 <?= $available <= 0 ? 'bg-red-50' : ($is_almost_full ? 'bg-yellow-50' : '') ?>">
                            <td class="px-4 py-3 font-mono text-sm text-blue-600">
                                <?= htmlspecialchars($s['tour_code'] ?? '') ?>
                            </td>
                            <td class="px-4 py-3 font-medium">
                                <a href="?act=staff-schedules&action=show&id=<?= $s['id'] ?>"
                                    class="text-gray-800 hover:text-accent hover:underline">
                                    <?= htmlspecialchars($s['tour_name'] ?? '') ?>
                                </a>
                                <div class="text-xs text-gray-500 mt-1">
                                    <?= ($s['duration_days'] ?? 0) ?> ngày 
                                    <?= ($s['duration_nights'] ?? 0) > 0 ? ($s['duration_nights'] ?? 0) . ' đêm' : '' ?>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <?= htmlspecialchars($s['category_name'] ?? 'Chưa phân loại') ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-green-700 font-bold"><?= date('d/m/Y', strtotime($s['start_date'])) ?></span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                <?= date('d/m/Y', strtotime($s['end_date'])) ?>
                            </td>
                            <td class="px-4 py-3 text-center font-medium"><?= $s['quota'] ?? 0 ?></td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-bold <?= $fill_rate >= 80 ? 'text-yellow-600' : 'text-accent' ?>">
                                    <?= $s['booked'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-bold <?= $available <= 0 ? 'text-red-600' : ($available <= 5 ? 'text-yellow-600' : 'text-green-600') ?>">
                                    <?= $available ?>
                                </span>
                                <?php if ($fill_rate > 0): ?>
                                    <div class="text-xs text-gray-400"><?= $fill_rate ?>%</div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right font-medium">
                                <?= number_format($s['adult_price'] ?? 0, 0, ',', '.') ?> đ
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-gray-600">
                                <?= number_format($s['child_price'] ?? 0, 0, ',', '.') ?> đ
                            </td>
                            <td class="px-4 py-3">
                                <?php if (!empty($s['guide_name'])): ?>
                                    <span class="text-sm text-blue-600" title="<?= htmlspecialchars($s['guide_phone'] ?? '') ?>">
                                        👤 <?= htmlspecialchars($s['guide_name']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400">Chưa gán</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full font-medium
                                    <?= $s['status'] == 'open' ? 'bg-green-100 text-green-800' :
                                        ($s['status'] == 'closed' ? 'bg-red-100 text-red-800' :
                                            ($s['status'] == 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) ?>">
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
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="?act=staff-schedules&action=show&id=<?= $s['id'] ?>"
                                        class="text-blue-600 hover:text-blue-800" title="Xem chi tiết">
                                        👁️
                                    </a>
                                    <?php if ($s['status'] == 'open' && $available > 0): ?>
                                        <a href="?act=staff-bookings&action=create&tour_id=<?= $s['tour_id'] ?>&start_date=<?= $s['start_date'] ?>"
                                            class="text-green-600 hover:text-green-800 font-medium" title="Tạo booking">
                                            ➕
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

    <!-- Pagination -->
    <?php if (($total_pages ?? 1) > 1): ?>
        <div class="mt-4 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?act=staff-schedules&page=<?= $i ?>&tour_id=<?= $view_filters['tour_id'] ?? '' ?>&start_date=<?= $view_filters['start_date'] ?? '' ?>&end_date=<?= $view_filters['end_date'] ?? '' ?>&status=<?= $view_filters['status'] ?? '' ?>&category_id=<?= $view_filters['category_id'] ?? '' ?>"
                    class="px-3 py-1 rounded <?= $i == $current_page ? 'bg-accent text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

