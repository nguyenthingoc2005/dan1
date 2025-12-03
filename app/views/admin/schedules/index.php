<?php
/**
 * ADMIN - QUẢN LÝ LỊCH KHỞI HÀNH
 * Variables: $schedules, $tours, $total, $total_pages, $current_page, $filters
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Lịch Khởi Hành</h1>
        <a href="?act=admin&module=schedules&action=create"
            class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600 shadow">
            + Thêm Lịch Mới
        </a>
    </div>
    <!-- Summary Stats -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="text-sm text-green-600 mb-1">Đang mở bán</div>
            <div class="text-2xl font-bold text-green-700">
                <?= count(array_filter($schedules, fn($s) => $s['status'] == 'open')) ?>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="text-sm text-red-600 mb-1">Đóng bán</div>
            <div class="text-2xl font-bold text-red-700">
                <?= count(array_filter($schedules, fn($s) => $s['status'] == 'closed')) ?>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="text-sm text-blue-600 mb-1">Hoàn thành</div>
            <div class="text-2xl font-bold text-blue-700">
                <?= count(array_filter($schedules, fn($s) => $s['status'] == 'completed')) ?>
            </div>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <div class="text-sm text-gray-600 mb-1">Tổng lịch</div>
            <div class="text-2xl font-bold text-gray-700"><?= $total ?></div>
        </div>
    </div>
    <!-- FILTER -->
    <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="schedules">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tour</label>
                <select name="tour_id" class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    <option value="">-- Tất cả Tour --</option>
                    <?php foreach ($tours as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($filters['tour_id'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['tour_code'] . ' - ' . $t['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                <input type="date" name="start_date" value="<?= $filters['start_date'] ?? '' ?>"
                    class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select name="status" class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    <option value="">-- Tất cả --</option>
                    <option value="open" <?= ($filters['status'] ?? '') == 'open' ? 'selected' : '' ?>>Đang mở bán</option>
                    <option value="closed" <?= ($filters['status'] ?? '') == 'closed' ? 'selected' : '' ?>>Đóng bán
                    </option>
                    <option value="completed" <?= ($filters['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Hoàn thành
                    </option>
                    <option value="cancelled" <?= ($filters['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Đã hủy
                    </option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
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
                    <th class="px-4 py-3 border-b">Khởi hành</th>
                    <th class="px-4 py-3 border-b">Kết thúc</th>
                    <th class="px-4 py-3 border-b text-center">Chỗ</th>
                    <th class="px-4 py-3 border-b text-center">Đã đặt</th>
                    <th class="px-4 py-3 border-b text-center">Còn lại</th>
                    <th class="px-4 py-3 border-b text-right">Giá (NL)</th>
                    <th class="px-4 py-3 border-b">HDV</th>
                    <th class="px-4 py-3 border-b text-center">Trạng thái</th>
                    <th class="px-4 py-3 border-b text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="11" class="px-6 py-8 text-center text-gray-500">
                            Chưa có lịch khởi hành nào.
                            <a href="?act=admin&module=schedules&action=create"
                                class="text-accent hover:underline ml-2">Thêm mới</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($schedules as $s):
                        $available = $s['quota'] - $s['booked'];
                        $fill_rate = $s['quota'] > 0 ? round(($s['booked'] / $s['quota']) * 100, 1) : 0;
                        ?>
                        <tr
                            class="hover:bg-gray-50 <?= $available <= 0 ? 'bg-red-50' : ($fill_rate >= 80 ? 'bg-yellow-50' : '') ?>">
                            <td class="px-4 py-3 font-mono text-sm text-blue-600">
                                <?= htmlspecialchars($s['tour_code']) ?>
                            </td>
                            <td class="px-4 py-3 font-medium">
                                <a href="?act=admin&module=schedules&action=show&id=<?= $s['id'] ?>"
                                    class="text-gray-800 hover:text-accent hover:underline">
                                    <?= htmlspecialchars($s['tour_name']) ?>
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-green-700 font-bold"><?= date('d/m/Y', strtotime($s['start_date'])) ?></span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                <?= date('d/m/Y', strtotime($s['end_date'])) ?>
                            </td>
                            <td class="px-4 py-3 text-center font-medium"><?= $s['quota'] ?></td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-bold <?= $fill_rate >= 80 ? 'text-yellow-600' : 'text-accent' ?>">
                                    <?= $s['booked'] ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="font-bold <?= $available <= 0 ? 'text-red-600' : ($available <= 5 ? 'text-yellow-600' : 'text-green-600') ?>">
                                    <?= $available ?>
                                </span>
                                <?php if ($fill_rate > 0): ?>
                                    <div class="text-xs text-gray-400"><?= $fill_rate ?>%</div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right font-medium">
                                <?= number_format($s['adult_price'], 0, ',', '.') ?> đ
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
                                <span
                                    class="px-2 py-1 text-xs rounded-full font-medium
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
                                    <a href="?act=admin&module=schedules&action=show&id=<?= $s['id'] ?>"
                                        class="text-blue-600 hover:text-blue-800" title="Xem chi tiết">
                                        👁️
                                    </a>
                                    <a href="?act=admin&module=schedules&action=edit&id=<?= $s['id'] ?>"
                                        class="text-green-600 hover:text-green-800" title="Sửa">
                                        ✏️
                                    </a>
                                    <?php if ($s['status'] == 'open'): ?>
                                        <form method="POST" action="?act=admin&module=schedules&action=changeStatus" class="inline">
                                            <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                            <input type="hidden" name="status" value="closed">
                                            <button type="submit" class="text-orange-600 hover:text-orange-800" title="Đóng bán"
                                                onclick="return confirm('<?= $s['booked'] > 0 ? 'Lịch này đang có ' . $s['booked'] . ' khách đã đặt. Bạn có chắc muốn đóng bán? (Sẽ không nhận thêm booking mới)' : 'Đóng bán lịch này?' ?>')">
                                                🔒
                                            </button>
                                        </form>
                                    <?php elseif ($s['status'] == 'closed'): ?>
                                        <form method="POST" action="?act=admin&module=schedules&action=changeStatus" class="inline">
                                            <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                            <input type="hidden" name="status" value="open">
                                            <button type="submit" class="text-green-600 hover:text-green-800" title="Mở lại"
                                                onclick="return confirm('Mở lại lịch này?')">
                                                🔓
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($s['booked'] == 0): ?>
                                        <a href="?act=admin&module=schedules&action=delete&id=<?= $s['id'] ?>"
                                            class="text-red-600 hover:text-red-800" title="Xóa"
                                            onclick="return confirm('Xóa lịch này?')">
                                            🗑️
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
    <?php if ($total_pages > 1): ?>
        <div class="mt-4 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?act=admin&module=schedules&page=<?= $i ?>&tour_id=<?= $filters['tour_id'] ?? '' ?>&start_date=<?= $filters['start_date'] ?? '' ?>&status=<?= $filters['status'] ?? '' ?>"
                    class="px-3 py-1 rounded <?= $i == $current_page ? 'bg-accent text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>


</div>