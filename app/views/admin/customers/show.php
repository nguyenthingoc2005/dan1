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
    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">Chi tiết Khách hàng</h1>
            <p class="text-sm text-gray-500">Mã KH:
                <?= htmlspecialchars($customer['customer_code'] ?? 'KH' . $customer['id']) ?></p>
        </div>
        <div class="flex gap-2">
            <a href="?act=admin&module=customers&action=edit&id=<?= $customer['id'] ?>"
                class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition-colors shadow-sm">
                <i class="fas fa-edit mr-1"></i> Sửa
            </a>
            <a href="?act=admin&module=customers"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- LEFT COLUMN: INFO & STATS -->
        <div class="space-y-6">
            <!-- Customer Info Card -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex flex-col items-center">
                        <div
                            class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-3xl font-bold mb-3 ring-4 ring-white shadow-sm">
                            <?= strtoupper(substr($customer['full_name'], 0, 1)) ?>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($customer['full_name']) ?></h2>
                        <span class="text-sm text-gray-500 mt-1">
                            <?= $customer['customer_type'] == 'corporate' ? 'Doanh nghiệp' : ($customer['customer_type'] == 'group' ? 'Nhóm' : 'Cá nhân') ?>
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <ul class="space-y-4">
                        <li
                            class="flex justify-between items-center border-b border-gray-50 pb-3 last:border-0 last:pb-0">
                            <span class="text-gray-500 text-sm"><i class="fas fa-phone-alt w-5"></i> Điện thoại</span>
                            <span class="font-medium text-gray-800"><?= htmlspecialchars($customer['phone']) ?></span>
                        </li>
                        <li
                            class="flex justify-between items-center border-b border-gray-50 pb-3 last:border-0 last:pb-0">
                            <span class="text-gray-500 text-sm"><i class="fas fa-envelope w-5"></i> Email</span>
                            <span
                                class="font-medium text-gray-800"><?= htmlspecialchars($customer['email'] ?? '---') ?></span>
                        </li>
                        <li
                            class="flex justify-between items-center border-b border-gray-50 pb-3 last:border-0 last:pb-0">
                            <span class="text-gray-500 text-sm"><i class="fas fa-venus-mars w-5"></i> Giới tính</span>
                            <span class="font-medium text-gray-800">
                                <?= $customer['gender'] == 'male' ? 'Nam' : ($customer['gender'] == 'female' ? 'Nữ' : 'Khác') ?>
                            </span>
                        </li>
                        <li
                            class="flex justify-between items-center border-b border-gray-50 pb-3 last:border-0 last:pb-0">
                            <span class="text-gray-500 text-sm"><i class="fas fa-birthday-cake w-5"></i> Ngày
                                sinh</span>
                            <span class="font-medium text-gray-800">
                                <?= !empty($customer['date_of_birth']) ? date('d/m/Y', strtotime($customer['date_of_birth'])) : '---' ?>
                            </span>
                        </li>
                        <li class="border-b border-gray-50 pb-3 last:border-0 last:pb-0">
                            <span class="text-gray-500 text-sm block mb-1"><i class="fas fa-map-marker-alt w-5"></i> Địa
                                chỉ</span>
                            <span
                                class="font-medium text-gray-800 pl-6 block"><?= htmlspecialchars($customer['address'] ?? '---') ?></span>
                        </li>
                        <li>
                            <span class="text-gray-500 text-sm block mb-1"><i class="fas fa-sticky-note w-5"></i> Ghi
                                chú</span>
                            <div class="text-sm text-gray-600 bg-gray-50 p-3 rounded pl-6 italic">
                                <?= nl2br(htmlspecialchars($customer['notes'] ?? 'Không có ghi chú')) ?>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800">Thống kê</h3>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold text-gray-800"><?= count($bookings ?? []) ?></div>
                        <div class="text-xs font-bold text-gray-500 uppercase mt-1">Lần đặt tour</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-600">
                            <?= format_currency($customer['total_spent'] ?? 0) ?></div>
                        <div class="text-xs font-bold text-green-600 uppercase mt-1">Tổng chi tiêu</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: BOOKING HISTORY -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden h-full">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 text-lg">Lịch sử đặt tour</h3>
                    <a href="?act=admin&module=bookings&action=create&customer_id=<?= $customer['id'] ?>"
                        class="px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition-colors shadow-sm">
                        <i class="fas fa-plus mr-1"></i> Đặt tour mới
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-6 py-3 border-b">Mã Booking</th>
                                <th class="px-6 py-3 border-b">Tour</th>
                                <th class="px-6 py-3 border-b">Ngày đi</th>
                                <th class="px-6 py-3 border-b text-right">Tổng tiền</th>
                                <th class="px-6 py-3 border-b text-center">Trạng thái</th>
                                <th class="px-6 py-3 border-b text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (!empty($bookings)): ?>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <a href="?act=admin&module=bookings&action=show&id=<?= $booking['id'] ?>"
                                                class="font-mono font-bold text-blue-600 hover:text-blue-800">
                                                <?= htmlspecialchars($booking['booking_code']) ?>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-800">
                                            <?= htmlspecialchars($booking['tour_name'] ?? '---') ?>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">
                                            <?= date('d/m/Y', strtotime($booking['start_date'])) ?>
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-green-600">
                                            <?= format_currency($booking['final_amount']) ?>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <?php
                                            $statusClass = 'bg-gray-100 text-gray-800';
                                            $statusText = 'Chờ duyệt';
                                            // Sử dụng payment_status thay vì approval_status
                                            $statusClass = get_payment_status_color($booking['payment_status']);
                                            $statusText = payment_status_text($booking['payment_status']);
                                            ?>
                                            <span
                                                class="px-2 py-1 rounded-full text-xs font-bold uppercase <?= $statusClass ?>">
                                                <?= $statusText ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="?act=admin&module=bookings&action=show&id=<?= $booking['id'] ?>"
                                                class="text-blue-600 hover:text-blue-800 p-2" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">
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