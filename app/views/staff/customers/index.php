<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-8">
    <!-- Page Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý Khách hàng</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Danh sách khách hàng và lịch sử đặt tour</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <label
                class="w-full sm:w-auto bg-success hover:opacity-90 text-white font-semibold py-2 lg:py-2.5 px-4 lg:px-5 rounded-xl transition-all text-sm lg:text-base flex items-center justify-center gap-2 shadow-sm cursor-pointer">
                <i data-lucide="upload" class="w-4 h-4"></i>
                <span>Import Excel/CSV</span>
                <input type="file" id="importFile" accept=".csv,.xlsx,.xls" class="hidden"
                    onchange="handleImportFile(event)">
            </label>
            <a href="?act=staff-customers&action=downloadTemplate"
                class="w-full sm:w-auto bg-primary-500 hover:opacity-90 text-white font-semibold py-2 lg:py-2.5 px-4 lg:px-5 rounded-xl transition-all text-sm lg:text-base flex items-center justify-center gap-2 shadow-sm"
                title="Tải file mẫu">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Template</span>
            </a>
            <a href="?act=staff-customers&action=create"
                class="w-full sm:w-auto bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white font-semibold py-2 lg:py-2.5 px-4 lg:px-5 rounded-xl transition-all text-sm lg:text-base flex items-center justify-center gap-2 shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Thêm khách hàng</span>
            </a>
        </div>
    </div>

    <!-- Import Status -->
    <div id="importStatus" class="mb-4 hidden"></div>

    <!-- Filters - Responsive -->
    <div class="bg-panel rounded-2xl border border-primary-100 p-4 lg:p-5 mb-4 lg:mb-6">
        <form method="GET" action="" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <input type="hidden" name="act" value="staff-customers">

            <div class="lg:col-span-5">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-primary-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input type="text" name="search"
                        class="w-full pl-10 pr-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="Tìm kiếm theo tên, SĐT, Email..."
                        value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
            </div>

            <div class="lg:col-span-3">
                <select name="status"
                    class="w-full px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent transition-all text-sm lg:text-base text-primary-700">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="active" <?= ($filters['status'] ?? '') == 'active' ? 'selected' : '' ?>>Đang hoạt động
                    </option>
                    <option value="inactive" <?= ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt
                        động</option>
                    <option value="blacklist" <?= ($filters['status'] ?? '') == 'blacklist' ? 'selected' : '' ?>>Blacklist
                    </option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <button type="submit"
                    class="w-full bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white font-semibold py-2 lg:py-2.5 px-4 rounded-xl transition-all text-sm lg:text-base h-full">
                    <i data-lucide="filter" class="w-4 h-4 inline mr-2"></i>
                    Lọc dữ liệu
                </button>
            </div>
        </form>
    </div>

    <!-- Customers Table - Responsive -->
    <div class="bg-panel rounded-2xl border border-primary-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-primary-50">
                        <th
                            class="px-3 lg:px-6 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Mã KH</th>
                        <th
                            class="px-3 lg:px-6 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Thông tin khách hàng</th>
                        <th
                            class="px-3 lg:px-6 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Liên hệ</th>
                        <th
                            class="px-3 lg:px-6 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Phân loại</th>
                        <th
                            class="px-3 lg:px-6 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Tổng chi tiêu</th>
                        <th
                            class="px-3 lg:px-6 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Trạng thái</th>
                        <th
                            class="px-3 lg:px-6 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider text-right">
                            Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-100">
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr class="border-b border-primary-100 hover:bg-primary-50 transition-colors group">
                                <td class="px-3 lg:px-6 py-3 lg:py-4 whitespace-nowrap">
                                    <a href="?act=staff-customers&action=show&id=<?= $customer['id'] ?>"
                                        class="font-mono text-accent font-semibold text-sm hover:text-accent-hover transition-colors">
                                        <?= htmlspecialchars($customer['customer_code'] ?? 'KH' . $customer['id']) ?>
                                    </a>
                                </td>
                                <td class="px-3 lg:px-6 py-3 lg:py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-500 font-bold text-sm mr-3">
                                            <?= strtoupper(substr($customer['full_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-primary-700 text-sm">
                                                <?= htmlspecialchars($customer['full_name']) ?>
                                            </div>
                                            <div class="text-xs text-primary-500">
                                                <?= $customer['gender'] == 'male' ? 'Nam' : ($customer['gender'] == 'female' ? 'Nữ' : 'Khác') ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 lg:px-6 py-3 lg:py-4">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center text-sm text-primary-700">
                                            <i data-lucide="phone" class="w-4 h-4 text-primary-400 mr-1"></i>
                                            <span><?= htmlspecialchars($customer['phone']) ?></span>
                                        </div>
                                        <?php if (!empty($customer['email'])): ?>
                                            <div class="flex items-center text-sm text-primary-700">
                                                <i data-lucide="mail" class="w-4 h-4 text-primary-400 mr-1"></i>
                                                <span class="truncate max-w-[150px]"
                                                    title="<?= htmlspecialchars($customer['email']) ?>">
                                                    <?= htmlspecialchars($customer['email']) ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-3 lg:px-6 py-3 lg:py-4">
                                    <?php
                                    $typeClass = 'bg-primary-100 text-primary-500';
                                    $typeName = 'Cá nhân';
                                    if ($customer['customer_type'] == 'group') {
                                        $typeClass = 'bg-info-bg text-info-text';
                                        $typeName = 'Nhóm';
                                    } elseif ($customer['customer_type'] == 'corporate') {
                                        $typeClass = 'bg-accent-light/20 text-accent-light';
                                        $typeName = 'Doanh nghiệp';
                                    }
                                    ?>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold <?= $typeClass ?>">
                                        <?= $typeName ?>
                                    </span>
                                </td>
                                <td class="px-3 lg:px-6 py-3 lg:py-4 font-semibold text-success-text text-sm">
                                    <?= format_currency($customer['total_spent'] ?? 0) ?>
                                </td>
                                <td class="px-3 lg:px-6 py-3 lg:py-4">
                                    <?php if ($customer['status'] == 'active'): ?>
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-success-bg text-success-text">
                                            <span class="w-1.5 h-1.5 rounded-full bg-success-text mr-1.5"></span>
                                            Hoạt động
                                        </span>
                                    <?php elseif ($customer['status'] == 'blacklist'): ?>
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-danger-bg text-danger-text">
                                            <span class="w-1.5 h-1.5 rounded-full bg-danger-text mr-1.5"></span>
                                            Blacklist
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary-100 text-primary-500">
                                            <span class="w-1.5 h-1.5 rounded-full bg-primary-500 mr-1.5"></span>
                                            Ngừng HĐ
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 lg:px-6 py-3 lg:py-4 text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="?act=staff-customers&action=show&id=<?= $customer['id'] ?>"
                                            class="text-accent hover:text-accent-hover p-1.5 transition-colors"
                                            title="Chi tiết">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="?act=staff-customers&action=edit&id=<?= $customer['id'] ?>"
                                            class="text-primary-700 hover:text-primary-900 p-1.5 transition-colors" title="Sửa">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                        </a>
                                        <!-- Staff không có quyền xóa -->
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-primary-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-lucide="users" class="w-12 h-12 text-primary-300 mb-3"></i>
                                    <p class="text-sm">Không tìm thấy khách hàng nào phù hợp.</p>
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
                        <a href="?act=staff-customers&page=<?= $i ?>&search=<?= urlencode($filters['search'] ?? '') ?>" class="px-3 py-1 rounded-md text-sm font-medium transition-colors <?= $i == $current_page
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

<script>
    // Import Excel/CSV
    function handleImportFile(event) {
        const file = event.target.files[0];
        console.log('📥 handleImportFile() - File:', file);

        if (!file) {
            console.log('❌ No file selected');
            return;
        }

        console.log('📄 File details:', {
            name: file.name,
            size: file.size,
            type: file.type
        });

        const importStatus = document.getElementById('importStatus');
        importStatus.classList.remove('hidden');
        importStatus.innerHTML = '<div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg">⏳ Đang xử lý file...</div>';

        const formData = new FormData();
        formData.append('file', file);
        formData.append('csrf_token', '<?= get_csrf_token() ?>');

        const url = '?act=staff-customers&action=importStore';
        console.log('🌐 Sending request to:', url);

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                console.log('📡 Response status:', response.status, response.statusText);
                // Check if response is redirect
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }
                return response.text();
            })
            .then(data => {
                console.log('📦 Response data:', data);

                // If redirect happened, the page will reload
                // Otherwise show success message
                if (data && !data.includes('<!DOCTYPE')) {
                    try {
                        const jsonData = JSON.parse(data);
                        if (jsonData.success) {
                            importStatus.innerHTML = `<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">✅ ${jsonData.message}</div>`;
                            // Reload page after 2 seconds
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            importStatus.innerHTML = `<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">❌ Lỗi: ${jsonData.message}</div>`;
                        }
                    } catch (e) {
                        // If not JSON, might be HTML redirect
                        window.location.reload();
                    }
                } else {
                    // Page will reload due to redirect
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('❌ Fetch error:', error);
                importStatus.innerHTML = `<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">❌ Lỗi: ${error.message}</div>`;
            });

        // Reset file input
        event.target.value = '';
    }
</script>