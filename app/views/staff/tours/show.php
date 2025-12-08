<?php
/**
 * ADMIN - CHI TIẾT TOUR
 * Variables: $tour
 */
require_staff_or_admin();
?>

<div class="max-w-6xl mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Chi tiết Tour</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1 flex flex-wrap items-center gap-2">
                Mã: <span
                    class="font-mono bg-primary-100 px-2 py-0.5 rounded-xl text-accent font-semibold"><?= htmlspecialchars($tour['tour_code']) ?></span>
                <?php if ($tour['tour_type'] == 'custom'): ?>
                    <span class="px-3 py-1 bg-accent-light/20 text-accent-light text-xs font-bold rounded-full">Custom Tour</span>
                <?php else: ?>
                    <span class="px-3 py-1 bg-info-bg text-info-text text-xs font-bold rounded-full">Public Tour</span>
                <?php endif; ?>
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <a href="?act=staff-tours"
                class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
            <a href="?act=staff-tours&action=edit&id=<?= $tour['id'] ?>"
                class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="pencil" class="w-4 h-4"></i>
                Chỉnh sửa
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">

        <!-- LEFT COLUMN -->
        <div class="lg:col-span-2 space-y-4 lg:space-y-6">

            <!-- Basic Info -->
            <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
                <h2 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-4 lg:mb-5">Thông tin chung</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <span class="block text-xs lg:text-sm text-primary-500 font-semibold mb-1">Tên Tour</span>
                        <span class="font-bold text-base lg:text-lg text-primary-700"><?= htmlspecialchars($tour['name']) ?></span>
                    </div>
                    <?php if (!empty($tour['introduction'])): ?>
                        <div class="sm:col-span-2">
                            <span class="block text-xs lg:text-sm text-primary-500 font-semibold mb-1">Giới thiệu ngắn</span>
                            <p class="text-primary-700 mt-1 text-sm lg:text-base"><?= nl2br(htmlspecialchars($tour['introduction'])) ?></p>
                        </div>
                    <?php endif; ?>
                    <div>
                        <span class="block text-xs lg:text-sm text-primary-500 font-semibold mb-1">Điểm khởi hành</span>
                        <span class="text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($tour['departure_location'] ?: '-') ?></span>
                    </div>
                    <div>
                        <span class="block text-xs lg:text-sm text-primary-500 font-semibold mb-1">Thời gian</span>
                        <span class="font-semibold text-primary-700 text-sm lg:text-base"><?= $tour['duration_days'] ?> ngày <?= $tour['duration_nights'] ?>
                            đêm</span>
                    </div>
                    <div>
                        <span class="block text-xs lg:text-sm text-primary-500 font-semibold mb-1">Số khách</span>
                        <span class="font-semibold text-primary-700 text-sm lg:text-base"><?= $tour['min_participants'] ?? 10 ?> -
                            <?= $tour['max_participants'] ?? 45 ?> người</span>
                    </div>
                    <div>
                        <span class="block text-xs lg:text-sm text-primary-500 font-semibold mb-1">Hạn đặt tour</span>
                        <span class="font-semibold text-primary-700 text-sm lg:text-base"><?= $tour['booking_deadline_days'] ?? 1 ?> ngày trước khởi hành</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="block text-xs lg:text-sm text-primary-500 font-semibold mb-1">Mô tả chi tiết</span>
                        <p class="text-primary-700 mt-1 text-sm lg:text-base">
                            <?= nl2br(htmlspecialchars($tour['description'] ?: 'Chưa có mô tả')) ?></p>
                    </div>
                </div>

                <!-- Chi phí cố định -->
                <?php if (!empty($tour['fixed_cost_guide']) || !empty($tour['fixed_cost_management']) || !empty($tour['fixed_cost_marketing']) || !empty($tour['fixed_cost_other'])): ?>
                    <div class="mt-4 lg:mt-5 p-4 lg:p-5 bg-warning-bg rounded-2xl border border-warning">
                        <h3 class="font-semibold text-warning-text mb-3 text-sm lg:text-base flex items-center gap-2">
                            <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                            Chi phí cố định
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <?php if (!empty($tour['fixed_cost_guide'])): ?>
                                <div>
                                    <span class="text-primary-500">Lương HDV:</span>
                                    <span class="font-semibold ml-2 text-primary-700"><?= number_format($tour['fixed_cost_guide'], 0, ',', '.') ?>
                                        đ</span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($tour['fixed_cost_management'])): ?>
                                <div>
                                    <span class="text-primary-500">Chi phí quản lý:</span>
                                    <span
                                        class="font-semibold ml-2 text-primary-700"><?= number_format($tour['fixed_cost_management'], 0, ',', '.') ?>
                                        đ</span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($tour['fixed_cost_marketing'])): ?>
                                <div>
                                    <span class="text-primary-500">Chi phí marketing:</span>
                                    <span
                                        class="font-semibold ml-2 text-primary-700"><?= number_format($tour['fixed_cost_marketing'], 0, ',', '.') ?>
                                        đ</span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($tour['fixed_cost_other'])): ?>
                                <div>
                                    <span class="text-primary-500">Chi phí khác:</span>
                                    <span class="font-semibold ml-2 text-primary-700"><?= number_format($tour['fixed_cost_other'], 0, ',', '.') ?>
                                        đ</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php
                        $totalFixedCost = ($tour['fixed_cost_guide'] ?? 0) + ($tour['fixed_cost_management'] ?? 0) + ($tour['fixed_cost_marketing'] ?? 0) + ($tour['fixed_cost_other'] ?? 0);
                        $minParticipants = $tour['min_participants'] ?? 15;
                        $fixedCostPerPerson = $minParticipants > 0 ? $totalFixedCost / $minParticipants : 0;
                        ?>
                        <div class="mt-3 pt-3 border-t border-warning">
                            <div class="flex justify-between items-center">
                                <span class="text-primary-700 font-semibold text-sm lg:text-base">Tổng chi phí cố định:</span>
                                <span class="font-bold text-warning-text text-sm lg:text-base"><?= number_format($totalFixedCost, 0, ',', '.') ?>
                                    đ</span>
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-primary-500 text-xs lg:text-sm">Chi phí cố định/người:</span>
                                <span class="font-semibold text-xs lg:text-sm text-primary-700"><?= number_format($fixedCostPerPerson, 0, ',', '.') ?>
                                    đ</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Itinerary -->
            <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
                <h2 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2 lg:pb-3 mb-4 lg:mb-5">
                    Lịch trình (<?= $tour['duration_days'] ?>N<?= $tour['duration_nights'] ?>Đ)
                </h2>
                <?php if (!empty($tour['itinerary'])): ?>
                    <div class="space-y-4">
                        <?php
                        // Group day services by day_number
                        $day_services_by_day = [];
                        if (!empty($tour['itinerary_day_services'])) {
                            foreach ($tour['itinerary_day_services'] as $service) {
                                $day = $service['day_number'] ?? null;
                                if ($day) {
                                    if (!isset($day_services_by_day[$day])) {
                                        $day_services_by_day[$day] = [];
                                    }
                                    $day_services_by_day[$day][] = $service;
                                }
                            }
                        }
                        ?>
                        <?php foreach ($tour['itinerary'] as $item): ?>
                            <?php
                            $day_num = $item['day_number'];
                            $day_services = $day_services_by_day[$day_num] ?? [];
                            ?>
                            <div class="border border-primary-200 rounded-xl p-4 lg:p-6">
                                <div class="flex gap-4 mb-3">
                                    <div
                                        class="w-10 h-10 bg-accent text-white rounded-full flex items-center justify-center font-bold flex-shrink-0">
                                        <?= $day_num ?>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-bold text-primary-800">
                                            <?= htmlspecialchars($item['title'] ?: "Ngày $day_num") ?></h4>
                                        <?php if (!empty($item['destination_name'])): ?>
                                            <p class="text-sm text-blue-600">📍 <?= htmlspecialchars($item['destination_name']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if (!empty($item['description'])): ?>
                                    <div class="ml-14 mb-3">
                                        <div class="text-sm text-gray-700 prose max-w-none"><?= $item['description'] ?></div>
                                    </div>
                                <?php endif; ?>

                                <!-- Dịch vụ theo ngày -->
                                <?php if (!empty($day_services)): ?>
                                    <div class="ml-14 mt-3 pt-3 border-t border-primary-200">
                                        <h5 class="text-sm font-semibold text-primary-700 mb-2">
                                            <i class="fas fa-concierge-bell mr-1 text-green-500"></i>Dịch vụ theo ngày:
                                        </h5>
                                        <div class="space-y-2">
                                            <?php foreach ($day_services as $service): ?>
                                                <div class="bg-green-50 border border-green-200 rounded p-2 text-sm">
                                                    <div class="font-medium text-gray-800">
                                                        <?= htmlspecialchars($service['service_name'] ?? 'N/A') ?></div>
                                                    <div class="text-gray-600 mt-1">
                                                        Giá: <?= number_format($service['unit_price'] ?? 0, 0, ',', '.') ?>đ /
                                                        <?= htmlspecialchars($service['unit'] ?? 'đơn vị') ?>
                                                        × <?= $service['quantity'] ?? 1 ?>
                                                        <?php if ($service['is_included_in_price'] ?? 0): ?>
                                                            <span class="text-green-600 ml-2">✓ Bao gồm trong giá</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php if (!empty($service['notes'])): ?>
                                                        <div class="text-xs text-gray-500 mt-1">Ghi chú:
                                                            <?= htmlspecialchars($service['notes']) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 italic text-center py-4">Chưa có lịch trình.</p>
                <?php endif; ?>
            </div>

            <!-- Included / Excluded -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Bao gồm / Không bao gồm</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Included -->
                    <div>
                        <h3 class="font-medium text-green-700 mb-2">✅ Giá tour BAO GỒM:</h3>
                        <?php if (!empty($tour['includes'])): ?>
                            <ul class="space-y-1">
                                <?php foreach ($tour['includes'] as $item): ?>
                                    <li class="text-sm text-gray-700 flex items-start gap-2">
                                        <span class="text-green-500 mt-0.5">✓</span>
                                        <span><?= htmlspecialchars($item) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-gray-400 italic text-sm">Chưa cập nhật</p>
                        <?php endif; ?>
                    </div>
                    <!-- Excluded -->
                    <div>
                        <h3 class="font-medium text-red-700 mb-2">❌ Giá tour KHÔNG BAO GỒM:</h3>
                        <?php if (!empty($tour['excludes'])): ?>
                            <ul class="space-y-1">
                                <?php foreach ($tour['excludes'] as $item): ?>
                                    <li class="text-sm text-gray-700 flex items-start gap-2">
                                        <span class="text-red-500 mt-0.5">✗</span>
                                        <span><?= htmlspecialchars($item) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-gray-400 italic text-sm">Chưa cập nhật</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN -->
        <div class="space-y-6">

            <!-- Status & Actions -->
            <div
                class="bg-white rounded-lg shadow-sm p-6 border-l-4 <?= $tour['status'] == 'active' ? 'border-green-500' : ($tour['status'] == 'draft' ? 'border-yellow-500' : 'border-red-500') ?>">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Trạng thái</h2>

                <div class="space-y-3 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Trạng thái:</span>
                        <?php if ($tour['status'] == 'active'): ?>
                            <span class="px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full font-medium">✓ Đang hoạt
                                động</span>
                        <?php elseif ($tour['status'] == 'draft'): ?>
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm rounded-full font-medium">📝 Bản
                                nháp</span>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-red-100 text-red-700 text-sm rounded-full font-medium">⛔ Tạm
                                dừng</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Trạng thái:</span>
                        <?php
                        switch ($tour['status']) {
                            case 'pending':
                                echo '<span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm rounded-full">Chờ duyệt</span>';
                                break;
                            case 'active':
                                echo '<span class="px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full">Đã duyệt - Hoạt động</span>';
                                break;
                            case 'rejected':
                                echo '<span class="px-3 py-1 bg-red-100 text-red-700 text-sm rounded-full">Từ chối</span>';
                                break;
                            case 'draft':
                                echo '<span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full">Nháp</span>';
                                break;
                            case 'inactive':
                                echo '<span class="px-3 py-1 bg-gray-300 text-gray-800 text-sm rounded-full">Đã ẩn</span>';
                                break;
                        }
                        ?>
                    </div>
                </div>

                <!-- Actions - Staff không có quyền changeStatus -->
                <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                    <p class="text-sm text-yellow-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        Tour đang ở trạng thái: <strong><?= htmlspecialchars($tour['status']) ?></strong>
                    </p>
                    <p class="text-xs text-yellow-600 mt-2">Chỉ admin mới có quyền duyệt/từ chối tour.</p>
                </div>
            </div>

            <!-- Pricing -->
            <div class="bg-white rounded-lg shadow-sm p-6">
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
                    <hr>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Đặt cọc</span>
                        <span class="text-gray-700"><?= $tour['deposit_percentage'] ?? 30 ?>%</span>
                    </div>
                    <?php if (!empty($tour['estimated_cost_per_person'])): ?>
                        <div class="flex justify-between items-center text-sm mt-2 pt-2 border-t">
                            <span class="text-gray-500">Chi phí ước tính/người</span>
                            <span
                                class="text-gray-700 font-medium"><?= number_format($tour['estimated_cost_per_person'], 0, ',', '.') ?>
                                đ</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Policies -->
            <?php if (!empty($tour['policies'])): ?>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Chính sách</h2>
                    <div class="space-y-3">
                        <?php foreach ($tour['policies'] as $policy): ?>
                            <div class="border border-gray-200 rounded p-3">
                                <div class="font-medium text-gray-800"><?= htmlspecialchars($policy['name']) ?></div>
                                <?php if (!empty($policy['description'])): ?>
                                    <div class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($policy['description']) ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Highlights -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Điểm nổi bật</h2>
                <?php if (!empty($tour['highlights'])): ?>
                    <ul class="space-y-2">
                        <?php foreach ($tour['highlights'] as $hl): ?>
                            <li class="flex items-start gap-2 text-sm text-gray-700">
                                <span class="text-yellow-500 mt-0.5">⭐</span>
                                <span><?= htmlspecialchars($hl) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-400 italic">Chưa cập nhật.</p>
                <?php endif; ?>
            </div>

            <!-- Images -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Thư viện ảnh</h2>
                <?php if (!empty($tour['images'])): ?>
                    <div class="grid grid-cols-2 gap-2">
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
                    </div>
                <?php else: ?>
                    <p class="text-gray-400 italic text-center py-4">Chưa có hình ảnh.</p>
                <?php endif; ?>
            </div>

            <!-- Delete - Staff không có quyền xóa -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="font-medium text-gray-800 mb-2">Xóa Tour</h3>
                <p class="text-sm text-gray-600 mb-3">Chỉ admin mới có quyền xóa tour.</p>
            </div>

        </div>
    </div>
</div>