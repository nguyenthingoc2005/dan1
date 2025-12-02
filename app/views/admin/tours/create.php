<?php
/**
 * ADMIN - FORM TẠO TOUR (WIZARD UI)
 * Variables: $categories, $destinations, $old_input (optional), $errors (optional)
 */
if (!is_admin())
    redirect('?act=access-denied');

$old = $old_input ?? [];
$errs = $errors ?? [];
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Thêm Tour mới</h1>
        <a href="?act=admin&module=tours" class="text-gray-500 hover:text-gray-700">Quay lại danh sách</a>
    </div>

    <!-- ERROR ALERT -->
    <?php if (!empty($errs)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Có lỗi xảy ra!</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                <?php foreach ($errs as $key => $msg): ?>
                    <li><?= htmlspecialchars($msg) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form id="tourForm" method="POST" action="?act=admin&module=tours&action=store" enctype="multipart/form-data"
        class="bg-white rounded shadow-sm overflow-hidden">

        <!-- WIZARD STEPS -->
        <div class="flex border-b bg-gray-50">
            <div class="step-indicator px-6 py-3 text-sm font-medium text-accent border-b-2 border-accent w-1/4 text-center"
                data-step="1">
                <?php
                /**
                 * ADMIN - FORM TẠO TOUR (WIZARD UI)
                 * Variables: $categories, $destinations, $old_input (optional), $errors (optional)
                 */
                if (!is_admin())
                    redirect('?act=access-denied');

                $old = $old_input ?? [];
                $errs = $errors ?? [];
                ?>

                <div class="max-w-6xl mx-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-primary">Thêm Tour mới</h1>
                        <a href="?act=admin&module=tours" class="text-gray-500 hover:text-gray-700">Quay lại danh
                            sách</a>
                    </div>

                    <!-- ERROR ALERT -->
                    <?php if (!empty($errs)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">Có lỗi xảy ra!</strong>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                <?php foreach ($errs as $key => $msg): ?>
                                    <li><?= htmlspecialchars($msg) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form id="tourForm" method="POST" action="?act=admin&module=tours&action=store"
                        enctype="multipart/form-data" class="bg-white rounded shadow-sm overflow-hidden">

                        <!-- WIZARD STEPS -->
                        <div class="flex border-b bg-gray-50">
                            <div class="step-indicator px-6 py-3 text-sm font-medium text-accent border-b-2 border-accent w-1/5 text-center"
                                data-step="1">
                                1. Thông tin chung
                            </div>
                            <div class="step-indicator px-6 py-3 text-sm font-medium text-gray-400 border-b-2 border-transparent w-1/5 text-center"
                                data-step="2">
                                2. Giá & Vận hành
                            </div>
                            <div class="step-indicator px-6 py-3 text-sm font-medium text-gray-400 border-b-2 border-transparent w-1/5 text-center"
                                data-step="3">
                                3. Lịch trình
                            </div>
                            <div class="step-indicator px-6 py-3 text-sm font-medium text-gray-400 border-b-2 border-transparent w-1/5 text-center"
                                data-step="4">
                                4. Dịch vụ (Costing)
                            </div>
                            <div class="step-indicator px-6 py-3 text-sm font-medium text-gray-400 border-b-2 border-transparent w-1/5 text-center"
                                data-step="5">
                                5. Hình ảnh & Khác
                            </div>
                        </div>

                        <!-- STEPS CONTENT -->
                        <div class="p-6">

                            <!-- STEP 1: INFO -->
                            <div id="step-1" class="step-content block">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tên Tour <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="name" id="name"
                                            value="<?= htmlspecialchars($old['name'] ?? '') ?>" required
                                            class="w-full px-3 py-2 border rounded focus:border-accent <?= isset($errs['name']) ? 'border-red-500' : '' ?>">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Mã Tour <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="code" id="code"
                                            value="<?= htmlspecialchars($old['code'] ?? '') ?>" required
                                            class="w-full px-3 py-2 border rounded focus:border-accent <?= isset($errs['code']) ? 'border-red-500' : '' ?>">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                                        <select name="category_id"
                                            class="w-full px-3 py-2 border rounded focus:border-accent">
                                            <option value="">-- Chọn danh mục --</option>
                                            <?php foreach ($categories as $id => $name): ?>
                                                <option value="<?= $id ?>" <?= ($old['category_id'] ?? '') == $id ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Điểm khởi
                                            hành</label>
                                        <input type="text" name="departure_location"
                                            value="<?= htmlspecialchars($old['departure_location'] ?? '') ?>"
                                            class="w-full px-3 py-2 border rounded focus:border-accent">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Số ngày <span
                                                    class="text-red-500">*</span></label>
                                            <input type="number" name="duration_days" id="duration_days" min="1"
                                                value="<?= $old['duration_days'] ?? '1' ?>" required
                                                class="w-full px-3 py-2 border rounded focus:border-accent font-bold text-accent"
                                                onchange="generateItinerary()">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Số đêm</label>
                                            <input type="number" name="duration_nights" min="0"
                                                value="<?= $old['duration_nights'] ?? '0' ?>"
                                                class="w-full px-3 py-2 border rounded focus:border-accent">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                                        <select name="status"
                                            class="w-full px-3 py-2 border rounded focus:border-accent">
                                            <option value="draft" <?= ($old['status'] ?? '') == 'draft' ? 'selected' : '' ?>>Bản nháp
                                                (Draft)</option>
                                            <option value="active" <?= ($old['status'] ?? '') == 'active' ? 'selected' : '' ?>>Hoạt động
                                                (Active)</option>
                                            <option value="inactive" <?= ($old['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Tạm dừng
                                                (Inactive)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả ngắn</label>
                                    <textarea name="description" rows="3"
                                        class="w-full px-3 py-2 border rounded focus:border-accent"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <!-- STEP 2: PRICING -->
                            <div id="step-2" class="step-content hidden">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá người lớn <span
                                                class="text-red-500">*</span></label>
                                        <input type="number" name="adult_price" id="adult_price" required min="0"
                                            value="<?= $old['adult_price'] ?? '' ?>"
                                            class="w-full px-3 py-2 border rounded focus:border-accent font-bold text-accent text-lg">
                                        <p class="text-xs text-gray-500 mt-1">Giá áp dụng cho khách > 12 tuổi</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá trẻ em</label>
                                        <input type="number" name="child_price" min="0"
                                            value="<?= $old['child_price'] ?? '' ?>"
                                            class="w-full px-3 py-2 border rounded focus:border-accent">
                                        <p class="text-xs text-gray-500 mt-1">Thường bằng 70-80% giá người lớn</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá em bé</label>
                                        <input type="number" name="infant_price" min="0"
                                            value="<?= $old['infant_price'] ?? '0' ?>"
                                            class="w-full px-3 py-2 border rounded focus:border-accent">
                                        <p class="text-xs text-gray-500 mt-1">Dưới 2 tuổi</p>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 3: ITINERARY -->
                            <div id="step-3" class="step-content hidden">
                                <div
                                    class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-2 rounded mb-4 text-sm">
                                    ℹ️ Lịch trình được tạo tự động dựa trên số ngày bạn nhập ở Bước 1.
                                </div>
                                <div id="itinerary-container">
                                    <!-- Dynamic Days will be added here via JS -->
                                </div>
                            </div>

                            <!-- STEP 4: SERVICES -->
                            <div id="step-4" class="step-content hidden">
                                <div class="mb-4 flex justify-between items-center">
                                    <h3 class="font-bold text-gray-700">Cấu hình dịch vụ (Costing)</h3>
                                    <button type="button" onclick="addServiceRow()"
                                        class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 text-sm">
                                        + Thêm dịch vụ
                                    </button>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left text-gray-500 border rounded">
                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3">Dịch vụ</th>
                                                <th class="px-4 py-3">Tính theo</th>
                                                <th class="px-4 py-3 w-24">SL</th>
                                                <th class="px-4 py-3">Đơn giá</th>
                                                <th class="px-4 py-3">ĐVT</th>
                                                <th class="px-4 py-3">Ghi chú</th>
                                                <th class="px-4 py-3"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="services-container">
                                            <!-- Dynamic Rows -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4 bg-yellow-50 p-3 rounded text-sm text-yellow-700">
                                    💡 <strong>Lưu ý:</strong> Đây là giá vốn (Cost) dự kiến để tính lợi nhuận. Giá bán
                                    Tour ở Bước 2.
                                </div>
                            </div>

                            <!-- STEP 5: MEDIA -->
                            <div id="step-5" class="step-content hidden">
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh Tour (Chọn
                                        nhiều)</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:bg-gray-50"
                                        onclick="document.getElementById('images').click()">
                                        <input type="file" name="images[]" id="images" multiple accept="image/*"
                                            class="hidden" onchange="previewImages(this)">
                                        <div class="text-4xl mb-2">📷</div>
                                        <p class="text-gray-500">Click để tải ảnh lên</p>
                                    </div>
                                    <div id="image-preview" class="grid grid-cols-4 gap-4 mt-4"></div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Điểm nổi bật
                                        (Highlights)</label>
                                    <textarea name="highlights" rows="5" placeholder="Mỗi dòng một điểm nổi bật..."
                                        class="w-full px-3 py-2 border rounded focus:border-accent"><?= htmlspecialchars(isset($old['highlights']) ? implode("\n", $old['highlights']) : '') ?></textarea>
                                </div>
                            </div>

                        </div>

                        <!-- FOOTER ACTIONS -->
                        <div class="bg-gray-50 px-6 py-4 flex justify-between border-t">
                            <button type="button" id="prevBtn"
                                class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50 hidden"
                                onclick="changeStep(-1)">
                                ← Quay lại
                            </button>
                            <div class="flex gap-2">
                                <a href="?act=admin&module=tours"
                                    class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50">Hủy</a>
                                <button type="button" id="nextBtn"
                                    class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600 shadow"
                                    onclick="changeStep(1)">
                                    Tiếp theo →
                                </button>
                                <button type="submit" id="submitBtn"
                                    class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 shadow hidden">
                                    Hoàn tất & Lưu
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <script>
                    let currentStep = 1;
                    const totalSteps = 5;
                    const destinations = <?= json_encode($destinations) ?>;
                    const servicesList = <?= json_encode($services ?? []) ?>;

                    // Old Input Data for Itinerary
                    const oldItinerary = <?= json_encode($old['itinerary'] ?? []) ?>;
                    const oldServices = <?= json_encode($old['services'] ?? []) ?>;

                    function showStep(step) {
                        // Hide all steps
                        document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
                        document.querySelectorAll('.step-indicator').forEach(el => {
                            el.classList.remove('text-accent', 'border-accent');
                            el.classList.add('text-gray-400', 'border-transparent');
                        });

                        // Show current step
                        document.getElementById(`step-${step}`).classList.remove('hidden');

                        // Update indicators
                        for (let i = 1; i <= step; i++) {
                            const indicator = document.querySelector(`.step-indicator[data-step="${i}"]`);
                            indicator.classList.remove('text-gray-400', 'border-transparent');
                            indicator.classList.add('text-accent', 'border-accent');
                        }

                        // Button visibility
                        document.getElementById('prevBtn').classList.toggle('hidden', step === 1);
                        document.getElementById('nextBtn').classList.toggle('hidden', step === totalSteps);
                        document.getElementById('submitBtn').classList.toggle('hidden', step !== totalSteps);
                    }

                    function validateStep(step) {
                        let isValid = true;
                        const stepEl = document.getElementById(`step-${step}`);
                        const inputs = stepEl.querySelectorAll('input[required], select[required]');

                        inputs.forEach(input => {
                            if (!input.value.trim()) {
                                isValid = false;
                                input.classList.add('border-red-500');
                                // Remove red border on input
                                input.addEventListener('input', () => input.classList.remove('border-red-500'));
                            }
                        });

                        if (!isValid) {
                            alert('Vui lòng điền đầy đủ thông tin bắt buộc trước khi tiếp tục.');
                        }
                        return isValid;
                    }

                    function changeStep(direction) {
                        if (direction === 1 && !validateStep(currentStep)) return;

                        currentStep += direction;

                        if (currentStep === 3) {
                            generateItinerary(); // Regenerate/Check itinerary when entering step 3
                        }
                        if (currentStep === 4 && oldServices.length === 0 && document.getElementById('services-container').children.length === 0) {
                            addServiceRow(); // Add a default service row if none exist
                        }

                        showStep(currentStep);
                    }

                    function generateItinerary() {
                        const days = parseInt(document.getElementById('duration_days').value) || 1;
                        const container = document.getElementById('itinerary-container');

                        // Check if we need to regenerate (only if count differs)
                        // Note: This simple logic clears data if days change. 
                        // Improvement: Keep existing data if days increase.

                        const currentItems = container.querySelectorAll('.itinerary-item').length;
                        if (currentItems === days && container.innerHTML.trim() !== '') return;

                        container.innerHTML = ''; // Clear current

                        let destOptions = '<option value="">-- Chọn điểm đến chính --</option>';
                        for (const [id, name] of Object.entries(destinations)) {
                            destOptions += `<option value="${id}">${name}</option>`;
                        }

                        for (let i = 1; i <= days; i++) {
                            // Try to recover old data if available
                            const oldData = oldItinerary[i - 1] || {};
                            const title = oldData.title || '';
                            const desc = oldData.description || '';
                            const destId = oldData.destination_id || '';

                            const html = `
                <div class="bg-gray-50 p-4 rounded border mb-4 itinerary-item">
                    <div class="flex justify-between mb-2">
                        <h3 class="font-bold text-gray-700">Ngày ${i}</h3>
                        <input type="hidden" name="itinerary_day[]" value="${i}">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                        <input type="text" name="itinerary_title[]" value="${title}" placeholder="Tiêu đề hoạt động (VD: Đón khách - City Tour)" class="w-full px-3 py-2 border rounded" required>
                        <select name="itinerary_dest[]" class="w-full px-3 py-2 border rounded">
                            ${destOptions}
                        </select>
                    </div>
                    <textarea name="itinerary_desc[]" rows="2" placeholder="Mô tả chi tiết..." class="w-full px-3 py-2 border rounded">${desc}</textarea>
                </div>
            `;
                            container.insertAdjacentHTML('beforeend', html);

                            // Set selected destination manually since template literal is messy with conditionals
                            if (destId) {
                                const selects = container.querySelectorAll('select[name="itinerary_dest[]"]');
                                selects[selects.length - 1].value = destId;
                            }
                        }
                    }

                    function previewImages(input) {
                        const container = document.getElementById('image-preview');
                        container.innerHTML = '';
                        if (input.files) {
                            Array.from(input.files).forEach(file => {
                                const reader = new FileReader();
                                reader.onload = function (e) {
                                    const div = document.createElement('div');
                                    div.className = 'relative aspect-video bg-gray-100 rounded overflow-hidden shadow-sm';
                                    div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                                    container.appendChild(div);
                                }
                                reader.readAsDataURL(file);
                            });
                        }
                    }

                    // SERVICES LOGIC
                    function addServiceRow(data = {}) {
                        const container = document.getElementById('services-container');

                        let serviceOptions = '<option value="">-- Chọn dịch vụ --</option>';
                        servicesList.forEach(s => {
                            const selected = (data.service_id == s.id) ? 'selected' : '';
                            serviceOptions += `<option value="${s.id}" data-price="${s.unit_price}" data-unit="${s.unit}" data-name="${s.name}" ${selected}>${s.name} (${new Intl.NumberFormat('vi-VN').format(s.unit_price)}đ)</option>`;
                        });

                        const html = `
            <tr class="bg-white border-b hover:bg-gray-50">
                <td class="px-2 py-2">
                    <select name="service_ids[]" class="w-full px-2 py-1 border rounded service-select" onchange="fillServiceData(this)" required>
                        ${serviceOptions}
                    </select>
                    <input type="hidden" name="service_names[]" class="service-name-input" value="${data.service_name || ''}">
                </td>
                <td class="px-2 py-2">
                    <select name="service_calc_types[]" class="w-full px-2 py-1 border rounded">
                        <option value="per_person" ${data.calculation_type == 'per_person' ? 'selected' : ''}>Theo khách</option>
                        <option value="per_group" ${data.calculation_type == 'per_group' ? 'selected' : ''}>Theo đoàn</option>
                        <option value="per_day" ${data.calculation_type == 'per_day' ? 'selected' : ''}>Theo ngày</option>
                        <option value="fixed" ${data.calculation_type == 'fixed' ? 'selected' : ''}>Cố định</option>
                    </select>
                </td>
                <td class="px-2 py-2">
                    <input type="number" name="service_quantities[]" value="${data.fixed_quantity || 1}" min="1" class="w-full px-2 py-1 border rounded text-center">
                </td>
                <td class="px-2 py-2">
                    <input type="number" name="service_prices[]" value="${data.unit_price || 0}" min="0" class="w-full px-2 py-1 border rounded text-right service-price">
                </td>
                <td class="px-2 py-2">
                    <input type="text" name="service_units[]" value="${data.unit || ''}" class="w-full px-2 py-1 border rounded service-unit">
                </td>
                <td class="px-2 py-2">
                    <input type="text" name="service_notes[]" value="${data.notes || ''}" class="w-full px-2 py-1 border rounded">
                </td>
                <td class="px-2 py-2 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-red-500 hover:text-red-700">🗑️</button>
                </td>
            </tr>
        `;
                        container.insertAdjacentHTML('beforeend', html);
                    }

                    function fillServiceData(select) {
                        const option = select.options[select.selectedIndex];
                        const row = select.closest('tr');

                        if (option.value) {
                            row.querySelector('.service-price').value = option.dataset.price;
                            row.querySelector('.service-unit').value = option.dataset.unit;
                            row.querySelector('.service-name-input').value = option.dataset.name;
                        }
                    }

                    // Initialize
                    showStep(1);
                    // If old input exists (error case), generate itinerary immediately
                    if (oldItinerary.length > 0) {
                        generateItinerary();
                    }
                    // If old services exist, populate them
                    if (oldServices.length > 0) {
                        oldServices.forEach(service => addServiceRow(service));
                    }
                </script>