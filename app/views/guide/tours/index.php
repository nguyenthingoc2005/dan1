<?php
/**
 * GUIDE - MY TOURS LIST
 */
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Lịch Tour Của Tôi</h1>
        
        <!-- Filter Buttons -->
        <div class="flex gap-2">
            <a href="?act=guide-tours&filter=all" 
               class="px-4 py-2 rounded border <?= (!isset($_GET['filter']) || $_GET['filter'] === 'all') ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">
                Tất cả
            </a>
            <a href="?act=guide-tours&filter=upcoming" 
               class="px-4 py-2 rounded border <?= (isset($_GET['filter']) && $_GET['filter'] === 'upcoming') ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">
                Sắp tới
            </a>
            <a href="?act=guide-tours&filter=history" 
               class="px-4 py-2 rounded border <?= (isset($_GET['filter']) && $_GET['filter'] === 'history') ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">
                Đã qua
            </a>
        </div>
    </div>

    <div class="bg-panel rounded overflow-hidden border border-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">Mã Tour</th>
                        <th class="px-6 py-4 font-medium">Tên Tour</th>
                        <th class="px-6 py-4 font-medium">Khởi hành</th>
                        <th class="px-6 py-4 font-medium">Kết thúc</th>
                        <th class="px-6 py-4 font-medium">Khách</th>
                        <th class="px-6 py-4 font-medium text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($schedules)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">
                                Không tìm thấy lịch tour nào.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($schedules as $s): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-mono text-blue-600 font-medium">
                                    <?= htmlspecialchars($s['tour_code']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900"><?= htmlspecialchars($s['tour_name']) ?></div>
                                    <?php if (!empty($s['guide_notes'])): ?>
                                        <div class="text-xs text-yellow-600 mt-1">
                                            <i class="fas fa-sticky-note mr-1"></i> <?= htmlspecialchars($s['guide_notes']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <?= date('d/m/Y', strtotime($s['start_date'])) ?>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <?= date('d/m/Y', strtotime($s['end_date'])) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <?= $s['booked'] ?> / <?= $s['quota'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="?act=guide-tours&action=show&id=<?= $s['id'] ?>"
                                        class="inline-block px-4 py-2 bg-accent text-white rounded hover:bg-blue-700 font-medium transition-colors">
                                        Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-center">
                <div class="flex gap-2">
                    <?php 
                    $filter_param = isset($_GET['filter']) ? '&filter=' . htmlspecialchars($_GET['filter']) : '';
                    for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?act=guide-tours&page=<?= $i ?><?= $filter_param ?>"
                            class="px-3 py-1 rounded border <?= $i == $current_page ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>