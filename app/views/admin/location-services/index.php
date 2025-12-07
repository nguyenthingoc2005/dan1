<?php
/**
 * ADMIN - QUẢN LÝ ĐỊA ĐIỂM & DỊCH VỤ THỐNG NHẤT
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<style>
    /* Flat Design - Simple & Clean */
    :root {
        --primary: #1e293b;
        --accent: #3b82f6;
        --background: #f3f4f6;
        --panel: #ffffff;
        --gray-200: #e5e7eb;
        --gray-400: #9ca3af;
        --gray-600: #4b5563;
        --gray-700: #374151;
    }

    /* Simple Tree Item */
    .tree-item {
        padding: 12px 16px;
        margin-bottom: 4px;
        background-color: var(--panel);
        border-left: 3px solid transparent;
        transition: background-color 0.15s;
    }

    .tree-item:hover {
        background-color: var(--background);
        border-left-color: var(--accent);
    }

    .tree-item.active {
        background-color: var(--primary);
        color: white;
        border-left-color: var(--accent);
    }

    .tree-item.active * {
        color: white;
    }

    .tree-item a.no-underline {
        text-decoration: none;
        color: inherit;
        display: block;
    }


    .tree-icon {
        margin-right: 10px;
        font-size: 16px;
        width: 20px;
        text-align: center;
    }

    .tree-item.country .tree-icon {
        color: var(--accent);
    }

    .tree-item.active .tree-icon {
        color: white;
    }

    .tree-label {
        flex: 1;
        font-weight: 500;
        font-size: 14px;
    }

    .tree-badge {
        background-color: var(--gray-200);
        padding: 3px 8px;
        font-size: 11px;
        font-weight: 600;
        color: var(--gray-700);
        margin-left: 8px;
        margin-right: 8px;
    }

    .tree-item.active .tree-badge {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .tree-actions {
        display: flex;
        gap: 4px;
        margin-left: 12px;
    }

    .tree-action-btn {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        border: none;
        transition: opacity 0.15s;
    }

    .tree-action-btn:hover {
        opacity: 0.8;
    }

    /* Service Cards */
    .service-card {
        background-color: var(--panel);
        border-left: 3px solid var(--accent);
        padding: 20px;
        margin-bottom: 16px;
    }

    .service-card:hover {
        background-color: var(--background);
    }

    .service-card.inactive {
        opacity: 0.6;
        border-left-color: var(--gray-400);
    }

    /* Price Items */
    .price-item {
        background-color: var(--panel);
        border-left: 2px solid var(--gray-200);
        padding: 12px 16px;
        margin-bottom: 8px;
    }

    .price-item:hover {
        background-color: var(--background);
        border-left-color: var(--accent);
    }

    /* Main Content */
    #mainContent {
        min-height: 600px;
        background-color: var(--background);
    }

    /* Empty States */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--gray-500);
    }

    .empty-state i {
        font-size: 48px;
        color: var(--gray-400);
        margin-bottom: 12px;
    }

    .empty-state p {
        font-size: 14px;
        color: var(--gray-600);
    }

    /* Tabs */
    .tab-nav {
        border-bottom: 1px solid var(--gray-200);
        margin-bottom: 20px;
    }

    .tab-link {
        padding: 12px 20px;
        font-weight: 500;
        font-size: 14px;
        color: var(--gray-600);
        border-bottom: 2px solid transparent;
    }

    .tab-link:hover {
        color: var(--accent);
        background-color: var(--background);
    }

    .tab-link.active {
        color: var(--accent);
        border-bottom-color: var(--accent);
    }

    /* Buttons */
    .btn-primary {
        background-color: var(--accent);
        color: white;
    }

    .btn-primary:hover {
        opacity: 0.9;
    }

    /* Province Cards */
    .province-card {
        padding: 16px;
        background: var(--panel);
        border: 1px solid var(--gray-200);
        border-left: 3px solid var(--accent);
        color: inherit;
        display: block;
    }

    .province-card:hover {
        background-color: var(--background);
    }

    /* Destination Cards */
    .destination-card {
        background: var(--panel);
        border: 1px solid var(--gray-200);
    }

    .destination-card:hover {
        background-color: var(--background);
    }

    /* Status Badges */
    .status-badge {
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-badge.active {
        background-color: #d1fae5;
        color: var(--gray-700);
    }

    .status-badge.inactive {
        background-color: var(--gray-200);
        color: var(--gray-600);
    }

    /* Search Box */
    #searchBox {
        border: 1px solid var(--gray-200);
    }

    #searchBox:focus {
        border-color: var(--accent);
        outline: none;
    }

    /* Sidebar Container */
    .sidebar-container {
        background: var(--panel);
        border: 1px solid var(--gray-200);
    }

    /* Empty state */
    .tree-view-empty {
        text-align: center;
        padding: 40px 20px;
        color: var(--gray-400);
    }

    .tree-view-empty i {
        font-size: 36px;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .tree-view-empty p {
        font-size: 13px;
        color: var(--gray-500);
    }

    /* Header */
    .page-header {
        background: var(--panel);
        padding: 20px;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--gray-200);
    }

    .page-header h1 {
        font-size: 24px;
        font-weight: 600;
        color: var(--primary);
    }

