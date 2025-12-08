<?php
/**
 * GUIDE - TOUR DETAIL & PASSENGER MANIFEST
 * Variables: $schedule, $tour, $passengers
 * Flat Design - Không shadow, không gradient, dùng spacing thay border
 */
?>

<div class="max-w-8xl mx-auto">
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

    <!-- SINGLE TAB: THÔNG TIN TOUR -->
    <div class="space-y-4 lg:space-y-8 mb-6 lg:mb-8">
        <!-- Sticky Navigation Menu -->
        <div class="sticky top-0 bg-panel border-b border-primary-100 z-10 mb-6 -mx-4 lg:-mx-8 px-4 lg:px-8 py-3">
            <div class="flex gap-2 overflow-x-auto">
                <?php
                $active_tab = $_GET['tab'] ?? 'tour-info';
                $base_url = "?act=guide-tours&action=show&id=" . $schedule['id'];
                ?>
                <a href="<?= $base_url ?>&tab=tour-info"
                    class="px-3 lg:px-4 py-2 rounded-xl whitespace-nowrap font-semibold transition-colors text-xs lg:text-sm <?= $active_tab === 'tour-info' ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' ?>">
                    <i data-lucide="file-text" class="w-4 h-4 inline mr-1"></i>
                    Thông tin Tour
                </a>
                <?php if ($can_checkin || !empty($checkin_passengers)): ?>
                    <a href="<?= $base_url ?>&tab=checkin"
                        class="px-3 lg:px-4 py-2 rounded-xl whitespace-nowrap font-semibold transition-colors text-xs lg:text-sm <?= $active_tab === 'checkin' ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' ?>">
                        <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i>
                        Check-in
                    </a>
                <?php endif; ?>
                <a href="<?= $base_url ?>&tab=expenses"
                    class="px-3 lg:px-4 py-2 rounded-xl whitespace-nowrap font-semibold transition-colors text-xs lg:text-sm <?= $active_tab === 'expenses' ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' ?>">
                    <i data-lucide="dollar-sign" class="w-4 h-4 inline mr-1"></i>
                    Chi phí phát sinh
                </a>
                <a href="<?= $base_url ?>&tab=journals"
                    class="px-3 lg:px-4 py-2 rounded-xl whitespace-nowrap font-semibold transition-colors text-xs lg:text-sm <?= $active_tab === 'journals' ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' ?>">
                    <i data-lucide="book-open" class="w-4 h-4 inline mr-1"></i>
                    Nhật ký tour
                </a>
                <a href="<?= $base_url ?>&tab=services"
                    class="px-3 lg:px-4 py-2 rounded-xl whitespace-nowrap font-semibold transition-colors text-xs lg:text-sm <?= $active_tab === 'services' ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' ?>">
                    <i data-lucide="briefcase" class="w-4 h-4 inline mr-1"></i>
                    Dịch vụ
                </a>
                <a href="<?= $base_url ?>&tab=passengers"
                    class="px-3 lg:px-4 py-2 rounded-xl whitespace-nowrap font-semibold transition-colors text-xs lg:text-sm <?= $active_tab === 'passengers' ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' ?>">
                    <i data-lucide="users" class="w-4 h-4 inline mr-1"></i>
                    Hành khách
                </a>
                <a href="<?= $base_url ?>&tab=rooms"
                    class="px-3 lg:px-4 py-2 rounded-xl whitespace-nowrap font-semibold transition-colors text-xs lg:text-sm <?= $active_tab === 'rooms' ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' ?>">
                    <i data-lucide="home" class="w-4 h-4 inline mr-1"></i>
                    Phân phòng
                </a>
                <a href="<?= $base_url ?>&tab=vehicles"
                    class="px-3 lg:px-4 py-2 rounded-xl whitespace-nowrap font-semibold transition-colors text-xs lg:text-sm <?= $active_tab === 'vehicles' ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white' : 'bg-primary-100 text-primary-700 hover:bg-primary-200' ?>">
                    <i data-lucide="car" class="w-4 h-4 inline mr-1"></i>
                    Xe & Tài xế
                </a>
            </div>
        </div>

        <!-- Render content based on active tab -->
        <?php if ($active_tab === 'tour-info'): ?>
            <!-- Basic Info - Responsive -->
            <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
                <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-4 lg:mb-6">Thông tin chuyến đi</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 lg:gap-6">
                    <div>
                        <div class="text-xs text-primary-500 uppercase tracking-wide mb-1 font-semibold">Ngày khởi hành
                        </div>
                        <div class="font-semibold text-primary-700 text-sm lg:text-base">
                            <?= date('d/m/Y', strtotime($schedule['start_date'])) ?>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-primary-500 uppercase tracking-wide mb-1 font-semibold">Ngày kết thúc
                        </div>
                        <div class="font-semibold text-primary-700 text-sm lg:text-base">
                            <?= date('d/m/Y', strtotime($schedule['end_date'])) ?>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-primary-500 uppercase tracking-wide mb-1 font-semibold">Thời lượng
                        </div>
                        <div class="font-semibold text-primary-700 text-sm lg:text-base">
                            <?= $tour['duration_days'] ?> ngày <?= $tour['duration_nights'] ?> đêm
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-primary-500 uppercase tracking-wide mb-1 font-semibold">Số lượng khách
                        </div>
                        <div class="font-bold text-accent text-base lg:text-lg"><?= count($passengers) ?></div>
                        <div class="text-xs text-primary-500">/ <?= $schedule['quota'] ?> chỗ</div>
                    </div>
                    <div>
                        <div class="text-xs text-primary-500 uppercase tracking-wide mb-1 font-semibold">Điểm khởi hành
                        </div>
                        <div class="font-semibold text-primary-700 text-sm lg:text-base">
                            <?= htmlspecialchars($tour['departure_location'] ?? '-') ?>
                        </div>
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
                                <span
                                    class="text-primary-700 text-sm lg:text-base"><?= htmlspecialchars(is_array($highlight) ? ($highlight['highlight'] ?? '') : $highlight) ?></span>
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
                                    Ngày <?= $day['day_number'] ?? $day['day'] ?? '' ?>:
                                    <?= htmlspecialchars($day['title'] ?? '') ?>
                                </div>

                                <!-- Destination Info -->
                                <?php if (!empty($day['destination_name'])): ?>
                                    <div class="bg-slate-50 rounded p-3 mb-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-accent text-lg">📍</span>
                                            <span
                                                class="text-slate-800 font-semibold"><?= htmlspecialchars($day['destination_name']) ?></span>
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
                                                <span class="font-medium">Địa chỉ:</span>
                                                <?= htmlspecialchars($day['destination_locations']) ?>
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
                                    <div class="text-slate-600 text-sm leading-relaxed">
                                        <?= $day['description'] ?>
                                    </div>
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
                                        <span
                                            class="text-slate-700"><?= htmlspecialchars(is_array($item) ? ($item['item'] ?? '') : $item) ?></span>
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
                                        <span
                                            class="text-slate-700"><?= htmlspecialchars(is_array($item) ? ($item['item'] ?? '') : $item) ?></span>
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
                                <h3 class="font-semibold text-slate-800 mb-1"><?= htmlspecialchars($policy['name'] ?? '') ?>
                                </h3>
                                <?php if (!empty($policy['description'])): ?>
                                    <p class="text-slate-600 text-sm leading-relaxed">
                                        <?= nl2br(htmlspecialchars($policy['description'])) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php elseif ($active_tab === 'checkin'): ?>
            <!-- Section: Activity Check-in (NEW SYSTEM) -->
            <div class="space-y-6 mb-8">
                <!-- Header với các nút action -->
                <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
                        <div>
                            <h2 class="text-base lg:text-lg font-bold text-primary-700">Check-in theo Hoạt động</h2>
                            <p class="text-xs text-primary-500 mt-1">Quản lý check-in chi tiết theo từng hoạt động trong tour</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="?act=guide-checkpoints&action=create&schedule_id=<?= $schedule['id'] ?>"
                                class="px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-xl font-semibold transition-colors text-sm flex items-center gap-2">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Tạo Checkpoint
                            </a>
                            <a href="?act=guide-activity-checkin&action=index&schedule_id=<?= $schedule['id'] ?>"
                                class="px-4 py-2 bg-primary-100 hover:bg-primary-200 text-primary-700 rounded-xl font-semibold transition-colors text-sm flex items-center gap-2">
                                <i data-lucide="list" class="w-4 h-4"></i>
                                Xem tất cả
                            </a>
                        </div>
                    </div>

                    <!-- Danh sách Checkpoints -->
                    <?php if (!empty($activity_checkpoints)): ?>
                        <div class="space-y-4">
                            <?php 
                            // Group checkpoints by date
                            $checkpoints_by_date = [];
                            foreach ($activity_checkpoints as $cp) {
                                $date = $cp['scheduled_date'];
                                if (!isset($checkpoints_by_date[$date])) {
                                    $checkpoints_by_date[$date] = [];
                                }
                                $checkpoints_by_date[$date][] = $cp;
                            }
                            ksort($checkpoints_by_date);
                            ?>
                            
                            <?php foreach ($checkpoints_by_date as $date => $checkpoints): ?>
                                <div class="border border-primary-100 rounded-xl p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="font-semibold text-primary-700">
                                            <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                                            <?= date('d/m/Y', strtotime($date)) ?>
                                        </h3>
                                        <span class="text-xs text-primary-500">
                                            <?= count($checkpoints) ?> checkpoint<?= count($checkpoints) > 1 ? 's' : '' ?>
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <?php foreach ($checkpoints as $cp): ?>
                                            <?php 
                                            $stats = $activity_checkpoint_stats[$cp['id']] ?? [];
                                            $type_labels = [
                                                'boarding' => 'Lên xe',
                                                'meal' => 'Bữa ăn',
                                                'accommodation' => 'Nghỉ đêm',
                                                'attraction' => 'Tham quan',
                                                'shopping' => 'Mua sắm',
                                                'other' => 'Khác'
                                            ];
                                            $status_labels = [
                                                'active' => 'Đang hoạt động',
                                                'completed' => 'Hoàn thành',
                                                'cancelled' => 'Đã hủy'
                                            ];
                                            $status_colors = [
                                                'active' => 'bg-success-bg text-success-text',
                                                'completed' => 'bg-primary-100 text-primary-700',
                                                'cancelled' => 'bg-danger-bg text-danger-text'
                                            ];
                                            ?>
                                            <div class="bg-primary-50 rounded-lg p-4 border border-primary-100 hover:border-accent transition-colors">
                                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <h4 class="font-semibold text-primary-700">
                                                                <?= htmlspecialchars($cp['checkpoint_name']) ?>
                                                            </h4>
                                                            <span class="text-xs px-2 py-1 rounded <?= $status_colors[$cp['status']] ?? 'bg-primary-100 text-primary-700' ?>">
                                                                <?= $status_labels[$cp['status']] ?? $cp['status'] ?>
                                                            </span>
                                                        </div>
                                                        <div class="flex flex-wrap gap-3 text-xs text-primary-600 mb-2">
                                                            <span>
                                                                <i data-lucide="tag" class="w-3 h-3 inline mr-1"></i>
                                                                <?= $type_labels[$cp['checkpoint_type']] ?? $cp['checkpoint_type'] ?>
                                                            </span>
                                                            <?php if (!empty($cp['scheduled_time'])): ?>
                                                                <span>
                                                                    <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                                                                    <?= date('H:i', strtotime($cp['scheduled_time'])) ?>
                                                                </span>
                                                            <?php endif; ?>
                                                            <?php if (!empty($cp['location'])): ?>
                                                                <span>
                                                                    <i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i>
                                                                    <?= htmlspecialchars($cp['location']) ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if (!empty($cp['description'])): ?>
                                                            <p class="text-xs text-primary-500 line-clamp-2">
                                                                <?= htmlspecialchars($cp['description']) ?>
                                                            </p>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <div class="flex flex-col sm:flex-row gap-2 items-start sm:items-center">
                                                        <!-- Stats -->
                                                        <?php if (!empty($stats)): ?>
                                                            <div class="flex gap-2 text-xs">
                                                                <div class="bg-success-bg text-success-text px-2 py-1 rounded">
                                                                    <strong><?= $stats['checked_in'] ?? 0 ?></strong>/<?= $stats['total_passengers'] ?? 0 ?>
                                                                </div>
                                                                <?php if (($stats['absent'] ?? 0) > 0): ?>
                                                                    <div class="bg-danger-bg text-danger-text px-2 py-1 rounded">
                                                                        Vắng: <?= $stats['absent'] ?? 0 ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <?php if (($stats['late'] ?? 0) > 0): ?>
                                                                    <div class="bg-warning-bg text-warning-text px-2 py-1 rounded">
                                                                        Muộn: <?= $stats['late'] ?? 0 ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                        <!-- Actions -->
                                                        <div class="flex gap-2">
                                                            <a href="?act=guide-activity-checkin&action=show&checkpoint_id=<?= $cp['id'] ?>"
                                                                class="px-3 py-1.5 bg-accent hover:bg-accent-hover text-white rounded-lg font-semibold text-xs transition-colors flex items-center gap-1">
                                                                <i data-lucide="check-circle" class="w-3 h-3"></i>
                                                                Check-in
                                                            </a>
                                                            <a href="?act=guide-activity-checkin&action=summary&checkpoint_id=<?= $cp['id'] ?>"
                                                                class="px-3 py-1.5 bg-primary-100 hover:bg-primary-200 text-primary-700 rounded-lg font-semibold text-xs transition-colors flex items-center gap-1">
                                                                <i data-lucide="bar-chart" class="w-3 h-3"></i>
                                                                Tổng hợp
                                                            </a>
                                                            <a href="?act=guide-checkpoints&action=edit&id=<?= $cp['id'] ?>"
                                                                class="px-3 py-1.5 bg-warning-bg hover:opacity-90 text-warning-text rounded-lg font-semibold text-xs transition-colors flex items-center gap-1">
                                                                <i data-lucide="edit" class="w-3 h-3"></i>
                                                                Sửa
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 bg-primary-50 rounded-lg border border-primary-100">
                            <i data-lucide="map-pin-off" class="w-12 h-12 text-primary-400 mx-auto mb-3"></i>
                            <p class="text-primary-500 text-sm mb-4">Chưa có checkpoint nào được tạo cho tour này.</p>
                            <?php if ($can_checkin): ?>
                                <a href="?act=guide-checkpoints&action=create&schedule_id=<?= $schedule['id'] ?>"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-xl font-semibold transition-colors text-sm">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                    Tạo Checkpoint đầu tiên
                                </a>
                            <?php else: ?>
                                <p class="text-xs text-primary-400">Tour chưa bắt đầu. Chỉ có thể tạo checkpoint từ ngày
                                    <?= date('d/m/Y', strtotime($schedule['start_date'])) ?> trở đi.
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Section: Check-in Cũ (Legacy) -->
                <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
                        <div>
                            <h2 class="text-base lg:text-lg font-bold text-primary-700">Check-in Hành khách (Cũ)</h2>
                            <p class="text-xs text-primary-500 mt-1">Hệ thống check-in cũ - Khuyến nghị sử dụng hệ thống mới ở trên</p>
                        </div>
                        <?php if ($can_checkin): ?>
                            <a href="?act=guide-checkin&action=show&schedule_id=<?= $schedule['id'] ?>"
                                class="px-4 py-2 bg-primary-100 hover:bg-primary-200 text-primary-700 rounded-xl font-semibold transition-colors text-sm flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                Check-in (Cũ)
                            </a>
                        <?php else: ?>
                            <span class="text-xs text-primary-500">Tour chưa bắt đầu</span>
                        <?php endif; ?>
                    </div>

                <?php if (!empty($checkin_stats)): ?>
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div class="text-center p-3 bg-primary-50 rounded-lg">
                            <div class="text-2xl font-bold text-primary-700"><?= $checkin_stats['total'] ?? 0 ?></div>
                            <div class="text-xs text-primary-500">Tổng số</div>
                        </div>
                        <div class="text-center p-3 bg-success-bg rounded-lg">
                            <div class="text-2xl font-bold text-success"><?= $checkin_stats['checked_in'] ?? 0 ?></div>
                            <div class="text-xs text-success-text">Đã check-in</div>
                        </div>
                        <div class="text-center p-3 bg-warning-bg rounded-lg">
                            <div class="text-2xl font-bold text-warning"><?= $checkin_stats['not_checked_in'] ?? 0 ?></div>
                            <div class="text-xs text-warning-text">Chưa check-in</div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($checkin_passengers)): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-primary-100">
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-primary-500 uppercase">Họ tên
                                    </th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-primary-500 uppercase">Booking
                                    </th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-primary-500 uppercase">Trạng
                                        thái</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-primary-500 uppercase">Thời
                                        gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($checkin_passengers, 0, 5) as $p): ?>
                                    <tr class="border-b border-primary-50 hover:bg-primary-50/50">
                                        <td class="px-3 py-2 text-primary-700"><?= htmlspecialchars($p['full_name']) ?></td>
                                        <td class="px-3 py-2 text-primary-500 text-xs">
                                            <?= htmlspecialchars($p['booking_code']) ?>
                                        </td>
                                        <td class="px-3 py-2">
                                            <?php
                                            $status_map = ['present' => 'Có mặt', 'absent' => 'Vắng mặt', 'late' => 'Muộn'];
                                            $status_class = ['present' => 'bg-success-bg text-success-text', 'absent' => 'bg-danger-bg text-danger-text', 'late' => 'bg-warning-bg text-warning-text'];
                                            ?>
                                            <?php if ($p['checkin_status']): ?>
                                                <span
                                                    class="px-2 py-1 rounded text-xs font-semibold <?= $status_class[$p['checkin_status']] ?? 'bg-primary-100 text-primary-700' ?>">
                                                    <?= $status_map[$p['checkin_status']] ?? $p['checkin_status'] ?>
                                                </span>
                                            <?php else: ?>
                                                <span
                                                    class="px-2 py-1 rounded text-xs font-semibold bg-primary-100 text-primary-500">Chưa
                                                    check-in</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-3 py-2 text-primary-500 text-xs">
                                            <?= $p['checkin_time'] ? date('H:i', strtotime($p['checkin_time'])) : '-' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if (count($checkin_passengers) > 5): ?>
                            <div class="mt-4 text-center">
                                <a href="?act=guide-checkin&action=show&schedule_id=<?= $schedule['id'] ?>"
                                    class="text-accent hover:text-accent-hover text-sm font-medium">
                                    Xem tất cả (<?= count($checkin_passengers) ?>) →
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <p class="text-primary-500 text-sm mb-4">Chưa có dữ liệu check-in.</p>
                        <?php if ($can_checkin): ?>
                            <a href="?act=guide-checkin&action=show&schedule_id=<?= $schedule['id'] ?>"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-xl font-semibold transition-colors text-sm">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Bắt đầu Check-in
                            </a>
                        <?php else: ?>
                            <p class="text-xs text-primary-400">Tour chưa bắt đầu. Chỉ có thể check-in từ ngày
                                <?= date('d/m/Y', strtotime($schedule['start_date'])) ?> trở đi.
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($active_tab === 'expenses'): ?>
            <!-- Section: Chi phí phát sinh -->
            <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                    <h2 class="text-base lg:text-lg font-bold text-primary-700">Chi phí phát sinh</h2>
                    <div class="flex gap-2">
                        <?php if ($can_add_expense): ?>
                            <a href="?act=guide-expenses&action=create&schedule_id=<?= $schedule['id'] ?>"
                                class="px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-xl font-semibold transition-colors text-sm flex items-center gap-2">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Thêm chi phí
                            </a>
                        <?php endif; ?>
                        <a href="?act=guide-expenses&action=show&schedule_id=<?= $schedule['id'] ?>"
                            class="px-4 py-2 bg-primary-100 hover:bg-primary-200 text-primary-700 rounded-xl font-semibold transition-colors text-sm">
                            Xem tất cả
                        </a>
                    </div>
                </div>

                <?php if (!empty($expenses)): ?>
                    <div class="mb-4 p-3 bg-primary-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-primary-500">Tổng chi phí (đã duyệt):</span>
                            <span class="text-lg font-bold text-accent"><?= number_format($expense_total, 0, ',', '.') ?>
                                VNĐ</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <?php foreach (array_slice($expenses, 0, 3) as $exp): ?>
                            <div class="flex items-center justify-between p-2 bg-primary-50 rounded-lg">
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-primary-700">
                                        <?= htmlspecialchars($exp['description']) ?>
                                    </div>
                                    <div class="text-xs text-primary-500"><?= date('d/m/Y', strtotime($exp['expense_date'])) ?>
                                    </div>
                                </div>
                                <div class="text-sm font-semibold text-primary-700">
                                    <?= number_format($exp['amount'], 0, ',', '.') ?> VNĐ
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($expenses) > 3): ?>
                        <div class="mt-4 text-center">
                            <a href="?act=guide-expenses&action=show&schedule_id=<?= $schedule['id'] ?>"
                                class="text-accent hover:text-accent-hover text-sm font-medium">
                                Xem thêm (<?= count($expenses) - 3 ?> chi phí khác) →
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <p class="text-primary-500 text-sm mb-4">Chưa có chi phí phát sinh nào.</p>
                        <?php if ($can_add_expense): ?>
                            <a href="?act=guide-expenses&action=create&schedule_id=<?= $schedule['id'] ?>"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-xl font-semibold transition-colors text-sm">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Thêm chi phí phát sinh
                            </a>
                        <?php else: ?>
                            <p class="text-xs text-primary-400">Tour chưa bắt đầu. Chỉ có thể thêm chi phí từ ngày
                                <?= date('d/m/Y', strtotime($schedule['start_date'])) ?> trở đi.
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($active_tab === 'journals'): ?>
            <!-- Section: Nhật ký tour -->
            <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                    <h2 class="text-base lg:text-lg font-bold text-primary-700">Nhật ký Tour</h2>
                    <div class="flex gap-2">
                        <?php if ($can_add_journal): ?>
                            <a href="?act=guide-journals&action=create&schedule_id=<?= $schedule['id'] ?>"
                                class="px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-xl font-semibold transition-colors text-sm flex items-center gap-2">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Viết nhật ký
                            </a>
                        <?php endif; ?>
                        <a href="?act=guide-journals&action=index&schedule_id=<?= $schedule['id'] ?>"
                            class="px-4 py-2 bg-primary-100 hover:bg-primary-200 text-primary-700 rounded-xl font-semibold transition-colors text-sm">
                            Xem tất cả
                        </a>
                    </div>
                </div>

                <?php if (!empty($journals)): ?>
                    <div class="space-y-3">
                        <?php foreach (array_slice($journals, 0, 3) as $journal): ?>
                            <div class="border border-primary-100 rounded-xl p-3 hover:bg-primary-50/50 transition-colors">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-2">
                                    <h3 class="font-semibold text-primary-700 text-sm">
                                        <?= htmlspecialchars($journal['title'] ?? 'Nhật ký ngày ' . date('d/m/Y', strtotime($journal['journal_date']))) ?>
                                    </h3>
                                    <span
                                        class="text-xs text-primary-500"><?= date('d/m/Y', strtotime($journal['journal_date'])) ?></span>
                                </div>
                                <?php if (!empty($journal['content'])): ?>
                                    <p class="text-sm text-primary-700 line-clamp-2 mb-2">
                                        <?= strip_tags(substr($journal['content'], 0, 150)) ?>...
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($journal['images']) && count($journal['images']) > 0): ?>
                                    <div class="flex gap-2 mb-2">
                                        <img src="<?= (defined('BASE_URL') ? BASE_URL : '') ?>/public/<?= htmlspecialchars($journal['images'][0]['image_url']) ?>"
                                            alt="" class="w-16 h-16 object-cover rounded">
                                        <?php if (count($journal['images']) > 1): ?>
                                            <div
                                                class="w-16 h-16 bg-primary-100 rounded flex items-center justify-center text-xs text-primary-500">
                                                +<?= count($journal['images']) - 1 ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <a href="?act=guide-journals&action=show&id=<?= $journal['id'] ?>"
                                    class="text-accent hover:text-accent-hover text-xs font-medium">Xem chi tiết →</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($journals) > 3): ?>
                        <div class="mt-4 text-center">
                            <a href="?act=guide-journals&action=index&schedule_id=<?= $schedule['id'] ?>"
                                class="text-accent hover:text-accent-hover text-sm font-medium">
                                Xem thêm (<?= count($journals) - 3 ?> nhật ký khác) →
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <p class="text-primary-500 text-sm mb-4">Chưa có nhật ký nào.</p>
                        <?php if ($can_add_journal): ?>
                            <a href="?act=guide-journals&action=create&schedule_id=<?= $schedule['id'] ?>"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-xl font-semibold transition-colors text-sm">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Viết nhật ký tour
                            </a>
                        <?php else: ?>
                            <p class="text-xs text-primary-400">Tour chưa bắt đầu. Chỉ có thể viết nhật ký từ ngày
                                <?= date('d/m/Y', strtotime($schedule['start_date'])) ?> trở đi.
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($active_tab === 'services'): ?>
            <!-- Section: Dịch vụ -->
            <div class="space-y-8 mb-8">
                <!-- Dịch vụ đã đặt thực tế (Booking Services) -->
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
                                                                    (<?= date('d/m', strtotime($service['from_date'])) ?> -
                                                                    <?= date('d/m', strtotime($service['to_date'])) ?>)
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php if (!empty($service['booking_code'])): ?>
                                                            <div class="text-xs text-slate-500">
                                                                Booking: <span
                                                                    class="font-mono"><?= htmlspecialchars($service['booking_code']) ?></span>
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
                                                                    <?= $service['quantity'] ?>                         <?= $service['unit'] ?? '' ?>
                                                                    × <?= number_format($service['unit_price'] ?? 0) ?> VNĐ
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <?php if (!empty($service['notes'])): ?>
                                                    <div class="text-sm text-slate-600 mt-2 pt-2 border-t border-slate-200">
                                                        <span class="font-medium">Ghi chú:</span>
                                                        <?= nl2br(htmlspecialchars($service['notes'])) ?>
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
                                                                <span class="font-medium">Nhà cung cấp:</span>
                                                                <?= htmlspecialchars($service['service_provider_name']) ?>
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
                                                                    × <?= $service['quantity'] ?>                         <?= $service['unit'] ?? '' ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if (!empty($service['notes'])): ?>
                                                    <div class="text-sm text-slate-600 mt-2 pt-2 border-t border-slate-200">
                                                        <span class="font-medium">Ghi chú:</span>
                                                        <?= nl2br(htmlspecialchars($service['notes'])) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (isset($service['is_included_in_price'])): ?>
                                                    <div class="mt-2">
                                                        <span
                                                            class="text-xs px-2 py-0.5 rounded <?= $service['is_included_in_price'] ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
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
                <?php else: ?>
                    <div class="bg-panel rounded p-6">
                        <div class="text-center py-8">
                            <p class="text-primary-500 text-sm">Chưa có dịch vụ nào được đặt cho chuyến đi này.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($active_tab === 'passengers'): ?>
            <!-- Section: Hành khách -->
            <div>
                <div class="bg-panel rounded p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-bold text-slate-800">Danh sách hành khách</h2>
                        <button onclick="window.print()"
                            class="text-accent hover:text-accent/80 text-sm font-medium transition-colors">
                            <i class="fas fa-print mr-1"></i> In danh sách
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        #</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        Họ tên</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        Năm sinh</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        Giới tính</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        SĐT</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        SĐT Khẩn cấp</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        Booking</th>
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
                                                <div class="font-medium text-slate-900"><?= htmlspecialchars($p['full_name']) ?>
                                                </div>
                                                <?php if ($p['is_primary']): ?>
                                                    <span
                                                        class="inline-block mt-1 text-xs bg-accent/10 text-accent px-2 py-0.5 rounded">
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

        <?php elseif ($active_tab === 'rooms'): ?>
            <!-- Section: Phân phòng -->
            <?php if (!empty($room_assignments)): ?>
                <div class="space-y-4 lg:space-y-8 mb-6 lg:mb-8">
                    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
                        <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-4 lg:mb-6">Phân phòng</h2>
                        <div class="space-y-4">
                            <?php
                            $rooms_by_day = [];
                            foreach ($room_assignments as $ra) {
                                $day = $ra['day_number'] ?? 1;
                                if (!isset($rooms_by_day[$day])) {
                                    $rooms_by_day[$day] = [];
                                }
                                $rooms_by_day[$day][] = $ra;
                            }
                            ksort($rooms_by_day);
                            ?>
                            <?php foreach ($rooms_by_day as $day => $rooms): ?>
                                <div class="border border-primary-100 rounded-xl p-4">
                                    <h3 class="font-semibold text-primary-700 mb-3">Đêm <?= $day ?></h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <?php foreach ($rooms as $room): ?>
                                            <div class="bg-primary-50 rounded-lg p-3">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="font-semibold text-primary-700">Phòng
                                                        <?= htmlspecialchars($room['room_number'] ?? '-') ?></span>
                                                    <span
                                                        class="text-xs text-primary-500"><?= htmlspecialchars($room['room_type']) ?></span>
                                                </div>
                                                <div class="text-xs text-primary-500 mb-1">
                                                    <?= htmlspecialchars($room['hotel_name'] ?? 'Khách sạn') ?>
                                                </div>
                                                <div class="text-sm text-primary-700">
                                                    <strong><?= $room['actual_occupancy'] ?>/<?= $room['max_capacity'] ?></strong>
                                                    người
                                                </div>
                                                <?php if (!empty($room['customers'])): ?>
                                                    <div class="mt-2 text-xs text-primary-600">
                                                        <?= htmlspecialchars($room['customers']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
                    <div class="text-center py-8">
                        <p class="text-primary-500 text-sm">Chưa có thông tin phân phòng cho tour này.</p>
                    </div>
                </div>
            <?php endif; ?>

        <?php elseif ($active_tab === 'vehicles'): ?>
            <!-- Section: Xe & Tài xế -->
            <?php if (!empty($vehicle_assignments)): ?>
                <div class="space-y-4 lg:space-y-8 mb-6 lg:mb-8">
                    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
                        <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-4 lg:mb-6">Xe và Tài xế</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($vehicle_assignments as $va): ?>
                                <div class="border border-primary-100 rounded-xl p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="font-semibold text-primary-700"><?= htmlspecialchars($va['vehicle_code']) ?>
                                        </h3>
                                        <span
                                            class="text-xs px-2 py-1 bg-primary-100 text-primary-700 rounded"><?= htmlspecialchars($va['vehicle_type']) ?></span>
                                    </div>
                                    <div class="space-y-2 text-sm">
                                        <div>
                                            <span class="text-primary-500">Biển số:</span>
                                            <span
                                                class="font-semibold text-primary-700"><?= htmlspecialchars($va['license_plate']) ?></span>
                                        </div>
                                        <div>
                                            <span class="text-primary-500">Sức chứa:</span>
                                            <span class="font-semibold text-primary-700"><?= $va['capacity'] ?> chỗ</span>
                                        </div>
                                        <div>
                                            <span class="text-primary-500">Tài xế:</span>
                                            <span
                                                class="font-semibold text-primary-700"><?= htmlspecialchars($va['driver_name']) ?></span>
                                        </div>
                                        <?php if (!empty($va['driver_phone'])): ?>
                                            <div>
                                                <span class="text-primary-500">SĐT:</span>
                                                <a href="tel:<?= htmlspecialchars($va['driver_phone']) ?>"
                                                    class="text-accent hover:text-accent-hover font-medium">
                                                    <?= htmlspecialchars($va['driver_phone']) ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($va['driver_salary'])): ?>
                                            <div>
                                                <span class="text-primary-500">Phụ cấp:</span>
                                                <span
                                                    class="font-semibold text-primary-700"><?= number_format($va['driver_salary'], 0, ',', '.') ?>
                                                    VNĐ</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
                    <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-4 lg:mb-6">Xe và Tài xế</h2>
                    <div class="text-center py-8">
                        <p class="text-primary-500 text-sm">Chưa có thông tin xe và tài xế cho tour này.</p>
                    </div>
                </div>
            <?php endif; ?>

        <?php endif; ?>