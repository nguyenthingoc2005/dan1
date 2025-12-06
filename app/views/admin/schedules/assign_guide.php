<?php
/**
 * ADMIN - PHÂN CÔNG HƯỚNG DẪN VIÊN CHO LỊCH TRÌNH (TOUR-010)
 * Variables: $schedule, $tour, $guides, $booked, $min_participants, $can_assign
 */
if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="?act=admin&module=schedules" class="hover:text-blue-600">Lịch khởi hành</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="?act=admin&module=schedules&action=show&id=<?= $schedule['id'] ?>" class="hover:text-blue-600">Chi
                tiết</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span>Phân công HDV</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-800">Phân công Hướng dẫn viên</h1>
        <p class="text-sm text-gray-500 mt-1">Gán hướng dẫn viên cho lịch trình tour</p>
    </div>

    <!-- Schedule Info Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Thông tin Lịch trình</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="text-sm text-gray-600">Tour:</span>
                <p class="font-bold text-gray-800"><?= htmlspecialchars($schedule['tour_name']) ?></p>
                <p class="text-xs text-gray-500"><?= htmlspecialchars($schedule['tour_code']) ?></p>
            </div>
            <div>
                <span class="text-sm text-gray-600">Ngày khởi hành:</span>
                <p class="font-bold text-green-700"><?= date('d/m/Y', strtotime($schedule['start_date'])) ?></p>
                <p class="text-xs text-gray-500">Kết thúc: <?= date('d/m/Y', strtotime($schedule['end_date'])) ?></p>
            </div>
        </div>
    </div>

    <!-- Participant Info Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Thông tin Số người</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="text-sm text-blue-600 mb-1">Số người tối thiểu</div>
                <div class="text-2xl font-bold text-blue-700"><?= $min_participants ?></div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="text-sm text-green-600 mb-1">Số người đã đặt</div>
                <div class="text-2xl font-bold text-green-700"><?= $booked ?></div>
            </div>
            <div
                class="bg-<?= $can_assign ? 'green' : 'red' ?>-50 border border-<?= $can_assign ? 'green' : 'red' ?>-200 rounded-lg p-4">
                <div class="text-sm text-<?= $can_assign ? 'green' : 'red' ?>-600 mb-1">Trạng thái</div>
                <div class="text-lg font-bold text-<?= $can_assign ? 'green' : 'red' ?>-700">
                    <?php if ($can_assign): ?>
                        ✅ Đủ điều kiện
                    <?php else: ?>
                        ❌ Chưa đủ
                    <?php endif; ?>
                </div>
                <?php if (!$can_assign): ?>
                    <div class="text-xs text-red-600 mt-1">
                        Còn thiếu: <?= $min_participants - $booked ?> người
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Warning if not enough participants -->
    <?php if (!$can_assign): ?>
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-red-800">Chưa đủ số người tối thiểu</h3>
                    <div class="mt-2 text-sm text-red-700">
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
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Chọn Hướng dẫn viên</h2>

        <form action="?act=admin&module=schedules&action=assignGuide" method="POST">
            <input type="hidden" name="id" value="<?= $schedule['id'] ?>">

            <div class="space-y-6">
                <!-- Guide Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Hướng dẫn viên <span class="text-red-500">*</span>
                    </label>
                    <select name="guide_id" id="guide_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
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
                    <p class="text-xs text-gray-500 mt-1">Chọn hướng dẫn viên sẽ phụ trách tour này</p>
                </div>

                <!-- Guide Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú cho HDV</label>
                    <textarea name="guide_notes" id="guide_notes" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        placeholder="Ví dụ: Hướng dẫn đặc biệt cho đoàn VIP, Guide sẽ đón khách tại sân bay..."><?= htmlspecialchars($schedule['guide_notes'] ?? '') ?></textarea>
                    <p class="text-xs text-gray-500 mt-1">Ghi chú đặc biệt hoặc hướng dẫn cho hướng dẫn viên</p>
                </div>

                <!-- Current Guide Info (if exists) -->
                <?php if (!empty($schedule['guide_name'])): ?>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="text-sm text-blue-600 mb-1">HDV hiện tại:</div>
                        <div class="font-bold text-blue-800">
                            <?= htmlspecialchars($schedule['guide_name']) ?>
                            <?php if (!empty($schedule['guide_phone'])): ?>
                                - <?= htmlspecialchars($schedule['guide_phone']) ?>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($schedule['guide_notes'])): ?>
                            <div class="text-xs text-blue-700 mt-2">
                                <strong>Ghi chú:</strong> <?= htmlspecialchars($schedule['guide_notes']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="?act=admin&module=schedules&action=show&id=<?= $schedule['id'] ?>"
                        class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Hủy
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow"
                        <?= !$can_assign ? 'disabled' : '' ?>>
                        <?php if ($can_assign): ?>
                            ✓ Phân công HDV
                        <?php else: ?>
                            ⚠️ Chưa đủ số người
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