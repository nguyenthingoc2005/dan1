<?php
/**
 * ADMIN - DANH SÁCH BOOKING
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý Đặt Tour</h1>
        <a href="?act=admin&module=bookings&action=create"
            class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Tạo Booking mới
        </a>
    </div>

    <!-- Status Tabs - Responsive -->
    <div class="mb-4 lg:mb-6 border-b border-primary-100 overflow-x-auto">
        <nav class="-mb-px flex space-x-4 lg:space-x-8" aria-label="Tabs">
            <?php
            $current_status = $_GET['status'] ?? '';
            $tabs = [
                '' => 'Tất cả',
                'unpaid' => 'Chờ thanh toán',
                'partial' => 'Đã cọc',
                'paid' => 'Đã thanh toán',
                'rejected' => 'Từ chối',
                'cancelled' => 'Đã hủy',
                'refunded' => 'Đã hoàn tiền'
            ];
            ?>
            <?php foreach ($tabs as $key => $label): ?>
                <a href="?act=admin&module=bookings&status=<?= $key ?>" class="<?= $current_status == $key
                      ? 'border-accent text-accent'
                      : 'border-transparent text-primary-500 hover:text-primary-700 hover:border-primary-300' ?> 
                       whitespace-nowrap py-3 lg:py-4 px-1 border-b-2 font-semibold text-xs lg:text-sm">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Search & Filter - Responsive -->
    <form method="GET" class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100 mb-4 lg:mb-6">
        <input type="hidden" name="act" value="admin">
        <input type="hidden" name="module" value="bookings">
        <input type="hidden" name="status" value="<?= $current_status ?>">

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <div class="lg:col-span-1">
                <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>"
                    placeholder="Mã booking, tên khách, SĐT..."
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
            </div>
            <div class="lg:col-span-1">
                <select name="tour_id"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Tất cả Tour --</option>
                    <?php foreach ($tours as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($_GET['tour_id'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['tour_code'] . ' - ' . $t['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="lg:col-span-1">
                <input type="date" name="start_date" value="<?= $_GET['start_date'] ?? '' ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
            </div>
            <div>
                <button type="submit"
                    class="w-full px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base">
                    <i data-lucide="search" class="w-4 h-4 inline mr-2"></i>
                    Lọc dữ liệu
                </button>
            </div>
        </div>
    </form>

    <!-- Table - Responsive -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-primary-50">
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Mã Booking</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Thông tin Tour</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Khách hàng</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Ngày đi</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider text-right">
                            Tổng tiền</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider text-center">
                            Trạng thái</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider text-right">
                            Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-primary-500">
                                Chưa có booking nào.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $b): ?>
                            <tr class="border-b border-primary-100 hover:bg-primary-50 transition-colors">
                                <td class="px-3 lg:px-4 py-3 lg:py-4">
                                    <a href="?act=admin&module=bookings&action=show&id=<?= $b['id'] ?>"
                                        class="font-mono font-bold text-accent text-sm hover:text-accent-hover transition-colors">
                                        <?= htmlspecialchars($b['booking_code']) ?>
                                    </a>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4">
                                    <div class="font-semibold text-primary-700 text-sm"><?= htmlspecialchars($b['tour_name']) ?>
                                    </div>
                                    <div class="text-xs text-primary-500"><?= htmlspecialchars($b['tour_code']) ?></div>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4">
                                    <div class="font-semibold text-primary-700 text-sm">
                                        <?= htmlspecialchars($b['customer_name']) ?></div>
                                    <div class="text-xs text-primary-500"><?= htmlspecialchars($b['customer_phone']) ?></div>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4 text-primary-700 text-sm">
                                    <?= date('d/m/Y', strtotime($b['start_date'])) ?>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4 text-right font-bold text-primary-700 text-sm">
                                    <?= number_format($b['final_amount'], 0, ',', '.') ?> đ
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4 text-center">
                                    <!-- Payment Status (đã gộp approval_status) -->
                                    <?php
                                    switch ($b['payment_status']) {
                                        case 'unpaid':
                                            echo '<span class="block px-3 py-1 bg-warning-bg text-warning-text text-xs font-bold rounded-full mb-1">Chờ thanh toán</span>';
                                            break;
                                        case 'partial':
                                            echo '<span class="block px-3 py-1 bg-warning-bg text-warning-text text-xs font-bold rounded-full mb-1">Đã cọc</span>';
                                            break;
                                        case 'paid':
                                            echo '<span class="block px-3 py-1 bg-success-bg text-success-text text-xs font-bold rounded-full mb-1">Đã thanh toán</span>';
                                            break;
                                        case 'rejected':
                                            echo '<span class="block px-3 py-1 bg-primary-100 text-primary-500 text-xs font-bold rounded-full mb-1">Từ chối</span>';
                                            break;
                                        case 'cancelled':
                                            echo '<span class="block px-3 py-1 bg-danger-bg text-danger-text text-xs font-bold rounded-full mb-1">Đã hủy</span>';
                                            break;
                                        case 'refunded':
                                            echo '<span class="block px-3 py-1 bg-info-bg text-info-text text-xs font-bold rounded-full mb-1">Đã hoàn tiền</span>';
                                            break;
                                        default:
                                            echo '<span class="block px-3 py-1 bg-primary-100 text-primary-500 text-xs font-bold rounded-full mb-1">' . htmlspecialchars($b['payment_status']) . '</span>';
                                    }
                                    ?>

                                    <?php
                                    $daysToStart = (strtotime($b['start_date']) - time()) / (60 * 60 * 24);
                                    if ($daysToStart <= 1 && !in_array($b['payment_status'], ['paid', 'cancelled', 'refunded'])):
                                        ?>
                                        <div
                                            class="mt-1 text-[10px] font-bold text-danger flex items-center justify-center gap-1 animate-pulse">
                                            <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                            <span>Sắp khởi hành</span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4 text-right whitespace-nowrap">
                                    <a href="?act=admin&module=bookings&action=show&id=<?= $b['id'] ?>"
                                        class="text-accent hover:text-accent-hover font-semibold text-xs lg:text-sm border border-primary-100 bg-primary-50 px-3 py-1.5 rounded-xl hover:bg-primary-100 transition-all inline-flex items-center gap-1">
                                        <i data-lucide="eye" class="w-3 h-3"></i>
                                        Chi tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination - Responsive -->
    <?php if ($total_pages > 1): ?>
        <div class="mt-4 lg:mt-6 flex justify-center gap-2 flex-wrap">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?act=admin&module=bookings&page=<?= $i ?>&search=<?= $_GET['search'] ?? '' ?>&status=<?= $_GET['status'] ?? '' ?>&tour_id=<?= $_GET['tour_id'] ?? '' ?>"
                    class="px-3 py-1.5 rounded-xl text-sm font-semibold transition-colors <?= $i == $page ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white' : 'bg-panel border border-primary-100 text-primary-700 hover:bg-primary-50' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>