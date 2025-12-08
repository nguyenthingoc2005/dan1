<?php
/**
 * ADMIN - CHỌN PHƯƠNG THỨC TẠO TOUR
 * Variables: $templates
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-4xl mx-auto p-4 lg:p-8">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Thêm Tour mới</h1>
        <a href="?act=admin&module=tours" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại danh sách
        </a>
    </div>

    <!-- METHOD SELECTION -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6 mb-6 lg:mb-8">
        <!-- Option 1: Create New -->
        <a href="?act=admin&module=tours&action=create"
            class="block bg-panel border-2 border-primary-100 rounded-2xl p-4 lg:p-6 hover:border-accent hover:shadow-md transition-all group">
            <div class="flex items-start gap-3 lg:gap-4">
                <div class="w-12 h-12 lg:w-14 lg:h-14 bg-accent-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-gradient-to-r group-hover:from-accent-gradient-from group-hover:to-accent-gradient-to transition-all">
                    <i data-lucide="plus" class="w-6 h-6 lg:w-7 lg:h-7 text-accent group-hover:text-white"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-1">Tạo mới từ đầu</h2>
                    <p class="text-xs lg:text-sm text-primary-500">Nhập đầy đủ thông tin tour, lịch trình, dịch vụ mới hoàn toàn.</p>
                    <span class="inline-block mt-2 lg:mt-3 text-accent text-xs lg:text-sm font-semibold group-hover:translate-x-1 transition-transform flex items-center gap-1">
                        Bắt đầu
                        <i data-lucide="chevron-right" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                    </span>
                </div>
            </div>
        </a>

        <!-- Option 2: From Template (scroll hint) -->
        <div class="bg-success-bg border-2 border-success rounded-2xl p-4 lg:p-6">
            <div class="flex items-start gap-3 lg:gap-4">
                <div class="w-12 h-12 lg:w-14 lg:h-14 bg-success rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="copy" class="w-6 h-6 lg:w-7 lg:h-7 text-white"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-1">Clone từ Template</h2>
                    <p class="text-xs lg:text-sm text-primary-500">Sao chép từ tour có sẵn, chỉnh sửa theo yêu cầu khách hàng.</p>
                    <span class="inline-block mt-2 lg:mt-3 text-success-text text-xs lg:text-sm font-semibold flex items-center gap-1">
                        <i data-lucide="chevron-down" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                        Chọn template bên dưới
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- TEMPLATE LIST -->
    <div class="bg-panel rounded-2xl shadow-sm overflow-hidden border border-primary-100">
        <div class="bg-gradient-to-r from-primary-900 to-primary-700 px-4 lg:px-6 py-3 lg:py-4 flex items-center justify-between rounded-t-2xl">
            <h2 class="font-bold text-white text-base lg:text-lg">Danh sách Tour Template</h2>
            <span class="text-primary-300 text-xs lg:text-sm font-semibold"><?= count($templates) ?> tour</span>
        </div>

        <?php if (empty($templates)): ?>
            <div class="p-8 lg:p-12 text-center">
                <div class="text-primary-300 mb-3 lg:mb-4 flex justify-center">
                    <i data-lucide="file-x" class="w-12 h-12 lg:w-16 lg:h-16"></i>
                </div>
                <p class="text-primary-600 font-semibold text-sm lg:text-base">Chưa có tour nào đủ điều kiện làm template</p>
                <p class="text-primary-500 text-xs lg:text-sm mt-1">Tour cần là Public và đã được duyệt (Approved)</p>
            </div>
        <?php else: ?>
            <!-- Search -->
            <div class="p-3 lg:p-4 border-b border-primary-100">
                <input type="text" id="searchTemplate" placeholder="Tìm kiếm theo tên, mã tour..."
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
            </div>

            <!-- List -->
            <div class="divide-y divide-primary-100 max-h-96 overflow-y-auto" id="templateList">
                <?php foreach ($templates as $t): ?>
                    <div class="template-item p-3 lg:p-4 hover:bg-primary-50 flex flex-col sm:flex-row items-start sm:items-center gap-3 lg:gap-4 transition-all"
                        data-name="<?= htmlspecialchars($t['name']) ?>"
                        data-code="<?= htmlspecialchars($t['tour_code']) ?>"
                        data-category="<?= htmlspecialchars($t['category_name'] ?? '') ?>">

                        <div class="flex-1 min-w-0 w-full sm:w-auto">
                            <div class="flex flex-wrap items-center gap-2 mb-1 lg:mb-2">
                                <span class="px-2 lg:px-3 py-0.5 lg:py-1 bg-accent-100 text-accent-700 text-xs font-bold rounded-full">
                                    <?= htmlspecialchars($t['tour_code']) ?>
                                </span>
                                <span class="text-primary-400 text-xs">
                                    <?= $t['duration_days'] ?>N<?= $t['duration_nights'] ?>Đ
                                </span>
                                <?php if (!empty($t['category_name'])): ?>
                                    <span class="text-primary-400 text-xs">•</span>
                                    <span class="text-primary-500 text-xs"><?= htmlspecialchars($t['category_name']) ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 class="font-semibold text-primary-700 text-sm lg:text-base truncate"><?= htmlspecialchars($t['name']) ?></h3>
                        </div>

                        <div class="text-right flex-shrink-0 w-full sm:w-auto">
                            <div class="text-sm lg:text-base font-bold text-primary-700"><?= number_format($t['adult_price'], 0, ',', '.') ?>đ</div>
                            <div class="text-xs text-primary-500">/ người lớn</div>
                        </div>

                        <a href="?act=admin&module=tours&action=createFromTemplate&template_id=<?= $t['id'] ?>"
                            class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-success to-success-dark hover:opacity-90 text-white text-xs lg:text-sm rounded-xl transition-all whitespace-nowrap font-semibold flex-shrink-0 flex items-center justify-center gap-2 shadow-sm">
                            <i data-lucide="check" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                            Chọn
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('searchTemplate')?.addEventListener('input', function () {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.template-item').forEach(item => {
            const name = item.dataset.name.toLowerCase();
            const code = item.dataset.code.toLowerCase();
            const category = (item.dataset.category || '').toLowerCase();
            item.style.display = (name.includes(query) || code.includes(query) || category.includes(query)) ? '' : 'none';
        });
    });
</script>
