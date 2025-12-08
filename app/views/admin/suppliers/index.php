<?php
/**
 * ADMIN - DANH SÁCH SUPPLIERS
 * Variables: $suppliers, $total, $total_pages, $current_page
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý nhà cung cấp</h1>
        <a href="?act=admin&module=suppliers&action=create"
            class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Thêm mới
        </a>
    </div>

    <!-- Search & Filter -->
    <form method="GET" class="bg-panel p-4 lg:p-5 rounded-2xl shadow-sm border border-primary-100 mb-4">
        <input type="hidden" name="act" value="admin">
        <input type="hidden" name="module" value="suppliers">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 lg:gap-4">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>"
                    placeholder="Tìm theo tên, email, SĐT..."
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
            </div>
            <div>
                <select name="status"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base mb-2 lg:mb-0">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" <?= ($_GET['status'] ?? '') == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="inactive" <?= ($_GET['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Vô hiệu
                    </option>
                </select>
            </div>
            <div class="lg:col-span-3 flex gap-2">
                <button type="submit" class="flex-1 lg:flex-none px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-600 hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Tìm kiếm
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
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Mã NCC</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Tên công ty</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Người liên hệ</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Email</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">SĐT</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Trạng thái</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-right text-xs uppercase font-semibold text-primary-700 tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary-100">
                <?php if (empty($suppliers)): ?>
                    <tr>
                        <td colspan="7" class="px-3 lg:px-4 py-6 lg:py-8 text-center text-primary-500 text-sm">
                            Chưa có nhà cung cấp nào.
                            <a href="?act=admin&module=suppliers&action=create" class="text-accent hover:text-accent-hover font-semibold ml-2">Thêm
                                mới</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($suppliers as $sup): ?>
                        <tr class="hover:bg-primary-50 transition-colors">
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm">
                                <span class="px-2 lg:px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-xs font-mono font-semibold">
                                    <?= htmlspecialchars($sup['supplier_code']) ?>
                                </span>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm font-semibold text-primary-700"><?= htmlspecialchars($sup['company_name']) ?></td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-600"><?= htmlspecialchars($sup['contact_person'] ?? '-') ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-600"><?= htmlspecialchars($sup['email'] ?? '-') ?></td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-600"><?= htmlspecialchars($sup['phone'] ?? '-') ?></td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm">
                                <?php if ($sup['status'] == 'active'): ?>
                                    <span class="px-2 lg:px-3 py-1 bg-success-bg text-success-text rounded-full text-xs font-bold uppercase">Hoạt động</span>
                                <?php else: ?>
                                    <span class="px-2 lg:px-3 py-1 bg-primary-100 text-primary-500 rounded-full text-xs font-bold uppercase">Vô hiệu</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-right">
                                <div class="flex items-center justify-end gap-1 lg:gap-2">
                                    <a href="?act=admin&module=suppliers&action=edit&id=<?= $sup['id'] ?>"
                                        class="text-warning-text hover:text-warning-text p-1.5 rounded-xl hover:bg-warning-bg transition-all" title="Sửa">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <a href="?act=admin&module=suppliers&action=delete&id=<?= $sup['id'] ?>"
                                        onclick="return confirm('Xác nhận vô hiệu hóa nhà cung cấp này?')"
                                        class="text-danger-text hover:text-danger-text p-1.5 rounded-xl hover:bg-danger-bg transition-all" title="Xóa">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
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
        <div class="mt-4 flex justify-center gap-2 flex-wrap">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?act=admin&module=suppliers&page=<?= $i ?>&search=<?= $_GET['search'] ?? '' ?>&status=<?= $_GET['status'] ?? '' ?>"
                    class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-sm font-semibold transition-all <?= $i == $current_page ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white shadow-sm' : 'bg-panel border border-primary-100 text-primary-700 hover:bg-primary-50' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>