</style>

<div class="max-w-full mx-auto px-4 py-6">
    <!-- Header -->
    <div class="page-header">
        <h1 class="text-xl font-semibold text-primary">
            <i class="fas fa-map-marked-alt mr-2"></i>
            Quản lý Địa điểm & Dịch vụ
        </h1>
    </div>

    <!-- Main Container -->
    <div class="flex gap-6" style="min-height: 600px;">
        <!-- Sidebar - Tree View -->
        <div class="w-96 sidebar-container p-6"
            style="max-height: calc(100vh - 200px); overflow-y: auto; min-width: 384px; position: sticky; top: 24px;">
            <div class="mb-6">
                <a href="?act=admin&module=location-services&action=create-country"
                    class="w-full mb-4 px-4 py-2.5 btn-primary text-white text-sm font-medium text-center inline-block flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>Thêm Quốc gia</span>
                </a>
                <div class="relative">
                    <i
                        class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="searchBox" placeholder="Tìm kiếm..."
                        class="w-full pl-10 pr-3 py-2.5 text-sm">
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
                
                // Check if empty
                if (empty($countries['data'])):
            ?>
                <div class="tree-view-empty">
                    <i class="fas fa-globe"></i>
                    <p>Chưa có quốc gia nào</p>
                    <p style="font-size: 12px; margin-top: 4px;">Nhấn "Thêm Quốc gia" để bắt đầu</p>
                </div>
            <?php else: ?>
                <?php
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
                    // Kiểm tra kỹ: chỉ skip nếu thực sự không có id hợp lệ
                    // Không dùng empty() vì có thể skip country hợp lệ có giá trị 0 hoặc false
                    if (!isset($country['id']) || $country['id'] === null || $country['id'] === '') {
                        continue; // Bỏ qua nếu không có ID hợp lệ
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
                        data-country-id="<?= $country_id ?>"
                        data-search-text="<?= htmlspecialchars(strtolower($country['name'])) ?>">
                        <div class="flex items-center justify-between w-full">
                            <a href="?act=admin&module=location-services&country_id=<?= $country_id ?>" 
                               class="flex items-center flex-1 no-underline" style="cursor: pointer; text-decoration: none; color: inherit;">
                                <i class="fas fa-globe tree-icon"></i>
                                <span class="tree-label"><?= htmlspecialchars($country['name']) ?></span>
                                <span class="tree-badge"><?= $country['provinces_count'] ?? 0 ?></span>
                            </a>
                            <div class="tree-actions" onclick="event.stopPropagation();">
                                <a href="?act=admin&module=location-services&action=edit-country&id=<?= $country['id'] ?>"
                                    class="tree-action-btn bg-blue-500 text-white hover:bg-blue-600" title="Sửa" onclick="event.stopPropagation();">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="event.stopPropagation(); toggleCountryStatus(<?= $country['id'] ?>, '<?= $country['status'] ?>')"
                                    class="tree-action-btn <?= $country['status'] == 'active' ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-400 hover:bg-gray-500' ?> text-white"
                                    title="<?= $country['status'] == 'active' ? 'Vô hiệu hóa' : 'Kích hoạt' ?>">
                                    <i class="fas fa-<?= $country['status'] == 'active' ? 'check' : 'times' ?>"></i>
                                </button>
                                <button onclick="event.stopPropagation(); deleteCountry(<?= $country['id'] ?>)"
                                    class="tree-action-btn bg-red-500 text-white hover:bg-red-600" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 p-5" id="mainContent" style="min-height: 600px;">
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
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p class="text-lg font-medium mb-2">Chưa có dịch vụ nào</p>
                        <p class="text-sm text-gray-500">Hãy thêm dịch vụ mới để bắt đầu</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($services['data'] as $service): ?>
                        <?php
                        $is_inactive = ($service['status'] ?? 'active') === 'inactive';
                        ?>
                        <div class="service-card <?= $is_inactive ? 'inactive' : '' ?>">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h5 class="font-semibold text-lg <?= $is_inactive ? 'text-gray-500' : 'text-primary' ?>">
                                            <?= htmlspecialchars($service['service_type_name'] ?? '') ?> -
                                            <?= htmlspecialchars($service['name']) ?>
                                        </h5>
                                        <!-- Status Badge -->
                                        <span class="status-badge <?= $is_inactive ? 'inactive' : 'active' ?>">
                                            <i class="fas fa-<?= $is_inactive ? 'times-circle' : 'check-circle' ?>"></i>
                                            <?= $is_inactive ? 'Đã vô hiệu hóa' : 'Hoạt động' ?>
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
                <div class="tab-nav">
                    <nav class="flex gap-2">
                        <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>&tab=providers"
                            class="tab-link <?= ($current_tab ?? 'providers') == 'providers' ? 'active' : '' ?>">
                            <i class="fas fa-building mr-2"></i>
                            Nhà cung cấp dịch vụ
                            <span class="ml-2 px-2 py-0.5 bg-gray-100 rounded-full text-xs font-semibold"><?= count($service_providers['data'] ?? []) ?></span>
                        </a>
                        <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>&tab=destinations"
                            class="tab-link <?= ($current_tab ?? 'providers') == 'destinations' ? 'active' : '' ?>">
                            <i class="fas fa-map-marked-alt mr-2"></i>
                            Địa điểm du lịch
                            <span class="ml-2 px-2 py-0.5 bg-gray-100 rounded-full text-xs font-semibold"><?= count($destinations['data'] ?? []) ?></span>
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
                        <div class="empty-state">
                            <i class="fas fa-building"></i>
                            <p class="text-lg font-medium mb-2">Chưa có nhà cung cấp nào</p>
                            <p class="text-sm text-gray-500">Hãy thêm nhà cung cấp mới để bắt đầu</p>
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
                        <div class="empty-state">
                            <i class="fas fa-map-marked-alt"></i>
                            <p class="text-lg font-medium mb-2">Chưa có địa điểm nào</p>
                            <p class="text-sm text-gray-500">Hãy thêm địa điểm du lịch mới</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($destinations['data'] as $destination): ?>
                                <div class="destination-card">
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
                                        <div class="absolute top-3 right-3">
                                            <span class="status-badge <?= ($destination['status'] ?? 'active') == 'active' ? 'active' : 'inactive' ?>">
                                                <i class="fas fa-<?= ($destination['status'] ?? 'active') == 'active' ? 'check-circle' : 'times-circle' ?>"></i>
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
                <!-- Chỉ có country_id - Hiển thị danh sách provinces với action buttons -->
                <div>
                    <div class="mb-6 flex justify-between items-center">
                        <h2 class="text-xl font-bold">📍 <?= htmlspecialchars($current_country['name']) ?></h2>
                        <a href="?act=admin&module=location-services&action=create-province&country_id=<?= $current_country_id ?>"
                            class="px-4 py-2.5 bg-green-500 text-white font-medium hover:bg-green-600 transition-colors inline-flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Thêm tỉnh thành</span>
                        </a>
                    </div>
                    <?php if (!empty($provinces)): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php
                            $displayed_province_ids = [];
                            foreach ($provinces as $province):
                                $province_id = (int) $province['id'];
                                // Chỉ hiển thị provinces thuộc country hiện tại
                                if ((int) $province['country_id'] !== (int) $current_country_id) {
                                    continue;
                                }
                                if (in_array($province_id, $displayed_province_ids)) {
                                    continue;
                                }
                                $displayed_province_ids[] = $province_id;
                                $province_url = "?act=admin&module=location-services&country_id={$current_country_id}&province_id={$province_id}&tab=providers";
                                $is_province_active = (!empty($current_province_id) && $current_province_id == $province_id);
                                ?>
                                <div class="province-card <?= $is_province_active ? 'border-accent border-2' : '' ?>">
                                    <div class="flex items-center justify-between mb-3">
                                        <a href="<?= $province_url ?>" class="flex-1">
                                            <h3 class="font-semibold text-lg hover:text-accent"><?= htmlspecialchars($province['name']) ?></h3>
                                        </a>
                                        <span class="status-badge <?= ($province['status'] ?? 'active') == 'active' ? 'active' : 'inactive' ?> ml-2">
                                            <?= ($province['status'] ?? 'active') == 'active' ? 'Hoạt động' : 'Tạm dừng' ?>
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-600 mb-3">
                                        <p>Nhà cung cấp: <span class="font-medium"><?= $province['providers_count'] ?? 0 ?></span></p>
                                    </div>
                                    <div class="flex gap-2 pt-3 border-t border-gray-200">
                                        <a href="<?= $province_url ?>" 
                                           class="flex-1 px-3 py-2 bg-accent text-white text-sm font-medium hover:bg-blue-600 transition-colors text-center">
                                            <i class="fas fa-eye mr-1"></i> Xem
                                        </a>
                                        <a href="?act=admin&module=location-services&action=edit-province&id=<?= $province['id'] ?>&country_id=<?= $current_country_id ?>"
                                            class="px-3 py-2 bg-blue-500 text-white text-sm font-medium hover:bg-blue-600 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button
                                            onclick="toggleProvinceStatus(<?= $province['id'] ?>, '<?= $province['status'] ?? 'active' ?>')"
                                            class="px-3 py-2 <?= ($province['status'] ?? 'active') == 'active' ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-400 hover:bg-gray-500' ?> text-white transition-colors"
                                            title="<?= ($province['status'] ?? 'active') == 'active' ? 'Vô hiệu hóa' : 'Kích hoạt' ?>">
                                            <i class="fas fa-<?= ($province['status'] ?? 'active') == 'active' ? 'check' : 'times' ?>"></i>
                                        </button>
                                        <button onclick="deleteProvince(<?= $province['id'] ?>, <?= $current_country_id ?>)"
                                            class="px-3 py-2 bg-red-500 text-white hover:bg-red-600 transition-colors" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-map-marker-alt"></i>
                            <p class="text-lg font-medium mb-2">Chưa có tỉnh thành nào</p>
                            <p class="text-sm text-gray-500">Hãy thêm tỉnh thành mới cho quốc gia này</p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <!-- Chưa chọn gì - Hiển thị hướng dẫn -->
                <div class="empty-state">
                    <i class="fas fa-hand-pointer"></i>
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

        // Không cần JavaScript handlers cho tree-item click nữa vì đã dùng thẻ <a>
        // Các buttons (edit, delete, toggle status) vẫn dùng JavaScript với preventDefault

        // Không cần AJAX nữa - data đã được load từ server

        // Format price (nếu cần dùng trong modals)
        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price);
        }

        // Search functionality - Tìm kiếm real-time (chỉ tìm countries)
        $('#searchBox').on('input', function() {
            const searchTerm = $(this).val().toLowerCase().trim();
            const $treeView = $('#treeView');
            
            if (searchTerm === '') {
                // Reset: hiển thị tất cả
                $treeView.find('.tree-item').show();
                $treeView.find('.search-no-results').remove();
                return;
            }

            let hasVisibleResults = false;

            // Search countries only
            $treeView.find('.tree-item.country').each(function() {
                const $countryItem = $(this);
                const countrySearchText = $countryItem.data('search-text') || '';
                
                if (countrySearchText.includes(searchTerm)) {
                    $countryItem.show();
                    hasVisibleResults = true;
                } else {
                    $countryItem.hide();
                }
            });

            // Hiển thị message nếu không có kết quả
            let $noResults = $treeView.find('.search-no-results');
            if (!hasVisibleResults && searchTerm !== '') {
                if ($noResults.length === 0) {
                    $noResults = $('<div class="search-no-results" style="padding: 20px; text-align: center; color: var(--gray-500);"><i class="fas fa-search" style="font-size: 32px; margin-bottom: 8px; opacity: 0.5;"></i><p style="font-size: 14px;">Không tìm thấy kết quả</p></div>');
                    $treeView.append($noResults);
                }
            } else {
                $noResults.remove();
            }
        });

        // Clear search on Escape key
        $('#searchBox').on('keydown', function(e) {
            if (e.key === 'Escape') {
                $(this).val('').trigger('input');
            }
        });

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