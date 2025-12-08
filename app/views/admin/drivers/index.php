<?php
/**
 * ADMIN - QUẢN LÝ TÀI XẾ
 */
if (!is_admin())
    redirect('?act=access-denied');
?>
<div class="max-w-8xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý Tài xế</h1>
        <a href="?act=admin&module=drivers&action=create"
            class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Thêm Tài xế Mới
        </a>
    </div>

    <!-- Filter -->
    <div class="bg-panel p-4 lg:p-5 rounded-2xl shadow-sm border border-primary-100 mb-4 lg:mb-6">
        <form method="GET" class="grid grid-cols-1 lg:grid-cols-4 gap-3 lg:gap-4">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="drivers">

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tìm kiếm</label>
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base"
                    placeholder="Tên, SĐT, số bằng lái...">
            </div>

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Hạng bằng</label>
                <select name="license_type" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Tất cả --</option>
                    <option value="D" <?= ($_GET['license_type'] ?? '') == 'D' ? 'selected' : '' ?>>Bằng D</option>
                    <option value="E" <?= ($_GET['license_type'] ?? '') == 'E' ? 'selected' : '' ?>>Bằng E</option>
                    <option value="B1" <?= ($_GET['license_type'] ?? '') == 'B1' ? 'selected' : '' ?>>Bằng B1</option>
                    <option value="B2" <?= ($_GET['license_type'] ?? '') == 'B2' ? 'selected' : '' ?>>Bằng B2</option>
                </select>
            </div>

            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
                <select name="status" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Tất cả --</option>
                    <option value="active" <?= ($_GET['status'] ?? '') == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="on_trip" <?= ($_GET['status'] ?? '') == 'on_trip' ? 'selected' : '' ?>>Đang đi tour</option>
                    <option value="off_duty" <?= ($_GET['status'] ?? '') == 'off_duty' ? 'selected' : '' ?>>Nghỉ</option>
                    <option value="inactive" <?= ($_GET['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 lg:px-5 py-2 lg:py-2.5 bg-accent hover:bg-opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Lọc
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead class="bg-primary-50 text-primary-700 uppercase text-xs font-bold">
                <tr>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Mã TX</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Họ tên</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">SĐT</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Số bằng lái</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100">Hạng bằng</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-center">Trạng thái</th>
                    <th class="px-3 lg:px-4 py-2 lg:py-3 border-b border-primary-100 text-right">Hành động</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-primary-100">
                <?php if (empty($drivers)): ?>
                    <tr>
                        <td colspan="7" class="px-3 lg:px-4 py-6 lg:py-8 text-center text-primary-500 text-sm">
                            Chưa có tài xế nào.
                            <a href="?act=admin&module=drivers&action=create"
                                class="text-accent hover:text-accent-hover font-semibold ml-2">Thêm mới</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($drivers as $d): ?>
                        <tr class="hover:bg-primary-50 transition-colors">
                            <td class="px-3 lg:px-4 py-2 lg:py-3 font-mono text-sm text-accent">
                                <?= htmlspecialchars($d['driver_code'] ?: 'DRV' . $d['id']) ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 font-semibold text-sm">
                                <?= htmlspecialchars($d['full_name']) ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm">
                                <?= htmlspecialchars($d['phone'] ?? '-') ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm">
                                <?= htmlspecialchars($d['license_number']) ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm">
                                <?= htmlspecialchars($d['license_type'] ?? '-') ?>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                <?php
                                $statusColors = [
                                    'active' => 'bg-success-bg text-success-text',
                                    'on_trip' => 'bg-info-bg text-info-text',
                                    'off_duty' => 'bg-warning-bg text-warning-text',
                                    'suspended' => 'bg-danger-bg text-danger-text',
                                    'inactive' => 'bg-primary-100 text-primary-500'
                                ];
                                $statusLabels = [
                                    'active' => 'Hoạt động',
                                    'on_trip' => 'Đang đi tour',
                                    'off_duty' => 'Nghỉ',
                                    'suspended' => 'Tạm ngưng',
                                    'inactive' => 'Ngừng hoạt động'
                                ];
                                ?>
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold <?= $statusColors[$d['status']] ?? 'bg-primary-100 text-primary-700' ?>">
                                    <?= $statusLabels[$d['status']] ?? $d['status'] ?>
                                </span>
                            </td>
                            <td class="px-3 lg:px-4 py-2 lg:py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="?act=admin&module=drivers&action=show&id=<?= $d['id'] ?>"
                                        class="px-3 py-1 bg-info hover:opacity-90 text-white rounded-lg text-xs font-semibold transition-all">
                                        Xem
                                    </a>
                                    <a href="?act=admin&module=drivers&action=edit&id=<?= $d['id'] ?>"
                                        class="px-3 py-1 bg-accent hover:opacity-90 text-white rounded-lg text-xs font-semibold transition-all">
                                        Sửa
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
        <div class="mt-4 flex justify-center">
            <div class="flex gap-2">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?act=admin&module=drivers&page=<?= $i ?><?= !empty($_GET['status']) ? '&status=' . $_GET['status'] : '' ?><?= !empty($_GET['license_type']) ? '&license_type=' . $_GET['license_type'] : '' ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>"
                        class="px-3 py-1 rounded-lg <?= $i == $current_page ? 'bg-accent text-white' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' ?> text-sm font-semibold">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

