<?php
/**
 * ADMIN - CHI TIẾT KHÁCH HÀNG
 */

// Helper function for currency if not exists
if (!function_exists('format_currency')) {
    function format_currency($amount)
    {
        return number_format($amount, 0, ',', '.') . ' đ';
    }
}
?>
<div class="max-w-[95%] mx-auto">
    <!-- HEADER - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Chi tiết Khách hàng</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Mã KH:
                <?= htmlspecialchars($customer['customer_code'] ?? 'KH' . $customer['id']) ?></p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <a href="?act=admin&module=customers&action=edit&id=<?= $customer['id'] ?>"
                class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-warning hover:opacity-90 text-white rounded-xl font-semibold transition-all shadow-sm text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="pencil" class="w-4 h-4"></i>
                Sửa
            </a>
            <a href="?act=admin&module=customers"
                class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- LEFT COLUMN: INFO & STATS -->
        <div class="space-y-4 lg:space-y-6">
            <!-- Customer Info Card -->
            <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
                <div class="p-4 lg:p-6 border-b border-primary-100 bg-primary-50">
                    <div class="flex flex-col items-center">
                        <div
                            class="w-20 lg:w-24 h-20 lg:h-24 rounded-full bg-info-bg flex items-center justify-center text-info-text text-2xl lg:text-3xl font-bold mb-3 ring-4 ring-white shadow-sm">
                            <?= strtoupper(substr($customer['full_name'], 0, 1)) ?>
                        </div>
                        <h2 class="text-lg lg:text-xl font-bold text-primary-700"><?= htmlspecialchars($customer['full_name']) ?></h2>
                        <span class="text-xs lg:text-sm text-primary-500 mt-1">
                            <?= $customer['customer_type'] == 'corporate' ? 'Doanh nghiệp' : ($customer['customer_type'] == 'group' ? 'Nhóm' : 'Cá nhân') ?>
                        </span>
                    </div>
                </div>
                <div class="p-4 lg:p-6">
                    <ul class="space-y-3 lg:space-y-4">
                        <li
                            class="flex justify-between items-center border-b border-primary-100 pb-3 last:border-0 last:pb-0">
                            <span class="text-xs lg:text-sm text-primary-500 flex items-center gap-2"><i data-lucide="phone" class="w-4 h-4"></i> Điện thoại</span>
                            <span class="font-semibold text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($customer['phone']) ?></span>
                        </li>
                        <li
                            class="flex justify-between items-center border-b border-primary-100 pb-3 last:border-0 last:pb-0">
                            <span class="text-xs lg:text-sm text-primary-500 flex items-center gap-2"><i data-lucide="mail" class="w-4 h-4"></i> Email</span>
                            <span
                                class="font-semibold text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($customer['email'] ?? '---') ?></span>
                        </li>
                        <li
                            class="flex justify-between items-center border-b border-primary-100 pb-3 last:border-0 last:pb-0">
                            <span class="text-xs lg:text-sm text-primary-500 flex items-center gap-2"><i data-lucide="user" class="w-4 h-4"></i> Giới tính</span>
                            <span class="font-semibold text-primary-700 text-sm lg:text-base">
                                <?= $customer['gender'] == 'male' ? 'Nam' : ($customer['gender'] == 'female' ? 'Nữ' : 'Khác') ?>
                            </span>
                        </li>
                        <li
                            class="flex justify-between items-center border-b border-primary-100 pb-3 last:border-0 last:pb-0">
                            <span class="text-xs lg:text-sm text-primary-500 flex items-center gap-2"><i data-lucide="calendar" class="w-4 h-4"></i> Ngày sinh</span>
                            <span class="font-semibold text-primary-700 text-sm lg:text-base">
                                <?= !empty($customer['date_of_birth']) ? date('d/m/Y', strtotime($customer['date_of_birth'])) : '---' ?>
                            </span>
                        </li>
                        <li class="border-b border-primary-100 pb-3 last:border-0 last:pb-0">
                            <span class="text-xs lg:text-sm text-primary-500 block mb-1 flex items-center gap-2"><i data-lucide="map-pin" class="w-4 h-4"></i> Địa chỉ</span>
                            <span
                                class="font-semibold text-primary-700 pl-6 block text-sm lg:text-base"><?= htmlspecialchars($customer['address'] ?? '---') ?></span>
                        </li>
                        <li>
                            <span class="text-xs lg:text-sm text-primary-500 block mb-1 flex items-center gap-2"><i data-lucide="file-text" class="w-4 h-4"></i> Ghi chú</span>
                            <div class="text-xs lg:text-sm text-primary-600 bg-primary-50 p-3 rounded-xl pl-6 italic">
                                <?= nl2br(htmlspecialchars($customer['notes'] ?? 'Không có ghi chú')) ?>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
                <div class="p-4 border-b border-primary-100">
                    <h3 class="font-bold text-primary-700 text-sm lg:text-base">Thống kê</h3>
                </div>
                <div class="p-4 lg:p-6 grid grid-cols-2 gap-4 text-center">
                    <div>
                        <div class="text-xl lg:text-2xl font-bold text-primary-700"><?= count($bookings ?? []) ?></div>
                        <div class="text-xs font-bold text-primary-500 uppercase mt-1">Lần đặt tour</div>
                    </div>
                    <div>
                        <div class="text-xl lg:text-2xl font-bold text-success-text">
                            <?= format_currency($customer['total_spent'] ?? 0) ?></div>
                        <div class="text-xs font-bold text-success-text uppercase mt-1">Tổng chi tiêu</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: BOOKING HISTORY -->
        <div class="lg:col-span-2">
            <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden h-full">
                <div class="p-4 lg:p-6 border-b border-primary-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <h3 class="font-bold text-primary-700 text-base lg:text-lg">Lịch sử đặt tour</h3>
                    <a href="?act=admin&module=bookings&action=create&customer_id=<?= $customer['id'] ?>"
                        class="w-full sm:w-auto px-3 lg:px-4 py-1.5 lg:py-2 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white text-xs lg:text-sm font-semibold rounded-xl transition-all shadow-sm flex items-center justify-center gap-1">
                        <i data-lucide="plus" class="w-3 h-3"></i>
                        Đặt tour mới
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[600px]">
                        <thead class="bg-primary-50 text-primary-700 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Mã Booking</th>
                                <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Tour</th>
                                <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Ngày đi</th>
                                <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-right">Tổng tiền</th>
                                <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-center">Trạng thái</th>
                                <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary-100">
                            <?php if (!empty($bookings)): ?>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr class="hover:bg-primary-50 transition-colors">
                                        <td class="px-3 lg:px-4 py-2 lg:py-3">
                                            <a href="?act=admin&module=bookings&action=show&id=<?= $booking['id'] ?>"
                                                class="font-mono font-bold text-accent hover:text-accent-hover text-sm">
                                                <?= htmlspecialchars($booking['booking_code']) ?>
                                            </a>
                                        </td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 font-semibold text-primary-700 text-sm">
                                            <?= htmlspecialchars($booking['tour_name'] ?? '---') ?>
                                        </td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 text-sm">
                                            <?= date('d/m/Y', strtotime($booking['start_date'])) ?>
                                        </td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-right font-bold text-success-text text-sm">
                                            <?= format_currency($booking['final_amount']) ?>
                                        </td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                            <?php
                                            $statusClass = 'bg-primary-100 text-primary-500';
                                            $statusText = 'Chờ duyệt';
                                            // Sử dụng payment_status thay vì approval_status
                                            $statusClass = get_payment_status_color($booking['payment_status']);
                                            $statusText = payment_status_text($booking['payment_status']);
                                            ?>
                                            <span
                                                class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase <?= $statusClass ?>">
                                                <?= $statusText ?>
                                            </span>
                                        </td>
                                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                            <a href="?act=admin&module=bookings&action=show&id=<?= $booking['id'] ?>"
                                                class="text-accent hover:text-accent-hover p-2 rounded-xl hover:bg-primary-50 transition-all" title="Xem chi tiết">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-3 lg:px-4 py-6 lg:py-8 text-center text-primary-500 italic text-sm">
                                        Khách hàng này chưa có lịch sử đặt tour nào.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>