<?php
/**
 * STAFF - CHỌN PHƯƠNG THỨC TẠO TOUR
 * Variables: $templates
 */
require_staff_or_admin();
?>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Thêm Tour mới</h1>
        <a href="?act=staff-tours" class="text-gray-500 hover:text-gray-700">← Quay lại danh sách</a>
    </div>

    <!-- METHOD SELECTION -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Option 1: Create New -->
        <a href="?act=staff-tours&action=create"
            class="block bg-white border-2 border-gray-200 rounded-lg p-6 hover:border-accent hover:shadow-md transition-all group">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-accent transition-all">
                    <svg class="w-7 h-7 text-accent group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-bold text-gray-800 mb-1">Tạo mới từ đầu</h2>
                    <p class="text-gray-500 text-sm">Nhập đầy đủ thông tin tour, lịch trình, dịch vụ mới hoàn toàn.</p>
                    <span class="inline-block mt-3 text-accent text-sm font-medium group-hover:translate-x-1 transition-transform">
                        Bắt đầu →
                    </span>
                </div>
            </div>
        </a>

        <!-- Option 2: From Template (scroll hint) -->
        <div class="bg-white border-2 border-green-200 rounded-lg p-6 bg-green-50">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-bold text-gray-800 mb-1">Clone từ Template</h2>
                    <p class="text-gray-500 text-sm">Sao chép từ tour có sẵn, chỉnh sửa theo yêu cầu khách hàng.</p>
                    <span class="inline-block mt-3 text-green-600 text-sm font-medium">
                        ↓ Chọn template bên dưới
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- TEMPLATE LIST -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="bg-gray-800 px-5 py-3 flex items-center justify-between">
            <h2 class="font-bold text-white">Danh sách Tour Template</h2>
            <span class="text-gray-400 text-sm"><?= count($templates) ?> tour</span>
        </div>

        <?php if (empty($templates)): ?>
            <div class="p-10 text-center">
                <div class="text-gray-300 mb-3">
                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <p class="text-gray-500 font-medium">Chưa có tour nào đủ điều kiện làm template</p>
                <p class="text-gray-400 text-sm mt-1">Tour cần là Public và đã được duyệt (Approved)</p>
            </div>
        <?php else: ?>
            <!-- Search -->
            <div class="p-4 border-b">
                <input type="text" id="searchTemplate" placeholder="Tìm kiếm theo tên, mã tour..."
                    class="w-full px-4 py-2 border rounded-lg focus:border-accent focus:outline-none">
            </div>

            <!-- List -->
            <div class="divide-y max-h-96 overflow-y-auto" id="templateList">
                <?php foreach ($templates as $t): ?>
                    <div class="template-item p-4 hover:bg-gray-50 flex items-center gap-4 transition-all"
                        data-name="<?= htmlspecialchars($t['name']) ?>"
                        data-code="<?= htmlspecialchars($t['tour_code']) ?>"
                        data-category="<?= htmlspecialchars($t['category_name'] ?? '') ?>">

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-medium rounded">
                                    <?= htmlspecialchars($t['tour_code']) ?>
                                </span>
                                <span class="text-gray-400 text-xs">
                                    <?= $t['duration_days'] ?>N<?= $t['duration_nights'] ?>Đ
                                </span>
                                <?php if (!empty($t['category_name'])): ?>
                                    <span class="text-gray-400 text-xs">•</span>
                                    <span class="text-gray-500 text-xs"><?= htmlspecialchars($t['category_name']) ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 class="font-medium text-gray-800 truncate"><?= htmlspecialchars($t['name']) ?></h3>
                        </div>

                        <div class="text-right flex-shrink-0">
                            <div class="text-sm font-bold text-gray-800"><?= number_format($t['adult_price'], 0, ',', '.') ?>đ</div>
                            <div class="text-xs text-gray-400">/ người lớn</div>
                        </div>

                        <a href="?act=staff-tours&action=createFromTemplate&template_id=<?= $t['id'] ?>"
                            class="px-4 py-2 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition-all whitespace-nowrap font-medium flex-shrink-0">
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

