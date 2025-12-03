<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Quản lý Khách hàng</h1>
            <p class="text-sm text-gray-500 mt-1">Danh sách khách hàng và lịch sử đặt tour</p>
        </div>
        <a href="?act=admin&module=customers&action=create"
            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2 shadow-sm">
            <i class="fas fa-plus"></i>
            <span>Thêm khách hàng</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="customers">

            <div class="md:col-span-5">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm"
                        placeholder="Tìm kiếm theo tên, SĐT, Email..."
                        value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
            </div>

            <div class="md:col-span-3">
                <select name="status"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm bg-white">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="active" <?= ($filters['status'] ?? '') == 'active' ? 'selected' : '' ?>>Đang hoạt động
                    </option>
                    <option value="inactive" <?= ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt
                        động</option>
                    <option value="blacklist" <?= ($filters['status'] ?? '') == 'blacklist' ? 'selected' : '' ?>>Blacklist
                    </option>
                </select>
            </div>

            <div class="md:col-span-2">
                <button type="submit"
                    class="w-full bg-slate-800 hover:bg-slate-900 text-white font-medium py-2 px-4 rounded-lg transition-colors text-sm h-full">
                    Lọc dữ liệu
                </button>
            </div>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                        <th class="px-6 py-4">Mã KH</th>
                        <th class="px-6 py-4">Thông tin khách hàng</th>
                        <th class="px-6 py-4">Liên hệ</th>
                        <th class="px-6 py-4">Phân loại</th>
                        <th class="px-6 py-4">Tổng chi tiêu</th>
                        <th class="px-6 py-4">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="?act=admin&module=customers&action=show&id=<?= $customer['id'] ?>"
                                        class="font-mono text-blue-600 font-medium hover:underline">
                                        <?= htmlspecialchars($customer['customer_code'] ?? 'KH' . $customer['id']) ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-sm mr-3">
                                            <?= strtoupper(substr($customer['full_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-800">
                                                <?= htmlspecialchars($customer['full_name']) ?></div>
                                            <div class="text-xs text-gray-500">
                                                <?= $customer['gender'] == 'male' ? 'Nam' : ($customer['gender'] == 'female' ? 'Nữ' : 'Khác') ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-phone-alt w-4 text-gray-400"></i>
                                            <span><?= htmlspecialchars($customer['phone']) ?></span>
                                        </div>
                                        <?php if (!empty($customer['email'])): ?>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <i class="fas fa-envelope w-4 text-gray-400"></i>
                                                <span class="truncate max-w-[150px]"
                                                    title="<?= htmlspecialchars($customer['email']) ?>">
                                                    <?= htmlspecialchars($customer['email']) ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $typeClass = 'bg-gray-100 text-gray-600';
                                    $typeName = 'Cá nhân';
                                    if ($customer['customer_type'] == 'group') {
                                        $typeClass = 'bg-blue-50 text-blue-600';
                                        $typeName = 'Nhóm';
                                    } elseif ($customer['customer_type'] == 'corporate') {
                                        $typeClass = 'bg-purple-50 text-purple-600';
                                        $typeName = 'Doanh nghiệp';
                                    }
                                    ?>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $typeClass ?>">
                                        <?= $typeName ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium text-emerald-600">
                                    <?= format_currency($customer['total_spent'] ?? 0) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($customer['status'] == 'active'): ?>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Hoạt động
                                        </span>
                                    <?php elseif ($customer['status'] == 'blacklist'): ?>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                            Blacklist
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>
                                            Ngừng HĐ
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="?act=admin&module=customers&action=show&id=<?= $customer['id'] ?>"
                                            class="text-blue-600 hover:text-blue-900 p-1" title="Chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="?act=admin&module=customers&action=edit&id=<?= $customer['id'] ?>"
                                            class="text-amber-600 hover:text-amber-900 p-1" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?act=admin&module=customers&action=delete&id=<?= $customer['id'] ?>"
                                            class="text-red-600 hover:text-red-900 p-1"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?')" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-users text-4xl text-gray-300 mb-3"></i>
                                    <p>Không tìm thấy khách hàng nào phù hợp.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-center">
                <nav class="flex gap-1">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?act=admin&module=customers&page=<?= $i ?>&search=<?= urlencode($filters['search'] ?? '') ?>&status=<?= $filters['status'] ?? '' ?>"
                            class="px-3 py-1 rounded-md text-sm font-medium transition-colors <?= $i == $current_page
                                ? 'bg-blue-600 text-white shadow-sm'
                                : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>