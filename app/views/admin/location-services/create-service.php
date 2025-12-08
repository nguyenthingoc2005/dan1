<?php
/**
 * Create Service Page
 */
?>

<div class="mb-4 lg:mb-6">
    <div class="flex flex-wrap items-center gap-2 text-xs lg:text-sm text-primary-500 mb-2 lg:mb-3">
        <a href="?act=admin&module=location-services" class="hover:text-accent font-semibold flex items-center gap-1">
            <i data-lucide="map-pin" class="w-3 h-3 lg:w-4 lg:h-4"></i>
            Địa điểm & Dịch vụ
        </a>
        <span>/</span>
        <?php if (!empty($current_country)): ?>
            <a href="?act=admin&module=location-services&country_id=<?= $current_country['id'] ?>"
                class="hover:text-accent font-semibold">
                <?= htmlspecialchars($current_country['name']) ?>
            </a>
            <span>/</span>
        <?php endif; ?>
        <?php if (!empty($current_province)): ?>
            <a href="?act=admin&module=location-services&country_id=<?= $current_country['id'] ?? '' ?>&province_id=<?= $current_province['id'] ?>"
                class="hover:text-accent font-semibold">
                <?= htmlspecialchars($current_province['name']) ?>
            </a>
            <span>/</span>
        <?php endif; ?>
        <?php if (!empty($current_provider)): ?>
            <a href="?act=admin&module=location-services&country_id=<?= $current_country['id'] ?? '' ?>&province_id=<?= $current_province['id'] ?? '' ?>&service_provider_id=<?= $current_provider['id'] ?>"
                class="hover:text-accent font-semibold">
                <?= htmlspecialchars($current_provider['name']) ?>
            </a>
            <span>/</span>
        <?php endif; ?>
        <span class="text-primary-700 font-semibold">Thêm dịch vụ</span>
    </div>
    <h1 class="text-xl lg:text-2xl font-bold text-primary-700 flex items-center gap-2">
        <i data-lucide="plus" class="w-5 h-5 lg:w-6 lg:h-6 text-accent"></i>
        Thêm Dịch vụ
    </h1>
    <?php if (!empty($current_provider)): ?>
        <p class="text-xs lg:text-sm text-primary-500 mt-1">Nhà dịch vụ: <?= htmlspecialchars($current_provider['name']) ?></p>
    <?php endif; ?>
</div>

<div class="bg-panel p-4 lg:p-6 rounded-2xl border-l-4 border-accent shadow-sm">
    <?php include __DIR__ . '/components/service-form.php'; ?>
</div>
