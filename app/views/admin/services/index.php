<?php
/**
 * ADMIN - DANH SÁCH SERVICES
 * Variables: $services, $total, $total_pages, $current_page, $service_types, $suppliers
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý dịch vụ</h1>
        <a href="?act=admin&module=services&action=create"
            class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Thêm mới
        </a>
    </div>

    <!-- Search & Filter -->
    <form method="GET" class="bg-panel p-4 lg:p-5 rounded-2xl shadow-sm border border-primary-100 mb-4">
        <input type="hidden" name="act" value="admin">
        <input type="hidden" name="module" value="services">

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-3 lg:gap-4">
            <!-- Search -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tìm kiếm</label>
                <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>" placeholder="Tên dịch vụ..."
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
            </div>

            <!-- Filter Type -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Loại dịch vụ</label>
                <select name="service_type_id"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
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
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Nhà cung cấp</label>
                <select name="supplier_id"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
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
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
                    <select name="status"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                        <option value="">Tất cả</option>
                        <option value="active" <?= ($_GET['status'] ?? '') == 'active' ? 'selected' : '' ?>>Hoạt động
                        </option>
                        <option value="inactive" <?= ($_GET['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Vô hiệu
                        </option>
                    </select>
                </div>
                <button type="submit" class="px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-600 hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2 h-[42px]">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse min-w-[800px]">
                <thead class="bg-primary-50 border-b border-primary-100">
                <tr>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Tên dịch vụ</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Loại</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Nhà cung cấp</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Đơn vị</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-right text-xs uppercase font-semibold text-primary-700 tracking-wider">Giá dự kiến</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-center text-xs uppercase font-semibold text-primary-700 tracking-wider">Trạng thái</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-right text-xs uppercase font-semibold text-primary-700 tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary-100">
                <?php if (empty($services)): ?>
                    <tr>
                        <td colspan="7" class="px-3 lg:px-4 py-6 lg:py-8 text-center text-primary-500 text-sm">
                            Chưa có dịch vụ nào.
                            <a href="?act=admin&module=services&action=create" class="text-accent hover:text-accent-hover font-semibold ml-2">Thêm
                                mới</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($services as $svc): ?>
                        <tr class="hover:bg-primary-50 transition-colors">
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm font-semibold text-primary-700">
                                <?= htmlspecialchars($svc['name']) ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm">
                                <span class="px-2 lg:px-3 py-1 bg-info-bg text-info-text rounded-full text-xs font-bold">
                                    <?= htmlspecialchars($svc['service_type_name']) ?>
                                </span>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-600">
                                <?= htmlspecialchars($svc['supplier_name']) ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-600">
                                <?= htmlspecialchars($svc['unit'] ?? '-') ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-right font-mono text-primary-700">
                                <?= $svc['estimated_price'] ? number_format($svc['estimated_price']) . ' ₫' : '-' ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-center">
                                <?php if ($svc['status'] == 'active'): ?>
                                    <span class="px-2 lg:px-3 py-1 bg-success-bg text-success-text rounded-full text-xs font-bold uppercase">Active</span>
                                <?php else: ?>
                                    <span class="px-2 lg:px-3 py-1 bg-primary-100 text-primary-500 rounded-full text-xs font-bold uppercase">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-right">
                                <div class="flex items-center justify-end gap-1 lg:gap-2">
                                    <a href="?act=admin&module=services&action=edit&id=<?= $svc['id'] ?>"
                                        class="text-warning-text hover:text-warning-text p-1.5 rounded-xl hover:bg-warning-bg transition-all" title="Sửa">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <a href="?act=admin&module=services&action=delete&id=<?= $svc['id'] ?>"
                                        onclick="return confirm('Xác nhận vô hiệu hóa dịch vụ này?')"
                                        class="text-danger-text hover:text-danger-text p-1.5 rounded-xl hover:bg-danger-bg transition-all" title="Xóa">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="mt-4 flex justify-center gap-2 flex-wrap">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?act=admin&module=services&page=<?= $i ?>&search=<?= $_GET['search'] ?? '' ?>&service_type_id=<?= $_GET['service_type_id'] ?? '' ?>&supplier_id=<?= $_GET['supplier_id'] ?? '' ?>&status=<?= $_GET['status'] ?? '' ?>"
                    class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-sm font-semibold transition-all <?= $i == $current_page ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white shadow-sm' : 'bg-panel border border-primary-100 text-primary-700 hover:bg-primary-50' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>