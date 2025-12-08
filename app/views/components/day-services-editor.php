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
    <div class="mb-3 lg:mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 lg:gap-3">
        <h4 class="text-base lg:text-lg font-bold text-primary-700 flex items-center gap-2">
            <i data-lucide="briefcase" class="w-4 h-4 lg:w-5 lg:h-5 text-accent"></i>
            Dịch vụ - Day <?= $day_number ?>
        </h4>
        <button type="button" onclick="openAddServiceModal(<?= $day_number ?>)"
            class="px-3 lg:px-4 py-2 lg:py-2.5 bg-gradient-to-r from-success-gradient-from to-success-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-xs lg:text-sm flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Thêm dịch vụ
        </button>
    </div>

    <!-- Services List -->
    <div id="day-services-list-day-<?= $day_number ?>" class="space-y-2 lg:space-y-3 mb-3 lg:mb-4">
        <?php if (empty($day_services)): ?>
            <div class="text-primary-500 text-center py-6 lg:py-8 bg-primary-50 rounded-xl border-2 border-dashed border-primary-200">
                <i data-lucide="briefcase" class="w-12 h-12 lg:w-16 lg:h-16 mx-auto mb-2 lg:mb-3 text-primary-300"></i>
                <p class="text-sm lg:text-base font-semibold">Chưa có dịch vụ nào cho ngày này</p>
                <p class="text-xs lg:text-sm mt-1 text-primary-400">Click "Thêm dịch vụ" để bắt đầu</p>
            </div>
        <?php else: ?>
            <?php foreach ($day_services as $idx => $service): ?>
                <div class="day-service-item bg-panel border border-primary-100 rounded-xl p-3 lg:p-4 shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-start gap-3 lg:gap-4">
                        <div class="flex-shrink-0 mt-1">
                            <input type="checkbox" name="day_service_included[<?= $day_number ?>][<?= $idx ?>]" value="1"
                                <?= ($service['is_included_in_price'] ?? 1) ? 'checked' : '' ?>
                                onchange="updateDayServiceTotal(<?= $day_number ?>)" class="w-4 h-4 lg:w-5 lg:h-5 text-accent rounded-lg border-primary-200 focus:ring-2 focus:ring-accent">
                        </div>

                        <div class="flex-1">
                            <div class="font-bold text-primary-700 text-sm lg:text-base">
                                <?= htmlspecialchars($service['service_name'] ?? 'N/A') ?>
                                <?php if (!empty($service['service_provider_name'])): ?>
                                    <span class="text-xs lg:text-sm text-primary-500 font-normal">-
                                        <?= htmlspecialchars($service['service_provider_name']) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="mt-2 lg:mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-3 text-xs lg:text-sm">
                                <div>
                                    <span class="text-primary-500">Đơn giá/người:</span>
                                    <span class="font-semibold text-primary-700 ml-2"><?= number_format($service['unit_price'] ?? 0, 0, ',', '.') ?>đ</span>
                                </div>
                                <div>
                                    <span class="text-primary-500">Số lượng:</span>
                                    <span class="font-semibold text-primary-700 ml-2">
                                        <?= number_format($service['quantity'] ?? 1, 2, ',', '.') ?>
                                        <?php if (!empty($service['unit'])): ?>
                                            <?= htmlspecialchars($service['unit']) ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-primary-500">Tổng:</span>
                                    <span class="font-bold text-accent ml-2">
                                        <?= number_format(($service['unit_price'] ?? 0) * ($service['quantity'] ?? 1), 0, ',', '.') ?>đ
                                    </span>
                                </div>
                                <div>
                                    <span class="text-primary-500">Bao gồm:</span>
                                    <span class="font-semibold ml-2 <?= ($service['is_included_in_price'] ?? 1) ? 'text-success' : 'text-primary-400' ?>">
                                        <?= ($service['is_included_in_price'] ?? 1) ? 'Có' : 'Không' ?>
                                    </span>
                                </div>
                            </div>

                            <?php if (!empty($service['notes'])): ?>
                                <div class="mt-2 lg:mt-3 text-xs lg:text-sm text-primary-500 flex items-start gap-2">
                                    <i data-lucide="file-text" class="w-3 h-3 lg:w-4 lg:h-4 mt-0.5 flex-shrink-0"></i>
                                    <span><?= htmlspecialchars($service['notes']) ?></span>
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
                            <button type="button" onclick="removeDayService(this)" class="text-danger hover:opacity-80 transition-all p-1 lg:p-2 rounded-lg hover:bg-danger-bg">
                                <i data-lucide="trash-2" class="w-4 h-4 lg:w-5 lg:h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Day Total -->
    <div class="bg-info-bg border border-info rounded-xl p-3 lg:p-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <span class="font-bold text-info-dark text-sm lg:text-base flex items-center gap-2">
                <i data-lucide="calculator" class="w-4 h-4 lg:w-5 lg:h-5"></i>
                Tổng Day <?= $day_number ?>:
            </span>
            <span id="day-total-<?= $day_number ?>" class="text-lg lg:text-xl font-bold text-info-dark">
                <?= number_format($day_total, 0, ',', '.') ?>đ/người
            </span>
        </div>
        <p class="text-xs lg:text-sm text-info-text mt-1 lg:mt-2">(Chỉ tính các dịch vụ được đánh dấu "Bao gồm trong giá")</p>
    </div>
