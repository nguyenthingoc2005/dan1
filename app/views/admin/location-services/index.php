<?php
/**
 * ADMIN - QUẢN LÝ ĐỊA ĐIỂM & DỊCH VỤ THỐNG NHẤT
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<style>
    /* Flat Design - Improved spacing and colors */
    .tree-item {
        cursor: pointer;
        padding: 12px 16px;
        margin-bottom: 4px;
        transition: all 0.15s;
        border-left: 4px solid transparent;
        border-radius: 0;
    }

    .tree-item:hover {
        background-color: #f8fafc;
        border-left-color: #cbd5e1;
    }

    .tree-item.active {
        background-color: #3b82f6;
        color: white;
        border-left-color: #1e293b;
    }

    .tree-item.active:hover {
        background-color: #2563eb;
    }

    .tree-children {
        margin-left: 32px;
        margin-top: 4px;
        border-left: 2px solid #e2e8f0;
        padding-left: 12px;
    }

    .tree-children.hidden {
        display: none;
    }

    .tree-item.expanded .tree-children {
        display: block;
    }

    .tree-toggle {
        font-size: 12px;
        opacity: 0.8;
        margin-left: 8px;
    }

    .tree-icon {
        margin-right: 10px;
        font-size: 16px;
        width: 20px;
        text-align: center;
    }

    .tree-label {
        flex: 1;
        font-weight: 500;
        font-size: 14px;
    }

    .tree-badge {
        background-color: #e2e8f0;
        padding: 3px 8px;
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        margin-left: 8px;
        margin-right: 8px;
    }

    .tree-item.active .tree-badge {
        background-color: rgba(255, 255, 255, 0.3);
        color: white;
    }

    .tree-actions {
        display: flex;
        gap: 6px;
        margin-left: 12px;
    }

    .tree-action-btn {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: all 0.15s;
    }

    .service-card {
        background-color: #ffffff;
        border-left: 4px solid #3b82f6;
        padding: 20px;
        margin-bottom: 16px;
        transition: background-color 0.15s;
    }

    .service-card:hover {
        background-color: #f8fafc;
    }

    /* Cải thiện spacing cho main content */
    #mainContent {
        min-height: 600px;
    }

    /* Cải thiện spacing cho province grid */
    .province-grid {
        gap: 12px;
    }

    .price-item {
        background-color: #ffffff;
        border-left: 3px solid #e5e7eb;
        padding: 12px 16px;
        transition: background-color 0.15s;
    }

    .price-item:hover {
        background-color: #f8fafc;
        border-left-color: #3b82f6;
    }

    .loading {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }
</style>

