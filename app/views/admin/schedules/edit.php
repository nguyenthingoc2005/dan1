<?php
/**
 * ADMIN - CHỈNH SỬA LỊCH KHỞI HÀNH
 * Variables: $schedule, $tours, $guides
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-8">
    <!-- Page Header - Responsive -->
    <div class="mb-4 lg:mb-8">
        <div class="flex items-center gap-2 text-xs lg:text-sm text-primary-500 mb-2">
            <a href="?act=admin&module=schedules" class="hover:text-accent">Lịch khởi hành</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span>Chỉnh sửa</span>
        </div>
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Chỉnh sửa Lịch Khởi Hành</h1>
        <p class="text-xs lg:text-sm text-primary-500 mt-1">Cập nhật thông tin lịch khởi hành cho tour</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 lg:gap-4 mb-4 lg:mb-6">
        <div class="bg-info-bg border border-info rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-info-text mb-1 font-semibold">Số chỗ mở bán</div>
            <div class="text-xl lg:text-2xl font-bold text-info-text"><?= $schedule['quota'] ?></div>
        </div>
        <div class="bg-success-bg border border-success rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-success-text mb-1 font-semibold">Đã đặt</div>
            <div class="text-xl lg:text-2xl font-bold text-success-text"><?= $schedule['booked'] ?? 0 ?></div>
        </div>
        <div class="bg-accent-bg border border-accent rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-accent-text mb-1 font-semibold">Còn lại</div>
            <div class="text-xl lg:text-2xl font-bold text-accent-text">
                <?= max(0, ($schedule['quota'] - ($schedule['booked'] ?? 0))) ?>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm overflow-hidden">
        <form action="?act=admin&module=schedules&action=update" method="POST" class="p-4 lg:p-6">
            <input type="hidden" name="id" value="<?= $schedule['id'] ?>">
            
            <div class="space-y-4 lg:space-y-6">
                <!-- Tour Selection (Read-only) -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tour du lịch</label>
                    <div class="relative">
                        <select name="tour_id" 
                            class="w-full pl-10 pr-4 py-2 lg:py-2.5 border border-primary-100 rounded-xl bg-primary-50 text-primary-500 cursor-not-allowed outline-none text-sm lg:text-base" 
                            readonly onclick="return false;">
                            <?php foreach ($tours as $tour): ?>
                                <option value="<?= $tour['id'] ?>" <?= $tour['id'] == $schedule['tour_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tour['name']) ?> (<?= $tour['tour_code'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="map-pin" class="w-4 h-4 text-primary-400"></i>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-primary-500">Không thể thay đổi tour của lịch đã tạo.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                    <!-- Start Date -->
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                            Ngày khởi hành <span class="text-danger">*</span>
                        </label>
                        <div class="relative">
                            <input type="date" name="start_date" 
                                   value="<?= $schedule['start_date'] ?>"
                                   min="<?= date('Y-m-d') ?>"
                                   class="w-full pl-10 pr-4 py-2 lg:py-2.5 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all bg-primary-50 text-primary-700 text-sm lg:text-base" required>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="calendar" class="w-4 h-4 text-primary-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Quota -->
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                            Số chỗ mở bán <span class="text-danger">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="quota" min="<?= $schedule['booked'] ?? 0 ?>" 
                                   value="<?= $schedule['quota'] ?>"
                                   id="quota-input"
                                   class="w-full pl-10 pr-4 py-2 lg:py-2.5 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all bg-primary-50 text-primary-700 text-sm lg:text-base" required>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="users" class="w-4 h-4 text-primary-400"></i>
                            </div>
                        </div>
                        <p id="quota-warning" class="mt-1 text-xs text-danger-text hidden"></p>
                        <p class="mt-1 text-xs text-primary-500">
                            ⚠️ Không được nhỏ hơn số đã đặt: <strong><?= $schedule['booked'] ?? 0 ?></strong>
                        </p>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="bg-primary-50 p-4 lg:p-5 rounded-2xl border border-primary-100">
                    <h3 class="text-xs lg:text-sm font-semibold text-primary-700 mb-3 flex items-center gap-2">
                        <i data-lucide="tag" class="w-4 h-4 text-accent"></i>
                        Giá bán áp dụng (VND)
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-primary-600 mb-1 lg:mb-2">Người lớn</label>
                            <input type="number" name="adult_price" min="0" step="1000"
                                   value="<?= $schedule['adult_price'] ?>"
                                   class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all bg-white text-primary-700 text-sm lg:text-base" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-primary-600 mb-1 lg:mb-2">Trẻ em</label>
                            <input type="number" name="child_price" min="0" step="1000"
                                   value="<?= $schedule['child_price'] ?>"
                                   class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all bg-white text-primary-700 text-sm lg:text-base" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-primary-600 mb-1 lg:mb-2">Em bé</label>
                            <input type="number" name="infant_price" min="0" step="1000"
                                   value="<?= $schedule['infant_price'] ?>"
                                   class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all bg-white text-primary-700 text-sm lg:text-base" required>
                        </div>
                    </div>
                </div>

                <!-- Guide Assignment -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                        Hướng dẫn viên <span class="text-primary-400 text-xs">(Tùy chọn)</span>
                    </label>
                    <select name="guide_id" 
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all bg-primary-50 text-primary-700 text-sm lg:text-base">
                        <option value="">-- Chưa gán HDV --</option>
                        <?php foreach ($guides as $g): ?>
                            <option value="<?= $g['id'] ?>" 
                                <?= ($schedule['guide_id'] ?? null) == $g['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['full_name']) ?> 
                                <?php if (!empty($g['phone'])): ?>
                                    - <?= htmlspecialchars($g['phone']) ?>
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($schedule['guide_name'])): ?>
                        <p class="mt-1 text-xs text-accent">
                            HDV hiện tại: <strong><?= htmlspecialchars($schedule['guide_name']) ?></strong>
                            <?php if (!empty($schedule['guide_phone'])): ?>
                                (<?= htmlspecialchars($schedule['guide_phone']) ?>)
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Guide Change Reason (only show if guide is being changed) -->
                <div id="guide-change-reason-section" class="hidden">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                        Lý do đổi HDV <span class="text-danger">*</span>
                    </label>
                    <select name="guide_change_reason" 
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all bg-primary-50 text-primary-700 text-sm lg:text-base">
                        <option value="">-- Chọn lý do --</option>
                        <option value="HDV bận">HDV bận</option>
                        <option value="HDV ốm">HDV ốm</option>
                        <option value="HDV xin nghỉ">HDV xin nghỉ</option>
                        <option value="Yêu cầu khách hàng">Yêu cầu khách hàng</option>
                        <option value="Tối ưu phân công">Tối ưu phân công</option>
                        <option value="Khác">Khác</option>
                    </select>
                    <p class="text-xs text-primary-500 mt-1">Vui lòng chọn lý do khi đổi HDV</p>
                </div>

                <!-- Guide Notes -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ghi chú cho HDV</label>
                    <textarea name="guide_notes" rows="3" 
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all bg-primary-50 placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="Ghi chú đặc biệt cho hướng dẫn viên..."><?= htmlspecialchars($schedule['guide_notes'] ?? '') ?></textarea>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
                    <select name="status" 
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all bg-primary-50 text-primary-700 text-sm lg:text-base">
                        <option value="open" <?= ($schedule['status'] ?? 'open') == 'open' ? 'selected' : '' ?>>Đang mở bán</option>
                        <option value="closed" <?= ($schedule['status'] ?? '') == 'closed' ? 'selected' : '' ?>>Đóng bán</option>
                        <option value="completed" <?= ($schedule['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Đã hoàn thành</option>
                        <option value="cancelled" 
                            <?= ($schedule['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>
                            <?= ($schedule['booked'] ?? 0) > 0 ? 'disabled' : '' ?>>
                            Đã hủy
                            <?php if (($schedule['booked'] ?? 0) > 0): ?>
                                (Không thể hủy khi có booking)
                            <?php endif; ?>
                        </option>
                    </select>
                    <?php if (($schedule['booked'] ?? 0) > 0): ?>
                        <p class="mt-1 text-xs text-danger-text">
                            ⚠️ Lịch này đang có <?= $schedule['booked'] ?> khách đã đặt. Không thể hủy lịch. Hãy hủy các booking trước.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Guide Change History -->
            <?php if (!empty($guide_history)): ?>
                <div class="mt-4 lg:mt-6 border-t border-primary-100 pt-4 lg:pt-6">
                    <h3 class="text-xs lg:text-sm font-semibold text-primary-700 mb-3 flex items-center gap-2">
                        <i data-lucide="history" class="w-4 h-4 text-info-text"></i>
                        Lịch sử thay đổi HDV
                    </h3>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        <?php foreach ($guide_history as $h): ?>
                            <div class="bg-primary-50 p-3 rounded-2xl border border-primary-100 text-xs lg:text-sm">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-semibold text-primary-700">
                                            <?php if ($h['old_guide_name'] && $h['new_guide_name']): ?>
                                                <span class="text-danger-text"><?= htmlspecialchars($h['old_guide_name']) ?></span>
                                                <span class="mx-2">→</span>
                                                <span class="text-success-text"><?= htmlspecialchars($h['new_guide_name']) ?></span>
                                            <?php elseif ($h['new_guide_name']): ?>
                                                Gán: <span class="text-success-text"><?= htmlspecialchars($h['new_guide_name']) ?></span>
                                            <?php else: ?>
                                                Hủy gán: <span class="text-red-600"><?= htmlspecialchars($h['old_guide_name']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($h['reason']): ?>
                                            <div class="text-xs text-gray-600 mt-1">Lý do: <?= htmlspecialchars($h['reason']) ?></div>
                                        <?php endif; ?>
                                        <?php if ($h['notes']): ?>
                                            <div class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($h['notes']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-xs text-gray-500 text-right">
                                        <div><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></div>
                                        <?php if ($h['changed_by_name']): ?>
                                            <div class="text-gray-400">bởi <?= htmlspecialchars($h['changed_by_name']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mt-8 flex items-center justify-end gap-3">
                <a href="?act=admin&module=schedules" 
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium shadow-sm transition-colors flex items-center gap-2">
                    <i class="fas fa-save"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const quotaInput = document.getElementById('quota-input');
    const quotaWarning = document.getElementById('quota-warning');
    const bookedCount = <?= $schedule['booked'] ?? 0 ?>;
    const guideSelect = document.querySelector('select[name="guide_id"]');
    const guideChangeReasonSection = document.getElementById('guide-change-reason-section');
    const currentGuideId = <?= $schedule['guide_id'] ?? 'null' ?>;

    quotaInput.addEventListener('input', function() {
        const quota = parseInt(quotaInput.value) || 0;
        
        if (quota < bookedCount) {
            quotaWarning.textContent = `⚠️ Số chỗ không được nhỏ hơn số đã đặt (${bookedCount} khách)`;
            quotaWarning.classList.remove('hidden');
            quotaInput.setCustomValidity('Số chỗ phải >= ' + bookedCount);
        } else {
            quotaWarning.classList.add('hidden');
            quotaInput.setCustomValidity('');
        }
    });

    // Show/hide guide change reason when guide changes
    guideSelect.addEventListener('change', function() {
        const newGuideId = this.value ? parseInt(this.value) : null;
        const isChanging = (currentGuideId && newGuideId && newGuideId != currentGuideId) || 
                          (currentGuideId && !newGuideId) || 
                          (!currentGuideId && newGuideId);
        
        if (isChanging) {
            guideChangeReasonSection.classList.remove('hidden');
            guideChangeReasonSection.querySelector('select').required = true;
        } else {
            guideChangeReasonSection.classList.add('hidden');
            guideChangeReasonSection.querySelector('select').required = false;
        }
    });

    // Initial validation
    if (parseInt(quotaInput.value) < bookedCount) {
        quotaInput.dispatchEvent(new Event('input'));
    }
</script>
