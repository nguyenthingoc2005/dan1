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

    <!-- Status Tabs - Responsive -->
    <div class="mb-4 lg:mb-6 border-b border-primary-100 overflow-x-auto">
        <nav class="-mb-px flex space-x-4 lg:space-x-8" aria-label="Tabs">
            <?php
            $current_status = $_GET['status'] ?? '';
            $tabs = [
                '' => 'Tất cả',
                'active' => 'Đang bán',
                'draft' => 'Chờ duyệt',
                'inactive' => 'Đã ẩn/Từ chối'
            ];
            ?>
            <?php foreach ($tabs as $key => $label): ?>
                <a href="?act=admin&module=tours&status=<?= $key ?>" class="<?= $current_status == $key
                      ? 'border-accent text-accent'
                      : 'border-transparent text-primary-500 hover:text-primary-700 hover:border-primary-300' ?> 
                       whitespace-nowrap py-3 lg:py-4 px-1 border-b-2 font-semibold text-xs lg:text-sm">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Search & Filter - Responsive -->
    <form method="GET" class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100 mb-4 lg:mb-6">
        <input type="hidden" name="act" value="admin">
        <input type="hidden" name="module" value="tours">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>"
                    placeholder="Tìm kiếm tên tour, mã tour..."
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
            </div>
            <div>
                <button type="submit"
                    class="w-full px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base">
                    <i data-lucide="search" class="w-4 h-4 inline mr-2"></i>
                    Lọc dữ liệu
                </button>
            </div>
        </div>
    </form>

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
                            <tr class="border-b border-primary-100 hover:bg-primary-50 transition-colors">
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

    <!-- Pagination - Responsive -->
    <?php if ($total_pages > 1): ?>
        <div class="mt-4 lg:mt-6 flex justify-center gap-2 flex-wrap">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?act=admin&module=tours&page=<?= $i ?>&search=<?= $_GET['search'] ?? '' ?>&status=<?= $_GET['status'] ?? '' ?>&tour_type=<?= $_GET['tour_type'] ?? '' ?>"
                    class="px-3 py-1.5 rounded-xl text-sm font-semibold transition-colors <?= $i == $current_page ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white' : 'bg-panel border border-primary-100 text-primary-700 hover:bg-primary-50' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>