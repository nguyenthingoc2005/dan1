<?php
/**
 * Create Service Page
 */
?>

<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
        <a href="?act=admin&module=location-services" class="hover:text-blue-600">Địa điểm & Dịch vụ</a>
        <span>/</span>
        <?php if (!empty($current_country)): ?>
            <a href="?act=admin&module=location-services&country_id=<?= $current_country['id'] ?>"
                class="hover:text-blue-600">
                <?= htmlspecialchars($current_country['name']) ?>
            </a>
            <span>/</span>
        <?php endif; ?>
        <?php if (!empty($current_province)): ?>
            <a href="?act=admin&module=location-services&country_id=<?= $current_country['id'] ?? '' ?>&province_id=<?= $current_province['id'] ?>"
                class="hover:text-blue-600">
                <?= htmlspecialchars($current_province['name']) ?>
            </a>
            <span>/</span>
        <?php endif; ?>
        <?php if (!empty($current_provider)): ?>
            <a href="?act=admin&module=location-services&country_id=<?= $current_country['id'] ?? '' ?>&province_id=<?= $current_province['id'] ?? '' ?>&service_provider_id=<?= $current_provider['id'] ?>"
                class="hover:text-blue-600">
                <?= htmlspecialchars($current_provider['name']) ?>
            </a>
            <span>/</span>
        <?php endif; ?>
        <span class="text-gray-700">Thêm dịch vụ</span>
    </div>
    <h1 class="text-2xl font-bold text-primary">Thêm Dịch vụ</h1>
    <?php if (!empty($current_provider)): ?>
        <p class="text-sm text-gray-500">Nhà dịch vụ: <?= htmlspecialchars($current_provider['name']) ?></p>
    <?php endif; ?>
</div>

<div class="bg-white p-6 border-l-4 border-accent">
    <?php include __DIR__ . '/components/service-form.php'; ?>
</div>
