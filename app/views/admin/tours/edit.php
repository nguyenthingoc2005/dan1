<?php
/**
 * ADMIN - FORM SỬA TOUR
 * Variables: $tour, $categories, $destinations, $services, $tour_services
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary">Sửa Tour</h1>
            <p class="text-sm text-gray-500">
                <?= htmlspecialchars($tour['tour_code']) ?> - <?= htmlspecialchars($tour['name']) ?>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="?act=admin&module=tours&action=show&id=<?= $tour['id'] ?>" class="px-4 py-2 bg-white border rounded hover:bg-gray-50">Xem chi tiết</a>
            <a href="?act=admin&module=tours" class="text-gray-500 hover:text-gray-700">← Danh sách</a>
        </div>
    </div>

    <form method="POST" action="?act=admin&module=tours&action=update" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="id" value="<?= $tour['id'] ?>">
        <input type="hidden" name="tour_type" value="<?= $tour['tour_type'] ?>">

        <!-- Section 1: Thông tin cơ bản -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b">
                <h2 class="font-bold text-gray-800">1. Thông tin cơ bản</h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tên Tour <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="<?= htmlspecialchars($tour['name']) ?>" required
                            class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                        <select name="category_id" class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $id => $name): ?>
                                <option value="<?= $id ?>" <?= $tour['category_id'] == $id ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Điểm khởi hành</label>
                        <input type="text" name="departure_location" value="<?= htmlspecialchars($tour['departure_location']) ?>"
                            class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số ngày <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_days" id="duration_days" min="1" required
                            value="<?= $tour['duration_days'] ?>"
                            class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số đêm</label>
                        <input type="number" name="duration_nights" min="0" value="<?= $tour['duration_nights'] ?>"
                            class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả ngắn</label>
                        <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none"><?= htmlspecialchars($tour['description']) ?></textarea>
                    </div>
                </div>

                <!-- Số lượng khách & Đặt cọc -->
                <div class="mt-5 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h4 class="font-medium text-blue-800 mb-3">📊 Số lượng khách & Đặt cọc</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số khách tối thiểu</label>
                            <input type="number" name="min_participants" min="1" value="<?= $tour['min_participants'] ?? 10 ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent text-center">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số khách tối đa</label>
                            <input type="number" name="max_participants" min="1" value="<?= $tour['max_participants'] ?? 45 ?>"
                                class="w-full px-3 py-2 border rounded focus:border-accent text-center">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Giá tính cho</label>
                            <div class="flex items-center">
                                <input type="number" name="price_based_on_pax" min="1" value="<?= $tour['price_based_on_pax'] ?? 30 ?>"
                                    class="w-full px-3 py-2 border rounded-l focus:border-accent text-center">
                                <span class="px-3 py-2 bg-gray-100 border border-l-0 rounded-r text-sm text-gray-600">khách</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tỷ lệ đặt cọc</label>
                            <div class="flex items-center">
                                <input type="number" name="deposit_percentage" min="0" max="100" value="<?= $tour['deposit_percentage'] ?? 30 ?>"
                                    class="w-full px-3 py-2 border rounded-l focus:border-accent text-center">
                                <span class="px-3 py-2 bg-gray-100 border border-l-0 rounded-r text-sm text-gray-600">%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trạng thái -->
                <div class="mt-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" class="w-full md:w-1/2 px-3 py-2 border rounded focus:border-accent focus:outline-none">
                        <option value="draft" <?= $tour['status'] == 'draft' ? 'selected' : '' ?>>Bản nháp (Draft)</option>
                        <option value="active" <?= $tour['status'] == 'active' ? 'selected' : '' ?>>Hoạt động (Active)</option>
                        <option value="inactive" <?= $tour['status'] == 'inactive' ? 'selected' : '' ?>>Tạm dừng (Inactive)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 2: Giá bán -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b">
                <h2 class="font-bold text-gray-800">2. Giá bán</h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá người lớn <span class="text-red-500">*</span></label>
                        <input type="number" name="adult_price" required min="0" value="<?= $tour['adult_price'] ?>"
                            class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none font-bold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá trẻ em</label>
                        <input type="number" name="child_price" min="0" value="<?= $tour['child_price'] ?>"
                            class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá em bé</label>
                        <input type="number" name="infant_price" min="0" value="<?= $tour['infant_price'] ?>"
                            class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Lịch trình -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b flex justify-between items-center">
                <h2 class="font-bold text-gray-800">3. Lịch trình</h2>
                <button type="button" onclick="addItineraryDay()" class="text-sm text-accent hover:underline">+ Thêm ngày</button>
            </div>
            <div class="p-5" id="itinerary-container">
                <?php if (!empty($tour['itinerary'])): ?>
                    <?php foreach ($tour['itinerary'] as $item): ?>
                        <div class="bg-gray-50 p-4 rounded border mb-3 itinerary-item">
                            <input type="hidden" name="itinerary_day[]" value="<?= $item['day_number'] ?>">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="w-8 h-8 bg-accent text-white rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0"><?= $item['day_number'] ?></span>
                                <input type="text" name="itinerary_title[]" value="<?= htmlspecialchars($item['title']) ?>" placeholder="Tiêu đề..."
                                    class="flex-1 px-3 py-2 border rounded focus:border-accent focus:outline-none">
                                <select name="itinerary_dest[]" class="px-3 py-2 border rounded focus:border-accent focus:outline-none w-48">
                                    <option value="">-- Điểm đến --</option>
                                    <?php foreach ($destinations as $id => $name): ?>
                                        <option value="<?= $id ?>" <?= $item['destination_id'] == $id ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" onclick="this.closest('.itinerary-item').remove()" class="text-red-500 hover:text-red-700">✕</button>
                            </div>
                            <textarea name="itinerary_desc[]" rows="2" placeholder="Mô tả hoạt động..."
                                class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none text-sm"><?= htmlspecialchars($item['description']) ?></textarea>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section 4: Bao gồm / Không bao gồm -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b">
                <h2 class="font-bold text-gray-800">4. Bao gồm / Không bao gồm</h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Included -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-bold text-green-800">✅ Giá tour BAO GỒM</h3>
                            <button type="button" onclick="addIncludedItem()" class="text-sm text-green-600 hover:underline">+ Thêm</button>
                        </div>
                        <div id="included-container" class="space-y-2">
                            <?php if (!empty($tour['includes'])): ?>
                                <?php foreach ($tour['includes'] as $item): ?>
                                    <div class="flex items-center gap-2">
                                        <span class="text-green-600">✓</span>
                                        <input type="text" name="included[]" value="<?= htmlspecialchars($item) ?>"
                                            class="flex-1 px-3 py-2 border rounded focus:border-green-500 focus:outline-none text-sm">
                                        <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600">✕</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Excluded -->
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-bold text-red-800">❌ Giá tour KHÔNG BAO GỒM</h3>
                            <button type="button" onclick="addExcludedItem()" class="text-sm text-red-600 hover:underline">+ Thêm</button>
                        </div>
                        <div id="excluded-container" class="space-y-2">
                            <?php if (!empty($tour['excludes'])): ?>
                                <?php foreach ($tour['excludes'] as $item): ?>
                                    <div class="flex items-center gap-2">
                                        <span class="text-red-600">✗</span>
                                        <input type="text" name="excluded[]" value="<?= htmlspecialchars($item) ?>"
                                            class="flex-1 px-3 py-2 border rounded focus:border-red-500 focus:outline-none text-sm">
                                        <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600">✕</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Hình ảnh & Highlights -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b">
                <h2 class="font-bold text-gray-800">5. Hình ảnh & Điểm nổi bật</h2>
            </div>
            <div class="p-5 space-y-5">
                <!-- Current Images -->
                <?php if (!empty($tour['images'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh hiện tại</label>
                        <div class="grid grid-cols-4 md:grid-cols-6 gap-3">
                            <?php foreach ($tour['images'] as $img): ?>
                                <div class="relative aspect-square rounded overflow-hidden border <?= $img['is_primary'] ? 'border-2 border-accent' : '' ?>">
                                    <img src="<?= htmlspecialchars($img['image_url']) ?>" class="w-full h-full object-cover">
                                    <?php if ($img['is_primary']): ?>
                                        <span class="absolute top-1 right-1 bg-accent text-white text-[9px] px-1 rounded">Main</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Upload New -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thêm ảnh mới</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:bg-gray-50"
                        onclick="document.getElementById('images').click()">
                        <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden" onchange="previewImages(this)">
                        <div class="text-gray-400 text-3xl mb-2">📷</div>
                        <p class="text-gray-500 text-sm">Click để thêm ảnh</p>
                    </div>
                    <div id="image-preview" class="grid grid-cols-5 gap-3 mt-3"></div>
                </div>

                <!-- Highlights -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Điểm nổi bật</label>
                    <textarea name="highlights" rows="4" placeholder="Mỗi dòng một điểm nổi bật..."
                        class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none"><?= htmlspecialchars(implode("\n", $tour['highlights'] ?? [])) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-3">
            <a href="?act=admin&module=tours&action=show&id=<?= $tour['id'] ?>" class="px-6 py-2 border rounded hover:bg-gray-50">Hủy</a>
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600 font-medium">
                ✓ Cập nhật Tour
            </button>
        </div>
    </form>
</div>

<script>
    const destinations = <?= json_encode($destinations) ?>;
    let dayCount = <?= count($tour['itinerary'] ?? []) ?>;

    function addItineraryDay() {
        dayCount++;
        const container = document.getElementById('itinerary-container');
        let destOptions = '<option value="">-- Điểm đến --</option>';
        for (const [id, name] of Object.entries(destinations)) {
            destOptions += `<option value="${id}">${name}</option>`;
        }

        const html = `
            <div class="bg-gray-50 p-4 rounded border mb-3 itinerary-item">
                <input type="hidden" name="itinerary_day[]" value="${dayCount}">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-8 h-8 bg-accent text-white rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">${dayCount}</span>
                    <input type="text" name="itinerary_title[]" placeholder="Tiêu đề..."
                        class="flex-1 px-3 py-2 border rounded focus:border-accent focus:outline-none">
                    <select name="itinerary_dest[]" class="px-3 py-2 border rounded focus:border-accent focus:outline-none w-48">
                        ${destOptions}
                    </select>
                    <button type="button" onclick="this.closest('.itinerary-item').remove()" class="text-red-500 hover:text-red-700">✕</button>
                </div>
                <textarea name="itinerary_desc[]" rows="2" placeholder="Mô tả hoạt động..."
                    class="w-full px-3 py-2 border rounded focus:border-accent focus:outline-none text-sm"></textarea>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function addIncludedItem(text = '') {
        const container = document.getElementById('included-container');
        const html = `
            <div class="flex items-center gap-2">
                <span class="text-green-600">✓</span>
                <input type="text" name="included[]" value="${text}" placeholder="Nhập nội dung..."
                    class="flex-1 px-3 py-2 border rounded focus:border-green-500 focus:outline-none text-sm">
                <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600">✕</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function addExcludedItem(text = '') {
        const container = document.getElementById('excluded-container');
        const html = `
            <div class="flex items-center gap-2">
                <span class="text-red-600">✗</span>
                <input type="text" name="excluded[]" value="${text}" placeholder="Nhập nội dung..."
                    class="flex-1 px-3 py-2 border rounded focus:border-red-500 focus:outline-none text-sm">
                <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600">✕</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function previewImages(input) {
        const container = document.getElementById('image-preview');
        container.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'aspect-video bg-gray-100 rounded overflow-hidden';
                    div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    container.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        }
    }
</script>
