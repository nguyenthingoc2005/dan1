<?php
/**
 * ==============================================================================
 * TIMELINE ITEM FORM COMPONENT
 * ==============================================================================
 * 
 * Component con để render một timeline item (đã có dữ liệu)
 * 
 * Variables:
 * - $item (required): Array timeline item data
 * - $idx (required): Index của item trong array
 * - $day_number (required): Số ngày
 * - $destinations (optional): Array destinations
 * - $service_providers (optional): Array service_providers
 * - $services (optional): Array services
 * 
 * @version 1.0
 * @date 2024-12-06
 * ==============================================================================
 */

$item = $item ?? [];
$idx = $idx ?? 0;
$day_number = $day_number ?? 1;
$destinations = $destinations ?? [];
$service_providers = $service_providers ?? [];
$services = $services ?? [];

// Get icon and color based on timeline type
$type_icons = [
    'meal' => '🍽️',
    'accommodation' => '🏨',
    'activity' => '🎯',
    'transport' => '🚌'
];

$type_colors = [
    'meal' => 'bg-warning-bg border-warning',
    'accommodation' => 'bg-info-bg border-info',
    'activity' => 'bg-success-bg border-success',
    'transport' => 'bg-primary-100 border-primary-300'
];

$timeline_type = $item['timeline_type'] ?? 'activity';
$icon = $type_icons[$timeline_type] ?? '🎯';
$color_class = $type_colors[$timeline_type] ?? 'bg-gray-50 border-gray-200';
?>

