<?php
/**
 * Booking Services - List & Manage
 * 
 * Variables:
 * - $booking: Thông tin booking
 * - $services: Danh sách dịch vụ
 * - $totals: Tổng chi phí
 * - $availableServices: Dịch vụ có sẵn để thêm
 * - $suppliers: Danh sách NCC
 */
?>

<div class="max-w-8xl mx-auto p-4 lg:p-8">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs lg:text-sm text-primary-500 mb-4">
        <a href="?act=admin&module=bookings" class="hover:text-primary-700 font-semibold">Bookings</a>
        <i data-lucide="chevron-right" class="w-3 h-3 lg:w-4 lg:h-4"></i>
        <a href="?act=admin&module=bookings&action=show&id=<?= $booking['id'] ?>" class="hover:text-primary-700 font-semibold">
            <?= htmlspecialchars($booking['booking_code']) ?>
        </a>
        <i data-lucide="chevron-right" class="w-3 h-3 lg:w-4 lg:h-4"></i>
        <span class="text-primary-700 font-semibold">Dịch vụ</span>
    </div>

    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý Dịch vụ</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">
                Booking: <span class="font-semibold text-primary-700"><?= htmlspecialchars($booking['booking_code']) ?></span> - 
                Tour: <span class="font-semibold text-primary-700"><?= htmlspecialchars($booking['tour_name']) ?></span>
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <button onclick="openAddModal()" 
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Thêm dịch vụ
            </button>
            <form action="?act=admin&module=booking-services&action=copyFromTour" method="POST" class="inline">
                <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-100 text-primary-700 rounded-xl hover:bg-primary-200 font-semibold transition-colors text-sm lg:text-base"
                        onclick="return confirm('Copy tất cả dịch vụ từ tour template?')">
                    <i data-lucide="copy" class="w-4 h-4 inline-block mr-1"></i>
                    Copy từ Tour
                </button>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-4 lg:mb-6">
        <div class="bg-panel p-3 lg:p-4 rounded-2xl border border-primary-100 shadow-sm">
            <div class="text-xs lg:text-sm text-primary-500 mb-1 lg:mb-2">Tổng chi phí dịch vụ</div>
            <div class="text-lg lg:text-xl font-bold text-primary-700"><?= number_format($totals['total_cost'] ?? 0) ?> đ</div>
        </div>
        <div class="bg-panel p-3 lg:p-4 rounded-2xl border border-success shadow-sm">
            <div class="text-xs lg:text-sm text-success-text mb-1 lg:mb-2">Đã thanh toán NCC</div>
            <div class="text-lg lg:text-xl font-bold text-success-dark"><?= number_format($totals['total_paid'] ?? 0) ?> đ</div>
        </div>
        <div class="bg-panel p-3 lg:p-4 rounded-2xl border border-danger shadow-sm">
            <div class="text-xs lg:text-sm text-danger-text mb-1 lg:mb-2">Còn nợ NCC</div>
            <div class="text-lg lg:text-xl font-bold text-danger-dark"><?= number_format($totals['total_remaining'] ?? 0) ?> đ</div>
        </div>
        <div class="bg-panel p-3 lg:p-4 rounded-2xl border border-accent shadow-sm">
            <div class="text-xs lg:text-sm text-primary-500 mb-1 lg:mb-2">Số dịch vụ</div>
            <div class="text-lg lg:text-xl font-bold text-accent"><?= count($services) ?></div>
        </div>
    </div>

