<?php
/**
 * ==============================================================================
 * ADMIN - FORM TẠO TOUR (WIZARD UI - 6 STEPS) - HOÀN TOÀN MỚI
 * ==============================================================================
 * 
 * Variables:
 * - $destinations (required): Array destinations
 * - $services (required): Array services
 * - $service_providers (required): Array service_providers
 * - $policies (required): Array policies
 * - $old_input (optional): Old form data for errors
 * - $errors (optional): Validation errors
 * - $is_from_template (optional): Boolean - đang clone từ template
 * - $template_info (optional): Array - thông tin template
 * 
 * @version 2.0
 * @date 2024-12-06
 * ==============================================================================
 */

if (!is_admin())
    redirect('?act=access-denied');

$old = $old_input ?? [];
$errs = $errors ?? [];
$is_from_template = $is_from_template ?? false;
$template_info = $template_info ?? null;

// Prepare data for components
$destinations = is_array($destinations) ? $destinations : [];
$services = is_array($services) ? $services : [];
$service_providers = is_array($service_providers) ? $service_providers : [];
$policies = is_array($policies) ? $policies : [];

// Prepare old data for itinerary
$old_itinerary = $old['itinerary'] ?? [];
$old_timelines = $old['itinerary_timelines'] ?? [];
$old_day_services = $old['itinerary_day_services'] ?? [];

// Handle highlights - can be array or string (newline separated)
$highlights_value = $old['highlights'] ?? null;
if (is_array($highlights_value)) {
    $old_highlights = $highlights_value;
} elseif (is_string($highlights_value) && !empty($highlights_value)) {
    $old_highlights = explode("\n", $highlights_value);
} else {
    $old_highlights = [];
}

$old_includes = $old['includes'] ?? ($old['included'] ?? []);
$old_excludes = $old['excludes'] ?? ($old['excluded'] ?? []);
$selected_policy_ids = $old['policy_ids'] ?? [];

// Group timelines and day services by day_number
$timelines_by_day = [];
if (is_array($old_timelines)) {
    foreach ($old_timelines as $timeline) {
        if (is_array($timeline)) {
            $day = $timeline['day_number'] ?? 1;
            if (!isset($timelines_by_day[$day])) {
                $timelines_by_day[$day] = [];
            }
            $timelines_by_day[$day][] = $timeline;
        }
    }
}

