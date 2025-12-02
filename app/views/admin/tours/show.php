<?php
/**
 * ADMIN - CHI TIẾT TOUR
 * Variables: $tour
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Chi tiết Tour</h1>
        <div class="flex gap-2">
            <a href="?act=admin&module=tours"
                class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                ← Quay lại
            </a>
            <a href="?act=admin&module=tours&action=edit&id=<?= $tour['id'] ?>"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow">
                ✏️ Chỉnh sửa
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT COLUMN: INFO & PRICING -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Basic Info -->
            <div class="bg-white rounded shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Thông tin chung</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="block text-sm text-gray-500">Tên Tour</span>
                        <span class="font-medium text-gray-900"><?= htmlspecialchars($tour['name']) ?></span>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500">Mã Tour</span>
                        <span class="font-mono text-gray-900 bg-gray-100 px-2 py-1 rounded text-sm"><?= htmlspecialchars($tour['tour_code']) ?></span>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500">Danh mục</span>
                        <span class="text-blue-600"><?= htmlspecialchars($tour['category_name'] ?? 'N/A') ?></span>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500">Trạng thái</span>
                        <?php if ($tour['status'] == 'active'): ?>
                            <span class="inline-block px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Active</span>
                        <?php elseif ($tour['status'] == 'draft'): ?>
                            <span class="inline-block px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded-full">Draft</span>
                        <?php else: ?>
                            <span class="inline-block px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full">Inactive</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-span-2">
                        <span class="block text-sm text-gray-500">Điểm khởi hành</span>
                        <span class="text-gray-900"><?= htmlspecialchars($tour['departure_location']) ?></span>
                    </div>
                    <div class="col-span-2">
                        <span class="block text-sm text-gray-500">Mô tả ngắn</span>
                        <p class="text-gray-700 mt-1 text-sm"><?= nl2br(htmlspecialchars($tour['description'])) ?></p>
                    </div>
                </div>
            </div>

            <!-- Itinerary -->
            <div class="bg-white rounded shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Lịch trình
                    (<?= $tour['duration_days'] ?>N<?= $tour['duration_nights'] ?>Đ)</h2>
                <div
                    class="space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-300 before:to-transparent">
                    <?php if (!empty($tour['itinerary'])): ?>
                        <?php foreach ($tour['itinerary'] as $item): ?>
                            <div
                                class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                                <div
                                    class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-slate-300 group-[.is-active]:bg-accent text-slate-500 group-[.is-active]:text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                                    <span class="font-bold text-sm"><?= $item['day_number'] ?></span>
                                </div>
                                <div
                                    class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-4 rounded border border-slate-200 shadow-sm">
                                    <div class="flex items-center justify-between space-x-2 mb-1">
                                        <div class="font-bold text-slate-900"><?= htmlspecialchars($item['title']) ?></div>
                                    </div>
                                    <div class="text-slate-500 text-sm mb-2">📍
                                        <?= htmlspecialchars($item['destination_name'] ?? 'Chưa xác định') ?></div>
                                    <div class="text-slate-600 text-sm"><?= nl2br(htmlspecialchars($item['description'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 italic text-center">Chưa có lịch trình.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: IMAGES & HIGHLIGHTS -->
        <div class="space-y-6">

            <!-- ADMIN ACTIONS PANEL -->
            <div class="bg-white rounded shadow-sm p-6 border-l-4 border-accent">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Xử lý Tour</h2>
                <form method="POST" action="?act=admin&module=tours&action=changeStatus">
                    <input type="hidden" name="id" value="<?= $tour['id'] ?>">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chọn hành động</label>
                        <select name="action" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                            <option value="">-- Chọn trạng thái --</option>
                            <?php if ($tour['status'] != 'active'): ?>
                                <option value="approve">✅ Duyệt Tour (Mở bán)</option>
                            <?php endif; ?>
                            <?php if ($tour['status'] != 'inactive'): ?>
                                <option value="reject">⛔ Từ chối / Ẩn Tour</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 font-medium shadow" onclick="return confirm('Bạn có chắc chắn muốn cập nhật trạng thái?')">
                        Cập nhật trạng thái
                    </button>
                </form>
            </div>

            <!-- Pricing -->
            <div class="bg-white rounded shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Bảng giá</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Người lớn</span>
                        <span
                            class="font-bold text-accent text-lg"><?= number_format($tour['adult_price'], 0, ',', '.') ?>
                            đ</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Trẻ em</span>
                        <span class="font-medium"><?= number_format($tour['child_price'], 0, ',', '.') ?> đ</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Em bé</span>
                        <span class="font-medium"><?= number_format($tour['infant_price'], 0, ',', '.') ?> đ</span>
                    </div>
                </div>
            </div>

            <!-- Highlights -->
            <div class="bg-white rounded shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Điểm nổi bật</h2>
                <ul class="space-y-2">
                    <?php if (!empty($tour['highlights'])): ?>
                        <?php foreach ($tour['highlights'] as $hl): ?>
                            <li class="flex items-start gap-2 text-sm text-gray-700">
                                <span class="text-green-500 mt-0.5">✓</span>
                                <span><?= htmlspecialchars($hl) ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="text-gray-500 italic">Chưa cập nhật.</li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Images -->
            <div class="bg-white rounded shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Thư viện ảnh</h2>
                <div class="grid grid-cols-2 gap-2">
                    <?php if (!empty($tour['images'])): ?>
                        <?php foreach ($tour['images'] as $img): ?>
                            <div class="relative aspect-square rounded overflow-hidden group cursor-pointer">
                                <img src="<?= htmlspecialchars($img['image_url']) ?>"
                                    class="w-full h-full object-cover transition duration-300 group-hover:scale-110">
                                <?php if ($img['is_primary']): ?>
                                    <span
                                        class="absolute top-1 right-1 bg-accent text-white text-[10px] px-1.5 py-0.5 rounded">Main</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="col-span-2 text-gray-500 italic text-center py-4">Chưa có hình ảnh.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>