<div class="timeline-item bg-panel border-2 <?= $color_class ?> rounded-xl p-3 lg:p-4 mb-3 lg:mb-4 relative shadow-sm hover:shadow-md transition-all" 
     data-day="<?= $day_number ?>" 
     data-index="<?= $idx ?>"
     data-time="<?= htmlspecialchars($item['timeline_time'] ?? '') ?>">
    
    <!-- Timeline Dot -->
    <div class="absolute -left-4 lg:-left-6 top-6 w-6 h-6 lg:w-8 lg:h-8 bg-panel border-4 border-accent rounded-full flex items-center justify-center z-10 shadow-sm">
        <i data-lucide="circle" class="w-2 h-2 lg:w-3 lg:h-3 text-accent"></i>
    </div>
    
    <!-- Header with Time and Type -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 lg:gap-3 mb-2 lg:mb-3">
        <div class="flex items-center gap-2 lg:gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <input type="time" 
                           name="timeline_time[]" 
                           value="<?= htmlspecialchars($item['timeline_time'] ?? '') ?>"
                           required
                           class="px-2 lg:px-3 py-1 lg:py-1.5 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all font-bold text-accent text-sm lg:text-base bg-primary-50"
                           onchange="sortTimelineByTime(<?= $day_number ?>)">
                    <span class="text-xs lg:text-sm font-semibold text-primary-600 px-2 lg:px-3 py-1 bg-primary-50 rounded-xl">
                        <?= ucfirst($timeline_type === 'meal' ? 'Bữa ăn' : ($timeline_type === 'accommodation' ? 'Nơi nghỉ' : ($timeline_type === 'activity' ? 'Hoạt động' : 'Di chuyển'))) ?>
                    </span>
                </div>
            </div>
        </div>
        <button type="button" onclick="removeTimelineItem(this)" class="text-danger hover:opacity-80 transition-all p-1 lg:p-2 rounded-lg hover:bg-danger-bg">
            <i data-lucide="trash-2" class="w-4 h-4 lg:w-5 lg:h-5"></i>
        </button>
    </div>
    
    <!-- Form Fields -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 lg:gap-4">
        <div class="md:col-span-2">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                Hoạt động <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   name="timeline_activity_title[]" 
                   value="<?= htmlspecialchars($item['activity_title'] ?? '') ?>"
                   placeholder="VD: Ăn sáng, Check-in, Tham quan..."
                   required
                   class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
            <input type="hidden" name="timeline_day_number[]" value="<?= $day_number ?>">
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả</label>
            <textarea name="timeline_activity_description[]" 
                      rows="2"
                      class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                      placeholder="Mô tả chi tiết hoạt động..."><?= htmlspecialchars($item['activity_description'] ?? '') ?></textarea>
        </div>
        
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Loại timeline</label>
            <select name="timeline_type[]" 
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                    onchange="updateTimelineType(this)">
                <option value="activity" <?= $timeline_type === 'activity' ? 'selected' : '' ?>>Hoạt động</option>
                <option value="meal" <?= $timeline_type === 'meal' ? 'selected' : '' ?>>Bữa ăn</option>
                <option value="accommodation" <?= $timeline_type === 'accommodation' ? 'selected' : '' ?>>Nơi nghỉ</option>
                <option value="transport" <?= $timeline_type === 'transport' ? 'selected' : '' ?>>Di chuyển</option>
            </select>
        </div>
        
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Địa điểm</label>
            <input type="text" 
                   name="timeline_location[]" 
                   value="<?= htmlspecialchars($item['location'] ?? '') ?>"
                   placeholder="VD: Nhà hàng ABC"
                   class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
        </div>
        
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Nhà dịch vụ</label>
            <select name="timeline_service_provider[]" 
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                    onchange="updateLocationFromProvider(this)">
                <option value="">-- Chọn nhà dịch vụ --</option>
                <?php foreach ($service_providers as $provider): ?>
                    <option value="<?= $provider['id'] ?>" 
                            data-address="<?= htmlspecialchars($provider['address'] ?? '') ?>"
                            <?= ($item['service_provider_id'] ?? 0) == $provider['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($provider['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Địa điểm du lịch</label>
            <select name="timeline_destination[]" 
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                <option value="">-- Chọn địa điểm --</option>
                <?php foreach ($destinations as $dest): ?>
                    <?php 
                    $dest_id = is_array($dest) ? $dest['id'] : null;
                    $dest_name = is_array($dest) ? $dest['name'] : $dest;
                    ?>
                    <option value="<?= $dest_id ?>" 
                            <?= ($item['destination_id'] ?? 0) == $dest_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dest_name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Dịch vụ</label>
            <select name="timeline_service[]" 
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                    onchange="promptAddToDayServices(this, <?= $day_number ?>)">
                <option value="">-- Chọn dịch vụ --</option>
                <?php foreach ($services as $service): ?>
                    <?php 
                    $service_id = is_array($service) ? $service['id'] : null;
                    $service_name = is_array($service) ? $service['name'] : $service;
                    ?>
                    <option value="<?= $service_id ?>" 
                            <?= ($item['service_id'] ?? 0) == $service_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($service_name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ghi chú</label>
            <textarea name="timeline_notes[]" 
                      rows="2"
                      class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                      placeholder="Ghi chú thêm..."><?= htmlspecialchars($item['notes'] ?? '') ?></textarea>
        </div>
        
        <input type="hidden" name="timeline_display_order[]" value="<?= $item['display_order'] ?? $idx ?>">
    </div>
    
    <!-- Actions -->
    <div class="mt-3 flex justify-end gap-2">
        <button type="button" onclick="moveTimelineUp(this)" class="px-3 py-1.5 lg:py-2 text-xs lg:text-sm bg-primary-50 hover:bg-primary-100 rounded-xl text-primary-700 font-semibold transition-colors flex items-center gap-1">
            <i data-lucide="arrow-up" class="w-3 h-3 lg:w-4 lg:h-4"></i>
            Lên
        </button>
        <button type="button" onclick="moveTimelineDown(this)" class="px-3 py-1.5 lg:py-2 text-xs lg:text-sm bg-primary-50 hover:bg-primary-100 rounded-xl text-primary-700 font-semibold transition-colors flex items-center gap-1">
            <i data-lucide="arrow-down" class="w-3 h-3 lg:w-4 lg:h-4"></i>
            Xuống
        </button>
    </div>
</div>

