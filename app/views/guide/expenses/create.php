<?php
/**
 * GUIDE - GHI CHI PHÍ PHÁT SINH MỚI
 * Variables: $schedule, $tour, $bookings
 */
?>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Ghi chi phí phát sinh</h1>
            <p class="text-slate-500 text-sm mt-1">
                <?= htmlspecialchars($tour['tour_code']) ?> - <?= htmlspecialchars($tour['name']) ?>
            </p>
        </div>
        <a href="?act=guide-expenses&action=show&schedule_id=<?= $schedule['id'] ?>" 
           class="px-4 py-2 bg-panel border border-slate-300 text-slate-700 rounded hover:bg-slate-50">
            ← Quay lại
        </a>
    </div>

    <form method="POST" action="?act=guide-expenses&action=store" enctype="multipart/form-data" class="bg-panel rounded p-6 border border-slate-200">
        <?= csrf_field() ?>
        <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">

        <div class="space-y-6">
            <!-- Booking Selection -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Booking <span class="text-red-500">*</span>
                </label>
                <select name="booking_id" required 
                        class="w-full px-3 py-2 border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-accent">
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
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Ngày phát sinh <span class="text-red-500">*</span>
                </label>
                <input type="date" name="expense_date" required 
                       value="<?= date('Y-m-d') ?>"
                       max="<?= date('Y-m-d') ?>"
                       class="w-full px-3 py-2 border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-accent">
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Loại chi phí
                </label>
                <select name="category" 
                        class="w-full px-3 py-2 border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-accent">
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
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Mô tả <span class="text-red-500">*</span>
                </label>
                <textarea name="description" required rows="3"
                          placeholder="Mô tả chi tiết về chi phí phát sinh..."
                          class="w-full px-3 py-2 border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
            </div>

            <!-- Amount -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Số tiền (VNĐ) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="amount" required min="0" step="1000"
                       placeholder="0"
                       class="w-full px-3 py-2 border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-accent">
            </div>

            <!-- Receipt File -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Hóa đơn/Chứng từ (nếu có)
                </label>
                <input type="file" name="receipt_file" accept="image/*,.pdf"
                       class="w-full px-3 py-2 border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-accent">
                <p class="text-xs text-slate-500 mt-1">Chấp nhận: JPG, PNG, PDF (tối đa 5MB)</p>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Ghi chú
                </label>
                <textarea name="notes" rows="2"
                          placeholder="Ghi chú thêm (nếu có)..."
                          class="w-full px-3 py-2 border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="?act=guide-expenses&action=show&schedule_id=<?= $schedule['id'] ?>" 
                   class="px-6 py-2 border border-slate-300 rounded hover:bg-slate-50">
                    Hủy
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-accent text-white rounded hover:bg-accent/90 font-medium">
                    💾 Lưu chi phí
                </button>
            </div>
        </div>
    </form>
</div>

