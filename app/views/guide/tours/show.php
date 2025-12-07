<?php
/**
 * GUIDE - TOUR DETAIL & PASSENGER MANIFEST
 * Variables: $schedule, $tour, $passengers
 * Flat Design - Không shadow, không gradient, dùng spacing thay border
 */
?>

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Chi tiết Tour</h1>
            <p class="text-slate-500 text-sm mt-1">
                <?= htmlspecialchars($tour['tour_code']) ?> - <?= htmlspecialchars($tour['name']) ?>
            </p>
        </div>
        <a href="?act=guide-tours"
            class="px-4 py-2 bg-panel border border-slate-300 text-slate-700 rounded hover:bg-slate-50 transition-colors">
            ← Quay lại
        </a>
    </div>

    <!-- TOUR DETAILS SECTION -->
    <div class="space-y-8 mb-8">
        <!-- Basic Info - Flat Design: Dùng gap thay border -->
        <div class="bg-panel rounded p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-6">Thông tin chuyến đi</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                <div>
                    <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Ngày khởi hành</div>
                    <div class="font-semibold text-slate-900"><?= date('d/m/Y', strtotime($schedule['start_date'])) ?></div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Ngày kết thúc</div>
                    <div class="font-semibold text-slate-900"><?= date('d/m/Y', strtotime($schedule['end_date'])) ?></div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Thời lượng</div>
                    <div class="font-semibold text-slate-900">
                        <?= $tour['duration_days'] ?> ngày <?= $tour['duration_nights'] ?> đêm
                    </div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Số lượng khách</div>
                    <div class="font-bold text-accent text-lg"><?= count($passengers) ?></div>
                    <div class="text-xs text-slate-400">/ <?= $schedule['quota'] ?> chỗ</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Điểm khởi hành</div>
                    <div class="font-semibold text-slate-900"><?= htmlspecialchars($tour['departure_location'] ?? '-') ?></div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Loại tour</div>
                    <div class="font-semibold text-slate-900">
                        <?= $tour['tour_type'] === 'public' ? 'Tour công khai' : 'Tour riêng' ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Introduction & Description -->
        <?php if (!empty($tour['introduction']) || !empty($tour['description'])): ?>
            <div class="bg-panel rounded p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Giới thiệu Tour</h2>
                <div class="space-y-4 text-slate-700 leading-relaxed">
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
            <div class="bg-panel rounded p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Điểm nổi bật</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <?php foreach ($tour['highlights'] as $highlight): ?>
                        <div class="flex items-start gap-2">
                            <span class="text-accent mt-0.5">•</span>
                            <span class="text-slate-700"><?= htmlspecialchars(is_array($highlight) ? ($highlight['highlight'] ?? '') : $highlight) ?></span>
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
                        <div class="pl-4 border-l-2 border-slate-200">
                            <div class="font-bold text-slate-800 mb-2">
                                Ngày <?= $day['day_number'] ?? $day['day'] ?? '' ?>: <?= htmlspecialchars($day['title'] ?? '') ?>
                            </div>
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

        <!-- Guide Notes -->
        <?php if (!empty($schedule['guide_notes'])): ?>
            <div class="bg-panel rounded p-6 border-l-4 border-yellow-500">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Ghi chú từ Điều hành</h2>
                <p class="text-slate-700 leading-relaxed"><?= nl2br(htmlspecialchars($schedule['guide_notes'])) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- PASSENGER LIST -->
    <div class="bg-panel rounded p-6">
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
                        <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">Booking</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($passengers)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
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
