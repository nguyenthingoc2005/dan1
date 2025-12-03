<?php
/**
 * ADMIN - SUPPLIER-SERVICES INDEX
 * Trang quản lý quan hệ Nhà cung cấp ↔ Dịch vụ
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Quản lý Dịch vụ theo Nhà cung cấp</h1>
        <a href="?act=admin&module=suppliers" class="text-gray-500 hover:text-gray-700">
            ← Quay lại Nhà cung cấp
        </a>
    </div>

    <?php if (empty($suppliers)): ?>
        <div class="bg-white rounded shadow p-8 text-center text-gray-500">
            <p class="mb-4">Chưa có nhà cung cấp nào</p>
            <a href="?act=admin&module=suppliers&action=create"
                class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600">
                + Thêm nhà cung cấp
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($suppliers as $supplier): ?>
            <div class="bg-white rounded shadow mb-4 overflow-hidden">
                <!-- Supplier Header -->
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">
                            <?= htmlspecialchars($supplier['company_name']) ?>
                        </h2>
                        <p class="text-sm text-slate-600 mt-1">
                            Mã: <span class="font-mono"><?= $supplier['supplier_code'] ?></span>
                            <?php if ($supplier['contact_person']): ?>
                                | Liên hệ: <?= htmlspecialchars($supplier['contact_person']) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <a href="?act=admin&module=supplier-services&action=add-service&supplier_id=<?= $supplier['id'] ?>"
                        class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 text-sm">
                        + Thêm dịch vụ
                    </a>
                </div>

                <!-- Services Table -->
                <div class="p-6">
                    <?php if (empty($supplier['services'])): ?>
                        <p class="text-gray-500 text-sm italic">
                            Chưa có dịch vụ nào.
                            <a href="?act=admin&module=supplier-services&action=add-service&supplier_id=<?= $supplier['id'] ?>"
                                class="text-accent hover:underline">Thêm dịch vụ ngay</a>
                        </p>
                    <?php else: ?>
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-3 py-2 text-left">Loại</th>
                                    <th class="px-3 py-2 text-left">Tên dịch vụ</th>
                                    <th class="px-3 py-2 text-left">Đơn vị</th>
                                    <th class="px-3 py-2 text-right">Giá dự kiến</th>
                                    <th class="px-3 py-2 text-center">Trạng thái</th>
                                    <th class="px-3 py-2 text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <?php foreach ($supplier['services'] as $service): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-3">
                                            <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs">
                                                <?= htmlspecialchars($service['service_type_name']) ?>
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 font-medium">
                                            <?= htmlspecialchars($service['name']) ?>
                                        </td>
                                        <td class="px-3 py-3 text-slate-600">
                                            <?= htmlspecialchars($service['unit'] ?? '-') ?>
                                        </td>
                                        <td class="px-3 py-3 text-right font-mono">
                                            <?= $service['estimated_price'] ? number_format($service['estimated_price']) . ' ₫' : '-' ?>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <?php if ($service['status'] == 'active'): ?>
                                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Active</span>
                                            <?php else: ?>
                                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <a href="?act=admin&module=services&action=edit&id=<?= $service['id'] ?>"
                                                class="text-blue-600 hover:text-blue-800 mr-2" title="Sửa">✏️</a>
                                            <?php if ($service['status'] == 'active'): ?>
                                                <a href="?act=admin&module=supplier-services&action=delete-service&service_id=<?= $service['id'] ?>"
                                                    onclick="return confirm('Xác nhận vô hiệu hóa dịch vụ này?')"
                                                    class="text-red-600 hover:text-red-800" title="Xóa">🗑️</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>