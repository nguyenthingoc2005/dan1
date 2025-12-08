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

<div class="itinerary-manager bg-panel border-2 border-primary-100 rounded-2xl p-4 lg:p-6 mb-4 lg:mb-6 shadow-sm" 
     data-day="<?= $day_number ?>"
     data-timeline-count="<?= count($timeline_items) ?>"
     data-services-count="<?= count($day_services) ?>">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 lg:gap-3 mb-3 lg:mb-4">
        <h3 class="text-base lg:text-lg font-bold text-primary-700 flex items-center gap-2">
            <i data-lucide="calendar" class="w-5 h-5 lg:w-6 lg:h-6 text-accent"></i>
            Ngày <?= $day_number ?>
        </h3>
        <div class="flex gap-2">
            <button type="button" data-action="toggle-day-manager" data-day="<?= $day_number ?>"
                    class="px-3 lg:px-4 py-1.5 lg:py-2 text-xs lg:text-sm bg-primary-50 hover:bg-primary-100 rounded-xl text-primary-700 font-semibold transition-colors">
                <i data-lucide="chevron-down" id="toggle-icon-<?= $day_number ?>" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <!-- Collapsible Content -->
    <div id="day-manager-content-<?= $day_number ?>" class="space-y-6">
        
        <!-- TIMELINE SECTION -->
        <div class="border-t border-primary-100 pt-3 lg:pt-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 lg:gap-3 mb-3 lg:mb-4">
                <h4 class="font-bold text-primary-700 text-sm lg:text-base flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4 lg:w-5 lg:h-5 text-accent"></i>
                    Timeline chi tiết
                </h4>
                <button type="button" data-action="open-timeline-modal" data-day="<?= $day_number ?>"
                    class="px-3 lg:px-4 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-xs lg:text-sm flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Thêm timeline
                </button>
            </div>

            <!-- Timeline Items -->
            <div id="timeline-items-day-<?= $day_number ?>" class="space-y-2 lg:space-y-3">
                <?php if (empty($timeline_items)): ?>
                    <div class="text-primary-500 text-center py-4 lg:py-6 bg-primary-50 rounded-xl border-2 border-dashed border-primary-200">
                        <i data-lucide="clock" class="w-10 h-10 lg:w-12 lg:h-12 mx-auto mb-2 lg:mb-3 text-primary-300"></i>
                        <p class="text-xs lg:text-sm font-semibold">Chưa có timeline nào</p>
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
<div id="add-timeline-modal-day-<?= $day_number ?>" class="hidden fixed inset-0 bg-primary-900 bg-opacity-60 z-[9999] flex items-center justify-center p-4">
    <div class="bg-panel rounded-2xl shadow-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto border-l-4 border-accent">
        <div class="p-4 lg:p-6 border-b border-primary-100">
            <h3 class="text-base lg:text-lg font-bold text-primary-700 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 lg:w-6 lg:h-6 text-accent"></i>
                Thêm timeline - Day <?= $day_number ?>
            </h3>
        </div>
        <form id="add-timeline-form-day-<?= $day_number ?>" class="p-4 lg:p-6" data-action="save-timeline" data-day="<?= $day_number ?>">
            <div class="space-y-3 lg:space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-4">
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Giờ <span class="text-danger">*</span></label>
                        <input type="time" id="modal-timeline-time-day-<?= $day_number ?>" required
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Loại timeline</label>
                        <select id="modal-timeline-type-day-<?= $day_number ?>" 
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                            <option value="activity">Hoạt động</option>
                            <option value="meal">Bữa ăn</option>
                            <option value="accommodation">Nơi nghỉ</option>
                            <option value="transport">Di chuyển</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Hoạt động <span class="text-danger">*</span></label>
                    <input type="text" id="modal-timeline-activity-day-<?= $day_number ?>" required
                        placeholder="VD: Ăn sáng, Check-in, Tham quan..."
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                </div>
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả</label>
                    <textarea id="modal-timeline-description-day-<?= $day_number ?>" rows="3"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                        placeholder="Mô tả chi tiết..."></textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-4">
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Địa điểm</label>
                        <input type="text" id="modal-timeline-location-day-<?= $day_number ?>"
                            placeholder="VD: Nhà hàng ABC"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Nhà dịch vụ</label>
                        <select id="modal-timeline-provider-day-<?= $day_number ?>"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-4">
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Địa điểm du lịch</label>
                        <select id="modal-timeline-destination-day-<?= $day_number ?>"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
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
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Dịch vụ</label>
                        <select id="modal-timeline-service-day-<?= $day_number ?>"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
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
                <div id="service-price-info-day-<?= $day_number ?>" class="hidden bg-info-bg border border-info rounded-xl p-3 lg:p-4">
                    <h4 class="font-bold text-info-dark mb-2 lg:mb-3 text-sm lg:text-base">Thông tin giá dịch vụ</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 lg:gap-4">
                        <div>
                            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Đơn giá/người</label>
                            <input type="number" id="modal-timeline-service-price-day-<?= $day_number ?>" step="1000" min="0"
                                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                                placeholder="0"
                                onchange="calculateTimelineServiceTotal(<?= $day_number ?>)">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số lượng</label>
                            <input type="number" id="modal-timeline-service-quantity-day-<?= $day_number ?>" step="0.01" min="0.01" value="1"
                                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                                onchange="calculateTimelineServiceTotal(<?= $day_number ?>)">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Đơn vị</label>
                            <input type="text" id="modal-timeline-service-unit-day-<?= $day_number ?>"
                                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                                placeholder="VD: bữa, đêm, vé">
                        </div>
                    </div>
                    <div class="mt-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 lg:gap-3">
                        <div>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" id="modal-timeline-service-included-day-<?= $day_number ?>" checked
                                    class="w-4 h-4 lg:w-5 lg:h-5 text-accent rounded-lg border-primary-200 focus:ring-2 focus:ring-accent">
                                <span class="text-xs lg:text-sm font-semibold text-primary-700">Bao gồm trong giá tour</span>
                            </label>
                        </div>
                        <div class="text-right">
                            <span class="text-xs lg:text-sm text-primary-500">Tổng:</span>
                            <span id="modal-timeline-service-total-day-<?= $day_number ?>" class="text-base lg:text-lg font-bold text-info-dark ml-2">0đ</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ghi chú</label>
                    <textarea id="modal-timeline-notes-day-<?= $day_number ?>" rows="2"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                        placeholder="Ghi chú thêm..."></textarea>
                </div>
            </div>
            <div class="mt-4 lg:mt-6 flex flex-col sm:flex-row justify-end gap-2 lg:gap-3">
                <button type="button" data-action="close-timeline-modal" data-day="<?= $day_number ?>"
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl font-semibold hover:bg-primary-100 transition-colors text-sm lg:text-base">
                    Hủy
                </button>
                <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base">
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
