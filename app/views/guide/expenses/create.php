<?php
/**
 * GUIDE - GHI CHI PHÍ PHÁT SINH MỚI
 * Variables: $schedule, $tour, $bookings
 */
?>

<div class="max-w-4xl mx-auto p-4 lg:p-8">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Ghi chi phí phát sinh</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">
                <?= htmlspecialchars($tour['tour_code']) ?> - <?= htmlspecialchars($tour['name']) ?>
            </p>
        </div>
        <a href="?act=guide-expenses&action=show&schedule_id=<?= $schedule['id'] ?>" 
           class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    <form method="POST" action="?act=guide-expenses&action=store" enctype="multipart/form-data" class="bg-panel rounded-2xl p-4 lg:p-6 border border-primary-100 shadow-sm">
        <?= csrf_field() ?>
        <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">

        <div class="space-y-4 lg:space-y-6">
            <!-- Booking Selection -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Booking <span class="text-danger">*</span>
                </label>
                <select name="booking_id" required 
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    <option value="">-- Chọn booking --</option>
                    <?php foreach ($bookings as $booking): ?>
                        <option value="<?= $booking['id'] ?>">
                            <?= htmlspecialchars($booking['booking_code']) ?> - 
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
                       value="<?= date('Y-m-d') ?>"
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
                    <option value="Ăn uống">Ăn uống</option>
                    <option value="Vận chuyển">Vận chuyển</option>
                    <option value="Lưu trú">Lưu trú</option>
                    <option value="Tham quan">Tham quan</option>
                    <option value="Y tế">Y tế</option>
                    <option value="Khác">Khác</option>
                </select>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Mô tả <span class="text-danger">*</span>
                </label>
                <textarea name="description" required rows="3"
                          placeholder="Mô tả chi tiết về chi phí phát sinh..."
                          class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"></textarea>
            </div>

            <!-- Amount -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Số tiền (VNĐ) <span class="text-danger">*</span>
                </label>
                <input type="number" name="amount" required min="0" step="1000"
                       placeholder="0"
                       class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
            </div>

            <!-- Receipt File -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Hóa đơn/Chứng từ (nếu có)
                </label>
                <input type="file" name="receipt_file" accept="image/*,.pdf"
                       class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                <p class="text-xs text-primary-500 mt-1">Chấp nhận: JPG, PNG, PDF (tối đa 5MB)</p>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                    Ghi chú
                </label>
                <textarea name="notes" rows="2"
                          placeholder="Ghi chú thêm (nếu có)..."
                          class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"></textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-primary-100">
                <a href="?act=guide-expenses&action=show&schedule_id=<?= $schedule['id'] ?>" 
                   class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base text-center">
                    Hủy
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Lưu chi phí
                </button>
            </div>
        </div>
    </form>
</div>

