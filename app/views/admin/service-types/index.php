<?php
/**
 * ADMIN - DANH SÁCH SERVICE TYPES
 * Variables: $service_types, $total, $total_pages, $current_page
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto p-4 lg:p-8">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý loại dịch vụ</h1>
        <a href="?act=admin&module=service-types&action=create"
            class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Thêm mới
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="bg-panel p-3 lg:p-4 rounded-2xl mb-4 border border-primary-100 shadow-sm">
        <form method="GET" id="filterForm">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="service-types">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 lg:gap-4">
                <div>
                    <input type="text" id="searchInput" 
                        placeholder="Tìm theo tên loại dịch vụ..."
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
                </div>
                <div>
                    <select name="status" id="statusFilter"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                        onchange="document.getElementById('filterForm').submit();">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" <?= ($_GET['status'] ?? '') == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="inactive" <?= ($_GET['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Vô hiệu</option>
                    </select>
                </div>
                <div>
                    <button type="button" onclick="clearFilters()" class="w-full px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-100 text-primary-700 rounded-xl hover:bg-primary-200 font-semibold transition-colors text-sm lg:text-base">
                        Xóa bộ lọc
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px]">
                <thead class="bg-primary-50">
                    <tr>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-600 uppercase tracking-wider">ID</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-600 uppercase tracking-wider">Tên loại dịch vụ</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-600 uppercase tracking-wider">Mô tả</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-600 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-right text-xs font-semibold text-primary-600 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="serviceTypesTableBody" class="divide-y divide-primary-100">
                    <?php if (empty($service_types)): ?>
                        <tr id="noResultsRow">
                            <td colspan="5" class="px-3 lg:px-4 py-8 lg:py-12 text-center text-primary-500">
                                Chưa có loại dịch vụ nào.
                                <a href="?act=admin&module=service-types&action=create" class="text-accent hover:text-accent-dark font-semibold ml-1">Thêm mới</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($service_types as $type): ?>
                            <tr class="service-type-row hover:bg-primary-50 transition-colors" 
                                data-name="<?= htmlspecialchars(strtolower($type['name']), ENT_QUOTES, 'UTF-8') ?>"
                                data-id="<?= $type['id'] ?>">
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-primary-600"><?= $type['id'] ?></td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm font-semibold text-primary-700 service-type-name"><?= htmlspecialchars($type['name']) ?></td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-primary-500">
                                    <?= htmlspecialchars(substr($type['description'] ?? '', 0, 50)) ?>
                                    <?= strlen($type['description'] ?? '') > 50 ? '...' : '' ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm">
                                    <?php if ($type['status'] == 'active'): ?>
                                        <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase bg-success-bg text-success-text">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="px-2 lg:px-3 py-1 rounded-full text-xs font-bold uppercase bg-primary-100 text-primary-600">Vô hiệu</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="?act=admin&module=service-types&action=edit&id=<?= $type['id'] ?>"
                                            class="text-accent hover:text-accent-dark font-semibold flex items-center gap-1">
                                            <i data-lucide="pencil" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                            Sửa
                                        </a>
                                        <a href="?act=admin&module=service-types&action=delete&id=<?= $type['id'] ?>"
                                            onclick="return confirm('Xác nhận vô hiệu hóa loại dịch vụ này?')"
                                            class="text-danger-text hover:text-danger-dark font-semibold flex items-center gap-1">
                                            <i data-lucide="trash-2" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                            Xóa
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
        <div class="mt-4 lg:mt-6 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?act=admin&module=service-types&page=<?= $i ?>&status=<?= $_GET['status'] ?? '' ?>"
                    class="px-3 lg:px-4 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-semibold transition-all <?= $i == $current_page ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white shadow-sm' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

    <!-- Note -->
    <div class="mt-4 lg:mt-6 p-3 lg:p-4 bg-info-bg border-l-4 border-info rounded-xl">
        <p class="text-xs lg:text-sm text-info-text flex items-start gap-2">
            <i data-lucide="info" class="w-4 h-4 flex-shrink-0 mt-0.5"></i>
            <span><strong>Lưu ý:</strong> Loại dịch vụ đã có services không thể xóa. Chỉ có thể chuyển trạng thái inactive.</span>
        </p>
    </div>
</div>

<script>
    // JavaScript search - Tìm kiếm theo name trên client-side
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('serviceTypesTableBody');
        
        if (!searchInput || !tableBody) return;
        
        // Lấy tất cả rows có class service-type-row
        const rows = tableBody.querySelectorAll('.service-type-row');
        
        // Function để lấy hoặc tạo noResultsRow
        function getOrCreateNoResultsRow() {
            let noResultsRow = document.getElementById('noResultsRow');
            // Nếu không tìm thấy hoặc không phải là row cho search (không có class đặc biệt)
            if (!noResultsRow || noResultsRow.classList.contains('service-type-row')) {
                // Xóa row cũ nếu có
                if (noResultsRow && noResultsRow.parentElement === tableBody) {
                    noResultsRow.remove();
                }
                // Tạo row mới
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'noResultsRow';
                noResultsRow.className = 'no-results-message';
                noResultsRow.innerHTML = '<td colspan="5" class="px-4 py-8 text-center text-slate-500">Không tìm thấy loại dịch vụ nào phù hợp.</td>';
            }
            return noResultsRow;
        }
        
        // Function để filter rows
        function filterRows() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;
            
            // Filter các rows
            rows.forEach(function(row) {
                const name = row.getAttribute('data-name') || '';
                const isVisible = name.includes(searchTerm);
                
                if (isVisible) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Xử lý noResultsRow - ẩn tất cả noResultsRow cũ trước
            const existingNoResults = tableBody.querySelectorAll('#noResultsRow.no-results-message');
            existingNoResults.forEach(function(row) {
                row.remove();
            });
            
            // Hiển thị "Không tìm thấy" nếu không có kết quả VÀ có search term
            if (visibleCount === 0 && searchTerm !== '' && rows.length > 0) {
                const noResultsRow = getOrCreateNoResultsRow();
                tableBody.appendChild(noResultsRow);
            }
            // Nếu không có rows nào (từ PHP - empty service_types), giữ nguyên noResultsRow từ PHP
        }
        
        // Event listener cho search input
        searchInput.addEventListener('input', filterRows);
        
        // Function để xóa bộ lọc
        window.clearFilters = function() {
            searchInput.value = '';
            filterRows();
            // Reset status filter
            window.location.href = '?act=admin&module=service-types';
        };
    });
</script>