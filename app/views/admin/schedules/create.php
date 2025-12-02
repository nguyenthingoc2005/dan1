<?php
/**
 * ADMIN - THÊM LỊCH KHỞI HÀNH
 */
?>
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Thêm Lịch Khởi Hành</h1>
        <a href="?act=admin&module=schedules" class="text-gray-600 hover:text-gray-800">
            ← Quay lại danh sách
        </a>
    </div>

    <form action="?act=admin&module=schedules&action=store" method="POST" class="bg-white p-6 rounded shadow-sm">

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Chọn Tour <span
                    class="text-red-500">*</span></label>
            <select name="tour_id" id="tour_id" class="w-full px-3 py-2 border rounded focus:border-accent" required>
                <option value="">-- Chọn Tour --</option>
                <?php foreach ($tours as $t): ?>
                    <option value="<?= $t['id'] ?>" data-price-adult="<?= $t['adult_price'] ?>"
                        data-price-child="<?= $t['child_price'] ?>" data-price-infant="<?= $t['infant_price'] ?>"
                        data-duration="<?= $t['duration_days'] ?>">
                        <?= htmlspecialchars($t['tour_code'] . ' - ' . $t['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ngày khởi hành <span
                        class="text-red-500">*</span></label>
                <input type="date" name="start_date" id="start_date"
                    class="w-full px-3 py-2 border rounded focus:border-accent" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ngày kết thúc (Dự kiến)</label>
                <input type="date" id="end_date_display" class="w-full px-3 py-2 border bg-gray-100 rounded" readonly>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Số chỗ mở bán (Quota)</label>
            <input type="number" name="quota" value="20" class="w-full px-3 py-2 border rounded focus:border-accent">
        </div>

        <div class="border-t pt-4 mt-4">
            <h3 class="font-bold text-gray-800 mb-2">Giá bán (Để trống nếu dùng giá gốc của Tour)</h3>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-gray-600">Giá người lớn</label>
                    <input type="number" name="adult_price" id="adult_price" placeholder="Giá gốc..."
                        class="w-full px-3 py-2 border rounded">
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Giá trẻ em</label>
                    <input type="number" name="child_price" id="child_price" placeholder="Giá gốc..."
                        class="w-full px-3 py-2 border rounded">
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Giá em bé</label>
                    <input type="number" name="infant_price" id="infant_price" placeholder="Giá gốc..."
                        class="w-full px-3 py-2 border rounded">
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <a href="?act=admin&module=schedules"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Hủy</a>
            <button type="submit" class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600">Lưu Lịch
                Trình</button>
        </div>
    </form>
</div>

<script>
    const tourSelect = document.getElementById('tour_id');
    const startDateInput = document.getElementById('start_date');
    const endDateDisplay = document.getElementById('end_date_display');

    // Price inputs
    const adultPriceInput = document.getElementById('adult_price');
    const childPriceInput = document.getElementById('child_price');
    const infantPriceInput = document.getElementById('infant_price');

    function updateEndDate() {
        const selectedOption = tourSelect.options[tourSelect.selectedIndex];
        const duration = parseInt(selectedOption.dataset.duration) || 0;
        const startDate = new Date(startDateInput.value);

        if (startDateInput.value && duration > 0) {
            startDate.setDate(startDate.getDate() + duration);
            endDateDisplay.value = startDate.toISOString().split('T')[0];
        }
    }

    function updateDefaultPrices() {
        const selectedOption = tourSelect.options[tourSelect.selectedIndex];
        if (selectedOption.value) {
            adultPriceInput.placeholder = new Intl.NumberFormat('vi-VN').format(selectedOption.dataset.priceAdult) + ' đ';
            childPriceInput.placeholder = new Intl.NumberFormat('vi-VN').format(selectedOption.dataset.priceChild) + ' đ';
            infantPriceInput.placeholder = new Intl.NumberFormat('vi-VN').format(selectedOption.dataset.priceInfant) + ' đ';
        }
    }

    tourSelect.addEventListener('change', function () {
        updateEndDate();
        updateDefaultPrices();
    });
    startDateInput.addEventListener('change', updateEndDate);
</script>