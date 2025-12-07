<?php
/**
 * ADMIN - DANH SÁCH POLICIES
 * Variables: $policies
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">Quản lý chính sách</h1>
            <p class="text-sm text-gray-500 mt-1">Quản lý các chính sách áp dụng cho tour</p>
        </div>
        <a href="?act=admin&module=policies&action=create"
            class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600">
            + Thêm chính sách mới
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <form method="GET" action="?act=admin&module=policies" class="flex gap-4 items-end">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="policies">

            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Loại chính sách</label>
                <select name="policy_type" class="w-full px-3 py-2 border rounded focus:border-accent">
                    <option value="">-- Tất cả --</option>
                    <option value="cancellation" <?= ($_GET['policy_type'] ?? '') == 'cancellation' ? 'selected' : '' ?>>
                        Hủy tour</option>
                    <option value="refund" <?= ($_GET['policy_type'] ?? '') == 'refund' ? 'selected' : '' ?>>Hoàn tiền
                    </option>
                    <option value="payment" <?= ($_GET['policy_type'] ?? '') == 'payment' ? 'selected' : '' ?>>Thanh toán
                    </option>
                    <option value="other" <?= ($_GET['policy_type'] ?? '') == 'other' ? 'selected' : '' ?>>Khác</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select name="status" class="w-full px-3 py-2 border rounded focus:border-accent">
                    <option value="">-- Tất cả --</option>
                    <option value="active" <?= ($_GET['status'] ?? '') == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="inactive" <?= ($_GET['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Vô hiệu
                    </option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                🔍 Lọc
            </button>

            <?php if (!empty($_GET['status']) || !empty($_GET['policy_type'])): ?>
                <a href="?act=admin&module=policies" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    ✕ Xóa bộ lọc
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">ID</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Tên chính sách</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Loại</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Mô tả</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Số tour sử dụng</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Trạng thái</th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-slate-700">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php if (empty($policies)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                            Chưa có chính sách nào.
                            <a href="?act=admin&module=policies&action=create" class="text-accent hover:underline">Thêm
                                mới</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($policies as $policy): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-sm"><?= $policy['id'] ?></td>
                            <td class="px-4 py-3 text-sm font-medium">
                                <a href="?act=admin&module=policies&action=edit&id=<?= $policy['id'] ?>"
                                    class="text-blue-600 hover:text-blue-800">
                                    <?= htmlspecialchars($policy['name']) ?>
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <?php if ($policy['policy_type']): ?>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs">
                                        <?= htmlspecialchars($policy['policy_type']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                <?= htmlspecialchars(substr($policy['description'] ?? '', 0, 50)) ?>
                                <?= strlen($policy['description'] ?? '') > 50 ? '...' : '' ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">
                                    <?= $policy['tour_count'] ?? 0 ?> tour
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <?php if ($policy['status'] == 'active'): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Hoạt động</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">Vô hiệu</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <a href="?act=admin&module=policies&action=edit&id=<?= $policy['id'] ?>"
                                    class="text-blue-600 hover:text-blue-800 mr-3">
                                    ✏️ Sửa
                                </a>
                                <a href="?act=admin&module=policies&action=delete&id=<?= $policy['id'] ?>"
                                    onclick="return confirm('Xác nhận xóa chính sách này? Chính sách đang được sử dụng sẽ không thể xóa.')"
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

    <!-- Note -->
    <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r">
        <p class="text-sm text-blue-700">
            💡 <strong>Lưu ý:</strong> Chính sách có thể được gán cho nhiều tour. Chỉ có thể xóa chính sách khi không
            còn tour nào sử dụng.
        </p>
    </div>
</div>