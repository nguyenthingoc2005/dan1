<?php
/**
 * ADMIN - SỬA CHI PHÍ PHÁT SINH
 * Variables: $schedule, $tour, $bookings, $expense
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-8">
    <!-- Page Header -->
    <div class="mb-4 lg:mb-6">
        <div class="flex items-center gap-2 text-xs lg:text-sm text-primary-500 mb-2">
            <a href="?act=admin&module=expenses&action=show&schedule_id=<?= $schedule['id'] ?>" class="hover:text-accent">Chi phí phát sinh</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span>Sửa</span>
        </div>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Sửa chi phí phát sinh</h1>
                <p class="text-xs lg:text-sm text-primary-500 mt-1">
                    <?= htmlspecialchars($tour['tour_code']) ?> - <?= htmlspecialchars($tour['name']) ?>
                </p>
            </div>
            <a href="?act=admin&module=expenses&action=show&schedule_id=<?= $schedule['id'] ?>" 
               class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
    </div>

    <form method="POST" action="?act=admin&module=expenses&action=update" enctype="multipart/form-data" class="bg-panel rounded-2xl p-4 lg:p-6 border border-primary-100 shadow-sm">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $expense['id'] ?>">
        <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">

        <div class="space-y-4 lg:space-y-6">
            <!-- Booking Selection -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Booking (tùy chọn)
                </label>
                <select name="booking_id" 
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Không chọn (chi phí chung cho tour) --</option>
                    <?php foreach ($bookings as $booking): ?>
                        <option value="<?= $booking['id'] ?>" <?= ($expense['booking_id'] ?? null) == $booking['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($booking['booking_code'] ?? 'N/A') ?> - 
                            <?= htmlspecialchars($booking['customer_name'] ?? 'Khách hàng') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Expense Date -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Ngày phát sinh <span class="text-danger">*</span>
                </label>
                <input type="date" name="expense_date" required 
                       value="<?= htmlspecialchars($expense['expense_date']) ?>"
                       max="<?= date('Y-m-d') ?>"
                       class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
            </div>

            <!-- Category -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Loại chi phí
                </label>
                <select name="category" 
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Chọn loại --</option>
                    <option value="Ăn uống" <?= ($expense['category'] ?? '') == 'Ăn uống' ? 'selected' : '' ?>>Ăn uống</option>
                    <option value="Vận chuyển" <?= ($expense['category'] ?? '') == 'Vận chuyển' ? 'selected' : '' ?>>Vận chuyển</option>
                    <option value="Lưu trú" <?= ($expense['category'] ?? '') == 'Lưu trú' ? 'selected' : '' ?>>Lưu trú</option>
                    <option value="Tham quan" <?= ($expense['category'] ?? '') == 'Tham quan' ? 'selected' : '' ?>>Tham quan</option>
                    <option value="Y tế" <?= ($expense['category'] ?? '') == 'Y tế' ? 'selected' : '' ?>>Y tế</option>
                    <option value="Khác" <?= ($expense['category'] ?? '') == 'Khác' ? 'selected' : '' ?>>Khác</option>
                </select>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Mô tả <span class="text-danger">*</span>
                </label>
                <textarea name="description" required rows="3"
                          placeholder="Mô tả chi tiết về chi phí phát sinh..."
                          class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($expense['description']) ?></textarea>
            </div>

            <!-- Amount -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Số tiền (VNĐ) <span class="text-danger">*</span>
                </label>
                <input type="number" name="amount" required min="0" step="1000"
                       value="<?= htmlspecialchars($expense['amount']) ?>"
                       class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
            </div>

            <!-- Receipt File -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Hóa đơn/Chứng từ
                </label>
                <?php if (!empty($expense['receipt_file'])): ?>
                    <div class="mb-2">
                        <a href="<?= BASE_URL ?>/public/<?= $expense['receipt_file'] ?>" target="_blank"
                           class="text-accent hover:text-accent-hover text-sm font-semibold flex items-center gap-2">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            Xem hóa đơn hiện tại
                        </a>
                    </div>
                <?php endif; ?>
                <input type="file" name="receipt_file" accept="image/*,.pdf"
                       class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                <p class="text-xs text-primary-500 mt-1">Chấp nhận: JPG, PNG, PDF (tối đa 5MB). Để trống nếu không thay đổi.</p>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Ghi chú
                </label>
                <textarea name="notes" rows="2"
                          placeholder="Ghi chú thêm (nếu có)..."
                          class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($expense['notes'] ?? '') ?></textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-primary-100">
                <a href="?act=admin&module=expenses&action=show&schedule_id=<?= $schedule['id'] ?>" 
                   class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base text-center">
                    Hủy
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Cập nhật
                </button>
            </div>
        </div>
    </form>
</div>

