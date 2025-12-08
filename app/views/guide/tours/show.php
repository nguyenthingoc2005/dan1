<?php
/**
 * GUIDE - TOUR DETAIL & PASSENGER MANIFEST
 * Variables: $schedule, $tour, $passengers
 * Flat Design - Không shadow, không gradient, dùng spacing thay border
 */
?>

<div class="max-w-7xl mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Chi tiết Tour</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">
                <?= htmlspecialchars($tour['tour_code']) ?> - <?= htmlspecialchars($tour['name']) ?>
            </p>
        </div>
        <a href="?act=guide-tours"
            class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center flex items-center justify-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    <!-- Navigation Tabs - Responsive -->
    <div class="bg-panel rounded-2xl p-2 lg:p-3 mb-4 lg:mb-6 border border-primary-100">
        <div class="flex gap-2 overflow-x-auto">
            <a href="#tour-info" class="px-3 lg:px-4 py-2 rounded-xl whitespace-nowrap bg-primary-100 text-primary-700 font-semibold hover:bg-primary-200 transition-colors text-xs lg:text-sm">
                <i data-lucide="file-text" class="w-4 h-4 inline mr-1"></i>
                Thông tin Tour
            </a>
            <a href="#services" class="px-3 lg:px-4 py-2 rounded-xl whitespace-nowrap transition-colors text-xs lg:text-sm <?= !empty($bookingServices) ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' ?>">
                <i data-lucide="briefcase" class="w-4 h-4 inline mr-1"></i>
                Dịch vụ (<?= count($bookingServices ?? []) ?>)
            </a>
            <a href="#passengers" class="px-3 lg:px-4 py-2 rounded-xl whitespace-nowrap bg-primary-100 text-primary-700 font-semibold hover:bg-primary-200 transition-colors text-xs lg:text-sm">
                <i data-lucide="users" class="w-4 h-4 inline mr-1"></i>
                Hành khách (<?= count($passengers ?? []) ?>)
            </a>
        </div>
    </div>

    <!-- TOUR DETAILS SECTION -->
    <div id="tour-info" class="space-y-4 lg:space-y-8 mb-6 lg:mb-8">
        <!-- Basic Info - Responsive -->
        <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
            <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-4 lg:mb-6">Thông tin chuyến đi</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 lg:gap-6">
                <div>
                    <div class="text-xs text-primary-500 uppercase tracking-wide mb-1 font-semibold">Ngày khởi hành</div>
                    <div class="font-semibold text-primary-700 text-sm lg:text-base"><?= date('d/m/Y', strtotime($schedule['start_date'])) ?></div>
                </div>
                <div>
                    <div class="text-xs text-primary-500 uppercase tracking-wide mb-1 font-semibold">Ngày kết thúc</div>
                    <div class="font-semibold text-primary-700 text-sm lg:text-base"><?= date('d/m/Y', strtotime($schedule['end_date'])) ?></div>
                </div>
                <div>
                    <div class="text-xs text-primary-500 uppercase tracking-wide mb-1 font-semibold">Thời lượng</div>
                    <div class="font-semibold text-primary-700 text-sm lg:text-base">
                        <?= $tour['duration_days'] ?> ngày <?= $tour['duration_nights'] ?> đêm
                    </div>
                </div>
                <div>
                    <div class="text-xs text-primary-500 uppercase tracking-wide mb-1 font-semibold">Số lượng khách</div>
                    <div class="font-bold text-accent text-base lg:text-lg"><?= count($passengers) ?></div>
                    <div class="text-xs text-primary-500">/ <?= $schedule['quota'] ?> chỗ</div>
                </div>
                <div>
                    <div class="text-xs text-primary-500 uppercase tracking-wide mb-1 font-semibold">Điểm khởi hành</div>
                    <div class="font-semibold text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($tour['departure_location'] ?? '-') ?></div>
                </div>
                <div>
                    <div class="text-xs text-primary-500 uppercase tracking-wide mb-1 font-semibold">Loại tour</div>
                    <div class="font-semibold text-primary-700 text-sm lg:text-base">
                        <?= $tour['tour_type'] === 'public' ? 'Tour công khai' : 'Tour riêng' ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Introduction & Description -->
        <?php if (!empty($tour['introduction']) || !empty($tour['description'])): ?>
            <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
                <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-4">Giới thiệu Tour</h2>
                <div class="space-y-4 text-primary-700 leading-relaxed text-sm lg:text-base">
                    <?php if (!empty($tour['introduction'])): ?>
                        <p><?= nl2br(htmlspecialchars($tour['introduction'])) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($tour['description'])): ?>
                        <p><?= nl2br(htmlspecialchars($tour['description'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Highlights -->
        <?php if (!empty($tour['highlights'])): ?>
            <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
                <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-4">Điểm nổi bật</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                    <?php foreach ($tour['highlights'] as $highlight): ?>
                        <div class="flex items-start gap-2">
                            <i data-lucide="star" class="w-4 h-4 text-accent mt-0.5 flex-shrink-0"></i>
                            <span class="text-primary-700 text-sm lg:text-base"><?= htmlspecialchars(is_array($highlight) ? ($highlight['highlight'] ?? '') : $highlight) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Itinerary -->
        <?php if (!empty($tour['itinerary'])): ?>
            <div class="bg-panel rounded p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-6">Lịch trình Tour</h2>
                <div class="space-y-6">
                    <?php foreach ($tour['itinerary'] as $day): ?>
                        <div class="pl-4 border-l-2 border-accent">
                            <div class="font-bold text-slate-800 mb-2">
                                Ngày <?= $day['day_number'] ?? $day['day'] ?? '' ?>: <?= htmlspecialchars($day['title'] ?? '') ?>
                            </div>
                            
                            <!-- Destination Info -->
                            <?php if (!empty($day['destination_name'])): ?>
                                <div class="bg-slate-50 rounded p-3 mb-3">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-accent text-lg">📍</span>
                                        <span class="text-slate-800 font-semibold"><?= htmlspecialchars($day['destination_name']) ?></span>
                                    </div>
                                    <?php if (!empty($day['province_name']) || !empty($day['country_name'])): ?>
                                        <div class="text-xs text-slate-600 ml-6">
                                            <?php if (!empty($day['province_name'])): ?>
                                                <?= htmlspecialchars($day['province_name']) ?>
                                            <?php endif; ?>
                                            <?php if (!empty($day['province_name']) && !empty($day['country_name'])): ?>
                                                <span class="mx-1">•</span>
                                            <?php endif; ?>
                                            <?php if (!empty($day['country_name'])): ?>
                                                <?= htmlspecialchars($day['country_name']) ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($day['destination_locations'])): ?>
                                        <div class="text-xs text-slate-600 ml-6 mt-1">
                                            <span class="font-medium">Địa chỉ:</span> <?= htmlspecialchars($day['destination_locations']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($day['destination_description'])): ?>
                                        <div class="text-sm text-slate-700 mt-2 ml-6">
                                            <?= nl2br(htmlspecialchars($day['destination_description'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Day Description -->
                            <?php if (!empty($day['description'])): ?>
                                <div class="text-slate-600 text-sm leading-relaxed"><?= nl2br(htmlspecialchars($day['description'])) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Included & Excluded - Side by side -->
        <?php if (!empty($tour['includes']) || !empty($tour['excludes'])): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php if (!empty($tour['includes'])): ?>
                    <div class="bg-panel rounded p-6">
                        <h2 class="text-lg font-bold text-slate-800 mb-4">Bao gồm</h2>
                        <div class="space-y-2">
                            <?php foreach ($tour['includes'] as $item): ?>
                                <div class="flex items-start gap-2">
                                    <span class="text-green-600 mt-0.5">✓</span>
                                    <span class="text-slate-700"><?= htmlspecialchars(is_array($item) ? ($item['item'] ?? '') : $item) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($tour['excludes'])): ?>
                    <div class="bg-panel rounded p-6">
                        <h2 class="text-lg font-bold text-slate-800 mb-4">Không bao gồm</h2>
                        <div class="space-y-2">
                            <?php foreach ($tour['excludes'] as $item): ?>
                                <div class="flex items-start gap-2">
                                    <span class="text-red-600 mt-0.5">✗</span>
                                    <span class="text-slate-700"><?= htmlspecialchars(is_array($item) ? ($item['item'] ?? '') : $item) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Policies -->
        <?php if (!empty($tour['policies'])): ?>
            <div class="bg-panel rounded p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-6">Chính sách</h2>
                <div class="space-y-4">
                    <?php foreach ($tour['policies'] as $policy): ?>
                        <div class="pl-4 border-l-2 border-slate-200">
                            <h3 class="font-semibold text-slate-800 mb-1"><?= htmlspecialchars($policy['name'] ?? '') ?></h3>
                            <?php if (!empty($policy['description'])): ?>
                                <p class="text-slate-600 text-sm leading-relaxed"><?= nl2br(htmlspecialchars($policy['description'])) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Dịch vụ đã đặt thực tế (Booking Services) -->
        <div id="services" class="space-y-8 mb-8">
        <?php if (!empty($bookingServices)): ?>
            <div class="bg-panel rounded p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-6">Dịch vụ đã đặt cho chuyến đi</h2>
                <div class="space-y-6">
                    <?php foreach ($bookingServicesByType as $type => $services): ?>
                        <div class="border-l-2 border-accent pl-4">
                            <h3 class="font-bold text-slate-800 mb-4"><?= htmlspecialchars($type) ?></h3>
                            <div class="space-y-3">
                                <?php foreach ($services as $service): ?>
                                    <div class="bg-slate-50 rounded p-4 border border-slate-200">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex-1">
                                                <div class="font-semibold text-slate-900 mb-1">
                                                    <?= htmlspecialchars($service['service_name'] ?? $service['service_name_original'] ?? 'Dịch vụ') ?>
                                                </div>
                                                <?php if (!empty($service['supplier_name'])): ?>
                                                    <div class="text-sm text-slate-600 mb-1">
                                                        <span class="font-medium">Nhà cung cấp:</span> 
                                                        <?= htmlspecialchars($service['supplier_name']) ?>
                                                        <?php if (!empty($service['supplier_phone'])): ?>
                                                            <span class="ml-2 text-xs">
                                                                📞 <a href="tel:<?= htmlspecialchars($service['supplier_phone']) ?>" 
                                                                      class="text-accent hover:text-accent/80">
                                                                    <?= htmlspecialchars($service['supplier_phone']) ?>
                                                                </a>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php if (!empty($service['supplier_contact'])): ?>
                                                        <div class="text-xs text-slate-500 mb-1">
                                                            Người liên hệ: <?= htmlspecialchars($service['supplier_contact']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?php if (!empty($service['service_date'])): ?>
                                                    <div class="text-xs text-slate-500 mb-1">
                                                        📅 Ngày: <?= date('d/m/Y', strtotime($service['service_date'])) ?>
                                                        <?php if (!empty($service['from_date']) && !empty($service['to_date'])): ?>
                                                            (<?= date('d/m', strtotime($service['from_date'])) ?> - <?= date('d/m', strtotime($service['to_date'])) ?>)
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($service['booking_code'])): ?>
                                                    <div class="text-xs text-slate-500">
                                                        Booking: <span class="font-mono"><?= htmlspecialchars($service['booking_code']) ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-right">
                                                <?php if (!empty($service['total_price'])): ?>
                                                    <div class="font-semibold text-slate-900">
                                                        <?= number_format($service['total_price']) ?> VNĐ
                                                    </div>
                                                    <?php if (!empty($service['quantity']) && $service['quantity'] > 1): ?>
                                                        <div class="text-xs text-slate-500">
                                                            <?= $service['quantity'] ?> <?= $service['unit'] ?? '' ?> 
                                                            × <?= number_format($service['unit_price'] ?? 0) ?> VNĐ
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if (!empty($service['notes'])): ?>
                                            <div class="text-sm text-slate-600 mt-2 pt-2 border-t border-slate-200">
                                                <span class="font-medium">Ghi chú:</span> <?= nl2br(htmlspecialchars($service['notes'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-panel rounded p-6 border border-slate-200">
                <div class="text-center py-8 text-slate-400">
                    <p class="text-lg mb-2">Chưa có dịch vụ nào được đặt cho chuyến đi này.</p>
                    <p class="text-sm">Dịch vụ sẽ được hiển thị khi có booking được xác nhận.</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Lịch trình dịch vụ theo ngày (Template) -->
        <?php if (!empty($servicesByDay)): ?>
            <div class="bg-panel rounded p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-6">Lịch trình dịch vụ theo ngày (Template)</h2>
                <div class="space-y-6">
                    <?php foreach ($servicesByDay as $day => $services): ?>
                        <div class="border-l-2 border-slate-300 pl-4">
                            <h3 class="font-bold text-slate-800 mb-4">Ngày <?= $day ?></h3>
                            <div class="space-y-3">
                                <?php foreach ($services as $service): ?>
                                    <div class="bg-slate-50 rounded p-4 border border-slate-200">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex-1">
                                                <div class="font-semibold text-slate-900 mb-1">
                                                    <?= htmlspecialchars($service['service_name'] ?? $service['service_name_original'] ?? 'Dịch vụ') ?>
                                                </div>
                                                <?php if (!empty($service['service_provider_name'])): ?>
                                                    <div class="text-sm text-slate-600 mb-1">
                                                        <span class="font-medium">Nhà cung cấp:</span> <?= htmlspecialchars($service['service_provider_name']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($service['unit_price'])): ?>
                                                <div class="text-right">
                                                    <div class="font-semibold text-slate-900">
                                                        <?= number_format($service['unit_price']) ?> VNĐ
                                                    </div>
                                                    <?php if (!empty($service['quantity']) && $service['quantity'] > 1): ?>
                                                        <div class="text-xs text-slate-500">
                                                            × <?= $service['quantity'] ?> <?= $service['unit'] ?? '' ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($service['notes'])): ?>
                                            <div class="text-sm text-slate-600 mt-2 pt-2 border-t border-slate-200">
                                                <span class="font-medium">Ghi chú:</span> <?= nl2br(htmlspecialchars($service['notes'])) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (isset($service['is_included_in_price'])): ?>
                                            <div class="mt-2">
                                                <span class="text-xs px-2 py-0.5 rounded <?= $service['is_included_in_price'] ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                                    <?= $service['is_included_in_price'] ? '✓ Đã bao gồm trong giá' : '⚠ Chưa bao gồm trong giá' ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        </div>

        <!-- Guide Notes -->
        <?php if (!empty($schedule['guide_notes'])): ?>
            <div class="bg-panel rounded p-6 border-l-4 border-yellow-500">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Ghi chú từ Điều hành</h2>
                <p class="text-slate-700 leading-relaxed"><?= nl2br(htmlspecialchars($schedule['guide_notes'])) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- PASSENGER LIST -->
    <div id="passengers" class="bg-panel rounded p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold text-slate-800">Danh sách hành khách</h2>
            <button onclick="window.print()" class="text-accent hover:text-accent/80 text-sm font-medium transition-colors">
                <i class="fas fa-print mr-1"></i> In danh sách
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Họ tên</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Năm sinh</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Giới tính</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">SĐT</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">SĐT Khẩn cấp</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Booking</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($passengers)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Chưa có hành khách nào.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($passengers as $index => $p): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-slate-400 text-sm"><?= $index + 1 ?></td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-slate-900"><?= htmlspecialchars($p['full_name']) ?></div>
                                    <?php if ($p['is_primary']): ?>
                                        <span class="inline-block mt-1 text-xs bg-accent/10 text-accent px-2 py-0.5 rounded">
                                            Trưởng đoàn
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-sm">
                                    <?= $p['date_of_birth'] ? date('Y', strtotime($p['date_of_birth'])) : '-' ?>
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-sm">
                                    <?php
                                    $gender_map = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                                    echo $gender_map[$p['gender']] ?? $p['gender'];
                                    ?>
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-sm font-mono">
                                    <?= htmlspecialchars($p['phone']) ?>
                                </td>
                                <td class="px-4 py-3 text-slate-600 text-sm font-mono">
                                    <?php if (!empty($p['emergency_contact'])): ?>
                                        <a href="tel:<?= htmlspecialchars($p['emergency_contact']) ?>" 
                                           class="text-accent hover:text-accent/80 font-medium">
                                            <?= htmlspecialchars($p['emergency_contact']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-slate-500 text-xs font-mono">
                                    <?= htmlspecialchars($p['booking_code']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
