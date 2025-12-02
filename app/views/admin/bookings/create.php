<?php
/**
 * ADMIN - TẠO BOOKING MỚI
 */
if (!is_admin())
    redirect('?act=access-denied');
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>

<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Tạo Booking Mới</h1>
        <a href="?act=admin&module=bookings" class="text-gray-600 hover:text-gray-800">
            ← Quay lại danh sách
        </a>
    </div>

    <form action="?act=admin&module=bookings&action=store" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6"
        id="bookingForm">

        <!-- LEFT COLUMN: TOUR & CUSTOMER -->
        <div class="lg:col-span-2 space-y-6">

            <!-- 1. Tour Selection -->
            <div class="bg-white p-6 rounded shadow-sm">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">1. Thông tin Tour</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chọn Tour <span
                                class="text-red-500">*</span></label>
                        <select name="tour_id" id="tour_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                            required>
                            <option value="">-- Chọn Tour --</option>
                            <?php foreach ($tours as $t): ?>
                                <option value="<?= $t['id'] ?>" data-price-adult="<?= $t['adult_price'] ?>"
                                    data-price-child="<?= $t['child_price'] ?>"
                                    data-price-infant="<?= $t['infant_price'] ?>" data-duration="<?= $t['duration_days'] ?>"
                                    <?= ($old['tour_id'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['tour_code'] . ' - ' . $t['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lịch khởi hành <span
                                class="text-red-500">*</span></label>
                        <select name="start_date" id="start_date"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                            required>
                            <option value="">-- Chọn ngày đi --</option>
                            <?php foreach ($schedules as $s): ?>
                                <option value="<?= $s['start_date'] ?>" data-tour-id="<?= $s['tour_id'] ?>"
                                    data-end-date="<?= $s['end_date'] ?>" data-quota="<?= $s['quota'] ?>"
                                    data-booked="<?= $s['booked'] ?>" data-price-adult="<?= $s['adult_price'] ?>"
                                    data-price-child="<?= $s['child_price'] ?>"
                                    data-price-infant="<?= $s['infant_price'] ?>" class="schedule-option hidden">
                                    <?= date('d/m/Y', strtotime($s['start_date'])) ?> (Còn <?= $s['quota'] - $s['booked'] ?>
                                    chỗ)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="end_date" id="end_date">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày kết thúc</label>
                        <input type="text" id="end_date_display" value="<?= $old['end_date'] ?? '' ?>"
                            class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded focus:outline-none"
                            readonly>
                    </div>
                </div>
            </div>

            <!-- 2. Customer Info -->
            <div class="bg-white p-6 rounded shadow-sm">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">2. Khách hàng</h2>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Khách hàng đại diện <span
                            class="text-red-500">*</span></label>
                    <select name="customer_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                        required>
                        <option value="">-- Chọn Khách hàng --</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($old['customer_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['full_name'] . ' - ' . $c['phone']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Chưa có khách? <a
                            href="?act=admin&module=customers&action=create" target="_blank"
                            class="text-blue-600 hover:underline">Tạo mới tại đây</a></p>
                </div>
            </div>

            <!-- 3. Passengers & Notes -->
            <div class="bg-white p-6 rounded shadow-sm">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">3. Số lượng & Ghi chú</h2>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Người lớn (>12t)</label>
                        <input type="number" name="adult_count" id="adult_count" value="<?= $old['adult_count'] ?? 1 ?>"
                            min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent text-center font-bold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trẻ em (5-11t)</label>
                        <input type="number" name="child_count" id="child_count" value="<?= $old['child_count'] ?? 0 ?>"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent text-center">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Em bé (<5t)< /label>
                                <input type="number" name="infant_count" id="infant_count"
                                    value="<?= $old['infant_count'] ?? 0 ?>" min="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent text-center">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú đơn hàng</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"><?= $old['notes'] ?? '' ?></textarea>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: FINANCIALS -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded shadow-sm border-t-4 border-accent sticky top-6">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Thanh toán</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-600">Tổng tiền tour</label>
                        <input type="text" id="total_amount_display"
                            class="w-full text-right font-bold text-gray-800 bg-gray-50 border-0 text-lg" value="0 đ"
                            readonly>
                        <input type="hidden" name="total_amount" id="total_amount" value="0">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giảm giá (VNĐ)</label>
                        <input type="number" name="discount_amount" id="discount_amount"
                            value="<?= $old['discount_amount'] ?? 0 ?>" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent text-right">
                    </div>

                    <div class="border-t pt-4">
                        <label class="block text-sm text-gray-600 font-bold">THÀNH TIỀN</label>
                        <input type="text" id="final_amount_display"
                            class="w-full text-right font-bold text-red-600 bg-white border-0 text-xl" value="0 đ"
                            readonly>
                        <input type="hidden" name="final_amount" id="final_amount" value="0">
                    </div>

                    <div class="border-t pt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Đã thanh toán / Cọc</label>
                        <input type="number" name="deposit_amount" id="deposit_amount"
                            value="<?= $old['deposit_amount'] ?? 0 ?>" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent text-right font-bold text-green-600">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600">Còn lại</label>
                        <input type="text" id="remaining_amount_display"
                            class="w-full text-right font-bold text-gray-500 bg-gray-50 border-0" value="0 đ" readonly>
                        <input type="hidden" name="remaining_amount" id="remaining_amount" value="0">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái thanh toán</label>
                        <select name="payment_status" id="payment_status"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                            <option value="unpaid">Chưa thanh toán</option>
                            <option value="partial">Đã đặt cọc</option>
                            <option value="paid">Đã thanh toán hết</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-accent text-white font-bold rounded hover:bg-blue-600 shadow mt-4">
                        TẠO BOOKING
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tourSelect = document.getElementById('tour_id');
        const startDateSelect = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const endDateDisplay = document.getElementById('end_date_display');

        const adultCountInput = document.getElementById('adult_count');
        const childCountInput = document.getElementById('child_count');
        const infantCountInput = document.getElementById('infant_count');

        const discountInput = document.getElementById('discount_amount');
        const depositInput = document.getElementById('deposit_amount');

        // Hidden inputs
        const totalAmountInput = document.getElementById('total_amount');
        const finalAmountInput = document.getElementById('final_amount');
        const remainingAmountInput = document.getElementById('remaining_amount');

        // Display inputs
        const totalAmountDisplay = document.getElementById('total_amount_display');
        const finalAmountDisplay = document.getElementById('final_amount_display');
        const remainingAmountDisplay = document.getElementById('remaining_amount_display');

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        }

        function filterSchedules() {
            const tourId = tourSelect.value;
            const options = startDateSelect.querySelectorAll('.schedule-option');
            let hasOption = false;

            startDateSelect.value = ""; // Reset selection
            endDateDisplay.value = "";

            options.forEach(opt => {
                if (opt.dataset.tourId == tourId) {
                    opt.classList.remove('hidden');
                    hasOption = true;
                } else {
                    opt.classList.add('hidden');
                }
            });
        }

        function updateScheduleInfo() {
            const selectedOption = startDateSelect.options[startDateSelect.selectedIndex];
            if (!selectedOption.value) return;

            // Update End Date
            const endDate = selectedOption.dataset.endDate;
            endDateInput.value = endDate;
            endDateDisplay.value = endDate.split('-').reverse().join('/');

            calculateTotal();
        }

        function calculateTotal() {
            const selectedTour = tourSelect.options[tourSelect.selectedIndex];
            const selectedSchedule = startDateSelect.options[startDateSelect.selectedIndex];

            if (!selectedTour.value) return;

            // Priority: Schedule Price > Tour Price
            let priceAdult = parseInt(selectedTour.dataset.priceAdult) || 0;
            let priceChild = parseInt(selectedTour.dataset.priceChild) || 0;
            let priceInfant = parseInt(selectedTour.dataset.priceInfant) || 0;

            if (selectedSchedule && selectedSchedule.value) {
                if (selectedSchedule.dataset.priceAdult) priceAdult = parseInt(selectedSchedule.dataset.priceAdult);
                if (selectedSchedule.dataset.priceChild) priceChild = parseInt(selectedSchedule.dataset.priceChild);
                if (selectedSchedule.dataset.priceInfant) priceInfant = parseInt(selectedSchedule.dataset.priceInfant);
            }

            const countAdult = parseInt(adultCountInput.value) || 0;
            const countChild = parseInt(childCountInput.value) || 0;
            const countInfant = parseInt(infantCountInput.value) || 0;

            const total = (priceAdult * countAdult) + (priceChild * countChild) + (priceInfant * countInfant);

            totalAmountInput.value = total;
            totalAmountDisplay.value = formatCurrency(total);

            const discount = parseInt(discountInput.value) || 0;
            const final = Math.max(0, total - discount);

            finalAmountInput.value = final;
            finalAmountDisplay.value = formatCurrency(final);

            const deposit = parseInt(depositInput.value) || 0;
            const remaining = Math.max(0, final - deposit);

            remainingAmountInput.value = remaining;
            remainingAmountDisplay.value = formatCurrency(remaining);

            // Auto update payment status
            const paymentStatusSelect = document.getElementById('payment_status');
            if (deposit >= final && final > 0) {
                paymentStatusSelect.value = 'paid';
            } else if (deposit > 0) {
                paymentStatusSelect.value = 'partial';
            } else {
                paymentStatusSelect.value = 'unpaid';
            }
        }

        // Event Listeners
        tourSelect.addEventListener('change', function () {
            filterSchedules();
            calculateTotal();
        });

        startDateSelect.addEventListener('change', function () {
            updateScheduleInfo();
        });

        [adultCountInput, childCountInput, infantCountInput, discountInput, depositInput].forEach(input => {
            input.addEventListener('input', calculateTotal);
        });

        // Initial calculation if data exists
        if (tourSelect.value) {
            filterSchedules();
            calculateTotal();
        }
    });
</script>