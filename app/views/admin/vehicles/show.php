<?php
/**
 * ADMIN - CHI TIẾT XE
 */
?>
<div class="max-w-6xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Chi tiết Xe</h1>
            <p class="text-sm text-primary-500 mt-1"><?= htmlspecialchars($vehicle['license_plate']) ?></p>
        </div>
        <div class="flex gap-2">
            <a href="?act=admin&module=vehicles&action=edit&id=<?= $vehicle['id'] ?>"
                class="px-4 py-2 bg-accent hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm">
                Chỉnh sửa
            </a>
            <a href="?act=admin&module=vehicles"
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
                    <span class="text-xs text-primary-500">Mã xe:</span>
                    <p class="font-semibold text-primary-700"><?= htmlspecialchars($vehicle['vehicle_code'] ?: 'VH' . $vehicle['id']) ?></p>
                </div>
                <div>
                    <span class="text-xs text-primary-500">Loại xe:</span>
                    <p class="font-semibold text-primary-700">
                        <?php
                        $types = [
                            'bus_45' => 'Xe bus 45 chỗ',
                            'bus_29' => 'Xe bus 29 chỗ',
                            'bus_16' => 'Xe bus 16 chỗ',
                            'car_7' => 'Xe 7 chỗ',
                            'car_4' => 'Xe 4 chỗ'
                        ];
                        echo htmlspecialchars($types[$vehicle['vehicle_type']] ?? $vehicle['vehicle_type']);
                        ?>
                    </p>
                </div>
                <div>
                    <span class="text-xs text-primary-500">Biển số:</span>
                    <p class="font-semibold text-primary-700"><?= htmlspecialchars($vehicle['license_plate']) ?></p>
                </div>
                <div>
                    <span class="text-xs text-primary-500">Số chỗ:</span>
                    <p class="font-semibold text-primary-700"><?= $vehicle['capacity'] ?> chỗ</p>
                </div>
                <div>
                    <span class="text-xs text-primary-500">Trạng thái:</span>
                    <p class="font-semibold">
                        <?php
                        $statusColors = [
                            'active' => 'text-success-text',
                            'maintenance' => 'text-warning-text',
                            'inactive' => 'text-danger-text'
                        ];
                        $statusLabels = [
                            'active' => 'Hoạt động',
                            'maintenance' => 'Bảo dưỡng',
                            'inactive' => 'Ngừng hoạt động'
                        ];
                        ?>
                        <span class="<?= $statusColors[$vehicle['status']] ?? 'text-primary-700' ?>">
                            <?= $statusLabels[$vehicle['status']] ?? $vehicle['status'] ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <?php if (!empty($vehicle['notes'])): ?>
        <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6">
            <h2 class="text-base lg:text-lg font-bold text-primary-700 mb-4">Ghi chú</h2>
            <p class="text-sm text-primary-600"><?= nl2br(htmlspecialchars($vehicle['notes'])) ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

