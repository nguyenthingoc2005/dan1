<?php
/**
 * Create Service Provider Page
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
        <a href="?act=admin&module=location-services&country_id=<?= $current_country['id'] ?>&province_id=<?= $current_province['id'] ?>"
            class="hover:text-blue-600">
            <?= htmlspecialchars($current_province['name']) ?>
        </a>
        <span>/</span>
        <span class="text-gray-700">Thêm nhà dịch vụ</span>
    </div>
    <h1 class="text-2xl font-bold text-primary">Thêm Nhà dịch vụ</h1>
</div>

<div class="bg-white p-6 border-l-4 border-accent">
    <div class="mb-4 p-3 bg-blue-50 border-l-4 border-accent text-sm">
        <span class="font-medium text-primary">📍 Địa điểm: </span>
        <span class="text-gray-700"><?= htmlspecialchars($current_country['name']) ?> >
            <?= htmlspecialchars($current_province['name']) ?></span>
    </div>

    <?php include __DIR__ . '/components/service-provider-form.php'; ?>
</div>