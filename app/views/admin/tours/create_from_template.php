<?php
/**
 * ADMIN - TẠO TOUR TỪ TEMPLATE (Clone & Customize)
 * Variables: $old_input, $categories, $destinations, $services, $template_info
 */
if (!is_admin())
    redirect('?act=access-denied');

$old = $old_input ?? [];
$errs = $errors ?? [];
?>

<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 lg:gap-3 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700 flex items-center gap-2">
                <i data-lucide="copy" class="w-5 h-5 lg:w-6 lg:h-6 text-accent"></i>
                Tạo Tour từ Template
            </h1>
            <?php if (!empty($template_info)): ?>
                <p class="text-xs lg:text-sm text-primary-500 mt-1 lg:mt-2">
                    Dựa trên: <span class="font-semibold text-accent"><?= htmlspecialchars($template_info['code']) ?></span>
                    - <?= htmlspecialchars($template_info['name']) ?>
                </p>
            <?php endif; ?>
        </div>
        <a href="?act=admin&module=tours&action=selectTemplate" class="text-primary-500 hover:text-accent font-semibold flex items-center gap-2 text-xs lg:text-sm transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Chọn template khác
        </a>
    </div>

    <!-- Error Alert -->
    <?php if (!empty($errs)): ?>
        <div class="bg-danger-bg border border-danger rounded-xl px-4 py-3 lg:px-5 lg:py-4 mb-4 lg:mb-6">
            <ul class="list-disc list-inside text-xs lg:text-sm text-danger-dark">
                <?php foreach ($errs as $msg): ?>
                    <li class="font-semibold"><?= htmlspecialchars($msg) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="?act=admin&module=tours&action=store" enctype="multipart/form-data" class="space-y-6">
        <!-- Hidden Fields -->
        <input type="hidden" name="parent_tour_id" value="<?= $old['parent_tour_id'] ?? '' ?>">
        <input type="hidden" name="tour_type" value="custom">
        <input type="hidden" name="code" value="AUTO">

        <!-- Section 1: Thông tin cơ bản -->
        <div class="bg-panel rounded-2xl shadow-sm overflow-hidden mb-4 lg:mb-6">
            <div class="bg-primary-50 px-4 lg:px-5 py-3 lg:py-4 border-b border-primary-100">
                <h2 class="font-bold text-primary-700 text-base lg:text-lg flex items-center gap-2">
                    <i data-lucide="info" class="w-4 h-4 lg:w-5 lg:h-5 text-accent"></i>
                    1. Thông tin cơ bản
                </h2>
            </div>
            <div class="p-4 lg:p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tên Tour <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Danh mục</label>
                        <select name="category_id" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $id => $name): ?>
                                <option value="<?= $id ?>" <?= ($old['category_id'] ?? '') == $id ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Điểm khởi hành</label>
                        <input type="text" name="departure_location" value="<?= htmlspecialchars($old['departure_location'] ?? '') ?>"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số ngày <span class="text-danger">*</span></label>
                        <input type="number" name="duration_days" id="duration_days" min="1" required
                            value="<?= $old['duration_days'] ?? '1' ?>"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                            onchange="updateItineraryDays()">
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số đêm</label>
                        <input type="number" name="duration_nights" min="0" value="<?= $old['duration_nights'] ?? '0' ?>"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả ngắn</label>
                        <textarea name="description" rows="2" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Số lượng khách & Đặt cọc -->
                <div class="mt-4 lg:mt-5 p-3 lg:p-4 bg-info-bg rounded-xl border border-info">
                    <h4 class="font-bold text-info-dark mb-2 lg:mb-3 text-sm lg:text-base flex items-center gap-2">
                        <i data-lucide="users" class="w-4 h-4 lg:w-5 lg:h-5"></i>
                        Số lượng khách & Đặt cọc
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 lg:gap-4">
                        <div>
                            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số khách tối thiểu</label>
                            <input type="number" name="min_participants" min="1" value="<?= $old['min_participants'] ?? '10' ?>"
                                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base text-center">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số khách tối đa</label>
                            <input type="number" name="max_participants" min="1" value="<?= $old['max_participants'] ?? '45' ?>"
                                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base text-center">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Giá tính cho</label>
                            <div class="flex items-center">
                                <input type="number" name="price_based_on_pax" min="1" value="<?= $old['price_based_on_pax'] ?? '30' ?>"
                                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-l-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base text-center">
                                <span class="px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-100 border border-l-0 border-primary-100 rounded-r-xl text-xs lg:text-sm text-primary-600 font-semibold">khách</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tỷ lệ đặt cọc</label>
                            <div class="flex items-center">
                                <input type="number" name="deposit_percentage" min="0" max="100" value="<?= $old['deposit_percentage'] ?? '30' ?>"
                                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-l-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base text-center">
                                <span class="px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-100 border border-l-0 border-primary-100 rounded-r-xl text-xs lg:text-sm text-primary-600 font-semibold">%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Lịch trình -->
        <div class="bg-panel rounded-2xl shadow-sm overflow-hidden mb-4 lg:mb-6">
            <div class="bg-primary-50 px-4 lg:px-5 py-3 lg:py-4 border-b border-primary-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <h2 class="font-bold text-primary-700 text-base lg:text-lg flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 lg:w-5 lg:h-5 text-accent"></i>
                    2. Lịch trình
                </h2>
                <span class="text-xs lg:text-sm text-success font-semibold flex items-center gap-1">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Đã copy từ template
                </span>
            </div>
            <div class="p-4 lg:p-5" id="itinerary-container">
                <!-- Generated by JS -->
            </div>
        </div>

        <!-- Section 3: Giá bán -->
        <div class="bg-panel rounded-2xl shadow-sm overflow-hidden mb-4 lg:mb-6">
            <div class="bg-primary-50 px-4 lg:px-5 py-3 lg:py-4 border-b border-primary-100">
                <h2 class="font-bold text-primary-700 text-base lg:text-lg flex items-center gap-2">
                    <i data-lucide="dollar-sign" class="w-4 h-4 lg:w-5 lg:h-5 text-accent"></i>
                    3. Giá bán
                </h2>
            </div>
            <div class="p-4 lg:p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-5">
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Giá người lớn <span class="text-danger">*</span></label>
                        <input type="number" name="adult_price" id="adult_price" required min="0"
                            value="<?= $old['adult_price'] ?? '' ?>"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base font-bold">
                        <p class="text-xs text-primary-400 mt-1">Trên 12 tuổi</p>
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Giá trẻ em</label>
                        <input type="number" name="child_price" min="0" value="<?= $old['child_price'] ?? '' ?>"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                        <p class="text-xs text-primary-400 mt-1">5-11 tuổi (thường 70-80% người lớn)</p>
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Giá em bé</label>
                        <input type="number" name="infant_price" min="0" value="<?= $old['infant_price'] ?? '0' ?>"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                        <p class="text-xs text-primary-400 mt-1">Dưới 5 tuổi</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Dịch vụ (Optional) -->
        <div class="bg-panel rounded-2xl shadow-sm overflow-hidden mb-4 lg:mb-6">
            <div class="bg-primary-50 px-4 lg:px-5 py-3 lg:py-4 border-b border-primary-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <h2 class="font-bold text-primary-700 text-base lg:text-lg flex items-center gap-2">
                    <i data-lucide="briefcase" class="w-4 h-4 lg:w-5 lg:h-5 text-accent"></i>
                    4. Dịch vụ đi kèm <span class="text-primary-400 font-normal text-xs lg:text-sm">(Tùy chọn)</span>
                </h2>
                <button type="button" onclick="addServiceRow()" class="text-xs lg:text-sm text-accent hover:opacity-80 font-semibold flex items-center gap-1 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Thêm dịch vụ
                </button>
            </div>
            <div class="p-4 lg:p-5 overflow-x-auto">
                <table class="w-full text-xs lg:text-sm border border-primary-100 rounded-xl overflow-hidden">
                    <thead class="bg-primary-50">
                        <tr>
                            <th class="px-3 lg:px-4 py-2 lg:py-2.5 text-left font-semibold text-primary-700">Dịch vụ</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-2.5 text-left font-semibold text-primary-700 w-28">Số lượng</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-2.5 text-left font-semibold text-primary-700 w-32">Đơn giá</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-2.5 text-left font-semibold text-primary-700 w-24">ĐVT</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-2.5 w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="services-container" class="bg-panel">
                        <!-- Generated by JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 5: Bao gồm / Không bao gồm -->
        <div class="bg-panel rounded-2xl shadow-sm overflow-hidden mb-4 lg:mb-6">
            <div class="bg-primary-50 px-4 lg:px-5 py-3 lg:py-4 border-b border-primary-100">
                <h2 class="font-bold text-primary-700 text-base lg:text-lg flex items-center gap-2">
                    <i data-lucide="check-square" class="w-4 h-4 lg:w-5 lg:h-5 text-accent"></i>
                    5. Bao gồm / Không bao gồm
                </h2>
            </div>
            <div class="p-4 lg:p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-5">
                    <!-- Bao gồm -->
                    <div class="bg-success-bg border border-success rounded-xl p-3 lg:p-4">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-2 lg:mb-3">
                            <h3 class="font-bold text-success-dark text-sm lg:text-base flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4 lg:w-5 lg:h-5"></i>
                                Giá tour BAO GỒM
                            </h3>
                            <button type="button" onclick="addIncludedItem()" class="text-xs lg:text-sm text-success hover:opacity-80 font-semibold flex items-center gap-1 transition-colors">
                                <i data-lucide="plus" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                Thêm
                            </button>
                        </div>
                        <div id="included-container" class="space-y-2"></div>
                    </div>
                    <!-- Không bao gồm -->
                    <div class="bg-danger-bg border border-danger rounded-xl p-3 lg:p-4">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-2 lg:mb-3">
                            <h3 class="font-bold text-danger-dark text-sm lg:text-base flex items-center gap-2">
                                <i data-lucide="x-circle" class="w-4 h-4 lg:w-5 lg:h-5"></i>
                                Giá tour KHÔNG BAO GỒM
                            </h3>
                            <button type="button" onclick="addExcludedItem()" class="text-xs lg:text-sm text-danger hover:opacity-80 font-semibold flex items-center gap-1 transition-colors">
                                <i data-lucide="plus" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                Thêm
                            </button>
                        </div>
                        <div id="excluded-container" class="space-y-2"></div>
                    </div>
                </div>
                <div class="mt-3 lg:mt-4 text-center flex flex-wrap justify-center gap-2 lg:gap-3">
                    <button type="button" onclick="applyTemplate('domestic')" class="text-xs lg:text-sm text-accent hover:opacity-80 font-semibold transition-colors">Mẫu tour trong nước</button>
                    <span class="text-primary-300">|</span>
                    <button type="button" onclick="applyTemplate('international')" class="text-xs lg:text-sm text-accent hover:opacity-80 font-semibold transition-colors">Mẫu tour quốc tế</button>
                </div>
            </div>
        </div>

        <!-- Section 6: Hình ảnh & Highlights -->
        <div class="bg-panel rounded-2xl shadow-sm overflow-hidden mb-4 lg:mb-6">
            <div class="bg-primary-50 px-4 lg:px-5 py-3 lg:py-4 border-b border-primary-100">
                <h2 class="font-bold text-primary-700 text-base lg:text-lg flex items-center gap-2">
                    <i data-lucide="image" class="w-4 h-4 lg:w-5 lg:h-5 text-accent"></i>
                    6. Hình ảnh & Điểm nổi bật
                </h2>
            </div>
            <div class="p-4 lg:p-5 space-y-4 lg:space-y-5">
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-2 lg:mb-3">Hình ảnh Tour</label>
                    <div class="border-2 border-dashed border-primary-200 rounded-xl p-4 lg:p-6 text-center cursor-pointer hover:bg-primary-50 transition-colors"
                        onclick="document.getElementById('images').click()">
                        <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden" onchange="previewImages(this)">
                        <div class="mb-2 lg:mb-3">
                            <i data-lucide="image" class="w-12 h-12 lg:w-16 lg:h-16 mx-auto text-primary-300"></i>
                        </div>
                        <p class="text-primary-600 text-xs lg:text-sm font-semibold">Click để chọn ảnh (tối đa 10 ảnh)</p>
                    </div>
                    <div id="image-preview" class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 gap-2 lg:gap-3 mt-3"></div>
                </div>
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Điểm nổi bật</label>
                    <textarea name="highlights" rows="3" placeholder="Mỗi dòng một điểm nổi bật..."
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"><?= htmlspecialchars(is_array($old['highlights'] ?? null) ? implode("\n", $old['highlights']) : ($old['highlights'] ?? '')) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex flex-col sm:flex-row justify-end gap-2 lg:gap-3">
            <a href="?act=admin&module=tours&action=selectTemplate" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl font-semibold hover:bg-primary-100 transition-colors text-sm lg:text-base text-center">
                Hủy
            </a>
            <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-success-gradient-from to-success-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                Tạo Tour Custom
            </button>
        </div>
    </form>
