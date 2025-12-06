<?php
/**
 * ==============================================================================
 * DAY SERVICES EDITOR COMPONENT
 * ==============================================================================
 * 
 * Component để quản lý dịch vụ theo ngày
 * 
 * Variables:
 * - $day_number (required): Số ngày (1, 2, 3...)
 * - $day_services (optional): Array các services đã có cho ngày này
 * - $services (optional): Array services cho dropdown
 * - $service_providers (optional): Array service_providers cho dropdown
 * 
 * @version 1.0
 * @date 2024-12-06
 * ==============================================================================
 */

$day_number = $day_number ?? 1;
$day_services = $day_services ?? [];
$services = $services ?? [];
$service_providers = $service_providers ?? [];

// Calculate total cost for this day
$day_total = 0;
foreach ($day_services as $service) {
    if ($service['is_included_in_price']) {
        $day_total += ($service['unit_price'] * $service['quantity']);
    }
}
?>

<div class="day-services-editor" data-day="<?= $day_number ?>">
    <div class="mb-4 flex justify-between items-center">
        <h4 class="text-lg font-semibold text-gray-800">Dịch vụ - Day <?= $day_number ?></h4>
        <button type="button" onclick="openAddServiceModal(<?= $day_number ?>)"
            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm">
            <i class="fas fa-plus mr-2"></i>Thêm dịch vụ
        </button>
    </div>

    <!-- Services List -->
    <div id="day-services-list-day-<?= $day_number ?>" class="space-y-3 mb-4">
        <?php if (empty($day_services)): ?>
            <div class="text-gray-500 text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed">
                <i class="fas fa-concierge-bell text-4xl mb-2"></i>
                <p>Chưa có dịch vụ nào cho ngày này</p>
                <p class="text-sm mt-1">Click "Thêm dịch vụ" để bắt đầu</p>
            </div>
        <?php else: ?>
            <?php foreach ($day_services as $idx => $service): ?>
                <div class="day-service-item bg-white border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 mt-1">
                            <input type="checkbox" name="day_service_included[<?= $day_number ?>][<?= $idx ?>]" value="1"
                                <?= ($service['is_included_in_price'] ?? 1) ? 'checked' : '' ?>
                                onchange="updateDayServiceTotal(<?= $day_number ?>)" class="w-5 h-5 text-blue-600 rounded">
                        </div>

                        <div class="flex-1">
                            <div class="font-medium text-gray-800">
                                <?= htmlspecialchars($service['service_name'] ?? 'N/A') ?>
                                <?php if (!empty($service['service_provider_name'])): ?>
                                    <span class="text-sm text-gray-500">-
                                        <?= htmlspecialchars($service['service_provider_name']) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-600">Đơn giá/người:</span>
                                    <span
                                        class="font-medium ml-2"><?= number_format($service['unit_price'] ?? 0, 0, ',', '.') ?>đ</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Số lượng:</span>
                                    <span class="font-medium ml-2">
                                        <?= number_format($service['quantity'] ?? 1, 2, ',', '.') ?>
                                        <?php if (!empty($service['unit'])): ?>
                                            <?= htmlspecialchars($service['unit']) ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Tổng:</span>
                                    <span class="font-medium text-blue-600 ml-2">
                                        <?= number_format(($service['unit_price'] ?? 0) * ($service['quantity'] ?? 1), 0, ',', '.') ?>đ
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Bao gồm:</span>
                                    <span
                                        class="font-medium ml-2 <?= ($service['is_included_in_price'] ?? 1) ? 'text-green-600' : 'text-gray-400' ?>">
                                        <?= ($service['is_included_in_price'] ?? 1) ? 'Có' : 'Không' ?>
                                    </span>
                                </div>
                            </div>

                            <?php if (!empty($service['notes'])): ?>
                                <div class="mt-2 text-sm text-gray-500">
                                    <i class="fas fa-sticky-note mr-1"></i>
                                    <?= htmlspecialchars($service['notes']) ?>
                                </div>
                            <?php endif; ?>

                            <!-- Hidden fields -->
                            <input type="hidden" name="day_service_day_number[]" value="<?= $day_number ?>">
                            <input type="hidden" name="day_service_service_id[]" value="<?= $service['service_id'] ?? '' ?>">
                            <input type="hidden" name="day_service_provider_id[]"
                                value="<?= $service['service_provider_id'] ?? '' ?>">
                            <input type="hidden" name="day_service_name[]"
                                value="<?= htmlspecialchars($service['service_name'] ?? '') ?>">
                            <input type="hidden" name="day_service_unit_price[]" value="<?= $service['unit_price'] ?? 0 ?>">
                            <input type="hidden" name="day_service_quantity[]" value="<?= $service['quantity'] ?? 1 ?>">
                            <input type="hidden" name="day_service_unit[]"
                                value="<?= htmlspecialchars($service['unit'] ?? '') ?>">
                            <input type="hidden" name="day_service_notes[]"
                                value="<?= htmlspecialchars($service['notes'] ?? '') ?>">
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

    <!-- Day Total -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex justify-between items-center">
            <span class="font-semibold text-blue-900">Tổng Day <?= $day_number ?>:</span>
            <span id="day-total-<?= $day_number ?>" class="text-xl font-bold text-blue-700">
                <?= number_format($day_total, 0, ',', '.') ?>đ/người
            </span>
        </div>
        <p class="text-xs text-blue-600 mt-1">(Chỉ tính các dịch vụ được đánh dấu "Bao gồm trong giá")</p>
    </div>
