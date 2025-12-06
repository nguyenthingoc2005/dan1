<?php
/**
 * ==============================================================================
 * ITINERARY MANAGER COMPONENT
 * ==============================================================================
 * 
 * Component quản lý lịch trình cho một ngày (bao gồm Timeline và Dịch vụ)
 * 
 * Variables:
 * - $day_number (required): Số ngày (1, 2, 3...)
 * - $timeline_items (optional): Array các timeline items
 * - $day_services (optional): Array các services
 * - $destinations (optional): Array destinations
 * - $service_providers (optional): Array service_providers
 * - $services (optional): Array services
 * 
 * @version 1.0
 * @date 2024-12-06
 * ==============================================================================
 */

$day_number = $day_number ?? 1;
$timeline_items = $timeline_items ?? [];
$day_services = $day_services ?? [];
$destinations = $destinations ?? [];
$service_providers = $service_providers ?? [];
$services = $services ?? [];

// Sort timeline by time
usort($timeline_items, function($a, $b) {
    $time_a = $a['timeline_time'] ?? '00:00:00';
    $time_b = $b['timeline_time'] ?? '00:00:00';
    return strcmp($time_a, $time_b);
});
?>

<div class="itinerary-manager bg-white border-2 border-gray-200 rounded-lg p-6 mb-6" 
     data-day="<?= $day_number ?>"
     data-timeline-count="<?= count($timeline_items) ?>"
     data-services-count="<?= count($day_services) ?>">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Ngày <?= $day_number ?></h3>
        <div class="flex gap-2">
            <button type="button" data-action="toggle-day-manager" data-day="<?= $day_number ?>"
                    class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded">
                <i class="fas fa-chevron-down" id="toggle-icon-<?= $day_number ?>"></i>
            </button>
        </div>
    </div>

    <!-- Collapsible Content -->
    <div id="day-manager-content-<?= $day_number ?>" class="space-y-6">
        
        <!-- TIMELINE SECTION -->
        <div class="border-t pt-4">
            <div class="flex justify-between items-center mb-4">
                <h4 class="font-semibold text-gray-700">
                    <i class="fas fa-clock mr-2 text-blue-500"></i>Timeline chi tiết
                </h4>
                <button type="button" data-action="open-timeline-modal" data-day="<?= $day_number ?>"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm">
                    <i class="fas fa-plus mr-2"></i>Thêm timeline
                </button>
            </div>

            <!-- Timeline Items -->
            <div id="timeline-items-day-<?= $day_number ?>" class="space-y-3">
                <?php if (empty($timeline_items)): ?>
                    <div class="text-gray-500 text-center py-4 bg-gray-50 rounded border-2 border-dashed">
                        <i class="fas fa-clock text-2xl mb-2"></i>
                        <p class="text-sm">Chưa có timeline nào</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($timeline_items as $idx => $item): ?>
                        <?php 
                        $day_number_for_item = $day_number;
                        include __DIR__ . '/timeline-item-form.php'; 
                        ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Timeline Modal -->
