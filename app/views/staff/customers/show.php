<?php
/**
 * View: Chi tiết Khách Hàng (Staff)
 */
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Info Column -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="p-6 text-center border-b border-slate-100">
                <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl">
                    <?php echo ($customer['gender'] == 'female') ? '👩' : '👨'; ?>
                </div>
                <h2 class="text-xl font-bold text-slate-800 mb-1">
                    <?php echo htmlspecialchars($customer['full_name']); ?></h2>
                <span class="inline-block px-3 py-1 bg-blue-50 text-blue-600 text-xs font-medium rounded-full">
                    Khách Hàng
                </span>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-3 text-slate-600">
                    <i class="fas fa-phone w-5 text-center text-slate-400"></i>
                    <span class="font-medium"><?php echo htmlspecialchars($customer['phone']); ?></span>
                </div>
                <?php if (!empty($customer['email'])): ?>
                    <div class="flex items-center gap-3 text-slate-600">
                        <i class="fas fa-envelope w-5 text-center text-slate-400"></i>
                        <span><?php echo htmlspecialchars($customer['email']); ?></span>
                    </div>
                <?php endif; ?>
                <div class="flex items-center gap-3 text-slate-600">
                    <i class="fas fa-map-marker-alt w-5 text-center text-slate-400"></i>
                    <span><?php echo htmlspecialchars($customer['address'] ?? 'Chưa cập nhật'); ?></span>
                </div>
                <div class="flex items-center gap-3 text-slate-600">
                    <i class="fas fa-birthday-cake w-5 text-center text-slate-400"></i>
                    <span><?php echo !empty($customer['date_of_birth']) ? date('d/m/Y', strtotime($customer['date_of_birth'])) : 'Chưa cập nhật'; ?></span>
                </div>
            </div>
            <div class="p-6 border-t border-slate-100 bg-slate-50">
                <a href="?act=staff-customers&action=edit&id=<?php echo $customer['id']; ?>"
                    class="block w-full py-2 bg-white border border-slate-300 text-slate-700 text-center rounded-lg hover:bg-slate-50 font-medium transition-colors">
                    <i class="fas fa-edit mr-2"></i> Chỉnh sửa thông tin
                </a>
            </div>
        </div>
    </div>

    <!-- History Column -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800">Lịch Sử Booking</h3>
                <a href="?act=staff-bookings&action=create&customer_id=<?php echo $customer['id']; ?>"
                    class="text-sm text-accent hover:underline font-medium">
                    + Tạo Booking Mới
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                        <tr>
                            <th class="p-4">Mã Booking</th>
                            <th class="p-4">Tour</th>
                            <th class="p-4">Ngày Đi</th>
                            <th class="p-4">Tổng Tiền</th>
                            <th class="p-4">Trạng Thái</th>
                            <th class="p-4 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (!empty($bookings)): ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-4 font-medium text-accent">
                                        <a href="?act=staff-bookings&action=show&id=<?php echo $booking['id']; ?>">
                                            <?php echo $booking['booking_code']; ?>
                                        </a>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-medium text-slate-800 line-clamp-1"
                                            title="<?php echo htmlspecialchars($booking['tour_name']); ?>">
                                            <?php echo htmlspecialchars($booking['tour_name']); ?>
                                        </div>
                                        <div class="text-xs text-slate-500"><?php echo $booking['tour_code']; ?></div>
                                    </td>
                                    <td class="p-4 text-slate-600">
                                        <?php echo date('d/m/Y', strtotime($booking['start_date'])); ?>
                                    </td>
                                    <td class="p-4 font-medium text-slate-800">
                                        <?php echo number_format($booking['final_amount']); ?> đ
                                    </td>
                                    <td class="p-4">
                                        <?php
                                        $statusClass = '';
                                        $statusText = '';
                                        switch ($booking['approval_status']) {
                                            case 'approved':
                                                $statusClass = 'bg-green-100 text-green-700';
                                                $statusText = 'Đã duyệt';
                                                break;
                                            case 'pending':
                                                $statusClass = 'bg-yellow-100 text-yellow-700';
                                                $statusText = 'Chờ duyệt';
                                                break;
                                            case 'cancelled':
                                                $statusClass = 'bg-red-100 text-red-700';
                                                $statusText = 'Đã hủy';
                                                break;
                                            case 'rejected':
                                                $statusClass = 'bg-red-100 text-red-700';
                                                $statusText = 'Từ chối';
                                                break;
                                            default:
                                                $statusClass = 'bg-slate-100 text-slate-700';
                                                $statusText = $booking['approval_status'];
                                        }
                                        ?>
                                        <span class="px-2 py-1 rounded text-xs font-medium <?php echo $statusClass; ?>">
                                            <?php echo $statusText; ?>
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <a href="?act=staff-bookings&action=show&id=<?php echo $booking['id']; ?>"
                                            class="text-slate-400 hover:text-accent">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-500">
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