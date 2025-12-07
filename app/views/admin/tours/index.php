<?php
/**
 * ADMIN - DANH SÁCH TOURS
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Quản lý Tour du lịch</h1>
        <a href="?act=admin&module=tours&action=selectTemplate"
            class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600 shadow flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Thêm Tour mới
        </a>
    </div>

    <!-- Status Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
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
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> 
                       whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Search & Filter -->
    <form method="GET" class="bg-white p-4 rounded shadow-sm mb-6">
        <input type="hidden" name="act" value="admin">
        <input type="hidden" name="module" value="tours">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>"
                    placeholder="Tìm kiếm tên tour, mã tour..."
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                    Lọc dữ liệu
                </button>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-bold">
                <tr>
                    <th class="px-4 py-3 border-b">Hình ảnh</th>
                    <th class="px-4 py-3 border-b">Mã Tour</th>
                    <th class="px-4 py-3 border-b">Tên Tour</th>
                    <th class="px-4 py-3 border-b">Giá (Người lớn)</th>
                    <th class="px-4 py-3 border-b">Thời gian</th>
                    <th class="px-4 py-3 border-b">Ngày đi</th>
                    <th class="px-4 py-3 border-b">Trạng thái</th>
                    <th class="px-4 py-3 border-b text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                <?php if (empty($tours)): ?>
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            Chưa có tour nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tours as $tour): ?>
                        <tr class="hover:bg-gray-50 border-b last:border-0">
                            <td class="px-4 py-3 w-24">
                                <?php if ($tour['thumbnail']): ?>
                                    <img src="<?= htmlspecialchars($tour['thumbnail']) ?>" class="w-16 h-12 object-cover rounded">
                                <?php else: ?>
                                    <div
                                        class="w-16 h-12 bg-gray-200 rounded flex items-center justify-center text-gray-400 text-xs">
                                        No Img</div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 font-mono text-gray-600">
                                <?= htmlspecialchars($tour['tour_code']) ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800"><?= htmlspecialchars($tour['name']) ?></div>
                            </td>
                            <td class="px-4 py-3 font-bold text-accent">
                                <?= number_format($tour['adult_price'], 0, ',', '.') ?> đ
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <?= $tour['duration_days'] ?>N<?= $tour['duration_nights'] ?>Đ
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <?php if (!empty($tour['next_departure_date'])): ?>
                                    <div class="font-medium text-blue-600">
                                        <?= date('d/m/Y', strtotime($tour['next_departure_date'])) ?>
                                    </div>
                                    <?php if (!empty($tour['upcoming_schedules_count']) && $tour['upcoming_schedules_count'] > 1): ?>
                                        <div class="text-xs text-gray-500 mt-1">
                                            +<?= $tour['upcoming_schedules_count'] - 1 ?> lịch khác
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-gray-400 italic text-xs">Chưa có lịch</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php
                                // Hiển thị trạng thái từ cột status (đã gộp approval_status)
                                switch ($tour['status']) {
                                    case 'pending':
                                        echo '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">Chờ duyệt</span>';
                                        break;
                                    case 'active':
                                        echo '<span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Hoạt động</span>';
                                        break;
                                    case 'rejected':
                                        echo '<span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full">Từ chối</span>';
                                        break;
                                    case 'draft':
                                        echo '<span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded-full">Nháp</span>';
                                        break;
                                    case 'inactive':
                                        echo '<span class="px-2 py-1 bg-gray-300 text-gray-800 text-xs rounded-full">Đã ẩn</span>';
                                        break;
                                    default:
                                        echo '<span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded-full">' . htmlspecialchars($tour['status']) . '</span>';
                                }
                                ?>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <!-- Quick Approve for Pending tours -->
                                <?php if ($tour['status'] == 'pending'): ?>
                                    <form method="POST" action="?act=admin&module=tours&action=changeStatus"
                                        class="inline-block mr-2" onsubmit="return confirm('Duyệt tour này?');">
                                        <input type="hidden" name="id" value="<?= $tour['id'] ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="text-green-600 hover:text-green-800 font-bold"
                                            title="Duyệt nhanh">✓</button>
                                    </form>
                                <?php endif; ?>

                                <a href="?act=admin&module=tours&action=show&id=<?= $tour['id'] ?>"
                                    class="text-green-600 hover:text-green-800 font-medium mr-3">Xem</a>
                                <a href="?act=admin&module=tours&action=edit&id=<?= $tour['id'] ?>"
                                    class="text-blue-600 hover:text-blue-800 font-medium mr-3">Sửa</a>
                                <a href="?act=admin&module=tours&action=delete&id=<?= $tour['id'] ?>"
                                    onclick="return confirm('Bạn có chắc muốn xóa tour này?')"
                                    class="text-red-600 hover:text-red-800 font-medium">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="mt-6 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?act=admin&module=tours&page=<?= $i ?>&search=<?= $_GET['search'] ?? '' ?>&status=<?= $_GET['status'] ?? '' ?>&tour_type=<?= $_GET['tour_type'] ?? '' ?>"
                    class="px-3 py-1 rounded <?= $i == $current_page ? 'bg-accent text-white' : 'bg-white border hover:bg-gray-100' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>