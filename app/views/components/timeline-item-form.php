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
    'meal' => 'bg-orange-50 border-orange-200',
    'accommodation' => 'bg-blue-50 border-blue-200',
    'activity' => 'bg-green-50 border-green-200',
    'transport' => 'bg-purple-50 border-purple-200'
];

$timeline_type = $item['timeline_type'] ?? 'activity';
$icon = $type_icons[$timeline_type] ?? '🎯';
$color_class = $type_colors[$timeline_type] ?? 'bg-gray-50 border-gray-200';
?>

<div class="timeline-item bg-white border-2 <?= $color_class ?> rounded-lg p-4 mb-4 relative" 
     data-day="<?= $day_number ?>" 
     data-index="<?= $idx ?>"
     data-time="<?= htmlspecialchars($item['timeline_time'] ?? '') ?>">
    
    <!-- Timeline Dot -->
    <div class="absolute -left-6 top-6 w-6 h-6 bg-white border-4 border-blue-500 rounded-full flex items-center justify-center z-10">
        <span class="text-sm"><?= $icon ?></span>
    </div>
    
    <!-- Header with Time and Type -->
    <div class="flex justify-between items-start mb-3">
        <div class="flex items-center gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <input type="time" 
                           name="timeline_time[]" 
                           value="<?= htmlspecialchars($item['timeline_time'] ?? '') ?>"
                           required
                           class="px-2 py-1 border rounded focus:border-blue-500 font-semibold text-blue-600 text-lg"
                           onchange="sortTimelineByTime(<?= $day_number ?>)">
                    <span class="text-sm font-medium text-gray-600 px-2 py-1 bg-white rounded">
                        <?= ucfirst($timeline_type === 'meal' ? 'Bữa ăn' : ($timeline_type === 'accommodation' ? 'Nơi nghỉ' : ($timeline_type === 'activity' ? 'Hoạt động' : 'Di chuyển'))) ?>
                    </span>
                </div>
            </div>
        </div>
        <button type="button" onclick="removeTimelineItem(this)" class="text-red-500 hover:text-red-700">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    
    <!-- Form Fields -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Hoạt động <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   name="timeline_activity_title[]" 
                   value="<?= htmlspecialchars($item['activity_title'] ?? '') ?>"
                   placeholder="VD: Ăn sáng, Check-in, Tham quan..."
                   required
                   class="w-full px-3 py-2 border rounded focus:border-blue-500">
            <input type="hidden" name="timeline_day_number[]" value="<?= $day_number ?>">
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
            <textarea name="timeline_activity_description[]" 
                      rows="2"
                      class="w-full px-3 py-2 border rounded focus:border-blue-500"
                      placeholder="Mô tả chi tiết hoạt động..."><?= htmlspecialchars($item['activity_description'] ?? '') ?></textarea>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Loại timeline</label>
            <select name="timeline_type[]" 
                    class="w-full px-3 py-2 border rounded focus:border-blue-500"
                    onchange="updateTimelineType(this)">
                <option value="activity" <?= $timeline_type === 'activity' ? 'selected' : '' ?>>Hoạt động</option>
                <option value="meal" <?= $timeline_type === 'meal' ? 'selected' : '' ?>>Bữa ăn</option>
                <option value="accommodation" <?= $timeline_type === 'accommodation' ? 'selected' : '' ?>>Nơi nghỉ</option>
                <option value="transport" <?= $timeline_type === 'transport' ? 'selected' : '' ?>>Di chuyển</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Địa điểm</label>
            <input type="text" 
                   name="timeline_location[]" 
                   value="<?= htmlspecialchars($item['location'] ?? '') ?>"
                   placeholder="VD: Nhà hàng ABC"
                   class="w-full px-3 py-2 border rounded focus:border-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nhà dịch vụ</label>
            <select name="timeline_service_provider[]" 
                    class="w-full px-3 py-2 border rounded focus:border-blue-500"
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
            <label class="block text-sm font-medium text-gray-700 mb-1">Địa điểm du lịch</label>
            <select name="timeline_destination[]" 
                    class="w-full px-3 py-2 border rounded focus:border-blue-500">
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
            <label class="block text-sm font-medium text-gray-700 mb-1">Dịch vụ</label>
            <select name="timeline_service[]" 
                    class="w-full px-3 py-2 border rounded focus:border-blue-500"
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
            <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
            <textarea name="timeline_notes[]" 
                      rows="2"
                      class="w-full px-3 py-2 border rounded focus:border-blue-500"
                      placeholder="Ghi chú thêm..."><?= htmlspecialchars($item['notes'] ?? '') ?></textarea>
        </div>
        
        <input type="hidden" name="timeline_display_order[]" value="<?= $item['display_order'] ?? $idx ?>">
    </div>
    
    <!-- Actions -->
    <div class="mt-3 flex justify-end gap-2">
        <button type="button" onclick="moveTimelineUp(this)" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded">
            <i class="fas fa-arrow-up"></i> Lên
        </button>
        <button type="button" onclick="moveTimelineDown(this)" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded">
            <i class="fas fa-arrow-down"></i> Xuống
        </button>
    </div>
</div>

