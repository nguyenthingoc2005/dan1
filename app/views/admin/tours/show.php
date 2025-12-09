<?php
/**
 * ADMIN - CHI TIẾT TOUR
 * Variables: $tour
 */
if (!is_admin())
    redirect('?act=access-denied');
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
            <a href="?act=admin&module=tours"
                class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
            <a href="?act=admin&module=tours&action=edit&id=<?= $tour['id'] ?>"
                class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="pencil" class="w-4 h-4"></i>
                Chỉnh sửa
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">

        <!-- LEFT COLUMN -->
        <div class="lg:col-span-2 space-y-6">

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
                <?php 
                // Chỉ hiển thị fixed_cost_total hoặc fixed_cost_other (nếu fixed_cost_total không có)
                $fixedCostTotal = !empty($tour['fixed_cost_total']) ? (float)$tour['fixed_cost_total'] : (!empty($tour['fixed_cost_other']) ? (float)$tour['fixed_cost_other'] : 0);
                if ($fixedCostTotal > 0): 
                ?>
                    <div class="mt-4 lg:mt-5 p-4 lg:p-5 bg-warning-bg rounded-2xl border border-warning">
                        <h3 class="font-semibold text-warning-text mb-3 text-sm lg:text-base flex items-center gap-2">
                            <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                            Chi phí cố định
                        </h3>
                        <div class="text-sm">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-primary-500">Chi phí khác:</span>
                                <span class="font-semibold text-primary-700"><?= number_format($fixedCostTotal, 0, ',', '.') ?> đ</span>
                            </div>
                        </div>
                        <?php
                        $minParticipants = $tour['min_participants'] ?? 15;
                        $fixedCostPerPerson = $minParticipants > 0 ? $fixedCostTotal / $minParticipants : 0;
                        ?>
                        <div class="mt-3 pt-3 border-t border-warning">
                            <div class="flex justify-between items-center">
                                <span class="text-primary-700 font-semibold text-sm lg:text-base">Tổng chi phí cố định:</span>
                                <span class="font-bold text-warning-text text-sm lg:text-base"><?= number_format($fixedCostTotal, 0, ',', '.') ?> đ</span>
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-primary-500 text-xs lg:text-sm">Chi phí cố định/người:</span>
                                <span class="font-semibold text-xs lg:text-sm text-primary-700"><?= number_format($fixedCostPerPerson, 0, ',', '.') ?> đ</span>
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
                            <div class="border border-gray-200 rounded-lg p-4">
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
                                    <div class="ml-14 mt-3 pt-3 border-t border-gray-200">
                                        <h5 class="text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-concierge-bell mr-1 text-green-500"></i>Dịch vụ theo ngày:
                                        </h5>
                                        <div class="space-y-2">
                                            <?php foreach ($day_services as $service): ?>
                                                <div class="bg-green-50 border border-green-200 rounded p-2 text-sm">
                                                    <div class="font-medium text-primary-800">
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
            <div class="bg-panel rounded-xl shadow-sm p-4 lg:p-6">
                <h2 class="text-lg font-bold text-primary-800 border-b border-primary-200 pb-2 mb-4">Bao gồm / Không bao gồm</h2>
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
                class="bg-panel rounded-xl shadow-sm p-4 lg:p-6 border-l-4 <?= $tour['status'] == 'active' ? 'border-success' : ($tour['status'] == 'draft' ? 'border-warning' : 'border-danger') ?>">
                <h2 class="text-lg font-bold text-primary-800 border-b border-primary-200 pb-2 mb-4">Trạng thái</h2>

                <div class="space-y-3 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Trạng thái:</span>
                        <?php
                        switch ($tour['status']) {
                            case 'pending':
                                echo '<span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm rounded-full font-medium">⏳ Chờ duyệt</span>';
                                break;
                            case 'active':
                                echo '<span class="px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full font-medium">✓ Đang hoạt động</span>';
                                break;
                            case 'rejected':
                                echo '<span class="px-3 py-1 bg-red-100 text-red-700 text-sm rounded-full font-medium">⛔ Từ chối</span>';
                                break;
                            case 'draft':
                                echo '<span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full font-medium">📝 Bản nháp</span>';
                                break;
                            case 'inactive':
                                echo '<span class="px-3 py-1 bg-primary-200 text-primary-800 text-sm rounded-full font-medium">🙈 Đã ẩn</span>';
                                break;
                            default:
                                echo '<span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full font-medium">' . htmlspecialchars($tour['status']) . '</span>';
                                break;
                        }
                        ?>
                    </div>
                </div>

                <!-- Actions -->
                <form method="POST" action="?act=admin&module=tours&action=changeStatus">
                    <input type="hidden" name="id" value="<?= $tour['id'] ?>">
                    <div class="space-y-2">
                        <?php if ($tour['status'] == 'pending'): ?>
                            <button type="submit" name="action" value="approve"
                                class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                                onclick="return confirm('Duyệt tour này?')">
                                ✅ Duyệt Tour
                            </button>
                            <button type="submit" name="action" value="reject"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                                onclick="return confirm('Từ chối tour này?')">
                                ⛔ Từ chối
                            </button>
                        <?php endif; ?>
                        <?php if ($tour['status'] == 'active'): ?>
                            <button type="submit" name="action" value="hide"
                                class="w-full px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
                                onclick="return confirm('Ẩn tour này?')">
                                🙈 Ẩn Tour
                            </button>
                        <?php elseif ($tour['status'] == 'inactive'): ?>
                            <button type="submit" name="action" value="show"
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                onclick="return confirm('Hiện lại tour này?')">
                                👁️ Hiện Tour
                            </button>
                        <?php endif; ?>
                        <?php if ($tour['status'] == 'rejected' && !empty($tour['rejection_reason'])): ?>
                            <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded text-sm">
                                <strong class="text-red-800">Lý do từ chối:</strong>
                                <p class="text-red-700 mt-1"><?= htmlspecialchars($tour['rejection_reason']) ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if ($tour['status'] == 'active' && !empty($tour['approved_by'])): ?>
                            <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded text-sm">
                                <strong class="text-green-800">Đã duyệt bởi:</strong>
                                <p class="text-green-700 mt-1">
                                    <?= htmlspecialchars($tour['approved_by_name'] ?? 'Admin') ?>
                                    vào
                                    <?= !empty($tour['approved_at']) ? date('d/m/Y H:i', strtotime($tour['approved_at'])) : '' ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Pricing -->
            <div class="bg-panel rounded-xl shadow-sm p-4 lg:p-6">
                <h2 class="text-lg font-bold text-primary-800 border-b border-primary-200 pb-2 mb-4">Bảng giá</h2>
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
                <div class="bg-panel rounded-xl shadow-sm p-4 lg:p-6">
                    <h2 class="text-lg font-bold text-primary-800 border-b border-primary-200 pb-2 mb-4">Chính sách</h2>
                    <div class="space-y-3">
                        <?php foreach ($tour['policies'] as $policy): ?>
                            <div class="border border-gray-200 rounded p-3">
                                <div class="font-medium text-primary-800"><?= htmlspecialchars($policy['name']) ?></div>
                                <?php if (!empty($policy['description'])): ?>
                                    <div class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($policy['description']) ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Highlights -->
            <div class="bg-panel rounded-xl shadow-sm p-4 lg:p-6">
                <h2 class="text-lg font-bold text-primary-800 border-b border-primary-200 pb-2 mb-4">Điểm nổi bật</h2>
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
            <div class="bg-panel rounded-xl shadow-sm p-4 lg:p-6">
                <h2 class="text-lg font-bold text-primary-800 border-b border-primary-200 pb-2 mb-4">Thư viện ảnh</h2>
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

            <!-- Delete -->
            <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                <h3 class="font-medium text-red-800 mb-2">Xóa Tour</h3>
                <p class="text-sm text-red-600 mb-3">Hành động này không thể hoàn tác. Tour đã có booking sẽ không thể
                    xóa.</p>
                <a href="?act=admin&module=tours&action=delete&id=<?= $tour['id'] ?>"
                    class="block w-full text-center px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                    onclick="return confirm('Bạn có chắc chắn muốn xóa tour này? Hành động này không thể hoàn tác!')">
                    🗑️ Xóa Tour
                </a>
            </div>

        </div>
    </div>
</div>