<div class="max-w-full mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Quản lý Địa điểm & Dịch vụ</h1>
    </div>

    <!-- Main Container -->
    <div class="flex gap-4" style="min-height: 600px;">
        <!-- Sidebar - Tree View -->
        <div class="w-96 bg-white border-r border-gray-200 p-5"
            style="max-height: 80vh; overflow-y: auto; min-width: 384px;">
            <div class="mb-6">
                <a href="?act=admin&module=location-services&action=create-country"
                    class="w-full mb-4 px-4 py-3 bg-accent text-white hover:bg-blue-600 text-sm font-semibold text-center inline-block transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>Thêm Quốc gia</span>
                </a>
                <div class="relative">
                    <i
                        class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="searchBox" placeholder="Tìm kiếm..."
                        class="w-full pl-10 pr-3 py-2.5 border border-gray-300 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent text-sm">
                </div>
            </div>

            <div id="treeView" class="tree-view">
                <?php
                // Ensure countries is properly initialized
                if (!isset($countries) || !is_array($countries)) {
                    $countries = ['data' => []];
                }
                if (!isset($countries['data'])) {
                    $countries['data'] = [];
                }

                // Get current country_id from controller variable (ưu tiên từ controller, sau đó từ URL)
                // Đảm bảo luôn cast về int hoặc null để so sánh đúng
                if (isset($current_country_id) && $current_country_id !== null && $current_country_id !== '') {
                    $current_country_id = (int) $current_country_id;
                } elseif (!empty($_GET['country_id'])) {
                    $current_country_id = (int) $_GET['country_id'];
                } else {
                    $current_country_id = null;
                }

                if (isset($current_province_id) && $current_province_id !== null && $current_province_id !== '') {
                    $current_province_id = (int) $current_province_id;
                } elseif (!empty($_GET['province_id'])) {
                    $current_province_id = (int) $_GET['province_id'];
                } else {
                    $current_province_id = null;
                }

                // Nếu có province_id nhưng chưa có country_id, lấy từ province
                if ($current_province_id && !$current_country_id && !empty($provinces)) {
                    foreach ($provinces as $prov) {
                        if ((int) $prov['id'] === $current_province_id) {
                            $current_country_id = (int) $prov['country_id'];
                            break;
                        }
                    }
                }

                // Nếu có service_provider_id nhưng chưa có province_id/country_id, cố gắng lấy từ provider
                if (empty($current_province_id) && !empty($current_service_provider_id) && !empty($current_provider)) {
                    if (!empty($current_provider['province_id'])) {
                        $current_province_id = (int) $current_provider['province_id'];
                    }
                    if (!empty($current_provider['country_id'])) {
                        $current_country_id = (int) $current_provider['country_id'];
                    }
                }
                ?>
                <?php
                // Loại bỏ trùng lặp trong view (double check - đảm bảo an toàn)
                $displayed_country_ids = [];
                foreach ($countries['data'] as $country):
                    if (empty($country) || !isset($country['id'])) {
                        continue; // Bỏ qua nếu không có ID
                    }
                    $country_id = (int) $country['id'];
                    if (in_array($country_id, $displayed_country_ids)) {
                        continue; // Bỏ qua nếu đã hiển thị
                    }
                    $displayed_country_ids[] = $country_id;

                    // Check nếu country này đang active
                    // Đảm bảo so sánh đúng với type casting
                    $is_expanded = false;
                    if ($current_country_id !== null && $current_country_id !== '') {
                        $is_expanded = ((int) $current_country_id === (int) $country_id);
                    }
                    ?>
                    <div class="tree-item country <?= $is_expanded ? 'expanded active' : '' ?>"
                        data-country-id="<?= $country_id ?>">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center flex-1" style="cursor: pointer;">
                                <i class="fas fa-globe tree-icon"></i>
                                <span class="tree-label"><?= htmlspecialchars($country['name']) ?></span>
                                <span class="tree-badge"><?= $country['provinces_count'] ?? 0 ?></span>
                                <i class="fas fa-chevron-<?= $is_expanded ? 'down' : 'right' ?> tree-toggle"
                                    style="cursor: pointer;"></i>
                            </div>
                            <div class="tree-actions" onclick="event.stopPropagation();">
                                <a href="?act=admin&module=location-services&action=edit-country&id=<?= $country['id'] ?>"
                                    class="tree-action-btn bg-blue-500 text-white hover:bg-blue-600" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="toggleCountryStatus(<?= $country['id'] ?>, '<?= $country['status'] ?>')"
                                    class="tree-action-btn <?= $country['status'] == 'active' ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-400 hover:bg-gray-500' ?> text-white"
                                    title="<?= $country['status'] == 'active' ? 'Vô hiệu hóa' : 'Kích hoạt' ?>">
                                    <i class="fas fa-<?= $country['status'] == 'active' ? 'check' : 'times' ?>"></i>
                                </button>
                                <button onclick="deleteCountry(<?= $country['id'] ?>)"
                                    class="tree-action-btn bg-red-500 text-white hover:bg-red-600" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="tree-children <?= $is_expanded ? '' : 'hidden' ?>"
                        id="country-<?= $country['id'] ?>-children">
                        <?php if ($is_expanded && !empty($provinces)): ?>
                            <?php
                            // Loại bỏ trùng lặp trong tree view (double check)
                            $displayed_tree_province_ids = [];
                            foreach ($provinces as $province):
                                $province_id = (int) $province['id'];
                                // Chỉ hiển thị provinces thuộc country hiện tại
                                if ((int) $province['country_id'] !== (int) $country['id']) {
                                    continue;
                                }
                                if (in_array($province_id, $displayed_tree_province_ids)) {
                                    continue; // Bỏ qua nếu đã hiển thị
                                }
                                $displayed_tree_province_ids[] = $province_id;

                                $is_province_active = (!empty($current_province_id) && $current_province_id == $province_id);
                                ?>
                                <div class="tree-item province <?= $is_province_active ? 'active' : '' ?>"
                                    data-province-id="<?= $province['id'] ?>">
                                    <div class="flex items-center justify-between w-full">
                                        <div class="flex items-center flex-1" style="cursor: pointer;">
                                            <i class="fas fa-map-marker-alt tree-icon"></i>
                                            <span class="tree-label"><?= htmlspecialchars($province['name']) ?></span>
                                            <span class="tree-badge"><?= $province['providers_count'] ?? 0 ?></span>
                                        </div>
                                        <div class="tree-actions" onclick="event.stopPropagation();">
                                            <a href="?act=admin&module=location-services&action=edit-province&id=<?= $province['id'] ?>&country_id=<?= $current_country_id ?>"
                                                class="tree-action-btn bg-blue-500 text-white hover:bg-blue-600" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button
                                                onclick="toggleProvinceStatus(<?= $province['id'] ?>, '<?= $province['status'] ?? 'active' ?>')"
                                                class="tree-action-btn <?= ($province['status'] ?? 'active') == 'active' ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-400 hover:bg-gray-500' ?> text-white"
                                                title="<?= ($province['status'] ?? 'active') == 'active' ? 'Vô hiệu hóa' : 'Kích hoạt' ?>">
                                                <i
                                                    class="fas fa-<?= ($province['status'] ?? 'active') == 'active' ? 'check' : 'times' ?>"></i>
                                            </button>
                                            <button onclick="deleteProvince(<?= $province['id'] ?>, <?= $current_country_id ?>)"
                                                class="tree-action-btn bg-red-500 text-white hover:bg-red-600" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($provinces) || count($displayed_tree_province_ids) == 0): ?>
                                <div class="tree-item" style="padding-left: 20px; color: #94a3b8; font-size: 13px;">
                                    <i class="fas fa-info-circle mr-2"></i>Chưa có tỉnh thành
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 bg-white p-6" id="mainContent" style="min-height: 600px;">
            <?php if (!empty($current_service_provider_id) && !empty($current_provider)): ?>
                <!-- Services List for Provider - Kiểm tra service_provider_id TRƯỚC province_id -->
                <div class="mb-4">
                    <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>"
                        class="text-blue-600 hover:underline mb-2 inline-block">← Quay lại danh sách nhà cung cấp</a>
                    <h2 class="text-xl font-bold mb-2">Dịch vụ của: <?= htmlspecialchars($current_provider['name'] ?? '') ?>
                    </h2>
                    <a href="?act=admin&module=location-services&action=create-service&service_provider_id=<?= $current_service_provider_id ?>"
                        class="px-4 py-2.5 bg-green-500 text-white font-medium hover:bg-green-600 transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        <span>Thêm dịch vụ</span>
                    </a>
                </div>

                <?php if (empty($services['data'])): ?>
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                        <p class="text-sm">Chưa có dịch vụ nào. Hãy thêm mới!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($services['data'] as $service): ?>
                        <?php
                        $is_inactive = ($service['status'] ?? 'active') === 'inactive';
                        ?>
                        <div class="service-card mb-4 <?= $is_inactive ? 'opacity-60 border-2 border-gray-300 bg-gray-50' : '' ?>">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h5 class="font-semibold text-lg <?= $is_inactive ? 'text-gray-500' : 'text-primary' ?>">
                                            <?= htmlspecialchars($service['service_type_name'] ?? '') ?> -
                                            <?= htmlspecialchars($service['name']) ?>
                                        </h5>
                                        <!-- Status Badge -->
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded <?= $is_inactive ? 'bg-gray-400 text-white' : 'bg-green-500 text-white' ?>">
                                            <?= $is_inactive ? '⛔ Đã vô hiệu hóa' : '✓ Hoạt động' ?>
                                        </span>
                                    </div>
                                    <?php if (!empty($service['description'])): ?>
                                        <p class="text-sm <?= $is_inactive ? 'text-gray-400' : 'text-gray-600' ?> mt-2">
                                            <?= htmlspecialchars($service['description']) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($service['unit'])): ?>
                                        <p class="text-xs <?= $is_inactive ? 'text-gray-400' : 'text-gray-500' ?> mt-2">Đơn vị:
                                            <?= htmlspecialchars($service['unit']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="flex gap-2 ml-4">
                                    <button
                                        class="px-3 py-1.5 bg-accent text-white text-xs font-medium hover:bg-blue-600 transition-colors inline-flex items-center gap-1.5 <?= $is_inactive ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                        onclick="<?= $is_inactive ? 'return false;' : 'openCreatePriceModal(' . $service['id'] . ')' ?>"
                                        <?= $is_inactive ? 'disabled title="Dịch vụ đã bị vô hiệu hóa"' : '' ?>>
                                        <i class="fas fa-plus text-xs"></i>
                                        <span>Thêm giá</span>
                                    </button>
                                    <a href="?act=admin&module=location-services&action=edit-service&id=<?= $service['id'] ?>"
                                        class="px-3 py-1.5 bg-yellow-500 text-white text-xs font-medium hover:bg-yellow-600 inline-flex items-center gap-1.5 transition-colors">
                                        <i class="fas fa-edit text-xs"></i>
                                        <span>Sửa</span>
                                    </a>
                                    <button
                                        class="px-3 py-1.5 bg-red-500 text-white text-xs font-medium hover:bg-red-600 transition-colors inline-flex items-center gap-1.5"
                                        onclick="deleteService(<?= $service['id'] ?>)">
                                        <i class="fas fa-trash text-xs"></i>
                                        <span>Xóa</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Danh sách giá -->
                            <div class="mt-5 pt-4 border-t border-gray-200">
                                <h6 class="font-semibold text-sm mb-3 text-primary">
                                    Bảng giá (<?= count($service['prices'] ?? []) ?>)
                                </h6>
                                <?php if (empty($service['prices'])): ?>
                                    <p class="text-xs text-gray-500">Chưa có giá nào. Nhấn "+ Thêm giá" để thêm giá mới.</p>
                                <?php else: ?>
                                    <div class="space-y-2">
                                        <?php foreach ($service['prices'] as $price): ?>
                                            <div class="price-item flex justify-between items-center">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-3 mb-1">
                                                        <span class="font-semibold text-accent text-base">
                                                            <?= number_format($price['unit_price'], 0, ',', '.') ?> VND
                                                        </span>
                                                        <span
                                                            class="px-2 py-0.5 text-xs font-medium
                                                            <?= $price['price_type'] == 'peak' ? 'bg-red-100 text-red-700' :
                                                                ($price['price_type'] == 'low' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') ?>">
                                                            <?= $price['price_type'] == 'peak' ? 'Cao điểm' :
                                                                ($price['price_type'] == 'low' ? 'Thấp điểm' : 'Tiêu chuẩn') ?>
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center gap-4 text-xs text-gray-500">
                                                        <?php if (!empty($price['start_date']) || !empty($price['end_date'])): ?>
                                                            <span>
                                                                <?= !empty($price['start_date']) ? date('d/m/Y', strtotime($price['start_date'])) : '...' ?>
                                                                -
                                                                <?= !empty($price['end_date']) ? date('d/m/Y', strtotime($price['end_date'])) : '...' ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <span>Vô thời hạn</span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($price['notes'])): ?>
                                                            <span class="text-gray-400">
                                                                <?= htmlspecialchars(substr($price['notes'], 0, 50)) ?>
                                                                <?= strlen($price['notes']) > 50 ? '...' : '' ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="flex gap-2 ml-4">
                                                    <button
                                                        class="px-2.5 py-1 bg-yellow-500 text-white text-xs font-medium hover:bg-yellow-600 transition-colors inline-flex items-center gap-1"
                                                        onclick="openEditPriceModal(<?= $price['id'] ?>)">
                                                        <i class="fas fa-edit text-xs"></i>
                                                        <span>Sửa</span>
                                                    </button>
                                                    <button
                                                        class="px-2.5 py-1 bg-red-500 text-white text-xs font-medium hover:bg-red-600 transition-colors inline-flex items-center gap-1"
                                                        onclick="deletePrice(<?= $price['id'] ?>)">
                                                        <i class="fas fa-trash text-xs"></i>
                                                        <span>Xóa</span>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            <?php elseif (!empty($current_province_id)): ?>
                <!-- Có province_id - Hiển thị tabs và nội dung -->
                <!-- Tab Navigation -->
                <div class="mb-6 border-b border-gray-200">
                    <nav class="flex gap-1">
                        <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>&tab=providers"
                            class="px-4 py-3 font-medium text-sm transition-colors <?= ($current_tab ?? 'providers') == 'providers' ? 'border-b-2 border-accent text-accent' : 'text-gray-600 hover:text-primary' ?>">
                            Nhà cung cấp dịch vụ (<?= count($service_providers['data'] ?? []) ?>)
                        </a>
                        <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>&tab=destinations"
                            class="px-4 py-3 font-medium text-sm transition-colors <?= ($current_tab ?? 'providers') == 'destinations' ? 'border-b-2 border-accent text-accent' : 'text-gray-600 hover:text-primary' ?>">
                            Địa điểm du lịch (<?= count($destinations['data'] ?? []) ?>)
                        </a>
                    </nav>
                </div>

                <?php if (($current_tab ?? 'providers') == 'providers'): ?>
                    <!-- Service Providers Tab -->
                    <div class="mb-4">
                        <h2 class="text-xl font-bold mb-2">📍 <?= htmlspecialchars($current_province['name'] ?? 'Tỉnh thành') ?>
                        </h2>
                        <a href="?act=admin&module=location-services&action=create-provider&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>"
                            class="px-4 py-2.5 bg-accent text-white font-medium hover:bg-blue-600 transition-colors inline-flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Thêm nhà cung cấp</span>
                        </a>
                    </div>

                    <?php if (empty($service_providers['data'])): ?>
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-building text-4xl mb-3 text-gray-300"></i>
                            <p class="text-sm">Chưa có nhà cung cấp nào. Hãy thêm mới!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($service_providers['data'] as $provider): ?>
                            <div class="service-card mb-4">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-lg text-primary mb-2"><?= htmlspecialchars($provider['name']) ?></h3>
                                        <div class="space-y-1 text-sm text-gray-600">
                                            <?php if (!empty($provider['service_code'])): ?>
                                                <p>Mã: <span class="font-medium"><?= htmlspecialchars($provider['service_code']) ?></span>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($provider['phone'])): ?>
                                                <p>📞 <?= htmlspecialchars($provider['phone']) ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($provider['email'])): ?>
                                                <p>✉️ <?= htmlspecialchars($provider['email']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 ml-4">
                                        <a href="?act=admin&module=location-services&action=edit-provider&id=<?= $provider['id'] ?>&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>"
                                            class="px-3 py-1.5 bg-accent text-white text-sm font-medium hover:bg-blue-600 transition-colors inline-flex items-center gap-1.5">
                                            <i class="fas fa-edit text-xs"></i>
                                            <span>Sửa</span>
                                        </a>
                                        <button
                                            class="px-3 py-1.5 bg-red-500 text-white text-sm font-medium hover:bg-red-600 transition-colors inline-flex items-center gap-1.5"
                                            onclick="deleteServiceProvider(<?= $provider['id'] ?>)">
                                            <i class="fas fa-trash text-xs"></i>
                                            <span>Xóa</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="pt-4 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <h4 class="font-semibold text-primary">Dịch vụ (<?= $provider['services_count'] ?? 0 ?>)</h4>
                                        <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>&service_provider_id=<?= $provider['id'] ?>"
                                            class="px-3 py-1.5 bg-green-500 text-white text-sm font-medium hover:bg-green-600 transition-colors inline-flex items-center gap-1.5">
                                            <i class="fas fa-list text-xs"></i>
                                            <span>Xem dịch vụ</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                <?php elseif (($current_tab ?? 'providers') == 'destinations'): ?>
                    <!-- Destinations Tab -->
                    <div class="mb-4">
                        <h2 class="text-xl font-bold mb-2">📍 <?= htmlspecialchars($current_province['name'] ?? 'Tỉnh thành') ?>
                        </h2>
                        <a href="?act=admin&module=location-services&action=create-destination&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>"
                            class="px-4 py-2.5 bg-accent text-white font-medium hover:bg-blue-600 transition-colors inline-flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Thêm địa điểm du lịch</span>
                        </a>
                    </div>

                    <?php if (empty($destinations['data'])): ?>
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-map-marked-alt text-4xl mb-3 text-gray-300"></i>
                            <p class="text-sm">Chưa có địa điểm nào. Hãy thêm mới!</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($destinations['data'] as $destination): ?>
                                <div
                                    class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm transition-all hover:shadow-md hover:border-accent">
                                    <!-- Image Section -->
                                    <div class="relative w-full h-48 bg-gray-100 overflow-hidden">
                                        <?php if (!empty($destination['thumbnail'])): ?>
                                            <img src="<?= htmlspecialchars($destination['thumbnail']) ?>"
                                                alt="<?= htmlspecialchars($destination['name']) ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div
                                                class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                                <div class="text-center">
                                                    <i class="fas fa-image text-4xl text-gray-400 mb-2"></i>
                                                    <p class="text-xs text-gray-500">Chưa có ảnh</p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <!-- Status Badge -->
                                        <div class="absolute top-2 right-2">
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded <?= ($destination['status'] ?? 'active') == 'active' ? 'bg-green-500 text-white' : 'bg-gray-400 text-white' ?>">
                                                <?= ($destination['status'] ?? 'active') == 'active' ? 'Hoạt động' : 'Ngừng hoạt động' ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Content Section -->
                                    <div class="p-4">
                                        <h3 class="font-bold text-lg text-primary mb-2 line-clamp-1">
                                            <?= htmlspecialchars($destination['name']) ?>
                                        </h3>
                                        <?php if (!empty($destination['description'])): ?>
                                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                                <?= htmlspecialchars(substr($destination['description'], 0, 100)) ?>
                                                <?= mb_strlen($destination['description']) > 100 ? '...' : '' ?>
                                            </p>
                                        <?php endif; ?>

                                        <!-- Actions -->
                                        <div class="flex gap-2 pt-3 border-t border-gray-200">
                                            <a href="?act=admin&module=location-services&action=edit-destination&id=<?= $destination['id'] ?>&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>"
                                                class="flex-1 px-3 py-1.5 bg-accent text-white text-sm font-medium hover:bg-blue-600 transition-colors inline-flex items-center justify-center gap-1.5">
                                                <i class="fas fa-edit text-xs"></i>
                                                <span>Sửa</span>
                                            </a>
                                            <button
                                                class="flex-1 px-3 py-1.5 bg-red-500 text-white text-sm font-medium hover:bg-red-600 transition-colors inline-flex items-center justify-center gap-1.5"
                                                onclick="deleteDestination(<?= $destination['id'] ?>)">
                                                <i class="fas fa-trash text-xs"></i>
                                                <span>Xóa</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            <?php elseif (!empty($current_country_id) && !empty($current_country)): ?>
                <!-- Chỉ có country_id - Hiển thị danh sách provinces -->
                <div class="text-center py-10">
                    <div class="mb-4">
                        <h2 class="text-xl font-bold mb-2">📍 <?= htmlspecialchars($current_country['name']) ?></h2>
                        <a href="?act=admin&module=location-services&action=create-province&country_id=<?= $current_country_id ?>"
                            class="px-4 py-2.5 bg-green-500 text-white font-medium hover:bg-green-600 transition-colors inline-flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Thêm Tỉnh thành</span>
                        </a>
                    </div>
                    <p class="text-gray-600 mb-4">Vui lòng chọn một tỉnh thành từ danh sách bên trái hoặc từ danh sách dưới
                        đây để xem nhà cung cấp và địa điểm du lịch.</p>
                    <?php if (!empty($provinces)): ?>
                        <div class="mt-6">
                            <p class="text-sm font-medium text-gray-700 mb-3">Danh sách tỉnh thành:</p>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                <?php
                                // Loại bỏ trùng lặp trong view (double check)
                                $displayed_province_ids = [];
                                foreach ($provinces as $province):
                                    $province_id = (int) $province['id'];
                                    if (in_array($province_id, $displayed_province_ids)) {
                                        continue; // Bỏ qua nếu đã hiển thị
                                    }
                                    $displayed_province_ids[] = $province_id;
                                    ?>
                                    <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?>&province_id=<?= $province['id'] ?>&tab=providers"
                                        class="px-4 py-3 bg-blue-50 hover:bg-blue-100 text-sm text-center border-l-4 border-accent transition-colors block">
                                        <span
                                            class="font-medium text-primary block"><?= htmlspecialchars($province['name']) ?></span>
                                        <span class="text-xs text-gray-500 mt-1 block"><?= $province['providers_count'] ?? 0 ?> nhà
                                            cung cấp</span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <i class="fas fa-map text-4xl mb-3 text-gray-300"></i>
                            <p class="text-gray-500">Chưa có tỉnh thành nào trong quốc gia này.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Chưa chọn gì - Hiển thị hướng dẫn -->
                <div class="loading">
                    <i class="fas fa-hand-pointer text-5xl mb-4 text-gray-300"></i>
                    <p class="text-base font-medium">Vui lòng chọn một quốc gia từ danh sách bên trái để bắt đầu</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modals -->
<?php include __DIR__ . '/modals.php'; ?>

<script>
    $(document).ready(function () {
        let currentProvinceId = null;
        window.currentServiceProviderId = null;
        let currentServiceId = null;

        // Tree view functionality - Dùng URL parameters thay vì AJAX
        $(document).on('click', '.tree-item.country .flex.items-center.flex-1', function (e) {
            e.stopPropagation();
            const $item = $(this).closest('.tree-item.country');
            const countryId = $item.data('country-id');

            // Redirect với country_id parameter
            const url = new URL(window.location.href);
            url.searchParams.set('act', 'admin');
            url.searchParams.set('module', 'location-services');
            url.searchParams.set('country_id', countryId);
            // Xóa province_id và service_provider_id khi chọn country mới
            url.searchParams.delete('province_id');
            url.searchParams.delete('service_provider_id');
            url.searchParams.delete('tab');

            window.location.href = url.toString();
        });

        // Toggle expand/collapse cho country (click vào toggle hoặc label)
        $(document).on('click', '.tree-item.country .tree-toggle', function (e) {
            e.stopPropagation();
            const $item = $(this).closest('.tree-item.country');
            const countryId = $item.data('country-id');
            const $children = $(`#country-${countryId}-children`);

            if ($children.hasClass('hidden')) {
                // Expand - redirect với country_id
                const url = new URL(window.location.href);
                url.searchParams.set('act', 'admin');
                url.searchParams.set('module', 'location-services');
                url.searchParams.set('country_id', countryId);
                url.searchParams.delete('province_id');
                url.searchParams.delete('service_provider_id');
                url.searchParams.delete('tab');
                window.location.href = url.toString();
            } else {
                // Collapse - chỉ ẩn children
                $children.addClass('hidden');
                $item.removeClass('expanded');
                $(this).removeClass('fa-chevron-down').addClass('fa-chevron-right');
            }
        });

        // Select province - Dùng URL parameters
        // Select province - Dùng URL parameters (nếu provinces được render từ server)
        $(document).on('click', '.tree-item.province', function (e) {
            e.stopPropagation();
            const provinceId = $(this).data('province-id');
            const $countryItem = $(this).closest('.tree-children').siblings('.tree-item.country');
            const countryId = $countryItem.data('country-id') || <?= json_encode($current_country_id ?? null) ?>;

            // Redirect với province_id parameter
            const url = new URL(window.location.href);
            url.searchParams.set('act', 'admin');
            url.searchParams.set('module', 'location-services');
            if (countryId) {
                url.searchParams.set('country_id', countryId);
            }
            url.searchParams.set('province_id', provinceId);
            url.searchParams.set('tab', 'providers'); // Default tab
            // Xóa service_provider_id khi chọn province mới
            url.searchParams.delete('service_provider_id');

            window.location.href = url.toString();
        });

        // Không cần AJAX nữa - data đã được load từ server

        // Format price (nếu cần dùng trong modals)
        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price);
        }

        // Country functions
        window.deleteCountry = function (id) {
            if (!confirm('Bạn có chắc muốn xóa quốc gia này? Lưu ý: Không thể xóa nếu đang có tỉnh thành.')) return;

            const url = new URL(window.location.href);
            url.searchParams.set('act', 'admin');
            url.searchParams.set('module', 'location-services');
            url.searchParams.set('action', 'delete-country');
            url.searchParams.set('id', id);

            window.location.href = url.toString();
        };

        window.deleteProvince = function (id, countryId) {
            if (!confirm('Bạn có chắc muốn xóa tỉnh thành này? Lưu ý: Không thể xóa nếu đang có địa điểm hoặc nhà dịch vụ.')) return;

            const url = new URL(window.location.href);
            url.searchParams.set('act', 'admin');
            url.searchParams.set('module', 'location-services');
            url.searchParams.set('action', 'delete-province');
            url.searchParams.set('id', id);
            if (countryId) {
                url.searchParams.set('country_id', countryId);
            }

            window.location.href = url.toString();
        };

        window.toggleProvinceStatus = function (id, currentStatus) {
            $.ajax({
                url: `?act=admin&module=location-services&action=toggle-province-status&id=${id}`,
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        showToast(response.message || 'Cập nhật trạng thái thành công!', 'success');
                        // Reload page để cập nhật UI
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else {
                        showToast(response.message || 'Có lỗi xảy ra', 'error');
                    }
                },
                error: function () {
                    showToast('Lỗi khi cập nhật trạng thái', 'error');
                }
            });
        };

        window.toggleCountryStatus = function (id, currentStatus) {
            $.ajax({
                url: `?act=admin&module=location-services&action=toggle-country-status&id=${id}`,
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        showToast(response.message || 'Cập nhật trạng thái thành công!', 'success');
                        // Reload page để cập nhật UI
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else {
                        showToast(response.message || 'Có lỗi xảy ra', 'error');
                    }
                },
                error: function () {
                    showToast('Lỗi khi cập nhật trạng thái', 'error');
                }
            });
        };

        // Delete functions - Dùng URL redirect sau khi xóa
        window.deleteServiceProvider = function (id) {
            if (!confirm('Bạn có chắc muốn xóa nhà cung cấp này?')) return;

            const url = new URL(window.location.href);
            url.searchParams.set('act', 'admin');
            url.searchParams.set('module', 'location-services');
            url.searchParams.set('action', 'deleteServiceProvider');
            url.searchParams.set('id', id);
            url.searchParams.set('country_id', url.searchParams.get('country_id') || '');
            url.searchParams.set('province_id', url.searchParams.get('province_id') || '');

            window.location.href = url.toString();
        };

        window.deleteService = function (id) {
            if (!confirm('Bạn có chắc muốn xóa dịch vụ này?')) return;

            const url = new URL(window.location.href);
            url.searchParams.set('act', 'admin');
            url.searchParams.set('module', 'location-services');
            url.searchParams.set('action', 'deleteService');
            url.searchParams.set('id', id);

            window.location.href = url.toString();
        };

        window.deletePrice = function (priceId) {
            if (!confirm('Bạn có chắc muốn xóa giá này?')) return;

            const url = new URL(window.location.href);
            url.searchParams.set('act', 'admin');
            url.searchParams.set('module', 'location-services');
            url.searchParams.set('action', 'deletePrice');
            url.searchParams.set('id', priceId);

            window.location.href = url.toString();
        };

        window.openEditPriceModal = function (priceId) {
            // Load price data và mở modal
            $.ajax({
                url: `?act=admin&module=location-services&action=getPrice&id=${priceId}`,
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.success && response.data) {
                        const price = response.data;

                        // Set form data
                        $('#priceId').val(price.id);
                        $('#priceServiceId').val(price.service_id);
                        $('#priceUnitPrice').val(price.unit_price);
                        $('#priceType').val(price.price_type || 'standard');
                        $('#priceValidFrom').val(price.start_date || '');
                        $('#priceValidTo').val(price.end_date || '');
                        $('#priceNotes').val(price.notes || '');

                        // Update modal title
                        $('#priceModalTitle').text('Sửa giá dịch vụ');

                        // Load service info để hiển thị context
                        $.ajax({
                            url: `?act=admin&module=location-services&action=getService&id=${price.service_id}`,
                            method: 'GET',
                            dataType: 'json',
                            success: function (serviceResponse) {
                                if (serviceResponse.success && serviceResponse.data) {
                                    const service = serviceResponse.data;
                                    const providerName = service.service_provider_name || '';
                                    const serviceName = service.name || '';

                                    let contextParts = [];
                                    if (providerName) contextParts.push(providerName);
                                    if (serviceName) contextParts.push(serviceName);
                                    const contextText = contextParts.length > 0
                                        ? contextParts.join(' > ')
                                        : 'Đang sửa giá dịch vụ';
                                    $('#priceContextText').text(contextText);
                                }
                            }
                        });

                        $('#priceModal').removeClass('hidden');
                    } else {
                        showToast('Không thể tải thông tin giá', 'error');
                    }
                },
                error: function () {
                    showToast('Lỗi khi tải thông tin giá', 'error');
                }
            });
        };


        // Modal functions are defined in modals.php
    });
</script>