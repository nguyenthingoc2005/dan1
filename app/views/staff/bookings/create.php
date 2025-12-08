<?php
/**
 * STAFF - TẠO BOOKING MỚI
 */
require_staff_or_admin();
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>

<div class="max-w-[95%] mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Tạo Booking Mới</h1>
        <a href="?act=staff-bookings" class="text-primary-500 hover:text-primary-700 font-semibold text-sm lg:text-base flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại danh sách
        </a>
    </div>

    <form action="?act=staff-bookings&action=store" method="POST" class="grid grid-cols-1 lg:grid-cols-4 gap-4 lg:gap-6"
        id="bookingForm">
        <?= csrf_field() ?>

        <!-- LEFT COLUMN: TOUR & CUSTOMER -->
        <div class="lg:col-span-3 grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 h-fit">

            <!-- 1. Tour Selection -->
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100 h-full">
                <h2 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-4 lg:mb-5">1. Thông tin Tour</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Chọn Tour <span
                                class="text-danger">*</span></label>
                        <select name="tour_id" id="tour_id"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base"
                            required>
                            <option value="">-- Chọn Tour --</option>
                            <?php if (!empty($tours)): ?>
                                <?php foreach ($tours as $t): ?>
                                    <?php
                                    $tourId = $t['id'] ?? '';
                                    $adultPrice = $t['adult_price'] ?? 0;
                                    $childPrice = $t['child_price'] ?? 0;
                                    $infantPrice = $t['infant_price'] ?? 0;
                                    $duration = $t['duration_days'] ?? 0;
                                    $deadlineDays = $t['booking_deadline_days'] ?? 1;
                                    $tourCode = $t['tour_code'] ?? '';
                                    $tourName = $t['name'] ?? '';
                                    ?>
                                    <option value="<?= htmlspecialchars($tourId) ?>"
                                        data-price-adult="<?= (float) $adultPrice ?>"
                                        data-price-child="<?= (float) $childPrice ?>"
                                        data-price-infant="<?= (float) $infantPrice ?>" 
                                        data-duration="<?= (int) $duration ?>"
                                        data-booking-deadline-days="<?= (int) $deadlineDays ?>"
                                        <?= (($old['tour_id'] ?? $prefill['tour_id'] ?? '') == $tourId) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tourCode . ' - ' . $tourName) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Giá tour hiển thị -->
                    <div id="tour_price_display" class="hidden bg-info-bg p-3 lg:p-4 rounded-2xl border border-info">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-4 text-sm">
                            <div>
                                <span class="text-primary-500">Giá người lớn:</span>
                                <span id="display_adult_price" class="font-bold text-accent ml-2">0 đ</span>
                            </div>
                            <div>
                                <span class="text-primary-500">Giá trẻ em:</span>
                                <span id="display_child_price" class="font-bold text-accent ml-2">0 đ</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Lịch khởi hành <span
                                    class="text-danger">*</span></label>
                            <div class="bg-warning-bg border border-warning rounded-2xl p-2 lg:p-3 mb-2 text-xs lg:text-sm flex items-start gap-2" id="deadline_notice">
                                <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0 mt-0.5"></i>
                                <span><strong>Lưu ý:</strong> Phải đặt trước <span id="deadline_days_display">1</span> ngày so với ngày khởi hành</span>
                            </div>
                            <select name="start_date" id="start_date"
                                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base"
                                required>
                                <option value="">-- Chọn ngày đi --</option>
                                <?php if (!empty($schedules)): ?>
                                    <?php foreach ($schedules as $s): ?>
                                        <?php
                                        $startDate = $s['start_date'] ?? '';
                                        $endDate = $s['end_date'] ?? '';
                                        $tourId = $s['tour_id'] ?? '';
                                        $quota = $s['quota'] ?? 0;
                                        $booked = $s['booked'] ?? 0;
                                        $adultPrice = $s['adult_price'] ?? 0;
                                        $childPrice = $s['child_price'] ?? 0;
                                        $infantPrice = $s['infant_price'] ?? 0;
                                        $available = max(0, $quota - $booked);
                                        ?>
                                        <option value="<?= htmlspecialchars($startDate) ?>" data-tour-id="<?= (int) $tourId ?>"
                                            data-end-date="<?= htmlspecialchars($endDate) ?>" data-quota="<?= (int) $quota ?>"
                                            data-booked="<?= (int) $booked ?>" data-price-adult="<?= (float) $adultPrice ?>"
                                            data-price-child="<?= (float) $childPrice ?>"
                                            data-price-infant="<?= (float) $infantPrice ?>" class="schedule-option hidden"
                                            <?= (($old['start_date'] ?? $prefill['start_date'] ?? '') == $startDate) ? 'selected' : '' ?>>
                                            <?php if (!empty($startDate)): ?>
                                                <?= date('d/m/Y', strtotime($startDate)) ?> (Còn <?= $available ?> chỗ)
                                            <?php else: ?>
                                                -- Chọn ngày --
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <input type="hidden" name="end_date" id="end_date">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-primary-700 mb-1 lg:mb-2">Ngày kết thúc</label>
                            <input type="text" id="end_date_display" value="<?= $old['end_date'] ?? '' ?>"
                                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none text-primary-700"
                                readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Customer Info -->
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100 h-full">
                <h2 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-4 lg:mb-5">2. Khách hàng</h2>

                <div class="flex gap-4 mb-4">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="customer_mode" value="existing" class="form-radio text-accent" checked
                            onchange="toggleCustomerMode()">
                        <span class="ml-2">Khách hàng cũ</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="customer_mode" value="new" class="form-radio text-accent"
                            onchange="toggleCustomerMode()">
                        <span class="ml-2">Tạo khách mới</span>
                    </label>
                </div>

                <!-- EXISTING CUSTOMER SELECT -->
                <div id="existing_customer_section">
                    <label class="block text-sm font-medium text-primary-700 mb-1 lg:mb-2">Tìm khách hàng <span
                            class="text-danger">*</span></label>
                    <select name="customer_id" id="customer_id"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent text-primary-700">
                        <option value="">-- Chọn Khách hàng --</option>
                        <?php if (!empty($customers)): ?>
                            <?php foreach ($customers as $c): ?>
                                <?php
                                $customerId = $c['id'] ?? '';
                                $customerName = $c['full_name'] ?? '';
                                $customerPhone = $c['phone'] ?? '';
                                ?>
                                <option value="<?= htmlspecialchars($customerId) ?>" <?= ($old['customer_id'] ?? '') == $customerId ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($customerName . ' - ' . $customerPhone) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- NEW CUSTOMER FORM -->
                <div id="new_customer_section" class="hidden space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="new_customer_name" class="w-full px-3 py-2 border rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="new_customer_phone" class="w-full px-3 py-2 border rounded">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="new_customer_email" class="w-full px-3 py-2 border rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                            <input type="text" name="new_customer_address" class="w-full px-3 py-2 border rounded">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Passengers & Notes -->
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100 md:col-span-2">
                <h2 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-4 lg:mb-5">3. Số lượng & Ghi chú</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Người lớn (>12t)</label>
                        <input type="number" name="adult_count" id="adult_count" value="<?= $old['adult_count'] ?? 1 ?>"
                            min="1"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-center font-bold text-sm lg:text-base">
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trẻ em (5-11t)</label>
                        <input type="number" name="child_count" id="child_count" value="<?= $old['child_count'] ?? 0 ?>"
                            min="0"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-center text-sm lg:text-base">
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Em bé (<5t)</label>
                        <input type="number" name="infant_count" id="infant_count"
                            value="<?= $old['infant_count'] ?? 0 ?>" min="0"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-center text-sm lg:text-base">
                    </div>
                </div>

                <!-- PASSENGER LIST -->
                <div class="mt-6 border-t pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-bold text-gray-700">Danh sách đoàn (Tùy chọn)</h3>
                        <div class="flex gap-2 items-center">
                            <label
                                class="text-sm text-green-600 hover:underline cursor-pointer border border-green-600 px-3 py-1 rounded hover:bg-green-50">
                                📥 Import Excel/CSV
                                <input type="file" id="importFile" accept=".csv,.xlsx,.xls" class="hidden"
                                    onchange="handleImportFile(event)">
                            </label>
                            <a href="?act=staff-bookings&action=downloadTemplate"
                                class="text-xs text-gray-500 hover:text-gray-700 border border-gray-300 px-2 py-1 rounded hover:bg-gray-50"
                                title="Tải file mẫu">
                                📄 Template
                            </a>
                            <button type="button" onclick="addPassengerRow()"
                                class="text-sm text-blue-600 hover:underline">+ Thêm người</button>
                        </div>
                    </div>
                    <div id="importStatus" class="mb-2 hidden"></div>
                    <p class="text-xs text-gray-500 mb-2">
                        💡 Hướng dẫn: Tải file mẫu, điền thông tin khách hàng, sau đó import.
                        File Excel (.xlsx) cần lưu dưới dạng CSV trước khi import.
                    </p>
                    <table class="w-full text-sm text-left text-gray-500 border rounded">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 py-2">Họ tên</th>
                                <th class="px-2 py-2 w-32">SĐT</th>
                                <th class="px-2 py-2 w-40">Email</th>
                                <th class="px-2 py-2 w-24">Loại</th>
                                <th class="px-2 py-2 w-24">Giới tính</th>
                                <th class="px-2 py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="passenger-container">
                            <!-- Dynamic Rows -->
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-1 lg:mb-2">Nguồn booking</label>
                        <select name="source" id="source"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent text-primary-700">
                            <option value="">-- Chọn nguồn --</option>
                            <option value="phone" <?= ($old['source'] ?? '') == 'phone' ? 'selected' : '' ?>>Điện thoại</option>
                            <option value="email" <?= ($old['source'] ?? '') == 'email' ? 'selected' : '' ?>>Email</option>
                            <option value="facebook" <?= ($old['source'] ?? '') == 'facebook' ? 'selected' : '' ?>>Facebook</option>
                            <option value="zalo" <?= ($old['source'] ?? '') == 'zalo' ? 'selected' : '' ?>>Zalo</option>
                            <option value="walk_in" <?= ($old['source'] ?? '') == 'walk_in' ? 'selected' : '' ?>>Đến trực tiếp</option>
                            <option value="other" <?= ($old['source'] ?? '') == 'other' ? 'selected' : '' ?>>Khác</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-1 lg:mb-2">Yêu cầu đặc biệt</label>
                        <textarea name="special_requests" rows="3" placeholder="VD: Khách ăn chay, Cần phòng riêng, Khách có trẻ em nhỏ..."
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent text-primary-700"><?= $old['special_requests'] ?? '' ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-1 lg:mb-2">Ghi chú cho khách</label>
                        <textarea name="notes" rows="3"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent text-primary-700"><?= $old['notes'] ?? '' ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-1 lg:mb-2">Ghi chú nội bộ</label>
                        <textarea name="internal_notes" rows="3" placeholder="Chỉ staff/admin mới thấy"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent text-primary-700"><?= $old['internal_notes'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: FINANCIALS -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border-t-4 border-accent sticky top-6">
                <h2 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-4 lg:mb-5">Thanh toán</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs lg:text-sm text-primary-500 font-semibold mb-1">Tổng tiền tour</label>
                        <input type="text" id="total_amount_display"
                            class="w-full text-right font-bold text-primary-700 bg-primary-50 border-0 rounded-xl text-base lg:text-lg px-3 py-2" value="0 đ"
                            readonly>
                        <input type="hidden" name="total_amount" id="total_amount" value="0">
                    </div>

                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mã giảm giá</label>
                        <input type="text" name="discount_code" id="discount_code"
                            value="<?= $old['discount_code'] ?? '' ?>" placeholder="Nhập mã giảm giá (nếu có)"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
                    </div>

                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Giảm giá (VNĐ)</label>
                        <input type="number" name="discount_amount" id="discount_amount"
                            value="<?= $old['discount_amount'] ?? 0 ?>" min="0"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-right text-sm lg:text-base">
                    </div>

                    <div class="border-t border-primary-100 pt-4">
                        <label class="block text-xs lg:text-sm text-primary-700 font-bold mb-1">THÀNH TIỀN</label>
                        <input type="text" id="final_amount_display"
                            class="w-full text-right font-bold text-accent bg-white border-0 rounded-xl text-lg lg:text-xl px-3 py-2" value="0 đ"
                            readonly>
                        <input type="hidden" name="final_amount" id="final_amount" value="0">
                    </div>

                    <div class="border-t pt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Đã thanh toán / Cọc</label>
                        <input type="number" name="deposit_amount" id="deposit_amount"
                            value="<?= $old['deposit_amount'] ?? 0 ?>" min="0"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent text-right font-bold text-success">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600">Còn lại</label>
                        <input type="text" id="remaining_amount_display"
                            class="w-full text-right font-bold text-gray-500 bg-gray-50 border-0" value="0 đ"
                            readonly>
                        <input type="hidden" name="remaining_amount" id="remaining_amount" value="0">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-1 lg:mb-2">Trạng thái thanh toán</label>
                        <select name="payment_status" id="payment_status"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent text-primary-700">
                            <option value="unpaid">Chưa thanh toán</option>
                            <option value="partial">Đã đặt cọc</option>
                            <option value="paid">Đã thanh toán hết</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full py-3 lg:py-3.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white font-bold rounded-xl shadow-sm transition-all mt-4 text-sm lg:text-base">
                        <i data-lucide="check-circle" class="w-4 h-4 inline mr-2"></i>
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
            try {
                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
            } catch (e) {
                return amount.toLocaleString('vi-VN') + ' đ';
            }
        }

        function filterSchedules() {
            const tourId = tourSelect.value;
            console.log('🔍 filterSchedules() - Tour ID selected:', tourId);

            if (!tourId) {
                // Nếu chưa chọn tour, ẩn tất cả schedules
                const options = startDateSelect.querySelectorAll('.schedule-option');
                console.log('❌ No tour selected, hiding all schedules. Total options:', options.length);
                options.forEach(opt => opt.classList.add('hidden'));
                startDateSelect.value = "";
                endDateDisplay.value = "";
                return;
            }

            const options = startDateSelect.querySelectorAll('.schedule-option');
            console.log('📋 Total schedule options found:', options.length);
            let hasOption = false;

            startDateSelect.value = ""; // Reset selection
            endDateDisplay.value = "";

            options.forEach((opt, index) => {
                const optTourId = String(opt.dataset.tourId || '');
                if (optTourId === String(tourId)) {
                    opt.classList.remove('hidden');
                    hasOption = true;
                    console.log(`  ✅ Showing option ${index + 1}: ${opt.textContent}`);
                } else {
                    opt.classList.add('hidden');
                }
            });

            // Nếu không có schedule nào, hiển thị thông báo
            if (!hasOption) {
                console.warn('⚠️ Không có lịch khởi hành cho tour này (ID:', tourId, ')');
            } else {
                console.log('✅ Found', hasOption ? 'schedules' : 'schedule', 'for tour', tourId);
            }
        }

        function updateScheduleInfo() {
            const selectedOption = startDateSelect.options[startDateSelect.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                endDateInput.value = "";
                endDateDisplay.value = "";
                return;
            }

            // Validate deadline: Phải đặt trước booking_deadline_days ngày
            const selectedDate = selectedOption.value;
            const selectedTour = tourSelect.options[tourSelect.selectedIndex];
            const deadlineDays = parseInt(selectedTour?.dataset?.bookingDeadlineDays || '1', 10);
            
            // Get today in local timezone (YYYY-MM-DD) - Use toLocaleDateString to get correct local date
            const now = new Date();
            const todayStr = now.toLocaleDateString('en-CA'); // Returns YYYY-MM-DD format in local timezone
            
            // Calculate minDate string (today + deadlineDays) - Parse and add days
            const [todayYear, todayMonth, todayDay] = todayStr.split('-').map(Number);
            const minDateObj = new Date(todayYear, todayMonth - 1, todayDay + deadlineDays);
            const minDateStr = minDateObj.getFullYear() + '-' + 
                              String(minDateObj.getMonth() + 1).padStart(2, '0') + '-' + 
                              String(minDateObj.getDate()).padStart(2, '0');
            
            console.log('🔍 DEBUG VALIDATION (Staff):');
            console.log('  - now (raw):', now);
            console.log('  - todayStr (toLocaleDateString):', todayStr);
            console.log('  - minDateStr (today + ' + deadlineDays + ' days):', minDateStr);
            console.log('  - selectedDate:', selectedDate);
            console.log('  - Comparison (string): selectedDate < minDateStr?', selectedDate < minDateStr);

            // Compare as strings (YYYY-MM-DD format is sortable)
            if (selectedDate < minDateStr) {
                // Format dates for display
                const [tdY, tdM, tdD] = todayStr.split('-');
                const [mdY, mdM, mdD] = minDateStr.split('-');
                const todayDisplay = tdD + '/' + tdM + '/' + tdY;
                const minDateDisplay = mdD + '/' + mdM + '/' + mdY;
                alert(`Không thể đặt booking. Phải đặt trước ${deadlineDays} ngày so với ngày khởi hành. (Hôm nay: ${todayDisplay}, Ngày khởi hành tối thiểu: ${minDateDisplay})`);
                startDateSelect.value = "";
                endDateInput.value = "";
                endDateDisplay.value = "";
                return;
            }

            // Update End Date
            const endDate = selectedOption.dataset.endDate || '';
            if (endDate) {
                endDateInput.value = endDate;
                // Format: YYYY-MM-DD -> DD/MM/YYYY
                const dateParts = endDate.split('-');
                if (dateParts.length === 3) {
                    endDateDisplay.value = dateParts[2] + '/' + dateParts[1] + '/' + dateParts[0];
                } else {
                    endDateDisplay.value = endDate;
                }
            } else {
                endDateInput.value = "";
                endDateDisplay.value = "";
            }

            calculateTotal();
        }

        function calculateTotal() {
            console.log('💰 calculateTotal() called');
            const selectedTour = tourSelect.options[tourSelect.selectedIndex];
            const selectedSchedule = startDateSelect.options[startDateSelect.selectedIndex];

            if (!selectedTour || !selectedTour.value) {
                console.log('❌ No tour selected');
                return;
            }

            console.log('📊 Tour:', selectedTour.textContent);
            console.log('📅 Schedule:', selectedSchedule ? selectedSchedule.textContent : 'None');

            // Priority: Schedule Price > Tour Price
            let priceAdult = parseInt(selectedTour.dataset.priceAdult) || 0;
            let priceChild = parseInt(selectedTour.dataset.priceChild) || 0;
            let priceInfant = parseInt(selectedTour.dataset.priceInfant) || 0;

            console.log('💵 Tour prices - Adult:', priceAdult, 'Child:', priceChild, 'Infant:', priceInfant);

            if (selectedSchedule && selectedSchedule.value) {
                if (selectedSchedule.dataset.priceAdult) priceAdult = parseInt(selectedSchedule.dataset.priceAdult);
                if (selectedSchedule.dataset.priceChild) priceChild = parseInt(selectedSchedule.dataset.priceChild);
                if (selectedSchedule.dataset.priceInfant) priceInfant = parseInt(selectedSchedule.dataset.priceInfant);
                console.log('💵 Schedule prices - Adult:', priceAdult, 'Child:', priceChild, 'Infant:', priceInfant);
            }

            const countAdult = parseInt(adultCountInput.value) || 0;
            const countChild = parseInt(childCountInput.value) || 0;
            const countInfant = parseInt(infantCountInput.value) || 0;

            console.log('👥 Counts - Adult:', countAdult, 'Child:', countChild, 'Infant:', countInfant);

            const total = (priceAdult * countAdult) + (priceChild * countChild) + (priceInfant * countInfant);
            console.log('💵 Total:', total);

            totalAmountInput.value = total;
            totalAmountDisplay.value = formatCurrency(total);

            const discount = parseInt(discountInput.value) || 0;
            const final = Math.max(0, total - discount);
            console.log('💵 Final (after discount):', final);

            finalAmountInput.value = final;
            finalAmountDisplay.value = formatCurrency(final);

            const deposit = parseInt(depositInput.value) || 0;
            const remaining = Math.max(0, final - deposit);
            console.log('💵 Remaining:', remaining);

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
            console.log('💳 Payment status:', paymentStatusSelect.value);
        }

        // Update tour price display
        function updateTourPriceDisplay() {
            const selectedTour = tourSelect.options[tourSelect.selectedIndex];
            const priceDisplay = document.getElementById('tour_price_display');
            const adultPriceDisplay = document.getElementById('display_adult_price');
            const childPriceDisplay = document.getElementById('display_child_price');

            if (selectedTour && selectedTour.value) {
                const adultPrice = parseInt(selectedTour.dataset.priceAdult) || 0;
                const childPrice = parseInt(selectedTour.dataset.priceChild) || 0;
                
                adultPriceDisplay.textContent = formatCurrency(adultPrice);
                childPriceDisplay.textContent = formatCurrency(childPrice);
                priceDisplay.classList.remove('hidden');
            } else {
                priceDisplay.classList.add('hidden');
            }
        }

        // Event Listeners
        tourSelect.addEventListener('change', function () {
            filterSchedules();
            updateTourPriceDisplay();
            calculateTotal();
        });

        startDateSelect.addEventListener('change', function () {
            updateScheduleInfo();
            validateDeadline();
        });

        // Update deadline notice when tour changes
        function updateDeadlineNotice() {
            const selectedTour = tourSelect.options[tourSelect.selectedIndex];
            const deadlineDays = parseInt(selectedTour?.dataset?.bookingDeadlineDays || '1', 10);
            const deadlineDaysDisplay = document.getElementById('deadline_days_display');
            if (deadlineDaysDisplay) {
                deadlineDaysDisplay.textContent = deadlineDays;
            }
        }

        // Validate deadline on form submit
        function validateDeadline() {
            const selectedDate = startDateSelect.value;
            if (!selectedDate) return true;

            const selectedTour = tourSelect.options[tourSelect.selectedIndex];
            const deadlineDays = parseInt(selectedTour?.dataset?.bookingDeadlineDays || '1', 10);

            // Get today in local timezone (YYYY-MM-DD) - Use toLocaleDateString to get correct local date
            const now = new Date();
            const todayStr = now.toLocaleDateString('en-CA'); // Returns YYYY-MM-DD format in local timezone
            
            // Calculate minDate string (today + deadlineDays) - Parse and add days
            const [todayYear, todayMonth, todayDay] = todayStr.split('-').map(Number);
            const minDateObj = new Date(todayYear, todayMonth - 1, todayDay + deadlineDays);
            const minDateStr = minDateObj.getFullYear() + '-' + 
                              String(minDateObj.getMonth() + 1).padStart(2, '0') + '-' + 
                              String(minDateObj.getDate()).padStart(2, '0');
            
            console.log('🔍 DEBUG validateDeadline (Staff):');
            console.log('  - todayStr:', todayStr);
            console.log('  - minDateStr:', minDateStr);
            console.log('  - selectedDate:', selectedDate);
            console.log('  - Comparison (string): selectedDate < minDateStr?', selectedDate < minDateStr);

            // Compare as strings (YYYY-MM-DD format is sortable)
            if (selectedDate < minDateStr) {
                // Format dates for display
                const [tdY, tdM, tdD] = todayStr.split('-');
                const [mdY, mdM, mdD] = minDateStr.split('-');
                const todayFormatted = tdD + '/' + tdM + '/' + tdY;
                const minDateFormatted = mdD + '/' + mdM + '/' + mdY;
                alert(`Không thể đặt booking. Phải đặt trước ${deadlineDays} ngày so với ngày khởi hành.\n(Hôm nay: ${todayFormatted}, Ngày khởi hành tối thiểu: ${minDateFormatted})`);
                startDateSelect.focus();
                return false;
            }
            return true;
        }

        // Initialize deadline notice on page load
        updateDeadlineNotice();

        // Add validation before form submit
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            if (!validateDeadline()) {
                e.preventDefault();
                return false;
            }
        });

        [adultCountInput, childCountInput, infantCountInput, discountInput, depositInput].forEach(input => {
            input.addEventListener('input', calculateTotal);
        });

        // Initial calculation if data exists
        if (tourSelect.value) {
            filterSchedules();
            updateTourPriceDisplay();
            // If start_date is pre-filled, trigger update
            if (startDateSelect.value) {
                updateScheduleInfo();
            }
            calculateTotal();
        }
        toggleCustomerMode(); // Initialize customer mode
    });

    // CUSTOMER MODE TOGGLE
    function toggleCustomerMode() {
        const mode = document.querySelector('input[name="customer_mode"]:checked').value;
        const existingSection = document.getElementById('existing_customer_section');
        const newSection = document.getElementById('new_customer_section');
        const customerSelect = document.getElementById('customer_id');
        const newInputs = newSection.querySelectorAll('input');

        if (mode === 'existing') {
            existingSection.classList.remove('hidden');
            newSection.classList.add('hidden');
            customerSelect.setAttribute('required', 'required');
            newInputs.forEach(i => i.removeAttribute('required'));
        } else {
            existingSection.classList.add('hidden');
            newSection.classList.remove('hidden');
            customerSelect.removeAttribute('required');
            newSection.querySelector('input[name="new_customer_name"]').setAttribute('required', 'required');
            newSection.querySelector('input[name="new_customer_phone"]').setAttribute('required', 'required');
        }
    }

    // PASSENGER LOGIC
    function addPassengerRow() {
        const container = document.getElementById('passenger-container');
        const html = `
            <tr class="border-b passenger-row">
                <td class="px-2 py-2">
                    <input type="text" name="passenger_names[]" class="w-full px-2 py-1 border rounded text-sm" placeholder="Họ tên" required>
                </td>
                <td class="px-2 py-2">
                    <input type="text" name="passenger_phones[]" class="w-full px-2 py-1 border rounded text-sm" placeholder="SĐT">
                </td>
                <td class="px-2 py-2">
                    <input type="email" name="passenger_emails[]" class="w-full px-2 py-1 border rounded text-sm" placeholder="Email">
                </td>
                <td class="px-2 py-2">
                    <select name="passenger_types[]" class="w-full px-2 py-1 border rounded text-sm passenger-type" onchange="handleTypeChange(this)">
                        <option value="adult">Người lớn</option>
                        <option value="child">Trẻ em</option>
                        <option value="infant">Em bé</option>
                    </select>
                </td>
                <td class="px-2 py-2">
                    <select name="passenger_genders[]" class="w-full px-2 py-1 border rounded text-sm">
                        <option value="male">Nam</option>
                        <option value="female">Nữ</option>
                        <option value="other">Khác</option>
                    </select>
                </td>
                <td class="px-2 py-2 text-center">
                    <button type="button" onclick="removePassengerRow(this)" class="text-red-500 hover:text-red-700">×</button>
                </td>
            </tr>
        `;
        container.insertAdjacentHTML('beforeend', html);

        // Default is Adult, so increment Adult count
        updateCount('adult', 1);
    }

    function removePassengerRow(btn) {
        const row = btn.closest('tr');
        const type = row.querySelector('.passenger-type').value;
        row.remove();
        updateCount(type, -1);
    }

    // Store previous value to handle change
    document.addEventListener('focusin', function (e) {
        if (e.target.classList.contains('passenger-type')) {
            e.target.dataset.oldValue = e.target.value;
        }
    });

    function handleTypeChange(select) {
        const oldValue = select.dataset.oldValue || 'adult';
        const newValue = select.value;

        if (oldValue !== newValue) {
            updateCount(oldValue, -1);
            updateCount(newValue, 1);
            select.dataset.oldValue = newValue;
        }
    }

    function updateCount(type, change) {
        let inputId = '';
        if (type === 'adult') inputId = 'adult_count';
        else if (type === 'child') inputId = 'child_count';
        else if (type === 'infant') inputId = 'infant_count';

        const input = document.getElementById(inputId);
        let currentVal = parseInt(input.value) || 0;
        let newVal = currentVal + change;
        if (newVal < 0) newVal = 0;

        input.value = newVal;

        // Trigger calculation
        input.dispatchEvent(new Event('input'));
    }

    // Import Excel/CSV
    function handleImportFile(event) {
        const file = event.target.files[0];
        console.log('📥 handleImportFile() - File:', file);

        if (!file) {
            console.log('❌ No file selected');
            return;
        }

        console.log('📄 File details:', {
            name: file.name,
            size: file.size,
            type: file.type
        });

        const importStatus = document.getElementById('importStatus');
        importStatus.classList.remove('hidden');
        importStatus.innerHTML = '<div class="text-blue-600">⏳ Đang xử lý file...</div>';

        const formData = new FormData();
        formData.append('file', file);

        const url = '?act=staff-bookings&action=previewPassengers';
        console.log('🌐 Sending request to:', url);

        fetch(url, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                console.log('📡 Response status:', response.status, response.statusText);
                return response.json();
            })
            .then(data => {
                console.log('📦 Response data:', data);

                if (data.success) {
                    // Fill passengers vào form
                    const passengers = data.passengers || [];
                    console.log('✅ Import successful, passengers count:', passengers.length);
                    console.log('👥 Passengers data:', passengers);

                    if (passengers.length === 0) {
                        console.warn('⚠️ No valid passengers in file');
                        importStatus.innerHTML = '<div class="text-yellow-600">⚠️ File không có dữ liệu hợp lệ</div>';
                        return;
                    }

                    // Add each passenger
                    passengers.forEach((passenger, index) => {
                        console.log(`  Adding passenger ${index + 1}:`, passenger);
                        addPassengerRowWithData(passenger);
                    });

                    // Update counts (bao gồm cả primary customer)
                    updateCountsFromPassengers();

                    importStatus.innerHTML = `<div class="text-green-600">✅ Đã import ${passengers.length} khách hàng</div>`;

                    // Auto-hide after 3 seconds
                    setTimeout(() => {
                        importStatus.classList.add('hidden');
                    }, 3000);
                } else {
                    console.error('❌ Import failed:', data.message);
                    importStatus.innerHTML = `<div class="text-red-600">❌ Lỗi: ${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('❌ Fetch error:', error);
                importStatus.innerHTML = `<div class="text-red-600">❌ Lỗi: ${error.message}</div>`;
            });

        // Reset file input
        event.target.value = '';
    }

    function addPassengerRowWithData(data) {
        const container = document.getElementById('passenger-container');
        const row = document.createElement('tr');
        row.className = 'passenger-row';

        // Escape HTML để tránh XSS và đảm bảo hiển thị đúng
        const escapeHtml = (text) => {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        };
        
        const name = escapeHtml(data.name || '');
        const phone = escapeHtml(String(data.phone || '')); // Đảm bảo phone là string
        const email = escapeHtml(data.email || '');
        const gender = data.gender || 'other';
        const ageType = data.age_type || 'adult';

        row.innerHTML = `
            <td class="px-2 py-2">
                <input type="text" name="passenger_names[]" value="${name}" 
                    class="w-full px-2 py-1 border rounded text-sm" placeholder="Họ tên">
            </td>
            <td class="px-2 py-2">
                <input type="text" name="passenger_phones[]" value="${phone}" 
                    class="w-full px-2 py-1 border rounded text-sm" placeholder="SĐT">
            </td>
            <td class="px-2 py-2">
                <input type="email" name="passenger_emails[]" value="${email}" 
                    class="w-full px-2 py-1 border rounded text-sm" placeholder="Email">
            </td>
            <td class="px-2 py-2">
                <select name="passenger_types[]" class="passenger-type w-full px-2 py-1 border rounded text-sm" 
                    onchange="handleTypeChange(this)">
                    <option value="adult" ${ageType === 'adult' ? 'selected' : ''}>Người lớn</option>
                    <option value="child" ${ageType === 'child' ? 'selected' : ''}>Trẻ em</option>
                    <option value="infant" ${ageType === 'infant' ? 'selected' : ''}>Em bé</option>
                </select>
            </td>
            <td class="px-2 py-2">
                <select name="passenger_genders[]" class="w-full px-2 py-1 border rounded text-sm">
                    <option value="male" ${gender === 'male' ? 'selected' : ''}>Nam</option>
                    <option value="female" ${gender === 'female' ? 'selected' : ''}>Nữ</option>
                    <option value="other" ${gender === 'other' ? 'selected' : ''}>Khác</option>
                </select>
            </td>
            <td class="px-2 py-2 text-center">
                <button type="button" onclick="removePassengerRow(this)" 
                    class="text-red-600 hover:text-red-800">✕</button>
            </td>
        `;

        container.appendChild(row);

        // Update count
        updateCount(ageType, 1);
    }

    function updateCountsFromPassengers() {
        // Reset counts
        document.getElementById('adult_count').value = 0;
        document.getElementById('child_count').value = 0;
        document.getElementById('infant_count').value = 0;

        // Count from passenger rows
        const rows = document.querySelectorAll('.passenger-row');
        rows.forEach(row => {
            const typeSelect = row.querySelector('.passenger-type');
            if (typeSelect) {
                const type = typeSelect.value;
                updateCount(type, 1);
            }
        });

        // Primary customer is always adult, so add 1
        updateCount('adult', 1);
    }
</script>
