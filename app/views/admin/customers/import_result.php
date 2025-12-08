<?php
/**
 * ADMIN - KẾT QUẢ IMPORT KHÁCH HÀNG
 * Variables: $log, $errors
 */
?>
<div class="max-w-6xl mx-auto p-4 lg:p-8">
    <!-- HEADER - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Kết quả Import</h1>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <a href="?act=admin&module=customers&action=import"
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="upload" class="w-4 h-4"></i>
                Import tiếp
            </a>
            <a href="?act=admin&module=customers"
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
    </div>

    <!-- SUMMARY CARD -->
    <div class="bg-panel rounded-2xl shadow-sm overflow-hidden mb-4 lg:mb-6 border border-primary-100">
        <div class="p-4 lg:p-6 border-b border-primary-100 bg-primary-50">
            <h2 class="text-base lg:text-lg font-bold text-primary-700">Thông tin Import</h2>
        </div>
        <div class="p-4 lg:p-6">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
                <div class="text-center p-3 lg:p-4 bg-info-bg border border-info rounded-2xl">
                    <div class="text-2xl lg:text-3xl font-bold text-info-dark"><?= $log['total_rows'] ?></div>
                    <div class="text-xs lg:text-sm text-info-text mt-1">Tổng số dòng</div>
                </div>
                <div class="text-center p-3 lg:p-4 bg-success-bg border border-success rounded-2xl">
                    <div class="text-2xl lg:text-3xl font-bold text-success-dark"><?= $log['success_count'] ?></div>
                    <div class="text-xs lg:text-sm text-success-text mt-1">Thành công</div>
                </div>
                <div class="text-center p-3 lg:p-4 bg-danger-bg border border-danger rounded-2xl">
                    <div class="text-2xl lg:text-3xl font-bold text-danger-dark"><?= $log['error_count'] ?></div>
                    <div class="text-xs lg:text-sm text-danger-text mt-1">Lỗi</div>
                </div>
                <div class="text-center p-3 lg:p-4 bg-primary-50 border border-primary-100 rounded-2xl">
                    <div class="text-2xl lg:text-3xl font-bold text-primary-700">
                        <?= $log['total_rows'] - $log['success_count'] - $log['error_count'] ?>
                    </div>
                    <div class="text-xs lg:text-sm text-primary-600 mt-1">Bỏ qua (trùng)</div>
                </div>
            </div>

            <div class="mt-4 lg:mt-6 pt-4 lg:pt-6 border-t border-primary-100">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 lg:gap-4 text-xs lg:text-sm">
                    <div>
                        <span class="text-primary-500">File:</span>
                        <span class="font-semibold text-primary-700 ml-2"><?= htmlspecialchars($log['file_name']) ?></span>
                    </div>
                    <div>
                        <span class="text-primary-500">Người import:</span>
                        <span class="font-semibold text-primary-700 ml-2"><?= htmlspecialchars($log['importer_name'] ?? 'N/A') ?></span>
                    </div>
                    <div>
                        <span class="text-primary-500">Thời gian:</span>
                        <span class="font-semibold text-primary-700 ml-2"><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ERROR DETAILS -->
    <?php if (!empty($errors)): ?>
    <div class="bg-panel rounded-2xl shadow-sm overflow-hidden border border-primary-100">
        <div class="p-4 lg:p-6 border-b border-primary-100 bg-primary-50">
            <h2 class="text-base lg:text-lg font-bold text-primary-700">Chi tiết lỗi</h2>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Các dòng có lỗi trong quá trình import</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-primary-100">
                <thead class="bg-primary-50">
                    <tr>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-600 uppercase tracking-wider">Dòng</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-600 uppercase tracking-wider">Tên khách hàng</th>
                        <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-semibold text-primary-600 uppercase tracking-wider">Lỗi</th>
                    </tr>
                </thead>
                <tbody class="bg-panel divide-y divide-primary-100">
                    <?php foreach ($errors as $error): ?>
                    <tr class="hover:bg-primary-50 transition-colors">
                        <td class="px-3 lg:px-4 py-2 lg:py-3 whitespace-nowrap text-xs lg:text-sm font-semibold text-primary-700">
                            <?= htmlspecialchars($error['row'] ?? 'N/A') ?>
                        </td>
                        <td class="px-3 lg:px-4 py-2 lg:py-3 whitespace-nowrap text-xs lg:text-sm text-primary-600">
                            <?= htmlspecialchars($error['name'] ?? 'N/A') ?>
                        </td>
                        <td class="px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-danger-text">
                            <?= htmlspecialchars($error['error'] ?? 'N/A') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-success-bg border border-success rounded-2xl p-6 lg:p-8 text-center">
        <div class="flex justify-center mb-3 lg:mb-4">
            <i data-lucide="check-circle" class="w-12 h-12 lg:w-16 lg:h-16 text-success-text"></i>
        </div>
        <p class="text-success-dark font-semibold text-sm lg:text-base">Không có lỗi nào trong quá trình import!</p>
    </div>
    <?php endif; ?>
</div>