</div>

<!-- Add Service Modal -->
<div id="add-service-modal-day-<?= $day_number ?>"
    class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b">
            <h3 class="text-lg font-bold text-gray-800">Thêm dịch vụ - Day <?= $day_number ?></h3>
        </div>

        <form id="add-service-form-day-<?= $day_number ?>" class="p-6"
            onsubmit="saveDayService(event, <?= $day_number ?>); return false;">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chọn dịch vụ <span
                            class="text-red-500">*</span></label>
                    <select id="modal-service-id-day-<?= $day_number ?>" required
                        class="w-full px-3 py-2 border rounded focus:border-blue-500"
                        onchange="loadServiceInfo(this, <?= $day_number ?>)">
                        <option value="">-- Chọn dịch vụ --</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?= $service['id'] ?>" data-name="<?= htmlspecialchars($service['name']) ?>">
                                <?= htmlspecialchars($service['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chọn nhà dịch vụ</label>
                    <select id="modal-service-provider-id-day-<?= $day_number ?>"
                        class="w-full px-3 py-2 border rounded focus:border-blue-500">
                        <option value="">-- Chọn nhà dịch vụ --</option>
                        <?php foreach ($service_providers as $provider): ?>
                            <option value="<?= $provider['id'] ?>"><?= htmlspecialchars($provider['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Đơn giá/người <span
                                class="text-red-500">*</span></label>
                        <input type="number" id="modal-unit-price-day-<?= $day_number ?>" step="1000" min="0" required
                            class="w-full px-3 py-2 border rounded focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng</label>
                        <input type="number" id="modal-quantity-day-<?= $day_number ?>" step="0.01" min="0.01" value="1"
                            class="w-full px-3 py-2 border rounded focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị</label>
                    <input type="text" id="modal-unit-day-<?= $day_number ?>" placeholder="VD: bữa, đêm, vé"
                        class="w-full px-3 py-2 border rounded focus:border-blue-500">
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="modal-included-day-<?= $day_number ?>" checked
                            class="w-5 h-5 text-blue-600 rounded">
                        <span class="text-sm font-medium text-gray-700">Bao gồm trong giá tour</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                    <textarea id="modal-notes-day-<?= $day_number ?>" rows="3"
                        class="w-full px-3 py-2 border rounded focus:border-blue-500"
                        placeholder="Ghi chú thêm..."></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeAddServiceModal(<?= $day_number ?>)"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Hủy
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Thêm dịch vụ
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    window.dayServiceCounter = window.dayServiceCounter || {};
    window.dayServiceCounter[<?= $day_number ?>] = <?= count($day_services) ?>;

    function openAddServiceModal(dayNumber) {
        document.getElementById(`add-service-modal-day-${dayNumber}`).classList.remove('hidden');
    }

    function closeAddServiceModal(dayNumber) {
        document.getElementById(`add-service-modal-day-${dayNumber}`).classList.add('hidden');
        document.getElementById(`add-service-form-day-${dayNumber}`).reset();
    }

    function loadServiceInfo(select, dayNumber) {
        const serviceId = select.value;
        if (!serviceId) return;

        // AJAX load service info để auto-fill giá
        fetch(`?act=admin&module=tours&action=getServiceInfo&id=${serviceId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const priceInput = document.getElementById(`modal-unit-price-day-${dayNumber}`);
                    const unitInput = document.getElementById(`modal-unit-day-${dayNumber}`);
                    if (data.data.unit_price > 0) {
                        priceInput.value = data.data.unit_price;
                    }
                    if (data.data.unit) {
                        unitInput.value = data.data.unit;
                    }
                }
            });
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
                    <input type="checkbox" 
                           name="day_service_included[${dayNumber}][${counter}]"
                           value="1"
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
                            <span class="font-medium ml-2">
                                ${formatNumber(quantity)} ${unit || ''}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-600">Tổng:</span>
                            <span class="font-medium text-blue-600 ml-2">
                                ${formatCurrency(total)}
                            </span>
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
                    <button type="button" 
                            onclick="removeDayService(this)"
                            class="text-red-500 hover:text-red-700">
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

    function removeDayService(btn) {
        if (confirm('Bạn có chắc muốn xóa dịch vụ này?')) {
            btn.closest('.day-service-item').remove();
            const container = btn.closest('.day-services-editor');
            const dayNumber = container.getAttribute('data-day');
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

        document.getElementById(`day-total-${dayNumber}`).textContent = formatCurrency(total) + '/người';
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(amount));
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('vi-VN', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(num);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function addServiceToDayServices(dayNumber, serviceId, providerId) {
        // Auto-fill modal and open it
        document.getElementById(`modal-service-id-day-${dayNumber}`).value = serviceId;
        if (providerId) {
            document.getElementById(`modal-service-provider-id-day-${dayNumber}`).value = providerId;
        }
        loadServiceInfo(document.getElementById(`modal-service-id-day-${dayNumber}`), dayNumber);
        openAddServiceModal(dayNumber);
    }
</script>