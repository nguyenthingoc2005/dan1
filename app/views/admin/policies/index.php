<?php
/**
 * ADMIN - DANH SÁCH POLICIES
 * Variables: $policies
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý chính sách</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Quản lý các chính sách áp dụng cho tour</p>
        </div>
        <a href="?act=admin&module=policies&action=create"
            class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Thêm chính sách mới
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-5 mb-4">
        <form method="GET" action="?act=admin&module=policies" class="flex flex-col lg:flex-row gap-3 lg:gap-4 items-end">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="policies">

            <div class="flex-1 w-full lg:w-auto">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Loại chính sách</label>
                <select name="policy_type" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
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

            <div class="w-full lg:w-auto">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
                <select name="status" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Tất cả --</option>
                    <option value="active" <?= ($_GET['status'] ?? '') == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="inactive" <?= ($_GET['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Vô hiệu
                    </option>
                </select>
            </div>

            <button type="submit" class="w-full lg:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-600 hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="filter" class="w-4 h-4"></i>
                Lọc
            </button>

            <?php if (!empty($_GET['status']) || !empty($_GET['policy_type'])): ?>
                <a href="?act=admin&module=policies" class="w-full lg:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Xóa bộ lọc
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse min-w-[800px]">
                <thead class="bg-primary-50 border-b border-primary-100">
                <tr>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">ID</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Tên chính sách</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Loại</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Mô tả</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Số tour sử dụng</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Trạng thái</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-right text-xs uppercase font-semibold text-primary-700 tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary-100">
                <?php if (empty($policies)): ?>
                    <tr>
                        <td colspan="7" class="px-3 lg:px-4 py-6 lg:py-8 text-center text-primary-500 text-sm">
                            Chưa có chính sách nào.
                            <a href="?act=admin&module=policies&action=create" class="text-accent hover:text-accent-hover font-semibold ml-1">Thêm
                                mới</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($policies as $policy): ?>
                        <tr class="hover:bg-primary-50 transition-colors">
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-700"><?= $policy['id'] ?></td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm font-semibold">
                                <a href="?act=admin&module=policies&action=edit&id=<?= $policy['id'] ?>"
                                    class="text-accent hover:text-accent-hover">
                                    <?= htmlspecialchars($policy['name']) ?>
                                </a>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm">
                                <?php if ($policy['policy_type']): ?>
                                    <span class="px-2 lg:px-3 py-1 rounded-full bg-info-bg text-info-text text-xs font-bold">
                                        <?= htmlspecialchars($policy['policy_type']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-primary-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-600">
                                <?= htmlspecialchars(substr($policy['description'] ?? '', 0, 50)) ?>
                                <?= strlen($policy['description'] ?? '') > 50 ? '...' : '' ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-center">
                                <span class="px-2 lg:px-3 py-1 rounded-full bg-primary-100 text-primary-700 text-xs font-bold">
                                    <?= $policy['tour_count'] ?? 0 ?> tour
                                </span>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm">
                                <?php if ($policy['status'] == 'active'): ?>
                                    <span class="px-2 lg:px-3 py-1 rounded-full bg-success-bg text-success-text text-xs font-bold">Hoạt động</span>
                                <?php else: ?>
                                    <span class="px-2 lg:px-3 py-1 rounded-full bg-primary-100 text-primary-500 text-xs font-bold">Vô hiệu</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="?act=admin&module=policies&action=edit&id=<?= $policy['id'] ?>"
                                        class="text-accent hover:text-accent-hover p-1.5 rounded-xl hover:bg-primary-50 transition-all" title="Sửa">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <a href="?act=admin&module=policies&action=delete&id=<?= $policy['id'] ?>"
                                        onclick="return confirm('Xác nhận xóa chính sách này? Chính sách đang được sử dụng sẽ không thể xóa.')"
                                        class="text-danger hover:text-danger-text p-1.5 rounded-xl hover:bg-danger-bg transition-all" title="Xóa">
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

    <!-- Note -->
    <div class="mt-4 p-4 bg-info-bg rounded-2xl border border-info">
        <p class="text-xs lg:text-sm text-info-text flex items-start gap-2">
            <i data-lucide="info" class="w-4 h-4 flex-shrink-0 mt-0.5"></i>
            <span><strong>Lưu ý:</strong> Chính sách có thể được gán cho nhiều tour. Chỉ có thể xóa chính sách khi không
            còn tour nào sử dụng.
        </p>
    </div>
</div>