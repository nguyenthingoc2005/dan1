<?php
/**
 * ADMIN - FORM SỬA TOUR (TABS UI)
 * Variables: $tour, $categories, $destinations
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Sửa Tour: <?= htmlspecialchars($tour['name']) ?></h1>
        <a href="?act=admin&module=tours" class="text-gray-500 hover:text-gray-700">Quay lại danh sách</a>
    </div>

    <form method="POST" action="?act=admin&module=tours&action=update" enctype="multipart/form-data"
        class="bg-white rounded shadow-sm overflow-hidden">
        <input type="hidden" name="id" value="<?= $tour['id'] ?>">

        <!-- TABS HEADER -->
        <div class="flex border-b bg-gray-50">
            <button type="button"
                class="px-6 py-3 text-sm font-medium text-accent border-b-2 border-accent focus:outline-none tab-btn"
                data-tab="tab-info">
                1. Thông tin chung
            </button>
            <button type="button"
                class="px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none tab-btn"
                data-tab="tab-pricing">
                2. Giá & Vận hành
                <?php
                /**
                 * ADMIN - FORM SỬA TOUR (TABS UI)
                 * Variables: $tour, $categories, $destinations
                 */
                if (!is_admin())
                    redirect('?act=access-denied');
                ?>

                <div class="max-w-6xl mx-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-primary">Sửa Tour: <?= htmlspecialchars($tour['name']) ?>
                        </h1>
                        <a href="?act=admin&module=tours" class="text-gray-500 hover:text-gray-700">Quay lại danh
                            sách</a>
                    </div>

                    <form method="POST" action="?act=admin&module=tours&action=update" enctype="multipart/form-data"
                        class="bg-white rounded shadow-sm overflow-hidden">
                        <input type="hidden" name="id" value="<?= $tour['id'] ?>">

                        <!-- TABS HEADER -->
                        <div class="flex border-b bg-gray-50">
                            <button type="button"
                                class="px-6 py-3 text-sm font-medium text-accent border-b-2 border-accent focus:outline-none tab-btn"
                                data-tab="tab-info">
                                1. Thông tin chung
                            </button>
                            <button type="button"
                                class="px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none tab-btn"
                                data-tab="tab-pricing">
                                2. Giá & Vận hành
                            </button>
                            <button type="button"
                                class="px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none tab-btn"
                                data-tab="tab-itinerary">
                                3. Lịch trình
                            </button>
                            <button type="button"
                                class="px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none tab-btn"
                                data-tab="tab-services">
                                4. Dịch vụ (Costing)
                            </button>
                            <button type="button"
                                class="px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none tab-btn"
                                data-tab="tab-media">
                                5. Hình ảnh & Khác
                            </button>
                        </div>

                        <!-- TABS CONTENT -->
                        <div class="p-6">

                            <!-- TAB 1: INFO -->
                            <div id="tab-info" class="tab-content block">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <?php foreach ($categories as $id => $name): ?>
                                        <option value="<?= $id ?>" <?= $tour['category_id'] == $id ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Điểm khởi hành</label>
                                    <input type="text" name="departure_location"
                                        value="<?= htmlspecialchars($tour['departure_location']) ?>"
                                        class="w-full px-3 py-2 border rounded focus:border-accent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại Tour <span
                                            class="text-red-500">*</span></label>
                                    <select name="tour_type" required
                                        class="w-full px-3 py-2 border rounded focus:border-accent">
                                        <option value="public" <?= ($tour['tour_type'] ?? 'public') == 'public' ? 'selected' : '' ?>>
                                            Tour Công Khai (Public) - Có lịch cố định
                                        </option>
                                        <option value="custom" <?= ($tour['tour_type'] ?? '') == 'custom' ? 'selected' : '' ?>>
                                            Tour Tùy Chỉnh (Custom) - Theo yêu cầu khách
                                        </option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <strong>Public:</strong> Tour có lịch khởi hành cố định, cần tạo schedule trước khi booking.<br>
                                        <strong>Custom:</strong> Tour theo yêu cầu, có thể tự động tạo schedule khi booking.
                                    </p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Số ngày</label>
                                        <input type="number" name="duration_days" min="1"
                                            value="<?= $tour['duration_days'] ?>"
                                            class="w-full px-3 py-2 border rounded focus:border-accent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Số đêm</label>
                                        <input type="number" name="duration_nights" min="0"
                                            value="<?= $tour['duration_nights'] ?>"
                                            class="w-full px-3 py-2 border rounded focus:border-accent">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                                    <select name="status" class="w-full px-3 py-2 border rounded focus:border-accent">
                                        <option value="draft" <?= $tour['status'] == 'draft' ? 'selected' : '' ?>>Bản nháp
                                            (Draft)
                                        </option>
                                        <option value="active" <?= $tour['status'] == 'active' ? 'selected' : '' ?>>Hoạt
                                            động (Active)
                                        </option>
                                        <option value="inactive" <?= $tour['status'] == 'inactive' ? 'selected' : '' ?>>Tạm
                                            dừng
                                            (Inactive)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả ngắn</label>
                                <textarea name="description" rows="3"
                                    class="w-full px-3 py-2 border rounded focus:border-accent"><?= htmlspecialchars($tour['description']) ?></textarea>
                            </div>
                        </div>

                        <!-- TAB 2: PRICING -->
                        <div id="tab-pricing" class="tab-content hidden">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Giá người lớn <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" name="adult_price" value="<?= $tour['adult_price'] ?>" required
                                        min="0"
                                        class="w-full px-3 py-2 border rounded focus:border-accent font-bold text-accent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Giá trẻ em</label>
                                    <input type="number" name="child_price" value="<?= $tour['child_price'] ?>" min="0"
                                        class="w-full px-3 py-2 border rounded focus:border-accent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Giá em bé</label>
                                    <input type="number" name="infant_price" value="<?= $tour['infant_price'] ?>"
                                        min="0" class="w-full px-3 py-2 border rounded focus:border-accent">
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: ITINERARY -->
                        <div id="tab-itinerary" class="tab-content hidden">
                            <div id="itinerary-container">
                                <?php if (!empty($tour['itinerary'])): ?>
                                    <?php foreach ($tour['itinerary'] as $item): ?>
                                        <div class="bg-gray-50 p-4 rounded border mb-4 itinerary-item relative group">
                                            <div class="flex justify-between mb-2">
                                                <h3 class="font-bold text-gray-700">Ngày <?= $item['day_number'] ?></h3>
                                                <input type="hidden" name="itinerary_day[]" value="<?= $item['day_number'] ?>">
                                                <button type="button" onclick="this.parentElement.parentElement.remove()"
                                                    class="text-red-500 text-sm hover:underline">Xóa</button>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                                                <input type="text" name="itinerary_title[]"
                                                    value="<?= htmlspecialchars($item['title']) ?>"
                                                    placeholder="Tiêu đề hoạt động" class="w-full px-3 py-2 border rounded">
                                                <select name="itinerary_dest[]" class="w-full px-3 py-2 border rounded">
                                                    <option value="">-- Chọn điểm đến chính --</option>
                                                    <?php foreach ($destinations as $id => $name): ?>
                                                        <option value="<?= $id ?>" <?= $item['destination_id'] == $id ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($name) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <textarea name="itinerary_desc[]" rows="2" placeholder="Mô tả chi tiết..."
                                                class="w-full px-3 py-2 border rounded"><?= htmlspecialchars($item['description']) ?></textarea>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <button type="button" onclick="addItineraryDay()"
                                class="px-4 py-2 bg-green-100 text-green-700 rounded hover:bg-green-200 border border-green-200">
                                + Thêm ngày tiếp theo
                            </button>
                        </div>

                        <!-- TAB 4: SERVICES -->
                        <div id="tab-services" class="tab-content hidden">
                            <div class="mb-4 flex justify-between items-center">
                                <h3 class="font-bold text-gray-700">Cấu hình dịch vụ (Costing)</h3>
                                <button type="button" onclick="addServiceRow()"
                                    class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 text-sm">
                                    + Thêm dịch vụ
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500 border rounded">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3">Dịch vụ</th>
                                            <th class="px-4 py-3">Tính theo</th>
                                            <th class="px-4 py-3 w-24">SL</th>
                                            <th class="px-4 py-3">Đơn giá</th>
                                            <th class="px-4 py-3">ĐVT</th>
                                            <th class="px-4 py-3">Ghi chú</th>
                                            <th class="px-4 py-3"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="services-container">
                                        <!-- Dynamic Rows -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 bg-yellow-50 p-3 rounded text-sm text-yellow-700">
                                💡 <strong>Lưu ý:</strong> Đây là giá vốn (Cost) dự kiến để tính lợi nhuận. Giá bán Tour
                                ở Bước 2.
                            </div>
                        </div>

                        <!-- TAB 5: MEDIA -->
                        <div id="tab-media" class="tab-content hidden">
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh hiện tại</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                    <?php foreach ($tour['images'] as $img): ?>
                                        <div
                                            class="relative group aspect-video bg-gray-100 rounded overflow-hidden border <?= $img['is_primary'] ? 'border-2 border-accent' : 'border-gray-200' ?>">
                                            <img src="<?= htmlspecialchars($img['image_url']) ?>"
                                                class="w-full h-full object-cover">
                                            <?php if ($img['is_primary']): ?>
                                                <span
                                                    class="absolute top-2 right-2 px-2 py-1 bg-accent text-white text-xs rounded shadow">Primary</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <label class="block text-sm font-medium text-gray-700 mb-2">Thêm ảnh mới</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:bg-gray-50"
                                    onclick="document.getElementById('images').click()">
                                    <input type="file" name="images[]" id="images" multiple accept="image/*"
                                        class="hidden" onchange="previewImages(this)">
                                    <div class="text-4xl mb-2">📷</div>
                                    <p class="text-gray-500">Click để tải thêm ảnh</p>
                                </div>
                                <div id="image-preview" class="grid grid-cols-4 gap-4 mt-4"></div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Điểm nổi bật
                                    (Highlights)</label>
                                <textarea name="highlights" rows="5"
                                    class="w-full px-3 py-2 border rounded focus:border-accent"><?= htmlspecialchars(implode("\n", $tour['highlights'])) ?></textarea>
                                <p class="text-xs text-gray-500 mt-1">Mỗi dòng một điểm nổi bật.</p>
                            </div>
                        </div>

                </div>

                <!-- FOOTER ACTIONS -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t">
                    <a href="?act=admin&module=tours"
                        class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50">Hủy</a>
                    <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600 shadow">Cập
                        nhật
                        Tour</button>
                </div>
    </form>
