<?php
/**
 * ==============================================================================
 * GUIDE DASHBOARD VIEW
 * ==============================================================================
 */
?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- Assigned Tours -->
    <div class="bg-panel rounded-lg p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-slate-600 font-medium">Tours Được Giao</h3>
            <span class="text-2xl">✈️</span>
        </div>
        <div class="text-3xl font-bold text-accent">
            <?php echo number_format($stats['assigned_tours'] ?? 0); ?>
        </div>
        <p class="text-sm text-slate-500 mt-2">Đang chờ xử lý</p>
    </div>

    <!-- Completed Tours -->
    <div class="bg-panel rounded-lg p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-slate-600 font-medium">Tours Hoàn Thành</h3>
            <span class="text-2xl">✅</span>
        </div>
        <div class="text-3xl font-bold text-green-600">
            <?php echo number_format($stats['completed_tours'] ?? 0); ?>
        </div>
        <p class="text-sm text-slate-500 mt-2">Đã hoàn tất</p>
    </div>
</div>

<!-- Upcoming Tours -->
<div class="bg-panel rounded-lg border border-slate-200 mb-8">
    <div class="px-6 py-4 border-b border-slate-200">
        <h2 class="text-lg font-bold text-primary">Tours Sắp Tới</h2>
    </div>

    <div class="space-y-4 p-6">
        <?php if (empty($my_assignments)): ?>
            <div class="text-center py-8 text-slate-500">
                <div class="text-4xl mb-3">📭</div>
                <p>Bạn chưa được giao tour nào</p>
            </div>
        <?php else: ?>
            <?php foreach (($my_assignments ?? []) as $assignment): ?>
                <div class="border border-slate-200 rounded-lg p-4 hover:border-accent transition-colors">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-bold text-slate-900">
                                <?php echo sanitize($assignment['tour_name']); ?>
                            </h3>
                            <p class="text-sm text-slate-500">
                                Mã: <span class="font-mono"><?php echo sanitize($assignment['booking_code']); ?></span>
                            </p>
                        </div>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                            Sắp tới
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-3 text-sm">
                        <div>
                            <div class="text-slate-500">Ngày khởi hành</div>
                            <div class="font-bold text-slate-900">
                                <?php echo format_date($assignment['start_date'], 'd/m/Y'); ?>
                            </div>
                        </div>
                        <div>
                            <div class="text-slate-500">Số khách</div>
                            <div class="font-bold text-slate-900">
                                <?php echo ($assignment['adult_count'] + $assignment['child_count']); ?> người
                            </div>
                        </div>
                        <div>
                            <div class="text-slate-500">Thành phố</div>
                            <div class="font-bold text-slate-900">TP.HCM</div>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-slate-200">
                        <button
                            class="flex-1 px-4 py-2 bg-accent text-white rounded-lg text-sm hover:bg-blue-600 transition-colors">
                            Chi tiết
                        </button>
                        <button
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition-colors">
                            Check-in
                        </button>
                        <button
                            class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg text-sm hover:bg-purple-700 transition-colors">
                            Nhật ký
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Tips -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
        <div class="flex items-start gap-3">
            <span class="text-2xl">💡</span>
            <div>
                <h3 class="font-bold text-slate-900 mb-2">Nhập Nhật Ký Tour</h3>
                <p class="text-sm text-slate-600 mb-3">
                    Ghi lại những điểm đặc biệt, vấn đề, ảnh trong suốt tour
                </p>
                <a href="<?php echo BASE_URL; ?>/guide/journal" class="text-sm text-accent font-medium hover:underline">
                    → Viết nhật ký ngay
                </a>
            </div>
        </div>
    </div>

    <div class="bg-green-50 rounded-lg p-6 border border-green-200">
        <div class="flex items-start gap-3">
            <span class="text-2xl">✅</span>
            <div>
                <h3 class="font-bold text-slate-900 mb-2">Check-in Khách Hàng</h3>
                <p class="text-sm text-slate-600 mb-3">
                    Kiểm tra danh sách khách khi khởi hành
                </p>
                <a href="<?php echo BASE_URL; ?>/guide/checkin"
                    class="text-sm text-green-600 font-medium hover:underline">
                    → Check-in ngay
                </a>
            </div>
        </div>
    </div>
</div>