<?php
/**
 * STAFF - LỊCH SỬ IMPORT KHÁCH HÀNG
 * Variables: $logs, $total, $total_pages, $current_page
 */
?>
<div class="max-w-8xl mx-auto p-4 lg:p-8">
    <!-- HEADER - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Lịch sử Import</h1>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <a href="?act=staff-customers&action=import"
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="upload" class="w-4 h-4"></i>
                Import mới
            </a>
            <a href="?act=staff-customers"
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-panel rounded-2xl shadow-sm overflow-hidden border border-primary-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-primary-100">
                <thead class="bg-primary-50">
                    <tr>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-600 uppercase tracking-wider">STT</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-600 uppercase tracking-wider">File</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-600 uppercase tracking-wider">Người import</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-center text-xs font-semibold text-primary-600 uppercase tracking-wider">Tổng</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-center text-xs font-semibold text-primary-600 uppercase tracking-wider">Thành công</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-center text-xs font-semibold text-primary-600 uppercase tracking-wider">Lỗi</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-600 uppercase tracking-wider">Thời gian</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-center text-xs font-semibold text-primary-600 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-panel divide-y divide-primary-100">
                    <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="8" class="px-3 lg:px-4 py-8 lg:py-12 text-center text-primary-500">
                            <div class="flex justify-center mb-3 lg:mb-4">
                                <i data-lucide="inbox" class="w-12 h-12 lg:w-16 lg:h-16 text-primary-300"></i>
                            </div>
                            Chưa có lịch sử import nào
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php $stt = ($current_page - 1) * 20 + 1; ?>
                    <?php foreach ($logs as $log): ?>
                    <tr class="hover:bg-primary-50 transition-colors">
                        <td class="px-3 lg:px-4 py-2 lg:py-3 whitespace-nowrap text-xs lg:text-sm text-primary-600">
                            <?= $stt++ ?>
                        </td>
                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-primary-700">
                            <div class="flex items-center gap-2">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4 lg:w-5 lg:h-5 text-success-text"></i>
                                <?= htmlspecialchars($log['file_name']) ?>
                            </div>
                        </td>
                        <td class="px-3 lg:px-4 py-2 lg:py-3 whitespace-nowrap text-xs lg:text-sm text-primary-600">
                            <?= htmlspecialchars($log['importer_name'] ?? 'N/A') ?>
                        </td>
                        <td class="px-3 lg:px-4 py-2 lg:py-3 whitespace-nowrap text-xs lg:text-sm text-center text-primary-700 font-semibold">
                            <?= $log['total_rows'] ?>
                        </td>
                        <td class="px-3 lg:px-4 py-2 lg:py-3 whitespace-nowrap text-xs lg:text-sm text-center text-success-text font-semibold">
                            <?= $log['success_count'] ?>
                        </td>
                        <td class="px-3 lg:px-4 py-2 lg:py-3 whitespace-nowrap text-xs lg:text-sm text-center text-danger-text font-semibold">
                            <?= $log['error_count'] ?>
                        </td>
                        <td class="px-3 lg:px-4 py-2 lg:py-3 whitespace-nowrap text-xs lg:text-sm text-primary-500">
                            <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                        </td>
                        <td class="px-3 lg:px-4 py-2 lg:py-3 whitespace-nowrap text-center text-xs lg:text-sm">
                            <a href="?act=staff-customers&action=importResult&log_id=<?= $log['id'] ?>"
                                class="text-accent hover:text-accent-dark font-semibold flex items-center justify-center gap-1">
                                <i data-lucide="eye" class="w-3 h-3 lg:w-4 lg:h-4"></i>
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
        <div class="px-4 lg:px-6 py-3 lg:py-4 border-t border-primary-100 bg-primary-50 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs lg:text-sm text-primary-600">
                Trang <?= $current_page ?> / <?= $total_pages ?>
            </div>
            <div class="flex gap-2">
                <?php if ($current_page > 1): ?>
                <a href="?act=staff-customers&action=importLogs&page=<?= $current_page - 1 ?>"
                    class="px-3 lg:px-4 py-1.5 lg:py-2 bg-primary-100 text-primary-700 rounded-xl hover:bg-primary-200 font-semibold transition-colors text-xs lg:text-sm flex items-center gap-1">
                    <i data-lucide="chevron-left" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                    Trước
                </a>
                <?php endif; ?>
                <?php if ($current_page < $total_pages): ?>
                <a href="?act=staff-customers&action=importLogs&page=<?= $current_page + 1 ?>"
                    class="px-3 lg:px-4 py-1.5 lg:py-2 bg-primary-100 text-primary-700 rounded-xl hover:bg-primary-200 font-semibold transition-colors text-xs lg:text-sm flex items-center gap-1">
                    Sau
                    <i data-lucide="chevron-right" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