</div>

<script>
    // TABS LOGIC
    const tabs = document.querySelectorAll('.tab-btn');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => {
                t.classList.remove('text-accent', 'border-accent');
                t.classList.add('text-gray-500', 'border-transparent');
            });
            contents.forEach(c => c.classList.add('hidden'));
            contents.forEach(c => c.classList.remove('block'));

            tab.classList.remove('text-gray-500', 'border-transparent');
            tab.classList.add('text-accent', 'border-accent');

            const target = document.getElementById(tab.dataset.tab);
            target.classList.remove('hidden');
            target.classList.add('block');
        });
    });

    // ITINERARY LOGIC
    let dayCount = <?= !empty($tour['itinerary']) ? count($tour['itinerary']) : 0 ?>;
    const destinations = <?= json_encode($destinations) ?>;
    const servicesList = <?= json_encode($services ?? []) ?>;
    const existingServices = <?= json_encode($tour_services ?? []) ?>;

    function addItineraryDay() {
        dayCount++;
        const container = document.getElementById('itinerary-container');

        let destOptions = '<option value="">-- Chọn điểm đến chính --</option>';
        for (const [id, name] of Object.entries(destinations)) {
            destOptions += `<option value="${id}">${name}</option>`;
        }

        const html = `
            <div class="bg-gray-50 p-4 rounded border mb-4 itinerary-item relative group">
                <div class="flex justify-between mb-2">
                    <h3 class="font-bold text-gray-700">Ngày ${dayCount}</h3>
                    <input type="hidden" name="itinerary_day[]" value="${dayCount}">
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-500 text-sm hover:underline">Xóa</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                    <input type="text" name="itinerary_title[]" placeholder="Tiêu đề hoạt động" class="w-full px-3 py-2 border rounded">
                    <select name="itinerary_dest[]" class="w-full px-3 py-2 border rounded">
                        ${destOptions}
                    </select>
                </div>
                <textarea name="itinerary_desc[]" rows="2" placeholder="Mô tả chi tiết..." class="w-full px-3 py-2 border rounded"></textarea>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    // IMAGE PREVIEW
    function previewImages(input) {
        const container = document.getElementById('image-preview');
        container.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const div = document.createElement('div');
                    div.className = 'relative aspect-video bg-gray-100 rounded overflow-hidden shadow-sm';
                    div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    container.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        }
    }

    // SERVICES LOGIC
    function addServiceRow(data = {}) {
        const container = document.getElementById('services-container');

        let serviceOptions = '<option value="">-- Chọn dịch vụ --</option>';
        servicesList.forEach(s => {
            const selected = (data.service_id == s.id) ? 'selected' : '';
            serviceOptions += `<option value="${s.id}" data-price="${s.unit_price}" data-unit="${s.unit}" data-name="${s.name}" ${selected}>${s.name} (${new Intl.NumberFormat('vi-VN').format(s.unit_price)}đ)</option>`;
        });

        const html = `
            <tr class="bg-white border-b hover:bg-gray-50">
                <td class="px-2 py-2">
                    <select name="service_ids[]" class="w-full px-2 py-1 border rounded service-select" onchange="fillServiceData(this)" required>
                        ${serviceOptions}
                    </select>
                    <input type="hidden" name="service_names[]" class="service-name-input" value="${data.service_name || ''}">
                </td>
                <td class="px-2 py-2">
                    <select name="service_calc_types[]" class="w-full px-2 py-1 border rounded">
                        <option value="per_person" ${data.calculation_type == 'per_person' ? 'selected' : ''}>Theo khách</option>
                        <option value="per_group" ${data.calculation_type == 'per_group' ? 'selected' : ''}>Theo đoàn</option>
                        <option value="per_day" ${data.calculation_type == 'per_day' ? 'selected' : ''}>Theo ngày</option>
                        <option value="fixed" ${data.calculation_type == 'fixed' ? 'selected' : ''}>Cố định</option>
                    </select>
                </td>
                <td class="px-2 py-2">
                    <input type="number" name="service_quantities[]" value="${data.fixed_quantity || 1}" min="1" class="w-full px-2 py-1 border rounded text-center">
                </td>
                <td class="px-2 py-2">
                    <input type="number" name="service_prices[]" value="${data.unit_price || 0}" min="0" class="w-full px-2 py-1 border rounded text-right service-price">
                </td>
                <td class="px-2 py-2">
                    <input type="text" name="service_units[]" value="${data.unit || ''}" class="w-full px-2 py-1 border rounded service-unit">
                </td>
                <td class="px-2 py-2">
                    <input type="text" name="service_notes[]" value="${data.notes || ''}" class="w-full px-2 py-1 border rounded">
                </td>
                <td class="px-2 py-2 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-red-500 hover:text-red-700">🗑️</button>
                </td>
            </tr>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function fillServiceData(select) {
        const option = select.options[select.selectedIndex];
        const row = select.closest('tr');

        if (option.value) {
            row.querySelector('.service-price').value = option.dataset.price;
            row.querySelector('.service-unit').value = option.dataset.unit;
            row.querySelector('.service-name-input').value = option.dataset.name;
        }
    }

    // Load existing services
    if (existingServices.length > 0) {
        existingServices.forEach(s => addServiceRow(s));
    } else {
        // addServiceRow(); // Optional: Add empty row if none
    }
</script>