<?php
/**
 * ADMIN - DANH SÁCH DESTINATIONS
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý địa điểm</h1>
        <a href="?act=admin&module=destinations&action=create"
            class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Thêm địa điểm
        </a>
    </div>

    <!-- Search & Filter -->
    <form method="GET" class="bg-panel p-4 lg:p-5 rounded-2xl shadow-sm border border-primary-100 mb-4">
        <input type="hidden" name="act" value="admin">
        <input type="hidden" name="module" value="destinations">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 lg:gap-4">
            <div class="lg:col-span-2">
                <input type="text" name="search" value="<?= $_GET['search'] ?? '' ?>" placeholder="Tìm kiếm địa điểm..."
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
            </div>
            <div>
                <button type="submit" class="w-full px-4 lg:px-5 py-2 lg:py-2.5 bg-primary-600 hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Tìm kiếm
                </button>
            </div>
        </div>
    </form>

    <!-- Grid View -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-6">
        <?php if (empty($destinations)): ?>
            <div class="col-span-full text-center py-10 text-primary-500 text-sm">
                Chưa có địa điểm nào.
            </div>
        <?php else: ?>
            <?php foreach ($destinations as $dest): ?>
                <div class="bg-panel rounded-2xl shadow-sm hover:shadow-md transition-all overflow-hidden border border-primary-100">
                    <!-- Image -->
                    <div class="h-48 bg-primary-100 relative">
                        <?php if ($dest['thumbnail']): ?>
                            <img src="<?= htmlspecialchars($dest['thumbnail']) ?>" alt="<?= htmlspecialchars($dest['name']) ?>"
                                class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-primary-400">
                                <i data-lucide="image" class="w-12 h-12"></i>
                            </div>
                        <?php endif; ?>

                        <div class="absolute top-2 right-2">
                            <?php if ($dest['status'] == 'active'): ?>
                                <span class="px-2 lg:px-3 py-1 bg-success-bg text-success-text text-xs rounded-full font-bold uppercase shadow-sm">Active</span>
                            <?php else: ?>
                                <span class="px-2 lg:px-3 py-1 bg-primary-100 text-primary-500 text-xs rounded-full font-bold uppercase shadow-sm">Inactive</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <div class="text-xs text-info-text font-semibold mb-1">
                            <?= htmlspecialchars($dest['category_name'] ?? 'Chưa phân loại') ?>
                        </div>
                        <h3 class="font-bold text-base lg:text-lg text-primary-700 mb-2 truncate"
                            title="<?= htmlspecialchars($dest['name']) ?>">
                            <?= htmlspecialchars($dest['name']) ?>
                        </h3>
                        <p class="text-xs lg:text-sm text-primary-500 line-clamp-2 mb-4 h-10">
                            <?= htmlspecialchars($dest['description'] ?? 'Chưa có mô tả') ?>
                        </p>

                        <div class="flex justify-between items-center pt-3 border-t border-primary-100">
                            <span class="text-xs text-primary-400">
                                <?= date('d/m/Y', strtotime($dest['created_at'])) ?>
                            </span>
                            <div class="flex gap-2">
                                <a href="?act=admin&module=destinations&action=edit&id=<?= $dest['id'] ?>"
                                    class="text-warning-text hover:text-warning-text p-1.5 rounded-xl hover:bg-warning-bg transition-all" title="Sửa">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                                <a href="?act=admin&module=destinations&action=delete&id=<?= $dest['id'] ?>"
                                    onclick="return confirm('Xóa địa điểm này?')"
                                    class="text-danger-text hover:text-danger-text p-1.5 rounded-xl hover:bg-danger-bg transition-all" title="Xóa">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
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