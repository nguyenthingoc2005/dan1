<?php
/**
 * Create Destination Page
 */
?>

<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="?act=admin&module=location-services" class="hover:text-blue-600">Địa điểm & Dịch vụ</a>
        <span>/</span>
        <a href="?act=admin&module=location-services&country_id=<?= $current_country['id'] ?>"
            class="hover:text-blue-600">
            <?= htmlspecialchars($current_country['name']) ?>
        </a>
        <span>/</span>
        <a href="?act=admin&module=location-services&country_id=<?= $current_country['id'] ?>&province_id=<?= $current_province['id'] ?>&tab=destinations"
            class="hover:text-blue-600">
            <?= htmlspecialchars($current_province['name']) ?>
        </a>
        <span>/</span>
        <span class="text-gray-700">Thêm địa điểm</span>
    </div>
    <h1 class="text-2xl font-bold text-primary">Thêm Địa điểm du lịch</h1>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded text-sm">
        <span class="font-medium text-blue-800">📍 Địa điểm: </span>
        <span class="text-blue-700"><?= htmlspecialchars($current_country['name']) ?> >
            <?= htmlspecialchars($current_province['name']) ?></span>
    </div>

    <?php include __DIR__ . '/components/destination-form.php'; ?>
</div>