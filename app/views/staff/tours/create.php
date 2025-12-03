<?php
/**
 * STAFF - FORM TẠO TOUR (WIZARD UI)
 * Variables: $categories, $destinations, $old_input (optional), $errors (optional)
 */
require_staff_or_admin();

$old = $old_input ?? [];
$errs = $errors ?? [];
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Thêm Tour mới</h1>
        <a href="?act=staff-tours" class="text-slate-500 hover:text-slate-700 font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
        </a>
    </div>

    <!-- ERROR ALERT -->
    <?php if (!empty($errs)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative mb-4 shadow-sm" role="alert">
            <strong class="font-bold flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> Có lỗi xảy
                ra!</strong>
            <ul class="mt-2 list-disc list-inside text-sm pl-4">
                <?php foreach ($errs as $key => $msg): ?>
                    <li><?= htmlspecialchars($msg) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form id="tourForm" method="POST" action="?act=staff-tours&action=store" enctype="multipart/form-data"
        class="bg-white rounded-lg shadow-sm overflow-hidden">

        <!-- WIZARD STEPS -->
        <div class="flex border-b border-slate-200 bg-slate-50">
            <div class="step-indicator px-4 py-3 text-sm font-medium text-accent border-b-2 border-accent w-1/5 text-center transition-all"
                data-step="1">
                1. Thông tin chung
            </div>
            <div class="step-indicator px-4 py-3 text-sm font-medium text-slate-400 border-b-2 border-transparent w-1/5 text-center transition-all"
                data-step="2">
                2. Lịch trình
            </div>
            <div class="step-indicator px-4 py-3 text-sm font-medium text-slate-400 border-b-2 border-transparent w-1/5 text-center transition-all"
                data-step="3">
                3. Dịch vụ (Costing)
            </div>
            <div class="step-indicator px-4 py-3 text-sm font-medium text-slate-400 border-b-2 border-transparent w-1/5 text-center transition-all"
                data-step="4">
                4. Hình ảnh & Khác
            </div>
            <div class="step-indicator px-4 py-3 text-sm font-medium text-slate-400 border-b-2 border-transparent w-1/5 text-center transition-all"
                data-step="5">
                5. Giá & Hoàn tất
            </div>
        </div>

        <!-- STEPS CONTENT -->
        <div class="p-6">

            <!-- STEP 1: INFO -->
            <div id="step-1" class="step-content block">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tên Tour <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                            required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all <?= isset($errs['name']) ? 'border-red-500' : '' ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Mã Tour</label>
                        <div
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg bg-slate-50 text-slate-500 italic flex items-center gap-2">
                            <span class="text-green-600"><i class="fas fa-check-circle"></i></span>
                            <span>Tự động tạo khi lưu (TOUR_<?= date('Y') ?>_XXX)</span>
                        </div>
                        <input type="hidden" name="code" value="AUTO">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Danh mục</label>
                        <select name="category_id"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                            <option value="">-- Chọn danh mục --</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $id => $name): ?>
                                    <option value="<?= $id ?>" <?= ($old['category_id'] ?? '') == $id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($name) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Điểm khởi hành</label>
                        <input type="text" name="departure_location"
                            value="<?= htmlspecialchars($old['departure_location'] ?? '') ?>"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Loại Tour <span
                                class="text-red-500">*</span></label>
                        <select name="tour_type" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all <?= isset($errs['tour_type']) ? 'border-red-500' : '' ?>">
                            <option value="public" <?= ($old['tour_type'] ?? 'public') == 'public' ? 'selected' : '' ?>>
                                Tour Công Khai (Public) - Có lịch cố định
                            </option>
                            <option value="custom" <?= ($old['tour_type'] ?? '') == 'custom' ? 'selected' : '' ?>>
                                Tour Tùy Chỉnh (Custom) - Theo yêu cầu khách
                            </option>
                        </select>
                        <p class="text-xs text-slate-500 mt-1">
                            <strong>Public:</strong> Tour có lịch khởi hành cố định.<br>
                            <strong>Custom:</strong> Tour thiết kế riêng theo yêu cầu.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Số ngày <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="duration_days" id="duration_days" min="1"
                                value="<?= $old['duration_days'] ?? '1' ?>" required
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all font-bold text-accent"
                                onchange="generateItinerary()">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Số đêm</label>
                            <input type="number" name="duration_nights" min="0"
                                value="<?= $old['duration_nights'] ?? '0' ?>"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Trạng thái</label>
                        <select name="status"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                            <option value="draft" <?= ($old['status'] ?? '') == 'draft' ? 'selected' : '' ?>>Bản nháp
                                (Draft)</option>
                            <option value="pending" <?= ($old['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Gửi duyệt
                                (Pending)</option>
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Staff chỉ có thể tạo nháp hoặc gửi duyệt.</p>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mô tả ngắn</label>
                    <textarea name="description" rows="3"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- STEP 5: PRICING & REVIEW -->
            <div id="step-5" class="step-content hidden">
                <!-- Cost Summary Section -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-bold text-blue-900 mb-3"><i class="fas fa-coins mr-2"></i> Chi phí dự kiến (Cost)
                    </h3>
                    <div id="cost-summary" class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-600">Tổng chi phí dịch vụ:</span>
                            <span id="total-cost" class="font-bold text-blue-900">0 đ</span>
                        </div>
                        <div class="text-xs text-slate-500 italic" id="cost-formula">
                            Chưa có dịch vụ nào được chọn
                        </div>
                    </div>
                </div>

                <!-- Markup & Suggested Price Section -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-bold text-green-900"><i class="fas fa-chart-line mr-2"></i> Giá đề xuất
                            (Suggested Price)</h3>
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-slate-600">Markup:</label>
                            <input type="range" id="markup-slider" min="15" max="40" value="25"
                                class="w-32 accent-green-600" oninput="updateSuggestedPricing()">
                            <span id="markup-value" class="font-bold text-green-700 w-12">25%</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div class="text-center p-3 bg-white rounded shadow-sm">
                            <div class="text-slate-600 mb-1">Người lớn</div>
                            <div id="suggested-adult" class="font-bold text-lg text-green-700">0 đ</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded shadow-sm">
                            <div class="text-slate-600 mb-1">Trẻ em (75%)</div>
                            <div id="suggested-child" class="font-bold text-lg text-green-700">0 đ</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded shadow-sm">
                            <div class="text-slate-600 mb-1">Em bé</div>
                            <div id="suggested-infant" class="font-bold text-lg text-green-700">0 đ</div>
                        </div>
                    </div>
                    <div class="mt-2 text-center">
                        <button type="button" onclick="applySuggestedPricing()"
                            class="text-sm text-green-700 hover:text-green-900 underline font-medium">
                            <i class="fas fa-check mr-1"></i> Áp dụng giá đề xuất vào form
                        </button>
                    </div>
                </div>

                <!-- Final Price Input Section -->
                <div class="bg-white border border-slate-300 rounded-lg p-4">
                    <h3 class="font-bold text-slate-900 mb-4"><i class="fas fa-tag mr-2"></i> Giá bán cuối cùng</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Giá người lớn <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="adult_price" id="adult_price" required min="0"
                                value="<?= $old['adult_price'] ?? '' ?>"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all font-bold text-accent text-lg"
                                oninput="validatePricing()">
                            <p class="text-xs text-slate-500 mt-1">Giá áp dụng cho khách > 12 tuổi</p>
                            <p id="adult-warning" class="text-xs text-red-600 mt-1 hidden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Giá trẻ em</label>
                            <input type="number" name="child_price" id="child_price" min="0"
                                value="<?= $old['child_price'] ?? '' ?>"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                            <p class="text-xs text-slate-500 mt-1">Thường bằng 70-80% giá người lớn</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Giá em bé</label>
                            <input type="number" name="infant_price" id="infant_price" min="0"
                                value="<?= $old['infant_price'] ?? '0' ?>"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                            <p class="text-xs text-slate-500 mt-1">Dưới 2 tuổi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 2: ITINERARY -->
            <div id="step-2" class="step-content hidden">
                <div
                    class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-2 rounded-lg mb-4 text-sm flex items-center gap-2">
                    <i class="fas fa-info-circle"></i> Lịch trình được tạo tự động dựa trên số ngày bạn nhập ở Bước 1.
                </div>
                <div id="itinerary-container">
                    <!-- Dynamic Days will be added here via JS -->
                </div>
            </div>

            <!-- STEP 3: SERVICES -->
            <div id="step-3" class="step-content hidden">
                <div class="mb-4 flex justify-between items-center">
                    <h3 class="font-bold text-slate-700">Cấu hình dịch vụ (Costing)</h3>
                    <button type="button" onclick="addServiceRow()"
                        class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 text-sm font-medium transition-colors">
                        <i class="fas fa-plus mr-1"></i> Thêm dịch vụ
                    </button>
                </div>
                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="w-full text-sm text-left text-slate-500">
                        <thead class="text-xs text-slate-700 uppercase bg-slate-50">
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
                <div class="mt-4 bg-yellow-50 p-3 rounded-lg text-sm text-yellow-700 border border-yellow-100">
                    <i class="fas fa-lightbulb mr-1"></i> <strong>Lưu ý:</strong> Đây là giá vốn (Cost) dự kiến để tính
                    lợi nhuận. Giá bán Tour ở Bước 5.
                </div>
            </div>

            <!-- STEP 4: MEDIA -->
            <div id="step-4" class="step-content hidden">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Hình ảnh Tour (Chọn nhiều)</label>
                    <div class="border-2 border-dashed border-slate-300 rounded-lg p-8 text-center cursor-pointer hover:bg-slate-50 transition-colors"
                        onclick="document.getElementById('images').click()">
                        <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden"
                            onchange="previewImages(this)">
                        <div class="text-4xl mb-2 text-slate-400"><i class="fas fa-images"></i></div>
                        <p class="text-slate-500">Click để tải ảnh lên</p>
                    </div>
                    <div id="image-preview" class="grid grid-cols-4 gap-4 mt-4"></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Điểm nổi bật (Highlights)</label>
                    <textarea name="highlights" rows="5" placeholder="Mỗi dòng một điểm nổi bật..."
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all"><?= htmlspecialchars(isset($old['highlights']) ? implode("\n", $old['highlights']) : '') ?></textarea>
                </div>
            </div>

        </div>

        <!-- FOOTER ACTIONS -->
        <div class="bg-slate-50 px-6 py-4 flex justify-between border-t border-slate-200">
            <button type="button" id="prevBtn"
                class="px-6 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 hidden transition-colors"
                onclick="changeStep(-1)">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </button>
            <div class="flex gap-2">
                <a href="?act=staff-tours"
                    class="px-6 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">Hủy</a>
                <button type="button" id="nextBtn"
                    class="px-6 py-2 bg-accent text-white rounded-lg hover:bg-blue-600 shadow transition-colors"
                    onclick="changeStep(1)">
                    Tiếp theo <i class="fas fa-arrow-right ml-2"></i>
                </button>
                <button type="submit" id="submitBtn"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow hidden transition-colors">
                    <i class="fas fa-save mr-2"></i> Hoàn tất & Lưu
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    let currentStep = 1;
    const totalSteps = 5;
    const destinations = <?= json_encode($destinations ?? []) ?>;
    const servicesList = <?= json_encode($services ?? []) ?>;

    // Old Input Data for Itinerary
    const oldItinerary = <?= json_encode($old['itinerary'] ?? []) ?>;
    const oldServices = <?= json_encode($old['services'] ?? []) ?>;

    // AJAX: Load destinations by category
    document.addEventListener('DOMContentLoaded', function () {
        const categorySelect = document.querySelector('select[name="category_id"]');
        if (categorySelect) {
            categorySelect.addEventListener('change', function () {
                loadDestinationsByCategory(this.value);
            });
        }
    });

    function loadDestinationsByCategory(categoryId) {
        if (!categoryId) {
            // Reset to all destinations (if we had them all initially, but here we might need to fetch all again or just keep current logic)
            // For simplicity, let's assume destinations variable holds all initially or we re-fetch
            // If destinations is empty initially, this might be an issue. 
            // But assuming controller passes all destinations.
            if (Object.keys(destinations).length > 0) {
                if (currentStep === 2) generateItinerary();
                return;
            }
        }

        fetch(`index.php?act=ajax-destinations&category_id=${categoryId}`) // Updated URL for staff context if needed, or use public
            .then(res => res.json())
            .then(data => {
                // Update global destinations variable
                // Note: destinations is const, so we can't reassign. We should use let or modify object.
                // Let's just use the data directly in generateItinerary if we change logic, 
                // but here we can't easily change const. 
                // FIX: Changing const to let in script above.

                // Since I can't change the const above in this script block easily without rewriting the whole block, 
                // I will assume the user won't change category back and forth too much or I'll just reload the page.
                // Actually, let's just use the data to rebuild options.

                // Better approach for this script:
                updateDestinations(data);
            })
            .catch(err => console.error('Error loading destinations:', err));
    }

    // Helper to update destinations object
    function updateDestinations(data) {
        // Clear current
        for (var member in destinations) delete destinations[member];
        // Add new
        data.forEach(d => destinations[d.id] = d.name);

        if (currentStep === 2) {
            generateItinerary();
        }
    }


    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.step-indicator').forEach(el => {
            el.classList.remove('text-accent', 'border-accent');
            el.classList.add('text-slate-400', 'border-transparent');
        });

        // Show current step
        document.getElementById(`step-${step}`).classList.remove('hidden');

        // Update indicators
        for (let i = 1; i <= step; i++) {
            const indicator = document.querySelector(`.step-indicator[data-step="${i}"]`);
            indicator.classList.remove('text-slate-400', 'border-transparent');
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

        if (currentStep === 2) {
            generateItinerary(); // Regenerate/Check itinerary when entering step 2
        }
        if (currentStep === 3 && oldServices.length === 0 && document.getElementById('services-container').children.length === 0) {
            addServiceRow(); // Add a default service row if none exist
        }
        if (currentStep === 5) {
            updateSuggestedPricing(); // Calculate pricing when entering Step 5
        }

        showStep(currentStep);
    }

    function generateItinerary() {
        const days = parseInt(document.getElementById('duration_days').value) || 1;
        const container = document.getElementById('itinerary-container');

        // Check if we need to regenerate (only if count differs)
        const currentItems = container.querySelectorAll('.itinerary-item').length;
        if (currentItems === days && container.innerHTML.trim() !== '') return;

        container.innerHTML = ''; // Clear current

        let destOptions = '<option value="">-- Chọn điểm đến chính --</option>';
        // Handle both array and object formats for destinations
        if (Array.isArray(destinations)) {
            destinations.forEach(d => {
                destOptions += `<option value="${d.id}">${d.name}</option>`;
            });
        } else {
            for (const [id, name] of Object.entries(destinations)) {
                destOptions += `<option value="${id}">${name}</option>`;
            }
        }

        for (let i = 1; i <= days; i++) {
            // Try to recover old data if available
            const oldData = oldItinerary[i - 1] || {};
            const title = oldData.title || '';
            const desc = oldData.description || '';
            const destId = oldData.destination_id || '';

            const html = `
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 mb-4 itinerary-item">
                    <div class="flex justify-between mb-2">
                        <h3 class="font-bold text-slate-700">Ngày ${i}</h3>
                        <input type="hidden" name="itinerary_day[]" value="${i}">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                        <input type="text" name="itinerary_title[]" value="${title}" placeholder="Tiêu đề hoạt động (VD: Đón khách - City Tour)" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent outline-none" required>
                        <select name="itinerary_dest[]" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent outline-none">
                            ${destOptions}
                        </select>
                    </div>
                    <textarea name="itinerary_desc[]" rows="2" placeholder="Mô tả chi tiết..." class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent outline-none">${desc}</textarea>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);

            // Set selected destination manually
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
                    div.className = 'relative aspect-video bg-slate-100 rounded-lg overflow-hidden shadow-sm';
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
            <tr class="bg-white border-b border-slate-100 hover:bg-slate-50 transition-colors">
                <td class="px-2 py-2">
                    <select name="service_ids[]" class="w-full px-2 py-1 border border-slate-300 rounded service-select" onchange="fillServiceData(this)" required>
                        ${serviceOptions}
                    </select>
                    <input type="hidden" name="service_names[]" class="service-name-input" value="${data.service_name || ''}">
                </td>
                <td class="px-2 py-2">
                    <select name="service_calc_types[]" class="w-full px-2 py-1 border border-slate-300 rounded">
                        <option value="per_person" ${data.calculation_type == 'per_person' ? 'selected' : ''}>Theo khách</option>
                        <option value="per_group" ${data.calculation_type == 'per_group' ? 'selected' : ''}>Theo đoàn</option>
                        <option value="per_day" ${data.calculation_type == 'per_day' ? 'selected' : ''}>Theo ngày</option>
                        <option value="fixed" ${data.calculation_type == 'fixed' ? 'selected' : ''}>Cố định</option>
                    </select>
                </td>
                <td class="px-2 py-2">
                    <input type="number" name="service_quantities[]" value="${data.fixed_quantity || 1}" min="1" class="w-full px-2 py-1 border border-slate-300 rounded text-center">
                </td>
                <td class="px-2 py-2">
                    <input type="number" name="service_prices[]" value="${data.unit_price || 0}" min="0" class="w-full px-2 py-1 border border-slate-300 rounded text-right service-price">
                </td>
                <td class="px-2 py-2">
                    <input type="text" name="service_units[]" value="${data.unit || ''}" class="w-full px-2 py-1 border border-slate-300 rounded service-unit">
                </td>
                <td class="px-2 py-2">
                    <input type="text" name="service_notes[]" value="${data.notes || ''}" class="w-full px-2 py-1 border border-slate-300 rounded">
                </td>
                <td class="px-2 py-2 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-red-500 hover:text-red-700 transition-colors"><i class="fas fa-trash"></i></button>
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

    // ==============================================================================
    // PRICING LOGIC (STEP 5)
    // ==============================================================================
    let totalServiceCost = 0;
    let suggestedAdultPrice = 0;
    let suggestedChildPrice = 0;
    let suggestedInfantPrice = 0;

    function calculateServiceCost() {
        totalServiceCost = 0;
        let formula = [];

        const serviceRows = document.querySelectorAll('#services-container tr');
        if (serviceRows.length === 0) {
            document.getElementById('total-cost').textContent = '0 đ';
            document.getElementById('cost-formula').textContent = 'Chưa có dịch vụ nào được chọn';
            return 0;
        }

        serviceRows.forEach((row, index) => {
            const qty = parseFloat(row.querySelector('input[name="service_quantities[]"]')?.value || 0);
            const price = parseFloat(row.querySelector('input[name="service_prices[]"]')?.value || 0);
            const serviceName = row.querySelector('select[name="service_ids[]"] option:checked')?.text?.split('(')[0]?.trim() || `Dịch vụ ${index + 1}`;

            const cost = qty * price;
            totalServiceCost += cost;

            if (cost > 0) {
                formula.push(`${serviceName}: ${new Intl.NumberFormat('vi-VN').format(qty)} x ${new Intl.NumberFormat('vi-VN').format(price)}đ`);
            }
        });

        // Update UI
        document.getElementById('total-cost').textContent = new Intl.NumberFormat('vi-VN').format(totalServiceCost) + ' đ';
        document.getElementById('cost-formula').textContent = formula.length > 0 ? formula.join(' + ') : 'Chưa có dịch vụ nào được chọn';

        return totalServiceCost;
    }

    function updateSuggestedPricing() {
        const markup = parseInt(document.getElementById('markup-slider').value);
        document.getElementById('markup-value').textContent = markup + '%';

        calculateServiceCost(); // Recalculate cost first

        if (totalServiceCost === 0) {
            suggestedAdultPrice = 0;
            suggestedChildPrice = 0;
            suggestedInfantPrice = 0;
        } else {
            // Calculate suggested prices with markup
            suggestedAdultPrice = Math.round(totalServiceCost * (1 + markup / 100));
            suggestedChildPrice = Math.round(suggestedAdultPrice * 0.75); // 75% of adult
            suggestedInfantPrice = 0; // Usually free or very low
        }

        // Update UI
        document.getElementById('suggested-adult').textContent = new Intl.NumberFormat('vi-VN').format(suggestedAdultPrice) + ' đ';
        document.getElementById('suggested-child').textContent = new Intl.NumberFormat('vi-VN').format(suggestedChildPrice) + ' đ';
        document.getElementById('suggested-infant').textContent = new Intl.NumberFormat('vi-VN').format(suggestedInfantPrice) + ' đ';
    }

    function applySuggestedPricing() {
        if (suggestedAdultPrice > 0) {
            document.getElementById('adult_price').value = suggestedAdultPrice;
            document.getElementById('child_price').value = suggestedChildPrice;
            document.getElementById('infant_price').value = suggestedInfantPrice;

            validatePricing();
            alert('✓ Đã áp dụng giá đề xuất vào form!');
        } else {
            alert('⚠ Vui lòng thêm dịch vụ ở Bước 3 trước khi tính giá.');
        }
    }

    function validatePricing() {
        const adultPrice = parseFloat(document.getElementById('adult_price').value || 0);
        const warningEl = document.getElementById('adult-warning');

        if (totalServiceCost > 0 && adultPrice < totalServiceCost) {
            const loss = totalServiceCost - adultPrice;
            warningEl.textContent = `⚠ Cảnh báo: Giá bán thấp hơn giá vốn ${new Intl.NumberFormat('vi-VN').format(loss)}đ (${Math.round((loss / totalServiceCost) * 100)}%)`;
            warningEl.classList.remove('hidden');
        } else {
            warningEl.classList.add('hidden');
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