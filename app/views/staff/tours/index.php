<?php
/**
 * STAFF - DANH SÁCH TOURS
 */

require_staff_or_admin();
?>

<div class="max-w-8xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Quản lý Tour của tôi</h1>
        <a href="?act=staff-tours&action=selectTemplate"
            class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600 shadow transition-colors">
            + Thêm Tour mới
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
                'draft' => 'Bản nháp',
                'pending' => 'Chờ duyệt',
                'rejected' => 'Bị từ chối'
            ];
            ?>
            <?php foreach ($tabs as $key => $label): ?>
                <a href="?act=staff-tours&status=<?= $key ?>" class="<?= $current_status == $key
                      ? 'border-accent text-accent'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> 
                       whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Search & Filter -->
    <form method="GET" class="bg-white p-4 rounded shadow-sm mb-6">
        <input type="hidden" name="act" value="staff-tours">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    placeholder="Tìm kiếm tên tour, mã tour..."
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent transition-colors">
            </div>
            <div>
                <select name="category_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent transition-colors">
                    <option value="">-- Tất cả danh mục --</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $id => $name): ?>
                            <option value="<?= $id ?>" <?= ($_GET['category_id'] ?? '') == $id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($name) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <button type="submit"
                    class="w-full px-4 py-2 bg-slate-800 text-white rounded hover:bg-slate-700 transition-colors">
                    Lọc dữ liệu
                </button>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-slate-700 uppercase text-xs font-bold">
                <tr>
                    <th class="px-4 py-3 border-b">Hình ảnh</th>
                    <th class="px-4 py-3 border-b">Mã Tour</th>
                    <th class="px-4 py-3 border-b">Tên Tour</th>
                    <th class="px-4 py-3 border-b">Giá (Người lớn)</th>
                    <th class="px-4 py-3 border-b">Thời gian</th>
                    <th class="px-4 py-3 border-b">Trạng thái</th>
                    <th class="px-4 py-3 border-b text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                <?php if (empty($tours)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                            <div class="flex flex-col items-center gap-3">
                                <i class="fas fa-map-marked-alt text-4xl text-slate-300"></i>
                                <p>Bạn chưa tạo tour nào.</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tours as $tour): ?>
                        <tr class="hover:bg-slate-50 border-b last:border-0 transition-colors">
                            <td class="px-4 py-3 w-24">
                                <?php if (!empty($tour['thumbnail'])): ?>
                                    <img src="<?= htmlspecialchars($tour['thumbnail']) ?>"
                                        class="w-16 h-12 object-cover rounded shadow-sm">
                                <?php else: ?>
                                    <div
                                        class="w-16 h-12 bg-slate-200 rounded flex items-center justify-center text-slate-400 text-xs">
                                        No Img
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600">
                                <?= htmlspecialchars($tour['tour_code']) ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-800 mb-1"><?= htmlspecialchars($tour['name']) ?></div>
                                <span class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded">
                                    <?= htmlspecialchars($tour['category_name'] ?? 'Chưa phân loại') ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold text-accent">
                                <?= number_format($tour['adult_price'], 0, ',', '.') ?> đ
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <?= $tour['duration_days'] ?>N<?= $tour['duration_nights'] ?>Đ
                            </td>
                            <td class="px-4 py-3">
                                <?php
                                $statusClass = '';
                                $statusLabel = '';
                                switch ($tour['status']) {
                                    case 'active':
                                        $statusClass = 'bg-green-100 text-green-700';
                                        $statusLabel = 'Đang bán';
                                        break;
                                    case 'draft':
                                        $statusClass = 'bg-slate-200 text-slate-700';
                                        $statusLabel = 'Bản nháp';
                                        break;
                                    case 'pending':
                                        $statusClass = 'bg-yellow-100 text-yellow-700';
                                        $statusLabel = 'Chờ duyệt';
                                        break;
                                    case 'rejected':
                                        $statusClass = 'bg-red-100 text-red-700';
                                        $statusLabel = 'Từ chối';
                                        break;
                                    default:
                                        $statusClass = 'bg-slate-100 text-slate-700';
                                        $statusLabel = $tour['status'];
                                }
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs font-medium <?= $statusClass ?>">
                                    <?= $statusLabel ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="?act=staff-tours&action=show&id=<?= $tour['id'] ?>"
                                    class="text-blue-600 hover:text-blue-800 font-medium mr-3 transition-colors"
                                    title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if (in_array($tour['status'], ['draft', 'pending', 'rejected'])): ?>
                                    <a href="?act=staff-tours&action=edit&id=<?= $tour['id'] ?>"
                                        class="text-amber-600 hover:text-amber-800 font-medium transition-colors" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if (isset($total_pages) && $total_pages > 1): ?>
        <div class="mt-6 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?act=staff-tours&page=<?= $i ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&category_id=<?= $_GET['category_id'] ?? '' ?>"
                    class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-medium transition-colors <?= $i == ($current_page ?? 1) ? 'bg-accent text-white shadow-md' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>