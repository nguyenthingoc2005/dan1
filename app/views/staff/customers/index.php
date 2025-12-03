<?php
/**
 * View: Danh sách Khách hàng (Staff)
 */
?>

<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-slate-200 flex justify-between items-center">
        <h2 class="text-xl font-bold text-slate-800">Danh Sách Khách Hàng</h2>
        <a href="?act=staff-customers&action=create"
            class="bg-accent hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
            <i class="fas fa-plus"></i> Thêm Khách Mới
        </a>
    </div>

    <div class="p-6">
        <!-- Filter -->
        <form action="" method="GET" class="mb-6 flex gap-4">
            <input type="hidden" name="act" value="staff-customers">
            <div class="flex-1">
                <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                    placeholder="Tìm theo tên, SĐT, email..."
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
            </div>
            <button type="submit"
                class="bg-slate-800 text-white px-6 py-2 rounded-lg hover:bg-slate-700 transition-colors font-medium">
                <i class="fas fa-search mr-2"></i> Tìm kiếm
            </button>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 text-sm uppercase tracking-wider">
                        <th class="p-4 font-semibold border-b border-slate-200">ID</th>
                        <th class="p-4 font-semibold border-b border-slate-200">Họ Tên</th>
                        <th class="p-4 font-semibold border-b border-slate-200">Liên Hệ</th>
                        <th class="p-4 font-semibold border-b border-slate-200">Địa Chỉ</th>
                        <th class="p-4 font-semibold border-b border-slate-200">Ngày Tạo</th>
                        <th class="p-4 font-semibold border-b border-slate-200 text-right">Hành Động</th>
                    </tr>
                </thead>
                <tbody class="text-slate-700 text-sm">
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                <td class="p-4 font-medium text-slate-500">#<?php echo $customer['id']; ?></td>
                                <td class="p-4">
                                    <div class="font-bold text-slate-800">
                                        <?php echo htmlspecialchars($customer['full_name']); ?></div>
                                    <div class="text-xs text-slate-500">
                                        <?php echo $customer['gender'] == 'male' ? 'Nam' : ($customer['gender'] == 'female' ? 'Nữ' : 'Khác'); ?>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2 mb-1">
                                        <i class="fas fa-phone text-slate-400 text-xs w-4"></i>
                                        <span><?php echo htmlspecialchars($customer['phone']); ?></span>
                                    </div>
                                    <?php if (!empty($customer['email'])): ?>
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-envelope text-slate-400 text-xs w-4"></i>
                                            <span class="text-slate-500"><?php echo htmlspecialchars($customer['email']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 text-slate-600 max-w-xs truncate">
                                    <?php echo htmlspecialchars($customer['address'] ?? '-'); ?>
                                </td>
                                <td class="p-4 text-slate-500">
                                    <?php echo date('d/m/Y', strtotime($customer['created_at'])); ?>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="?act=staff-customers&action=show&id=<?php echo $customer['id']; ?>"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="?act=staff-customers&action=edit&id=<?php echo $customer['id']; ?>"
                                            class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                            title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">
                                <div class="flex flex-col items-center gap-3">
                                    <i class="fas fa-users text-4xl text-slate-300"></i>
                                    <p>Chưa có khách hàng nào.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-6 flex justify-center gap-2">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?act=staff-customers&page=<?php echo $i; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>"
                        class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors <?php echo $i == $current_page ? 'bg-accent text-white shadow-md' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>