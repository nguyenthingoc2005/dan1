<?php
/**
 * ADMIN - CHI TIẾT BOOKING
 * Variables: $booking
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">Chi tiết Booking: <span
                    class="font-mono text-accent"><?= $booking['booking_code'] ?></span></h1>
            <p class="text-sm text-gray-500">Ngày tạo: <?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?></p>
        </div>
        <div class="flex gap-2">
            <a href="?act=admin&module=bookings"
                class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                ← Quay lại
            </a>
            <!-- Print Button (Placeholder) -->
            <button onclick="window.print()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                🖨️ In phiếu
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT COLUMN: INFO -->
        <div class="lg:col-span-2 space-y-6">

            <!-- 1. Tour Info -->
            <div class="bg-white rounded shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Thông tin Tour</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <span class="block text-sm text-gray-500">Tên Tour</span>
                        <a href="?act=admin&module=tours&action=show&id=<?= $booking['tour_id'] ?>"
                            class="font-medium text-blue-600 hover:underline">
                            <?= htmlspecialchars($booking['tour_name']) ?>
                        </a>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500">Mã Tour</span>
                        <span class="font-mono text-gray-900"><?= htmlspecialchars($booking['tour_code']) ?></span>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500">Thời gian</span>
                        <span
                            class="text-gray-900"><?= $booking['duration_days'] ?>N<?= $booking['duration_nights'] ?>Đ</span>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500">Ngày đi</span>
                        <span
                            class="font-bold text-gray-900"><?= date('d/m/Y', strtotime($booking['start_date'])) ?></span>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500">Ngày về</span>
                        <span
                            class="font-bold text-gray-900"><?= date('d/m/Y', strtotime($booking['end_date'])) ?></span>
                    </div>
                </div>
            </div>

            <!-- 2. Customer Info -->
            <div class="bg-white rounded shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Thông tin Khách hàng</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="block text-sm text-gray-500">Họ tên</span>
                        <span
                            class="font-medium text-gray-900"><?= htmlspecialchars($booking['customer_name']) ?></span>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500">Số điện thoại</span>
                        <span class="font-mono text-gray-900"><?= htmlspecialchars($booking['customer_phone']) ?></span>
                    </div>
                    <div class="col-span-2">
                        <span class="block text-sm text-gray-500">Email</span>
                        <span class="text-gray-900"><?= htmlspecialchars($booking['customer_email']) ?></span>
                    </div>
                    <div class="col-span-2">
                        <span class="block text-sm text-gray-500">Địa chỉ</span>
                        <span
                            class="text-gray-900"><?= htmlspecialchars($booking['customer_address'] ?? 'N/A') ?></span>
                    </div>
                </div>
            </div>

            <!-- 3. Passengers & Notes -->
            <div class="bg-white rounded shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Chi tiết đặt chỗ</h2>
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="text-center p-3 bg-gray-50 rounded">
                        <span class="block text-xs text-gray-500 uppercase">Người lớn</span>
                        <span class="text-xl font-bold text-gray-800"><?= $booking['adult_count'] ?></span>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded">
                        <span class="block text-xs text-gray-500 uppercase">Trẻ em</span>
                        <span class="text-xl font-bold text-gray-800"><?= $booking['child_count'] ?></span>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded">
                        <span class="block text-xs text-gray-500 uppercase">Em bé</span>
                        <span class="text-xl font-bold text-gray-800"><?= $booking['infant_count'] ?></span>
                    </div>
                </div>
                <div>
                    <span class="block text-sm text-gray-500 mb-1">Ghi chú:</span>
                    <p class="text-gray-700 bg-gray-50 p-3 rounded text-sm italic">
                        <?= nl2br(htmlspecialchars($booking['notes'] ?? 'Không có ghi chú')) ?>
                    </p>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: STATUS & FINANCIALS -->
        <div class="space-y-6">

            <!-- ACTION PANEL -->
            <div class="bg-white rounded shadow-sm p-6 border-l-4 border-accent">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Xử lý Đơn hàng</h2>

                <!-- Current Status -->
                <div class="mb-4 flex justify-between items-center">
                    <span class="text-gray-600 text-sm">Trạng thái:</span>
                    <?php if ($booking['approval_status'] == 'approved'): ?>
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-bold">ĐÃ DUYỆT</span>
                    <?php elseif ($booking['approval_status'] == 'cancelled'): ?>
                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full font-bold">ĐÃ HỦY</span>
                    <?php elseif ($booking['approval_status'] == 'rejected'): ?>
                        <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded-full font-bold">TỪ CHỐI</span>
                    <?php else: ?>
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full font-bold">CHỜ
                            DUYỆT</span>
                    <?php endif; ?>
                </div>

                <form method="POST" action="?act=admin&module=bookings&action=changeStatus">
                    <input type="hidden" name="id" value="<?= $booking['id'] ?>">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hành động</label>
                        <select name="action"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                            <option value="">-- Chọn hành động --</option>
                            <?php if ($booking['approval_status'] == 'pending'): ?>
                                <option value="approve">✅ Duyệt đơn (Approve)</option>
                                <option value="reject">⛔ Từ chối (Reject)</option>
                            <?php endif; ?>
                            <?php if ($booking['approval_status'] == 'approved'): ?>
                                <option value="cancel">❌ Hủy đơn (Cancel)</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Reason input for Reject/Cancel -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lý do (nếu hủy/từ chối)</label>
                        <textarea name="reason" rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                            placeholder="Nhập lý do..."></textarea>
                    </div>

                    <button type="submit"
                        class="w-full px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 font-medium shadow"
                        onclick="return confirm('Bạn có chắc chắn muốn thực hiện hành động này?')">
                        Cập nhật trạng thái
                    </button>
                </form>
            </div>

            <!-- Financials -->
            <div class="bg-white rounded shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Thanh toán</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Tổng tiền tour</span>
                        <span class="font-medium"><?= number_format($booking['total_amount'], 0, ',', '.') ?> đ</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Giảm giá</span>
                        <span class="text-red-500">-<?= number_format($booking['discount_amount'], 0, ',', '.') ?>
                            đ</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between items-center">
                        <span class="font-bold text-gray-800">THÀNH TIỀN</span>
                        <span
                            class="font-bold text-xl text-red-600"><?= number_format($booking['final_amount'], 0, ',', '.') ?>
                            đ</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">Đã thanh toán/cọc</span>
                        <span
                            class="font-bold text-green-600"><?= number_format($booking['deposit_amount'] + $booking['paid_amount'], 0, ',', '.') ?>
                            đ</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">Còn lại</span>
                        <span
                            class="font-bold text-gray-500"><?= number_format($booking['remaining_amount'], 0, ',', '.') ?>
                            đ</span>
                    </div>

                    <div class="mt-4 pt-4 border-t text-center">
                        <span class="block text-xs text-gray-500 mb-1">Trạng thái thanh toán</span>
                        <?php if ($booking['payment_status'] == 'paid'): ?>
                            <span
                                class="inline-block px-3 py-1 bg-green-100 text-green-700 text-sm rounded font-bold uppercase">Đã
                                thanh toán</span>
                        <?php elseif ($booking['payment_status'] == 'partial'): ?>
                            <span
                                class="inline-block px-3 py-1 bg-orange-100 text-orange-700 text-sm rounded font-bold uppercase">Đã
                                đặt cọc</span>
                        <?php else: ?>
                            <span
                                class="inline-block px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded font-bold uppercase">Chưa
                                thanh toán</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>