</div>

<!-- Add Service Modal -->
<div id="add-service-modal-day-<?= $day_number ?>"
    class="hidden fixed inset-0 bg-primary-900 bg-opacity-60 z-50 flex items-center justify-center p-4">
    <div class="bg-panel rounded-2xl shadow-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto border-l-4 border-accent">
        <div class="p-4 lg:p-6 border-b border-primary-100">
            <h3 class="text-base lg:text-lg font-bold text-primary-700 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 lg:w-6 lg:h-6 text-accent"></i>
                Thêm dịch vụ - Day <?= $day_number ?>
            </h3>
        </div>

        <form id="add-service-form-day-<?= $day_number ?>" class="p-4 lg:p-6"
            onsubmit="saveDayService(event, <?= $day_number ?>); return false;">
            <div class="space-y-3 lg:space-y-4">
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Chọn dịch vụ <span
                            class="text-danger">*</span></label>
                    <select id="modal-service-id-day-<?= $day_number ?>" required
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
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
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Chọn nhà dịch vụ</label>
                    <select id="modal-service-provider-id-day-<?= $day_number ?>"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                        <option value="">-- Chọn nhà dịch vụ --</option>
                        <?php foreach ($service_providers as $provider): ?>
                            <option value="<?= $provider['id'] ?>"><?= htmlspecialchars($provider['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-4">
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Đơn giá/người <span
                                class="text-danger">*</span></label>
                        <input type="number" id="modal-unit-price-day-<?= $day_number ?>" step="1000" min="0" required
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    </div>

                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số lượng</label>
                        <input type="number" id="modal-quantity-day-<?= $day_number ?>" step="0.01" min="0.01" value="1"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    </div>
                </div>

                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Đơn vị</label>
                    <input type="text" id="modal-unit-day-<?= $day_number ?>" placeholder="VD: bữa, đêm, vé"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="modal-included-day-<?= $day_number ?>" checked
                            class="w-4 h-4 lg:w-5 lg:h-5 text-accent rounded-lg border-primary-200 focus:ring-2 focus:ring-accent">
                        <span class="text-xs lg:text-sm font-semibold text-primary-700">Bao gồm trong giá tour</span>
                    </label>
                </div>

                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ghi chú</label>
                    <textarea id="modal-notes-day-<?= $day_number ?>" rows="3"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                        placeholder="Ghi chú thêm..."></textarea>
                </div>
            </div>

            <div class="mt-4 lg:mt-6 flex flex-col sm:flex-row justify-end gap-2 lg:gap-3">
                <button type="button" onclick="closeAddServiceModal(<?= $day_number ?>)"
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl font-semibold hover:bg-primary-100 transition-colors text-sm lg:text-base">
                    Hủy
                </button>
                <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base">
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
        const providerSelect = document.getElementById(`modal-service-provider-id-day-${dayNumber}`);
        
        if (!serviceId) {
            // Reset providers dropdown nếu không chọn service
            if (providerSelect) {
                providerSelect.innerHTML = '<option value="">-- Chọn nhà dịch vụ --</option>';
                // Reload all providers
                if (typeof serviceProviders !== 'undefined' && serviceProviders) {
                    let options = '<option value="">-- Chọn nhà dịch vụ --</option>';
                    if (Array.isArray(serviceProviders)) {
                        serviceProviders.forEach(provider => {
                            options += `<option value="${provider.id}">${escapeHtml(provider.name)}</option>`;
                        });
                    } else {
                        Object.entries(serviceProviders).forEach(([id, provider]) => {
                            const name = typeof provider === 'object' ? provider.name : provider;
                            options += `<option value="${id}">${escapeHtml(name)}</option>`;
                        });
                    }
                    providerSelect.innerHTML = options;
                }
            }
            return;
        }

        // Show loading
        if (providerSelect) {
            providerSelect.innerHTML = '<option value="">Đang tải...</option>';
            providerSelect.disabled = true;
        }

        // Get tour start date để load giá theo mùa (nếu có)
        const tourStartDate = document.querySelector('[name="start_date"]')?.value || null;
        let url = `?act=admin&module=tours&action=getServiceInfo&id=${serviceId}`;
        if (tourStartDate) {
            url += `&date=${tourStartDate}`;
        }

        // AJAX load service info để auto-fill giá và filter providers
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
        <div class="day-service-item bg-panel border border-primary-100 rounded-xl p-3 lg:p-4 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-start gap-3 lg:gap-4">
                <div class="flex-shrink-0 mt-1">
                    <input type="checkbox" 
                           name="day_service_included[${dayNumber}][${counter}]"
                           value="1"
                           ${included ? 'checked' : ''}
                           onchange="updateDayServiceTotal(${dayNumber})"
                           class="w-4 h-4 lg:w-5 lg:h-5 text-accent rounded-lg border-primary-200 focus:ring-2 focus:ring-accent">
                </div>
                
                <div class="flex-1">
                    <div class="font-bold text-primary-700 text-sm lg:text-base">
                        ${escapeHtml(serviceName)}
                        ${providerName ? `<span class="text-xs lg:text-sm text-primary-500 font-normal">- ${escapeHtml(providerName)}</span>` : ''}
                    </div>
                    
                    <div class="mt-2 lg:mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-3 text-xs lg:text-sm">
                        <div>
                            <span class="text-primary-500">Đơn giá/người:</span>
                            <span class="font-semibold text-primary-700 ml-2">${formatCurrency(unitPrice)}</span>
                        </div>
                        <div>
                            <span class="text-primary-500">Số lượng:</span>
                            <span class="font-semibold text-primary-700 ml-2">
                                ${formatNumber(quantity)} ${unit || ''}
                            </span>
                        </div>
                        <div>
                            <span class="text-primary-500">Tổng:</span>
                            <span class="font-bold text-accent ml-2">
                                ${formatCurrency(total)}
                            </span>
                        </div>
                        <div>
                            <span class="text-primary-500">Bao gồm:</span>
                            <span class="font-semibold ml-2 ${included ? 'text-success' : 'text-primary-400'}">
                                ${included ? 'Có' : 'Không'}
                            </span>
                        </div>
                    </div>
                    
                    ${notes ? `
                        <div class="mt-2 lg:mt-3 text-xs lg:text-sm text-primary-500 flex items-start gap-2">
                            <i data-lucide="file-text" class="w-3 h-3 lg:w-4 lg:h-4 mt-0.5 flex-shrink-0"></i>
                            <span>${escapeHtml(notes)}</span>
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
                            class="text-danger hover:opacity-80 transition-all p-1 lg:p-2 rounded-lg hover:bg-danger-bg">
                        <i data-lucide="trash-2" class="w-4 h-4 lg:w-5 lg:h-5"></i>
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
        
        // Initialize Lucide icons for new content
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
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