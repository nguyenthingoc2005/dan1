<?php
/**
 * ADMIN - IMPORT KHÁCH HÀNG TỪ EXCEL
 */
?>
<div class="max-w-4xl mx-auto p-4 lg:p-8">
    <!-- HEADER - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Import Khách hàng từ Excel</h1>
        <a href="?act=admin&module=customers"
            class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    <!-- FORM CARD -->
    <div class="bg-panel rounded-2xl shadow-sm overflow-hidden border border-primary-100">
        <div class="p-4 lg:p-6 border-b border-primary-100 bg-primary-50">
            <h2 class="text-base lg:text-lg font-bold text-primary-700">Upload file Excel/CSV</h2>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Chọn file Excel hoặc CSV chứa danh sách khách hàng</p>
        </div>

        <form action="?act=admin&module=customers&action=importStore" method="POST" enctype="multipart/form-data" class="p-4 lg:p-6">
            <?php csrf_field(); ?>

            <!-- Download Template -->
            <div class="mb-4 lg:mb-6 p-3 lg:p-4 bg-info-bg border border-info rounded-2xl">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-info-dark text-sm lg:text-base mb-1">Chưa có file mẫu?</h3>
                        <p class="text-xs lg:text-sm text-info-text">Tải file mẫu Excel/CSV để điền thông tin khách hàng</p>
                    </div>
                    <a href="?act=admin&module=customers&action=downloadTemplate"
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-info text-white rounded-xl hover:opacity-90 font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2 shadow-sm">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        Tải mẫu
                    </a>
                </div>
            </div>

            <!-- File Upload -->
            <div class="mb-4 lg:mb-6">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Chọn file <span class="text-danger">*</span></label>
                <input type="file" name="file" accept=".csv,.xlsx,.xls" required
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                <p class="mt-2 text-xs lg:text-sm text-primary-500 flex items-center gap-1">
                    <i data-lucide="info" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                    Chấp nhận file: CSV, XLSX, XLS (tối đa 10MB)
                </p>
            </div>

            <!-- Instructions -->
            <div class="mb-4 lg:mb-6 p-3 lg:p-4 bg-primary-50 border border-primary-100 rounded-2xl">
                <h3 class="font-semibold text-primary-700 mb-2 text-sm lg:text-base">Hướng dẫn:</h3>
                <ul class="text-xs lg:text-sm text-primary-600 space-y-1 list-disc list-inside">
                    <li>File phải có header row (dòng đầu tiên chứa tên cột)</li>
                    <li>Các cột bắt buộc: <strong>Họ tên</strong> hoặc <strong>SĐT</strong> (ít nhất một trong hai)</li>
                    <li>Các cột tùy chọn: Email, Ngày sinh, Giới tính, Địa chỉ, CMND, Hộ chiếu</li>
                    <li>Nếu số điện thoại trùng, hệ thống sẽ bỏ qua dòng đó (không tính là lỗi)</li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-primary-100">
                <a href="?act=admin&module=customers"
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 font-semibold rounded-xl hover:bg-primary-100 transition-colors text-sm lg:text-base text-center">
                    Hủy bỏ
                </a>
                <button type="submit"
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="upload" class="w-4 h-4"></i>
                    Import khách hàng
                </button>
            </div>
        </form>
    </div>

    <!-- Link to Import Logs -->
    <div class="mt-4 lg:mt-6 text-center">
        <a href="?act=admin&module=customers&action=importLogs"
            class="text-accent hover:text-accent-dark font-semibold text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="history" class="w-4 h-4"></i>
            Xem lịch sử import
        </a>
    </div>
</div>

