<?php
/**
 * ADMIN - SUPPLIER-SERVICES INDEX
 * Trang quản lý quan hệ Nhà cung cấp ↔ Dịch vụ
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class=" mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý Dịch vụ theo Nhà cung cấp</h1>
        <a href="?act=admin&module=suppliers" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại Nhà cung cấp
        </a>
    </div>

    <?php if (empty($suppliers)): ?>
        <div class="bg-panel rounded-2xl shadow-sm p-8 lg:p-12 text-center border border-primary-100">
            <div class="text-primary-300 mb-4 flex justify-center">
                <i data-lucide="building-2" class="w-16 h-16"></i>
            </div>
            <p class="mb-4 text-primary-600 font-semibold text-sm lg:text-base">Chưa có nhà cung cấp nào</p>
            <a href="?act=admin&module=suppliers&action=create"
                class="inline-flex items-center gap-2 px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Thêm nhà cung cấp
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($suppliers as $supplier): ?>
            <div class="bg-panel rounded-2xl shadow-sm mb-4 lg:mb-6 overflow-hidden border border-primary-100">
                <!-- Supplier Header -->
                <div class="bg-primary-50 px-4 lg:px-6 py-3 lg:py-4 border-b border-primary-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div>
                        <h2 class="text-base lg:text-lg font-bold text-primary-700">
                            <?= htmlspecialchars($supplier['company_name']) ?>
                        </h2>
                        <p class="text-xs lg:text-sm text-primary-500 mt-1">
                            Mã: <span class="font-mono text-primary-700"><?= $supplier['supplier_code'] ?></span>
                            <?php if ($supplier['contact_person']): ?>
                                | Liên hệ: <span class="text-primary-700"><?= htmlspecialchars($supplier['contact_person']) ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <a href="?act=admin&module=supplier-services&action=add-service&supplier_id=<?= $supplier['id'] ?>"
                        class="w-full sm:w-auto px-3 lg:px-4 py-1.5 lg:py-2 bg-accent-100 text-accent-700 rounded-xl hover:bg-accent-200 font-semibold transition-colors text-xs lg:text-sm flex items-center justify-center gap-1">
                        <i data-lucide="plus" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                        Thêm dịch vụ
                    </a>
                </div>

                <!-- Services Table -->
                <div class="p-4 lg:p-6">
                    <?php if (empty($supplier['services'])): ?>
                        <p class="text-primary-500 text-xs lg:text-sm italic text-center py-4">
                            Chưa có dịch vụ nào.
                            <a href="?act=admin&module=supplier-services&action=add-service&supplier_id=<?= $supplier['id'] ?>"
                                class="text-accent hover:text-accent-dark font-semibold ml-1">Thêm dịch vụ ngay</a>
                        </p>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs lg:text-sm min-w-[600px]">
                                <thead class="bg-primary-50">
                                    <tr>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left font-semibold text-primary-600">Loại</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left font-semibold text-primary-600">Tên dịch vụ</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left font-semibold text-primary-600">Đơn vị</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-right font-semibold text-primary-600">Giá dự kiến</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-center font-semibold text-primary-600">Trạng thái</th>
                                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-right font-semibold text-primary-600">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-primary-100">
                                    <?php foreach ($supplier['services'] as $service): ?>
                                        <tr class="hover:bg-primary-50 transition-colors">
                                            <td class="px-3 lg:px-4 py-2 lg:py-3">
                                                <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase bg-accent-100 text-accent-700">
                                                    <?= htmlspecialchars($service['service_type_name']) ?>
                                                </span>
                                            </td>
                                            <td class="px-3 lg:px-4 py-2 lg:py-3 font-semibold text-primary-700">
                                                <?= htmlspecialchars($service['name']) ?>
                                            </td>
                                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-primary-600">
                                                <?= htmlspecialchars($service['unit'] ?? '-') ?>
                                            </td>
                                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-right font-mono text-primary-700">
                                                <?= $service['estimated_price'] ? number_format($service['estimated_price']) . ' ₫' : '-' ?>
                                            </td>
                                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                                <?php if ($service['status'] == 'active'): ?>
                                                    <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase bg-success-bg text-success-text">Active</span>
                                                <?php else: ?>
                                                    <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase bg-primary-100 text-primary-600">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="?act=admin&module=services&action=edit&id=<?= $service['id'] ?>"
                                                        class="text-accent hover:text-accent-dark font-semibold flex items-center gap-1" title="Sửa">
                                                        <i data-lucide="pencil" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                                        Sửa
                                                    </a>
                                                    <?php if ($service['status'] == 'active'): ?>
                                                        <a href="?act=admin&module=supplier-services&action=delete-service&service_id=<?= $service['id'] ?>"
                                                            onclick="return confirm('Xác nhận vô hiệu hóa dịch vụ này?')"
                                                            class="text-danger-text hover:text-danger-dark font-semibold flex items-center gap-1" title="Xóa">
                                                            <i data-lucide="trash-2" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                                            Xóa
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>