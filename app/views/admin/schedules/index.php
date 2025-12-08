<?php
/**
 * ADMIN - QUẢN LÝ LỊCH KHỞI HÀNH
 * Variables: $schedules, $tours, $total, $total_pages, $current_page, $filters
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Lịch Khởi Hành</h1>
        <a href="?act=admin&module=schedules&action=create"
            class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Thêm Lịch Mới
        </a>
    </div>
    <!-- Summary Stats -->
    <div class="mt-4 lg:mt-6 grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
        <div class="bg-success-bg border border-success rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-success-text mb-1 font-semibold">Đang mở bán</div>
            <div class="text-xl lg:text-2xl font-bold text-success-text">
                <?= count(array_filter($schedules, fn($s) => $s['status'] == 'open')) ?>
            </div>
        </div>
        <div class="bg-danger-bg border border-danger rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-danger-text mb-1 font-semibold">Đóng bán</div>
            <div class="text-xl lg:text-2xl font-bold text-danger-text">
                <?= count(array_filter($schedules, fn($s) => $s['status'] == 'closed')) ?>
            </div>
        </div>
        <div class="bg-info-bg border border-info rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-info-text mb-1 font-semibold">Hoàn thành</div>
            <div class="text-xl lg:text-2xl font-bold text-info-text">
                <?= count(array_filter($schedules, fn($s) => $s['status'] == 'completed')) ?>
            </div>
        </div>
        <div class="bg-primary-50 border border-primary-100 rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-primary-500 mb-1 font-semibold">Tổng lịch</div>
            <div class="text-xl lg:text-2xl font-bold text-primary-700"><?= $total ?></div>
        </div>
    </div>
    <!-- FILTER -->
    <div class="bg-panel p-4 lg:p-5 rounded-2xl shadow-sm border border-primary-100 mb-4 lg:mb-6">
        <form method="GET" class="grid grid-cols-1 lg:grid-cols-4 gap-3 lg:gap-4">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="schedules">

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tour</label>
                <select name="tour_id" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Tất cả Tour --</option>
                    <?php foreach ($tours as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($filters['tour_id'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['tour_code'] . ' - ' . $t['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Từ ngày</label>
                <input type="date" name="start_date" value="<?= $filters['start_date'] ?? '' ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
            </div>

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
                <select name="status" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
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
                <button type="submit" class="w-full px-4 lg:px-5 py-2 lg:py-2.5 bg-accent hover:bg-opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Lọc
                </button>
            </div>
        </form>
    </div>

    <!-- TABLE -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead class="bg-primary-50 text-primary-700 uppercase text-xs font-bold">
                <tr>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Mã Tour</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Tên Tour</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Khởi hành</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Kết thúc</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-center">Chỗ</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-center">Đã đặt</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-center">Còn lại</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-right">Giá (NL)</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">HDV</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-center">Trạng thái</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary-100">
                <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="11" class="px-3 lg:px-4 py-6 lg:py-8 text-center text-primary-500 text-sm">
                            Chưa có lịch khởi hành nào.
                            <a href="?act=admin&module=schedules&action=create"
                                class="text-accent hover:text-accent-hover font-semibold ml-2">Thêm mới</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($schedules as $s):
                        $available = $s['quota'] - $s['booked'];
                        $fill_rate = $s['quota'] > 0 ? round(($s['booked'] / $s['quota']) * 100, 1) : 0;
                        ?>
                        <tr
                            class="hover:bg-primary-50 transition-colors <?= $available <= 0 ? 'bg-danger-bg/30' : ($fill_rate >= 80 ? 'bg-warning-bg/30' : '') ?>">
                            <td class="px-3 lg:px-4 py-2 lg:py-3 font-mono text-sm text-accent">
                                <?= htmlspecialchars($s['tour_code']) ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">
                                <a href="?act=admin&module=schedules&action=show&id=<?= $s['id'] ?>"
                                    class="text-primary-700 hover:text-accent hover:underline text-sm">
                                    <?= htmlspecialchars($s['tour_name']) ?>
                                </a>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3">
                                <span class="text-success-text font-bold text-sm"><?= date('d/m/Y', strtotime($s['start_date'])) ?></span>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 text-sm">
                                <?= date('d/m/Y', strtotime($s['end_date'])) ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-center font-semibold text-sm"><?= $s['quota'] ?></td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                <span class="font-bold text-sm <?= $fill_rate >= 80 ? 'text-warning-text' : 'text-accent' ?>">
                                    <?= $s['booked'] ?>
                                </span>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                <span
                                    class="font-bold text-sm <?= $available <= 0 ? 'text-danger-text' : ($available <= 5 ? 'text-warning-text' : 'text-success-text') ?>">
                                    <?= $available ?>
                                </span>
                                <?php if ($fill_rate > 0): ?>
                                    <div class="text-xs text-primary-400"><?= $fill_rate ?>%</div>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-right font-semibold text-sm">
                                <?= number_format($s['adult_price'], 0, ',', '.') ?> đ
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
                                <span
                                    class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase
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
                                <div class="flex items-center justify-end gap-1 lg:gap-2 flex-wrap">
                                    <a href="?act=admin&module=schedules&action=show&id=<?= $s['id'] ?>"
                                        class="text-accent hover:text-accent-hover p-1.5 rounded-xl hover:bg-primary-50 transition-all" title="Xem chi tiết">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="?act=admin&module=schedules&action=edit&id=<?= $s['id'] ?>"
                                        class="text-warning-text hover:text-warning-text p-1.5 rounded-xl hover:bg-warning-bg transition-all" title="Sửa">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <!-- Đóng/Mở -->
                                    <?php if ($s['status'] == 'open'): ?>
                                        <form method="POST" action="?act=admin&module=schedules&action=changeStatus" class="inline">
                                            <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                            <input type="hidden" name="status" value="closed">
                                            <button type="submit" class="text-warning-text hover:text-warning-text p-1.5 rounded-xl hover:bg-warning-bg transition-all" title="Đóng bán"
                                                onclick="return confirm('<?= $s['booked'] > 0 ? 'Lịch này đang có ' . $s['booked'] . ' khách đã đặt. Bạn có chắc muốn đóng bán? (Sẽ không nhận thêm booking mới)' : 'Đóng bán lịch này?' ?>')">
                                                <i data-lucide="lock" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    <?php elseif ($s['status'] == 'closed'): ?>
                                        <form method="POST" action="?act=admin&module=schedules&action=changeStatus" class="inline">
                                            <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                            <input type="hidden" name="status" value="open">
                                            <button type="submit" class="text-success-text hover:text-success-text p-1.5 rounded-xl hover:bg-success-bg transition-all" title="Mở lại"
                                                onclick="return confirm('Mở lại lịch này?')">
                                                <i data-lucide="unlock" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <!-- Hủy lịch trình (chỉ khi status != completed) -->
                                    <?php if ($s['status'] != 'completed' && $s['status'] != 'cancelled'): ?>
                                        <a href="?act=admin&module=schedules&action=cancelForm&id=<?= $s['id'] ?>"
                                            class="text-danger-text hover:text-danger-text p-1.5 rounded-xl hover:bg-danger-bg transition-all" title="Hủy lịch trình">
                                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                        </a>
                                    <?php endif; ?>
                                    <!-- Xóa (chỉ khi không có booking) -->
                                    <?php if ($s['booked'] == 0 && $s['status'] != 'completed'): ?>
                                        <a href="?act=admin&module=schedules&action=delete&id=<?= $s['id'] ?>"
                                            class="text-danger-text hover:text-danger-text p-1.5 rounded-xl hover:bg-danger-bg transition-all" title="Xóa"
                                            onclick="return confirm('Xóa lịch này?')">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
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
    <?php if ($total_pages > 1): ?>
        <div class="mt-4 flex justify-center gap-2 flex-wrap">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?act=admin&module=schedules&page=<?= $i ?>&tour_id=<?= $filters['tour_id'] ?? '' ?>&start_date=<?= $filters['start_date'] ?? '' ?>&status=<?= $filters['status'] ?? '' ?>"
                    class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-sm font-semibold transition-all <?= $i == $current_page ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white shadow-sm' : 'bg-panel border border-primary-100 text-primary-700 hover:bg-primary-50' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>


</div>