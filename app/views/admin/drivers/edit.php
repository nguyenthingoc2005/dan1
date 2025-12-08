<?php
/**
 * ADMIN - CHỈNH SỬA TÀI XẾ
 */
?>
<div class="max-w-4xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Chỉnh sửa Tài xế</h1>
        <a href="?act=admin&module=drivers&action=show&id=<?= $driver['id'] ?>" class="w-full sm:w-auto px-4 lg:px-5 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center flex items-center justify-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="p-4 lg:p-6 border-b border-primary-100">
            <h2 class="text-base lg:text-lg font-bold text-primary-700">Thông tin tài xế</h2>
        </div>

        <form action="?act=admin&module=drivers&action=update" method="POST" class="p-4 lg:p-6">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?= $driver['id'] ?>">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-4 lg:mb-6">
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mã tài xế</label>
                    <input type="text" name="driver_code" value="<?= htmlspecialchars($driver['driver_code'] ?? '') ?>"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base">
                </div>
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Họ tên <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" required value="<?= htmlspecialchars($driver['full_name']) ?>"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-4 lg:mb-6">
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số điện thoại</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($driver['phone'] ?? '') ?>"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                </div>
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($driver['email'] ?? '') ?>"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-4 lg:mb-6">
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số bằng lái <span class="text-danger">*</span></label>
                    <input type="text" name="license_number" required value="<?= htmlspecialchars($driver['license_number']) ?>"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                </div>
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Hạng bằng</label>
                    <select name="license_type"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                        <option value="">-- Chọn hạng bằng --</option>
                        <option value="D" <?= $driver['license_type'] == 'D' ? 'selected' : '' ?>>Bằng D</option>
                        <option value="E" <?= $driver['license_type'] == 'E' ? 'selected' : '' ?>>Bằng E</option>
                        <option value="B1" <?= $driver['license_type'] == 'B1' ? 'selected' : '' ?>>Bằng B1</option>
                        <option value="B2" <?= $driver['license_type'] == 'B2' ? 'selected' : '' ?>>Bằng B2</option>
                        <option value="C" <?= $driver['license_type'] == 'C' ? 'selected' : '' ?>>Bằng C</option>
                    </select>
                </div>
            </div>

            <div class="mb-4 lg:mb-6">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
                <select name="status"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="active" <?= $driver['status'] == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="on_trip" <?= $driver['status'] == 'on_trip' ? 'selected' : '' ?>>Đang đi tour</option>
                    <option value="off_duty" <?= $driver['status'] == 'off_duty' ? 'selected' : '' ?>>Nghỉ</option>
                    <option value="suspended" <?= $driver['status'] == 'suspended' ? 'selected' : '' ?>>Tạm ngưng</option>
                    <option value="inactive" <?= $driver['status'] == 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="px-5 py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base">
                    Cập nhật
                </button>
                <a href="?act=admin&module=drivers&action=show&id=<?= $driver['id'] ?>"
                    class="px-5 py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>

