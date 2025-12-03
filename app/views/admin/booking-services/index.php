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

<div class="mb-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-slate-500 mb-4">
        <a href="?act=admin&module=bookings" class="hover:text-accent">Bookings</a>
        <span>/</span>
        <a href="?act=admin&module=bookings&action=show&id=<?= $booking['id'] ?>" class="hover:text-accent">
            <?= htmlspecialchars($booking['booking_code']) ?>
        </a>
        <span>/</span>
        <span class="text-slate-700">Dịch vụ</span>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-primary">Quản lý Dịch vụ</h1>
            <p class="text-slate-500 mt-1">
                Booking: <span class="font-medium"><?= htmlspecialchars($booking['booking_code']) ?></span> - 
                Tour: <span class="font-medium"><?= htmlspecialchars($booking['tour_name']) ?></span>
            </p>
        </div>
        <div class="flex gap-2">
            <button onclick="openAddModal()" 
                    class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Thêm dịch vụ
            </button>
            <form action="?act=admin&module=booking-services&action=copyFromTour" method="POST" class="inline">
                <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                <button type="submit" class="px-4 py-2 bg-slate-200 text-slate-700 rounded hover:bg-slate-300"
                        onclick="return confirm('Copy tất cả dịch vụ từ tour template?')">
                    Copy từ Tour
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded">
        <div class="text-sm text-slate-500">Tổng chi phí dịch vụ</div>
        <div class="text-xl font-bold text-primary"><?= number_format($totals['total_cost'] ?? 0) ?> đ</div>
    </div>
    <div class="bg-white p-4 rounded">
        <div class="text-sm text-slate-500">Đã thanh toán NCC</div>
        <div class="text-xl font-bold text-green-600"><?= number_format($totals['total_paid'] ?? 0) ?> đ</div>
    </div>
    <div class="bg-white p-4 rounded">
        <div class="text-sm text-slate-500">Còn nợ NCC</div>
        <div class="text-xl font-bold text-red-600"><?= number_format($totals['total_remaining'] ?? 0) ?> đ</div>
    </div>
    <div class="bg-white p-4 rounded">
        <div class="text-sm text-slate-500">Số dịch vụ</div>
        <div class="text-xl font-bold text-accent"><?= count($services) ?></div>
    </div>
</div>

<!-- Services List -->
<div class="bg-white rounded overflow-hidden">
    <table class="w-full">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Dịch vụ</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nhà cung cấp</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">SL</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Đơn giá</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Thành tiền</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Ngày SD</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">TT</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if (empty($services)): ?>
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                        Chưa có dịch vụ nào. Nhấn "Thêm dịch vụ" hoặc "Copy từ Tour" để bắt đầu.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($services as $service): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-primary">
                                <?= htmlspecialchars($service['service_name'] ?: $service['service_name_original']) ?>
                            </div>
                            <div class="text-xs text-slate-400">
                                <?= htmlspecialchars($service['service_type_name'] ?? '') ?>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm"><?= htmlspecialchars($service['supplier_name'] ?? '-') ?></div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?= $service['quantity'] ?> <?= htmlspecialchars($service['unit'] ?? '') ?>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <?= number_format($service['unit_price']) ?>
                        </td>
                        <td class="px-4 py-3 text-right font-medium">
                            <?= number_format($service['total_price']) ?>
                        </td>
                        <td class="px-4 py-3 text-center text-sm">
                            <?php if ($service['from_date'] && $service['to_date']): ?>
                                <?= date('d/m', strtotime($service['from_date'])) ?> - <?= date('d/m', strtotime($service['to_date'])) ?>
                            <?php elseif ($service['service_date']): ?>
                                <?= date('d/m/Y', strtotime($service['service_date'])) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'partial' => 'bg-blue-100 text-blue-700',
                                'paid' => 'bg-green-100 text-green-700'
                            ];
                            $statusLabels = [
                                'pending' => 'Chưa TT',
                                'partial' => 'TT một phần',
                                'paid' => 'Đã TT'
                            ];
                            $color = $statusColors[$service['payment_status']] ?? 'bg-slate-100';
                            $label = $statusLabels[$service['payment_status']] ?? $service['payment_status'];
                            ?>
                            <span class="px-2 py-1 text-xs rounded <?= $color ?>"><?= $label ?></span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openEditModal(<?= htmlspecialchars(json_encode($service)) ?>)" 
                                        class="p-1 text-slate-400 hover:text-accent" title="Sửa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <?php if ($service['paid_amount'] <= 0): ?>
                                    <a href="?act=admin&module=booking-services&action=delete&id=<?= $service['id'] ?>" 
                                       onclick="return confirm('Xóa dịch vụ này?')"
                                       class="p-1 text-slate-400 hover:text-red-500" title="Xóa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
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

