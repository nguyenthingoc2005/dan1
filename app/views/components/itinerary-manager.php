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

<div class="itinerary-manager bg-white border-2 border-gray-200 rounded-lg p-6 mb-6" data-day="<?= $day_number ?>">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Ngày <?= $day_number ?></h3>
        <div class="flex gap-2">
            <button type="button" onclick="toggleDayManager(<?= $day_number ?>)" 
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
                <button type="button" onclick="openAddTimelineModal(<?= $day_number ?>)"
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
                <button type="button" onclick="openAddServiceModal(<?= $day_number ?>)"
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
<div id="add-timeline-modal-day-<?= $day_number ?>" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b">
            <h3 class="text-lg font-bold text-gray-800">Thêm timeline - Day <?= $day_number ?></h3>
        </div>
        <form id="add-timeline-form-day-<?= $day_number ?>" class="p-6" onsubmit="saveTimelineItem(event, <?= $day_number ?>); return false;">
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
                <button type="button" onclick="closeAddTimelineModal(<?= $day_number ?>)"
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

<!-- Add Service Modal -->
<div id="add-service-modal-day-<?= $day_number ?>" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b">
            <h3 class="text-lg font-bold text-gray-800">Thêm dịch vụ - Day <?= $day_number ?></h3>
        </div>
        <form id="add-service-form-day-<?= $day_number ?>" class="p-6" onsubmit="saveDayService(event, <?= $day_number ?>); return false;">
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
                <button type="button" onclick="closeAddServiceModal(<?= $day_number ?>)"
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

<script>
// Initialize counters
window.timelineCounter = window.timelineCounter || {};
window.timelineCounter[<?= $day_number ?>] = <?= count($timeline_items) ?>;
window.dayServiceCounter = window.dayServiceCounter || {};
window.dayServiceCounter[<?= $day_number ?>] = <?= count($day_services) ?>;

