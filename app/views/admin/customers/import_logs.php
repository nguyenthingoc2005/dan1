<?php
/**
 * ADMIN - LỊCH SỬ IMPORT KHÁCH HÀNG
 * Variables: $logs, $total, $total_pages, $current_page
 */
?>
<div class="max-w-7xl mx-auto">
    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Lịch sử Import</h1>
        <div class="flex gap-3">
            <a href="?act=admin&module=customers&action=import"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                <i class="fas fa-upload mr-2"></i> Import mới
            </a>
            <a href="?act=admin&module=customers"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người import</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thành công</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Lỗi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 block"></i>
                            Chưa có lịch sử import nào
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php $stt = ($current_page - 1) * 20 + 1; ?>
                    <?php foreach ($logs as $log): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= $stt++ ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="flex items-center">
                                <i class="fas fa-file-excel text-green-600 mr-2"></i>
                                <?= htmlspecialchars($log['file_name']) ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= htmlspecialchars($log['importer_name'] ?? 'N/A') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 font-medium">
                            <?= $log['total_rows'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-green-600 font-medium">
                            <?= $log['success_count'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-red-600 font-medium">
                            <?= $log['error_count'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <a href="?act=admin&module=customers&action=importResult&log_id=<?= $log['id'] ?>"
                                class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye mr-1"></i> Xem chi tiết
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
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Trang <?= $current_page ?> / <?= $total_pages ?>
            </div>
            <div class="flex gap-2">
                <?php if ($current_page > 1): ?>
                <a href="?act=admin&module=customers&action=importLogs&page=<?= $current_page - 1 ?>"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>
                <?php if ($current_page < $total_pages): ?>
                <a href="?act=admin&module=customers&action=importLogs&page=<?= $current_page + 1 ?>"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

