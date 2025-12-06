<?php
/**
 * STAFF - KẾT QUẢ IMPORT KHÁCH HÀNG
 * Variables: $log, $errors
 */
?>
<div class="max-w-6xl mx-auto">
    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Kết quả Import</h1>
        <div class="flex gap-3">
            <a href="?act=staff-customers&action=import"
                class="px-4 py-2 bg-accent text-white rounded hover:bg-blue-700 transition-colors">
                <i class="fas fa-upload mr-2"></i> Import tiếp
            </a>
            <a href="?act=staff-customers"
                class="px-4 py-2 bg-slate-200 text-slate-700 rounded hover:bg-slate-300 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- SUMMARY CARD -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">Thông tin Import</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-3xl font-bold text-blue-600"><?= $log['total_rows'] ?></div>
                    <div class="text-sm text-slate-600 mt-1">Tổng số dòng</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-3xl font-bold text-green-600"><?= $log['success_count'] ?></div>
                    <div class="text-sm text-slate-600 mt-1">Thành công</div>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <div class="text-3xl font-bold text-red-600"><?= $log['error_count'] ?></div>
                    <div class="text-sm text-slate-600 mt-1">Lỗi</div>
                </div>
                <div class="text-center p-4 bg-slate-50 rounded-lg">
                    <div class="text-3xl font-bold text-slate-600">
                        <?= $log['total_rows'] - $log['success_count'] - $log['error_count'] ?>
                    </div>
                    <div class="text-sm text-slate-600 mt-1">Bỏ qua (trùng)</div>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-slate-600">File:</span>
                        <span class="font-medium ml-2"><?= htmlspecialchars($log['file_name']) ?></span>
                    </div>
                    <div>
                        <span class="text-slate-600">Người import:</span>
                        <span class="font-medium ml-2"><?= htmlspecialchars($log['importer_name'] ?? 'N/A') ?></span>
                    </div>
                    <div>
                        <span class="text-slate-600">Thời gian:</span>
                        <span class="font-medium ml-2"><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ERROR DETAILS -->
    <?php if (!empty($errors)): ?>
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">Chi tiết lỗi</h2>
            <p class="text-sm text-slate-500 mt-1">Các dòng có lỗi trong quá trình import</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Dòng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tên khách hàng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Lỗi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php foreach ($errors as $error): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                            <?= htmlspecialchars($error['row'] ?? 'N/A') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            <?= htmlspecialchars($error['name'] ?? 'N/A') ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-red-600">
                            <?= htmlspecialchars($error['error'] ?? 'N/A') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
        <i class="fas fa-check-circle text-green-600 text-4xl mb-3"></i>
        <p class="text-green-800 font-medium">Không có lỗi nào trong quá trình import!</p>
    </div>
    <?php endif; ?>
</div>

