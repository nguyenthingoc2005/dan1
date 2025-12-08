<?php
/**
 * ADMIN - DANH SÁCH TOURS
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý Tour du lịch</h1>
        <a href="?act=admin&module=tours&action=selectTemplate"
            class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Thêm Tour mới
        </a>
    </div>

    <!-- Status Tabs - JavaScript Filter -->
    <div class="mb-4 lg:mb-6 border-b border-primary-100 overflow-x-auto">
        <nav class="-mb-px flex space-x-4 lg:space-x-8" aria-label="Tabs">
            <button type="button" onclick="filterByStatus('')" data-status-filter=""
                class="status-filter-btn border-accent text-accent whitespace-nowrap py-3 lg:py-4 px-1 border-b-2 font-semibold text-xs lg:text-sm">
                Tất cả (<span id="count-all">0</span>)
            </button>
            <button type="button" onclick="filterByStatus('active')" data-status-filter="active"
                class="status-filter-btn border-transparent text-primary-500 hover:text-primary-700 hover:border-primary-300 whitespace-nowrap py-3 lg:py-4 px-1 border-b-2 font-semibold text-xs lg:text-sm">
                Đang bán (<span id="count-active">0</span>)
            </button>
            <button type="button" onclick="filterByStatus('draft')" data-status-filter="draft"
                class="status-filter-btn border-transparent text-primary-500 hover:text-primary-700 hover:border-primary-300 whitespace-nowrap py-3 lg:py-4 px-1 border-b-2 font-semibold text-xs lg:text-sm">
                Chờ duyệt (<span id="count-draft">0</span>)
            </button>
            <button type="button" onclick="filterByStatus('pending')" data-status-filter="pending"
                class="status-filter-btn border-transparent text-primary-500 hover:text-primary-700 hover:border-primary-300 whitespace-nowrap py-3 lg:py-4 px-1 border-b-2 font-semibold text-xs lg:text-sm">
                Nháp (<span id="count-pending">0</span>)
            </button>
            <button type="button" onclick="filterByStatus('inactive')" data-status-filter="inactive"
                class="status-filter-btn border-transparent text-primary-500 hover:text-primary-700 hover:border-primary-300 whitespace-nowrap py-3 lg:py-4 px-1 border-b-2 font-semibold text-xs lg:text-sm">
                Đã ẩn (<span id="count-inactive">0</span>)
            </button>
            <button type="button" onclick="filterByStatus('rejected')" data-status-filter="rejected"
                class="status-filter-btn border-transparent text-primary-500 hover:text-primary-700 hover:border-primary-300 whitespace-nowrap py-3 lg:py-4 px-1 border-b-2 font-semibold text-xs lg:text-sm">
                Từ chối (<span id="count-rejected">0</span>)
            </button>
        </nav>
    </div>

    <!-- Search & Filter - JavaScript -->
    <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100 mb-4 lg:mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2">
                <div class="relative">
                    <i data-lucide="search"
                        class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-primary-400"></i>
                    <input type="text" id="tour-search-input" style="padding-left: 30px;"
                        placeholder="Tìm kiếm tên tour, mã tour, điểm khởi hành..."
                        class="w-full  pr-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        oninput="filterTours()">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="clearFilters()"
                    class="w-full px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-100 hover:bg-primary-200 text-primary-700 rounded-xl font-semibold transition-all text-sm lg:text-base">
                    <i data-lucide="x" class="w-4 h-4 inline mr-2"></i>
                    Xóa bộ lọc
                </button>
            </div>
        </div>
        <div class="mt-3 text-xs text-primary-500">
            Hiển thị: <span id="filter-result-count">0</span> / <span id="total-tours-count">0</span> tour
        </div>
    </div>

    <!-- Table - Responsive -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-primary-50">
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Hình ảnh</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Mã Tour</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Tên Tour</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Giá (Người lớn)</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Thời gian</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Ngày đi</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider">
                            Trạng thái</th>
                        <th
                            class="px-3 lg:px-4 py-3 lg:py-4 border-b border-primary-100 text-primary-700 font-semibold text-xs uppercase tracking-wider text-right">
                            Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tours)): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-primary-500">
                                Chưa có tour nào.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tours as $tour): ?>
                            <tr class="tour-row border-b border-primary-100 hover:bg-primary-50 transition-colors"
                                data-tour-id="<?= $tour['id'] ?>"
                                data-tour-code="<?= htmlspecialchars(strtolower($tour['tour_code'])) ?>"
                                data-tour-name="<?= htmlspecialchars(strtolower($tour['name'])) ?>"
                                data-tour-status="<?= htmlspecialchars($tour['status']) ?>"
                                data-departure-location="<?= htmlspecialchars(strtolower($tour['departure_location'] ?? '')) ?>">
                                <td class="px-3 lg:px-4 py-3 lg:py-4 w-20 lg:w-24">
                                    <?php if ($tour['thumbnail']): ?>
                                        <img src="<?= htmlspecialchars($tour['thumbnail']) ?>"
                                            class="w-12 h-10 lg:w-16 lg:h-12 object-cover rounded-xl">
                                    <?php else: ?>
                                        <div
                                            class="w-12 h-10 lg:w-16 lg:h-12 bg-primary-100 rounded-xl flex items-center justify-center text-primary-400 text-xs">
                                            No Img</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4">
                                    <span
                                        class="font-mono text-accent font-semibold text-sm"><?= htmlspecialchars($tour['tour_code']) ?></span>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4">
                                    <div class="font-semibold text-primary-700 text-sm"><?= htmlspecialchars($tour['name']) ?>
                                    </div>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4">
                                    <span
                                        class="font-bold text-accent text-sm"><?= number_format($tour['adult_price'], 0, ',', '.') ?>
                                        đ</span>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4 text-primary-700 text-sm">
                                    <?= $tour['duration_days'] ?>N<?= $tour['duration_nights'] ?>Đ
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4 text-primary-700 text-sm">
                                    <?php if (!empty($tour['next_departure_date'])): ?>
                                        <div class="font-semibold text-accent">
                                            <?= date('d/m/Y', strtotime($tour['next_departure_date'])) ?>
                                        </div>
                                        <?php if (!empty($tour['upcoming_schedules_count']) && $tour['upcoming_schedules_count'] > 1): ?>
                                            <div class="text-xs text-primary-500 mt-1">
                                                +<?= $tour['upcoming_schedules_count'] - 1 ?> lịch khác
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-primary-400 italic text-xs">Chưa có lịch</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4">
                                    <?php
                                    // Hiển thị trạng thái từ cột status (đã gộp approval_status)
                                    switch ($tour['status']) {
                                        case 'pending':
                                            echo '<span class="px-3 py-1 bg-warning-bg text-warning-text text-xs font-bold rounded-full">Chờ duyệt</span>';
                                            break;
                                        case 'active':
                                            echo '<span class="px-3 py-1 bg-success-bg text-success-text text-xs font-bold rounded-full">Hoạt động</span>';
                                            break;
                                        case 'rejected':
                                            echo '<span class="px-3 py-1 bg-danger-bg text-danger-text text-xs font-bold rounded-full">Từ chối</span>';
                                            break;
                                        case 'draft':
                                            echo '<span class="px-3 py-1 bg-primary-100 text-primary-500 text-xs font-bold rounded-full">Nháp</span>';
                                            break;
                                        case 'inactive':
                                            echo '<span class="px-3 py-1 bg-primary-300 text-primary-700 text-xs font-bold rounded-full">Đã ẩn</span>';
                                            break;
                                        default:
                                            echo '<span class="px-3 py-1 bg-primary-100 text-primary-500 text-xs font-bold rounded-full">' . htmlspecialchars($tour['status']) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td class="px-3 lg:px-4 py-3 lg:py-4 text-right whitespace-nowrap">
                                    <div class="flex gap-2 justify-end">
                                        <!-- Quick Approve for Pending tours -->
                                        <?php if ($tour['status'] == 'pending'): ?>
                                            <form method="POST" action="?act=admin&module=tours&action=changeStatus"
                                                class="inline-block" onsubmit="return confirm('Duyệt tour này?');">
                                                <input type="hidden" name="id" value="<?= $tour['id'] ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="text-success-text hover:text-success font-bold text-sm"
                                                    title="Duyệt nhanh">
                                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <a href="?act=admin&module=tours&action=show&id=<?= $tour['id'] ?>"
                                            class="text-accent hover:text-accent-hover transition-colors">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="?act=admin&module=tours&action=edit&id=<?= $tour['id'] ?>"
                                            class="text-primary-700 hover:text-primary-900 transition-colors">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                        </a>
                                        <a href="?act=admin&module=tours&action=delete&id=<?= $tour['id'] ?>"
                                            onclick="return confirm('Bạn có chắc muốn xóa tour này?')"
                                            class="text-danger hover:text-red-600 transition-colors">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
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

</div>

<script>
    // Global variables
    let allTours = [];
    let currentStatusFilter = '';

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        // Store all tours data
        const tourRows = document.querySelectorAll('.tour-row');
        allTours = Array.from(tourRows).map(row => ({
            id: row.dataset.tourId,
            code: row.dataset.tourCode,
            name: row.dataset.tourName,
            status: row.dataset.tourStatus,
            departure: row.dataset.departureLocation,
            element: row
        }));

        // Initialize counts
        updateStatusCounts();
        updateFilterResultCount();

        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    // Filter by status
    function filterByStatus(status) {
        currentStatusFilter = status;

        // Update active tab
        document.querySelectorAll('.status-filter-btn').forEach(btn => {
            const btnStatus = btn.dataset.statusFilter || '';
            if (btnStatus === status) {
                btn.classList.remove('border-transparent', 'text-primary-500');
                btn.classList.add('border-accent', 'text-accent');
            } else {
                btn.classList.remove('border-accent', 'text-accent');
                btn.classList.add('border-transparent', 'text-primary-500');
            }
        });

        filterTours();
    }

    // Filter tours by search and status
    function filterTours() {
        const searchInput = document.getElementById('tour-search-input');
        const searchTerm = (searchInput?.value || '').toLowerCase().trim();

        let visibleCount = 0;

        allTours.forEach(tour => {
            let matches = true;

            // Filter by status
            if (currentStatusFilter && tour.status !== currentStatusFilter) {
                matches = false;
            }

            // Filter by search term
            if (matches && searchTerm) {
                const matchesCode = tour.code.includes(searchTerm);
                const matchesName = tour.name.includes(searchTerm);
                const matchesDeparture = tour.departure.includes(searchTerm);

                if (!matchesCode && !matchesName && !matchesDeparture) {
                    matches = false;
                }
            }

            // Show/hide row
            if (matches) {
                tour.element.style.display = '';
                visibleCount++;
            } else {
                tour.element.style.display = 'none';
            }
        });

        // Update result count
        document.getElementById('filter-result-count').textContent = visibleCount;

        // Show "no results" message if needed
        const tbody = document.querySelector('tbody');
        let noResultsRow = tbody.querySelector('.no-results-row');

        if (visibleCount === 0) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results-row';
                noResultsRow.innerHTML = '<td colspan="8" class="px-4 py-8 text-center text-primary-500">Không tìm thấy tour nào phù hợp.</td>';
                tbody.appendChild(noResultsRow);
            }
        } else {
            if (noResultsRow) {
                noResultsRow.remove();
            }
        }
    }

    // Update status counts
    function updateStatusCounts() {
        const counts = {
            '': allTours.length,
            'active': 0,
            'draft': 0,
            'pending': 0,
            'inactive': 0,
            'rejected': 0
        };

        allTours.forEach(tour => {
            if (counts.hasOwnProperty(tour.status)) {
                counts[tour.status]++;
            }
        });

        document.getElementById('count-all').textContent = counts[''];
        document.getElementById('count-active').textContent = counts['active'];
        document.getElementById('count-draft').textContent = counts['draft'];
        document.getElementById('count-pending').textContent = counts['pending'];
        document.getElementById('count-inactive').textContent = counts['inactive'];
        document.getElementById('count-rejected').textContent = counts['rejected'];
        document.getElementById('total-tours-count').textContent = counts[''];
    }

    // Update filter result count
    function updateFilterResultCount() {
        const visibleRows = document.querySelectorAll('.tour-row:not([style*="display: none"])');
        document.getElementById('filter-result-count').textContent = visibleRows.length;
    }

    // Clear all filters
    function clearFilters() {
        currentStatusFilter = '';
        document.getElementById('tour-search-input').value = '';

        // Reset status tabs
        document.querySelectorAll('.status-filter-btn').forEach(btn => {
            const btnStatus = btn.dataset.statusFilter || '';
            if (btnStatus === '') {
                btn.classList.remove('border-transparent', 'text-primary-500');
                btn.classList.add('border-accent', 'text-accent');
            } else {
                btn.classList.remove('border-accent', 'text-accent');
                btn.classList.add('border-transparent', 'text-primary-500');
            }
        });

        filterTours();
    }
</script>