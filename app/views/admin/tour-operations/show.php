<?php
/**
 * ADMIN - CHI TIẾT TOUR OPERATIONS
 * Gán HDV, xe, phân phòng cho tour đã chốt
 */
if (!is_admin())
    redirect('?act=access-denied');
?>
<div class="max-w-8xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700"><?= htmlspecialchars($summary['tour_name']) ?>
            </h1>
            <p class="text-sm text-primary-500 mt-1">
                <?= htmlspecialchars($summary['tour_code']) ?> |
                Khởi hành: <?= date('d/m/Y', strtotime($summary['start_date'])) ?> -
                <?= date('d/m/Y', strtotime($summary['end_date'])) ?>
            </p>
        </div>
        <a href="?act=admin&module=tour-operations"
            class="px-4 py-2 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm">
            Quay lại
        </a>
    </div>

    <!-- Warning nếu chưa đủ điều kiện thao tác -->
    <?php if (isset($canOperate) && !$canOperate): ?>
        <div class="bg-warning-bg border border-warning rounded-xl p-4 mb-4 lg:mb-6">
            <div class="flex items-start gap-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-warning-text mt-0.5"></i>
                <div>
                    <h3 class="font-bold text-warning-text mb-1">Chưa thể thao tác</h3>
                    <p class="text-sm text-warning-text/80">
                        Tour này chưa đủ điều kiện để gán HDV, xe hoặc phân phòng.
                        Cần đóng tour (status = 'closed') hoặc đợi đến deadline booking
                        (<?= isset($deadlineDate) ? date('d/m/Y', strtotime($deadlineDate)) : '' ?>).
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 lg:gap-4 mb-4 lg:mb-6">
        <div class="bg-success-bg border border-success rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-success-text mb-1 font-semibold">Số người đã thanh toán</div>
            <div class="text-xl lg:text-2xl font-bold text-success-text"><?= $summary['total_paid_participants'] ?>
            </div>
            <div class="text-xs text-success-text/70 mt-1"><?= $summary['total_paid_bookings'] ?> booking</div>
        </div>
        <div class="bg-info-bg border border-info rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-info-text mb-1 font-semibold">Doanh thu</div>
            <div class="text-xl lg:text-2xl font-bold text-info-text">
                <?= number_format($summary['total_revenue'], 0, ',', '.') ?> đ
            </div>
        </div>
        <div class="bg-primary-50 border border-primary-100 rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-primary-500 mb-1 font-semibold">HDV</div>
            <div class="text-xl lg:text-2xl font-bold text-primary-700">
                <?= $summary['guide_name'] ? htmlspecialchars($summary['guide_name']) : '<span class="text-warning-text text-sm">Chưa gán</span>' ?>
            </div>
        </div>
        <div class="bg-primary-50 border border-primary-100 rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-primary-500 mb-1 font-semibold">Xe & Tài xế</div>
            <div class="text-xl lg:text-2xl font-bold text-primary-700">
                <?= $summary['vehicle_count'] > 0 ? $summary['vehicle_count'] . ' xe' : '<span class="text-warning-text text-sm">Chưa gán</span>' ?>
            </div>
        </div>
        <div class="bg-primary-50 border border-primary-100 rounded-2xl p-3 lg:p-4">
            <div class="text-xs lg:text-sm text-primary-500 mb-1 font-semibold">Phòng</div>
            <div class="text-xl lg:text-2xl font-bold text-primary-700">
                <?= isset($summary['room_count']) && $summary['room_count'] > 0 ? $summary['room_count'] . ' phòng' : '<span class="text-warning-text text-sm">Chưa phân</span>' ?>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 overflow-hidden">
        <div class="border-b border-primary-100">
            <div class="flex overflow-x-auto">
                <button onclick="showTab('info')" id="tab-info"
                    class="tab-button px-4 py-3 font-semibold text-sm border-b-2 border-accent text-accent">
                    Thông tin
                </button>
                <button onclick="showTab('bookings')" id="tab-bookings"
                    class="tab-button px-4 py-3 font-semibold text-sm border-b-2 border-transparent text-primary-500 hover:text-primary-700">
                    Booking
                </button>
                <button onclick="showTab('participants')" id="tab-participants"
                    class="tab-button px-4 py-3 font-semibold text-sm border-b-2 border-transparent text-primary-500 hover:text-primary-700">
                    Khách hàng
                </button>
                <button onclick="showTab('guide')" id="tab-guide"
                    class="tab-button px-4 py-3 font-semibold text-sm border-b-2 border-transparent text-primary-500 hover:text-primary-700">
                    HDV
                </button>
                <button onclick="showTab('vehicle')" id="tab-vehicle"
                    class="tab-button px-4 py-3 font-semibold text-sm border-b-2 border-transparent text-primary-500 hover:text-primary-700">
                    Xe & Tài xế
                </button>
                <button onclick="showTab('room')" id="tab-room"
                    class="tab-button px-4 py-3 font-semibold text-sm border-b-2 border-transparent text-primary-500 hover:text-primary-700">
                    Phòng
                </button>
            </div>
        </div>

        <!-- Tab Content: Info -->
        <div id="content-info" class="tab-content p-4 lg:p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                <div>
                    <h3 class="text-base font-bold text-primary-700 mb-3">Thông tin Tour</h3>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-primary-500">Mã tour:</span> <span
                                class="font-semibold"><?= htmlspecialchars($summary['tour_code']) ?></span></div>
                        <div><span class="text-primary-500">Ngày khởi hành:</span> <span
                                class="font-semibold"><?= date('d/m/Y', strtotime($summary['start_date'])) ?></span>
                        </div>
                        <div><span class="text-primary-500">Ngày kết thúc:</span> <span
                                class="font-semibold"><?= date('d/m/Y', strtotime($summary['end_date'])) ?></span></div>
                        <div><span class="text-primary-500">Số ngày:</span> <span
                                class="font-semibold"><?= $summary['duration_days'] ?> ngày
                                <?= $summary['duration_nights'] ?> đêm</span></div>
                        <div><span class="text-primary-500">Deadline booking:</span> <span
                                class="font-semibold"><?= date('d/m/Y', strtotime($summary['booking_deadline_date'])) ?></span>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-base font-bold text-primary-700 mb-3">Thống kê</h3>
                    <div class="space-y-2 text-sm">
                        <div><span class="text-primary-500">Booking đã thanh toán:</span> <span
                                class="font-semibold"><?= $summary['total_paid_bookings'] ?></span></div>
                        <div><span class="text-primary-500">Tổng số người:</span> <span
                                class="font-semibold"><?= $summary['total_paid_participants'] ?></span></div>
                        <div><span class="text-primary-500">Tối thiểu:</span> <span
                                class="font-semibold"><?= $summary['min_participants'] ?></span></div>
                        <div><span class="text-primary-500">Tối đa:</span> <span
                                class="font-semibold"><?= $summary['max_participants'] ?></span></div>
                        <div><span class="text-primary-500">Trạng thái:</span>
                            <span
                                class="px-2 py-1 rounded-lg text-xs font-semibold <?= $summary['participant_status'] == 'SUFFICIENT' ? 'bg-success-bg text-success-text' : 'bg-danger-bg text-danger-text' ?>">
                                <?= $summary['participant_status'] == 'SUFFICIENT' ? 'Đủ' : 'Thiếu' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($summary['guide_name'] && $summary['vehicle_count'] > 0): ?>
                <div class="mt-6">
                    <form action="?act=admin&module=tour-operations&action=updateStatus" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="schedule_id" value="<?= $summary['id'] ?>">
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all">
                            Xác nhận Tour
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab Content: Bookings -->
        <div id="content-bookings" class="tab-content hidden p-4 lg:p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-primary-50">
                        <tr>
                            <th class="px-4 py-2 border-b border-primary-100 text-xs font-bold text-primary-700">Mã
                                Booking</th>
                            <th class="px-4 py-2 border-b border-primary-100 text-xs font-bold text-primary-700">Khách
                                hàng</th>
                            <th class="px-4 py-2 border-b border-primary-100 text-xs font-bold text-primary-700">Số
                                người</th>
                            <th
                                class="px-4 py-2 border-b border-primary-100 text-xs font-bold text-primary-700 text-right">
                                Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b): ?>
                            <tr class="border-b border-primary-100 hover:bg-primary-50">
                                <td class="px-4 py-2 text-sm font-mono text-accent">
                                    <?= htmlspecialchars($b['booking_code']) ?>
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    <?= htmlspecialchars($b['customer_name']) ?><br>
                                    <span
                                        class="text-xs text-primary-500"><?= htmlspecialchars($b['customer_phone']) ?></span>
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    <?= $b['adult_count'] ?> NL, <?= $b['child_count'] ?> TE, <?= $b['infant_count'] ?> EB
                                </td>
                                <td class="px-4 py-2 text-sm text-right font-semibold">
                                    <?= number_format($b['final_amount'], 0, ',', '.') ?> đ
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Content: Participants -->
        <div id="content-participants" class="tab-content hidden p-4 lg:p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-primary-50">
                        <tr>
                            <th class="px-4 py-2 border-b border-primary-100 text-xs font-bold text-primary-700">Họ tên
                            </th>
                            <th class="px-4 py-2 border-b border-primary-100 text-xs font-bold text-primary-700">Giới
                                tính</th>
                            <th class="px-4 py-2 border-b border-primary-100 text-xs font-bold text-primary-700">Loại
                            </th>
                            <th class="px-4 py-2 border-b border-primary-100 text-xs font-bold text-primary-700">Booking
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($participants as $p): ?>
                            <tr class="border-b border-primary-100 hover:bg-primary-50">
                                <td class="px-4 py-2 text-sm"><?= htmlspecialchars($p['full_name']) ?></td>
                                <td class="px-4 py-2 text-sm">
                                    <?= $p['gender'] == 'male' ? 'Nam' : ($p['gender'] == 'female' ? 'Nữ' : 'Khác') ?>
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    <?= $p['age_type'] == 'adult' ? 'Người lớn' : ($p['age_type'] == 'child' ? 'Trẻ em' : 'Em bé') ?>
                                </td>
                                <td class="px-4 py-2 text-sm font-mono text-accent">
                                    <?= htmlspecialchars($p['booking_code']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Content: Guide -->
        <div id="content-guide" class="tab-content hidden p-4 lg:p-6">
            <?php if (isset($canOperate) && !$canOperate): ?>
                <div class="bg-warning-bg border border-warning rounded-xl p-4 mb-4">
                    <p class="text-sm text-warning-text">
                        <i data-lucide="lock" class="w-4 h-4 inline"></i>
                        Chưa thể gán HDV. Cần đóng tour hoặc đợi đến deadline booking.
                    </p>
                </div>
            <?php endif; ?>

            <?php if ($currentGuide): ?>
                <div class="bg-success-bg border border-success rounded-xl p-4 mb-4">
                    <h3 class="font-bold text-success-text mb-2">HDV hiện tại</h3>
                    <p class="text-success-text"><?= htmlspecialchars($currentGuide['full_name']) ?></p>
                    <p class="text-sm text-success-text/70"><?= htmlspecialchars($currentGuide['phone'] ?? '') ?></p>
                </div>
            <?php endif; ?>

            <!-- Lịch sử thay đổi HDV -->
            <?php if (!empty($guideHistory)): ?>
                <div class="bg-panel border border-primary-100 rounded-xl p-4 mb-4">
                    <h3 class="font-bold text-primary-700 mb-3 flex items-center gap-2">
                        <i data-lucide="history" class="w-5 h-5"></i>
                        Lịch sử thay đổi HDV
                    </h3>
                    <div class="space-y-3">
                        <?php foreach ($guideHistory as $history): ?>
                            <div class="border-l-4 border-primary-300 pl-4 py-2 bg-primary-50 rounded-r-lg">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
                                    <div class="flex-1">
                                        <div class="text-sm font-semibold text-primary-700 mb-1">
                                            <?php if ($history['old_guide_name'] && $history['new_guide_name']): ?>
                                                <span
                                                    class="text-warning-text"><?= htmlspecialchars($history['old_guide_name']) ?></span>
                                                <i data-lucide="arrow-right" class="w-4 h-4 inline mx-1"></i>
                                                <span
                                                    class="text-success-text"><?= htmlspecialchars($history['new_guide_name']) ?></span>
                                            <?php elseif ($history['new_guide_name']): ?>
                                                Gán mới: <span
                                                    class="text-success-text"><?= htmlspecialchars($history['new_guide_name']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($history['reason'])): ?>
                                            <p class="text-xs text-primary-600 mb-1">
                                                <span class="font-semibold">Lý do:</span>
                                                <?= htmlspecialchars($history['reason']) ?>
                                            </p>
                                        <?php endif; ?>
                                        <p class="text-xs text-primary-500">
                                            <span class="font-semibold">Thay đổi bởi:</span>
                                            <?= htmlspecialchars($history['changed_by_name'] ?? 'N/A') ?>
                                            <span class="mx-2">•</span>
                                            <span class="font-semibold">Thời gian:</span>
                                            <?= date('d/m/Y H:i', strtotime($history['created_at'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form action="?act=admin&module=tour-operations&action=assignGuide" method="POST"
                class="bg-panel border border-primary-100 rounded-xl p-4" id="assignGuideForm">
                <?php if (isset($canOperate) && !$canOperate): ?>
                    <fieldset disabled>
                    <?php endif; ?>
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="schedule_id" value="<?= $summary['id'] ?>">

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-primary-700 mb-2">Chọn HDV</label>
                        <select name="guide_id" id="guide_id_select" required
                            class="w-full px-4 py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700">
                            <option value="">-- Chọn HDV --</option>
                            <?php foreach ($availableGuides as $g): ?>
                                <option value="<?= $g['id'] ?>" <?= $currentGuide && $currentGuide['id'] == $g['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($g['full_name']) ?> (<?= htmlspecialchars($g['phone'] ?? '') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Trường lý do thay đổi (chỉ hiện khi đang thay đổi HDV) -->
                    <div class="mb-4" id="change_reason_container" style="display: none;">
                        <label class="block text-sm font-semibold text-primary-700 mb-2">
                            Lý do thay đổi HDV <span class="text-red-500">*</span>
                        </label>
                        <textarea name="change_reason" id="change_reason" rows="3"
                            placeholder="Nhập lý do thay đổi HDV (VD: HDV cũ bị ốm, yêu cầu khách hàng, lịch trùng, ...)"
                            class="w-full px-4 py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700"></textarea>
                        <p class="text-xs text-primary-500 mt-1">Vui lòng nhập lý do khi thay đổi HDV</p>
                    </div>

                    <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all"
                        <?= (isset($canOperate) && !$canOperate) ? 'disabled' : '' ?>>
                        <?= $currentGuide ? 'Cập nhật HDV' : 'Gán HDV' ?>
                    </button>
                    <?php if (isset($canOperate) && !$canOperate): ?>
                    </fieldset>
                <?php endif; ?>
            </form>

            <script>
                // Main tabs function - must be defined first (before any onclick calls)
                function showTab(tabName) {
                    // Hide all tabs
                    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
                    document.querySelectorAll('.tab-button').forEach(el => {
                        el.classList.remove('border-accent', 'text-accent');
                        el.classList.add('border-transparent', 'text-primary-500');
                    });

                    // Show selected tab
                    const contentEl = document.getElementById('content-' + tabName);
                    if (contentEl) {
                        contentEl.classList.remove('hidden');
                    }
                    const button = document.getElementById('tab-' + tabName);
                    if (button) {
                        button.classList.remove('border-transparent', 'text-primary-500');
                        button.classList.add('border-accent', 'text-accent');
                    }
                }

                // Hiển thị/ẩn trường lý do khi thay đổi HDV
                document.addEventListener('DOMContentLoaded', function () {
                    const guideSelect = document.getElementById('guide_id_select');
                    const changeReasonContainer = document.getElementById('change_reason_container');
                    const changeReasonInput = document.getElementById('change_reason');
                    const currentGuideId = <?= $currentGuide ? $currentGuide['id'] : 'null' ?>;

                    if (guideSelect && changeReasonContainer) {
                        guideSelect.addEventListener('change', function () {
                            const selectedGuideId = parseInt(this.value);

                            // Hiển thị trường lý do nếu:
                            // 1. Đã có HDV hiện tại (currentGuideId !== null)
                            // 2. Đang chọn HDV khác (selectedGuideId !== currentGuideId)
                            // 3. Đã chọn một HDV (selectedGuideId > 0)
                            if (currentGuideId && selectedGuideId && selectedGuideId !== currentGuideId) {
                                changeReasonContainer.style.display = 'block';
                                changeReasonInput.setAttribute('required', 'required');
                            } else {
                                changeReasonContainer.style.display = 'none';
                                changeReasonInput.removeAttribute('required');
                                changeReasonInput.value = '';
                            }
                        });
                    }
                });
            </script>
        </div>

        <!-- Tab Content: Vehicle -->
        <div id="content-vehicle" class="tab-content hidden p-4 lg:p-6">
            <?php if (isset($canOperate) && !$canOperate): ?>
                <div class="bg-warning-bg border border-warning rounded-xl p-4 mb-4">
                    <p class="text-sm text-warning-text">
                        <i data-lucide="lock" class="w-4 h-4 inline"></i>
                        Chưa thể phân công xe. Cần đóng tour hoặc đợi đến deadline booking.
                    </p>
                </div>
            <?php endif; ?>

            <?php if (!empty($vehicleAssignments)): ?>
                <div class="bg-success-bg border border-success rounded-xl p-4 mb-4">
                    <h3 class="font-bold text-success-text mb-2">Phân công hiện tại</h3>
                    <?php foreach ($vehicleAssignments as $va): ?>
                        <div class="mb-2">
                            <p class="text-success-text">Xe: <?= htmlspecialchars($va['vehicle_code']) ?>
                                (<?= htmlspecialchars($va['license_plate']) ?>)</p>
                            <p class="text-sm text-success-text/70">Tài xế: <?= htmlspecialchars($va['driver_name']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Lịch sử thay đổi xe/tài xế -->
            <?php if (!empty($vehicleHistory)): ?>
                <div class="bg-panel border border-primary-100 rounded-xl p-4 mb-4">
                    <h3 class="font-bold text-primary-700 mb-3 flex items-center gap-2">
                        <i data-lucide="history" class="w-5 h-5"></i>
                        Lịch sử thay đổi xe/tài xế
                    </h3>
                    <div class="space-y-3">
                        <?php foreach ($vehicleHistory as $history): ?>
                            <div class="border-l-4 border-primary-300 pl-4 py-2 bg-primary-50 rounded-r-lg">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
                                    <div class="flex-1">
                                        <div class="text-sm font-semibold text-primary-700 mb-1">
                                            <?php if ($history['change_type'] == 'vehicle' || $history['change_type'] == 'both'): ?>
                                                <?php if ($history['old_vehicle_code'] && $history['new_vehicle_code']): ?>
                                                    <span class="text-warning-text">Xe:
                                                        <?= htmlspecialchars($history['old_vehicle_code']) ?>
                                                        (<?= htmlspecialchars($history['old_vehicle_plate'] ?? '') ?>)</span>
                                                    <i data-lucide="arrow-right" class="w-4 h-4 inline mx-1"></i>
                                                    <span
                                                        class="text-success-text"><?= htmlspecialchars($history['new_vehicle_code']) ?>
                                                        (<?= htmlspecialchars($history['new_vehicle_plate'] ?? '') ?>)</span>
                                                <?php elseif ($history['new_vehicle_code']): ?>
                                                    Gán xe mới: <span
                                                        class="text-success-text"><?= htmlspecialchars($history['new_vehicle_code']) ?>
                                                        (<?= htmlspecialchars($history['new_vehicle_plate'] ?? '') ?>)</span>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php if ($history['change_type'] == 'driver' || $history['change_type'] == 'both'): ?>
                                                <?php if (($history['change_type'] == 'vehicle' || $history['change_type'] == 'both') && ($history['old_vehicle_code'] || $history['new_vehicle_code'])): ?>
                                                    <br>
                                                <?php endif; ?>
                                                <?php if ($history['old_driver_name'] && $history['new_driver_name']): ?>
                                                    <span class="text-warning-text">Tài xế:
                                                        <?= htmlspecialchars($history['old_driver_name']) ?></span>
                                                    <i data-lucide="arrow-right" class="w-4 h-4 inline mx-1"></i>
                                                    <span
                                                        class="text-success-text"><?= htmlspecialchars($history['new_driver_name']) ?></span>
                                                <?php elseif ($history['new_driver_name']): ?>
                                                    Gán tài xế mới: <span
                                                        class="text-success-text"><?= htmlspecialchars($history['new_driver_name']) ?></span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($history['reason'])): ?>
                                            <p class="text-xs text-primary-600 mb-1">
                                                <span class="font-semibold">Lý do:</span>
                                                <?= htmlspecialchars($history['reason']) ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if (!empty($history['notes'])): ?>
                                            <p class="text-xs text-primary-600 mb-1">
                                                <span class="font-semibold">Ghi chú:</span>
                                                <?= htmlspecialchars($history['notes']) ?>
                                            </p>
                                        <?php endif; ?>
                                        <p class="text-xs text-primary-500">
                                            <span class="font-semibold">Thay đổi bởi:</span>
                                            <?= htmlspecialchars($history['changed_by_name'] ?? 'N/A') ?>
                                            <span class="mx-2">•</span>
                                            <span class="font-semibold">Thời gian:</span>
                                            <?= date('d/m/Y H:i', strtotime($history['created_at'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form action="?act=admin&module=tour-operations&action=assignVehicle" method="POST"
                class="bg-panel border border-primary-100 rounded-xl p-4">
                <?php if (isset($canOperate) && !$canOperate): ?>
                    <fieldset disabled>
                    <?php endif; ?>
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="schedule_id" value="<?= $summary['id'] ?>">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-primary-700 mb-2">
                                Chọn xe
                                <?php if (empty($vehicleAssignments)): ?>
                                    <span class="text-red-500">*</span>
                                <?php else: ?>
                                    <span class="text-xs text-primary-500">(Để trống nếu không đổi)</span>
                                <?php endif; ?>
                            </label>
                            <select name="vehicle_id" <?= empty($vehicleAssignments) ? 'required' : '' ?>
                                class="w-full px-4 py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700">
                                <option value="">--
                                    <?= !empty($vehicleAssignments) ? 'Giữ nguyên xe hiện tại' : 'Chọn xe' ?> --
                                </option>
                                <?php if (!empty($availableVehicles)): ?>
                                    <?php foreach ($availableVehicles as $v): ?>
                                        <option value="<?= $v['id'] ?>" <?= !empty($vehicleAssignments) && isset($vehicleAssignments[0]['vehicle_id']) && $vehicleAssignments[0]['vehicle_id'] == $v['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($v['vehicle_code'] ?: 'VH' . $v['id']) ?> -
                                            <?= htmlspecialchars($v['license_plate']) ?>
                                            (<?= $v['capacity'] ?> chỗ)
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>Không có xe khả dụng</option>
                                <?php endif; ?>
                            </select>
                            <?php if (empty($availableVehicles)): ?>
                                <p class="text-xs text-warning-text mt-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3 inline"></i>
                                    Không có xe khả dụng trong khoảng thời gian này.
                                    Vui lòng kiểm tra: Xe có status = 'active'? Xe có bị trùng lịch?
                                    Số người: <?= $summary['total_paid_participants'] ?? 0 ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-primary-700 mb-2">
                                Chọn tài xế
                                <?php if (empty($vehicleAssignments)): ?>
                                    <span class="text-red-500">*</span>
                                <?php else: ?>
                                    <span class="text-xs text-primary-500">(Để trống nếu không đổi)</span>
                                <?php endif; ?>
                            </label>
                            <select name="driver_id" <?= empty($vehicleAssignments) ? 'required' : '' ?>
                                class="w-full px-4 py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700">
                                <option value="">--
                                    <?= !empty($vehicleAssignments) ? 'Giữ nguyên tài xế hiện tại' : 'Chọn tài xế' ?> --
                                </option>
                                <?php if (!empty($availableDrivers)): ?>
                                    <?php foreach ($availableDrivers as $d): ?>
                                        <option value="<?= $d['id'] ?>" <?= !empty($vehicleAssignments) && isset($vehicleAssignments[0]['driver_id']) && $vehicleAssignments[0]['driver_id'] == $d['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($d['full_name']) ?>
                                            (<?= htmlspecialchars($d['license_type'] ?? '') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>Không có tài xế khả dụng</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Trường lý do thay đổi (chỉ hiện khi đang thay đổi xe/tài xế) -->
                    <div class="mb-4" id="vehicle_change_reason_container" style="display: none;">
                        <label class="block text-sm font-semibold text-primary-700 mb-2">
                            Lý do thay đổi <span class="text-red-500">*</span>
                        </label>
                        <textarea name="change_reason" id="vehicle_change_reason" rows="3"
                            placeholder="Nhập lý do thay đổi xe/tài xế (VD: Xe cũ bị hỏng, tài xế cũ bị ốm, yêu cầu khách hàng, ...)"
                            class="w-full px-4 py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700"></textarea>
                        <p class="text-xs text-primary-500 mt-1">Vui lòng nhập lý do khi thay đổi xe hoặc tài xế</p>
                    </div>

                    <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all"
                        <?= (isset($canOperate) && !$canOperate) ? 'disabled' : '' ?>>
                        <?= !empty($vehicleAssignments) ? 'Cập nhật phân công' : 'Phân công xe và tài xế' ?>
                    </button>
                    <?php if (isset($canOperate) && !$canOperate): ?>
                    </fieldset>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tab Content: Room -->
        <div id="content-room" class="tab-content hidden">
            <?php if (isset($canOperate) && !$canOperate): ?>
                <div class="bg-warning-bg border border-warning rounded-xl p-4 m-4 lg:m-6">
                    <p class="text-sm text-warning-text">
                        <i data-lucide="lock" class="w-4 h-4 inline"></i>
                        Chưa thể phân phòng. Cần đóng tour hoặc đợi đến deadline booking.
                    </p>
                </div>
            <?php endif; ?>

            <!-- Room Sub-tabs -->
            <div class="border-b border-primary-200 px-4 lg:px-6">
                <div class="flex gap-1">
                    <button onclick="showRoomSubTab('room-list')"
                        class="room-subtab-button px-4 py-3 border-b-2 border-accent text-accent font-semibold transition-all">
                        <i data-lucide="users" class="w-4 h-4 inline mr-2"></i>
                        Danh sách khách
                    </button>
                    <button onclick="showRoomSubTab('room-config')"
                        class="room-subtab-button px-4 py-3 border-b-2 border-transparent text-primary-500 hover:text-accent font-semibold transition-all">
                        <i data-lucide="settings" class="w-4 h-4 inline mr-2"></i>
                        Cấu hình
                    </button>
                    <button onclick="showRoomSubTab('room-result')"
                        class="room-subtab-button px-4 py-3 border-b-2 border-transparent text-primary-500 hover:text-accent font-semibold transition-all">
                        <i data-lucide="bed-double" class="w-4 h-4 inline mr-2"></i>
                        Kết quả phân phòng
                    </button>
                </div>
            </div>

            <!-- Room Sub-tab Content: Danh sách khách -->
            <div id="room-list" class="room-subtab-content p-4 lg:p-6">
                <?php
                // Lấy danh sách khách đã thanh toán
                $allCustomers = $roomAssignmentModel->getUnassignedCustomers($summary['id']);

                // Nhóm theo booking để hiển thị
                $customersByBooking = [];
                foreach ($allCustomers as $customer) {
                    $booking_id = $customer['booking_id'];
                    if (!isset($customersByBooking[$booking_id])) {
                        $customersByBooking[$booking_id] = [
                            'booking_code' => $customer['booking_code'] ?? 'N/A',
                            'customers' => []
                        ];
                    }
                    $customersByBooking[$booking_id]['customers'][] = $customer;
                }

                // Lấy yêu cầu đặc biệt để đánh dấu
                $allRoomRequests = $roomAssignmentModel->getRoomRequests($summary['id']);
                $requestsByCustomerId = [];
                foreach ($allRoomRequests as $req) {
                    if (!isset($requestsByCustomerId[$req['customer_id']])) {
                        $requestsByCustomerId[$req['customer_id']] = [];
                    }
                    $requestsByCustomerId[$req['customer_id']][] = $req;
                }
                ?>

                <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div>
                        <h3 class="font-bold text-primary-700 text-lg mb-1">Danh sách khách hàng</h3>
                        <p class="text-sm text-primary-600">Chọn khách cần xử lý thủ công (sẽ không tự động phân phòng)
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="selectAllCustomers(true)"
                            class="px-4 py-2 bg-primary-100 hover:bg-primary-200 text-primary-700 rounded-xl text-sm font-semibold transition-all">
                            Chọn tất cả
                        </button>
                        <button type="button" onclick="selectAllCustomers(false)"
                            class="px-4 py-2 bg-primary-100 hover:bg-primary-200 text-primary-700 rounded-xl text-sm font-semibold transition-all">
                            Bỏ chọn
                        </button>
                    </div>
                </div>

                <!-- Filter -->
                <div class="mb-4">
                    <select id="customer-filter" onchange="filterCustomers()"
                        class="px-4 py-2 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent text-primary-700">
                        <option value="all">Tất cả khách</option>
                        <option value="with_request">Có yêu cầu đặc biệt</option>
                        <option value="no_request">Không có yêu cầu</option>
                    </select>
                </div>

                <!-- Danh sách khách -->
                <div class="space-y-4">
                    <?php if (empty($allCustomers)): ?>
                        <div class="bg-primary-50 border border-primary-100 rounded-xl p-8 text-center">
                            <p class="text-primary-600">Không có khách hàng nào cần phân phòng.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($customersByBooking as $booking_id => $bookingData): ?>
                            <div class="bg-panel border border-primary-100 rounded-xl p-4">
                                <div class="font-semibold text-primary-700 mb-3 flex items-center gap-2">
                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                    Booking: <?= htmlspecialchars($bookingData['booking_code']) ?>
                                </div>
                                <div class="space-y-2">
                                    <?php foreach ($bookingData['customers'] as $customer):
                                        $customerRequests = $requestsByCustomerId[$customer['customer_id']] ?? [];
                                        $hasRequest = !empty($customerRequests);
                                        ?>
                                        <div class="customer-item flex items-start gap-3 p-3 rounded-lg hover:bg-primary-50 transition-all <?= $hasRequest ? 'border-l-4 border-warning' : '' ?>"
                                            data-customer-id="<?= $customer['customer_id'] ?>"
                                            data-has-request="<?= $hasRequest ? '1' : '0' ?>">
                                            <input type="checkbox" name="manual_customers[]"
                                                value="<?= $customer['booking_customer_id'] ?>"
                                                id="customer_<?= $customer['booking_customer_id'] ?>"
                                                class="manual-customer-checkbox mt-1 w-4 h-4 text-accent border-primary-300 rounded focus:ring-accent"
                                                data-customer-id="<?= $customer['booking_customer_id'] ?>"
                                                <?= in_array($customer['booking_customer_id'], $session_manual_customers ?? []) ? 'checked' : '' ?> onchange="updateManualCount(); saveManualCustomersToSession();">
                                            <label for="customer_<?= $customer['booking_customer_id'] ?>"
                                                class="flex-1 cursor-pointer">
                                                <div class="flex items-start justify-between">
                                                    <div>
                                                        <span class="font-semibold text-primary-700">
                                                            <?= htmlspecialchars($customer['full_name']) ?>
                                                        </span>
                                                        <span class="text-xs text-primary-500 ml-2">
                                                            (<?= $customer['gender'] == 'male' || $customer['gender'] == 'nam' ? 'Nam' : ($customer['gender'] == 'female' || $customer['gender'] == 'nữ' ? 'Nữ' : 'Khác') ?>)
                                                        </span>
                                                        <?php if ($customer['age_type']): ?>
                                                            <span class="text-xs text-primary-400 ml-1">
                                                                - <?= htmlspecialchars($customer['age_type']) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php if ($hasRequest): ?>
                                                        <div class="flex flex-wrap gap-1 ml-2">
                                                            <?php foreach ($customerRequests as $req): ?>
                                                                <span class="text-xs px-2 py-1 rounded bg-warning-bg text-warning-text">
                                                                    <?php
                                                                    switch ($req['request_type']) {
                                                                        case 'single_room':
                                                                            echo '🏷️ Đơn phòng';
                                                                            if ($req['single_room_supplement'] > 0) {
                                                                                echo ' (+' . number_format($req['single_room_supplement'], 0, ',', '.') . 'đ)';
                                                                            }
                                                                            break;
                                                                        case 'share_with':
                                                                            echo '👥 Cùng phòng với: ' . htmlspecialchars($req['target_customer_name'] ?? '');
                                                                            break;
                                                                        case 'avoid_sharing_with':
                                                                            echo '🚫 Tránh: ' . htmlspecialchars($req['target_customer_name'] ?? '');
                                                                            break;
                                                                    }
                                                                    ?>
                                                                </span>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Thống kê -->
                <div class="mt-4 bg-primary-50 border border-primary-100 rounded-xl p-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-primary-600">Tổng số khách:</span>
                            <span class="font-semibold text-primary-700 ml-2"><?= count($allCustomers) ?></span>
                        </div>
                        <div>
                            <span class="text-primary-600">Đã chọn xử lý thủ công:</span>
                            <span id="manual-count" class="font-semibold text-warning-text ml-2">0</span>
                        </div>
                        <div>
                            <span class="text-primary-600">Sẽ tự động phân phòng:</span>
                            <span id="auto-count"
                                class="font-semibold text-success-text ml-2"><?= count($allCustomers) ?></span>
                        </div>
                        <div>
                            <span class="text-primary-600">Có yêu cầu đặc biệt:</span>
                            <span class="font-semibold text-info-text ml-2"><?= count($allRoomRequests) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room Sub-tab Content: Cấu hình -->
            <div id="room-config" class="room-subtab-content hidden p-4 lg:p-6">
                <div class="max-w-2xl">
                    <h3 class="font-bold text-primary-700 text-lg mb-4">Cấu hình phân phòng tự động</h3>

                    <form id="room-assign-config-form" class="space-y-4">
                        <div class="bg-panel border border-primary-100 rounded-xl p-4">
                            <label class="block text-sm font-semibold text-primary-700 mb-2">
                                Số người/phòng mặc định <span class="text-red-500">*</span>
                            </label>
                            <select name="max_customers_per_room" id="max_customers_per_room"
                                class="w-full px-4 py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent text-primary-700">
                                <option value="2" selected>2 người/phòng (Double/Twin)</option>
                                <option value="3">3 người/phòng (Triple)</option>
                                <option value="4">4 người/phòng (Quad)</option>
                            </select>
                            <p class="text-xs text-primary-500 mt-1">
                                Hệ thống sẽ ưu tiên ghép theo số người này. Nếu lẻ sẽ tự động điều chỉnh.
                            </p>
                        </div>

                        <div class="bg-panel border border-primary-100 rounded-xl p-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="prioritize_same_booking" id="prioritize_same_booking"
                                    checked class="w-4 h-4 text-accent border-primary-300 rounded focus:ring-accent">
                                <span class="text-sm font-semibold text-primary-700">
                                    Ưu tiên ghép khách cùng booking (gia đình/nhóm)
                                </span>
                            </label>
                            <p class="text-xs text-primary-500 mt-1 ml-6">
                                Khách cùng booking sẽ được ưu tiên ở cùng phòng nếu cùng giới tính.
                            </p>
                        </div>

                        <div class="bg-panel border border-primary-100 rounded-xl p-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="auto_single_room" id="auto_single_room" checked
                                    class="w-4 h-4 text-accent border-primary-300 rounded focus:ring-accent">
                                <span class="text-sm font-semibold text-primary-700">
                                    Tự động tạo phòng đơn nếu lẻ
                                </span>
                            </label>
                            <p class="text-xs text-primary-500 mt-1 ml-6">
                                Nếu sau khi phân phòng vẫn còn lẻ người, tự động tạo phòng đơn.
                            </p>
                        </div>

                        <div class="bg-panel border border-primary-100 rounded-xl p-4">
                            <h4 class="font-semibold text-primary-700 mb-3">Xem trước</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-primary-600">Tổng số khách:</span>
                                    <span class="font-semibold text-primary-700"><?= count($allCustomers) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-primary-600">Xử lý thủ công:</span>
                                    <span id="preview-manual-count" class="font-semibold text-warning-text">0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-primary-600">Sẽ tự động phân phòng:</span>
                                    <span id="preview-auto-count"
                                        class="font-semibold text-success-text"><?= count($allCustomers) ?></span>
                                </div>
                                <div class="flex justify-between border-t border-primary-200 pt-2 mt-2">
                                    <span class="text-primary-700 font-semibold">Số phòng dự kiến (≈):</span>
                                    <span id="preview-room-count" class="font-bold text-accent">-</span>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="executeAutoAssign()"
                            class="w-full px-6 py-3 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold transition-all text-base">
                            <i data-lucide="sparkles" class="w-5 h-5 inline mr-2"></i>
                            Phân phòng tự động
                        </button>
                    </form>
                </div>
            </div>

            <!-- Room Sub-tab Content: Kết quả -->
            <div id="room-result" class="room-subtab-content hidden p-4 lg:p-6">

                <!-- Phân phòng theo từng đêm -->
                <?php if (!empty($roomAssignments)): ?>
                    <?php foreach ($roomAssignments as $day_number => $day_data): ?>
                        <div class="bg-panel border border-primary-100 rounded-xl p-4 mb-4">
                            <h3 class="font-bold text-primary-700 mb-3">
                                Đêm <?= $day_number ?>: <?= htmlspecialchars($day_data['day_title'] ?? 'Ngày ' . $day_number) ?>
                            </h3>

                            <div class="space-y-3">
                                <?php
                                $roomIndex = 1;
                                foreach ($day_data['rooms'] as $room):
                                    ?>
                                    <div class="border-l-4 border-primary-300 bg-primary-50 rounded-r-lg p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <span class="font-semibold text-primary-700 text-lg">
                                                    Phòng <?= $roomIndex ?>
                                                </span>
                                                <span class="text-xs text-primary-500 ml-2">
                                                    (<?= $room['actual_occupancy'] ?> người)
                                                </span>
                                            </div>
                                        </div>
                                        <?php if ($room['customers']): ?>
                                            <div class="mt-2">
                                                <div class="flex flex-wrap gap-2">
                                                    <?php
                                                    // Parse customers từ string
                                                    $customersList = explode(', ', $room['customers']);
                                                    foreach ($customersList as $customerInfo):
                                                        // Format: "Tên (age_type)"
                                                        $parts = explode(' (', $customerInfo);
                                                        $customerName = trim($parts[0]);
                                                        $ageType = isset($parts[1]) ? rtrim($parts[1], ')') : '';
                                                        ?>
                                                        <span
                                                            class="inline-block px-3 py-1.5 bg-white border border-primary-200 rounded-lg text-primary-700 text-sm font-medium">
                                                            <?= htmlspecialchars($customerName) ?>
                                                            <?php if ($ageType): ?>
                                                                <span
                                                                    class="text-primary-400 text-xs ml-1">(<?= htmlspecialchars($ageType) ?>)</span>
                                                            <?php endif; ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-sm text-primary-400 italic mt-2">
                                                Chưa có khách
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php $roomIndex++; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-primary-50 border border-primary-100 rounded-xl p-4 text-center">
                        <p class="text-primary-600 mb-4">Chưa có phân phòng. Vui lòng sử dụng chức năng "Phân phòng tự động"
                            bên dưới.</p>
                    </div>
                <?php endif; ?>


            </div>
        </div>
    </div>

    <script>
        // Room Sub-tabs
        function showRoomSubTab(tabName, event) {
            // Hide all room sub-tabs
            document.querySelectorAll('.room-subtab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.room-subtab-button').forEach(el => {
                el.classList.remove('border-accent', 'text-accent');
                el.classList.add('border-transparent', 'text-primary-500', 'hover:text-accent');
            });

            // Show selected tab
            const selectedTab = document.getElementById(tabName);
            if (selectedTab) {
                selectedTab.classList.remove('hidden');
            }

            // Update button style - find button by onclick attribute
            const buttons = document.querySelectorAll('.room-subtab-button');
            buttons.forEach(btn => {
                const onclickAttr = btn.getAttribute('onclick') || '';
                if (onclickAttr.includes("'" + tabName + "'") || onclickAttr.includes('"' + tabName + '"')) {
                    btn.classList.remove('border-transparent', 'text-primary-500');
                    btn.classList.add('border-accent', 'text-accent');
                }
            });
        }

        // Customer selection
        function selectAllCustomers(select) {
            const checkboxes = document.querySelectorAll('input[name="manual_customers[]"]');
            checkboxes.forEach(cb => cb.checked = select);
            updateManualCount();
        }

        function filterCustomers() {
            const filter = document.getElementById('customer-filter').value;
            const items = document.querySelectorAll('.customer-item');

            items.forEach(item => {
                const hasRequest = item.dataset.hasRequest === '1';
                if (filter === 'all') {
                    item.style.display = '';
                } else if (filter === 'with_request') {
                    item.style.display = hasRequest ? '' : 'none';
                } else if (filter === 'no_request') {
                    item.style.display = hasRequest ? 'none' : '';
                }
            });
        }

        // Lưu manual customers vào session khi checkbox thay đổi
        function saveManualCustomersToSession() {
            const checked = Array.from(document.querySelectorAll('input[name="manual_customers[]"]:checked'))
                .map(cb => cb.value);

            // Gửi AJAX request để lưu vào PHP session
            const formData = new FormData();
            formData.append('action', 'save_manual_customers');
            formData.append('schedule_id', '<?= $summary['id'] ?? '' ?>');
            formData.append('csrf_token', '<?= get_csrf_token() ?>');
            checked.forEach(id => {
                formData.append('manual_customers[]', id);
            });

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            }).catch(err => {
                console.error('Error saving to session:', err);
            });
        }

        function updateManualCount() {
            const checked = document.querySelectorAll('input[name="manual_customers[]"]:checked').length;
            const total = document.querySelectorAll('input[name="manual_customers[]"]').length;

            const manualCountEl = document.getElementById('manual-count');
            const autoCountEl = document.getElementById('auto-count');
            const previewManualEl = document.getElementById('preview-manual-count');
            const previewAutoEl = document.getElementById('preview-auto-count');
            const previewRoomEl = document.getElementById('preview-room-count');

            if (manualCountEl) manualCountEl.textContent = checked;
            if (autoCountEl) autoCountEl.textContent = total - checked;
            if (previewManualEl) previewManualEl.textContent = checked;
            if (previewAutoEl) previewAutoEl.textContent = total - checked;

            // Tính số phòng dự kiến
            const maxPerRoomEl = document.getElementById('max_customers_per_room');
            if (maxPerRoomEl && previewRoomEl) {
                const maxPerRoom = parseInt(maxPerRoomEl.value || 2);
                const autoCount = total - checked;
                const estimatedRooms = Math.ceil(autoCount / maxPerRoom);
                previewRoomEl.textContent = estimatedRooms > 0 ? estimatedRooms : '-';
            }
        }

        function executeAutoAssign() {
            // Lấy danh sách đã checked (từ UI)
            const checked = Array.from(document.querySelectorAll('input[name="manual_customers[]"]:checked'))
                .map(cb => cb.value);
            
            // Lấy tổng số khách
            const total = document.querySelectorAll('input[name="manual_customers[]"]').length;
            const autoCount = total - checked.length;
            
            // Kiểm tra nếu tất cả đều được chọn manual
            if (total > 0 && checked.length >= total) {
                alert('Tất cả khách đều được chọn xử lý thủ công. Không có khách nào để phân phòng tự động.\n\nVui lòng bỏ chọn một số khách nếu muốn phân phòng tự động.');
                return;
            }
            
            // Kiểm tra nếu không có khách nào để phân phòng tự động
            if (autoCount === 0 && total > 0) {
                alert('Tất cả khách đều được chọn xử lý thủ công. Không có khách nào để phân phòng tự động.\n\nVui lòng bỏ chọn một số khách nếu muốn phân phòng tự động.');
                return;
            }

            // Lưu vào session trước khi submit (async nhưng không chờ kết quả)
            saveManualCustomersToSession();

            const maxPerRoom = parseInt(document.getElementById('max_customers_per_room').value);
            const prioritizeSameBooking = document.getElementById('prioritize_same_booking').checked;
            const autoSingleRoom = document.getElementById('auto_single_room').checked;

            if (confirm('Bạn có chắc muốn phân phòng tự động với cấu hình này?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '?act=admin&module=tour-operations&action=autoAssignRooms';

                // CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = 'csrf_token';
                csrfInput.value = '<?= get_csrf_token() ?>';
                form.appendChild(csrfInput);

                // Schedule ID
                const scheduleInput = document.createElement('input');
                scheduleInput.type = 'hidden';
                scheduleInput.name = 'schedule_id';
                scheduleInput.value = '<?= $summary['id'] ?? '' ?>';
                form.appendChild(scheduleInput);

                // Manual customers
                checked.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'manual_customers[]';
                    input.value = id;
                    form.appendChild(input);
                });

                // Config
                const configInput = document.createElement('input');
                configInput.type = 'hidden';
                configInput.name = 'max_customers_per_room';
                configInput.value = maxPerRoom;
                form.appendChild(configInput);

                const prioritizeInput = document.createElement('input');
                prioritizeInput.type = 'hidden';
                prioritizeInput.name = 'prioritize_same_booking';
                prioritizeInput.value = prioritizeSameBooking ? '1' : '0';
                form.appendChild(prioritizeInput);

                const singleRoomInput = document.createElement('input');
                singleRoomInput.type = 'hidden';
                singleRoomInput.name = 'auto_single_room';
                singleRoomInput.value = autoSingleRoom ? '1' : '0';
                form.appendChild(singleRoomInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            // Show first room sub-tab by default (room-list)
            showRoomSubTab('room-list');

            updateManualCount();

            // Listen to config changes
            const maxPerRoomEl = document.getElementById('max_customers_per_room');
            if (maxPerRoomEl) {
                maxPerRoomEl.addEventListener('change', updateManualCount);
            }
        });

        // Hiển thị/ẩn trường lý do khi thay đổi xe/tài xế
        document.addEventListener('DOMContentLoaded', function () {
            const vehicleSelect = document.querySelector('select[name="vehicle_id"]');
            const driverSelect = document.querySelector('select[name="driver_id"]');
            const changeReasonContainer = document.getElementById('vehicle_change_reason_container');
            const changeReasonInput = document.getElementById('vehicle_change_reason');

            const currentVehicleId = <?= !empty($vehicleAssignments) && isset($vehicleAssignments[0]['vehicle_id']) ? $vehicleAssignments[0]['vehicle_id'] : 'null' ?>;
            const currentDriverId = <?= !empty($vehicleAssignments) && isset($vehicleAssignments[0]['driver_id']) ? $vehicleAssignments[0]['driver_id'] : 'null' ?>;

            function checkIfChanged() {
                if (!changeReasonContainer || !vehicleSelect || !driverSelect) return;

                const selectedVehicleId = vehicleSelect.value ? parseInt(vehicleSelect.value) : null;
                const selectedDriverId = driverSelect.value ? parseInt(driverSelect.value) : null;

                // Hiển thị trường lý do nếu:
                // 1. Đã có xe/tài xế hiện tại (currentVehicleId hoặc currentDriverId !== null)
                // 2. Đang chọn khác với hiện tại
                // 3. Đã chọn một giá trị (selectedVehicleId hoặc selectedDriverId > 0)
                const vehicleChanged = currentVehicleId && selectedVehicleId && selectedVehicleId !== currentVehicleId;
                const driverChanged = currentDriverId && selectedDriverId && selectedDriverId !== currentDriverId;

                if (vehicleChanged || driverChanged) {
                    changeReasonContainer.style.display = 'block';
                    changeReasonInput.setAttribute('required', 'required');
                } else {
                    changeReasonContainer.style.display = 'none';
                    changeReasonInput.removeAttribute('required');
                    changeReasonInput.value = '';
                }
            }

            if (vehicleSelect) {
                vehicleSelect.addEventListener('change', checkIfChanged);
            }
            if (driverSelect) {
                driverSelect.addEventListener('change', checkIfChanged);
            }
        });
    </script>