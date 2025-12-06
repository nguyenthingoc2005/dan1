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
                 STEP 2: LỊCH TRÌNH (Timeline và Dịch vụ ngay dưới mỗi ngày)
                 ============================================================ -->
            <div id="step-2" class="step-content hidden">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Lịch trình</h2>

                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-2 rounded mb-4 text-sm">
                    ℹ️ Lịch trình được tạo tự động dựa trên số ngày bạn nhập ở Bước 1. 
                    Mỗi ngày có thể quản lý Timeline chi tiết và Dịch vụ ngay bên dưới.
                </div>

                <div id="itinerary-overview-container" class="space-y-4">
                    <!-- Will be generated by JavaScript - Mỗi ngày sẽ có component itinerary-manager -->
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
        
        // Update URL with step parameter để lưu trạng thái
        const url = new URL(window.location);
        url.searchParams.set('step', step);
        window.history.pushState({}, '', url);
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

        let isValid = true;
        let errorMessage = '';

        // Special validation for step 2 (Itinerary) - Bắt buộc phải có timeline cho mỗi ngày
        if (step === 2) {
            const durationDays = parseInt(document.getElementById('duration_days')?.value || 0);
            if (durationDays > 0) {
                const missingTimelineDays = [];
                for (let day = 1; day <= durationDays; day++) {
                    // Check timeline items trong component itinerary-manager
                    const manager = stepEl.querySelector(`.itinerary-manager[data-day="${day}"]`);
                    if (manager) {
                        const timelineItems = manager.querySelectorAll('.timeline-item input[name="timeline_time[]"]');
                        let hasValidTimeline = false;
                        
                        timelineItems.forEach(input => {
                            const item = input.closest('.timeline-item');
                            if (item) {
                                const time = input.value;
                                const activity = item.querySelector('[name="timeline_activity_title[]"]')?.value;
                                if (time && activity && time.trim() && activity.trim()) {
                                    hasValidTimeline = true;
                                }
                            }
                        });
                        
                        if (!hasValidTimeline) {
                            missingTimelineDays.push(day);
                        }
                    } else {
                        // Component chưa được load, coi như chưa có timeline
                        missingTimelineDays.push(day);
                    }
                }
                
                if (missingTimelineDays.length > 0) {
                    isValid = false;
                    errorMessage = `Vui lòng nhập timeline chi tiết cho các ngày: ${missingTimelineDays.join(', ')}. Mỗi ngày phải có ít nhất một timeline item với giờ và hoạt động.`;
                    // Highlight các ngày thiếu timeline
                    missingTimelineDays.forEach(day => {
                        const dayItem = stepEl.querySelector(`.itinerary-day-item[data-day="${day}"]`);
                        if (dayItem) {
                            dayItem.style.border = '2px solid red';
                            dayItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            setTimeout(() => {
                                dayItem.style.border = '';
                            }, 5000);
                        }
                    });
                }
            }
        }

        // Special validation for step 4 (Policies) - Check FIRST before other required inputs
        if (step === 4) {
            const policyInputs = stepEl.querySelectorAll('input[name="policy_ids[]"]');
            if (policyInputs.length === 0) {
                isValid = false;
                errorMessage = 'Vui lòng chọn ít nhất một chính sách trước khi tiếp tục.';
                // Highlight the policy selector area
                const policySelector = stepEl.querySelector('.policy-selector');
                if (policySelector) {
                    policySelector.style.border = '2px solid red';
                    policySelector.style.borderRadius = '8px';
                    policySelector.style.padding = '8px';
                    setTimeout(() => {
                        policySelector.style.border = '';
                        policySelector.style.padding = '';
                    }, 3000);
                }
            }
        }

        // Check required inputs (but skip inputs inside hidden modals)
        const inputs = stepEl.querySelectorAll('input[required], select[required], textarea[required]');
        inputs.forEach(input => {
            // Skip inputs that are inside hidden modals or not visible
            const modal = input.closest('#create-policy-modal, #preview-policy-modal');
            if (modal && modal.classList.contains('hidden')) return;

            // Also check if input itself is hidden
            if (input.offsetParent === null) return; // Element is hidden

            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('border-red-500');
                input.addEventListener('input', () => input.classList.remove('border-red-500'), { once: true });
            }
        });

        if (!isValid) {
            if (errorMessage) {
                alert(errorMessage);
            } else {
                alert('Vui lòng điền đầy đủ thông tin bắt buộc trước khi tiếp tục.');
            }
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
            <div class="bg-gray-50 p-4 rounded border mb-4 itinerary-day-item" data-day="${i}">
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
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả lịch trình</label>
                    <textarea name="itinerary_description[]" 
                              rows="3"
                              class="w-full px-3 py-2 border rounded"
                              placeholder="Mô tả chi tiết lịch trình ngày ${i}...">${escapeHtml(oldDay.description || '')}</textarea>
                </div>
                
                <!-- Itinerary Manager Component (Timeline + Services) -->
                <div id="itinerary-manager-day-${i}" class="mt-4">
                    <!-- Component sẽ được load ở đây -->
                </div>
            </div>
        `;

            timelineOptions += `<option value="${i}">Day ${i}</option>`;
            servicesOptions += `<option value="${i}">Day ${i}</option>`;
        }

        container.innerHTML = html;

        // Load itinerary manager component for each day
        for (let i = 1; i <= days; i++) {
            loadItineraryManagerForDay(i);
        }
    }
    
    function loadItineraryManagerForDay(dayNumber) {
        const container = document.getElementById(`itinerary-manager-day-${dayNumber}`);
        if (!container) return;
        
        // Get current step from URL or default to 2
        const urlParams = new URLSearchParams(window.location.search);
        const step = urlParams.get('step') || '2';
        
        // Build URL
        let url = `?act=admin&module=tours&action=loadItineraryManager&day=${dayNumber}&step=${step}`;
        const tourId = document.querySelector('input[name="tour_id"]')?.value || '';
        if (tourId) {
            url += `&tour_id=${tourId}`;
        }
        
        // Show loading
        container.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-xl text-blue-500"></i></div>';
        
        // Load via fetch
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.text();
        })
        .then(html => {
            container.innerHTML = html;
            // Execute any scripts in the loaded HTML immediately
            const scripts = container.querySelectorAll('script');
            scripts.forEach(oldScript => {
                if (oldScript.src) {
                    // External script - load normally
                    const newScript = document.createElement('script');
                    newScript.src = oldScript.src;
                    document.head.appendChild(newScript);
                } else {
                    // Inline script - execute immediately using eval
                    try {
                        eval(oldScript.textContent);
                    } catch (e) {
                        console.error('Error executing script:', e);
                    }
                }
                oldScript.remove();
            });
        })
        .catch(err => {
            console.error('Error loading itinerary manager:', err);
            container.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-700 text-sm">Lỗi khi tải component. Vui lòng thử lại.</p>
                    <button onclick="loadItineraryManagerForDay(${dayNumber})" class="mt-2 px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600">
                        Thử lại
                    </button>
                </div>
            `;
        });
    }

    function loadTimelineForDay(dayNumber) {
        if (!dayNumber) return;
        currentTimelineDay = dayNumber;

        const container = document.getElementById('timeline-editor-container');

        // Load component via URL (direct load with AJAX)
        let url = `?act=admin&module=tours&action=loadTimelineEditor&day=${dayNumber}`;
        const tourId = document.querySelector('input[name="tour_id"]')?.value || '';
        if (tourId) {
            url += `&tour_id=${tourId}`;
        }

        // Show loading
        container.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i><p class="mt-2 text-gray-600">Đang tải timeline...</p></div>';

        // Load via fetch
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.text();
            })
            .then(html => {
                container.innerHTML = html;
                // Re-initialize scripts
                if (typeof sortTimelineByTime === 'function') {
                    setTimeout(() => sortTimelineByTime(dayNumber), 100);
                }
            })
            .catch(err => {
                console.error('Error loading timeline editor:', err);
                container.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-700">Lỗi khi tải component timeline. Vui lòng thử lại.</p>
                    <button onclick="loadTimelineForDay(${dayNumber})" class="mt-2 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        Thử lại
                    </button>
                </div>
            `;
            });
    }

    function loadDayServicesForDay(dayNumber) {
        if (!dayNumber) return;
        currentDayServicesDay = dayNumber;

        const container = document.getElementById('day-services-editor-container');

        // Load component via URL (direct load with AJAX)
        let url = `?act=admin&module=tours&action=loadDayServicesEditor&day=${dayNumber}`;
        const tourId = document.querySelector('input[name="tour_id"]')?.value || '';
        if (tourId) {
            url += `&tour_id=${tourId}`;
        }

        // Show loading
        container.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-green-500"></i><p class="mt-2 text-gray-600">Đang tải dịch vụ...</p></div>';

        // Load via fetch
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.text();
            })
            .then(html => {
                container.innerHTML = html;
                // Re-initialize scripts
                if (typeof updateDayServiceTotal === 'function') {
                    setTimeout(() => updateDayServiceTotal(dayNumber), 100);
                }
            })
            .catch(err => {
                console.error('Error loading day services editor:', err);
                container.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-700">Lỗi khi tải component dịch vụ. Vui lòng thử lại.</p>
                    <button onclick="loadDayServicesForDay(${dayNumber})" class="mt-2 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        Thử lại
                    </button>
                </div>
            `;
            });
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
        // Get step from URL or default to 1
        const urlParams = new URLSearchParams(window.location.search);
        const stepFromUrl = urlParams.get('step');
        const initialStep = stepFromUrl ? parseInt(stepFromUrl) : 1;
        
        showStep(initialStep);
        
        // If step 2, generate itinerary days
        if (initialStep === 2 || (oldItinerary && oldItinerary.length > 0)) {
            setTimeout(() => generateItineraryDays(), 100);
        }
        
        // Event delegation for itinerary manager
        initItineraryManagerEvents();
    });
    
    // ============================================================================
    // ITINERARY MANAGER EVENT DELEGATION
    // ============================================================================
    
    function initItineraryManagerEvents() {
        // Use event delegation for all itinerary manager actions
        document.addEventListener('click', function(e) {
            const action = e.target.closest('[data-action]')?.getAttribute('data-action');
            const dayNumber = e.target.closest('[data-action]')?.getAttribute('data-day');
            
            if (!action || !dayNumber) return;
            
            switch(action) {
                case 'toggle-day-manager':
                    toggleDayManager(parseInt(dayNumber));
                    break;
                case 'open-timeline-modal':
                    openAddTimelineModal(parseInt(dayNumber));
                    break;
                case 'close-timeline-modal':
                    closeAddTimelineModal(parseInt(dayNumber));
                    break;
                case 'open-service-modal':
                    openAddServiceModal(parseInt(dayNumber));
                    break;
                case 'close-service-modal':
                    closeAddServiceModal(parseInt(dayNumber));
                    break;
            }
        });
        
        // Form submissions
        document.addEventListener('submit', function(e) {
            const form = e.target;
            const action = form.getAttribute('data-action');
            const dayNumber = form.getAttribute('data-day');
            
            if (!action || !dayNumber) return;
            
            e.preventDefault();
            
            switch(action) {
                case 'save-timeline':
                    saveTimelineItem(e, parseInt(dayNumber));
                    break;
                case 'save-service':
                    saveDayService(e, parseInt(dayNumber));
                    break;
            }
        });
    }
    
    // Initialize counters
    window.timelineCounter = window.timelineCounter || {};
    window.dayServiceCounter = window.dayServiceCounter || {};
    
    // Itinerary Manager Functions
    function toggleDayManager(dayNumber) {
        const content = document.getElementById(`day-manager-content-${dayNumber}`);
        const icon = document.getElementById(`toggle-icon-${dayNumber}`);
        if (content && icon) {
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
    }
    
    function openAddTimelineModal(dayNumber) {
        const modal = document.getElementById(`add-timeline-modal-day-${dayNumber}`);
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }
    }
    
    function closeAddTimelineModal(dayNumber) {
        const modal = document.getElementById(`add-timeline-modal-day-${dayNumber}`);
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
            const form = document.getElementById(`add-timeline-form-day-${dayNumber}`);
            if (form) form.reset();
        }
    }
    
    function openAddServiceModal(dayNumber) {
        const modal = document.getElementById(`add-service-modal-day-${dayNumber}`);
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }
    }
    
    function closeAddServiceModal(dayNumber) {
        const modal = document.getElementById(`add-service-modal-day-${dayNumber}`);
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
            const form = document.getElementById(`add-service-form-day-${dayNumber}`);
            if (form) form.reset();
        }
    }
    
    function saveTimelineItem(event, dayNumber) {
        // Initialize counter if needed
        if (!window.timelineCounter[dayNumber]) {
            const dataEl = document.getElementById(`itinerary-manager-data-${dayNumber}`);
            if (dataEl) {
                const data = JSON.parse(dataEl.textContent);
                window.timelineCounter[dayNumber] = data.timeline_count || 0;
            } else {
                window.timelineCounter[dayNumber] = 0;
            }
        }
        
        const counter = window.timelineCounter[dayNumber] = window.timelineCounter[dayNumber] + 1;
        const container = document.getElementById(`timeline-items-day-${dayNumber}`);
        if (!container) return;
        
        const time = document.getElementById(`modal-timeline-time-day-${dayNumber}`)?.value || '';
        const type = document.getElementById(`modal-timeline-type-day-${dayNumber}`)?.value || 'activity';
        const activity = document.getElementById(`modal-timeline-activity-day-${dayNumber}`)?.value || '';
        const description = document.getElementById(`modal-timeline-description-day-${dayNumber}`)?.value || '';
        const location = document.getElementById(`modal-timeline-location-day-${dayNumber}`)?.value || '';
        const providerId = document.getElementById(`modal-timeline-provider-day-${dayNumber}`)?.value || '';
        const destinationId = document.getElementById(`modal-timeline-destination-day-${dayNumber}`)?.value || '';
        const serviceId = document.getElementById(`modal-timeline-service-day-${dayNumber}`)?.value || '';
        const notes = document.getElementById(`modal-timeline-notes-day-${dayNumber}`)?.value || '';
        
        if (!time || !activity) {
            alert('Vui lòng nhập giờ và hoạt động');
            return;
        }
        
        // Get data for dropdowns
        const dataEl = document.getElementById(`itinerary-manager-data-${dayNumber}`);
        const data = dataEl ? JSON.parse(dataEl.textContent) : {};
        
        const typeIcons = {
            'meal': '🍽️',
            'accommodation': '🏨',
            'activity': '🎯',
            'transport': '🚌'
        };
        
        const typeLabels = {
            'meal': 'Bữa ăn',
            'accommodation': 'Nơi nghỉ',
            'activity': 'Hoạt động',
            'transport': 'Di chuyển'
        };
        
        const typeColors = {
            'meal': 'bg-orange-50 border-orange-200',
            'accommodation': 'bg-blue-50 border-blue-200',
            'activity': 'bg-green-50 border-green-200',
            'transport': 'bg-purple-50 border-purple-200'
        };
        
        // Build options HTML
        let providerOptions = '<option value="">-- Chọn nhà dịch vụ --</option>';
        if (data.service_providers) {
            data.service_providers.forEach(provider => {
                const id = provider.id || provider;
                const name = provider.name || provider;
                const address = provider.address || '';
                const selected = providerId && String(id) === String(providerId) ? 'selected' : '';
                providerOptions += `<option value="${id}" data-address="${escapeHtml(address)}" ${selected}>${escapeHtml(name)}</option>`;
            });
        }
        
        let destOptions = '<option value="">-- Chọn địa điểm --</option>';
        if (data.destinations) {
            if (Array.isArray(data.destinations)) {
                data.destinations.forEach(dest => {
                    const id = dest.id || dest;
                    const name = dest.name || dest;
                    const selected = destinationId && String(id) === String(destinationId) ? 'selected' : '';
                    destOptions += `<option value="${id}" ${selected}>${escapeHtml(name)}</option>`;
                });
            }
        }
        
        let serviceOptions = '<option value="">-- Chọn dịch vụ --</option>';
        if (data.services) {
            data.services.forEach(service => {
                const id = service.id || service;
                const name = service.name || service;
                const selected = serviceId && String(id) === String(serviceId) ? 'selected' : '';
                serviceOptions += `<option value="${id}" ${selected}>${escapeHtml(name)}</option>`;
            });
        }
        
        const itemHtml = `
            <div class="timeline-item bg-white border-2 ${typeColors[type]} rounded-lg p-4 mb-4 relative" 
                 data-day="${dayNumber}" data-index="${counter}" data-time="${time}">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center gap-2">
                        <input type="time" name="timeline_time[]" value="${time}" required
                            class="px-2 py-1 border rounded focus:border-blue-500 font-semibold text-blue-600 text-lg">
                        <span class="text-sm font-medium text-gray-600 px-2 py-1 bg-white rounded">${typeLabels[type]}</span>
                    </div>
                    <button type="button" onclick="this.closest('.timeline-item').remove()" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hoạt động <span class="text-red-500">*</span></label>
                        <input type="text" name="timeline_activity_title[]" value="${escapeHtml(activity)}" required
                            class="w-full px-3 py-2 border rounded focus:border-blue-500">
                        <input type="hidden" name="timeline_day_number[]" value="${dayNumber}">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                        <textarea name="timeline_activity_description[]" rows="2"
                            class="w-full px-3 py-2 border rounded focus:border-blue-500">${escapeHtml(description)}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loại timeline</label>
                        <select name="timeline_type[]" class="w-full px-3 py-2 border rounded focus:border-blue-500">
                            <option value="activity" ${type === 'activity' ? 'selected' : ''}>Hoạt động</option>
                            <option value="meal" ${type === 'meal' ? 'selected' : ''}>Bữa ăn</option>
                            <option value="accommodation" ${type === 'accommodation' ? 'selected' : ''}>Nơi nghỉ</option>
                            <option value="transport" ${type === 'transport' ? 'selected' : ''}>Di chuyển</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Địa điểm</label>
                        <input type="text" name="timeline_location[]" value="${escapeHtml(location)}"
                            class="w-full px-3 py-2 border rounded focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nhà dịch vụ</label>
                        <select name="timeline_service_provider[]" class="w-full px-3 py-2 border rounded focus:border-blue-500">
                            ${providerOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Địa điểm du lịch</label>
                        <select name="timeline_destination[]" class="w-full px-3 py-2 border rounded focus:border-blue-500">
                            ${destOptions}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dịch vụ</label>
                        <select name="timeline_service[]" class="w-full px-3 py-2 border rounded focus:border-blue-500">
                            ${serviceOptions}
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                        <textarea name="timeline_notes[]" rows="2"
                            class="w-full px-3 py-2 border rounded focus:border-blue-500">${escapeHtml(notes)}</textarea>
                    </div>
                    <input type="hidden" name="timeline_display_order[]" value="${counter}">
                </div>
            </div>
        `;
        
        if (container.innerHTML.includes('Chưa có timeline')) {
            container.innerHTML = itemHtml;
        } else {
            container.insertAdjacentHTML('beforeend', itemHtml);
        }
        
        closeAddTimelineModal(dayNumber);
    }
    
    function saveDayService(event, dayNumber) {
        // Initialize counter if needed
        if (!window.dayServiceCounter[dayNumber]) {
            const dataEl = document.getElementById(`itinerary-manager-data-${dayNumber}`);
            if (dataEl) {
                const data = JSON.parse(dataEl.textContent);
                window.dayServiceCounter[dayNumber] = data.services_count || 0;
            } else {
                window.dayServiceCounter[dayNumber] = 0;
            }
        }
        
        const counter = window.dayServiceCounter[dayNumber] = window.dayServiceCounter[dayNumber] + 1;
        const container = document.getElementById(`day-services-list-day-${dayNumber}`);
        if (!container) return;
        
        const serviceSelect = document.getElementById(`modal-service-id-day-${dayNumber}`);
        const serviceId = serviceSelect?.value || '';
        const serviceName = serviceSelect?.options[serviceSelect.selectedIndex]?.getAttribute('data-name') || 
                           serviceSelect?.options[serviceSelect.selectedIndex]?.text || '';
        const providerId = document.getElementById(`modal-service-provider-id-day-${dayNumber}`)?.value || '';
        const providerName = providerId ? 
            document.getElementById(`modal-service-provider-id-day-${dayNumber}`)?.options[document.getElementById(`modal-service-provider-id-day-${dayNumber}`).selectedIndex]?.text : '';
        const unitPrice = parseFloat(document.getElementById(`modal-unit-price-day-${dayNumber}`)?.value || 0);
        const quantity = parseFloat(document.getElementById(`modal-quantity-day-${dayNumber}`)?.value || 1);
        const unit = document.getElementById(`modal-unit-day-${dayNumber}`)?.value || '';
        const included = document.getElementById(`modal-included-day-${dayNumber}`)?.checked || false;
        const notes = document.getElementById(`modal-notes-day-${dayNumber}`)?.value || '';
        
        if (!serviceId || !serviceName) {
            alert('Vui lòng chọn dịch vụ');
            return;
        }
        
        const total = unitPrice * quantity;
        
        const serviceHtml = `
            <div class="day-service-item bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 mt-1">
                        <input type="checkbox" name="day_service_included[${dayNumber}][${counter}]" value="1"
                            ${included ? 'checked' : ''}
                            onchange="updateDayServiceTotal(${dayNumber})"
                            class="w-5 h-5 text-blue-600 rounded">
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-800">
                            ${escapeHtml(serviceName)}
                            ${providerName ? `<span class="text-sm text-gray-500">- ${escapeHtml(providerName)}</span>` : ''}
                        </div>
                        <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                            <div>
                                <span class="text-gray-600">Đơn giá/người:</span>
                                <span class="font-medium ml-2">${formatCurrency(unitPrice)}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Số lượng:</span>
                                <span class="font-medium ml-2">${formatNumber(quantity)} ${unit || ''}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Tổng:</span>
                                <span class="font-medium text-blue-600 ml-2">${formatCurrency(total)}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Bao gồm:</span>
                                <span class="font-medium ml-2 ${included ? 'text-green-600' : 'text-gray-400'}">
                                    ${included ? 'Có' : 'Không'}
                                </span>
                            </div>
                        </div>
                        ${notes ? `
                            <div class="mt-2 text-sm text-gray-500">
                                <i class="fas fa-sticky-note mr-1"></i>
                                ${escapeHtml(notes)}
                            </div>
                        ` : ''}
                        <input type="hidden" name="day_service_day_number[]" value="${dayNumber}">
                        <input type="hidden" name="day_service_service_id[]" value="${serviceId}">
                        <input type="hidden" name="day_service_provider_id[]" value="${providerId || ''}">
                        <input type="hidden" name="day_service_name[]" value="${escapeHtml(serviceName)}">
                        <input type="hidden" name="day_service_unit_price[]" value="${unitPrice}">
                        <input type="hidden" name="day_service_quantity[]" value="${quantity}">
                        <input type="hidden" name="day_service_unit[]" value="${escapeHtml(unit)}">
                        <input type="hidden" name="day_service_notes[]" value="${escapeHtml(notes)}">
                    </div>
                    <div class="flex-shrink-0">
                        <button type="button" onclick="this.closest('.day-service-item').remove()" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        if (container.innerHTML.includes('Chưa có dịch vụ')) {
            container.innerHTML = serviceHtml;
        } else {
            container.insertAdjacentHTML('beforeend', serviceHtml);
        }
        
        updateDayServiceTotal(dayNumber);
        closeAddServiceModal(dayNumber);
    }
    
    function updateDayServiceTotal(dayNumber) {
        const container = document.getElementById(`day-services-list-day-${dayNumber}`);
        if (!container) return;
        
        const items = container.querySelectorAll('.day-service-item');
        let total = 0;
        
        items.forEach(item => {
            const checkbox = item.querySelector('[name^="day_service_included"]');
            if (checkbox && checkbox.checked) {
                const unitPrice = parseFloat(item.querySelector('[name="day_service_unit_price[]"]')?.value || 0);
                const quantity = parseFloat(item.querySelector('[name="day_service_quantity[]"]')?.value || 1);
                total += (unitPrice * quantity);
            }
        });
        
        const totalEl = document.getElementById(`day-total-${dayNumber}`);
        if (totalEl) {
            totalEl.textContent = formatCurrency(total) + '/người';
        }
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(amount || 0)) + 'đ';
    }
    
    function formatNumber(num) {
        return new Intl.NumberFormat('vi-VN', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(num);
    }
</script>