</div>

<script>
    let currentDestinations = <?= json_encode($destinations) ?>;
    const allDestinations = <?= json_encode($destinations) ?>;
    const servicesList = <?= json_encode($services ?? []) ?>;
    const serviceUnits = <?= json_encode(get_service_units()) ?>;
    const oldItinerary = <?= json_encode($old['itinerary'] ?? []) ?>;
    const oldServices = <?= json_encode($old['services'] ?? []) ?>;

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        generateItinerary();
        if (oldServices.length > 0) {
            oldServices.forEach(s => addServiceRow(s));
        }

        // Listen for category change
        const categorySelect = document.querySelector('select[name="category_id"]');
        if (categorySelect) {
            categorySelect.addEventListener('change', function() {
                loadDestinationsByCategory(this.value);
            });
        }
    });

    function loadDestinationsByCategory(categoryId) {
        if (!categoryId) {
            currentDestinations = {...allDestinations};
            updateDestinationDropdowns();
            return;
        }

        fetch(`?act=admin&module=tours&action=getDestinations&category_id=${categoryId}`)
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    currentDestinations = {};
                    response.data.forEach(d => {
                        currentDestinations[d.id] = d.name;
                    });
                    updateDestinationDropdowns();
                }
            })
            .catch(err => console.error('Error:', err));
    }

    function updateDestinationDropdowns() {
        document.querySelectorAll('select[name="itinerary_dest[]"]').forEach(select => {
            const currentValue = select.value;
            let options = '<option value="">-- Điểm đến --</option>';
            for (const [id, name] of Object.entries(currentDestinations)) {
                const selected = id == currentValue ? 'selected' : '';
                options += `<option value="${id}" ${selected}>${name}</option>`;
            }
            select.innerHTML = options;
        });
    }

    function generateItinerary() {
        const days = parseInt(document.getElementById('duration_days').value) || 1;
        const container = document.getElementById('itinerary-container');
        container.innerHTML = '';

        let destOptions = '<option value="">-- Điểm đến --</option>';
        for (const [id, name] of Object.entries(currentDestinations)) {
            destOptions += `<option value="${id}">${name}</option>`;
        }

        for (let i = 1; i <= days; i++) {
            const oldData = oldItinerary[i - 1] || {};
            const title = oldData.title || '';
            const desc = oldData.description || '';
            const destId = oldData.destination_id || '';

            const html = `
                <div class="bg-gray-50 p-4 rounded border mb-3">
                    <input type="hidden" name="itinerary_day[]" value="${i}">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-8 h-8 bg-accent text-white rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">${i}</span>
                        <input type="text" name="itinerary_title[]" value="${title}" placeholder="Tiêu đề ngày ${i}..."
                            class="flex-1 px-3 py-2 border rounded focus:border-accent focus:outline-none" required>
                        <select name="itinerary_dest[]" class="px-3 py-2 border rounded focus:border-accent focus:outline-none w-48">
                            ${destOptions}
                        </select>
                    </div>
                    <textarea name="itinerary_desc[]" rows="2" placeholder="Mô tả hoạt động..."
                        class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none text-sm">${desc}</textarea>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);

            if (destId) {
                const selects = container.querySelectorAll('select[name="itinerary_dest[]"]');
                selects[selects.length - 1].value = destId;
            }
        }
    }

    function updateItineraryDays() {
        generateItinerary();
    }

    function buildUnitOptions(selectedUnit = '') {
        let options = '<option value="">-- Chọn --</option>';
        for (const [value, label] of Object.entries(serviceUnits)) {
            const selected = (value === selectedUnit) ? 'selected' : '';
            options += `<option value="${value}" ${selected}>${label}</option>`;
        }
        return options;
    }

    function addServiceRow(data = {}) {
        const container = document.getElementById('services-container');
        let serviceOptions = '<option value="">-- Chọn dịch vụ --</option>';
        servicesList.forEach(s => {
            const selected = (data.service_id == s.id) ? 'selected' : '';
            const price = s.unit_price || 0;
            const unit = s.unit || '';
            const name = s.name || '';
            serviceOptions += `<option value="${s.id}" data-price="${price}" data-unit="${unit}" data-name="${name}" ${selected}>${name} (${new Intl.NumberFormat('vi-VN').format(price)}đ)</option>`;
        });

        const unitPrice = data.unit_price || 0;
        const unitVal = data.unit || '';
        const serviceName = data.service_name || '';

        const html = `
            <tr class="border-b hover:bg-gray-50">
                <td class="px-3 py-2">
                    <select name="service_ids[]" class="w-full px-2 py-1 border rounded text-sm" onchange="fillServiceData(this)">
                        ${serviceOptions}
                    </select>
                    <input type="hidden" name="service_names[]" class="service-name" value="${serviceName}">
                    <input type="hidden" name="service_calc_types[]" value="${data.calculation_type || 'per_person'}">
                </td>
                <td class="px-3 py-2">
                    <input type="number" name="service_quantities[]" value="${data.fixed_quantity || 1}" min="1" class="w-full px-2 py-1 border rounded text-center text-sm">
                </td>
                <td class="px-3 py-2">
                    <input type="number" name="service_prices[]" value="${unitPrice}" min="0" class="w-full px-2 py-1 border rounded text-right text-sm service-price">
                </td>
                <td class="px-3 py-2">
                    <select name="service_units[]" class="w-full px-2 py-1 border rounded text-sm service-unit">
                        ${buildUnitOptions(unitVal)}
                    </select>
                </td>
                <td class="px-3 py-2 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-red-500 hover:text-red-700">✕</button>
                </td>
            </tr>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function fillServiceData(select) {
        const opt = select.options[select.selectedIndex];
        const row = select.closest('tr');
        if (opt && opt.value) {
            const price = opt.getAttribute('data-price') || 0;
            const unit = opt.getAttribute('data-unit') || '';
            const name = opt.getAttribute('data-name') || '';

            row.querySelector('.service-price').value = price;
            row.querySelector('.service-name').value = name;
            
            // Set unit dropdown
            const unitSelect = row.querySelector('.service-unit');
            if (unit && unitSelect) {
                unitSelect.value = unit;
                if (!unitSelect.value && unit) {
                    unitSelect.innerHTML += `<option value="${unit}" selected>${unit}</option>`;
                }
            }
        } else {
            row.querySelector('.service-price').value = 0;
            row.querySelector('.service-name').value = '';
            row.querySelector('.service-unit').value = '';
        }
    }

    function previewImages(input) {
        const container = document.getElementById('image-preview');
        container.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'aspect-video bg-gray-100 rounded overflow-hidden';
                    div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    container.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        }
    }

    // ==============================================================================
    // INCLUDED / EXCLUDED LOGIC
    // ==============================================================================
    const templates = {
        domestic: {
            included: [
                'Xe đưa đón theo chương trình',
                'Khách sạn tiêu chuẩn 3* hoặc tương đương (2 khách/phòng)',
                'Các bữa ăn theo chương trình',
                'Vé tham quan các điểm trong chương trình',
                'Hướng dẫn viên suốt tuyến',
                'Bảo hiểm du lịch (mức bồi thường tối đa 20.000.000đ/vụ)',
                'Nước suối trên xe (1 chai/ngày)'
            ],
            excluded: [
                'Chi phí cá nhân: giặt ủi, điện thoại, đồ uống...',
                'Các chi phí không đề cập trong mục "Bao gồm"',
                'Phụ thu phòng đơn',
                'Tiền tip cho lái xe, HDV (tùy tâm)'
            ]
        },
        international: {
            included: [
                'Vé máy bay khứ hồi (bao gồm thuế sân bay)',
                'Khách sạn tiêu chuẩn 3-4* (2 khách/phòng)',
                'Xe đưa đón theo chương trình',
                'Các bữa ăn theo chương trình',
                'Vé tham quan các điểm',
                'Hướng dẫn viên Việt Nam suốt tuyến',
                'Bảo hiểm du lịch quốc tế',
                'Visa (nếu có)'
            ],
            excluded: [
                'Hộ chiếu còn hạn trên 6 tháng',
                'Chi phí cá nhân: giặt ủi, điện thoại, đồ uống...',
                'Phụ thu phòng đơn',
                'Tiền tip cho lái xe, HDV ($5/ngày/khách)',
                'Chi phí ngoài chương trình'
            ]
        }
    };

    function addIncludedItem(text = '') {
        const container = document.getElementById('included-container');
        const html = `
            <div class="flex items-center gap-2">
                <span class="text-green-600">✓</span>
                <input type="text" name="included[]" value="${text}" placeholder="Nhập nội dung..."
                    class="flex-1 px-3 py-2 border rounded focus:border-green-500 focus:outline-none text-sm">
                <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600">✕</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function addExcludedItem(text = '') {
        const container = document.getElementById('excluded-container');
        const html = `
            <div class="flex items-center gap-2">
                <span class="text-red-600">✗</span>
                <input type="text" name="excluded[]" value="${text}" placeholder="Nhập nội dung..."
                    class="flex-1 px-3 py-2 border rounded focus:border-red-500 focus:outline-none text-sm">
                <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600">✕</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function applyTemplate(type) {
        const template = templates[type];
        if (!template) return;

        document.getElementById('included-container').innerHTML = '';
        document.getElementById('excluded-container').innerHTML = '';

        template.included.forEach(item => addIncludedItem(item));
        template.excluded.forEach(item => addExcludedItem(item));
    }

    // Init default template
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('included-container').children.length === 0) {
            applyTemplate('domestic');
        }
    });
</script>
