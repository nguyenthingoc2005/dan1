<?php
/**
 * ADMIN - CHỈNH SỬA CHÍNH SÁCH HỦY
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Chỉnh sửa Chính sách Hủy</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">Cập nhật thông tin chính sách hủy</p>
        </div>
        <a href="?act=admin&module=cancellation-policies"
            class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center flex items-center justify-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    <!-- Form -->
    <div class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100">
        <form action="?act=admin&module=cancellation-policies&action=update" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $policy['id'] ?>">

            <div class="space-y-4 lg:space-y-6">
                <div>
                    <label class="block text-sm font-medium text-primary-700 mb-2">
                        Tên chính sách <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($policy['name']) ?>"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base"
                        placeholder="VD: Hủy trước 3 ngày">
                </div>

                <div>
                    <label class="block text-sm font-medium text-primary-700 mb-2">
                        Số ngày trước khởi hành <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="days_before" required min="0" step="1" value="<?= $policy['days_before'] ?>"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base"
                        placeholder="VD: 3">
                    <p class="text-xs text-primary-500 mt-1">
                        Policy này sẽ áp dụng cho các booking hủy ≤ số ngày này trước khởi hành
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-primary-700 mb-2">
                        Phí hủy (%) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="fee_percentage" required min="0" max="100" step="0.01" value="<?= $policy['fee_percentage'] ?>"
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
                        placeholder="Mô tả chi tiết về chính sách này..."><?= htmlspecialchars($policy['description'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-primary-700 mb-2">
                        Trạng thái
                    </label>
                    <select name="status"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                        <option value="active" <?= $policy['status'] === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="inactive" <?= $policy['status'] === 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <a href="?act=admin&module=cancellation-policies"
                    class="px-4 lg:px-5 py-2 lg:py-2.5 text-primary-600 hover:bg-primary-50 rounded-xl font-semibold transition-all text-sm lg:text-base">
                    Hủy
                </a>
                <button type="submit"
                    class="px-4 lg:px-5 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base">
                    Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>

