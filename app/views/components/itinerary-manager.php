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

        <!-- DAY SERVICES SECTION -->
        <div class="border-t pt-4">
            <div class="flex justify-between items-center mb-4">
                <h4 class="font-semibold text-gray-700">
                    <i class="fas fa-concierge-bell mr-2 text-green-500"></i>Dịch vụ theo ngày
                </h4>
                <button type="button" data-action="open-service-modal" data-day="<?= $day_number ?>"
                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 text-sm">
                    <i class="fas fa-plus mr-2"></i>Thêm dịch vụ
                </button>
            </div>

            <!-- Services List -->
            <div id="day-services-list-day-<?= $day_number ?>" class="space-y-3">
                <?php if (empty($day_services)): ?>
                    <div class="text-gray-500 text-center py-4 bg-gray-50 rounded border-2 border-dashed">
                        <i class="fas fa-concierge-bell text-2xl mb-2"></i>
                        <p class="text-sm">Chưa có dịch vụ nào</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($day_services as $idx => $service): ?>
                        <div class="day-service-item bg-white border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 mt-1">
                                    <input type="checkbox" name="day_service_included[<?= $day_number ?>][<?= $idx ?>]" value="1"
                                        <?= ($service['is_included_in_price'] ?? 1) ? 'checked' : '' ?>
                                        onchange="updateDayServiceTotal(<?= $day_number ?>)" 
                                        class="w-5 h-5 text-blue-600 rounded">
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-800">
                                        <?= htmlspecialchars($service['service_name'] ?? 'N/A') ?>
                                        <?php if (!empty($service['service_provider_name'])): ?>
                                            <span class="text-sm text-gray-500">- <?= htmlspecialchars($service['service_provider_name']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                        <div>
                                            <span class="text-gray-600">Đơn giá:</span>
                                            <span class="font-medium ml-2"><?= number_format($service['unit_price'] ?? 0, 0, ',', '.') ?>đ</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Số lượng:</span>
                                            <span class="font-medium ml-2"><?= number_format($service['quantity'] ?? 1, 2, ',', '.') ?> <?= htmlspecialchars($service['unit'] ?? '') ?></span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Tổng:</span>
                                            <span class="font-medium text-blue-600 ml-2"><?= number_format(($service['unit_price'] ?? 0) * ($service['quantity'] ?? 1), 0, ',', '.') ?>đ</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Bao gồm:</span>
                                            <span class="font-medium ml-2 <?= ($service['is_included_in_price'] ?? 1) ? 'text-green-600' : 'text-gray-400' ?>">
                                                <?= ($service['is_included_in_price'] ?? 1) ? 'Có' : 'Không' ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php if (!empty($service['notes'])): ?>
                                        <div class="mt-2 text-sm text-gray-500">
                                            <i class="fas fa-sticky-note mr-1"></i><?= htmlspecialchars($service['notes']) ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Hidden fields -->
                                    <input type="hidden" name="day_service_day_number[]" value="<?= $day_number ?>">
                                    <input type="hidden" name="day_service_service_id[]" value="<?= $service['service_id'] ?? '' ?>">
                                    <input type="hidden" name="day_service_provider_id[]" value="<?= $service['service_provider_id'] ?? '' ?>">
                                    <input type="hidden" name="day_service_name[]" value="<?= htmlspecialchars($service['service_name'] ?? '') ?>">
                                    <input type="hidden" name="day_service_unit_price[]" value="<?= $service['unit_price'] ?? 0 ?>">
                                    <input type="hidden" name="day_service_quantity[]" value="<?= $service['quantity'] ?? 1 ?>">
                                    <input type="hidden" name="day_service_unit[]" value="<?= htmlspecialchars($service['unit'] ?? '') ?>">
                                    <input type="hidden" name="day_service_notes[]" value="<?= htmlspecialchars($service['notes'] ?? '') ?>">
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="button" onclick="removeDayService(this)" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Day Services Total -->
            <?php
            $day_total = 0;
            foreach ($day_services as $service) {
                if ($service['is_included_in_price'] ?? 1) {
                    $day_total += ($service['unit_price'] ?? 0) * ($service['quantity'] ?? 1);
                }
            }
            ?>
            <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-green-900">Tổng Day <?= $day_number ?>:</span>
                    <span id="day-total-<?= $day_number ?>" class="text-xl font-bold text-green-700">
                        <?= number_format($day_total, 0, ',', '.') ?>đ/người
                    </span>
                </div>
                <p class="text-xs text-green-600 mt-1">(Chỉ tính các dịch vụ được đánh dấu "Bao gồm trong giá")</p>
            </div>
        </div>
    </div>
</div>

<!-- Add Timeline Modal -->
<div id="add-timeline-modal-day-<?= $day_number ?>" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[9999]">
    <div class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
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
                            onchange="promptAddToDayServices(this, <?= $day_number ?>)">
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
</div>

<!-- Add Service Modal -->
<div id="add-service-modal-day-<?= $day_number ?>" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[9999]">
    <div class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b">
            <h3 class="text-lg font-bold text-gray-800">Thêm dịch vụ - Day <?= $day_number ?></h3>
        </div>
        <form id="add-service-form-day-<?= $day_number ?>" class="p-6" data-action="save-service" data-day="<?= $day_number ?>">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chọn dịch vụ <span class="text-red-500">*</span></label>
                    <select id="modal-service-id-day-<?= $day_number ?>" required
                        class="w-full px-3 py-2 border rounded focus:border-green-500"
                        onchange="loadServiceInfo(this, <?= $day_number ?>)">
                        <option value="">-- Chọn dịch vụ --</option>
                        <?php foreach ($services as $service): ?>
                            <?php 
                            $service_id = is_array($service) ? $service['id'] : null;
                            $service_name = is_array($service) ? $service['name'] : $service;
                            ?>
                            <option value="<?= $service_id ?>" data-name="<?= htmlspecialchars($service_name) ?>">
                                <?= htmlspecialchars($service_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chọn nhà dịch vụ</label>
                    <select id="modal-service-provider-id-day-<?= $day_number ?>"
                        class="w-full px-3 py-2 border rounded focus:border-green-500">
                        <option value="">-- Chọn nhà dịch vụ --</option>
                        <?php foreach ($service_providers as $provider): ?>
                            <option value="<?= $provider['id'] ?>"><?= htmlspecialchars($provider['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Đơn giá/người <span class="text-red-500">*</span></label>
                        <input type="number" id="modal-unit-price-day-<?= $day_number ?>" step="1000" min="0" required
                            class="w-full px-3 py-2 border rounded focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng</label>
                        <input type="number" id="modal-quantity-day-<?= $day_number ?>" step="0.01" min="0.01" value="1"
                            class="w-full px-3 py-2 border rounded focus:border-green-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị</label>
                    <input type="text" id="modal-unit-day-<?= $day_number ?>" placeholder="VD: bữa, đêm, vé"
                        class="w-full px-3 py-2 border rounded focus:border-green-500">
                </div>
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="modal-included-day-<?= $day_number ?>" checked
                            class="w-5 h-5 text-green-600 rounded">
                        <span class="text-sm font-medium text-gray-700">Bao gồm trong giá tour</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                    <textarea id="modal-notes-day-<?= $day_number ?>" rows="3"
                        class="w-full px-3 py-2 border rounded focus:border-green-500"
                        placeholder="Ghi chú thêm..."></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" data-action="close-service-modal" data-day="<?= $day_number ?>"
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
