<?php
/**
 * ADMIN - FORM TẠO SERVICE TYPE
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-4xl mx-auto p-4 lg:p-8">
    <!-- Header - Responsive -->
    <div class="mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Thêm loại dịch vụ mới</h1>
    </div>

    <form method="POST" action="?act=admin&module=service-types&action=store" class="bg-panel p-4 lg:p-6 rounded-2xl border border-primary-100 shadow-sm">
        <div class="space-y-4 lg:space-y-6">
            <!-- Tên loại dịch vụ -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Tên loại dịch vụ <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" required
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                    placeholder="VD: Khách sạn, Nhà hàng, Xe du lịch">
            </div>

            <!-- Mô tả -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả</label>
                <textarea name="description" rows="3"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                    placeholder="Mô tả ngắn về loại dịch vụ"></textarea>
            </div>

            <!-- Trạng thái -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
                <select name="status"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Vô hiệu</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-primary-100">
                <a href="?act=admin&module=service-types"
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base text-center">
                    Hủy
                </a>
                <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Tạo loại dịch vụ
                </button>
            </div>
        </div>
    </form>
</div>