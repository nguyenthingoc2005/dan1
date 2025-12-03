<?php
/**
 * ADMIN - THÊM LỊCH KHỞI HÀNH
 * Variables: $tours, $guides
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Thêm Lịch Khởi Hành</h1>
        <a href="?act=admin&module=schedules" class="text-gray-600 hover:text-gray-800">← Quay lại danh sách</a>
    </div>

    <form action="?act=admin&module=schedules&action=store" method="POST" class="bg-white p-6 rounded-lg shadow-sm space-y-6">
        
        <!-- Tour Selection -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Chọn Tour <span class="text-red-500">*</span>
            </label>
            <select name="tour_id" id="tour_id" 
                class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none" required>
                <option value="">-- Chọn Tour --</option>
                <?php foreach ($tours as $t): ?>
                    <option value="<?= $t['id'] ?>" 
                        data-price-adult="<?= $t['adult_price'] ?>"
                        data-price-child="<?= $t['child_price'] ?>"
                        data-price-infant="<?= $t['infant_price'] ?>"
                        data-duration="<?= $t['duration_days'] ?>"
                        data-tour-type="<?= $t['tour_type'] ?>"
                        data-min-pax="<?= $t['min_participants'] ?? 10 ?>"
                        data-max-pax="<?= $t['max_participants'] ?? 45 ?>">
                        <?= htmlspecialchars($t['tour_code'] . ' - ' . $t['name']) ?>
                        <?php if ($t['tour_type'] == 'custom'): ?>
                            <span class="text-purple-600">(Custom)</span>
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div id="tour-info" class="mt-2 text-sm text-gray-600 hidden">
                <span id="tour-type-badge" class="px-2 py-1 rounded text-xs"></span>
                <span id="tour-participants" class="ml-2"></span>
            </div>
        </div>

        <!-- Dates -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Ngày khởi hành <span class="text-red-500">*</span>
                </label>
                <input type="date" name="start_date" id="start_date" 
                    min="<?= date('Y-m-d') ?>"
                    class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none" required>
                <p class="text-xs text-gray-500 mt-1">Phải từ hôm nay trở đi</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ngày kết thúc (Tự động)</label>
                <input type="date" id="end_date_display" 
                    class="w-full px-3 py-2 border bg-gray-100 rounded" readonly>
                <input type="hidden" id="end_date" name="end_date">
            </div>
        </div>

        <!-- Quota -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Số chỗ mở bán (Quota) <span class="text-red-500">*</span>
            </label>
            <div class="flex items-center gap-2">
                <input type="number" name="quota" id="quota" value="20" min="1" 
                    class="w-full md:w-48 px-3 py-2 border rounded focus:border-accent focus:outline-none" required>
                <span class="text-sm text-gray-500">
                    (Tối thiểu: <span id="min-pax-display">10</span>, 
                    Tối đa: <span id="max-pax-display">45</span>)
                </span>
            </div>
            <p id="quota-warning" class="text-xs text-red-600 mt-1 hidden"></p>
        </div>

        <!-- Guide Assignment (Optional) -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Hướng dẫn viên <span class="text-gray-400 text-xs">(Tùy chọn)</span>
            </label>
            <select name="guide_id" class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                <option value="">-- Chưa gán HDV --</option>
                <?php foreach ($guides as $g): ?>
                    <option value="<?= $g['id'] ?>">
                        <?= htmlspecialchars($g['full_name']) ?> 
                        <?php if (!empty($g['phone'])): ?>
                            - <?= htmlspecialchars($g['phone']) ?>
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="text-xs text-gray-500 mt-1">Có thể gán sau khi tạo lịch</p>
        </div>

        <!-- Pricing -->
        <div class="border-t pt-4">
            <h3 class="font-bold text-gray-800 mb-3">Giá bán (Để trống nếu dùng giá gốc của Tour)</h3>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Giá người lớn</label>
                    <input type="number" name="adult_price" id="adult_price" min="0" step="1000"
                        placeholder="Giá gốc..."
                        class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Giá trẻ em</label>
                    <input type="number" name="child_price" id="child_price" min="0" step="1000"
                        placeholder="Giá gốc..."
                        class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Giá em bé</label>
                    <input type="number" name="infant_price" id="infant_price" min="0" step="1000"
                        placeholder="Giá gốc..."
                        class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                </div>
            </div>
        </div>

        <!-- Guide Notes -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú cho HDV</label>
            <textarea name="guide_notes" rows="2" 
                class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none"
                placeholder="Ghi chú đặc biệt cho hướng dẫn viên..."></textarea>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="?act=admin&module=schedules"
                class="px-6 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Hủy</a>
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600 shadow">
                ✓ Lưu Lịch Khởi Hành
            </button>
        </div>
    </form>
</div>

<script>
    const tourSelect = document.getElementById('tour_id');
    const startDateInput = document.getElementById('start_date');
    const endDateDisplay = document.getElementById('end_date_display');
    const endDateHidden = document.getElementById('end_date');
    const quotaInput = document.getElementById('quota');
    const adultPriceInput = document.getElementById('adult_price');
    const childPriceInput = document.getElementById('child_price');
    const infantPriceInput = document.getElementById('infant_price');
    const tourInfo = document.getElementById('tour-info');
    const tourTypeBadge = document.getElementById('tour-type-badge');
    const tourParticipants = document.getElementById('tour-participants');
    const minPaxDisplay = document.getElementById('min-pax-display');
    const maxPaxDisplay = document.getElementById('max-pax-display');
    const quotaWarning = document.getElementById('quota-warning');

    function updateEndDate() {
        const selectedOption = tourSelect.options[tourSelect.selectedIndex];
        const duration = parseInt(selectedOption.dataset.duration) || 0;
        const startDate = startDateInput.value;

        if (startDate && duration > 0) {
            const endDate = new Date(startDate);
            endDate.setDate(endDate.getDate() + duration - 1); // -1 vì tính cả ngày đầu
            const endDateStr = endDate.toISOString().split('T')[0];
            endDateDisplay.value = endDateStr;
            endDateHidden.value = endDateStr;
        } else {
            endDateDisplay.value = '';
            endDateHidden.value = '';
        }
    }

    function updateTourInfo() {
        const selectedOption = tourSelect.options[tourSelect.selectedIndex];
        if (selectedOption.value) {
            const tourType = selectedOption.dataset.tourType;
            const minPax = selectedOption.dataset.minPax || 10;
            const maxPax = selectedOption.dataset.maxPax || 45;

            tourInfo.classList.remove('hidden');
            
            if (tourType == 'custom') {
                tourTypeBadge.textContent = 'Tour Tùy Chỉnh';
                tourTypeBadge.className = 'px-2 py-1 rounded text-xs bg-purple-100 text-purple-700';
            } else {
                tourTypeBadge.textContent = 'Tour Công Khai';
                tourTypeBadge.className = 'px-2 py-1 rounded text-xs bg-blue-100 text-blue-700';
            }

            tourParticipants.textContent = `Số khách: ${minPax} - ${maxPax} người`;
            minPaxDisplay.textContent = minPax;
            maxPaxDisplay.textContent = maxPax;

            // Update quota max
            quotaInput.max = maxPax;
            if (parseInt(quotaInput.value) > maxPax) {
                quotaInput.value = maxPax;
            }
        } else {
            tourInfo.classList.add('hidden');
        }
    }

    function updateDefaultPrices() {
        const selectedOption = tourSelect.options[tourSelect.selectedIndex];
        if (selectedOption.value) {
            const adult = parseFloat(selectedOption.dataset.priceAdult) || 0;
            const child = parseFloat(selectedOption.dataset.priceChild) || 0;
            const infant = parseFloat(selectedOption.dataset.priceInfant) || 0;

            adultPriceInput.placeholder = new Intl.NumberFormat('vi-VN').format(adult) + ' đ';
            childPriceInput.placeholder = new Intl.NumberFormat('vi-VN').format(child) + ' đ';
            infantPriceInput.placeholder = new Intl.NumberFormat('vi-VN').format(infant) + ' đ';
        }
    }

    function validateQuota() {
        const selectedOption = tourSelect.options[tourSelect.selectedIndex];
        if (!selectedOption.value) {
            quotaWarning.classList.add('hidden');
            return;
        }

        const maxPax = parseInt(selectedOption.dataset.maxPax) || 45;
        const quota = parseInt(quotaInput.value) || 0;

        if (quota > maxPax) {
            quotaWarning.textContent = `⚠ Số chỗ không được vượt quá ${maxPax} (số khách tối đa của tour)`;
            quotaWarning.classList.remove('hidden');
        } else {
            quotaWarning.classList.add('hidden');
        }
    }

    // Set min date to today
    startDateInput.min = new Date().toISOString().split('T')[0];

    tourSelect.addEventListener('change', function() {
        updateEndDate();
        updateTourInfo();
        updateDefaultPrices();
        validateQuota();
    });

    startDateInput.addEventListener('change', updateEndDate);
    quotaInput.addEventListener('input', validateQuota);

    // Initial update if tour is pre-selected
    if (tourSelect.value) {
        updateTourInfo();
        updateDefaultPrices();
    }
</script>

