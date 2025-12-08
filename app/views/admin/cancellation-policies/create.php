<?php
/**
 * ADMIN - TẠO CHÍNH SÁCH HỦY MỚI
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Tạo Chính sách Hủy mới</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Thêm chính sách tính phí hủy booking mới</p>
        </div>
        <a href="?act=admin&module=cancellation-policies"
            class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center flex items-center justify-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    <!-- Form -->
    <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100">
        <form action="?act=admin&module=cancellation-policies&action=store" method="POST">
            <?= csrf_field() ?>

            <div class="space-y-4 lg:space-y-6">
                <div>
                    <label class="block text-sm font-medium text-primary-700 mb-2">
                        Tên chính sách <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base"
                        placeholder="VD: Hủy trước 3 ngày">
                </div>

                <div>
                    <label class="block text-sm font-medium text-primary-700 mb-2">
                        Số ngày trước khởi hành <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="days_before" required min="0" step="1"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base"
                        placeholder="VD: 3 (áp dụng cho booking hủy ≤ 3 ngày trước khởi hành)">
                    <p class="text-xs text-primary-500 mt-1">
                        Policy này sẽ áp dụng cho các booking hủy ≤ số ngày này trước khởi hành
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-primary-700 mb-2">
                        Phí hủy (%) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="fee_percentage" required min="0" max="100" step="0.01"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base"
                        placeholder="VD: 30.00">
                    <p class="text-xs text-primary-500 mt-1">
                        Phí hủy tính theo % của thành tiền (0% - 100%)
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-primary-700 mb-2">
                        Mô tả
                    </label>
                    <textarea name="description" rows="3"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base"
                        placeholder="Mô tả chi tiết về chính sách này..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-primary-700 mb-2">
                        Trạng thái
                    </label>
                    <select name="status"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                        <option value="active" selected>Hoạt động</option>
                        <option value="inactive">Ngừng hoạt động</option>
                    </select>
                </div>

                <!-- Example -->
                <div class="bg-info-bg p-4 rounded-xl border border-info">
                    <p class="text-sm font-semibold text-info-text mb-2">Ví dụ:</p>
                    <ul class="text-xs text-info-text space-y-1 list-disc list-inside">
                        <li>Nếu tạo policy: <strong>days_before = 3, fee_percentage = 30%</strong></li>
                        <li>→ Booking hủy trước 1, 2, hoặc 3 ngày sẽ bị tính phí 30%</li>
                        <li>→ Booking hủy trước 4 ngày sẽ tìm policy khác có days_before ≥ 4</li>
                    </ul>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <a href="?act=admin&module=cancellation-policies"
                    class="px-4 lg:px-5 py-2 lg:py-2.5 text-primary-600 hover:bg-primary-50 rounded-xl font-semibold transition-all text-sm lg:text-base">
                    Hủy
                </a>
                <button type="submit"
                    class="px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base">
                    Tạo chính sách
                </button>
            </div>
        </form>
    </div>
</div>

