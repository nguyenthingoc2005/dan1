<?php
/**
 * ADMIN - CHỈNH SỬA XE
 */
?>
<div class="max-w-4xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Chỉnh sửa Xe</h1>
        <a href="?act=admin&module=vehicles&action=show&id=<?= $vehicle['id'] ?>" class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center flex items-center justify-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="p-4 lg:p-6 border-b border-primary-100">
            <h2 class="text-base lg:text-lg font-bold text-primary-700">Thông tin xe</h2>
        </div>

        <form action="?act=admin&module=vehicles&action=update" method="POST" class="p-4 lg:p-6">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?= $vehicle['id'] ?>">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-4 lg:mb-6">
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mã xe</label>
                    <input type="text" name="vehicle_code" value="<?= htmlspecialchars($vehicle['vehicle_code'] ?? '') ?>"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="VD: VH001">
                </div>
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Loại xe <span class="text-danger">*</span></label>
                    <select name="vehicle_type" required
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                        <option value="bus_45" <?= $vehicle['vehicle_type'] == 'bus_45' ? 'selected' : '' ?>>Xe bus 45 chỗ</option>
                        <option value="bus_29" <?= $vehicle['vehicle_type'] == 'bus_29' ? 'selected' : '' ?>>Xe bus 29 chỗ</option>
                        <option value="bus_16" <?= $vehicle['vehicle_type'] == 'bus_16' ? 'selected' : '' ?>>Xe bus 16 chỗ</option>
                        <option value="car_7" <?= $vehicle['vehicle_type'] == 'car_7' ? 'selected' : '' ?>>Xe 7 chỗ</option>
                        <option value="car_4" <?= $vehicle['vehicle_type'] == 'car_4' ? 'selected' : '' ?>>Xe 4 chỗ</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-4 lg:mb-6">
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Biển số xe <span class="text-danger">*</span></label>
                    <input type="text" name="license_plate" required value="<?= htmlspecialchars($vehicle['license_plate']) ?>"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
                </div>
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số chỗ <span class="text-danger">*</span></label>
                    <input type="number" name="capacity" required min="1" value="<?= $vehicle['capacity'] ?>"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                </div>
            </div>

            <div class="mb-4 lg:mb-6">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
                <select name="status"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="active" <?= $vehicle['status'] == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="maintenance" <?= $vehicle['status'] == 'maintenance' ? 'selected' : '' ?>>Bảo dưỡng</option>
                    <option value="inactive" <?= $vehicle['status'] == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động</option>
                </select>
            </div>

            <div class="mb-4 lg:mb-6">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ghi chú</label>
                <textarea name="notes" rows="3"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($vehicle['notes'] ?? '') ?></textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="px-5 py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base">
                    Cập nhật
                </button>
                <a href="?act=admin&module=vehicles&action=show&id=<?= $vehicle['id'] ?>"
                    class="px-5 py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>

