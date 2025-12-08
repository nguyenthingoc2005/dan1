<?php
/**
 * View: Chi tiết Khách Hàng (Staff)
 */
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
    <!-- Info Column -->
    <div class="lg:col-span-1">
        <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden mb-4 lg:mb-6">
            <div class="p-4 lg:p-6 text-center border-b border-primary-100">
                <div class="w-20 lg:w-24 h-20 lg:h-24 bg-info-bg rounded-full flex items-center justify-center mx-auto mb-4 text-3xl lg:text-4xl">
                    <?php echo ($customer['gender'] == 'female') ? '👩' : '👨'; ?>
                </div>
                <h2 class="text-lg lg:text-xl font-bold text-primary-700 mb-1">
                    <?php echo htmlspecialchars($customer['full_name']); ?></h2>
                <span class="inline-block px-3 py-1 bg-info-bg text-info-text text-xs font-bold rounded-full">
                    Khách Hàng
                </span>
            </div>
            <div class="p-4 lg:p-6 space-y-3 lg:space-y-4">
                <div class="flex items-center gap-3 text-primary-600">
                    <i data-lucide="phone" class="w-4 h-4 text-primary-400"></i>
                    <span class="font-semibold text-sm lg:text-base"><?php echo htmlspecialchars($customer['phone']); ?></span>
                </div>
                <?php if (!empty($customer['email'])): ?>
                    <div class="flex items-center gap-3 text-primary-600">
                        <i data-lucide="mail" class="w-4 h-4 text-primary-400"></i>
                        <span class="text-sm lg:text-base"><?php echo htmlspecialchars($customer['email']); ?></span>
                    </div>
                <?php endif; ?>
                <div class="flex items-center gap-3 text-primary-600">
                    <i data-lucide="map-pin" class="w-4 h-4 text-primary-400"></i>
                    <span class="text-sm lg:text-base"><?php echo htmlspecialchars($customer['address'] ?? 'Chưa cập nhật'); ?></span>
                </div>
                <div class="flex items-center gap-3 text-primary-600">
                    <i data-lucide="calendar" class="w-4 h-4 text-primary-400"></i>
                    <span class="text-sm lg:text-base"><?php echo !empty($customer['date_of_birth']) ? date('d/m/Y', strtotime($customer['date_of_birth'])) : 'Chưa cập nhật'; ?></span>
                </div>
            </div>
            <div class="p-4 lg:p-6 border-t border-primary-100 bg-primary-50">
                <a href="?act=staff-customers&action=edit&id=<?php echo $customer['id']; ?>"
                    class="block w-full py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 text-center rounded-xl hover:bg-white font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="pencil" class="w-4 h-4"></i>
                    Chỉnh sửa thông tin
                </a>
            </div>
        </div>
    </div>

    <!-- History Column -->
    <div class="lg:col-span-2">
        <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
            <div class="p-4 lg:p-6 border-b border-primary-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <h3 class="text-base lg:text-lg font-bold text-primary-700">Lịch Sử Booking</h3>
                <a href="?act=staff-bookings&action=create&customer_id=<?php echo $customer['id']; ?>"
                    class="text-xs lg:text-sm text-accent hover:text-accent-hover font-semibold flex items-center gap-1">
                    <i data-lucide="plus" class="w-3 h-3"></i>
                    Tạo Booking Mới
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead class="bg-primary-50 text-primary-700 text-xs uppercase font-semibold">
                        <tr>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Mã Booking</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Tour</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Ngày Đi</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Tổng Tiền</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Trạng Thái</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary-100">
                        <?php if (!empty($bookings)): ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr class="hover:bg-primary-50 transition-colors">
                                    <td class="px-3 lg:px-4 py-2 lg:py-3 font-semibold text-accent text-sm">
                                        <a href="?act=staff-bookings&action=show&id=<?php echo $booking['id']; ?>" class="hover:text-accent-hover">
                                            <?php echo $booking['booking_code']; ?>
                                        </a>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3">
                                        <div class="font-semibold text-primary-700 line-clamp-1 text-sm"
                                            title="<?php echo htmlspecialchars($booking['tour_name']); ?>">
                                            <?php echo htmlspecialchars($booking['tour_name']); ?>
                                        </div>
                                        <div class="text-xs text-primary-500"><?php echo $booking['tour_code']; ?></div>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600 text-sm">
                                        <?php echo date('d/m/Y', strtotime($booking['start_date'])); ?>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3 font-semibold text-primary-700 text-sm">
                                        <?php echo number_format($booking['final_amount']); ?> đ
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3">
                                        <?php
                                        $statusClass = '';
                                        $statusText = '';
                                        $statusClass = get_payment_status_color($booking['payment_status'] ?? 'unpaid');
                                        $statusText = payment_status_text($booking['payment_status'] ?? 'unpaid');
                                        ?>
                                        <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase <?php echo $statusClass; ?>">
                                            <?php echo $statusText; ?>
                                        </span>
                                    </td>
                                    <td class="px-3 lg:px-4 py-2 lg:py-3 text-right">
                                        <a href="?act=staff-bookings&action=show&id=<?php echo $booking['id']; ?>"
                                            class="text-primary-400 hover:text-accent rounded-xl hover:bg-primary-50 p-2 transition-all inline-block">
                                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-3 lg:px-4 py-6 lg:py-8 text-center text-primary-500 text-sm">
                                    Khách hàng này chưa có booking nào.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>