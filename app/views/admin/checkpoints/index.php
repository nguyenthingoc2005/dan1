<?php
/**
 * ADMIN - DANH SÁCH CHECKPOINTS (CHỈ XEM)
 * Variables: $allCheckpoints
 */
?>

<div class="max-w-7xl mx-auto p-4 lg:p-8">
    <div class="mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Quản lý Checkpoints</h1>
        <p class="text-xs lg:text-sm text-primary-500 mt-1">Xem tất cả checkpoints của các tour (chỉ xem)</p>
    </div>

    <?php if (empty($allCheckpoints)): ?>
        <div class="bg-panel rounded-2xl p-8 lg:p-12 text-center border border-primary-100">
            <i data-lucide="map-pin" class="w-12 h-12 lg:w-16 lg:h-16 text-primary-300 mx-auto mb-4"></i>
            <h3 class="text-lg lg:text-xl font-semibold text-primary-700 mb-2">Chưa có checkpoint nào</h3>
        </div>
    <?php else: ?>
        <div class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead>
                        <tr class="bg-primary-50 text-primary-600 text-xs uppercase tracking-wider">
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Checkpoint</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Tour</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">Ngày</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold">HDV</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold text-center">Thống kê</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 font-semibold text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary-100">
                        <?php foreach ($allCheckpoints as $cp): ?>
                            <tr class="hover:bg-primary-50 transition-colors">
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <div class="font-semibold text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($cp['checkpoint_name']) ?></div>
                                    <div class="text-xs text-primary-500 mt-1">
                                        <span class="bg-primary-100 text-primary-700 px-2 py-0.5 rounded-full">
                                            <?= ucfirst($cp['checkpoint_type']) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3">
                                    <div class="text-sm text-primary-700"><?= htmlspecialchars($cp['schedule']['tour_code'] ?? 'N/A') ?></div>
                                    <div class="text-xs text-primary-500">
                                        <?= date('d/m/Y', strtotime($cp['schedule']['start_date'] ?? '')) ?>
                                    </div>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm">
                                    <div><?= date('d/m/Y', strtotime($cp['scheduled_date'])) ?></div>
                                    <?php if ($cp['scheduled_time']): ?>
                                        <div class="text-xs text-primary-500"><?= date('H:i', strtotime($cp['scheduled_time'])) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-sm text-primary-600">
                                    <?= htmlspecialchars($cp['created_by_name'] ?? 'N/A') ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                    <?php if ($cp['stats']): ?>
                                        <div class="text-xs">
                                            <div class="text-success-600 font-semibold"><?= $cp['stats']['present'] ?? 0 ?> có mặt</div>
                                            <div class="text-danger-600"><?= $cp['stats']['absent'] ?? 0 ?> vắng</div>
                                            <div class="text-primary-500">Tổng: <?= $cp['stats']['total'] ?? 0 ?></div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-primary-400 text-xs">Chưa có</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-center">
                                    <a href="?act=admin&module=checkpoints&action=show&id=<?= $cp['id'] ?>" 
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 text-xs font-semibold transition-colors">
                                        <i data-lucide="eye" class="w-3 h-3"></i>
                                        Xem
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