<!-- Back Button -->
<div class="mt-6">
    <a href="?act=admin&module=bookings&action=show&id=<?= $booking['id'] ?>" 
       class="inline-flex items-center gap-2 text-slate-600 hover:text-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Quay lại Booking
    </a>
</div>

<!-- Add/Edit Modal -->
<div id="serviceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <form id="serviceForm" method="POST" action="">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
            <input type="hidden" name="id" id="form_id">
            
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 id="modalTitle" class="text-lg font-semibold text-primary">Thêm dịch vụ</h3>
                <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <!-- Service Selection -->
                <div class="grid grid-cols-2 gap-4" id="serviceSelectRow">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Dịch vụ *</label>
                        <select name="service_id" id="service_id" required onchange="autoFillService(this.value)"
                                class="w-full px-3 py-2 border border-slate-200 rounded focus:ring-2 focus:ring-accent focus:border-accent">
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
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nhà cung cấp *</label>
                        <select name="supplier_id" id="supplier_id" required
                                class="w-full px-3 py-2 border border-slate-200 rounded focus:ring-2 focus:ring-accent focus:border-accent">
                            <option value="">-- Chọn NCC --</option>
                            <?php foreach ($suppliers as $id => $name): ?>
                                <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Service Name (Override) -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tên dịch vụ (tùy chỉnh)</label>
                    <input type="text" name="service_name" id="service_name"
                           class="w-full px-3 py-2 border border-slate-200 rounded focus:ring-2 focus:ring-accent focus:border-accent"
                           placeholder="Để trống sẽ lấy tên mặc định">
                </div>
                
                <!-- Quantity & Price -->
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Số lượng *</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" required
                               class="w-full px-3 py-2 border border-slate-200 rounded focus:ring-2 focus:ring-accent focus:border-accent"
                               onchange="calculateTotal()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Đơn vị</label>
                        <input type="text" name="unit" id="unit"
                               class="w-full px-3 py-2 border border-slate-200 rounded focus:ring-2 focus:ring-accent focus:border-accent"
                               placeholder="VD: phòng, suất, vé...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Đơn giá *</label>
                        <input type="number" name="unit_price" id="unit_price" value="0" min="0" required
                               class="w-full px-3 py-2 border border-slate-200 rounded focus:ring-2 focus:ring-accent focus:border-accent"
                               onchange="calculateTotal()">
                    </div>
                </div>
                
                <!-- Total -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Thành tiền</label>
                    <input type="number" name="total_price" id="total_price" value="0" min="0"
                           class="w-full px-3 py-2 border border-slate-200 rounded bg-slate-50 font-medium">
                </div>
                
                <!-- Dates -->
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Ngày sử dụng</label>
                        <input type="date" name="service_date" id="service_date"
                               class="w-full px-3 py-2 border border-slate-200 rounded focus:ring-2 focus:ring-accent focus:border-accent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Từ ngày</label>
                        <input type="date" name="from_date" id="from_date"
                               class="w-full px-3 py-2 border border-slate-200 rounded focus:ring-2 focus:ring-accent focus:border-accent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Đến ngày</label>
                        <input type="date" name="to_date" id="to_date"
                               class="w-full px-3 py-2 border border-slate-200 rounded focus:ring-2 focus:ring-accent focus:border-accent">
                    </div>
                </div>
                
                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Ghi chú</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="w-full px-3 py-2 border border-slate-200 rounded focus:ring-2 focus:ring-accent focus:border-accent"
                              placeholder="Ghi chú thêm..."></textarea>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" 
                        class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded">
                    Hủy
                </button>
                <button type="submit" class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600">
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

