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

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-primary">Quản lý nhân viên</h1>
        <p class="text-gray-600 mt-1">Tổng số: <?= number_format($total) ?> nhân viên</p>
    </div>
    <a href="?act=admin&module=users&action=create"
        class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600 transition">
        + Thêm nhân viên mới
    </a>
</div>

<!-- Filter Bar -->
<div class="bg-white p-4 rounded mb-4">
    <form method="GET" class="flex gap-4 items-end">
        <input type="hidden" name="act" value="admin">
        <input type="hidden" name="module" value="users">

        <!-- Search -->
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                placeholder="Tên, email, số điện thoại..."
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
        </div>

        <!-- Filter Role -->
        <div class="w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
            <select name="role"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                <option value="">Tất cả</option>
                <option value="admin" <?= $filter_role == 'admin' ? 'selected' : '' ?>>Quản trị viên</option>
                <option value="staff" <?= $filter_role == 'staff' ? 'selected' : '' ?>>Nhân viên</option>
                <option value="guide" <?= $filter_role == 'guide' ? 'selected' : '' ?>>Hướng dẫn viên</option>
            </select>
        </div>

        <!-- Filter Status -->
        <div class="w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
            <select name="status"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                <option value="">Tất cả</option>
                <option value="active" <?= $filter_status == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                <option value="inactive" <?= $filter_status == 'inactive' ? 'selected' : '' ?>>Vô hiệu</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600">
                Lọc
            </button>
            <a href="?act=admin&module=users" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">ID</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Avatar</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Họ tên</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Email</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Vai trò</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">SĐT</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Trạng thái</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                        Không tìm thấy nhân viên nào.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">#<?= $user['id'] ?></td>
                        <td class="px-4 py-3">
                            <?php if ($user['avatar']): ?>
                                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar"
                                    class="w-10 h-10 rounded-full object-cover">
                            <?php else: ?>
                                <div
                                    class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-medium">
                                    <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm font-medium"><?= htmlspecialchars($user['full_name']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars($user['email']) ?></td>
                        <td class="px-4 py-3 text-sm">
                            <?php
                            $badge_colors = [
                                'admin' => 'bg-red-100 text-red-700',
                                'staff' => 'bg-blue-100 text-blue-700',
                                'guide' => 'bg-green-100 text-green-700'
                            ];
                            $color = $badge_colors[$user['role']] ?? 'bg-gray-100 text-gray-700';
                            ?>
                            <span class="px-2 py-1 rounded text-xs font-medium <?= $color ?>">
                                <?= htmlspecialchars($user['role_display']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
                        <td class="px-4 py-3 text-sm">
                            <?php if ($user['status'] == 'active'): ?>
                                <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700">
                                    Hoạt động
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                    Vô hiệu
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex gap-2">
                                <a href="?act=admin&module=users&action=edit&id=<?= $user['id'] ?>"
                                    class="text-accent hover:text-blue-700" title="Sửa">
                                    ✏️
                                </a>
                                <?php if ($user['id'] != get_user_id()): ?>
                                    <a href="?act=admin&module=users&action=delete&id=<?= $user['id'] ?>"
                                        class="text-red-500 hover:text-red-700" title="Xóa"
                                        onclick="return confirm('Bạn có chắc muốn xóa nhân viên này?')">
                                        🗑️
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

<!-- Pagination -->
<?php if ($total_pages > 1): ?>
    <div class="mt-4 flex justify-center gap-2">
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
                class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">
                « Trước
            </a>
        <?php endif; ?>

        <!-- Pages -->
        <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
            <?php if ($i == $current_page): ?>
                <span class="px-3 py-1 bg-accent text-white rounded">
                    <?= $i ?>
                </span>
            <?php else: ?>
                <a href="<?= $base_url ?>&page=<?= $i ?>" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">
                    <?= $i ?>
                </a>
            <?php endif; ?>
        <?php endfor; ?>

        <!-- Next -->
        <?php if ($current_page < $total_pages): ?>
            <a href="<?= $base_url ?>&page=<?= $current_page + 1 ?>"
                class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">
                Sau »
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>