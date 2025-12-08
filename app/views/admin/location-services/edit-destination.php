<?php
/**
 * Edit Destination Page
 */
?>

<div class="mb-4 lg:mb-6">
    <div class="flex flex-wrap items-center gap-2 text-xs lg:text-sm text-primary-500 mb-2 lg:mb-3">
        <a href="?act=admin&module=location-services" class="hover:text-accent font-semibold flex items-center gap-1">
            <i data-lucide="map-pin" class="w-3 h-3 lg:w-4 lg:h-4"></i>
            Địa điểm & Dịch vụ
        </a>
        <span>/</span>
        <a href="?act=admin&module=location-services&country_id=<?= $current_country['id'] ?>"
            class="hover:text-accent font-semibold">
            <?= htmlspecialchars($current_country['name']) ?>
        </a>
        <span>/</span>
        <a href="?act=admin&module=location-services&country_id=<?= $current_country['id'] ?>&province_id=<?= $current_province['id'] ?>&tab=destinations"
            class="hover:text-accent font-semibold">
            <?= htmlspecialchars($current_province['name']) ?>
        </a>
        <span>/</span>
        <span class="text-primary-700 font-semibold">Sửa địa điểm</span>
    </div>
    <h1 class="text-xl lg:text-2xl font-bold text-primary-700 flex items-center gap-2">
        <i data-lucide="edit" class="w-5 h-5 lg:w-6 lg:h-6 text-accent"></i>
        Sửa Địa điểm du lịch
    </h1>
</div>

<div class="bg-panel p-4 lg:p-6 rounded-2xl border-l-4 border-accent shadow-sm">
    <div class="mb-4 lg:mb-6 p-3 lg:p-4 bg-info-bg border border-info rounded-xl text-xs lg:text-sm">
        <span class="font-bold text-info-dark flex items-center gap-2">
            <i data-lucide="map-pin" class="w-4 h-4"></i>
            Địa điểm:
        </span>
        <span class="text-info-text"><?= htmlspecialchars($current_country['name']) ?> >
            <?= htmlspecialchars($current_province['name']) ?></span>
    </div>

    <?php include __DIR__ . '/components/destination-form.php'; ?>
</div>