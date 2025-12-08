<?php
/**
 * GUIDE - TẠO CHECKPOINT
 * Variables: $schedule, $tour
 */
?>

<div class="max-w-4xl mx-auto p-4 lg:p-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Tạo Checkpoint</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">
                <?= htmlspecialchars($tour['tour_code']) ?> - <?= date('d/m/Y', strtotime($schedule['start_date'])) ?> - <?= date('d/m/Y', strtotime($schedule['end_date'])) ?>
            </p>
        </div>
        <a href="?act=guide-checkpoints&action=index&schedule_id=<?= $schedule['id'] ?>" 
            class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    <form method="POST" action="?act=guide-checkpoints&action=store" class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm">
        <?= csrf_field() ?>
        <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">
        
        <div class="p-4 lg:p-6 space-y-4 lg:space-y-6">
            <!-- Basic Info - Chỉ giữ lại các trường cần thiết -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-primary-700 mb-2">Tên Checkpoint *</label>
                    <input type="text" name="checkpoint_name" required
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl text-sm lg:text-base text-primary-700 focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all"
                        placeholder="VD: Lên xe ngày 1, Ăn trưa ngày 2">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-primary-700 mb-2">Loại Checkpoint *</label>
                    <select name="checkpoint_type" required
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl text-sm lg:text-base text-primary-700 focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                        <option value="">-- Chọn loại --</option>
                        <option value="boarding">Lên xe (Boarding)</option>
                        <option value="meal">Ăn (Meal)</option>
                        <option value="accommodation">Ngủ (Accommodation)</option>
                        <option value="transfer">Di chuyển (Transfer)</option>
                        <option value="activity">Hoạt động (Activity)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-primary-700 mb-2">Ngày *</label>
                    <input type="date" name="scheduled_date" required
                        min="<?= $schedule['start_date'] ?>" max="<?= $schedule['end_date'] ?>"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl text-sm lg:text-base text-primary-700 focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-primary-700 mb-2">Thời gian *</label>
                    <input type="time" name="scheduled_time" required
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl text-sm lg:text-base text-primary-700 focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                </div>
            </div>

            <!-- Options -->
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_required" value="1" checked
                        class="w-4 h-4 text-accent border-primary-300 rounded focus:ring-accent">
                    <span class="text-sm lg:text-base text-primary-700 font-semibold">Bắt buộc check-in</span>
                </label>
            </div>

            <input type="hidden" name="status" value="active">
        </div>

        <div class="px-4 lg:px-6 py-4 lg:py-5 bg-primary-50 border-t border-primary-100 flex flex-col sm:flex-row justify-end gap-3">
            <a href="?act=guide-checkpoints&action=index&schedule_id=<?= $schedule['id'] ?>" 
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-white text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base text-center">
                Hủy
            </a>
            <button type="submit" 
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                Tạo Checkpoint
            </button>
        </div>
    </form>
</div>


