<?php
/**
 * GUIDE - SỬA CHECKPOINT
 * Variables: $checkpoint, $schedule, $tour
 */
?>

<div class="max-w-4xl mx-auto p-4 lg:p-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Sửa Checkpoint</h1>
            <p class="text-xs lg:text-sm text-primary-500 mt-1">
                <?= htmlspecialchars($tour['tour_code']) ?> - <?= htmlspecialchars($checkpoint['checkpoint_name']) ?>
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <a href="?act=guide-tours&action=show&id=<?= $checkpoint['tour_schedule_id'] ?>&tab=checkin" 
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="home" class="w-4 h-4"></i>
                Quay về Tour
            </a>
            <a href="?act=guide-checkpoints&action=index&schedule_id=<?= $checkpoint['tour_schedule_id'] ?>" 
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
    </div>

    <form method="POST" action="?act=guide-checkpoints&action=update" class="bg-panel rounded-2xl overflow-hidden border border-primary-100 shadow-sm">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $checkpoint['id'] ?>">
        
        <div class="p-4 lg:p-6 space-y-4 lg:space-y-6">
            <!-- Basic Info - Chỉ giữ lại các trường cần thiết -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-primary-700 mb-2">Tên Checkpoint *</label>
                    <input type="text" name="checkpoint_name" required value="<?= htmlspecialchars($checkpoint['checkpoint_name']) ?>"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl text-sm lg:text-base text-primary-700 focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-primary-700 mb-2">Loại Checkpoint *</label>
                    <select name="checkpoint_type" required
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl text-sm lg:text-base text-primary-700 focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                        <option value="boarding" <?= $checkpoint['checkpoint_type'] == 'boarding' ? 'selected' : '' ?>>Lên xe (Boarding)</option>
                        <option value="meal" <?= $checkpoint['checkpoint_type'] == 'meal' ? 'selected' : '' ?>>Ăn (Meal)</option>
                        <option value="accommodation" <?= $checkpoint['checkpoint_type'] == 'accommodation' ? 'selected' : '' ?>>Ngủ (Accommodation)</option>
                        <option value="transfer" <?= $checkpoint['checkpoint_type'] == 'transfer' ? 'selected' : '' ?>>Di chuyển (Transfer)</option>
                        <option value="activity" <?= $checkpoint['checkpoint_type'] == 'activity' ? 'selected' : '' ?>>Hoạt động (Activity)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-primary-700 mb-2">Ngày *</label>
                    <input type="date" name="scheduled_date" required value="<?= $checkpoint['scheduled_date'] ?>"
                        min="<?= $schedule['start_date'] ?>" max="<?= $schedule['end_date'] ?>"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl text-sm lg:text-base text-primary-700 focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-primary-700 mb-2">Thời gian *</label>
                    <input type="time" name="scheduled_time" required value="<?= $checkpoint['scheduled_time'] ?>"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl text-sm lg:text-base text-primary-700 focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                </div>
            </div>

            <!-- Options -->
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_required" value="1" <?= $checkpoint['is_required'] ? 'checked' : '' ?>
                        class="w-4 h-4 text-accent border-primary-300 rounded focus:ring-accent">
                    <span class="text-sm lg:text-base text-primary-700 font-semibold">Bắt buộc check-in</span>
                </label>
            </div>

            <div>
                <label class="block text-sm font-semibold text-primary-700 mb-2">Trạng thái</label>
                <select name="status"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl text-sm lg:text-base text-primary-700 focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all">
                    <option value="active" <?= $checkpoint['status'] == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="inactive" <?= $checkpoint['status'] == 'inactive' ? 'selected' : '' ?>>Không hoạt động</option>
                </select>
            </div>
        </div>

        <div class="px-4 lg:px-6 py-4 lg:py-5 bg-primary-50 border-t border-primary-100 flex flex-col sm:flex-row justify-end gap-3">
            <a href="?act=guide-tours&action=show&id=<?= $checkpoint['tour_schedule_id'] ?>&tab=checkin" 
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-white text-primary-700 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base text-center">
                Hủy
            </a>
            <button type="submit" 
                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                Cập nhật
            </button>
        </div>
    </form>
</div>


