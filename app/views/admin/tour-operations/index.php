<?php
/**
 * ADMIN - QUẢN LÝ TOUR ĐÃ CHỐT
 * Hiển thị các tour đã đủ số người tối thiểu (>= min_participants)
 * Thao tác chỉ được phép khi tour đóng hoặc đã qua deadline booking
 */
if (!is_admin())
    redirect('?act=access-denied');
?>
<div class="max-w-8xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý Tour Đã Chốt</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Các tour đã đủ số người tối thiểu (>= min_participants).
                Thao tác chỉ được phép khi tour đóng hoặc đã qua deadline booking.</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-panel p-4 lg:p-5 rounded-2xl shadow-sm border border-primary-100 mb-4 lg:mb-6">
        <form method="GET" class="grid grid-cols-1 lg:grid-cols-5 gap-3 lg:gap-4">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="tour-operations">

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tour</label>
                <select name="tour_id"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Tất cả Tour --</option>
                    <?php if (!empty($allTours)): ?>
                        <?php foreach ($allTours as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= ($filters['tour_id'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['tour_code'] . ' - ' . $t['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Từ ngày</label>
                <input type="date" name="start_date_from" value="<?= $filters['start_date_from'] ?? '' ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
            </div>

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Đến ngày</label>
                <input type="date" name="start_date_to" value="<?= $filters['start_date_to'] ?? '' ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
            </div>

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
                <select name="status"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Tất cả --</option>
                    <option value="open" <?= ($filters['status'] ?? '') == 'open' ? 'selected' : '' ?>>Đang mở bán</option>
                    <option value="closed" <?= ($filters['status'] ?? '') == 'closed' ? 'selected' : '' ?>>Đóng bán
                    </option>
                    <option value="confirmed" <?= ($filters['status'] ?? '') == 'confirmed' ? 'selected' : '' ?>>Đã xác
                        nhận</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit"
                    class="w-full px-4 lg:px-5 py-2 lg:py-2.5 bg-accent hover:bg-opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Lọc
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead class="bg-primary-50 text-primary-700 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Mã Tour</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Tên Tour</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Khởi hành</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-center">Số người đã thanh
                            toán</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">HDV</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Xe</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Phân phòng</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-center">Trạng thái</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-100">
                    <?php
                    // Debug: Kiểm tra biến
                    if (!isset($tours)) {
                        echo '<tr><td colspan="9" class="px-3 lg:px-4 py-6 lg:py-8 text-center text-red-500 text-sm">Lỗi: Biến $tours không tồn tại</td></tr>';
                    } elseif (empty($tours)) {
                        ?>
                        <tr>
                            <td colspan="9" class="px-3 lg:px-4 py-6 lg:py-8 text-center text-primary-500 text-sm">
                                Chưa có tour nào đã chốt. (Tổng: <?= $total ?? 0 ?>)
                            </td>
                        </tr>
                        <?php
                    } else {
                        foreach ($tours as $tour):
                            ?>
                            <tr class="hover:bg-primary-50 transition-colors">
                                <td class="px-3 lg:px-4 py-2 lg:py-3 font-mono text-sm text-accent">
                                    <?= htmlspecialchars($tour['tour_code']) ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 font-semibold text-sm">
                                    <?= htmlspecialchars($tour['tour_name']) ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm">
                                    <span
                                        class="text-success-text font-bold"><?= date('d/m/Y', strtotime($tour['start_date'])) ?></span>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                    <span class="font-bold text-accent text-sm"><?= $tour['total_paid_participants'] ?></span>
                                    <div class="text-xs text-primary-400"><?= $tour['total_paid_bookings'] ?> booking</div>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <?php if (!empty($tour['guide_name'])): ?>
                                        <span class="text-xs text-info-text flex items-center gap-1">
                                            <i data-lucide="user-check" class="w-3 h-3"></i>
                                            <?= htmlspecialchars($tour['guide_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-warning-text">Chưa gán</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <?php if ($tour['vehicle_count'] > 0): ?>
                                        <span class="text-xs text-info-text flex items-center gap-1">
                                            <i data-lucide="truck" class="w-3 h-3"></i>
                                            <?= $tour['vehicle_count'] ?> xe
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-warning-text">Chưa gán</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <?php if ($tour['room_assignment_count'] > 0): ?>
                                        <span class="text-xs text-info-text flex items-center gap-1">
                                            <i data-lucide="home" class="w-3 h-3"></i>
                                            Đã phân phòng
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-warning-text">Chưa phân phòng</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                    <?php
                                    // Kiểm tra có thể thao tác không (tour đóng hoặc qua deadline)
                                    $deadlineDate = date('Y-m-d', strtotime($tour['start_date'] . ' -' . ($tour['booking_deadline_days'] ?? 1) . ' days'));
                                    $canOperate = ($tour['status'] == 'closed') || (date('Y-m-d') >= $deadlineDate);

                                    if ($canOperate) {
                                        $statusColors = [
                                            'ready' => 'bg-success-bg text-success-text',
                                            'in_progress' => 'bg-warning-bg text-warning-text',
                                            'not_started' => 'bg-danger-bg text-danger-text'
                                        ];
                                        $statusLabels = [
                                            'ready' => 'Sẵn sàng',
                                            'in_progress' => 'Đang xử lý',
                                            'not_started' => 'Chưa bắt đầu'
                                        ];
                                        $status = $statusLabels[$tour['operations_status']] ?? $tour['operations_status'];
                                    } else {
                                        $status = 'Chờ đóng/Hết deadline';
                                        $statusColors = ['default' => 'bg-primary-100 text-primary-500'];
                                    }
                                    ?>
                                    <span
                                        class="px-2 py-1 rounded-lg text-xs font-semibold <?= $canOperate ? ($statusColors[$tour['operations_status']] ?? 'bg-primary-100 text-primary-700') : 'bg-primary-100 text-primary-500' ?>">
                                        <?= $status ?>
                                    </span>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-right">
                                    <a href="?act=admin&module=tour-operations&action=show&id=<?= $tour['id'] ?>"
                                        class="px-3 py-1 bg-accent hover:opacity-90 text-white rounded-lg text-xs font-semibold transition-all">
                                        Quản lý
                                    </a>
                                </td>
                            </tr>
                        <?php
                        endforeach;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="mt-4 flex justify-center">
            <div class="flex gap-2">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?act=admin&module=tour-operations&page=<?= $i ?><?= !empty($_GET['tour_id']) ? '&tour_id=' . $_GET['tour_id'] : '' ?><?= !empty($_GET['start_date_from']) ? '&start_date_from=' . $_GET['start_date_from'] : '' ?><?= !empty($_GET['start_date_to']) ? '&start_date_to=' . $_GET['start_date_to'] : '' ?><?= !empty($_GET['status']) ? '&status=' . $_GET['status'] : '' ?>"
                        class="px-3 py-1 rounded-lg <?= $i == $current_page ? 'bg-accent text-white' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' ?> text-sm font-semibold">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>