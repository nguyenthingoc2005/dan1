<?php
/**
 * ADMIN - CHI TIẾT TÀI XẾ
 */
?>
<div class="max-w-6xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Chi tiết Tài xế</h1>
            <p class="text-sm text-primary-500 mt-1"><?= htmlspecialchars($driver['full_name']) ?></p>
        </div>
        <div class="flex gap-2">
            <a href="?act=admin&module=drivers&action=edit&id=<?= $driver['id'] ?>"
                class="px-4 py-2 bg-accent hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm">
                Chỉnh sửa
            </a>
            <a href="?act=admin&module=drivers"
                class="px-4 py-2 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm">
                Quay lại
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-4 lg:mb-6">
        <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
            <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-4">Thông tin cơ bản</h2>
            <div class="space-y-3">
                <div>
                    <span class="text-xs text-primary-500">Mã tài xế:</span>
                    <p class="font-semibold text-primary-700"><?= htmlspecialchars($driver['driver_code'] ?: 'DRV' . $driver['id']) ?></p>
                </div>
                <div>
                    <span class="text-xs text-primary-500">Họ tên:</span>
                    <p class="font-semibold text-primary-700"><?= htmlspecialchars($driver['full_name']) ?></p>
                </div>
                <div>
                    <span class="text-xs text-primary-500">Số điện thoại:</span>
                    <p class="font-semibold text-primary-700"><?= htmlspecialchars($driver['phone'] ?? '-') ?></p>
                </div>
                <div>
                    <span class="text-xs text-primary-500">Email:</span>
                    <p class="font-semibold text-primary-700"><?= htmlspecialchars($driver['email'] ?? '-') ?></p>
                </div>
                <div>
                    <span class="text-xs text-primary-500">Trạng thái:</span>
                    <p class="font-semibold">
                        <?php
                        $statusColors = [
                            'active' => 'text-success-text',
                            'on_trip' => 'text-info-text',
                            'off_duty' => 'text-warning-text',
                            'suspended' => 'text-danger-text',
                            'inactive' => 'text-primary-500'
                        ];
                        $statusLabels = [
                            'active' => 'Hoạt động',
                            'on_trip' => 'Đang đi tour',
                            'off_duty' => 'Nghỉ',
                            'suspended' => 'Tạm ngưng',
                            'inactive' => 'Ngừng hoạt động'
                        ];
                        ?>
                        <span class="<?= $statusColors[$driver['status']] ?? 'text-primary-700' ?>">
                            <?= $statusLabels[$driver['status']] ?? $driver['status'] ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
            <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-4">Thông tin bằng lái</h2>
            <div class="space-y-3">
                <div>
                    <span class="text-xs text-primary-500">Số bằng lái:</span>
                    <p class="font-semibold text-primary-700"><?= htmlspecialchars($driver['license_number']) ?></p>
                </div>
                <div>
                    <span class="text-xs text-primary-500">Hạng bằng:</span>
                    <p class="font-semibold text-primary-700"><?= htmlspecialchars($driver['license_type'] ?? '-') ?></p>
                </div>
                <div>
                    <span class="text-xs text-primary-500">Ngày cấp:</span>
                    <p class="font-semibold text-primary-700"><?= $driver['license_issue_date'] ? date('d/m/Y', strtotime($driver['license_issue_date'])) : '-' ?></p>
                </div>
                <div>
                    <span class="text-xs text-primary-500">Ngày hết hạn:</span>
                    <p class="font-semibold text-primary-700"><?= $driver['license_expiry_date'] ? date('d/m/Y', strtotime($driver['license_expiry_date'])) : '-' ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($schedules)): ?>
    <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
        <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-4">Lịch làm việc gần đây</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-primary-50">
                    <tr>
                        <th class="px-4 py-2 border-b border-primary-100 text-xs font-bold text-primary-700">Ngày</th>
                        <th class="px-4 py-2 border-b border-primary-100 text-xs font-bold text-primary-700">Tour</th>
                        <th class="px-4 py-2 border-b border-primary-100 text-xs font-bold text-primary-700">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $s): ?>
                        <tr class="border-b border-primary-100 hover:bg-primary-50">
                            <td class="px-4 py-2 text-sm"><?= date('d/m/Y', strtotime($s['schedule_date'])) ?></td>
                            <td class="px-4 py-2 text-sm">
                                <?= htmlspecialchars($s['tour_code']) ?> - <?= htmlspecialchars($s['tour_name']) ?>
                            </td>
                            <td class="px-4 py-2 text-sm">
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-info-bg text-info-text">
                                    <?= $s['status'] == 'completed' ? 'Hoàn thành' : ($s['status'] == 'in_progress' ? 'Đang làm' : 'Đã lên lịch') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

