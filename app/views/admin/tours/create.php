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

// Group day services by day_number
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
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">
                <?= $is_from_template ? 'Tạo Tour Custom từ Template' : 'Thêm Tour mới' ?>
            </h1>
            <?php if (!empty($template_info) && is_array($template_info)): ?>
                <p class="text-xs lg:text-sm text-primary-500 mt-1">
                    Template: <?= htmlspecialchars($template_info['name'] ?? 'N/A') ?>
                    (<?= htmlspecialchars($template_info['code'] ?? 'N/A') ?>)
                </p>
            <?php endif; ?>
        </div>
        <a href="?act=admin&module=tours" class="text-primary-500 hover:text-primary-700 font-semibold text-sm lg:text-base flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại danh sách
        </a>
    </div>

    <!-- ERROR ALERT - Responsive -->
    <?php if (!empty($errs)): ?>
        <div class="bg-danger-bg border border-danger text-danger-text px-4 lg:px-6 py-3 lg:py-4 rounded-2xl relative mb-4 lg:mb-6" role="alert">
            <strong class="font-bold text-sm lg:text-base">Có lỗi xảy ra!</strong>
            <ul class="mt-2 list-disc list-inside text-xs lg:text-sm">
                <?php foreach ($errs as $key => $msg): ?>
                    <li><?= htmlspecialchars(is_array($msg) ? implode(', ', $msg) : $msg) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form id="tourForm" method="POST" action="?act=admin&module=tours&action=store" enctype="multipart/form-data"
        class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">

        <!-- Hidden fields -->
        <input type="hidden" name="tour_type" value="<?= htmlspecialchars($old['tour_type'] ?? 'public') ?>">
        <?php if (!empty($old['parent_tour_id'])): ?>
            <input type="hidden" name="parent_tour_id" value="<?= (int) $old['parent_tour_id'] ?>">
        <?php endif; ?>

        <!-- WIZARD STEPS (6 steps) - Responsive -->
        <div class="flex border-b border-primary-100 bg-primary-50 overflow-x-auto">
            <div class="step-indicator px-2 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-semibold text-accent border-b-2 border-accent flex-1 text-center whitespace-nowrap min-w-[100px]"
                data-step="1">
                1. Thông tin chung
            </div>
            <div class="step-indicator px-2 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-semibold text-primary-500 border-b-2 border-transparent flex-1 text-center whitespace-nowrap min-w-[100px]"
                data-step="2">
                2. Lịch trình
            </div>
            <div class="step-indicator px-2 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-semibold text-primary-500 border-b-2 border-transparent flex-1 text-center whitespace-nowrap min-w-[100px]"
                data-step="3">
                3. Bao gồm
            </div>
            <div class="step-indicator px-2 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-semibold text-primary-500 border-b-2 border-transparent flex-1 text-center whitespace-nowrap min-w-[100px]"
                data-step="4">
                4. Chính sách
            </div>
            <div class="step-indicator px-2 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-semibold text-primary-500 border-b-2 border-transparent flex-1 text-center whitespace-nowrap min-w-[100px]"
                data-step="5">
                5. Hình ảnh
            </div>
            <div class="step-indicator px-2 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-semibold text-primary-500 border-b-2 border-transparent flex-1 text-center whitespace-nowrap min-w-[100px]"
                data-step="6">
                6. Giá & Lưu
            </div>
        </div>

        <!-- STEPS CONTENT - Responsive -->
        <div class="p-4 lg:p-6">

            <!-- ============================================================
                 STEP 1: THÔNG TIN CHUNG
                 ============================================================ -->
            <div id="step-1" class="step-content block">
                <h2 class="text-lg lg:text-xl font-bold text-primary-700 mb-4 lg:mb-6">Thông tin chung</h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-5">
                    <!-- Tên Tour -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                            Tên Tour <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                            required
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base <?= isset($errs['name']) ? 'border-danger' : '' ?>"
                            placeholder="VD: Tour Đà Lạt 3 ngày 2 đêm">
                        <?php if (isset($errs['name'])): ?>
                            <p class="text-danger text-xs mt-1"><?= htmlspecialchars($errs['name']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Điểm khởi hành -->
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Điểm khởi hành</label>
                        <input type="text" name="departure_location"
                            value="<?= htmlspecialchars($old['departure_location'] ?? '') ?>"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                            placeholder="VD: TP. Hồ Chí Minh">
                    </div>

                    <!-- Số ngày -->
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                            Số ngày <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="duration_days" id="duration_days" min="1"
                            value="<?= $old['duration_days'] ?? '3' ?>" required
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 font-bold text-accent text-sm lg:text-base"
                            onchange="generateItineraryDays()">
                    </div>

                    <!-- Số đêm -->
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số đêm</label>
                        <input type="number" name="duration_nights" id="duration_nights" min="0"
                            value="<?= $old['duration_nights'] ?? '2' ?>"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
                    </div>
                </div>

                <!-- Giới thiệu ngắn -->
                <div class="mt-4 lg:mt-5">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Giới thiệu ngắn</label>
                    <textarea name="introduction" rows="3" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="Mô tả ngắn gọn về tour (sẽ hiển thị ở danh sách)..."><?= htmlspecialchars($old['introduction'] ?? '') ?></textarea>
                </div>

                <!-- Mô tả chi tiết -->
                <div class="mt-4 lg:mt-5">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả chi tiết</label>
                    <textarea name="description" id="description" rows="6"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="Mô tả chi tiết về tour..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                    <p class="text-xs text-primary-500 mt-1">Bạn có thể sử dụng HTML hoặc rich text editor</p>
                </div>

                <!-- Số người & Booking Deadline -->
                <div class="mt-4 lg:mt-5 p-4 lg:p-5 bg-info-bg rounded-2xl border border-info">
                    <h4 class="font-semibold text-info-text mb-3 text-sm lg:text-base flex items-center gap-2">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        Thông tin số lượng khách
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số người tối thiểu</label>
                            <input type="number" name="min_participants" id="min_participants" min="1"
                                value="<?= $old['min_participants'] ?? '15' ?>"
                                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-white border border-primary-100 rounded-xl focus:outline-none focus:border-accent transition-all text-primary-700 text-center text-sm lg:text-base"
                                onchange="updatePricing(); updateMinParticipantsDisplay();">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số người tối đa</label>
                            <input type="number" name="max_participants" min="1"
                                value="<?= $old['max_participants'] ?? '45' ?>"
                                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-white border border-primary-100 rounded-xl focus:outline-none focus:border-accent transition-all text-primary-700 text-center text-sm lg:text-base">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Deadline đặt tour (ngày)</label>
                            <input type="number" name="booking_deadline_days" min="1"
                                value="<?= $old['booking_deadline_days'] ?? '1' ?>"
                                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-white border border-primary-100 rounded-xl focus:outline-none focus:border-accent transition-all text-primary-700 text-center text-sm lg:text-base" placeholder="1">
                            <p class="text-xs text-primary-500 mt-1">Trước ngày khởi hành</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================
                 STEP 2: LỊCH TRÌNH (Timeline và Dịch vụ ngay dưới mỗi ngày)
                 ============================================================ -->
            <div id="step-2" class="step-content hidden">
                <h2 class="text-lg lg:text-xl font-bold text-primary-700 mb-4 lg:mb-6">Lịch trình</h2>

                <div class="bg-info-bg border border-info text-info-text px-4 lg:px-5 py-3 lg:py-4 rounded-2xl mb-4 lg:mb-6 text-xs lg:text-sm flex items-start gap-2">
                    <i data-lucide="info" class="w-4 h-4 flex-shrink-0 mt-0.5"></i>
                    <span>Lịch trình được tạo tự động dựa trên số ngày bạn nhập ở Bước 1. Mỗi ngày có thể quản lý Timeline chi tiết và Dịch vụ ngay bên dưới.</span>
                </div>

                <div id="itinerary-overview-container" class="space-y-4">
                    <!-- Will be generated by JavaScript - Mỗi ngày sẽ có component itinerary-manager -->
                </div>
            </div>

            <!-- ============================================================
                 STEP 3: BAO GỒM/KHÔNG BAO GỒM
                 ============================================================ -->
            <div id="step-3" class="step-content hidden">
                <h2 class="text-lg lg:text-xl font-bold text-primary-700 mb-4 lg:mb-6">Bao gồm / Không bao gồm</h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                    <!-- Điểm nổi bật -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                            Điểm nổi bật (Highlights)
                        </label>
                        <textarea name="highlights" id="highlights" rows="5"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                            placeholder="Mỗi dòng một điểm nổi bật..."><?= htmlspecialchars(implode("\n", is_array($old_highlights) ? $old_highlights : [])) ?></textarea>
                        <p class="text-xs text-primary-500 mt-1">Mỗi dòng là một điểm nổi bật</p>
                    </div>

                    <!-- Bao gồm -->
                    <div class="bg-success-bg border border-success rounded-2xl p-4 lg:p-5">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-bold text-success-text text-sm lg:text-base flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                Giá tour BAO GỒM
                            </h3>
                            <button type="button" onclick="addIncludedItem()"
                                class="text-xs lg:text-sm text-success-text hover:text-success font-semibold flex items-center gap-1">
                                <i data-lucide="plus" class="w-3 h-3"></i>
                                Thêm
                            </button>
                        </div>
                        <div id="included-container" class="space-y-2">
                            <!-- Dynamic items -->
                        </div>
                        <p class="text-xs text-success-text mt-3 flex items-center gap-1">
                            <i data-lucide="lightbulb" class="w-3 h-3"></i>
                            Các dịch vụ/tiện ích đã tính trong giá tour
                        </p>
                    </div>

                    <!-- Không bao gồm -->
                    <div class="bg-danger-bg border border-danger rounded-2xl p-4 lg:p-5">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-bold text-danger-text text-sm lg:text-base flex items-center gap-2">
                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                Giá tour KHÔNG BAO GỒM
                            </h3>
                            <button type="button" onclick="addExcludedItem()"
                                class="text-xs lg:text-sm text-danger-text hover:text-danger font-semibold flex items-center gap-1">
                                <i data-lucide="plus" class="w-3 h-3"></i>
                                Thêm
                            </button>
                        </div>
                        <div id="excluded-container" class="space-y-2">
                            <!-- Dynamic items -->
                        </div>
                        <p class="text-xs text-danger-text mt-3 flex items-center gap-1">
                            <i data-lucide="lightbulb" class="w-3 h-3"></i>
                            Chi phí khách tự chi trả thêm
                        </p>
                    </div>
                </div>

                <!-- Quick Templates -->
                <div class="mt-4 p-3 lg:p-4 bg-primary-50 rounded-2xl border border-primary-100">
                    <span class="text-xs lg:text-sm text-primary-700 mr-2 font-semibold">Mẫu nhanh:</span>
                    <button type="button" onclick="applyTemplate('domestic')"
                        class="text-xs lg:text-sm text-accent hover:text-accent-hover font-semibold mr-3">
                        Tour trong nước
                    </button>
                    <button type="button" onclick="applyTemplate('international')"
                        class="text-xs lg:text-sm text-accent hover:text-accent-hover font-semibold">
                        Tour quốc tế
                    </button>
                </div>
            </div>

            <!-- ============================================================
                 STEP 4: CHÍNH SÁCH
                 ============================================================ -->
            <div id="step-4" class="step-content hidden">
                <h2 class="text-lg lg:text-xl font-bold text-primary-700 mb-4 lg:mb-6">Chọn chính sách</h2>

                <?php
                // Include Policy Selector Component
                require VIEWS_PATH . '/components/policy-selector.php';
                ?>
            </div>

            <!-- ============================================================
                 STEP 5: HÌNH ẢNH
                 ============================================================ -->
            <div id="step-5" class="step-content hidden">
                <h2 class="text-lg lg:text-xl font-bold text-primary-700 mb-4 lg:mb-6">Hình ảnh Tour</h2>

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
                <h2 class="text-lg lg:text-xl font-bold text-primary-700 mb-4 lg:mb-6">Giá Tour & Lưu</h2>

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
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Chi phí cố định/người:</span>
                                <span id="fixed-cost-per-person" class="font-medium">0đ</span>
                            </div>
                            <div id="fixed-cost-breakdown" class="ml-4 text-xs text-gray-500 mt-1">
                                <!-- Fixed cost breakdown sẽ được hiển thị ở đây -->
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
                                (Dựa trên tổng chi phí dịch vụ + chi phí cố định)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Final Price Input -->
                <div class="bg-white border border-gray-300 rounded-lg p-6 mb-6">
                    <h3 class="font-bold text-gray-900 mb-4">🎯 Giá bán cuối cùng</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-primary-700 mb-1 lg:mb-2">
                                Giá người lớn <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="adult_price" id="adult_price" required min="0" step="1000"
                                value="<?= $old['adult_price'] ?? '' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent font-bold text-accent text-lg"
                                oninput="validatePricing()">
                            <button type="button" onclick="applySuggestedPrice()"
                                class="mt-2 text-sm text-blue-600 hover:underline">
                                <i data-lucide="sparkles" class="w-4 h-4 inline-block mr-1"></i>Dùng giá đề xuất
                            </button>
                            <p class="text-xs text-gray-500 mt-1">Giá áp dụng cho khách > 12 tuổi</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-primary-700 mb-1 lg:mb-2">Giá trẻ em</label>
                            <input type="number" name="child_price" id="child_price" min="0" step="1000"
                                value="<?= $old['child_price'] ?? '' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent">
                            <p class="text-xs text-gray-500 mt-1">Thường bằng 70-80% giá người lớn</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-primary-700 mb-1 lg:mb-2">Giá em bé</label>
                            <input type="number" name="infant_price" id="infant_price" min="0" step="1000"
                                value="<?= $old['infant_price'] ?? '0' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent">
                            <p class="text-xs text-gray-500 mt-1">Dưới 2 tuổi</p>
                        </div>
                    </div>
                </div>

                <!-- Fixed Costs Input -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                    <h3 class="font-bold text-yellow-900 mb-4">💼 Chi phí cố định (chia đều cho số người)</h3>
                    <p class="text-sm text-yellow-700 mb-4">
                        Nhập các chi phí cố định cho tour (lương HDV, quản lý, marketing...).
                        Hệ thống sẽ tự động chia đều cho số người tối thiểu để tính chi phí/người.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Lương HDV (Hướng dẫn viên)
                            </label>
                            <input type="number" name="fixed_cost_guide" id="fixed_cost_guide" min="0" step="10000"
                                value="<?= $old['fixed_cost_guide'] ?? '0' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-yellow-500"
                                onchange="updatePricing()" placeholder="VD: 2000000">
                            <p class="text-xs text-gray-500 mt-1">Tổng lương cho HDV trong tour</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Chi phí quản lý
                            </label>
                            <input type="number" name="fixed_cost_management" id="fixed_cost_management" min="0"
                                step="10000" value="<?= $old['fixed_cost_management'] ?? '0' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-yellow-500"
                                onchange="updatePricing()" placeholder="VD: 1500000">
                            <p class="text-xs text-gray-500 mt-1">Chi phí quản lý tour</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Chi phí marketing
                            </label>
                            <input type="number" name="fixed_cost_marketing" id="fixed_cost_marketing" min="0"
                                step="10000" value="<?= $old['fixed_cost_marketing'] ?? '0' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-yellow-500"
                                onchange="updatePricing()" placeholder="VD: 500000">
                            <p class="text-xs text-gray-500 mt-1">Chi phí marketing, quảng cáo</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Chi phí khác
                            </label>
                            <input type="number" name="fixed_cost_other" id="fixed_cost_other" min="0" step="10000"
                                value="<?= $old['fixed_cost_other'] ?? '0' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-yellow-500"
                                onchange="updatePricing()" placeholder="VD: 0">
                            <p class="text-xs text-gray-500 mt-1">Các chi phí khác (nếu có)</p>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-yellow-100 rounded text-sm text-yellow-800">
                        <strong>💡 Lưu ý:</strong> Chi phí cố định sẽ được chia đều cho <span
                            id="min-participants-display" class="font-bold">15</span> người (số người tối thiểu).
                        Chi phí cố định/người = Tổng chi phí cố định ÷ Số người tối thiểu.
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

        <!-- FOOTER ACTIONS - Responsive -->
        <div class="bg-primary-50 px-4 lg:px-6 py-3 lg:py-4 flex flex-col sm:flex-row justify-between gap-3 border-t border-primary-100">
            <button type="button" id="prevBtn"
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base hidden flex items-center justify-center gap-2"
                onclick="changeStep(-1)">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </button>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <a href="?act=admin&module=tours"
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center">
                    Hủy
                </a>
                <button type="button" id="nextBtn"
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2" onclick="changeStep(1)">
                    Tiếp theo
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
                <button type="submit" id="submitBtn" form="tourForm"
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-success hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base hidden flex items-center justify-center gap-2"
                    onclick="console.log('🔵 Button onclick triggered!'); return true;">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Hoàn tất & Lưu
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
    const oldDayServices = <?= json_encode(is_array($day_services_by_day) ? $day_services_by_day : []) ?>;
    const oldHighlights = <?= json_encode(is_array($old_highlights) ? $old_highlights : []) ?>;
    const oldIncludes = <?= json_encode(is_array($old_includes) ? $old_includes : []) ?>;
    const oldExcludes = <?= json_encode(is_array($old_excludes) ? $old_excludes : []) ?>;
    const selectedPolicyIds = <?= json_encode(is_array($selected_policy_ids) ? $selected_policy_ids : []) ?>;

    // Debug: Log services và serviceProviders để kiểm tra
    console.log('Services loaded:', services);
    console.log('ServiceProviders loaded:', serviceProviders);
    console.log('Services count:', Array.isArray(services) ? services.length : Object.keys(services || {}).length);
    console.log('ServiceProviders count:', Array.isArray(serviceProviders) ? serviceProviders.length : Object.keys(serviceProviders || {}).length);

    // Global variables for wizard navigation - MUST be declared before functions
    var currentStep = 1;
    var totalSteps = 6;
    var currentItineraryTab = 'overview';
    var currentDayServicesDay = null;
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
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            const isLastStep = step === totalSteps;
            submitBtn.classList.toggle('hidden', !isLastStep);
            console.log(`📊 Step ${step}/${totalSteps}, Submit button ${isLastStep ? 'visible' : 'hidden'}`);

            // Đảm bảo button không bị disabled khi hiện
            if (isLastStep) {
                submitBtn.disabled = false;
            }
        }

        // Special handling for each step
        if (step === 2) {
            // Wait a bit for DOM to be ready before generating itinerary
            setTimeout(() => {
                generateItineraryDays();
            }, 200);
        }
        if (step === 3) {
            initIncludedExcluded();
        }
        if (step === 4) {
            // Restore policy IDs from session when entering step 4
            restorePolicyIdsFromSession();
        }
        if (step === 6) {
            updatePricing();
            updateMinParticipantsDisplay();
        }

        // Update URL with step parameter để lưu trạng thái
        const url = new URL(window.location);
        url.searchParams.set('step', step);
        window.history.pushState({}, '', url);
    }

    function changeStep(direction) {
        if (direction === 1 && !validateStep(currentStep)) return;

        // Auto-save form data to session before changing step
        if (direction === 1) {
            saveFormDataToSession();
        }

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

        // Special validation for step 2 (Itinerary) - Bắt buộc phải có mô tả cho mỗi ngày
        if (step === 2) {
            const durationDays = parseInt(document.getElementById('duration_days')?.value || 0);
            if (durationDays > 0) {
                const missingDescriptionDays = [];
                for (let day = 1; day <= durationDays; day++) {
                    const textarea = document.getElementById(`itinerary-description-day-${day}`);
                    let description = '';

                    // Get content from TinyMCE if initialized
                    if (typeof tinymce !== 'undefined' && tinymce.get(`itinerary-description-day-${day}`)) {
                        description = tinymce.get(`itinerary-description-day-${day}`).getContent();
                    } else if (textarea) {
                        description = textarea.value;
                    }

                    // Check if description is empty (remove HTML tags for validation)
                    const textContent = description.replace(/<[^>]*>/g, '').trim();
                    if (!textContent) {
                        missingDescriptionDays.push(day);
                    }
                }

                if (missingDescriptionDays.length > 0) {
                    isValid = false;
                    errorMessage = `Vui lòng nhập mô tả lịch trình cho các ngày: ${missingDescriptionDays.join(', ')}.`;
                    // Highlight các ngày thiếu mô tả
                    missingDescriptionDays.forEach(day => {
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
        // Timeline tab đã bị xóa - không còn sử dụng
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

        // Save TinyMCE content before replacing HTML
        const savedContents = {};
        for (let i = 1; i <= days; i++) {
            const textareaId = `itinerary-description-day-${i}`;
            const editor = tinymce.get(textareaId);
            if (editor) {
                savedContents[i] = editor.getContent();
                // Remove TinyMCE instance before replacing HTML
                editor.remove();
            }
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
                    <?php
                    // Use TinyMCE component for itinerary description
                    $dayNum = '${i}';
                    $content = '${escapeHtml(oldDay.description || \'\')}';
                    ?>
                    <textarea name="itinerary_description[]" 
                              id="itinerary-description-day-${i}"
                              rows="6"
                              class="w-full px-3 py-2 border rounded tinymce-editor"
                              placeholder="Mô tả chi tiết lịch trình ngày ${i}...">${escapeHtml(oldDay.description || '')}</textarea>
                </div>
                
                <!-- Day Services Section -->
                <div class="mt-4 border-t pt-4">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-semibold text-gray-700">
                            <i class="fas fa-concierge-bell mr-2 text-green-500"></i>Dịch vụ theo ngày
                        </h4>
                        <button type="button" onclick="openAddServiceModal(${i})"
                            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 text-sm">
                            <i class="fas fa-plus mr-2"></i>Thêm dịch vụ
                        </button>
                    </div>
                    
                    <div id="day-services-list-day-${i}" class="space-y-3">
                        <div class="text-gray-500 text-center py-4 bg-gray-50 rounded border-2 border-dashed">
                            <i class="fas fa-concierge-bell text-2xl mb-2"></i>
                            <p class="text-sm">Chưa có dịch vụ nào</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-green-900">Tổng Day ${i}:</span>
                            <span id="day-total-${i}" class="text-xl font-bold text-green-700">0đ/người</span>
                        </div>
                        <p class="text-xs text-green-600 mt-1">(Chỉ tính các dịch vụ được đánh dấu "Bao gồm trong giá")</p>
                    </div>
                </div>
            </div>
        `;

            timelineOptions += `<option value="${i}">Day ${i}</option>`;
            servicesOptions += `<option value="${i}">Day ${i}</option>`;
        }

        container.innerHTML = html;

        // Initialize TinyMCE for each itinerary description textarea
        // Wait for DOM to be ready and TinyMCE to be loaded
        const initAllTinyMCE = () => {
            if (typeof tinymce === 'undefined') {
                console.log('Waiting for TinyMCE to load...');
                setTimeout(initAllTinyMCE, 200);
                return;
            }

            console.log('✅ TinyMCE is ready, initializing editors for', days, 'days...');
            for (let i = 1; i <= days; i++) {
                // Add delay between each initialization to avoid conflicts
                setTimeout(() => {
                    initTinyMCEForItinerary(i, savedContents[i]);
                }, i * 100);
            }

            // After all TinyMCE initialized, restore day services from session
            setTimeout(() => {
                restoreDayServicesFromSession();
            }, (days * 100) + 500);
        };

        // Start initialization after a short delay
        setTimeout(initAllTinyMCE, 500);
    }

    // Initialize TinyMCE for itinerary description
    function initTinyMCEForItinerary(dayNumber, savedContent = null) {
        const textareaId = `itinerary-description-day-${dayNumber}`;
        const textarea = document.getElementById(textareaId);

        if (!textarea) {
            console.warn(`⏳ Textarea not found: ${textareaId}, retrying...`);
            setTimeout(() => initTinyMCEForItinerary(dayNumber, savedContent), 200);
            return;
        }

        // Wait for TinyMCE to be available
        if (typeof tinymce === 'undefined') {
            console.warn('⏳ TinyMCE not loaded yet, retrying...');
            setTimeout(() => initTinyMCEForItinerary(dayNumber, savedContent), 300);
            return;
        }

        // Check if TinyMCE is already initialized for this textarea
        const existingEditor = tinymce.get(textareaId);
        if (existingEditor) {
            console.log(`✅ TinyMCE already initialized for ${textareaId}`);
            // Restore saved content if available
            if (savedContent) {
                existingEditor.setContent(savedContent);
            }
            return;
        }

        // Make sure textarea is a real DOM element
        if (!(textarea instanceof HTMLElement)) {
            console.error(`❌ Textarea is not a valid DOM element: ${textareaId}`);
            setTimeout(() => initTinyMCEForItinerary(dayNumber, savedContent), 200);
            return;
        }

        console.log(`🚀 Initializing TinyMCE for ${textareaId}`);

        // Initialize TinyMCE - Use selector with # prefix (CSS selector)
        try {
            tinymce.init({
                selector: '#' + textareaId,
                license_key: 'gpl', // GPL license for open source projects
                height: 400,
                menubar: false,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | formatselect | ' +
                    'bold italic underline strikethrough | forecolor backcolor | ' +
                    'alignleft aligncenter alignright alignjustify | ' +
                    'bullist numlist | outdent indent | ' +
                    'removeformat | image link | code | fullscreen | help',
                content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; }',
                // language: 'vi', // Comment out if vi.js not found, will use English
                images_upload_url: '?act=admin&module=tours&action=uploadImage',
                automatic_uploads: true,
                file_picker_types: 'image',
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true,
                branding: false,
                promotion: false,
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save();
                    });
                    editor.on('init', function () {
                        console.log(`✅ TinyMCE initialized successfully for ${textareaId}`);
                        // Restore saved content if available
                        if (savedContent) {
                            editor.setContent(savedContent);
                            console.log(`📝 Restored content for ${textareaId}`);
                        } else {
                            // BUG FIX: Nếu không có savedContent (khi validation fail và reload form),
                            // lấy từ textarea value (đã có oldDay.description từ PHP)
                            const textareaValue = textarea.value || '';
                            if (textareaValue.trim()) {
                                editor.setContent(textareaValue);
                                console.log(`📝 Restored content from textarea for ${textareaId}`);
                            }
                        }
                    });
                    editor.on('error', function (e) {
                        console.error(`❌ TinyMCE error for ${textareaId}:`, e);
                    });
                }
            });
        } catch (error) {
            console.error(`❌ Failed to initialize TinyMCE for ${textareaId}:`, error);
        }
    }

    // Deprecated: loadItineraryManagerForDay - không dùng nữa
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

    // Timeline editor đã bị xóa - không còn sử dụng

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
        // Calculate pricing based on day services only
        // Fixed costs removed
        updateTotalCost();
    }

    function updateTotalCost() {
        // Tính tổng chi phí từ tất cả day services
        const durationDays = parseInt(document.getElementById('duration_days')?.value || 0);
        let totalServiceCost = 0;
        const dayBreakdown = [];

        // Duyệt qua tất cả các ngày
        for (let day = 1; day <= durationDays; day++) {
            const container = document.getElementById(`day-services-list-day-${day}`);
            if (!container) continue;

            const items = container.querySelectorAll('.day-service-item');
            let dayTotal = 0;
            const dayServices = [];

            items.forEach(item => {
                const checkbox = item.querySelector('[name^="day_service_included"]');
                if (checkbox && checkbox.checked) {
                    const unitPrice = parseFloat(item.querySelector('[name="day_service_unit_price[]"]')?.value || 0);
                    const quantity = parseFloat(item.querySelector('[name="day_service_quantity[]"]')?.value || 1);
                    const serviceName = item.querySelector('[name="day_service_name[]"]')?.value || 'Dịch vụ';
                    const cost = unitPrice * quantity;
                    dayTotal += cost;

                    if (cost > 0) {
                        dayServices.push({
                            name: serviceName,
                            cost: cost
                        });
                    }
                }
            });

            totalServiceCost += dayTotal;

            if (dayTotal > 0) {
                dayBreakdown.push({
                    day: day,
                    total: dayTotal,
                    services: dayServices
                });
            }
        }

        // Hiển thị service cost
        const serviceCostEl = document.getElementById('service-cost-per-person');
        if (serviceCostEl) {
            serviceCostEl.textContent = formatCurrency(totalServiceCost);
        }

        // Hiển thị breakdown theo ngày
        const dayBreakdownEl = document.getElementById('day-breakdown');
        if (dayBreakdownEl) {
            if (dayBreakdown.length > 0) {
                let html = '<div class="mt-2 space-y-1">';
                dayBreakdown.forEach(item => {
                    html += `<div class="text-gray-600">Ngày ${item.day}: ${formatCurrency(item.total)}</div>`;
                });
                html += '</div>';
                dayBreakdownEl.innerHTML = html;
            } else {
                dayBreakdownEl.innerHTML = '<div class="text-gray-400 italic">Chưa có dịch vụ nào được thêm</div>';
            }
        }

        // Tính chi phí cố định/người
        const fixedCostGuide = parseFloat(document.getElementById('fixed_cost_guide')?.value || 0);
        const fixedCostManagement = parseFloat(document.getElementById('fixed_cost_management')?.value || 0);
        const fixedCostMarketing = parseFloat(document.getElementById('fixed_cost_marketing')?.value || 0);
        const fixedCostOther = parseFloat(document.getElementById('fixed_cost_other')?.value || 0);
        const minParticipants = parseInt(document.getElementById('min_participants')?.value || 15);

        const totalFixedCost = fixedCostGuide + fixedCostManagement + fixedCostMarketing + fixedCostOther;
        const fixedCostPerPerson = minParticipants > 0 ? totalFixedCost / minParticipants : 0;

        // Hiển thị chi phí cố định/người
        const fixedCostEl = document.getElementById('fixed-cost-per-person');
        if (fixedCostEl) {
            fixedCostEl.textContent = formatCurrency(fixedCostPerPerson);
        }

        // Hiển thị breakdown chi phí cố định
        const fixedCostBreakdownEl = document.getElementById('fixed-cost-breakdown');
        if (fixedCostBreakdownEl) {
            if (totalFixedCost > 0) {
                let html = '<div class="mt-1 space-y-1">';
                if (fixedCostGuide > 0) {
                    html += `<div>Lương HDV: ${formatCurrency(fixedCostGuide)} ÷ ${minParticipants} = ${formatCurrency(fixedCostGuide / minParticipants)}</div>`;
                }
                if (fixedCostManagement > 0) {
                    html += `<div>Quản lý: ${formatCurrency(fixedCostManagement)} ÷ ${minParticipants} = ${formatCurrency(fixedCostManagement / minParticipants)}</div>`;
                }
                if (fixedCostMarketing > 0) {
                    html += `<div>Marketing: ${formatCurrency(fixedCostMarketing)} ÷ ${minParticipants} = ${formatCurrency(fixedCostMarketing / minParticipants)}</div>`;
                }
                if (fixedCostOther > 0) {
                    html += `<div>Khác: ${formatCurrency(fixedCostOther)} ÷ ${minParticipants} = ${formatCurrency(fixedCostOther / minParticipants)}</div>`;
                }
                html += '</div>';
                fixedCostBreakdownEl.innerHTML = html;
            } else {
                fixedCostBreakdownEl.innerHTML = '<div class="text-gray-400 italic">Chưa nhập chi phí cố định</div>';
            }
        }

        // Cập nhật min participants display
        const minParticipantsDisplayEl = document.getElementById('min-participants-display');
        if (minParticipantsDisplayEl) {
            minParticipantsDisplayEl.textContent = minParticipants;
        }

        // Tổng chi phí = chi phí dịch vụ + chi phí cố định
        const total = totalServiceCost + fixedCostPerPerson;
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

    function updateMinParticipantsDisplay() {
        const minParticipants = parseInt(document.getElementById('min_participants')?.value || 15);
        const minParticipantsDisplayEl = document.getElementById('min-participants-display');
        if (minParticipantsDisplayEl) {
            minParticipantsDisplayEl.textContent = minParticipants;
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

        // Restore policy IDs from session if on step 4
        if (initialStep === 4) {
            setTimeout(() => {
                restorePolicyIdsFromSession();
            }, 500);
        }

        // If step 2, generate itinerary days
        if (initialStep === 2 || (oldItinerary && oldItinerary.length > 0)) {
            // Wait a bit for page to be fully loaded
            setTimeout(() => {
                generateItineraryDays();
                // Restore day services from session after generating itinerary
                restoreDayServicesFromSession();
            }, 500);
        }

        // Restore day services if already on step 2
        if (initialStep === 2) {
            setTimeout(() => {
                restoreDayServicesFromSession();
            }, 1000);
        }

        // Event delegation for itinerary manager
        initItineraryManagerEvents();

        // Handle main form submit - Save TinyMCE content before submit
        const tourForm = document.getElementById('tourForm');
        const submitBtn = document.getElementById('submitBtn');

        if (tourForm) {
            console.log('✅ Form submit handler attached to #tourForm');

            // Also attach click handler to button directly - BUT DON'T PREVENT DEFAULT
            if (submitBtn) {
                console.log('✅ Submit button found, attaching click handler');
                submitBtn.addEventListener('click', function (e) {
                    console.log('🔵 Submit button clicked!', {
                        type: e.target.type,
                        formId: tourForm.id,
                        currentStep: currentStep,
                        totalSteps: totalSteps,
                        disabled: e.target.disabled,
                        hidden: e.target.classList.contains('hidden')
                    });

                    // Check if button is disabled
                    if (e.target.disabled) {
                        console.warn('⚠️ Button is disabled!');
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }

                    // Check if button is hidden
                    if (e.target.classList.contains('hidden')) {
                        console.warn('⚠️ Button is hidden!');
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }

                    // Save TinyMCE content before validation
                    if (typeof tinymce !== 'undefined') {
                        tinymce.triggerSave();
                        console.log('✅ TinyMCE content saved on button click');
                    }

                    // Don't use checkValidity() because it checks hidden required fields in modals
                    // We'll validate manually in form submit handler
                    console.log('✅ Button click passed, allowing form to submit');
                    // DON'T preventDefault - let the form submit naturally
                    // The form submit event handler will handle validation and submission
                }, false);
            } else {
                console.error('❌ Submit button not found!');
            }

            // Use capture phase to ensure this runs first
            tourForm.addEventListener('submit', function (e) {
                console.log('🚀 Form submit event triggered!', {
                    formId: e.target.id,
                    currentStep: currentStep,
                    totalSteps: totalSteps,
                    submitter: e.submitter?.id || 'unknown'
                });

                // Save all TinyMCE editors before submit - CRITICAL!
                if (typeof tinymce !== 'undefined') {
                    // Save all editors
                    tinymce.triggerSave();

                    // Double check: manually save each itinerary editor
                    const durationDays = parseInt(document.getElementById('duration_days')?.value || 0);
                    for (let i = 1; i <= durationDays; i++) {
                        const editor = tinymce.get(`itinerary-description-day-${i}`);
                        if (editor) {
                            editor.save(); // Save to textarea
                            const content = editor.getContent();
                            const textarea = document.getElementById(`itinerary-description-day-${i}`);
                            if (textarea) {
                                textarea.value = content;
                                console.log(`✅ Saved TinyMCE content for day ${i}:`, content.substring(0, 50) + '...');
                            }
                        }
                    }

                    console.log('✅ All TinyMCE content saved to textareas');
                } else {
                    console.warn('⚠️ TinyMCE not available!');
                }

                // Save to session one last time before submit (synchronous if possible)
                console.log('💾 Saving to session before submit...');
                // Note: saveFormDataToSession is async, but we'll let it run
                saveFormDataToSession();

                // Validate required fields
                const name = document.getElementById('name')?.value?.trim();
                console.log('📝 Validating name:', name);
                if (!name) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('Vui lòng nhập tên tour');
                    showStep(1);
                    return false;
                }

                const durationDays = parseInt(document.getElementById('duration_days')?.value || 0);
                console.log('📝 Validating duration_days:', durationDays);
                if (durationDays <= 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('Vui lòng nhập số ngày hợp lệ');
                    showStep(1);
                    return false;
                }

                const adultPrice = parseFloat(document.getElementById('adult_price')?.value || 0);
                console.log('📝 Validating adult_price:', adultPrice);
                if (adultPrice <= 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('Vui lòng nhập giá người lớn');
                    showStep(6);
                    return false;
                }

                console.log('✅ All validations passed, submitting form...');

                // Show loading
                const submitBtn = document.getElementById('submitBtn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang lưu...';
                    console.log('✅ Submit button disabled and loading state set');
                } else {
                    console.warn('⚠️ Submit button not found!');
                }

                // Allow form to submit normally - DON'T preventDefault
                console.log('✅ Form will submit now - allowing default behavior');

                // Mark form as submitting to prevent double submission
                tourForm.setAttribute('data-submitting', '1');
                tourForm.setAttribute('data-submitted', '1');

                // Log that we're about to submit
                console.log('📤 Form is submitting to:', tourForm.action);

                return true; // Allow form to submit
            }, false); // Use bubble phase (default)
        } else {
            console.error('❌ Form #tourForm not found!');
        }
    });

    // ============================================================================
    // ITINERARY MANAGER EVENT DELEGATION
    // ============================================================================

    function initItineraryManagerEvents() {
        // Use event delegation for all itinerary manager actions
        document.addEventListener('click', function (e) {
            const action = e.target.closest('[data-action]')?.getAttribute('data-action');
            const dayNumber = e.target.closest('[data-action]')?.getAttribute('data-day');

            if (!action || !dayNumber) return;

            switch (action) {
                case 'toggle-day-manager':
                    toggleDayManager(parseInt(dayNumber));
                    break;
                case 'open-service-modal':
                    openAddServiceModal(parseInt(dayNumber));
                    break;
                case 'close-service-modal':
                    closeAddServiceModal(parseInt(dayNumber));
                    break;
            }
        });

        // Form submissions - CHỈ xử lý form con (modal forms), KHÔNG chặn form chính
        document.addEventListener('submit', function (e) {
            const form = e.target;
            const action = form.getAttribute('data-action');
            const dayNumber = form.getAttribute('data-day');
            const formId = form.id || '';

            console.log('📋 Form submit event caught:', { action, dayNumber, formId, isMainForm: formId === 'tourForm' });

            // Nếu là form chính (tourForm), KHÔNG xử lý ở đây, để handler riêng xử lý
            if (formId === 'tourForm') {
                console.log('✅ Main form submit - letting main handler process');
                return; // Let the main form submit handler process it
            }

            // Chỉ xử lý form con (modal forms) có data-action
            if (!action || !dayNumber) {
                console.log('⚠️ No action or dayNumber, allowing default submit');
                return; // Allow default submit for other forms
            }

            e.preventDefault();
            e.stopPropagation();
            console.log('🛑 Prevented default for modal form, calling handler for:', action);

            switch (action) {
                case 'save-service':
                    console.log('Calling saveDayService');
                    saveDayService(e, parseInt(dayNumber));
                    break;
                default:
                    console.warn('Unknown action:', action);
            }
        });
    }

    // Initialize counters
    // Timeline counter đã bị xóa - không còn sử dụng
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

    function openAddServiceModal(dayNumber) {
        console.log('openAddServiceModal called for day', dayNumber);
        console.log('Services available:', services);

        // Create modal if not exists
        let modal = document.getElementById(`add-service-modal-day-${dayNumber}`);
        if (!modal) {
            console.log('Creating new modal for day', dayNumber);
            createServiceModal(dayNumber);
            modal = document.getElementById(`add-service-modal-day-${dayNumber}`);
        }

        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';

            // Debug: Check if service select has options
            const serviceSelect = document.getElementById(`modal-service-id-day-${dayNumber}`);
            if (serviceSelect) {
                console.log('Service select found, options count:', serviceSelect.options.length);
                if (serviceSelect.options.length <= 1) {
                    console.warn('⚠️ Service select has no options! Rebuilding...');
                    // Rebuild options
                    serviceSelect.innerHTML = buildServiceOptions(dayNumber);
                }
            } else {
                console.error('❌ Service select not found!');
            }
        } else {
            console.error('❌ Modal not found after creation!');
        }
    }

    function buildServiceOptions(dayNumber) {
        // Get services from global services list
        let options = '<option value="">-- Chọn dịch vụ --</option>';
        if (typeof services !== 'undefined' && services) {
            // Check if services is array or object
            if (Array.isArray(services)) {
                // services is array: [ {id: 1, name: '...'}, {id: 2, name: '...'} ]
                services.forEach(service => {
                    if (service && service.id) {
                        const id = service.id;
                        const name = service.name || 'N/A';
                        options += `<option value="${id}" data-name="${escapeHtml(name)}">${escapeHtml(name)}</option>`;
                    }
                });
            } else {
                // services is object: { 1: {name: '...'}, 2: {name: '...'} }
                Object.entries(services).forEach(([id, service]) => {
                    const name = typeof service === 'object' ? (service.name || service) : service;
                    options += `<option value="${id}" data-name="${escapeHtml(name)}">${escapeHtml(name)}</option>`;
                });
            }
        }
        console.log('buildServiceOptions: Generated', options.split('<option').length - 1, 'options');
        return options;
    }

    function buildServiceProviderOptions(dayNumber) {
        // Get service providers from global list
        let options = '<option value="">-- Chọn nhà dịch vụ --</option>';
        if (typeof serviceProviders !== 'undefined' && serviceProviders) {
            // Check if serviceProviders is array or object
            if (Array.isArray(serviceProviders)) {
                // serviceProviders is array: [ {id: 1, name: '...'}, {id: 2, name: '...'} ]
                serviceProviders.forEach(provider => {
                    if (provider && provider.id) {
                        const id = provider.id;
                        const name = provider.name || 'N/A';
                        options += `<option value="${id}">${escapeHtml(name)}</option>`;
                    }
                });
            } else {
                // serviceProviders is object: { 1: {name: '...'}, 2: {name: '...'} }
                Object.entries(serviceProviders).forEach(([id, provider]) => {
                    const name = typeof provider === 'object' ? (provider.name || provider) : provider;
                    options += `<option value="${id}">${escapeHtml(name)}</option>`;
                });
            }
        }
        console.log('buildServiceProviderOptions: Generated', options.split('<option').length - 1, 'options');
        return options;
    }

    function createServiceModal(dayNumber) {
        console.log('createServiceModal called for day', dayNumber);
        const serviceOptions = buildServiceOptions(dayNumber);
        const providerOptions = buildServiceProviderOptions(dayNumber);

        console.log('Service options generated:', serviceOptions.split('<option').length - 1, 'options');
        console.log('Provider options generated:', providerOptions.split('<option').length - 1, 'options');

        const modalHtml = `
            <div id="add-service-modal-day-${dayNumber}" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center p-4">
                <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6 border-b">
                        <h3 class="text-lg font-bold text-gray-800">Thêm dịch vụ - Day ${dayNumber}</h3>
                    </div>
                    <form id="add-service-form-day-${dayNumber}" class="p-6" data-action="save-service" data-day="${dayNumber}">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Chọn dịch vụ <span class="text-red-500">*</span></label>
                                <select id="modal-service-id-day-${dayNumber}"
                                    class="w-full px-3 py-2 border rounded focus:border-green-500"
                                    onchange="loadServiceInfoForDay(${dayNumber})">
                                    ${serviceOptions}
                                </select>
                                <p class="text-xs text-gray-500 mt-1" id="service-select-debug-day-${dayNumber}"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Chọn nhà dịch vụ</label>
                                <select id="modal-service-provider-id-day-${dayNumber}"
                                    class="w-full px-3 py-2 border rounded focus:border-green-500">
                                    ${providerOptions}
                                </select>
                                <p class="text-xs text-gray-500 mt-1" id="provider-select-debug-day-${dayNumber}"></p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Đơn giá/người <span class="text-red-500">*</span></label>
                                    <input type="number" id="modal-unit-price-day-${dayNumber}" step="1000" min="0"
                                        class="w-full px-3 py-2 border rounded focus:border-green-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng</label>
                                    <input type="number" id="modal-quantity-day-${dayNumber}" step="0.01" min="0.01" value="1"
                                        class="w-full px-3 py-2 border rounded focus:border-green-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị</label>
                                <input type="text" id="modal-unit-day-${dayNumber}" placeholder="VD: bữa, đêm, vé"
                                    class="w-full px-3 py-2 border rounded focus:border-green-500">
                            </div>
                            <div>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" id="modal-included-day-${dayNumber}" checked
                                        class="w-5 h-5 text-green-600 rounded">
                                    <span class="text-sm font-medium text-gray-700">Bao gồm trong giá tour</span>
                                </label>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                                <textarea id="modal-notes-day-${dayNumber}" rows="3"
                                    class="w-full px-3 py-2 border rounded focus:border-green-500"
                                    placeholder="Ghi chú thêm..."></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" onclick="closeAddServiceModal(${dayNumber})"
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                Hủy
                            </button>
                            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                Thêm dịch vụ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    // Removed duplicate buildServiceOptions and buildServiceProviderOptions functions
    // Using the ones defined earlier (lines 1501-1548)

    function loadServiceInfoForDay(dayNumber) {
        const select = document.getElementById(`modal-service-id-day-${dayNumber}`);
        const serviceId = select?.value;
        if (!serviceId) {
            // Reset providers dropdown nếu không chọn service
            const providerSelect = document.getElementById(`modal-service-provider-id-day-${dayNumber}`);
            if (providerSelect) {
                providerSelect.innerHTML = '<option value="">-- Chọn nhà dịch vụ --</option>';
            }
            return;
        }

        // Show loading
        const providerSelect = document.getElementById(`modal-service-provider-id-day-${dayNumber}`);
        if (providerSelect) {
            providerSelect.innerHTML = '<option value="">Đang tải...</option>';
            providerSelect.disabled = true;
        }

        // Get tour start date để load giá theo mùa (nếu có)
        const tourStartDate = document.getElementById('duration_days')?.closest('form')?.querySelector('[name="start_date"]')?.value || null;
        let url = `?act=admin&module=tours&action=getServiceInfo&id=${serviceId}`;
        if (tourStartDate) {
            url += `&date=${tourStartDate}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    const priceInput = document.getElementById(`modal-unit-price-day-${dayNumber}`);
                    const unitInput = document.getElementById(`modal-unit-day-${dayNumber}`);

                    // Auto-fill price và unit
                    if (priceInput && data.data.unit_price > 0) {
                        priceInput.value = data.data.unit_price;
                    }
                    if (unitInput && data.data.unit) {
                        unitInput.value = data.data.unit;
                    }

                    // Update providers dropdown - chỉ hiển thị providers của service này
                    if (providerSelect && data.data.providers) {
                        let options = '<option value="">-- Chọn nhà dịch vụ --</option>';
                        data.data.providers.forEach(provider => {
                            const selected = (data.data.service_provider_id && provider.id == data.data.service_provider_id) ? 'selected' : '';
                            options += `<option value="${provider.id}" ${selected}>${escapeHtml(provider.name)}</option>`;
                        });
                        providerSelect.innerHTML = options;
                        providerSelect.disabled = false;

                        // Auto-select provider nếu service chỉ có 1 provider
                        if (data.data.providers.length === 1) {
                            providerSelect.value = data.data.providers[0].id;
                        }
                    } else {
                        // Nếu không có providers, reset dropdown
                        if (providerSelect) {
                            providerSelect.innerHTML = '<option value="">-- Không có nhà dịch vụ --</option>';
                            providerSelect.disabled = false;
                        }
                    }
                } else {
                    console.error('Error loading service info:', data.message || 'Unknown error');
                    if (providerSelect) {
                        providerSelect.innerHTML = '<option value="">-- Lỗi tải dữ liệu --</option>';
                        providerSelect.disabled = false;
                    }
                }
            })
            .catch(err => {
                console.error('Error loading service info:', err);
                if (providerSelect) {
                    providerSelect.innerHTML = '<option value="">-- Lỗi tải dữ liệu --</option>';
                    providerSelect.disabled = false;
                }
            });
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

    function saveDayService(event, dayNumber) {
        console.log('saveDayService called for day', dayNumber);

        // Prevent default form submission
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        // Initialize counter if needed
        if (!window.dayServiceCounter) {
            window.dayServiceCounter = {};
        }
        if (!window.dayServiceCounter[dayNumber]) {
            window.dayServiceCounter[dayNumber] = 0;
        }

        const counter = window.dayServiceCounter[dayNumber] = window.dayServiceCounter[dayNumber] + 1;
        const container = document.getElementById(`day-services-list-day-${dayNumber}`);
        if (!container) {
            console.error('Container not found:', `day-services-list-day-${dayNumber}`);
            alert('❌ Không tìm thấy container day-services-list-day-' + dayNumber);
            return;
        }

        console.log('Container found, counter:', counter);

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
                        <button type="button" onclick="removeDayServiceItem(this, ${dayNumber})" class="text-red-500 hover:text-red-700">
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

        // Lưu dịch vụ vào session
        saveDayServiceToSession(dayNumber);
    }

    /**
     * Lưu dịch vụ theo ngày vào session
     */
    function saveDayServiceToSession(dayNumber) {
        const container = document.getElementById(`day-services-list-day-${dayNumber}`);
        if (!container) return;

        const items = container.querySelectorAll('.day-service-item');
        const dayServices = [];

        items.forEach(item => {
            const dayNum = item.querySelector('[name="day_service_day_number[]"]')?.value || dayNumber;
            const serviceId = item.querySelector('[name="day_service_service_id[]"]')?.value;
            const providerId = item.querySelector('[name="day_service_provider_id[]"]')?.value || '';
            const serviceName = item.querySelector('[name="day_service_name[]"]')?.value || '';
            const unitPrice = parseFloat(item.querySelector('[name="day_service_unit_price[]"]')?.value || 0);
            const quantity = parseFloat(item.querySelector('[name="day_service_quantity[]"]')?.value || 1);
            const unit = item.querySelector('[name="day_service_unit[]"]')?.value || '';
            const notes = item.querySelector('[name="day_service_notes[]"]')?.value || '';
            const included = item.querySelector('[name^="day_service_included"]')?.checked || false;

            if (serviceId) {
                dayServices.push({
                    day_number: parseInt(dayNum),
                    service_id: parseInt(serviceId),
                    service_provider_id: providerId ? parseInt(providerId) : null,
                    service_name: serviceName,
                    unit_price: unitPrice,
                    quantity: quantity,
                    unit: unit,
                    is_included_in_price: included ? 1 : 0,
                    notes: notes
                });
            }
        });

        // Lưu vào session qua AJAX
        saveFormDataToSession({
            itinerary_day_services: {
                [dayNumber]: dayServices
            }
        });
    }

    /**
     * Restore policy IDs from session data
     */
    function restorePolicyIdsFromSession() {
        if (!selectedPolicyIds || !Array.isArray(selectedPolicyIds) || selectedPolicyIds.length === 0) {
            console.log('No policy IDs to restore from session');
            return;
        }

        console.log('📋 Restoring policy IDs from session:', selectedPolicyIds);

        // Wait a bit for policy selector to be ready
        setTimeout(() => {
            selectedPolicyIds.forEach(policyId => {
                const checkbox = document.getElementById(`policy-${policyId}`);
                if (checkbox && !checkbox.checked) {
                    // Get policy name and type from the card
                    const policyCard = checkbox.closest('.policy-card');
                    if (policyCard) {
                        const policyName = policyCard.querySelector('.font-medium')?.textContent?.trim() || '';
                        const policyTypeText = policyCard.closest('.policy-type-group')?.querySelector('h5')?.textContent?.trim() || 'Khác';
                        // Trigger toggle to add to list
                        checkbox.checked = true;
                        if (typeof togglePolicy === 'function') {
                            togglePolicy(checkbox, policyId, policyName, policyTypeText);
                        }
                    }
                }
            });
            console.log('✅ Policy IDs restored from session');
        }, 300);
    }

    /**
     * Restore day services from session data
     */
    function restoreDayServicesFromSession() {
        if (!oldDayServices) {
            console.log('No oldDayServices data to restore');
            return;
        }

        console.log('Restoring day services from session:', oldDayServices);

        // oldDayServices có thể là:
        // 1. Object: { 1: [...], 2: [...] }
        // 2. Array: [{day_number: 1, ...}, {day_number: 2, ...}]

        let servicesByDay = {};

        if (Array.isArray(oldDayServices)) {
            // Format: [{day_number: 1, ...}, {day_number: 2, ...}]
            oldDayServices.forEach(service => {
                const dayNum = service.day_number || 1;
                if (!servicesByDay[dayNum]) {
                    servicesByDay[dayNum] = [];
                }
                servicesByDay[dayNum].push(service);
            });
        } else if (typeof oldDayServices === 'object' && oldDayServices !== null) {
            // Format: { 1: [...], 2: [...] }
            servicesByDay = oldDayServices;
        } else {
            console.warn('Invalid oldDayServices format:', typeof oldDayServices);
            return;
        }

        // Restore services for each day
        Object.keys(servicesByDay).forEach(dayNumber => {
            const dayServices = servicesByDay[dayNumber];
            if (Array.isArray(dayServices) && dayServices.length > 0) {
                const container = document.getElementById(`day-services-list-day-${dayNumber}`);
                if (!container) {
                    console.warn('Container not found for day', dayNumber, '- will retry later');
                    // Retry after a delay
                    setTimeout(() => {
                        restoreDayServicesFromSession();
                    }, 1000);
                    return;
                }

                // Clear container (only if it has placeholder)
                if (container.innerHTML.includes('Chưa có dịch vụ')) {
                    container.innerHTML = '';
                }

                // Initialize counter for this day
                if (!window.dayServiceCounter) {
                    window.dayServiceCounter = {};
                }
                window.dayServiceCounter[dayNumber] = dayServices.length;

                // Add each service
                dayServices.forEach((service, index) => {
                    const counter = index + 1;
                    const serviceHtml = `
                        <div class="day-service-item bg-white border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 mt-1">
                                    <input type="checkbox" name="day_service_included[${dayNumber}][${index + 1}]" value="1"
                                        ${service.is_included_in_price ? 'checked' : ''}
                                        onchange="updateDayServiceTotal(${dayNumber})"
                                        class="w-5 h-5 text-blue-600 rounded">
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-800">
                                        ${escapeHtml(service.service_name || 'N/A')}
                                    </div>
                                    <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                        <div>
                                            <span class="text-gray-600">Đơn giá/người:</span>
                                            <span class="font-medium ml-2">${formatCurrency(service.unit_price || 0)}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Số lượng:</span>
                                            <span class="font-medium ml-2">${formatNumber(service.quantity || 1)} ${service.unit || ''}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Tổng:</span>
                                            <span class="font-medium text-blue-600 ml-2">${formatCurrency((service.unit_price || 0) * (service.quantity || 1))}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Bao gồm:</span>
                                            <span class="font-medium ml-2 ${service.is_included_in_price ? 'text-green-600' : 'text-gray-400'}">
                                                ${service.is_included_in_price ? 'Có' : 'Không'}
                                            </span>
                                        </div>
                                    </div>
                                    ${service.notes ? `
                                        <div class="mt-2 text-sm text-gray-500">
                                            <i class="fas fa-sticky-note mr-1"></i>
                                            ${escapeHtml(service.notes)}
                                        </div>
                                    ` : ''}
                                    <input type="hidden" name="day_service_day_number[]" value="${dayNumber}">
                                    <input type="hidden" name="day_service_service_id[]" value="${service.service_id || ''}">
                                    <input type="hidden" name="day_service_provider_id[]" value="${service.service_provider_id || ''}">
                                    <input type="hidden" name="day_service_name[]" value="${escapeHtml(service.service_name || '')}">
                                    <input type="hidden" name="day_service_unit_price[]" value="${service.unit_price || 0}">
                                    <input type="hidden" name="day_service_quantity[]" value="${service.quantity || 1}">
                                    <input type="hidden" name="day_service_unit[]" value="${escapeHtml(service.unit || '')}">
                                    <input type="hidden" name="day_service_notes[]" value="${escapeHtml(service.notes || '')}">
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" onclick="removeDayServiceItem(this, ${dayNumber})" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', serviceHtml);
                });

                // Update total
                updateDayServiceTotal(parseInt(dayNumber));
            }
        });
    }

    /**
     * Lưu toàn bộ form data vào session
     */
    function saveFormDataToSession(additionalData = {}) {
        // Collect all form data
        const formData = {
            // Basic info
            name: document.getElementById('name')?.value || '',
            introduction: document.querySelector('[name="introduction"]')?.value || '',
            description: document.querySelector('[name="description"]')?.value || '',
            duration_days: parseInt(document.getElementById('duration_days')?.value || 0),
            duration_nights: parseInt(document.getElementById('duration_nights')?.value || 0),
            departure_location: document.querySelector('[name="departure_location"]')?.value || '',
            min_participants: parseInt(document.getElementById('min_participants')?.value || 15),
            max_participants: parseInt(document.querySelector('[name="max_participants"]')?.value || 45),
            adult_price: parseFloat(document.getElementById('adult_price')?.value || 0),
            child_price: parseFloat(document.getElementById('child_price')?.value || 0),
            infant_price: parseFloat(document.getElementById('infant_price')?.value || 0),
            deposit_percentage: parseFloat(document.querySelector('[name="deposit_percentage"]')?.value || 30),
            booking_deadline_days: parseInt(document.querySelector('[name="booking_deadline_days"]')?.value || 1),
            // Fixed costs
            fixed_cost_guide: parseFloat(document.getElementById('fixed_cost_guide')?.value || 0),
            fixed_cost_management: parseFloat(document.getElementById('fixed_cost_management')?.value || 0),
            fixed_cost_marketing: parseFloat(document.getElementById('fixed_cost_marketing')?.value || 0),
            fixed_cost_other: parseFloat(document.getElementById('fixed_cost_other')?.value || 0),
            status: document.querySelector('[name="status"]')?.value || 'draft',
            tour_type: document.querySelector('[name="tour_type"]')?.value || 'public'
        };

        // Collect itinerary data (with TinyMCE content)
        const itinerary = [];
        const durationDays = formData.duration_days || 0;
        for (let i = 1; i <= durationDays; i++) {
            const dayNumber = i;
            let description = '';

            // Get TinyMCE content if available
            if (typeof tinymce !== 'undefined') {
                const editor = tinymce.get(`itinerary-description-day-${dayNumber}`);
                if (editor) {
                    description = editor.getContent();
                } else {
                    const textarea = document.getElementById(`itinerary-description-day-${dayNumber}`);
                    if (textarea) {
                        description = textarea.value;
                    }
                }
            } else {
                const textarea = document.getElementById(`itinerary-description-day-${dayNumber}`);
                if (textarea) {
                    description = textarea.value;
                }
            }

            // Get title and destination for this specific day
            // Find the itinerary day item for this day number
            const dayItem = document.querySelector(`.itinerary-day-item[data-day="${dayNumber}"]`);
            let title = '';
            let destinationId = '';

            if (dayItem) {
                const titleInput = dayItem.querySelector(`input[name="itinerary_title[]"]`);
                const destSelect = dayItem.querySelector(`select[name="itinerary_destination[]"]`);
                title = titleInput?.value || '';
                destinationId = destSelect?.value || '';
            }

            itinerary.push({
                day_number: dayNumber,
                title: title,
                description: description,
                destination_id: destinationId ? parseInt(destinationId) : null
            });
        }

        // Collect day services for all days
        const itineraryDayServices = [];
        const durationDaysForServices = formData.duration_days || 0;
        for (let i = 1; i <= durationDaysForServices; i++) {
            const container = document.getElementById(`day-services-list-day-${i}`);
            if (!container) continue;

            const items = container.querySelectorAll('.day-service-item');
            items.forEach(item => {
                const dayNum = item.querySelector('[name="day_service_day_number[]"]')?.value || i;
                const serviceId = item.querySelector('[name="day_service_service_id[]"]')?.value;
                const providerId = item.querySelector('[name="day_service_provider_id[]"]')?.value || '';
                const serviceName = item.querySelector('[name="day_service_name[]"]')?.value || '';
                const unitPrice = parseFloat(item.querySelector('[name="day_service_unit_price[]"]')?.value || 0);
                const quantity = parseFloat(item.querySelector('[name="day_service_quantity[]"]')?.value || 1);
                const unit = item.querySelector('[name="day_service_unit[]"]')?.value || '';
                const notes = item.querySelector('[name="day_service_notes[]"]')?.value || '';
                const included = item.querySelector('[name^="day_service_included"]')?.checked || false;

                if (serviceId) {
                    itineraryDayServices.push({
                        day_number: parseInt(dayNum),
                        service_id: parseInt(serviceId),
                        service_provider_id: providerId ? parseInt(providerId) : null,
                        service_name: serviceName,
                        unit_price: unitPrice,
                        quantity: quantity,
                        unit: unit,
                        is_included_in_price: included ? 1 : 0,
                        notes: notes
                    });
                }
            });
        }

        // Collect highlights, included, excluded
        const highlights = document.getElementById('highlights')?.value || '';
        const included = Array.from(document.querySelectorAll('[name="included[]"]')).map(input => input.value).filter(v => v);
        const excluded = Array.from(document.querySelectorAll('[name="excluded[]"]')).map(input => input.value).filter(v => v);

        // Collect policy IDs
        const policyIds = Array.from(document.querySelectorAll('[name="policy_ids[]"]:checked')).map(cb => parseInt(cb.value));

        // Prepare data to send
        const dataToSave = {
            form_data: formData,
            itinerary: itinerary,
            itinerary_day_services: itineraryDayServices, // ✅ Lưu tất cả day services
            highlights: highlights ? highlights.split('\n').filter(h => h.trim()) : [],
            included: included,
            excluded: excluded,
            policy_ids: policyIds,
            ...additionalData // Merge additional data (override nếu có)
        };

        // Save to session via AJAX
        fetch('?act=admin&module=tours&action=saveFormSession', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(dataToSave)
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    console.log('✅ Form data saved to session');
                } else {
                    console.error('❌ Failed to save to session:', data.message);
                }
            })
            .catch(err => {
                console.error('❌ Error saving to session:', err);
            });
    }

    function removeDayServiceItem(button, dayNumber) {
        if (confirm('Bạn có chắc muốn xóa dịch vụ này?')) {
            button.closest('.day-service-item').remove();
            updateDayServiceTotal(dayNumber);
            // Lưu lại vào session sau khi xóa
            saveDayServiceToSession(dayNumber);
        }
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

        // Cập nhật pricing breakdown nếu đang ở tab 6
        if (currentStep === 6) {
            updateTotalCost();
        }
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('vi-VN', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(num);
    }
</script>