<div id="add-timeline-modal-day-<?= $day_number ?>" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b">
            <h3 class="text-lg font-bold text-gray-800">Thêm timeline - Day <?= $day_number ?></h3>
        </div>
        <form id="add-timeline-form-day-<?= $day_number ?>" class="p-6" data-action="save-timeline" data-day="<?= $day_number ?>">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giờ <span class="text-red-500">*</span></label>
                        <input type="time" id="modal-timeline-time-day-<?= $day_number ?>" required
                            class="w-full px-3 py-2 border rounded focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loại timeline</label>
                        <select id="modal-timeline-type-day-<?= $day_number ?>" 
                            class="w-full px-3 py-2 border rounded focus:border-blue-500">
                            <option value="activity">Hoạt động</option>
                            <option value="meal">Bữa ăn</option>
                            <option value="accommodation">Nơi nghỉ</option>
                            <option value="transport">Di chuyển</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hoạt động <span class="text-red-500">*</span></label>
                    <input type="text" id="modal-timeline-activity-day-<?= $day_number ?>" required
                        placeholder="VD: Ăn sáng, Check-in, Tham quan..."
                        class="w-full px-3 py-2 border rounded focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea id="modal-timeline-description-day-<?= $day_number ?>" rows="3"
                        class="w-full px-3 py-2 border rounded focus:border-blue-500"
                        placeholder="Mô tả chi tiết..."></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Địa điểm</label>
                        <input type="text" id="modal-timeline-location-day-<?= $day_number ?>"
                            placeholder="VD: Nhà hàng ABC"
                            class="w-full px-3 py-2 border rounded focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nhà dịch vụ</label>
                        <select id="modal-timeline-provider-day-<?= $day_number ?>"
                            class="w-full px-3 py-2 border rounded focus:border-blue-500"
                            onchange="updateLocationFromProvider(this, <?= $day_number ?>)">
                            <option value="">-- Chọn nhà dịch vụ --</option>
                            <?php foreach ($service_providers as $provider): ?>
                                <option value="<?= $provider['id'] ?>" data-address="<?= htmlspecialchars($provider['address'] ?? '') ?>">
                                    <?= htmlspecialchars($provider['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Địa điểm du lịch</label>
                        <select id="modal-timeline-destination-day-<?= $day_number ?>"
                            class="w-full px-3 py-2 border rounded focus:border-blue-500">
                            <option value="">-- Chọn địa điểm --</option>
                            <?php foreach ($destinations as $dest): ?>
                                <?php 
                                $dest_id = is_array($dest) ? $dest['id'] : null;
                                $dest_name = is_array($dest) ? $dest['name'] : $dest;
                                ?>
                                <option value="<?= $dest_id ?>"><?= htmlspecialchars($dest_name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dịch vụ</label>
                        <select id="modal-timeline-service-day-<?= $day_number ?>"
                            class="w-full px-3 py-2 border rounded focus:border-blue-500"
                            onchange="loadServiceInfoForTimeline(this, <?= $day_number ?>)">
                            <option value="">-- Chọn dịch vụ --</option>
                            <?php foreach ($services as $service): ?>
                                <?php 
                                $service_id = is_array($service) ? $service['id'] : null;
                                $service_name = is_array($service) ? $service['name'] : $service;
                                ?>
                                <option value="<?= $service_id ?>"><?= htmlspecialchars($service_name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Service Price Info (hiển thị khi chọn dịch vụ) -->
                <div id="service-price-info-day-<?= $day_number ?>" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800 mb-3">Thông tin giá dịch vụ</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Đơn giá/người</label>
                            <input type="number" id="modal-timeline-service-price-day-<?= $day_number ?>" step="1000" min="0"
                                class="w-full px-3 py-2 border rounded focus:border-blue-500"
                                placeholder="0"
                                onchange="calculateTimelineServiceTotal(<?= $day_number ?>)">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng</label>
                            <input type="number" id="modal-timeline-service-quantity-day-<?= $day_number ?>" step="0.01" min="0.01" value="1"
                                class="w-full px-3 py-2 border rounded focus:border-blue-500"
                                onchange="calculateTimelineServiceTotal(<?= $day_number ?>)">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị</label>
                            <input type="text" id="modal-timeline-service-unit-day-<?= $day_number ?>"
                                class="w-full px-3 py-2 border rounded focus:border-blue-500"
                                placeholder="VD: bữa, đêm, vé">
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between">
                        <div>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" id="modal-timeline-service-included-day-<?= $day_number ?>" checked
                                    class="w-5 h-5 text-blue-600 rounded">
                                <span class="text-sm font-medium text-gray-700">Bao gồm trong giá tour</span>
                            </label>
                        </div>
                        <div class="text-right">
                            <span class="text-sm text-gray-600">Tổng:</span>
                            <span id="modal-timeline-service-total-day-<?= $day_number ?>" class="text-lg font-bold text-blue-600 ml-2">0đ</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                    <textarea id="modal-timeline-notes-day-<?= $day_number ?>" rows="2"
                        class="w-full px-3 py-2 border rounded focus:border-blue-500"
                        placeholder="Ghi chú thêm..."></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" data-action="close-timeline-modal" data-day="<?= $day_number ?>"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Hủy
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Thêm timeline
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Data for JavaScript -->
<script type="application/json" id="itinerary-manager-data-<?= $day_number ?>">
<?= json_encode([
    'day_number' => $day_number,
    'timeline_count' => count($timeline_items),
    'services_count' => count($day_services),
    'destinations' => $destinations,
    'services' => $services,
    'service_providers' => $service_providers
]) ?>
</script>
