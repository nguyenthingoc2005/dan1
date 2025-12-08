<?php
/**
 * ==============================================================================
 * TIMELINE EDITOR COMPONENT
 * ==============================================================================
 * 
 * Component để quản lý timeline chi tiết cho một ngày của tour
 * 
 * Variables:
 * - $day_number (required): Số ngày (1, 2, 3...)
 * - $timeline_items (optional): Array các timeline items đã có
 * - $destinations (optional): Array destinations cho dropdown
 * - $service_providers (optional): Array service_providers cho dropdown
 * - $services (optional): Array services cho dropdown
 * 
 * @version 1.0
 * @date 2024-12-06
 * ==============================================================================
 */

$day_number = $day_number ?? 1;
$timeline_items = $timeline_items ?? [];
$destinations = $destinations ?? [];
$service_providers = $service_providers ?? [];
$services = $services ?? [];
?>

<div class="timeline-editor" data-day="<?= $day_number ?>">
    <div class="mb-3 lg:mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 lg:gap-3">
        <h4 class="text-base lg:text-lg font-bold text-primary-700 flex items-center gap-2">
            <i data-lucide="clock" class="w-4 h-4 lg:w-5 lg:h-5 text-accent"></i>
            Timeline - Day <?= $day_number ?>
        </h4>
        <button type="button" onclick="addTimelineItem(<?= $day_number ?>)"
            class="px-3 lg:px-4 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-xs lg:text-sm flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Thêm timeline item
        </button>
    </div>

    <!-- Timeline Items List - Vertical Timeline View -->
    <div id="timeline-items-day-<?= $day_number ?>" class="relative pl-8 lg:pl-12">
        <?php if (empty($timeline_items)): ?>
            <div class="text-primary-500 text-center py-6 lg:py-8 bg-primary-50 rounded-xl border-2 border-dashed border-primary-200">
                <i data-lucide="clock" class="w-12 h-12 lg:w-16 lg:h-16 mx-auto mb-2 lg:mb-3 text-primary-300"></i>
                <p class="text-sm lg:text-base font-semibold">Chưa có timeline nào cho ngày này</p>
                <p class="text-xs lg:text-sm mt-1 text-primary-400">Click "Thêm timeline item" để bắt đầu</p>
            </div>
        <?php else: ?>
            <!-- Vertical Timeline Line -->
            <div class="absolute left-4 lg:left-6 top-0 bottom-0 w-0.5 bg-accent"></div>
            
            <div class="space-y-4 relative">
                <?php foreach ($timeline_items as $idx => $item): ?>
                    <?php include __DIR__ . '/timeline-item-form.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Timeline items counter per day
    window.timelineCounter = window.timelineCounter || {};
    window.timelineCounter[<?= $day_number ?>] = <?= count($timeline_items) ?>;

    function addTimelineItem(dayNumber) {
        const counter = window.timelineCounter[dayNumber] = (window.timelineCounter[dayNumber] || 0) + 1;
        const container = document.getElementById(`timeline-items-day-${dayNumber}`);

        const itemHtml = `
        <div class="timeline-item bg-panel border-2 border-success rounded-xl p-3 lg:p-4 mb-3 lg:mb-4 relative shadow-sm hover:shadow-md transition-all" data-day="${dayNumber}" data-index="${counter}">
            <!-- Timeline Dot -->
            <div class="absolute -left-4 lg:-left-6 top-6 w-6 h-6 lg:w-8 lg:h-8 bg-panel border-4 border-accent rounded-full flex items-center justify-center z-10 shadow-sm">
                <i data-lucide="circle" class="w-2 h-2 lg:w-3 lg:h-3 text-accent"></i>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 lg:gap-3 mb-2 lg:mb-3">
                <div class="flex items-center gap-2">
                    <input type="time" 
                           name="timeline_time[]" 
                           value=""
                           required
                           class="px-2 lg:px-3 py-1 lg:py-1.5 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all font-bold text-accent text-sm lg:text-base bg-primary-50"
                           onchange="sortTimelineByTime(${dayNumber})">
                    <span class="text-xs lg:text-sm font-semibold text-primary-600 px-2 lg:px-3 py-1 bg-primary-50 rounded-xl">Hoạt động</span>
                </div>
                <button type="button" onclick="removeTimelineItem(this)" class="text-danger hover:opacity-80 transition-all p-1 lg:p-2 rounded-lg hover:bg-danger-bg">
                    <i data-lucide="trash-2" class="w-4 h-4 lg:w-5 lg:h-5"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 lg:gap-4">
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Giờ <span class="text-danger">*</span></label>
                    <input type="time" 
                           name="timeline_time[]" 
                           value=""
                           required
                           class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                </div>
                
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Loại timeline</label>
                    <select name="timeline_type[]" 
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                            onchange="updateTimelineType(this)">
                        <option value="activity">Hoạt động</option>
                        <option value="meal">Bữa ăn</option>
                        <option value="accommodation">Nơi nghỉ</option>
                        <option value="transport">Di chuyển</option>
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Hoạt động <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="timeline_activity_title[]" 
                           placeholder="VD: Ăn sáng, Check-in, Tham quan..."
                           required
                           class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    <input type="hidden" name="timeline_day_number[]" value="${dayNumber}">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả</label>
                    <textarea name="timeline_activity_description[]" 
                              rows="2"
                              class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                              placeholder="Mô tả chi tiết hoạt động..."></textarea>
                </div>
                
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Địa điểm</label>
                    <input type="text" 
                           name="timeline_location[]" 
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
                            <option value="<?= $provider['id'] ?>" data-address="<?= htmlspecialchars($provider['address'] ?? '') ?>">
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
                            <option value="<?= $dest['id'] ?>"><?= htmlspecialchars($dest['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Dịch vụ</label>
                    <select name="timeline_service[]" 
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                            onchange="promptAddToDayServices(this, ${dayNumber})">
                        <option value="">-- Chọn dịch vụ --</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?= $service['id'] ?>"><?= htmlspecialchars($service['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ghi chú</label>
                    <textarea name="timeline_notes[]" 
                              rows="2"
                              class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                              placeholder="Ghi chú thêm..."></textarea>
                </div>
                
                <input type="hidden" name="timeline_display_order[]" value="${counter}">
            </div>
            
            <div class="mt-3 flex justify-end gap-2">
                <button type="button" onclick="moveTimelineUp(this)" class="px-3 py-1.5 lg:py-2 text-xs lg:text-sm bg-primary-50 hover:bg-primary-100 rounded-xl text-primary-700 font-semibold transition-colors">
                    <i data-lucide="arrow-up" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                </button>
                <button type="button" onclick="moveTimelineDown(this)" class="px-3 py-1.5 lg:py-2 text-xs lg:text-sm bg-primary-50 hover:bg-primary-100 rounded-xl text-primary-700 font-semibold transition-colors">
                    <i data-lucide="arrow-down" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                </button>
            </div>
        </div>
    `;

        // Add to container
        if (container.innerHTML.includes('Chưa có timeline')) {
            container.innerHTML = itemHtml;
        } else {
            container.insertAdjacentHTML('beforeend', itemHtml);
        }
        
        // Initialize Lucide icons for new content
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function removeTimelineItem(btn) {
        if (confirm('Bạn có chắc muốn xóa timeline item này?')) {
            btn.closest('.timeline-item').remove();
            // Show empty message if no items left
            const container = btn.closest('.timeline-items');
            if (container && container.children.length === 0) {
                container.innerHTML = '<div class="text-primary-500 text-center py-6 lg:py-8 bg-primary-50 rounded-xl border-2 border-dashed border-primary-200"><i data-lucide="clock" class="w-12 h-12 lg:w-16 lg:h-16 mx-auto mb-2 lg:mb-3 text-primary-300"></i><p class="text-sm lg:text-base font-semibold">Chưa có timeline nào</p></div>';
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        }
    }

    function updateTimelineType(select) {
        const item = select.closest('.timeline-item');
        const type = select.value;
        const providerSelect = item.querySelector('[name="timeline_service_provider[]"]');
        const dot = item.querySelector('.absolute.-left-6');
        const typeLabel = item.querySelector('.text-sm.font-medium.text-gray-600');
        
        // Update icon and color based on type
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
            'meal': 'bg-warning-bg border-warning',
            'accommodation': 'bg-info-bg border-info',
            'activity': 'bg-success-bg border-success',
            'transport': 'bg-primary-100 border-primary-300'
        };
        
        if (dot) {
            dot.innerHTML = `<span class="text-sm">${typeIcons[type] || '🎯'}</span>`;
        }
        
        if (typeLabel) {
            typeLabel.textContent = typeLabels[type] || 'Hoạt động';
        }
        
        // Update border color
        item.className = item.className.replace(/bg-\w+-\d+ border-\w+-\d+/g, '');
        item.classList.add(...typeColors[type].split(' '));

        // Suggest service provider based on type
        if (type === 'meal' || type === 'accommodation') {
            providerSelect.style.borderColor = '#3b82f6';
            providerSelect.title = 'Khuyến khích chọn nhà dịch vụ';
        } else {
            providerSelect.style.borderColor = '';
            providerSelect.title = '';
        }
    }

    function updateLocationFromProvider(select) {
        const item = select.closest('.timeline-item');
        const locationInput = item.querySelector('[name="timeline_location[]"]');
        const selectedOption = select.options[select.selectedIndex];
        const address = selectedOption.getAttribute('data-address');

        if (address && !locationInput.value) {
            locationInput.value = address;
        }
    }

    function promptAddToDayServices(select, dayNumber) {
        if (!select.value) return;

        const serviceId = select.value;
        const serviceName = select.options[select.selectedIndex].text;
        const providerSelect = select.closest('.timeline-item').querySelector('[name="timeline_service_provider[]"]');
        const providerId = providerSelect ? providerSelect.value : '';

        if (providerId && confirm(`Bạn có muốn thêm dịch vụ "${serviceName}" vào danh sách dịch vụ của ngày ${dayNumber} không?`)) {
            // Trigger add to day services
            addServiceToDayServices(dayNumber, serviceId, providerId);
        }
    }

    function moveTimelineUp(btn) {
        const item = btn.closest('.timeline-item');
        const prev = item.previousElementSibling;
        if (prev) {
            item.parentNode.insertBefore(item, prev);
        }
        updateDisplayOrder(dayNumber);
    }

    function moveTimelineDown(btn) {
        const item = btn.closest('.timeline-item');
        const next = item.nextElementSibling;
        if (next) {
            item.parentNode.insertBefore(next, item);
        }
        updateDisplayOrder(dayNumber);
    }

    function sortTimelineByTime(dayNumber) {
        const container = document.getElementById(`timeline-items-day-${dayNumber}`);
        const items = Array.from(container.querySelectorAll('.timeline-item'));
        
        // Sort by time
        items.sort((a, b) => {
            const timeA = a.querySelector('[name="timeline_time[]"]').value || '00:00:00';
            const timeB = b.querySelector('[name="timeline_time[]"]').value || '00:00:00';
            return timeA.localeCompare(timeB);
        });
        
        // Re-append sorted items
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
</script>