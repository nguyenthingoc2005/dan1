<?php
/**
 * ADMIN - DANH SÁCH CHÍNH SÁCH HỦY
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý Chính sách Hủy</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Quản lý các chính sách tính phí hủy booking theo số ngày trước khởi hành</p>
        </div>
        <a href="?act=admin&module=cancellation-policies&action=create"
            class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Tạo chính sách mới
        </a>
    </div>

    <!-- Filter -->
    <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100 mb-4 lg:mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-4">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="cancellation-policies">
            
            <select name="status"
                class="flex-1 px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="active" <?= ($_GET['status'] ?? '') == 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                <option value="inactive" <?= ($_GET['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động</option>
            </select>
            
            <button type="submit"
                class="px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base">
                <i data-lucide="search" class="w-4 h-4 inline mr-2"></i>
                Lọc
            </button>
        </form>
    </div>

    <!-- Info Box -->
    <div class="bg-info-bg p-4 lg:p-6 rounded-2xl border border-info mb-4 lg:mb-6">
        <div class="flex items-start gap-3">
            <i data-lucide="info" class="w-5 h-5 text-info-text mt-0.5"></i>
            <div class="text-sm text-info-text">
                <p class="font-semibold mb-1">Cách hoạt động:</p>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    <li>Hệ thống sẽ tìm policy có <strong>days_before</strong> lớn nhất nhưng ≤ số ngày trước khởi hành</li>
                    <li>Ví dụ: Hủy trước 5 ngày → Tìm policy có days_before ≤ 5, lấy policy có days_before lớn nhất</li>
                    <li>Nếu không tìm thấy policy phù hợp → Phí hủy = 0%</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-primary-50">
                        <th class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">Tên chính sách</th>
                        <th class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">Số ngày trước KH</th>
                        <th class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">Phí hủy (%)</th>
                        <th class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">Mô tả</th>
                        <th class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider text-center">Trạng thái</th>
                        <th class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($policies)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-primary-500">
                                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 opacity-50"></i>
                                <p>Chưa có chính sách hủy nào</p>
                                <a href="?act=admin&module=cancellation-policies&action=create"
                                    class="text-accent hover:underline mt-2 inline-block">Tạo chính sách đầu tiên</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($policies as $policy): ?>
                            <tr class="border-b border-primary-100 hover:bg-primary-50 transition-colors">
                                <td class="px-3 lg:px-4 py-3 lg:py-4">
                                    <div class="font-semibold text-primary-700 text-sm"><?= htmlspecialchars($policy['name']) ?></div>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4">
                                    <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-xs font-semibold">
                                        ≤ <?= $policy['days_before'] ?> ngày
                                    </span>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4">
                                    <span class="font-bold text-danger-text text-sm"><?= number_format($policy['fee_percentage'], 2) ?>%</span>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4">
                                    <div class="text-sm text-primary-700 max-w-xs truncate" title="<?= htmlspecialchars($policy['description'] ?? '') ?>">
                                        <?= htmlspecialchars($policy['description'] ?? '-') ?>
                                    </div>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $policy['status'] === 'active' ? 'bg-success-bg text-success-text' : 'bg-gray-100 text-gray-600' ?>">
                                        <?= $policy['status'] === 'active' ? 'Hoạt động' : 'Ngừng' ?>
                                    </span>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="?act=admin&module=cancellation-policies&action=edit&id=<?= $policy['id'] ?>"
                                            class="text-accent hover:text-accent-hover font-semibold text-sm flex items-center gap-1">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                            Sửa
                                        </a>
                                        <form action="?act=admin&module=cancellation-policies&action=toggleStatus" method="POST" class="inline"
                                            onsubmit="return confirm('Bạn có chắc muốn thay đổi trạng thái?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $policy['id'] ?>">
                                            <button type="submit" class="text-primary-500 hover:text-primary-700 font-semibold text-sm">
                                                <i data-lucide="<?= $policy['status'] === 'active' ? 'eye-off' : 'eye' ?>" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        <form action="?act=admin&module=cancellation-policies&action=delete" method="POST" class="inline"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa chính sách này?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $policy['id'] ?>">
                                            <button type="submit" class="text-danger hover:text-danger-hover font-semibold text-sm">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
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

