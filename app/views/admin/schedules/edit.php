<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="?act=admin&module=schedules" class="hover:text-blue-600">Lịch khởi hành</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span>Chỉnh sửa</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-800">Chỉnh sửa Lịch Khởi Hành</h1>
        <p class="text-sm text-gray-500 mt-1">Cập nhật thông tin lịch khởi hành cho tour</p>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="?act=admin&module=schedules&action=update" method="POST" class="p-6">
            <input type="hidden" name="id" value="<?= $schedule['id'] ?>">
            
            <div class="space-y-6">
                <!-- Tour Selection (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tour du lịch</label>
                    <div class="relative">
                        <select name="tour_id" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed outline-none" readonly onclick="return false;">
                            <?php foreach ($tours as $tour): ?>
                                <option value="<?= $tour['id'] ?>" <?= $tour['id'] == $schedule['tour_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tour['name']) ?> (<?= $tour['tour_code'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-map-marked-alt text-gray-400"></i>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Không thể thay đổi tour của lịch đã tạo.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày khởi hành <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="date" name="start_date" 
                                   value="<?= $schedule['start_date'] ?>"
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" required>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="far fa-calendar-alt text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Quota -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Số chỗ mở bán <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="quota" min="1" 
                                   value="<?= $schedule['quota'] ?>"
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" required>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-users text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-tag text-blue-500"></i> Giá bán áp dụng (VND)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Người lớn</label>
                            <input type="number" name="adult_price" 
                                   value="<?= $schedule['adult_price'] ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Trẻ em</label>
                            <input type="number" name="child_price" 
                                   value="<?= $schedule['child_price'] ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Em bé</label>
                            <input type="number" name="infant_price" 
                                   value="<?= $schedule['infant_price'] ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm" required>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                        <option value="open" <?= ($schedule['status'] ?? 'open') == 'open' ? 'selected' : '' ?>>Đang mở bán</option>
                        <option value="closed" <?= ($schedule['status'] ?? '') == 'closed' ? 'selected' : '' ?>>Đóng bán</option>
                        <option value="completed" <?= ($schedule['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Đã hoàn thành</option>
                        <option value="cancelled" <?= ($schedule['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3">
                <a href="?act=admin&module=schedules" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium shadow-sm transition-colors flex items-center gap-2">
                    <i class="fas fa-save"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
