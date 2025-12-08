<?php
/**
 * ADMIN - DANH SÁCH MÃ GIẢM GIÁ
 * Variables: $discountCodes
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý mã giảm giá</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Tạo và quản lý các mã giảm giá cho tour</p>
        </div>
        <a href="?act=admin&module=discount-codes&action=create"
            class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Thêm mã giảm giá mới
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-5 mb-4">
        <form method="GET" action="?act=admin&module=discount-codes" class="flex flex-col lg:flex-row gap-3 lg:gap-4 items-end">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="discount-codes">

            <div class="flex-1 w-full lg:w-auto">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tìm kiếm</label>
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    placeholder="Tìm theo mã hoặc tên..."
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
            </div>

            <div class="w-full lg:w-auto">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
                <select name="status" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Tất cả --</option>
                    <option value="active" <?= ($_GET['status'] ?? '') == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="inactive" <?= ($_GET['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Vô hiệu</option>
                </select>
            </div>

            <button type="submit" class="w-full lg:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-600 hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="filter" class="w-4 h-4"></i>
                Lọc
            </button>

            <?php if (!empty($_GET['status']) || !empty($_GET['search'])): ?>
                <a href="?act=admin&module=discount-codes" class="w-full lg:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Xóa bộ lọc
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse min-w-[1000px]">
                <thead class="bg-primary-50 border-b border-primary-100">
                <tr>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Mã</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Tên</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Loại</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Giá trị</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Thời gian</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Sử dụng</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Trạng thái</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 text-right text-xs uppercase font-semibold text-primary-700 tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary-100">
                <?php if (empty($discountCodes)): ?>
                    <tr>
                        <td colspan="8" class="px-3 lg:px-4 py-6 lg:py-8 text-center text-primary-500 text-sm">
                            Chưa có mã giảm giá nào.
                            <a href="?act=admin&module=discount-codes&action=create" class="text-accent hover:text-accent-hover font-semibold ml-1">Thêm mới</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($discountCodes as $code): ?>
                        <?php
                        $today = date('Y-m-d');
                        $isExpired = !empty($code['end_date']) && $today > $code['end_date'];
                        $isNotStarted = !empty($code['start_date']) && $today < $code['start_date'];
                        $isExhausted = $code['usage_limit'] > 0 && $code['used_count'] >= $code['usage_limit'];
                        $isValid = $code['status'] === 'active' && !$isExpired && !$isNotStarted && !$isExhausted;
                        ?>
                        <tr class="hover:bg-primary-50 transition-colors">
                            <td class="px-3 lg:px-4 py-2 lg:py-3">
                                <span class="font-bold text-accent text-sm lg:text-base"><?= htmlspecialchars($code['code']) ?></span>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-700">
                                <?= htmlspecialchars($code['name'] ?: '-') ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold <?= $code['discount_type'] === 'percentage' ? 'bg-info-bg text-info' : 'bg-success-bg text-success-text' ?>">
                                    <?= $code['discount_type'] === 'percentage' ? 'Phần trăm' : 'Cố định' ?>
                                </span>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm font-semibold text-primary-700">
                                <?php if ($code['discount_type'] === 'percentage'): ?>
                                    <?= number_format($code['discount_value'], 0) ?>%
                                <?php else: ?>
                                    <?= format_currency($code['discount_value']) ?>
                                <?php endif; ?>
                                <?php if ($code['min_purchase'] > 0): ?>
                                    <br><span class="text-xs text-primary-500">(Tối thiểu: <?= format_currency($code['min_purchase']) ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs text-primary-600">
                                <?php if ($code['start_date'] || $code['end_date']): ?>
                                    <?php if ($code['start_date']): ?>
                                        <div>Từ: <?= date('d/m/Y', strtotime($code['start_date'])) ?></div>
                                    <?php endif; ?>
                                    <?php if ($code['end_date']): ?>
                                        <div>Đến: <?= date('d/m/Y', strtotime($code['end_date'])) ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-primary-400">Không giới hạn</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-700">
                                <?php if ($code['usage_limit'] > 0): ?>
                                    <span class="<?= $isExhausted ? 'text-danger-text' : '' ?>">
                                        <?= $code['used_count'] ?> / <?= $code['usage_limit'] ?>
                                    </span>
                                    <br><span class="text-xs text-primary-500">(<?= $code['booking_count'] ?> booking)</span>
                                <?php else: ?>
                                    <span><?= $code['used_count'] ?> lần</span>
                                    <br><span class="text-xs text-primary-500">(<?= $code['booking_count'] ?> booking)</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3">
                                <?php if (!$isValid): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-warning-bg text-warning">
                                        <?php
                                        if ($isExpired) echo 'Hết hạn';
                                        elseif ($isNotStarted) echo 'Chưa bắt đầu';
                                        elseif ($isExhausted) echo 'Hết lượt';
                                        else echo 'Vô hiệu';
                                        ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-success-bg text-success-text">
                                        Hoạt động
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="?act=admin&module=discount-codes&action=edit&id=<?= $code['id'] ?>"
                                        class="p-2 text-accent hover:bg-accent hover:text-white rounded-lg transition-all"
                                        title="Sửa">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    <a href="?act=admin&module=discount-codes&action=toggleStatus&id=<?= $code['id'] ?>"
                                        class="p-2 <?= $code['status'] === 'active' ? 'text-warning hover:bg-warning hover:text-white' : 'text-success hover:bg-success hover:text-white' ?> rounded-lg transition-all"
                                        title="<?= $code['status'] === 'active' ? 'Vô hiệu hóa' : 'Kích hoạt' ?>"
                                        onclick="return confirm('Bạn có chắc muốn <?= $code['status'] === 'active' ? 'vô hiệu hóa' : 'kích hoạt' ?> mã này?')">
                                        <i data-lucide="<?= $code['status'] === 'active' ? 'eye-off' : 'eye' ?>" class="w-4 h-4"></i>
                                    </a>
                                    <a href="?act=admin&module=discount-codes&action=delete&id=<?= $code['id'] ?>"
                                        class="p-2 text-danger hover:bg-danger hover:text-white rounded-lg transition-all"
                                        title="Xóa"
                                        onclick="return confirm('Bạn có chắc muốn xóa mã giảm giá này?')">
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
</div>

