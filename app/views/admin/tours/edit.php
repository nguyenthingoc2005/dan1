<?php
/**
 * ADMIN - FORM SỬA TOUR
 * Variables: $tour, $categories, $destinations, $services, $tour_services
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">Sửa Tour</h1>
            <p class="text-sm text-gray-500">
                <?= htmlspecialchars($tour['tour_code']) ?> - <?= htmlspecialchars($tour['name']) ?>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="?act=admin&module=tours&action=show&id=<?= $tour['id'] ?>" class="px-4 py-2 bg-white border rounded hover:bg-gray-50">Xem chi tiết</a>
            <a href="?act=admin&module=tours" class="text-gray-500 hover:text-gray-700">← Danh sách</a>
        </div>
    </div>

    <form method="POST" action="?act=admin&module=tours&action=update" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="id" value="<?= $tour['id'] ?>">
        <input type="hidden" name="tour_type" value="<?= $tour['tour_type'] ?>">

        <!-- Section 1: Thông tin cơ bản -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b">
                <h2 class="font-bold text-gray-800">1. Thông tin cơ bản</h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tên Tour <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="<?= htmlspecialchars($tour['name']) ?>" required
                            class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Điểm khởi hành</label>
                        <input type="text" name="departure_location" value="<?= htmlspecialchars($tour['departure_location']) ?>"
                            class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số ngày <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_days" id="duration_days" min="1" required
                            value="<?= $tour['duration_days'] ?>"
                            class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số đêm</label>
                        <input type="number" name="duration_nights" min="0" value="<?= $tour['duration_nights'] ?>"
                            class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giới thiệu ngắn</label>
                        <textarea name="introduction" rows="2" class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none" placeholder="Giới thiệu ngắn gọn về tour..."><?= htmlspecialchars($tour['introduction'] ?? '') ?></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả chi tiết</label>
                        <textarea name="description" rows="4" class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none"><?= htmlspecialchars($tour['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Số lượng khách & Đặt cọc -->
                <div class="mt-5 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h4 class="font-medium text-blue-800 mb-3">📊 Số lượng khách & Đặt cọc</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số khách tối thiểu</label>
                            <input type="number" name="min_participants" min="1" value="<?= $tour['min_participants'] ?? 10 ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent text-center">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số khách tối đa</label>
                            <input type="number" name="max_participants" min="1" value="<?= $tour['max_participants'] ?? 45 ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent text-center">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tỷ lệ đặt cọc</label>
                            <div class="flex items-center">
                                <input type="number" name="deposit_percentage" min="0" max="100" value="<?= $tour['deposit_percentage'] ?? 30 ?>"
                                    class="w-full px-3 py-2 border rounded-l focus:border-accent text-center">
                                <span class="px-3 py-2 bg-gray-100 border border-l-0 rounded-r text-sm text-gray-600">%</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hạn đặt tour (ngày)</label>
                            <div class="flex items-center">
                                <input type="number" name="booking_deadline_days" min="0" value="<?= $tour['booking_deadline_days'] ?? 1 ?>"
                                    class="w-full px-3 py-2 border rounded-l focus:border-accent text-center">
                                <span class="px-3 py-2 bg-gray-100 border border-l-0 rounded-r text-sm text-gray-600">ngày</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Số ngày trước khi khởi hành phải đặt tour</p>
                        </div>
                    </div>
                </div>

                <!-- Chi phí cố định -->
                <div class="mt-5 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <h4 class="font-medium text-yellow-900 mb-3">💼 Chi phí cố định (chia đều cho số người)</h4>
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
                                value="<?= $tour['fixed_cost_guide'] ?? '0' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-yellow-500" placeholder="VD: 2000000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Chi phí quản lý
                            </label>
                            <input type="number" name="fixed_cost_management" id="fixed_cost_management" min="0"
                                step="10000" value="<?= $tour['fixed_cost_management'] ?? '0' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-yellow-500" placeholder="VD: 1500000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Chi phí marketing
                            </label>
                            <input type="number" name="fixed_cost_marketing" id="fixed_cost_marketing" min="0"
                                step="10000" value="<?= $tour['fixed_cost_marketing'] ?? '0' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-yellow-500" placeholder="VD: 500000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Chi phí khác
                            </label>
                            <input type="number" name="fixed_cost_other" id="fixed_cost_other" min="0" step="10000"
                                value="<?= $tour['fixed_cost_other'] ?? '0' ?>"
                                class="w-full px-3 py-2 border rounded focus:border-yellow-500" placeholder="VD: 0">
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-yellow-100 rounded text-sm text-yellow-800">
                        <strong>💡 Lưu ý:</strong> Chi phí cố định sẽ được chia đều cho <span
                            id="min-participants-display" class="font-bold"><?= $tour['min_participants'] ?? 15 ?></span> người (số người tối thiểu).
                        Chi phí cố định/người = Tổng chi phí cố định ÷ Số người tối thiểu.
                    </div>
                </div>

                <!-- Trạng thái -->
                <div class="mt-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" class="w-full md:w-1/2 px-3 py-2 border rounded focus:border-accent focus:outline-none">
                        <option value="draft" <?= $tour['status'] == 'draft' ? 'selected' : '' ?>>Bản nháp (Draft)</option>
                        <option value="active" <?= $tour['status'] == 'active' ? 'selected' : '' ?>>Hoạt động (Active)</option>
                        <option value="inactive" <?= $tour['status'] == 'inactive' ? 'selected' : '' ?>>Tạm dừng (Inactive)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 2: Giá bán -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b">
                <h2 class="font-bold text-gray-800">2. Giá bán</h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá người lớn <span class="text-red-500">*</span></label>
                        <input type="number" name="adult_price" required min="0" value="<?= $tour['adult_price'] ?>"
                            class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none font-bold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá trẻ em</label>
                        <input type="number" name="child_price" min="0" value="<?= $tour['child_price'] ?>"
                            class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá em bé</label>
                        <input type="number" name="infant_price" min="0" value="<?= $tour['infant_price'] ?>"
                            class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Lịch trình -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b flex justify-between items-center">
                <h2 class="font-bold text-gray-800">3. Lịch trình</h2>
                <button type="button" onclick="addItineraryDay()" class="text-sm text-accent hover:underline">+ Thêm ngày</button>
            </div>
            <div class="p-5" id="itinerary-container">
                <?php if (!empty($tour['itinerary'])): ?>
                    <?php foreach ($tour['itinerary'] as $item): ?>
                        <?php 
                        $day_num = $item['day_number'];
                        $day_services = $tour['day_services_by_day'][$day_num] ?? [];
                        ?>
                        <div class="bg-gray-50 p-4 rounded border mb-3 itinerary-item" data-day="<?= $day_num ?>">
                            <input type="hidden" name="itinerary_day_number[]" value="<?= $day_num ?>">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="w-8 h-8 bg-accent text-white rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0"><?= $day_num ?></span>
                                <input type="text" name="itinerary_title[]" value="<?= htmlspecialchars($item['title']) ?>" placeholder="Tiêu đề..."
                                    class="flex-1 px-3 py-2 border rounded focus:border-accent focus:outline-none">
                                <select name="itinerary_destination[]" class="px-3 py-2 border rounded focus:border-accent focus:outline-none w-48">
                                    <option value="">-- Điểm đến --</option>
                                    <?php foreach ($destinations as $id => $name): ?>
                                        <option value="<?= $id ?>" <?= $item['destination_id'] == $id ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" onclick="this.closest('.itinerary-item').remove()" class="text-red-500 hover:text-red-700">✕</button>
                            </div>
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả lịch trình</label>
                                <textarea name="itinerary_description[]" 
                                    id="itinerary-description-day-<?= $day_num ?>"
                                    rows="6"
                                    class="w-full px-3 py-2 border rounded tinymce-editor"
                                    placeholder="Mô tả chi tiết lịch trình ngày <?= $day_num ?>..."><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
                            </div>
                            
                            <!-- Dịch vụ theo ngày -->
                            <div class="mt-4 border-t pt-4">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-semibold text-gray-700">
                                        <i class="fas fa-concierge-bell mr-2 text-green-500"></i>Dịch vụ theo ngày
                                    </h4>
                                    <button type="button" onclick="openAddServiceModal(<?= $day_num ?>)"
                                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 text-sm">
                                        <i class="fas fa-plus mr-2"></i>Thêm dịch vụ
                                    </button>
                                </div>
                                
                                <div id="day-services-list-day-<?= $day_num ?>" class="space-y-3">
                                    <?php if (!empty($day_services)): ?>
                                        <?php foreach ($day_services as $service): ?>
                                            <div class="bg-white p-3 rounded border border-green-200 day-service-item" data-service-id="<?= $service['id'] ?? '' ?>">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <div class="font-medium text-gray-800"><?= htmlspecialchars($service['service_name'] ?? 'N/A') ?></div>
                                                        <div class="text-sm text-gray-600 mt-1">
                                                            Giá: <?= number_format($service['unit_price'] ?? 0) ?>đ / <?= htmlspecialchars($service['unit'] ?? 'đơn vị') ?>
                                                            × <?= $service['quantity'] ?? 1 ?>
                                                            <?= ($service['is_included_in_price'] ?? 0) ? '<span class="text-green-600 ml-2">✓ Bao gồm trong giá</span>' : '' ?>
                                                        </div>
                                                        <?php if (!empty($service['notes'])): ?>
                                                            <div class="text-xs text-gray-500 mt-1">Ghi chú: <?= htmlspecialchars($service['notes']) ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <button type="button" onclick="removeDayService(this)" class="text-red-500 hover:text-red-700 ml-2">✕</button>
                                                </div>
                                                <!-- Hidden inputs -->
                                                <input type="hidden" name="day_service_day_number[]" value="<?= $day_num ?>">
                                                <input type="hidden" name="day_service_service_id[]" value="<?= $service['service_id'] ?? '' ?>">
                                                <input type="hidden" name="day_service_provider_id[]" value="<?= $service['service_provider_id'] ?? '' ?>">
                                                <input type="hidden" name="day_service_name[]" value="<?= htmlspecialchars($service['service_name'] ?? '') ?>">
                                                <input type="hidden" name="day_service_unit_price[]" value="<?= $service['unit_price'] ?? 0 ?>">
                                                <input type="hidden" name="day_service_quantity[]" value="<?= $service['quantity'] ?? 1 ?>">
                                                <input type="hidden" name="day_service_unit[]" value="<?= htmlspecialchars($service['unit'] ?? '') ?>">
                                                <input type="hidden" name="day_service_included[]" value="<?= ($service['is_included_in_price'] ?? 0) ? '1' : '' ?>">
                                                <input type="hidden" name="day_service_notes[]" value="<?= htmlspecialchars($service['notes'] ?? '') ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-gray-500 text-center py-4 bg-gray-50 rounded border-2 border-dashed">
                                            <i class="fas fa-concierge-bell text-2xl mb-2"></i>
                                            <p class="text-sm">Chưa có dịch vụ nào</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section 4: Bao gồm / Không bao gồm -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b">
                <h2 class="font-bold text-gray-800">4. Bao gồm / Không bao gồm</h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Included -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-bold text-green-800">✅ Giá tour BAO GỒM</h3>
                            <button type="button" onclick="addIncludedItem()" class="text-sm text-green-600 hover:underline">+ Thêm</button>
                        </div>
                        <div id="included-container" class="space-y-2">
                            <?php if (!empty($tour['includes'])): ?>
                                <?php foreach ($tour['includes'] as $item): ?>
                                    <div class="flex items-center gap-2">
                                        <span class="text-green-600">✓</span>
                                        <input type="text" name="included[]" value="<?= htmlspecialchars($item) ?>"
                                            class="flex-1 px-3 py-2 border rounded focus:border-green-500 focus:outline-none text-sm">
                                        <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600">✕</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Excluded -->
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-bold text-red-800">❌ Giá tour KHÔNG BAO GỒM</h3>
                            <button type="button" onclick="addExcludedItem()" class="text-sm text-red-600 hover:underline">+ Thêm</button>
                        </div>
                        <div id="excluded-container" class="space-y-2">
                            <?php if (!empty($tour['excludes'])): ?>
                                <?php foreach ($tour['excludes'] as $item): ?>
                                    <div class="flex items-center gap-2">
                                        <span class="text-red-600">✗</span>
                                        <input type="text" name="excluded[]" value="<?= htmlspecialchars($item) ?>"
                                            class="flex-1 px-3 py-2 border rounded focus:border-red-500 focus:outline-none text-sm">
                                        <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600">✕</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Chính sách -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b">
                <h2 class="font-bold text-gray-800">5. Chính sách</h2>
            </div>
            <div class="p-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Chọn các chính sách áp dụng cho tour</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <?php if (!empty($policies)): ?>
                        <?php foreach ($policies as $policy): ?>
                            <label class="flex items-start gap-2 p-3 border rounded hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="policy_ids[]" value="<?= $policy['id'] ?>"
                                    <?= in_array($policy['id'], $tour['policy_ids'] ?? []) ? 'checked' : '' ?>
                                    class="mt-1">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-800"><?= htmlspecialchars($policy['name']) ?></div>
                                    <?php if (!empty($policy['description'])): ?>
                                        <div class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($policy['description']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-sm">Chưa có chính sách nào. <a href="?act=admin&module=policies&action=create" class="text-accent hover:underline">Tạo mới</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Section 6: Hình ảnh & Highlights -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b">
                <h2 class="font-bold text-gray-800">6. Hình ảnh & Điểm nổi bật</h2>
            </div>
            <div class="p-5 space-y-5">
                <!-- Current Images -->
                <?php if (!empty($tour['images'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh hiện tại</label>
                        <div class="grid grid-cols-4 md:grid-cols-6 gap-3">
                            <?php foreach ($tour['images'] as $img): ?>
                                <div class="relative aspect-square rounded overflow-hidden border <?= $img['is_primary'] ? 'border-2 border-accent' : '' ?>">
                                    <img src="<?= htmlspecialchars($img['image_url']) ?>" class="w-full h-full object-cover">
                                    <?php if ($img['is_primary']): ?>
                                        <span class="absolute top-1 right-1 bg-accent text-white text-[9px] px-1 rounded">Main</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Upload New -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thêm ảnh mới</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:bg-gray-50"
                        onclick="document.getElementById('images').click()">
                        <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden" onchange="previewImages(this)">
                        <div class="text-gray-400 text-3xl mb-2">📷</div>
                        <p class="text-gray-500 text-sm">Click để thêm ảnh</p>
                    </div>
                    <div id="image-preview" class="grid grid-cols-5 gap-3 mt-3"></div>
                </div>

                <!-- Highlights -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Điểm nổi bật</label>
                    <textarea name="highlights" rows="4" placeholder="Mỗi dòng một điểm nổi bật..."
                        class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none"><?= htmlspecialchars(implode("\n", $tour['highlights'] ?? [])) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-3">
            <a href="?act=admin&module=tours&action=show&id=<?= $tour['id'] ?>" class="px-6 py-2 border rounded hover:bg-gray-50">Hủy</a>
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600 font-medium">
                ✓ Cập nhật Tour
            </button>
        </div>
    </form>
</div>

<script>
    const destinations = <?= json_encode($destinations) ?>;
    const services = <?= json_encode($services ?? []) ?>;
    const serviceProviders = <?= json_encode($service_providers ?? []) ?>;
    let dayCount = <?= count($tour['itinerary'] ?? []) ?>;

    // Initialize TinyMCE for existing textareas
    document.addEventListener('DOMContentLoaded', function() {
        // Wait for TinyMCE to load from admin_layout.php
        const initAllTinyMCE = () => {
            if (typeof tinymce === 'undefined') {
                setTimeout(initAllTinyMCE, 100);
                return;
            }
            
            // Initialize TinyMCE for all itinerary description textareas
            const textareas = document.querySelectorAll('.tinymce-editor');
            textareas.forEach(textarea => {
                if (!tinymce.get(textarea.id)) {
                    tinymce.init({
                        selector: '#' + textarea.id,
                        license_key: 'gpl',
                        height: 300,
                        menubar: false,
                        plugins: ['advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen', 'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'],
                        toolbar: 'undo redo | formatselect | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | removeformat | image link | code | fullscreen | help',
                        content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; }',
                        branding: false,
                        promotion: false
                    });
                }
            });
        };
        
        initAllTinyMCE();
    });

    function addItineraryDay() {
        dayCount++;
        const container = document.getElementById('itinerary-container');
        let destOptions = '<option value="">-- Điểm đến --</option>';
        for (const [id, name] of Object.entries(destinations)) {
            destOptions += `<option value="${id}">${name}</option>`;
        }

        const html = `
            <div class="bg-gray-50 p-4 rounded border mb-3 itinerary-item" data-day="${dayCount}">
                <input type="hidden" name="itinerary_day_number[]" value="${dayCount}">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-8 h-8 bg-accent text-white rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">${dayCount}</span>
                    <input type="text" name="itinerary_title[]" placeholder="Tiêu đề..."
                        class="flex-1 px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    <select name="itinerary_destination[]" class="px-3 py-2 border rounded focus:border-accent focus:outline-none w-48">
                        ${destOptions}
                    </select>
                    <button type="button" onclick="this.closest('.itinerary-item').remove()" class="text-red-500 hover:text-red-700">✕</button>
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả lịch trình</label>
                    <textarea name="itinerary_description[]" 
                        id="itinerary-description-day-${dayCount}"
                        rows="6"
                        class="w-full px-3 py-2 border rounded tinymce-editor"
                        placeholder="Mô tả chi tiết lịch trình ngày ${dayCount}..."></textarea>
                </div>
                <div class="mt-4 border-t pt-4">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-semibold text-gray-700">
                            <i class="fas fa-concierge-bell mr-2 text-green-500"></i>Dịch vụ theo ngày
                        </h4>
                        <button type="button" onclick="openAddServiceModal(${dayCount})"
                            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 text-sm">
                            <i class="fas fa-plus mr-2"></i>Thêm dịch vụ
                        </button>
                    </div>
                    <div id="day-services-list-day-${dayCount}" class="space-y-3">
                        <div class="text-gray-500 text-center py-4 bg-gray-50 rounded border-2 border-dashed">
                            <i class="fas fa-concierge-bell text-2xl mb-2"></i>
                            <p class="text-sm">Chưa có dịch vụ nào</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        
        // Initialize TinyMCE for the new textarea
        const initNewTinyMCE = () => {
            if (typeof tinymce === 'undefined') {
                setTimeout(initNewTinyMCE, 100);
                return;
            }
            
            tinymce.init({
                selector: '#itinerary-description-day-' + dayCount,
                license_key: 'gpl',
                    height: 300,
                    menubar: false,
                    plugins: ['advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen', 'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'],
                    toolbar: 'undo redo | formatselect | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | removeformat | image link | code | fullscreen | help',
                    content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; }',
                    branding: false,
                    promotion: false
                });
        };
        
        setTimeout(initNewTinyMCE, 100);
    }

    function openAddServiceModal(dayNumber) {
        // Load day services editor via AJAX (sử dụng endpoint có sẵn)
        fetch(`?act=admin&module=tours&action=loadDayServicesEditor&day=${dayNumber}&tour_id=<?= $tour['id'] ?>`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hiển thị modal hoặc load vào container
                    // Tạm thời: redirect đến trang loadDayServicesEditor
                    window.open(`?act=admin&module=tours&action=loadDayServicesEditor&day=${dayNumber}&tour_id=<?= $tour['id'] ?>`, '_blank');
                }
            })
            .catch(error => {
                console.error('Error loading service editor:', error);
                alert('Không thể tải trình chỉnh sửa dịch vụ. Vui lòng thử lại.');
            });
    }

    function removeDayService(button) {
        if (confirm('Bạn có chắc muốn xóa dịch vụ này?')) {
            button.closest('.day-service-item').remove();
        }
    }

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
</script>