// Toggle day manager
function toggleDayManager(dayNumber) {
    const content = document.getElementById(`day-manager-content-${dayNumber}`);
    const icon = document.getElementById(`toggle-icon-${dayNumber}`);
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

// Timeline Modal Functions
function openAddTimelineModal(dayNumber) {
    document.getElementById(`add-timeline-modal-day-${dayNumber}`).classList.remove('hidden');
}

function closeAddTimelineModal(dayNumber) {
    document.getElementById(`add-timeline-modal-day-${dayNumber}`).classList.add('hidden');
    document.getElementById(`add-timeline-form-day-${dayNumber}`).reset();
}

function saveTimelineItem(event, dayNumber) {
    event.preventDefault();
    
    const counter = window.timelineCounter[dayNumber] = (window.timelineCounter[dayNumber] || 0) + 1;
    const container = document.getElementById(`timeline-items-day-${dayNumber}`);
    
    const time = document.getElementById(`modal-timeline-time-day-${dayNumber}`).value;
    const type = document.getElementById(`modal-timeline-type-day-${dayNumber}`).value;
    const activity = document.getElementById(`modal-timeline-activity-day-${dayNumber}`).value;
    const description = document.getElementById(`modal-timeline-description-day-${dayNumber}`).value;
    const location = document.getElementById(`modal-timeline-location-day-${dayNumber}`).value;
    const providerId = document.getElementById(`modal-timeline-provider-day-${dayNumber}`).value;
    const destinationId = document.getElementById(`modal-timeline-destination-day-${dayNumber}`).value;
    const serviceId = document.getElementById(`modal-timeline-service-day-${dayNumber}`).value;
    const notes = document.getElementById(`modal-timeline-notes-day-${dayNumber}`).value;
    
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
    
    const itemHtml = `
        <div class="timeline-item bg-white border-2 ${typeColors[type]} rounded-lg p-4 mb-4 relative" 
             data-day="${dayNumber}" data-index="${counter}" data-time="${time}">
            <div class="absolute -left-6 top-6 w-6 h-6 bg-white border-4 border-blue-500 rounded-full flex items-center justify-center z-10">
                <span class="text-sm">${typeIcons[type] || '🎯'}</span>
            </div>
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center gap-2">
                    <input type="time" name="timeline_time[]" value="${time}" required
                        class="px-2 py-1 border rounded focus:border-blue-500 font-semibold text-blue-600 text-lg"
                        onchange="sortTimelineByTime(${dayNumber})">
                    <span class="text-sm font-medium text-gray-600 px-2 py-1 bg-white rounded">${typeLabels[type]}</span>
                </div>
                <button type="button" onclick="removeTimelineItem(this)" class="text-red-500 hover:text-red-700">
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
                    <select name="timeline_type[]" class="w-full px-3 py-2 border rounded focus:border-blue-500"
                        onchange="updateTimelineType(this)">
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
                    <select name="timeline_service_provider[]" class="w-full px-3 py-2 border rounded focus:border-blue-500"
                        onchange="updateLocationFromProvider(this)">
                        <option value="">-- Chọn nhà dịch vụ --</option>
                        ${getServiceProviderOptions(${dayNumber}, providerId)}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Địa điểm du lịch</label>
                    <select name="timeline_destination[]" class="w-full px-3 py-2 border rounded focus:border-blue-500">
                        <option value="">-- Chọn địa điểm --</option>
                        ${getDestinationOptions(${dayNumber}, destinationId)}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dịch vụ</label>
                    <select name="timeline_service[]" class="w-full px-3 py-2 border rounded focus:border-blue-500"
                        onchange="promptAddToDayServices(this, ${dayNumber})">
                        <option value="">-- Chọn dịch vụ --</option>
                        ${getServiceOptions(${dayNumber}, serviceId)}
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                    <textarea name="timeline_notes[]" rows="2"
                        class="w-full px-3 py-2 border rounded focus:border-blue-500">${escapeHtml(notes)}</textarea>
                </div>
                <input type="hidden" name="timeline_display_order[]" value="${counter}">
            </div>
            <div class="mt-3 flex justify-end gap-2">
                <button type="button" onclick="moveTimelineUp(this)" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded">
                    <i class="fas fa-arrow-up"></i> Lên
                </button>
                <button type="button" onclick="moveTimelineDown(this)" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded">
                    <i class="fas fa-arrow-down"></i> Xuống
                </button>
            </div>
        </div>
    `;
    
    if (container.innerHTML.includes('Chưa có timeline')) {
        container.innerHTML = itemHtml;
    } else {
        container.insertAdjacentHTML('beforeend', itemHtml);
    }
    
    sortTimelineByTime(dayNumber);
    closeAddTimelineModal(dayNumber);
}

// Service Modal Functions
function openAddServiceModal(dayNumber) {
    document.getElementById(`add-service-modal-day-${dayNumber}`).classList.remove('hidden');
}

function closeAddServiceModal(dayNumber) {
    document.getElementById(`add-service-modal-day-${dayNumber}`).classList.add('hidden');
    document.getElementById(`add-service-form-day-${dayNumber}`).reset();
}

function saveDayService(event, dayNumber) {
    event.preventDefault();
    
    const counter = window.dayServiceCounter[dayNumber] = (window.dayServiceCounter[dayNumber] || 0) + 1;
    const container = document.getElementById(`day-services-list-day-${dayNumber}`);
    
    const serviceId = document.getElementById(`modal-service-id-day-${dayNumber}`).value;
    const serviceName = document.getElementById(`modal-service-id-day-${dayNumber}`).options[document.getElementById(`modal-service-id-day-${dayNumber}`).selectedIndex].getAttribute('data-name');
    const providerId = document.getElementById(`modal-service-provider-id-day-${dayNumber}`).value;
    const providerName = providerId ? document.getElementById(`modal-service-provider-id-day-${dayNumber}`).options[document.getElementById(`modal-service-provider-id-day-${dayNumber}`).selectedIndex].text : '';
    const unitPrice = parseFloat(document.getElementById(`modal-unit-price-day-${dayNumber}`).value);
    const quantity = parseFloat(document.getElementById(`modal-quantity-day-${dayNumber}`).value || 1);
    const unit = document.getElementById(`modal-unit-day-${dayNumber}`).value;
    const included = document.getElementById(`modal-included-day-${dayNumber}`).checked;
    const notes = document.getElementById(`modal-notes-day-${dayNumber}`).value;
    
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
                    <button type="button" onclick="removeDayService(this)" class="text-red-500 hover:text-red-700">
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

// Helper functions
function removeTimelineItem(btn) {
    if (confirm('Bạn có chắc muốn xóa timeline item này?')) {
        const item = btn.closest('.timeline-item');
        const dayNumber = item.getAttribute('data-day');
        item.remove();
        sortTimelineByTime(dayNumber);
    }
}

function removeDayService(btn) {
    if (confirm('Bạn có chắc muốn xóa dịch vụ này?')) {
        const item = btn.closest('.day-service-item');
        const container = item.closest('.itinerary-manager');
        const dayNumber = container.getAttribute('data-day');
        item.remove();
        updateDayServiceTotal(dayNumber);
    }
}

function updateDayServiceTotal(dayNumber) {
    const container = document.getElementById(`day-services-list-day-${dayNumber}`);
    const items = container.querySelectorAll('.day-service-item');
    let total = 0;
    
    items.forEach(item => {
        const checkbox = item.querySelector('[name^="day_service_included"]');
        if (checkbox && checkbox.checked) {
            const unitPrice = parseFloat(item.querySelector('[name="day_service_unit_price[]"]').value || 0);
            const quantity = parseFloat(item.querySelector('[name="day_service_quantity[]"]').value || 1);
            total += (unitPrice * quantity);
        }
    });
    
    const totalEl = document.getElementById(`day-total-${dayNumber}`);
    if (totalEl) {
        totalEl.textContent = formatCurrency(total) + '/người';
    }
}

function sortTimelineByTime(dayNumber) {
    const container = document.getElementById(`timeline-items-day-${dayNumber}`);
    const items = Array.from(container.querySelectorAll('.timeline-item'));
    
    items.sort((a, b) => {
        const timeA = a.querySelector('[name="timeline_time[]"]').value || '00:00:00';
        const timeB = b.querySelector('[name="timeline_time[]"]').value || '00:00:00';
        return timeA.localeCompare(timeB);
    });
    
    items.forEach(item => container.appendChild(item));
    updateDisplayOrder(dayNumber);
}

function updateDisplayOrder(dayNumber) {
    const container = document.getElementById(`timeline-items-day-${dayNumber}`);
    const items = container.querySelectorAll('.timeline-item');
    items.forEach((item, index) => {
        const orderInput = item.querySelector('[name="timeline_display_order[]"]');
        if (orderInput) {
            orderInput.value = index;
        }
    });
}

function moveTimelineUp(btn) {
    const item = btn.closest('.timeline-item');
    const prev = item.previousElementSibling;
    if (prev) {
        item.parentNode.insertBefore(item, prev);
    }
    const dayNumber = item.getAttribute('data-day');
    updateDisplayOrder(dayNumber);
}

function moveTimelineDown(btn) {
    const item = btn.closest('.timeline-item');
    const next = item.nextElementSibling;
    if (next) {
        item.parentNode.insertBefore(next, item);
    }
    const dayNumber = item.getAttribute('data-day');
    updateDisplayOrder(dayNumber);
}

function updateTimelineType(select) {
    const item = select.closest('.timeline-item');
    const type = select.value;
    const dot = item.querySelector('.absolute.-left-6');
    const typeLabel = item.querySelector('.text-sm.font-medium.text-gray-600');
    
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
    
    if (dot) {
        dot.innerHTML = `<span class="text-sm">${typeIcons[type] || '🎯'}</span>`;
    }
    
    if (typeLabel) {
        typeLabel.textContent = typeLabels[type] || 'Hoạt động';
    }
    
    item.className = item.className.replace(/bg-\w+-\d+ border-\w+-\d+/g, '');
    item.classList.add(...typeColors[type].split(' '));
}

function updateLocationFromProvider(select, dayNumber) {
    const item = select.closest('.timeline-item') || select.closest('.itinerary-manager');
    const locationInput = item ? item.querySelector('[name="timeline_location[]"], #modal-timeline-location-day-' + dayNumber) : null;
    const selectedOption = select.options[select.selectedIndex];
    const address = selectedOption.getAttribute('data-address');
    
    if (locationInput && address && !locationInput.value) {
        locationInput.value = address;
    }
}

function promptAddToDayServices(select, dayNumber) {
    if (!select.value) return;
    
    const serviceId = select.value;
    const serviceName = select.options[select.selectedIndex].text;
    const providerSelect = select.closest('.timeline-item')?.querySelector('[name="timeline_service_provider[]"]') || 
                          document.getElementById(`modal-timeline-provider-day-${dayNumber}`);
    const providerId = providerSelect ? providerSelect.value : '';
    
    if (providerId && confirm(`Bạn có muốn thêm dịch vụ "${serviceName}" vào danh sách dịch vụ của ngày ${dayNumber} không?`)) {
        // Auto-fill service modal
        document.getElementById(`modal-service-id-day-${dayNumber}`).value = serviceId;
        if (providerId) {
            document.getElementById(`modal-service-provider-id-day-${dayNumber}`).value = providerId;
        }
        loadServiceInfo(document.getElementById(`modal-service-id-day-${dayNumber}`), dayNumber);
        openAddServiceModal(dayNumber);
    }
}

function loadServiceInfo(select, dayNumber) {
    const serviceId = select.value;
    if (!serviceId) return;
    
    fetch(`?act=admin&module=tours&action=getServiceInfo&id=${serviceId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const priceInput = document.getElementById(`modal-unit-price-day-${dayNumber}`);
                const unitInput = document.getElementById(`modal-unit-day-${dayNumber}`);
                if (priceInput && data.data.unit_price > 0) {
                    priceInput.value = data.data.unit_price;
                }
                if (unitInput && data.data.unit) {
                    unitInput.value = data.data.unit;
                }
            }
        });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(Math.round(amount || 0)) + 'đ';
}

function formatNumber(num) {
    return new Intl.NumberFormat('vi-VN', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(num);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Data from PHP - Store in global scope for this day
window.itineraryManagerData = window.itineraryManagerData || {};
window.itineraryManagerData[<?= $day_number ?>] = {
    destinations: <?= json_encode($destinations) ?>,
    services: <?= json_encode($services) ?>,
    serviceProviders: <?= json_encode($service_providers) ?>
};

function getServiceProviderOptions(dayNumber, selectedId) {
    let options = '<option value="">-- Chọn nhà dịch vụ --</option>';
    const data = window.itineraryManagerData[dayNumber];
    if (!data || !data.serviceProviders) return options;
    
    const providers = data.serviceProviders;
    if (Array.isArray(providers)) {
        providers.forEach(provider => {
            const id = (typeof provider === 'object' && provider.id) ? provider.id : null;
            const name = (typeof provider === 'object' && provider.name) ? provider.name : provider;
            const address = (typeof provider === 'object' && provider.address) ? provider.address : '';
            const selected = selectedId && String(id) === String(selectedId) ? 'selected' : '';
            options += `<option value="${id}" data-address="${escapeHtml(address)}" ${selected}>${escapeHtml(name)}</option>`;
        });
    } else {
        for (const [id, name] of Object.entries(providers)) {
            const selected = selectedId && String(id) === String(selectedId) ? 'selected' : '';
            options += `<option value="${id}" ${selected}>${escapeHtml(name)}</option>`;
        }
    }
    return options;
}

function getDestinationOptions(dayNumber, selectedId) {
    let options = '<option value="">-- Chọn địa điểm --</option>';
    const data = window.itineraryManagerData[dayNumber];
    if (!data || !data.destinations) return options;
    
    const dests = data.destinations;
    if (Array.isArray(dests)) {
        dests.forEach(dest => {
            const id = (typeof dest === 'object' && dest.id) ? dest.id : null;
            const name = (typeof dest === 'object' && dest.name) ? dest.name : dest;
            const selected = selectedId && String(id) === String(selectedId) ? 'selected' : '';
            options += `<option value="${id}" ${selected}>${escapeHtml(name)}</option>`;
        });
    } else {
        for (const [id, name] of Object.entries(dests)) {
            const selected = selectedId && String(id) === String(selectedId) ? 'selected' : '';
            options += `<option value="${id}" ${selected}>${escapeHtml(name)}</option>`;
        }
    }
    return options;
}

function getServiceOptions(dayNumber, selectedId) {
    let options = '<option value="">-- Chọn dịch vụ --</option>';
    const data = window.itineraryManagerData[dayNumber];
    if (!data || !data.services) return options;
    
    const services = data.services;
    if (Array.isArray(services)) {
        services.forEach(service => {
            const id = (typeof service === 'object' && service.id) ? service.id : null;
            const name = (typeof service === 'object' && service.name) ? service.name : service;
            const selected = selectedId && String(id) === String(selectedId) ? 'selected' : '';
            options += `<option value="${id}" ${selected}>${escapeHtml(name)}</option>`;
        });
    } else {
        for (const [id, name] of Object.entries(services)) {
            const selected = selectedId && String(id) === String(selectedId) ? 'selected' : '';
            options += `<option value="${id}" ${selected}>${escapeHtml(name)}</option>`;
        }
    }
    return options;
}
</script>

