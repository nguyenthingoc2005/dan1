<?php
/**
 * GUIDE DASHBOARD
 */
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stats Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Tour Sắp Tới</p>
                <h3 class="text-2xl font-bold text-gray-800"><?= $upcoming_tours_count ?></h3>
            </div>
            <div class="p-3 bg-blue-50 rounded-full text-blue-600">
                <i class="fas fa-calendar-alt text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Tours List -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h2 class="text-lg font-bold text-gray-800">Lịch Tour Được Phân Công</h2>
        <a href="?act=guide-tours" class="text-sm text-blue-600 hover:underline">Xem tất cả</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium">Mã Tour</th>
                    <th class="px-6 py-4 font-medium">Tên Tour</th>
                    <th class="px-6 py-4 font-medium">Khởi hành</th>
                    <th class="px-6 py-4 font-medium">Thời lượng</th>
                    <th class="px-6 py-4 font-medium text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($my_schedules)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">
                            Bạn chưa có lịch tour nào sắp tới.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($my_schedules as $s): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-mono text-blue-600 font-medium">
                                <?= htmlspecialchars($s['tour_code']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900"><?= htmlspecialchars($s['tour_name']) ?></div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?= date('d/m/Y', strtotime($s['start_date'])) ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?= $s['duration_days'] ?>N<?= $s['duration_nights'] ?>Đ
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="?act=guide-tours&action=show&id=<?= $s['id'] ?>"
                                    class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold hover:bg-blue-200 transition-colors">
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>