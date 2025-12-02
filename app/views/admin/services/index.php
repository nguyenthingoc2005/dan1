<?php
/**
 * ADMIN - DANH SÁCH SERVICES
 * Variables: $services, $total, $total_pages, $current_page, $service_types, $suppliers
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Quản lý dịch vụ</h1>
        <a href="?act=admin&module=services&action=create"
            class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600">
            + Thêm mới
        </a>
    </div>

    <!-- Search & Filter -->
    <form method="GET" class="bg-white p-4 rounded mb-4">
        <input type="hidden" name="act" value="admin">
        <input type="hidden" name="module" value="services">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>" placeholder="Tên dịch vụ..."
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
            </div>

            <!-- Filter Type -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Loại dịch vụ</label>
                <select name="service_type_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                    <option value="">Tất cả loại</option>
                    <?php foreach ($service_types as $type): ?>
                        <option value="<?= $type['id'] ?>" <?= ($_GET['service_type_id'] ?? '') == $type['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filter Supplier -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nhà cung cấp</label>
                <select name="supplier_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                    <option value="">Tất cả nhà cung cấp</option>
                    <?php foreach ($suppliers as $sup): ?>
                        <option value="<?= $sup['id'] ?>" <?= ($_GET['supplier_id'] ?? '') == $sup['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sup['company_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filter Status & Button -->
            <div class="flex gap-2 items-end">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                        <option value="">Tất cả</option>
                        <option value="active" <?= ($_GET['status'] ?? '') == 'active' ? 'selected' : '' ?>>Hoạt động
                        </option>
                        <option value="inactive" <?= ($_GET['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Vô hiệu
                        </option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600 h-[42px]">
                    Lọc
                </button>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Tên dịch vụ</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Loại</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Nhà cung cấp</th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-slate-700">Đơn giá</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-slate-700">Sức chứa</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-slate-700">Trạng thái</th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-slate-700">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if (empty($services)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                            Chưa có dịch vụ nào.
                            <a href="?act=admin&module=services&action=create" class="text-accent hover:underline">Thêm
                                mới</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($services as $svc): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-sm font-medium">
                                <?= htmlspecialchars($svc['name']) ?>
                                <?php if ($svc['availability'] == 'unavailable'): ?>
                                    <span class="ml-2 text-xs text-red-500">(Hết chỗ)</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs">
                                    <?= htmlspecialchars($svc['service_type_name']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                <?= htmlspecialchars($svc['supplier_name']) ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-mono">
                                <?= number_format($svc['unit_price']) ?> ₫
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                <?= $svc['capacity'] ? number_format($svc['capacity']) : '-' ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                <?php if ($svc['status'] == 'active'): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Active</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <a href="?act=admin&module=services&action=edit&id=<?= $svc['id'] ?>"
                                    class="text-blue-600 hover:text-blue-800 mr-3">
                                    ✏️
                                </a>
                                <a href="?act=admin&module=services&action=delete&id=<?= $svc['id'] ?>"
                                    onclick="return confirm('Xác nhận vô hiệu hóa dịch vụ này?')"
                                    class="text-red-600 hover:text-red-800">
                                    🗑️
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="mt-4 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?act=admin&module=services&page=<?= $i ?>&search=<?= $_GET['search'] ?? '' ?>&service_type_id=<?= $_GET['service_type_id'] ?? '' ?>&supplier_id=<?= $_GET['supplier_id'] ?? '' ?>&status=<?= $_GET['status'] ?? '' ?>"
                    class="px-3 py-1 rounded <?= $i == $current_page ? 'bg-accent text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>