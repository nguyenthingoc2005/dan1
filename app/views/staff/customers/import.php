<?php
/**
 * STAFF - IMPORT KHÁCH HÀNG TỪ EXCEL
 */
?>
<div class="max-w-4xl mx-auto">
    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Import Khách hàng từ Excel</h1>
        <a href="?act=staff-customers"
            class="px-4 py-2 bg-slate-200 text-slate-700 rounded hover:bg-slate-300 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>

    <!-- FORM CARD -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">Upload file Excel/CSV</h2>
            <p class="text-sm text-slate-500 mt-1">Chọn file Excel hoặc CSV chứa danh sách khách hàng</p>
        </div>

        <form action="?act=staff-customers&action=importStore" method="POST" enctype="multipart/form-data" class="p-6">
            <?php csrf_field(); ?>

            <!-- Download Template -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-medium text-blue-900 mb-1">Chưa có file mẫu?</h3>
                        <p class="text-sm text-blue-700">Tải file mẫu Excel/CSV để điền thông tin khách hàng</p>
                    </div>
                    <a href="?act=staff-customers&action=downloadTemplate"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-download mr-2"></i> Tải mẫu
                    </a>
                </div>
            </div>

            <!-- File Upload -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-2">Chọn file <span class="text-red-500">*</span></label>
                <input type="file" name="file" accept=".csv,.xlsx,.xls" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                <p class="mt-2 text-sm text-slate-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Chấp nhận file: CSV, XLSX, XLS (tối đa 10MB)
                </p>
            </div>

            <!-- Instructions -->
            <div class="mb-6 p-4 bg-slate-50 border border-slate-200 rounded-lg">
                <h3 class="font-medium text-slate-800 mb-2">Hướng dẫn:</h3>
                <ul class="text-sm text-slate-600 space-y-1 list-disc list-inside">
                    <li>File phải có header row (dòng đầu tiên chứa tên cột)</li>
                    <li>Các cột bắt buộc: <strong>Họ tên</strong> hoặc <strong>SĐT</strong> (ít nhất một trong hai)</li>
                    <li>Các cột tùy chọn: Email, Ngày sinh, Giới tính, Địa chỉ, CMND, Hộ chiếu</li>
                    <li>Nếu số điện thoại trùng, hệ thống sẽ bỏ qua dòng đó (không tính là lỗi)</li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="?act=staff-customers"
                    class="px-6 py-2.5 bg-slate-100 text-slate-700 font-medium rounded-lg hover:bg-slate-200 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-accent text-white font-medium rounded-lg hover:bg-blue-700 shadow-md hover:shadow-lg transition-all">
                    <i class="fas fa-upload mr-2"></i> Import khách hàng
                </button>
            </div>
        </form>
    </div>

    <!-- Link to Import Logs -->
    <div class="mt-4 text-center">
        <a href="?act=staff-customers&action=importLogs"
            class="text-blue-600 hover:text-blue-700 text-sm">
            <i class="fas fa-history mr-1"></i> Xem lịch sử import
        </a>
    </div>
</div>

