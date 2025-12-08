<?php
/**
 * ==============================================================================
 * ADMIN - DANH SÁCH NHÂN VIÊN
 * ==============================================================================
 * 
 * Hiển thị danh sách users với search, filter, pagination
 * 
 * Variables:
 * - $users: Array of users
 * - $total: Total count
 * - $total_pages: Total pages
 * - $current_page: Current page
 * 
 * ==============================================================================
 */

// Require admin permission
if (!is_admin()) {
    redirect('?act=access-denied');
}

// Get current filters
$filter_role = $_GET['role'] ?? '';
$filter_status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
?>

<!-- Header - Responsive -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
    <div>
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý nhân viên</h1>
        <p class="text-xs lg:text-sm text-primary-500 mt-1">Tổng số: <?= number_format($total) ?> nhân viên</p>
    </div>
    <a href="?act=admin&module=users&action=create"
        class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Thêm nhân viên mới
    </a>
</div>

<!-- Filter Bar -->
<div class="bg-panel p-4 lg:p-5 rounded-2xl shadow-sm border border-primary-100 mb-4">
    <form method="GET" class="flex flex-col lg:flex-row gap-3 lg:gap-4 items-end">
        <input type="hidden" name="act" value="admin">
        <input type="hidden" name="module" value="users">

        <!-- Search -->
        <div class="flex-1 w-full lg:w-auto">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tìm kiếm</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                placeholder="Tên, email, số điện thoại..."
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
        </div>

        <!-- Filter Role -->
        <div class="w-full lg:w-48">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Vai trò</label>
            <select name="role"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                <option value="">Tất cả</option>
                <option value="admin" <?= $filter_role == 'admin' ? 'selected' : '' ?>>Quản trị viên</option>
                <option value="staff" <?= $filter_role == 'staff' ? 'selected' : '' ?>>Nhân viên</option>
                <option value="guide" <?= $filter_role == 'guide' ? 'selected' : '' ?>>Hướng dẫn viên</option>
            </select>
        </div>

        <!-- Filter Status -->
        <div class="w-full lg:w-48">
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
            <select name="status"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                <option value="">Tất cả</option>
                <option value="active" <?= $filter_status == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                <option value="inactive" <?= $filter_status == 'inactive' ? 'selected' : '' ?>>Vô hiệu</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="flex gap-2 w-full lg:w-auto">
            <button type="submit" class="flex-1 lg:flex-none px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-600 hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="filter" class="w-4 h-4"></i>
                Lọc
            </button>
            <a href="?act=admin&module=users" class="flex-1 lg:flex-none px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse min-w-[800px]">
            <thead class="bg-primary-50 border-b border-primary-100">
            <tr>
                <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">ID</th>
                <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Avatar</th>
                <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Họ tên</th>
                <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Email</th>
                <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Vai trò</th>
                <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">SĐT</th>
                <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs uppercase font-semibold text-primary-700 tracking-wider">Trạng thái</th>
                <th class="px-3 lg:px-4 py-2 lg:py-3 text-right text-xs uppercase font-semibold text-primary-700 tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-primary-100">
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="8" class="px-3 lg:px-4 py-6 lg:py-8 text-center text-primary-500 text-sm">
                        Không tìm thấy nhân viên nào.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-primary-50 transition-colors">
                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-700">#<?= $user['id'] ?></td>
                        <td class="px-3 lg:px-4 py-2 lg:py-3">
                            <?php if ($user['avatar']): ?>
                                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar"
                                    class="w-10 h-10 rounded-full object-cover border border-primary-100">
                            <?php else: ?>
                                <div
                                    class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-semibold text-sm">
                                    <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm font-semibold text-primary-700"><?= htmlspecialchars($user['full_name']) ?></td>
                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-600"><?= htmlspecialchars($user['email']) ?></td>
                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm">
                            <?php
                            $badge_colors = [
                                'admin' => 'bg-danger-bg text-danger-text',
                                'staff' => 'bg-info-bg text-info-text',
                                'guide' => 'bg-success-bg text-success-text'
                            ];
                            $color = $badge_colors[$user['role']] ?? 'bg-primary-100 text-primary-500';
                            ?>
                            <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase <?= $color ?>">
                                <?= htmlspecialchars($user['role_display']) ?>
                            </span>
                        </td>
                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-600"><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm">
                            <?php if ($user['status'] == 'active'): ?>
                                <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase bg-success-bg text-success-text">
                                    Hoạt động
                                </span>
                            <?php else: ?>
                                <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase bg-primary-100 text-primary-500">
                                    Vô hiệu
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-right">
                            <div class="flex items-center justify-end gap-1 lg:gap-2">
                                <a href="?act=admin&module=users&action=edit&id=<?= $user['id'] ?>"
                                    class="text-warning-text hover:text-warning-text p-1.5 rounded-xl hover:bg-warning-bg transition-all" title="Sửa">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                <?php if ($user['id'] != get_user_id()): ?>
                                    <a href="?act=admin&module=users&action=delete&id=<?= $user['id'] ?>"
                                        class="text-danger-text hover:text-danger-text p-1.5 rounded-xl hover:bg-danger-bg transition-all" title="Xóa"
                                        onclick="return confirm('Bạn có chắc muốn xóa nhân viên này?')">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                <?php endif; ?>
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
        <?php
        $base_url = '?act=admin&module=users';
        if ($search)
            $base_url .= '&search=' . urlencode($search);
        if ($filter_role)
            $base_url .= '&role=' . $filter_role;
        if ($filter_status)
            $base_url .= '&status=' . $filter_status;
        ?>

        <!-- Previous -->
        <?php if ($current_page > 1): ?>
            <a href="<?= $base_url ?>&page=<?= $current_page - 1 ?>"
                class="px-3 lg:px-4 py-1.5 lg:py-2 border border-primary-100 rounded-xl hover:bg-primary-50 text-primary-700 font-semibold transition-all text-sm flex items-center gap-1">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
                Trước
            </a>
        <?php endif; ?>

        <!-- Pages -->
        <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
            <?php if ($i == $current_page): ?>
                <span class="px-3 lg:px-4 py-1.5 lg:py-2 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white rounded-xl font-semibold shadow-sm text-sm">
                    <?= $i ?>
                </span>
            <?php else: ?>
                <a href="<?= $base_url ?>&page=<?= $i ?>" class="px-3 lg:px-4 py-1.5 lg:py-2 border border-primary-100 rounded-xl hover:bg-primary-50 text-primary-700 font-semibold transition-all text-sm">
                    <?= $i ?>
                </a>
            <?php endif; ?>
        <?php endfor; ?>

        <!-- Next -->
        <?php if ($current_page < $total_pages): ?>
            <a href="<?= $base_url ?>&page=<?= $current_page + 1 ?>"
                class="px-3 lg:px-4 py-1.5 lg:py-2 border border-primary-100 rounded-xl hover:bg-primary-50 text-primary-700 font-semibold transition-all text-sm flex items-center gap-1">
                Sau
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>