$day_services_by_day = [];
if (is_array($old_day_services)) {
    foreach ($old_day_services as $service) {
        if (is_array($service)) {
            $day = $service['day_number'] ?? 1;
            if (!isset($day_services_by_day[$day])) {
                $day_services_by_day[$day] = [];
            }
            $day_services_by_day[$day][] = $service;
        }
    }
}
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">
                <?= $is_from_template ? 'Tạo Tour Custom từ Template' : 'Thêm Tour mới' ?>
            </h1>
            <?php if (!empty($template_info) && is_array($template_info)): ?>
                <p class="text-sm text-gray-600 mt-1">
                    Template: <?= htmlspecialchars($template_info['name'] ?? 'N/A') ?>
                    (<?= htmlspecialchars($template_info['code'] ?? 'N/A') ?>)
                </p>
            <?php endif; ?>
        </div>
        <a href="?act=admin&module=tours" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách
        </a>
    </div>

    <!-- ERROR ALERT -->
    <?php if (!empty($errs)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
            <strong class="font-bold">Có lỗi xảy ra!</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                <?php foreach ($errs as $key => $msg): ?>
                    <li><?= htmlspecialchars(is_array($msg) ? implode(', ', $msg) : $msg) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form id="tourForm" method="POST" action="?act=admin&module=tours&action=store" enctype="multipart/form-data"
        class="bg-white rounded-lg shadow-sm overflow-hidden">

        <!-- Hidden fields -->
        <input type="hidden" name="tour_type" value="<?= htmlspecialchars($old['tour_type'] ?? 'public') ?>">
        <?php if (!empty($old['parent_tour_id'])): ?>
            <input type="hidden" name="parent_tour_id" value="<?= (int) $old['parent_tour_id'] ?>">
        <?php endif; ?>

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
                3. Bao gồm
            </div>
            <div class="step-indicator px-4 py-3 text-sm font-medium text-gray-400 border-b-2 border-transparent flex-1 text-center whitespace-nowrap"
                data-step="4">
                4. Chính sách
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

            <!-- ============================================================
                 STEP 1: THÔNG TIN CHUNG
                 ============================================================ -->
            <div id="step-1" class="step-content block">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Thông tin chung</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Tên Tour -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tên Tour <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                            required
                            class="w-full px-3 py-2 border rounded focus:border-accent <?= isset($errs['name']) ? 'border-red-500' : '' ?>"
                            placeholder="VD: Tour Đà Lạt 3 ngày 2 đêm">
                        <?php if (isset($errs['name'])): ?>
                            <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errs['name']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Điểm khởi hành -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Điểm khởi hành</label>
                        <input type="text" name="departure_location"
                            value="<?= htmlspecialchars($old['departure_location'] ?? '') ?>"
                            class="w-full px-3 py-2 border rounded focus:border-accent"
                            placeholder="VD: TP. Hồ Chí Minh">
                    </div>

                    <!-- Số ngày -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Số ngày <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="duration_days" id="duration_days" min="1"
                            value="<?= $old['duration_days'] ?? '3' ?>" required
                            class="w-full px-3 py-2 border rounded focus:border-accent font-bold text-accent"
                            onchange="generateItineraryDays()">
                    </div>

                    <!-- Số đêm -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số đêm</label>
                        <input type="number" name="duration_nights" id="duration_nights" min="0"
                            value="<?= $old['duration_nights'] ?? '2' ?>"
                            class="w-full px-3 py-2 border rounded focus:border-accent">
                    </div>
                </div>

                <!-- Giới thiệu ngắn -->
                <div class="mt-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giới thiệu ngắn</label>
                    <textarea name="introduction" rows="3" class="w-full px-3 py-2 border rounded focus:border-accent"
                        placeholder="Mô tả ngắn gọn về tour (sẽ hiển thị ở danh sách)..."><?= htmlspecialchars($old['introduction'] ?? '') ?></textarea>
                </div>

                <!-- Mô tả chi tiết -->
                <div class="mt-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả chi tiết</label>
                    <textarea name="description" id="description" rows="6"
                        class="w-full px-3 py-2 border rounded focus:border-accent"
                        placeholder="Mô tả chi tiết về tour..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                    <p class="text-xs text-gray-500 mt-1">Bạn có thể sử dụng HTML hoặc rich text editor</p>
                </div>

                <!-- Số người & Booking Deadline -->
                <div class="mt-5 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h4 class="font-medium text-blue-800 mb-3">📊 Thông tin số lượng khách</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số người tối thiểu</label>
                            <input type="number" name="min_participants" id="min_participants" min="1"
                                value="<?= $old['min_participants'] ?? '15' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent text-center"
                                onchange="updatePricing()">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số người tối đa</label>
                            <input type="number" name="max_participants" min="1"
                                value="<?= $old['max_participants'] ?? '45' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent text-center">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deadline đặt tour (ngày)</label>
                            <input type="number" name="booking_deadline_days" min="1"
                                value="<?= $old['booking_deadline_days'] ?? '1' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent text-center" placeholder="1">
                            <p class="text-xs text-gray-500 mt-1">Trước ngày khởi hành</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================
                 STEP 2: LỊCH TRÌNH (với 3 tabs)
                 ============================================================ -->
            <div id="step-2" class="step-content hidden">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Lịch trình</h2>

                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="flex -mb-px">
                        <button type="button" onclick="switchItineraryTab('overview')" id="tab-overview-btn"
                            class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600 mr-4">
                            <i class="fas fa-list mr-2"></i>Tổng quan từng ngày
                        </button>
                        <button type="button" onclick="switchItineraryTab('timeline')" id="tab-timeline-btn"
                            class="px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 mr-4">
                            <i class="fas fa-clock mr-2"></i>Timeline chi tiết
                        </button>
                        <button type="button" onclick="switchItineraryTab('services')" id="tab-services-btn"
                            class="px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300">
                            <i class="fas fa-concierge-bell mr-2"></i>Dịch vụ theo ngày
                        </button>
                    </nav>
                </div>

                <!-- Tab 1: Tổng quan từng ngày -->
                <div id="tab-overview" class="itinerary-tab">
                    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-2 rounded mb-4 text-sm">
                        ℹ️ Lịch trình được tạo tự động dựa trên số ngày bạn nhập ở Bước 1.
                    </div>
                    <div id="itinerary-overview-container" class="space-y-4">
                        <!-- Will be generated by JavaScript -->
                    </div>
                </div>

                <!-- Tab 2: Timeline chi tiết -->
                <div id="tab-timeline" class="itinerary-tab hidden">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Chọn ngày để quản lý
                            timeline</label>
                        <select id="timeline-day-selector" onchange="loadTimelineForDay(this.value)"
                            class="w-full md:w-64 px-3 py-2 border rounded focus:border-blue-500">
                            <option value="">-- Chọn ngày --</option>
                        </select>
                    </div>
                    <div id="timeline-editor-container">
                        <!-- Timeline Editor Component sẽ được load ở đây -->
                    </div>
                </div>

                <!-- Tab 3: Dịch vụ theo ngày -->
                <div id="tab-services" class="itinerary-tab hidden">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Chọn ngày để quản lý dịch vụ</label>
                        <select id="day-services-day-selector" onchange="loadDayServicesForDay(this.value)"
                            class="w-full md:w-64 px-3 py-2 border rounded focus:border-green-500">
                            <option value="">-- Chọn ngày --</option>
                        </select>
                    </div>
                    <div id="day-services-editor-container">
                        <!-- Day Services Editor Component sẽ được load ở đây -->
                    </div>
                </div>
            </div>

            <!-- ============================================================
                 STEP 3: BAO GỒM/KHÔNG BAO GỒM
                 ============================================================ -->
            <div id="step-3" class="step-content hidden">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Bao gồm / Không bao gồm</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Điểm nổi bật -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Điểm nổi bật (Highlights)
                        </label>
                        <textarea name="highlights" id="highlights" rows="5"
                            class="w-full px-3 py-2 border rounded focus:border-accent"
                            placeholder="Mỗi dòng một điểm nổi bật..."><?= htmlspecialchars(implode("\n", is_array($old_highlights) ? $old_highlights : [])) ?></textarea>
                        <p class="text-xs text-gray-500 mt-1">Mỗi dòng là một điểm nổi bật</p>
                    </div>

                    <!-- Bao gồm -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-bold text-green-800">✅ Giá tour BAO GỒM</h3>
                            <button type="button" onclick="addIncludedItem()"
                                class="text-sm text-green-600 hover:underline">
                                <i class="fas fa-plus mr-1"></i>Thêm
                            </button>
                        </div>
                        <div id="included-container" class="space-y-2">
                            <!-- Dynamic items -->
                        </div>
                        <p class="text-xs text-green-600 mt-3">💡 Các dịch vụ/tiện ích đã tính trong giá tour</p>
                    </div>

                    <!-- Không bao gồm -->
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-bold text-red-800">❌ Giá tour KHÔNG BAO GỒM</h3>
                            <button type="button" onclick="addExcludedItem()"
                                class="text-sm text-red-600 hover:underline">
                                <i class="fas fa-plus mr-1"></i>Thêm
                            </button>
                        </div>
                        <div id="excluded-container" class="space-y-2">
                            <!-- Dynamic items -->
                        </div>
                        <p class="text-xs text-red-600 mt-3">💡 Chi phí khách tự chi trả thêm</p>
                    </div>
                </div>

                <!-- Quick Templates -->
                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-600 mr-2">Mẫu nhanh:</span>
                    <button type="button" onclick="applyTemplate('domestic')"
                        class="text-sm text-accent hover:underline mr-3">
                        Tour trong nước
                    </button>
                    <button type="button" onclick="applyTemplate('international')"
                        class="text-sm text-accent hover:underline">
                        Tour quốc tế
                    </button>
                </div>
            </div>

            <!-- ============================================================
                 STEP 4: CHÍNH SÁCH
                 ============================================================ -->
            <div id="step-4" class="step-content hidden">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Chọn chính sách</h2>

                <?php
                // Include Policy Selector Component
                require VIEWS_PATH . '/components/policy-selector.php';
                ?>
            </div>

            <!-- ============================================================
                 STEP 5: HÌNH ẢNH
                 ============================================================ -->
            <div id="step-5" class="step-content hidden">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Hình ảnh Tour</h2>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Upload hình ảnh (tối đa 10 hình, tổng dung lượng ≤ 10MB)
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:bg-gray-50 transition-colors"
                        onclick="document.getElementById('tour_images').click()">
                        <input type="file" name="images[]" id="tour_images" multiple accept="image/*" class="hidden"
                            onchange="previewTourImages(this)">
                        <div class="text-4xl mb-2">📷</div>
                        <p class="text-gray-500">Click để tải ảnh lên</p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, GIF (tối đa 5MB mỗi file)</p>
                    </div>
                    <div id="image-preview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                        <!-- Image previews will be shown here -->
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Lưu ý:</strong> Hình đầu tiên sẽ được đặt làm hình chính của tour.
                    </p>
                </div>
            </div>

            <!-- ============================================================
                 STEP 6: GIÁ & LƯU
                 ============================================================ -->
            <div id="step-6" class="step-content hidden">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Giá Tour & Lưu</h2>

                <!-- Pricing Breakdown -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h3 class="font-bold text-blue-900 mb-4">💰 PHÂN TÍCH GIÁ TOUR</h3>

                    <div id="pricing-breakdown" class="space-y-3 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Chi phí dịch vụ/người:</span>
                            <span id="service-cost-per-person" class="font-medium">0đ</span>
                        </div>
                        <div id="day-breakdown" class="ml-4 text-xs text-gray-500">
                            <!-- Day breakdown sẽ được hiển thị ở đây -->
                        </div>

                        <div class="border-t border-blue-300 pt-3 mt-3">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Chi phí cố định/người:</span>
                                <span id="fixed-cost-per-person" class="font-medium">0đ</span>
                            </div>
                            <div id="fixed-cost-detail" class="ml-4 text-xs text-gray-500">
                                <!-- Fixed cost detail -->
                            </div>
                        </div>

                        <div class="border-t-2 border-blue-400 pt-3 mt-3">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-blue-900">Tổng chi phí/người:</span>
                                <span id="total-cost-per-person" class="text-xl font-bold text-blue-700">0đ</span>
                            </div>
                        </div>

                        <div class="border-t border-blue-300 pt-3 mt-3">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-blue-900">Giá đề xuất/người:</span>
                                <span id="suggested-price-per-person" class="text-xl font-bold text-blue-700">0đ</span>
                            </div>
                            <p class="text-xs text-blue-600 mt-1">
                                (Đã tính đủ: dịch vụ + nhân sự + marketing + quản lý)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Fixed Costs Input -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-6">
                    <h3 class="font-bold text-gray-900 mb-4">Chi phí cố định của công ty</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Lương HDV <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="fixed_cost_guide" id="fixed_cost_guide" step="1000" min="0"
                                value="<?= $old['fixed_cost_guide'] ?? 0 ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent" onchange="updatePricing()">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chi phí quản lý tour</label>
                            <input type="number" name="fixed_cost_management" id="fixed_cost_management" step="1000"
                                min="0" value="<?= $old['fixed_cost_management'] ?? 0 ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent" onchange="updatePricing()">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chi phí marketing</label>
                            <input type="number" name="fixed_cost_marketing" id="fixed_cost_marketing" step="1000"
                                min="0" value="<?= $old['fixed_cost_marketing'] ?? 0 ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent" onchange="updatePricing()">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chi phí khác</label>
                            <input type="number" name="fixed_cost_other" id="fixed_cost_other" step="1000" min="0"
                                value="<?= $old['fixed_cost_other'] ?? 0 ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent" onchange="updatePricing()">
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-700">Tổng chi phí cố định:</span>
                            <span id="total-fixed-cost" class="text-lg font-bold text-gray-900">0đ</span>
                        </div>
                    </div>
                </div>

                <!-- Final Price Input -->
                <div class="bg-white border border-gray-300 rounded-lg p-6 mb-6">
                    <h3 class="font-bold text-gray-900 mb-4">🎯 Giá bán cuối cùng</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Giá người lớn <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="adult_price" id="adult_price" required min="0" step="1000"
                                value="<?= $old['adult_price'] ?? '' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent font-bold text-accent text-lg"
                                oninput="validatePricing()">
                            <button type="button" onclick="applySuggestedPrice()"
                                class="mt-2 text-sm text-blue-600 hover:underline">
                                <i class="fas fa-magic mr-1"></i>Dùng giá đề xuất
                            </button>
                            <p class="text-xs text-gray-500 mt-1">Giá áp dụng cho khách > 12 tuổi</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giá trẻ em</label>
                            <input type="number" name="child_price" id="child_price" min="0" step="1000"
                                value="<?= $old['child_price'] ?? '' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent">
                            <p class="text-xs text-gray-500 mt-1">Thường bằng 70-80% giá người lớn</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giá em bé</label>
                            <input type="number" name="infant_price" id="infant_price" min="0" step="1000"
                                value="<?= $old['infant_price'] ?? '0' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent">
                            <p class="text-xs text-gray-500 mt-1">Dưới 2 tuổi</p>
                        </div>
                    </div>
                </div>

                <!-- Deposit & Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phần trăm đặt cọc</label>
                        <div class="flex items-center">
                            <input type="number" name="deposit_percentage" min="0" max="100"
                                value="<?= $old['deposit_percentage'] ?? '30' ?>"
                                class="w-full px-3 py-2 border rounded-l focus:border-accent text-center">
                            <span
                                class="px-3 py-2 bg-gray-100 border border-l-0 rounded-r text-sm text-gray-600">%</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                        <select name="status" class="w-full px-3 py-2 border rounded focus:border-accent">
                            <option value="draft" <?= ($old['status'] ?? 'draft') == 'draft' ? 'selected' : '' ?>>
                                Nháp (Draft)
                            </option>
                            <option value="pending" <?= ($old['status'] ?? '') == 'pending' ? 'selected' : '' ?>>
                                Chờ duyệt (Pending)
                            </option>
                            <option value="active" <?= ($old['status'] ?? '') == 'active' ? 'selected' : '' ?>>
                                Hoạt động (Active) - Admin only
                            </option>
                        </select>
                    </div>
                </div>
            </div>

        </div>

        <!-- FOOTER ACTIONS -->
        <div class="bg-gray-50 px-6 py-4 flex justify-between border-t">
            <button type="button" id="prevBtn"
                class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50 hidden"
                onclick="changeStep(-1)">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại
            </button>
            <div class="flex gap-2">
                <a href="?act=admin&module=tours"
                    class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                    Hủy
                </a>
                <button type="button" id="nextBtn"
                    class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600 shadow" onclick="changeStep(1)">
                    Tiếp theo <i class="fas fa-arrow-right ml-2"></i>
                </button>
                <button type="submit" id="submitBtn"
                    class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 shadow hidden">
                    <i class="fas fa-save mr-2"></i>Hoàn tất & Lưu
                </button>
            </div>
        </div>
    </form>
</div>

<!-- JavaScript sẽ được thêm vào file riêng hoặc inline ở đây -->
<script>
    // Data từ PHP
    const destinations = <?= json_encode($destinations) ?>;
    const services = <?= json_encode($services) ?>;
    const serviceProviders = <?= json_encode($service_providers) ?>;
    const oldItinerary = <?= json_encode(is_array($old_itinerary) ? $old_itinerary : []) ?>;
    const oldTimelines = <?= json_encode(is_array($timelines_by_day) ? $timelines_by_day : []) ?>;
    const oldDayServices = <?= json_encode(is_array($day_services_by_day) ? $day_services_by_day : []) ?>;
    const oldHighlights = <?= json_encode(is_array($old_highlights) ? $old_highlights : []) ?>;
    const oldIncludes = <?= json_encode(is_array($old_includes) ? $old_includes : []) ?>;
    const oldExcludes = <?= json_encode(is_array($old_excludes) ? $old_excludes : []) ?>;
    const selectedPolicyIds = <?= json_encode(is_array($selected_policy_ids) ? $selected_policy_ids : []) ?>;

    let currentStep = 1;
    const totalSteps = 6;
    let currentItineraryTab = 'overview';
    let currentTimelineDay = null;
    let currentDayServicesDay = null;
</script>

<!-- Inline JavaScript for Tour Form Wizard -->
<script>
    // ============================================================================
    // WIZARD NAVIGATION
    // ============================================================================

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
            if (indicator) {
                indicator.classList.remove('text-gray-400', 'border-transparent');
                indicator.classList.add('text-accent', 'border-accent');
            }
        }

        // Button visibility
        document.getElementById('prevBtn').classList.toggle('hidden', step === 1);
        document.getElementById('nextBtn').classList.toggle('hidden', step === totalSteps);
        document.getElementById('submitBtn').classList.toggle('hidden', step !== totalSteps);

        // Special handling for each step
        if (step === 2) {
            generateItineraryDays();
        }
        if (step === 3) {
            initIncludedExcluded();
        }
        if (step === 6) {
            updatePricing();
        }
    }

    function changeStep(direction) {
        if (direction === 1 && !validateStep(currentStep)) return;

        currentStep += direction;
        if (currentStep < 1) currentStep = 1;
        if (currentStep > totalSteps) currentStep = totalSteps;

        showStep(currentStep);
    }

    function validateStep(step) {
        const stepEl = document.getElementById(`step-${step}`);
        if (!stepEl) return true;

        const inputs = stepEl.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('border-red-500');
                input.addEventListener('input', () => input.classList.remove('border-red-500'), { once: true });
            }
        });

        if (!isValid) {
            alert('Vui lòng điền đầy đủ thông tin bắt buộc trước khi tiếp tục.');
        }

        return isValid;
    }

    // ============================================================================
    // ITINERARY TABS
    // ============================================================================

    function switchItineraryTab(tab) {
        currentItineraryTab = tab;

        // Hide all tabs
        document.querySelectorAll('.itinerary-tab').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('[id^="tab-"]').forEach(btn => {
            btn.classList.remove('text-blue-600', 'border-blue-600');
            btn.classList.add('text-gray-500', 'border-transparent');
        });

        // Show selected tab
        document.getElementById(`tab-${tab}`).classList.remove('hidden');
        const btn = document.getElementById(`tab-${tab}-btn`);
        if (btn) {
            btn.classList.remove('text-gray-500', 'border-transparent');
            btn.classList.add('text-blue-600', 'border-blue-600');
        }

        // Load data for specific tabs
        if (tab === 'timeline' && !currentTimelineDay) {
            const days = parseInt(document.getElementById('duration_days').value) || 0;
            if (days > 0) {
                loadTimelineForDay(1);
            }
        }
        if (tab === 'services' && !currentDayServicesDay) {
            const days = parseInt(document.getElementById('duration_days').value) || 0;
            if (days > 0) {
                loadDayServicesForDay(1);
            }
        }
    }

    function generateItineraryDays() {
        const days = parseInt(document.getElementById('duration_days').value) || 0;
        const container = document.getElementById('itinerary-overview-container');
        const timelineSelector = document.getElementById('timeline-day-selector');
        const servicesSelector = document.getElementById('day-services-day-selector');

        if (days <= 0) {
            container.innerHTML = '<p class="text-gray-500">Vui lòng nhập số ngày ở Bước 1.</p>';
            return;
        }

        // Generate overview HTML
        let html = '';
        let timelineOptions = '<option value="">-- Chọn ngày --</option>';
        let servicesOptions = '<option value="">-- Chọn ngày --</option>';

        for (let i = 1; i <= days; i++) {
            const oldDay = oldItinerary[i - 1] || {};

            // Build destination options với selected value cho ngày này
            let dayDestOptions = '<option value="">-- Chọn điểm đến --</option>';
            if (destinations && typeof destinations === 'object') {
                if (Array.isArray(destinations)) {
                    destinations.forEach(dest => {
                        const id = (typeof dest === 'object' ? dest.id : null) || dest;
                        const name = (typeof dest === 'object' ? dest.name : null) || dest;
                        const selected = (oldDay.destination_id && String(id) === String(oldDay.destination_id)) ? 'selected' : '';
                        dayDestOptions += `<option value="${id}" ${selected}>${escapeHtml(String(name))}</option>`;
                    });
                } else {
                    for (const [id, name] of Object.entries(destinations)) {
                        const selected = (oldDay.destination_id && String(id) === String(oldDay.destination_id)) ? 'selected' : '';
                        dayDestOptions += `<option value="${id}" ${selected}>${escapeHtml(String(name))}</option>`;
                    }
                }
            }

            html += `
            <div class="bg-gray-50 p-4 rounded border mb-4 itinerary-day-item">
                <h3 class="font-bold text-gray-700 mb-3">Ngày ${i}</h3>
                <input type="hidden" name="itinerary_day_number[]" value="${i}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề ngày</label>
                        <input type="text" 
                               name="itinerary_title[]" 
                               value="${escapeHtml(oldDay.title || '')}"
                               placeholder="VD: Ngày ${i}: Khởi hành Đà Lạt"
                               class="w-full px-3 py-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Điểm đến</label>
                        <select name="itinerary_destination[]" 
                                class="w-full px-3 py-2 border rounded">
                            ${dayDestOptions}
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả lịch trình</label>
                    <textarea name="itinerary_description[]" 
                              rows="3"
                              class="w-full px-3 py-2 border rounded"
                              placeholder="Mô tả chi tiết lịch trình ngày ${i}...">${escapeHtml(oldDay.description || '')}</textarea>
                </div>
            </div>
        `;

            timelineOptions += `<option value="${i}">Day ${i}</option>`;
            servicesOptions += `<option value="${i}">Day ${i}</option>`;
        }

        container.innerHTML = html;

        if (timelineSelector) {
            timelineSelector.innerHTML = timelineOptions;
        }
        if (servicesSelector) {
            servicesSelector.innerHTML = servicesOptions;
        }
    }

    function loadTimelineForDay(dayNumber) {
        if (!dayNumber) return;
        currentTimelineDay = dayNumber;

        const container = document.getElementById('timeline-editor-container');
        const timelines = oldTimelines[dayNumber] || [];

        // Load timeline editor component
        // This would ideally load the component, but for now we'll show a message
        container.innerHTML = `
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-700">Timeline Editor cho Day ${dayNumber} - Component sẽ được load tại đây</p>
            <p class="text-sm text-yellow-600 mt-2">Timeline items: ${timelines.length}</p>
        </div>
    `;
    }

    function loadDayServicesForDay(dayNumber) {
        if (!dayNumber) return;
        currentDayServicesDay = dayNumber;

        const container = document.getElementById('day-services-editor-container');
        const services = oldDayServices[dayNumber] || [];

        container.innerHTML = `
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-700">Day Services Editor cho Day ${dayNumber} - Component sẽ được load tại đây</p>
            <p class="text-sm text-yellow-600 mt-2">Services: ${services.length}</p>
        </div>
    `;
    }

    // ============================================================================
    // INCLUDED/EXCLUDED
    // ============================================================================

    let includedInitialized = false;

    function initIncludedExcluded() {
        if (includedInitialized) return;
        includedInitialized = true;

        // Load old data
        if (Array.isArray(oldIncludes) && oldIncludes.length > 0) {
            oldIncludes.forEach(item => {
                if (item) addIncludedItem(item);
            });
        }
        if (Array.isArray(oldExcludes) && oldExcludes.length > 0) {
            oldExcludes.forEach(item => {
                if (item) addExcludedItem(item);
            });
        }
    }

    function addIncludedItem(text = '') {
        const container = document.getElementById('included-container');
        const html = `
        <div class="flex items-center gap-2 included-item">
            <span class="text-green-600">✓</span>
            <input type="text" name="included[]" value="${escapeHtml(text)}" 
                   placeholder="Nhập nội dung bao gồm..."
                   class="flex-1 px-3 py-2 border rounded focus:border-green-500 text-sm">
            <button type="button" onclick="this.parentElement.remove()" 
                    class="text-red-400 hover:text-red-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function addExcludedItem(text = '') {
        const container = document.getElementById('excluded-container');
        const html = `
        <div class="flex items-center gap-2 excluded-item">
            <span class="text-red-600">✗</span>
            <input type="text" name="excluded[]" value="${escapeHtml(text)}" 
                   placeholder="Nhập nội dung không bao gồm..."
                   class="flex-1 px-3 py-2 border rounded focus:border-red-500 text-sm">
            <button type="button" onclick="this.parentElement.remove()" 
                    class="text-red-400 hover:text-red-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
        container.insertAdjacentHTML('beforeend', html);
    }

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

    function applyTemplate(type) {
        const template = templates[type];
        if (!template) return;

        document.getElementById('included-container').innerHTML = '';
        document.getElementById('excluded-container').innerHTML = '';

        template.included.forEach(item => addIncludedItem(item));
        template.excluded.forEach(item => addExcludedItem(item));
    }

    // ============================================================================
    // PRICING CALCULATOR
    // ============================================================================

    function updatePricing() {
        // This will calculate pricing based on day services and fixed costs
        // For now, just update fixed cost display
        const guide = parseFloat(document.getElementById('fixed_cost_guide')?.value || 0);
        const management = parseFloat(document.getElementById('fixed_cost_management')?.value || 0);
        const marketing = parseFloat(document.getElementById('fixed_cost_marketing')?.value || 0);
        const other = parseFloat(document.getElementById('fixed_cost_other')?.value || 0);
        const minParticipants = parseFloat(document.getElementById('min_participants')?.value || 15);

        const totalFixed = guide + management + marketing + other;
        const fixedPerPerson = minParticipants > 0 ? totalFixed / minParticipants : 0;

        // Update UI
        const totalFixedEl = document.getElementById('total-fixed-cost');
        const fixedPerPersonEl = document.getElementById('fixed-cost-per-person');

        if (totalFixedEl) {
            totalFixedEl.textContent = formatCurrency(totalFixed);
        }
        if (fixedPerPersonEl) {
            fixedPerPersonEl.textContent = formatCurrency(fixedPerPerson);
        }

        // TODO: Calculate service cost from day services
        // For now, just update total
        updateTotalCost();
    }

    function updateTotalCost() {
        const serviceCost = 0; // TODO: Calculate from day services
        const fixedCost = parseFloat(document.getElementById('fixed-cost-per-person')?.textContent?.replace(/[^0-9]/g, '') || 0);
        const total = serviceCost + fixedCost;

        const totalEl = document.getElementById('total-cost-per-person');
        const suggestedEl = document.getElementById('suggested-price-per-person');

        if (totalEl) totalEl.textContent = formatCurrency(total);
        if (suggestedEl) suggestedEl.textContent = formatCurrency(total);
    }

    function applySuggestedPrice() {
        const suggested = document.getElementById('suggested-price-per-person')?.textContent?.replace(/[^0-9]/g, '') || '0';
        const adultPriceEl = document.getElementById('adult_price');
        if (adultPriceEl) {
            adultPriceEl.value = suggested;
        }
    }

    function validatePricing() {
        const adult = parseFloat(document.getElementById('adult_price')?.value || 0);
        const child = parseFloat(document.getElementById('child_price')?.value || 0);
        const infant = parseFloat(document.getElementById('infant_price')?.value || 0);

        if (child > adult) {
            alert('Giá trẻ em không được lớn hơn giá người lớn');
            document.getElementById('child_price').value = adult;
        }
        if (infant > child) {
            alert('Giá em bé không được lớn hơn giá trẻ em');
            document.getElementById('infant_price').value = child;
        }
    }

    // ============================================================================
    // IMAGE PREVIEW
    // ============================================================================

    function previewTourImages(input) {
        const container = document.getElementById('image-preview');
        container.innerHTML = '';

        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach(file => {
                if (file.size > 5 * 1024 * 1024) {
                    alert(`File ${file.name} vượt quá 5MB`);
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    const div = document.createElement('div');
                    div.className = 'relative aspect-video bg-gray-100 rounded overflow-hidden shadow-sm';
                    div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                    <button type="button" onclick="this.parentElement.remove()" 
                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                `;
                    container.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    }

    // ============================================================================
    // UTILITY FUNCTIONS
    // ============================================================================

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(amount || 0)) + 'đ';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        showStep(1);
        if (oldItinerary && oldItinerary.length > 0) {
            generateItineraryDays();
        }
    });
</script>