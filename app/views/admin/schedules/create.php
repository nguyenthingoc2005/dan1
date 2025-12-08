<?php
/**
 * ADMIN - THÊM LỊCH KHỞI HÀNH
 * Variables: $tours
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-4xl mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Thêm Lịch Khởi Hành</h1>
        <a href="?act=admin&module=schedules" class="text-primary-500 hover:text-primary-700 font-semibold text-sm lg:text-base flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại danh sách
        </a>
    </div>

    <form action="?act=admin&module=schedules&action=store" method="POST" class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100 space-y-4 lg:space-y-6">
        
        <!-- Tour Selection with Quick Search -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                Chọn Tour <span class="text-danger">*</span>
            </label>
            
            <!-- Quick Search Box -->
            <div class="mb-3">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-primary-400"></i>
                    <input type="text" id="tour-search-input" 
                        placeholder="Tìm kiếm tour nhanh (mã tour, tên tour)..."
                        class="w-full pl-10 pr-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        oninput="filterTourOptions()">
                </div>
                <p class="text-xs text-primary-500 mt-1">Gõ để tìm kiếm tour nhanh chóng</p>
            </div>
            
            <select name="tour_id" id="tour_id" 
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base" required>
                <option value="">-- Chọn Tour --</option>
                <?php foreach ($tours as $t): ?>
                    <option value="<?= $t['id'] ?>" 
                        data-price-adult="<?= $t['adult_price'] ?>"
                        data-price-child="<?= $t['child_price'] ?>"
                        data-price-infant="<?= $t['infant_price'] ?>"
                        data-duration="<?= $t['duration_days'] ?>"
                        data-tour-type="<?= $t['tour_type'] ?>"
                        data-min-pax="<?= $t['min_participants'] ?? 10 ?>"
                        data-max-pax="<?= $t['max_participants'] ?? 45 ?>"
                        data-tour-code="<?= htmlspecialchars(strtolower($t['tour_code'])) ?>"
                        data-tour-name="<?= htmlspecialchars(strtolower($t['name'])) ?>">
                        <?= htmlspecialchars($t['tour_code'] . ' - ' . $t['name']) ?>
                        <?php if ($t['tour_type'] == 'custom'): ?>
                            <span class="text-purple-600">(Custom)</span>
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div id="tour-info" class="mt-3 p-4 bg-info-bg border border-info rounded-2xl hidden">
                <div class="flex items-center gap-2 mb-2">
                    <span id="tour-type-badge" class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold"></span>
                    <span id="tour-participants" class="text-xs lg:text-sm font-semibold text-primary-700"></span>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 text-xs lg:text-sm text-primary-600">
                    <div>
                        <span class="font-semibold">Thời gian:</span>
                        <span id="tour-duration" class="ml-2"></span>
                    </div>
                    <div>
                        <span class="font-semibold">Giá mặc định:</span>
                        <span id="tour-default-prices" class="ml-2"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dates -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Ngày khởi hành <span class="text-danger">*</span>
                </label>
                <input type="date" name="start_date" id="start_date" 
                    min="<?= date('Y-m-d') ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base" required>
                <p class="text-xs text-primary-500 mt-1">Phải từ hôm nay trở đi</p>
            </div>
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ngày kết thúc (Tự động)</label>
                <input type="date" id="end_date_display" 
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl text-primary-700 text-sm lg:text-base" readonly>
                <input type="hidden" id="end_date" name="end_date">
            </div>
        </div>

        <!-- Quota -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                Số chỗ mở bán (Quota) <span class="text-danger">*</span>
            </label>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                <input type="number" name="quota" id="quota" value="" min="1" 
                    class="w-full sm:w-48 px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base" required>
                <span class="text-xs lg:text-sm text-primary-500">
                    (Tối thiểu: <span id="min-pax-display" class="font-semibold">10</span>, 
                    Tối đa: <span id="max-pax-display" class="font-semibold">45</span>)
                </span>
            </div>
            <p id="quota-warning" class="text-xs text-danger-text mt-1 hidden"></p>
            <p class="text-xs text-primary-500 mt-1">Mặc định: Số chỗ = Số người tối đa của tour</p>
        </div>

        <!-- Pricing -->
        <div class="border-t border-primary-100 pt-4">
            <h3 class="font-bold text-primary-700 mb-3 text-sm lg:text-base">Giá bán (Để trống nếu dùng giá gốc của Tour)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-600 mb-1 lg:mb-2">Giá người lớn</label>
                    <input type="number" name="adult_price" id="adult_price" min="0" step="1000"
                        placeholder="Giá gốc..."
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
                </div>
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-600 mb-1 lg:mb-2">Giá trẻ em</label>
                    <input type="number" name="child_price" id="child_price" min="0" step="1000"
                        placeholder="Giá gốc..."
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
                </div>
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-600 mb-1 lg:mb-2">Giá em bé</label>
                    <input type="number" name="infant_price" id="infant_price" min="0" step="1000"
                        placeholder="Giá gốc..."
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-primary-100">
            <a href="?act=admin&module=schedules"
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center">
                Hủy
            </a>
            <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                Lưu Lịch Khởi Hành
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
            const minPax = parseInt(selectedOption.dataset.minPax) || 10;
            const maxPax = parseInt(selectedOption.dataset.maxPax) || 45;
            const duration = parseInt(selectedOption.dataset.duration) || 1;
            const adultPrice = parseFloat(selectedOption.dataset.priceAdult) || 0;
            const childPrice = parseFloat(selectedOption.dataset.priceChild) || 0;
            const infantPrice = parseFloat(selectedOption.dataset.priceInfant) || 0;

            tourInfo.classList.remove('hidden');
            
            if (tourType == 'custom') {
                tourTypeBadge.textContent = 'Tour Tùy Chỉnh';
                tourTypeBadge.className = 'px-2 lg:px-3 py-1 rounded-full text-xs font-bold bg-accent-bg text-accent-text';
            } else {
                tourTypeBadge.textContent = 'Tour Công Khai';
                tourTypeBadge.className = 'px-2 lg:px-3 py-1 rounded-full text-xs font-bold bg-info-bg text-info-text';
            }

            tourParticipants.textContent = `Số khách: ${minPax} - ${maxPax} người`;
            minPaxDisplay.textContent = minPax;
            maxPaxDisplay.textContent = maxPax;

            // Hiển thị thời gian
            const durationDisplay = document.getElementById('tour-duration');
            if (durationDisplay) {
                durationDisplay.textContent = `${duration} ngày ${duration > 1 ? duration - 1 : 0} đêm`;
            }

            // Hiển thị giá mặc định
            const defaultPricesDisplay = document.getElementById('tour-default-prices');
            if (defaultPricesDisplay) {
                const formatPrice = (price) => new Intl.NumberFormat('vi-VN').format(price) + ' đ';
                defaultPricesDisplay.innerHTML = `
                    NL: <span class="font-bold text-blue-700">${formatPrice(adultPrice)}</span>, 
                    TE: ${formatPrice(childPrice)}, 
                    EB: ${formatPrice(infantPrice)}
                `;
            }

            // Update quota: Set default = max_participants
            quotaInput.min = minPax;
            quotaInput.max = maxPax;
            quotaInput.value = maxPax; // Default = max_participants
        } else {
            tourInfo.classList.add('hidden');
            quotaInput.value = '';
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

        const minPax = parseInt(selectedOption.dataset.minPax) || 10;
        const maxPax = parseInt(selectedOption.dataset.maxPax) || 45;
        const quota = parseInt(quotaInput.value) || 0;

        if (quota < minPax) {
            quotaWarning.textContent = `⚠ Số chỗ không được nhỏ hơn ${minPax} (số khách tối thiểu của tour)`;
            quotaWarning.classList.remove('hidden');
        } else if (quota > maxPax) {
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

    // Quick search function for tour selection
    function filterTourOptions() {
        const searchInput = document.getElementById('tour-search-input');
        const searchTerm = searchInput.value.toLowerCase().trim();
        const options = tourSelect.querySelectorAll('option:not([value=""])');
        
        let visibleCount = 0;
        
        options.forEach(option => {
            const tourCode = option.dataset.tourCode || '';
            const tourName = option.dataset.tourName || '';
            const match = tourCode.includes(searchTerm) || tourName.includes(searchTerm);
            
            if (match) {
                option.style.display = '';
                visibleCount++;
            } else {
                option.style.display = 'none';
            }
        });
        
        // Show/hide placeholder option based on search
        const placeholderOption = tourSelect.querySelector('option[value=""]');
        if (placeholderOption) {
            if (searchTerm && visibleCount === 0) {
                placeholderOption.textContent = '-- Không tìm thấy tour --';
            } else {
                placeholderOption.textContent = '-- Chọn Tour --';
            }
        }
    }
</script>

