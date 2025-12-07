<?php
/**
 * ADMIN - DANH SÁCH SERVICE TYPES
 * Variables: $service_types, $total, $total_pages, $current_page
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Quản lý loại dịch vụ</h1>
        <a href="?act=admin&module=service-types&action=create"
            class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600">
            + Thêm mới
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white p-4 rounded mb-4">
        <form method="GET" id="filterForm">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="service-types">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <input type="text" id="searchInput" 
                        placeholder="Tìm theo tên loại dịch vụ..."
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                </div>
                <div>
                    <select name="status" id="statusFilter"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                        onchange="document.getElementById('filterForm').submit();">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" <?= ($_GET['status'] ?? '') == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="inactive" <?= ($_GET['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Vô hiệu</option>
                    </select>
                </div>
                <div>
                    <button type="button" onclick="clearFilters()" class="px-6 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Xóa bộ lọc
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">ID</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Tên loại dịch vụ</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Mô tả</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-700">Trạng thái</th>
                    <th class="px-4 py-3 text-right text-sm font-medium text-slate-700">Thao tác</th>
                </tr>
            </thead>
            <tbody id="serviceTypesTableBody" class="divide-y divide-slate-200">
                <?php if (empty($service_types)): ?>
                    <tr id="noResultsRow">
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                            Chưa có loại dịch vụ nào.
                            <a href="?act=admin&module=service-types&action=create" class="text-accent hover:underline">Thêm
                                mới</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($service_types as $type): ?>
                        <tr class="service-type-row hover:bg-slate-50" 
                            data-name="<?= htmlspecialchars(strtolower($type['name']), ENT_QUOTES, 'UTF-8') ?>"
                            data-id="<?= $type['id'] ?>">
                            <td class="px-4 py-3 text-sm"><?= $type['id'] ?></td>
                            <td class="px-4 py-3 text-sm font-medium service-type-name"><?= htmlspecialchars($type['name']) ?></td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                <?= htmlspecialchars(substr($type['description'] ?? '', 0, 50)) ?>
                                <?= strlen($type['description'] ?? '') > 50 ? '...' : '' ?>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <?php if ($type['status'] == 'active'): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Hoạt động</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">Vô hiệu</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <a href="?act=admin&module=service-types&action=edit&id=<?= $type['id'] ?>"
                                    class="text-blue-600 hover:text-blue-800 mr-3">
                                    ✏️ Sửa
                                </a>
                                <a href="?act=admin&module=service-types&action=delete&id=<?= $type['id'] ?>"
                                    onclick="return confirm('Xác nhận vô hiệu hóa loại dịch vụ này?')"
                                    class="text-red-600 hover:text-red-800">
                                    🗑️ Xóa
                                </a>
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
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?act=admin&module=service-types&page=<?= $i ?>&status=<?= $_GET['status'] ?? '' ?>"
                    class="px-3 py-1 rounded <?= $i == $current_page ? 'bg-accent text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

    <!-- Note -->
    <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r">
        <p class="text-sm text-blue-700">
            💡 <strong>Lưu ý:</strong> Loại dịch vụ đã có services không thể xóa. Chỉ có thể chuyển trạng thái inactive.
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