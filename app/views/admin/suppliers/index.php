<?php
/**
 * ADMIN - DANH SÁCH SUPPLIERS
 * Variables: $suppliers, $total, $total_pages, $current_page
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Quản lý nhà cung cấp</h1>
        <a href="?act=admin&module=suppliers&action=create"
            class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600">
            + Thêm mới
        </a>
    </div>

    <!-- Search & Filter -->
    <form method="GET" class="bg-white p-4 rounded mb-4">
        <input type="hidden" name="act" value="admin">
        <input type="hidden" name="module" value="suppliers">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>"
                    placeholder="Tìm theo tên, email, SĐT..."
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
            </div>
            <div>
                <select name="status"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" <?= ($_GET['status'] ?? '') == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="inactive" <?= ($_GET['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Vô hiệu
                    </option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                    Tìm kiếm
                </button>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Mã NCC</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Tên công ty</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Người liên hệ</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Email</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">SĐT</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Trạng thái</th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-slate-700">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if (empty($suppliers)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                            Chưa có nhà cung cấp nào.
                            <a href="?act=admin&module=suppliers&action=create" class="text-accent hover:underline">Thêm
                                mới</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($suppliers as $sup): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-mono">
                                    <?= htmlspecialchars($sup['supplier_code']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium"><?= htmlspecialchars($sup['company_name']) ?></td>
                            <td class="px-4 py-3 text-sm text-slate-600"><?= htmlspecialchars($sup['contact_person'] ?? '-') ?>
                            </td>
                            <td class="px-4 py-3 text-sm"><?= htmlspecialchars($sup['email'] ?? '-') ?></td>
                            <td class="px-4 py-3 text-sm"><?= htmlspecialchars($sup['phone'] ?? '-') ?></td>
                            <td class="px-4 py-3 text-sm">
                                <?php if ($sup['status'] == 'active'): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Hoạt động</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">Vô hiệu</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <a href="?act=admin&module=suppliers&action=edit&id=<?= $sup['id'] ?>"
                                    class="text-blue-600 hover:text-blue-800 mr-3">
                                    ✏️ Sửa
                                </a>
                                <a href="?act=admin&module=suppliers&action=delete&id=<?= $sup['id'] ?>"
                                    onclick="return confirm('Xác nhận vô hiệu hóa nhà cung cấp này?')"
                                    class="text-red-600 hover:text-red-800">
                                    🗑️ Xóa
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
                <a href="?act=admin&module=suppliers&page=<?= $i ?>&search=<?= $_GET['search'] ?? '' ?>&status=<?= $_GET['status'] ?? '' ?>"
                    class="px-3 py-1 rounded <?= $i == $current_page ? 'bg-accent text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>