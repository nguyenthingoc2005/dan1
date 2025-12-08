<?php
/**
 * ADMIN - PHÂN CÔNG HƯỚNG DẪN VIÊN CHO LỊCH TRÌNH (TOUR-010)
 * Variables: $schedule, $tour, $guides, $booked, $min_participants, $can_assign
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
            <a href="?act=admin&module=schedules&action=show&id=<?= $schedule['id'] ?>" class="hover:text-accent">Chi
                tiết</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span>Phân công HDV</span>
        </div>
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Phân công Hướng dẫn viên</h1>
        <p class="text-xs lg:text-sm text-primary-500 mt-1">Gán hướng dẫn viên cho lịch trình tour</p>
    </div>

    <!-- Schedule Info Card -->
    <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm p-4 lg:p-6 mb-4 lg:mb-6">
        <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4">Thông tin Lịch trình</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <span class="text-xs lg:text-sm text-primary-500">Tour:</span>
                <p class="font-bold text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($schedule['tour_name']) ?></p>
                <p class="text-xs text-primary-500"><?= htmlspecialchars($schedule['tour_code']) ?></p>
            </div>
            <div>
                <span class="text-xs lg:text-sm text-primary-500">Ngày khởi hành:</span>
                <p class="font-bold text-success-text text-sm lg:text-base"><?= date('d/m/Y', strtotime($schedule['start_date'])) ?></p>
                <p class="text-xs text-primary-500">Kết thúc: <?= date('d/m/Y', strtotime($schedule['end_date'])) ?></p>
            </div>
        </div>
    </div>

    <!-- Participant Info Card -->
    <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm p-4 lg:p-6 mb-4 lg:mb-6">
        <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4">Thông tin Số người</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 lg:gap-4">
            <div class="bg-info-bg border border-info rounded-2xl p-3 lg:p-4">
                <div class="text-xs lg:text-sm text-info-text mb-1 font-semibold">Số người tối thiểu</div>
                <div class="text-xl lg:text-2xl font-bold text-info-text"><?= $min_participants ?></div>
            </div>
            <div class="bg-success-bg border border-success rounded-2xl p-3 lg:p-4">
                <div class="text-xs lg:text-sm text-success-text mb-1 font-semibold">Số người đã đặt</div>
                <div class="text-xl lg:text-2xl font-bold text-success-text"><?= $booked ?></div>
            </div>
            <div
                class="bg-<?= $can_assign ? 'success' : 'danger' ?>-bg border border-<?= $can_assign ? 'success' : 'danger' ?> rounded-2xl p-3 lg:p-4">
                <div class="text-xs lg:text-sm text-<?= $can_assign ? 'success' : 'danger' ?>-text mb-1 font-semibold">Trạng thái</div>
                <div class="text-base lg:text-lg font-bold text-<?= $can_assign ? 'success' : 'danger' ?>-text flex items-center gap-2">
                    <?php if ($can_assign): ?>
                        <i data-lucide="check-circle" class="w-4 h-4 lg:w-5 lg:h-5"></i>
                        Đủ điều kiện
                    <?php else: ?>
                        <i data-lucide="x-circle" class="w-4 h-4 lg:w-5 lg:h-5"></i>
                        Chưa đủ
                    <?php endif; ?>
                </div>
                <?php if (!$can_assign): ?>
                    <div class="text-xs text-danger-text mt-1">
                        Còn thiếu: <?= $min_participants - $booked ?> người
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Warning if not enough participants -->
    <?php if (!$can_assign): ?>
        <div class="bg-danger-bg border-l-4 border-danger p-4 lg:p-5 mb-4 lg:mb-6 rounded-2xl">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5 lg:w-6 lg:h-6 text-danger"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-xs lg:text-sm font-bold text-danger-text">Chưa đủ số người tối thiểu</h3>
                    <div class="mt-2 text-xs lg:text-sm text-danger-text">
                        <p>Tour này chưa đủ số người tối thiểu
                            (<strong><?= $booked ?></strong>/<strong><?= $min_participants ?></strong>).</p>
                        <p class="mt-1">Chỉ phân công guide khi đã đủ số người tối thiểu.</p>
                        <p class="mt-1 font-bold">Còn thiếu: <strong><?= $min_participants - $booked ?></strong> người</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Assign Guide Form -->
    <div class="bg-panel rounded-2xl border border-primary-100 shadow-sm p-4 lg:p-6">
        <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4">Chọn Hướng dẫn viên</h2>

        <form action="?act=admin&module=schedules&action=assignGuide" method="POST">
            <input type="hidden" name="id" value="<?= $schedule['id'] ?>">

            <div class="space-y-4 lg:space-y-6">
                <!-- Guide Selection -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                        Hướng dẫn viên <span class="text-danger">*</span>
                    </label>
                    <select name="guide_id" id="guide_id"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all bg-primary-50 text-primary-700 text-sm lg:text-base <?= !$can_assign ? 'opacity-50 cursor-not-allowed' : '' ?>"
                        <?= !$can_assign ? 'disabled' : 'required' ?>>
                        <option value="">-- Chọn Hướng dẫn viên --</option>
                        <?php foreach ($guides as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= ($schedule['guide_id'] ?? null) == $g['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['full_name']) ?>
                                <?php if (!empty($g['phone'])): ?>
                                    - <?= htmlspecialchars($g['phone']) ?>
                                <?php endif; ?>
                                <?php if (!empty($g['email'])): ?>
                                    (<?= htmlspecialchars($g['email']) ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-primary-500 mt-1">Chọn hướng dẫn viên sẽ phụ trách tour này</p>
                </div>

                <!-- Guide Notes -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ghi chú cho HDV</label>
                    <textarea name="guide_notes" id="guide_notes" rows="4"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all bg-primary-50 placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="Ví dụ: Hướng dẫn đặc biệt cho đoàn VIP, Guide sẽ đón khách tại sân bay..."><?= htmlspecialchars($schedule['guide_notes'] ?? '') ?></textarea>
                    <p class="text-xs text-primary-500 mt-1">Ghi chú đặc biệt hoặc hướng dẫn cho hướng dẫn viên</p>
                </div>

                <!-- Current Guide Info (if exists) -->
                <?php if (!empty($schedule['guide_name'])): ?>
                    <div class="bg-info-bg border border-info rounded-2xl p-4">
                        <div class="text-xs lg:text-sm text-info-text mb-1 font-semibold">HDV hiện tại:</div>
                        <div class="font-bold text-primary-700 text-sm lg:text-base">
                            <?= htmlspecialchars($schedule['guide_name']) ?>
                            <?php if (!empty($schedule['guide_phone'])): ?>
                                - <?= htmlspecialchars($schedule['guide_phone']) ?>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($schedule['guide_notes'])): ?>
                            <div class="text-xs text-primary-600 mt-2">
                                <strong>Ghi chú:</strong> <?= htmlspecialchars($schedule['guide_notes']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Submit Buttons -->
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-primary-100">
                    <a href="?act=admin&module=schedules&action=show&id=<?= $schedule['id'] ?>"
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center">
                        Hủy
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2 <?= !$can_assign ? 'opacity-50 cursor-not-allowed' : '' ?>"
                        <?= !$can_assign ? 'disabled' : '' ?>>
                        <?php if ($can_assign): ?>
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            Phân công HDV
                        <?php else: ?>
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            Chưa đủ số người
                        <?php endif; ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Disable form submission if not enough participants
    <?php if (!$can_assign): ?>
        document.querySelector('form').addEventListener('submit', function (e) {
            e.preventDefault();
            alert('Không thể phân công guide. Cần đủ <?= $min_participants ?> người (hiện tại: <?= $booked ?> người).');
            return false;
        });
    <?php endif; ?>
</script>