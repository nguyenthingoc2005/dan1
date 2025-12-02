<?php
/**
 * ADMIN - DANH SÁCH DESTINATIONS
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Quản lý địa điểm</h1>
        <a href="?act=admin&module=destinations&action=create"
            class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-600">
            + Thêm địa điểm
        </a>
    </div>

    <!-- Search & Filter -->
    <form method="GET" class="bg-white p-4 rounded mb-4">
        <input type="hidden" name="act" value="admin">
        <input type="hidden" name="module" value="destinations">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>" placeholder="Tìm kiếm địa điểm..."
                    </button>
            </div>
        </div>
    </form>

    <!-- Grid View -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php if (empty($destinations)): ?>
            <div class="col-span-full text-center py-10 text-gray-500">
                Chưa có địa điểm nào.
            </div>
        <?php else: ?>
            <?php foreach ($destinations as $dest): ?>
                <div class="bg-white rounded shadow-sm hover:shadow-md transition overflow-hidden border border-gray-200">
                    <!-- Image -->
                    <div class="h-48 bg-gray-200 relative">
                        <?php if ($dest['thumbnail']): ?>
                            <img src="<?= htmlspecialchars($dest['thumbnail']) ?>" alt="<?= htmlspecialchars($dest['name']) ?>"
                                class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <span class="text-4xl">📷</span>
                            </div>
                        <?php endif; ?>

                        <div class="absolute top-2 right-2">
                            <?php if ($dest['status'] == 'active'): ?>
                                <span class="px-2 py-1 bg-green-500 text-white text-xs rounded shadow">Active</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-gray-500 text-white text-xs rounded shadow">Inactive</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <div class="text-xs text-blue-600 font-medium mb-1">
                            <?= htmlspecialchars($dest['category_name'] ?? 'Chưa phân loại') ?>
                        </div>
                        <h3 class="font-bold text-lg text-gray-800 mb-2 truncate"
                            title="<?= htmlspecialchars($dest['name']) ?>">
                            <?= htmlspecialchars($dest['name']) ?>
                        </h3>
                        <p class="text-sm text-gray-500 line-clamp-2 mb-4 h-10">
                            <?= htmlspecialchars($dest['description'] ?? 'Chưa có mô tả') ?>
                        </p>

                        <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                            <span class="text-xs text-gray-400">
                                <?= date('d/m/Y', strtotime($dest['created_at'])) ?>
                            </span>
                            <div class="flex gap-2">
                                <a href="?act=admin&module=destinations&action=edit&id=<?= $dest['id'] ?>"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Sửa
                                </a>
                                <a href="?act=admin&module=destinations&action=delete&id=<?= $dest['id'] ?>"
                                    onclick="return confirm('Xóa địa điểm này?')"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Xóa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="mt-8 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?act=admin&module=destinations&page=<?= $i ?>&search=<?= $_GET['search'] ?? '' ?>&category_id=<?= $_GET['category_id'] ?? '' ?>"
                    class="px-3 py-1 rounded <?= $i == $current_page ? 'bg-accent text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>