<div class=" mx-auto ">
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
            <a href="?act=admin&module=customers&action=downloadTemplate"
                class="w-full sm:w-auto bg-primary-500 hover:opacity-90 text-white font-semibold py-2 lg:py-2.5 px-4 lg:px-5 rounded-xl transition-all text-sm lg:text-base flex items-center justify-center gap-2 shadow-sm"
                title="Tải file mẫu">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Template</span>
            </a>
            <a href="?act=admin&module=customers&action=create"
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
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <div class="lg:col-span-5">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-primary-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input type="text" id="searchInput"
                        class="w-full pl-10 pr-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="Tìm kiếm theo tên, SĐT, Email, Mã KH, CMND/CCCD, Hộ chiếu...">
                </div>
            </div>

            <div class="lg:col-span-3">
                <select id="statusSelect"
                    class="w-full px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent transition-all text-sm lg:text-base text-primary-700">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="active">Đang hoạt động</option>
                    <option value="inactive">Ngừng hoạt động</option>
                    <option value="blacklist">Blacklist</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <button type="button" id="clearFilters"
                    class="w-full bg-primary-500 hover:opacity-90 text-white font-semibold py-2 lg:py-2.5 px-4 rounded-xl transition-all text-sm lg:text-base h-full">
                    Xóa bộ lọc
                </button>
            </div>
        </div>
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
                <tbody id="customersTableBody" class="divide-y divide-primary-100">
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr class="customer-row border-b border-primary-100 hover:bg-primary-50 transition-colors group"
                                data-search="<?= htmlspecialchars(strtolower(($customer['full_name'] ?? '') . ' ' . ($customer['phone'] ?? '') . ' ' . ($customer['email'] ?? '') . ' ' . ($customer['customer_code'] ?? 'KH' . $customer['id']) . ' ' . ($customer['id_card'] ?? '') . ' ' . ($customer['passport'] ?? ''))) ?>"
                                data-status="<?= htmlspecialchars($customer['status'] ?? '') ?>">
                                <td class="px-3 lg:px-6 py-3 lg:py-4 whitespace-nowrap">
                                    <a href="?act=admin&module=customers&action=show&id=<?= $customer['id'] ?>"
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
                                    <div class="flex justify-end gap-2">
                                        <a href="?act=admin&module=customers&action=show&id=<?= $customer['id'] ?>"
                                            class="text-accent hover:text-accent-hover p-1.5 transition-colors"
                                            title="Chi tiết">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="?act=admin&module=customers&action=edit&id=<?= $customer['id'] ?>"
                                            class="text-primary-700 hover:text-primary-900 p-1.5 transition-colors" title="Sửa">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                        </a>
                                        <a href="?act=admin&module=customers&action=delete&id=<?= $customer['id'] ?>"
                                            class="text-danger hover:text-red-600 p-1.5 transition-colors"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?')" title="Xóa">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <!-- Empty state (sẽ được hiển thị bằng JS khi không có kết quả) -->
                    <tr id="emptyState" class="hidden">
                        <td colspan="7" class="px-6 py-12 text-center text-primary-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-users text-4xl text-gray-300 mb-3"></i>
                                <p>Không tìm thấy khách hàng nào phù hợp.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Results Count -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
            <div class="text-sm text-gray-600">
                Hiển thị <span id="visibleCount" class="font-medium"><?= count($customers) ?></span> /
                <span class="font-medium"><?= $total ?></span> khách hàng
            </div>
        </div>
    </div>
</div>

<script>
    // Customer Filtering bằng JavaScript
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const statusSelect = document.getElementById('statusSelect');
        const clearFiltersBtn = document.getElementById('clearFilters');
        const customerRows = document.querySelectorAll('.customer-row');
        const emptyState = document.getElementById('emptyState');
        const visibleCount = document.getElementById('visibleCount');

        function filterCustomers() {
            const searchTerm = (searchInput.value || '').toLowerCase().trim();
            const statusFilter = statusSelect.value || '';

            let visibleRows = 0;

            customerRows.forEach(row => {
                const rowSearch = row.getAttribute('data-search') || '';
                const rowStatus = row.getAttribute('data-status') || '';

                // Kiểm tra search
                const matchesSearch = !searchTerm || rowSearch.includes(searchTerm);

                // Kiểm tra status
                const matchesStatus = !statusFilter || rowStatus === statusFilter;

                // Hiển thị/ẩn row
                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Hiển thị empty state nếu không có kết quả
            if (visibleRows === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }

            // Cập nhật số lượng hiển thị
            if (visibleCount) {
                visibleCount.textContent = visibleRows;
            }
        }

        // Event listeners
        searchInput.addEventListener('input', filterCustomers);
        statusSelect.addEventListener('change', filterCustomers);

        clearFiltersBtn.addEventListener('click', function () {
            searchInput.value = '';
            statusSelect.value = '';
            filterCustomers();
        });

        // Filter ban đầu nếu có giá trị từ URL (nếu cần)
        filterCustomers();
    });

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
        importStatus.innerHTML = '<div class="bg-info-bg border border-info text-info-text px-4 lg:px-6 py-3 lg:py-4 rounded-2xl flex items-center gap-2"><i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i><span>Đang xử lý file...</span></div>';

        const formData = new FormData();
        formData.append('file', file);
        formData.append('csrf_token', '<?= get_csrf_token() ?>');

        const url = '?act=admin&module=customers&action=importStore';
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
                            importStatus.innerHTML = `<div class="bg-success-bg border border-success text-success-text px-4 lg:px-6 py-3 lg:py-4 rounded-2xl flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4"></i><span>${jsonData.message}</span></div>`;
                            // Reload page after 2 seconds
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            importStatus.innerHTML = `<div class="bg-danger-bg border border-danger text-danger-text px-4 lg:px-6 py-3 lg:py-4 rounded-2xl flex items-center gap-2"><i data-lucide="x-circle" class="w-4 h-4"></i><span>Lỗi: ${jsonData.message}</span></div>`;
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
                importStatus.innerHTML = `<div class="bg-danger-bg border border-danger text-danger-text px-4 lg:px-6 py-3 lg:py-4 rounded-2xl flex items-center gap-2"><i data-lucide="x-circle" class="w-4 h-4"></i><span>Lỗi: ${error.message}</span></div>`;
            });

        // Reset file input
        event.target.value = '';
    }
</script>