<!-- Services List -->
<div class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px]">
            <thead class="bg-primary-50">
                <tr>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-600 uppercase tracking-wider">Dịch vụ</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-600 uppercase tracking-wider">Nhà cung cấp</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-center text-xs font-semibold text-primary-600 uppercase tracking-wider">SL</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-right text-xs font-semibold text-primary-600 uppercase tracking-wider">Đơn giá</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-right text-xs font-semibold text-primary-600 uppercase tracking-wider">Thành tiền</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-center text-xs font-semibold text-primary-600 uppercase tracking-wider">Ngày SD</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-center text-xs font-semibold text-primary-600 uppercase tracking-wider">TT</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-center text-xs font-semibold text-primary-600 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary-100">
                <?php if (empty($services)): ?>
                    <tr>
                        <td colspan="8" class="px-3 lg:px-4 py-8 lg:py-12 text-center text-primary-500">
                            Chưa có dịch vụ nào. Nhấn "Thêm dịch vụ" hoặc "Copy từ Tour" để bắt đầu.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($services as $service): ?>
                        <tr class="hover:bg-primary-50 transition-colors">
                            <td class="px-3 lg:px-4 py-2 lg:py-3">
                                <div class="font-semibold text-primary-700 text-sm lg:text-base">
                                    <?= htmlspecialchars($service['service_name'] ?: $service['service_name_original']) ?>
                                </div>
                                <div class="text-xs text-primary-500 mt-1">
                                    <?= htmlspecialchars($service['service_type_name'] ?? '') ?>
                                </div>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3">
                                <div class="text-xs lg:text-sm text-primary-600"><?= htmlspecialchars($service['supplier_name'] ?? '-') ?></div>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-center text-xs lg:text-sm text-primary-600">
                                <?= $service['quantity'] ?> <?= htmlspecialchars($service['unit'] ?? '') ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-right text-xs lg:text-sm text-primary-700 font-mono">
                                <?= number_format($service['unit_price']) ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-right font-bold text-sm lg:text-base text-primary-700">
                                <?= number_format($service['total_price']) ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-center text-xs lg:text-sm text-primary-600">
                                <?php if ($service['from_date'] && $service['to_date']): ?>
                                    <?= date('d/m', strtotime($service['from_date'])) ?> - <?= date('d/m', strtotime($service['to_date'])) ?>
                                <?php elseif ($service['service_date']): ?>
                                    <?= date('d/m/Y', strtotime($service['service_date'])) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                <?php
                                $statusColors = [
                                    'pending' => 'bg-warning-bg text-warning-text',
                                    'partial' => 'bg-info-bg text-info-text',
                                    'paid' => 'bg-success-bg text-success-text'
                                ];
                                $statusLabels = [
                                    'pending' => 'Chưa TT',
                                    'partial' => 'TT một phần',
                                    'paid' => 'Đã TT'
                                ];
                                $color = $statusColors[$service['payment_status']] ?? 'bg-primary-100 text-primary-600';
                                $label = $statusLabels[$service['payment_status']] ?? $service['payment_status'];
                                ?>
                                <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase <?= $color ?>"><?= $label ?></span>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openEditModal(<?= htmlspecialchars(json_encode($service)) ?>)" 
                                            class="p-1.5 lg:p-2 text-primary-400 hover:text-accent transition-colors rounded-lg hover:bg-primary-50" title="Sửa">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>
                                    <?php if ($service['paid_amount'] <= 0): ?>
                                        <a href="?act=admin&module=booking-services&action=delete&id=<?= $service['id'] ?>" 
                                           onclick="return confirm('Xóa dịch vụ này?')"
                                           class="p-1.5 lg:p-2 text-primary-400 hover:text-danger-text transition-colors rounded-lg hover:bg-danger-bg/20" title="Xóa">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Back Button -->
<div class="mt-4 lg:mt-6">
    <a href="?act=admin&module=bookings&action=show&id=<?= $booking['id'] ?>" 
       class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 font-semibold text-sm lg:text-base">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Quay lại Booking
    </a>
</div>
</div>

