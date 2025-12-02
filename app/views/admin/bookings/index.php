<?php
/**
 * ADMIN - DANH SÁCH BOOKING
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Quản lý Đặt Tour</h1>
        <a href="?act=admin&module=bookings&action=create"
            class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600 shadow flex items-center gap-2">
            <span>+</span> Tạo Booking mới
        </a>
    </div>

    <!-- Status Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
            <?php
            $current_status = $_GET['status'] ?? '';
            $tabs = [
                '' => 'Tất cả',
                'pending' => 'Chờ duyệt',
                'approved' => 'Đã duyệt',
                'paid' => 'Đã thanh toán',
                'cancelled' => 'Đã hủy'
            ];
            ?>
            <?php foreach ($tabs as $key => $label): ?>
                <a href="?act=admin&module=bookings&status=<?= $key ?>" class="<?= $current_status == $key
                      ? 'border-accent text-accent'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> 
                       whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Search & Filter -->
    <form method="GET" class="bg-white p-4 rounded shadow-sm mb-6">
        <input type="hidden" name="act" value="admin">
        <input type="hidden" name="module" value="bookings">
        <input type="hidden" name="status" value="<?= $current_status ?>">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-1">
                <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>"
                    placeholder="Mã booking, tên khách, SĐT..."
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
            </div>
            <div class="md:col-span-1">
                <select name="tour_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                    <option value="">-- Tất cả Tour --</option>
                    <?php foreach ($tours as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($_GET['tour_id'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['tour_code'] . ' - ' . $t['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="md:col-span-1">
                <input type="date" name="start_date" value="<?= $_GET['start_date'] ?? '' ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                    🔍 Lọc dữ liệu
                </button>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-bold">
                <tr>
                    <th class="px-4 py-3 border-b">Mã Booking</th>
                    <th class="px-4 py-3 border-b">Thông tin Tour</th>
                    <th class="px-4 py-3 border-b">Khách hàng</th>
                    <th class="px-4 py-3 border-b">Ngày đi</th>
                    <th class="px-4 py-3 border-b text-right">Tổng tiền</th>
                    <th class="px-4 py-3 border-b text-center">Trạng thái</th>
                    <th class="px-4 py-3 border-b text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            Chưa có booking nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bookings as $b): ?>
                        <tr class="hover:bg-gray-50 border-b last:border-0">
                            <td class="px-4 py-3 font-mono font-bold text-accent">
                                <a href="?act=admin&module=bookings&action=show&id=<?= $b['id'] ?>">
                                    <?= htmlspecialchars($b['booking_code']) ?>
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900"><?= htmlspecialchars($b['tour_name']) ?></div>
                                <div class="text-xs text-gray-500"><?= htmlspecialchars($b['tour_code']) ?></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900"><?= htmlspecialchars($b['customer_name']) ?></div>
                                <div class="text-xs text-gray-500"><?= htmlspecialchars($b['customer_phone']) ?></div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <?= date('d/m/Y', strtotime($b['start_date'])) ?>
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-gray-800">
                                <?= number_format($b['final_amount'], 0, ',', '.') ?> đ
                            </td>
                            <td class="px-4 py-3 text-center">
                                <!-- Approval Status -->
                                <?php if ($b['approval_status'] == 'approved'): ?>
                                    <span class="block px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full mb-1">Đã
                                        duyệt</span>
                                <?php elseif ($b['approval_status'] == 'cancelled'): ?>
                                    <span class="block px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded-full mb-1">Đã hủy</span>
                                <?php elseif ($b['approval_status'] == 'rejected'): ?>
                                    <span class="block px-2 py-0.5 bg-gray-200 text-gray-700 text-xs rounded-full mb-1">Từ
                                        chối</span>
                                <?php else: ?>
                                    <span class="block px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full mb-1">Chờ
                                        duyệt</span>
                                <?php endif; ?>

                                <!-- Payment Status -->
                                <?php if ($b['payment_status'] == 'paid'): ?>
                                    <span class="text-[10px] font-bold text-green-600 uppercase">Đã thanh toán</span>
                                <?php elseif ($b['payment_status'] == 'partial'): ?>
                                    <span class="text-[10px] font-bold text-orange-500 uppercase">Đã cọc</span>
                                <?php else: ?>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">Chưa thanh toán</span>
                                <?php endif; ?>

                                <?php
                                $daysToStart = (strtotime($b['start_date']) - time()) / (60 * 60 * 24);
                                if ($daysToStart <= 1 && $b['payment_status'] != 'paid' && $b['approval_status'] != 'cancelled'):
                                    ?>
                                    <div
                                        class="mt-1 text-[10px] font-bold text-red-600 flex items-center justify-center gap-1 animate-pulse">
                                        <span>⚠ Sắp khởi hành</span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="?act=admin&module=bookings&action=show&id=<?= $b['id'] ?>"
                                    class="text-blue-600 hover:text-blue-800 font-medium text-sm border border-blue-200 bg-blue-50 px-3 py-1 rounded hover:bg-blue-100">
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="mt-6 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?act=admin&module=bookings&page=<?= $i ?>&search=<?= $_GET['search'] ?? '' ?>&status=<?= $_GET['status'] ?? '' ?>&tour_id=<?= $_GET['tour_id'] ?? '' ?>"
                    class="px-3 py-1 rounded <?= $i == $page ? 'bg-accent text-white' : 'bg-white border hover:bg-gray-100' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>