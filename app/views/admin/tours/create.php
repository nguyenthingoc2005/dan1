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

        <!-- WIZARD STEPS (6 steps) -->
        <div class="flex border-b bg-gray-50 overflow-x-auto">
            <div class="step-indicator px-4 py-3 text-sm font-medium text-accent border-b-2 border-accent flex-1 text-center whitespace-nowrap"
                data-step="1">
                1. Thông tin chung
            </div>
            <div class="step-indicator px-4 py-3 text-sm font-medium text-gray-400 border-b-2 border-transparent flex-1 text-center whitespace-nowrap"
                data-step="2">
                2. Lịch trình
            </div>
            <div class="step-indicator px-4 py-3 text-sm font-medium text-gray-400 border-b-2 border-transparent flex-1 text-center whitespace-nowrap"
                data-step="3">
                3. Dịch vụ
            </div>
            <div class="step-indicator px-4 py-3 text-sm font-medium text-gray-400 border-b-2 border-transparent flex-1 text-center whitespace-nowrap"
                data-step="4">
                4. Bao gồm
            </div>
            <div class="step-indicator px-4 py-3 text-sm font-medium text-gray-400 border-b-2 border-transparent flex-1 text-center whitespace-nowrap"
                data-step="5">
                5. Hình ảnh
            </div>
            <div class="step-indicator px-4 py-3 text-sm font-medium text-gray-400 border-b-2 border-transparent flex-1 text-center whitespace-nowrap"
                data-step="6">
                6. Giá & Lưu
            </div>
        </div>

        <!-- STEPS CONTENT -->
        <div class="p-6">

            <!-- STEP 1: INFO -->
            <div id="step-1" class="step-content block">
                <!-- Hidden fields -->
                <input type="hidden" name="code" value="AUTO">
                <input type="hidden" name="tour_type" value="public">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Tên Tour -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tên Tour <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required
                            class="w-full px-3 py-2 border rounded focus:border-accent <?= isset($errs['name']) ? 'border-red-500' : '' ?>">
                    </div>

                    <!-- Danh mục -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                        <select name="category_id" class="w-full px-3 py-2 border rounded focus:border-accent">
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $id => $name): ?>
                                <option value="<?= $id ?>" <?= ($old['category_id'] ?? '') == $id ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Điểm khởi hành -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Điểm khởi hành</label>
                        <input type="text" name="departure_location" value="<?= htmlspecialchars($old['departure_location'] ?? '') ?>"
                            class="w-full px-3 py-2 border rounded focus:border-accent" placeholder="VD: TP. Hồ Chí Minh">
                    </div>

                    <!-- Số ngày / đêm -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số ngày <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_days" id="duration_days" min="1" value="<?= $old['duration_days'] ?? '3' ?>" required
                            class="w-full px-3 py-2 border rounded focus:border-accent font-bold text-accent" onchange="generateItinerary()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số đêm</label>
                        <input type="number" name="duration_nights" min="0" value="<?= $old['duration_nights'] ?? '2' ?>"
                            class="w-full px-3 py-2 border rounded focus:border-accent">
                    </div>
                </div>

                <!-- Số lượng khách & Đặt cọc -->
                <div class="mt-5 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h4 class="font-medium text-blue-800 mb-3">📊 Số lượng khách & Đặt cọc</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số khách tối thiểu</label>
                            <input type="number" name="min_participants" min="1" value="<?= $old['min_participants'] ?? '10' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent text-center">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số khách tối đa</label>
                            <input type="number" name="max_participants" min="1" value="<?= $old['max_participants'] ?? '45' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent text-center">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giá tính cho</label>
                            <div class="flex items-center">
                                <input type="number" name="price_based_on_pax" min="1" value="<?= $old['price_based_on_pax'] ?? '30' ?>"
                                    class="w-full px-3 py-2 border rounded-l focus:border-accent text-center">
                                <span class="px-3 py-2 bg-gray-100 border border-l-0 rounded-r text-sm text-gray-600">khách</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tỷ lệ đặt cọc</label>
                            <div class="flex items-center">
                                <input type="number" name="deposit_percentage" min="0" max="100" value="<?= $old['deposit_percentage'] ?? '30' ?>"
                                    class="w-full px-3 py-2 border rounded-l focus:border-accent text-center">
                                <span class="px-3 py-2 bg-gray-100 border border-l-0 rounded-r text-sm text-gray-600">%</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-blue-600 mt-2">💡 Giá tour sẽ được tính dựa trên số khách cơ sở. Tỷ lệ đặt cọc áp dụng khi tạo booking.</p>
                </div>

                <!-- Mô tả -->
                <div class="mt-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả ngắn</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded focus:border-accent"
                        placeholder="Giới thiệu ngắn về tour..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                </div>

                <!-- Trạng thái -->
                <div class="mt-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" class="w-full md:w-1/2 px-3 py-2 border rounded focus:border-accent">
                        <option value="draft" <?= ($old['status'] ?? 'draft') == 'draft' ? 'selected' : '' ?>>Bản nháp (Draft)</option>
                        <option value="pending" <?= ($old['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Chờ duyệt (Pending)</option>
                    </select>
                </div>
            </div>

            <!-- STEP 6: PRICING & REVIEW -->
            <div id="step-6" class="step-content hidden">
                <!-- Cost Summary Section -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-bold text-blue-900 mb-3">💰 Chi phí dự kiến (Cost)</h3>
                    <div id="cost-summary" class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tổng chi phí dịch vụ:</span>
                            <span id="total-cost" class="font-bold text-blue-900">0 đ</span>
                        </div>
                        <div class="text-xs text-gray-500 italic" id="cost-formula">
                            Chưa có dịch vụ nào được chọn
                        </div>
                    </div>
                </div>

                <!-- Markup & Suggested Price Section -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-bold text-green-900">📊 Giá đề xuất (Suggested Price)</h3>
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-600">Markup:</label>
                            <input type="range" id="markup-slider" min="15" max="40" value="25" class="w-32"
                                oninput="updateSuggestedPricing()">
                            <span id="markup-value" class="font-bold text-green-700 w-12">25%</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div class="text-center p-3 bg-white rounded">
                            <div class="text-gray-600 mb-1">Người lớn</div>
                            <div id="suggested-adult" class="font-bold text-lg text-green-700">0 đ</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded">
                            <div class="text-gray-600 mb-1">Trẻ em (75%)</div>
                            <div id="suggested-child" class="font-bold text-lg text-green-700">0 đ</div>
                        </div>
                        <div class="text-center p-3 bg-white rounded">
                            <div class="text-gray-600 mb-1">Em bé</div>
                            <div id="suggested-infant" class="font-bold text-lg text-green-700">0 đ</div>
                        </div>
                    </div>
                    <div class="mt-2 text-center">
                        <button type="button" onclick="applySuggestedPricing()"
                            class="text-sm text-green-700 hover:text-green-900 underline">
                            ✓ Áp dụng giá đề xuất vào form
                        </button>
                    </div>
                </div>

                <!-- Final Price Input Section -->
                <div class="bg-white border border-gray-300 rounded-lg p-4">
                    <h3 class="font-bold text-gray-900 mb-4">🎯 Giá bán cuối cùng</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giá người lớn <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="adult_price" id="adult_price" required min="0"
                                value="<?= $old['adult_price'] ?? '' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent font-bold text-accent text-lg"
                                oninput="validatePricing()">
                            <p class="text-xs text-gray-500 mt-1">Giá áp dụng cho khách > 12 tuổi</p>
                            <p id="adult-warning" class="text-xs text-red-600 mt-1 hidden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giá trẻ em</label>
                            <input type="number" name="child_price" id="child_price" min="0"
                                value="<?= $old['child_price'] ?? '' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent">
                            <p class="text-xs text-gray-500 mt-1">Thường bằng 70-80% giá người lớn</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giá em bé</label>
                            <input type="number" name="infant_price" id="infant_price" min="0"
                                value="<?= $old['infant_price'] ?? '0' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent">
                            <p class="text-xs text-gray-500 mt-1">Dưới 2 tuổi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 3: ITINERARY -->
            <div id="step-2" class="step-content hidden">
                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-2 rounded mb-4 text-sm">
                    ℹ️ Lịch trình được tạo tự động dựa trên số ngày bạn nhập ở Bước 1.
                </div>
                <div id="itinerary-container">
                    <!-- Dynamic Days will be added here via JS -->
                </div>
            </div>

            <!-- STEP 4: SERVICES -->
            <div id="step-3" class="step-content hidden">
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
                    💡 <strong>Lưu ý:</strong> Đây là giá vốn (Cost) dự kiến để tính lợi nhuận. Giá bán Tour ở Bước 2.
                </div>
            </div>

            <!-- STEP 4: INCLUDED/EXCLUDED -->
            <div id="step-4" class="step-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- GIÁ TOUR BAO GỒM -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-bold text-green-800">✅ Giá tour BAO GỒM</h3>
                            <button type="button" onclick="addIncludedItem()" class="text-sm text-green-600 hover:underline">+ Thêm</button>
                        </div>
                        <div id="included-container" class="space-y-2">
                            <!-- Default items -->
                        </div>
                        <p class="text-xs text-green-600 mt-3">💡 Các dịch vụ/tiện ích đã tính trong giá tour</p>
                    </div>

                    <!-- GIÁ TOUR KHÔNG BAO GỒM -->
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-bold text-red-800">❌ Giá tour KHÔNG BAO GỒM</h3>
                            <button type="button" onclick="addExcludedItem()" class="text-sm text-red-600 hover:underline">+ Thêm</button>
                        </div>
                        <div id="excluded-container" class="space-y-2">
                            <!-- Default items -->
                        </div>
                        <p class="text-xs text-red-600 mt-3">💡 Chi phí khách tự chi trả thêm</p>
                    </div>
                </div>

                <!-- Quick Templates -->
                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600 mr-2">Mẫu nhanh:</span>
                    <button type="button" onclick="applyTemplate('domestic')" class="text-sm text-accent hover:underline mr-3">Tour trong nước</button>
                    <button type="button" onclick="applyTemplate('international')" class="text-sm text-accent hover:underline">Tour quốc tế</button>
                </div>
            </div>

            <!-- STEP 5: MEDIA & HIGHLIGHTS -->
            <div id="step-5" class="step-content hidden">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh Tour (Chọn nhiều)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:bg-gray-50"
                        onclick="document.getElementById('images').click()">
                        <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden"
                            onchange="previewImages(this)">
                        <div class="text-4xl mb-2">📷</div>
                        <p class="text-gray-500">Click để tải ảnh lên (tối đa 10 ảnh)</p>
                    </div>
                    <div id="image-preview" class="grid grid-cols-4 gap-4 mt-4"></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Điểm nổi bật (Highlights)</label>
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
                    class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600 shadow" onclick="changeStep(1)">
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
    const totalSteps = 6; // Updated to 6 steps
    let currentDestinations = <?= json_encode($destinations) ?>; // Mutable
    const allDestinations = <?= json_encode($destinations) ?>; // Original
    const servicesList = <?= json_encode($services ?? []) ?>;
    const serviceUnits = <?= json_encode(get_service_units()) ?>;

    // Old Input Data
    const oldItinerary = <?= json_encode($old['itinerary'] ?? []) ?>;
    const oldServices = <?= json_encode($old['services'] ?? []) ?>;
    const oldIncluded = <?= json_encode($old['included'] ?? []) ?>;
    const oldExcluded = <?= json_encode($old['excluded'] ?? []) ?>;

    // Templates for Included/Excluded
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

    // Init: Load destinations by category
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
            // Reset to all destinations
            currentDestinations = {...allDestinations};
            updateDestinationDropdowns();
            return;
        }

        // Call AJAX API
        fetch(`?act=admin&module=tours&action=getDestinations&category_id=${categoryId}`)
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    // Convert array to object {id: name}
                    currentDestinations = {};
                    response.data.forEach(d => {
                        currentDestinations[d.id] = d.name;
                    });
                    updateDestinationDropdowns();
                }
            })
            .catch(err => console.error('Error loading destinations:', err));
    }

    function updateDestinationDropdowns() {
        // Update all destination dropdowns in itinerary
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

        if (currentStep === 2) {
            generateItinerary(); // Regenerate/Check itinerary when entering step 2
        }
        if (currentStep === 3 && oldServices.length === 0 && document.getElementById('services-container').children.length === 0) {
            addServiceRow(); // Add a default service row if none exist
        }
        if (currentStep === 4) {
            initIncludedExcluded(); // Init included/excluded items
        }
        if (currentStep === 6) {
            updateSuggestedPricing(); // Calculate pricing when entering Step 6
        }

        showStep(currentStep);
    }

    // ==============================================================================
    // INCLUDED / EXCLUDED LOGIC
    // ==============================================================================
    let includedInitialized = false;

    function initIncludedExcluded() {
        if (includedInitialized) return;
        includedInitialized = true;

        // Load old data or default template
        if (oldIncluded.length > 0) {
            oldIncluded.forEach(item => addIncludedItem(item));
        }
        if (oldExcluded.length > 0) {
            oldExcluded.forEach(item => addExcludedItem(item));
        }

        // If empty, add some default rows
        if (oldIncluded.length === 0 && oldExcluded.length === 0) {
            applyTemplate('domestic');
        }
    }

    function addIncludedItem(text = '') {
        const container = document.getElementById('included-container');
        const html = `
            <div class="flex items-center gap-2 included-item">
                <span class="text-green-600">✓</span>
                <input type="text" name="included[]" value="${text}" placeholder="Nhập nội dung bao gồm..."
                    class="flex-1 px-3 py-2 border rounded focus:border-green-500 focus:outline-none text-sm">
                <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600">✕</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function addExcludedItem(text = '') {
        const container = document.getElementById('excluded-container');
        const html = `
            <div class="flex items-center gap-2 excluded-item">
                <span class="text-red-600">✗</span>
                <input type="text" name="excluded[]" value="${text}" placeholder="Nhập nội dung không bao gồm..."
                    class="flex-1 px-3 py-2 border rounded focus:border-red-500 focus:outline-none text-sm">
                <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600">✕</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function applyTemplate(type) {
        const template = templates[type];
        if (!template) return;

        // Clear existing
        document.getElementById('included-container').innerHTML = '';
        document.getElementById('excluded-container').innerHTML = '';

        // Add items from template
        template.included.forEach(item => addIncludedItem(item));
        template.excluded.forEach(item => addExcludedItem(item));
    }

    function generateItinerary() {
        const days = parseInt(document.getElementById('duration_days').value) || 1;
        const container = document.getElementById('itinerary-container');

        // Check if we need to regenerate (only if count differs)
        const currentItems = container.querySelectorAll('.itinerary-item').length;
        if (currentItems === days && container.innerHTML.trim() !== '') return;

        container.innerHTML = ''; // Clear current

        let destOptions = '<option value="">-- Chọn điểm đến chính --</option>';
        for (const [id, name] of Object.entries(currentDestinations)) {
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
                    div.className = 'relative aspect-video bg-gray-100 rounded overflow-hidden shadow-sm';
                    div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    container.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        }
    }

    // SERVICES LOGIC
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

        // Build service options with data attributes
        let serviceOptions = '<option value="">-- Chọn dịch vụ --</option>';
        servicesList.forEach(s => {
            const selected = (data.service_id == s.id) ? 'selected' : '';
            const price = s.unit_price || 0;
            const unit = s.unit || '';
            const name = s.name || '';
            serviceOptions += `<option value="${s.id}" data-price="${price}" data-unit="${unit}" data-name="${name}" ${selected}>${name} (${new Intl.NumberFormat('vi-VN').format(price)}đ)</option>`;
        });

        // Default values
        const unitPrice = data.unit_price || 0;
        const unitVal = data.unit || '';
        const serviceName = data.service_name || '';

        const html = `
            <tr class="bg-white border-b hover:bg-gray-50 service-row">
                <td class="px-2 py-2">
                    <select name="service_ids[]" class="w-full px-2 py-1 border rounded service-select" onchange="fillServiceData(this)">
                        ${serviceOptions}
                    </select>
                    <input type="hidden" name="service_names[]" class="service-name-input" value="${serviceName}">
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
                    <input type="number" name="service_prices[]" value="${unitPrice}" min="0" class="w-full px-2 py-1 border rounded text-right service-price">
                </td>
                <td class="px-2 py-2">
                    <select name="service_units[]" class="w-full px-2 py-1 border rounded service-unit">
                        ${buildUnitOptions(unitVal)}
                    </select>
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

        if (option && option.value) {
            const price = option.getAttribute('data-price') || 0;
            const unit = option.getAttribute('data-unit') || '';
            const name = option.getAttribute('data-name') || '';

            row.querySelector('.service-price').value = price;
            row.querySelector('.service-name-input').value = name;
            
            // Set unit dropdown
            const unitSelect = row.querySelector('.service-unit');
            if (unit && unitSelect) {
                unitSelect.value = unit;
                // If unit not in list, add it
                if (!unitSelect.value && unit) {
                    unitSelect.innerHTML += `<option value="${unit}" selected>${unit}</option>`;
                }
            }
        } else {
            row.querySelector('.service-price').value = 0;
            row.querySelector('.service-name-input').value = '';
            row.querySelector('.service-unit').value = '';
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