<!-- Add/Edit Modal -->
<div id="serviceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-panel rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-xl border border-primary-100">
        <form id="serviceForm" method="POST" action="">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
            <input type="hidden" name="id" id="form_id">
            
            <div class="px-4 lg:px-6 py-4 border-b border-primary-100 flex justify-between items-center bg-primary-50 rounded-t-2xl">
                <h3 id="modalTitle" class="text-base lg:text-lg font-bold text-primary-700">Thêm dịch vụ</h3>
                <button type="button" onclick="closeModal()" class="text-primary-400 hover:text-primary-600 transition-colors p-1 rounded-lg hover:bg-primary-100">
                    <i data-lucide="x" class="w-5 h-5 lg:w-6 lg:h-6"></i>
                </button>
            </div>
            
            <div class="p-4 lg:p-6 space-y-4 lg:space-y-6">
                <!-- Service Selection -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="serviceSelectRow">
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Dịch vụ *</label>
                        <select name="service_id" id="service_id" required onchange="autoFillService(this.value)"
                                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                            <option value="">-- Chọn dịch vụ --</option>
                            <?php foreach ($availableServices as $svc): ?>
                                <option value="<?= $svc['id'] ?>" 
                                        data-supplier="<?= $svc['supplier_id'] ?>"
                                        data-unit="<?= htmlspecialchars($svc['unit'] ?? '') ?>"
                                        data-price="<?= $svc['estimated_price'] ?>">
                                    <?= htmlspecialchars($svc['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Nhà cung cấp *</label>
                        <select name="supplier_id" id="supplier_id" required
                                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                            <option value="">-- Chọn NCC --</option>
                            <?php foreach ($suppliers as $id => $name): ?>
                                <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Service Name (Override) -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tên dịch vụ (tùy chỉnh)</label>
                    <input type="text" name="service_name" id="service_name"
                           class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                           placeholder="Để trống sẽ lấy tên mặc định">
                </div>
                
                <!-- Quantity & Price -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số lượng *</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" required
                               class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                               onchange="calculateTotal()">
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Đơn vị</label>
                        <input type="text" name="unit" id="unit"
                               class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                               placeholder="VD: phòng, suất, vé...">
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Đơn giá *</label>
                        <input type="number" name="unit_price" id="unit_price" value="0" min="0" required
                               class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                               onchange="calculateTotal()">
                    </div>
                </div>
                
                <!-- Total -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Thành tiền</label>
                    <input type="number" name="total_price" id="total_price" value="0" min="0"
                           class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-100 border border-primary-200 rounded-xl font-bold text-primary-700 text-sm lg:text-base">
                </div>
                
                <!-- Dates -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ngày sử dụng</label>
                        <input type="date" name="service_date" id="service_date"
                               class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Từ ngày</label>
                        <input type="date" name="from_date" id="from_date"
                               class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    </div>
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Đến ngày</label>
                        <input type="date" name="to_date" id="to_date"
                               class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    </div>
                </div>
                
                <!-- Notes -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ghi chú</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                              placeholder="Ghi chú thêm..."></textarea>
                </div>
            </div>
            
            <div class="px-4 lg:px-6 py-4 border-t border-primary-100 flex flex-col sm:flex-row justify-end gap-3 bg-primary-50 rounded-b-2xl">
                <button type="button" onclick="closeModal()" 
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-100 text-primary-700 rounded-xl hover:bg-primary-200 font-semibold transition-colors text-sm lg:text-base">
                    Hủy
                </button>
                <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Thêm dịch vụ';
    document.getElementById('serviceForm').action = '?act=admin&module=booking-services&action=store';
    document.getElementById('form_id').value = '';
    document.getElementById('serviceSelectRow').style.display = 'grid';
    
    // Reset form
    document.getElementById('serviceForm').reset();
    document.getElementById('serviceModal').classList.remove('hidden');
    document.getElementById('serviceModal').classList.add('flex');
}

function openEditModal(service) {
    document.getElementById('modalTitle').textContent = 'Sửa dịch vụ';
    document.getElementById('serviceForm').action = '?act=admin&module=booking-services&action=update';
    document.getElementById('form_id').value = service.id;
    document.getElementById('serviceSelectRow').style.display = 'none'; // Hide service/supplier select when editing
    
    // Fill form
    document.getElementById('service_name').value = service.service_name || '';
    document.getElementById('quantity').value = service.quantity || 1;
    document.getElementById('unit').value = service.unit || '';
    document.getElementById('unit_price').value = service.unit_price || 0;
    document.getElementById('total_price').value = service.total_price || 0;
    document.getElementById('service_date').value = service.service_date || '';
    document.getElementById('from_date').value = service.from_date || '';
    document.getElementById('to_date').value = service.to_date || '';
    document.getElementById('notes').value = service.notes || '';
    
    document.getElementById('serviceModal').classList.remove('hidden');
    document.getElementById('serviceModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('serviceModal').classList.add('hidden');
    document.getElementById('serviceModal').classList.remove('flex');
}

function autoFillService(serviceId) {
    const option = document.querySelector(`#service_id option[value="${serviceId}"]`);
    if (option) {
        document.getElementById('supplier_id').value = option.dataset.supplier || '';
        document.getElementById('unit').value = option.dataset.unit || '';
        document.getElementById('unit_price').value = option.dataset.price || 0;
        calculateTotal();
    }
}

function calculateTotal() {
    const qty = parseFloat(document.getElementById('quantity').value) || 0;
    const price = parseFloat(document.getElementById('unit_price').value) || 0;
    document.getElementById('total_price').value = qty * price;
}

// Close modal on outside click
document.getElementById('serviceModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

